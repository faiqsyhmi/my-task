<div class="task-card flex items-center justify-between group {{ $routine->is_completed ? 'opacity-50' : '' }}">
    <div class="flex items-center gap-4 flex-grow">
        {{-- Selection Checkbox --}}
        <input 
            type="checkbox" 
            wire:model.live="selectedRoutineIds" 
            value="{{ (string)$routine->id }}" 
            class="w-5 h-5 rounded-lg border-slate-700 bg-slate-900 text-blue-600 focus:ring-blue-500/20 opacity-0 group-hover:opacity-100 transition-opacity"
        >

        {{-- Completion Toggle --}}
        <button wire:click="toggleStatus('{{ $routine->id }}')" class="p-1 rounded-full transition-all {{ $routine->is_completed ? 'text-emerald-400 bg-emerald-400/10' : 'text-slate-600 hover:text-slate-400' }}">
            @if($routine->is_completed)
                <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            @else
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @endif
        </button>

        <div class="flex flex-col">
            <h3 class="text-sm font-bold text-slate-100 {{ $routine->is_completed ? 'line-through text-slate-500' : '' }}">
                {{ $routine->title }}
            </h3>
            @if($routine->description)
                <p class="text-xs text-slate-500 line-clamp-1 group-hover:line-clamp-none transition-all">
                    {{ $routine->description }}
                </p>
            @endif
        </div>
    </div>

    <div class="flex items-center gap-3">
        {{-- Frequency Badge --}}
        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-800 text-slate-400">
            {{ $routine->frequency }}
        </span>

        {{-- Energy Indicator --}}
        <div class="flex gap-0.5">
            @for($i=1; $i<=5; $i++)
                <div class="w-1 h-3 rounded-full {{ $i <= $routine->energy_level ? ($routine->energy_level >= 4 ? 'bg-rose-500' : ($routine->energy_level >= 3 ? 'bg-amber-400' : 'bg-emerald-400')) : 'bg-slate-800' }}"></div>
            @endfor
        </div>

        {{-- Delete Button --}}
        <button wire:click="deleteRoutine('{{ $routine->id }}')" onclick="return confirm('Delete this routine?')" class="p-2 text-slate-700 hover:text-rose-500 opacity-0 group-hover:opacity-100 transition-opacity">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </button>
    </div>
</div>
