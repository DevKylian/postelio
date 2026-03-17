<?php

namespace App\Services\PostGeneration;

use App\Models\Post;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;

class ImageGeneratorService
{
    protected $manager;
    protected $fontPath;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
        // Use forward slashes for Windows compatibility in Intervention V3
        $this->fontPath = str_replace('\\', '/', storage_path('fonts/Montserrat-Bold.ttf'));
    }

    /**
     * Generate a 1080x1080 post image.
     */
    public function generate(Post $post): string
    {
        $company = $post->company;
        $content = $post->refined_content;
        $template = $post->template;

        // 1. Create Canvas
        $image = $this->manager->create(1080, 1080);

        // 2. Apply Background
        $brandColor = $company->brand_color ?? '#F59E0B';
        
        // For reviews, the background is the brand color
        if ($template === 'review') {
            $image->fill($brandColor);
            $this->drawReviewElements($image);
            $this->drawText($image, $content, 350, 950, 60); // Centered in the middle zone
        } elseif ($template === 'faq') {
            $image->fill('#0f172a'); // Midnight/Slate dark background
            $this->drawFaqElements($image);
            $this->drawText($image, $content, 400, 850, 60); // Centered Question
        } elseif ($template === 'lifestyle') {
            $this->drawLifestyleElements($image, $post);
            // Typically no text on the image for lifestyle as per user request
        } elseif ($template === 'project') {
            // For projects, background doesn't matter much as images cover it
            $image->fill('#FFFFFF');
            $this->drawProjectElements($image, $post);
            // No descriptive text on before/after as requested by user
        } else {
            $image->fill($brandColor);
            $this->drawText($image, $content, 200, 880, 50);
        }

        // 5. Add Logo (Optional: Could be integrated into banner if needed)
        //$this->addLogo($image, $company->logo_path);

        // 6. Save
        $fileName = 'posts/' . $post->id . '_generated.png';
        Storage::disk('public')->put($fileName, $image->toPng());

        return $fileName;
    }

    private function drawReviewElements($image)
    {
        $starY = 250;
        $starSpacing = 90;
        $startX = 540 - (($starSpacing * 4) / 2);

        for ($i = 0; $i < 5; $i++) {
            $this->drawStar($image, $startX + ($i * $starSpacing), $starY, 35, 15, '#FACC15'); // Yellow-400 for stars
        }
    }

    private function drawProjectElements($image, Post $post)
    {
        $bannerHeight = 150; // Balanced height for a 1080px canvas
        $imageHeight = 1080 - $bannerHeight;
        $halfWidth = 540;

        // 1. Draw Images Side-by-Side
        if ($post->image_before_path && Storage::disk('public')->exists($post->image_before_path)) {
            $before = $this->manager->read(Storage::disk('public')->get($post->image_before_path));
            $before->cover($halfWidth, $imageHeight);
            $image->place($before, 'top-left', 0, 0);
        }

        if ($post->image_after_path && Storage::disk('public')->exists($post->image_after_path)) {
            $after = $this->manager->read(Storage::disk('public')->get($post->image_after_path));
            $after->cover($halfWidth, $imageHeight);
            $image->place($after, 'top-right', 0, 0);
        }

        // 2. Separator line between images (like in gabarit)
        $image->drawRectangle($halfWidth - 1, 0, function ($draw) use ($imageHeight) {
            $draw->size(2, $imageHeight);
            $draw->background('#FFFFFF');
        });

        // 3. Draw Bottom Banner using Brand Color
        $brandColor = $post->company->brand_color ?? '#F59E0B';
        $image->drawRectangle(0, $imageHeight, function ($draw) use ($bannerHeight, $brandColor) {
            $draw->size(1080, $bannerHeight);
            $draw->background($brandColor);
        });

        // 4. Draw labels "AVANT" and "APRÈS" in the banner (CLEAN - no background boxes as requested)
        $labelY = $imageHeight + ($bannerHeight / 2);
        
        $image->text('AVANT', 270, $labelY, function ($font) {
            $font->filename($this->fontPath);
            $font->size(40);
            $font->color('#FFFFFF');
            $font->align('center');
            $font->valign('middle');
        });

        $image->text('APRÈS', 1080 - 270, $labelY, function ($font) {
            $font->filename($this->fontPath);
            $font->size(40);
            $font->color('#FFFFFF');
            $font->align('center');
            $font->valign('middle');
        });
    }

    private function drawFaqElements($image)
    {
        $orange = '#F59E0B';
        
        // 1. Top Orange Border (thick)
        $image->drawRectangle(0, 0, function ($draw) use ($orange) {
            $draw->size(1080, 20);
            $draw->background($orange);
        });

        // 2. Small Orange Dash/Divider
        $image->drawRectangle(490, 250, function ($draw) use ($orange) {
            $draw->size(100, 10);
            $draw->background($orange);
        });

        // 3. Label "FAQ ARTISAN"
        $image->text('FAQ ARTISAN', 540, 320, function ($font) use ($orange) {
            $font->filename($this->fontPath);
            $font->size(35);
            $font->color($orange);
            $font->align('center');
            $font->valign('middle');
        });

        // 4. Decorative Icon (Question Mark in watermark)
        $image->text('?', 950, 950, function ($font) {
            $font->filename($this->fontPath);
            $font->size(300);
            $font->color('rgba(255, 255, 255, 0.05)');
            $font->align('center');
            $font->valign('middle');
        });
        
        // 5. Bottom border line
        $image->drawRectangle(100, 900, function ($draw) {
            $draw->size(880, 1);
            $draw->background('rgba(255, 255, 255, 0.1)');
        });
    }

    private function drawLifestyleElements($image, Post $post)
    {
        // 1. Fill canvas with the photo only (Raw style as requested)
        if ($post->image_before_path && Storage::disk('public')->exists($post->image_before_path)) {
            $photo = $this->manager->read(Storage::disk('public')->get($post->image_before_path));
            $photo->cover(1080, 1080);
            $image->place($photo, 'top-left', 0, 0);
        }
    }

    private function drawLabel($image, $x, $y, $text)
    {
        $image->drawRectangle($x - 60, $y - 20, function ($draw) {
            $draw->size(120, 40);
            $draw->background('#00000088');
        });

        $image->text($text, $x, $y, function ($font) {
            $font->filename($this->fontPath);
            $font->size(20);
            $font->color('#FFFFFF');
            $font->align('center');
            $font->valign('middle');
        });
    }

    private function drawStar($image, $centerX, $centerY, $outerRadius, $innerRadius, $color)
    {
        $points = [];
        for ($i = 0; $i < 5; $i++) {
            // Outer point
            $angle = deg2rad(-90 + ($i * 72));
            $points[] = $centerX + ($outerRadius * cos($angle));
            $points[] = $centerY + ($outerRadius * sin($angle));

            // Inner point
            $angle = deg2rad(-90 + ($i * 72) + 36);
            $points[] = $centerX + ($innerRadius * cos($angle));
            $points[] = $centerY + ($innerRadius * sin($angle));
        }

        $image->drawPolygon(function ($draw) use ($points, $color) {
            foreach (array_chunk($points, 2) as $point) {
                $draw->point((int) $point[0], (int) $point[1]);
            }
            $draw->background($color);
        });
    }

    private function drawText($image, $text, $topBound = 420, $bottomBound = 880, $fontSize = 50)
    {
        $text = '"' . mb_strtoupper(trim($text, '"')) . '"'; // Wrap with quotes and force uppercase
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';
        $maxCharsPerLine = 24; // Slightly more characters per line for better flow

        foreach ($words as $word) {
            if (strlen($currentLine . ' ' . $word) < $maxCharsPerLine) {
                $currentLine .= ($currentLine ? ' ' : '') . $word;
            } else {
                $lines[] = $currentLine;
                $currentLine = $word;
            }
        }
        $lines[] = $currentLine;

        // Positioning Constraints
        $lineHeight = 70;
        
        $totalTextHeight = count($lines) * $lineHeight;
        $availableHeight = $bottomBound - $topBound;
        
        // Center the whole block vertically in the available space
        $startY = $topBound + ($availableHeight - $totalTextHeight) / 2 + ($lineHeight / 2);

        $y = $startY;
        foreach ($lines as $line) {
            $image->text($line, 540, $y, function ($font) use ($fontSize) {
                if (file_exists($this->fontPath)) {
                    $font->filename($this->fontPath);
                    $font->size($fontSize); 
                }
                $font->color('#FFFFFF');
                $font->align('center');
                $font->valign('middle');
            });
            $y += $lineHeight;
        }
    }

    private function addLogo($image, $logoPath)
    {
        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            $logo = $this->manager->read(Storage::disk('public')->get($logoPath));
            $logo->scale(width: 150);
            $image->place($logo, 'bottom-right', 50, 50);
        }
    }
}
