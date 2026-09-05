<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50/50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel')) - Super Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- PWA Manifest & Meta -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Yoimo">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/icons/favicon-16x16.png">

    <!-- Scripts & Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased text-slate-800 min-h-full flex">

    <!-- Sidebar Modular Drawer -->
    @include('superadmin.layouts.partials.sidebar')
    <div id="sidebar-overlay" class="fixed inset-0 bg-slate-950/40 backdrop-blur-xs z-40 hidden xl:hidden" onclick="toggleSidebar()"></div>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 pb-20 xl:pb-0">

        <!-- Header Topbar Modular -->
        @include('superadmin.layouts.partials.header')

        <!-- Dynamic Body Page Content -->
        <main class="flex-1 p-4 sm:p-8">
            @yield('content')
        </main>

        <!-- Footer Modular -->
        @include('superadmin.layouts.partials.footer')

    </div>

    <!-- Mobile Bottom Navigation Bar (xl:hidden) -->
    <div class="fixed bottom-0 inset-x-0 bg-white border-t border-slate-100 z-40 flex items-center justify-around py-2.5 px-4 xl:hidden shadow-[0_-4px_24px_rgba(0,0,0,0.06)] rounded-t-2xl">
        <!-- Home / Dashboard -->
        <a href="{{ route('superadmin.dashboard') }}" class="flex flex-col items-center justify-center gap-0.5 {{ Request::routeIs('superadmin.dashboard') ? 'text-emerald-600 font-bold' : 'text-slate-400 hover:text-emerald-600' }} transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span class="text-[9px] tracking-wide">Home</span>
        </a>

        <!-- Users Manager -->
        <a href="{{ route('superadmin.users.index') }}" class="flex flex-col items-center justify-center gap-0.5 {{ Request::routeIs('superadmin.users.index') ? 'text-emerald-600 font-bold' : 'text-slate-400 hover:text-emerald-600' }} transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <span class="text-[9px] tracking-wide">Users</span>
        </a>

        <!-- Companies Manager -->
        <a href="{{ route('superadmin.company.index') }}" class="flex flex-col items-center justify-center gap-0.5 {{ Request::routeIs('superadmin.company.index') ? 'text-emerald-600 font-bold' : 'text-slate-400 hover:text-emerald-600' }} transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            <span class="text-[9px] tracking-wide">Companies</span>
        </a>

        <!-- Hamburger / Menu -->
        <button type="button" onclick="toggleSidebar()" class="flex flex-col items-center justify-center gap-0.5 text-slate-400 hover:text-emerald-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
            <span class="text-[9px] tracking-wide">Menu</span>
        </button>
    </div>

    <!-- Toggling and PWA Scripts -->
    <script>
        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => console.log('Service worker registered.', reg))
                    .catch((err) => console.log('Service worker registration failed.', err));
            });
        }

        // Mobile Sidebar Drawer Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('mobile-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            if (sidebar && overlay) {
                sidebar.classList.toggle('hidden');
                overlay.classList.toggle('hidden');
            }
        }
    </script>
    @include('layouts.partials.toast')
    @include('layouts.partials.chatbot')
    @include('layouts.partials.quick_nav_modal')
    @stack('scripts')
</body>
</html>
