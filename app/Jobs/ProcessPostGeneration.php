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

            // 1. Transcription (or use text_content directly when no audio)
            $textContent = $this->post->template_options['text_content'] ?? null;
            if (!$this->post->audio_path && $textContent) {
                $transcript = $textContent;
            } else {
                $transcript = $transcriber->transcribe($this->post->audio_path);
            }
            $this->post->update(['transcription' => $transcript]);

            // 2. AI content refinement
            $photoCount = count($this->post->extra_images ?? []);
            $templateOptions = $this->post->template_options ?? [];
            $refined = $refiner->refine($transcript, $this->post->template, $photoCount, $templateOptions);
            $this->post->update(['refined_content' => $refined]);

            // 3. Image generation (single or carousel array)
            $result = $generator->generate($this->post);

            if (is_array($result)) {
                // Carousel: store all slides, use first as thumbnail
                $this->post->update([
                    'image_path' => $result[0],
                    'slides'     => $result,
                    'status'     => 'review_pending',
                ]);
            } else {
                // Single image
                $this->post->update([
                    'image_path' => $result,
                    'status'     => 'review_pending',
                ]);
            }

        } catch (Exception $e) {
            Log::error("Post Generation Failed: " . $e->getMessage(), [
                'post_id'   => $this->post->id,
                'exception' => $e,
            ]);

            $this->post->update(['status' => 'failed']);

            throw $e; // re-throw so the Livewire component can show a precise error
        }
    }
}
