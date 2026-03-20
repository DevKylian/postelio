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

    public $timeout = 300;

    public function __construct(public Post $post)
    {
        //
    }

    public function handle(
        TranscriptionService $transcriber,
        ContentRefinerService $refiner,
        ImageGeneratorService $generator
    ): void {
        Log::info("ProcessPostGeneration handle started", ['post_id' => $this->post->id]);
        set_time_limit(300);
        try {
            $this->post->update(['status' => 'processing']);

            // 1. Transcription
            $textContent = $this->post->template_options['text_content'] ?? null;
            $audioPath   = $this->post->audio_path;

            if ($audioPath) {
                Log::info("Starting transcription", ['path' => $audioPath]);
                $transcript = $transcriber->transcribe($audioPath);
                Log::info("Transcription finished", ['length' => strlen($transcript)]);
            } elseif ($textContent) {
                Log::info("Using text content instead of audio");
                $transcript = $textContent;
            } else {
                throw new Exception("Aucun contenu (audio ou texte) fourni pour la génération.");
            }

            $this->post->update(['transcription' => $transcript]);

            // 2. AI content refinement
            Log::info("Starting AI refinement", ['template' => $this->post->template]);
            $photoCount = count($this->post->extra_images ?? []);
            $templateOptions = $this->post->template_options ?? [];
            $refined = $refiner->refine($transcript, $this->post->template, $photoCount, $templateOptions);
            Log::info("AI refinement finished");
            $this->post->update(['refined_content' => $refined]);

            // 3. Image generation
            Log::info("Starting image generation");
            $result = $generator->generate($this->post);
            Log::info("Image generation finished");

            $updateData = ['status' => 'review_pending'];

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

            Log::info("ProcessPostGeneration handle completed successfully");

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
                $this->post->update(['status' => 'failed']);
            }

            throw $e;
        }
    }
}
