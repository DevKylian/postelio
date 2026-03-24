<?php

use App\Services\FacebookService;
use Livewire\Volt\Component;

new class extends Component {

    public ?array $page = null;
    public array $allPosts = [];
    public array $posts = [];
    public ?string $error = null;
    public string $sortBy = 'recent';

    public array $sortedPosts = [];
    public int $totalLikes = 0;
    public int $totalComments = 0;
    public int $totalShares = 0;
    public int $avgLikes = 0;
    public int $avgComments = 0;
    public array $topPosts = [];

    // Pagination
    public int $currentPage = 1;
    public int $perPage = 12;
    public bool $hasMore = false;

    public function mount(FacebookService $facebook): void
    {
        $company = auth()->user()->company;

        if (!$company || !$company->facebook_connected) {
            $this->error = 'not_connected';
            return;
        }

        try {
            $this->page = $facebook->getPageStats(
                '',
                $company->zernio_facebook_account_id
            );
            
            $result = $facebook->getRecentPosts(
                '',
                $company->zernio_facebook_account_id,
                1,
                1000 // Zernio returns all
            );
            
            $this->allPosts = $result['media'] ?? [];
            $this->applyPagination();
            $this->computeStats();
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
        }
    }

    public function loadMore(): void
    {
        if (!$this->hasMore) return;
        $this->currentPage++;
        $this->applyPagination();
        $this->computeStats();
    }

    private function applyPagination(): void
    {
        $this->posts = array_slice($this->allPosts, 0, $this->currentPage * $this->perPage);
        $this->hasMore = count($this->allPosts) > count($this->posts);
    }

    private function computeStats(): void
    {
        $this->totalLikes    = array_sum(array_column($this->allPosts, 'like_count'));
        $this->totalComments = array_sum(array_column($this->allPosts, 'comments_count'));
        $this->totalShares   = array_sum(array_column($this->allPosts, 'shares_count'));
        $count               = count($this->allPosts);
        $this->avgLikes      = $count > 0 ? (int) round($this->totalLikes / $count) : 0;
        $this->avgComments   = $count > 0 ? (int) round($this->totalComments / $count) : 0;

        $sorted = $this->allPosts;
        usort($sorted, fn($a, $b) =>
            (($b['like_count'] ?? 0) + ($b['comments_count'] ?? 0) + ($b['shares_count'] ?? 0)) <=>
            (($a['like_count'] ?? 0) + ($a['comments_count'] ?? 0) + ($a['shares_count'] ?? 0))
        );
        $this->topPosts = array_slice($sorted, 0, 3);
        $this->recomputeSortedPosts();
    }

    private function recomputeSortedPosts(): void
    {
        $posts = $this->posts;

        if ($this->sortBy === 'likes') {
            usort($posts, fn($a, $b) => ($b['like_count'] ?? 0) <=> ($a['like_count'] ?? 0));
        } elseif ($this->sortBy === 'comments') {
            usort($posts, fn($a, $b) => ($b['comments_count'] ?? 0) <=> ($a['comments_count'] ?? 0));
        } elseif ($this->sortBy === 'shares') {
            usort($posts, fn($a, $b) => ($b['shares_count'] ?? 0) <=> ($a['shares_count'] ?? 0));
        }

        $this->sortedPosts = $posts;
    }

    public function sortPosts(string $by): void
    {
        $this->sortBy = $by;
        $this->recomputeSortedPosts();
    }
};

?>

<div class="py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">

        {{-- Error: not connected --}}
        @if($error === 'not_connected')
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-[#1877F2] flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-midnight text-lg mb-2">Page Facebook non connectée</h3>
                <p class="text-gray-400 text-sm mb-6 max-w-xs mx-auto">Connectez votre Page Facebook pour visualiser vos statistiques.</p>
                <a href="{{ route('post-settings') }}" wire:navigate
                   class="inline-flex items-center gap-2 bg-[#1877F2] text-white text-sm font-semibold px-5 py-2.5 rounded-xl hover:opacity-90 transition-opacity">
                    Connecter Facebook
                </a>
            </div>

        @elseif($error)
            <div class="bg-red-50 border border-red-100 rounded-2xl p-6">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-red-700 text-sm">Erreur lors du chargement des statistiques</h4>
                        <p class="text-red-500 text-xs mt-1 font-mono">{{ $error }}</p>
                    </div>
                </div>
            </div>

        @else

        {{-- Page Profile Card --}}
        @if($page)
        <div class="bg-midnight rounded-2xl p-6 relative overflow-hidden">
            <div class="absolute -top-12 -right-12 w-48 h-48 bg-blue-500/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-8 -left-8 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
            <div class="relative flex flex-col sm:flex-row items-start sm:items-center gap-5">
                {{-- Avatar --}}
                <div class="relative shrink-0">
                    @if(!empty($page['picture_url']))
                        <img src="{{ $page['picture_url'] }}" alt="{{ $page['name'] }}"
                             class="w-16 h-16 rounded-2xl object-cover ring-2 ring-white/10">
                    @else
                        <div class="w-16 h-16 rounded-2xl bg-[#1877F2] flex items-center justify-center ring-2 ring-white/10">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </div>
                    @endif
                    <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-[#1877F2] border-2 border-midnight flex items-center justify-center">
                        <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>

                <div class="flex-1 min-w-0">
                    <h2 class="text-white font-bold text-xl leading-tight">{{ $page['name'] }}</h2>
                    @if(!empty($page['category']))
                        <p class="text-white/40 text-sm mt-0.5">{{ $page['category'] }}</p>
                    @endif
                </div>

                {{-- KPIs --}}
                <div class="flex gap-6 sm:gap-8 shrink-0">
                    <div class="text-center">
                        <p class="text-white font-bold text-xl">{{ number_format($page['followers_count'] ?? 0) }}</p>
                        <p class="text-white/40 text-xs mt-0.5">Abonnés</p>
                    </div>
                    <div class="text-center">
                        <p class="text-white font-bold text-xl">{{ count($allPosts) }}</p>
                        <p class="text-white/40 text-xs mt-0.5">Publications</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Engagement Stats --}}
        @if(count($allPosts) > 0)
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 group card-hover">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Réactions</p>
                <p class="text-2xl font-bold text-midnight mt-1">{{ number_format($totalLikes) }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 group card-hover">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Moy. Réactions</p>
                <p class="text-2xl font-bold text-midnight mt-1">{{ number_format($avgLikes) }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 group card-hover">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Commentaires</p>
                <p class="text-2xl font-bold text-midnight mt-1">{{ number_format($totalComments) }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 group card-hover">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Partages</p>
                <p class="text-2xl font-bold text-midnight mt-1">{{ number_format($totalShares) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Top Performers --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden h-fit">
                <div class="px-6 py-4 border-b border-gray-50">
                    <h3 class="font-semibold text-midnight text-sm">🏆 Meilleures performances</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($topPosts as $post)
                    <div class="flex items-center gap-3 p-4">
                        @if(!empty($post['full_picture']))
                            <img src="{{ $post['full_picture'] }}" class="w-12 h-12 rounded-xl object-cover shrink-0">
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-400 truncate">{{ $post['message'] ?: 'Sans texte' }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Post Feed --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                        <h3 class="font-semibold text-midnight text-sm">Publications</h3>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @foreach($sortedPosts as $post)
                        <div class="flex items-start gap-4 p-4">
                            @if(!empty($post['full_picture']))
                                <img src="{{ $post['full_picture'] }}" class="w-16 h-16 rounded-xl object-cover shrink-0">
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-600 line-clamp-2">{{ $post['message'] ?: 'Publication sans texte' }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                @if($hasMore)
                <div class="flex justify-center">
                    <button wire:click="loadMore" class="bg-white border border-gray-100 shadow-sm px-6 py-2.5 rounded-xl text-sm font-semibold">Voir plus</button>
                </div>
                @endif
            </div>
        </div>
        @endif

        @endif
    </div>
</div>
