<div class="min-h-screen flex flex-col items-center justify-center p-6 bg-[#0a0f1d] relative overflow-hidden" 
     x-data="{ 
        timeLeft: 1500, 
        isRunning: false, 
        timer: null,
        mode: 'work',
        progress: 0,
        totalTime: 1500,

        init() {
            this.$watch('timeLeft', value => {
                this.progress = ((this.totalTime - value) / this.totalTime) * 100;
            });
        },

        formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        },

        toggleTimer() {
            if (this.isRunning) {
                clearInterval(this.timer);
                this.isRunning = false;
            } else {
                this.isRunning = true;
                this.timer = setInterval(() => {
                    if (this.timeLeft > 0) {
                        this.timeLeft--;
                    } else {
                        this.finishSession();
                    }
                }, 1000);
            }
        },

        resetTimer() {
            clearInterval(this.timer);
            this.isRunning = false;
            this.timeLeft = this.mode === 'work' ? 1500 : 300;
            this.totalTime = this.timeLeft;
        },

        setMode(newMode) {
            this.mode = newMode;
            this.resetTimer();
        },

        finishSession() {
            clearInterval(this.timer);
            this.isRunning = false;
            alert(this.mode === 'work' ? 'Break time!' : 'Back to work!');
            this.setMode(this.mode === 'work' ? 'break' : 'work');
        }
     }">

    {{-- Background Glow --}}
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-blue-500/10 rounded-full blur-[120px] pointer-events-none"></div>

    {{-- Exit Button --}}
    <div class="absolute top-8 left-8">
        <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2 text-slate-500 hover:text-slate-100 transition-colors group">
            <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7"/></svg>
            <span class="text-xs uppercase tracking-widest font-bold">Exit Focus</span>
        </a>
    </div>

    <div class="max-w-2xl w-full flex flex-col items-center">
        @if(!$isFocusing)
            {{-- Task Selection --}}
            <div class="w-full text-center mb-12">
                <p class="uppercase tracking-[0.3em] text-[0.7rem] text-blue-400 font-bold mb-3">Target Objective</p>
                <h1 class="text-4xl font-black text-white tracking-tight leading-tight">What are we crushing?</h1>
            </div>

            <div class="w-full space-y-3 mb-10">
                @foreach($tasks as $task)
                    <button wire:click="selectTask('{{ $task->id }}')" 
                            class="w-full p-6 rounded-3xl border transition-all duration-300 text-left flex items-center justify-between group {{ $selectedTaskId == $task->id ? 'bg-blue-500/10 border-blue-500/50 shadow-[0_0_20px_rgba(59,130,246,0.1)]' : 'bg-slate-900/40 border-slate-800 hover:border-slate-700' }}">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $selectedTaskId == $task->id ? 'bg-blue-500 text-white' : 'bg-slate-800 text-slate-500 group-hover:bg-slate-700' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <h3 class="font-bold {{ $selectedTaskId == $task->id ? 'text-white' : 'text-slate-300' }}">{{ $task->title }}</h3>
                                <p class="text-xs text-slate-500">Energy required: {{ $task->energy_level }}</p>
                            </div>
                        </div>
                        @if($task->is_flagged)
                            <div class="text-amber-400">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            </div>
                        @endif
                    </button>
                @endforeach

                @if($tasks->isEmpty())
                    <div class="p-12 text-center bg-slate-900/20 border-2 border-dashed border-slate-800 rounded-[2.5rem]">
                        <p class="text-slate-500 italic">No tasks left in your todo list. Time to rest or add more!</p>
                    </div>
                @endif
            </div>

            @if($activeTask)
                <button wire:click="startFocus" class="px-10 py-5 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl font-black text-lg shadow-[0_0_30px_rgba(59,130,246,0.3)] transition-all hover:scale-105 active:scale-95 uppercase tracking-widest">
                    Enter Hyperfocus
                </button>
            @endif

        @else
            {{-- Distraction-Free Timer View --}}
            <div class="w-full text-center">
                <p class="uppercase tracking-[0.4em] text-[0.8rem] text-blue-400 font-bold mb-6" x-text="mode === 'work' ? 'Stay Focused' : 'Bio-Reload'"></p>
                
                {{-- Large Timer --}}
                <div class="relative w-72 h-72 mx-auto mb-12">
                    <svg class="w-full h-full -rotate-90">
                        <circle cx="144" cy="144" r="130" stroke="currentColor" stroke-width="8" fill="transparent" class="text-slate-800" />
                        <circle cx="144" cy="144" r="130" stroke="currentColor" stroke-width="8" fill="transparent" 
                                class="text-blue-500 transition-all duration-1000" 
                                style="stroke-dasharray: 816.8; stroke-dashoffset: calc(816.8 - (816.8 * progress) / 100)" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-7xl font-black text-white tabular-nums tracking-tighter" x-text="formatTime(timeLeft)"></span>
                    </div>
                </div>

                {{-- Task Info --}}
                <div class="mb-12 p-8 bg-slate-900/40 border border-slate-800 rounded-[2.5rem] backdrop-blur-xl">
                    <h2 class="text-2xl font-black text-white mb-2">{{ $activeTask->title }}</h2>
                    <p class="text-slate-400 max-w-md mx-auto">{{ $activeTask->description ?: 'No description provided.' }}</p>
                </div>

                {{-- Timer Controls --}}
                <div class="flex items-center justify-center gap-6">
                    <button @click="resetTimer" class="p-4 bg-slate-800 hover:bg-slate-700 text-slate-400 rounded-2xl transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </button>
                    
                    <button @click="toggleTimer" 
                            class="w-20 h-20 flex items-center justify-center rounded-3xl transition-all hover:scale-110"
                            :class="isRunning ? 'bg-orange-600/20 text-orange-400 border border-orange-500/30' : 'bg-emerald-600/20 text-emerald-400 border border-emerald-500/30'">
                        <template x-if="!isRunning">
                            <svg class="w-10 h-10 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </template>
                        <template x-if="isRunning">
                            <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                        </template>
                    </button>

                    <button wire:click="completeTask" class="p-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl transition-colors shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </button>
                </div>

                {{-- Mode Switcher --}}
                <div class="mt-12 flex bg-slate-900/80 p-1 rounded-2xl border border-slate-800 max-w-xs mx-auto">
                    <button @click="setMode('work')" 
                            class="flex-1 py-2 px-4 rounded-xl text-xs font-black uppercase tracking-widest transition-all"
                            :class="mode === 'work' ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-500 hover:text-slate-300'">Work</button>
                    <button @click="setMode('break')" 
                            class="flex-1 py-2 px-4 rounded-xl text-xs font-black uppercase tracking-widest transition-all"
                            :class="mode === 'break' ? 'bg-purple-600 text-white shadow-lg' : 'text-slate-500 hover:text-slate-300'">Break</button>
                </div>
            </div>
        @endif
    </div>
</div>
