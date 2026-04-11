<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="font-bold text-xl lg:text-2xl text-midnight tracking-tight leading-tight">
                Paramètres
            </h1>
            <p class="text-gray-400 text-sm mt-0.5">Branding et connexions pour vos posts générés.</p>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto space-y-6">

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-100 rounded-xl text-green-800 text-sm font-medium animate-fade-in">
                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-100 rounded-xl text-red-800 text-sm font-medium animate-fade-in">
                    <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Left: Brand preview --}}
                <div class="space-y-4">
                    @if(auth()->user()->company)
                    @php $company = auth()->user()->company; @endphp
                    <div class="glass-panel rounded-2xl p-5">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-4">Aperçu du branding</p>
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl shadow-sm shrink-0"
                                     style="background-color: {{ $company->brand_color ?? '#F59E0B' }}"></div>
                                <div class="min-w-0">
                                    <p class="font-bold text-midnight text-sm truncate">{{ $company->name }}</p>
                                    @if($company->city)
                                        <p class="text-xs text-gray-400 truncate">{{ $company->city }}</p>
                                    @endif
                                </div>
                            </div>
                            @if($company->logo_path)
                            <div class="pt-3 border-t border-gray-50">
                                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-2">Logo actuel</p>
                                <img src="{{ Storage::url($company->logo_path) }}"
                                     alt="Logo"
                                     class="h-14 w-auto max-w-full rounded-lg object-contain border border-gray-100 bg-gray-50 p-2" />
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif


                    {{-- Instagram Status Card --}}
                    @if(auth()->user()->company)
                    @php $company = auth()->user()->company; @endphp
                    <div class="glass-panel rounded-2xl overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-50/80 flex items-center gap-3">
                            <div class="w-7 h-7 rounded-xl flex items-center justify-center shrink-0"
                                 style="background: linear-gradient(135deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%)">
                                <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-midnight leading-none">Instagram</p>
                                <p class="text-xs text-gray-400 mt-0.5">Publication automatique</p>
                            </div>
                        </div>
                        <div class="p-6">
                            @if(!auth()->user()->canPublishToSocialMedia())
                                <div class="bg-gray-50/50 rounded-2xl p-6 border border-dashed border-gray-200 text-center">
                                    <div class="w-10 h-10 bg-amber-50 text-copper rounded-xl flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-midnight uppercase tracking-wider mb-2">Option Premium</h4>
                                    <p class="text-xs text-gray-500 leading-relaxed mb-6 max-w-[200px] mx-auto">L'automatisation Instagram est réservée aux abonnés Premium.</p>
                                    <x-primary-button href="{{ route('subscription.plans') }}" wire:navigate class="w-full justify-center !text-[11px] !py-3">
                                        Débloquer maintenant
                                    </x-primary-button>
                                </div>
                            @elseif($company->instagram_connected)
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="w-2 h-2 rounded-full bg-green-400 shrink-0"></span>
                                    <span class="text-sm font-semibold text-midnight">Connecté</span>
                                    @if($company->instagram_username)
                                        <span class="text-xs text-gray-400">· {{ $company->instagram_username }}</span>
                                    @endif
                                </div>
                                @if($company->instagram_token_expires_at)
                                    <p class="text-xs text-gray-400 mb-4">
                                        Token valide jusqu'au {{ $company->instagram_token_expires_at->format('d/m/Y') }}
                                    </p>
                                @endif
                                <p class="text-xs text-gray-400 mb-4 leading-relaxed">
                                    Vos posts validés seront publiés automatiquement sur ce compte.
                                </p>
                                <form method="POST" action="{{ route('instagram.disconnect') }}"
                                      onsubmit="return confirm('Déconnecter Instagram ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-full py-2 text-sm font-semibold text-red-500 border border-red-100 rounded-xl hover:bg-red-50 transition-colors">
                                        Déconnecter
                                    </button>
                                </form>
                            @elseif(!\App\Models\Setting::get('instagram_connection_enabled', true))
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="w-2 h-2 rounded-full bg-orange-300 shrink-0"></span>
                                    <span class="text-sm text-gray-500">Temporairement indisponible</span>
                                </div>
                                <p class="text-xs text-gray-400 leading-relaxed">
                                    La connexion Instagram est momentanément désactivée. Revenez plus tard.
                                </p>
                            @else
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="w-2 h-2 rounded-full bg-gray-300 shrink-0"></span>
                                    <span class="text-sm text-gray-500">Non connecté</span>
                                </div>
                                <p class="text-xs text-gray-400 mb-4 leading-relaxed">
                                    Connectez un compte Instagram Business pour publier automatiquement.
                                </p>
                                <a href="{{ route('instagram.connect') }}"
                                   class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-sm font-semibold text-white transition-opacity hover:opacity-90"
                                   style="background: linear-gradient(135deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%)">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                    </svg>
                                    Connecter Instagram
                                </a>
                                <p class="text-[11px] text-gray-400 mt-2.5 text-center leading-relaxed">
                                    Nécessite un compte Instagram Business lié à une Page Facebook.
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Facebook Status Card --}}
                    <div class="glass-panel rounded-2xl overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-50/80 flex items-center gap-3">
                            <div class="w-7 h-7 rounded-xl bg-[#1877F2] flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-midnight leading-none">Facebook</p>
                                <p class="text-xs text-gray-400 mt-0.5">Publication automatique</p>
                            </div>
                        </div>
                        <div class="p-6">
                            @if(!auth()->user()->canPublishToSocialMedia())
                                <div class="bg-gray-50/50 rounded-2xl p-6 border border-dashed border-gray-200 text-center">
                                    <div class="w-10 h-10 bg-[#1877F2]/10 text-[#1877F2] rounded-xl flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-midnight uppercase tracking-wider mb-2">Option Premium</h4>
                                    <p class="text-xs text-gray-500 leading-relaxed mb-6 max-w-[200px] mx-auto">L'automatisation Facebook est réservée aux abonnés Premium.</p>
                                    <x-primary-button href="{{ route('subscription.plans') }}" wire:navigate class="w-full justify-center !text-[11px] !py-3">
                                        Débloquer maintenant
                                    </x-primary-button>
                                </div>
                            @elseif($company->facebook_connected)
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="w-2 h-2 rounded-full bg-green-400 shrink-0"></span>
                                    <span class="text-sm font-semibold text-midnight">Connecté</span>
                                    @if($company->facebook_page_name)
                                        <span class="text-xs text-gray-400">· {{ $company->facebook_page_name }}</span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-400 mb-4 leading-relaxed">
                                    Vos posts validés seront également publiés sur votre Page Facebook.
                                </p>
                                <form method="POST" action="{{ route('facebook.disconnect') }}"
                                      onsubmit="return confirm('Déconnecter Facebook ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-full py-2 text-sm font-semibold text-red-500 border border-red-100 rounded-xl hover:bg-red-50 transition-colors">
                                        Déconnecter
                                    </button>
                                </form>
                            @elseif(!\App\Models\Setting::get('facebook_connection_enabled', true))
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="w-2 h-2 rounded-full bg-orange-300 shrink-0"></span>
                                    <span class="text-sm text-gray-500">Temporairement indisponible</span>
                                </div>
                                <p class="text-xs text-gray-400 leading-relaxed">
                                    La connexion Facebook est momentanément désactivée. Revenez plus tard.
                                </p>
                            @else
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="w-2 h-2 rounded-full bg-gray-300 shrink-0"></span>
                                    <span class="text-sm text-gray-500">Non connecté</span>
                                </div>
                                <p class="text-xs text-gray-400 mb-4 leading-relaxed">
                                    Connectez une Page Facebook pour publier automatiquement.
                                </p>
                                <a href="{{ route('facebook.connect') }}"
                                   class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-sm font-semibold text-white bg-[#1877F2] transition-opacity hover:opacity-90">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                    Connecter Facebook
                                </a>
                                <p class="text-[11px] text-gray-400 mt-2.5 text-center leading-relaxed">
                                    Nécessite une Page Facebook dont vous êtes administrateur.
                                </p>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Right: Company Settings Form --}}
                <div class="lg:col-span-2">
                    <div class="glass-panel rounded-2xl p-6 lg:p-8">
                        <div class="mb-6">
                            <h2 class="font-semibold text-midnight">Identité de la société</h2>
                            <p class="text-sm text-gray-400 mt-0.5">Ces informations sont utilisées dans chaque visuel généré.</p>
                        </div>
                        <livewire:settings.update-company-settings-form />
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
