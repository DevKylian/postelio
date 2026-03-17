<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Post;
use App\Jobs\ProcessPostGeneration;
use Illuminate\Support\Facades\Auth;

class PostGenerator extends Component
{
    use WithFileUploads;

    public $audio;
    public $imageBefore;
    public $imageAfter;
    public $template = 'review';
    public $status = 'idle'; // idle, processing, review_pending, completed, failed
    public $generatedPostId;
    public $previewImageUrl;
    public $previewContent;

    protected function rules()
    {
        return [
            'audio' => 'required|max:10240',
            'template' => 'required|in:review,project,faq,lifestyle',
            'imageBefore' => in_array($this->template, ['project', 'lifestyle']) ? 'required|image|max:5120' : 'nullable',
            'imageAfter' => $this->template === 'project' ? 'required|image|max:5120' : 'nullable',
        ];
    }

    public function generate()
    {
        $this->validate();

        $this->status = 'processing';

        // 1. Save Files
        $audioPath = $this->audio->store('memos', 'public');
        $imageBeforePath = $this->imageBefore ? $this->imageBefore->store('projects', 'public') : null;
        $imageAfterPath = $this->imageAfter ? $this->imageAfter->store('projects', 'public') : null;

        // 2. Create Post Record
        $post = Post::create([
            'user_id' => Auth::id(),
            'company_id' => Auth::user()->company->id,
            'audio_path' => $audioPath,
            'image_before_path' => $imageBeforePath,
            'image_after_path' => $imageAfterPath,
            'template' => $this->template,
            'status' => 'pending',
        ]);

        $this->generatedPostId = $post->id;

        // 3. Dispatch Job
        ProcessPostGeneration::dispatch($post);
        
        // Refresh the post to get the status updated by the Job
        $post->refresh();

        if ($post->status === 'review_pending') {
            $this->previewImageUrl = $post->image_path ? \Illuminate\Support\Facades\Storage::url($post->image_path) : null;
            $this->previewContent = $post->refined_content;
            $this->status = 'review_pending';
        } else {
            $this->status = 'failed';
        }
    }

    public function validatePost()
    {
        $post = Post::find($this->generatedPostId);

        if ($post && $post->user_id === \Illuminate\Support\Facades\Auth::id()) {
            $post->update(['status' => 'completed']);
        }

        return redirect()->route('posts.index')->with('success', 'Votre post a été validé et ajouté à vos créations !');
    }

    public function discardPost()
    {
        $post = Post::find($this->generatedPostId);

        if ($post && $post->user_id === \Illuminate\Support\Facades\Auth::id()) {
            foreach ([$post->audio_path, $post->image_path, $post->image_before_path, $post->image_after_path] as $path) {
                if ($path && \Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                }
            }
            $post->delete();
        }

        $this->status = 'idle';
        $this->generatedPostId = null;
        $this->previewImageUrl = null;
        $this->previewContent = null;
    }

    public function render()
    {
        return view('livewire.pages.post-generator');
    }
}
