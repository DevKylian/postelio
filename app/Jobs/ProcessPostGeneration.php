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
        set_time_limit(300);
        try {
            $this->post->update(['status' => Post::STATUS_PROCESSING]);

            // 1. Transcription / Content Acquisition
            $textContent = $this->post->template_options['text_content'] ?? null;
            $audioPath   = $this->post->audio_path;
            $transcript  = null;

            if ($textContent) {
                Log::info("Using provided text content (transcript from UI or manual entry)");
                $transcript = $textContent;
            } elseif ($audioPath) {
                Log::info("Starting transcription from audio file", ['path' => $audioPath]);
                $transcript = $transcriber->transcribe($audioPath);
                Log::info("Transcription finished", ['length' => strlen($transcript)]);
            } else {
                throw new Exception("Aucun contenu (audio ou texte) fourni pour la génération.");
            }

            // 1b. Correct known company terms in transcript (city, name, trade)
            $company = $this->post->company;
            if ($company) {
                $transcript = $this->correctKnownTerms($transcript, $company);
            }

            $this->post->update(['transcription' => $transcript]);

            // 2. AI content refinement
            Log::info("Starting AI refinement", ['template' => $this->post->template]);
            $photoCount = count($this->post->extra_images ?? []);
            $templateOptions = $this->post->template_options ?? [];
            if ($company) {
                $company->refresh(); // CRITICAL: Get latest city/phone from DB
                $templateOptions['company_city']  = $company->city;
                $templateOptions['company_name']  = $company->name;
                $templateOptions['company_trade'] = $company->trade;
                $templateOptions['company_phone'] = $company->phone;
                $templateOptions['company_email'] = $company->email;
                // Inject artisan bio for personalized AI generation
                if (!empty($company->bio_text)) {
                    $templateOptions['artisan_bio'] = $company->bio_text;
                }
            }
            $refined = $refiner->refine($transcript, $this->post->template, $photoCount, $templateOptions);
            Log::info("AI refinement finished");

            // 2b. Correct known company terms in refined content
            if ($company) {
                $refined = $this->correctKnownTermsInJson($refined, $company);
            }

            // 2c. Enforce text zone constraints (truncate if AI exceeded limits)
            $refined = $fitter->fit($refined, $this->post->template, $templateOptions);
            Log::info("Text fitting applied");

            $this->post->update(['refined_content' => $refined]);

            // 3. Image generation
            Log::info("Starting image generation");
            $result = $generator->generate($this->post);
            Log::info("Image generation finished");

            $updateData = ['status' => Post::STATUS_REVIEW_PENDING];

            if (is_array($result)) {
                $updateData['image_path'] = $result[0];
                $updateData['slides']     = $result;
            } else {
                $updateData['image_path'] = $result;
                $updateData['slides']     = [$result];
            }

            $this->post->update($updateData);

            // 4. Facebook dimensions
            $platforms = $this->post->platforms ?? ['instagram'];
            if (in_array('facebook', $platforms)) {
                Log::info("Starting Facebook image generation");
                $this->post->refresh();
                $fbResult = $generator->generateForFacebook($this->post);
                Log::info("Facebook image generation finished");

                if (is_array($fbResult)) {
                    $this->post->update([
                        'facebook_image_path' => $fbResult[0],
                        'facebook_slides'     => $fbResult,
                    ]);
                } else {
                    $this->post->update([
                        'facebook_image_path' => $fbResult,
                        'facebook_slides'     => [$fbResult],
                    ]);
                }
            }

            Log::info("ProcessPostGeneration handle completed successfully — waiting for user validation");

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
