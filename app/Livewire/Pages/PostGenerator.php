<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Post;
use App\Jobs\ProcessPostGeneration;
use App\Jobs\PublishToInstagramJob;
use Illuminate\Support\Facades\Auth;

class PostGenerator extends Component
{
    use WithFileUploads;

    public $audio;
    public $imageBefore;
    public $imageAfter;
    public $extraImages = [];        // for chantier_detail & step_by_step (up to 5)
    public $template = 'review';
    public $status = 'idle';
    public $generatedPostId;
    public $previewImageUrl;
    public $previewContent;
    public $previewSlides = [];      // array of URLs for carousel preview

    // Carousel templates
    const CAROUSEL_TEMPLATES  = ['chantier_detail', 'step_by_step', 'guide_pedagogique'];
    // Templates requiring photos
    const PHOTO_TEMPLATES     = ['project', 'lifestyle', 'quality_focus', 'chantier_detail', 'step_by_step'];
    // Templates requiring before+after
    const BEFORE_AFTER_TEMPLATES = ['project'];

    protected function rules()
    {
        $isCarousel     = in_array($this->template, self::CAROUSEL_TEMPLATES);
        $needsPhoto     = in_array($this->template, self::PHOTO_TEMPLATES);
        $needsAfter     = in_array($this->template, self::BEFORE_AFTER_TEMPLATES);
        $needsExtra     = in_array($this->template, ['chantier_detail', 'step_by_step']);

        return [
            'audio'         => 'required|max:10240',
            'template'      => 'required|in:review,project,faq,lifestyle,quality_focus,chantier_detail,step_by_step,guide_pedagogique',
            'imageBefore'   => ($needsPhoto && !$needsExtra) ? 'required|image|max:5120' : 'nullable|image|max:5120',
            'imageAfter'    => $needsAfter ? 'required|image|max:5120' : 'nullable',
            'extraImages'   => $needsExtra ? 'required|array|min:2|max:5' : 'nullable|array',
            'extraImages.*' => 'image|max:5120',
        ];
    }

    public function generate()
    {
        $this->validate();

        $this->status = 'processing';

        // Store files
        $audioPath       = $this->audio->store('memos', 'public');
        $imageBeforePath = $this->imageBefore ? $this->imageBefore->store('projects', 'public') : null;
        $imageAfterPath  = $this->imageAfter  ? $this->imageAfter->store('projects', 'public')  : null;

        $extraImagePaths = [];
        foreach ($this->extraImages as $img) {
            $extraImagePaths[] = $img->store('projects', 'public');
        }

        $post = Post::create([
            'user_id'           => Auth::id(),
            'company_id'        => Auth::user()->company->id,
            'audio_path'        => $audioPath,
            'image_before_path' => $imageBeforePath,
            'image_after_path'  => $imageAfterPath,
            'extra_images'      => count($extraImagePaths) ? $extraImagePaths : null,
            'template'          => $this->template,
            'status'            => 'pending',
        ]);

        $this->generatedPostId = $post->id;

        try {
            ProcessPostGeneration::dispatchSync($post);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('PostGenerator: job failed', [
                'post_id' => $post->id,
                'error'   => $e->getMessage(),
            ]);
            $this->status = 'failed';
            return;
        }

        $post->refresh();

        if ($post->status === 'review_pending') {
            $content = $post->refined_content;
            $decoded = json_decode($content, true);
            
            if (json_last_error() === JSON_ERROR_NONE && isset($decoded['caption'])) {
                $this->previewContent = $decoded['caption'];
            } else {
                $this->previewContent = $content;
            }

            if ($post->slides && count($post->slides) > 0) {
                $this->previewSlides  = array_map(
                    fn($p) => \Illuminate\Support\Facades\Storage::url($p),
                    $post->slides
                );
                $this->previewImageUrl = $this->previewSlides[0];
            } else {
                $this->previewImageUrl = $post->image_path
                    ? \Illuminate\Support\Facades\Storage::url($post->image_path)
                    : null;
                $this->previewSlides = [];
            }

            $this->status = 'review_pending';
        } else {
            $this->status = 'failed';
        }
    }

    public function validatePost()
    {
        $post = Post::find($this->generatedPostId);

        if ($post && $post->user_id === Auth::id()) {
            $post->update(['status' => 'completed']);

            $company = Auth::user()->company;
            if ($company && $company->instagram_connected) {
                PublishToInstagramJob::dispatch($post->id);
            }
        }

        return redirect()->route('posts.index')->with('success', 'Votre post a été validé et ajouté à vos créations !');
    }

    public function discardPost()
    {
        $post = Post::find($this->generatedPostId);

        if ($post && $post->user_id === Auth::id()) {
            $allPaths = array_filter(array_merge(
                [$post->audio_path, $post->image_path, $post->image_before_path, $post->image_after_path],
                $post->slides       ?? [],
                $post->extra_images ?? []
            ));
            foreach ($allPaths as $path) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                }
            }
            $post->delete();
        }

        $this->status            = 'idle';
        $this->generatedPostId   = null;
        $this->previewImageUrl   = null;
        $this->previewContent    = null;
        $this->previewSlides     = [];
    }

    public function render()
    {
        return view('livewire.pages.post-generator');
    }
}
