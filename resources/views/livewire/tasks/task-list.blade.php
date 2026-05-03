<div class="app-shell px-4">
    {{-- ══════════ HEADER ══════════ --}}
    <header class="app-header max-w-7xl mx-auto w-full">
        <div class="flex items-center justify-between mb-8">
            <div>
                <p class="app-header__greeting">Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 18 ? 'Afternoon' : 'Evening') }}, {{ explode(' ', auth()->user()->name)[0] }}</p>
                <h1 class="app-header__title">
                    @if($dateFilter)
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
</div>
