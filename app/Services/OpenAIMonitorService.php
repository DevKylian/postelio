<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class OpenAIMonitorService
{
    private ?string $adminKey;
    private string $baseUrl = 'https://api.openai.com/v1';

    public function __construct()
    {
        $this->adminKey = config('openai.admin_key');
    }

    /**
     * Get the estimated costs for the current month.
     * Returns ['total_usd' => float, 'currency' => string, 'buckets' => array]
     */
    public function getMonthlyUsage(): array
    {
        if (!$this->adminKey) {
            return [];
        }

        return Cache::remember('openai_monthly_usage_v3', 3600, function () {
            try {
                $startTime = Carbon::now()->startOfMonth()->timestamp;
                $endTime = Carbon::now()->timestamp;

                $totalUsd = 0;

                // 1. Fetch Completions Usage (GPT)
                $compResp = Http::withoutVerifying()->withToken($this->adminKey)
                    ->get("{$this->baseUrl}/organization/usage/completions", [
                        'start_time' => $startTime,
                        'end_time'   => $endTime
                    ]);

                if ($compResp->successful()) {
                    foreach ($compResp->json('data', []) as $bucket) {
                        foreach ($bucket['results'] ?? [] as $result) {
                            $uncached = (int) ($result['input_uncached_tokens'] ?? 0);
                            $cached   = (int) ($result['input_cached_tokens'] ?? 0);
                            $output   = (int) ($result['output_tokens'] ?? 0);

                            // GPT-4o-2024-08-06 Prices
                            $totalUsd += ($uncached / 1000000) * 2.50;
                            $totalUsd += ($cached / 1000000) * 1.25;
                            $totalUsd += ($output / 1000000) * 10.00;
                        }
                    }
                }

                // 2. Fetch Audio Usage (Whisper)
                $audioResp = Http::withoutVerifying()->withToken($this->adminKey)
                    ->get("{$this->baseUrl}/organization/usage/audio_transcriptions", [
                        'start_time' => $startTime,
                        'end_time'   => $endTime
                    ]);

                if ($audioResp->successful()) {
                    foreach ($audioResp->json('data', []) as $bucket) {
                        foreach ($bucket['results'] ?? [] as $result) {
                            $seconds = (int) ($result['num_seconds'] ?? 0);
                            // Whisper Price: $0.006 / minute
                            $totalUsd += ($seconds / 60) * 0.006;
                        }
                    }
                }

                return [
                    'total_usd' => $totalUsd,
                    'currency'  => 'USD',
                    'updated_at' => now()->toDateTimeString(),
                    'method'     => 'realtime_calc'
                ];

            } catch (\Exception $e) {
                Log::error('OpenAIMonitor: Exception while calculating usage', [
                    'message' => $e->getMessage()
                ]);
                return [];
            }
        });
    }

    /**
     * Helper to get the first organization ID associated with the admin key.
     */
    private function getOrganizationId(): ?string
    {
        return Cache::remember('openai_org_id', 86400, function () {
            $response = Http::withoutVerifying()->withToken($this->adminKey)
                ->get("{$this->baseUrl}/organizations");

            if ($response->successful()) {
                return $response->json('data.0.id');
            }

            return null;
        });
    }
}
