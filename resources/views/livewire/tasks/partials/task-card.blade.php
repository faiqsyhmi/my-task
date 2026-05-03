@php
    $isDone   = $task->status === 'done';
    $isOverdue = $task->due_at && !$isDone && $task->due_at->isPast();
    $energyColors = [
        1 => '#10b981', // emerald
        3 => '#f59e0b', // amber
        5 => '#f43f5e', // rose
    ];
@endphp

<div wire:key="task-{{ $task->id }}" class="task-card flex items-center gap-4 bg-[#0a0f1d] border border-slate-800 rounded-2xl p-4 transition-all hover:border-slate-700">
    {{-- Checkbox --}}
    <button
        wire:click="toggleStatus('{{ $task->id }}')"
        class="flex-shrink-0 w-6 h-6 rounded-lg border-2 {{ $isDone ? 'bg-blue-600 border-blue-600' : 'border-slate-700 hover:border-blue-500' }} flex items-center justify-center transition-all"
    >
        @if($isDone)
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
        @endif
    </button>

    {{-- Body --}}
    <div class="flex-grow">
        <p class="text-sm font-medium {{ $isDone ? 'text-slate-500 line-through' : 'text-slate-100' }}">{{ $task->title }}</p>
        
        <div class="flex items-center gap-3 mt-1">
            <div class="flex gap-0.5">
                @for($i = 1; $i <= 5; $i++)
                    <div class="w-3 h-1 rounded-full {{ $i <= $task->energy_level ? '' : 'bg-slate-800' }}"
                         style="{{ $i <= $task->energy_level ? 'background:' . ($energyColors[$task->energy_level] ?? '#3b82f6') : '' }}">
                    </div>
                @endfor
            </div>
            
            @if($task->due_at)
                <span class="text-[10px] uppercase tracking-wider font-bold {{ $isOverdue ? 'text-rose-500' : 'text-slate-500' }}">
                    {{ $task->due_at->format('M j') }}
                </span>
            @endif

            @if($task->status === 'doing')
                <span class="text-[10px] font-black uppercase text-blue-400">In Progress</span>
            @endif
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-1">
        <button
            wire:click="toggleFlag('{{ $task->id }}')"
            class="p-2 transition-colors {{ $task->is_flagged ? 'text-blue-500' : 'text-slate-700 hover:text-slate-500' }}"
        >
            <svg class="w-5 h-5" fill="{{ $task->is_flagged ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.518 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.921-.755 1.688-1.54 1.118l-3.976-2.888a1 1 0 00-1.175 0l-3.976 2.888c-.784.57-1.838-.197-1.539-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
            </svg>
        </button>
        
        <button
            wire:click="deleteTask('{{ $task->id }}')"
            onclick="return confirm('Delete task?')"
            class="p-2 text-slate-800 hover:text-rose-500 transition-colors"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </button>
    </div>
</div>
