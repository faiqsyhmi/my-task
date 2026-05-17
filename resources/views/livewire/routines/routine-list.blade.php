<div class="app-shell px-4">
    {{-- ══════════ HEADER ══════════ --}}
    <header class="app-header max-w-7xl mx-auto w-full">
        <div class="flex items-center justify-between mb-8">
            <div>
                <p class="app-header__greeting">Daily Rituals</p>
                <h1 class="app-header__title">My Routines</h1>
            </div>
            <div class="btn-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
        </div>

        {{-- ── Filter Tabs ── --}}
        <div class="filter-tabs">
            <button
                wire:click="setEnergy(null)"
                class="filter-tab {{ $energyFilter === null ? 'filter-tab--active' : '' }}"
            >All</button>

            {{-- Energy Filters --}}
            @foreach([1 => '🔋 Easy', 3 => '⚡ Mid', 5 => '🔥 Hard'] as $lvl => $lbl)
                <button
                    wire:click="setEnergy({{ $energyFilter === $lvl ? 'null' : $lvl }})"
                    class="filter-tab filter-tab--energy-{{ $lvl }} {{ $energyFilter === $lvl ? 'filter-tab--active' : '' }}"
                >{{ $lbl }}</button>
            @endforeach
        </div>

        {{-- ── Select All Toggle ── --}}
        @if(!$routines->isEmpty())
            <div class="flex items-center gap-2 mt-4 px-1">
                <input type="checkbox" wire:model.live="selectAll" id="selectAll" class="w-4 h-4 rounded border-slate-700 bg-slate-900 text-blue-600 focus:ring-blue-500/20">
                <label for="selectAll" class="text-xs font-semibold uppercase tracking-wider text-slate-500 cursor-pointer select-none">Select All Routines</label>
            </div>
        @endif
    </header>

    {{-- ══════════ ROUTINE LIST ══════════ --}}
    <main class="task-list max-w-7xl mx-auto w-full transition-opacity duration-200" 
          wire:loading.class="opacity-50 pointer-events-none" 
          id="routine-list-main">
        @if($routines->isEmpty())
            <div class="empty-state">
                <div class="empty-state__icon text-slate-600">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="empty-state__text">
                    No routines yet.<br>Tap + to build a habit.
                </p>
            </div>
        @else
            @foreach($routines as $routine)
                @include('livewire.routines.partials.routine-card', ['routine' => $routine])
            @endforeach
        @endif
    </main>

    {{-- ══════════ FLOATING ACTION BUTTON ══════════ --}}
    <button 
        x-data="" 
        x-on:click="$dispatch('open-modal')"
        class="fab"
        title="Add Routine"
    >
        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.0" d="M12 4v16m8-8H4"/>
        </svg>
    </button>

    {{-- ══════════ BULK ACTIONS BAR ══════════ --}}
    @if(count($selectedRoutineIds) > 0)
        <div class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 flex items-center gap-4 px-6 py-3 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl animate-in fade-in slide-in-from-bottom-4 duration-300">
            <span class="text-sm font-bold text-slate-100 whitespace-nowrap">{{ count($selectedRoutineIds) }} selected</span>
            
            <div class="w-px h-4 bg-slate-700"></div>

            <div class="flex items-center gap-2">
                <button wire:click="bulkComplete" class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold uppercase tracking-wider text-emerald-400 hover:bg-emerald-400/10 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Mark Done
                </button>
                <button wire:click="bulkDelete" onclick="return confirm('Delete selected routines?')" class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold uppercase tracking-wider text-rose-500 hover:bg-rose-500/10 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete
                </button>
            </div>

            <div class="w-px h-4 bg-slate-700"></div>

            <button wire:click="$set('selectedRoutineIds', [])" class="text-xs font-bold uppercase tracking-wider text-slate-500 hover:text-slate-300">
                Cancel
            </button>
        </div>
    @endif
</div>
