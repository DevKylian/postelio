<?php

use App\Services\InstagramService;
use Livewire\Volt\Component;

new class extends Component {

    public ?array $account = null;
    public array $allMedia = [];
    public array $media = [];
    public ?string $error = null;
    public string $sortBy = 'recent';

    public array $sortedMedia = [];
    public int $totalLikes = 0;
    public int $totalComments = 0;
    public int $avgLikes = 0;
    public int $avgComments = 0;
    public array $topPosts = [];

    // Pagination
    public int $page = 1;
    public int $perPage = 12;
    public bool $hasMore = false;

    public function mount(InstagramService $instagram): void
    {
        $company = auth()->user()->company;

        if (!$company || !$company->instagram_connected) {
            $this->error = 'not_connected';
            return;
        }

        try {
            $this->account = $instagram->getAccountStats(
                '',
                $company->zernio_instagram_account_id
            );
            
            // Fetch everything because Zernio doesn't support limit on this endpoint
            $result = $instagram->getRecentMedia(
                '',
                $company->zernio_instagram_account_id,
                1,
                1000 // High limit just in case, but Zernio returns all
            );
            
            $this->allMedia = $result['media'] ?? [];
            $this->applyPagination();
            $this->computeStats();
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
        }
    }

    public function loadMore(): void
    {
        if (!$this->hasMore) return;
        $this->page++;
        $this->applyPagination();
        // stats are computed on visible media? usually yes for engagement cards
        $this->computeStats();
    }

    private function applyPagination(): void
    {
        $this->media = array_slice($this->allMedia, 0, $this->page * $this->perPage);
        $this->hasMore = count($this->allMedia) > count($this->media);
    }

    private function computeStats(): void
    {
        // Compute stats only on the visible media for dynamic updates, or on allMedia?
        // Let's do allMedia for total KPIs and allMedia for averages to be consistent
        $this->totalLikes    = array_sum(array_column($this->allMedia, 'like_count'));
        $this->totalComments = array_sum(array_column($this->allMedia, 'comments_count'));
        $count               = count($this->allMedia);
        $this->avgLikes      = $count > 0 ? (int) round($this->totalLikes / $count) : 0;
        $this->avgComments   = $count > 0 ? (int) round($this->totalComments / $count) : 0;

        $sorted = $this->allMedia;
        usort($sorted, fn($a, $b) =>
            (($b['like_count'] ?? 0) + ($b['comments_count'] ?? 0)) <=>
            (($a['like_count'] ?? 0) + ($a['comments_count'] ?? 0))
        );
        $this->topPosts = array_slice($sorted, 0, 3);

        $this->recomputeSortedMedia();
    }

    private function recomputeSortedMedia(): void
    {
        // Sort only what is currently displayed
        $media = $this->media;

        if ($this->sortBy === 'likes') {
            usort($media, fn($a, $b) => ($b['like_count'] ?? 0) <=> ($a['like_count'] ?? 0));
        } elseif ($this->sortBy === 'comments') {
            usort($media, fn($a, $b) => ($b['comments_count'] ?? 0) <=> ($a['comments_count'] ?? 0));
        }

        $this->sortedMedia = $media;
    }

    public function sortMedia(string $by): void
    {
        $this->sortBy = $by;
        $this->recomputeSortedMedia();
    }
};

?>

<div class="py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">

        {{-- Error: not connected --}}
        @if($error === 'not_connected')
            {{-- ... same UI ... --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl flex items-center justify-center"
                     style="background: linear-gradient(135deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888)">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-midnight text-lg mb-2">Compte Instagram non connecté</h3>
                <p class="text-gray-400 text-sm mb-6 max-w-xs mx-auto">Connectez votre compte Instagram Business pour visualiser vos statistiques.</p>
                <a href="{{ route('post-settings') }}" wire:navigate
                   class="inline-flex items-center gap-2 bg-midnight text-white text-sm font-semibold px-5 py-2.5 rounded-xl hover:bg-midnight/90 transition-colors">
                    Connecter Instagram
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

        {{-- Profile Card --}}
        @if($account)
        <div class="bg-midnight rounded-2xl p-6 relative overflow-hidden">
            <div class="absolute -top-12 -right-12 w-48 h-48 bg-copper/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-8 -left-8 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
            <div class="relative flex flex-col sm:flex-row items-start sm:items-center gap-5">
                {{-- Avatar --}}
                <div class="relative shrink-0">
                    @if(!empty($account['profile_picture_url']))
                        <img src="{{ $account['profile_picture_url'] }}" alt="{{ $account['username'] }}"
                             class="w-16 h-16 rounded-2xl object-cover ring-2 ring-white/10">
                    @else
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center ring-2 ring-white/10"
                             style="background: linear-gradient(135deg, #f09433, #dc2743, #bc1888)">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073z"/>
                            </svg>
                        </div>
                    @endif
                    <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full border-2 border-midnight flex items-center justify-center"
                         style="background: linear-gradient(135deg, #f09433, #bc1888)">
                        <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-white font-bold text-xl leading-tight">{{ $account['name'] ?? $account['username'] }}</h2>
                        @if(!empty($account['username']))
                            <span class="text-white/40 text-sm">&#64;{{ $account['username'] }}</span>
                        @endif
                    </div>
                </div>

                {{-- Account KPIs --}}
                <div class="flex gap-6 sm:gap-8 shrink-0">
                    <div class="text-center">
                        <p class="text-white font-bold text-xl">{{ number_format($account['followers_count'] ?? 0) }}</p>
                        <p class="text-white/40 text-xs mt-0.5">Abonnés</p>
                    </div>
                    <div class="text-center">
                        <p class="text-white font-bold text-xl">{{ number_format($account['media_count'] ?? count($allMedia)) }}</p>
                        <p class="text-white/40 text-xs mt-0.5">Publications</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Engagement Stats --}}
        @if(count($allMedia) > 0)
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 group card-hover">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-9 h-9 rounded-xl bg-rose-50 flex items-center justify-center text-rose-400 group-hover:bg-rose-400 group-hover:text-white transition-all duration-200">
                        <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 21.593c-5.63-5.539-11-10.297-11-14.402 0-3.791 3.068-5.191 5.281-5.191 1.312 0 4.151.501 5.719 4.457 1.59-3.968 4.464-4.447 5.726-4.447 2.54 0 5.274 1.621 5.274 5.181 0 4.069-5.136 8.625-11 14.402z"/>
                        </svg>
                    </div>
                    <span class="text-[11px] font-bold text-rose-400 bg-rose-50 px-2 py-0.5 rounded-full">{{ count($allMedia) }} posts</span>
                </div>
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Total likes</p>
                <p class="text-2xl font-bold text-midnight mt-1">{{ number_format($totalLikes) }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 group card-hover">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center text-orange-400 group-hover:bg-orange-400 group-hover:text-white transition-all duration-200">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <span class="text-[11px] font-bold text-orange-400 bg-orange-50 px-2 py-0.5 rounded-full">Moy.</span>
                </div>
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Likes / post</p>
                <p class="text-2xl font-bold text-midnight mt-1">{{ number_format($avgLikes) }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 group card-hover">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center text-blue-400 group-hover:bg-blue-500 group-hover:text-white transition-all duration-200">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                </div>
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Total commentaires</p>
                <p class="text-2xl font-bold text-midnight mt-1">{{ number_format($totalComments) }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 group card-hover">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500 group-hover:bg-amber-500 group-hover:text-white transition-all duration-200">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Commentaires / post</p>
                <p class="text-2xl font-bold text-midnight mt-1">{{ number_format($avgComments) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Top Performers --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden h-fit">
                <div class="px-6 py-4 border-b border-gray-50">
                    <h3 class="font-semibold text-midnight text-sm flex items-center gap-2">🏆 Meilleures performances</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($topPosts as $i => $post)
                    <div class="flex items-center gap-3 p-4">
                        @if($post['thumbnail_url'] ?? $post['media_url'])
                            <img src="{{ $post['thumbnail_url'] ?? $post['media_url'] }}" class="w-12 h-12 rounded-xl object-cover shrink-0">
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-400 truncate">{{ $post['caption'] ?? 'Sans légende' }}</p>
                            <div class="flex items-center gap-3 mt-1.5">
                                <span class="text-rose-400 text-xs font-bold">❤️ {{ $post['like_count'] }}</span>
                                <span class="text-blue-400 text-xs font-bold">💬 {{ $post['comments_count'] }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Media Grid --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                        <h3 class="font-semibold text-midnight text-sm">Publications</h3>
                        <div class="flex bg-gray-50 rounded-xl p-1 gap-1">
                            @foreach(['recent','likes','comments'] as $b)
                            <button wire:click="sortMedia('{{$b}}')" class="text-xs font-semibold px-3 py-1.5 rounded-lg {{ $sortBy === $b ? 'bg-white shadow-sm' : '' }}">{{ ucfirst($b) }}</button>
                            @endforeach
                        </div>
                    </div>
                    <div class="p-4 grid grid-cols-3 sm:grid-cols-4 gap-2.5">
                        @foreach($sortedMedia as $post)
                        <div class="relative aspect-square rounded-xl overflow-hidden bg-gray-50">
                            <img src="{{ $post['thumbnail_url'] ?? $post['media_url'] }}" class="w-full h-full object-cover">
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Manual Load More --}}
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
