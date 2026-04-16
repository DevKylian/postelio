<?php

namespace App\Livewire\Admin;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class QueueMonitor extends Component
{
    public function deleteJob(int $id)
    {
        DB::table('jobs')->where('id', $id)->delete();
        session()->flash('success', "Job #{$id} supprimé.");
    }

    public function render()
    {
        $rawJobs = DB::table('jobs')->orderBy('available_at', 'asc')->get();
        $jobs = [];

        foreach ($rawJobs as $raw) {
            $payload = json_decode($raw->payload, true);
            $command = $payload['data']['command'] ?? null;
            $postId  = null;
            $type    = $payload['displayName'] ?? 'Unknown';

            if ($command) {
                try {
                    $decoded = unserialize($command);
                    if (property_exists($decoded, 'postId')) {
                        $postId = $decoded->postId;
                    }
                } catch (\Throwable $e) {
                    if (preg_match('/"postId";i:(\d+);/', $command, $matches)) {
                        $postId = $matches[1];
                    }
                }
            }

            $post = $postId ? Post::with('user.company')->find($postId) : null;

            $jobs[] = [
                'id'           => $raw->id,
                'queue'        => $raw->queue,
                'type'         => str_replace(['App\\Jobs\\', 'Illuminate\\Queue\\'], '', $type),
                'attempts'     => $raw->attempts,
                'available_at' => Carbon::createFromTimestamp($raw->available_at)->isoFormat('lll'),
                'is_reserved'  => $raw->reserved_at !== null,
                'postId'       => $postId,
                'post'         => $post,
            ];
        }

        // Stats
        $stats = [
            'pending'    => DB::table('jobs')->whereNull('reserved_at')->count(),
            'processing' => DB::table('jobs')->whereNotNull('reserved_at')->count(),
            'today'      => Post::whereIn('status', [Post::STATUS_COMPLETED, Post::STATUS_PUBLISHED])
                                ->whereDate('updated_at', Carbon::today())
                                ->count(),
            'failed'     => DB::table('failed_jobs')->count(),
        ];

        return view('livewire.admin.queue-monitor', [
            'jobs'  => $jobs,
            'stats' => $stats,
        ]);
    }
}
