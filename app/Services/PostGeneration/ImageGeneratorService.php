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
        $this->manager  = new ImageManager(new Driver());
        $this->fontPath = str_replace('\\', '/', storage_path('fonts/Montserrat-Bold.ttf'));
    }

    /**
     * Generate image(s) for a post.
     * Returns a single path (string) for single-image templates,
     * or an array of paths for carousel templates.
     */
    public function generate(Post $post): string|array
    {
        $template = $post->template;

        return match ($template) {
            'chantier_detail'   => $this->generateChantierDetail($post),
            'step_by_step'      => $this->generateStepByStep($post),
            'guide_pedagogique' => $this->generateGuidePedagogique($post),
            default             => $this->generateSingle($post),
        };
    }

    // -------------------------------------------------------------------------
    // Single-image templates
    // -------------------------------------------------------------------------

    private function generateSingle(Post $post): string
    {
        $company    = $post->company;
        $content    = $this->cleanText($post->refined_content);
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
        } elseif ($template === 'quality_focus') {
            $this->drawQualityFocusElements($image, $post, $company, $brandColor, $content);
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
    // Carousel: Chantier en Détail
    // -------------------------------------------------------------------------

    private function parseStructuredContent(string $content): array
    {
        $data = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($data['slides'])) {
            return $data;
        }
        return [
            'caption' => $content,
            'slides'  => []
        ];
    }

    private function generateChantierDetail(Post $post): array
    {
        $company    = $post->company;
        $decoded    = $this->parseStructuredContent($post->refined_content);
        $brandColor = $company->brand_color ?? '#F59E0B';
        $photos     = $post->extra_images ?? [];
        $slides     = [];

        // Slide 1 – Cover
        $coverImage = $this->manager->create(1080, 1080);
        if (!empty($photos[0]) && Storage::disk('public')->exists($photos[0])) {
            $photo = $this->manager->read(Storage::disk('public')->get($photos[0]));
            $photo->cover(1080, 1080);
            $coverImage->place($photo, 'top-left', 0, 0);
        } else {
            $coverImage->fill('#1E293B');
        }
        $coverImage->drawRectangle(0, 540, function ($draw) {
            $draw->size(1080, 540);
            $draw->background('rgba(0,0,0,0.6)');
        });
        
        $titleText = !empty($decoded['slides'][0]) 
            ? $this->cleanText($decoded['slides'][0]) 
            : $this->cleanText(mb_substr($decoded['caption'], 0, 60));

        $this->drawTextRaw($coverImage, strtoupper($titleText), 540, 780, 56, '#FFFFFF', 24);
        $this->drawBottomBand($coverImage, $company, $brandColor);
        $this->addLogo($coverImage, $company->logo_path);
        $path = 'posts/' . $post->id . '_slide_1.png';
        Storage::disk('public')->put($path, $coverImage->toPng());
        $slides[] = $path;

        // Slides 2..N – detail photos
        foreach (array_slice($photos, 1, 4) as $i => $photoPath) {
            $slideImage = $this->manager->create(1080, 1080);
            if (Storage::disk('public')->exists($photoPath)) {
                $photo = $this->manager->read(Storage::disk('public')->get($photoPath));
                $photo->cover(1080, 1080);
                $slideImage->place($photo, 'top-left', 0, 0);
            } else {
                $slideImage->fill('#1E293B');
            }
            
            // Draw specific slide text if available
            if (!empty($decoded['slides'][$i + 1])) {
                $slideImage->drawRectangle(0, 800, function ($draw) {
                    $draw->size(1080, 160);
                    $draw->background('rgba(0,0,0,0.5)');
                });
                $this->drawTextRaw($slideImage, strtoupper($this->cleanText($decoded['slides'][$i + 1])), 800, 960, 38, '#FFFFFF', 30);
            }

            $this->drawBottomBand($slideImage, $company, $brandColor);
            $this->addLogo($slideImage, $company->logo_path);
            $path = 'posts/' . $post->id . '_slide_' . ($i + 2) . '.png';
            Storage::disk('public')->put($path, $slideImage->toPng());
            $slides[] = $path;
        }

        return $slides;
    }

    private function generateStepByStep(Post $post): array
    {
        $company    = $post->company;
        $decoded    = $this->parseStructuredContent($post->refined_content);
        $brandColor = $company->brand_color ?? '#F59E0B';
        $photos     = $post->extra_images ?? [];
        $slides     = [];

        // Slide 1 – Cover
        $coverImage = $this->manager->create(1080, 1080);
        $coverImage->fill($brandColor);
        $coverImage->drawRectangle(0, 0, function ($draw) {
            $draw->size(12, 1080);
            $draw->background('rgba(0,0,0,0.25)');
        });
        $coverImage->text('ÉTAPE', 540, 380, function ($font) {
            $font->filename($this->fontPath);
            $font->size(60);
            $font->color('rgba(255,255,255,0.25)');
            $font->align('center');
            $font->valign('middle');
        });
        $coverImage->text('PAR ÉTAPE', 540, 460, function ($font) {
            $font->filename($this->fontPath);
            $font->size(60);
            $font->color('rgba(255,255,255,0.25)');
            $font->align('center');
            $font->valign('middle');
        });

        $titleText = !empty($decoded['slides'][0]) 
            ? strtoupper($this->cleanText($decoded['slides'][0])) 
            : 'NOS ÉTAPES DE TRAVAIL';

        $this->drawTextRaw($coverImage, $titleText, 540, 700, 56, '#FFFFFF', 24);
        $this->drawBottomBand($coverImage, $company, '#000000');
        $this->addLogo($coverImage, $company->logo_path);
        $path = 'posts/' . $post->id . '_slide_1.png';
        Storage::disk('public')->put($path, $coverImage->toPng());
        $slides[] = $path;

        // Slides 2..N – one per photo/step
        foreach ($photos as $idx => $photoPath) {
            $slideImage = $this->manager->create(1080, 1080);
            if (Storage::disk('public')->exists($photoPath)) {
                $photo = $this->manager->read(Storage::disk('public')->get($photoPath));
                $photo->cover(1080, 1080);
                $slideImage->place($photo, 'top-left', 0, 0);
            } else {
                $slideImage->fill('#1E293B');
            }
            $slideImage->drawRectangle(0, 880, function ($draw) {
                $draw->size(1080, 200);
                $draw->background('rgba(0,0,0,0.72)');
            });
            $this->drawCircle($slideImage, 100, 100, 70, $brandColor);
            $slideImage->text((string)($idx + 1), 100, 100, function ($font) {
                $font->filename($this->fontPath);
                $font->size(56);
                $font->color('#FFFFFF');
                $font->align('center');
                $font->valign('middle');
            });

            $stepLabel = isset($decoded['slides'][$idx + 1]) 
                ? strtoupper($this->cleanText($decoded['slides'][$idx + 1])) 
                : ('ÉTAPE ' . ($idx + 1));
            
            $stepLabel = preg_replace('/^ÉTAPE\s*\d+\s*[:\-]?\s*/iu', '', $stepLabel);
            $this->drawTextRaw($slideImage, $stepLabel, 900, 960, 38, '#FFFFFF', 28);
            $this->addLogo($slideImage, $company->logo_path);
            $path = 'posts/' . $post->id . '_slide_' . ($idx + 2) . '.png';
            Storage::disk('public')->put($path, $slideImage->toPng());
            $slides[] = $path;
        }

        return $slides;
    }

    private function generateGuidePedagogique(Post $post): array
    {
        $company    = $post->company;
        $decoded    = $this->parseStructuredContent($post->refined_content);
        $brandColor = $company->brand_color ?? '#F59E0B';
        $slides     = [];

        $aiSlides = !empty($decoded['slides']) ? $decoded['slides'] : [];

        // --- Slide 1: Accroche ---
        $slide1 = $this->manager->create(1080, 1080);
        $slide1->fill($brandColor);

        $question = !empty($aiSlides[0]) 
            ? strtoupper($this->cleanText($aiSlides[0])) 
            : 'CONSEIL D\'EXPERT';
            
        $this->drawTextRaw($slide1, $question, 540, 540, 68, '#FFFFFF', 20);

        $slide1->text('CONSEIL D\'EXPERT', 540, 220, function ($font) {
            $font->filename($this->fontPath);
            $font->size(32);
            $font->color('rgba(255,255,255,0.7)');
            $font->align('center');
            $font->valign('middle');
        });
        $this->addLogo($slide1, $company->logo_path);
        $path = 'posts/' . $post->id . '_slide_1.png';
        Storage::disk('public')->put($path, $slide1->toPng());
        $slides[] = $path;

        // --- Middle slides ---
        $middleSlides = array_slice($aiSlides, 1);
        // If the AI didn't provide enough slides, we fallback to splitting the caption
        if (count($middleSlides) < 2 && !empty($decoded['caption'])) {
            $middleSlides = $this->splitTextIntoSlides($decoded['caption'], 140);
        }

        foreach ($middleSlides as $ci => $slideContent) {
            $slide = $this->manager->create(1080, 1080);
            $slide->fill('#F8FAFC');
            $slide->drawRectangle(0, 0, function ($draw) use ($brandColor) {
                $draw->size(1080, 12);
                $draw->background($brandColor);
            });
            $slideNum = $ci + 2;
            $slide->drawRectangle(80, 120, function ($draw) use ($brandColor) {
                $draw->size(60, 60);
                $draw->background($brandColor);
            });
            $slide->text((string)$slideNum, 110, 150, function ($font) {
                $font->filename($this->fontPath);
                $font->size(32);
                $font->color('#FFFFFF');
                $font->align('center');
                $font->valign('middle');
            });
            $this->drawTextRaw($slide, strtoupper($this->cleanText($slideContent)), 540, 540, 52, '#1E293B', 24);
            $this->drawBottomBand($slide, $company, '#1E293B');
            $this->addLogo($slide, $company->logo_path);

            $path = 'posts/' . $post->id . '_slide_' . $slideNum . '.png';
            Storage::disk('public')->put($path, $slide->toPng());
            $slides[] = $path;
        }

        // --- Last slide: CTA ---
        $lastSlideNum = count($slides) + 1;
        $cta = $this->manager->create(1080, 1080);
        $cta->fill('#1E293B');
        $cta->drawRectangle(0, 0, function ($draw) use ($brandColor) {
            $draw->size(1080, 16);
            $draw->background($brandColor);
        });
        $cta->drawRectangle(0, 1064, function ($draw) use ($brandColor) {
            $draw->size(1080, 16);
            $draw->background($brandColor);
        });
        if ($company->logo_path && Storage::disk('public')->exists($company->logo_path)) {
            $logo = $this->manager->read(Storage::disk('public')->get($company->logo_path));
            $logo->scale(width: 200);
            $cta->place($logo, 'center', 0, -80);
        }
        $cta->text('UN PROJET EN TÊTE ?', 540, 620, function ($font) {
            $font->filename($this->fontPath);
            $font->size(48);
            $font->color('#FFFFFF');
            $font->align('center');
            $font->valign('middle');
        });
        $cta->text('Contactez-nous pour un devis gratuit', 540, 700, function ($font) {
            $font->filename($this->fontPath);
            $font->size(30);
            $font->color('rgba(255,255,255,0.7)');
            $font->align('center');
            $font->valign('middle');
        });
        $cta->text($this->companyLabel($company), 540, 820, function ($font) {
            $font->filename($this->fontPath);
            $font->size(26);
            $font->color('#F59E0B');
            $font->align('center');
            $font->valign('middle');
        });
        $path = 'posts/' . $post->id . '_slide_' . $lastSlideNum . '.png';
        Storage::disk('public')->put($path, $cta->toPng());
        $slides[] = $path;

        return $slides;
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

    private function drawCompanyBadge($image, $company): void
    {
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

        $image->drawRectangle($halfWidth - 1, 0, function ($draw) use ($imageHeight) {
            $draw->size(2, $imageHeight);
            $draw->background('#FFFFFF');
        });

        $image->drawRectangle(0, $imageHeight, function ($draw) use ($bannerHeight, $brandColor) {
            $draw->size(1080, $bannerHeight);
            $draw->background($brandColor);
        });

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
        $image->drawRectangle(0, 0, function ($draw) use ($brandColor) {
            $draw->size(1080, 20);
            $draw->background($brandColor);
        });

        $image->drawRectangle(490, 250, function ($draw) use ($brandColor) {
            $draw->size(100, 10);
            $draw->background($brandColor);
        });

        $image->text('FAQ ARTISAN', 540, 320, function ($font) use ($brandColor) {
            $font->filename($this->fontPath);
            $font->size(35);
            $font->color($brandColor);
            $font->align('center');
            $font->valign('middle');
        });

        $image->text('?', 950, 950, function ($font) {
            $font->filename($this->fontPath);
            $font->size(300);
            $font->color('rgba(255, 255, 255, 0.05)');
            $font->align('center');
            $font->valign('middle');
        });

        $image->drawRectangle(100, 900, function ($draw) {
            $draw->size(880, 1);
            $draw->background('rgba(255, 255, 255, 0.1)');
        });
    }

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

    private function drawLifestyleOverlay($image, $company, string $brandColor): void
    {
        $image->drawRectangle(0, 968, function ($draw) {
            $draw->size(1080, 112);
            $draw->background('rgba(0, 0, 0, 0.55)');
        });

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

    /**
     * Focus Qualité: full-bleed photo + "SÉLECTION PRO" badge top-right + bottom black band.
     */
    private function drawQualityFocusElements($image, Post $post, $company, string $brandColor, string $content): void
    {
        // Full-bleed photo
        $photoPath = $post->image_before_path;
        if ($photoPath && Storage::disk('public')->exists($photoPath)) {
            $photo = $this->manager->read(Storage::disk('public')->get($photoPath));
            $photo->cover(1080, 1080);
            $image->place($photo, 'top-left', 0, 0);
        } else {
            $image->fill('#1E293B');
        }

        // "SÉLECTION PRO" badge top-right
        $image->drawRectangle(780, 40, function ($draw) use ($brandColor) {
            $draw->size(260, 60);
            $draw->background($brandColor);
        });
        $image->text('SÉLECTION PRO', 910, 70, function ($font) {
            $font->filename($this->fontPath);
            $font->size(22);
            $font->color('#FFFFFF');
            $font->align('center');
            $font->valign('middle');
        });

        // Bottom dark band
        $image->drawRectangle(0, 880, function ($draw) {
            $draw->size(1080, 200);
            $draw->background('rgba(0,0,0,0.75)');
        });
        // Brand accent line
        $image->drawRectangle(0, 878, function ($draw) use ($brandColor) {
            $draw->size(1080, 4);
            $draw->background($brandColor);
        });
        // Content text
        $this->drawTextRaw($image, mb_strtoupper($content), 900, 1060, 28, '#FFFFFF', 36);
        // Company name
        $image->text($this->companyLabel($company), 540, 896, function ($font) {
            $font->filename($this->fontPath);
            $font->size(22);
            $font->color('rgba(255,255,255,0.65)');
            $font->align('center');
            $font->valign('middle');
        });
    }

    // -------------------------------------------------------------------------
    // Shared helpers
    // -------------------------------------------------------------------------

    /**
     * Black bottom band with company label — used for carousel slides.
     */
    private function drawBottomBand($image, $company, string $bgColor = '#000000'): void
    {
        // Dark premium band at the bottom
        $image->drawRectangle(0, 960, function ($draw) use ($bgColor) {
            $draw->size(1080, 120);
            $draw->background($bgColor === '#000000' ? 'rgba(0,0,0,0.85)' : $bgColor);
        });
        $image->text($this->companyLabel($company), 540, 1020, function ($font) {
            $font->filename($this->fontPath);
            $font->size(28);
            $font->color('rgba(255,255,255,0.7)');
            $font->align('center');
            $font->valign('middle');
        });
    }

    /**
     * Intelligently split long text into slides based on max length.
     * Tries to split on sentence boundaries or newlines first.
     */
    private function splitTextIntoSlides(string $text, int $maxChars = 200, int $maxSlides = 10): array
    {
        // Normalize newlines
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        
        // Priority split: single newlines (paragraphs or bullet points)
        $paragraphs = array_values(array_filter(explode("\n", $text), fn($p) => trim($p) !== ''));
        
        $slides = [];
        $currentSlide = "";

        foreach ($paragraphs as $para) {
            $para = trim($para);
            if ($para === "") continue;

            // If a single paragraph is way too long, split it by sentence
            if (mb_strlen($para) > $maxChars) {
                // Approximate sentence split
                $sentences = preg_split('/(?<=[.!?])\s+/', $para, -1, PREG_SPLIT_NO_EMPTY);
                
                // FALLBACK: if it's still one big block, split by words
                if (count($sentences) === 1 && mb_strlen($sentences[0]) > $maxChars) {
                    $words = explode(' ', $sentences[0]);
                    $tempChunks = [];
                    $currentChunk = "";
                    foreach ($words as $word) {
                        if (mb_strlen($currentChunk . " " . $word) <= $maxChars) {
                            $currentChunk .= ($currentChunk === "" ? "" : " ") . $word;
                        } else {
                            if ($currentChunk !== "") $tempChunks[] = $currentChunk;
                            $currentChunk = $word;
                        }
                    }
                    if ($currentChunk !== "") $tempChunks[] = $currentChunk;
                    $sentences = $tempChunks;
                }

                foreach ($sentences as $sentence) {
                    if (mb_strlen($currentSlide . "\n" . $sentence) <= $maxChars) {
                        $currentSlide .= ($currentSlide === "" ? "" : "\n") . $sentence;
                    } else {
                        if ($currentSlide !== "") $slides[] = trim($currentSlide);
                        $currentSlide = $sentence;
                    }
                }
            } else {
                // If adding this para exceeds maxChars, start a new slide
                if (mb_strlen($currentSlide . "\n" . $para) > $maxChars && $currentSlide !== "") {
                    $slides[] = trim($currentSlide);
                    $currentSlide = $para;
                } else {
                    $currentSlide .= ($currentSlide === "" ? "" : "\n") . $para;
                }
            }
        }
        
        if ($currentSlide !== "") $slides[] = trim($currentSlide);

        return array_slice($slides, 0, $maxSlides);
    }

    private function drawCircle($image, int $cx, int $cy, int $radius, string $color): void
    {
        $image->drawEllipse($cx, $cy, function ($draw) use ($radius, $color) {
            $draw->size($radius * 2, $radius * 2);
            $draw->background($color);
        });
    }

    private function companyLabel($company): string
    {
        $name = $company->name ?? '';
        $city = $company->city ?? '';

        $label = ($name && $city) ? $name . '  ·  ' . $city : $name;
        
        return mb_strtoupper($this->cleanText($label));
    }

    /**
     * Draw text with automatic word-wrap, vertically centered in the given bounds.
     * (Original method — adds quotes, uppercase.)
     */
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

    /**
     * Draw arbitrary text with word-wrap, no quotes added.
     */
    private function drawTextRaw($image, string $text, int $topBound, int $bottomBound, int $fontSize, string $color = '#FFFFFF', int $maxChars = 24): void
    {
        $words   = explode(' ', $text);
        $lines   = [];
        $current = '';

        foreach ($words as $word) {
            if (mb_strlen($current . ' ' . $word) <= $maxChars) {
                $current .= ($current ? ' ' : '') . $word;
            } else {
                if ($current !== '') $lines[] = $current;
                $current = $word;
            }
        }
        if ($current !== '') $lines[] = $current;

        $lineHeight      = intval($fontSize * 1.35);
        $totalTextHeight = count($lines) * $lineHeight;
        $availableHeight = $bottomBound - $topBound;
        $startY          = $topBound + ($availableHeight - $totalTextHeight) / 2 + ($lineHeight / 2);

        $y = $startY;
        foreach ($lines as $line) {
            $image->text($line, 540, (int)$y, function ($font) use ($fontSize, $color) {
                if (file_exists($this->fontPath)) {
                    $font->filename($this->fontPath);
                    $font->size($fontSize);
                }
                $font->color($color);
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

    /**
     * Remove emojis, markdown formatting and accents from text.
     */
    private function cleanText(?string $text): string
    {
        if (!$text) return '';

        // Remove markdown bold/italic symbols
        $text = str_replace(['**', '__', '*', '_'], '', $text);

        // Remove Emojis and special symbols
        $text = preg_replace('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{1F900}-\x{1F9FF}\x{1F1E6}-\x{1F1FF}\x{1F3FB}-\x{1F3FF}\x{200D}\x{200B}\x{FE0F}]/u', '', $text);

        // Remove accents (convert é to e, à to a, etc.)
        $map = [
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
            'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
            'ß' => 'ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
            'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
            'ÿ' => 'y'
        ];

        $text = strtr($text, $map);

        return trim($text);
    }
}
