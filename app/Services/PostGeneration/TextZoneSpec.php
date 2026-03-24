<?php

namespace App\Services\PostGeneration;

/**
 * Defines strict text zone constraints for each custom template layout.
 *
 * Every layout has a precise text area on the 1080×1080 canvas. This class
 * centralises the limits so the AI prompt, the content refiner and the
 * image renderer all share the same source of truth.
 *
 * Constraints are expressed for a 1080×1080 base and scaled proportionally
 * by the ImageGeneratorService when the canvas differs.
 */
class TextZoneSpec
{
    // ── Per-layout specifications ────────────────────────────────────────────
    //
    //  maxChars        → hard character limit for the whole image_text field
    //  maxCharsPerLine → chars per visual line (used by drawText* methods)
    //  maxLines        → maximum visible lines that fit in the zone
    //  fontSize        → base font size at 1080px height
    //  textTopPct      → top of text zone as % of canvas height
    //  textBottomPct   → bottom of text zone as % of canvas height
    //  alignment       → 'left' or 'center'

    private const SPECS = [
        'standard' => [
            'maxChars'        => 90,
            'maxCharsPerLine' => 22,
            'maxLines'        => 5,
            'fontSize'        => 54,
            'textTopPct'      => 0.389,
            'textBottomPct'   => 0.852,
            'alignment'       => 'left',
        ],
        'centered' => [
            'maxChars'        => 80,
            'maxCharsPerLine' => 24,
            'maxLines'        => 4,
            'fontSize'        => 30,
            'textTopPct'      => 0.42,
            'textBottomPct'   => 0.82,
            'alignment'       => 'center',
        ],
        'quote' => [
            'maxChars'        => 90,
            'maxCharsPerLine' => 22,
            'maxLines'        => 5,
            'fontSize'        => 32,
            'textTopPct'      => 0.26,
            'textBottomPct'   => 0.80,
            'alignment'       => 'center',
        ],
        'bold_title' => [
            'maxChars'        => 110,
            'maxCharsPerLine' => 28,
            'maxLines'        => 5,
            'fontSize'        => 26,
            'textTopPct'      => 0.36,
            'textBottomPct'   => 0.83,
            'alignment'       => 'left',
        ],
        'split' => [
            'maxChars'        => 100,
            'maxCharsPerLine' => 26,
            'maxLines'        => 5,
            'fontSize'        => 24,
            'textTopPct'      => 0.515,
            'textBottomPct'   => 0.83,
            'alignment'       => 'left',
        ],
        'minimal' => [
            'maxChars'        => 120,
            'maxCharsPerLine' => 30,
            'maxLines'        => 5,
            'fontSize'        => 26,
            'textTopPct'      => 0.435,
            'textBottomPct'   => 0.87,
            'alignment'       => 'left',
        ],
    ];

    /**
     * Return the full spec array for a given layout type.
     */
    public static function for(string $layoutType): array
    {
        return self::SPECS[$layoutType] ?? self::SPECS['standard'];
    }

    /**
     * Max total characters for the image_text field.
     */
    public static function maxChars(string $layoutType): int
    {
        return self::for($layoutType)['maxChars'];
    }

    /**
     * Max characters per visual line.
     */
    public static function maxCharsPerLine(string $layoutType): int
    {
        return self::for($layoutType)['maxCharsPerLine'];
    }

    /**
     * Base font size (at 1080px canvas height).
     */
    public static function fontSize(string $layoutType): int
    {
        return self::for($layoutType)['fontSize'];
    }

    /**
     * Max visible text lines in the zone.
     */
    public static function maxLines(string $layoutType): int
    {
        return self::for($layoutType)['maxLines'];
    }

    /**
     * Build human-readable constraint text suitable for injection into an AI prompt.
     */
    public static function promptConstraint(string $layoutType): string
    {
        $spec = self::for($layoutType);

        return "CONTRAINTES DE ZONE TEXTE (OBLIGATOIRE) :\n"
            . "- Le champ image_text doit faire MAXIMUM {$spec['maxChars']} caractères (espaces compris).\n"
            . "- Chaque ligne visuelle affiche environ {$spec['maxCharsPerLine']} caractères.\n"
            . "- L'image ne peut afficher que {$spec['maxLines']} lignes de texte maximum.\n"
            . "- Sois concis, percutant et direct. Privilégie les phrases courtes.\n"
            . "- Si le contenu est trop long, raccourcis en gardant l'essentiel — ne dépasse JAMAIS la limite.\n"
            . "- image_text ne doit JAMAIS contenir de hashtags, d'emojis ni de ponctuation lourde.";
    }

    /**
     * Compute how many lines a given text would occupy in this layout.
     */
    public static function countLines(string $text, string $layoutType): int
    {
        $maxCharsPerLine = self::maxCharsPerLine($layoutType);
        $words   = explode(' ', $text);
        $lines   = 0;
        $current = '';

        foreach ($words as $word) {
            $candidate = $current ? $current . ' ' . $word : $word;
            if (mb_strlen($candidate) <= $maxCharsPerLine) {
                $current = $candidate;
            } else {
                $lines++;
                $current = $word;
            }
        }
        if ($current !== '') {
            $lines++;
        }

        return $lines;
    }

    /**
     * Smart-truncate text to fit within the layout's constraints.
     * Cuts at word boundaries and adds "…" if truncated.
     */
    public static function fitText(string $text, string $layoutType): string
    {
        $spec = self::for($layoutType);
        $maxChars = $spec['maxChars'];

        // Already fits
        if (mb_strlen($text) <= $maxChars && self::countLines($text, $layoutType) <= $spec['maxLines']) {
            return $text;
        }

        // Truncate by total chars first
        if (mb_strlen($text) > $maxChars) {
            $text = mb_substr($text, 0, $maxChars);
            // Cut at last space
            $lastSpace = mb_strrpos($text, ' ');
            if ($lastSpace !== false && $lastSpace > $maxChars * 0.6) {
                $text = mb_substr($text, 0, $lastSpace);
            }
        }

        // Then check line count — trim words until it fits
        while (self::countLines($text, $layoutType) > $spec['maxLines']) {
            $lastSpace = mb_strrpos($text, ' ');
            if ($lastSpace === false) break;
            $text = mb_substr($text, 0, $lastSpace);
        }

        return $text;
    }

    /**
     * List all known layout types.
     */
    public static function layoutTypes(): array
    {
        return array_keys(self::SPECS);
    }
}
