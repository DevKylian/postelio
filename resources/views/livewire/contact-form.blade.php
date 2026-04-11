<div>
    @if($sent)
        {{-- Success State --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-card p-10 text-center">
            <div class="w-16 h-16 rounded-full bg-green-50 border border-green-100 flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="font-display font-bold text-midnight text-2xl mb-3">Message envoyé !</h3>
            <p class="text-slate-500 font-light leading-relaxed mb-8 max-w-md mx-auto">
                Merci pour votre message. Nous vous répondrons dans les plus brefs délais à <strong class="font-medium text-midnight">{{ $email ?: 'votre adresse' }}</strong>.
            </p>
            <button
                wire:click="$set('sent', false)"
                class="inline-flex items-center gap-2 text-sm font-medium text-copper hover:text-amber-600 transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Envoyer un autre message
            </button>
        </div>
    @else
        {{-- Form --}}
        <form wire:submit="send" class="bg-white rounded-3xl border border-slate-100 shadow-card p-8 sm:p-10 space-y-6">

            @if($error)
                <div class="flex items-start gap-3 bg-red-50 border border-red-100 rounded-2xl px-4 py-4 text-sm text-red-600">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>{{ $error }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Name --}}
                <div>
                    <label for="contact-name" class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Nom complet</label>
                    <input
                        id="contact-name"
                        type="text"
                        wire:model="name"
                        placeholder="Jean Dupont"
                        autocomplete="name"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50/50 text-midnight text-sm placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-copper/30 focus:border-copper/50 transition-all"
                    >
                    @error('name')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="contact-email" class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Adresse email</label>
                    <input
                        id="contact-email"
                        type="email"
                        wire:model="email"
                        placeholder="jean@exemple.fr"
                        autocomplete="email"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50/50 text-midnight text-sm placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-copper/30 focus:border-copper/50 transition-all"
                    >
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Subject --}}
            <div>
                <label for="contact-subject" class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Sujet</label>
                <input
                    id="contact-subject"
                    type="text"
                    wire:model="subject"
                    placeholder="Comment pouvons-nous vous aider ?"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50/50 text-midnight text-sm placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-copper/30 focus:border-copper/50 transition-all"
                >
                @error('subject')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Message --}}
            <div>
                <label for="contact-message" class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Message</label>
                <textarea
                    id="contact-message"
                    wire:model="message"
                    rows="6"
                    placeholder="Décrivez votre demande en détail..."
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50/50 text-midnight text-sm placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-copper/30 focus:border-copper/50 transition-all resize-none"
                ></textarea>
                <div class="flex justify-between mt-1.5">
                    @error('message')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @else
                        <span></span>
                    @enderror
                    <span class="text-xs text-slate-300 tabular-nums">{{ strlen($message) }}/2000</span>
                </div>
            </div>

            {{-- Submit --}}
            <div class="pt-2">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full sm:w-auto btn-primary px-8 py-3.5 flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove wire:target="send">
                        Envoyer le message
                    </span>
                    <span wire:loading wire:target="send" class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Envoi en cours...
                    </span>
                    <svg wire:loading.remove wire:target="send" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </div>

        </form>
    @endif
</div>
