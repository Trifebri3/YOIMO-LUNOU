<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portofolio Profesional: {{ $user->name }} | Yoimo Workspace - YOTA FAMILY</title>
    <meta name="description" content="Eksplorasi portofolio profesional, rekam jejak tugas terverifikasi, proyek, dan lencana resmi {{ $user->name }} di Yoimo Workspace (Ekosistem Utama YOTA FAMILY).">

    <!-- Open Graph / LinkedIn / WhatsApp -->
    @php
        $portfolioUrl = route('public.portfolio.show', $user->slug ?: $user->id);
        $userRoleTitle = $user->position ?: ($user->role === 'management' ? 'Project Manager' : ($user->role === 'finance' ? 'Finance Specialist' : 'Professional Specialist'));
        $companiesList = $user->allCompanies()->pluck('company_name')->take(2)->join(', ');
        $companySuffix = $companiesList ? " di {$companiesList}" : '';
        $ogDescription = "Portofolio kerja resmi {$user->name} ({$userRoleTitle}{$companySuffix}) di Yoimo Workspace • Ekosistem Utama YOTA FAMILY. Level {$stats['level']} • {$stats['total_xp']} XP • {$stats['completed_tasks_count']} Tugas Terverifikasi • {$stats['ontime_rate']}% Tepat Waktu.";
    @endphp
    <meta property="og:type" content="profile">
    <meta property="og:url" content="{{ $portfolioUrl }}">
    <meta property="og:title" content="Portofolio & Rekam Jejak Kerja: {{ $user->name }} | Yoimo Workspace • YOTA FAMILY">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:image" content="{{ asset('icon/11.png') }}">
    <meta property="og:site_name" content="Yoimo Workspace - YOTA FAMILY">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $portfolioUrl }}">
    <meta name="twitter:title" content="Portofolio & Rekam Jejak Kerja: {{ $user->name }} | Yoimo Workspace • YOTA FAMILY">
    <meta name="twitter:description" content="{{ $ogDescription }}">
    <meta name="twitter:image" content="{{ asset('icon/11.png') }}">

    <!-- Favicon & PWA Icons -->
    <link rel="icon" type="image/png" href="{{ asset('icons/icon-96x96.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }
        .white-card {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .white-card-hover:hover {
            transform: translateY(-2px);
            border-color: rgba(99, 102, 241, 0.4);
            box-shadow: 0 14px 30px -6px rgba(99, 102, 241, 0.12);
        }
    </style>
</head>
<body class="min-h-screen selection:bg-indigo-500 selection:text-white relative overflow-x-hidden bg-slate-50 font-sans">

    <!-- Top Ecosystem Identification Bar -->
    <div class="bg-gradient-to-r from-slate-950 via-indigo-950 to-slate-900 text-white text-[11px] py-2 px-4 shadow-sm">
        <div class="max-w-6xl mx-auto flex items-center justify-between gap-4">
            <div class="flex items-center gap-2 truncate">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                <span class="font-black uppercase tracking-widest text-indigo-200">EKOSISTEM UTAMA YOTA FAMILY</span>
                <span class="text-slate-400 hidden sm:inline">•</span>
                <span class="text-slate-300 hidden sm:inline truncate">Platform Kolaborasi Kinerja & Portofolio Tertutup PT YOTA INOVASI NUSANTARA</span>
            </div>
            <span class="text-[10px] font-mono text-indigo-300 bg-white/10 px-2 py-0.5 rounded-full shrink-0">Official Verified</span>
        </div>
    </div>

    <!-- Ambient Subtle Pastel Glows -->
    <div class="fixed top-12 left-1/4 w-96 h-96 bg-indigo-100/40 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed top-1/3 right-10 w-96 h-96 bg-blue-100/30 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed bottom-10 left-10 w-80 h-80 bg-emerald-100/30 rounded-full blur-3xl pointer-events-none -z-10"></div>

    <!-- Navigation Header (Clean White Luxury) -->
    <header class="border-b border-slate-200/80 bg-white/95 sticky top-0 z-40 backdrop-blur-md shadow-xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                    <img src="{{ asset('icon/11.png') }}" alt="Yoimo" class="w-8 h-8 rounded-xl object-contain group-hover:scale-105 transition-transform shadow-2xs">
                    <div class="leading-tight">
                        <span class="font-black text-lg text-slate-900 tracking-tight block">YOIMO<span class="text-indigo-600 text-xs ml-1 font-bold">PORTFOLIO</span></span>
                    </div>
                </a>
                <span class="hidden md:inline-flex items-center gap-1 text-[10px] font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200/70">
                    YOTA FAMILY
                </span>
            </div>

            <div class="flex items-center gap-2.5 sm:gap-3">
                <button type="button" onclick="shareToLinkedIn()" class="inline-flex items-center gap-2 px-3.5 py-2 bg-[#0a66c2] hover:bg-[#004182] text-white text-xs font-bold rounded-xl transition shadow-xs">
                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>
                    <span class="hidden sm:inline">Bagikan ke LinkedIn</span>
                    <span class="sm:hidden">Share</span>
                </button>

                <button type="button" onclick="copyPortfolioLink()" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl transition border border-slate-200 shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <span class="hidden sm:inline">Salin Link</span>
                </button>

                @auth
                    <a href="{{ route('dashboard') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 ml-2">Dashboard &rarr;</a>
                @else
                    <a href="{{ route('login') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 ml-2">Masuk</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-12 space-y-8 sm:space-y-12">

        <!-- 1. Hero Profile Card (Crisp All-White Luxury) -->
        <section class="white-card rounded-3xl p-6 sm:p-10 border border-slate-200/90 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] relative overflow-hidden">
            <!-- Subtle accent top strip -->
            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-indigo-500 via-purple-500 to-emerald-500"></div>

            <div class="flex flex-col md:flex-row items-center md:items-start gap-6 sm:gap-10 pt-2">
                <!-- Avatar & Level Ring -->
                <div class="relative shrink-0">
                    <div class="w-28 h-28 sm:w-36 sm:h-36 rounded-3xl overflow-hidden border-4 border-slate-100 shadow-xl bg-slate-100 relative">
                        @if(!empty($user->avatar))
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover rounded-2xl">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-tr from-indigo-600 via-indigo-700 to-purple-600 text-white text-3xl sm:text-4xl font-black rounded-2xl">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                        @endif
                    </div>
                    <div class="absolute -bottom-2 -right-2 px-3 py-1 bg-gradient-to-r from-amber-400 to-amber-500 text-slate-950 font-black text-xs rounded-full shadow-md flex items-center gap-1.5 border border-white">
                        <svg class="w-3.5 h-3.5 fill-current text-slate-950 shrink-0" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <span>LVL {{ $stats['level'] }}</span>
                    </div>
                </div>

                <!-- User Identity & Ecosystem Details -->
                <div class="flex-1 text-center md:text-left space-y-4">
                    <div class="space-y-1.5">
                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-2.5">
                            <h1 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight">{{ $user->name }}</h1>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-2xs">
                                <svg class="w-3.5 h-3.5 text-emerald-600 fill-current shrink-0" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>Verified Contributor • Ekosistem Utama YOTA FAMILY</span>
                            </span>
                        </div>

                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-2">
                            <p class="text-indigo-600 font-bold text-sm sm:text-base">
                                {{ $userRoleTitle }}
                            </p>
                            <span class="text-slate-300 hidden sm:inline">•</span>
                            <span class="text-slate-500 text-xs sm:text-sm font-medium">Yoimo Workspace Professional</span>
                        </div>
                    </div>

                    <p class="text-slate-600 text-xs sm:text-sm leading-relaxed max-w-2xl font-normal">
                        {{ $user->bio ?: 'Profesional berdedikasi dalam ekosistem utama YOTA FAMILY yang aktif berkontribusi menyelesaikan penugasan nyata, memimpin proyek kolaboratif, dan menjaga standar kualitas kerja tinggi.' }}
                    </p>

                    <!-- LOGO-LOGO PERUSAHAAN TERAFILIASI DI EKOSISTEM YOTA FAMILY -->
                    @php
                        $allUserCompanies = $user->allCompanies();
                    @endphp
                    @if($allUserCompanies->isNotEmpty())
                        <div class="pt-3 border-t border-slate-100">
                            <span class="text-[11px] font-black uppercase text-slate-400 tracking-wider block mb-2 text-center md:text-left">
                                ENTITAS BISNIS & PERUSAHAAN TERAFILIASI (YOTA FAMILY):
                            </span>
                            <div class="flex flex-wrap items-center justify-center md:justify-start gap-2.5">
                                @foreach($allUserCompanies as $comp)
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-slate-200/90 rounded-2xl text-xs font-semibold text-slate-800 shadow-2xs hover:border-indigo-300 transition-colors">
                                        @if(!empty($comp->logo))
                                            <img src="{{ asset('storage/' . $comp->logo) }}" alt="{{ $comp->company_name }}" class="w-5 h-5 rounded-md object-contain p-0.5 bg-white border border-slate-100 shrink-0">
                                        @else
                                            <div class="w-5 h-5 rounded-md bg-indigo-50 text-indigo-700 font-black text-[10px] flex items-center justify-center shrink-0 border border-indigo-200/50">
                                                {{ strtoupper(substr($comp->company_name, 0, 2)) }}
                                            </div>
                                        @endif
                                        <span class="font-bold text-slate-900">{{ $comp->company_name }}</span>
                                        <span class="text-[9px] font-black px-1.5 py-0.2 rounded bg-indigo-50 text-indigo-700">YOTA</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="pt-2 flex flex-wrap items-center justify-center md:justify-start gap-3">
                        <button type="button" onclick="shareToLinkedIn()" class="px-5 py-2.5 bg-[#0a66c2] hover:bg-[#004182] text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>
                            <span>Bagikan ke LinkedIn</span>
                        </button>

                        <button type="button" onclick="shareToWhatsApp()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2m.01 1.67c4.56 0 8.27 3.71 8.27 8.24 0 2.2-.86 4.28-2.42 5.84l-.59.59-3.25.86.86-3.25.59-.59c1.56-1.56 2.42-3.64 2.42-5.84 0-4.53-3.71-8.24-8.27-8.24-4.54 0-8.24 3.7-8.24 8.24 0 1.58.45 3.12 1.31 4.46l.29.45-.73 2.66 2.72-.71.43.27c1.3.82 2.81 1.25 4.35 1.25z"/></svg>
                            <span>WhatsApp</span>
                        </button>

                        <button type="button" onclick="copyPortfolioLink()" class="px-5 py-2.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl transition border border-slate-200 shadow-2xs flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <span>Salin Tautan</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. Credibility & Gamification Stats Grid (All-White) -->
        <section class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Total XP & Level -->
            <div class="white-card white-card-hover rounded-2xl p-5 sm:p-6 space-y-3">
                <div class="flex items-center justify-between text-xs text-slate-500 font-bold uppercase tracking-wider">
                    <span>Poin Reputasi</span>
                    <span class="text-amber-600 font-black">Level {{ $stats['level'] }}</span>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900">
                    {{ number_format($stats['total_xp']) }} <span class="text-xs font-semibold text-slate-400">XP</span>
                </div>
                @php
                    $currentLevelXp = $stats['total_xp'] % 200;
                    $progressPct = min(100, round(($currentLevelXp / 200) * 100));
                @endphp
                <div class="space-y-1">
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-gradient-to-r from-amber-400 to-amber-500 h-2 rounded-full transition-all duration-500" style="width: {{ $progressPct }}%"></div>
                    </div>
                    <div class="flex justify-between text-[10px] text-slate-400 font-mono">
                        <span>{{ $currentLevelXp }}/200 XP</span>
                        <span>Level {{ $stats['level'] + 1 }}</span>
                    </div>
                </div>
            </div>

            <!-- Proyek yang Diikuti -->
            <div class="white-card white-card-hover rounded-2xl p-5 sm:p-6 space-y-3">
                <div class="flex items-center justify-between text-xs text-slate-500 font-bold uppercase tracking-wider">
                    <span>Proyek Kolaboratif</span>
                    <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600 fill-current" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/></svg>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900">
                    {{ $stats['projects_count'] }} <span class="text-xs font-semibold text-slate-400">Proyek</span>
                </div>
                <p class="text-[11px] text-slate-500">
                    Terdaftar aktif dalam ekosistem proyek YOTA FAMILY
                </p>
            </div>

            <!-- Verified Tasks Completed -->
            <div class="white-card white-card-hover rounded-2xl p-5 sm:p-6 space-y-3">
                <div class="flex items-center justify-between text-xs text-slate-500 font-bold uppercase tracking-wider">
                    <span>Tugas Tuntas</span>
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-600 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900">
                    {{ $stats['completed_tasks_count'] }} <span class="text-xs font-semibold text-slate-400">Tugas</span>
                </div>
                <p class="text-[11px] text-emerald-600 font-semibold">
                    100% tuntas terverifikasi manajemen
                </p>
            </div>

            <!-- On-Time Completion Rate -->
            <div class="white-card white-card-hover rounded-2xl p-5 sm:p-6 space-y-3">
                <div class="flex items-center justify-between text-xs text-slate-500 font-bold uppercase tracking-wider">
                    <span>Ketepatan Waktu</span>
                    <div class="w-7 h-7 rounded-lg bg-sky-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-sky-600 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900">
                    {{ $stats['ontime_rate'] }}%
                </div>
                <div class="flex items-center gap-1.5 text-[11px] text-slate-500">
                    <span class="text-amber-600 font-bold">{{ $stats['ontime_count'] }}x</span> tepat tenggat waktu
                </div>
            </div>
        </section>

        <!-- 3. Badges & Awards Showcase (All-White) -->
        @if($user->awards->isNotEmpty())
            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center shrink-0 border border-amber-200/60">
                                <svg class="w-4 h-4 text-amber-600 fill-current" viewBox="0 0 24 24"><path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94A5.01 5.01 0 0011 15.9V19H7v2h10v-2h-4v-3.1c1.8-.44 3.2-1.85 3.61-3.96C19.08 11.63 21 9.55 21 7V5c0-1.1-.9-2-2-2zm-14 3V7h2v3.82C5.84 10.4 5 9.3 5 8zm14 0c0 1.3-.84 2.4-2 2.82V7h2v1z"/></svg>
                            </div>
                            <span>Lencana & Penghargaan Resmi</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Pengakuan prestasi kinerja di bawah naungan ekosistem Yoimo & YOTA FAMILY</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($user->awards as $award)
                        <div class="white-card white-card-hover rounded-2xl p-5 border border-slate-200/90 relative overflow-hidden flex flex-col justify-between">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="px-2.5 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-black rounded-lg uppercase tracking-wider border border-amber-200">
                                        Badge Terbit
                                    </span>
                                    <span class="text-[11px] text-slate-400 font-mono">{{ $award->issued_date->format('d M Y') }}</span>
                                </div>
                                <div>
                                    <h3 class="text-base font-black text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $award->title }}</h3>
                                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                        @if($award->award_type === 'si_paling_tepat_waktu')
                                            Menuntaskan tugas secara konsisten tepat waktu atau sebelum deadline.
                                        @elseif($award->award_type === 'si_paling_produktif')
                                            Mengumpulkan lebih dari 500 XP produktivitas kerja berkualitas.
                                        @elseif($award->award_type === 'si_paling_rajin_login')
                                            Menjaga login streak 5 hari berturut-turut dalam ekosistem kerja.
                                        @elseif($award->award_type === 'master_tugas')
                                            Menyelesaikan minimal 5 tugas bernilai strategis tinggi.
                                        @elseif($award->award_type === 'kolaborator_handal')
                                            Berkontribusi aktif dalam berbagai proyek lintas divisi.
                                        @else
                                            Dedikasi dan kinerja luar biasa dalam lingkungan kolaborasi.
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="pt-4 mt-2 border-t border-slate-100 flex items-center justify-between">
                                <a href="{{ route('public.award.show', $award->share_token) }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                                    <span>Lihat Sertifikat Resmi</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                                <span class="text-[10px] text-slate-400 font-mono">#{{ substr($award->share_token, 0, 6) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- 4. Projects Showcase (All-White with Company Logos) -->
        <section class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0 border border-indigo-200/60">
                            <svg class="w-4 h-4 text-indigo-600 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/><path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"/></svg>
                        </div>
                        <span>Portofolio Proyek yang Diikuti</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar proyek riil tempat {{ $user->name }} berpartisipasi dan berkontribusi secara nyata di ekosistem YOTA FAMILY</p>
                </div>
                <span class="text-xs font-bold px-3 py-1 bg-white rounded-xl text-slate-700 self-start sm:self-auto border border-slate-200 shadow-2xs">
                    Total {{ $projects->count() }} Proyek
                </span>
            </div>

            @if($projects->isEmpty())
                <div class="white-card rounded-2xl p-10 text-center space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto text-slate-400">
                        <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Belum Ada Proyek Publik</h3>
                    <p class="text-xs text-slate-500 max-w-md mx-auto">Pengguna ini sedang mempersiapkan keterlibatan dalam proyek baru di Yoimo Workspace.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                    @foreach($projects as $project)
                        @php
                            $matrix = collect($project->team_matrix ?? []);
                            $userRow = $matrix->first(function($item) use ($user) {
                                return isset($item['user_id']) && (string)$item['user_id'] === (string)$user->id;
                            });
                            $roleInProject = $userRow['role'] ?? ($project->created_by === $user->id ? 'Lead / Creator' : 'Team Contributor');
                            $userTasksCount = $project->tasks->where('assigned_to', $user->id)->count();
                            $userCompletedTasksCount = $project->tasks->where('assigned_to', $user->id)->where('status', 'Completed')->count();
                        @endphp
                        <div class="white-card white-card-hover rounded-2xl p-6 space-y-4 border border-slate-200/90 flex flex-col justify-between">
                            <div class="space-y-3.5">
                                <!-- Badges row with Company Logo -->
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                                            {{ $project->category ?: 'Project' }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $project->status === 'Completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-sky-50 text-sky-700 border border-sky-200' }}">
                                            {{ $project->status }}
                                        </span>
                                    </div>
                                    @if($project->company)
                                        <div class="flex items-center gap-1.5 text-[11px] font-bold text-slate-700 bg-slate-50 px-2.5 py-1 rounded-xl border border-slate-200/70">
                                            @if(!empty($project->company->logo))
                                                <img src="{{ asset('storage/' . $project->company->logo) }}" alt="{{ $project->company->company_name }}" class="w-4 h-4 rounded-md object-contain bg-white shrink-0">
                                            @endif
                                            <span>{{ $project->company->company_name }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Project Title & Client -->
                                <div>
                                    <h3 class="text-lg font-black text-slate-900">{{ $project->name }}</h3>
                                    @if($project->client_name)
                                        <p class="text-xs text-indigo-600 font-semibold mt-0.5">Klien / Inisiator: {{ $project->client_name }}</p>
                                    @endif
                                </div>

                                <!-- Problem statement / brief -->
                                <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed">
                                    {{ $project->problem_statement ?: ($project->description ?: 'Proyek kolaboratif strategis yang dikerjakan bersama tim profesional di bawah naungan ekosistem Yoimo & YOTA FAMILY.') }}
                                </p>

                                <!-- Role & Contributions -->
                                <div class="pt-1 flex flex-wrap items-center gap-2 text-xs">
                                    <span class="px-3 py-1 bg-slate-100 rounded-xl text-slate-700 font-semibold border border-slate-200">
                                        Peran: <strong class="text-slate-900">{{ $roleInProject }}</strong>
                                    </span>
                                    @if($userTasksCount > 0)
                                        <span class="px-3 py-1 bg-indigo-50 rounded-xl text-indigo-700 font-semibold border border-indigo-200/70">
                                            {{ $userCompletedTasksCount }}/{{ $userTasksCount }} Tugas Tuntas
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="pt-3 border-t border-slate-100 space-y-1.5">
                                <div class="flex justify-between text-[11px] text-slate-500">
                                    <span>Progres Proyek</span>
                                    <span class="font-mono text-slate-900 font-bold">{{ $project->progress_percentage }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-gradient-to-r from-indigo-600 to-emerald-500 h-1.5 rounded-full" style="width: {{ $project->progress_percentage }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <!-- 5. Verified Completed Tasks (Deliverables) -->
        @if($completedTasks->isNotEmpty())
            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0 border border-emerald-200/60">
                                <svg class="w-4 h-4 text-emerald-600 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            </div>
                            <span>Kontribusi Tugas Terverifikasi</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar penugasan nyata yang telah diselesaikan 100% dan diverifikasi resmi oleh manajemen</p>
                    </div>
                </div>

                <div class="white-card rounded-2xl divide-y divide-slate-100 overflow-hidden border border-slate-200/90 shadow-xs">
                    @foreach($completedTasks as $task)
                        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/80 transition-colors">
                            <div class="space-y-1.5 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $task->priority === 'Urgent' ? 'bg-rose-50 text-rose-700 border border-rose-200' : ($task->priority === 'High' ? 'bg-orange-50 text-orange-700 border border-orange-200' : 'bg-slate-100 text-slate-700') }}">
                                        {{ $task->priority }}
                                    </span>
                                    @if($task->project)
                                        <div class="inline-flex items-center gap-1.5 text-xs text-indigo-700 font-bold bg-indigo-50/80 px-2.5 py-0.5 rounded-lg border border-indigo-200/60">
                                            @if($task->project->company && !empty($task->project->company->logo))
                                                <img src="{{ asset('storage/' . $task->project->company->logo) }}" alt="{{ $task->project->company->company_name }}" class="w-3.5 h-3.5 rounded object-contain bg-white">
                                            @endif
                                            <span>{{ $task->project->name }}</span>
                                        </div>
                                    @endif
                                </div>
                                <h3 class="text-sm font-bold text-slate-900">{{ $task->title }}</h3>
                                @if($task->description)
                                    <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">{{ strip_tags($task->description) }}</p>
                                @endif
                            </div>

                            <div class="flex sm:flex-col items-center sm:items-end justify-between shrink-0 gap-1.5 text-xs">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-lg border border-emerald-200 text-[11px]">
                                    <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    Tuntas (100%)
                                </span>
                                @if($task->submitted_at)
                                    <span class="text-[10px] text-slate-400 font-mono">
                                        {{ $task->submitted_at->format('d M Y') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- 6. Gamification Tracing Activity Logs (All-White) -->
        @if($recentLogs->isNotEmpty())
            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center shrink-0 border border-amber-200/60">
                                <svg class="w-4 h-4 text-amber-600 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                            </div>
                            <span>Rekam Jejak Gamifikasi & Tracing Poin</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Audit log poin reputasi transparan atas aktivitas kinerja di platform Yoimo Workspace</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($recentLogs as $log)
                        <div class="white-card rounded-xl p-3.5 flex items-center justify-between gap-3 text-xs border border-slate-200/80 shadow-2xs">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 border border-indigo-200/50">
                                    @if($log->source_type === 'task_completed')
                                        <svg class="w-4 h-4 text-indigo-600 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    @elseif($log->source_type === 'task_ontime')
                                        <svg class="w-4 h-4 text-sky-600 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                                    @elseif($log->source_type === 'daily_login')
                                        <svg class="w-4 h-4 text-amber-600 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.527.82-1.144 2.08-1.444 3.07-.46 1.517-.679 2.766-.679 3.882 0 2.21 1.79 4 4 4s4-1.79 4-4c0-1.89-1.05-3.66-2.055-5.212a20.086 20.086 0 00-1.6-2.235zM7.95 7.424A9.97 9.97 0 006 12c0 3.314 2.686 6 6 6s6-2.686 6-6a9.97 9.97 0 00-1.95-4.576A11.958 11.958 0 0112 11c-1.657 0-3-1.343-3-3 0-.196.019-.387.05-.576z" clip-rule="evenodd"/></svg>
                                    @else
                                        <svg class="w-4 h-4 text-purple-600 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/></svg>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800">{{ $log->description ?: ucfirst(str_replace('_', ' ', $log->source_type)) }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <span class="font-black {{ $log->points >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-mono shrink-0">
                                {{ $log->points >= 0 ? '+' . $log->points : $log->points }} XP
                            </span>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

    </main>

    <!-- Footer (Crisp All-White with YOTA FAMILY & Yoimo Branding) -->
    <footer class="border-t border-slate-200/90 bg-white py-12 text-center text-xs text-slate-500 space-y-4 mt-12">
        <div class="flex items-center justify-center gap-3">
            <img src="{{ asset('icon/11.png') }}" alt="Yoimo" class="w-6 h-6 rounded-lg object-contain">
            <span class="font-black text-slate-900 tracking-wider text-sm">YOIMO WORKSPACE</span>
            <span class="text-slate-300">•</span>
            <span class="font-bold text-indigo-700 uppercase tracking-widest text-[11px]">YOTA FAMILY</span>
        </div>
        <p class="max-w-xl mx-auto leading-relaxed text-slate-500 px-4">
            Platform Kolaborasi Kerja, Pelacakan Kinerja & Portofolio Digital Terverifikasi Khusus Para Talenta di Bawah Naungan Ekosistem Utama <strong>YOTA FAMILY</strong> (PT YOTA INOVASI NUSANTARA).
        </p>
        <p class="text-[11px] text-slate-400">&copy; {{ date('Y') }} PT YOTA INOVASI NUSANTARA. Seluruh hak cipta dilindungi.</p>
    </footer>

    <!-- JavaScript Helpers for Sharing & Copying -->
    <script>
        function copyPortfolioLink() {
            const url = "{{ $portfolioUrl }}";
            navigator.clipboard.writeText(url).then(() => {
                alert('Tautan portofolio resmi Anda di ekosistem YOTA FAMILY berhasil disalin ke clipboard!');
            }).catch(() => {
                const el = document.createElement('textarea');
                el.value = url;
                document.body.appendChild(el);
                el.select();
                document.execCommand('copy');
                document.body.removeChild(el);
                alert('Tautan portofolio resmi Anda di ekosistem YOTA FAMILY berhasil disalin!');
            });
        }

        function shareToLinkedIn() {
            const url = "{{ $portfolioUrl }}";
            const caption = `Senang dapat membagikan portofolio profesional dan rekam jejak kerja terverifikasi saya di Yoimo Workspace (Ekosistem Utama YOTA FAMILY)!\n\nNama: {{ $user->name }}\nPeran: {{ $userRoleTitle }}\nLevel Reputasi: Level {{ $stats['level'] }} ({{ $stats['total_xp'] }} XP)\nKontribusi: {{ $stats['completed_tasks_count'] }} Tugas Tuntas • {{ $stats['ontime_rate'] }}% Ketepatan Waktu\n\nCek portofolio dan proyek yang saya ikuti di tautan berikut:\n${url}\n\n#YotaFamily #YoimoWorkspace #ProjectManagement #ProfessionalGrowth #TechTalent #Collaboration`;
            
            navigator.clipboard.writeText(caption).then(() => {
                const linkedInUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`;
                window.open(linkedInUrl, '_blank', 'width=600,height=600');
            }).catch(() => {
                const linkedInUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`;
                window.open(linkedInUrl, '_blank', 'width=600,height=600');
            });
        }

        function shareToWhatsApp() {
            const url = "{{ $portfolioUrl }}";
            const text = `Halo! Lihat portofolio profesional terverifikasi {{ $user->name }} di Yoimo Workspace (Ekosistem Utama YOTA FAMILY):\n${url}`;
            window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(text)}`, '_blank');
        }
    </script>
</body>
</html>
