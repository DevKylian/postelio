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

    private function generateCarouselViaBrowsershot(Post $post): array
    {
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
            $totalSlides = count($post->extra_images ?? []) + 1; // +1 for cover/intro
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
            
            Log::debug("Image Generator: Slide $i saved as JPEG", ['path' => $fullPath]);
            $slides[] = $fileName;
        }
        
        return $slides;
    }
}
