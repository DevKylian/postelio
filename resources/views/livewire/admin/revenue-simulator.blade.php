<div class="p-4 md:p-8 max-w-7xl mx-auto space-y-6" x-data="{ activeTab: @entangle('mode') }">
    
    <!-- Meta for Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
        <div class="w-full lg:w-auto">
            <div class="flex items-center gap-2 mb-1.5">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-1.5 text-[10px] font-black text-gray-400 hover:text-copper transition-colors uppercase tracking-[0.2em]">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
                    Retour Dashboard
                </a>
            </div>
            <h1 class="text-xl md:text-2xl font-black text-midnight tracking-tight">Simulateur de revenus</h1>
            <p class="text-gray-400 text-xs md:text-sm font-medium">Estimez vos revenus réels après URSSAF et frais.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
             <button wire:click="toggleDisplay" 
                class="flex-1 lg:flex-none flex items-center justify-center gap-3 bg-white px-4 md:px-5 py-2.5 rounded-full shadow-sm border border-gray-100 hover:bg-gray-50 transition-all font-bold text-[10px] md:text-xs uppercase tracking-widest whitespace-nowrap">
                <span class="{{ !$isNet ? 'text-orange-500' : 'text-gray-300' }}">Brut</span>
                <div class="w-8 h-4 md:w-10 md:h-5 bg-gray-100 rounded-full relative p-1 flex items-center {{ $isNet ? 'justify-end' : 'justify-start' }}">
                    <div class="w-2.5 h-2.5 md:w-3 md:h-3 rounded-full {{ $isNet ? 'bg-emerald-500' : 'bg-orange-500' }} shadow-sm"></div>
                </div>
                <span class="{{ $isNet ? 'text-emerald-600' : 'text-gray-300' }}">Net</span>
            </button>

            <div class="flex-1 lg:flex-none flex items-center bg-gray-100 p-1 rounded-full border border-gray-200">
                <button @click="activeTab = 'actual'" :class="activeTab === 'actual' ? 'bg-white shadow-sm text-midnight' : 'text-gray-400 hover:text-midnight'" class="flex-1 px-3 md:px-5 py-2 rounded-full text-[9px] md:text-[10px] font-black uppercase tracking-widest transition-all whitespace-nowrap">Actuel</button>
                <button @click="activeTab = 'simulator'" :class="activeTab === 'simulator' ? 'bg-white shadow-sm text-midnight' : 'text-gray-400 hover:text-midnight'" class="flex-1 px-3 md:px-5 py-2 rounded-full text-[9px] md:text-[10px] font-black uppercase tracking-widest transition-all whitespace-nowrap">Simulateur</button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 pb-20 md:pb-0">
        
        <!-- Controls Sidebar -->
        <div class="lg:col-span-4 space-y-4 md:space-y-6 order-2 lg:order-1">
            
            <div x-show="activeTab === 'simulator'" x-transition class="space-y-4 md:space-y-6">
                <!-- Base Clients -->
                <div class="bg-white p-5 md:p-6 rounded-2xl border border-gray-100 shadow-sm space-y-5">
                    <h3 class="text-[10px] md:text-xs font-black text-midnight uppercase tracking-widest flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-orange-500 rounded-full"></span>
                        Clients Actuels
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3 md:gap-4">
                            <div>
                                <label class="text-[9px] font-black text-gray-400 uppercase mb-1.5 block">Basic (19€)</label>
                                <input type="number" wire:model.live="simBasicMo" class="w-full bg-slate-50 border-transparent rounded-xl px-3 md:px-4 py-2 md:py-2.5 text-xs md:text-sm font-bold focus:bg-white focus:ring-orange-500 focus:border-orange-500">
                            </div>
                            <div>
                                <label class="text-[9px] font-black text-gray-400 uppercase mb-1.5 block">Pro (29€)</label>
                                <input type="number" wire:model.live="simProMo" class="w-full bg-slate-50 border-transparent rounded-xl px-3 md:px-4 py-2 md:py-2.5 text-xs md:text-sm font-bold focus:bg-white focus:ring-orange-500 focus:border-orange-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 md:gap-4">
                            <div>
                                <label class="text-[9px] font-black text-gray-400 uppercase mb-1.5 block">Basic An (180€)</label>
                                <input type="number" wire:model.live="simBasicYr" class="w-full bg-slate-50 border-transparent rounded-xl px-3 md:px-4 py-2 md:py-2.5 text-xs md:text-sm font-bold focus:bg-white focus:ring-orange-500 focus:border-orange-500">
                            </div>
                            <div>
                                <label class="text-[9px] font-black text-gray-400 uppercase mb-1.5 block">Pro An (280€)</label>
                                <input type="number" wire:model.live="simProYr" class="w-full bg-slate-50 border-transparent rounded-xl px-3 md:px-4 py-2 md:py-2.5 text-xs md:text-sm font-bold focus:bg-white focus:ring-orange-500 focus:border-orange-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Growth -->
                <div class="bg-white p-5 md:p-6 rounded-2xl border border-gray-100 shadow-sm space-y-5">
                    <h3 class="text-[10px] md:text-xs font-black text-midnight uppercase tracking-widest flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-emerald-500 rounded-full"></span>
                        Croissance / mois
                    </h3>
                    <div class="grid grid-cols-2 gap-3 md:gap-4">
                        <div>
                            <label class="text-[9px] font-black text-gray-400 uppercase mb-1.5 block">New Basic</label>
                            <input type="number" wire:model.live="growthBasicMo" class="w-full bg-slate-50 border-transparent rounded-xl px-3 md:px-4 py-2 md:py-2.5 text-xs md:text-sm font-bold focus:bg-white focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-gray-400 uppercase mb-1.5 block">New Pro</label>
                            <input type="number" wire:model.live="growthProMo" class="w-full bg-slate-50 border-transparent rounded-xl px-3 md:px-4 py-2 md:py-2.5 text-xs md:text-sm font-bold focus:bg-white focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actual Summary -->
            <div x-show="activeTab === 'actual'" x-transition class="bg-white p-5 md:p-6 rounded-2xl border border-gray-100 shadow-sm space-y-5">
                <h3 class="text-[10px] md:text-xs font-black text-midnight uppercase tracking-widest">Base de données réelle</h3>
                <div class="space-y-2 md:space-y-3">
                    <div class="flex items-center justify-between p-2.5 md:p-3 bg-slate-50 rounded-xl">
                        <span class="text-[10px] md:text-xs font-bold text-gray-500">Basic</span>
                        <span class="text-xs md:text-sm font-black text-midnight">{{ (int) $actualCounts['basicMo'] + (int) $actualCounts['basicYr'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2.5 md:p-3 bg-slate-50 rounded-xl">
                        <span class="text-[10px] md:text-xs font-bold text-gray-500">Pro</span>
                        <span class="text-xs md:text-sm font-black text-midnight">{{ (int) $actualCounts['proMo'] + (int) $actualCounts['proYr'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Fix Costs -->
            <div class="bg-white p-5 md:p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4 md:space-y-5">
                 <h3 class="text-[10px] md:text-xs font-black text-midnight uppercase tracking-widest">Coûts et Taxes</h3>
                 <div class="space-y-2.5 md:space-y-3">
                     <div class="flex items-center justify-between text-[10px] md:text-xs">
                         <span class="text-gray-400 font-bold">URSSAF</span>
                         <span class="font-black text-midnight tracking-wide">21.2%</span>
                     </div>
                     <div class="flex items-center justify-between text-[10px] md:text-xs">
                         <span class="text-gray-400 font-bold">Stripe</span>
                         <span class="font-black text-midnight tracking-wide">1.5% + 0.25€</span>
                     </div>
                     <div class="flex items-center justify-between text-[10px] md:text-xs">
                         <span class="text-gray-400 font-bold">Serveur (VPS)</span>
                         <span class="font-black text-midnight tracking-wide">8.00 €</span>
                     </div>
                 </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="lg:col-span-8 space-y-4 md:space-y-6 order-1 lg:order-2">
            
            <!-- Key KPIs -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                 <div class="bg-white p-6 md:p-10 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
                    <div>
                        <p class="text-[9px] md:text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">MRR {{ $isNet ? 'NET' : 'BRUT' }}</p>
                        <h3 class="text-3xl md:text-4xl font-black text-midnight tracking-tighter">{{ number_format($isNet ? $this->analytics['mrrNet'] : $this->analytics['mrrBrut'], 2) }} <span class="text-base md:text-lg text-gray-300">€</span></h3>
                    </div>
                    <p class="text-[9px] md:text-[10px] font-bold text-gray-400 mt-4 italic">"{{ $isNet ? 'Revenu réel net' : 'Volume de facturation' }}"</p>
                 </div>

                 <div class="bg-white p-6 md:p-10 rounded-2xl border border-gray-100 shadow-sm">
                    <p class="text-[9px] md:text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Objectif 1500€ Net</p>
                    @php $goalPercent = min(100, ($this->analytics['mrrNet'] / 1500) * 100); @endphp
                    <div class="flex items-end justify-between mb-3">
                        <span class="text-3xl md:text-4xl font-black text-emerald-500 tracking-tighter">{{ round($goalPercent) }}%</span>
                        <span class="text-[9px] md:text-[10px] font-black text-gray-300 uppercase tracking-widest">Reste {{ number_format(max(0, 1500 - $this->analytics['mrrNet']), 0) }}€</span>
                    </div>
                    <div class="h-2 md:h-2.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500 rounded-full transition-all duration-1000" style="width: {{ $goalPercent }}%"></div>
                    </div>
                 </div>
            </div>

            <!-- Summary Breakdowns -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 md:gap-4">
                 <div class="bg-white p-4 md:p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <p class="text-[8px] md:text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Clients Total</p>
                    <p class="text-lg md:text-xl font-black text-midnight tracking-tight">{{ $this->analytics['totalUsers'] }}</p>
                 </div>
                 <div class="bg-white p-4 md:p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <p class="text-[8px] md:text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Charges URSSAF</p>
                    <p class="text-lg md:text-xl font-black text-midnight tracking-tight">{{ number_format($this->analytics['taxes'], 2) }}€</p>
                 </div>
                 <div class="bg-white p-4 md:p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <p class="text-[8px] md:text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Coûts API/VPS</p>
                    <p class="text-lg md:text-xl font-black text-midnight tracking-tight whitespace-nowrap">{{ number_format($this->analytics['zernioCost'] + $this->analytics['openaiCost'] + 8, 2) }}€</p>
                 </div>
                 <div class="bg-white p-4 md:p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <p class="text-[8px] md:text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Frais Stripe</p>
                    <p class="text-lg md:text-xl font-black text-midnight tracking-tight">{{ number_format($this->analytics['stripeFees'], 2) }}€</p>
                 </div>
            </div>

            <!-- Projection Chart -->
            <div class="bg-white p-5 md:p-8 rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 md:mb-10">
                    <div>
                        <h3 class="text-[10px] md:text-sm font-black text-midnight uppercase tracking-widest">Projection 36 mois</h3>
                        <p class="text-[8px] md:text-[10px] text-gray-300 font-bold uppercase tracking-widest mt-1">Évolution des revenus</p>
                    </div>
                    <div class="flex items-center gap-3 md:gap-4 text-[8px] md:text-[10px] font-black uppercase tracking-widest">
                         <div class="flex items-center gap-1.5"><span class="w-2 md:w-2.5 h-2 md:h-2.5 rounded-full bg-orange-500"></span> Basic</div>
                         <div class="flex items-center gap-1.5"><span class="w-2 md:w-2.5 h-2 md:h-2.5 rounded-full bg-slate-800"></span> Pro</div>
                    </div>
                </div>

                <div class="h-[250px] md:h-[350px]" wire:ignore>
                    <canvas id="projectionChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const ctx = document.getElementById('projectionChart').getContext('2d');
            let chart;
            let currentProjections = @json($this->projections);

            function initChart(labels, dataNet, dataBasic, dataPro) {
                const isNet = @json($isNet);
                
                chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: isNet ? [
                            { label: 'Revenu Net', data: dataNet, backgroundColor: '#10b981', borderRadius: 4 },
                            { label: 'Objectif 1500€', data: Array(labels.length).fill(1500), type: 'line', borderColor: '#cbd5e1', borderDash: [5, 5], pointRadius: 0, fill: false }
                        ] : [
                            { label: 'Basic', data: dataBasic, backgroundColor: '#f97316', stack: 'brut' },
                            { label: 'Pro', data: dataPro, backgroundColor: '#1e293b', stack: 'brut', borderRadius: { topLeft: 4, topRight: 4 } },
                            { label: 'Objectifs', data: Array(labels.length).fill(1500 / 0.75), type: 'line', borderColor: '#cbd5e1', borderDash: [5, 5], pointRadius: 0, fill: false }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: { 
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    afterBody: (context) => {
                                        const index = context[0].dataIndex;
                                        const isNet = @this.get('isNet');
                                        if (isNet) {
                                            const brut = currentProjections.basic[index] + currentProjections.pro[index];
                                            return `Revenu Brut: ${brut}€`;
                                        } else {
                                            const net = currentProjections.net[index];
                                            return `Revenu Net: ${net}€`;
                                        }
                                    }
                                }
                            }
                        },
                        scales: {
                            x: { grid: { display: false }, stacked: true, ticks: { font: { size: 9, weight: 'bold' } } },
                            y: { grid: { borderDash: [4, 4], color: '#f3f4f6' }, stacked: true, ticks: { font: { size: 9, weight: 'bold' }, callback: (v) => v + '€' } }
                        }
                    }
                });
            }

            initChart(currentProjections.labels, currentProjections.net, currentProjections.basic, currentProjections.pro);

            Livewire.on('chart-updated', (event) => {
                currentProjections = event.projections;
                const p = event.projections;
                const isNet = event.isNet;
                
                chart.data.labels = p.labels;
                if (isNet) {
                    chart.data.datasets = [
                        { label: 'Revenu Net', data: p.net, backgroundColor: '#10b981', borderRadius: 4 },
                        { label: 'Objectif 1500€', data: Array(p.labels.length).fill(1500), type: 'line', borderColor: '#cbd5e1', borderDash: [5, 5], pointRadius: 0, fill: false }
                    ];
                } else {
                    chart.data.datasets = [
                        { label: 'Basic', data: p.basic, backgroundColor: '#f97316', stack: 'brut' },
                        { label: 'Pro', data: p.pro, backgroundColor: '#1e293b', stack: 'brut', borderRadius: { topLeft: 4, topRight: 4 } },
                        { label: 'Objectifs', data: Array(p.labels.length).fill(1500 / 0.75), type: 'line', borderColor: '#cbd5e1', borderDash: [5, 5], pointRadius: 0, fill: false }
                    ];
                }
                chart.update('none');
            });
            
            @this.on('updated', () => Livewire.dispatch('chart-updated', { projections: @this.projections, isNet: @this.isNet }));
        });
    </script>
</div>
