<?php

namespace App\Services\PostGeneration;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Storage;

class TranscriptionService
{
    /**
     * Transcribe an audio file using OpenAI Whisper.
     */
    public function transcribe(string $audioPath): string
    {
        $response = OpenAI::audio()->transcribe([
            'model' => 'whisper-1',
            'file' => fopen(Storage::disk('public')->path($audioPath), 'r'),
            'response_format' => 'text',
        ]);

        return $response->text;
    }
}
