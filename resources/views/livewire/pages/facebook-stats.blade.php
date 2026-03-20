<?php

use App\Services\FacebookService;
use Livewire\Volt\Component;

new class extends Component {

    public ?array $page = null;
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

    public function mount(FacebookService $facebook): void
    {
        $company = auth()->user()->company;

        if (!$company || !$company->facebook_connected) {
            $this->error = 'not_connected';
            return;
        }

        try {
            $this->page = $facebook->getPageStats(
                $company->facebook_access_token,
                $company->facebook_page_id
            );
            $this->posts = $facebook->getRecentPosts(
                $company->facebook_access_token,
                $company->facebook_page_id,
                24
            );
            $this->computeStats();
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
        }
    }

    private function computeStats(): void
    {
        $this->totalLikes    = array_sum(array_column($this->posts, 'like_count'));
        $this->totalComments = array_sum(array_column($this->posts, 'comments_count'));
        $this->totalShares   = array_sum(array_column($this->posts, 'shares_count'));
        $count               = count($this->posts);
        $this->avgLikes      = $count > 0 ? (int) round($this->totalLikes / $count) : 0;
        $this->avgComments   = $count > 0 ? (int) round($this->totalComments / $count) : 0;

        $sorted = $this->posts;
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

        {{-- Error: API error --}}
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

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <h2 class="text-white font-bold text-xl leading-tight">{{ $page['name'] }}</h2>
                    @if(!empty($page['category']))
                        <p class="text-white/40 text-sm mt-0.5">{{ $page['category'] }}</p>
                    @endif
                    @if(!empty($page['about']))
                        <p class="text-white/50 text-sm mt-1.5 leading-relaxed line-clamp-2">{{ $page['about'] }}</p>
                    @endif
                </div>

                {{-- KPIs --}}
                <div class="flex gap-6 sm:gap-8 shrink-0">
                    <div class="text-center">
                        <p class="text-white font-bold text-xl">{{ number_format($page['followers_count'] ?? 0) }}</p>
                        <p class="text-white/40 text-xs mt-0.5">Abonnés</p>
                    </div>
                    <div class="text-center">
                        <p class="text-white font-bold text-xl">{{ count($posts) }}</p>
                        <p class="text-white/40 text-xs mt-0.5">Publications</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Engagement Stats Cards --}}
        @if(count($posts) > 0)
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Total likes --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 group card-hover">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center text-blue-400 group-hover:bg-blue-500 group-hover:text-white transition-all duration-200">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                        </svg>
                    </div>
                    <span class="text-[11px] font-bold text-blue-400 bg-blue-50 px-2 py-0.5 rounded-full">{{ count($posts) }} posts</span>
                </div>
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Total réactions</p>
                <p class="text-2xl font-bold text-midnight mt-1">{{ number_format($totalLikes) }}</p>
            </div>

            {{-- Avg likes --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 group card-hover">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center text-orange-400 group-hover:bg-orange-400 group-hover:text-white transition-all duration-200">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <span class="text-[11px] font-bold text-orange-400 bg-orange-50 px-2 py-0.5 rounded-full">Moy.</span>
                </div>
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Réactions / post</p>
                <p class="text-2xl font-bold text-midnight mt-1">{{ number_format($avgLikes) }}</p>
            </div>

            {{-- Total comments --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 group card-hover">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-9 h-9 rounded-xl bg-green-50 flex items-center justify-center text-green-400 group-hover:bg-green-500 group-hover:text-white transition-all duration-200">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <span class="text-[11px] font-bold text-green-400 bg-green-50 px-2 py-0.5 rounded-full">Total</span>
                </div>
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Total commentaires</p>
                <p class="text-2xl font-bold text-midnight mt-1">{{ number_format($totalComments) }}</p>
            </div>

            {{-- Total shares --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 group card-hover">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-9 h-9 rounded-xl bg-purple-50 flex items-center justify-center text-purple-400 group-hover:bg-purple-500 group-hover:text-white transition-all duration-200">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                        </svg>
                    </div>
                    <span class="text-[11px] font-bold text-purple-400 bg-purple-50 px-2 py-0.5 rounded-full">Total</span>
                </div>
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Total partages</p>
                <p class="text-2xl font-bold text-midnight mt-1">{{ number_format($totalShares) }}</p>
            </div>

        </div>

        {{-- Main Grid: Top posts + Feed --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Top 3 performers --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50">
                    <h3 class="font-semibold text-midnight text-sm flex items-center gap-2">
                        <span class="text-base">🏆</span>
                        Meilleures performances
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">Classés par engagement total</p>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($topPosts as $i => $post)
                    @php $medals = ['🥇', '🥈', '🥉']; @endphp
                    <div class="flex items-center gap-3 p-4">
                        <span class="text-xl shrink-0 w-7 text-center">{{ $medals[$i] }}</span>
                        @if(!empty($post['full_picture']))
                            <img src="{{ $post['full_picture'] }}" alt=""
                                 class="w-12 h-12 rounded-xl object-cover shrink-0 border border-gray-100">
                        @else
                            <div class="w-12 h-12 rounded-xl bg-gray-100 shrink-0 flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-400 truncate leading-relaxed">
                                {{ Str::limit($post['message'] ?: 'Sans texte', 50) }}
                            </p>
                            <div class="flex items-center gap-3 mt-1.5">
                                <span class="flex items-center gap-1 text-xs font-semibold text-blue-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                    </svg>
                                    {{ number_format($post['like_count'] ?? 0) }}
                                </span>
                                <span class="flex items-center gap-1 text-xs font-semibold text-green-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    {{ number_format($post['comments_count'] ?? 0) }}
                                </span>
                                <span class="flex items-center gap-1 text-xs font-semibold text-purple-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                    </svg>
                                    {{ number_format($post['shares_count'] ?? 0) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Posts feed --}}
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between gap-3">
                    <h3 class="font-semibold text-midnight text-sm">Publications récentes</h3>
                    <div class="flex items-center bg-gray-50 rounded-xl p-1 gap-0.5">
                        @foreach(['recent' => 'Récents', 'likes' => 'Réactions', 'comments' => 'Commentaires', 'shares' => 'Partages'] as $key => $label)
                        <button wire:click="sortPosts('{{ $key }}')"
                                class="text-xs font-semibold px-3 py-1.5 rounded-lg transition-all duration-150 {{ $sortBy === $key ? 'bg-white text-midnight shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <div class="divide-y divide-gray-50">
                    @foreach($sortedPosts as $post)
                    <div class="flex items-start gap-4 p-4 hover:bg-gray-50/50 transition-colors">
                        @if(!empty($post['full_picture']))
                            <img src="{{ $post['full_picture'] }}" alt=""
                                 class="w-16 h-16 rounded-xl object-cover shrink-0 border border-gray-100">
                        @else
                            <div class="w-16 h-16 rounded-xl bg-gray-100 shrink-0 flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-600 line-clamp-2 leading-relaxed">{{ $post['message'] ?: 'Publication sans texte' }}</p>
                            <div class="flex items-center gap-4 mt-2">
                                <span class="flex items-center gap-1 text-xs font-semibold text-blue-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                    </svg>
                                    {{ number_format($post['like_count'] ?? 0) }}
                                </span>
                                <span class="flex items-center gap-1 text-xs font-semibold text-green-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    {{ number_format($post['comments_count'] ?? 0) }}
                                </span>
                                <span class="flex items-center gap-1 text-xs font-semibold text-purple-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                    </svg>
                                    {{ number_format($post['shares_count'] ?? 0) }}
                                </span>
                                <span class="text-[10px] text-gray-300 ml-auto">
                                    {{ \Carbon\Carbon::parse($post['created_time'])->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
        @endif

        {{-- No posts --}}
        @if(count($posts) === 0 && !$error && $page)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h4 class="font-semibold text-midnight text-sm mb-1">Aucune publication trouvée</h4>
                <p class="text-xs text-gray-400">Publiez votre premier post pour voir vos statistiques apparaître ici.</p>
            </div>
        @endif

        @endif
    </div>
</div>
