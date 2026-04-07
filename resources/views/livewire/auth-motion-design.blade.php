<div class="w-full h-full flex flex-col items-center justify-center relative select-none pointer-events-none"
     x-data="authMotionDesign()"
     x-init="startTimeline()">
    
    <!-- Confetti Burst (LP Style) - OUTSIDE OF SCENES -->
    <template x-if="showConfetti">
        <div class="absolute inset-x-0 top-1/2 flex items-center justify-center pointer-events-none z-[100]" x-cloak>
            <div class="md-c md-c-1 burst"></div>
            <div class="md-c md-c-2 burst"></div>
            <div class="md-c md-c-3 burst"></div>
            <div class="md-c md-c-4 burst"></div>
            <div class="md-c md-c-5 burst"></div>
            <div class="md-c md-c-6 burst"></div>
            <div class="md-c md-c-7 burst"></div>
            <div class="md-c md-c-8 burst"></div>
            <div class="md-c md-c-9 burst"></div>
            <div class="md-c md-c-10 burst"></div>
            <div class="md-c md-c-11 burst"></div>
            <div class="md-c md-c-12 burst"></div>
            <div class="md-c md-c-13 burst"></div>
            <div class="md-c md-c-14 burst"></div>
            <div class="md-c md-c-15 burst"></div>
            <div class="md-c md-c-16 burst"></div>
            <div class="md-c md-c-17 burst"></div>
            <div class="md-c md-c-18 burst"></div>
        </div>
    </template>

    <!-- Scene 1: Vocal Input -->
    <div class="absolute inset-0 flex flex-col items-center justify-center gap-14 px-12 transition-all duration-700"
         x-show="scene === 1"
         x-transition:enter="transition cubic-bezier(0.34, 1.56, 0.64, 1) duration-700"
         x-transition:enter-start="opacity-0 scale-90 translate-y-10"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-400"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-110">
        
        <div class="relative">
            <div class="w-24 h-24 rounded-full bg-copper flex items-center justify-center text-white shadow-[0_20px_50px_rgba(245,158,11,0.3)] relative z-10">
                 <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" /></svg>
            </div>
            <div class="absolute inset-[-15px] rounded-full border-2 border-copper/10 animate-ping"></div>
            <div class="absolute inset-[-30px] rounded-full border border-copper/5 animate-pulse"></div>
        </div>

        <div class="w-full max-w-sm flex items-center justify-center gap-2.5 h-20">
            <template x-for="(h, i) in waveform" :key="i">
                <div class="w-2 bg-copper/70 rounded-full transition-all duration-200" :style="`height: ${h}%`"></div>
            </template>
        </div>

        <div class="bg-white/[0.04] backdrop-blur-3xl border border-white/5 rounded-[2rem] p-8 text-center w-full max-w-xl shadow-2xl">
            <p class="text-white text-2xl font-medium tracking-tight leading-relaxed">
                <span x-text="transcriptionPart1"></span><span class="text-copper" x-text="transcriptionPart2"></span>
            </p>
        </div>
    </div>

    <!-- Scene 2: Type Selection -->
    <div class="absolute inset-0 flex flex-col items-center justify-center gap-10 px-12"
         x-show="scene === 2"
         x-transition:enter="transition cubic-bezier(0.34, 1.56, 0.64, 1) duration-700"
         x-transition:enter-start="opacity-0 scale-90 translate-y-10"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-400"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-110">
        
        <div class="text-center space-y-4">
             <div class="inline-flex items-center gap-3 px-6 py-2 rounded-full bg-white/5 border border-white/10 text-white/40 text-[10px] font-bold uppercase tracking-[0.2em]">
                 Étape 02 · Type de post
             </div>
             <h3 class="text-white text-4xl font-black tracking-tighter">On choisit le format...</h3>
        </div>

        <!-- Type Grid (3x2) -->
        <div class="grid grid-cols-3 gap-3 w-full max-w-sm">
            <div class="p-3.5 rounded-2xl border-2 transition-all duration-500 flex flex-col items-center gap-3 bg-white/5"
                 :class="selectedType === 'avant-apres' ? 'border-copper bg-copper/5 scale-105' : 'border-white/10 opacity-40'">
                <div class="w-10 h-10 rounded-xl bg-copper/20 flex items-center justify-center text-copper">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="3" rx="2"/><line x1="12" x2="12" y1="3" y2="21"/></svg>
                </div>
                <span class="text-[8px] font-bold text-white uppercase tracking-widest text-center">Avant / Après</span>
            </div>
            <div class="p-3.5 rounded-2xl border-2 border-white/10 bg-white/5 opacity-40 flex flex-col items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white/40">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                </div>
                <span class="text-[8px] font-bold text-white/40 uppercase tracking-widest text-center">Conseil</span>
            </div>
            <div class="p-3.5 rounded-2xl border-2 border-white/10 bg-white/5 opacity-40 flex flex-col items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white/40">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V21"/><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3"/></svg>
                </div>
                <span class="text-[8px] font-bold text-white/40 uppercase tracking-widest text-center">Citation</span>
            </div>
            <div class="p-3.5 rounded-2xl border-2 border-white/10 bg-white/5 opacity-40 flex flex-col items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white/40">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/></svg>
                </div>
                <span class="text-[8px] font-bold text-white/40 uppercase tracking-widest text-center">Astuce</span>
            </div>
            <div class="p-3.5 rounded-2xl border-2 border-white/10 bg-white/5 opacity-40 flex flex-col items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white/40">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
                </div>
                <span class="text-[8px] font-bold text-white/40 uppercase tracking-widest text-center">FAQ</span>
            </div>
            <div class="p-3.5 rounded-2xl border-2 border-white/10 bg-white/5 opacity-40 flex flex-col items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white/40">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><line x1="12" x2="12" y1="20" y2="10"/><line x1="18" x2="18" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="16"/></svg>
                </div>
                <span class="text-[8px] font-bold text-white/40 uppercase tracking-widest text-center">Chiffre clé</span>
            </div>
        </div>
    </div>

    <!-- Scene 3: Photo Upload -->
    <div class="absolute inset-0 flex flex-col items-center justify-center gap-10 px-12"
         x-show="scene === 3"
         x-transition:enter="transition cubic-bezier(0.34, 1.56, 0.64, 1) duration-700"
         x-transition:enter-start="opacity-0 scale-90 translate-y-10"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-400"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-110">
        
        <div class="text-center space-y-4">
             <div class="inline-flex items-center gap-3 px-6 py-2 rounded-full bg-white/5 border border-white/10 text-white/40 text-[10px] font-bold uppercase tracking-[0.2em]">
                 Étape 03 · Photos du chantier
             </div>
             <h3 class="text-white text-4xl font-black tracking-tighter">On ajoute les photos...</h3>
        </div>

        <div class="flex gap-6 w-full max-w-md justify-center">
            <!-- Photo 1 -->
            <div class="w-40 aspect-[3.5/5] rounded-[2.5rem] border-2 border-dashed border-white/20 bg-white/5 relative overflow-hidden transition-all duration-500"
                 :class="hasPhoto1 ? 'border-solid border-copper/50 scale-105' : 'animate-pulse'">
                <img src="{{ asset('images/demo/avant.png') }}" class="w-full h-full object-cover transition-all duration-700 rounded-[2.5rem]"
                     :class="hasPhoto1 ? 'opacity-100 scale-100' : 'opacity-0 scale-95'">
                <div class="absolute inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm rounded-[2.5rem]" x-show="!hasPhoto1">
                    <span class="text-[10px] font-black text-white px-3 py-1 bg-white/10 rounded-full italic">AVANT</span>
                </div>
            </div>
            <!-- Photo 2 -->
            <div class="w-40 aspect-[3.5/5] rounded-[2.5rem] border-2 border-dashed border-white/20 bg-white/5 relative overflow-hidden transition-all duration-500"
                 :class="hasPhoto2 ? 'border-solid border-copper/50 scale-105' : 'animate-pulse'">
                <img src="{{ asset('images/demo/apres.png') }}" class="w-full h-full object-cover transition-all duration-700 rounded-[2.5rem]"
                     :class="hasPhoto2 ? 'opacity-100 scale-100' : 'opacity-0 scale-95'">
                <div class="absolute inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm rounded-[2.5rem]" x-show="!hasPhoto2">
                    <span class="text-[10px] font-black text-white px-3 py-1 bg-white/10 rounded-full italic">APRÈS</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Scene 4: AI Processing -->
    <div class="absolute inset-0 flex flex-col items-center justify-center gap-12 px-12"
         x-show="scene === 4"
         x-transition:enter="transition cubic-bezier(0.34, 1.56, 0.64, 1) duration-700"
         x-transition:enter-start="opacity-0 scale-90 translate-y-10"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-400"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-110">
        
        <div class="text-center space-y-4">
             <div class="inline-flex items-center gap-3 px-6 py-2 rounded-full bg-copper/10 border border-copper/20 text-copper text-[10px] font-bold uppercase tracking-[0.2em]">
                 <div class="w-2 h-2 rounded-full bg-copper animate-pulse"></div>
                 Intelligence Artificielle
             </div>
             <h3 class="text-white text-5xl font-black tracking-tighter italic">Rédaction en cours...</h3>
        </div>

        <div class="w-full max-w-xl bg-white/[0.03] backdrop-blur-3xl border border-white/5 rounded-[3rem] p-12 shadow-2xl space-y-10 relative overflow-hidden">
             <!-- Scan Line -->
             <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-copper to-transparent animate-scan-fast z-20"></div>

             <div class="flex flex-wrap justify-center gap-3">
                 <template x-for="tag in aiKeywords" :key="tag">
                     <span class="px-7 py-3 rounded-2xl bg-white/5 border border-white/10 text-sm font-bold text-copper shadow-lg animate-pop-in" x-text="tag"></span>
                 </template>
             </div>

             <div class="space-y-4">
                 <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden">
                     <div class="h-full bg-gradient-to-r from-copper to-amber-500 transition-all duration-300" :style="`width: ${aiProgress}%`"></div>
                 </div>
                 <div class="flex justify-between text-[10px] text-white/20 font-black uppercase tracking-[0.3em]">
                     <span>Mise en page premium</span>
                     <span x-text="aiProgress + '%'"></span>
                 </div>
             </div>
        </div>
    </div>

    <!-- Scene 5: Instagram Result -->
    <div class="absolute inset-0 flex flex-col items-center justify-center gap-8 px-12"
         x-show="scene === 5"
         x-transition:enter="transition cubic-bezier(0.34, 1.56, 0.64, 1) duration-1000"
         x-transition:enter-start="opacity-0 scale-75 rotate-3"
         x-transition:enter-end="opacity-100 scale-100 rotate-0"
         x-transition:leave="transition ease-in duration-500"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-110">
        
        <div class="text-center space-y-2">
             <div class="w-16 h-16 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-500 shadow-[0_0_50px_rgba(16,185,129,0.2)] mx-auto mb-4 animate-bounce-subtle">
                 <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
             </div>
             <h3 class="text-white text-4xl font-black tracking-tighter italic">C'est publié !</h3>
        </div>

        <!-- Instagram Mockup -->
        <div class="w-[min(400px,94%)] bg-white rounded-[2.5rem] shadow-[0_60px_120px_-20px_rgba(0,0,0,0.6)] overflow-hidden transform hover:scale-[1.02] transition-transform duration-500 flex flex-col shrink min-h-0">
             <div class="p-4 flex items-center justify-between border-b border-slate-50 shrink-0">
                  <div class="flex items-center gap-3">
                      <div class="w-9 h-9 rounded-full bg-amber-500 flex items-center justify-center text-white font-black text-sm">M</div>
                      <div>
                          <div class="text-[12px] font-bold text-midnight leading-tight">Menuiserie Thomas</div>
                          <div class="text-[10px] text-slate-400">Lyon, France</div>
                      </div>
                  </div>
                  <svg class="w-5 h-5 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" /></svg>
             </div>

             <div class="aspect-square bg-slate-100 relative overflow-hidden shrink min-h-0">
                  <img src="{{ asset('images/demo/result.png') }}" class="w-full h-full object-cover">
             </div>

             <div class="p-4 space-y-2 shrink-0">
                  <div class="flex items-center justify-between">
                       <div class="flex gap-4">
                            <svg class="w-6 h-6 text-rose-500 fill-current animate-pulse" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                            <svg class="w-6 h-6 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" /></svg>
                       </div>
                  </div>
                  <div class="text-[10px] sm:text-xs text-slate-600 line-clamp-2">
                       <strong class="text-midnight">Menuiserie Thomas</strong> Nouvelle cuisine terminée à Lyon ! Le rendu de ce bois clair avec... 🔨✨
                  </div>
             </div>
        </div>
    </div>

    <!-- UI Notification Center (Raffinées - Bottom Position) -->
    <div class="absolute bottom-12 left-12 right-12 z-50 flex flex-col items-center gap-4">
        <template x-for="notif in notifications" :key="notif.id">
            <div class="bg-white/[0.04] backdrop-blur-3xl border border-white/5 p-4 rounded-2xl flex items-center gap-4 transition-all duration-700 shadow-2xl w-full max-w-sm"
                 x-show="notif.show"
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 translate-y-8 scale-98"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-400"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0 scale-95">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shrink-0 shadow-lg"
                     :class="notif.type === 'lead' ? 'bg-emerald-500' : 'bg-copper'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-white font-bold text-base" x-text="notif.title"></p>
                    <p class="text-white/40 text-[10px] font-black uppercase tracking-widest mt-1" x-text="notif.time"></p>
                </div>
            </div>
        </template>
    </div>

    <style>
        .animate-scan-fast { animation: scanFast 2s linear infinite; }
        @keyframes scanFast { 0% { transform: translateY(-300px); opacity: 0; } 50% { opacity: 1; } 100% { transform: translateY(500px); opacity: 0; } }
        
        .animate-bounce-subtle { animation: bounceSubtle 3s ease-in-out infinite; }
        @keyframes bounceSubtle { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }

        .animate-pop-in { animation: popIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
        @keyframes popIn { from { opacity: 0; transform: scale(0.5); } to { opacity: 1; transform: scale(1); } }

        .md-c { position: absolute; width: 8px; height: 16px; border-radius: 4px; opacity: 0; left: 50%; top: 50%; margin-left:-4px; margin-top:-8px; z-index: 99; box-shadow:0 0 10px rgba(0,0,0,0.1); pointer-events: none; }
        .md-c.burst { animation: mdCBurst 2.5s ease-in-out forwards; }
        @keyframes mdCBurst { 
            0% { opacity: 1; transform: translate(0,0) rotate(0deg) scale(0); } 
            40% { opacity: 1; transform: translate(var(--tx), var(--ty)) rotate(var(--rot)) scale(1.5); } 
            100% { opacity: 0; transform: translate(calc(var(--tx)*1.2), calc(var(--ty) + 400px)) rotate(calc(var(--rot) + 400deg)) scale(1); } 
        }
        
        .md-c-1{background:#ef4444;--tx:-180px;--ty:-220px;--rot:120deg;}
        .md-c-2{background:#f59e0b;--tx:180px;--ty:-240px;--rot:240deg; animation-delay:0.05s;}
        .md-c-3{background:#10b981;--tx:-120px;--ty:-280px;--rot:400deg;}
        .md-c-4{background:#3b82f6;--tx:120px;--ty:-250px;--rot:180deg; animation-delay:0.02s;}
        .md-c-5{background:#a855f7;--tx:-220px;--ty:-100px;--rot:310deg;}
        .md-c-6{background:#ef4444;--tx:220px;--ty:-80px;--rot:90deg; animation-delay:0.08s;}
        .md-c-7{background:#f59e0b;--tx:-150px;--ty:-150px;--rot:420deg;}
        .md-c-8{background:#10b981;--tx:150px;--ty:-150px;--rot:270deg; animation-delay:0.04s;}
        .md-c-9{background:#3b82f6;--tx:-80px;--ty:-320px;--rot:200deg;}
        .md-c-10{background:#a855f7;--tx:80px;--ty:-320px;--rot:250deg; animation-delay:0.06s;}
        .md-c-11{background:#ef4444;--tx:-250px;--ty:-180px;--rot:150deg; animation-delay:0.02s;}
        .md-c-12{background:#f59e0b;--tx:260px;--ty:-160px;--rot:320deg; animation-delay:0.09s;}
        .md-c-13{background:#10b981;--tx:-100px;--ty:-380px;--rot:410deg; animation-delay:0.05s;}
        .md-c-14{background:#3b82f6;--tx:140px;--ty:-360px;--rot:130deg; animation-delay:0.11s;}
        .md-c-15{background:#a855f7;--tx:-190px;--ty:-290px;--rot:340deg; animation-delay:0.07s;}
        .md-c-16{background:#ef4444;--tx:210px;--ty:-260px;--rot:110deg; animation-delay:0.03s;}
        .md-c-17{background:#f59e0b;--tx:-50px;--ty:-220px;--rot:460deg; animation-delay:0.06s;}
        .md-c-18{background:#10b981;--tx:40px;--ty:-250px;--rot:210deg; animation-delay:0.14s;}
    </style>

    <script>
        function authMotionDesign() {
            return {
                scene: 1,
                showConfetti: false,
                aiProgress: 0,
                waveform: Array.from({length: 24}, () => Math.random() * 50 + 20),
                transcriptionPart1: "Réno de cuisine à ",
                transcriptionPart2: "",
                targetText: "Lyon, rendu superbe !",
                aiKeywords: ["#Cuisine", "#Renovation", "#BTP", "#Lyon"],
                selectedType: '',
                hasPhoto1: false,
                hasPhoto2: false,
                notifications: [
                    { id: 1, type: 'lead', title: 'Nouveau Devis !', time: 'À l\'instant', show: false },
                    { id: 2, type: 'post', title: 'Insta publié ✓', time: 'Il y a 2 min', show: false },
                ],
                startTimeline() {
                    this.loopTimeline();
                    setInterval(() => {
                        this.waveform = this.waveform.map(() => Math.random() * 60 + 20);
                    }, 150);
                    setTimeout(() => { this.startAlerts(); }, 15000);
                },
                startAlerts() {
                    let alertIdx = 0;
                    setInterval(() => {
                        this.notifications[alertIdx].show = true;
                        setTimeout(() => { this.notifications[alertIdx].show = false; }, 4000);
                        alertIdx = (alertIdx + 1) % this.notifications.length;
                    }, 8000);
                },
                async loopTimeline() {
                    while(true) {
                        this.scene = 1;
                        this.showConfetti = false;
                        this.transcriptionPart2 = "";
                        this.selectedType = "";
                        this.hasPhoto1 = false;
                        this.hasPhoto2 = false;
                        await this.typeText();
                        await new Promise(r => setTimeout(r, 1200));
                        
                        this.scene = 2; // Selection Type
                        await new Promise(r => setTimeout(r, 800));
                        this.selectedType = "avant-apres";
                        await new Promise(r => setTimeout(r, 1000));

                        this.scene = 3; // Photo Upload
                        await new Promise(r => setTimeout(r, 600));
                        this.hasPhoto1 = true;
                        await new Promise(r => setTimeout(r, 800));
                        this.hasPhoto2 = true;
                        await new Promise(r => setTimeout(r, 1200));

                        this.scene = 4; // AI Loading
                        this.aiProgress = 0;
                        let pi = setInterval(() => { if(this.aiProgress < 100) this.aiProgress += 1; }, 20);
                        await new Promise(r => setTimeout(r, 2200));
                        clearInterval(pi);

                        this.scene = 5; // Result
                        await new Promise(r => setTimeout(r, 100));
                        this.showConfetti = true;
                        await new Promise(r => setTimeout(r, 6000));
                        this.showConfetti = false;
                    }
                },
                async typeText() {
                    for(let char of this.targetText) {
                        this.transcriptionPart2 += char;
                        await new Promise(r => setTimeout(r, 40 + Math.random() * 30));
                    }
                }
            }
        }
    </script>
</div>
