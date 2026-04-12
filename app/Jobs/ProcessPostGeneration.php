<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Models\Post;
use App\Services\PostGeneration\TranscriptionService;
use App\Services\PostGeneration\ContentRefinerService;
use App\Services\PostGeneration\TextFitterService;
use App\Services\PostGeneration\ImageGeneratorService;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Jobs\PublishToInstagramJob;
use App\Jobs\PublishToFacebookJob;

class ProcessPostGeneration implements ShouldQueue
{
    use Queueable;

    public $timeout = 300;

    public function __construct(public Post $post)
    {
        //
    }

    public function handle(
        TranscriptionService $transcriber,
        ContentRefinerService $refiner,
        TextFitterService $fitter,
        ImageGeneratorService $generator
    ): void {
        Log::info("ProcessPostGeneration handle started", ['post_id' => $this->post->id]);
        set_time_limit(600);
        try {
            $this->post->update(['status' => Post::STATUS_PROCESSING]);

            $templateOptions  = $this->post->template_options ?? [];
            $variantCount     = max(1, (int) ($templateOptions['variant_count'] ?? 1));
            $company          = $this->post->company;
            $platforms        = $this->post->platforms ?? [];

            // ── Step 1 : Transcription (shared across all variants) ────────────
            $textContent = $templateOptions['text_content'] ?? null;
            $audioPath   = $this->post->audio_path;
            $transcript  = $this->post->transcription; // reuse if already set

            if (!$transcript) {
                if ($textContent) {
                    Log::info("Using provided text content");
                    $transcript = $textContent;
                } elseif ($audioPath) {
                    Log::info("Starting transcription from audio file", ['path' => $audioPath]);
                    $transcript = $transcriber->transcribe($audioPath);
                    Log::info("Transcription finished", ['length' => strlen($transcript)]);
                } else {
                    throw new Exception("Aucun contenu (audio ou texte) fourni pour la génération.");
                }

                if ($company) {
                    $transcript = $this->correctKnownTerms($transcript, $company);
                }
                $this->post->update(['transcription' => $transcript]);
            }

            // Enrich template options with company data (shared)
            $photoCount = count($this->post->extra_images ?? []);
            if ($company) {
                $company->refresh();
                $templateOptions['company_city']    = $company->city;
                $templateOptions['company_postal']  = $company->postal_code;
                $templateOptions['company_context'] = $company->city_context;
                $templateOptions['company_nearby']  = $company->nearby_cities ?? [];
                $templateOptions['company_name']    = $company->name;
                $templateOptions['company_trade']   = $company->trade;
                $templateOptions['company_phone']   = $company->phone;
                $templateOptions['company_email']   = $company->email;
                if (!empty($company->bio_text)) {
                    $templateOptions['artisan_bio'] = $company->bio_text;
                }
            }

            // ── Steps 2–4 : Generate each variant ─────────────────────────────
            $variants = [];
            for ($i = 0; $i < $variantCount; $i++) {
                Log::info("Generating variant " . ($i + 1) . "/$variantCount");

                // 2. AI text refinement
                $refined = $refiner->refine($transcript, $this->post->template, $photoCount, $templateOptions);
                if ($company) {
                    $refined = $this->correctKnownTermsInJson($refined, $company);
                }
                $refined = $fitter->fit($refined, $this->post->template, $templateOptions);

                // Temporarily set refined_content + unique run ID so each variant
                // gets its own image filenames (avoids overwriting between variants)
                $variantRunId = $this->post->id . '_v' . $i . '_' . uniqid();
                $variantOptions = array_merge($templateOptions, ['_variant_run' => $variantRunId]);
                $this->post->update([
                    'refined_content' => $refined,
                    'template_options' => $variantOptions,
                ]);
                $this->post->refresh();

                // 3. Image generation
                $result = $generator->generate($this->post);
                $slides    = is_array($result) ? $result : [$result];
                $imagePath = $slides[0];

                $variant = [
                    'slides'       => $slides,
                    'image_path'   => $imagePath,
                    'refined_content' => $refined,
                ];

                // 4. Facebook dimensions
                if (in_array('facebook', $platforms)) {
                    $fbResult = $generator->generateForFacebook($this->post);
                    $variant['facebook_slides']     = is_array($fbResult) ? $fbResult : [$fbResult];
                    $variant['facebook_image_path'] = is_array($fbResult) ? $fbResult[0] : $fbResult;
                }

                $variants[] = $variant;
                $this->post->increment('variants_generated');
                Log::info("Variant " . ($i + 1) . " done");
            }

            // ── Set first variant as main post data, store all variants ────────
            $first = $variants[0];
            $this->post->update([
                'status'          => Post::STATUS_REVIEW_PENDING,
                'refined_content' => $first['refined_content'],
                'image_path'      => $first['image_path'],
                'slides'          => $first['slides'],
                'facebook_image_path' => $first['facebook_image_path'] ?? null,
                'facebook_slides'     => $first['facebook_slides'] ?? null,
                'post_variants'   => $variants,
            ]);

            Log::info("ProcessPostGeneration completed — $variantCount variant(s) ready for review");

        } catch (\Throwable $e) {
            $errorMsg = "Post Generation Failed: " . $e->getMessage();
            Log::error($errorMsg, [
                'post_id'   => $this->post?->id,
                'exception' => $e->getMessage(),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
            ]);

            // Fallback for terminal visibility
            error_log($errorMsg . " in " . $e->getFile() . ":" . $e->getLine());

            if ($this->post) {
                $this->post->update(['status' => Post::STATUS_FAILED]);
            }

            throw $e;
        }
    }

    /**
     * Correct known company terms (city, name) in a plain text string.
     *
     * Whisper often misspells proper nouns (e.g. "Lannion" → "L'Agnon",
     * "Montpellier" → "Mont-Pelier"). This method finds words that are
     * phonetically similar to known company terms and replaces them.
     */
    private function correctKnownTerms(string $text, $company): string
    {
        $knownTerms = array_filter([
            $company->city,
            $company->name,
        ]);

        foreach ($knownTerms as $correct) {
            if (!$correct || mb_strlen($correct) < 3) continue;
            $text = $this->fuzzyReplace($text, $correct);
        }

        return $text;
    }

    /**
     * Correct known company terms inside a JSON string (refined content).
     */
    private function correctKnownTermsInJson(string $json, $company): string
    {
        $data = json_decode($json, true);
        if (!is_array($data)) return $json;

        $knownTerms = array_filter([
            $company->city,
            $company->name,
        ]);

        foreach ($data as $key => $value) {
            if (is_string($value)) {
                foreach ($knownTerms as $correct) {
                    if (!$correct || mb_strlen($correct) < 3) continue;
                    $data[$key] = $this->fuzzyReplace($value, $correct);
                }
            } elseif (is_array($value)) {
                foreach ($value as $i => $item) {
                    if (is_string($item)) {
                        foreach ($knownTerms as $correct) {
                            if (!$correct || mb_strlen($correct) < 3) continue;
                            $data[$key][$i] = $this->fuzzyReplace($item, $correct);
                        }
                    }
                }
            }
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Find and replace words/phrases that are phonetically similar to the correct term.
     *
     * Uses a combination of:
     * - Normalized string comparison (remove accents, hyphens, apostrophes)
     * - similar_text() percentage for fuzzy matching
     * - Word boundary detection to avoid replacing partial words
     */
    private function fuzzyReplace(string $text, string $correct): string
    {
        $correctNormalized = $this->normalizeForComparison($correct);
        $correctLen = mb_strlen($correct);
        $minLen = max(3, (int)($correctLen * 0.6));
        $maxLen = (int)($correctLen * 1.6);

        // Build a regex pattern that finds candidate words/phrases of similar length
        // Match sequences of word chars, hyphens, apostrophes that could be the misspelled term
        // e.g. "L'Agnon", "Mont-Pelier", "Lanion"
        $pattern = "/[A-ZÀ-ÿa-z][A-ZÀ-ÿa-z'''\-]{" . ($minLen - 1) . "," . ($maxLen - 1) . "}/u";

        return preg_replace_callback($pattern, function ($match) use ($correct, $correctNormalized) {
            $candidate = $match[0];

            // Skip if it's already the correct term
            if ($candidate === $correct) return $candidate;

            $candidateNormalized = $this->normalizeForComparison($candidate);

            // Skip if normalized forms are identical (already correct, just different casing)
            if (mb_strtolower($candidateNormalized) === mb_strtolower($correctNormalized)) {
                return $correct;
            }

            // Compare normalized forms
            similar_text(
                mb_strtolower($candidateNormalized),
                mb_strtolower($correctNormalized),
                $percent
            );

            // Threshold: 70%+ similarity on normalized forms = likely the same word
            if ($percent >= 70) {
                Log::info("Fuzzy term correction", [
                    'from' => $candidate,
                    'to' => $correct,
                    'similarity' => round($percent, 1),
                ]);
                return $correct;
            }

            return $candidate;
        }, $text);
    }

    /**
     * Normalize a string for comparison: remove accents, hyphens, apostrophes.
     * "L'Agnon" → "LAgnon", "Mont-Pelier" → "MontPelier", "Lannion" → "Lannion"
     */
    private function normalizeForComparison(string $str): string
    {
        // Remove hyphens, apostrophes, spaces
        $str = str_replace(['-', "'", "'", "'", ' '], '', $str);

        // Transliterate accented characters
        if (function_exists('transliterator_transliterate')) {
            $str = transliterator_transliterate('Any-Latin; Latin-ASCII', $str);
        } else {
            $str = strtr($str, [
                'à' => 'a', 'â' => 'a', 'ä' => 'a', 'é' => 'e', 'è' => 'e',
                'ê' => 'e', 'ë' => 'e', 'î' => 'i', 'ï' => 'i', 'ô' => 'o',
                'ö' => 'o', 'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ç' => 'c',
                'À' => 'A', 'Â' => 'A', 'Ä' => 'A', 'É' => 'E', 'È' => 'E',
                'Ê' => 'E', 'Ë' => 'E', 'Î' => 'I', 'Ï' => 'I', 'Ô' => 'O',
                'Ö' => 'O', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ç' => 'C',
            ]);
        }

        return $str;
    }
}
