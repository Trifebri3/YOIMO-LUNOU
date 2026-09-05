<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portofolio Profesional: {{ $user->name }} | Yoimo Workspace</title>
    <meta name="description" content="Eksplorasi portofolio profesional, proyek yang diikuti, tugas terverifikasi, dan pencapaian lencana resmi {{ $user->name }} di Yoimo Workspace.">

    <!-- Open Graph / LinkedIn / WhatsApp -->
    @php
        $portfolioUrl = route('public.portfolio.show', $user->id);
        $userRoleTitle = $user->position ?: ($user->role === 'management' ? 'Project Manager' : ($user->role === 'finance' ? 'Finance Specialist' : 'Professional Specialist'));
        $companiesList = $user->allCompanies()->pluck('company_name')->take(2)->join(', ');
        $companySuffix = $companiesList ? " di {$companiesList}" : '';
        $ogDescription = "Portofolio kerja resmi {$user->name} ({$userRoleTitle}{$companySuffix}) di Yoimo Workspace. Level {$stats['level']} • {$stats['total_xp']} XP • {$stats['completed_tasks_count']} Tugas Terverifikasi • {$stats['ontime_rate']}% Tepat Waktu.";
    @endphp
    <meta property="og:type" content="profile">
    <meta property="og:url" content="{{ $portfolioUrl }}">
    <meta property="og:title" content="Portofolio & Rekam Jejak Kerja: {{ $user->name }} (Level {{ $stats['level'] }}) | Yoimo Workspace">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:image" content="{{ asset('icon/11.png') }}">
    <meta property="og:site_name" content="Yoimo Workspace">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $portfolioUrl }}">
    <meta name="twitter:title" content="Portofolio & Rekam Jejak Kerja: {{ $user->name }} | Yoimo Workspace">
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
            darkMode: 'class',
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
            background-color: #0b0f19;
            color: #f1f5f9;
        }
        .glass-card {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glass-card-hover {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card-hover:hover {
            transform: translateY(-2px);
            border-color: rgba(99, 102, 241, 0.35);
            box-shadow: 0 12px 30px -10px rgba(79, 70, 229, 0.25);
        }
        .gradient-border {
            position: relative;
            border-radius: 1.5rem;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.5), rgba(168, 85, 247, 0.2), rgba(16, 185, 129, 0.4));
            padding: 1px;
        }
    </style>
</head>
<body class="min-h-screen selection:bg-indigo-500 selection:text-white relative overflow-x-hidden">

    <!-- Ambient Gradient Background Lights -->
    <div class="fixed top-0 left-1/4 w-96 h-96 bg-indigo-600/10 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed top-1/3 right-10 w-96 h-96 bg-purple-600/10 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed bottom-10 left-10 w-80 h-80 bg-emerald-600/10 rounded-full blur-3xl pointer-events-none -z-10"></div>

    <!-- Navigation Header -->
    <header class="border-b border-white/5 bg-slate-950/80 sticky top-0 z-40 backdrop-blur-md">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                <img src="{{ asset('icon/11.png') }}" alt="Yoimo" class="w-8 h-8 rounded-lg object-contain group-hover:scale-105 transition-transform">
                <span class="font-extrabold text-lg text-white tracking-tight">YOIMO<span class="text-indigo-400 text-xs ml-1 font-semibold">PORTFOLIO</span></span>
            </a>

            <div class="flex items-center gap-3">
                <button type="button" onclick="shareToLinkedIn()" class="hidden sm:inline-flex items-center gap-2 px-3.5 py-2 bg-[#0a66c2] hover:bg-[#004182] text-white text-xs font-bold rounded-xl transition shadow-sm">
                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>
                    <span>Bagikan Profil</span>
                </button>

                <button type="button" onclick="copyPortfolioLink()" class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl transition border border-white/10">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <span>Salin Tautan</span>
                </button>

                @auth
                    <a href="{{ route('dashboard') }}" class="text-xs font-bold text-indigo-400 hover:text-indigo-300 ml-2">Dashboard &rarr;</a>
                @else
                    <a href="{{ route('login') }}" class="text-xs font-bold text-slate-400 hover:text-white ml-2">Masuk</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-12 space-y-8 sm:space-y-12">

        <!-- 1. Hero Profile Card -->
        <section class="gradient-border shadow-2xl">
            <div class="bg-slate-900/95 rounded-[1.45rem] p-6 sm:p-10 relative overflow-hidden backdrop-blur-xl">
                <div class="flex flex-col md:flex-row items-center md:items-start gap-6 sm:gap-8">
                    <!-- Avatar & Level Ring -->
                    <div class="relative shrink-0">
                        <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-3xl overflow-hidden border-2 border-indigo-500/50 p-1 bg-slate-800 shadow-xl relative">
                            @if(!empty($user->avatar))
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover rounded-2xl">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-tr from-indigo-700 to-purple-600 text-white text-3xl sm:text-4xl font-black rounded-2xl">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                            @endif
                        </div>
                        <div class="absolute -bottom-2 -right-2 px-3 py-1 bg-gradient-to-r from-amber-500 to-orange-500 text-slate-950 font-black text-xs rounded-full shadow-lg flex items-center gap-1 border border-white/20">
                            <span>⭐</span>
                            <span>LVL {{ $stats['level'] }}</span>
                        </div>
                    </div>

                    <!-- User Identity & Bios -->
                    <div class="flex-1 text-center md:text-left space-y-3">
                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-2.5">
                            <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight">{{ $user->name }}</h1>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Verified Contributor
                            </span>
                        </div>

                        <p class="text-indigo-300 font-semibold text-sm sm:text-base">
                            {{ $userRoleTitle }}
                        </p>

                        <p class="text-slate-300 text-xs sm:text-sm leading-relaxed max-w-2xl">
                            {{ $user->bio ?: 'Profesional berdedikasi yang aktif berkontribusi dalam berbagai proyek kolaboratif, memastikan setiap target dan deliverable tercapai dengan standar kualitas tinggi.' }}
                        </p>

                        <!-- Affiliated Companies & Roles -->
                        @php
                            $allUserCompanies = $user->allCompanies();
                        @endphp
                        @if($allUserCompanies->isNotEmpty())
                            <div class="pt-2 flex flex-wrap items-center justify-center md:justify-start gap-2">
                                <span class="text-xs text-slate-400 font-semibold">Organisasi / Workspace:</span>
                                @foreach($allUserCompanies as $comp)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-800/80 border border-slate-700/60 rounded-xl text-xs text-slate-200 font-medium">
                                        <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                                        {{ $comp->company_name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="pt-4 flex flex-wrap items-center justify-center md:justify-start gap-3">
                            <button type="button" onclick="shareToLinkedIn()" class="px-5 py-2.5 bg-[#0a66c2] hover:bg-[#004182] text-white text-xs font-black rounded-xl transition shadow-md flex items-center gap-2">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>
                                <span>Bagikan ke LinkedIn</span>
                            </button>

                            <button type="button" onclick="shareToWhatsApp()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black rounded-xl transition shadow-md flex items-center gap-2">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2m.01 1.67c4.56 0 8.27 3.71 8.27 8.24 0 2.2-.86 4.28-2.42 5.84l-.59.59-3.25.86.86-3.25.59-.59c1.56-1.56 2.42-3.64 2.42-5.84 0-4.53-3.71-8.24-8.27-8.24-4.54 0-8.24 3.7-8.24 8.24 0 1.58.45 3.12 1.31 4.46l.29.45-.73 2.66 2.72-.71.43.27c1.3.82 2.81 1.25 4.35 1.25z"/></svg>
                                <span>WhatsApp</span>
                            </button>

                            <button type="button" onclick="copyPortfolioLink()" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-black rounded-xl transition border border-white/10 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <span>Salin Tautan</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. Credibility & Gamification Stats Grid -->
        <section class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Total XP & Level -->
            <div class="glass-card glass-card-hover rounded-2xl p-5 sm:p-6 space-y-3">
                <div class="flex items-center justify-between text-xs text-slate-400 font-bold uppercase tracking-wider">
                    <span>Poin Reputasi</span>
                    <span class="text-amber-400 font-black">Level {{ $stats['level'] }}</span>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-white">
                    {{ number_format($stats['total_xp']) }} <span class="text-xs font-semibold text-slate-400">XP</span>
                </div>
                @php
                    $currentLevelXp = $stats['total_xp'] % 200;
                    $progressPct = min(100, round(($currentLevelXp / 200) * 100));
                @endphp
                <div class="space-y-1">
                    <div class="w-full bg-slate-800 rounded-full h-2 overflow-hidden">
                        <div class="bg-gradient-to-r from-amber-500 to-orange-500 h-2 rounded-full transition-all duration-500" style="width: {{ $progressPct }}%"></div>
                    </div>
                    <div class="flex justify-between text-[10px] text-slate-400 font-mono">
                        <span>{{ $currentLevelXp }}/200 XP</span>
                        <span>Level {{ $stats['level'] + 1 }}</span>
                    </div>
                </div>
            </div>

            <!-- Proyek yang Diikuti -->
            <div class="glass-card glass-card-hover rounded-2xl p-5 sm:p-6 space-y-3">
                <div class="flex items-center justify-between text-xs text-slate-400 font-bold uppercase tracking-wider">
                    <span>Proyek Kolaboratif</span>
                    <span class="text-indigo-400 text-lg">📁</span>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-white">
                    {{ $stats['projects_count'] }} <span class="text-xs font-semibold text-slate-400">Proyek</span>
                </div>
                <p class="text-[11px] text-slate-400">
                    Terdaftar aktif dalam kolaborasi lintas perusahaan
                </p>
            </div>

            <!-- Verified Tasks Completed -->
            <div class="glass-card glass-card-hover rounded-2xl p-5 sm:p-6 space-y-3">
                <div class="flex items-center justify-between text-xs text-slate-400 font-bold uppercase tracking-wider">
                    <span>Tugas Tuntas</span>
                    <span class="text-emerald-400 text-lg">✅</span>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-white">
                    {{ $stats['completed_tasks_count'] }} <span class="text-xs font-semibold text-slate-400">Tugas</span>
                </div>
                <p class="text-[11px] text-emerald-400/90 font-medium">
                    100% tuntas terverifikasi manajemen
                </p>
            </div>

            <!-- On-Time Completion Rate -->
            <div class="glass-card glass-card-hover rounded-2xl p-5 sm:p-6 space-y-3">
                <div class="flex items-center justify-between text-xs text-slate-400 font-bold uppercase tracking-wider">
                    <span>Ketepatan Waktu</span>
                    <span class="text-sky-400 text-lg">⚡</span>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-white">
                    {{ $stats['ontime_rate'] }}%
                </div>
                <div class="flex items-center gap-1.5 text-[11px] text-slate-400">
                    <span class="text-amber-300 font-bold">{{ $stats['ontime_count'] }}x</span> tepat tenggat waktu
                </div>
            </div>
        </section>

        <!-- 3. Badges & Awards Showcase -->
        @if($user->awards->isNotEmpty())
            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight flex items-center gap-2">
                            <span>🏆 Lencana & Penghargaan Resmi</span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">Penghargaan yang diraih dan diverifikasi secara kriptografis oleh Yoimo Workspace</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($user->awards as $award)
                        <div class="glass-card glass-card-hover rounded-2xl p-5 border border-indigo-500/20 relative overflow-hidden flex flex-col justify-between">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="px-2.5 py-1 bg-amber-500/10 text-amber-300 text-[10px] font-black rounded-lg uppercase tracking-wider border border-amber-500/20">
                                        Badge Terbit
                                    </span>
                                    <span class="text-[11px] text-slate-400 font-mono">{{ $award->issued_date->format('d M Y') }}</span>
                                </div>
                                <div>
                                    <h3 class="text-base font-black text-white group-hover:text-indigo-300 transition-colors">{{ $award->title }}</h3>
                                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">
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
                            <div class="pt-4 mt-2 border-t border-white/5 flex items-center justify-between">
                                <a href="{{ route('public.award.show', $award->share_token) }}" target="_blank" class="text-xs font-bold text-indigo-400 hover:text-indigo-300 flex items-center gap-1">
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

        <!-- 4. Projects Showcase ("JADI PROJECT YANG DI IKUTI BISA MASUK") -->
        <section class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight flex items-center gap-2">
                        <span>💼 Portofolio Proyek yang Diikuti</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Daftar proyek riil tempat {{ $user->name }} berpartisipasi dan berkontribusi secara nyata</p>
                </div>
                <span class="text-xs font-bold px-3 py-1 bg-slate-800 rounded-xl text-slate-300 self-start sm:self-auto border border-white/5">
                    Total {{ $projects->count() }} Proyek
                </span>
            </div>

            @if($projects->isEmpty())
                <div class="glass-card rounded-2xl p-10 text-center space-y-3">
                    <div class="text-4xl">📂</div>
                    <h3 class="text-base font-bold text-slate-200">Belum Ada Proyek Publik</h3>
                    <p class="text-xs text-slate-400 max-w-md mx-auto">Pengguna ini sedang mempersiapkan keterlibatan dalam proyek baru di Yoimo Workspace.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                    @foreach($projects as $project)
                        @php
                            // Cari peran user di dalam proyek
                            $matrix = collect($project->team_matrix ?? []);
                            $userRow = $matrix->first(function($item) use ($user) {
                                return isset($item['user_id']) && (string)$item['user_id'] === (string)$user->id;
                            });
                            $roleInProject = $userRow['role'] ?? ($project->created_by === $user->id ? 'Lead / Creator' : 'Team Contributor');
                            $userTasksCount = $project->tasks->where('assigned_to', $user->id)->count();
                            $userCompletedTasksCount = $project->tasks->where('assigned_to', $user->id)->where('status', 'Completed')->count();
                        @endphp
                        <div class="glass-card glass-card-hover rounded-2xl p-6 space-y-4 border border-white/5 flex flex-col justify-between">
                            <div class="space-y-3">
                                <!-- Badges row -->
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-indigo-500/10 text-indigo-300 border border-indigo-500/20">
                                            {{ $project->category ?: 'Project' }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $project->status === 'Completed' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-sky-500/10 text-sky-400 border border-sky-500/20' }}">
                                            {{ $project->status }}
                                        </span>
                                    </div>
                                    @if($project->company)
                                        <span class="text-[11px] font-medium text-slate-400">
                                            {{ $project->company->company_name }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Project Title & Client -->
                                <div>
                                    <h3 class="text-lg font-black text-white">{{ $project->name }}</h3>
                                    @if($project->client_name)
                                        <p class="text-xs text-indigo-300 font-semibold mt-0.5">Klien: {{ $project->client_name }}</p>
                                    @endif
                                </div>

                                <!-- Problem statement / brief -->
                                <p class="text-xs text-slate-300 line-clamp-3 leading-relaxed">
                                    {{ $project->problem_statement ?: ($project->description ?: 'Proyek kolaboratif strategis yang dikerjakan bersama tim pengembang dan manajer di ekosistem Yoimo Workspace.') }}
                                </p>

                                <!-- Role & Contributions -->
                                <div class="pt-2 flex flex-wrap items-center gap-2 text-xs">
                                    <span class="px-3 py-1 bg-slate-800/90 rounded-xl text-slate-200 font-semibold border border-slate-700/50">
                                        Peran: <strong class="text-white">{{ $roleInProject }}</strong>
                                    </span>
                                    @if($userTasksCount > 0)
                                        <span class="px-3 py-1 bg-indigo-950/60 rounded-xl text-indigo-300 font-semibold border border-indigo-800/40">
                                            {{ $userCompletedTasksCount }}/{{ $userTasksCount }} Tugas Tuntas
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="pt-3 border-t border-white/5 space-y-1.5">
                                <div class="flex justify-between text-[11px] text-slate-400">
                                    <span>Progres Proyek</span>
                                    <span class="font-mono text-white font-bold">{{ $project->progress_percentage }}%</span>
                                </div>
                                <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-gradient-to-r from-indigo-500 to-emerald-400 h-1.5 rounded-full" style="width: {{ $project->progress_percentage }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <!-- 5. Verified Completed Tasks (Verified Deliverables) -->
        @if($completedTasks->isNotEmpty())
            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight flex items-center gap-2">
                            <span>✅ Kontribusi Tugas Terverifikasi</span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">Daftar penugasan nyata yang telah diselesaikan 100% dan diverifikasi oleh manajemen</p>
                    </div>
                </div>

                <div class="glass-card rounded-2xl divide-y divide-white/5 overflow-hidden border border-white/5">
                    @foreach($completedTasks as $task)
                        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-white/[0.02] transition-colors">
                            <div class="space-y-1 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $task->priority === 'Urgent' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : ($task->priority === 'High' ? 'bg-orange-500/10 text-orange-400 border border-orange-500/20' : 'bg-slate-700/50 text-slate-300') }}">
                                        {{ $task->priority }}
                                    </span>
                                    @if($task->project)
                                        <span class="text-xs text-indigo-400 font-semibold">
                                            {{ $task->project->name }}
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-sm font-bold text-white">{{ $task->title }}</h3>
                                @if($task->description)
                                    <p class="text-xs text-slate-400 line-clamp-2 leading-relaxed">{{ strip_tags($task->description) }}</p>
                                @endif
                            </div>

                            <div class="flex sm:flex-col items-center sm:items-end justify-between shrink-0 gap-1 text-xs">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-500/10 text-emerald-400 font-bold rounded-lg border border-emerald-500/20 text-[11px]">
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

        <!-- 6. Gamification Tracing Activity Logs -->
        @if($recentLogs->isNotEmpty())
            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg sm:text-xl font-black text-white tracking-tight flex items-center gap-2">
                            <span>⚡ Rekam Jejak Gamifikasi & Tracing Poin</span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">Audit log poin reputasi yang diperoleh secara transparan atas aktivitas platform</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($recentLogs as $log)
                        <div class="glass-card rounded-xl p-3.5 flex items-center justify-between gap-3 text-xs border border-white/5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 font-black flex items-center justify-center shrink-0 border border-indigo-500/20">
                                    @if($log->source_type === 'task_completed')
                                        ⭐
                                    @elseif($log->source_type === 'task_ontime')
                                        ⚡
                                    @elseif($log->source_type === 'daily_login')
                                        🔥
                                    @else
                                        💎
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-slate-200">{{ $log->description ?: ucfirst(str_replace('_', ' ', $log->source_type)) }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <span class="font-black {{ $log->points >= 0 ? 'text-emerald-400' : 'text-rose-400' }} font-mono shrink-0">
                                {{ $log->points >= 0 ? '+' . $log->points : $log->points }} XP
                            </span>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

    </main>

    <!-- Footer -->
    <footer class="border-t border-white/5 bg-slate-950/80 py-10 text-center text-xs text-slate-400 space-y-3">
        <div class="flex items-center justify-center gap-2">
            <img src="{{ asset('icon/11.png') }}" alt="Yoimo" class="w-5 h-5 rounded object-contain">
            <span class="font-bold text-white tracking-wider">YOIMO WORKSPACE</span>
        </div>
        <p class="max-w-md mx-auto leading-relaxed">
            Platform Kolaborasi Kerja Terintegrasi, Gamifikasi Kinerja, dan Portofolio Digital Modern.
        </p>
        <p class="text-[11px] text-slate-600">&copy; {{ date('Y') }} Yoimo Workspace by LUNOU. Hak cipta dilindungi.</p>
    </footer>

    <!-- Notification Toast -->
    <div id="portfolioToast" class="fixed bottom-6 right-6 max-w-md bg-slate-900/95 text-white border border-indigo-500/40 rounded-2xl p-4 shadow-2xl backdrop-blur-md transform translate-y-24 opacity-0 transition-all duration-300 z-50 pointer-events-none flex items-start gap-3">
        <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <div class="text-xs space-y-1">
            <p id="portfolioToastTitle" class="font-black text-indigo-200">Tautan Portofolio Disalin!</p>
            <p id="portfolioToastMsg" class="text-slate-300 leading-relaxed">Tautan telah disalin ke papan klip Anda.</p>
        </div>
    </div>

    <script>
        function showToast(title, msg) {
            const toast = document.getElementById('portfolioToast');
            if (title) document.getElementById('portfolioToastTitle').textContent = title;
            if (msg) document.getElementById('portfolioToastMsg').innerHTML = msg;
            toast.classList.remove('translate-y-24', 'opacity-0');
            setTimeout(() => {
                toast.classList.add('translate-y-24', 'opacity-0');
            }, 5000);
        }

        function copyPortfolioLink() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                showToast('Tautan Berhasil Disalin!', 'Bagikan link portofolio Anda ke klien, rekan tim, atau profil media sosial.');
            }).catch(() => {
                showToast('Gagal Menyalin', 'Silakan salin manual URL dari bilah alamat browser.');
            });
        }

        function shareToLinkedIn() {
            const url = window.location.href;
            const caption = `🚀 Senang dapat membagikan portofolio profesional dan rekam jejak kerja terverifikasi saya di Yoimo Workspace!\n\n👤 Nama: {{ $user->name }}\n💼 Peran: {{ $userRoleTitle }}\n⭐ Level Reputasi: Level {{ $stats['level'] }} ({{ $stats['total_xp'] }} XP)\n✅ Kontribusi: {{ $stats['completed_tasks_count'] }} Tugas Tuntas • {{ $stats['ontime_rate'] }}% Ketepatan Waktu\n\nCek portofolio dan proyek yang saya ikuti di tautan berikut:\n${url}\n\n#Portfolio #YoimoWorkspace #ProjectManagement #ProfessionalGrowth #TechTalent #Collaboration`;

            navigator.clipboard.writeText(caption).then(() => {
                showToast('Caption LinkedIn Disalin!', 'Tekan <kbd class="px-1.5 py-0.5 bg-slate-800 border border-slate-700 rounded text-[10px] font-mono text-amber-300">Ctrl + V</kbd> di jendela posting LinkedIn, lalu klik <strong>Post</strong>.');
                const shareUrl = "https://www.linkedin.com/sharing/share-offsite/?url=" + encodeURIComponent(url);
                window.open(shareUrl, '_blank', 'width=650,height=600');
            }).catch(() => {
                const shareUrl = "https://www.linkedin.com/sharing/share-offsite/?url=" + encodeURIComponent(url);
                window.open(shareUrl, '_blank', 'width=650,height=600');
            });
        }

        function shareToWhatsApp() {
            const url = window.location.href;
            const text = `Halo! Lihat portofolio profesional dan rekam jejak proyek ${encodeURIComponent('{{ $user->name }}')} di Yoimo Workspace: ${url}`;
            window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(text)}`, '_blank');
        }
    </script>
</body>
</html>
