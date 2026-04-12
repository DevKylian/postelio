<?php

namespace App\Services\PostGeneration;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranscriptionService
{
    /**
     * Transcribe an audio file using OpenAI Whisper.
     *
     * @param string      $audioPath     Path relative to the public disk.
     * @param string|null $whisperPrompt Optional prompt to prime Whisper with proper nouns.
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
     * Fetch the list of communes within 35 km of a given city/postal code.
     *
     * Uses the French government's geo API. Results are cached for 24 hours
     * so repeated calls (multiple posts in a day) cost nothing.
     *
     * Returns an array of strings like ["Sainte-Néomaye (79260)", "Aiffres (79230)", ...]
     * Returns [] on any network/API failure (non-blocking).
     */
    public static function fetchNearbyCities(string $city, string $postalCode): array
    {
        $cacheKey = 'nearby_cities_' . md5($city . $postalCode);

        return Cache::remember($cacheKey, 86400, function () use ($city, $postalCode) {
            try {
                // 1. Resolve coordinates from city + postal code
                $addrResp = Http::timeout(6)->get('https://api-adresse.data.gouv.fr/search/', [
                    'q'     => $city . ' ' . $postalCode,
                    'type'  => 'municipality',
                    'limit' => 1,
                ]);

                $features = $addrResp->json('features', []);
                if (empty($features)) {
                    Log::warning("[GeoFetch] No address result for {$city} ({$postalCode})");
                    return [];
                }

                [$lng, $lat] = $features[0]['geometry']['coordinates'];

                // 2. Fetch communes within 35 km
                $geoResp = Http::timeout(6)->get('https://geo.api.gouv.fr/communes', [
                    'lat'      => $lat,
                    'lon'      => $lng,
                    'distance' => 35000,
                    'fields'   => 'nom,codesPostaux',
                    'boost'    => 'population',
                    'limit'    => 60,
                    'type'     => 'commune-actuelle',
                ]);

                $communes = $geoResp->json() ?? [];

                $result = array_map(
                    fn($c) => $c['nom'] . ' (' . ($c['codesPostaux'][0] ?? '') . ')',
                    $communes
                );

                Log::info("[GeoFetch] Fetched " . count($result) . " communes around {$city}");
                return $result;

            } catch (\Throwable $e) {
                Log::warning("[GeoFetch] Failed: " . $e->getMessage());
                return [];
            }
        });
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
        if (!$city) {
            return null;
        }

        $base = "Artisan basé à {$city}" . ($postalCode ? " ({$postalCode})" : "") . ".";

        if (empty($nearbyCities)) {
            return $base;
        }

        $names = array_map(
            fn($c) => preg_replace('/\s*\(\d+\)$/', '', $c),
            $nearbyCities
        );

        $communeList = implode(', ', $names);
        if (strlen($communeList) > 650) {
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
