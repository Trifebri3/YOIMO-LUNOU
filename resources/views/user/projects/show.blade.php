@extends('user.layouts.app')

@section('title', 'Workspace - ' . $project->name)

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <style>
        trix-toolbar .trix-button-row { display: flex; flex-wrap: wrap; gap: 2px; }
        trix-editor { 
            min-height: 90px !important; 
            max-height: 180px; 
            overflow-y: auto; 
            background-color: #f8fafc; 
            border-radius: 0.75rem; 
            border-color: #e2e8f0; 
            font-size: 0.75rem;
        }
        .trix-content { font-size: 0.75rem; line-height: 1.5; }
    </style>
@endpush

@section('content')
<div class="space-y-8 font-sans">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center justify-between">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Sticky Quick Sub-Navigation Bar for Proyek -->
    <div class="bg-white/95 backdrop-blur-md border border-slate-200/80 rounded-2xl p-3 sm:p-4 shadow-xs flex flex-wrap items-center justify-between gap-3 sticky top-20 z-20">
        <!-- Tombol Kembali & Breadcrumb Lengkap -->
        <div class="flex items-center gap-2 sm:gap-3 flex-wrap min-w-0">
            <a href="{{ route('user.dashboard') }}" 
               class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all flex items-center gap-1.5 shrink-0"
               title="Kembali ke Ruang Pribadi">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Dashboard</span>
            </a>

            <div class="flex items-center gap-1.5 text-xs text-slate-400 font-semibold truncate">
                <span>/</span>
                @if($project->company)
                    <a href="{{ route('user.company.workspace', $project->company->id) }}" class="text-slate-600 hover:text-indigo-600 truncate max-w-[120px] sm:max-w-none">
                        {{ $project->company->company_name }}
                    </a>
                    <span>/</span>
                @endif
                <span class="text-indigo-950 font-black truncate max-w-[150px] sm:max-w-none">{{ $project->name }}</span>
            </div>
        </div>

        <!-- Quick Switch Project Dropdown & Action -->
        <div class="flex items-center gap-2">
            @if(isset($navProjects) && $navProjects->count() > 1)
                <div class="relative">
                    <button type="button" onclick="document.getElementById('quickProjSwitchDropdown').classList.toggle('hidden')" 
                            class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-900 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        <span class="hidden sm:inline">Pindah Proyek</span>
                        <span class="sm:hidden">Proyek</span>
                        <svg class="w-3 h-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div id="quickProjSwitchDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 p-2 space-y-1">
                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 px-2.5 py-1 block">Pilih Proyek Lain:</span>
                        <div class="max-h-48 overflow-y-auto space-y-0.5">
                            @foreach($navProjects as $otherPj)
                                <a href="{{ route('user.projects.show', $otherPj->id) }}" 
                                   class="flex items-center justify-between px-2.5 py-2 rounded-xl text-xs font-bold transition-all {{ $otherPj->id === $project->id ? 'bg-indigo-50 text-indigo-800' : 'text-slate-600 hover:bg-slate-50' }}">
                                    <span class="truncate">{{ $otherPj->name }}</span>
                                    <span class="text-[9px] px-1.5 py-0.2 bg-slate-100 rounded text-slate-500">{{ $otherPj->progress_percentage }}%</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <button type="button" onclick="openQuickNavModal()" class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-slate-100 rounded-xl transition-all" title="Buka Pencarian Cepat (Ctrl+K)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </button>
        </div>
    </div>

    <!-- 1. Header Proyek Banner -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200">
                        {{ $project->category }}
                    </span>
                    <span class="text-xs text-slate-400 font-semibold">• {{ $project->company->company_name ?? 'Workspace' }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mt-2 tracking-tight">{{ $project->name }}</h1>
                <p class="text-xs text-slate-400 mt-1">
                    Client: <strong class="text-slate-700">{{ $project->client_name ?? 'Internal Team' }}</strong> • Deadline: <strong class="text-rose-600">{{ $project->deadline ? $project->deadline->format('d M Y') : 'Fleksibel' }}</strong>
                </p>
            </div>

            <div class="flex items-center gap-3">
                <div class="p-4 bg-slate-50 border border-slate-100 rounded-2xl text-center min-w-[100px]">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TAHAPAN</span>
                    <span class="text-xs font-black text-indigo-700 block mt-0.5">{{ $project->current_stage }}</span>
                </div>
                <div class="p-4 bg-indigo-50/50 border border-indigo-100 rounded-2xl text-center min-w-[100px]">
                    <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider block">PROGRES PROYEK</span>
                    <span class="text-xs font-black text-indigo-950 block mt-0.5">{{ $project->progress_percentage }}%</span>
                </div>
            </div>
        </div>

        <!-- 2. Navigasi 7 Tab Lengkap (Horizontal Scrollable pada Layar HP/iPad) -->
        <div class="pt-4 border-t border-slate-100 flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
            <a href="{{ route('user.projects.show', [$project->id, 'tab' => 'overview']) }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $activeTab === 'overview' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path></svg>
                <span>Dashboard Ringkasan</span>
            </a>

            <a href="{{ route('user.projects.show', [$project->id, 'tab' => 'tasks']) }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $activeTab === 'tasks' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                <span>Papan Tugas ({{ $allTasks->count() }})</span>
            </a>

            <a href="{{ route('user.projects.show', [$project->id, 'tab' => 'team']) }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $activeTab === 'team' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span>Tim Proyek ({{ count($project->team_matrix ?? []) }})</span>
            </a>

            <a href="{{ route('user.projects.show', [$project->id, 'tab' => 'roadmap']) }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $activeTab === 'roadmap' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span>Linimasa</span>
            </a>

            <a href="{{ route('user.projects.show', [$project->id, 'tab' => 'agendas']) }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $activeTab === 'agendas' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Agenda</span>
            </a>

            <a href="{{ route('user.projects.show', [$project->id, 'tab' => 'documents']) }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $activeTab === 'documents' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span>Dokumen & SOP</span>
            </a>

            <a href="{{ route('user.projects.show', [$project->id, 'tab' => 'logs']) }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $activeTab === 'logs' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                <span>Audit Log</span>
            </a>

            @if($project->is_financial_transparent || Auth::user()->role === 'finance')
                <a href="{{ route('user.projects.show', [$project->id, 'tab' => 'expenses']) }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $activeTab === 'expenses' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Laporan Belanja</span>
                </a>
            @endif

            <a href="{{ route('user.projects.show', [$project->id, 'tab' => 'ai_history']) }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $activeTab === 'ai_history' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                <img src="{{ asset('icon/11.png') }}" alt="LUNOU" class="w-4 h-4 object-contain">
                <span>Riwayat LUNOU AI</span>
            </a>
        </div>
    </div>

    <!-- 3. KONTEN TAB DINAMIS -->

    <!-- TAB 0: DASHBOARD RINGKASAN OVERVIEW PROYEK -->
    @if($activeTab === 'overview')
        <div class="space-y-8 animate-fade-in">
            <!-- 1. Row Atas: Stepper Ketercapaian Fase -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-6">
                <div>
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Fase & Status Proyek Sekarang</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Alur proses proyek dari inisiasi hingga rilis publik.</p>
                </div>

                @php
                    $stages = ['Planning', 'Development', 'Review', 'Revision', 'Launch'];
                    $currentStageIndex = array_search($project->current_stage, $stages);
                    if ($currentStageIndex === false) $currentStageIndex = 0;
                @endphp

                <!-- Stepper Progress Bar -->
                <div class="relative flex items-center justify-between w-full">
                    <!-- Background Connector Line -->
                    <div class="absolute left-0 right-0 top-1/2 -translate-y-1/2 h-1 bg-slate-100 rounded-full z-0"></div>
                    <!-- Active Progress Line -->
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-indigo-600 rounded-full z-0 transition-all duration-500"
                         style="width: {{ $currentStageIndex * 25 }}%"></div>

                    @foreach($stages as $index => $stage)
                        @php
                            $isCompleted = $index < $currentStageIndex;
                            $isActive = $index === $currentStageIndex;
                            $isFuture = $index > $currentStageIndex;
                        @endphp
                        <div class="relative z-10 flex flex-col items-center group">
                            <!-- Step Bullet -->
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-black transition-all duration-300
                                 {{ $isCompleted ? 'bg-indigo-600 text-white ring-4 ring-indigo-50' : 
                                    ($isActive ? 'bg-white border-2 border-indigo-600 text-indigo-600 ring-4 ring-indigo-100/75 scale-110' : 
                                                 'bg-white border border-slate-200 text-slate-400') }}">
                                @if($isCompleted)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </div>
                            <!-- Step Title -->
                            <span class="absolute top-12 text-[10px] font-black uppercase tracking-wider text-center w-24
                                 {{ $isActive ? 'text-indigo-600 font-black' : ($isCompleted ? 'text-slate-700' : 'text-slate-400') }}">
                                {{ $stage }}
                            </span>
                        </div>
                    @endforeach
                </div>
                <div class="h-8"></div>
            </div>

            <!-- 2. Row Tengah: Grid Metrik Data Statistik Proyek -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card Progres Proyek -->
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col justify-between space-y-4">
                    <div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">PROGRES PROYEK</span>
                        <h3 class="text-2xl font-black text-slate-800 mt-1">Ketercapaian Target</h3>
                    </div>
                    
                    <div class="flex items-center justify-center py-2">
                        <div class="relative w-28 h-28 flex items-center justify-center rounded-full bg-slate-50 border border-slate-100 ring-4 ring-indigo-50">
                            <span class="text-2xl font-black text-indigo-900">{{ $project->progress_percentage }}%</span>
                        </div>
                    </div>

                    <p class="text-[10px] text-slate-400 text-center">Persentase ketercapaian berdasarkan progress milestone linimasa.</p>
                </div>

                <!-- Card Statistik Tasks -->
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col justify-between space-y-4">
                    <div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">TUGAS & PENUGASAN</span>
                        <h3 class="text-2xl font-black text-slate-800 mt-1">Status Aktivitas</h3>
                    </div>

                    @php
                        $totalT = $allTasks->count();
                        $pctTodo = $totalT > 0 ? round(($allTasks->where('status', 'Todo')->count() / $totalT) * 100) : 0;
                        $pctProg = $totalT > 0 ? round(($allTasks->where('status', 'In Progress')->count() / $totalT) * 100) : 0;
                        $pctRev = $totalT > 0 ? round(($allTasks->where('status', 'Review')->count() / $totalT) * 100) : 0;
                        $pctComp = $totalT > 0 ? round(($allTasks->where('status', 'Completed')->count() / $totalT) * 100) : 0;
                    @endphp
                    <div class="space-y-2">
                        <div class="h-3 w-full bg-slate-100 rounded-full flex overflow-hidden">
                            <div class="bg-slate-300" style="width: {{ $pctTodo }}%" title="Todo"></div>
                            <div class="bg-amber-500" style="width: {{ $pctProg }}%" title="In Progress"></div>
                            <div class="bg-indigo-500" style="width: {{ $pctRev }}%" title="Review"></div>
                            <div class="bg-emerald-500" style="width: {{ $pctComp }}%" title="Completed"></div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-1 text-[10px] font-bold">
                            <div class="flex items-center gap-1 text-slate-500">
                                <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                                <span>Todo: {{ $allTasks->where('status', 'Todo')->count() }}</span>
                            </div>
                            <div class="flex items-center gap-1 text-amber-600">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span>In Progress: {{ $allTasks->where('status', 'In Progress')->count() }}</span>
                            </div>
                            <div class="flex items-center gap-1 text-indigo-600">
                                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                <span>Review: {{ $allTasks->where('status', 'Review')->count() }}</span>
                            </div>
                            <div class="flex items-center gap-1 text-emerald-600">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>Selesai: {{ $allTasks->where('status', 'Completed')->count() }}</span>
                            </div>
                        </div>
                    </div>

                    <p class="text-[10px] text-slate-400 text-center">Terdapat total {{ $totalT }} penugasan tim dalam proyek ini.</p>
                </div>

                <!-- Card Keuangan Proyek / Tim Proyek -->
                @if($project->is_financial_transparent || Auth::user()->role === 'finance')
                    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col justify-between space-y-4">
                        <div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">BELANJA OPERASIONAL</span>
                            <h3 class="text-2xl font-black text-slate-800 mt-1">Transparansi Keuangan</h3>
                        </div>

                        <div class="space-y-2.5">
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-400 font-bold">Total Budget:</span>
                                <span class="font-extrabold text-slate-700">Rp {{ number_format($project->budget, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-400 font-bold">Terpakai:</span>
                                <span class="font-extrabold text-rose-600">Rp {{ number_format($totalExpensesApproved, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-xs border-t border-slate-100 pt-1.5">
                                <span class="text-indigo-600 font-black">Sisa Anggaran:</span>
                                <span class="font-black text-indigo-700">Rp {{ number_format($remainingBudget, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <p class="text-[10px] text-slate-400 text-center">Data diambil dari laporan belanja proyek yang telah disetujui.</p>
                    </div>
                @else
                    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col justify-between space-y-4">
                        <div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">TIM PROYEK</span>
                            <h3 class="text-2xl font-black text-slate-800 mt-1">PIC Terdaftar</h3>
                        </div>

                        <div class="space-y-2 max-h-[100px] overflow-y-auto pr-1">
                            @forelse($project->team_matrix ?? [] as $tm)
                                <div class="flex items-center gap-2 text-xs py-1 border-b border-slate-50 last:border-0">
                                    <div class="w-5 h-5 rounded-lg bg-indigo-100 text-indigo-700 font-bold text-[9px] flex items-center justify-center uppercase">
                                        {{ substr($tm['name'] ?? 'U', 0, 2) }}
                                    </div>
                                    <span class="font-bold text-slate-700">{{ $tm['name'] ?? '-' }}</span>
                                    <span class="text-[9px] bg-slate-100 px-1.5 py-0.5 rounded text-slate-500 ml-auto">{{ $tm['role'] ?? '-' }}</span>
                                </div>
                            @empty
                                <div class="text-xs text-slate-400 italic">Belum ada tim yang ditugaskan.</div>
                            @endforelse
                        </div>

                        <p class="text-[10px] text-slate-400 text-center">Manajer proyek mengawasi seluruh aktivitas anggota tim.</p>
                    </div>
                @endif
            </div>

            <!-- 3. Row Bawah: Agenda Mini Kalender Proyek -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Kalender Agenda & Rapat Proyek</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Daftar jadwal diskusi teknis, koordinasi, dan presentasi milestone proyek.</p>
                    </div>
                    <span class="text-xs text-indigo-650 font-bold bg-indigo-50 border border-indigo-100/50 px-3 py-1 rounded-xl">
                        {{ date('F Y') }}
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Column Left: Interactive CSS Grid Month Calendar -->
                    <div class="lg:col-span-6 space-y-4">
                        @php
                            $today = date('j');
                            $daysInMonth = date('t');
                            $startDayOfWeek = date('N', strtotime(date('Y-m-01')));
                            
                            $agendaDays = [];
                            foreach ($project->agendas as $agenda) {
                                if ($agenda->start_date) {
                                    $day = intval($agenda->start_date->format('j'));
                                    $agendaDays[$day][] = $agenda;
                                }
                            }
                        @endphp
                        
                        <div class="grid grid-cols-7 gap-1 text-center text-xs font-bold text-slate-400 mb-2">
                            <span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span><span>Min</span>
                        </div>

                        <div class="grid grid-cols-7 gap-1">
                            @for($i = 1; $i < $startDayOfWeek; $i++)
                                <div class="h-10"></div>
                            @endfor

                            @for($day = 1; $day <= $daysInMonth; $day++)
                                @php
                                    $hasAgenda = isset($agendaDays[$day]);
                                    $isToday = $day == $today;
                                @endphp
                                <div class="h-10 rounded-xl flex flex-col items-center justify-center relative transition-all group cursor-pointer
                                     {{ $isToday ? 'bg-indigo-600 text-white font-black shadow-xs' : 
                                        ($hasAgenda ? 'bg-indigo-50 border border-indigo-100 text-indigo-700 font-extrabold hover:bg-indigo-100' : 'bg-slate-50/50 hover:bg-slate-100/50 text-slate-700') }}">
                                    <span>{{ $day }}</span>
                                    
                                    @if($hasAgenda)
                                        <span class="w-1.5 h-1.5 rounded-full {{ $isToday ? 'bg-white' : 'bg-indigo-600' }} mt-0.5"></span>
                                        
                                        <!-- Tooltip Hover Calendar Day -->
                                        <div class="absolute bottom-11 bg-slate-900 text-white text-[10px] p-2.5 rounded-xl hidden group-hover:block z-50 shadow-md w-40 text-left space-y-1">
                                            @foreach($agendaDays[$day] as $ag)
                                                <div class="border-b border-white/10 last:border-0 pb-1 last:pb-0">
                                                    <span class="font-extrabold block text-indigo-300">{{ $ag->start_date->format('H:i') }}</span>
                                                    <span class="font-medium block leading-snug truncate">{{ $ag->title }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endfor
                        </div>
                    </div>

                    <!-- Column Right: Agenda Schedule List -->
                    <div class="lg:col-span-6 space-y-4">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">DAFTAR JADWAL TERDEKAT</span>
                        <div class="space-y-3 max-h-[250px] overflow-y-auto pr-1">
                            @forelse($project->agendas->where('start_date', '>=', now()->startOfDay()) as $ag)
                                <div class="p-3.5 bg-slate-50 border border-slate-100 rounded-2xl flex items-start gap-3">
                                    <div class="shrink-0 p-2 bg-indigo-50 border border-indigo-100 rounded-xl text-center min-w-[50px]">
                                        <span class="text-[9px] font-black text-indigo-600 block uppercase">{{ $ag->start_date->format('M') }}</span>
                                        <span class="text-base font-black text-indigo-950 block">{{ $ag->start_date->format('d') }}</span>
                                    </div>
                                    <div class="space-y-1">
                                        <h4 class="text-xs font-black text-slate-800 leading-snug">{{ $ag->title }}</h4>
                                        <div class="flex items-center gap-2 text-[10px] text-slate-400 font-bold">
                                            <span>Jam: {{ $ag->start_date->format('H:i') }}</span>
                                            @if($ag->location_link)
                                                <span>•</span>
                                                <a href="{{ $ag->location_link }}" target="_blank" class="text-indigo-600 hover:underline">Link Rapat &rarr;</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center bg-slate-50 border border-dashed border-slate-100 rounded-2xl text-xs text-slate-400 italic">
                                    Tidak ada jadwal terdekat saat ini.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- LUNOU AI Project Assistant & Counselor -->
            <div class="bg-gradient-to-br from-indigo-950 via-slate-900 to-indigo-900 border border-indigo-950 text-white rounded-3xl p-6 sm:p-7 shadow-xl space-y-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <img src="{{ asset('icon/11.png') }}" alt="LUNOU" class="w-11 h-11 object-contain bg-white rounded-2xl p-1 shadow-sm">
                            <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full bg-emerald-500 border-2 border-slate-900 animate-pulse"></span>
                        </div>
                        <div>
                            <h3 class="text-sm font-black uppercase tracking-wider text-indigo-300">Asisten Proyek LUNOU AI</h3>
                            <p class="text-xs text-slate-400 font-bold">LUNOU peka & memantau detail kesehatan serta aktivitas proyekmu secara real-time.</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-black tracking-wider uppercase bg-indigo-500/20 border border-indigo-500/30 text-indigo-200 px-3 py-1 rounded-xl">
                        AI Peka Proyek
                    </span>
                </div>

                <!-- AI Project Health Insight -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-4.5 space-y-3">
                    <span class="text-[9px] font-black text-indigo-300 uppercase tracking-widest block">Analisis Kesehatan Proyek</span>
                    <div class="text-xs leading-relaxed text-slate-200 space-y-2">
                        {!! nl2br(e($projectAiInsight)) !!}
                    </div>
                </div>

                <!-- Chat & Counsel Area -->
                <div class="space-y-4 pt-2 border-t border-white/5">
                    <div class="flex items-center justify-between">
                        <span class="text-[9px] font-black text-indigo-300 uppercase tracking-widest">Konsultasi / Tanya LUNOU tentang Proyek ini</span>
                        <span class="text-[9px] text-slate-400 font-bold">Speech-to-Text Didukung</span>
                    </div>

                    <!-- Chat Feed Log -->
                    <div id="project-ai-chat-log" class="hidden max-h-[160px] overflow-y-auto space-y-3 pr-2 scrollbar-thin scrollbar-thumb-white/10">
                        <!-- Messages go here -->
                    </div>

                    <!-- Input Bar -->
                    <div class="flex items-center gap-2">
                        <!-- Mic Button -->
                        <button type="button" id="project-ai-mic-btn" onclick="toggleProjectSpeech()" class="p-3 bg-white/5 hover:bg-white/10 border border-white/10 text-slate-300 hover:text-rose-450 rounded-2xl transition-all shrink-0" title="Gunakan Suara (Voice to Text)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                            </svg>
                        </button>

                        <input type="text" id="project-ai-input" placeholder="Tanyakan LUNOU... (misal: 'Apakah budget aman?', 'Apa langkah selanjutnya?')" class="flex-1 px-4 py-2.5 bg-white/5 border border-white/10 rounded-2xl text-xs font-semibold text-white focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 outline-none placeholder:text-slate-500">

                        <button type="button" id="project-ai-send-btn" onclick="sendProjectChatMessage()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-2xl shadow-md transition-all shrink-0 flex items-center gap-1.5">
                            <span>Tanya AI</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    @endif

    <!-- TAB 9: RIWAYAT LUNOU AI (CHAT & AUTO-FILL HISTORY LOGS) -->
    @if($activeTab === 'ai_history')
        <div class="space-y-6 animate-fade-in font-sans">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Pusat Riwayat Asisten LUNOU AI</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Tinjau seluruh log konsultasi proyek dan laporan progres otomatis yang dipetakan oleh AI.</p>
                </div>
                <div class="flex items-center gap-1 bg-slate-100 border border-slate-200 rounded-2xl p-1 shrink-0 self-start sm:self-auto">
                    <button type="button" onclick="switchAiSubTab('counsel')" id="btn-sub-counsel" class="ai-subtab-btn px-4 py-1.5 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-wider shadow-xs transition-all">
                        Diskusi Proyek
                    </button>
                    <button type="button" onclick="switchAiSubTab('progress')" id="btn-sub-progress" class="ai-subtab-btn px-4 py-1.5 text-slate-600 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all">
                        Auto-Fill Progres
                    </button>
                </div>
            </div>

            <!-- Subtab 1: Diskusi Proyek Chat Log -->
            <div id="subtab-ai-counsel" class="space-y-4">
                <div class="bg-gradient-to-br from-indigo-950 via-slate-900 to-indigo-900 border border-indigo-950 text-white rounded-3xl p-6 shadow-xl space-y-4">
                    <div class="flex items-center gap-2 pb-3 border-b border-white/5">
                        <img src="{{ asset('icon/11.png') }}" class="w-7 h-7 object-contain bg-white rounded-xl p-0.5">
                        <div>
                            <h3 class="text-xs font-black text-indigo-300 uppercase tracking-wider">Log Konsultasi LUNOU AI</h3>
                            <span class="text-[10px] text-slate-400 font-bold">Riwayat percakapan Anda tentang proyek "{{ $project->name }}"</span>
                        </div>
                    </div>

                    <!-- Scrollable Conversation Feed -->
                    <div class="max-h-[380px] overflow-y-auto space-y-4 pr-1 scrollbar-thin scrollbar-thumb-white/10" id="persisted-chat-log">
                        @forelse($projectAiChats as $chat)
                            @if($chat->role === 'user')
                                <div class="flex items-start gap-2.5 justify-end">
                                    <div class="space-y-1 text-right">
                                        <div class="bg-indigo-600 text-white rounded-2xl rounded-tr-none px-3.5 py-2 text-xs font-semibold max-w-[85%] ml-auto shadow-xs text-left">
                                            {{ $chat->message }}
                                        </div>
                                        <span class="text-[9px] text-slate-450 font-bold block mt-0.5">{{ $chat->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-start gap-2.5">
                                    <img src="{{ asset('icon/11.png') }}" class="w-6 h-6 object-contain bg-white rounded-lg p-0.5 mt-0.5 shadow-sm">
                                    <div class="space-y-1">
                                        <div class="bg-white/10 text-slate-100 rounded-2xl rounded-tl-none px-3.5 py-2 text-xs font-medium max-w-[85%] shadow-xs leading-relaxed">
                                            {!! nl2br(e($chat->message)) !!}
                                        </div>
                                        <span class="text-[9px] text-slate-450 font-bold block mt-0.5">{{ $chat->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="py-12 text-center text-slate-400 text-xs italic">
                                Belum ada riwayat diskusi. Tanyakan sesuatu pada panel LUNOU AI di Dashboard Ringkasan!
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Subtab 2: Auto-Fill Progres Logs -->
            <div id="subtab-ai-progress" class="hidden space-y-4">
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                    <span class="text-xs font-black text-slate-900 uppercase tracking-wider block">Riwayat Laporan Progres Tugas</span>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="border-b border-slate-150 text-slate-400 font-bold">
                                    <th class="pb-3 font-bold uppercase tracking-wider">Tanggal</th>
                                    <th class="pb-3 font-bold uppercase tracking-wider">Nama Tugas</th>
                                    <th class="pb-3 font-bold uppercase tracking-wider text-center">Progres</th>
                                    <th class="pb-3 font-bold uppercase tracking-wider">Catatan Kemajuan (AI)</th>
                                    <th class="pb-3 font-bold uppercase tracking-wider">Hambatan/Kendala</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($aiProgressLogs as $log)
                                    <tr class="hover:bg-slate-50/50 transition-all">
                                        <td class="py-3.5 text-slate-400 font-medium whitespace-nowrap">{{ $log->created_at->format('d M Y H:i') }}</td>
                                        <td class="py-3.5 font-bold text-slate-800">{{ $log->task->title ?? '-' }}</td>
                                        <td class="py-3.5 text-center">
                                            <span class="px-2 py-0.5 bg-indigo-50 border border-indigo-100 rounded-lg text-indigo-700 font-black">
                                                {{ $log->progress_percentage }}%
                                            </span>
                                        </td>
                                        <td class="py-3.5 text-slate-600 font-medium leading-relaxed max-w-[280px] break-words">{{ $log->notes }}</td>
                                        <td class="py-3.5 text-slate-600 font-medium whitespace-normal">
                                            @if($log->obstacles)
                                                <span class="px-2 py-0.5 bg-rose-50 border border-rose-100 text-rose-700 rounded-lg font-bold">
                                                    {{ $log->obstacles }}
                                                </span>
                                            @else
                                                <span class="text-slate-350 italic">Tidak ada</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-10 text-center text-slate-400 italic">
                                            Belum ada laporan progres bertahap yang terekam.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 1: PAPAN TUGAS DENGAN TIMER, PROGRES BERTAHAP & EVALUASI -->
    @if($activeTab === 'tasks')
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-black text-slate-900 tracking-tight">Daftar Aktivitas & Tugas Proyek</h2>
                    <p class="text-xs text-slate-400">Klik 'Mulai Tugas' untuk mengaktifkan timer. Laporkan progres bertahap (0-100%) dan evaluasi saat selesai.</p>
                </div>
                <span class="text-xs font-bold text-slate-400">{{ $allTasks->count() }} Tugas Terdaftar</span>
            </div>

            <!-- Quick Client-side Filters for Tasks on Mobile & Desktop -->
            <div class="flex flex-wrap items-center gap-1.5 p-3 bg-slate-50 border border-slate-100 rounded-2xl">
                <span class="text-[10px] font-bold text-slate-400 uppercase px-2 shrink-0">Filter Cepat:</span>
                <button type="button" onclick="filterUserTasks('all', this)" class="task-filter-btn px-3 py-1.5 bg-indigo-600 text-white rounded-xl text-xs font-bold shadow-xs transition-all">
                    Semua Tugas ({{ $allTasks->count() }})
                </button>
                <button type="button" onclick="filterUserTasks('my', this)" class="task-filter-btn px-3 py-1.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Tugas Saya ({{ $allTasks->where('assigned_to', Auth::id())->count() }})
                </button>
                <button type="button" onclick="filterUserTasks('open', this)" class="task-filter-btn px-3 py-1.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Tugas Terbuka ({{ $allTasks->whereNull('assigned_to')->where('status', '!=', 'Completed')->count() }})
                </button>
                <button type="button" onclick="filterUserTasks('completed', this)" class="task-filter-btn px-3 py-1.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Selesai ({{ $allTasks->where('status', 'Completed')->count() }})
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($allTasks as $task)
                    @php
                        $isMyTask = (Auth::id() === $task->assigned_to);
                        $isOpenTask = ($task->assigned_to === null);
                        $hasStarted = ($task->started_at !== null);
                    @endphp
                    <div class="project-task-card bg-white border {{ $isMyTask ? 'border-indigo-300 ring-2 ring-indigo-50 shadow-md' : ($isOpenTask ? 'border-dashed border-emerald-300 shadow-sm' : 'border-slate-100 shadow-sm') }} rounded-3xl p-6 space-y-4 transition-all flex flex-col justify-between"
                         data-task-my="{{ $isMyTask ? 'true' : 'false' }}"
                         data-task-open="{{ $isOpenTask ? 'true' : 'false' }}"
                         data-task-status="{{ $task->status }}">
                        
                        <div class="space-y-3">
                            <!-- Status & Timing Badge -->
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider {{ $task->status === 'Completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($task->status === 'Review' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-amber-50 text-amber-700 border border-amber-200') }}">
                                        {{ $task->status }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider {{ $task->priority === 'Urgent' ? 'bg-rose-50 text-rose-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $task->priority }}
                                    </span>
                                    @if($task->submission_timing_status)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[9px] font-black uppercase {{ $task->submission_timing_status === 'On Time' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                            @if($task->submission_timing_status === 'On Time')
                                                <svg class="w-2.5 h-2.5 fill-current text-emerald-700 shrink-0" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                                                <span>TEPAT WAKTU</span>
                                            @else
                                                <svg class="w-2.5 h-2.5 fill-current text-rose-700 shrink-0" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                                <span>TERLAMBAT</span>
                                            @endif
                                        </span>
                                    @endif
                                </div>

                                @if($isMyTask)
                                    <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase bg-indigo-600 text-white">
                                        TUGAS SAYA
                                    </span>
                                @elseif($isOpenTask)
                                    <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        TERBUKA
                                    </span>
                                @endif
                            </div>

                            <div>
                                <h3 class="text-base font-black text-slate-900">{{ $task->title }}</h3>
                                <span class="text-[10px] text-slate-400 font-semibold block mt-0.5">
                                    PIC: <strong class="text-slate-700">{{ $isOpenTask ? 'Belum Ada (Bisa Diklaim)' : ($task->assignee->name ?? '-') }}</strong>
                                </span>
                            </div>

                            <!-- Peringatan Catatan Revisi Jika Ditolak -->
                            @if($task->revision_notes)
                                <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-900 space-y-1">
                                    <div class="font-bold flex items-center gap-1.5 text-rose-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        <span>Catatan Revisi dari Management:</span>
                                    </div>
                                    <p class="font-medium leading-relaxed">{{ $task->revision_notes }}</p>
                                </div>
                            @endif

                            <!-- Bar Progres & Durasi Pengerjaan -->
                            <div class="space-y-1.5 p-3.5 bg-slate-50 rounded-2xl border border-slate-100">
                                <div class="flex justify-between text-[10px] font-bold text-slate-600">
                                    <span>Capaian Progres: {{ $task->progress_percentage }}%</span>
                                    <span>Durasi: {{ $task->duration_minutes }} Menit</span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: {{ $task->progress_percentage }}%"></div>
                                </div>
                                @if($hasStarted && $task->status === 'In Progress')
                                    <span class="text-[9px] text-indigo-600 font-bold block mt-1 animate-pulse">
                                        ⏱️ Timer berjalan sejak {{ $task->started_at->format('H:i, d M') }}
                                    </span>
                                @endif
                            </div>

                            <!-- Instruksi Detail -->
                            @if($task->description)
                                <div class="trix-content p-3 bg-slate-50 border border-slate-100 rounded-2xl text-slate-600 text-xs">
                                    {!! $task->description !!}
                                </div>
                            @endif

                            <!-- Riwayat Progres Bertahap Terhimpun -->
                            @if($task->progressLogs->count() > 0)
                                <div class="space-y-2 pt-2">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Riwayat Progres Terhimpun:</span>
                                    <div class="space-y-1.5 max-h-36 overflow-y-auto pr-1">
                                        @foreach($task->progressLogs as $pLog)
                                            <div class="p-2.5 bg-slate-50 border border-slate-100 rounded-xl text-xs space-y-1">
                                                <div class="flex items-center justify-between text-[10px]">
                                                    <span class="font-black text-indigo-700">{{ $pLog->progress_percentage }}% Tercapai</span>
                                                    <span class="text-slate-400">{{ $pLog->created_at->format('d M H:i') }}</span>
                                                </div>
                                                <p class="text-slate-700 font-medium">{{ $pLog->notes }}</p>
                                                @if($pLog->obstacles)
                                                    <p class="text-[10px] text-amber-700 font-semibold">Kendala: {{ $pLog->obstacles }}</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Action Bar Interaktif -->
                        <div class="pt-4 border-t border-slate-100 space-y-2">
                            <div class="flex items-center justify-between text-xs text-slate-400">
                                <span>Deadline: <strong class="text-slate-700">{{ $task->due_date ? $task->due_date->format('d M Y') : 'Fleksibel' }}</strong></span>
                            </div>

                            <div class="flex flex-wrap items-center gap-2 pt-1">
                                <!-- Tombol Klaim -->
                                @if($isOpenTask)
                                    <form method="POST" action="{{ route('user.projects.tasks.claim', [$project->id, $task->id]) }}" class="w-full">
                                        @csrf
                                        <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
                                            Ambil Tugas Ini
                                        </button>
                                    </form>
                                @endif

                                <!-- Tombol Mulai Timer (Jika Belum Dimulai) -->
                                @if($isMyTask && !$hasStarted && $task->status !== 'Completed')
                                    <form method="POST" action="{{ route('user.projects.tasks.start', [$project->id, $task->id]) }}" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all flex items-center justify-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path></svg>
                                            <span>Mulai Tugas (Timer)</span>
                                        </button>
                                    </form>
                                @endif

                                <!-- Tombol Log Progres Kecil Bertahap (misal 30%) -->
                                @if(($isMyTask || $isOpenTask) && $task->status !== 'Completed')
                                    <button type="button" onclick="openPartialModal({{ $task->id }}, '{{ addslashes($task->title) }}', {{ $task->progress_percentage }})" class="flex-1 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all">
                                        + Lapor Progres Bertahap
                                    </button>

                                    <!-- Tombol Pengajuan Selesai 100% -->
                                    <button type="button" onclick="openFinalModal({{ $task->id }}, '{{ addslashes($task->title) }}')" class="flex-1 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
                                        Selesai 100% & Evaluasi
                                    </button>
                                @endif

                                @if($task->status === 'Completed')
                                    @php
                                        $publicTaskUrl = route('public.task.show', $task->id);
                                        $taskProjName = $project->name ?? 'Proyek';
                                        $cleanTitle = addslashes($task->title);
                                        $cleanProj = addslashes($taskProjName);
                                        $taskCaption = "🎯 Senang sekali dapat menyelesaikan tugas \"{$cleanTitle}\" pada proyek \"{$cleanProj}\" di Yoimo Workspace!\n\n📌 Rincian Pencapaian:\n• Tugas: {$cleanTitle}\n• Proyek: {$cleanProj}\n• Status: Selesai 100% & Terverifikasi\n\nLihat bukti verifikasi penyelesaian tugas saya secara publik di sini:\n{$publicTaskUrl}\n\n#YoimoWorkspace #Productivity #ProjectManagement #WorkLifeHarmony #Achievement #KerjaCerdas";
                                    @endphp
                                    <button type="button" 
                                       onclick="openLinkedInShareModal('{{ $cleanTitle }}', 'Proyek: {{ $cleanProj }}', '{{ $publicTaskUrl }}', `{{ $taskCaption }}`)"
                                       class="w-full text-center py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all flex items-center justify-center gap-1.5 group">
                                        <svg class="w-4 h-4 fill-current opacity-90 group-hover:opacity-100" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>
                                        <span>Bagikan ke LinkedIn</span>
                                    </button>
                                @endif
                            </div>
                        </div>

                    </div>
                @empty
                    <div class="col-span-full bg-white border border-dashed border-slate-200 rounded-3xl p-12 text-center text-xs text-slate-400">
                        Belum ada tugas yang didaftarkan pada proyek ini.
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- TAB 2: TIM PROYEK -->
    @if($activeTab === 'team')
        <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
            <div>
                <h2 class="text-base font-black text-slate-900 tracking-tight">Daftar Anggota Tim & Penanggung Jawab</h2>
                <p class="text-xs text-slate-400">Seluruh rekan tim yang berkolaborasi dalam proyek ini.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($project->team_matrix ?? [] as $tm)
                    @php $mem = \App\Models\User::find($tm['user_id'] ?? null); @endphp
                    @if($mem)
                        <div class="p-4 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-3 truncate">
                                @if($mem->avatar)
                                    <img src="{{ asset('storage/' . $mem->avatar) }}" alt="{{ $mem->name }}" class="w-10 h-10 rounded-xl object-cover ring-1 ring-slate-200 shrink-0">
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-slate-900 text-white font-black text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($mem->name, 0, 2)) }}
                                    </div>
                                @endif
                                <div class="truncate">
                                    <div class="font-bold text-slate-900 truncate">{{ $mem->name }}</div>
                                    <div class="text-[10px] text-indigo-600 font-semibold">{{ $tm['role_title'] ?? 'Contributor' }}</div>
                                    <span class="text-[9px] text-slate-400 uppercase font-bold">{{ $mem->role }}</span>
                                </div>
                            </div>

                            @if($mem->phone)
                                <a href="https://wa.me/{{ $mem->phone }}" target="_blank" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-xl" title="Hubungi WA">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                </a>
                            @endif
                        </div>
                    @endif
                @empty
                    <p class="text-xs text-slate-400 italic">Belum ada tim yang didaftarkan.</p>
                @endforelse
            </div>
        </div>
    @endif

    <!-- TAB 3: AUDIT LOG AKTIVITAS -->
    @if($activeTab === 'logs')
        <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-base font-black text-slate-900 tracking-tight">Audit Trail & Rekaman Aktivitas Proyek</h2>
                <p class="text-xs text-slate-400">Pencatatan transparan seluruh riwayat penugasan, timer, dokumen, dan anggaran.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase">
                            <th class="py-3.5 px-6">Waktu</th>
                            <th class="py-3.5 px-6">Pelaku</th>
                            <th class="py-3.5 px-6">Modul & Aksi</th>
                            <th class="py-3.5 px-6">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-600">
                        @forelse($project->activityLogs as $log)
                            <tr>
                                <td class="py-3.5 px-6 whitespace-nowrap font-medium text-slate-700">
                                    {{ $log->created_at->format('d M H:i') }}
                                </td>
                                <td class="py-3.5 px-6 font-bold text-slate-900">{{ $log->user_name }}</td>
                                <td class="py-3.5 px-6">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-indigo-50 text-indigo-700">
                                        {{ $log->module }} • {{ $log->action }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-6">{{ $log->description }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400">Belum ada rekaman aktivitas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- TAB 4: LINIMASA / ROADMAP -->
    @if($activeTab === 'roadmap')
        <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
            <h2 class="text-base font-black text-slate-900 tracking-tight">Alur Linimasa & Target Capaian</h2>
            <div class="relative pl-6 border-l-2 border-indigo-200 space-y-8">
                @forelse($project->roadmaps as $idx => $rm)
                    <div class="relative bg-slate-50 border border-slate-100 rounded-3xl p-6 space-y-4">
                        <div class="absolute -left-[33px] top-6 w-5 h-5 rounded-full border-4 border-white shadow-sm {{ $rm->status === 'Completed' ? 'bg-emerald-500' : ($rm->status === 'In Progress' ? 'bg-indigo-600 animate-pulse' : 'bg-slate-400') }}"></div>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-black uppercase text-indigo-600">FASE {{ $idx + 1 }}</span>
                                <h3 class="text-base font-black text-slate-900">{{ $rm->title }}</h3>
                                <span class="text-xs text-slate-400 font-semibold">{{ $rm->start_date->format('d M Y') }} &rarr; {{ $rm->end_date->format('d M Y') }}</span>
                            </div>
                            <span class="px-3 py-1 rounded-xl text-[10px] font-black uppercase {{ $rm->status === 'Completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-indigo-100 text-indigo-800' }}">
                                {{ $rm->status }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic">Belum ada linimasa.</p>
                @endforelse
            </div>
        </div>
    @endif

    <!-- TAB 5: AGENDA -->
    @if($activeTab === 'agendas')
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($project->agendas as $ag)
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4 hover:shadow-md transition-all flex flex-col justify-between">
                    <div class="space-y-2">
                        <span class="px-2.5 py-0.5 rounded-lg text-[9px] font-black uppercase bg-indigo-50 text-indigo-700 border border-indigo-200">{{ $ag->category }}</span>
                        <h3 class="text-base font-black text-slate-900">{{ $ag->title }}</h3>
                    </div>
                    <div class="pt-3 border-t border-slate-100">
                        @if($ag->location_type === 'online' && $ag->meeting_url)
                            <a href="{{ $ag->meeting_url }}" target="_blank" class="w-full text-center py-2 bg-indigo-600 text-white font-bold text-xs rounded-xl block">Buka Link Meeting &rarr;</a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white border border-dashed border-slate-200 rounded-3xl p-12 text-center text-xs text-slate-400">Belum ada agenda.</div>
            @endforelse
        </div>
    @endif

    <!-- TAB 6: DOKUMEN & SOP -->
    @if($activeTab === 'documents')
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($project->repositoryDocuments as $doc)
                <div class="bg-white border {{ $doc->is_mandatory ? 'border-amber-300 ring-2 ring-amber-50' : 'border-slate-100' }} rounded-3xl p-6 shadow-sm space-y-4 flex flex-col justify-between">
                    <div>
                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-slate-100 text-slate-700">{{ $doc->category }}</span>
                        <h3 class="text-base font-black text-slate-900 mt-2">{{ $doc->title }}</h3>
                    </div>
                    <div class="pt-3 border-t border-slate-100">
                        @if($doc->doc_type === 'article')
                            <a href="{{ route('management.projects.documents.show', [$project->id, $doc->id]) }}" class="w-full text-center py-2 bg-indigo-600 text-white font-bold text-xs rounded-xl block">Buka Artikel Panduan</a>
                        @elseif($doc->doc_type === 'link')
                            <a href="{{ $doc->external_url }}" target="_blank" class="w-full text-center py-2 bg-purple-600 text-white font-bold text-xs rounded-xl block">Buka Tautan &rarr;</a>
                        @elseif($doc->doc_type === 'file' && $doc->file_path)
                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="w-full text-center py-2 bg-emerald-600 text-white font-bold text-xs rounded-xl block">Unduh File Dokumen</a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white border border-dashed border-slate-200 rounded-3xl p-12 text-center text-xs text-slate-400">Belum ada dokumen SOP.</div>
            @endforelse
        </div>
    @endif

    <!-- TAB 7: LAPORAN BELANJA -->
    @if($activeTab === 'expenses' && ($project->is_financial_transparent || Auth::user()->role === 'finance'))
        <div class="grid grid-cols-1 {{ Auth::user()->role === 'finance' ? 'xl:grid-cols-12' : '' }} gap-8">
            <!-- Kolom Kiri: Tabel Catatan Belanja -->
            <div class="{{ Auth::user()->role === 'finance' ? 'xl:col-span-8' : '' }} space-y-6">
                <!-- Rincian Pengeluaran Tim Card -->
                <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-4">
                    <div>
                        <h2 class="text-base font-black text-slate-900 tracking-tight">Transparansi Keuangan Proyek</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Rekapan belanja operasional dan infrastruktur proyek.</p>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/60 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="py-4 px-6">Item Belanja</th>
                                    <th class="py-4 px-6">Kategori</th>
                                    <th class="py-4 px-6">Tanggal</th>
                                    <th class="py-4 px-6">Nominal</th>
                                    <th class="py-4 px-6">Bukti Nota</th>
                                    @if(Auth::user()->role === 'finance')
                                        <th class="py-4 px-6 text-right">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-600">
                                @forelse($project->expenses as $exp)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="py-4 px-6">
                                            <div class="font-bold text-slate-900">{{ $exp->title }}</div>
                                            @if($exp->notes)
                                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $exp->notes }}</div>
                                            @endif
                                            <div class="text-[10px] text-indigo-600 font-semibold mt-0.5">
                                                Oleh: {{ $exp->uploader->name ?? 'Finance' }}
                                            </div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="px-2.5 py-1 bg-slate-100 border border-slate-200 rounded-lg text-[10px] font-bold text-slate-700">
                                                {{ $exp->category }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 font-medium text-slate-700">
                                            {{ $exp->expense_date->format('d M Y') }}
                                        </td>
                                        <td class="py-4 px-6 font-black text-rose-600">
                                            Rp {{ number_format($exp->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="py-4 px-6">
                                            @if($exp->receipt_file)
                                                <a href="{{ asset('storage/' . $exp->receipt_file) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg text-[10px] font-bold hover:bg-emerald-100 transition-all">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                    <span>Lihat Nota</span>
                                                </a>
                                            @else
                                                <span class="text-[10px] text-slate-400 italic">Tanpa Struk</span>
                                            @endif
                                        </td>
                                        @if(Auth::user()->role === 'finance')
                                            <td class="py-4 px-6 text-right">
                                                <form method="POST" action="{{ route('user.projects.expenses.destroy', [$project->id, $exp->id]) }}" onsubmit="return confirm('Hapus catatan belanja ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-xl transition-all" title="Hapus">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ Auth::user()->role === 'finance' ? 6 : 5 }}" class="py-8 text-center text-slate-400">Belum ada pengeluaran belanja yang dicatatkan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Form Input Pembelanjaan Baru (Hanya Finance) -->
            @if(Auth::user()->role === 'finance')
                <div class="xl:col-span-4 space-y-6">
                    <div class="bg-white border border-slate-100 rounded-3xl p-7 shadow-sm space-y-5">
                        <div>
                            <h2 class="text-sm font-black text-slate-900 tracking-tight">+ Catat Belanja Baru</h2>
                            <p class="text-xs text-slate-400 mt-0.5">Input nominal dan unggah bukti struk pembayaran.</p>
                        </div>

                        <form method="POST" action="{{ route('user.projects.expenses.store', $project->id) }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Nama Item / Keperluan</label>
                                <input type="text" name="title" required placeholder="misal: Langganan Server Cloud" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-emerald-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Kategori Belanja</label>
                                <select name="category" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-emerald-500">
                                    <option value="Infrastruktur & Server">Infrastruktur & Server</option>
                                    <option value="Lisensi & Software">Lisensi & Software</option>
                                    <option value="Operasional & Konsumsi">Operasional & Konsumsi</option>
                                    <option value="Peralatan & Hardware">Peralatan & Hardware</option>
                                    <option value="Honorarium & Jasa">Honorarium & Jasa</option>
                                    <option value="Marketing & Promosi">Marketing & Promosi</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Nominal Belanja (Rp)</label>
                                <input type="number" name="amount" required min="1" placeholder="0" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-black text-rose-600 focus:ring-emerald-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Tanggal Transaksi</label>
                                <input type="date" name="expense_date" required value="{{ date('Y-m-d') }}" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-emerald-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Unggah Bukti Struk / Nota (Opsional)</label>
                                <input type="file" name="receipt_file" accept="image/*,.pdf" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Catatan Tambahan (Opsional)</label>
                                <textarea name="notes" rows="2" placeholder="Nomor invoice atau keterangan transaksi..." class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-emerald-500"></textarea>
                            </div>

                            <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md transition-all">
                                Simpan Catatan Belanja
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    @endif

</div>

<!-- 1. MODAL LAPOR PROGRES BERTAHAP (MISAL: 30%, 60%) -->
<div id="partialModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-7 shadow-2xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-black text-slate-900">Lapor Progres Bertahap</h3>
                <span id="partialTaskTitle" class="text-xs text-indigo-600 font-bold block mt-0.5"></span>
            </div>
            <button type="button" onclick="closePartialModal()" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
        </div>

        <!-- LUNOU AI Auto-Fill Card -->
        <div class="bg-indigo-50/40 border border-indigo-150 rounded-2xl p-4.5 space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('icon/11.png') }}" alt="LUNOU" class="w-6 h-6 object-contain">
                    <span class="text-xs font-black text-indigo-950 uppercase tracking-tight">Auto-Fill via LUNOU AI</span>
                </div>
                <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
            </div>
            
            <div class="flex items-center gap-2">
                <!-- Mic Button -->
                <button type="button" id="ai-partial-mic-btn" onclick="togglePartialSpeech()" class="p-2.5 bg-white border border-slate-200 text-slate-400 hover:text-rose-500 rounded-xl transition-all shrink-0" title="Gunakan Suara (Voice to Text)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                    </svg>
                </button>
                <input type="text" id="ai-partial-prompt" placeholder="Bicara/tulis (misal: 'Slicing sudah 40% tidak ada kendala')" class="flex-1 px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                <button type="button" id="ai-partial-process-btn" onclick="processAIPartial()" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-xl shadow-sm transition-all shrink-0">
                    Proses
                </button>
            </div>
            <div id="ai-partial-status" class="hidden text-[10px] font-bold text-indigo-700 animate-pulse">LUNOU sedang menyusun progres tugas Anda...</div>
        </div>

        <form id="partialForm" method="POST" action="" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Persentase Progres Saat Ini (%)</label>
                <input type="number" name="progress_percentage" id="partialPercentage" min="1" max="99" required placeholder="misal: 30" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-black text-indigo-700">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan Progres Hari Ini</label>
                <textarea name="notes" rows="2" required placeholder="Apa saja yang telah berhasil diselesaikan hari ini..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kendala yang Dihadapi (Opsional)</label>
                <textarea name="obstacles" rows="2" placeholder="Hambatan teknis atau koordinasi jika ada..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Link URL / Repo / Demo (Opsional)</label>
                <input type="url" name="attachment_url" placeholder="https://..." class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closePartialModal()" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl">Batal</button>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md">
                    Simpan Progres Bertahap
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 2. MODAL PENGAJUAN SELESAI 100% + EVALUASI & KENDALA -->
<div id="finalModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-xl w-full p-7 shadow-2xl border border-slate-100 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-black text-slate-900">Pengajuan Selesai 100% & Evaluasi Kerja</h3>
                <span id="finalTaskTitle" class="text-xs text-indigo-600 font-bold block mt-0.5"></span>
            </div>
            <button type="button" onclick="closeFinalModal()" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
        </div>

        <!-- LUNOU AI Auto-Fill Card -->
        <div class="bg-indigo-50/40 border border-indigo-150 rounded-2xl p-4.5 space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('icon/11.png') }}" alt="LUNOU" class="w-6 h-6 object-contain">
                    <span class="text-xs font-black text-indigo-950 uppercase tracking-tight">Auto-Fill Selesai via LUNOU AI</span>
                </div>
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            </div>
            
            <div class="flex items-center gap-2">
                <!-- Mic Button -->
                <button type="button" id="ai-final-mic-btn" onclick="toggleFinalSpeech()" class="p-2.5 bg-white border border-slate-200 text-slate-400 hover:text-rose-500 rounded-xl transition-all shrink-0" title="Gunakan Suara (Voice to Text)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                    </svg>
                </button>
                <input type="text" id="ai-final-prompt" placeholder="Bicara/tulis (misal: 'Selesai 100% rilis DB, kendala lag dikit, evaluasi optimasi query')" class="flex-1 px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                <button type="button" id="ai-final-process-btn" onclick="processAIFinal()" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-xl shadow-sm transition-all shrink-0">
                    Proses
                </button>
            </div>
            <div id="ai-final-status" class="hidden text-[10px] font-bold text-indigo-700 animate-pulse">LUNOU sedang mengompilasi hasil tugas akhir Anda...</div>
        </div>

        <form id="finalForm" method="POST" action="" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Rincian Hasil Akhir (Rich Text)</label>
                <input id="final_notes" type="hidden" name="submission_notes">
                <trix-editor input="final_notes" placeholder="Rangkuman seluruh hasil kerja dan deliverable..."></trix-editor>
            </div>

            <!-- Form Kendala & Hambatan -->
            <div>
                <label class="block text-xs font-bold text-rose-700 uppercase mb-1">Kendala & Hambatan Selama Pengerjaan</label>
                <textarea name="obstacles_faced" rows="2" placeholder="Jelaskan kendala teknis, aset, atau komunikasi yang dihadapi..." class="w-full px-3.5 py-2.5 bg-rose-50/40 border border-rose-200 rounded-xl text-xs font-medium"></textarea>
            </div>

            <!-- Form Evaluasi Diri -->
            <div>
                <label class="block text-xs font-bold text-indigo-700 uppercase mb-1">Evaluasi & Saran Perbaikan Mandiri</label>
                <textarea name="self_evaluation" rows="2" placeholder="Apa yang bisa ditingkatkan untuk pengerjaan tugas berikutnya..." class="w-full px-3.5 py-2.5 bg-indigo-50/40 border border-indigo-200 rounded-xl text-xs font-medium"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Link Hasil / Demo URL</label>
                    <input type="url" name="submission_link" placeholder="https://..." class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Upload File Bukti / Dokumen</label>
                    <input type="file" name="submission_file" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeFinalModal()" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl">Batal</button>
                <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md">
                    Ajukan Selesai 100%
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
    <script>
        // Client-side quick filter logic
        function filterUserTasks(type, btn) {
            document.querySelectorAll('.task-filter-btn').forEach(b => {
                b.classList.remove('bg-indigo-600', 'text-white', 'shadow-xs');
                b.classList.add('bg-white', 'border', 'border-slate-200', 'text-slate-600');
            });
            btn.classList.add('bg-indigo-600', 'text-white', 'shadow-xs');
            btn.classList.remove('bg-white', 'border', 'border-slate-200', 'text-slate-600');

            document.querySelectorAll('.project-task-card').forEach(card => {
                const isCompleted = (card.getAttribute('data-task-status') === 'Completed');
                
                if (type === 'all') {
                    if (!isCompleted) {
                        card.classList.remove('hidden');
                    } else {
                        card.classList.add('hidden');
                    }
                } else if (type === 'my') {
                    if (card.getAttribute('data-task-my') === 'true' && !isCompleted) {
                        card.classList.remove('hidden');
                    } else {
                        card.classList.add('hidden');
                    }
                } else if (type === 'open') {
                    if (card.getAttribute('data-task-open') === 'true' && !isCompleted) {
                        card.classList.remove('hidden');
                    } else {
                        card.classList.add('hidden');
                    }
                } else if (type === 'completed') {
                    if (isCompleted) {
                        card.classList.remove('hidden');
                    } else {
                        card.classList.add('hidden');
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const defaultBtn = document.querySelector('.task-filter-btn');
            if (defaultBtn) {
                filterUserTasks('all', defaultBtn);
            }
        });

        function openPartialModal(taskId, taskTitle, currentPercentage) {
            document.getElementById('partialForm').action = `/user/projects/{{ $project->id }}/tasks/${taskId}/partial-log`;
            document.getElementById('partialTaskTitle').innerText = taskTitle;
            document.getElementById('partialPercentage').value = currentPercentage > 0 ? currentPercentage : 25;
            document.getElementById('partialModal').classList.remove('hidden');
        }

        function closePartialModal() {
            document.getElementById('partialModal').classList.add('hidden');
        }

        function openFinalModal(taskId, taskTitle) {
            document.getElementById('finalForm').action = `/user/projects/{{ $project->id }}/tasks/${taskId}/submit-final`;
            document.getElementById('finalTaskTitle').innerText = taskTitle;
            document.getElementById('finalModal').classList.remove('hidden');
        }

        function closeFinalModal() {
            document.getElementById('finalModal').classList.add('hidden');
        }

        // LUNOU AI Autocomplete Task forms
        let partialRecognition = null;
        let isPartialListening = false;

        function togglePartialSpeech() {
            const micBtn = document.getElementById('ai-partial-mic-btn');
            const input = document.getElementById('ai-partial-prompt');

            if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
                alert("Maaf, browser Anda tidak mendukung fitur Voice-to-Text. Silakan gunakan Google Chrome, Safari, atau Microsoft Edge.");
                return;
            }

            if (isPartialListening) {
                partialRecognition.stop();
                return;
            }

            const SpeechRecognitionClass = window.SpeechRecognition || window.webkitSpeechRecognition;
            partialRecognition = new SpeechRecognitionClass();
            partialRecognition.lang = 'id-ID';
            partialRecognition.continuous = false;
            partialRecognition.interimResults = false;

            partialRecognition.onstart = function() {
                isPartialListening = true;
                micBtn.classList.remove('text-slate-400');
                micBtn.classList.add('text-rose-500', 'bg-rose-50', 'animate-pulse');
                input.placeholder = "Mendengarkan...";
            };

            partialRecognition.onerror = function(event) {
                console.error("Speech recognition error", event.error);
                stopPartialSpeech();
            };

            partialRecognition.onend = function() {
                stopPartialSpeech();
            };

            partialRecognition.onresult = function(event) {
                const resultText = event.results[0][0].transcript;
                if (resultText) {
                    input.value = (input.value + " " + resultText).trim();
                }
            };

            partialRecognition.start();
        }

        function stopPartialSpeech() {
            isPartialListening = false;
            const micBtn = document.getElementById('ai-partial-mic-btn');
            const input = document.getElementById('ai-partial-prompt');
            if (micBtn) {
                micBtn.classList.remove('text-rose-500', 'bg-rose-50', 'animate-pulse');
                micBtn.classList.add('text-slate-400');
            }
            if (input) {
                input.placeholder = "Bicara/tulis (misal: 'Slicing sudah 40% tidak ada kendala')";
            }
        }

        function processAIPartial() {
            const promptInput = document.getElementById('ai-partial-prompt');
            const promptText = promptInput.value.trim();
            if (!promptText) return;

            const processBtn = document.getElementById('ai-partial-process-btn');
            const statusEl = document.getElementById('ai-partial-status');

            processBtn.disabled = true;
            statusEl.classList.remove('hidden');

            fetch('{{ route("user.tasks.auto-fill") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: promptText, type: 'partial' })
            })
            .then(res => {
                if (!res.ok) throw new Error("Gagal memetakan progress.");
                return res.json();
            })
            .then(data => {
                processBtn.disabled = false;
                statusEl.classList.add('hidden');

                if (data.success) {
                    document.getElementById('partialPercentage').value = data.progress_percentage || 25;
                    document.querySelector('#partialForm textarea[name="notes"]').value = data.notes || '';
                    document.querySelector('#partialForm textarea[name="obstacles"]').value = data.obstacles || '';
                    document.querySelector('#partialForm input[name="attachment_url"]').value = data.attachment_url || '';
                    
                    promptInput.value = '';
                    alert("LUNOU AI berhasil memetakan progres! Silakan periksa formulir.");
                } else {
                    alert("Gagal memetakan data: " + data.message);
                }
            })
            .catch(err => {
                console.error(err);
                processBtn.disabled = false;
                statusEl.classList.add('hidden');
                alert("Terjadi kesalahan koneksi.");
            });
        }

        let finalRecognition = null;
        let isFinalListening = false;

        function toggleFinalSpeech() {
            const micBtn = document.getElementById('ai-final-mic-btn');
            const input = document.getElementById('ai-final-prompt');

            if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
                alert("Maaf, browser Anda tidak mendukung fitur Voice-to-Text. Silakan gunakan Google Chrome, Safari, atau Microsoft Edge.");
                return;
            }

            if (isFinalListening) {
                finalRecognition.stop();
                return;
            }

            const SpeechRecognitionClass = window.SpeechRecognition || window.webkitSpeechRecognition;
            finalRecognition = new SpeechRecognitionClass();
            finalRecognition.lang = 'id-ID';
            finalRecognition.continuous = false;
            finalRecognition.interimResults = false;

            finalRecognition.onstart = function() {
                isFinalListening = true;
                micBtn.classList.remove('text-slate-400');
                micBtn.classList.add('text-rose-500', 'bg-rose-50', 'animate-pulse');
                input.placeholder = "Mendengarkan...";
            };

            finalRecognition.onerror = function(event) {
                console.error("Speech recognition error", event.error);
                stopFinalSpeech();
            };

            finalRecognition.onend = function() {
                stopFinalSpeech();
            };

            finalRecognition.onresult = function(event) {
                const resultText = event.results[0][0].transcript;
                if (resultText) {
                    input.value = (input.value + " " + resultText).trim();
                }
            };

            finalRecognition.start();
        }

        function stopFinalSpeech() {
            isFinalListening = false;
            const micBtn = document.getElementById('ai-final-mic-btn');
            const input = document.getElementById('ai-final-prompt');
            if (micBtn) {
                micBtn.classList.remove('text-rose-500', 'bg-rose-50', 'animate-pulse');
                micBtn.classList.add('text-slate-400');
            }
            if (input) {
                input.placeholder = "Bicara/tulis (misal: 'Selesai 100% rilis DB, kendala lag dikit, evaluasi optimasi query')";
            }
        }

        function processAIFinal() {
            const promptInput = document.getElementById('ai-final-prompt');
            const promptText = promptInput.value.trim();
            if (!promptText) return;

            const processBtn = document.getElementById('ai-final-process-btn');
            const statusEl = document.getElementById('ai-final-status');

            processBtn.disabled = true;
            statusEl.classList.remove('hidden');

            fetch('{{ route("user.tasks.auto-fill") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: promptText, type: 'final' })
            })
            .then(res => {
                if (!res.ok) throw new Error("Gagal memetakan progress.");
                return res.json();
            })
            .then(data => {
                processBtn.disabled = false;
                statusEl.classList.add('hidden');

                if (data.success) {
                    const trixEditor = document.querySelector('trix-editor');
                    if (trixEditor) {
                        trixEditor.editor.loadHTML(data.submission_notes || '');
                    } else {
                        document.getElementById('final_notes').value = data.submission_notes || '';
                    }

                    document.querySelector('#finalForm textarea[name="obstacles_faced"]').value = data.obstacles_faced || '';
                    document.querySelector('#finalForm textarea[name="self_evaluation"]').value = data.self_evaluation || '';
                    document.querySelector('#finalForm input[name="submission_link"]').value = data.submission_link || '';
                    
                    promptInput.value = '';
                    alert("LUNOU AI berhasil memetakan penyelesaian tugas! Silakan periksa formulir.");
                } else {
                    alert("Gagal memetakan data: " + data.message);
                }
            })
            .catch(err => {
                console.error(err);
                processBtn.disabled = false;
                statusEl.classList.add('hidden');
                alert("Terjadi kesalahan koneksi.");
            });
        }

        // Project Specific AI counselor Discussion
        let projectRecognition = null;
        let isProjectListening = false;

        function toggleProjectSpeech() {
            const micBtn = document.getElementById('project-ai-mic-btn');
            const input = document.getElementById('project-ai-input');

            if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
                alert("Maaf, browser Anda tidak mendukung fitur Voice-to-Text. Silakan gunakan Google Chrome, Safari, atau Microsoft Edge.");
                return;
            }

            if (isProjectListening) {
                projectRecognition.stop();
                return;
            }

            const SpeechRecognitionClass = window.SpeechRecognition || window.webkitSpeechRecognition;
            projectRecognition = new SpeechRecognitionClass();
            projectRecognition.lang = 'id-ID';
            projectRecognition.continuous = false;
            projectRecognition.interimResults = false;

            projectRecognition.onstart = function() {
                isProjectListening = true;
                micBtn.classList.remove('text-slate-300', 'bg-white/5');
                micBtn.classList.add('text-rose-500', 'bg-white/15', 'animate-pulse');
                input.placeholder = "Mendengarkan suara Anda...";
            };

            projectRecognition.onerror = function(event) {
                console.error("Speech recognition error", event.error);
                stopProjectSpeech();
            };

            projectRecognition.onend = function() {
                stopProjectSpeech();
            };

            projectRecognition.onresult = function(event) {
                const resultText = event.results[0][0].transcript;
                if (resultText) {
                    input.value = (input.value + " " + resultText).trim();
                }
            };

            projectRecognition.start();
        }

        function stopProjectSpeech() {
            isProjectListening = false;
            const micBtn = document.getElementById('project-ai-mic-btn');
            const input = document.getElementById('project-ai-input');
            if (micBtn) {
                micBtn.classList.remove('text-rose-500', 'bg-white/15', 'animate-pulse');
                micBtn.classList.add('text-slate-300', 'bg-white/5');
            }
            if (input) {
                input.placeholder = "Tanyakan LUNOU... (misal: 'Apakah budget aman?', 'Apa langkah selanjutnya?')";
            }
        }

        function sendProjectChatMessage() {
            const input = document.getElementById('project-ai-input');
            const message = input.value.trim();
            if (!message) return;

            const chatLog = document.getElementById('project-ai-chat-log');
            const sendBtn = document.getElementById('project-ai-send-btn');

            // Show chat log
            chatLog.classList.remove('hidden');

            // Add user message
            const userMsgDiv = document.createElement('div');
            userMsgDiv.className = 'flex items-start gap-2.5 justify-end';
            userMsgDiv.innerHTML = `
                <div class="bg-indigo-600 text-white rounded-2xl rounded-tr-none px-3.5 py-2 text-xs font-semibold max-w-[85%] shadow-xs">
                    ${escapeHtml(message)}
                </div>
            `;
            chatLog.appendChild(userMsgDiv);
            chatLog.scrollTop = chatLog.scrollHeight;

            input.value = '';
            sendBtn.disabled = true;

            // Add typewriter loading state from LUNOU
            const loadingMsgDiv = document.createElement('div');
            loadingMsgDiv.className = 'flex items-start gap-2.5';
            loadingMsgDiv.innerHTML = `
                <img src="{{ asset('icon/11.png') }}" class="w-6 h-6 object-contain bg-white rounded-lg p-0.5">
                <div class="bg-white/10 text-slate-100 rounded-2xl rounded-tl-none px-3.5 py-2 text-xs font-medium max-w-[85%] animate-pulse">
                    LUNOU sedang meninjau data proyek...
                </div>
            `;
            chatLog.appendChild(loadingMsgDiv);
            chatLog.scrollTop = chatLog.scrollHeight;

            fetch('{{ route("user.projects.discuss-ai", $project->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: message })
            })
            .then(res => {
                if (!res.ok) throw new Error("Gagal terhubung ke LUNOU.");
                return res.json();
            })
            .then(data => {
                sendBtn.disabled = false;
                chatLog.removeChild(loadingMsgDiv);

                if (data.success) {
                    const aiMsgDiv = document.createElement('div');
                    aiMsgDiv.className = 'flex items-start gap-2.5';
                    aiMsgDiv.innerHTML = `
                        <img src="{{ asset('icon/11.png') }}" class="w-6 h-6 object-contain bg-white rounded-lg p-0.5 shadow-sm">
                        <div class="bg-white/10 text-slate-100 rounded-2xl rounded-tl-none px-3.5 py-2 text-xs font-medium max-w-[85%] shadow-xs leading-relaxed">
                            ${data.reply.replace(/\n/g, '<br>')}
                        </div>
                    `;
                    chatLog.appendChild(aiMsgDiv);
                    chatLog.scrollTop = chatLog.scrollHeight;
                } else {
                    alert("LUNOU Error: " + data.message);
                }
            })
            .catch(err => {
                console.error(err);
                sendBtn.disabled = false;
                chatLog.removeChild(loadingMsgDiv);
                alert("Kesalahan koneksi.");
            });
        }

        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Project AI Subtab Switcher
        function switchAiSubTab(tab) {
            document.querySelectorAll('.ai-subtab-btn').forEach(btn => {
                btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-xs');
                btn.classList.add('text-slate-600');
            });

            document.getElementById('subtab-ai-counsel').classList.add('hidden');
            document.getElementById('subtab-ai-progress').classList.add('hidden');

            if (tab === 'counsel') {
                document.getElementById('btn-sub-counsel').classList.add('bg-indigo-600', 'text-white', 'shadow-xs');
                document.getElementById('btn-sub-counsel').classList.remove('text-slate-600');
                document.getElementById('subtab-ai-counsel').classList.remove('hidden');
            } else if (tab === 'progress') {
                document.getElementById('btn-sub-progress').classList.add('bg-indigo-600', 'text-white', 'shadow-xs');
                document.getElementById('btn-sub-progress').classList.remove('text-slate-600');
                document.getElementById('subtab-ai-progress').classList.remove('hidden');
            }
        }
    </script>
@endpush
@endsection