@php
    $isDone   = $task->status === 'done';
    $isOverdue = $task->due_at && !$isDone && $task->due_at->isPast();
    $energyColors = [
        1 => '#10b981', // emerald
        3 => '#f59e0b', // amber
        5 => '#f43f5e', // rose
    ];
@endphp

<div wire:key="task-{{ $task->id }}" class="task-card flex flex-col bg-[#0a0f1d] border border-slate-800 rounded-2xl transition-all hover:border-slate-700">
    {{-- Main Row --}}
    <div class="flex items-center gap-4 p-4">
        {{-- Selection Checkbox --}}
        <div class="flex-shrink-0">
            <input type="checkbox" 
                   value="{{ $task->id }}" 
                   wire:model.live="selectedTaskIds" 
                   class="w-4 h-4 rounded border-slate-700 bg-slate-900 text-blue-600 focus:ring-blue-500/20 transition-all cursor-pointer">
        </div>

        {{-- Body (Clickable Area) --}}
        <div class="flex-grow cursor-pointer group" wire:click="toggleExpand('{{ $task->id }}')">
            <p class="text-sm font-medium {{ $isDone ? 'text-slate-500 line-through' : 'text-slate-100 group-hover:text-blue-400' }} transition-colors">
                {{ $task->title }}
            </p>
            
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

                @if($task->description)
                    <svg class="w-3 h-3 text-slate-600 transition-transform {{ in_array($task->id, $expandedTaskIds) ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-1">
            <button
                wire:click="startEditingDetails('{{ $task->id }}')"
                class="p-2 text-slate-700 hover:text-blue-400 transition-colors"
                title="{{ $task->description ? 'Edit details' : 'Add details' }}"
                aria-label="{{ $task->description ? 'Edit details' : 'Add details' }} for {{ $task->title }}"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </button>

            <div class="flex flex-shrink-0 items-center rounded-lg border border-slate-800 bg-slate-900/70 p-0.5" role="group" aria-label="Set status for {{ $task->title }}">
                @foreach(['todo' => 'To Do', 'doing' => 'Doing', 'done' => 'Done'] as $status => $label)
                    <button
                        type="button"
                        wire:click="setTaskStatus('{{ $task->id }}', '{{ $status }}')"
                        wire:loading.attr="disabled"
                        wire:target="setTaskStatus('{{ $task->id }}', '{{ $status }}')"
                        aria-pressed="{{ $task->status === $status ? 'true' : 'false' }}"
                        class="rounded-md px-2 py-1 text-[10px] font-bold uppercase tracking-wide transition-colors disabled:opacity-50 {{ $task->status === $status ? 'bg-blue-600 text-white' : 'text-slate-500 hover:bg-slate-800 hover:text-slate-200' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>

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

    {{-- Expanded Details --}}
    @if(in_array($task->id, $expandedTaskIds))
        <div class="px-14 pb-5 pt-2 border-t border-slate-800/30 animate-in fade-in slide-in-from-top-1 duration-200">
            @if($editingTaskId === $task->id)
                <form wire:submit="saveDetails" class="space-y-3">
                    <label for="task-details-{{ $task->id }}" class="sr-only">Task details</label>
                    <textarea
                        id="task-details-{{ $task->id }}"
                        wire:model="editingDescription"
                        class="form-input form-input--textarea"
                        placeholder="Add task details..."
                        maxlength="1000"
                        autofocus
                    ></textarea>
                    @error('editingDescription')
                        <p class="text-xs text-rose-500">{{ $message }}</p>
                    @enderror

                    <div class="flex justify-end gap-2">
                        <button type="button" wire:click="cancelEditingDetails" class="px-4 py-2 text-xs font-bold text-slate-400 hover:text-slate-200 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-xs font-bold text-white hover:bg-blue-500 transition-colors" wire:loading.attr="disabled">
                            Save details
                        </button>
                    </div>
                </form>
            @else
                <div class="flex items-start justify-between gap-4">
                    <div class="text-slate-400 text-sm leading-relaxed whitespace-pre-wrap">
                        {{ $task->description ?: 'No details added.' }}
                    </div>
                    <button wire:click="startEditingDetails('{{ $task->id }}')" class="flex-shrink-0 text-xs font-bold text-blue-400 hover:text-blue-300 transition-colors">
                        {{ $task->description ? 'Edit' : 'Add details' }}
                    </button>
                </div>
            @endif

            <div class="mt-5 pt-4 border-t border-slate-800/50">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">
                        Attachments ({{ $task->attachments->count() }}/{{ \Modules\Tasks\Models\TaskAttachment::MAX_FILES_PER_TASK }})
                    </p>

                    @if($attachmentTaskId !== $task->id && $task->attachments->count() < \Modules\Tasks\Models\TaskAttachment::MAX_FILES_PER_TASK)
                        <button wire:click="startAddingAttachments('{{ $task->id }}')" class="text-xs font-bold text-blue-400 hover:text-blue-300 transition-colors">
                            Add files
                        </button>
                    @endif
                </div>

                @if($task->attachments->isNotEmpty())
                    <ul class="space-y-2 mb-3">
                        @foreach($task->attachments as $attachment)
                            <li wire:key="attachment-{{ $attachment->id }}" class="flex items-center justify-between gap-3 rounded-xl bg-slate-900/60 px-3 py-2">
                                <a
                                    href="{{ route('tasks.attachments.download', $attachment) }}"
                                    class="min-w-0 text-xs text-slate-300 hover:text-blue-300 truncate"
                                    title="Download {{ $attachment->original_name }}"
                                >
                                    {{ $attachment->original_name }}
                                    <span class="text-slate-600">({{ number_format($attachment->size / 1024, 1) }} KB)</span>
                                </a>
                                <button
                                    wire:click="deleteAttachment('{{ $attachment->id }}')"
                                    wire:confirm="Delete this attachment?"
                                    class="flex-shrink-0 text-xs text-slate-600 hover:text-rose-500 transition-colors"
                                    aria-label="Delete {{ $attachment->original_name }}"
                                >
                                    Delete
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if($attachmentTaskId === $task->id)
                    <form wire:submit="saveAttachments" class="space-y-3">
                        <input
                            type="file"
                            wire:model="pendingAttachments"
                            accept=".jpg,.jpeg,.png,.webp,.pdf,.xlsx,.txt"
                            multiple
                            class="block w-full text-xs text-slate-400 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-600 file:px-3 file:py-2 file:font-bold file:text-white hover:file:bg-blue-500"
                        >
                        <p class="text-[11px] text-slate-600">Images, PDF, XLSX, or TXT. Up to 5 files, 10 MB each.</p>

                        @error('pendingAttachments')
                            <p class="text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                        @error('pendingAttachments.*')
                            <p class="text-xs text-rose-500">{{ $message }}</p>
                        @enderror

                        <div wire:loading wire:target="pendingAttachments" class="text-xs text-blue-400">Checking files...</div>

                        <div class="flex justify-end gap-2">
                            <button type="button" wire:click="cancelAddingAttachments" class="px-3 py-2 text-xs font-bold text-slate-400 hover:text-slate-200">
                                Cancel
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-bold text-white hover:bg-blue-500">
                                Save files
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endif
</div>
