<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="font-bold text-2xl lg:text-3xl text-midnight tracking-tight">
                Paramètres des posts
            </h1>
            <p class="text-gray-500 text-sm mt-1">Personnalisez les informations utilisées pour générer vos visuels.</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash messages -->
            @if(session('success'))
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Aperçu de la couleur de marque -->
            @if(auth()->user()->company)
            <div class="p-4 sm:p-6 bg-white shadow sm:rounded-lg">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Aperçu de votre identité visuelle</h3>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl shadow-md"
                         style="background-color: {{ auth()->user()->company->brand_color ?? '#F59E0B' }}"></div>
                    <div>
                        <p class="font-semibold text-midnight">{{ auth()->user()->company->name }}</p>
                        @if(auth()->user()->company->city)
                            <p class="text-sm text-gray-500">{{ auth()->user()->company->city }}</p>
                        @endif
                    </div>
                    @if(auth()->user()->company->logo_path)
                        <img src="{{ Storage::url(auth()->user()->company->logo_path) }}"
                             alt="Logo"
                             class="h-12 w-12 rounded-lg object-contain border border-gray-200 bg-gray-50 p-1 ml-auto" />
                    @endif
                </div>
            </div>
            @endif

            <!-- Formulaire paramètres société -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:settings.update-company-settings-form />
                </div>
            </div>

            <!-- Connexion Instagram -->
            @if(auth()->user()->company)
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <header class="mb-6">
                        <div class="flex items-center gap-3">
                            <!-- Instagram icon -->
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                                 style="background: linear-gradient(135deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%)">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-medium text-gray-900">Instagram</h2>
                                <p class="text-sm text-gray-600">Publication automatique à chaque post validé.</p>
                            </div>
                        </div>
                    </header>

                    @php $company = auth()->user()->company; @endphp

                    @if($company->instagram_connected)
                        <!-- Connected state -->
                        <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-2.5 h-2.5 rounded-full bg-green-500"></div>
                                <div>
                                    <p class="text-sm font-semibold text-green-900">Connecté</p>
                                    @if($company->instagram_username)
                                        <p class="text-xs text-green-700">{{ $company->instagram_username }}</p>
                                    @endif
                                    @if($company->instagram_token_expires_at)
                                        <p class="text-xs text-green-600 mt-0.5">
                                            Token valide jusqu'au {{ $company->instagram_token_expires_at->format('d/m/Y') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <form method="POST" action="{{ route('instagram.disconnect') }}"
                                  onsubmit="return confirm('Déconnecter Instagram ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-sm font-medium text-red-600 hover:text-red-800 transition-colors">
                                    Déconnecter
                                </button>
                            </form>
                        </div>
                        <p class="mt-3 text-xs text-gray-500">
                            Vos prochains posts validés seront automatiquement publiés sur ce compte Instagram.
                        </p>
                    @else
                        <!-- Disconnected state -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 border border-gray-200 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-2.5 h-2.5 rounded-full bg-gray-400"></div>
                                <p class="text-sm text-gray-600">Aucun compte connecté</p>
                            </div>
                            <a href="{{ route('instagram.connect') }}"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                               style="background: linear-gradient(135deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%)">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                                Connecter Instagram
                            </a>
                        </div>
                        <p class="mt-3 text-xs text-gray-500">
                            Nécessite un compte Instagram Business lié à une Page Facebook.
                        </p>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
