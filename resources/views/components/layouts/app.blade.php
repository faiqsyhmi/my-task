<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MyTask') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { background-color: #030712 !important; color: #f1f5f9 !important; }
            .min-h-screen { background-color: #030712 !important; }
        </style>
    </head>
    <body
        class="font-sans antialiased text-slate-100 bg-[#030712]"
        x-data="{
            sidebarExpanded: localStorage.getItem('app-sidebar-expanded') !== null
                ? localStorage.getItem('app-sidebar-expanded') === 'true'
                : window.matchMedia('(min-width: 768px)').matches,
            toggleSidebar() {
                this.sidebarExpanded = ! this.sidebarExpanded;
                localStorage.setItem('app-sidebar-expanded', this.sidebarExpanded);
            }
        }"
    >
        <div class="min-h-screen bg-[#030712]">
            <x-app-sidebar />

            <!-- Page Content -->
            <main class="app-content" :class="{ 'app-content--sidebar-collapsed': ! sidebarExpanded }">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
