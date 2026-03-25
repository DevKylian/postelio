<?php

namespace App\Services\PostGeneration;

use Illuminate\Support\Facades\Log;

class TextFitterService
{
    /**
     * Enforce text zone constraints on the AI-generated content.
     *
     * Takes the raw JSON from ContentRefinerService and trims every text
     * field that exceeds the hard limits defined in config/template-text-zones.php.
     *
     * Returns the sanitised JSON string, ready for storage.
     */
    public function fit(string $refinedJson, string $template, array $templateOptions = []): string
    {
        $data = json_decode($refinedJson, true);
        if (!is_array($data)) {
            return $refinedJson;
        }

        $zones = config('template-text-zones');
        $isCarousel = in_array($template, ['chantier_detail', 'step_by_step', 'guide_pedagogique']);

        // ── image_text (single templates) ────────────────────────────────
        if (isset($data['image_text'])) {
            $zone = $this->resolveZone($template, 'image_text', $templateOptions, $zones);
            if ($zone) {
                $data['image_text'] = $this->truncate($data['image_text'], $zone['maxChars']);
            }
        }

        // ── title (carousel templates) ───────────────────────────────────
        if (isset($data['title'])) {
            $zone = $this->resolveZone($template, 'title', $templateOptions, $zones);
            if ($zone) {
                $data['title'] = $this->truncate($data['title'], $zone['maxChars']);
            }
        }

        // ── slides (carousel templates) ──────────────────────────────────
        if (isset($data['slides']) && is_array($data['slides'])) {
            $zone = $this->resolveZone($template, 'slide', $templateOptions, $zones);
            if ($zone) {
                $data['slides'] = array_map(
                    fn(string $s) => $this->truncate($s, $zone['maxChars']),
                    $data['slides']
                );
            }
        }

        // ── caption ─────────────────────────────────────────────────────
        if (isset($data['caption'])) {
            $captionType = $isCarousel ? 'carousel' : 'standard';
            $maxCaption = $zones['caption'][$captionType]['maxChars'] ?? 500;
            $data['caption'] = $this->truncate($data['caption'], $maxCaption);
        }

        $fitted = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($fitted !== $refinedJson) {
            Log::info('TextFitterService: content was trimmed to fit text zones', [
                'template' => $template,
            ]);
        }

        return $fitted;
    }

    /**
     * Look up the text zone config for a given template + field.
     */
    private function resolveZone(string $template, string $field, array $templateOptions, array $zones): ?array
    {
        // Custom templates → resolve by layout_type
        if ($template === 'custom') {
            $layout = $templateOptions['custom_layout_type'] ?? 'standard';
            return $zones['custom'][$layout] ?? $zones['custom']['standard'];
        }

        // Native templates
        return $zones[$template][$field] ?? null;
    }

    /**
     * Truncate text intelligently, never breaking mid-expression.
     *
     * Strategy:
     *  1. If text fits, return as-is.
     *  2. Try to cut at the last sentence-ending punctuation within limit.
     *  3. Cut at last word boundary, but ONLY at a "meaningful" break point
     *     (avoids cutting after prepositions/articles like "sur", "de", "une").
     *  4. If no clean break found, remove trailing incomplete words.
     */
    public function truncate(string $text, int $maxChars): string
    {
        $text = trim($text);

        if (mb_strlen($text) <= $maxChars) {
            return $text;
        }

        // Try to find a sentence/clause end (. ! ? :) within maxChars
        $sub = mb_substr($text, 0, $maxChars);

        // First try sentence-ending punctuation
        $lastSentenceEnd = max(
            mb_strrpos($sub, '.') ?: 0,
            mb_strrpos($sub, '!') ?: 0,
            mb_strrpos($sub, '?') ?: 0,
        );
        if ($lastSentenceEnd > $maxChars * 0.5) {
            return trim(mb_substr($text, 0, $lastSentenceEnd + 1));
        }

        // Try clause separators (: — –) — cut BEFORE the separator
        $lastClauseBreak = max(
            mb_strrpos($sub, ' :') ?: 0,
            mb_strrpos($sub, ' —') ?: 0,
            mb_strrpos($sub, ' –') ?: 0,
            mb_strrpos($sub, ' -') ?: 0,
        );
        if ($lastClauseBreak > $maxChars * 0.4) {
            return trim(mb_substr($text, 0, $lastClauseBreak));
        }

        // Never cut inside a hyphenated compound word (savoir-faire, sur-mesure, etc.)
        // If $sub ends mid-compound (e.g. "savoir-"), include the rest of the compound
        if (mb_substr($sub, -1) === '-' || (mb_strlen($text) > $maxChars && mb_substr($text, $maxChars, 1) === '-')) {
            // Find the end of the compound word
            $rest = mb_substr($text, $maxChars);
            if (preg_match('/^-?\S+/', $rest, $m)) {
                $extended = $sub . $m[0];
                // Only allow small overrun for compound words (up to 15 extra chars)
                if (mb_strlen($extended) <= $maxChars + 15) {
                    return trim($extended);
                }
            }
        }

        // Fall back to word boundary, but avoid ending on dangling words
        // (prepositions, articles, conjunctions that make no sense alone)
        $danglingWords = ['de', 'du', 'des', 'le', 'la', 'les', 'un', 'une',
            'sur', 'sous', 'dans', 'en', 'et', 'ou', 'à', 'au', 'aux',
            'pour', 'par', 'avec', 'sans', 'vers', "d'", "l'", "n'",
            'ce', 'cette', 'ces', 'son', 'sa', 'ses', 'mon', 'ma', 'mes',
            'ton', 'ta', 'tes', 'notre', 'votre', 'leur', 'leurs', 'qui', 'que'];

        $lastSpace = mb_strrpos($sub, ' ');
        if ($lastSpace && $lastSpace > $maxChars * 0.3) {
            $candidate = trim(mb_substr($text, 0, $lastSpace));

            // Keep removing trailing dangling words
            $maxIterations = 5;
            while ($maxIterations-- > 0) {
                $lastWord = mb_strtolower(preg_replace('/.*\s+/', '', $candidate));
                if (in_array($lastWord, $danglingWords) && mb_strrpos($candidate, ' ') !== false) {
                    $candidate = trim(mb_substr($candidate, 0, mb_strrpos($candidate, ' ')));
                } else {
                    break;
                }
            }

            if (mb_strlen($candidate) > $maxChars * 0.3) {
                return $candidate;
            }
        }

        // Hard cut as last resort (should rarely happen)
        return trim(mb_substr($text, 0, $maxChars));
    }
}
