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
    <div class="flex border-b border-slate-200">
        <button onclick="switchDashboardTab('work')" id="dashboard-tab-work-btn" class="px-6 py-3.5 border-b-2 border-indigo-600 text-indigo-750 font-black text-sm transition-all focus:outline-none">
            Pusat Kerja
        </button>
        <button onclick="switchDashboardTab('wellbeing')" id="dashboard-tab-wellbeing-btn" class="px-6 py-3.5 border-b-2 border-transparent text-slate-500 font-bold hover:text-slate-800 text-sm transition-all focus:outline-none flex items-center gap-1.5">
            Ruang Pemulihan & Refleksi
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
                                            $liTaskShareUrl = "https://www.linkedin.com/sharing/share-offsite/?url=" . urlencode(url('/'));
                                        @endphp
                                        <a href="{{ $liTaskShareUrl }}" target="_blank" 
                                           class="px-3.5 py-2 bg-blue-600 hover:bg-blue-750 text-white text-xs font-bold rounded-xl shadow-md transition-all flex items-center gap-1.5"
                                           title="Bagikan penyelesaian tugas ke LinkedIn">
                                            <span>Share LinkedIn</span>
                                        </a>
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
                                                    $publicUrl = urlencode(route('public.award.show', $aw->share_token));
                                                    $liShareUrl = "https://www.linkedin.com/sharing/share-offsite/?url=" . $publicUrl;
                                                @endphp
                                                <a href="{{ $liShareUrl }}" target="_blank" 
                                                   class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-bold rounded-lg transition-all">
                                                    Share LinkedIn
                                                </a>
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

            </div>

        </div>

    </div>

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
    const wellbeingSubTabs = ['today', 'auto-journal', 'journal', 'recovery', 'goals', 'reflection', 'leave', 'balance', 'gamification'];

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
</script>
@endpush
@endsection