<?php

namespace App\Services\PostGeneration;

use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Log;

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

        return \Spatie\Browsershot\Browsershot::html($html)
            ->windowSize($width, $height)
            ->deviceScaleFactor(4)
            ->clip(0, 0, $width, $height)
            ->format('png')
            ->noSandbox()
            ->waitUntilNetworkIdle()
            ->timeout(60);
    }

    private function generateCarouselViaBrowsershot(Post $post): array
    {
        $aspectRatio = $post->template_options['aspect_ratio'] ?? '1:1';
        $targetWidth = 1080;
        $targetHeight = ($aspectRatio === '4:5') ? 1350 : 1080;

        $slides = [];
        $totalSlides = 1;

        if ($post->template === 'guide_pedagogique') {
            $contentData = json_decode($post->refined_content, true);
            $totalSlides = is_array($contentData) && isset($contentData['slides']) ? count($contentData['slides']) : 3;
            $totalSlides += 1; // +1 for the cover slide
        } elseif ($post->template === 'project') {
            $totalSlides = 3;
        } elseif ($post->template === 'faq') {
            $faqContentData = json_decode($post->refined_content, true);
            $faqSlideCount = (is_array($faqContentData) && isset($faqContentData['slides']) && is_array($faqContentData['slides']))
                ? count($faqContentData['slides'])
                : 1;
            $totalSlides = 1 + $faqSlideCount;
        } elseif ($post->template === 'chantier_detail' || $post->template === 'step_by_step') {
            $photos = $post->extra_images ?? [];
            if(!empty($post->image_path)) {
                array_unshift($photos, $post->image_path);
            }
            $photoCount = count($photos);
            if ($photoCount === 0) {
                $totalSlides = 3; // Match the [null, null, null] default in post.blade.php
            } else {
                $totalSlides = $photoCount + ($post->template === 'step_by_step' ? 1 : 0);
            }
        } else {
            // Single image templates (lifestyle, quality_focus, review, etc.)
            $totalSlides = 1;
        }

        // Add final contact slide only if contact info is provided
        $company = $post->company;
        $hasContactInfo = !empty($post->template_options['company_phone'] ?? $company->phone) || 
                          !empty($post->template_options['company_email'] ?? $company->email) || 
                          !empty($post->template_options['company_website'] ?? $company->website);
                          
        if ($hasContactInfo) {
            $totalSlides += 1;
        }

        for ($i = 0; $i < $totalSlides; $i++) {
            $html = view('render.post', ['post' => $post, 'slideIndex' => $i])->render();
            $runId    = $post->template_options['_variant_run'] ?? $post->id;
            $fileName = "posts/{$runId}_{$i}.png";
            $fullPath = storage_path('app/public/' . $fileName);
            
            if (!file_exists(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }

            $imageData = $this->getBrowsershotInstance($html, $aspectRatio)->screenshot();

            $manager = new ImageManager(new Driver());
            $image = $manager->read($imageData);
            $image->cover($targetWidth, $targetHeight);
            $image->toPng()->save($fullPath);
            
            Log::debug("Image Generator: Slide $i saved as PNG", ['path' => $fullPath, 'dimensions' => "{$targetWidth}x{$targetHeight}"]);
            $slides[] = $fileName;
        }
        
        return $slides;
    }
}
