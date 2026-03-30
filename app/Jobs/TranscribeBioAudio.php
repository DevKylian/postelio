<?php

namespace App\Jobs;

use App\Models\Company;
use App\Services\PostGeneration\TranscriptionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TranscribeBioAudio implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(
        public readonly int $companyId,
        public readonly string $audioPath,
    ) {}

    public function handle(TranscriptionService $transcriptionService): void
    {
        $company = Company::find($this->companyId);

        if (! $company) {
            Log::warning('TranscribeBioAudio: company not found', ['id' => $this->companyId]);
            return;
        }

        try {
            $text = $transcriptionService->transcribe($this->audioPath);

            $company->update([
                'bio_text'       => $text,
                'bio_audio_path' => $this->audioPath,
            ]);
        } catch (\Throwable $e) {
            Log::error('TranscribeBioAudio failed', [
                'company_id' => $this->companyId,
                'error'      => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('TranscribeBioAudio permanently failed', [
            'company_id' => $this->companyId,
            'error'      => $e->getMessage(),
        ]);

        // Don't delete the audio file so we can retry manually
    }
}
