@extends('user.layouts.app')

@section('title', 'Ruang Pribadi & Agenda Saya')

@section('content')
<div class="space-y-8 font-sans">

    <!-- Notifikasi Flash -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-sm">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
        </div>
    @endif

    <style>
        @keyframes mascot-float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-6px) rotate(2deg); }
        }
        .animate-mascot {
            animation: mascot-float 3.5s ease-in-out infinite;
        }
        .animate-mascot-delayed {
            animation: mascot-float 3.5s ease-in-out infinite;
            animation-delay: 1.75s;
        }
    </style>

    <!-- 1. Hero Banner: Profil & Indikator Produktivitas -->
    <div class="bg-gradient-to-r from-indigo-50 via-purple-50/50 to-indigo-50 border border-indigo-100/70 rounded-3xl p-6 sm:p-8 text-slate-800 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="space-y-2">
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-indigo-100 border border-indigo-200 rounded-xl text-[10px] font-black uppercase tracking-wider text-indigo-700">
                    Pusat Informasi & Kerja
                </span>
                <span class="text-xs text-slate-500">• {{ Auth::user()->position ?? 'Team Member' }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Halo, {{ Auth::user()->name }}!</h1>
            <p class="text-xs text-slate-600 max-w-xl leading-relaxed">
                Pantau seluruh tugas aktif, agenda tim yang mengundang Anda, dan tenggat waktu mendatang di satu tempat.
            </p>
            <div class="pt-2">
                <span class="text-xs bg-white border border-indigo-100/80 text-indigo-900 px-3 py-1.5 rounded-xl font-bold inline-block shadow-xs">
                    @if($completionRate == 100)
                        Luar biasa! Semua tugas selesai dikerjakan!
                    @elseif($completionRate >= 75)
                        Keren sekali! Selangkah lagi menuju sempurna!
                    @elseif($completionRate >= 50)
                        Progres yang bagus! Teruskan semangat kerjamu!
                    @elseif($completionRate > 0)
                        Setiap langkah kecil membawamu lebih dekat! Tetap fokus!
                    @else
                        Mari mulai hari dengan penuh semangat! Ambil tugas pertamamu!
                    @endif
                </span>
            </div>
        </div>

        <!-- Indikator Produktivitas & Completion Rate -->
        <div class="flex items-center gap-4 bg-white/90 border border-indigo-100/60 p-4 rounded-2xl shrink-0 shadow-sm">
            <div class="text-center">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">RATE PENYELESAIAN</span>
                <span class="text-2xl font-black text-indigo-600 block mt-0.5">{{ $completionRate }}%</span>
            </div>
            <div class="w-px h-10 bg-slate-200"></div>
            <div class="text-center">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TOTAL TUGAS SAYA</span>
                <span class="text-2xl font-black text-purple-600 block mt-0.5">{{ $totalAssigned }}</span>
            </div>
        </div>
    </div>

    <!-- MAIN VIEW SWITCHER TABS (Clean and emoji-free) -->
    <div class="flex flex-wrap border-b border-slate-200 justify-between items-center pr-2 gap-2">
        <div class="flex">
            <button onclick="switchDashboardTab('work')" id="dashboard-tab-work-btn" class="px-6 py-3.5 border-b-2 border-indigo-600 text-indigo-755 font-black text-sm transition-all focus:outline-none cursor-pointer">
                Pusat Kerja
            </button>
            <button onclick="switchDashboardTab('wellbeing')" id="dashboard-tab-wellbeing-btn" class="px-6 py-3.5 border-b-2 border-transparent text-slate-500 font-bold hover:text-slate-800 text-sm transition-all focus:outline-none flex items-center gap-1.5 cursor-pointer">
                Ruang Pemulihan & Refleksi
            </button>
        </div>
        <button type="button" onclick="startGuidedTour()" id="start-tour-btn" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-750 hover:from-indigo-700 hover:to-indigo-850 text-white text-xs font-black rounded-xl shadow transition-all cursor-pointer flex items-center gap-2">
            Panduan Demo Interaktif
        </button>
    </div>

    <!-- TAB SECTION 1: PUSAT KERJA -->
    <div id="dashboard-section-work" class="space-y-8">
        
        <!-- LUNOU AI Productivity Analysis & Motivation Card -->
        <div class="bg-indigo-600 border border-indigo-700 text-white rounded-3xl p-6 shadow-md relative overflow-hidden flex flex-col md:flex-row items-center gap-6">
            <!-- Background glow effect -->
            <div class="absolute -right-16 -top-16 w-48 h-48 rounded-full bg-white/10 blur-2xl"></div>
            <div class="absolute -left-16 -bottom-16 w-48 h-48 rounded-full bg-indigo-500/30 blur-2xl"></div>

            <div class="shrink-0 flex items-center justify-center bg-white/10 border border-white/20 w-14 h-14 rounded-2xl z-10">
                <img src="{{ asset('icon/11.png') }}" alt="LUNOU" class="w-10 h-10 object-contain animate-mascot">
            </div>
            
            <div class="space-y-1.5 z-10">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 bg-white/20 border border-white/25 rounded-md text-[8px] font-black uppercase tracking-wider text-indigo-100">LUNOU AI Analisis Kerja</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                </div>
                <h4 class="text-xs font-black tracking-tight text-white uppercase">Analisis & Motivasi Kerja Anda</h4>
                <p class="text-xs text-indigo-100 font-semibold leading-relaxed">
                    {{ $workMotivationText }}
                </p>
            </div>
        </div>
        
        <!-- Sad Mascot Check-in Reminder (If not checked in today) -->
        @if(!$todayCheckin)
            <div id="sad-mascot-reminder" class="bg-amber-50/70 border border-amber-200/80 rounded-3xl p-5 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-xs">
                <div class="flex items-center gap-3.5">
                    <img src="{{ asset('icon/18.png') }}" alt="Sad LUNOU" class="w-12 h-12 object-contain animate-bounce shrink-0">
                    <div>
                        <h4 class="text-xs font-black text-amber-900 uppercase tracking-wider">LUNOU Khawatir Padamu</h4>
                        <p class="text-xs text-amber-855 font-bold leading-relaxed mt-0.5">LUNOU sedih melihatmu langsung bekerja tanpa jeda... Yuk luangkan 1 menit untuk check-in dan rileks sejenak.</p>
                    </div>
                </div>
                <button type="button" onclick="goToWellbeingCheckin()" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-black rounded-xl shadow-md transition-all shrink-0">
                    Menuju Ruang Pemulihan
                </button>
            </div>
        @endif

        <!-- Peringatan Dokumen Wajib Baca (Jika Ada) -->
        @if($mandatoryDocs->count() > 0)
            <div class="bg-amber-50 border border-amber-200 text-amber-900 p-5 rounded-3xl space-y-2 shadow-sm">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <h3 class="text-xs font-black uppercase tracking-wider">Perhatian: Ada Dokumen Panduan Wajib Baca yang Belum Anda Buka</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 pt-1">
                    @foreach($mandatoryDocs as $mDoc)
                        <a href="{{ route('user.projects.show', [$mDoc->project_id, 'tab' => 'documents']) }}" class="p-3 bg-white border border-amber-200 rounded-xl flex items-center justify-between text-xs font-bold text-slate-800 hover:bg-amber-100/50 transition-all shadow-sm">
                            <span class="truncate">{{ $mDoc->title }}</span>
                            <span class="text-[10px] text-amber-700 uppercase shrink-0">Buka SOP &rarr;</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Grid Indikator Metrik Tugas -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-white to-slate-50 border border-slate-100 rounded-3xl p-5 shadow-sm hover:shadow-md transition-all flex items-center justify-between gap-3 group">
                <div class="space-y-1">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">TUGAS SAYA (TODO)</span>
                    <div class="text-3xl font-black text-slate-800 tracking-tight group-hover:scale-105 transition-transform duration-200">{{ $todoCount }}</div>
                </div>
                <img src="{{ asset('icon/11.png') }}" alt="Mascot Todo" class="w-12 h-12 object-contain shrink-0 animate-mascot">
            </div>

            <div class="bg-gradient-to-br from-amber-50/50 to-white border border-amber-100 rounded-3xl p-5 shadow-sm hover:shadow-md transition-all flex items-center justify-between gap-3 group ring-1 ring-amber-100/50">
                <div class="space-y-1">
                    <span class="text-[10px] font-black text-amber-600 uppercase tracking-wider block">SEDANG DIKERJAKAN</span>
                    <div class="text-3xl font-black text-amber-600 tracking-tight group-hover:scale-105 transition-transform duration-200">{{ $inProgressCount }}</div>
                </div>
                <img src="{{ asset('icon/2.png') }}" alt="Mascot In Progress" class="w-12 h-12 object-contain shrink-0 animate-mascot-delayed">
            </div>

            <div class="bg-gradient-to-br from-indigo-50/50 to-white border border-indigo-100 rounded-3xl p-5 shadow-sm hover:shadow-md transition-all flex items-center justify-between gap-3 group ring-1 ring-indigo-100/50">
                <div class="space-y-1">
                    <span class="text-[10px] font-black text-indigo-600 uppercase tracking-wider block">MENUNGGU REVIEW</span>
                    <div class="text-3xl font-black text-indigo-600 tracking-tight group-hover:scale-105 transition-transform duration-200">{{ $reviewCount }}</div>
                </div>
                <img src="{{ asset('icon/4.png') }}" alt="Mascot Review" class="w-12 h-12 object-contain shrink-0 animate-mascot">
            </div>

            <div class="bg-gradient-to-br from-emerald-50/50 to-white border border-emerald-100 rounded-3xl p-5 shadow-sm hover:shadow-md transition-all flex items-center justify-between gap-3 group ring-1 ring-emerald-100/50">
                <div class="space-y-1">
                    <span class="text-[10px] font-black text-emerald-600 uppercase tracking-wider block">SELESAI</span>
                    <div class="text-3xl font-black text-emerald-600 tracking-tight group-hover:scale-105 transition-transform duration-200">{{ $completedCount }}</div>
                </div>
                <img src="{{ asset('icon/18.png') }}" alt="Mascot Completed" class="w-12 h-12 object-contain shrink-0 animate-mascot-delayed">
            </div>
        </div>

        <!-- Grid Utama Tugas & Agenda -->
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
            <div class="xl:col-span-8 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-base font-black text-slate-900 tracking-tight">Daftar Tanggung Jawab & Tugas Saya</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Tugas lintas seluruh proyek yang ditugaskan kepada Anda.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-1">
                        <a href="{{ route('user.dashboard') }}" class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all {{ !request('status') ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600' }}">
                            Semua
                        </a>
                        <a href="{{ route('user.dashboard', ['status' => 'In Progress']) }}" class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all {{ request('status') === 'In Progress' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600' }}">
                            In Progress
                        </a>
                        <a href="{{ route('user.dashboard', ['status' => 'Todo']) }}" class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all {{ request('status') === 'Todo' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600' }}">
                            Todo
                        </a>
                        <a href="{{ route('user.dashboard', ['status' => 'Review']) }}" class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all {{ request('status') === 'Review' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600' }}">
                            Review
                        </a>
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse($myTasks as $task)
                        <div class="bg-white border {{ $task->status === 'In Progress' ? 'border-indigo-300 ring-2 ring-indigo-50 shadow-md' : 'border-slate-100 shadow-sm' }} rounded-3xl p-6 space-y-4 hover:shadow-md transition-all">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="space-y-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider {{ $task->status === 'Completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($task->status === 'Review' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-amber-50 text-amber-700 border border-amber-200') }}">
                                            {{ $task->status }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider {{ $task->priority === 'Urgent' ? 'bg-rose-50 text-rose-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $task->priority }}
                                        </span>
                                        <span class="text-xs font-bold text-slate-400">
                                            • Proyek: <strong class="text-slate-700">{{ $task->project->name ?? 'Proyek' }}</strong>
                                        </span>
                                    </div>
                                    <h3 class="text-base font-black text-slate-900 mt-1">{{ $task->title }}</h3>
                                </div>
                                <div class="text-left sm:text-right shrink-0">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase block">DEADLINE</span>
                                    <span class="text-xs font-bold {{ $task->due_date && $task->due_date->isPast() && $task->status !== 'Completed' ? 'text-rose-600' : 'text-slate-700' }}">
                                        {{ $task->due_date ? $task->due_date->format('d M Y') : 'Fleksibel' }}
                                    </span>
                                </div>
                            </div>

                            @if($task->description)
                                <div class="p-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-xs text-slate-600 line-clamp-3">
                                    {!! $task->description !!}
                                </div>
                            @endif

                            @if($task->roadmap && $task->linked_objective_index !== null)
                                @php
                                    $targetText = $task->roadmap->objectives[$task->linked_objective_index]['target'] ?? 'Target output';
                                    $isAchieved = $task->roadmap->objectives[$task->linked_objective_index]['is_achieved'] ?? false;
                                @endphp
                                <div class="p-2.5 bg-indigo-50/50 border border-indigo-100 rounded-xl text-xs flex items-center justify-between">
                                    <span class="font-bold text-indigo-950">Terikat Target: {{ $targetText }}</span>
                                    <span class="text-[10px] font-black uppercase {{ $isAchieved ? 'text-emerald-700' : 'text-amber-700' }}">
                                        {{ $isAchieved ? 'Sudah Tercapai' : 'Sedang Berjalan' }}
                                    </span>
                                </div>
                            @endif

                            <div class="flex items-center justify-between gap-3 pt-3 border-t border-slate-100">
                                <div class="flex items-center gap-2">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase">Status:</span>
                                    <span class="px-2.5 py-1 bg-slate-100 border border-slate-200/60 rounded-xl text-[10px] font-black text-slate-700 uppercase">
                                        {{ $task->status }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($task->status === 'Completed')
                                        @php
                                            $publicTaskUrl = route('public.task.show', $task->id);
                                            $taskProjectName = $task->project?->name ?? 'Proyek';
                                            $cleanTitle = addslashes($task->title);
                                            $cleanProject = addslashes($taskProjectName);
                                            $taskCaption = "🎯 Senang sekali dapat menyelesaikan tugas \"{$cleanTitle}\" pada proyek \"{$cleanProject}\" di Yoimo Workspace!\n\n📌 Rincian Pencapaian:\n• Tugas: {$cleanTitle}\n• Proyek: {$cleanProject}\n• Status: Selesai 100% & Terverifikasi\n\nLihat bukti verifikasi penyelesaian tugas saya secara publik di sini:\n{$publicTaskUrl}\n\n#YoimoWorkspace #Productivity #ProjectManagement #WorkLifeHarmony #Achievement #KerjaCerdas";
                                        @endphp
                                        <button type="button" 
                                           onclick="openLinkedInShareModal('{{ $cleanTitle }}', 'Proyek: {{ $cleanProject }}', '{{ $publicTaskUrl }}', `{{ $taskCaption }}`)"
                                           class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md transition-all flex items-center gap-1.5 group"
                                           title="Bagikan penyelesaian tugas ke LinkedIn">
                                            <svg class="w-3.5 h-3.5 fill-current opacity-90 group-hover:opacity-100" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>
                                            <span>Share LinkedIn</span>
                                        </button>
                                    @endif
                                    <a href="{{ route('user.projects.show', [$task->project_id, 'tab' => 'tasks']) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md transition-all flex items-center gap-1.5">
                                        <span>Buka Lembar Kerja</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white border border-dashed border-slate-200 rounded-3xl p-12 text-center text-xs text-slate-400">
                            Tidak ada tugas yang sesuai dengan filter ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="xl:col-span-4 space-y-6">
                <!-- Deadlines Radar -->
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Radar Tenggat Waktu</h3>
                        <span class="text-rose-500 font-bold text-[10px]">Mendekati Waktu</span>
                    </div>
                    <div class="space-y-2.5">
                        @forelse($upcomingDeadlines as $dl)
                            <div class="p-3 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-between text-xs">
                                <div class="truncate mr-2">
                                    <span class="font-bold text-slate-900 block truncate">{{ $dl->title }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $dl->project->name ?? 'Project' }}</span>
                                </div>
                                <span class="px-2 py-1 rounded-lg text-[10px] font-black shrink-0 {{ $dl->due_date->isPast() ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $dl->due_date->format('d M') }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic">Tidak ada deadline yang mendesak.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Agenda Acara -->
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Agenda Terdaftar</h3>
                        <span class="text-indigo-600 font-bold text-[10px]">Undangan Anda</span>
                    </div>
                    <div class="space-y-2.5">
                        @forelse($myAgendas as $ag)
                            <div class="p-3.5 bg-indigo-50/50 border border-indigo-100 rounded-2xl space-y-1 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-black uppercase text-indigo-700">{{ $ag->category }}</span>
                                    <span class="text-[10px] font-bold text-slate-500">{{ $ag->start_date->format('d M') }} {{ $ag->start_time ? '(' . date('H:i', strtotime($ag->start_time)) . ')' : '' }}</span>
                                </div>
                                <h4 class="font-black text-slate-900">{{ $ag->title }}</h4>
                                @if($ag->location_type === 'online' && $ag->meeting_url)
                                    <a href="{{ $ag->meeting_url }}" target="_blank" class="text-[11px] text-indigo-600 font-bold underline block mt-1">
                                        Buka Link Meeting &rarr;
                                    </a>
                                @elseif($ag->location_address)
                                    <span class="text-[11px] text-slate-500 block mt-1">{{ $ag->location_address }}</span>
                                @endif
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic">Belum ada agenda rapat terdaftar.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Tugas Terbuka -->
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tugas Terbuka</h3>
                        <span class="text-emerald-600 font-bold text-[10px]">Bisa Diambil</span>
                    </div>
                    <div class="space-y-2.5">
                        @forelse($openTasks as $ot)
                            <div class="p-3.5 bg-emerald-50/50 border border-emerald-100 rounded-2xl space-y-2 text-xs">
                                <div>
                                    <span class="text-[9px] font-black uppercase text-emerald-700 block">{{ $ot->project->name ?? 'Proyek' }}</span>
                                    <h4 class="font-black text-slate-900">{{ $ot->title }}</h4>
                                </div>
                                <form method="POST" action="{{ route('user.projects.tasks.claim', [$ot->project_id, $ot->id]) }}">
                                    @csrf
                                    <button type="submit" class="w-full py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] rounded-xl shadow-sm transition-all">
                                        Ambil & Kerjakan Tugas Ini
                                    </button>
                                </form>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic">Tidak ada tugas terbuka saat ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- TAB SECTION 2: RUANG PEMULIHAN & REFLEKSI (WELLBEING) -->
    <div id="dashboard-section-wellbeing" class="hidden space-y-6">
        
        <!-- Header Philosophy Quote -->
        <div class="bg-gradient-to-br from-emerald-50/60 to-slate-50 border border-slate-200/60 rounded-3xl p-6 text-center space-y-3 shadow-xs">
            <h2 class="text-xl font-black text-slate-855 italic tracking-tight">"Kerja adalah bagian dari hidup. Bukan seluruh hidup."</h2>
            <div class="flex flex-wrap items-center justify-center gap-1.5 text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                <span>Privasi Terjamin Penuh: Jurnal & Check-in Pribadi Anda Tidak Dapat Diakses Perusahaan</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Side Wellbeing Navigation Tabs -->
            <div class="lg:col-span-3 space-y-2 bg-slate-50 p-3.5 border border-slate-200/50 rounded-3xl">
                <span class="text-[9px] font-black text-slate-400 tracking-wider uppercase block px-3 py-1">PANDUAN PEMULIHAN</span>
                
                <button type="button" onclick="switchWellbeingSubTab('auto-journal')" id="w-tab-auto-journal-btn" class="w-full flex items-center gap-2 text-left px-3.5 py-2.5 rounded-xl text-xs font-black transition-all bg-white text-indigo-755 shadow-xs border border-slate-200/60">
                    <img src="{{ asset('icon/11.png') }}" alt="LUNOU" class="w-4 h-4 object-contain">
                    AI Auto-Journaler
                </button>
                <button type="button" onclick="switchWellbeingSubTab('today')" id="w-tab-today-btn" class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all text-slate-600 hover:bg-white hover:text-slate-900">
                    Hari Ini (Check-in)
                </button>
                <button type="button" onclick="switchWellbeingSubTab('journal')" id="w-tab-journal-btn" class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all text-slate-600 hover:bg-white hover:text-slate-900">
                    Jurnal Refleksi
                </button>
                <button type="button" onclick="switchWellbeingSubTab('recovery')" id="w-tab-recovery-btn" class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all text-slate-600 hover:bg-white hover:text-slate-900">
                    Papan Rileksasi (Relax)
                </button>
                <button type="button" onclick="switchWellbeingSubTab('goals')" id="w-tab-goals-btn" class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all text-slate-600 hover:bg-white hover:text-slate-900">
                    Target Hidup (Goals)
                </button>
                <button type="button" onclick="switchWellbeingSubTab('reflection')" id="w-tab-reflection-btn" class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all text-slate-600 hover:bg-white hover:text-slate-900">
                    Refleksi Mingguan
                </button>
                <button type="button" onclick="switchWellbeingSubTab('leave')" id="w-tab-leave-btn" class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all text-slate-600 hover:bg-white hover:text-slate-900">
                    Lepaskan Pikiran (Pulang)
                </button>
                <button type="button" onclick="switchWellbeingSubTab('balance')" id="w-tab-balance-btn" class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all text-slate-600 hover:bg-white hover:text-slate-900">
                    Laporan Keseimbangan (AI)
                </button>
                <button type="button" onclick="switchWellbeingSubTab('gamification')" id="w-tab-gamification-btn" class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all text-slate-600 hover:bg-white hover:text-slate-900">
                    Poin & Pencapaian
                </button>
                
                <span class="text-[9px] font-black text-slate-400 tracking-wider uppercase block px-3 py-1 mt-2">RUANG MENTAL BARU</span>
                <button type="button" onclick="switchWellbeingSubTab('mental-friend')" id="w-tab-mental-friend-btn" class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all text-slate-600 hover:bg-white hover:text-slate-900 flex items-center gap-2">
                    Teman Cerita LUNOU
                </button>
                <button type="button" onclick="switchWellbeingSubTab('mental-game')" id="w-tab-mental-game-btn" class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all text-slate-600 hover:bg-white hover:text-slate-900 flex items-center gap-2">
                    Game Ketenangan
                </button>
                <button type="button" onclick="switchWellbeingSubTab('mental-reward')" id="w-tab-mental-reward-btn" class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all text-slate-600 hover:bg-white hover:text-slate-900 flex items-center gap-2">
                    Self-Reward Shop
                </button>
            </div>

            <!-- Right Side Wellbeing Active Tab Viewport -->
            <div class="lg:col-span-9 space-y-6">

                <!-- SUBTAB 1: TODAY DAILY CHECK-IN -->
                <div id="w-section-today" class="hidden bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Daily Self Check-in</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Bagaimana kondisi energimu dan perasaanmu hari ini?</p>
                        </div>
                        <!-- LUNOU AI Auto checkin trigger button -->
                        <button type="button" onclick="toggleAutoCheckinWidget()" class="flex items-center gap-2 px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 border border-indigo-150 rounded-xl text-xs font-black text-indigo-700 transition-all shadow-xs" title="Gunakan LUNOU AI Auto check-in">
                            <img src="{{ asset('icon/11.png') }}" alt="LUNOU" class="w-5 h-5 object-contain animate-bounce">
                            Auto Check-in AI
                        </button>
                    </div>

                    <!-- Collapsible Auto Check-in Panel -->
                    <div id="lunou-auto-checkin-panel" class="hidden p-5 bg-indigo-50/40 border border-indigo-100 rounded-3xl space-y-3 transition-all duration-300">
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('icon/11.png') }}" alt="LUNOU" class="w-6 h-6 object-contain">
                            <span class="text-xs font-black text-indigo-955 uppercase">Auto Check-in dengan LUNOU AI</span>
                        </div>
                        <p class="text-[10px] text-slate-500 font-bold">Cukup bicarakan atau ketikkan deskripsi perasaanmu (misal: <i>"Hari ini saya senang sekali tapi agak lelah karena kurang tidur tadi malam"</i>), LUNOU akan otomatis memetakan mood dan slider di bawah!</p>
                        
                        <div class="flex items-center gap-2">
                            <!-- Mic Button for Auto checkin voice -->
                            <button type="button" id="autocheckin-mic-btn" onclick="toggleAutoCheckinSpeech()" class="p-2.5 bg-white border border-slate-200 text-slate-400 hover:text-rose-500 rounded-xl transition-all shrink-0" title="Gunakan Suara (Voice Note)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                                </svg>
                            </button>
                            <input type="text" id="autocheckin-text-input" placeholder="Ceritakan perasaanmu hari ini..." 
                                   class="flex-1 px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all"
                                   onkeypress="if(event.key === 'Enter') processAutoCheckin();">
                            <button type="button" id="autocheckin-process-btn" onclick="processAutoCheckin()" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-xl shadow-md transition-all shrink-0">
                                Petakan AI
                            </button>
                        </div>
                        <div id="autocheckin-status" class="hidden text-[10px] font-bold text-indigo-700 mt-1 animate-pulse">LUNOU sedang menganalisis & memetakan perasaanmu...</div>
                    </div>

                    <form method="POST" action="{{ route('user.wellbeing.checkin') }}" class="space-y-5">
                        @csrf
                        
                        <!-- Mood Selector (Text tags instead of emojis) -->
                        <div class="space-y-2">
                            <label class="text-[11px] font-black text-slate-500 uppercase tracking-wider block">Mood Hari Ini</label>
                            <input type="hidden" name="mood" id="input-selected-mood" value="{{ $todayCheckin->mood ?? '' }}">
                            
                            <div class="flex flex-wrap gap-2">
                                @foreach(['Senang', 'Stabil', 'Cemas', 'Lelah', 'Stres'] as $moodTag)
                                    @php
                                        $isMoodActive = (isset($todayCheckin) && $todayCheckin->mood === $moodTag);
                                    @endphp
                                    <button type="button" onclick="selectMood('{{ $moodTag }}')" id="mood-btn-{{ $moodTag }}"
                                            class="mood-btn px-4 py-2 border rounded-xl text-xs font-bold transition-all
                                                 {{ $isMoodActive ? 'bg-indigo-600 border-indigo-600 text-white shadow-xs' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                                        {{ $moodTag }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- 3 Slider Ranges (Energy, Mental Load, Rest) -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label class="text-[11px] font-black text-slate-500 uppercase tracking-wider block">Level Energi</label>
                                <input type="range" name="energy" min="1" max="5" value="{{ $todayCheckin->energy ?? 3 }}" class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
                                <div class="flex justify-between text-[9px] text-slate-400 font-bold uppercase">
                                    <span>Habis</span>
                                    <span>Normal</span>
                                    <span>Penuh</span>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[11px] font-black text-slate-500 uppercase tracking-wider block">Beban Pikiran</label>
                                <input type="range" name="mental_load" min="1" max="5" value="{{ $todayCheckin->mental_load ?? 3 }}" class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
                                <div class="flex justify-between text-[9px] text-slate-400 font-bold uppercase">
                                    <span>Rendah</span>
                                    <span>Sedang</span>
                                    <span>Berat</span>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[11px] font-black text-slate-500 uppercase tracking-wider block">Kualitas Istirahat</label>
                                <input type="range" name="rest_condition" min="1" max="5" value="{{ $todayCheckin->rest_condition ?? 3 }}" class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
                                <div class="flex justify-between text-[9px] text-slate-400 font-bold uppercase">
                                    <span>Kurang</span>
                                    <span>Cukup</span>
                                    <span>Sangat Baik</span>
                                </div>
                            </div>
                        </div>

                        <!-- Short reflection question -->
                        <div class="space-y-2">
                            <label class="text-[11px] font-black text-slate-500 uppercase tracking-wider block">Apa yang paling memenuhi pikiranmu hari ini?</label>
                            <textarea name="thoughts" rows="2" placeholder="Tuliskan unek-unek atau hal yang menyita perhatianmu secara singkat..." 
                                      class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none">{{ $todayCheckin->thoughts ?? '' }}</textarea>
                        </div>

                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-xl shadow-md transition-all">
                            Simpan Check-in Harian
                        </button>
                    </form>
                </div>

                <!-- SUBTAB: AI AUTO JOURNALER -->
                <div id="w-section-auto-journal" class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center border border-indigo-100 shrink-0">
                            <img src="{{ asset('icon/11.png') }}" alt="LUNOU" class="w-7 h-7 object-contain">
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">LUNOU AI Auto-Journaler</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Tuliskan cerita bebas mengenai harimu. LUNOU AI akan otomatis menyimpan dan memetakan datanya ke Jurnal, Target Hidup, Refleksi Mingguan, dan Check-in Harian sekaligus!</p>
                        </div>
                    </div>

                    <!-- Realtime Productivity & Wellbeing Indicators Row -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-2">
                        <!-- Card 1: Task Completion -->
                        <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl space-y-1.5 shadow-xs">
                            <span class="text-[9px] font-black text-indigo-650 uppercase tracking-wider block">Rate Tugas</span>
                            <div class="flex items-baseline gap-1">
                                <span class="text-lg font-black text-slate-800">{{ $completionRate }}%</span>
                                <span class="text-[9px] text-slate-400 font-bold">Selesai</span>
                            </div>
                            <div class="w-full bg-slate-200 h-1 rounded-full overflow-hidden">
                                <div class="bg-indigo-600 h-1 rounded-full" style="width: {{ $completionRate }}%"></div>
                            </div>
                        </div>

                        <!-- Card 2: XP Level Points -->
                        <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl space-y-1.5 shadow-xs">
                            <span class="text-[9px] font-black text-purple-650 uppercase tracking-wider block">Poin Produktivitas</span>
                            <div class="flex items-baseline gap-1">
                                <span class="text-lg font-black text-slate-800">{{ $userPoint->total_points }}</span>
                                <span class="text-[9px] text-purple-500 font-bold">XP (Lv. {{ $userPoint->level }})</span>
                            </div>
                            <div class="text-[8px] text-slate-400 font-bold">Streak: {{ $userPoint->login_streak }} hari</div>
                        </div>

                        <!-- Card 3: Daily Energy -->
                        <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl space-y-1.5 shadow-xs">
                            <span class="text-[9px] font-black text-emerald-600 uppercase tracking-wider block">Level Energi</span>
                            <div class="flex items-baseline gap-1">
                                <span id="ind-energy" class="text-lg font-black text-slate-800">{{ $todayCheckin->energy ?? '-' }}</span>
                                <span class="text-[9px] text-emerald-650 font-bold">/ 5</span>
                            </div>
                            <div class="w-full bg-slate-200 h-1 rounded-full overflow-hidden">
                                <div id="ind-energy-bar" class="bg-emerald-500 h-1 rounded-full" style="width: {{ ($todayCheckin->energy ?? 0) * 20 }}%"></div>
                            </div>
                        </div>

                        <!-- Card 4: Daily Mental Load -->
                        <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl space-y-1.5 shadow-xs">
                            <span class="text-[9px] font-black text-amber-600 uppercase tracking-wider block">Beban Pikiran</span>
                            <div class="flex items-baseline gap-1">
                                <span id="ind-mental" class="text-lg font-black text-slate-800">{{ $todayCheckin->mental_load ?? '-' }}</span>
                                <span class="text-[9px] text-amber-650 font-bold">/ 5</span>
                            </div>
                            <div class="w-full bg-slate-200 h-1 rounded-full overflow-hidden">
                                <div id="ind-mental-bar" class="bg-amber-500 h-1 rounded-full" style="width: {{ ($todayCheckin->mental_load ?? 0) * 20 }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Input Box Card -->
                    <div class="bg-indigo-50/30 border border-indigo-100 rounded-3xl p-6 space-y-4">
                        <div class="space-y-2">
                            <label class="text-[11px] font-black text-indigo-955 uppercase tracking-wider block">Ceritakan harimu di sini (Bicara atau Ketik)</label>
                            <textarea id="master-journal-input" rows="5" placeholder="Contoh: Hari ini saya senang sekali karena berhasil merilis dashboard baru Yoimo (pencapaian). Tapi saya lelah karena begadang tadi malam. Saya bersyukur dibantu tim. Besok saya ingin belajar Laravel dynamic models dan tidur lebih cepat..." 
                                      class="w-full p-4 bg-white border border-slate-200 rounded-2xl text-xs font-bold placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none"></textarea>
                        </div>

                        <div class="flex items-center gap-3">
                            <!-- Mic button -->
                            <button type="button" id="master-journal-mic-btn" onclick="toggleMasterJournalSpeech()" class="p-3 bg-white border border-slate-200 text-slate-400 hover:text-rose-500 rounded-xl transition-all shadow-xs shrink-0" title="Gunakan Suara (Voice to Text)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                                </svg>
                            </button>
                            <!-- Submit button -->
                            <button type="button" id="master-journal-submit-btn" onclick="submitMasterJournal()" class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-xl shadow-md transition-all">
                                Proses & Simpan Otomatis via LUNOU AI
                            </button>
                        </div>
                        <div id="master-journal-status" class="hidden text-xs font-bold text-indigo-700 mt-1 animate-pulse flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-indigo-600 animate-ping"></span>
                            LUNOU sedang menganalisis, membagi data, dan menyimpannya ke seluruh database...
                        </div>
                    </div>

                    <!-- Result Panel Card (fades in when success) -->
                    <div id="master-journal-result" class="hidden bg-emerald-50/30 border border-emerald-100 rounded-3xl p-5 space-y-4">
                        <div class="flex items-center gap-2 text-emerald-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-xs font-black uppercase">Berhasil Disimpan & Dipetakan!</span>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-bold text-slate-700">
                            <!-- Daily Check-in Map -->
                            <div class="bg-white p-4 border border-slate-100 rounded-2xl shadow-xs space-y-2">
                                <span class="text-[9px] font-black text-indigo-600 uppercase tracking-wider block">Daily Check-in Terupdate</span>
                                <p id="mj-res-checkin" class="italic text-slate-600">Mood: -, Energi: -/5, Tidur: -/5</p>
                            </div>
                            
                            <!-- Reflection Journal Map -->
                            <div class="bg-white p-4 border border-slate-100 rounded-2xl shadow-xs space-y-2">
                                <span class="text-[9px] font-black text-purple-600 uppercase tracking-wider block">Jurnal Refleksi Disimpan</span>
                                <p id="mj-res-journal" class="italic text-slate-600">Jurnal harian tersimpan aman.</p>
                            </div>

                            <!-- Goals Map -->
                            <div class="bg-white p-4 border border-slate-100 rounded-2xl shadow-xs space-y-2">
                                <span class="text-[9px] font-black text-emerald-650 uppercase tracking-wider block">Target Hidup Baru</span>
                                <ul id="mj-res-goals" class="list-disc pl-4 space-y-1 text-slate-600 italic">
                                    <li>Tidak ada target baru yang terdeteksi</li>
                                </ul>
                            </div>

                            <!-- Weekly Reflection Map -->
                            <div class="bg-white p-4 border border-slate-100 rounded-2xl shadow-xs space-y-2">
                                <span class="text-[9px] font-black text-amber-600 uppercase tracking-wider block">Refleksi Mingguan Diperbarui</span>
                                <p id="mj-res-reflection" class="italic text-slate-600">Refleksi Mingguan berhasil diupdate.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Daily Journal Timeline Feed -->
                    <div class="space-y-4 pt-6 border-t border-slate-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider">Riwayat Catatan Harian & Analisis AI</h4>
                                <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Semua cerita yang Anda kirimkan terorganisir otomatis di bawah ini.</p>
                            </div>
                            
                            <!-- Timeline Filter Buttons -->
                            <div class="flex gap-1 bg-slate-100 border border-slate-200/60 rounded-xl p-0.5">
                                <button type="button" onclick="filterJournals('all')" id="j-filter-all-btn" class="px-2.5 py-1 text-[10px] font-black rounded-lg transition-all bg-white text-indigo-755 shadow-xs">Semua</button>
                                <button type="button" onclick="filterJournals('weekly')" id="j-filter-weekly-btn" class="px-2.5 py-1 text-[10px] font-black rounded-lg transition-all text-slate-500 hover:text-slate-900">Mingguan</button>
                                <button type="button" onclick="filterJournals('monthly')" id="j-filter-monthly-btn" class="px-2.5 py-1 text-[10px] font-black rounded-lg transition-all text-slate-500 hover:text-slate-900">Bulanan</button>
                            </div>
                        </div>

                        <!-- Journals Feed Wrapper -->
                        <div id="journals-feed-container" class="space-y-4 max-h-[500px] overflow-y-auto pr-1">
                            @forelse($myJournals as $journal)
                                @php
                                    $isThisWeek = $journal->created_at->gte(now()->startOfWeek());
                                    $isThisMonth = $journal->created_at->gte(now()->startOfMonth());
                                @endphp
                                <div class="journal-feed-item bg-slate-50/40 border border-slate-100 rounded-2xl p-4.5 space-y-3 transition-all hover:border-slate-350 hover:bg-slate-50/80" 
                                     data-week="{{ $isThisWeek ? 'true' : 'false' }}" 
                                     data-month="{{ $isThisMonth ? 'true' : 'false' }}">
                                    
                                    <!-- Header: Date & Categories -->
                                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200/50 pb-2">
                                        <span class="text-[10px] font-black text-slate-500">{{ $journal->created_at->isoFormat('dddd, D MMMM Y - HH:mm') }}</span>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($journal->categories ?? ['Pribadi'] as $cat)
                                                <span class="px-2 py-0.5 bg-indigo-50 border border-indigo-100 rounded text-[9px] font-black text-indigo-700 uppercase tracking-tight">{{ $cat }}</span>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Content body -->
                                    <div class="text-xs text-slate-800 font-bold leading-relaxed">
                                        <p>{{ $journal->raw_content ?: $journal->today_event }}</p>
                                    </div>

                                    @if($journal->analysis || $journal->appreciation)
                                        <!-- AI Feedback box -->
                                        <div class="bg-indigo-50/30 border border-indigo-100/60 rounded-xl p-3 space-y-2">
                                            @if($journal->analysis)
                                                <div class="space-y-0.5">
                                                    <span class="text-[8px] font-black text-indigo-650 uppercase tracking-wider block">🧠 LUNOU AI Analisis</span>
                                                    <p class="text-[10px] text-slate-650 font-semibold leading-relaxed italic">"{{ $journal->analysis }}"</p>
                                                </div>
                                            @endif
                                            @if($journal->appreciation)
                                                <div class="space-y-0.5">
                                                    <span class="text-[8px] font-black text-emerald-650 uppercase tracking-wider block">🌟 LUNOU Apresiasi</span>
                                                    <p class="text-[10px] text-slate-650 font-semibold leading-relaxed italic">"{{ $journal->appreciation }}"</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div id="no-journals-placeholder" class="border border-dashed border-slate-200 rounded-2xl p-6 text-center text-xs text-slate-400 bg-slate-50/50">
                                    Belum ada catatan harian. Tuliskan ceritamu di atas!
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>

                <!-- SUBTAB 2: PERSONAL JOURNAL -->
                <div id="w-section-journal" class="hidden bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Personal Recovery Journal</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Tuliskan refleksi hatimu di sini. Laporan jurnal ini dijamin 100% rahasia untukmu sendiri.</p>
                    </div>

                    <form method="POST" action="{{ route('user.wellbeing.journal') }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Apa yang saya rasakan saat ini?</label>
                                <textarea name="feeling" rows="3" placeholder="Gelisah, tenang, lelah, bersemangat..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"></textarea>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Apa yang terjadi hari ini?</label>
                                <textarea name="today_event" rows="3" placeholder="Tulis kejadian penting atau interaksi bermakna hari ini..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"></textarea>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Hal apa yang paling saya syukuri hari ini?</label>
                                <textarea name="gratitude" rows="3" placeholder="Sebutkan hal-hal kecil yang membuatmu bersyukur..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"></textarea>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Apa hal/beban pikiran yang ingin saya lepaskan?</label>
                                <textarea name="let_go" rows="3" placeholder="Hal-hal di luar kontrolmu yang ingin kamu ikhlaskan..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"></textarea>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Apa rencana perbaikan atau adaptasi ritme kerja saya besok?</label>
                            <textarea name="improvement" rows="2" placeholder="Mengatur istirahat, fokus ke 1 tugas utama, membatasi jam kerja..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"></textarea>
                        </div>

                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-xl shadow-md transition-all">
                            Simpan Jurnal Refleksi
                        </button>
                    </form>
                </div>

                <!-- SUBTAB 3: RECOVERY SPACE (Interactive exercises) -->
                <div id="w-section-recovery" class="hidden bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Recovery & Relaxation Lounge</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Ambil jeda 5 menit untuk mengatur napas, meluruskan pundak, dan menyegarkan pikiranmu.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Breathing Simulator -->
                        <div class="flex flex-col items-center justify-center p-6 bg-slate-50 border border-slate-150 rounded-3xl text-center space-y-5">
                            <h4 class="text-[10px] font-black uppercase tracking-wider text-slate-550">Latihan Relaksasi Pernapasan (4-4-4)</h4>
                            <div class="relative w-36 h-36 flex items-center justify-center">
                                <div id="breath-circle" class="w-20 h-20 bg-indigo-100 border-4 border-indigo-200 rounded-full flex items-center justify-center transition-all duration-1000 ease-in-out">
                                    <span id="breath-text" class="text-xs font-black text-indigo-900">Rileks</span>
                                </div>
                            </div>
                            <button type="button" id="breath-btn" onclick="toggleBreathingExercise()" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-xl shadow-md transition-all">
                                Mulai Latihan Napas
                            </button>
                        </div>

                        <!-- Browser Ambient Synthesizer & Workload Peringatan -->
                        <div class="space-y-4 flex flex-col justify-between">
                            
                            <!-- Workload Burnout Warning widget -->
                            <div class="p-4 bg-amber-50 border border-amber-100 rounded-2xl space-y-2">
                                <span class="text-[9px] font-black text-amber-700 uppercase block tracking-wider">WORKLOAD & BURNOUT EVALUATOR</span>
                                <p class="text-xs text-slate-700 leading-relaxed font-semibold">
                                    {{ $workloadAdvice }}
                                </p>
                            </div>

                            <!-- Ambient Synthesizer -->
                            <div class="p-4 bg-indigo-50/50 border border-indigo-100 rounded-2xl text-center space-y-3">
                                <h5 class="text-xs font-bold text-indigo-950">Ambient Rain Sound Generator</h5>
                                <p class="text-[10px] text-slate-500 leading-relaxed">Gunakan browser synthesiser untuk membunyikan suara deru ombak / hujan yang merelaksasi syaraf kepala.</p>
                                <button type="button" id="ambient-btn" onclick="toggleAmbientSound()" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-xl shadow-md transition-all">
                                    Putar Suara Hujan
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- SUBTAB 4: PERSONAL GOALS -->
                <div id="w-section-goals" class="hidden bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Personal Life Goals</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Karena Anda bukan sekadar unit sumber daya kerja, mari pelihara mimpi pribadimu.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                        
                        <!-- Target list -->
                        <div class="md:col-span-7 space-y-3">
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Daftar Target Pribadi</h4>
                            
                            <div class="space-y-2">
                                @forelse($personalGoals as $goal)
                                    <div class="p-3 bg-slate-50 border border-slate-200/50 rounded-2xl flex items-center justify-between gap-3 text-xs">
                                        <div class="flex items-center gap-2.5 truncate">
                                            <form method="POST" action="{{ route('user.wellbeing.toggle-goal', $goal->id) }}">
                                                @csrf
                                                <button type="submit" class="w-5 h-5 rounded-lg border border-slate-300 bg-white flex items-center justify-center text-emerald-600 hover:bg-slate-50">
                                                    @if($goal->is_completed)
                                                        &check;
                                                    @endif
                                                </button>
                                            </form>
                                            <span class="font-bold truncate {{ $goal->is_completed ? 'line-through text-slate-400 font-medium' : 'text-slate-800' }}">
                                                {{ $goal->title }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 text-[9px] font-black uppercase">{{ $goal->category }}</span>
                                            <form method="POST" action="{{ route('user.wellbeing.destroy-goal', $goal->id) }}" onsubmit="return confirm('Hapus target?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-500 hover:text-rose-700 font-bold px-1">&times;</button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400 italic">Belum ada target hidup terdaftar. Tambahkan satu untuk memulai hidup lebih seimbang!</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Add goal form -->
                        <div class="md:col-span-5 bg-slate-50 p-5 border border-slate-200/60 rounded-3xl space-y-4">
                            <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-wider block border-b border-slate-200/60 pb-1.5">Tambah Target Hidup</h4>
                            <form method="POST" action="{{ route('user.wellbeing.goal') }}" class="space-y-3.5">
                                @csrf
                                <div class="space-y-1">
                                    <label class="text-[9px] font-bold text-slate-500 uppercase">Nama Target</label>
                                    <input type="text" name="title" required placeholder="Contoh: Baca buku 15 menit" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[9px] font-bold text-slate-500 uppercase">Kategori</label>
                                    <select name="category" required class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                                        <option value="Belajar">Belajar / Skill</option>
                                        <option value="Membaca">Membaca</option>
                                        <option value="Olahraga">Olahraga / Kesehatan</option>
                                        <option value="Istirahat">Istirahat / Relaksasi</option>
                                        <option value="Family time">Hubungan / Keluarga</option>
                                        <option value="Personal project">Karya Pribadi</option>
                                    </select>
                                </div>
                                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-xl shadow-md transition-all">
                                    Tambahkan Target
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- SUBTAB 5: WEEKLY REFLECTION -->
                <div id="w-section-reflection" class="hidden bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Evaluasi Akhir Pekan (Weekly Summary)</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Tinjau ritme kehidupan kerjamu seminggu terakhir agar minggu depan berjalan lebih tenang.</p>
                    </div>

                    <form method="POST" action="{{ route('user.wellbeing.reflection') }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Apa hal yang berjalan baik di minggu ini?</label>
                                <textarea name="what_went_well" rows="3" placeholder="Pencapaian, kemajuan tugas, atau kesepahaman tim..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">{{ $weeklyReflection->what_went_well ?? '' }}</textarea>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Apa hal yang cukup melelahkan/menyita energi?</label>
                                <textarea name="what_was_exhausting" rows="3" placeholder="Rapat terlalu panjang, tugas menumpuk, kurang tidur..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">{{ $weeklyReflection->what_was_exhausting ?? '' }}</textarea>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Apa satu hal penting yang ingin diubah minggu depan?</label>
                                <textarea name="what_to_change" rows="3" placeholder="Mengurangi multitasking, tidur lebih teratur, lari sore..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">{{ $weeklyReflection->what_to_change ?? '' }}</textarea>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Apa hal yang paling membuatmu bangga minggu ini?</label>
                                <textarea name="what_proud_of" rows="3" placeholder="Menolong rekan, menyelesaikan modul rumit, atau bisa pulang tepat waktu..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">{{ $weeklyReflection->what_proud_of ?? '' }}</textarea>
                            </div>
                        </div>

                        @if(isset($weeklyReflection) && $weeklyReflection->summary)
                            <div class="p-4 bg-indigo-50/50 border border-indigo-100 rounded-2xl space-y-1.5">
                                <span class="text-[9px] font-black text-indigo-700 uppercase block tracking-wider">AUTO PERSONAL SUMMARY</span>
                                <p class="text-xs text-slate-700 leading-relaxed font-semibold italic">
                                    "{{ $weeklyReflection->summary }}"
                                </p>
                            </div>
                        @endif

                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-xl shadow-md transition-all">
                            Simpan Refleksi & Buat Summary
                        </button>
                    </form>
                </div>

                <!-- SUBTAB 6: LEAVE IT HERE -->
                <div id="w-section-leave" class="hidden bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">"Leave It Here" (Lepaskan Beban Pikiran)</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Sebelum keluar dan mematikan laptop, tinggalkan kekhawatiran atau beban kerjamu hari ini di sini agar tidurmu nyenyak.</p>
                    </div>

                    <form method="POST" action="{{ route('user.wellbeing.left-thought') }}" class="space-y-4">
                        @csrf
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Tuliskan beban pikiran yang ingin dilepaskan hari ini</label>
                            <textarea name="thought" rows="3" placeholder="Contoh: Khawatir dengan bug server, sungkan dengan atasan, atau rasa lelah berlebih..." 
                                      class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"></textarea>
                        </div>

                        <div class="bg-indigo-50/50 p-4 border border-indigo-150 rounded-2xl text-left space-y-1">
                            <p class="text-xs font-black text-indigo-855 leading-relaxed">
                                "Tugas hari ini telah selesai secukupnya. Tarik napas, tenangkan dirimu. Besok kita mulai lagi."
                            </p>
                        </div>

                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-xl shadow-md transition-all">
                            Lepaskan & Simpan Pikiran
                        </button>
                    </form>
                </div>

                <!-- SUBTAB 7: AI BALANCE REPORT WITH MASCOT ANIMATION -->
                <div id="w-section-balance" class="hidden bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Laporan Keseimbangan Kerja & Emosi (LUNOU AI)</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Analisis hubungan produktivitas kerja dan kondisi kesehatan mental Anda secara mingguan & bulanan secara privat.</p>
                    </div>

                    <!-- Trigger Zone & Flying Mascot Envelope Animation -->
                    <div id="balance-trigger-zone" class="p-6 bg-slate-50 border border-dashed border-slate-200 rounded-3xl text-center space-y-4">
                        <div class="relative h-28 flex items-center justify-center overflow-hidden">
                            <!-- Flying Mascot Image -->
                            <img id="delivery-mascot-img" src="{{ asset('icon/11.png') }}" alt="Mascot" class="w-20 h-20 object-contain">
                        </div>
                        <p id="delivery-status-text" class="text-xs text-slate-500 font-bold">LUNOU siap menganalisis data aktivitas dan check-in mingguan/bulanan Anda secara privat.</p>
                        <button type="button" id="balance-request-btn" onclick="requestAIBalanceReport()" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-xl shadow-md transition-all">
                            Minta Laporan Keseimbangan
                        </button>
                    </div>

                    <!-- Report Results Card (Fades in) -->
                    <div id="balance-report-card" class="hidden space-y-6 transition-all duration-500 opacity-0 transform translate-y-4">
                        
                        <!-- Weekly Report Card -->
                        <div class="bg-indigo-50/40 border border-indigo-100 rounded-3xl p-5 space-y-4">
                            <div class="flex items-center justify-between border-b border-indigo-100/60 pb-2">
                                <span class="text-xs font-black text-indigo-955 uppercase">Laporan Keseimbangan Mingguan</span>
                                <span class="text-[10px] bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded font-bold uppercase">7 Hari Terakhir</span>
                            </div>
                            
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                                <div class="bg-white p-3 rounded-2xl border border-slate-100 shadow-xs">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase block">Tugas Selesai</span>
                                    <span id="rep-week-completed" class="text-base font-black text-slate-800 mt-1 block">0 / 0</span>
                                </div>
                                <div class="bg-white p-3 rounded-2xl border border-slate-100 shadow-xs">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase block">Rata-rata Energi</span>
                                    <span id="rep-week-energy" class="text-base font-black text-indigo-600 mt-1 block">0 / 5</span>
                                </div>
                                <div class="bg-white p-3 rounded-2xl border border-slate-100 shadow-xs">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase block">Beban Mental</span>
                                    <span id="rep-week-mental" class="text-base font-black text-purple-600 mt-1 block">0 / 5</span>
                                </div>
                                <div class="bg-white p-3 rounded-2xl border border-slate-100 shadow-xs">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase block">Mood Dominan</span>
                                    <span id="rep-week-mood" class="text-xs font-black text-emerald-600 mt-1.5 block">Stabil</span>
                                </div>
                            </div>

                            <div class="bg-white p-4 border border-indigo-100 rounded-2xl space-y-1.5 shadow-xs">
                                <span class="text-[9px] font-black text-indigo-700 uppercase block tracking-wider">LUNOU AI ANALYSIS (WEEKLY)</span>
                                <p id="rep-week-analysis" class="text-xs text-slate-700 leading-relaxed font-semibold italic">
                                    Menganalisis...
                                </p>
                            </div>
                        </div>

                        <!-- Monthly Report Card -->
                        <div class="bg-purple-50/30 border border-purple-100 rounded-3xl p-5 space-y-4">
                            <div class="flex items-center justify-between border-b border-purple-100/60 pb-2">
                                <span class="text-xs font-black text-purple-950 uppercase">Laporan Keseimbangan Bulanan</span>
                                <span class="text-[10px] bg-purple-100 text-purple-800 px-2 py-0.5 rounded font-bold uppercase">30 Hari Terakhir</span>
                            </div>
                            
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                                <div class="bg-white p-3 rounded-2xl border border-slate-100 shadow-xs">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase block">Tugas Selesai</span>
                                    <span id="rep-month-completed" class="text-base font-black text-slate-800 mt-1 block">0 / 0</span>
                                </div>
                                <div class="bg-white p-3 rounded-2xl border border-slate-100 shadow-xs">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase block">Rata-rata Energi</span>
                                    <span id="rep-month-energy" class="text-base font-black text-indigo-600 mt-1 block">0 / 5</span>
                                </div>
                                <div class="bg-white p-3 rounded-2xl border border-slate-100 shadow-xs">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase block">Beban Mental</span>
                                    <span id="rep-month-mental" class="text-base font-black text-purple-600 mt-1 block">0 / 5</span>
                                </div>
                                <div class="bg-white p-3 rounded-2xl border border-slate-100 shadow-xs">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase block">Mood Dominan</span>
                                    <span id="rep-month-mood" class="text-xs font-black text-emerald-600 mt-1.5 block">Stabil</span>
                                </div>
                            </div>

                            <div class="bg-white p-4 border border-purple-150 rounded-2xl space-y-1.5 shadow-xs">
                                <span class="text-[9px] font-black text-purple-700 uppercase block tracking-wider">LUNOU AI ANALYSIS (MONTHLY)</span>
                                <p id="rep-month-analysis" class="text-xs text-slate-700 leading-relaxed font-semibold italic">
                                    Menganalisis...
                                </p>
                            </div>
                        </div>

                        <!-- Action Recommendations Card -->
                        <div class="bg-emerald-50/40 border border-emerald-100 rounded-3xl p-5 space-y-3">
                            <div class="flex items-center justify-between border-b border-emerald-100/60 pb-2">
                                <span class="text-xs font-black text-emerald-955 uppercase">Rekomendasi Tindakan Pemulihan</span>
                                <span class="text-[10px] bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded font-bold uppercase">Saran LUNOU</span>
                            </div>
                            
                            <ul id="rep-recommendations-list" class="space-y-2 text-xs text-slate-700 font-semibold list-disc pl-5 leading-relaxed">
                                <li>Menganalisis data...</li>
                            </ul>
                        </div>

                        <!-- Motivation Card -->
                        <div class="bg-amber-50/50 border border-amber-100 rounded-3xl p-5 space-y-2">
                            <span class="text-[9px] font-black text-amber-700 uppercase block tracking-wider">KATA MOTIVASI & PENYEMANGAT</span>
                            <p id="rep-motivation-text" class="text-xs text-slate-750 font-bold italic leading-relaxed">
                                Menganalisis...
                            </p>
                        </div>

                        <!-- Discussion Mode Card -->
                        <div class="bg-indigo-50/30 border border-indigo-150 rounded-3xl p-6 space-y-4">
                            <div class="flex items-center justify-between border-b border-indigo-100 pb-2">
                                <span class="text-xs font-black text-indigo-955 uppercase">Diskusi & Konseling LUNOU AI</span>
                                <span class="text-[10px] bg-indigo-600 text-white px-2.5 py-0.5 rounded-full font-bold uppercase">Konseling Mode</span>
                            </div>
                            
                            <!-- Discussion Chat Area -->
                            <div id="wellbeing-discussion-chat" class="h-48 overflow-y-auto space-y-3 bg-slate-50/50 border border-indigo-50/50 rounded-2xl p-4">
                                <div class="flex items-start gap-2 justify-start">
                                    <img src="/icon/11.png" alt="LUNOU" class="w-6 h-6 object-contain shrink-0 mt-0.5">
                                    <div class="max-w-[85%] bg-white border border-slate-100 rounded-2xl rounded-tl-none p-3 shadow-xs">
                                        <p class="text-[11px] text-slate-700 leading-relaxed font-semibold">Halo! Aku LUNOU. Laporan keseimbangan mingguan dan bulananmu sudah siap di atas. Apakah ada hal spesifik tentang energimu, tingkat stres, atau beban kerjamu yang ingin kamu diskusikan denganku hari ini?</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Discussion Input Footer -->
                            <div class="flex items-center gap-2">
                                <button type="button" id="wellbeing-mic-btn" onclick="toggleWellbeingSpeechRecognition()" class="p-2.5 bg-slate-50 border border-slate-200 text-slate-400 hover:text-rose-500 rounded-xl transition-all shrink-0" title="Gunakan Suara (Voice Note)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                                    </svg>
                                </button>
                                <input type="text" id="wellbeing-discussion-input" placeholder="Tanyakan saran atau cara mengatasi stres..." 
                                       class="flex-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all"
                                       onkeypress="if(event.key === 'Enter') sendWellbeingDiscussion();">
                                <button type="button" onclick="sendWellbeingDiscussion()" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md transition-all shrink-0">
                                    Kirim
                                </button>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- SUBTAB 8: GAMIFIKASI & PENCAPAIAN -->
                <div id="w-section-gamification" class="hidden space-y-6">
                    <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
                        <div>
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Poin & Pencapaian</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Pantau tingkat level, akumulasi poin per perusahaan/proyek, dan kumpulkan penghargaan.</p>
                        </div>

                        <!-- 3 Cards Metrik Ringkasan -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="p-4 bg-slate-50 border border-slate-150 rounded-2xl text-center">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Total Poin</span>
                                <div class="text-2xl font-black text-slate-800 mt-1">{{ $userPoint->total_points }} XP</div>
                            </div>
                            <div class="p-4 bg-slate-50 border border-slate-150 rounded-2xl text-center">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Level Anda</span>
                                <div class="text-2xl font-black text-indigo-650 mt-1">Level {{ $userPoint->level }}</div>
                            </div>
                            <div class="p-4 bg-slate-50 border border-slate-150 rounded-2xl text-center">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Streak Login</span>
                                <div class="text-2xl font-black text-emerald-650 mt-1">{{ $userPoint->login_streak }} Hari</div>
                            </div>
                        </div>

                        <!-- Grid Akumulasi Company & Project -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Akumulasi per Perusahaan -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-black text-slate-500 uppercase tracking-wider">Poin per Perusahaan</h4>
                                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 space-y-2.5 max-h-60 overflow-y-auto">
                                    @forelse($companyPoints as $cp)
                                        <div class="flex items-center justify-between text-xs border-b border-slate-200/50 pb-2 last:border-0 last:pb-0">
                                            <span class="font-bold text-slate-850">{{ $cp->company->company_name ?? 'Perusahaan' }}</span>
                                            <span class="px-2.5 py-1 bg-indigo-50 border border-indigo-100 rounded-lg font-black text-indigo-700">+{{ $cp->total }} XP</span>
                                        </div>
                                    @empty
                                        <p class="text-xs text-slate-400 italic">Belum ada poin terakumulasi di perusahaan.</p>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Akumulasi per Proyek -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-black text-slate-500 uppercase tracking-wider">Poin per Proyek</h4>
                                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 space-y-2.5 max-h-60 overflow-y-auto">
                                    @forelse($projectPoints as $pp)
                                        <div class="flex items-center justify-between text-xs border-b border-slate-200/50 pb-2 last:border-0 last:pb-0">
                                            <span class="font-bold text-slate-850">{{ $pp->project->name ?? 'Proyek' }}</span>
                                            <span class="px-2.5 py-1 bg-emerald-50 border border-emerald-100 rounded-lg font-black text-emerald-700">+{{ $pp->total }} XP</span>
                                        </div>
                                    @empty
                                        <p class="text-xs text-slate-400 italic">Belum ada poin terakumulasi di proyek.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Daftar Badge Penghargaan "Si Paling" -->
                        <div class="space-y-3 pt-2">
                            <h4 class="text-xs font-black text-slate-500 uppercase tracking-wider">Penghargaan Lencana Anda</h4>
                            @if($awards->count() === 0)
                                <div class="border border-dashed border-slate-200 rounded-2xl p-6 text-center text-xs text-slate-400 bg-slate-50/50">
                                    Anda belum mengumpulkan lencana. Terus selesaikan tugas tepat waktu dan jaga rutinitas login Anda!
                                </div>
                            @else
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    @foreach($awards as $aw)
                                        <div class="p-5 bg-gradient-to-br from-indigo-50/50 to-purple-50/30 border border-indigo-100 rounded-2xl flex flex-col justify-between gap-4 shadow-xs">
                                            <div class="space-y-1">
                                                <span class="text-[9px] font-bold text-indigo-600 bg-indigo-100/60 px-2 py-0.5 rounded uppercase tracking-wider">
                                                    Yoimo Achievement
                                                </span>
                                                <h5 class="text-sm font-black text-slate-900 mt-1 uppercase">{{ $aw->title }}</h5>
                                                <p class="text-[10px] text-slate-400 font-semibold">Diperoleh pada {{ $aw->issued_date->format('d M Y') }}</p>
                                            </div>
                                            
                                            <!-- Share Links -->
                                            <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                                                <a href="{{ route('public.award.show', $aw->share_token) }}" target="_blank" 
                                                   class="flex-1 text-center py-1.5 bg-slate-900 hover:bg-black text-white text-[10px] font-bold rounded-lg transition-all">
                                                    Sertifikat Publik
                                                </a>
                                                
                                                @php
                                                    $awardUrl = route('public.award.show', $aw->share_token);
                                                    $awardCleanTitle = addslashes($aw->title);
                                                    $awardDateStr = $aw->issued_date ? $aw->issued_date->format('d F Y') : date('d F Y');
                                                    $awardCaption = "🏆 Penghargaan Resmi Diraih di Yoimo Workspace!\n\nSaya bangga menerima penghargaan: \"{$awardCleanTitle}\"\n📅 Tanggal: {$awardDateStr}\n✨ Diberikan atas dedikasi kerja dan ritme produktivitas yang seimbang.\n\nVerifikasi sertifikat digital resmi:\n{$awardUrl}\n\n#YoimoWorkspace #Award #Achievement #WorkLifeBalance #Recognition #ProfessionalGrowth";
                                                @endphp
                                                <button type="button" 
                                                   onclick="openLinkedInShareModal('Penghargaan: {{ $awardCleanTitle }}', 'Sertifikat Resmi Yoimo', '{{ $awardUrl }}', `{{ $awardCaption }}`)"
                                                   class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-bold rounded-lg transition-all flex items-center gap-1 group"
                                                   title="Bagikan sertifikat ke LinkedIn">
                                                    <svg class="w-3 h-3 fill-current opacity-90 group-hover:opacity-100" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>
                                                    <span>Share LinkedIn</span>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Riwayat Point Log -->
                        <div class="space-y-3 pt-2">
                            <h4 class="text-xs font-black text-slate-500 uppercase tracking-wider">Riwayat Aktivitas & Poin Terkini</h4>
                            <div class="bg-white border border-slate-150 rounded-2xl overflow-hidden shadow-xs">
                                <table class="w-full text-xs text-left">
                                    <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase border-b border-slate-150">
                                        <tr>
                                            <th class="px-4 py-2.5">Aktivitas</th>
                                            <th class="px-4 py-2.5">Sumber</th>
                                            <th class="px-4 py-2.5 text-right">Poin</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($pointLogs as $log)
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <span class="font-bold text-slate-800">{{ $log->description }}</span>
                                                    <span class="text-[9px] text-slate-400 block mt-0.5">{{ $log->created_at->diffForHumans() }}</span>
                                                </td>
                                                <td class="px-4 py-3 font-semibold text-slate-500">
                                                    @if($log->project)
                                                        {{ $log->project->name }}
                                                    @elseif($log->company)
                                                        {{ $log->company->company_name }}
                                                    @else
                                                        Sistem Global
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-right font-black {{ $log->points >= 0 ? 'text-emerald-600' : 'text-rose-650' }}">
                                                    {{ $log->points >= 0 ? '+' : '' }}{{ $log->points }} XP
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-4 py-6 text-center text-slate-400 italic">Belum ada riwayat aktivitas koin.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- SUBTAB 9: TEMAN CERITA LUNOU -->
                <div id="w-section-mental-friend" class="hidden bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Teman Cerita LUNOU</h3>
                        <p class="text-xs text-slate-400 mt-0.5 font-semibold">LUNOU hadir sebagai teman cerita, pendengar curhat, dan pendukung pemulihan mental Anda. Ceritakan apa saja!</p>
                    </div>

                    <!-- Shortcut prompts cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <button type="button" onclick="sendMentalFriendShortcut('Beri saya kata-kata motivasi harian untuk membakar semangat!')" class="p-3.5 bg-indigo-50/50 hover:bg-indigo-50 border border-indigo-150 rounded-2xl text-left transition-all cursor-pointer">
                            <span class="text-xs font-black text-indigo-700 block">Motivasi Harian</span>
                            <span class="text-[10px] text-slate-500 font-semibold block mt-1 leading-normal">Dapatkan suntikan semangat positif dari LUNOU.</span>
                        </button>
                        <button type="button" onclick="sendMentalFriendShortcut('Beri saya candaan lucu atau lelucon AI yang bisa menghibur hari melelahkan ini!')" class="p-3.5 bg-emerald-50/50 hover:bg-emerald-50 border border-emerald-150 rounded-2xl text-left transition-all cursor-pointer">
                            <span class="text-xs font-black text-emerald-700 block">Lelucon Lucu AI</span>
                            <span class="text-[10px] text-slate-500 font-semibold block mt-1 leading-normal">Hiburan humor ringan buatan LUNOU untuk tersenyum.</span>
                        </button>
                        <button type="button" onclick="sendMentalFriendShortcut('Aku merasa lelah dan stres hari ini, tolong beri aku kata-kata hangat yang menenangkan pikiran.')" class="p-3.5 bg-amber-50/50 hover:bg-amber-50 border border-amber-150 rounded-2xl text-left transition-all cursor-pointer">
                            <span class="text-xs font-black text-amber-700 block">Kata-Kata Hangat</span>
                            <span class="text-[10px] text-slate-500 font-semibold block mt-1 leading-normal">Pesan tulus penyejuk hati yang lelah bekerja.</span>
                        </button>
                    </div>

                    <!-- Chat Box Container -->
                    <div class="border border-slate-100 rounded-3xl p-5 bg-slate-50/30 space-y-4">
                        <div id="mental-friend-chat-feed" class="h-64 overflow-y-auto space-y-3 bg-white border border-slate-100 rounded-2xl p-4 shadow-inner">
                            <div class="flex items-start gap-2">
                                <img src="/icon/11.png" alt="LUNOU" class="w-6 h-6 object-contain shrink-0 mt-0.5">
                                <div class="max-w-[85%] bg-slate-50 border border-slate-100 rounded-2xl rounded-tl-none p-3 shadow-xs">
                                    <p class="text-[11px] text-slate-700 leading-relaxed font-semibold">Halo Sahabat! Aku LUNOU, teman cerita dan pemulihan mentalmu. Bagikan perasaanmu, kekhawatiranmu, atau tantangan yang sedang kamu hadapi hari ini. Aku di sini untuk mendengarkan tanpa menghakimi.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Chat inputs -->
                        <div class="flex items-center gap-2">
                            <input type="text" id="mental-friend-chat-input" placeholder="Tulis ceritamu di sini..." onkeypress="if(event.key === 'Enter') sendMentalFriendMessage();" class="flex-1 px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                            <button type="button" onclick="sendMentalFriendMessage()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md transition-all cursor-pointer">Kirim</button>
                        </div>
                    </div>
                </div>

                <!-- SUBTAB 10: GAME KETENANGAN -->
                <div id="w-section-mental-game" class="hidden bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Ruang Game & Asah Otak</h3>
                            <p class="text-xs text-slate-400 mt-0.5 font-semibold">Mainkan game ketenangan dan kuis logika untuk melepas stres serta menyegarkan pikiran.</p>
                        </div>
                    </div>

                    <!-- Inner game navigation -->
                    <div class="flex flex-wrap border-b border-slate-200 gap-4 text-xs font-bold text-slate-500 pb-1">
                        <button type="button" onclick="switchInnerGame('balloon')" id="inner-game-balloon-btn" class="pb-2 border-b-2 border-indigo-600 text-indigo-700 font-black cursor-pointer">
                            Zen Balloon Popper
                        </button>
                        <button type="button" onclick="switchInnerGame('sudoku')" id="inner-game-sudoku-btn" class="pb-2 border-b-2 border-transparent hover:text-slate-800 cursor-pointer">
                            Zen Sudoku 9x9
                        </button>
                        <button type="button" onclick="switchInnerGame('logic')" id="inner-game-logic-btn" class="pb-2 border-b-2 border-transparent hover:text-slate-800 cursor-pointer">
                            Logic Quiz Hub
                        </button>
                        <button type="button" onclick="switchInnerGame('chess')" id="inner-game-chess-btn" class="pb-2 border-b-2 border-transparent hover:text-slate-800 cursor-pointer">
                            LUNOU Chess
                        </button>
                        <button type="button" onclick="switchInnerGame('strategy')" id="inner-game-strategy-btn" class="pb-2 border-b-2 border-transparent hover:text-slate-800 cursor-pointer">
                            2048 Strategy
                        </button>
                    </div>

                    <!-- GAME 1: Balloon Popper -->
                    <div id="inner-game-balloon" class="space-y-4">
                        <div class="p-6 bg-gradient-to-br from-indigo-950 to-slate-900 border border-indigo-900 rounded-3xl text-center space-y-6 relative overflow-hidden h-[360px] flex flex-col justify-between select-none">
                            <div class="flex items-center justify-between text-white border-b border-white/10 pb-3 z-10">
                                <span class="text-[10px] font-black uppercase tracking-wider text-indigo-300">Stress Relieved</span>
                                <span id="zen-points-display" class="px-3 py-1 bg-white/10 border border-white/20 rounded-lg text-xs font-black text-emerald-400">+0 XP</span>
                            </div>
                            <div id="balloon-arena" class="relative flex-1 w-full overflow-hidden">
                                <div class="absolute inset-0 flex items-center justify-center" id="game-start-layer">
                                    <button type="button" onclick="startZenGame()" class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-xs font-black rounded-2xl shadow-lg hover:from-emerald-600 hover:to-teal-600 transition-all cursor-pointer">
                                        Mulai Game Ketenangan
                                    </button>
                                </div>
                            </div>
                            <div class="text-[10px] text-indigo-200/60 font-semibold z-10">
                                Setiap balon yang dipecahkan menyimulasikan pelepasan hormon kortisol stres di otak.
                            </div>
                        </div>
                    </div>

                    <!-- GAME 2: Zen Sudoku -->
                    <div id="inner-game-sudoku" class="hidden space-y-4">
                        <div class="p-6 bg-slate-50 border border-slate-200/60 rounded-3xl flex flex-col items-center justify-between gap-6">
                            <!-- Header info -->
                            <div class="text-center space-y-2">
                                <span class="text-[9px] font-black text-indigo-700 bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded uppercase tracking-wider block mx-auto w-max">Fokus & Logika</span>
                                <h4 class="text-sm font-black text-slate-850 uppercase">Zen Sudoku 9x9</h4>
                                <p class="text-xs text-slate-500 max-w-xl font-semibold leading-relaxed">Isi setiap baris, kolom, dan blok 3x3 dengan angka 1-9 tanpa pengulangan. Selesaikan papan Sudoku ini dengan benar untuk mendapatkan +20 XP!</p>
                                <button type="button" onclick="generateSudokuBoard()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md transition-all cursor-pointer">Buat Papan Baru</button>
                            </div>

                            <!-- Sudoku 9x9 grid -->
                            <div class="flex flex-col items-center gap-3">
                                <div id="sudoku-grid-container" class="grid grid-cols-9 gap-0.5 p-2 bg-white border border-slate-200 rounded-2xl shadow-sm w-max">
                                    <!-- Dynamic 9x9 inputs -->
                                </div>
                                <div class="flex items-center gap-2 mt-2 w-full justify-center">
                                    <button type="button" onclick="verifySudoku()" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md transition-all cursor-pointer">Verifikasi Jawaban</button>
                                </div>
                                <div id="sudoku-feedback-msg" class="text-xs font-bold text-center hidden"></div>
                            </div>
                        </div>
                    </div>

                    <!-- GAME 3: Logic Quiz Hub -->
                    <div id="inner-game-logic" class="hidden space-y-4">
                        <div class="p-6 bg-slate-50 border border-slate-200/60 rounded-3xl space-y-5">
                            <div class="flex items-center justify-between border-b border-slate-200/60 pb-3">
                                <div class="space-y-0.5">
                                    <h4 class="text-xs font-black text-slate-850 uppercase">Logic Quiz Hub</h4>
                                    <p class="text-[10px] text-slate-400 font-semibold">Tebak jawaban yang benar dari teka-teki logika LUNOU AI untuk mendapatkan +15 XP!</p>
                                </div>
                                <button type="button" onclick="generateLogicQuiz()" id="logic-quiz-reload-btn" class="px-3.5 py-1.5 bg-indigo-650 hover:bg-indigo-700 text-white text-[10px] font-bold rounded-xl shadow cursor-pointer font-sans">
                                    Ambil Teka-Teki Logika
                                </button>
                            </div>

                            <!-- Quiz Content Area -->
                            <div id="logic-quiz-body" class="space-y-4 hidden">
                                <div class="p-4 bg-white border border-slate-150 rounded-2xl">
                                    <p id="logic-quiz-question" class="text-xs text-slate-800 font-black leading-relaxed"></p>
                                </div>

                                <!-- Multiple choice options -->
                                <div id="logic-quiz-options" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <!-- Dynamic option buttons -->
                                </div>

                                <!-- Result feedback & explanation -->
                                <div id="logic-quiz-result-panel" class="p-4 rounded-2xl hidden space-y-2">
                                    <span id="logic-quiz-status-title" class="text-xs font-black uppercase block"></span>
                                    <p id="logic-quiz-explanation" class="text-xs text-slate-650 font-semibold leading-relaxed"></p>
                                </div>
                            </div>

                            <!-- Initial loader card -->
                            <div id="logic-quiz-initial-msg" class="p-8 border border-dashed border-slate-200 bg-white/50 rounded-2xl text-center text-xs text-slate-400 italic">
                                Klik tombol di atas untuk meminta LUNOU AI merancang soal teka-teki logika untuk Anda.
                            </div>
                        </div>
                    </div>

                    <!-- GAME 4: Chess Game -->
                    <div id="inner-game-chess" class="hidden space-y-4">
                        <div class="p-6 bg-slate-50 border border-slate-200/60 rounded-3xl flex flex-col lg:flex-row gap-6 justify-between items-center">
                            <!-- Chess board description -->
                            <div class="space-y-4 max-w-sm">
                                <span class="text-[9px] font-black text-indigo-700 bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded uppercase tracking-wider block w-max">Taktik & Strategi</span>
                                <h4 class="text-sm font-black text-slate-850 uppercase">LUNOU Chess</h4>
                                <p class="text-xs text-slate-500 leading-relaxed font-semibold">Tantang LUNOU AI dalam permainan catur taktis. Anda memainkan bidak Putih (Light Indigo), dan AI memainkan bidak Hitam (Slate). Kalahkan AI dengan menangkap King lawan untuk memenangkan +30 XP!</p>
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="initializeChessGame()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md transition-all cursor-pointer">Mulai Ulang Catur</button>
                                </div>
                                <div id="chess-status-msg" class="text-xs font-bold text-indigo-650 bg-indigo-50/50 p-3 border border-indigo-100 rounded-xl">Klik 'Mulai Ulang Catur' untuk memulai!</div>
                            </div>

                            <!-- Chess Board Grid -->
                            <div class="flex flex-col items-center">
                                <div id="chess-board-grid" class="grid grid-cols-8 gap-0.5 bg-slate-200 border-2 border-slate-300 p-1.5 rounded-2xl w-max">
                                    <!-- Dynamic 8x8 squares -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- GAME 5: 2048 Strategy Game -->
                    <div id="inner-game-strategy" class="hidden space-y-4">
                        <div class="p-6 bg-slate-50 border border-slate-200/60 rounded-3xl flex flex-col md:flex-row gap-6 justify-between items-center">
                            <!-- Strategy description -->
                            <div class="space-y-4 max-w-sm">
                                <span class="text-[9px] font-black text-indigo-700 bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded uppercase tracking-wider block w-max">Slide & Merge</span>
                                <h4 class="text-sm font-black text-slate-850 uppercase">2048 Brain Strategy</h4>
                                <p class="text-xs text-slate-500 leading-relaxed font-semibold">Gabungkan angka-angka yang sama dengan menggeser kotak (ke atas, bawah, kiri, kanan). Capai ubin 512, 1024, atau 2048 untuk memenangkan poin XP tambahan!</p>
                                <div class="flex items-center justify-between gap-4 bg-white border border-slate-150 p-3 rounded-2xl">
                                    <div class="text-left">
                                        <span class="text-[9px] text-slate-400 font-bold uppercase block">Skor</span>
                                        <span id="game-2048-score" class="text-sm font-black text-indigo-700">0</span>
                                    </div>
                                    <button type="button" onclick="initialize2048Game()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow transition-all cursor-pointer">Mulai Baru</button>
                                </div>
                                <div id="game-2048-feedback" class="text-xs font-bold text-center text-emerald-600 hidden"></div>
                            </div>

                            <!-- 2048 Board Grid -->
                            <div class="flex flex-col items-center gap-4">
                                <div id="game-2048-board" class="grid grid-cols-4 gap-2.5 p-3.5 bg-slate-350 rounded-2xl w-64 h-64 relative shadow-inner">
                                    <!-- Dynamic 4x4 tiles -->
                                </div>

                                <!-- On-screen controls -->
                                <div class="grid grid-cols-3 gap-2 w-max select-none">
                                    <div></div>
                                    <button type="button" onclick="move2048('up')" class="px-3.5 py-2.5 bg-white hover:bg-slate-55 border border-slate-200 shadow-sm rounded-xl text-xs font-black cursor-pointer">UP</button>
                                    <div></div>
                                    <button type="button" onclick="move2048('left')" class="px-3.5 py-2.5 bg-white hover:bg-slate-55 border border-slate-200 shadow-sm rounded-xl text-xs font-black cursor-pointer">LEFT</button>
                                    <button type="button" onclick="move2048('down')" class="px-3.5 py-2.5 bg-white hover:bg-slate-55 border border-slate-200 shadow-sm rounded-xl text-xs font-black cursor-pointer">DOWN</button>
                                    <button type="button" onclick="move2048('right')" class="px-3.5 py-2.5 bg-white hover:bg-slate-55 border border-slate-200 shadow-sm rounded-xl text-xs font-black cursor-pointer">RIGHT</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SUBTAB 11: SELF-REWARD SHOP -->
                <div id="w-section-mental-reward" class="hidden bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Self-Reward Shop</h3>
                            <p class="text-xs text-slate-400 mt-0.5 font-semibold">Tukarkan Poin Produktivitas (XP) Anda dengan hadiah kecil sebagai bentuk penghargaan atas kerja keras Anda.</p>
                        </div>
                        <div class="px-4 py-2 bg-indigo-50 border border-indigo-200 rounded-2xl flex items-center gap-2">
                            <span class="text-[10px] font-bold text-slate-500">Saldo XP Anda:</span>
                            <span id="reward-xp-balance" class="text-xs font-black text-indigo-700">{{ $userPoint->total_points }} XP</span>
                        </div>
                    </div>

                    <!-- Catalog Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        
                        <!-- Reward 1 -->
                        <div class="p-5 bg-slate-50 border border-slate-200/60 rounded-3xl flex flex-col justify-between gap-4 hover:shadow-md transition-all">
                            <div class="space-y-2">
                                <span class="text-[9px] font-black text-indigo-700 bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded uppercase tracking-wider block w-max">Minuman</span>
                                <h4 class="text-xs font-black text-slate-850 uppercase">Kopi Susu Senja</h4>
                                <p class="text-[10px] text-slate-400 font-semibold leading-normal">Tarik napas dan nikmati secangkir kopi susu dingin di sore hari.</p>
                            </div>
                            <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100">
                                <span class="text-xs font-black text-slate-700">30 XP</span>
                                <button type="button" onclick="claimSelfReward('Kopi Susu Senja', 30)" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold rounded-xl shadow transition-all cursor-pointer">Klaim</button>
                            </div>
                        </div>

                        <!-- Reward 2 -->
                        <div class="p-5 bg-slate-50 border border-slate-200/60 rounded-3xl flex flex-col justify-between gap-4 hover:shadow-md transition-all">
                            <div class="space-y-2">
                                <span class="text-[9px] font-black text-amber-700 bg-amber-50 border border-amber-100 px-2 py-0.5 rounded uppercase tracking-wider block w-max">Istirahat</span>
                                <h4 class="text-xs font-black text-slate-850 uppercase">Tidur Siang 20 Menit</h4>
                                <p class="text-[10px] text-slate-400 font-semibold leading-normal">Jauhkan mata dari layar laptop dan istirahatkan pikiran sejenak.</p>
                            </div>
                            <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100">
                                <span class="text-xs font-black text-slate-700">50 XP</span>
                                <button type="button" onclick="claimSelfReward('Tidur Siang 20 Menit', 50)" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold rounded-xl shadow transition-all cursor-pointer">Klaim</button>
                            </div>
                        </div>

                        <!-- Reward 3 -->
                        <div class="p-5 bg-slate-50 border border-slate-200/60 rounded-3xl flex flex-col justify-between gap-4 hover:shadow-md transition-all">
                            <div class="space-y-2">
                                <span class="text-[9px] font-black text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded uppercase tracking-wider block w-max">Camilan</span>
                                <h4 class="text-xs font-black text-slate-850 uppercase">Es Krim Matcha</h4>
                                <p class="text-[10px] text-slate-400 font-semibold leading-normal">Beri dirimu asupan es krim dingin manis yang memanjakan lidah.</p>
                            </div>
                            <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100">
                                <span class="text-xs font-black text-slate-700">40 XP</span>
                                <button type="button" onclick="claimSelfReward('Es Krim Matcha', 40)" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold rounded-xl shadow transition-all cursor-pointer">Klaim</button>
                            </div>
                        </div>

                        <!-- Reward 4 -->
                        <div class="p-5 bg-slate-50 border border-slate-200/60 rounded-3xl flex flex-col justify-between gap-4 hover:shadow-md transition-all">
                            <div class="space-y-2">
                                <span class="text-[9px] font-black text-indigo-700 bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded uppercase tracking-wider block w-max">Aktivitas</span>
                                <h4 class="text-xs font-black text-slate-850 uppercase">Jalan Sore Bebas Laptop</h4>
                                <p class="text-[10px] text-slate-400 font-semibold leading-normal">Hirup udara segar di sekitar area rumah tanpa memikirkan push code.</p>
                            </div>
                            <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100">
                                <span class="text-xs font-black text-slate-700">60 XP</span>
                                <button type="button" onclick="claimSelfReward('Jalan Sore Bebas Laptop', 60)" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold rounded-xl shadow transition-all cursor-pointer">Klaim</button>
                            </div>
                        </div>

                        <!-- Reward 5 -->
                        <div class="p-5 bg-slate-50 border border-slate-200/60 rounded-3xl flex flex-col justify-between gap-4 hover:shadow-md transition-all">
                            <div class="space-y-2">
                                <span class="text-[9px] font-black text-purple-700 bg-purple-50 border border-purple-100 px-2 py-0.5 rounded uppercase tracking-wider block w-max">Hiburan</span>
                                <h4 class="text-xs font-black text-slate-850 uppercase">YouTube 30 Menit</h4>
                                <p class="text-[10px] text-slate-400 font-semibold leading-normal">Tonton video vlog, klip musik, atau review teknologi favorit Anda.</p>
                            </div>
                            <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100">
                                <span class="text-xs font-black text-slate-700">35 XP</span>
                                <button type="button" onclick="claimSelfReward('YouTube 30 Menit', 35)" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold rounded-xl shadow transition-all cursor-pointer">Klaim</button>
                            </div>
                        </div>

                        <!-- Reward 6 -->
                        <div class="p-5 bg-slate-50 border border-slate-200/60 rounded-3xl flex flex-col justify-between gap-4 hover:shadow-md transition-all">
                            <div class="space-y-2">
                                <span class="text-[9px] font-black text-rose-700 bg-rose-50 border border-rose-100 px-2 py-0.5 rounded uppercase tracking-wider block w-max">Makanan</span>
                                <h4 class="text-xs font-black text-slate-850 uppercase">1 Loyang Pizza Lezat</h4>
                                <p class="text-[10px] text-slate-400 font-semibold leading-normal">Hadiah besar setelah berhasil menuntaskan sprint besar minggu ini.</p>
                            </div>
                            <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100">
                                <span class="text-xs font-black text-slate-700">120 XP</span>
                                <button type="button" onclick="claimSelfReward('1 Loyang Pizza Lezat', 120)" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold rounded-xl shadow transition-all cursor-pointer">Klaim</button>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- INTERACTIVE GUIDED TOUR CONTAINER -->
    <div id="guided-tour-overlay" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-[2px] transition-all duration-300 pointer-events-auto" style="z-index: 9999;"></div>
    <div id="guided-tour-tooltip" class="hidden absolute bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xl w-[290px] transition-all duration-300" style="z-index: 10000;">
        <div class="space-y-3 font-sans">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <h5 class="text-xs font-black text-indigo-700 uppercase tracking-wider">Panduan Demo LUNOU</h5>
                <span id="guided-tour-step-indicator" class="text-[9px] bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded-md font-bold text-indigo-700">1 / 6</span>
            </div>
            <p id="guided-tour-text" class="text-[11px] text-slate-600 leading-relaxed font-semibold"></p>
            <div class="flex justify-between items-center pt-2">
                <button type="button" onclick="endGuidedTour()" class="text-[10px] text-slate-400 hover:text-slate-600 font-bold transition-all cursor-pointer">Lewati</button>
                <button type="button" onclick="nextGuidedTourStep()" id="guided-tour-next-btn" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold rounded-xl shadow-sm transition-all cursor-pointer">Lanjut</button>
            </div>
        </div>
    </div>

    <style>
        .tour-highlight {
            position: relative !important;
            z-index: 10001 !important;
            box-shadow: 0 0 0 8px rgba(79, 70, 229, 0.45), 0 20px 25px -5px rgba(0, 0, 0, 0.15) !important;
            background-color: white !important;
            pointer-events: auto !important;
        }
    </style>
</div>

@push('scripts')
<script>
    // 1. Switch Antara Pusat Kerja & Wellbeing
    function switchDashboardTab(tab) {
        const workSection = document.getElementById('dashboard-section-work');
        const wellbeingSection = document.getElementById('dashboard-section-wellbeing');
        
        const workBtn = document.getElementById('dashboard-tab-work-btn');
        const wellbeingBtn = document.getElementById('dashboard-tab-wellbeing-btn');
        
        if (tab === 'work') {
            workSection.classList.remove('hidden');
            wellbeingSection.classList.add('hidden');
            
            workBtn.className = "px-6 py-3.5 border-b-2 border-indigo-600 text-indigo-750 font-black text-sm transition-all focus:outline-none";
            wellbeingBtn.className = "px-6 py-3.5 border-b-2 border-transparent text-slate-500 font-bold hover:text-slate-800 text-sm transition-all focus:outline-none";
        } else {
            wellbeingSection.classList.remove('hidden');
            workSection.classList.add('hidden');
            
            wellbeingBtn.className = "px-6 py-3.5 border-b-2 border-indigo-600 text-indigo-750 font-black text-sm transition-all focus:outline-none";
            workBtn.className = "px-6 py-3.5 border-b-2 border-transparent text-slate-500 font-bold hover:text-slate-800 text-sm transition-all focus:outline-none";
        }
        localStorage.setItem('lunou_dashboard_tab', tab);
    }

    // 2. Switch Subtab didalam Wellbeing
    const wellbeingSubTabs = ['today', 'auto-journal', 'journal', 'recovery', 'goals', 'reflection', 'leave', 'balance', 'gamification', 'mental-friend', 'mental-game', 'mental-reward'];

    function switchWellbeingSubTab(activeSub) {
        wellbeingSubTabs.forEach(t => {
            const section = document.getElementById(`w-section-${t}`);
            const btn = document.getElementById(`w-tab-${t}-btn`);
            
            if (t === activeSub) {
                section.classList.remove('hidden');
                btn.className = "w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-black transition-all bg-white text-indigo-755 shadow-xs border border-slate-200/60";
            } else {
                section.classList.add('hidden');
                btn.className = "w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all text-slate-600 hover:bg-white hover:text-slate-900";
            }
        });
        localStorage.setItem('lunou_wellbeing_subtab', activeSub);
    }

    // Persist tab configurations on load
    document.addEventListener('DOMContentLoaded', () => {
        const savedTab = localStorage.getItem('lunou_dashboard_tab');
        if (savedTab) {
            switchDashboardTab(savedTab);
        }

        const savedSubTab = localStorage.getItem('lunou_wellbeing_subtab');
        if (savedSubTab && wellbeingSubTabs.includes(savedSubTab)) {
            switchWellbeingSubTab(savedSubTab);
        } else {
            switchWellbeingSubTab('auto-journal');
        }
    });

    // 3. Mood Selector Logic
    function selectMood(mood) {
        document.getElementById('input-selected-mood').value = mood;
        document.querySelectorAll('.mood-btn').forEach(btn => {
            btn.className = "mood-btn px-4 py-2 border rounded-xl text-xs font-bold transition-all bg-slate-55/60 border-slate-200 text-slate-600 hover:bg-slate-100";
        });
        document.getElementById(`mood-btn-${mood}`).className = "mood-btn px-4 py-2 border rounded-xl text-xs font-bold transition-all bg-indigo-600 border-indigo-600 text-white shadow-xs";
    }

    // 4. Interactive Breathing Exercise Guidance
    let breathingTimer = null;
    let breathState = 0; 

    function toggleBreathingExercise() {
        const circle = document.getElementById('breath-circle');
        const text = document.getElementById('breath-text');
        const btn = document.getElementById('breath-btn');
        
        if (breathingTimer) {
            clearInterval(breathingTimer);
            breathingTimer = null;
            circle.style.transform = "scale(1)";
            circle.style.backgroundColor = "#e0e7ff";
            text.innerText = "Rileks";
            btn.innerText = "Mulai Latihan Napas";
            breathState = 0;
        } else {
            btn.innerText = "Hentikan Latihan";
            runBreathingCycle();
            breathingTimer = setInterval(runBreathingCycle, 4000);
        }
    }

    function runBreathingCycle() {
        const circle = document.getElementById('breath-circle');
        const text = document.getElementById('breath-text');
        
        if (breathState === 0 || breathState === 3) {
            breathState = 1;
            text.innerText = "Tarik Napas";
            circle.style.transform = "scale(1.6)";
            circle.style.backgroundColor = "#c7d2fe";
            circle.style.transition = "transform 4s ease-in-out, background-color 4s ease-in-out";
        } else if (breathState === 1) {
            breathState = 2;
            text.innerText = "Tahan Napas";
            circle.style.transform = "scale(1.6)";
            circle.style.backgroundColor = "#818cf8";
            circle.style.transition = "transform 4s ease-in-out, background-color 4s ease-in-out";
        } else if (breathState === 2) {
            breathState = 3;
            text.innerText = "Hembuskan";
            circle.style.transform = "scale(1)";
            circle.style.backgroundColor = "#e0e7ff";
            circle.style.transition = "transform 4s ease-in-out, background-color 4s ease-in-out";
        }
    }

    // 5. Offline Web Audio Rain Ambient Synthesizer
    let audioCtx = null;
    let noiseNode = null;
    let gainNode = null;

    function toggleAmbientSound() {
        const btn = document.getElementById('ambient-btn');
        if (audioCtx) {
            audioCtx.close();
            audioCtx = null;
            btn.innerText = "Putar Suara Hujan";
            btn.className = "w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-xl shadow-md transition-all";
        } else {
            try {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                
                const bufferSize = 2 * audioCtx.sampleRate;
                const noiseBuffer = audioCtx.createBuffer(1, bufferSize, audioCtx.sampleRate);
                const output = noiseBuffer.getChannelData(0);
                
                let lastOut = 0.0;
                for (let i = 0; i < bufferSize; i++) {
                    const white = Math.random() * 2 - 1;
                    output[i] = (lastOut + (0.02 * white)) / 1.02;
                    lastOut = output[i];
                    output[i] *= 3.5;
                }
                
                const noiseSource = audioCtx.createBufferSource();
                noiseSource.buffer = noiseBuffer;
                noiseSource.loop = true;
                
                const filter = audioCtx.createBiquadFilter();
                filter.type = 'lowpass';
                filter.frequency.value = 420;
                
                gainNode = audioCtx.createGain();
                gainNode.gain.value = 0.45;
                
                noiseSource.connect(filter);
                filter.connect(gainNode);
                gainNode.connect(audioCtx.destination);
                
                noiseSource.start();
                btn.innerText = "Hentikan Suara Hujan";
                btn.className = "w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black rounded-xl shadow-md transition-all";
            } catch (e) {
                alert("Browser Anda tidak mendukung Web Audio Synthesizer");
            }
        }
    }

    // 6. Private AI Balance Report with Flying Mascot Animation
    function requestAIBalanceReport() {
        const mascot = document.getElementById('delivery-mascot-img');
        const statusText = document.getElementById('delivery-status-text');
        const reportCard = document.getElementById('balance-report-card');
        const reqBtn = document.getElementById('balance-request-btn');
        
        // Hide report card with transition for a smooth refresh animation!
        reportCard.classList.add('opacity-0', 'translate-y-4');
        setTimeout(() => {
            reportCard.classList.add('hidden');
        }, 300);

        // Start flight animation: change to icon 13 (carrying letter) and trigger floating bobbing
        mascot.src = "{{ asset('icon/13.png') }}";
        mascot.style.transition = "transform 0.5s ease-in-out";
        mascot.style.transform = "scale(1.3) translateY(-10px)";
        statusText.innerText = "LUNOU sedang terbang membawa amplop laporan keseimbangan Anda...";
        reqBtn.disabled = true;
        
        // Trigger AJAX fetch
        fetch('{{ route("user.wellbeing.balance-report") }}')
            .then(res => {
                if (!res.ok) throw new Error("Gagal mengambil laporan.");
                return res.json();
            })
            .then(data => {
                // Delay 2 seconds to let the mascot "fly" into place with high-fidelity feels!
                setTimeout(() => {
                    reqBtn.disabled = false;
                    reqBtn.innerText = "Perbarui Laporan Keseimbangan (Kapanpun)";
                    
                    // Mascot Arrived! Happy state (icon 5)
                    mascot.src = "{{ asset('icon/5.png') }}";
                    mascot.style.transform = "scale(1)";
                    statusText.innerText = "Laporan berhasil diantarkan oleh LUNOU! Silakan baca di bawah.";
                    
                    // Populate Weekly Report
                    document.getElementById('rep-week-completed').innerText = `${data.week.completed} / ${data.week.total}`;
                    document.getElementById('rep-week-energy').innerText = `${data.week.energy} / 5`;
                    document.getElementById('rep-week-mental').innerText = `${data.week.mental} / 5`;
                    document.getElementById('rep-week-mood').innerText = data.week.mood;
                    document.getElementById('rep-week-analysis').innerText = data.week.analysis;
                    
                    // Populate Monthly Report
                    document.getElementById('rep-month-completed').innerText = `${data.month.completed} / ${data.month.total}`;
                    document.getElementById('rep-month-energy').innerText = `${data.month.energy} / 5`;
                    document.getElementById('rep-month-mental').innerText = `${data.month.mental} / 5`;
                    document.getElementById('rep-month-mood').innerText = data.month.mood;
                    document.getElementById('rep-month-analysis').innerText = data.month.analysis;
                    
                    // Populate Recommendations
                    const recList = document.getElementById('rep-recommendations-list');
                    recList.innerHTML = '';
                    data.recommendations.forEach(rec => {
                        const li = document.createElement('li');
                        li.innerText = rec;
                        recList.appendChild(li);
                    });
                    
                    // Populate Motivation
                    document.getElementById('rep-motivation-text').innerText = `"${data.motivation}"`;

                    // Fade in results card
                    reportCard.classList.remove('hidden');
                    setTimeout(() => {
                        reportCard.classList.remove('opacity-0', 'translate-y-4');
                    }, 50);
                }, 2000);
            })
            .catch(err => {
                console.error(err);
                reqBtn.disabled = false;
                mascot.src = "{{ asset('icon/18.png') }}"; // error / sleeping state
                mascot.style.transform = "scale(1)";
                statusText.innerText = "Ups! Terjadi gangguan cuaca di udara, LUNOU gagal mengantar. Silakan coba lagi.";
            });
    }

    // Wellbeing Discussion Box Handler
    let wellbeingRecognition = null;
    let isWellbeingListening = false;

    function toggleWellbeingSpeechRecognition() {
        const micBtn = document.getElementById('wellbeing-mic-btn');
        const input = document.getElementById('wellbeing-discussion-input');

        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            alert("Maaf, browser Anda tidak mendukung fitur Voice-to-Text. Silakan gunakan Google Chrome, Safari, atau Microsoft Edge.");
            return;
        }

        if (isWellbeingListening) {
            wellbeingRecognition.stop();
            return;
        }

        const SpeechRecognitionClass = window.SpeechRecognition || window.webkitSpeechRecognition;
        wellbeingRecognition = new SpeechRecognitionClass();
        wellbeingRecognition.lang = 'id-ID';
        wellbeingRecognition.continuous = false;
        wellbeingRecognition.interimResults = false;

        wellbeingRecognition.onstart = function() {
            isWellbeingListening = true;
            micBtn.classList.remove('text-slate-400');
            micBtn.classList.add('text-rose-500', 'bg-rose-50', 'animate-pulse');
            input.placeholder = "Mendengarkan...";
        };

        wellbeingRecognition.onerror = function(event) {
            console.error("Speech recognition error", event.error);
            stopWellbeingRecognition();
        };

        wellbeingRecognition.onend = function() {
            stopWellbeingRecognition();
        };

        wellbeingRecognition.onresult = function(event) {
            const resultText = event.results[0][0].transcript;
            if (resultText) {
                input.value = resultText;
            }
        };

        wellbeingRecognition.start();
    }

    function stopWellbeingRecognition() {
        isWellbeingListening = false;
        const micBtn = document.getElementById('wellbeing-mic-btn');
        const input = document.getElementById('wellbeing-discussion-input');
        if (micBtn) {
            micBtn.classList.remove('text-rose-500', 'bg-rose-50', 'animate-pulse');
            micBtn.classList.add('text-slate-400');
        }
        if (input) {
            input.placeholder = "Tanyakan saran atau cara mengatasi stres...";
        }
    }

    function sendWellbeingDiscussion() {
        const input = document.getElementById('wellbeing-discussion-input');
        const queryText = input.value.trim();
        if (!queryText) return;

        input.value = '';

        // Append user query bubble to chat
        const chatContainer = document.getElementById('wellbeing-discussion-chat');
        chatContainer.insertAdjacentHTML('beforeend', `
            <div class="flex items-start gap-2 justify-end">
                <div class="max-w-[85%] bg-indigo-600 text-white rounded-2xl rounded-tr-none p-3 shadow-xs">
                    <p class="text-[11px] leading-relaxed font-semibold">${escapeHtml(queryText)}</p>
                </div>
            </div>
        `);
        chatContainer.scrollTop = chatContainer.scrollHeight;

        // Append thinking bubble
        const thinkingId = 'thinking-' + Date.now();
        chatContainer.insertAdjacentHTML('beforeend', `
            <div id="${thinkingId}" class="flex items-start gap-2 justify-start">
                <img src="/icon/15.png" alt="LUNOU" class="w-6 h-6 object-contain shrink-0 mt-0.5 animate-bounce">
                <div class="max-w-[85%] bg-white border border-slate-100 rounded-2xl rounded-tl-none p-3 shadow-xs">
                    <p class="text-[11px] text-slate-400 leading-relaxed font-semibold italic">LUNOU sedang berpikir...</p>
                </div>
            </div>
        `);
        chatContainer.scrollTop = chatContainer.scrollHeight;

        fetch('{{ route("user.wellbeing.discuss") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: queryText })
        })
        .then(res => {
            if (!res.ok) throw new Error("Gagal terhubung.");
            return res.json();
        })
        .then(data => {
            document.getElementById(thinkingId).remove();
            if (data.success) {
                chatContainer.insertAdjacentHTML('beforeend', `
                    <div class="flex items-start gap-2 justify-start">
                        <img src="/icon/11.png" alt="LUNOU" class="w-6 h-6 object-contain shrink-0 mt-0.5">
                        <div class="max-w-[85%] bg-white border border-slate-100 rounded-2xl rounded-tl-none p-3 shadow-xs">
                            <p class="text-[11px] text-slate-700 leading-relaxed font-semibold">${data.reply.replace(/\n/g, '<br>')}</p>
                        </div>
                    </div>
                `);
            } else {
                chatContainer.insertAdjacentHTML('beforeend', `
                    <div class="flex items-start gap-2 justify-start">
                        <img src="/icon/18.png" alt="LUNOU" class="w-6 h-6 object-contain shrink-0 mt-0.5">
                        <div class="max-w-[85%] bg-rose-50 border border-rose-100 rounded-2xl rounded-tl-none p-3 shadow-xs">
                            <p class="text-[11px] text-rose-600 leading-relaxed font-semibold">LUNOU lelah berpikir: ${data.message}</p>
                        </div>
                    </div>
                `);
            }
            chatContainer.scrollTop = chatContainer.scrollHeight;
        })
        .catch(err => {
            document.getElementById(thinkingId).remove();
            chatContainer.insertAdjacentHTML('beforeend', `
                <div class="flex items-start gap-2 justify-start">
                    <img src="/icon/18.png" alt="LUNOU" class="w-6 h-6 object-contain shrink-0 mt-0.5">
                    <div class="max-w-[85%] bg-rose-50 border border-rose-100 rounded-2xl rounded-tl-none p-3 shadow-xs">
                        <p class="text-[11px] text-rose-600 leading-relaxed font-semibold">Maaf, LUNOU gagal terhubung. Coba lagi nanti.</p>
                    </div>
                </div>
            `);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        });
    }

    // 7. Sad Mascot Chime Synthesizer & Auto-Redirection
    document.addEventListener('click', playSadChimeOnFirstClick, { once: true });
    
    function playSadChimeOnFirstClick() {
        if (document.getElementById('sad-mascot-reminder')) {
            playSadChime();
        }
    }
    
    function playSadChime() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            
            osc.connect(gain);
            gain.connect(ctx.destination);
            
            // Downward sad tone: C5 (523Hz) to G4 (392Hz) over 0.4 seconds
            osc.frequency.setValueAtTime(523.25, ctx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(392.00, ctx.currentTime + 0.4);
            
            gain.gain.setValueAtTime(0.15, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.4);
            
            osc.start();
            osc.stop(ctx.currentTime + 0.4);
        } catch (e) {
            console.log("Audio failed to play");
        }
    }

    function playHappyChime() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            
            osc.connect(gain);
            gain.connect(ctx.destination);
            
            // Upward happy tone: G4 (392Hz) to C5 (523Hz) over 0.3 seconds
            osc.frequency.setValueAtTime(392.00, ctx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(523.25, ctx.currentTime + 0.3);
            
            gain.gain.setValueAtTime(0.15, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.3);
            
            osc.start();
            osc.stop(ctx.currentTime + 0.3);
        } catch (e) {
            console.log("Audio failed to play");
        }
    }

    // 8. LUNOU Auto Check-in AI logic
    function toggleAutoCheckinWidget() {
        const panel = document.getElementById('lunou-auto-checkin-panel');
        panel.classList.toggle('hidden');
    }

    let autoCheckinRecognition = null;
    let isAutoCheckinListening = false;

    function toggleAutoCheckinSpeech() {
        const micBtn = document.getElementById('autocheckin-mic-btn');
        const input = document.getElementById('autocheckin-text-input');

        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            alert("Maaf, browser Anda tidak mendukung fitur Voice-to-Text. Silakan gunakan Google Chrome, Safari, atau Microsoft Edge.");
            return;
        }

        if (isAutoCheckinListening) {
            autoCheckinRecognition.stop();
            return;
        }

        const SpeechRecognitionClass = window.SpeechRecognition || window.webkitSpeechRecognition;
        autoCheckinRecognition = new SpeechRecognitionClass();
        autoCheckinRecognition.lang = 'id-ID';
        autoCheckinRecognition.continuous = false;
        autoCheckinRecognition.interimResults = false;

        autoCheckinRecognition.onstart = function() {
            isAutoCheckinListening = true;
            micBtn.classList.remove('text-slate-400');
            micBtn.classList.add('text-rose-500', 'bg-rose-50', 'animate-pulse');
            input.placeholder = "Mendengarkan...";
        };

        autoCheckinRecognition.onerror = function(event) {
            console.error("Speech recognition error", event.error);
            stopAutoCheckinSpeech();
        };

        autoCheckinRecognition.onend = function() {
            stopAutoCheckinSpeech();
        };

        autoCheckinRecognition.onresult = function(event) {
            const resultText = event.results[0][0].transcript;
            if (resultText) {
                input.value = resultText;
            }
        };

        autoCheckinRecognition.start();
    }

    function stopAutoCheckinSpeech() {
        isAutoCheckinListening = false;
        const micBtn = document.getElementById('autocheckin-mic-btn');
        const input = document.getElementById('autocheckin-text-input');
        if (micBtn) {
            micBtn.classList.remove('text-rose-500', 'bg-rose-50', 'animate-pulse');
            micBtn.classList.add('text-slate-400');
        }
        if (input) {
            input.placeholder = "Ceritakan perasaanmu hari ini...";
        }
    }

    function processAutoCheckin() {
        const input = document.getElementById('autocheckin-text-input');
        const text = input.value.trim();
        if (!text) return;

        const processBtn = document.getElementById('autocheckin-process-btn');
        const statusEl = document.getElementById('autocheckin-status');

        processBtn.disabled = true;
        statusEl.classList.remove('hidden');

        fetch('{{ route("user.wellbeing.checkin-auto-fill") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: text })
        })
        .then(res => {
            if (!res.ok) throw new Error("Gagal memetakan check-in.");
            return res.json();
        })
        .then(data => {
            processBtn.disabled = false;
            statusEl.classList.add('hidden');

            if (data.success) {
                // 1. Set Mood Button
                const mood = data.mood;
                if (mood) {
                    selectMood(mood);
                }

                // 2. Set Sliders
                if (data.energy !== undefined) {
                    const energySlider = document.querySelector('input[name="energy"]');
                    energySlider.value = data.energy;
                }
                if (data.mental_load !== undefined) {
                    const mentalSlider = document.querySelector('input[name="mental_load"]');
                    mentalSlider.value = data.mental_load;
                }
                if (data.rest_condition !== undefined) {
                    const restSlider = document.querySelector('input[name="rest_condition"]');
                    restSlider.value = data.rest_condition;
                }

                // 3. Set Thoughts Textarea
                if (data.thoughts) {
                    const thoughtsArea = document.querySelector('textarea[name="thoughts"]');
                    thoughtsArea.value = data.thoughts;
                }

                alert("LUNOU berhasil memetakan check-in Anda! Silakan review dan klik 'Simpan Check-in Harian' untuk menyimpan.");
                document.getElementById('lunou-auto-checkin-panel').classList.add('hidden');
                input.value = '';
            } else {
                alert("Gagal memetakan: " + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            processBtn.disabled = false;
            statusEl.classList.add('hidden');
            alert("Terjadi kesalahan koneksi, gagal memetakan check-in otomatis.");
        });
    }

    // 9. LUNOU AI Auto-Journaler Speech & submit functions
    let masterJournalRecognition = null;
    let isMasterJournalListening = false;

    function toggleMasterJournalSpeech() {
        const micBtn = document.getElementById('master-journal-mic-btn');
        const input = document.getElementById('master-journal-input');

        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            alert("Maaf, browser Anda tidak mendukung fitur Voice-to-Text. Silakan gunakan Google Chrome, Safari, atau Microsoft Edge.");
            return;
        }

        if (isMasterJournalListening) {
            masterJournalRecognition.stop();
            return;
        }

        const SpeechRecognitionClass = window.SpeechRecognition || window.webkitSpeechRecognition;
        masterJournalRecognition = new SpeechRecognitionClass();
        masterJournalRecognition.lang = 'id-ID';
        masterJournalRecognition.continuous = false;
        masterJournalRecognition.interimResults = false;

        masterJournalRecognition.onstart = function() {
            isMasterJournalListening = true;
            micBtn.classList.remove('text-slate-400');
            micBtn.classList.add('text-rose-500', 'bg-rose-50', 'animate-pulse');
            input.placeholder = "Mendengarkan...";
        };

        masterJournalRecognition.onerror = function(event) {
            console.error("Speech recognition error", event.error);
            stopMasterJournalSpeech();
        };

        masterJournalRecognition.onend = function() {
            stopMasterJournalSpeech();
        };

        masterJournalRecognition.onresult = function(event) {
            const resultText = event.results[0][0].transcript;
            if (resultText) {
                input.value = (input.value + " " + resultText).trim();
            }
        };

        masterJournalRecognition.start();
    }

    function stopMasterJournalSpeech() {
        isMasterJournalListening = false;
        const micBtn = document.getElementById('master-journal-mic-btn');
        const input = document.getElementById('master-journal-input');
        if (micBtn) {
            micBtn.classList.remove('text-rose-500', 'bg-rose-50', 'animate-pulse');
            micBtn.classList.add('text-slate-400');
        }
        if (input) {
            input.placeholder = "Contoh: Hari ini saya senang sekali karena berhasil merilis dashboard baru Yoimo...";
        }
    }

    function submitMasterJournal() {
        const input = document.getElementById('master-journal-input');
        const text = input.value.trim();
        if (!text) return;

        const submitBtn = document.getElementById('master-journal-submit-btn');
        const statusEl = document.getElementById('master-journal-status');
        const resultPanel = document.getElementById('master-journal-result');

        submitBtn.disabled = true;
        statusEl.classList.remove('hidden');
        resultPanel.classList.add('hidden');

        fetch('{{ route("user.wellbeing.auto-journal") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: text })
        })
        .then(res => {
            if (!res.ok) throw new Error("Gagal memproses Auto-Journal.");
            return res.json();
        })
        .then(data => {
            submitBtn.disabled = false;
            statusEl.classList.add('hidden');

            if (data.success) {
                // Populate result mapping
                document.getElementById('mj-res-checkin').innerText = data.checkin;
                document.getElementById('mj-res-journal').innerText = data.journal;
                document.getElementById('mj-res-reflection').innerText = data.reflection;

                // Populate Goals
                const goalsList = document.getElementById('mj-res-goals');
                goalsList.innerHTML = '';
                data.goals.forEach(goal => {
                    const li = document.createElement('li');
                    li.innerText = goal;
                    goalsList.appendChild(li);
                });

                // Prepend to journals timeline feed container
                const feedContainer = document.getElementById('journals-feed-container');
                const placeholder = document.getElementById('no-journals-placeholder');
                if (placeholder) placeholder.remove();

                const entry = data.new_entry;
                let catsHtml = '';
                entry.categories.forEach(cat => {
                    catsHtml += `<span class="px-2 py-0.5 bg-indigo-50 border border-indigo-100 rounded text-[9px] font-black text-indigo-700 uppercase tracking-tight">${cat}</span> `;
                });

                let aiFeedbackHtml = '';
                if (entry.analysis || entry.appreciation) {
                    aiFeedbackHtml = `<div class="bg-indigo-50/30 border border-indigo-100/60 rounded-xl p-3 space-y-2">`;
                    if (entry.analysis) {
                        aiFeedbackHtml += `<div class="space-y-0.5">
                            <span class="text-[8px] font-black text-indigo-650 uppercase tracking-wider block">🧠 LUNOU AI Analisis</span>
                            <p class="text-[10px] text-slate-650 font-semibold leading-relaxed italic">"${entry.analysis}"</p>
                        </div>`;
                    }
                    if (entry.appreciation) {
                        aiFeedbackHtml += `<div class="space-y-0.5">
                            <span class="text-[8px] font-black text-emerald-650 uppercase tracking-wider block">🌟 LUNOU Apresiasi</span>
                            <p class="text-[10px] text-slate-650 font-semibold leading-relaxed italic">"${entry.appreciation}"</p>
                        </div>`;
                    }
                    aiFeedbackHtml += `</div>`;
                }

                const newItemHtml = `
                    <div class="journal-feed-item bg-slate-50/40 border border-slate-100 rounded-2xl p-4.5 space-y-3 transition-all hover:border-slate-350 hover:bg-slate-50/80" 
                         data-week="true" 
                         data-month="true">
                        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200/50 pb-2">
                            <span class="text-[10px] font-black text-slate-500">${entry.date}</span>
                            <div class="flex flex-wrap gap-1">
                                ${catsHtml}
                            </div>
                        </div>
                        <div class="text-xs text-slate-800 font-bold leading-relaxed">
                            <p>${entry.raw_content}</p>
                        </div>
                        ${aiFeedbackHtml}
                    </div>
                `;

                feedContainer.insertAdjacentHTML('afterbegin', newItemHtml);

                // Update realtime card indicators at the top
                if (data.checkin_data) {
                    const energyEl = document.getElementById('ind-energy');
                    const energyBar = document.getElementById('ind-energy-bar');
                    const mentalEl = document.getElementById('ind-mental');
                    const mentalBar = document.getElementById('ind-mental-bar');

                    if (energyEl && data.checkin_data.energy) {
                        energyEl.innerText = data.checkin_data.energy;
                        energyBar.style.width = (data.checkin_data.energy * 20) + '%';
                    }
                    if (mentalEl && data.checkin_data.mental_load) {
                        mentalEl.innerText = data.checkin_data.mental_load;
                        mentalBar.style.width = (data.checkin_data.mental_load * 20) + '%';
                    }
                }

                // Show success block
                resultPanel.classList.remove('hidden');
                input.value = '';

                // Show a nice alert
                alert("LUNOU berhasil memetakan & menyimpan data Anda ke seluruh modul database!");
            } else {
                alert("Gagal memetakan data: " + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            submitBtn.disabled = false;
            statusEl.classList.add('hidden');
            alert("Terjadi kesalahan koneksi, gagal memetakan Auto-Journal.");
        });
    }

    function filterJournals(mode) {
        const items = document.querySelectorAll('.journal-feed-item');
        const allBtn = document.getElementById('j-filter-all-btn');
        const weeklyBtn = document.getElementById('j-filter-weekly-btn');
        const monthlyBtn = document.getElementById('j-filter-monthly-btn');

        // Reset button states
        [allBtn, weeklyBtn, monthlyBtn].forEach(btn => {
            btn.className = "px-2.5 py-1 text-[10px] font-black rounded-lg transition-all text-slate-500 hover:text-slate-900";
        });

        // Set active button
        if (mode === 'all') {
            allBtn.className = "px-2.5 py-1 text-[10px] font-black rounded-lg transition-all bg-white text-indigo-755 shadow-xs";
        } else if (mode === 'weekly') {
            weeklyBtn.className = "px-2.5 py-1 text-[10px] font-black rounded-lg transition-all bg-white text-indigo-755 shadow-xs";
        } else {
            monthlyBtn.className = "px-2.5 py-1 text-[10px] font-black rounded-lg transition-all bg-white text-indigo-755 shadow-xs";
        }

        // Show/hide items
        items.forEach(item => {
            const isWeek = item.getAttribute('data-week') === 'true';
            const isMonth = item.getAttribute('data-month') === 'true';

            if (mode === 'all') {
                item.classList.remove('hidden');
            } else if (mode === 'weekly') {
                if (isWeek) item.classList.remove('hidden');
                else item.classList.add('hidden');
            } else if (mode === 'monthly') {
                if (isMonth) item.classList.remove('hidden');
                else item.classList.add('hidden');
            }
        });
    }

    function goToWellbeingCheckin() {
        playHappyChime();
        switchDashboardTab('wellbeing');
        switchWellbeingSubTab('today');
    }

    // --- RUANG MENTAL BARU SCRIPTS ---

    // 1. Teman Cerita LUNOU
    function sendMentalFriendShortcut(promptText) {
        document.getElementById('mental-friend-chat-input').value = promptText;
        sendMentalFriendMessage();
    }

    function sendMentalFriendMessage() {
        const input = document.getElementById('mental-friend-chat-input');
        const queryText = input.value.trim();
        if (!queryText) return;

        input.value = '';

        // Append user bubble to chat feed
        const feed = document.getElementById('mental-friend-chat-feed');
        feed.insertAdjacentHTML('beforeend', `
            <div class="flex items-start gap-2 justify-end">
                <div class="max-w-[85%] bg-indigo-600 text-white rounded-2xl rounded-tr-none p-3 shadow-xs">
                    <p class="text-[11px] leading-relaxed font-semibold">${escapeHtml(queryText)}</p>
                </div>
            </div>
        `);
        feed.scrollTop = feed.scrollHeight;

        // Append thinking bubble
        const thinkingId = 'thinking-mental-' + Date.now();
        feed.insertAdjacentHTML('beforeend', `
            <div id="${thinkingId}" class="flex items-start gap-2 justify-start">
                <img src="/icon/15.png" alt="LUNOU" class="w-6 h-6 object-contain shrink-0 mt-0.5 animate-bounce">
                <div class="max-w-[85%] bg-white border border-slate-100 rounded-2xl rounded-tl-none p-3 shadow-xs">
                    <p class="text-[11px] text-slate-400 leading-relaxed font-semibold italic">LUNOU sedang menyimak...</p>
                </div>
            </div>
        `);
        feed.scrollTop = feed.scrollHeight;

        // Send query via discuss route (reusing wellbeing counseling endpoint)
        fetch('{{ route("user.wellbeing.discuss") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: queryText })
        })
        .then(res => {
            if (!res.ok) throw new Error("Connection failed");
            return res.json();
        })
        .then(data => {
            document.getElementById(thinkingId).remove();
            if (data.success) {
                // Support rich formatting or warm expressions
                feed.insertAdjacentHTML('beforeend', `
                    <div class="flex items-start gap-2 justify-start">
                        <img src="/icon/5.png" alt="LUNOU" class="w-6 h-6 object-contain shrink-0 mt-0.5">
                        <div class="max-w-[85%] bg-white border border-slate-100 rounded-2xl rounded-tl-none p-3 shadow-xs">
                            <p class="text-[11px] text-slate-700 leading-relaxed font-semibold">${data.reply.replace(/\n/g, '<br>')}</p>
                        </div>
                    </div>
                `);
                playHappyChime();
            } else {
                feed.insertAdjacentHTML('beforeend', `
                    <div class="flex items-start gap-2 justify-start">
                        <img src="/icon/18.png" alt="LUNOU" class="w-6 h-6 object-contain shrink-0 mt-0.5">
                        <div class="max-w-[85%] bg-rose-50 border border-rose-100 rounded-2xl rounded-tl-none p-3 shadow-xs">
                            <p class="text-[11px] text-rose-600 leading-relaxed font-semibold">Maaf Sahabat, LUNOU sedang mencerna kata-kata Anda. Coba tuliskan lagi.</p>
                        </div>
                    </div>
                `);
            }
            feed.scrollTop = feed.scrollHeight;
        })
        .catch(err => {
            document.getElementById(thinkingId).remove();
            feed.insertAdjacentHTML('beforeend', `
                <div class="flex items-start gap-2 justify-start">
                    <img src="/icon/18.png" alt="LUNOU" class="w-6 h-6 object-contain shrink-0 mt-0.5">
                    <div class="max-w-[85%] bg-rose-50 border border-rose-100 rounded-2xl rounded-tl-none p-3 shadow-xs">
                        <p class="text-[11px] text-rose-600 leading-relaxed font-semibold">Koneksi terputus sejenak. LUNOU tetap bersamamu.</p>
                    </div>
                </div>
            `);
            feed.scrollTop = feed.scrollHeight;
        });
    }

    // 2. Game Ketenangan: Zen Balloon Popper
    let zenPoints = 0;
    let gameInterval = null;

    function startZenGame() {
        // Remove start button
        document.getElementById('game-start-layer').remove();

        zenPoints = 0;
        document.getElementById('zen-points-display').innerText = `+${zenPoints} XP`;

        // Start spawning balloons
        gameInterval = setInterval(spawnBalloon, 1200);
        spawnBalloon();
    }

    function spawnBalloon() {
        const arena = document.getElementById('balloon-arena');
        if (!arena) return;

        const words = ['Burnout', 'Deadline', 'Overwork', 'Rasa Cemas', 'Stres Kerja', 'Multitasking', 'Lelah Fisik', 'Kurang Istirahat'];
        const word = words[Math.floor(Math.random() * words.length)];

        const colors = [
            'from-rose-400 to-rose-600 border-rose-300',
            'from-amber-400 to-amber-600 border-amber-300',
            'from-indigo-400 to-indigo-600 border-indigo-300',
            'from-purple-400 to-purple-600 border-purple-300',
            'from-sky-400 to-sky-600 border-sky-300'
        ];
        const colorClass = colors[Math.floor(Math.random() * colors.length)];

        const balloon = document.createElement('div');
        balloon.className = `absolute px-4 py-2 bg-gradient-to-t ${colorClass} border text-white text-[10px] font-black rounded-full shadow-lg cursor-pointer transition-all duration-300 transform scale-90 hover:scale-110 flex flex-col items-center justify-center select-none`;
        
        // Random horizontal pos
        const width = arena.clientWidth - 100;
        const left = Math.max(10, Math.floor(Math.random() * width));
        balloon.style.left = `${left}px`;
        balloon.style.bottom = `-50px`;

        // Label string
        balloon.innerHTML = `<span class="mt-0.5 tracking-tight uppercase">${word}</span>`;

        // Let's bind click to pop
        balloon.onclick = function() {
            popBalloon(balloon);
        };

        arena.appendChild(balloon);

        // Float up animation
        let bottom = -50;
        const interval = setInterval(() => {
            bottom += 2.5;
            balloon.style.bottom = `${bottom}px`;

            if (bottom > arena.clientHeight + 60) {
                clearInterval(interval);
                balloon.remove();
            }
        }, 30);

        balloon.dataset.floatInterval = interval;
    }

    function popBalloon(balloon) {
        clearInterval(balloon.dataset.floatInterval);

        // Sound effect (Web Audio Synthesizer Pop sound)
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            
            // Pop sound: short sine wave sweeps down quickly
            osc.type = 'sine';
            osc.frequency.setValueAtTime(300, ctx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(10, ctx.currentTime + 0.1);
            gain.gain.setValueAtTime(0.2, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.1);
            
            osc.start();
            osc.stop(ctx.currentTime + 0.1);
        } catch(e) {}

        // Visual Popping animation
        balloon.innerHTML = 'POP!';
        balloon.className = 'absolute px-4 py-2 bg-transparent text-emerald-400 text-xs font-black select-none pointer-events-none transform scale-125 transition-all duration-200';
        
        setTimeout(() => {
            balloon.remove();
        }, 200);

        // Update points
        zenPoints += 5;
        document.getElementById('zen-points-display').innerText = `+${zenPoints} XP`;
        playHappyChime();
    }

    // 3. Self-Reward Shop
    let currentXpBalance = parseInt('{{ $userPoint->total_points }}');

    function claimSelfReward(rewardName, cost) {
        if (currentXpBalance < cost) {
            alert(`Saldo XP Anda tidak mencukupi! Anda butuh ${cost} XP untuk mengklaim "${rewardName}". Tetap semangat bekerja dan check-in untuk menambah poin!`);
            return;
        }

        const confirmClaim = confirm(`Apakah Anda yakin ingin menukarkan ${cost} XP untuk hadiah "${rewardName}"?`);
        if (!confirmClaim) return;

        // Call AJAX to deduct points in database (using award-points with negative cost)
        fetch('{{ route("user.wellbeing.award-points") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ points: -cost, description: `Mengklaim reward: ${rewardName}` })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Deduct points locally with visual feedback
                currentXpBalance = data.total_points;
                document.getElementById('reward-xp-balance').innerText = `${currentXpBalance} XP`;
                
                // Update gamification points display if exists
                const gamificationBalance = document.querySelector('#w-section-gamification .text-2xl.font-black.text-slate-800');
                if (gamificationBalance) {
                    gamificationBalance.innerText = `${currentXpBalance} XP`;
                }

                // play happy chime
                playHappyChime();

                alert(`Selamat! Reward "${rewardName}" berhasil diklaim. Silakan nikmati waktu istirahat sejenak untuk memulihkan energi Anda. Kerja bagus!`);
            } else {
                alert("Gagal menukarkan poin: " + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert("Terjadi gangguan jaringan, gagal menukarkan poin.");
        });
    }

    // --- INNER GAME NAVIGATION & GAMEPLAY SCRIPTS ---

    function switchInnerGame(game) {
        const games = ['balloon', 'sudoku', 'logic', 'chess', 'strategy'];
        games.forEach(g => {
            const section = document.getElementById(`inner-game-${g}`);
            const btn = document.getElementById(`inner-game-${g}-btn`);

            if (g === game) {
                section.classList.remove('hidden');
                btn.className = "pb-2 border-b-2 border-indigo-600 text-indigo-700 font-black cursor-pointer";
                if (g === 'sudoku' && !sudokuSolution) {
                    generateSudokuBoard();
                } else if (g === 'chess' && chessBoardState.length === 0) {
                    initializeChessGame();
                } else if (g === 'strategy' && board2048.length === 0) {
                    initialize2048Game();
                }
            } else {
                section.classList.add('hidden');
                btn.className = "pb-2 border-b-2 border-transparent hover:text-slate-800 cursor-pointer";
            }
        });
    }

    // --- SUDOKU 9x9 GAME CODE ---
    let sudokuSolution = null;
    let sudokuBoard = null;

    function generateSudokuBoard() {
        let base = [
            [5, 3, 4, 6, 7, 8, 9, 1, 2],
            [6, 7, 2, 1, 9, 5, 3, 4, 8],
            [1, 9, 8, 3, 4, 2, 5, 6, 7],
            [8, 5, 9, 7, 6, 1, 4, 2, 3],
            [4, 2, 6, 8, 5, 3, 7, 9, 1],
            [7, 1, 3, 9, 2, 4, 8, 5, 6],
            [9, 6, 1, 5, 3, 7, 2, 8, 4],
            [2, 8, 7, 4, 1, 9, 6, 3, 5],
            [3, 4, 5, 2, 8, 6, 1, 7, 9]
        ];

        // Shuffle numbers mapping to create variety
        let numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        for (let i = numbers.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [numbers[i], numbers[j]] = [numbers[j], numbers[i]];
        }
        
        let solution = base.map(row => row.map(val => numbers[val - 1]));
        sudokuSolution = solution;

        // Clear 35 cells for gameplay
        sudokuBoard = solution.map(row => [...row]);
        let cellsToClear = 35;
        while (cellsToClear > 0) {
            let r = Math.floor(Math.random() * 9);
            let c = Math.floor(Math.random() * 9);
            if (sudokuBoard[r][c] !== 0) {
                sudokuBoard[r][c] = 0;
                cellsToClear--;
            }
        }

        const container = document.getElementById('sudoku-grid-container');
        container.innerHTML = '';

        for (let r = 0; r < 9; r++) {
            for (let c = 0; c < 9; c++) {
                const val = sudokuBoard[r][c];
                const input = document.createElement('input');
                input.type = 'text';
                input.maxLength = '1';
                input.className = 'w-8 h-8 text-center text-xs font-black border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500 transition-all font-sans';
                
                // Add thicker inner lines for 3x3 block boundaries
                if (r === 2 || r === 5) input.className += ' border-b-2 border-b-slate-400';
                if (r === 8) input.className += ' border-b-0';
                if (c === 2 || c === 5) input.className += ' border-r-2 border-r-slate-400';
                if (c === 8) input.className += ' border-r-0';

                // Add rounded corners for outer boundary squares
                if (r === 0 && c === 0) input.className += ' rounded-tl-xl';
                if (r === 0 && c === 8) input.className += ' rounded-tr-xl';
                if (r === 8 && c === 0) input.className += ' rounded-bl-xl';
                if (r === 8 && c === 8) input.className += ' rounded-br-xl';

                if (val !== 0) {
                    input.value = val;
                    input.disabled = true;
                    input.classList.add('bg-slate-100', 'text-slate-800');
                } else {
                    input.value = '';
                    input.classList.add('bg-indigo-50/20', 'text-indigo-650');
                    input.dataset.row = r;
                    input.dataset.col = c;
                }
                container.appendChild(input);
            }
        }

        const msg = document.getElementById('sudoku-feedback-msg');
        msg.classList.add('hidden');
    }

    function verifySudoku() {
        if (!sudokuSolution) return;

        const container = document.getElementById('sudoku-grid-container');
        const inputs = container.querySelectorAll('input:not([disabled])');
        let isCorrect = true;

        inputs.forEach(input => {
            const r = parseInt(input.dataset.row);
            const c = parseInt(input.dataset.col);
            const val = parseInt(input.value.trim());

            if (isNaN(val) || val !== sudokuSolution[r][c]) {
                isCorrect = false;
                input.classList.add('border-rose-500', 'bg-rose-50');
            } else {
                input.classList.remove('border-rose-500', 'bg-rose-50');
            }
        });

        const msg = document.getElementById('sudoku-feedback-msg');
        msg.classList.remove('hidden');

        if (isCorrect) {
            msg.innerText = 'Jawaban Benar! Anda mendapatkan +20 XP!';
            msg.className = 'text-xs font-bold text-center text-emerald-600 mt-2 font-sans';
            
            // Call AJAX to award points in database
            saveGamePointsToDatabase(20, 'Menyelesaikan Zen Sudoku');
        } else {
            msg.innerText = 'Ada kotak yang masih kosong atau terisi angka salah. Coba teliti kembali!';
            msg.className = 'text-xs font-bold text-center text-rose-600 mt-2 font-sans';
        }
    }

    function saveGamePointsToDatabase(points, description) {
        fetch('{{ route("user.wellbeing.award-points") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ points: points, description: description })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Update local balances
                currentXpBalance = data.total_points;
                document.getElementById('reward-xp-balance').innerText = `${currentXpBalance} XP`;
                
                const gamificationBalance = document.querySelector('#w-section-gamification .text-2xl.font-black.text-slate-800');
                if (gamificationBalance) {
                    gamificationBalance.innerText = `${currentXpBalance} XP`;
                }

                // Play sound
                playHappyChime();
            }
        })
        .catch(err => console.error("Gagal mencatat poin:", err));
    }

    // --- LOGIC QUIZ CODE ---
    let currentQuizAnswer = null;

    function generateLogicQuiz() {
        const reloadBtn = document.getElementById('logic-quiz-reload-btn');
        const initialMsg = document.getElementById('logic-quiz-initial-msg');
        const quizBody = document.getElementById('logic-quiz-body');
        const questionEl = document.getElementById('logic-quiz-question');
        const optionsEl = document.getElementById('logic-quiz-options');
        const resultPanel = document.getElementById('logic-quiz-result-panel');

        reloadBtn.disabled = true;
        reloadBtn.innerText = 'Menyusun...';
        resultPanel.classList.add('hidden');

        fetch('{{ route("user.wellbeing.generate-logic-quiz") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            reloadBtn.disabled = false;
            reloadBtn.innerText = 'Ambil Teka-Teki Logika';

            if (data.success) {
                const q = data.quiz;
                currentQuizAnswer = q.answerIndex;

                initialMsg.classList.add('hidden');
                quizBody.classList.remove('hidden');

                questionEl.innerText = q.question;
                optionsEl.innerHTML = '';

                q.options.forEach((opt, idx) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'p-3 bg-white border border-slate-200 hover:border-indigo-500 rounded-xl text-xs font-bold transition-all text-slate-700 text-left hover:bg-indigo-50/10 cursor-pointer font-sans';
                    btn.innerText = opt;
                    btn.onclick = () => submitLogicQuizAnswer(idx, q.explanation);
                    optionsEl.appendChild(btn);
                });
            } else {
                alert("Gagal merancang teka-teki logika.");
            }
        })
        .catch(err => {
            console.error(err);
            reloadBtn.disabled = false;
            reloadBtn.innerText = 'Ambil Teka-Teki Logika';
            alert("Kesalahan jaringan.");
        });
    }

    function submitLogicQuizAnswer(index, explanation) {
        const resultPanel = document.getElementById('logic-quiz-result-panel');
        const statusTitle = document.getElementById('logic-quiz-status-title');
        const explanationEl = document.getElementById('logic-quiz-explanation');

        resultPanel.classList.remove('hidden');

        // Disable all option buttons
        const optionButtons = document.querySelectorAll('#logic-quiz-options button');
        optionButtons.forEach(btn => btn.disabled = true);

        if (index === currentQuizAnswer) {
            statusTitle.innerText = 'Jawaban Benar! (+15 XP)';
            statusTitle.className = 'text-xs font-black uppercase block text-emerald-600 font-sans';
            explanationEl.innerText = explanation;

            // Save points to database
            saveGamePointsToDatabase(15, 'Menyelesaikan Teka-Teki Logika');
        } else {
            statusTitle.innerText = 'Jawaban Kurang Tepat!';
            statusTitle.className = 'text-xs font-black uppercase block text-rose-600 font-sans';
            explanationEl.innerText = 'Jangan menyerah! ' + explanation;
            playSadChime();
        }
    }

    // --- CHESS GAME CODE ---
    let selectedSquare = null;
    let validMoves = [];
    let chessBoardState = [];
    let isChessGameOver = false;

    function initializeChessGame() {
        chessBoardState = [
            ['bR', 'bN', 'bB', 'bQ', 'bK', 'bB', 'bN', 'bR'],
            ['bP', 'bP', 'bP', 'bP', 'bP', 'bP', 'bP', 'bP'],
            ['', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', ''],
            ['wP', 'wP', 'wP', 'wP', 'wP', 'wP', 'wP', 'wP'],
            ['wR', 'wN', 'wB', 'wQ', 'wK', 'wB', 'wN', 'wR']
        ];
        selectedSquare = null;
        validMoves = [];
        isChessGameOver = false;
        document.getElementById('chess-status-msg').innerText = 'Bidak Putih silakan jalan. Klik bidak Anda!';
        renderChessBoard();
    }

    function renderChessBoard() {
        const unicodePieces = {
            'wP': '♟', 'wR': '♜', 'wN': '♞', 'wB': '♝', 'wQ': '♛', 'wK': '♚',
            'bP': '♟', 'bR': '♜', 'bN': '♞', 'bB': '♝', 'bQ': '♛', 'bK': '♚'
        };

        const grid = document.getElementById('chess-board-grid');
        grid.innerHTML = '';

        for (let r = 0; r < 8; r++) {
            for (let c = 0; c < 8; c++) {
                const piece = chessBoardState[r][c];
                const square = document.createElement('button');
                square.type = 'button';
                
                const isLight = (r + c) % 2 === 0;
                square.className = `w-11 h-11 flex items-center justify-center font-sans font-black text-2xl transition-all relative border border-slate-100 ${isLight ? 'bg-slate-50' : 'bg-indigo-50/30'} cursor-pointer`;

                if (selectedSquare && selectedSquare.r === r && selectedSquare.c === c) {
                    square.classList.add('bg-amber-100/80');
                } else if (validMoves.some(m => m.r === r && m.c === c)) {
                    square.classList.add('bg-emerald-100/50');
                    const dot = document.createElement('span');
                    dot.className = 'absolute w-2 h-2 bg-emerald-500 rounded-full opacity-60';
                    square.appendChild(dot);
                }

                if (piece) {
                    const isWhite = piece.startsWith('w');
                    const labelSpan = document.createElement('span');
                    
                    if (isWhite) {
                        labelSpan.className = 'text-3xl text-indigo-650 drop-shadow-sm select-none z-10';
                    } else {
                        labelSpan.className = 'text-3xl text-slate-800 drop-shadow-sm select-none z-10';
                    }
                    labelSpan.innerText = unicodePieces[piece];
                    square.appendChild(labelSpan);
                }

                square.onclick = () => handleChessSquareClick(r, c);
                grid.appendChild(square);
            }
        }
    }

    function handleChessSquareClick(r, c) {
        if (isChessGameOver) return;

        const piece = chessBoardState[r][c];
        
        if (validMoves.some(m => m.r === r && m.c === c)) {
            executeMove(selectedSquare.r, selectedSquare.c, r, c);
            selectedSquare = null;
            validMoves = [];
            renderChessBoard();

            if (!isChessGameOver) {
                document.getElementById('chess-status-msg').innerText = 'LUNOU AI sedang berpikir...';
                setTimeout(executeAIMove, 800);
            }
            return;
        }

        if (piece && piece.startsWith('w')) {
            selectedSquare = { r, c };
            validMoves = getLegalMoves(r, c, piece);
            renderChessBoard();
        } else {
            selectedSquare = null;
            validMoves = [];
            renderChessBoard();
        }
    }

    function executeMove(fromR, fromC, toR, toC) {
        const piece = chessBoardState[fromR][fromC];
        const targetPiece = chessBoardState[toR][toC];

        chessBoardState[toR][toC] = piece;
        chessBoardState[fromR][fromC] = '';

        if (targetPiece === 'bK') {
            document.getElementById('chess-status-msg').innerText = 'Selamat! Anda Menang (+30 XP)!';
            isChessGameOver = true;
            saveGamePointsToDatabase(30, 'Menang Main Catur Melawan LUNOU Chess AI');
        } else if (targetPiece === 'wK') {
            document.getElementById('chess-status-msg').innerText = 'King Anda tertangkap! LUNOU AI menang. Coba lagi!';
            isChessGameOver = true;
            playSadChime();
        }
    }

    function executeAIMove() {
        if (isChessGameOver) return;

        let moves = [];
        for (let r = 0; r < 8; r++) {
            for (let c = 0; c < 8; c++) {
                const piece = chessBoardState[r][c];
                if (piece && piece.startsWith('b')) {
                    const legal = getLegalMoves(r, c, piece);
                    legal.forEach(m => {
                        moves.push({ from: { r, c }, to: m, weight: getMoveWeight(m.r, m.c) });
                    });
                }
            }
        }

        if (moves.length === 0) {
            document.getElementById('chess-status-msg').innerText = 'LUNOU AI menyerah! Anda Menang (+30 XP)!';
            isChessGameOver = true;
            saveGamePointsToDatabase(30, 'Menang Main Catur Melawan LUNOU Chess AI');
            return;
        }

        moves.sort((a, b) => b.weight - a.weight);
        const maxWeight = moves[0].weight;
        const bestMoves = moves.filter(m => m.weight === maxWeight);
        const chosen = bestMoves[Math.floor(Math.random() * bestMoves.length)];

        executeMove(chosen.from.r, chosen.from.c, chosen.to.r, chosen.to.c);
        renderChessBoard();

        if (!isChessGameOver) {
            document.getElementById('chess-status-msg').innerText = 'Giliran Anda! Klik bidak Putih.';
        }
    }

    function getMoveWeight(r, c) {
        const target = chessBoardState[r][c];
        if (!target) return 0;
        if (target.endsWith('K')) return 1000;
        if (target.endsWith('Q')) return 9;
        if (target.endsWith('R')) return 5;
        if (target.endsWith('B') || target.endsWith('N')) return 3;
        if (target.endsWith('P')) return 1;
        return 0;
    }

    function getLegalMoves(r, c, piece) {
        let moves = [];
        const isWhite = piece.startsWith('w');
        const type = piece.substring(1);

        const checkAdd = (toR, toC) => {
            if (toR < 0 || toR >= 8 || toC < 0 || toC >= 8) return false;
            const target = chessBoardState[toR][toC];
            if (!target) {
                moves.push({ r: toR, c: toC });
                return true;
            }
            if (target.startsWith(isWhite ? 'b' : 'w')) {
                moves.push({ r: toR, c: toC });
            }
            return false;
        };

        if (type === 'P') {
            const dir = isWhite ? -1 : 1;
            const startRow = isWhite ? 6 : 1;

            if (r + dir >= 0 && r + dir < 8 && !chessBoardState[r + dir][c]) {
                moves.push({ r: r + dir, c: c });
                if (r === startRow && !chessBoardState[r + dir * 2][c]) {
                    moves.push({ r: r + dir * 2, c: c });
                }
            }
            for (let side of [-1, 1]) {
                const targetC = c + side;
                if (targetC >= 0 && targetC < 8 && r + dir >= 0 && r + dir < 8) {
                    const target = chessBoardState[r + dir][targetC];
                    if (target && target.startsWith(isWhite ? 'b' : 'w')) {
                        moves.push({ r: r + dir, c: targetC });
                    }
                }
            }
        } 
        else if (type === 'N') {
            const offsets = [
                [-2, -1], [-2, 1], [-1, -2], [-1, 2],
                [1, -2], [1, 2], [2, -1], [2, 1]
            ];
            offsets.forEach(o => checkAdd(r + o[0], c + o[1]));
        } 
        else if (type === 'B' || type === 'Q') {
            const dirs = [[-1, -1], [-1, 1], [1, -1], [1, 1]];
            dirs.forEach(d => {
                let step = 1;
                while (checkAdd(r + d[0] * step, c + d[1] * step)) {
                    step++;
                }
            });
        }
        
        if (type === 'R' || type === 'Q') {
            const dirs = [[-1, 0], [1, 0], [0, -1], [0, 1]];
            dirs.forEach(d => {
                let step = 1;
                while (checkAdd(r + d[0] * step, c + d[1] * step)) {
                    step++;
                }
            });
        } 
        else if (type === 'K') {
            const dirs = [
                [-1, -1], [-1, 0], [-1, 1],
                [0, -1],           [0, 1],
                [1, -1],  [1, 0],  [1, 1]
            ];
            dirs.forEach(d => checkAdd(r + d[0], c + d[1]));
        }

        return moves;
    }

    // --- 2048 STRATEGY GAME CODE ---
    let board2048 = [];
    let score2048 = 0;
    let earnedXpMilestones = { 500: false, 1000: false, 2048: false };

    function initialize2048Game() {
        board2048 = [
            [0, 0, 0, 0],
            [0, 0, 0, 0],
            [0, 0, 0, 0],
            [0, 0, 0, 0]
        ];
        score2048 = 0;
        earnedXpMilestones = { 500: false, 1000: false, 2048: false };
        document.getElementById('game-2048-score').innerText = '0';
        document.getElementById('game-2048-feedback').classList.add('hidden');

        spawn2048Tile();
        spawn2048Tile();
        render2048Board();
    }

    function spawn2048Tile() {
        let empties = [];
        for (let r = 0; r < 4; r++) {
            for (let c = 0; c < 4; c++) {
                if (board2048[r][c] === 0) {
                    empties.push({ r, c });
                }
            }
        }
        if (empties.length > 0) {
            const cell = empties[Math.floor(Math.random() * empties.length)];
            board2048[cell.r][cell.c] = Math.random() < 0.9 ? 2 : 4;
        }
    }

    function render2048Board() {
        const container = document.getElementById('game-2048-board');
        container.innerHTML = '';

        const tileColors = {
            0: 'bg-slate-200/50 text-transparent',
            2: 'bg-indigo-50 text-indigo-700 border border-indigo-100',
            4: 'bg-indigo-100 text-indigo-800 border border-indigo-200',
            8: 'bg-indigo-200 text-indigo-900 border border-indigo-300',
            16: 'bg-amber-100 text-amber-800 border border-amber-200',
            32: 'bg-amber-200 text-amber-900 border border-amber-300',
            64: 'bg-orange-100 text-orange-800 border border-orange-200',
            128: 'bg-rose-100 text-rose-800 border border-rose-200',
            256: 'bg-emerald-100 text-emerald-800 border border-emerald-200',
            512: 'bg-emerald-200 text-emerald-900 border border-emerald-300',
            1024: 'bg-purple-100 text-purple-800 border border-purple-200',
            2048: 'bg-purple-600 text-white border border-purple-700 shadow-md animate-pulse'
        };

        for (let r = 0; r < 4; r++) {
            for (let c = 0; c < 4; c++) {
                const val = board2048[r][c];
                const tile = document.createElement('div');
                
                const colorClass = tileColors[val] || 'bg-slate-900 text-white';
                tile.className = `flex items-center justify-center rounded-xl font-sans font-black text-sm select-none transition-all duration-100 ${colorClass}`;
                tile.innerText = val !== 0 ? val : '';
                container.appendChild(tile);
            }
        }
    }

    function move2048(dir) {
        let moved = false;

        const rotate = (matrix) => {
            const n = matrix.length;
            let res = Array.from({ length: n }, () => Array(n).fill(0));
            for (let r = 0; r < n; r++) {
                for (let c = 0; c < n; c++) {
                    res[c][n - 1 - r] = matrix[r][c];
                }
            }
            return res;
        };

        let rotations = 0;
        if (dir === 'up') rotations = 3;
        else if (dir === 'right') rotations = 2;
        else if (dir === 'down') rotations = 1;

        let tempBoard = board2048.map(row => [...row]);
        for (let i = 0; i < rotations; i++) {
            tempBoard = rotate(tempBoard);
        }

        for (let r = 0; r < 4; r++) {
            let row = tempBoard[r].filter(val => val !== 0);
            let nextRow = [];
            for (let c = 0; c < row.length; c++) {
                if (c + 1 < row.length && row[c] === row[c + 1]) {
                    const combined = row[c] * 2;
                    nextRow.push(combined);
                    score2048 += combined;
                    c++;
                    moved = true;
                } else {
                    nextRow.push(row[c]);
                }
            }
            while (nextRow.length < 4) {
                nextRow.push(0);
            }
            if (JSON.stringify(tempBoard[r]) !== JSON.stringify(nextRow)) {
                moved = true;
            }
            tempBoard[r] = nextRow;
        }

        const unRotations = (4 - rotations) % 4;
        for (let i = 0; i < unRotations; i++) {
            tempBoard = rotate(tempBoard);
        }

        if (moved) {
            board2048 = tempBoard;
            spawn2048Tile();
            render2048Board();
            document.getElementById('game-2048-score').innerText = score2048;
            check2048Milestones();
        }
    }

    function check2048Milestones() {
        const milestones = [500, 1000, 2048];
        const feedback = document.getElementById('game-2048-feedback');
        
        milestones.forEach(m => {
            if (score2048 >= m && !earnedXpMilestones[m]) {
                earnedXpMilestones[m] = true;
                feedback.classList.remove('hidden');
                
                let bonusXp = m === 2048 ? 20 : 10;
                feedback.innerText = `Skor Anda mencapai ${m}! Anda mendapatkan +${bonusXp} XP!`;
                
                saveGamePointsToDatabase(bonusXp, `Mencapai skor ${m} di 2048 Brain Strategy`);
            }
        });
    }

    // --- INTERACTIVE GUIDED TOUR DRIVER ---
    let currentTourStep = 0;
    const tourSteps = [
        {
            selector: '#dashboard-tab-work-btn',
            text: 'Ini adalah Pusat Kerja. Di sini Anda dapat memantau rate penyelesaian tugas Anda, melihat motivasi kerja dari LUNOU AI, dan mengelola tugas proyek harian Anda.',
            action: () => switchDashboardTab('work')
        },
        {
            selector: '#dashboard-tab-wellbeing-btn',
            text: 'Ini adalah Ruang Pemulihan & Refleksi. Di sini Anda bisa mengelola kesehatan mental dan emosi Anda. Mari beralih ke tab ini untuk melanjutkan panduan.',
            action: () => {
                switchDashboardTab('wellbeing');
                playHappyChime();
            }
        },
        {
            selector: '#w-tab-mental-friend-btn',
            text: 'Menu Teman Cerita LUNOU adalah ruang konseling pribadi Anda. Ceritakan kelelahan, stres, atau kekhawatiran Anda di sini untuk mendapatkan respon hangat penyejuk hati.',
            action: () => {
                switchDashboardTab('wellbeing');
                switchWellbeingSubTab('mental-friend');
            }
        },
        {
            selector: '#w-tab-mental-game-btn',
            text: 'Di Ruang Game & Asah Otak, Anda bisa menenangkan pikiran dengan Zen Balloon Popper, atau melatih fokus dengan Zen Sudoku 9x9, catur taktis, serta puzzle strategi 2048. Semua poin XP yang Anda peroleh akan tersimpan otomatis!',
            action: () => {
                switchDashboardTab('wellbeing');
                switchWellbeingSubTab('mental-game');
            }
        },
        {
            selector: '#w-tab-mental-reward-btn',
            text: 'Dan ini adalah Self-Reward Shop. Belanjakan akumulasi XP dari pekerjaan dan game Anda untuk mengklaim reward riil seperti kopi susu sore atau tidur siang sebagai bentuk apresiasi diri.',
            action: () => {
                switchDashboardTab('wellbeing');
                switchWellbeingSubTab('mental-reward');
            }
        },
        {
            selector: '.fixed.bottom-6, #chatbot-toggle, [onclick*="chatbot"]',
            text: 'Terakhir, floating widget LUNOU Companion selalu ada di sudut kanan bawah untuk mendengarkan obrolan Anda, menyemangati Anda, serta menampilkan daftar tugas kapan saja.',
            action: () => {}
        }
    ];

    function startGuidedTour() {
        playHappyChime();
        currentTourStep = 0;
        
        document.getElementById('guided-tour-overlay').classList.remove('hidden');
        document.getElementById('guided-tour-tooltip').classList.remove('hidden');
        
        showTourStep(0);
    }

    function showTourStep(stepIndex) {
        // Clear previous highlights
        document.querySelectorAll('.tour-highlight').forEach(el => el.classList.remove('tour-highlight'));

        if (stepIndex >= tourSteps.length) {
            endGuidedTour();
            return;
        }

        const step = tourSteps[stepIndex];
        
        // Execute tab switching action if any
        if (step.action) {
            step.action();
        }

        // Try to query the element
        let targetEl = null;
        if (step.selector) {
            const selectors = step.selector.split(',');
            for (let sel of selectors) {
                targetEl = document.querySelector(sel.trim());
                if (targetEl && targetEl.offsetWidth > 0 && targetEl.offsetHeight > 0) {
                    break;
                }
            }
        }

        // Update indicators & content
        document.getElementById('guided-tour-step-indicator').innerText = `${stepIndex + 1} / ${tourSteps.length}`;
        document.getElementById('guided-tour-text').innerText = step.text;
        
        const nextBtn = document.getElementById('guided-tour-next-btn');
        if (stepIndex === tourSteps.length - 1) {
            nextBtn.innerText = 'Selesai';
        } else {
            nextBtn.innerText = 'Lanjut';
        }

        if (targetEl) {
            // Apply highlight class
            targetEl.classList.add('tour-highlight');
            targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Position tooltip
            setTimeout(() => {
                const rect = targetEl.getBoundingClientRect();
                const tooltip = document.getElementById('guided-tour-tooltip');
                
                let top = rect.bottom + window.scrollY + 12;
                let left = rect.left + window.scrollX;

                // Adjust if tooltip overflows viewport height
                if (rect.bottom + 220 > window.innerHeight) {
                    top = rect.top + window.scrollY - tooltip.offsetHeight - 12;
                }
                
                // Adjust horizontally to fit window
                left = Math.max(16, Math.min(left, window.innerWidth - tooltip.offsetWidth - 16));

                tooltip.style.top = `${top}px`;
                tooltip.style.left = `${left}px`;
            }, 300);
        } else {
            // Center tooltip on screen if target element not found
            const tooltip = document.getElementById('guided-tour-tooltip');
            tooltip.style.top = '50%';
            tooltip.style.left = '50%';
            tooltip.style.transform = 'translate(-50%, -50%)';
            tooltip.style.position = 'fixed';
        }
    }

    function nextGuidedTourStep() {
        currentTourStep++;
        if (currentTourStep >= tourSteps.length) {
            endGuidedTour();
        } else {
            showTourStep(currentTourStep);
        }
    }

    function endGuidedTour() {
        // Clear highlights
        document.querySelectorAll('.tour-highlight').forEach(el => el.classList.remove('tour-highlight'));

        document.getElementById('guided-tour-overlay').classList.add('hidden');
        const tooltip = document.getElementById('guided-tour-tooltip');
        tooltip.classList.add('hidden');
        tooltip.style.transform = ''; // Clear center styling
        tooltip.style.position = 'absolute';
        
        playHappyChime();
        alert('Panduan selesai! Selamat menjelajahi fitur Yoimo.');
    }
</script>
@endpush
@endsection