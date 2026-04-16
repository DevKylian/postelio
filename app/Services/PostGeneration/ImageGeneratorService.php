<?php

namespace App\Services\PostGeneration;

use App\Models\Post;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Symfony\Component\Process\Process;

class ImageGeneratorService
{
    protected $fontPath;

    public function __construct()
    {
        $this->fontPath = str_replace('\\', '/', storage_path('fonts/Montserrat-Bold.ttf'));
    }

    /**
     * Generate image(s) for a post.
     */
    public function generate(Post $post, int $width = 1080, int $height = 1080): array
    {
        return $this->generateCarouselViaBrowsershot($post);
    }

    public function generateForFacebook(Post $post): array
    {
        return $this->generate($post);
    }

    protected function getBrowsershotInstance(string $html, string $aspectRatio = '1:1'): \Spatie\Browsershot\Browsershot
    {
        $width = 360;
        $height = ($aspectRatio === '4:5') ? 450 : 360;

        $instance = \Spatie\Browsershot\Browsershot::html($html)
            ->windowSize($width, $height)
            ->deviceScaleFactor(4)
            ->clip(0, 0, $width, $height)
            ->noSandbox()
            ->waitUntilNetworkIdle()
            ->timeout(90);

        $chromePath = env('BROWSERSHOT_CHROME_PATH');
        if ($chromePath) {
            $instance->setChromePath($chromePath);
        }

        return $instance;
    }

    /**
     * Calculate the total number of slides for the given post.
     */
    private function calculateTotalSlides(Post $post): int
    {
        $totalSlides = 1;

        if ($post->template === 'guide_pedagogique') {
            $contentData = json_decode($post->refined_content, true);
            $totalSlides = is_array($contentData) && isset($contentData['slides']) ? count($contentData['slides']) : 3;
            $totalSlides += 1; // +1 for the cover slide
        } elseif ($post->template === 'project') {
            $totalSlides = 3;
        } elseif ($post->template === 'faq') {
            $faqContentData = json_decode($post->refined_content, true);
            $faqSlideCount  = (is_array($faqContentData) && isset($faqContentData['slides']) && is_array($faqContentData['slides']))
                ? count($faqContentData['slides'])
                : 1;
            $totalSlides = 1 + $faqSlideCount;
        } elseif ($post->template === 'chantier_detail' || $post->template === 'step_by_step') {
            $photos = $post->extra_images ?? [];
            if (!empty($post->image_path)) {
                array_unshift($photos, $post->image_path);
            }
            $photoCount = count($photos);
            $totalSlides = $photoCount === 0
                ? 3
                : $photoCount + ($post->template === 'step_by_step' ? 1 : 0);
        } else {
            $totalSlides = 1;
        }

        // Add final contact slide only if contact info is provided
        $company = $post->company;
        $hasContactInfo = !empty($post->template_options['company_phone'] ?? $company?->phone)
            || !empty($post->template_options['company_email'] ?? $company?->email)
            || !empty($post->template_options['company_website'] ?? $company?->website);

        if ($hasContactInfo) {
            $totalSlides += 1;
        }

        return $totalSlides;
    }

    private function generateCarouselViaBrowsershot(Post $post): array
    {
        $aspectRatio = $post->template_options['aspect_ratio'] ?? '1:1';
        $targetWidth  = 1080;
        $targetHeight = ($aspectRatio === '4:5') ? 1350 : 1080;
        $totalSlides  = $this->calculateTotalSlides($post);

        $runId     = $post->template_options['_variant_run'] ?? $post->id;
        $outputDir = storage_path('app/public/posts');
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        // ── 1. Pre-render all HTML strings (fast — pure PHP/Blade) ───────
        $tempHtmlFiles = [];
        $tempPngFiles  = [];

        for ($i = 0; $i < $totalSlides; $i++) {
            $html = view('render.post', ['post' => $post, 'slideIndex' => $i])->render();

            $tmpHtml = tempnam(sys_get_temp_dir(), 'slide_html_');
            file_put_contents($tmpHtml, $html);
            $tempHtmlFiles[$i] = $tmpHtml;

            $tempPngFiles[$i] = sys_get_temp_dir() . '/slide_png_' . uniqid() . '.png';
        }

        // ── 2. Launch all Browsershot processes in parallel ───────────────
        $processes = [];
        foreach ($tempHtmlFiles as $i => $tmpHtml) {
            $process = new Process([
                PHP_BINARY, base_path('artisan'),
                'post:render-slide',
                $tmpHtml,
                $tempPngFiles[$i],
                $aspectRatio,
            ]);
            $process->setTimeout(120);
            $process->start();
            $processes[$i] = $process;
            Log::debug("Image Generator: Launched slide {$i} render process");
        }

        // ── 3. Wait for all processes and collect results ─────────────────
        $slides  = [];
        $manager = new ImageManager(new Driver());

        foreach ($processes as $i => $process) {
            $process->wait();

            if (!$process->isSuccessful() || !file_exists($tempPngFiles[$i])) {
                // Cleanup remaining temp HTML files
                foreach ($tempHtmlFiles as $f) {
                    @unlink($f);
                }
                foreach ($tempPngFiles as $f) {
                    @unlink($f);
                }
                Log::error("Image Generator: Slide {$i} failed", [
                    'exit_code' => $process->getExitCode(),
                    'stderr'    => $process->getErrorOutput(),
                    'stdout'    => $process->getOutput(),
                ]);
                throw new \RuntimeException(
                    "Slide {$i} rendering failed (exit {$process->getExitCode()}): " . $process->getErrorOutput()
                );
            }

            $fileName = "posts/{$runId}_{$i}.png";
            $fullPath = storage_path('app/public/' . $fileName);

            $image = $manager->read(file_get_contents($tempPngFiles[$i]));
            $image->cover($targetWidth, $targetHeight);
            $image->toPng()->save($fullPath);

            Log::debug("Image Generator: Slide {$i} saved as PNG", [
                'path'       => $fullPath,
                'dimensions' => "{$targetWidth}x{$targetHeight}",
            ]);

            $slides[$i] = $fileName;

            // Cleanup temp files for this slide
            @unlink($tempHtmlFiles[$i]);
            @unlink($tempPngFiles[$i]);
        }

        return array_values($slides);
    }
}
