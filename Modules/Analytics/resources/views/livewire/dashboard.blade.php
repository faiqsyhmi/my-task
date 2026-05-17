<div class="app-shell px-4">
    {{-- ══════════ HEADER ══════════ --}}
    <header class="app-header max-w-7xl mx-auto w-full">
        <div class="flex items-center justify-between mb-8">
            <div>
                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-1 text-[0.65rem] uppercase tracking-widest text-slate-500 font-bold mb-4 hover:text-blue-400 transition-colors group">
                    <svg class="w-3 h-3 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
                    Back to Dashboard
                </a>
                <p class="app-header__greeting uppercase tracking-[0.2em] text-[0.65rem] text-blue-400 font-bold mb-1">Performance Overview</p>
                <h1 class="app-header__title text-4xl font-black text-slate-100 tracking-tight">Analytics</h1>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-[0.65rem] uppercase tracking-widest text-slate-500 font-bold">Current Speed</p>
                    <p class="text-xl font-black text-emerald-400">{{ $stats['completion_rate'] }}%</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500/20 to-purple-500/20 border border-blue-500/30 flex items-center justify-center text-blue-400 shadow-[0_0_20px_rgba(59,130,246,0.15)]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto w-full pb-20">
        {{-- ── Quick Stats Grid ── --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            {{-- Total Tasks --}}
            <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800 p-5 rounded-3xl group hover:border-blue-500/30 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 rounded-xl bg-blue-500/10 text-blue-400 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                </div>
                <p class="text-[0.65rem] uppercase tracking-widest text-slate-500 font-bold mb-1">Capture Count</p>
                <h3 class="text-3xl font-black text-slate-100">{{ $stats['total'] }}</h3>
            </div>

            {{-- Completed --}}
            <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800 p-5 rounded-3xl group hover:border-emerald-500/30 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 rounded-xl bg-emerald-500/10 text-emerald-400 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
                <p class="text-[0.65rem] uppercase tracking-widest text-slate-500 font-bold mb-1">Executed</p>
                <h3 class="text-3xl font-black text-slate-100">{{ $stats['done'] }}</h3>
            </div>

            {{-- In Progress --}}
            <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800 p-5 rounded-3xl group hover:border-amber-500/30 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 rounded-xl bg-amber-500/10 text-amber-400 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <p class="text-[0.65rem] uppercase tracking-widest text-slate-500 font-bold mb-1">Processing</p>
                <h3 class="text-3xl font-black text-slate-100">{{ $stats['doing'] }}</h3>
            </div>

            {{-- Flagged --}}
            <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800 p-5 rounded-3xl group hover:border-rose-500/30 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 rounded-xl bg-rose-500/10 text-rose-400 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.837-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    </div>
                </div>
                <p class="text-[0.65rem] uppercase tracking-widest text-slate-500 font-bold mb-1">Priority Load</p>
                <h3 class="text-3xl font-black text-slate-100">{{ $stats['flagged'] }}</h3>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Status Visualizer --}}
            <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800 p-8 rounded-[2rem]">
                <h4 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-8 flex items-center gap-2">
                    <span class="w-1.5 h-6 bg-blue-500 rounded-full"></span>
                    Workflow Status
                </h4>
                
                <div class="space-y-6">
                    @foreach(['todo' => ['label' => 'To Do', 'color' => 'slate'], 'doing' => ['label' => 'Doing', 'color' => 'amber'], 'done' => ['label' => 'Done', 'color' => 'emerald']] as $status => $meta)
                        @php
                            $count = $stats[$status];
                            $percentage = $stats['total'] > 0 ? ($count / $stats['total']) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between items-end mb-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ $meta['label'] }}</span>
                                <span class="text-sm font-black text-slate-100">{{ $count }}</span>
                            </div>
                            <div class="h-3 w-full bg-slate-800/50 rounded-full overflow-hidden border border-slate-700/50">
                                <div class="h-full rounded-full transition-all duration-1000 ease-out {{ $status === 'done' ? 'bg-emerald-500 shadow-[0_0_15px_rgba(16,185,129,0.3)]' : ($status === 'doing' ? 'bg-amber-500 shadow-[0_0_15px_rgba(245,158,11,0.3)]' : 'bg-slate-500') }}" 
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Energy Distribution --}}
            <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800 p-8 rounded-[2rem]">
                <h4 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-8 flex items-center gap-2">
                    <span class="w-1.5 h-6 bg-purple-500 rounded-full"></span>
                    Energy Bio-Load
                </h4>
                
                <div class="flex items-end justify-between h-48 gap-3">
                    @foreach($energyData as $label => $count)
                        @php
                            $maxEnergy = max(array_values($energyData));
                            $height = $maxEnergy > 0 ? ($count / $maxEnergy) * 100 : 0;
                        @endphp
                        <div class="flex-1 flex flex-col items-center group">
                            <div class="relative w-full flex-1 flex flex-col justify-end">
                                <div class="w-full bg-gradient-to-t from-blue-600/40 to-blue-400/10 border-x border-t border-blue-500/20 rounded-t-xl group-hover:from-blue-600/60 transition-all duration-500" 
                                     style="height: {{ $height }}%">
                                    <div class="absolute -top-8 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity bg-slate-800 text-[0.6rem] font-bold text-white px-2 py-1 rounded-md border border-slate-700 whitespace-nowrap z-10">
                                        {{ $count }} Tasks
                                    </div>
                                </div>
                            </div>
                            <span class="text-[0.6rem] font-bold uppercase tracking-widest text-slate-500 mt-4 rotate-[-45deg] origin-top-left">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </main>
</div>
