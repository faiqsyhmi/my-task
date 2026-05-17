<div
    x-data="{ shortcut(e){ if(e.ctrlKey && e.code==='Space'){ e.preventDefault(); $wire.openModal(); } } }"
    @keydown.window="shortcut"
    x-on:open-modal.window="$wire.openModal()"
>
    @if($open)
    <div
        class="modal-backdrop"
        x-data
        @click.self="$wire.closeModal()"
        @keydown.escape.window="$wire.closeModal()"
    >
        <div class="modal-sheet" role="dialog" aria-modal="true" aria-label="Add new routine">

            <div class="modal-handle"></div>
            <h2 class="modal-title">✨ New Routine</h2>

            <form wire:submit.prevent="save" novalidate>

                {{-- Title --}}
                <div class="form-field">
                    <label for="routine-title" class="form-label">What's the ritual?</label>
                    <input
                        id="routine-title"
                        wire:model.defer="title"
                        type="text"
                        class="form-input"
                        placeholder="e.g. Morning Stretch…"
                        autocomplete="off"
                        autofocus
                        maxlength="255"
                    >
                    @error('title') <p style="color:#ef4444;font-size:.75rem;margin-top:4px;">{{ $message }}</p> @enderror
                </div>

                {{-- Description --}}
                <div class="form-field">
                    <label for="routine-desc" class="form-label">Details (optional)</label>
                    <textarea
                        id="routine-desc"
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
                    <p style="font-size:.68rem;color:var(--text-secondary);margin-top:4px;">
                        {{ ['','😴 Very Low','🧘 Low','⚡ Medium','🎯 High','🔥 Max Focus'][$energyLevel] }}
                    </p>
                </div>

                {{-- Frequency --}}
                <div class="form-field">
                    <label for="routine-freq" class="form-label">Frequency</label>
                    <select id="routine-freq" wire:model.defer="frequency" class="form-input">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                    </select>
                </div>

                <button
                    type="submit"
                    class="btn-primary"
                    wire:loading.attr="disabled"
                    id="save-routine-btn"
                >
                    <span wire:loading.remove>Build Habit</span>
                    <span wire:loading>Saving…</span>
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
