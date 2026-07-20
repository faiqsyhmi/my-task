<x-layouts.app title="Notes">
    <div class="notes-page">
        <header class="notes-page__header">
            <div>
                <p class="notes-page__eyebrow">Notes</p>
                <h1 class="notes-page__title">All Tasks</h1>
                <p class="notes-page__summary">{{ $tasks->count() }} {{ Str::plural('task', $tasks->count()) }}</p>
            </div>

            <div class="notes-page__avatar" aria-label="Signed in as {{ auth()->user()->name }}">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
        </header>

        @if ($tasks->isEmpty())
            <div class="notes-empty">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 3h9l4 4v14H6zM14 3v5h5M9 12h6M9 16h4" />
                </svg>
                <h2>No tasks yet</h2>
                <p>Tasks you add will appear here as notes.</p>
                <a href="{{ route('tasks.index') }}" wire:navigate>Go to My Tasks</a>
            </div>
        @else
            <main class="notes-grid" aria-label="All task notes">
                @foreach ($tasks as $task)
                    @php
                        $displayDate = $task->due_at ?? $task->created_at;
                        $statusLabels = ['todo' => 'To Do', 'doing' => 'Doing', 'done' => 'Done'];
                    @endphp

                    <article class="note-card">
                        <div class="note-card__meta">
                            <time datetime="{{ $displayDate->toDateString() }}">{{ $displayDate->format('d/m/Y') }}</time>
                            <span class="note-card__status note-card__status--{{ $task->status }}">
                                {{ $statusLabels[$task->status] ?? ucfirst($task->status) }}
                            </span>
                        </div>

                        <h2 class="note-card__title">{{ $task->title }}</h2>

                        <div class="note-card__tasks">
                            @if (filled($task->description))
                                <p>{{ $task->description }}</p>
                            @else
                                <p class="note-card__tasks--empty">No task details.</p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </main>
        @endif
    </div>
</x-layouts.app>
