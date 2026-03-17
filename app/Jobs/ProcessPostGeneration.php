<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Models\Post;
use App\Services\PostGeneration\TranscriptionService;
use App\Services\PostGeneration\ContentRefinerService;
use App\Services\PostGeneration\ImageGeneratorService;
use Exception;
use Illuminate\Support\Facades\Log;

class ProcessPostGeneration implements ShouldQueue
{
    use Queueable;

    public function __construct(public Post $post)
    {
        //
    }

    public function handle(
        TranscriptionService $transcriber,
        ContentRefinerService $refiner,
        ImageGeneratorService $generator
    ): void {
        try {
            $this->post->update(['status' => 'processing']);

            // 1. Ingestion (Transcription)
            $transcript = $transcriber->transcribe($this->post->audio_path);
            $this->post->update(['transcription' => $transcript]);

            // 2. Formatage Sémantique (IA Texte)
            $refined = $refiner->refine($transcript, $this->post->template);
            $this->post->update(['refined_content' => $refined]);

            // 3. Calcul et Composition Visuelle
            $imagePath = $generator->generate($this->post);

            // 4. Finalisation
            $this->post->update([
                'image_path' => $imagePath,
                'status' => 'completed',
            ]);

        } catch (Exception $e) {
            Log::error("Post Generation Failed: " . $e->getMessage(), [
                'post_id' => $this->post->id,
                'exception' => $e
            ]);

            $this->post->update(['status' => 'failed']);
        }
    }
}
