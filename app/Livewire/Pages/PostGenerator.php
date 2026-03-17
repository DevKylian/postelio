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
    public $status = 'idle'; // idle, uploading, processing, completed, failed
    public $generatedPostId;

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

        if ($post->status === 'completed') {
            return redirect()->route('posts.index')->with('success', 'Votre post a été généré avec succès !');
        } else {
            $this->status = 'failed';
        }
    }

    public function render()
    {
        return view('livewire.pages.post-generator');
    }
}
