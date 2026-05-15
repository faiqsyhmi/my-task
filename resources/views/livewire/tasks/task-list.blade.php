<div class="app-shell px-4">
    {{-- ══════════ HEADER ══════════ --}}
    <header class="app-header max-w-7xl mx-auto w-full">
        <div class="flex items-center justify-between mb-8">
            <div>
                <p class="app-header__greeting">Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 18 ? 'Afternoon' : 'Evening') }}, {{ explode(' ', auth()->user()->name)[0] }}</p>
                <h1 class="app-header__title">
                    @if($statusFilter === 'starred')
                        Starred Tasks
                    @elseif($dateFilter)
                        {{ \Carbon\Carbon::parse($dateFilter)->format('D, M j') }}
                    @else
                        My Tasks
                    @endif
                </h1>
            </div>
            <div class="w-12 h-12 rounded-full bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-100 font-bold">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
        </div>

        {{-- ── Mini Calendar Strip ── --}}
        <div class="calendar-strip">
            <div class="calendar-strip__nav">
                <button wire:click="prevWeek" class="btn-nav">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <span class="calendar-strip__month bold uppercase tracking-tight">{{ $monthLabel }}</span>
                <button wire:click="nextWeek" class="btn-nav">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
            
            <div class="calendar-strip__days">
                @foreach($weekDays as $day)
                    <button
                        wire:click="filterByDate('{{ $day['date'] }}')"
                        class="calendar-day {{ ($dateFilter === $day['date']) ? 'calendar-day--active' : '' }}"
                    >
                        <span class="calendar-day__label">{{ substr($day['label'], 0, 3) }}</span>
                        <span class="calendar-day__num">{{ $day['num'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- ── Filter Tabs ── --}}
        <div class="filter-tabs">
            @foreach([
                'starred' => '★',
                'all'   => 'All',
                'todo'  => 'To Do',
                'doing' => 'Doing',
                'done'  => 'Done',
            ] as $val => $label)

                <button
                    wire:click="setStatus('{{ $val }}')"
                    class="filter-tab {{ $statusFilter === $val ? 'filter-tab--active' : '' }}"
                >{{ $label }}</button>
            @endforeach

            {{-- Energy Filters --}}
            @foreach([1 => '🔋 Easy', 3 => '⚡ Mid', 5 => '🔥 Hard'] as $lvl => $lbl)
                <button
                    wire:click="setEnergy({{ $energyFilter === $lvl ? 'null' : $lvl }})"
                    class="filter-tab filter-tab--energy-{{ $lvl }} {{ $energyFilter === $lvl ? 'filter-tab--active' : '' }}"
                >{{ $lbl }}</button>
            @endforeach
        </div>

        {{-- ── Select All Toggle ── --}}
        @if(!$tasks->isEmpty())
            <div class="flex items-center gap-2 mt-4 px-1">
                <input type="checkbox" wire:model.live="selectAll" id="selectAll" class="w-4 h-4 rounded border-slate-700 bg-slate-900 text-blue-600 focus:ring-blue-500/20">
                <label for="selectAll" class="text-xs font-semibold uppercase tracking-wider text-slate-500 cursor-pointer select-none">Select All Tasks</label>
            </div>
        @endif
    </header>

    {{-- ══════════ TASK LIST ══════════ --}}
    <main class="task-list max-w-7xl mx-auto w-full transition-opacity duration-200" 
          wire:loading.class="opacity-50 pointer-events-none" 
          id="task-list-main">
        @if($tasks->isEmpty())
            <div class="empty-state">
                <div class="empty-state__icon text-slate-600">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
                <p class="empty-state__text">
                    Your mind is clear!<br>Tap + to capture a thought.
                </p>
            </div>
        @else
            {{-- Tasks group --}}
            @foreach($tasks as $task)
                @include('livewire.tasks.partials.task-card', ['task' => $task])
            @endforeach
        @endif
    </main>

    {{-- ══════════ FLOATING ACTION BUTTON ══════════ --}}
    <button 
        x-data="" 
        x-on:click="$dispatch('open-modal')"
        class="fab"
        title="Add Task"
    >
        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.0" d="M12 4v16m8-8H4"/>
        </svg>
    </button>

    {{-- ══════════ BULK ACTIONS BAR ══════════ --}}
    @if(count($selectedTaskIds) > 0)
        <div class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 flex items-center gap-4 px-6 py-3 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl animate-in fade-in slide-in-from-bottom-4 duration-300">
            <span class="text-sm font-bold text-slate-100 whitespace-nowrap">{{ count($selectedTaskIds) }} selected</span>
            
            <div class="w-px h-4 bg-slate-700"></div>

            <div class="flex items-center gap-2">
                <button wire:click="bulkComplete" class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold uppercase tracking-wider text-emerald-400 hover:bg-emerald-400/10 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Done
                </button>
                <button wire:click="bulkIncomplete" class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold uppercase tracking-wider text-amber-400 hover:bg-amber-400/10 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Todo
                </button>
                <button wire:click="bulkDelete" onclick="return confirm('Delete selected tasks?')" class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold uppercase tracking-wider text-rose-500 hover:bg-rose-500/10 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete
                </button>
            </div>

            <div class="w-px h-4 bg-slate-700"></div>

            <button wire:click="$set('selectedTaskIds', [])" class="text-xs font-bold uppercase tracking-wider text-slate-500 hover:text-slate-300">
                Cancel
            </button>
        </div>
    @endif
</div>
