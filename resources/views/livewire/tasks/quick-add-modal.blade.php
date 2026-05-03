<div
    x-data="{ shortcut(e){ if(e.ctrlKey && e.code==='Space'){ e.preventDefault(); $wire.openModal(); } } }"
    @keydown.window="shortcut"
    x-on:open-modal.window="$wire.openModal()"
>
    {{-- Trigger handled externally via Ctrl+Space or FAB --}}

    @if($open)
    <div
        class="modal-backdrop"
        x-data
        @click.self="$wire.closeModal()"
        @keydown.escape.window="$wire.closeModal()"
    >
        <div class="modal-sheet" role="dialog" aria-modal="true" aria-label="Add new task">

            <div class="modal-handle"></div>
            <h2 class="modal-title">🧠 Brain Dump</h2>

            <form wire:submit.prevent="save" novalidate>

                {{-- Title --}}
                <div class="form-field">
                    <label for="task-title" class="form-label">What's on your mind?</label>
                    <input
                        id="task-title"
                        wire:model.defer="title"
                        type="text"
                        class="form-input"
                        placeholder="e.g. Reply to Jordan's email…"
                        autocomplete="off"
                        autofocus
                        maxlength="255"
                    >
                    @error('title') <p style="color:#ef4444;font-size:.75rem;margin-top:4px;">{{ $message }}</p> @enderror
                </div>

                {{-- Description --}}
                <div class="form-field">
                    <label for="task-desc" class="form-label">Details (optional)</label>
                    <textarea
                        id="task-desc"
                        wire:model.defer="description"
                        class="form-input form-input--textarea"
                        placeholder="Any extra context…"
                        maxlength="1000"
                    ></textarea>
                </div>

                {{-- Energy Level --}}
                <div class="form-field">
                    <label class="form-label">🔋 Brain Power Required</label>
                    <div class="energy-slider-wrap">
                        <input
                            wire:model.live="energyLevel"
                            type="range"
                            min="1" max="5" step="1"
                            class="energy-slider"
                            id="energy-slider"
                            aria-label="Energy level"
                        >
                        <span class="energy-label">{{ $energyLevel }}</span>
                    </div>
                    <p style="font-size:.68rem;color:var(--muted);margin-top:4px;">
                        {{ ['','😴 Very Low','🧘 Low','⚡ Medium','🎯 High','🔥 Max Focus'][$energyLevel] }}
                    </p>
                </div>

                {{-- Due Date --}}
                <div class="form-field">
                    <label for="task-due" class="form-label">Due date (optional)</label>
                    <input
                        id="task-due"
                        wire:model.defer="dueAt"
                        type="date"
                        class="form-input"
                        min="{{ now()->toDateString() }}"
                    >
                </div>

                <button
                    type="submit"
                    class="btn-primary"
                    wire:loading.attr="disabled"
                    id="save-task-btn"
                >
                    <span wire:loading.remove>Capture Task</span>
                    <span wire:loading>Saving…</span>
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
