@php
    $user = Auth::user();
    $myProjects = \App\Models\Project::whereJsonContains('team_matrix', ['user_id' => (string) $user->id])
        ->orWhereJsonContains('team_matrix', ['user_id' => (int) $user->id])
        ->orWhere('created_by', $user->id)
        ->with('company')
        ->latest()
        ->get();
    $assignedCompanies = $myProjects->pluck('company')->filter()->unique('id');
    $activeCompany = isset($company) ? $company : $assignedCompanies->first();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Workspace Anggota Tim') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Google Fonts & Tailwind CDN / Vite -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- PWA Manifest & Meta -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">
    <link rel="apple-touch-icon" href="/logokotak.png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-50/50 flex">

    <!-- Sidebar Desktop & Mobile Drawer -->
    @include('user.layouts.partials.sidebar')
    <div id="sidebar-overlay" class="fixed inset-0 bg-slate-950/40 backdrop-blur-xs z-40 hidden xl:hidden" onclick="toggleSidebar()"></div>

    <!-- Konten Utama + Header + Footer -->
    <div class="flex-1 flex flex-col min-w-0 min-h-screen overflow-y-auto pb-16 md:pb-0">
        <!-- Top Navbar / Header -->
        @include('user.layouts.partials.header')

        <!-- Main Body -->
        <main class="flex-1 p-4 sm:p-8 max-w-7xl w-full mx-auto">
            @yield('content')
        </main>

        <!-- Footer -->
        @include('user.layouts.partials.footer')
    </div>

    <!-- Mobile Bottom Navigation Bar -->
    <div class="fixed bottom-0 inset-x-0 bg-white border-t border-slate-100 z-40 flex items-center justify-around py-2.5 px-4 md:hidden shadow-[0_-4px_24px_rgba(0,0,0,0.06)] rounded-t-2xl">
        <!-- Home / Dashboard -->
        <a href="{{ route('user.dashboard') }}" class="flex flex-col items-center justify-center gap-0.5 {{ Request::routeIs('user.dashboard') ? 'text-indigo-600 font-bold' : 'text-slate-400 hover:text-indigo-600' }} transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span class="text-[9px] tracking-wide">Home</span>
        </a>

        <!-- Company Workspace -->
        @if($activeCompany)
            <a href="{{ route('user.company.workspace', $activeCompany->id) }}" class="flex flex-col items-center justify-center gap-0.5 {{ Request::routeIs('user.company.workspace') ? 'text-indigo-600 font-bold' : 'text-slate-400 hover:text-indigo-600' }} transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <span class="text-[9px] tracking-wide">Workspace</span>
            </a>
        @endif

        <!-- Profile -->
        <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center gap-0.5 {{ Request::routeIs('profile.edit') ? 'text-indigo-600 font-bold' : 'text-slate-400 hover:text-indigo-600' }} transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            <span class="text-[9px] tracking-wide">Profile</span>
        </a>

        <!-- Hamburger / Menu -->
        <button type="button" onclick="toggleSidebar()" class="flex flex-col items-center justify-center gap-0.5 text-slate-400 hover:text-indigo-600 transition-colors">
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
    @stack('scripts')
</body>
</html>