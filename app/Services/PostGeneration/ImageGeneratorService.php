<?php

namespace App\Services\PostGeneration;

use App\Models\Post;
use App\Services\PostGeneration\TextZoneSpec;
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
    public function generate(Post $post, int $width = 1080, int $height = 1080): string|array
    {
        $template = $post->template;

        return match ($template) {
            'chantier_detail'   => $this->generateChantierDetail($post, $width, $height),
            'step_by_step'      => $this->generateStepByStep($post, $width, $height),
            'guide_pedagogique' => $this->generateGuidePedagogique($post, $width, $height),
            'custom'            => $this->generateCustomTemplate($post, $width, $height),
            default             => $this->generateSingle($post, $width, $height),
        };
    }

    /**
     * Generate Facebook-optimised images from the existing Instagram images.
     * Single posts → 1200×630 (landscape link-style).
     * Carousels → 1200×1200 (square album-style).
     *
     * Expects the Instagram images to already exist on disk.
     */
    public function generateForFacebook(Post $post): string|array
    {
        $igSlides = $post->slides ?? [];

        // If we want a separate file for Facebook (e.g. for tracking or different filenames), 
        // we can still call generate with 1080x1080. 
        // Or simply resize the existing ones without scaling up.
        
        if (count($igSlides) > 1) {
            $fbSlides = [];
            foreach ($igSlides as $i => $igPath) {
                $fbSlides[] = $this->resizeForFacebook($igPath, $post->id, $i, true);
            }
            return $fbSlides;
        }

        return $this->generate($post, 1080, 1080);
    }

    /**
     * Read an existing Instagram image and produce a Facebook-sized version.
     * - Single posts: 1200×630 (centered with brand-color letterbox)
     * - Carousels: 1200×1200 (square, same as IG but higher res)
     */
    private function resizeForFacebook(string $igPath, int $postId, int $index, bool $isCarousel): string
    {
        $disk = Storage::disk('public');

        if (!$disk->exists($igPath)) {
            throw new \RuntimeException("Source image not found: {$igPath}");
        }

        $source = $this->manager->read($disk->get($igPath));

        // Just ensure it's 1080x1080 (already should be)
        if ($source->width() !== 1080 || $source->height() !== 1080) {
            $source->cover(1080, 1080);
        }

        $fileName = $isCarousel ? "posts/{$postId}_fb_{$index}.jpg" : "posts/{$postId}_fb.jpg";
        $disk->put($fileName, $source->toJpeg(quality: 90));

        return $fileName;
    }

    // -------------------------------------------------------------------------
    // Single-image templates
    // -------------------------------------------------------------------------

    /**
     * Extract the short text meant for the image from the structured refined_content.
     * Falls back gracefully so old posts (plain string) still work.
     * Strips hashtags and enforces a hard char limit as a safety net.
     */
    private function extractImageText(Post $post, int $maxChars = 80): string
    {
        $data = json_decode($post->refined_content, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            // Use image_text when present, otherwise a truncated caption
            $raw = $data['image_text'] ?? mb_substr($data['caption'] ?? '', 0, $maxChars);
        } else {
            // Legacy plain-text fallback
            $raw = $post->refined_content;
        }

        // Strip any hashtags that sneaked through
        $raw = preg_replace('/#\S+/', '', $raw);

        // Hard truncate + clean
        return $this->cleanText(mb_substr(trim($raw), 0, $maxChars));
    }

    private function generateSingle(Post $post, int $width = 1080, int $height = 1080): string
    {
        $company    = $post->company;
        $template   = $post->template;
        $brandColor = $company->brand_color ?? '#F59E0B';

        $image = $this->manager->create($width, $height);

        if ($template === 'review') {
            $imageText = $this->extractImageText($post, 70);
            $image->fill($brandColor);
            $this->drawReviewElements($image);
            $this->drawText($image, $imageText, 320, 900, 58);
            $this->drawCompanyBadge($image, $company);
        } elseif ($template === 'faq') {
            $imageText = $this->extractImageText($post, 75);
            $image->fill('#0f172a');
            $this->drawFaqElements($image, $brandColor);
            $this->drawText($image, $imageText, 380, 840, 55);
            $this->drawFaqCompanyName($image, $company, $brandColor);
        } elseif ($template === 'lifestyle') {
            $this->drawLifestyleElements($image, $post);
            $this->drawLifestyleOverlay($image, $company, $brandColor);
        } elseif ($template === 'project') {
            $image->fill('#FFFFFF');
            $this->drawProjectElements($image, $post, $company, $brandColor);
        } elseif ($template === 'quality_focus') {
            $imageText = $this->extractImageText($post, 55);
            $this->drawQualityFocusElements($image, $post, $company, $brandColor, $imageText);
        } elseif ($template === 'conseil_saisonnier') {
            $imageText = $this->extractImageText($post, 85);
            $this->drawConseilSaisonnierElements($image, $company, $brandColor, $imageText);
        } elseif ($template === 'erreur_a_eviter') {
            $imageText = $this->extractImageText($post, 75);
            $this->drawErreurAEviterElements($image, $company, $brandColor, $imageText);
        } else {
            $imageText = $this->extractImageText($post, 80);
            $image->fill($brandColor);
            $this->drawText($image, $imageText, 200, 880, 50);
        }

        $this->addLogo($image, $company->logo_path);

        $fileName = 'posts/' . $post->id . '_generated.jpg';
        Storage::disk('public')->put($fileName, $image->toJpeg(quality: 90));

        return $fileName;
    }

    // -------------------------------------------------------------------------
    // Custom (user-created) templates — 3 visual styles
    // -------------------------------------------------------------------------

    private function generateCustomTemplate(Post $post, int $width = 1080, int $height = 1080): string|array
    {
        $company     = $post->company;
        $brandColor  = $company->brand_color ?? '#F59E0B';
        $photoMode   = $post->template_options['custom_photo_mode'] ?? 'none';
        $visualStyle = $post->template_options['custom_visual_style'] ?? 'dark_branded';
        $layoutType  = $post->template_options['custom_layout_type']  ?? 'standard';

        $templateName = $post->template_options['custom_template_name'] ?? '';

        // ── Photo as background (single) ──────────────────────────────────────
        if ($photoMode === 'single') {
            $image = $this->manager->create($width, $height);

            if ($post->image_before_path && Storage::disk('public')->exists($post->image_before_path)) {
                $photo = $this->manager->read(Storage::disk('public')->get($post->image_before_path));
                $photo->cover($width, $height);
                $image->place($photo, 'top-left', 0, 0);
            } else {
                $image->fill('#1E293B');
            }

            // Semi-transparent header area so template label is readable
            $image->drawRectangle(0, 0, function ($draw) use ($width) {
                $draw->size($width, 140);
                $draw->background('rgba(0,0,0,0.55)');
            });

            // Brand color top bar
            $image->drawRectangle(0, 0, function ($draw) use ($brandColor, $width) {
                $draw->size($width, 16);
                $draw->background($brandColor);
            });

            // Short accent line left-aligned (mirrors card layout)
            $image->drawRectangle(100, 55, function ($draw) {
                $draw->size(80, 6);
                $draw->background('rgba(255,255,255,0.60)');
            });

            // Template name label — left-aligned to match card
            if ($templateName) {
                $image->text(mb_strtoupper($this->cleanText($templateName)), 100, 105, function ($font) use ($brandColor) {
                    $font->filename($this->fontPath);
                    $font->size(30);
                    $font->color($brandColor);
                    $font->align('left');
                    $font->valign('middle');
                });
            }

            $this->drawUnifiedBand($image, $company, $brandColor);
            $this->addLogo($image, $company->logo_path);

            $fileName = 'posts/' . $post->id . '_generated.jpg';
            Storage::disk('public')->put($fileName, $image->toJpeg(quality: 90));
            return $fileName;
        }

        // ── Photos as carousel backgrounds ────────────────────────────────────
        if ($photoMode === 'carousel') {
            $photos = $post->extra_images ?? [];
            $slides = [];

            foreach ($photos as $idx => $photoPath) {
                $slideImage = $this->manager->create($width, $height);

                if (Storage::disk('public')->exists($photoPath)) {
                    $photo = $this->manager->read(Storage::disk('public')->get($photoPath));
                    $photo->cover($width, $height);
                    $slideImage->place($photo, 'top-left', 0, 0);
                } else {
                    $slideImage->fill('#1E293B');
                }

                // Header overlay
                $slideImage->drawRectangle(0, 0, function ($draw) use ($width, $height) {
                    $draw->size($width, (int)($height * 0.13));
                    $draw->background('rgba(0,0,0,0.55)');
                });
                $slideImage->drawRectangle(0, 0, function ($draw) use ($brandColor, $width, $height) {
                    $draw->size($width, (int)($height * 0.015));
                    $draw->background($brandColor);
                });
                $slideImage->drawRectangle((int)($width * 0.092), (int)($height * 0.051), function ($draw) use ($width, $height) {
                    $draw->size((int)($width * 0.074), (int)($height * 0.005));
                    $draw->background('rgba(255,255,255,0.60)');
                });
                if ($templateName) {
                    $slideImage->text(mb_strtoupper($this->cleanText($templateName)), (int)($width * 0.092), (int)($height * 0.097), function ($font) use ($brandColor, $height) {
                        $font->filename($this->fontPath);
                        $font->size((int)($height * 0.028));
                        $font->color($brandColor);
                        $font->align('left');
                        $font->valign('middle');
                    });
                }

                $this->drawUnifiedBand($slideImage, $company, $brandColor);
                $this->addLogo($slideImage, $company->logo_path);

                $path = 'posts/' . $post->id . '_slide_' . ($idx + 1) . '.jpg';
                Storage::disk('public')->put($path, $slideImage->toJpeg(quality: 90));
                $slides[] = $path;
            }

            return $slides ?: $this->generateCustomNoPhoto($post, $company, $brandColor, $visualStyle, 'standard', $width, $height);
        }

        // ── No photo — branded background with chosen layout ─────────────────
        return $this->generateCustomNoPhoto($post, $company, $brandColor, $visualStyle, $layoutType, $width, $height);
    }

    private function generateCustomNoPhoto(Post $post, $company, string $brandColor, string $visualStyle, string $layoutType = 'standard', int $width = 1080, int $height = 1080): string
    {
        // Dispatch to the appropriate layout renderer
        $fileName = 'posts/' . $post->id . '_generated.jpg';

        return match ($layoutType) {
            'centered'   => $this->renderLayoutCentered($post, $company, $brandColor, $visualStyle, $fileName, $width, $height),
            'quote'      => $this->renderLayoutQuote($post, $company, $brandColor, $visualStyle, $fileName, $width, $height),
            'bold_title' => $this->renderLayoutBoldTitle($post, $company, $brandColor, $visualStyle, $fileName, $width, $height),
            'split'      => $this->renderLayoutSplit($post, $company, $brandColor, $visualStyle, $fileName, $width, $height),
            'minimal'    => $this->renderLayoutMinimal($post, $company, $brandColor, $fileName, $width, $height),
            default      => $this->renderLayoutStandard($post, $company, $brandColor, $visualStyle, $fileName, $width, $height),
        };
    }

    // ── Helper: resolve colors for a given visual_style ──────────────────────

    private function styleColors(string $visualStyle, string $brandColor): array
    {
        return match ($visualStyle) {
            'light_branded' => ['bg' => '#F8FAFC', 'title' => $brandColor,           'text' => '#1E293B', 'label' => $brandColor,           'sep' => 'rgba(0,0,0,0.08)'],
            'brand_color'   => ['bg' => $brandColor, 'title' => 'rgba(255,255,255,0.90)', 'text' => '#FFFFFF',  'label' => 'rgba(255,255,255,0.80)', 'sep' => 'rgba(0,0,0,0.20)'],
            default         => ['bg' => '#0B1426',   'title' => $brandColor,           'text' => '#FFFFFF',  'label' => $brandColor,           'sep' => 'rgba(255,255,255,0.10)'],
        };
    }

    // ── Layout: standard (original left-aligned layout) ──────────────────────

    private function renderLayoutStandard(Post $post, $company, string $brandColor, string $visualStyle, string $fileName, int $width = 1080, int $height = 1080): string
    {
        $spec         = TextZoneSpec::for('standard');
        $image        = $this->manager->create($width, $height);
        $templateName = mb_strtoupper($this->cleanText($post->template_options['custom_template_name'] ?? 'MODÈLE PERSO'));
        $imageText    = TextZoneSpec::fitText($this->extractImageText($post, $spec['maxChars']), 'standard');

        // Layout constants — proportionally scaled
        $left    = (int) ($width * (100 / 1080));
        $barH    = (int) ($height * (16 / 1080));
        $accentY = (int) ($height * (270 / 1080));
        $labelY  = (int) ($height * (336 / 1080));
        $textTop = (int) ($height * (420 / 1080));
        $sepY    = (int) ($height * (920 / 1080));
        $badgeY  = (int) ($height * (966 / 1080));

        if ($visualStyle === 'brand_color') {
            $image->fill($brandColor);

            // Ghost circles: upper-right + lower-left
            $image->drawEllipse((int)($width * 0.9), (int)($height * 0.12), function ($draw) use ($width) {
                $draw->size((int)($width * 0.37), (int)($width * 0.37));
                $draw->background('rgba(0,0,0,0.10)');
            });
            $image->drawEllipse((int)($width * 0.06), (int)($height * 0.93), function ($draw) use ($width) {
                $draw->size((int)($width * 0.28), (int)($width * 0.28));
                $draw->background('rgba(0,0,0,0.08)');
            });

            // Top bar
            $image->drawRectangle(0, 0, function ($draw) use ($width, $barH) {
                $draw->size($width, $barH);
                $draw->background('rgba(0,0,0,0.15)');
            });

            // Accent line — left-aligned
            $image->drawRectangle($left, $accentY, function ($draw) {
                $draw->size(120, 8);
                $draw->background('rgba(255,255,255,0.50)');
            });

            // Template name — left-aligned
            $image->text($templateName, $left, $labelY, function ($font) {
                $font->filename($this->fontPath);
                $font->size(34);
                $font->color('rgba(255,255,255,0.85)');
                $font->align('left');
                $font->valign('middle');
            });

            // Description text — left-aligned, auto-fit
            if ($imageText) {
                $this->drawTextAutoFit($image, mb_strtoupper($imageText), 'standard', $left, $textTop, '#FFFFFF', $width, $height);
            }

            // Bottom: separator + company label
            $image->drawRectangle(0, $sepY, function ($draw) use ($width) {
                $draw->size($width, 2);
                $draw->background('rgba(0,0,0,0.20)');
            });
            $image->text($this->companyLabel($company), $left, $badgeY, function ($font) {
                $font->filename($this->fontPath);
                $font->size(26);
                $font->color('rgba(255,255,255,0.80)');
                $font->align('left');
                $font->valign('middle');
            });

        } elseif ($visualStyle === 'light_branded') {
            $image->fill('#F8FAFC');

            // Brand top bar
            $image->drawRectangle(0, 0, function ($draw) use ($brandColor, $barH) {
                $draw->size(1080, $barH);
                $draw->background($brandColor);
            });

            // Accent line — left-aligned
            $image->drawRectangle($left, $accentY, function ($draw) use ($brandColor) {
                $draw->size(120, 8);
                $draw->background($brandColor);
            });

            // Template name — left-aligned
            $image->text($templateName, $left, $labelY, function ($font) use ($brandColor) {
                $font->filename($this->fontPath);
                $font->size(34);
                $font->color($brandColor);
                $font->align('left');
                $font->valign('middle');
            });

            // Description text — left-aligned, auto-fit
            if ($imageText) {
                $this->drawTextAutoFit($image, mb_strtoupper($imageText), 'standard', $left, $textTop, '#1E293B', $width, $height);
            }

            // Bottom: separator + company label
            $image->drawRectangle(0, $sepY, function ($draw) use ($width) {
                $draw->size($width, 1);
                $draw->background('rgba(0,0,0,0.08)');
            });
            $image->text($this->companyLabel($company), $left, $badgeY, function ($font) use ($brandColor) {
                $font->filename($this->fontPath);
                $font->size(26);
                $font->color($brandColor);
                $font->align('left');
                $font->valign('middle');
            });

        } else {
            // dark_branded (default) — deep navy, brand color accents
            $image->fill('#0B1426');

            // Ghost circles
            $image->drawEllipse((int)($width * 0.9), (int)($height * 0.12), function ($draw) use ($width) {
                $draw->size((int)($width * 0.37), (int)($width * 0.37));
                $draw->background('rgba(255,255,255,0.03)');
            });
            $image->drawEllipse((int)($width * 0.9), (int)($height * 0.12), function ($draw) use ($width) {
                $draw->size((int)($width * 0.24), (int)($width * 0.24));
                $draw->background('rgba(255,255,255,0.04)');
            });
            $image->drawEllipse((int)($width * 0.06), (int)($height * 0.93), function ($draw) use ($width) {
                $draw->size((int)($width * 0.3), (int)($width * 0.3));
                $draw->background('rgba(0,0,0,0.30)');
            });

            // Brand top bar
            $image->drawRectangle(0, 0, function ($draw) use ($brandColor, $barH) {
                $draw->size(1080, $barH);
                $draw->background($brandColor);
            });

            // Accent line — left-aligned
            $image->drawRectangle($left, $accentY, function ($draw) use ($brandColor) {
                $draw->size(120, 8);
                $draw->background($brandColor);
            });

            // Template name — left-aligned
            $image->text($templateName, $left, $labelY, function ($font) use ($brandColor) {
                $font->filename($this->fontPath);
                $font->size(34);
                $font->color($brandColor);
                $font->align('left');
                $font->valign('middle');
            });

            // Description text — left-aligned, auto-fit
            if ($imageText) {
                $this->drawTextAutoFit($image, mb_strtoupper($imageText), 'standard', $left, $textTop, '#FFFFFF', $width, $height);
            }

            // Bottom: separator + company label
            $image->drawRectangle(0, $sepY, function ($draw) use ($width) {
                $draw->size($width, 1);
                $draw->background('rgba(255,255,255,0.10)');
            });
            $image->text($this->companyLabel($company), $left, $badgeY, function ($font) use ($brandColor) {
                $font->filename($this->fontPath);
                $font->size(26);
                $font->color($brandColor);
                $font->align('left');
                $font->valign('middle');
            });
        }

        $this->addLogo($image, $company->logo_path);

        Storage::disk('public')->put($fileName, $image->toJpeg(quality: 90));

        return $fileName;
    }

    // ── Layout: centered — everything centered, short impactful content ───────

    private function renderLayoutCentered(Post $post, $company, string $brandColor, string $visualStyle, string $fileName, int $width = 1080, int $height = 1080): string
    {
        $spec         = TextZoneSpec::for('centered');
        $colors       = $this->styleColors($visualStyle, $brandColor);
        $image        = $this->manager->create($width, $height);
        $templateName = mb_strtoupper($this->cleanText($post->template_options['custom_template_name'] ?? 'MODÈLE PERSO'));
        $imageText    = TextZoneSpec::fitText($this->extractImageText($post, $spec['maxChars']), 'centered');

        $image->fill($colors['bg']);

        // Ghost circles for dark_branded only
        if ($visualStyle === 'dark_branded') {
            $image->drawEllipse((int)($width * 0.9), (int)($height * 0.12), fn($d) => $d->size((int)($width * 0.37), (int)($width * 0.37))->background('rgba(255,255,255,0.03)'));
            $image->drawEllipse((int)($width * 0.06), (int)($height * 0.93), fn($d) => $d->size((int)($width * 0.3), (int)($width * 0.3))->background('rgba(0,0,0,0.30)'));
        }

        // Top brand bar
        $image->drawRectangle(0, 0, fn($d) => $d->size($width, (int)($height * 0.015))->background($brandColor));

        $centerX = (int)($width / 2);

        // Template name — centered
        $image->text($templateName, $centerX, (int)($height * 0.37), function ($font) use ($brandColor, $height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.035));
            $font->color($brandColor);
            $font->align('center');
            $font->valign('middle');
        });

        // Centered accent line under title
        $image->drawRectangle($centerX - (int)($width * 0.09), (int)($height * 0.39), fn($d) => $d->size((int)($width * 0.18), (int)($height * 0.005))->background($brandColor));

        // Image text — centered with wrap, auto-fit
        if ($imageText) {
            $this->drawTextAutoFit($image, mb_strtoupper($imageText), 'centered', null, null, $colors['text'], $width, $height);
        }

        // Bottom separator + company centered
        $image->drawRectangle(0, (int)($height * 0.85), fn($d) => $d->size($width, 1)->background($colors['sep']));
        $image->text($this->companyLabel($company), $centerX, (int)($height * 0.89), function ($font) use ($colors, $height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.024));
            $font->color($colors['label']);
            $font->align('center');
            $font->valign('middle');
        });

        $this->addLogo($image, $company->logo_path);
        Storage::disk('public')->put($fileName, $image->toJpeg(quality: 90));
        return $fileName;
    }

    // ── Layout: quote — large decorative guillemets, anecdotes/citations ──────

    private function renderLayoutQuote(Post $post, $company, string $brandColor, string $visualStyle, string $fileName, int $width = 1080, int $height = 1080): string
    {
        $spec         = TextZoneSpec::for('quote');
        $colors       = $this->styleColors($visualStyle, $brandColor);
        $image        = $this->manager->create($width, $height);
        $templateName = mb_strtoupper($this->cleanText($post->template_options['custom_template_name'] ?? 'MODÈLE PERSO'));
        $imageText    = TextZoneSpec::fitText($this->extractImageText($post, $spec['maxChars']), 'quote');

        $image->fill($colors['bg']);

        // Subtle ghost circles for depth
        $image->drawEllipse((int)($width * 0.9), (int)($height * 0.18), fn($d) => $d->size((int)($width * 0.46), (int)($width * 0.46))->background('rgba(255,255,255,0.02)'));

        // Top brand bar
        $image->drawRectangle(0, 0, fn($d) => $d->size($width, (int)($height * 0.015))->background($brandColor));

        // Opening large decorative quote mark
        $image->text('"', (int)($width * 0.074), (int)($height * 0.2), function ($font) use ($brandColor, $height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.2));
            $font->color($brandColor . '30');
            $font->align('left');
            $font->valign('middle');
        });

        // Template name (category label) — small, top-left, brand color
        $image->text($templateName, (int)($width * 0.092), (int)($height * 0.06), function ($font) use ($brandColor, $height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.022));
            $font->color($brandColor);
            $font->align('left');
            $font->valign('middle');
        });

        // Thin accent line
        $image->drawRectangle((int)($width * 0.092), (int)($height * 0.074), fn($d) => $d->size((int)($width * 0.074), (int)($height * 0.004))->background($brandColor));

        // Quote text — centered, auto-fit
        if ($imageText) {
            $this->drawTextAutoFit($image, mb_strtoupper($imageText), 'quote', null, null, $colors['text'], $width, $height);
        }

        // Closing quote mark — bottom right
        $image->text('"', (int)($width * 0.9), (int)($height * 0.8), function ($font) use ($brandColor, $height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.17));
            $font->color($brandColor . '30');
            $font->align('right');
            $font->valign('middle');
        });

        // Bottom separator + company
        $image->drawRectangle(0, (int)($height * 0.85), fn($d) => $d->size($width, 1)->background($colors['sep']));
        $image->text($this->companyLabel($company), (int)($width * 0.092), (int)($height * 0.89), function ($font) use ($colors, $height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.024));
            $font->color($colors['label']);
            $font->align('left');
            $font->valign('middle');
        });

        $this->addLogo($image, $company->logo_path);
        Storage::disk('public')->put($fileName, $image->toJpeg(quality: 90));
        return $fileName;
    }

    // ── Layout: bold_title — giant template name, series/thematic posts ───────

    private function renderLayoutBoldTitle(Post $post, $company, string $brandColor, string $visualStyle, string $fileName, int $width = 1080, int $height = 1080): string
    {
        $spec         = TextZoneSpec::for('bold_title');
        $colors       = $this->styleColors($visualStyle, $brandColor);
        $image        = $this->manager->create($width, $height);
        $templateName = mb_strtoupper($this->cleanText($post->template_options['custom_template_name'] ?? 'MODÈLE PERSO'));
        $imageText    = TextZoneSpec::fitText($this->extractImageText($post, $spec['maxChars']), 'bold_title');

        $image->fill($colors['bg']);

        if ($visualStyle === 'dark_branded') {
            $image->drawEllipse((int)($width * 0.9), (int)($height * 0.12), fn($d) => $d->size((int)($width * 0.37), (int)($width * 0.37))->background('rgba(255,255,255,0.03)'));
        }

        // Large brand color block
        $image->drawRectangle(0, 0, fn($d) => $d->size($width, (int)($height * 0.31))->background($brandColor));

        $centerX = (int)($width / 2);

        // Template name — very large
        $image->text($templateName, $centerX, (int)($height * 0.17), function ($font) use ($height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.057));
            $font->color('#FFFFFF');
            $font->align('center');
            $font->valign('middle');
        });

        // Subtitle line
        $image->drawRectangle($centerX - (int)($width * 0.185), (int)($height * 0.24), fn($d) => $d->size((int)($width * 0.37), (int)($height * 0.002))->background('rgba(255,255,255,0.40)'));

        // Image text — left-aligned, auto-fit
        if ($imageText) {
            $this->drawTextAutoFit($image, mb_strtoupper($imageText), 'bold_title', (int)($width * 0.092), (int)($height * 0.36), $colors['text'], $width, $height);
        }

        // Bottom separator + company
        $image->drawRectangle(0, (int)($height * 0.85), fn($d) => $d->size($width, 1)->background($colors['sep']));
        $image->text($this->companyLabel($company), (int)($width * 0.092), (int)($height * 0.89), function ($font) use ($colors, $height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.024));
            $font->color($colors['label']);
            $font->align('left');
            $font->valign('middle');
        });

        $this->addLogo($image, $company->logo_path);
        Storage::disk('public')->put($fileName, $image->toJpeg(quality: 90));
        return $fileName;
    }

    // ── Layout: split — top half brand color / bottom half contrasted ─────────

    private function renderLayoutSplit(Post $post, $company, string $brandColor, string $visualStyle, string $fileName, int $width = 1080, int $height = 1080): string
    {
        $spec         = TextZoneSpec::for('split');
        $image        = $this->manager->create($width, $height);
        $templateName = mb_strtoupper($this->cleanText($post->template_options['custom_template_name'] ?? 'MODÈLE PERSO'));
        $imageText    = TextZoneSpec::fitText($this->extractImageText($post, $spec['maxChars']), 'split');

        $bottomBg    = ($visualStyle === 'light_branded') ? '#F8FAFC' : '#0B1426';
        $bottomText  = ($visualStyle === 'light_branded') ? '#1E293B' : '#FFFFFF';
        $bottomLabel = ($visualStyle === 'light_branded') ? $brandColor : $brandColor;

        $splitY = (int)($height * 0.45);

        // Top half — brand color
        $image->fill($brandColor);
        // Ghost circles
        $image->drawEllipse((int)($width * 0.925), (int)($height * 0.09), fn($d) => $d->size((int)($width * 0.35), (int)($width * 0.35))->background('rgba(0,0,0,0.08)'));
        $image->drawEllipse((int)($width * 0.074), (int)($height * 0.37), fn($d) => $d->size((int)($width * 0.185), (int)($width * 0.185))->background('rgba(0,0,0,0.06)'));

        // Bottom half
        $image->drawRectangle(0, $splitY, fn($d) => $d->size($width, $height - $splitY)->background($bottomBg));

        // Diagonal accent stripe
        $image->drawRectangle(0, $splitY - (int)($height * 0.006), fn($d) => $d->size($width, (int)($height * 0.013))->background('rgba(0,0,0,0.15)'));

        $centerX = (int)($width / 2);

        // Template name — top half
        $image->text($templateName, $centerX, (int)($splitY * 0.57), function ($font) use ($height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.04));
            $font->color('#FFFFFF');
            $font->align('center');
            $font->valign('middle');
        });

        // Decorative line
        $image->drawRectangle($centerX - (int)($width * 0.185), (int)($splitY * 0.65), fn($d) => $d->size((int)($width * 0.37), (int)($height * 0.003))->background('rgba(255,255,255,0.40)'));

        // Accent line in bottom half
        $image->drawRectangle((int)($width * 0.092), $splitY + (int)($height * 0.037), fn($d) => $d->size((int)($width * 0.074), (int)($height * 0.005))->background($brandColor));

        // Image text — bottom half, auto-fit
        if ($imageText) {
            $this->drawTextAutoFit($image, mb_strtoupper($imageText), 'split', (int)($width * 0.092), $splitY + (int)($height * 0.065), $bottomText, $width, $height);
        }

        // Bottom separator + company
        $image->drawRectangle(0, (int)($height * 0.85), fn($d) => $d->size($width, 1)->background('rgba(128,128,128,0.15)'));
        $image->text($this->companyLabel($company), (int)($width * 0.092), (int)($height * 0.89), function ($font) use ($bottomLabel, $height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.024));
            $font->color($bottomLabel);
            $font->align('left');
            $font->valign('middle');
        });

        $this->addLogo($image, $company->logo_path);
        Storage::disk('public')->put($fileName, $image->toJpeg(quality: 90));
        return $fileName;
    }

    // ── Layout: minimal — white bg, left brand stripe, very clean ────────────

    private function renderLayoutMinimal(Post $post, $company, string $brandColor, string $fileName, int $width = 1080, int $height = 1080): string
    {
        $spec         = TextZoneSpec::for('minimal');
        $image        = $this->manager->create($width, $height);
        $templateName = mb_strtoupper($this->cleanText($post->template_options['custom_template_name'] ?? 'MODÈLE PERSO'));
        $imageText    = TextZoneSpec::fitText($this->extractImageText($post, $spec['maxChars']), 'minimal');

        // White background
        $image->fill('#FFFFFF');

        // Left vertical brand stripe
        $image->drawRectangle(0, 0, fn($d) => $d->size((int)($width * 0.013), $height)->background($brandColor));

        // Template name
        $image->text($templateName, (int)($width * 0.074), (int)($height * 0.39), function ($font) use ($brandColor, $height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.033));
            $font->color($brandColor);
            $font->align('left');
            $font->valign('middle');
        });

        // Thin separator under title
        $image->drawRectangle((int)($width * 0.074), (int)($height * 0.41), fn($d) => $d->size((int)($width * 0.83), (int)($height * 0.002))->background('#E2E8F0'));

        // Image text — left-aligned, auto-fit
        if ($imageText) {
            $this->drawTextAutoFit($image, mb_strtoupper($imageText), 'minimal', (int)($width * 0.074), (int)($height * 0.435), '#334155', $width, $height);
        }

        // Bottom: thin line + company label
        $image->drawRectangle((int)($width * 0.074), (int)($height * 0.87), fn($d) => $d->size((int)($width * 0.83), 1)->background('#CBD5E1'));
        $image->text($this->companyLabel($company), (int)($width * 0.074), (int)($height * 0.9), function ($font) use ($brandColor, $height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.022));
            $font->color($brandColor);
            $font->align('left');
            $font->valign('middle');
        });

        $this->addLogo($image, $company->logo_path);
        Storage::disk('public')->put($fileName, $image->toJpeg(quality: 90));
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

    private function generateChantierDetail(Post $post, int $width = 1080, int $height = 1080): array
    {
        $company    = $post->company;
        $decoded    = $this->parseStructuredContent($post->refined_content);
        $brandColor = $company->brand_color ?? '#F59E0B';
        $photos     = $post->extra_images ?? [];
        $slides     = [];

        // Slide 1 – Cover
        $coverImage = $this->manager->create($width, $height);
        if (!empty($photos[0]) && Storage::disk('public')->exists($photos[0])) {
            $photo = $this->manager->read(Storage::disk('public')->get($photos[0]));
            $photo->cover($width, $height);
            $coverImage->place($photo, 'top-left', 0, 0);
        } else {
            $coverImage->fill('#1E293B');
        }
        $coverImage->drawRectangle(0, (int)($height / 2), function ($draw) use ($width, $height) {
            $draw->size($width, (int)($height / 2));
            $draw->background('rgba(0,0,0,0.6)');
        });
        
        $titleText = !empty($decoded['slides'][0]) 
            ? $this->cleanText($decoded['slides'][0]) 
            : $this->cleanText(mb_substr($decoded['caption'], 0, 60));

        $this->drawTextRaw($coverImage, strtoupper($titleText), (int)($height * 0.5), (int)($height * 0.72), (int)($height * 0.052), '#FFFFFF', 24);
        $this->drawBottomBand($coverImage, $company, $brandColor);
        $this->addLogo($coverImage, $company->logo_path);
        $path = 'posts/' . $post->id . '_slide_1.jpg';
        Storage::disk('public')->put($path, $coverImage->toJpeg(quality: 90));
        $slides[] = $path;

        // Slides 2..N – detail photos
        foreach (array_slice($photos, 1, 4) as $i => $photoPath) {
            $slideImage = $this->manager->create($width, $height);
            if (Storage::disk('public')->exists($photoPath)) {
                $photo = $this->manager->read(Storage::disk('public')->get($photoPath));
                $photo->cover($width, $height);
                $slideImage->place($photo, 'top-left', 0, 0);
            } else {
                $slideImage->fill('#1E293B');
            }
            
            // Single unified bottom band (fixed 160px) — slide text + company name
            $slideText = !empty($decoded['slides'][$i + 1])
                ? strtoupper($this->cleanText($decoded['slides'][$i + 1]))
                : null;
            $this->drawUnifiedBand($slideImage, $company, $brandColor, $slideText);
            $this->addLogo($slideImage, $company->logo_path);
            $path = 'posts/' . $post->id . '_slide_' . ($i + 2) . '.jpg';
            Storage::disk('public')->put($path, $slideImage->toJpeg(quality: 90));
            $slides[] = $path;
        }

        return $slides;
    }

    private function generateStepByStep(Post $post, int $width = 1080, int $height = 1080): array
    {
        $company    = $post->company;
        $decoded    = $this->parseStructuredContent($post->refined_content);
        $brandColor = $company->brand_color ?? '#F59E0B';
        $photos     = $post->extra_images ?? [];
        $slides     = [];

        // Slide 1 – Cover
        $coverImage = $this->manager->create($width, $height);
        $coverImage->fill($brandColor);
        $coverImage->drawRectangle(0, 0, function ($draw) use ($height) {
            $draw->size((int)($height * 0.011), $height);
            $draw->background('rgba(0,0,0,0.25)');
        });
        $coverImage->text('ÉTAPE', (int)($width / 2), (int)($height * 0.35), function ($font) use ($height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.055));
            $font->color('rgba(255,255,255,0.25)');
            $font->align('center');
            $font->valign('middle');
        });
        $coverImage->text('PAR ÉTAPE', (int)($width / 2), (int)($height * 0.42), function ($font) use ($height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.055));
            $font->color('rgba(255,255,255,0.25)');
            $font->align('center');
            $font->valign('middle');
        });

        $titleText = !empty($decoded['slides'][0]) 
            ? strtoupper($this->cleanText($decoded['slides'][0])) 
            : 'NOS ÉTAPES DE TRAVAIL';

        $this->drawTextRaw($coverImage, $titleText, (int)($height * 0.5), (int)($height * 0.65), (int)($height * 0.052), '#FFFFFF', 24);
        $this->drawBottomBand($coverImage, $company, '#000000');
        $this->addLogo($coverImage, $company->logo_path);
        $path = 'posts/' . $post->id . '_slide_1.jpg';
        Storage::disk('public')->put($path, $coverImage->toJpeg(quality: 90));
        $slides[] = $path;

        // Slides 2..N – one per photo/step
        foreach ($photos as $idx => $photoPath) {
            $slideImage = $this->manager->create($width, $height);
            if (Storage::disk('public')->exists($photoPath)) {
                $photo = $this->manager->read(Storage::disk('public')->get($photoPath));
                $photo->cover($width, $height);
                $slideImage->place($photo, 'top-left', 0, 0);
            } else {
                $slideImage->fill('#1E293B');
            }
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
            $this->drawUnifiedBand($slideImage, $company, $brandColor, $stepLabel);
            $this->addLogo($slideImage, $company->logo_path);
            $path = 'posts/' . $post->id . '_slide_' . ($idx + 2) . '.jpg';
            Storage::disk('public')->put($path, $slideImage->toJpeg(quality: 90));
            $slides[] = $path;
        }

        return $slides;
    }

    private function generateGuidePedagogique(Post $post, int $width = 1080, int $height = 1080): array
    {
        $company    = $post->company;
        $decoded    = $this->parseStructuredContent($post->refined_content);
        $brandColor = $company->brand_color ?? '#F59E0B';
        $slides     = [];

        $aiSlides = !empty($decoded['slides']) ? $decoded['slides'] : [];

        // --- Slide 1: Accroche ---
        $slide1 = $this->manager->create($width, $height);
        $slide1->fill($brandColor);

        $question = !empty($aiSlides[0]) 
            ? strtoupper($this->cleanText($aiSlides[0])) 
            : 'CONSEIL D\'EXPERT';
            
        $this->drawTextRaw($slide1, $question, (int)($height * 0.5), (int)($height * 0.5), (int)($height * 0.063), '#FFFFFF', 20);

        $slide1->text('CONSEIL D\'EXPERT', (int)($width * 0.5), (int)($height * 0.2), function ($font) use ($height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.03));
            $font->color('rgba(255,255,255,0.7)');
            $font->align('center');
            $font->valign('middle');
        });
        $this->addLogo($slide1, $company->logo_path);
        $path = 'posts/' . $post->id . '_slide_1.jpg';
        Storage::disk('public')->put($path, $slide1->toJpeg(quality: 90));
        $slides[] = $path;

        // --- Middle slides ---
        $middleSlides = array_slice($aiSlides, 1);
        // If the AI didn't provide enough slides, we fallback to splitting the caption
        if (count($middleSlides) < 2 && !empty($decoded['caption'])) {
            $middleSlides = $this->splitTextIntoSlides($decoded['caption'], 140);
        }

        foreach ($middleSlides as $ci => $slideContent) {
            $slide = $this->manager->create($width, $height);
            $slide->fill('#F8FAFC');
            $slide->drawRectangle(0, 0, function ($draw) use ($width, $height, $brandColor) {
                $draw->size($width, (int)($height * 0.011));
                $draw->background($brandColor);
            });
            $slideNum = $ci + 2;
            $slide->drawRectangle((int)($width * 0.074), (int)($height * 0.11), function ($draw) use ($height, $brandColor) {
                $draw->size((int)($height * 0.055), (int)($height * 0.055));
                $draw->background($brandColor);
            });
            $slide->text((string)$slideNum, (int)($width * 0.074 + ($height * 0.027)), (int)($height * 0.11 + ($height * 0.027)), function ($font) use ($height) {
                $font->filename($this->fontPath);
                $font->size((int)($height * 0.03));
                $font->color('#FFFFFF');
                $font->align('center');
                $font->valign('middle');
            });
            $this->drawTextRaw($slide, strtoupper($this->cleanText($slideContent)), (int)($height * 0.5), (int)($height * 0.5), (int)($height * 0.048), '#1E293B', 24);
            $this->drawBottomBand($slide, $company, '#1E293B');
            $this->addLogo($slide, $company->logo_path);

            $path = 'posts/' . $post->id . '_slide_' . $slideNum . '.jpg';
            Storage::disk('public')->put($path, $slide->toJpeg(quality: 90));
            $slides[] = $path;
        }

        // --- Last slide: CTA ---
        $lastSlideNum = count($slides) + 1;
        $cta = $this->manager->create($width, $height);
        $cta->fill('#1E293B');
        $cta->drawRectangle(0, 0, function ($draw) use ($width, $height, $brandColor) {
            $draw->size($width, (int)($height * 0.015));
            $draw->background($brandColor);
        });
        $cta->drawRectangle(0, $height - (int)($height * 0.015), function ($draw) use ($width, $height, $brandColor) {
            $draw->size($width, (int)($height * 0.015));
            $draw->background($brandColor);
        });
        if ($company->logo_path && Storage::disk('public')->exists($company->logo_path)) {
            $logo = $this->manager->read(Storage::disk('public')->get($company->logo_path));
            $logo->scale(width: (int)($width * 0.185));
            $cta->place($logo, 'center', 0, -(int)($height * 0.074));
        }
        $cta->text('UN PROJET EN TÊTE ?', (int)($width * 0.5), (int)($height * 0.57), function ($font) use ($height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.044));
            $font->color('#FFFFFF');
            $font->align('center');
            $font->valign('middle');
        });
        $cta->text('Contactez-nous pour un devis gratuit', (int)($width * 0.5), (int)($height * 0.65), function ($font) use ($height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.028));
            $font->color('rgba(255,255,255,0.7)');
            $font->align('center');
            $font->valign('middle');
        });
        $cta->text($this->companyLabel($company), (int)($width * 0.5), (int)($height * 0.76), function ($font) use ($height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.024));
            $font->color('#F59E0B');
            $font->align('center');
            $font->valign('middle');
        });
        $path = 'posts/' . $post->id . '_slide_' . $lastSlideNum . '.jpg';
        Storage::disk('public')->put($path, $cta->toJpeg(quality: 90));
        $slides[] = $path;

        return $slides;
    }

    // -------------------------------------------------------------------------
    // Template drawers
    // -------------------------------------------------------------------------

    private function drawReviewElements($image): void
    {
        $width  = $image->width();
        $height = $image->height();
        $starY       = (int)($height * 0.213);
        $starSpacing = (int)($width * 0.083);
        $startX      = (int)($width * 0.5) - (($starSpacing * 4) / 2);

        for ($i = 0; $i < 5; $i++) {
            $this->drawStar($image, $startX + ($i * $starSpacing), $starY, (int)($height * 0.032), (int)($height * 0.014), '#FACC15');
        }
    }

    private function drawCompanyBadge($image, $company): void
    {
        $width  = $image->width();
        $height = $image->height();
        $badgeH = (int)($height * 0.111);
        $badgeY = $height - $badgeH;

        $image->drawRectangle(0, $badgeY, function ($draw) use ($width, $badgeH) {
            $draw->size($width, $badgeH);
            $draw->background('rgba(0, 0, 0, 0.35)');
        });

        $label = $this->companyLabel($company);

        $image->text($label, (int)($width * 0.5), $badgeY + (int)($badgeH * 0.5), function ($font) use ($height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.028));
            $font->color('#FFFFFF');
            $font->align('center');
            $font->valign('middle');
        });
    }

    private function drawProjectElements($image, Post $post, $company, string $brandColor): void
    {
        $width        = $image->width();
        $height       = $image->height();
        $bannerHeight = (int)($height * 0.139);
        $imageHeight  = $height - $bannerHeight;
        $halfWidth    = (int)($width / 2);

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

        $image->drawRectangle(0, $imageHeight, function ($draw) use ($width, $bannerHeight, $brandColor) {
            $draw->size($width, $bannerHeight);
            $draw->background($brandColor);
        });

        $labelY = $imageHeight + intval($bannerHeight * 0.62);

        $image->text('AVANT', (int)($width * 0.15), $labelY, function ($font) use ($height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.033));
            $font->color('#FFFFFF');
            $font->align('center');
            $font->valign('middle');
        });

        $image->text('APRÈS', (int)($width * 0.85), $labelY, function ($font) use ($height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.033));
            $font->color('#FFFFFF');
            $font->align('center');
            $font->valign('middle');
        });

        $companyY = $imageHeight + intval($bannerHeight * 0.28);
        $label    = $this->companyLabel($company);

        $image->text($label, (int)($width * 0.5), $companyY, function ($font) use ($height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.02));
            $font->color('rgba(255, 255, 255, 0.85)');
            $font->align('center');
            $font->valign('middle');
        });
    }

    private function drawFaqElements($image, string $brandColor): void
    {
        $width  = $image->width();
        $height = $image->height();

        $image->drawRectangle(0, 0, function ($draw) use ($width, $height, $brandColor) {
            $draw->size($width, (int)($height * 0.018));
            $draw->background($brandColor);
        });

        $image->drawRectangle((int)($width * 0.45), (int)($height * 0.23), function ($draw) use ($width, $height, $brandColor) {
            $draw->size((int)($width * 0.092), (int)($height * 0.009));
            $draw->background($brandColor);
        });

        $image->text('FAQ ARTISAN', (int)($width * 0.5), (int)($height * 0.29), function ($font) use ($brandColor, $height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.032));
            $font->color($brandColor);
            $font->align('center');
            $font->valign('middle');
        });

        $image->text('?', (int)($width * 0.88), (int)($height * 0.88), function ($font) use ($height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.27));
            $font->color('rgba(255, 255, 255, 0.05)');
            $font->align('center');
            $font->valign('middle');
        });

        $image->drawRectangle((int)($width * 0.092), (int)($height * 0.83), function ($draw) use ($width) {
            $draw->size((int)($width * 0.81), 1);
            $draw->background('rgba(255, 255, 255, 0.1)');
        });
    }

    private function drawFaqCompanyName($image, $company, string $brandColor): void
    {
        $label = $this->companyLabel($company);

        $image->text($label, (int)($image->width() * 0.5), (int)($image->height() * 0.89), function ($font) use ($brandColor, $image) {
            $font->filename($this->fontPath);
            $font->size((int)($image->height() * 0.024));
            $font->color($brandColor);
            $font->align('center');
            $font->valign('middle');
        });
    }

    private function drawLifestyleElements($image, Post $post): void
    {
        if ($post->image_before_path && Storage::disk('public')->exists($post->image_before_path)) {
            $photo = $this->manager->read(Storage::disk('public')->get($post->image_before_path));
            $photo->cover($image->width(), $image->height());
            $image->place($photo, 'top-left', 0, 0);
        }
    }

    private function drawLifestyleOverlay($image, $company, string $brandColor): void
    {
        $width  = $image->width();
        $height = $image->height();
        $overlayH = (int)($height * 0.104);
        $overlayY = $height - $overlayH;

        $image->drawRectangle(0, $overlayY, function ($draw) use ($width, $overlayH) {
            $draw->size($width, $overlayH);
            $draw->background('rgba(0, 0, 0, 0.55)');
        });

        $image->drawRectangle(0, $overlayY - 2, function ($draw) use ($width, $brandColor) {
            $draw->size($width, 4);
            $draw->background($brandColor);
        });

        $label = $this->companyLabel($company);

        $image->text($label, (int)($width * 0.5), $overlayY + (int)($overlayH * 0.5), function ($font) use ($height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.028));
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
        $width  = $image->width();
        $height = $image->height();

        // Full-bleed photo
        $photoPath = $post->image_before_path;
        if ($photoPath && Storage::disk('public')->exists($photoPath)) {
            $photo = $this->manager->read(Storage::disk('public')->get($photoPath));
            $photo->cover($width, $height);
            $image->place($photo, 'top-left', 0, 0);
        } else {
            $image->fill('#1E293B');
        }

        // "SÉLECTION PRO" badge top-right
        $badgeW = (int)($width * 0.24);
        $badgeH = (int)($height * 0.055);
        $image->drawRectangle($width - $badgeW - (int)($width * 0.037), (int)($height * 0.037), function ($draw) use ($badgeW, $badgeH, $brandColor) {
            $draw->size($badgeW, $badgeH);
            $draw->background($brandColor);
        });
        $image->text('SÉLECTION PRO', $width - (int)($badgeW / 2) - (int)($width * 0.037), (int)($height * 0.037) + (int)($badgeH / 2), function ($font) use ($height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.02));
            $font->color('#FFFFFF');
            $font->align('center');
            $font->valign('middle');
        });

        // Unified bottom band
        $this->drawUnifiedBand($image, $company, $brandColor, mb_strtoupper($content));
    }

    /**
     * Conseil Saisonnier: deep navy + concentric ghost circles (sun motif) + seasonal advice.
     */
    private function drawConseilSaisonnierElements($image, $company, string $brandColor, string $content): void
    {
        $width  = $image->width();
        $height = $image->height();

        $image->fill('#0B1426');

        // Ghost sun
        $image->drawEllipse((int)($width * 0.9), (int)($height * 0.12), function ($draw) use ($width) {
            $draw->size((int)($width * 0.37), (int)($width * 0.37));
            $draw->background('rgba(255,255,255,0.03)');
        });
        $image->drawEllipse((int)($width * 0.9), (int)($height * 0.12), function ($draw) use ($width) {
            $draw->size((int)($width * 0.24), (int)($width * 0.24));
            $draw->background('rgba(255,255,255,0.04)');
        });

        // Brand color top bar
        $image->drawRectangle(0, 0, function ($draw) use ($width, $height, $brandColor) {
            $draw->size($width, (int)($height * 0.018));
            $draw->background($brandColor);
        });

        // "CONSEIL SAISONNIER" label
        $image->text('CONSEIL SAISONNIER', (int)($width * 0.5), (int)($height * 0.27), function ($font) use ($brandColor, $height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.033));
            $font->color($brandColor);
            $font->align('center');
            $font->valign('middle');
        });

        // Separator
        $image->drawRectangle((int)($width * 0.185), (int)($height * 0.30), function ($draw) use ($width, $brandColor) {
            $draw->size((int)($width * 0.63), 4);
            $draw->background($brandColor);
        });

        // Main advice text
        $this->drawTextRaw($image, mb_strtoupper($content), (int)($height * 0.34), (int)($height * 0.78), (int)($height * 0.048), '#FFFFFF', 22);

        // Company name
        $this->drawFaqCompanyName($image, $company, $brandColor);
    }

    /**
     * Erreur à Éviter: dark charcoal + ghost diagonal X + alert style text.
     */
    private function drawErreurAEviterElements($image, $company, string $brandColor, string $content): void
    {
        $width  = $image->width();
        $height = $image->height();

        $image->fill('#111827');

        // Ghost X
        $image->drawPolygon(function ($draw) use ($width, $height) {
            $draw->point((int)($width * 0.065), (int)($height * 0.13));
            $draw->point((int)($width * 0.13), (int)($height * 0.065));
            $draw->point((int)($width * 0.935), (int)($height * 0.87));
            $draw->point((int)($width * 0.87), (int)($height * 0.935));
            $draw->background('rgba(255,255,255,0.045)');
        });

        // Brand color top bar
        $image->drawRectangle(0, 0, function ($draw) use ($width, $height, $brandColor) {
            $draw->size($width, (int)($height * 0.018));
            $draw->background($brandColor);
        });

        // Left accent bar
        $image->drawRectangle(0, 0, function ($draw) use ($height, $brandColor) {
            $draw->size(12, $height);
            $draw->background($brandColor);
        });

        // "ERREUR A EVITER" label
        $image->text('ERREUR A EVITER', (int)($width * 0.5), (int)($height * 0.27), function ($font) use ($brandColor, $height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.033));
            $font->color($brandColor);
            $font->align('center');
            $font->valign('middle');
        });

        // Separator
        $image->drawRectangle((int)($width * 0.185), (int)($height * 0.30), function ($draw) use ($width, $brandColor) {
            $draw->size((int)($width * 0.63), 4);
            $draw->background($brandColor);
        });

        // Main error text
        $this->drawTextRaw($image, mb_strtoupper($content), (int)($height * 0.34), (int)($height * 0.78), (int)($height * 0.046), '#FFFFFF', 22);

        // Company name
        $this->drawFaqCompanyName($image, $company, $brandColor);
    }

    // -------------------------------------------------------------------------
    // Shared helpers
    // -------------------------------------------------------------------------

    /**
     * Black bottom band with company label — used for cover slides (brand-only, 120px).
     */
    private function drawBottomBand($image, $company, string $bgColor = '#000000'): void
    {
        $width  = $image->width();
        $height = $image->height();
        $bandH  = (int)($height * (120 / 1080));
        $bandY  = $height - $bandH;

        $image->drawRectangle(0, $bandY, function ($draw) use ($width, $bandH, $bgColor) {
            $draw->size($width, $bandH);
            $draw->background($bgColor === '#000000' ? 'rgba(0,0,0,0.85)' : $bgColor);
        });
        $image->text($this->companyLabel($company), (int)($width / 2), $bandY + (int)($bandH * 0.5), function ($font) use ($height) {
            $font->filename($this->fontPath);
            $font->size((int)($height * 0.026));
            $font->color('rgba(255,255,255,0.7)');
            $font->align('center');
            $font->valign('middle');
        });
    }

    /**
     * Unified bottom band — fixed 160px for all photo slides.
     * Shows optional primary text + company label in a single consistent band.
     */
    private function drawUnifiedBand($image, $company, string $brandColor, ?string $primaryText = null): void
    {
        $width  = $image->width();
        $height = $image->height();
        $bandH  = (int)($height * (160 / 1080));
        $bandY  = $height - $bandH;

        $image->drawRectangle(0, $bandY, function ($draw) use ($width, $bandH) {
            $draw->size($width, $bandH);
            $draw->background('rgba(0,0,0,0.80)');
        });

        // Brand accent line
        $image->drawRectangle(0, $bandY, function ($draw) use ($width, $brandColor) {
            $draw->size($width, 3);
            $draw->background($brandColor);
        });

        $centerX = (int)($width / 2);

        if ($primaryText !== null && $primaryText !== '') {
            $this->drawTextRaw($image, $primaryText, $bandY + (int)($bandH * 0.05), $bandY + (int)($bandH * 0.7), (int)($height * 0.028), '#FFFFFF', 34);
            $image->text($this->companyLabel($company), $centerX, $bandY + (int)($bandH * 0.86), function ($font) use ($height) {
                $font->filename($this->fontPath);
                $font->size((int)($height * 0.0185));
                $font->color('rgba(255,255,255,0.60)');
                $font->align('center');
                $font->valign('middle');
            });
        } else {
            $image->text($this->companyLabel($company), $centerX, $bandY + (int)($bandH * 0.5), function ($font) use ($height) {
                $font->filename($this->fontPath);
                $font->size((int)($height * 0.024));
                $font->color('rgba(255,255,255,0.70)');
                $font->align('center');
                $font->valign('middle');
            });
        }
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
     * Draw text on image with automatic font-size reduction to fit within the
     * layout's text zone. Uses TextZoneSpec to determine bounds and constraints.
     *
     * @param mixed       $image      Intervention Image instance
     * @param string      $text       The text to render
     * @param string      $layoutType One of TextZoneSpec layout types
     * @param int|null    $xLeft      X origin (for left-aligned layouts), null = use spec
     * @param int|null    $yTop       Y origin override, null = use spec's textTopPct
     * @param string      $color      Text color
     * @param int         $width      Canvas width
     * @param int         $height     Canvas height
     */
    private function drawTextAutoFit($image, string $text, string $layoutType, ?int $xLeft, ?int $yTop, string $color, int $width, int $height): void
    {
        $spec = TextZoneSpec::for($layoutType);

        $baseFontSize    = $spec['fontSize'];
        $maxCharsPerLine = $spec['maxCharsPerLine'];
        $maxLines        = $spec['maxLines'];
        $alignment       = $spec['alignment'];
        $topPct          = $spec['textTopPct'];
        $bottomPct       = $spec['textBottomPct'];

        // Scale to actual canvas
        $scaledFontSize = (int)($height * ($baseFontSize / 1080));
        $topBound       = $yTop ?? (int)($height * $topPct);
        $bottomBound    = (int)($height * $bottomPct);
        $availableH     = $bottomBound - $topBound;

        // Word-wrap into lines
        $wrapLines = function (string $t, int $maxCpl) {
            $words   = explode(' ', $t);
            $lines   = [];
            $current = '';
            foreach ($words as $word) {
                $candidate = $current ? $current . ' ' . $word : $word;
                if (mb_strlen($candidate) <= $maxCpl) {
                    $current = $candidate;
                } else {
                    if ($current !== '') $lines[] = $current;
                    $current = $word;
                }
            }
            if ($current !== '') $lines[] = $current;
            return $lines;
        };

        $lines      = $wrapLines($text, $maxCharsPerLine);
        $fontSize   = $scaledFontSize;
        $lineHeight = (int)($fontSize * 1.35);

        // Auto-reduce font size if text overflows the zone
        $minFontSize = (int)($scaledFontSize * 0.55);
        while (count($lines) > $maxLines || (count($lines) * $lineHeight) > $availableH) {
            $fontSize -= max(1, (int)($fontSize * 0.08));
            if ($fontSize < $minFontSize) {
                // Hard cap: truncate lines to maxLines
                $lines = array_slice($lines, 0, $maxLines);
                break;
            }
            $lineHeight = (int)($fontSize * 1.35);

            // Re-wrap with slightly more chars per line (proportional to font reduction)
            $adjustedCpl = (int)($maxCharsPerLine * ($scaledFontSize / $fontSize));
            $lines       = $wrapLines($text, $adjustedCpl);
        }

        // Draw
        if ($alignment === 'center') {
            $totalH = count($lines) * $lineHeight;
            $startY = $topBound + ($availableH - $totalH) / 2 + ($lineHeight / 2);
            $cx     = (int)($width / 2);

            foreach ($lines as $line) {
                $image->text($line, $cx, (int)$startY, function ($font) use ($fontSize, $color) {
                    if (file_exists($this->fontPath)) {
                        $font->filename($this->fontPath);
                        $font->size((int)$fontSize);
                    }
                    $font->color($color);
                    $font->align('center');
                    $font->valign('middle');
                });
                $startY += $lineHeight;
            }
        } else {
            // left-aligned
            $lx = $xLeft ?? (int)($width * 0.092);
            $y  = $topBound + (int)($lineHeight / 2);

            foreach ($lines as $line) {
                $image->text($line, $lx, (int)$y, function ($font) use ($fontSize, $color) {
                    if (file_exists($this->fontPath)) {
                        $font->filename($this->fontPath);
                        $font->size((int)$fontSize);
                    }
                    $font->color($color);
                    $font->align('left');
                    $font->valign('middle');
                });
                $y += $lineHeight;
            }
        }
    }

    /**
     * Draw text with automatic word-wrap, vertically centered in the given bounds.
     * (Original method — adds quotes, uppercase.)
     */
    private function drawText($image, $text, $topBound = 420, $bottomBound = 880, $fontSize = 50): void
    {
        $width  = $image->width();
        $height = $image->height();

        // Scale bounds if they are standard 1080 constants
        if ($topBound > 1) $topBound = $height * ($topBound / 1080);
        if ($bottomBound > 1) $bottomBound = $height * ($bottomBound / 1080);
        if ($fontSize > 0) $fontSize = $height * ($fontSize / 1080);

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

        $lineHeight      = (int)($fontSize * 1.4);
        $totalTextHeight = count($lines) * $lineHeight;
        $availableHeight = $bottomBound - $topBound;
        $startY          = $topBound + ($availableHeight - $totalTextHeight) / 2 + ($lineHeight / 2);

        $centerX = (int)($width / 2);
        $y = $startY;
        foreach ($lines as $line) {
            $image->text($line, $centerX, (int)$y, function ($font) use ($fontSize) {
                if (file_exists($this->fontPath)) {
                    $font->filename($this->fontPath);
                    $font->size((int)$fontSize);
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
        $width  = $image->width();
        $height = $image->height();

        // Scale bounds if they are standard 1080 constants
        if ($topBound > 1) $topBound = $height * ($topBound / 1080);
        if ($bottomBound > 1) $bottomBound = $height * ($bottomBound / 1080);
        if ($fontSize > 0) $fontSize = $height * ($fontSize / 1080);

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

        $centerX = (int)($width / 2);
        $y = $startY;
        foreach ($lines as $line) {
            $image->text($line, $centerX, (int)$y, function ($font) use ($fontSize, $color) {
                if (file_exists($this->fontPath)) {
                    $font->filename($this->fontPath);
                    $font->size((int)$fontSize);
                }
                $font->color($color);
                $font->align('center');
                $font->valign('middle');
            });
            $y += $lineHeight;
        }
    }

    /**
     * Draw text left-aligned with word-wrap, starting at ($xLeft, $yTop).
     * Mirrors drawTextRaw() but uses left alignment instead of centered.
     */
    private function drawTextLeftAligned($image, string $text, int $xLeft, int $yTop, int $fontSize, string $color = '#FFFFFF', int $maxChars = 22): void
    {
        $width  = $image->width();
        $height = $image->height();

        // Scale bounds if they are standard 1080 constants
        if ($yTop > 1) $yTop = $height * ($yTop / 1080);
        if ($fontSize > 0) $fontSize = $height * ($fontSize / 1080);
        if ($xLeft > 1) $xLeft = $width * ($xLeft / 1080);

        $words   = explode(' ', $text);
        $lines   = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = $current ? $current . ' ' . $word : $word;
            if (mb_strlen($candidate) <= $maxChars) {
                $current = $candidate;
            } else {
                if ($current !== '') $lines[] = $current;
                $current = $word;
            }
        }
        if ($current !== '') $lines[] = $current;

        $lineHeight = intval($fontSize * 1.35);
        $y          = $yTop + intval($lineHeight / 2);

        foreach ($lines as $line) {
            $image->text($line, (int)$xLeft, (int)$y, function ($font) use ($fontSize, $color) {
                if (file_exists($this->fontPath)) {
                    $font->filename($this->fontPath);
                    $font->size((int)$fontSize);
                }
                $font->color($color);
                $font->align('left');
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
            $logo->scale(width: (int)($image->width() * 0.12));
            $image->place($logo, 'bottom-right', (int)($image->width() * 0.037), (int)($image->height() * 0.037));
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
