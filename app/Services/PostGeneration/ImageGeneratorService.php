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
    public function generate(Post $post, int $width = 1080, int $height = 1080): string|array
    {
        $template = $post->template;

        if ($template === 'custom') {
            $photoMode = $post->template_options['custom_photo_mode'] ?? 'none';
            if ($photoMode === 'carousel') {
                return $this->generateCarouselViaBrowsershot($post);
            }
            return $this->generateSingleViaBrowsershot($post);
        }

        if (in_array($template, ['chantier_detail', 'step_by_step', 'guide_pedagogique'])) {
            return $this->generateCarouselViaBrowsershot($post);
        }

        return $this->generateSingleViaBrowsershot($post);
    }

    public function generateForFacebook(Post $post): string|array
    {
        // For Facebook, we can just reuse the existing generated images for now
        // Facebook accepts square 1080x1080 which is what Browsershot creates.
        return $this->generate($post);
    }

    protected function getBrowsershotInstance(string $html): \Spatie\Browsershot\Browsershot
    {
        return \Spatie\Browsershot\Browsershot::html($html)
            ->windowSize(360, 360)
            ->deviceScaleFactor(3)
            ->clip(0, 0, 360, 360)
            ->format('jpeg')
            ->noSandbox()
            ->timeout(60);
    }

    private function generateSingleViaBrowsershot(Post $post): string
    {
        $html = view('render.post', ['post' => $post, 'slideIndex' => null])->render();
        $fileName = 'posts/' . $post->id . '_generated.jpg';
        $fullPath = storage_path('app/public/' . $fileName);
        
        // Ensure directory exists
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        // Use Browsershot to get the image as a string, then force JPEG via Intervention Image
        $imageData = $this->getBrowsershotInstance($html)->screenshot();

        $manager = new ImageManager(new Driver());
        $image = $manager->read($imageData);
        $image->cover(1080, 1080);
        $image->toJpeg(90)->save($fullPath);

        Log::debug('Image Generator: Single image saved as JPEG', ['path' => $fullPath]);

        return $fileName;
    }

    private function generateCarouselViaBrowsershot(Post $post): array
    {
        $slides = [];
        $totalSlides = 3;

        if ($post->template === 'guide_pedagogique') {
            $contentData = json_decode($post->refined_content, true);
            $totalSlides = is_array($contentData) && isset($contentData['slides']) ? count($contentData['slides']) : 3;
            $totalSlides += 1; // +1 for the cover slide
        } else {
            $totalSlides = count($post->extra_images ?? []) + ($post->image_path ? 1 : 0);
            if ($post->template === 'step_by_step') {
                $totalSlides += 1; // +1 for the cover slide
            }
            // For custom carousel, ensure we have at least 1 slide if no images
            if ($totalSlides < 1) $totalSlides = 1; 
        }

        for ($i = 0; $i < $totalSlides; $i++) {
            $html = view('render.post', ['post' => $post, 'slideIndex' => $i])->render();
            $fileName = "posts/{$post->id}_{$i}.jpg";
            $fullPath = storage_path('app/public/' . $fileName);
            
            if (!file_exists(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }

            $imageData = $this->getBrowsershotInstance($html)->screenshot();

            $manager = new ImageManager(new Driver());
            $image = $manager->read($imageData);
            $image->cover(1080, 1080);
            $image->toJpeg(90)->save($fullPath);
            
            Log::debug("Image Generator: Carousel slide $i saved as JPEG", ['path' => $fullPath]);
            $slides[] = $fileName;
        }
        
        return $slides;
    }
}
