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
        $this->fontPath = str_replace('\\', '/', storage_path('fonts/Montserrat-Bold.ttf'));
    }

    /**
     * Generate a 1080x1080 post image.
     */
    public function generate(Post $post): string
    {
        $company    = $post->company;
        $content    = $post->refined_content;
        $template   = $post->template;
        $brandColor = $company->brand_color ?? '#F59E0B';

        $image = $this->manager->create(1080, 1080);

        if ($template === 'review') {
            $image->fill($brandColor);
            $this->drawReviewElements($image);
            $this->drawText($image, $content, 320, 900, 58);
            $this->drawCompanyBadge($image, $company);
        } elseif ($template === 'faq') {
            $image->fill('#0f172a');
            $this->drawFaqElements($image, $brandColor);
            $this->drawText($image, $content, 380, 840, 55);
            $this->drawFaqCompanyName($image, $company, $brandColor);
        } elseif ($template === 'lifestyle') {
            $this->drawLifestyleElements($image, $post);
            $this->drawLifestyleOverlay($image, $company, $brandColor);
        } elseif ($template === 'project') {
            $image->fill('#FFFFFF');
            $this->drawProjectElements($image, $post, $company, $brandColor);
        } else {
            $image->fill($brandColor);
            $this->drawText($image, $content, 200, 880, 50);
        }

        $this->addLogo($image, $company->logo_path);

        $fileName = 'posts/' . $post->id . '_generated.png';
        Storage::disk('public')->put($fileName, $image->toPng());

        return $fileName;
    }

    // -------------------------------------------------------------------------
    // Template drawers
    // -------------------------------------------------------------------------

    private function drawReviewElements($image): void
    {
        $starY       = 230;
        $starSpacing = 90;
        $startX      = 540 - (($starSpacing * 4) / 2);

        for ($i = 0; $i < 5; $i++) {
            $this->drawStar($image, $startX + ($i * $starSpacing), $starY, 35, 15, '#FACC15');
        }
    }

    /**
     * Semi-transparent dark strip at the very bottom of the review template.
     * Shows company name + city.
     */
    private function drawCompanyBadge($image, $company): void
    {
        // Dark overlay strip (960 → 1080)
        $image->drawRectangle(0, 960, function ($draw) {
            $draw->size(1080, 120);
            $draw->background('rgba(0, 0, 0, 0.35)');
        });

        $label = $this->companyLabel($company);

        $image->text($label, 540, 1020, function ($font) {
            $font->filename($this->fontPath);
            $font->size(30);
            $font->color('#FFFFFF');
            $font->align('center');
            $font->valign('middle');
        });
    }

    private function drawProjectElements($image, Post $post, $company, string $brandColor): void
    {
        $bannerHeight = 150;
        $imageHeight  = 1080 - $bannerHeight;
        $halfWidth    = 540;

        // Photos side-by-side
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

        // White separator line
        $image->drawRectangle($halfWidth - 1, 0, function ($draw) use ($imageHeight) {
            $draw->size(2, $imageHeight);
            $draw->background('#FFFFFF');
        });

        // Brand color banner
        $image->drawRectangle(0, $imageHeight, function ($draw) use ($bannerHeight, $brandColor) {
            $draw->size(1080, $bannerHeight);
            $draw->background($brandColor);
        });

        // AVANT / APRÈS at ~60% of the banner height
        $labelY = $imageHeight + intval($bannerHeight * 0.62);

        $image->text('AVANT', 162, $labelY, function ($font) {
            $font->filename($this->fontPath);
            $font->size(36);
            $font->color('#FFFFFF');
            $font->align('center');
            $font->valign('middle');
        });

        $image->text('APRÈS', 918, $labelY, function ($font) {
            $font->filename($this->fontPath);
            $font->size(36);
            $font->color('#FFFFFF');
            $font->align('center');
            $font->valign('middle');
        });

        // Company name centered at ~28% of the banner height
        $companyY = $imageHeight + intval($bannerHeight * 0.28);
        $label    = $this->companyLabel($company);

        $image->text($label, 540, $companyY, function ($font) {
            $font->filename($this->fontPath);
            $font->size(22);
            $font->color('rgba(255, 255, 255, 0.85)');
            $font->align('center');
            $font->valign('middle');
        });
    }

    private function drawFaqElements($image, string $brandColor): void
    {
        // Top border
        $image->drawRectangle(0, 0, function ($draw) use ($brandColor) {
            $draw->size(1080, 20);
            $draw->background($brandColor);
        });

        // Small dash/divider
        $image->drawRectangle(490, 250, function ($draw) use ($brandColor) {
            $draw->size(100, 10);
            $draw->background($brandColor);
        });

        // Label "FAQ ARTISAN"
        $image->text('FAQ ARTISAN', 540, 320, function ($font) use ($brandColor) {
            $font->filename($this->fontPath);
            $font->size(35);
            $font->color($brandColor);
            $font->align('center');
            $font->valign('middle');
        });

        // Watermark question mark
        $image->text('?', 950, 950, function ($font) {
            $font->filename($this->fontPath);
            $font->size(300);
            $font->color('rgba(255, 255, 255, 0.05)');
            $font->align('center');
            $font->valign('middle');
        });

        // Bottom line
        $image->drawRectangle(100, 900, function ($draw) {
            $draw->size(880, 1);
            $draw->background('rgba(255, 255, 255, 0.1)');
        });
    }

    /**
     * Company name at the bottom of the FAQ template, tinted in brand color.
     */
    private function drawFaqCompanyName($image, $company, string $brandColor): void
    {
        $label = $this->companyLabel($company);

        $image->text($label, 540, 960, function ($font) use ($brandColor) {
            $font->filename($this->fontPath);
            $font->size(26);
            $font->color($brandColor);
            $font->align('center');
            $font->valign('middle');
        });
    }

    private function drawLifestyleElements($image, Post $post): void
    {
        if ($post->image_before_path && Storage::disk('public')->exists($post->image_before_path)) {
            $photo = $this->manager->read(Storage::disk('public')->get($post->image_before_path));
            $photo->cover(1080, 1080);
            $image->place($photo, 'top-left', 0, 0);
        }
    }

    /**
     * Brand-colored accent line + dark overlay with company name at the bottom
     * of the lifestyle template.
     */
    private function drawLifestyleOverlay($image, $company, string $brandColor): void
    {
        // Dark gradient strip
        $image->drawRectangle(0, 968, function ($draw) {
            $draw->size(1080, 112);
            $draw->background('rgba(0, 0, 0, 0.55)');
        });

        // Brand color accent line
        $image->drawRectangle(0, 966, function ($draw) use ($brandColor) {
            $draw->size(1080, 4);
            $draw->background($brandColor);
        });

        $label = $this->companyLabel($company);

        $image->text($label, 540, 1024, function ($font) {
            $font->filename($this->fontPath);
            $font->size(30);
            $font->color('#FFFFFF');
            $font->align('center');
            $font->valign('middle');
        });
    }

    // -------------------------------------------------------------------------
    // Shared helpers
    // -------------------------------------------------------------------------

    private function companyLabel($company): string
    {
        $name = $company->name ?? '';
        $city = $company->city ?? '';

        return ($name && $city) ? $name . '  ·  ' . mb_strtoupper($city) : mb_strtoupper($name);
    }

    private function drawText($image, $text, $topBound = 420, $bottomBound = 880, $fontSize = 50): void
    {
        $text    = '"' . mb_strtoupper(trim($text, '"')) . '"';
        $words   = explode(' ', $text);
        $lines   = [];
        $current = '';
        $maxChars = 24;

        foreach ($words as $word) {
            if (strlen($current . ' ' . $word) < $maxChars) {
                $current .= ($current ? ' ' : '') . $word;
            } else {
                $lines[]  = $current;
                $current  = $word;
            }
        }
        $lines[] = $current;

        $lineHeight      = 70;
        $totalTextHeight = count($lines) * $lineHeight;
        $availableHeight = $bottomBound - $topBound;
        $startY          = $topBound + ($availableHeight - $totalTextHeight) / 2 + ($lineHeight / 2);

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

    private function drawStar($image, $centerX, $centerY, $outerRadius, $innerRadius, $color): void
    {
        $points = [];
        for ($i = 0; $i < 5; $i++) {
            $angle    = deg2rad(-90 + ($i * 72));
            $points[] = $centerX + ($outerRadius * cos($angle));
            $points[] = $centerY + ($outerRadius * sin($angle));

            $angle    = deg2rad(-90 + ($i * 72) + 36);
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

    private function addLogo($image, $logoPath): void
    {
        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            $logo = $this->manager->read(Storage::disk('public')->get($logoPath));
            $logo->scale(width: 130);
            $image->place($logo, 'bottom-right', 40, 40);
        }
    }
}
