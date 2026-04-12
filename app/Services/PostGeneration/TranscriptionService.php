<?php

namespace App\Services\PostGeneration;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Storage;

class TranscriptionService
{
    /**
     * Transcribe an audio file using OpenAI Whisper.
     *
     * @param string      $audioPath    Path relative to the public disk.
     * @param string|null $whisperPrompt Optional prompt to prime Whisper with proper nouns
     *                                   (city names, trade vocabulary, etc.) so it can spell
     *                                   them correctly even when spoken with a regional accent.
     *                                   Max ~224 tokens. Should be in the same language as the audio.
     */
    public function transcribe(string $audioPath, ?string $whisperPrompt = null): string
    {
        $params = [
            'model'           => 'whisper-1',
            'file'            => fopen(Storage::disk('public')->path($audioPath), 'r'),
            'response_format' => 'text',
            'language'        => 'fr',
        ];

        if ($whisperPrompt) {
            $params['prompt'] = $whisperPrompt;
        }

        $response = OpenAI::audio()->transcribe($params);

        return $response->text;
    }

    /**
     * Build a Whisper priming prompt from a company's geographic context.
     * Lists nearby commune names so Whisper recognises them even when spoken
     * with a regional accent or phonetic approximation.
     *
     * Kept short (≤ 200 tokens) because Whisper only uses the last 224 tokens.
     */
    public static function buildGeoPrompt(?string $city, ?string $postalCode, array $nearbyCities = []): ?string
    {
        if (!$city && empty($nearbyCities)) {
            return null;
        }

        // Start with a natural-language sentence Whisper can latch onto
        $base = $city
            ? "Artisan basé à {$city}" . ($postalCode ? " ({$postalCode})" : "") . "."
            : "Artisan.";

        if (empty($nearbyCities)) {
            return $base;
        }

        // Strip postal codes from stored strings like "Sainte-Néomaye (79260)" → "Sainte-Néomaye"
        $names = array_map(
            fn($c) => preg_replace('/\s*\(\d+\)$/', '', $c),
            $nearbyCities
        );

        // Keep under ~180 tokens: roughly 4 chars/token in French → ~700 chars
        $communeList = implode(', ', $names);
        if (strlen($communeList) > 650) {
            // Trim to fit — keep the first N communes that fit
            $truncated = [];
            $len = 0;
            foreach ($names as $name) {
                if ($len + strlen($name) + 2 > 650) break;
                $truncated[] = $name;
                $len += strlen($name) + 2;
            }
            $communeList = implode(', ', $truncated);
        }

        return $base . " Communes du secteur : " . $communeList . ".";
    }
}
