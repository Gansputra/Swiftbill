<div class="space-y-6">
    <!-- Header: Simple & Elegant -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Dashboard</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Business performance at a glance.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm text-xs font-bold text-slate-600 dark:text-slate-300 flex items-center">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                Live System
            </div>
        </div>
    </div>

    <!-- Bento Grid Structure -->
    <div class="grid grid-cols-1 md:grid-cols-4 md:grid-rows-4 gap-4 min-h-[800px]">
        
        <!-- 1. Main Revenue (Large Bento - spans 2 cols, 2 rows) -->
        <div class="md:col-span-2 md:row-span-2 bg-indigo-600 dark:bg-indigo-500 rounded-[2.5rem] p-8 relative overflow-hidden group flex flex-col justify-between shadow-xl shadow-indigo-200 dark:shadow-none">
            <!-- Decorative Circle -->
            <div class="absolute -top-12 -right-12 w-48 h-48 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all duration-500"></div>
            
            <div class="relative">
                <div class="flex items-center gap-2 mb-2">
                    <div class="p-2 bg-white/20 rounded-xl">
                        <x-heroicon-o-presentation-chart-line class="w-5 h-5 text-white" />
                    </div>
                    <span class="text-white/80 text-xs font-bold uppercase tracking-widest">Total Revenue Today</span>
                </div>
                <h2 class="text-4xl md:text-5xl font-black text-white tracking-tighter">
                    Rp {{ number_format($dailyRevenue, 0, ',', '.') }}
                </h2>
            </div>

            <div class="relative mt-8">
                <div class="flex items-end gap-2 text-white/90">
                    <div class="flex-1">
                        <p class="text-sm font-medium opacity-80">Previous period performance</p>
                        <p class="text-lg font-bold">+12.5% <span class="text-xs font-normal opacity-70">than yesterday</span></p>
                    </div>
                    <div class="h-16 w-24 flex items-end gap-1 pb-1">
                        <div class="bg-white/20 w-3 h-1/2 rounded-full"></div>
                        <div class="bg-white/30 w-3 h-2/3 rounded-full border border-white/40"></div>
                        <div class="bg-white/50 w-3 h-5/6 rounded-full"></div>
                        <div class="bg-white w-3 h-full rounded-full"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Revenue Pulse (Interactive Chart - 2 cols, 1 row) -->
        <div class="md:col-span-2 bg-white dark:bg-slate-900 rounded-[2rem] p-6 border border-slate-100 dark:border-slate-800 shadow-sm relative overflow-hidden group"
             x-data="{
                initChart() {
                    let options = {
                        series: [{
                            name: 'Revenue',
                            data: {{ json_encode($revenueChartData) }}
                        }],
                        chart: {
                            type: 'area',
                            height: '100%',
                            toolbar: { show: false },
                            sparkline: { enabled: true },
                            fontFamily: 'Outfit, sans-serif'
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 3,
                            colors: ['#4f46e5']
                        },
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.45,
                                opacityTo: 0.05,
                                stops: [20, 100],
                                colorStops: [
                                    { offset: 0, color: '#4f46e5', opacity: 0.4 },
                                    { offset: 100, color: '#4f46e5', opacity: 0 }
                                ]
                            }
                        },
                        tooltip: {
                            theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                            x: { show: true },
                            y: { formatter: (val) => 'Rp ' + val.toLocaleString() }
                        },
                        xaxis: {
                            categories: {{ json_encode($revenueChartLabels) }},
                            labels: { show: false },
                            axisBorder: { show: false },
                            axisTicks: { show: false }
                        },
                        yaxis: { show: false }
                    };
                    
                    if (this.chart) { this.chart.destroy(); }
                    this.chart = new ApexCharts(this.$refs.revenueChart, options);
                    this.chart.render();
                },
                chart: null
             }"
             x-init="initChart(); $watch('darkMode', () => setTimeout(() => initChart(), 100))">
            
            <div class="flex items-center justify-between relative z-10 mb-2">
                <div>
                    <span class="text-slate-400 dark:text-slate-500 text-[10px] font-black uppercase tracking-widest mb-1 block">Revenue Pulse</span>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white">Active Growth</h3>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-black text-indigo-500 uppercase">Last 7 Days</p>
                    <p class="text-xs font-bold text-slate-400">Trend Analysis</p>
                </div>
            </div>

            <!-- Chart Container -->
            <div class="absolute inset-0 top-16 -bottom-2 z-0">
                <div x-ref="revenueChart" class="w-full h-full"></div>
            </div>
        </div>

        <!-- 3. Stock Alert (Standard Bento - 1 col, 1 row) -->
        <div class="bg-white dark:bg-slate-900 rounded-[2rem] p-6 border border-slate-100 dark:border-slate-800 flex flex-col justify-between shadow-sm group">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 flex items-center justify-center rounded-2xl {{ $lowStockProducts > 0 ? 'bg-rose-50 text-rose-600' : 'bg-green-50 text-green-600' }}">
                        @if($lowStockProducts > 0)
                            <x-heroicon-o-exclamation-triangle class="w-5 h-5 animate-pulse" />
                        @else
                            <x-heroicon-o-check-badge class="w-5 h-5" />
                        @endif
                    </div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Inventory</span>
                </div>
                <p class="text-2xl font-black {{ $lowStockProducts > 0 ? 'text-rose-600' : 'text-slate-900 dark:text-white' }} mt-1">
                    {{ $lowStockProducts }} <span class="text-xs font-bold text-slate-400">Low Items</span>
                </p>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-50 dark:border-slate-800 flex items-center justify-between">
                <span class="text-[10px] text-slate-400 font-bold">Health Scan</span>
                <span class="text-[10px] font-black {{ $lowStockProducts > 10 ? 'text-rose-500' : 'text-emerald-500' }} uppercase tracking-tighter">{{ $lowStockProducts > 10 ? 'Critical' : 'Stable' }}</span>
            </div>
        </div>

        <!-- 4. Total SKU (Small Bento - 1 col, 1 row) -->
        <div class="bg-white dark:bg-slate-900 rounded-[2rem] p-6 border border-slate-100 dark:border-slate-800 shadow-sm">
            <h4 class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Product SKU</h4>
            <div class="flex items-baseline gap-1 mt-2">
                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $totalProducts }}</span>
                <span class="text-[10px] font-bold text-slate-400">Items</span>
            </div>
            <div class="mt-3 flex -space-x-2">
                @for($i=0; $i<4; $i++)
                    <div class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-800 border-2 border-white dark:border-slate-900 flex items-center justify-center text-[8px] font-bold">P{{$i}}</div>
                @endfor
            </div>
        </div>

        <!-- 5. Top Products (Mid-Wide Bento - 2 cols, 2 rows) -->
        <div class="md:col-span-2 md:row-span-2 bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col">
            <div class="p-8 border-b border-slate-50 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tighter">Trending</h3>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1">Units Sold</p>
                </div>
            </div>
            <div class="flex-1 px-8 py-6 overflow-y-auto">
                <div class="space-y-6">
                    @forelse($topProducts as $item)
                    <div class="flex items-center group">
                        <div class="w-10 h-10 bg-slate-50 dark:bg-slate-800 rounded-xl flex items-center justify-center text-[10px] font-black text-slate-400 group-hover:text-indigo-600 transition-colors">
                            {{ $loop->iteration }}
                        </div>
                        <div class="ml-4 flex-1">
                            <h5 class="text-xs font-bold text-slate-900 dark:text-white truncate w-32">{{ $item->product->name }}</h5>
                            <p class="text-[9px] text-slate-400 font-black uppercase tracking-tighter">{{ $item->product->sku ?? 'NO-SKU' }}</p>
                        </div>
                        <div class="text-right">
                             <span class="text-xs font-black text-indigo-600">{{ $item->total_sold }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="h-40 flex items-center justify-center text-slate-400 text-xs italic">No data</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- NEW: Category Share (Standard Bento - 1 col, 2 rows) -->
        <div class="md:row-span-2 bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-sm p-8 flex flex-col justify-between"
             x-data="{
                initDonut() {
                    let options = {
                        series: {{ json_encode($categoryChartData) }},
                        labels: {{ json_encode($categoryChartLabels) }},
                        chart: {
                            type: 'donut',
                            height: 250,
                            fontFamily: 'Outfit, sans-serif'
                        },
                        stroke: { show: false },
                        dataLabels: { enabled: false },
                        legend: {
                            position: 'bottom',
                            fontSize: '10px',
                            fontWeight: 700,
                            labels: { colors: document.documentElement.classList.contains('dark') ? '#94a3b8' : '#64748b' },
                            markers: { radius: 10 }
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '75%',
                                    labels: {
                                        show: true,
                                        name: { show: false },
                                        total: {
                                            show: true,
                                            label: 'Total',
                                            fontSize: '10px',
                                            fontWeight: 900,
                                            color: '#6366f1',
                                            formatter: () => '{{ array_sum($categoryChartData) }}'
                                        }
                                    }
                                }
                            }
                        },
                        colors: ['#4f46e5', '#818cf8', '#a5b4fc', '#c7d2fe', '#e0e7ff'],
                        tooltip: { theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light' }
                    };
                    if (this.donut) { this.donut.destroy(); }
                    this.donut = new ApexCharts(this.$refs.categoryChart, options);
                    this.donut.render();
                },
                donut: null
             }"
             x-init="initDonut(); $watch('darkMode', () => setTimeout(() => initDonut(), 100))">
            <div>
                <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tighter">Market Share</h3>
                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1">By Category</p>
            </div>
            
            <div class="flex-1 flex items-center justify-center py-4">
                <div x-ref="categoryChart" class="w-full"></div>
            </div>
        </div>

        <!-- 6. Recent Activity (Tall Bento - 1 col, 2 rows) -->
        <div class="md:row-span-2 bg-slate-900 dark:bg-indigo-950 rounded-[2.5rem] p-8 text-white flex flex-col shadow-2xl">
            <div class="mb-6">
                <h3 class="text-lg font-black tracking-tight">Activity Feed</h3>
                <p class="text-xs text-white/50">Recent incoming orders</p>
            </div>
            
            <div class="flex-1 space-y-6">
                @forelse($recentTransactions as $tx)
                <div class="relative pl-6 border-l-2 border-white/10">
                    <!-- Dot -->
                    <div class="absolute -left-[7px] top-0 w-3 h-3 bg-indigo-400 rounded-full border-2 border-slate-900"></div>
                    
                    <div>
                        <p class="text-xs font-black">#{{ $tx->invoice_number }}</p>
                        <p class="text-lg font-black mt-1">Rp {{ number_format($tx->total_price, 0) }}</p>
                        <div class="flex items-center gap-2 mt-2">
                             <div class="w-4 h-4 bg-white/20 rounded-full"></div>
                             <span class="text-[10px] font-bold text-white/60">{{ $tx->user->name }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-xs text-white/30 italic">No recent transactions</p>
                @endforelse
            </div>

            <button class="mt-8 w-full py-4 bg-white/10 hover:bg-white/20 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">
                Full Transaction History
            </button>
        </div>

    </div>
</div>
