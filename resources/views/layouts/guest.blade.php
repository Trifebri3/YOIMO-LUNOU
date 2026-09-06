<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Yoimo Workspace') }} - Platform Kolaborasi & Produktivitas Cerdas</title>
        <meta name="description" content="Yoimo Workspace adalah platform modern untuk kolaborasi tim, manajemen proyek, pelacakan linimasa, evaluasi pencapaian kerja, dan pemeliharaan ritme produktivitas kerja yang seimbang.">
        
        <!-- Open Graph / LinkedIn -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="Yoimo Workspace - Platform Kolaborasi & Produktivitas Cerdas">
        <meta property="og:description" content="Kelola proyek, pantau tugas, dan capai target tim dengan mudah dan transparan bersama ekosistem cerdas Yoimo Workspace.">
        <meta property="og:image" content="{{ asset('images/yoimo-og-banner.png') }}">
        <meta property="og:image:secure_url" content="{{ asset('images/yoimo-og-banner.png') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:site_name" content="Yoimo Workspace">

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="Yoimo Workspace - Platform Kolaborasi & Produktivitas Cerdas">
        <meta name="twitter:description" content="Kelola proyek, pantau tugas, dan capai target tim dengan mudah dan transparan bersama ekosistem cerdas Yoimo Workspace.">
        <meta name="twitter:image" content="{{ asset('images/yoimo-og-banner.png') }}">

        <!-- PWA Manifest & Meta -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#4f46e5">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Yoimo">
        <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/icons/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/icons/favicon-16x16.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>

        <!-- PWA Engine & Keep-Alive -->
        @include('layouts.partials.pwa_support')
    </body>
</html>
