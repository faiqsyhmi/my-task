@php
    $taskViewUrl = fn (string $view) => route('tasks.index', ['view' => $view]);

    $items = [
        ['label' => 'Dashboard', 'href' => route('dashboard'), 'active' => request()->routeIs('dashboard') || (request()->routeIs('tasks.index') && ! request('view')), 'icon' => 'dashboard'],
        ['label' => 'Notes', 'href' => $taskViewUrl('notes'), 'active' => request()->routeIs('tasks.index') && request('view') === 'notes', 'icon' => 'notes'],
        ['label' => 'Reminders', 'href' => $taskViewUrl('reminders'), 'active' => request()->routeIs('tasks.index') && request('view') === 'reminders', 'icon' => 'reminders'],
        ['label' => 'Pulse', 'href' => route('analytics.index'), 'active' => request()->routeIs('analytics.*'), 'icon' => 'pulse'],
        ['label' => 'Category 1', 'href' => $taskViewUrl('category-1'), 'active' => request()->routeIs('tasks.index') && request('view') === 'category-1', 'icon' => 'tag'],
        ['label' => 'Category 2', 'href' => $taskViewUrl('category-2'), 'active' => request()->routeIs('tasks.index') && request('view') === 'category-2', 'icon' => 'tag'],
        ['label' => 'Archive', 'href' => $taskViewUrl('archive'), 'active' => request()->routeIs('tasks.index') && request('view') === 'archive', 'icon' => 'archive'],
        ['label' => 'Trash', 'href' => $taskViewUrl('trash'), 'active' => request()->routeIs('tasks.index') && request('view') === 'trash', 'icon' => 'trash'],
    ];
@endphp

<aside
    class="app-sidebar"
    :class="{ 'app-sidebar--collapsed': ! sidebarExpanded }"
    aria-label="Main navigation"
>
    <div class="app-sidebar__header">
        <button
            type="button"
            class="app-sidebar__toggle"
            @click="toggleSidebar"
            :aria-expanded="sidebarExpanded.toString()"
            aria-controls="app-sidebar-navigation"
            title="Toggle main menu"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-width="1.8" d="M5 7h14M5 12h14M5 17h14" />
            </svg>
        </button>

        <a href="{{ route('dashboard') }}" class="app-sidebar__brand" wire:navigate>
            <span class="app-sidebar__brand-mark" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="m7 12 3 3 7-7" />
                </svg>
            </span>
            <span class="app-sidebar__brand-name">MyTask</span>
        </a>
    </div>

    <nav id="app-sidebar-navigation" class="app-sidebar__nav">
        @foreach ($items as $item)
            <a
                href="{{ $item['href'] }}"
                wire:navigate
                class="app-sidebar__link {{ $item['active'] ? 'app-sidebar__link--active' : '' }}"
                title="{{ $item['label'] }}"
                @if ($item['active']) aria-current="page" @endif
            >
                <span class="app-sidebar__icon" aria-hidden="true">
                    @switch($item['icon'])
                        @case('dashboard')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z" /></svg>
                            @break
                        @case('reminders')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 8h18c0-1-3-1-3-8ZM10 20h4" /></svg>
                            @break
                        @case('notes')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 3h9l4 4v14H6zM14 3v5h5M9 12h6M9 16h6" /></svg>
                            @break
                        @case('pulse')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12h4l2.5-6 5 12 2.5-6h4" /></svg>
                            @break
                        @case('tag')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 13 13 20l-9-9V4h7l9 9Z" /><path stroke-linecap="round" stroke-width="2" d="M8 8h.01" /></svg>
                            @break
                        @case('archive')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16v13H4zM3 4h18v3H3zM9 12h6" /></svg>
                            @break
                        @case('trash')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M9 7V4h6v3m3 0-1 13H7L6 7m4 4v5m4-5v5" /></svg>
                            @break
                    @endswitch
                </span>
                <span class="app-sidebar__label">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>
</aside>
