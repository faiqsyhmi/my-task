<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>MyTask | Design Your Day</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans bg-[#0a0f1d] text-slate-100 flex items-center justify-center min-h-screen">
        <div class="max-w-md w-full px-8 text-center">
            <div class="mb-8 flex justify-center">
                <div class="w-20 h-20 bg-blue-600 rounded-2xl flex items-center justify-center shadow-[0_0_30px_rgba(37,99,235,0.3)]">
                    <span class="text-3xl font-bold text-white">M</span>
                </div>
            </div>
            
            <h1 class="text-4xl font-bold tracking-tight mb-4">MyTask</h1>
            <p class="text-slate-400 text-lg mb-10 leading-relaxed">
                A minimalist, ADHD-friendly space to capture thoughts and design your perfect day.
            </p>

            <div class="space-y-4">
                <a href="{{ route('dashboard') }}" class="block w-full py-4 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl shadow-lg transition-all transform hover:scale-[1.02] active:scale-[0.98]">
                    Enter Your Workspace
                </a>
                
                @if (Route::has('login'))
                    <div class="flex items-center justify-center gap-6 pt-4 text-sm font-medium text-slate-500">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="hover:text-slate-300">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="hover:text-slate-300">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="hover:text-slate-300">Register</a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>

            <footer class="mt-20 text-slate-600 text-xs">
                &copy; {{ date('Y') }} MyTask. Built for focus.
            </footer>
        </div>
    </body>
</html>
