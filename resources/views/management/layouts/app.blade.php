@php
    $assignedCompanies = \App\Models\CompanyProfile::where('manager_id', Auth::id())->get();
    $activeCompany = isset($company) 
        ? $company 
        : (isset($project) && $project->company ? $project->company : $assignedCompanies->first());
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50/50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel')) - Management Portal</title>

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

    <!-- Sidebar Management Drawer -->
    @include('management.layouts.partials.sidebar')
    <div id="sidebar-overlay" class="fixed inset-0 bg-slate-950/40 backdrop-blur-xs z-40 hidden xl:hidden" onclick="toggleSidebar()"></div>

    <!-- Konten Utama -->
    <div class="flex-1 flex flex-col min-w-0 pb-20 xl:pb-0">
        <!-- Header Management -->
        @include('management.layouts.partials.header')

        <!-- Dynamic Body Page Content -->
        <main class="flex-1 p-4 sm:p-8">
            @yield('content')
        </main>

        <!-- Footer Management -->
        @include('management.layouts.partials.footer')
    </div>

    <!-- Mobile & iPad Bottom Navigation Bar (xl:hidden) -->
    @include('layouts.partials.mobile_bottom_nav')

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
    @include('layouts.partials.linkedin_share_modal')
    @include('layouts.partials.quick_nav_modal')
    @stack('scripts')
</body>
</html>
