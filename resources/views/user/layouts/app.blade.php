@php
    $user = Auth::user();
    $myProjects = \App\Models\Project::where(function ($query) use ($user) {
        $query->whereJsonContains('team_matrix', ['user_id' => (string) $user->id])
            ->orWhereJsonContains('team_matrix', ['user_id' => (int) $user->id])
            ->orWhere('team_matrix', 'like', '%"user_id":' . $user->id . '%')
            ->orWhere('team_matrix', 'like', '%"user_id":"' . $user->id . '"%')
            ->orWhere('created_by', $user->id);
    })
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
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Yoimo">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/icons/favicon-16x16.png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-50/50 flex">

    <!-- Sidebar Desktop & Mobile Drawer -->
    @include('user.layouts.partials.sidebar')
    <div id="sidebar-overlay" class="fixed inset-0 bg-slate-950/40 backdrop-blur-xs z-40 hidden xl:hidden" onclick="toggleSidebar()"></div>

    <!-- Konten Utama + Header + Footer -->
    <div class="flex-1 flex flex-col min-w-0 min-h-screen overflow-y-auto pb-20 xl:pb-0">
        <!-- Top Navbar / Header -->
        @include('user.layouts.partials.header')

        <!-- Main Body -->
        <main class="flex-1 p-4 sm:p-8 max-w-7xl w-full mx-auto">
            @yield('content')
        </main>

        <!-- Footer -->
        @include('user.layouts.partials.footer')
    </div>

    <!-- Mobile & iPad Bottom Navigation Bar (xl:hidden) -->
    @include('layouts.partials.mobile_bottom_nav')

    <!-- PWA Engine & Keep-Alive -->
    @include('layouts.partials.pwa_support')

    <!-- Toggling Scripts -->
    <script>

        // Mobile Sidebar Drawer Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('mobile-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            if (sidebar && overlay) {
                const isOpening = sidebar.classList.contains('hidden');
                sidebar.classList.toggle('hidden');
                overlay.classList.toggle('hidden');
                if (window.innerWidth < 1280) {
                    document.body.classList.toggle('overflow-hidden', isOpening);
                }
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