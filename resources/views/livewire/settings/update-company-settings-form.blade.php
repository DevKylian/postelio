<?php

use App\Models\Company;
use Illuminate\Support\Facades\Auth;

use function Livewire\Volt\{state};

state([
    'name'              => fn () => auth()->user()->company?->name ?? '',
    'city'              => fn () => auth()->user()->company?->city ?? '',
    'postal_code'       => fn () => auth()->user()->company?->postal_code ?? '',
    'city_context'      => fn () => auth()->user()->company?->city_context ?? '',
    'nearby_cities'     => fn () => auth()->user()->company?->nearby_cities ?? [],
    'phone'             => fn () => auth()->user()->company?->phone ?? '',
    'email'             => fn () => auth()->user()->company?->email ?? '',
    'website'           => fn () => auth()->user()->company?->website ?? '',
    'brand_color'       => fn () => auth()->user()->company?->brand_color ?? '#F59E0B',
    'secondary_color'   => fn () => auth()->user()->company?->secondary_color ?? '#0F172A',
    'weekly_posts_goal' => fn () => auth()->user()->company?->weekly_posts_goal ?? 3,
    'bio_text'          => fn () => auth()->user()->company?->bio_text ?? '',
]);

$updateCompanySettings = function () {
    $this->validate([
        'name'              => ['required', 'string', 'max:15'],
        'city'              => ['nullable', 'string', 'max:255'],
        'phone'             => ['nullable', 'string', 'max:30'],
        'email'             => ['nullable', 'email', 'max:255'],
        'website'           => ['nullable', 'string', 'max:255', 'regex:/\..+/'],
        'brand_color'       => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        'secondary_color'   => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        'weekly_posts_goal' => ['required', 'integer', 'min:0', 'max:7'],
        'bio_text'          => ['nullable', 'string', 'max:2000'],
    ], [
        'website.regex' => 'Le site internet doit être au format domaine.fr (contenir un point).',
    ]);

    if ($this->website) {
        $this->website = preg_replace('#^https?://#', '', $this->website);
        $this->website = preg_replace('#^www\.#', '', $this->website);
        $this->website = rtrim($this->website, '/');
    }

    $user = Auth::user();
    $company = $user->company;

    $data = [
        'name'              => $this->name,
        'city'              => $this->city,
        'postal_code'       => $this->postal_code ?: null,
        'city_context'      => $this->city_context ?: null,
        'nearby_cities'     => !empty($this->nearby_cities) ? $this->nearby_cities : null,
        'phone'             => $this->phone,
        'email'             => $this->email,
        'website'           => $this->website,
        'brand_color'       => $this->brand_color,
        'secondary_color'   => $this->secondary_color,
        'weekly_posts_goal' => $this->weekly_posts_goal,
        'bio_text'          => $this->bio_text,
    ];

    if ($company) {
        $company->update($data);
    } else {
        Company::create(array_merge($data, ['user_id' => $user->id]));
    }

    $user->load('company');

    $this->name              = $user->company->name;
    $this->city              = $user->company->city ?? '';
    $this->postal_code       = $user->company->postal_code ?? '';
    $this->city_context      = $user->company->city_context ?? '';
    $this->nearby_cities     = $user->company->nearby_cities ?? [];
    $this->phone             = $user->company->phone ?? '';
    $this->email             = $user->company->email ?? '';
    $this->website           = $user->company->website ?? '';
    $this->brand_color       = $user->company->brand_color;
    $this->secondary_color   = $user->company->secondary_color ?? '#0F172A';
    $this->weekly_posts_goal = $user->company->weekly_posts_goal;
    $this->bio_text          = $user->company->bio_text ?? '';

    $this->dispatch('company-updated');
};

?>

<section>

    <form wire:submit="updateCompanySettings" method="POST" class="mt-6 space-y-6">

        <!-- Nom de la société -->
        <div>
            <x-input-label for="company_name" value="Nom de la société" />
            <x-text-input wire:model="name"
                          id="company_name"
                          name="name"
                          type="text"
                          class="mt-1 block w-full"
                          required
                          maxlength="15"
                          autocomplete="organization" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Ville -->
        <div>
            <x-input-label for="city" value="Ville" />
            <x-city-autocomplete
                wire-city="city"
                wire-postal="postal_code"
                wire-context="city_context"
                wire-nearby="nearby_cities"
                :current-city="$city"
                placeholder="Ex : Lyon, Bordeaux, Marseille..."
                input-class="mt-1 block w-full border-gray-200 focus:border-copper focus:ring-2 focus:ring-copper/20 rounded-xl shadow-sm transition-all duration-200 text-midnight placeholder:text-gray-300 text-sm"
            />
            @if($city && $postal_code)
                <p class="mt-1 text-xs text-green-600 font-medium">{{ $city }} ({{ $postal_code }})</p>
            @elseif($city && !$postal_code)
                <p class="mt-1 text-xs text-amber-600">Tapez votre ville et sélectionnez une suggestion pour enregistrer le code postal.</p>
            @endif
            <x-input-error class="mt-2" :messages="$errors->get('city')" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Téléphone -->
            <div>
                <x-input-label for="phone" value="Téléphone (optionnel)" />
                <x-text-input wire:model="phone"
                              id="phone"
                              name="phone"
                              type="tel"
                              class="mt-1 block w-full"
                              autocomplete="tel" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>

            <!-- Email -->
            <div>
                <x-input-label for="email" value="Email pro (optionnel)" />
                <x-text-input wire:model="email"
                              id="email"
                              name="email"
                              type="email"
                              class="mt-1 block w-full"
                              autocomplete="email" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>
        </div>

        <!-- Site internet -->
        <div>
            <x-input-label for="website" value="Site internet (optionnel)" />
            <x-text-input wire:model="website"
                          id="website"
                          name="website"
                          type="text"
                          class="mt-1 block w-full"
                          placeholder="mon-entreprise.fr" />
            <x-input-error class="mt-2" :messages="$errors->get('website')" />
        </div>

        <!-- Description de l'activité -->
        <div>
            <x-input-label for="bio_text" value="Description de votre activité" />
            <textarea wire:model="bio_text"
                      id="bio_text"
                      name="bio_text"
                      rows="4"
                      class="mt-1 block w-full border-gray-200 focus:border-copper focus:ring-2 focus:ring-copper/20 rounded-xl shadow-sm transition-all duration-200 text-midnight placeholder:text-gray-300 text-sm"
                      placeholder="Décrivez votre activité en quelques phrases. Ces informations aident Postelio à personnaliser vos posts."></textarea>
            <p class="mt-1 text-xs text-gray-500">
                Ces informations aident Postelio à mieux comprendre ce que vous faites pour personnaliser vos posts.
            </p>
            <x-input-error class="mt-2" :messages="$errors->get('bio_text')" />
        </div>

        <!-- Couleur de marque -->
        <div>
            <x-input-label for="brand_color" :value="__('Couleur principale')" />
            <div class="mt-1">
                <x-custom-color-picker wire:model.live="brand_color" id="brand_color" />
            </div>
            <x-input-error :messages="$errors->get('brand_color')" class="mt-2" />
        </div>

        <!-- Couleur des bandeaux -->
        <div>
            <x-input-label for="secondary_color" :value="__('Couleur des bandeaux')" />
            <div class="mt-1">
                <x-custom-color-picker wire:model.live="secondary_color" id="secondary_color" />
            </div>
            <x-input-error :messages="$errors->get('secondary_color')" class="mt-2" />
        </div>

        <!-- Objectif hebdomadaire de posts -->
        <div>
            <x-input-label for="weekly_posts_goal" value="Objectif hebdomadaire de posts" />
            <div class="mt-2 space-y-4">
                <div class="relative pt-4 pb-2">
                    <input wire:model.live="weekly_posts_goal"
                           id="weekly_posts_goal"
                           name="weekly_posts_goal"
                           type="range"
                           min="0" max="7" step="1"
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-copper" />
                    
                    <div class="flex justify-between mt-3 text-[11px] font-bold text-gray-400">
                        @for($i = 0; $i <= 7; $i++)
                            <span class="{{ $weekly_posts_goal == $i ? 'text-copper scale-125 transition-transform' : '' }}">{{ $i }}</span>
                        @endfor
                    </div>
                </div>

                <div class="p-4 rounded-xl flex items-center gap-3 transition-colors duration-300 border
                            {{ $weekly_posts_goal == 0 ? 'bg-red-50 border-red-100 text-red-800' : '' }}
                            {{ $weekly_posts_goal >= 1 && $weekly_posts_goal <= 2 ? 'bg-orange-50 border-orange-100 text-orange-800' : '' }}
                            {{ $weekly_posts_goal >= 3 && $weekly_posts_goal <= 5 ? 'bg-green-50 border-green-100 text-green-800' : '' }}
                            {{ $weekly_posts_goal >= 6 ? 'bg-blue-50 border-blue-100 text-blue-800' : '' }}">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 border shadow-sm bg-white
                                {{ $weekly_posts_goal == 0 ? 'border-red-200' : '' }}
                                {{ $weekly_posts_goal >= 1 && $weekly_posts_goal <= 2 ? 'border-orange-200' : '' }}
                                {{ $weekly_posts_goal >= 3 && $weekly_posts_goal <= 5 ? 'border-green-200' : '' }}
                                {{ $weekly_posts_goal >= 6 ? 'border-blue-200' : '' }}">
                        <span class="text-xl font-black">{{ $weekly_posts_goal }}</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm">
                            @if($weekly_posts_goal == 0) Non recommandé
                            @elseif($weekly_posts_goal >= 1 && $weekly_posts_goal <= 2) Un peu juste
                            @elseif($weekly_posts_goal >= 3 && $weekly_posts_goal <= 5) Recommandé
                            @else Excellent 🚀
                            @endif
                        </h3>
                    </div>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('weekly_posts_goal')" />
        </div>

        <div class="flex items-center justify-end gap-4">
            <x-action-message on="company-updated">
                Paramètres enregistrés.
            </x-action-message>

            <x-primary-button>Enregistrer</x-primary-button>
        </div>

    </form>
</section>
