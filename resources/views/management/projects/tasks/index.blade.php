@extends('management.layouts.app')

@section('title', 'Manajemen Tugas - ' . $project->name)

@push('styles')
    <!-- Trix Rich Text Editor CDN -->
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <style>
        trix-toolbar .trix-button-row { display: flex; flex-wrap: wrap; gap: 2px; }
        trix-editor { 
            min-height: 120px !important; 
            max-height: 250px; 
            overflow-y: auto; 
            background-color: #f8fafc; 
            border-radius: 0.75rem; 
            border-color: #e2e8f0; 
            font-size: 0.75rem;
        }
        .trix-content { font-size: 0.75rem; line-height: 1.5; }
        .trix-content ul { list-style-type: disc; padding-left: 1.25rem; }
        .trix-content ol { list-style-type: decimal; padding-left: 1.25rem; }
    </style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto space-y-8 font-sans">

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

    <!-- Header Proyek -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase px-2.5 py-0.5 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200">
                        TASK HUB & TRANSPARANSI
                    </span>
                    <span class="text-xs font-semibold text-slate-400">• {{ $project->name }}</span>
                </div>
                <h1 class="text-2xl font-black text-slate-900 mt-1 tracking-tight">Daftar Penugasan & Dokumen Kerja</h1>
                <p class="text-xs text-slate-400 mt-0.5">Semua instruksi, link desain/repo, serta dokumen lampiran terbuka transparan untuk seluruh tim.</p>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="openGenerateTasksModal()" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-violet-600 text-white text-xs font-bold rounded-xl hover:from-indigo-700 hover:to-violet-700 shadow-sm flex items-center gap-2 hover:shadow transition-all cursor-pointer font-sans">
                    <svg class="w-3.5 h-3.5 animate-pulse text-indigo-200" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                    </svg>
                    <span>Generate Tugas via LUNOU AI</span>
                </button>

                <button type="button" onclick="openAddSingleTaskModal()" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-xs font-bold rounded-xl hover:from-emerald-600 hover:to-teal-600 shadow-sm flex items-center gap-2 hover:shadow transition-all cursor-pointer font-sans">
                    <svg class="w-3.5 h-3.5 animate-pulse text-emerald-100" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    <span>Tambah Tugas dengan LUNOU</span>
                </button>

                <a href="{{ route('management.projects.roadmaps.index', $project->id) }}" class="px-4 py-2 bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-100 transition-all font-sans">
                    Linimasa
                </a>
                <a href="{{ route('management.projects.show', $project->id) }}" class="px-4 py-2 bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-100 transition-all font-sans">
                    Detail Project
                </a>
            </div>
        </div>

        <!-- Task CSV Import/Export Panel -->
        <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                <span class="text-xs font-bold text-slate-700">Integrasi Spreadsheet Tugas (CSV)</span>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('management.projects.tasks.template', $project->id) }}" class="px-3.5 py-2 bg-slate-50 border border-slate-200 text-slate-700 hover:bg-slate-100 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5" title="Download Template CSV">
                    <span>Template CSV</span>
                </a>
                <a href="{{ route('management.projects.tasks.export', $project->id) }}" class="px-3.5 py-2 bg-slate-50 border border-slate-200 text-slate-700 hover:bg-slate-100 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5">
                    <span>Eksport Tugas</span>
                </a>
                <form method="POST" action="{{ route('management.projects.tasks.import', $project->id) }}" enctype="multipart/form-data" class="flex items-center gap-2 border-l border-slate-100 pl-3">
                    @csrf
                    <input type="file" name="csv_file" accept=".csv" required class="text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-semibold file:bg-indigo-50 file:text-indigo-700">
                    <button type="submit" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all">
                        Import
                    </button>
                </form>
            </div>
        </div>

        <!-- 4 Metrics Ringkasan -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 border-t border-slate-100">
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">TOTAL TUGAS</span>
                <div class="text-2xl font-black text-slate-800 mt-1">{{ $project->tasks->count() }}</div>
            </div>
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider">IN PROGRESS</span>
                <div class="text-2xl font-black text-amber-600 mt-1">{{ $project->tasks->where('status', 'In Progress')->count() }}</div>
            </div>
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider">REVIEW</span>
                <div class="text-2xl font-black text-indigo-600 mt-1">{{ $project->tasks->where('status', 'Review')->count() }}</div>
            </div>
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">SELESAI</span>
                <div class="text-2xl font-black text-emerald-600 mt-1">{{ $project->tasks->where('status', 'Completed')->count() }}</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">

        <!-- Kolom Kiri: Papan Tugas Transparan (7 Kolom) -->
        <div class="xl:col-span-7 space-y-6">
            <h2 class="text-base font-black text-slate-900 tracking-tight">Daftar Aktivitas & Tugas Tim</h2>

            <!-- Quick Client-side Filters for Tasks on Mobile & Desktop -->
            <div class="flex flex-wrap items-center gap-1.5 p-3 bg-slate-50 border border-slate-100 rounded-2xl">
                <span class="text-[10px] font-bold text-slate-400 uppercase px-2 shrink-0">Filter Cepat:</span>
                <button type="button" onclick="filterManagementTasks('all', this)" class="task-filter-btn px-3 py-1.5 bg-indigo-600 text-white rounded-xl text-xs font-bold shadow-xs transition-all">
                    Semua Tugas ({{ $project->tasks->count() }})
                </button>
                <button type="button" onclick="filterManagementTasks('my', this)" class="task-filter-btn px-3 py-1.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Tugas Saya ({{ $project->tasks->where('assigned_to', Auth::id())->count() }})
                </button>
                <button type="button" onclick="filterManagementTasks('open', this)" class="task-filter-btn px-3 py-1.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Tugas Terbuka ({{ $project->tasks->whereNull('assigned_to')->count() }})
                </button>
                <button type="button" onclick="filterManagementTasks('completed', this)" class="task-filter-btn px-3 py-1.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Selesai ({{ $project->tasks->where('status', 'Completed')->count() }})
                </button>
            </div>

            @forelse($project->tasks as $task)
                @php
                    $isOpenTask = ($task->assigned_to === null);
                    $isMyTask = (Auth::id() === $task->assigned_to);
                @endphp
                <div class="project-task-card bg-white border {{ $isMyTask ? 'border-indigo-300 ring-2 ring-indigo-50 shadow-md' : ($isOpenTask ? 'border-dashed border-emerald-300 shadow-sm' : 'border-slate-100 shadow-sm') }} rounded-3xl p-6 space-y-4 transition-all"
                     data-task-my="{{ $isMyTask ? 'true' : 'false' }}"
                     data-task-open="{{ $isOpenTask ? 'true' : 'false' }}"
                     data-task-status="{{ $task->status }}">
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <!-- Status Badge -->
                                @php
                                    $stBadge = match($task->status) {
                                        'Completed'   => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'Review'      => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'In Progress' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        default       => 'bg-slate-100 text-slate-600 border-slate-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider border {{ $stBadge }}">
                                    {{ $task->status }}
                                </span>

                                <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider {{ $task->priority === 'Urgent' ? 'bg-rose-50 text-rose-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $task->priority }}
                                </span>

                                <!-- Open vs My Task Badge -->
                                @if($isOpenTask)
                                    <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        TUGAS TERBUKA
                                    </span>
                                @elseif($isMyTask)
                                    <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider bg-indigo-600 text-white">
                                        TUGAS SAYA
                                    </span>
                                @endif
                            </div>

                            <h3 class="text-base font-black text-slate-900 mt-1">{{ $task->title }}</h3>
                        </div>

                        <!-- Info PIC -->
                        <div class="flex items-center gap-3 shrink-0">
                            <div class="text-right">
                                <span class="text-[10px] font-bold text-slate-400 block uppercase">PENANGGUNG JAWAB</span>
                                @if($isOpenTask)
                                    <span class="text-xs font-black text-emerald-600">Belum Diklaim</span>
                                @else
                                    <span class="text-xs font-bold text-slate-800">{{ $task->assignee->name ?? '-' }}</span>
                                @endif
                            </div>
                            @if($isOpenTask)
                                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 font-black text-xs flex items-center justify-center">
                                    OPEN
                                </div>
                            @elseif($task->assignee && $task->assignee->avatar)
                                <img src="{{ asset('storage/' . $task->assignee->avatar) }}" alt="Avatar" class="w-9 h-9 rounded-xl object-cover border border-slate-200">
                            @else
                                <div class="w-9 h-9 rounded-xl bg-slate-900 text-white font-black text-xs flex items-center justify-center">
                                    {{ strtoupper(substr($task->assignee->name ?? 'U', 0, 2)) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Instruksi Tugas (Render Rich Text HTML) -->
                    @if($task->description)
                        <div class="trix-content bg-slate-50 p-4 rounded-2xl border border-slate-100 text-slate-700">
                            {!! $task->description !!}
                        </div>
                    @endif

                    <!-- Lampiran dari Management (Link & File Awal) -->
                    @if($task->attachment_link || $task->attachment_file)
                        <div class="p-3 bg-slate-50/80 rounded-xl border border-slate-200/60 flex flex-wrap items-center gap-3 text-xs">
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Lampiran Panduan:</span>
                            @if($task->attachment_link)
                                <a href="{{ $task->attachment_link }}" target="_blank" class="text-indigo-600 font-bold underline flex items-center gap-1">
                                    <span>Tautan Rujukan / Figma</span> &rarr;
                                </a>
                            @endif
                            @if($task->attachment_file)
                                <a href="{{ asset('storage/' . $task->attachment_file) }}" target="_blank" class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg font-bold text-slate-700 hover:bg-slate-100">
                                    Unduh Dokumen Panduan
                                </a>
                            @endif
                        </div>
                    @endif

                    <!-- Info Linimasa & Deadline -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs pt-2 border-t border-slate-100">
                        <div class="text-slate-500 font-medium">
                            <span class="font-bold text-slate-700">Fase:</span> {{ $task->roadmap ? $task->roadmap->title : 'Umum / Non-Fase' }}
                        </div>
                        <div class="text-slate-500 font-medium sm:text-right">
                            <span class="font-bold text-slate-700">Deadline:</span> 
                            <span class="{{ $task->due_date && $task->due_date->isPast() && $task->status !== 'Completed' ? 'text-rose-600 font-bold' : '' }}">
                                {{ $task->due_date ? $task->due_date->format('d M Y') : 'Fleksibel' }}
                            </span>
                        </div>
                    </div>

                    <!-- Target Output Terikat -->
                    @if($task->roadmap && $task->linked_objective_index !== null)
                        @php
                            $targetText = $task->roadmap->objectives[$task->linked_objective_index]['target'] ?? 'Target output linimasa';
                            $isDone = $task->roadmap->objectives[$task->linked_objective_index]['is_achieved'] ?? false;
                        @endphp
                        <div class="p-3 bg-indigo-50/60 border border-indigo-100 rounded-xl text-xs flex items-center justify-between">
                            <div class="flex items-center gap-2 text-indigo-950 font-bold">
                                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                <span>Terikat Target: {{ $targetText }}</span>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase {{ $isDone ? 'bg-emerald-100 text-emerald-800' : 'bg-white text-indigo-700 border border-indigo-200' }}">
                                {{ $isDone ? 'Tercapai' : 'Proses' }}
                            </span>
                        </div>
                    @endif

                    <!-- Laporan Hasil Kerja (Submission) -->
                    @if($task->submission_notes)
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs space-y-2">
                            <div class="flex items-center justify-between font-bold text-slate-800">
                                <span>Laporan Hasil Kerja:</span>
                                <span class="text-[10px] text-slate-400">{{ $task->submitted_at ? $task->submitted_at->format('d M Y H:i') : '' }}</span>
                            </div>
                            <div class="trix-content text-slate-600 font-medium">
                                {!! $task->submission_notes !!}
                            </div>
                            
                            <div class="flex flex-wrap items-center gap-3 pt-2">
                                @if($task->submission_link)
                                    <a href="{{ $task->submission_link }}" target="_blank" class="text-indigo-600 font-bold underline flex items-center gap-1">
                                        <span>Link Hasil / Demo</span> &rarr;
                                    </a>
                                @endif
                                @if($task->submission_file)
                                    <a href="{{ asset('storage/' . $task->submission_file) }}" target="_blank" class="px-3 py-1 bg-white border border-slate-200 rounded-lg text-slate-700 font-bold hover:bg-slate-100">
                                        Unduh File Hasil Laporan
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Action Bar Bawah -->
                    <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100">
                        <form method="POST" action="{{ route('management.projects.tasks.update-status', [$project->id, $task->id]) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:ring-indigo-500">
                                <option value="Todo" {{ $task->status === 'Todo' ? 'selected' : '' }}>Todo</option>
                                <option value="In Progress" {{ $task->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Review" {{ $task->status === 'Review' ? 'selected' : '' }}>Review</option>
                                <option value="Completed" {{ $task->status === 'Completed' ? 'selected' : '' }}>Completed (Selesai)</option>
                            </select>
                        </form>

                        <div class="flex items-center gap-2">
                            <!-- Tombol Claim Task Jika Masih Terbuka -->
                            @if($isOpenTask)
                                <form method="POST" action="{{ route('management.projects.tasks.claim', [$project->id, $task->id]) }}">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <span>Ambil Tugas Ini</span>
                                    </button>
                                </form>
                            @endif

                            <!-- Tombol Kirim Laporan -->
                            @if($isOpenTask || $isMyTask || Auth::user()->role === 'management')
                                <button type="button" onclick="openSubmitModal({{ $task->id }}, '{{ addslashes($task->title) }}')" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
                                    {{ $task->submission_notes ? 'Perbarui Laporan' : 'Kirim Laporan Kerja' }}
                                </button>
                            @else
                                <span class="px-3 py-1.5 bg-slate-100 text-slate-400 text-xs font-semibold rounded-xl">
                                    Mode Pantau
                                </span>
                            @endif

                            <!-- Hapus Task (Management Only) -->
                            @if(Auth::user()->role === 'management')
                                <form method="POST" action="{{ route('management.projects.tasks.destroy', [$project->id, $task->id]) }}" onsubmit="return confirm('Hapus tugas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-xl transition-all" title="Hapus Tugas">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                </div>
            @empty
                <div class="bg-white border border-dashed border-slate-200 rounded-3xl p-12 text-center">
                    <p class="text-xs font-medium text-slate-400">Belum ada tugas yang dibuat untuk proyek ini.</p>
                </div>
            @endforelse

            <!-- Pagination Controls -->
            <div id="tasks-pagination-controls" class="flex items-center justify-between p-4 bg-slate-50 border border-slate-100 rounded-2xl mt-4">
                <button type="button" id="prev-page-btn" onclick="changeTasksPage(-1)" class="px-3.5 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                    &larr; Sebelumnya
                </button>
                <span id="page-info" class="text-xs font-bold text-slate-500">Halaman 1 dari 1</span>
                <button type="button" id="next-page-btn" onclick="changeTasksPage(1)" class="px-3.5 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                    Selanjutnya &rarr;
                </button>
            </div>
        </div>

        <!-- Kolom Kanan: Form Buat Tugas Baru (5 Kolom) -->
        <div class="xl:col-span-5 space-y-6">
            <div class="bg-white border border-slate-100 rounded-3xl p-7 shadow-sm space-y-5 sticky top-24 max-h-[calc(100vh-8rem)] overflow-y-auto">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-black text-slate-900 tracking-tight">+ Terbitkan Tugas Baru</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Isi secara manual atau gunakan LUNOU AI Copilot.</p>
                    </div>
                </div>

                <!-- LUNOU AI Task Assistant Panel -->
                <div class="p-4 bg-slate-900 text-slate-100 border border-slate-800 rounded-2xl space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-indigo-500/10 border border-indigo-500/25 p-0.5 shrink-0">
                                <img src="{{ asset('icon/11.png') }}" alt="LUNOU" class="w-full h-full object-contain">
                            </div>
                            <span class="text-[10px] font-black text-indigo-300 uppercase tracking-wider">LUNOU AI Task Copilot</span>
                        </div>
                        <button type="button" id="voice-ai-btn" onclick="toggleVoiceAI()" class="p-1.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-350 hover:text-white rounded-xl transition-all flex items-center justify-center relative" title="Gunakan Voice Command AI">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                            </svg>
                        </button>
                    </div>

                    <p class="text-[9px] text-slate-450 leading-normal">
                        Ketik instruksi tugas atau klik mic untuk bicara. LUNOU akan menganalisis brief proyek, roadmap/timeline, dan tim untuk merancang instruksi detail & otomatis mengisi form.
                    </p>

                    <div class="relative flex items-center gap-1.5">
                        <input type="text" id="ai-task-text-prompt" placeholder="Contoh: 'Tolong buat tugas bikin mockup Figma dan tunjuk Desainer'" class="flex-1 px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-[11px] text-white focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 outline-none placeholder:text-slate-500 font-semibold">
                        <button type="button" id="btn-run-task-ai" onclick="generateTaskWithAiPrompt()" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black rounded-xl transition-all shrink-0">
                            Rancang
                        </button>
                    </div>

                    <!-- Voice / Status Feedback message -->
                    <div id="voice-ai-feedback-msg" class="hidden text-[10px] text-indigo-300 font-bold animate-pulse"></div>
                </div>

                <form method="POST" action="{{ route('management.projects.tasks.store', $project->id) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Judul Tugas</label>
                        <input type="text" name="title" required placeholder="misal: Buat Slicing UI Komponen" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-indigo-500">
                    </div>

                    <!-- Rich Text Editor Trix -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Instruksi Detail (Rich Text)</label>
                        <input id="task_description" type="hidden" name="description">
                        <trix-editor input="task_description" placeholder="Tulis rincian instruksi kerja, format checklist, atau panduan..."></trix-editor>
                    </div>

                    <!-- Lampiran Link Eksternal -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Tautan Rujukan / Figma / Repo (Opsional)</label>
                        <input type="url" name="attachment_link" placeholder="https://figma.com/... atau https://github.com/..." class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                    </div>

                    <!-- Lampiran File Dokumen -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Lampirkan Dokumen / File Panduan (Opsional)</label>
                        <input type="file" name="attachment_file" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700">
                    </div>

                    <!-- Pilih User PIC (Bisa Terbuka / Tanpa PIC) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Pilih PIC Penanggung Jawab</label>
                        <select name="assigned_to" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-indigo-500">
                            <option value="">-- Tugas Terbuka (Bisa Dikerjakan Siapa Saja) --</option>
                            @foreach($teamMembers as $m)
                                <option value="{{ $m->id }}">{{ $m->name }} ({{ strtoupper($m->role) }} - {{ $m->position ?? 'Staff' }})</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1">Jika dikosongkan, tugas akan berstatus terbuka dan dapat diklaim oleh anggota tim.</p>
                    </div>

                    <!-- Pilih Fase Linimasa -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Fase Linimasa Terkait</label>
                        <select name="project_roadmap_id" id="roadmapSelect" onchange="updateObjectiveOptions()" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                            <option value="">-- Non-Fase / Tugas Umum --</option>
                            @foreach($project->roadmaps as $rm)
                                <option value="{{ $rm->id }}" data-objectives="{{ json_encode($rm->objectives ?? []) }}">
                                    {{ $rm->title }} ({{ $rm->start_date->format('d M') }} - {{ $rm->end_date->format('d M') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Keterikatan Target Output -->
                    <div id="objectiveContainer" class="hidden">
                        <label class="block text-xs font-bold text-indigo-700 uppercase mb-1.5">Kaitkan ke Target Output (Otomatis Checklist)</label>
                        <select name="linked_objective_index" id="objectiveSelect" class="w-full px-3.5 py-2.5 bg-indigo-50/50 border border-indigo-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                            <option value="">-- Tidak dikaitkan ke target spesifik --</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Prioritas</label>
                            <select name="priority" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                                <option value="Low">Low</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="High">High</option>
                                <option value="Urgent">Urgent</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Deadline</label>
                            <input type="date" name="due_date" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md transition-all">
                        Terbitkan Tugas
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>

<!-- Modal Kirim Laporan / Submission (Didukung Trix Editor) -->
<div id="submitModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-7 shadow-2xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-black text-slate-900">Kirim Laporan Hasil Tugas</h3>
                <span id="modalTaskTitle" class="text-xs text-indigo-600 font-bold block mt-0.5"></span>
            </div>
            <button type="button" onclick="closeSubmitModal()" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
        </div>

        <form id="submitForm" method="POST" action="" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Rincian Hasil Kerja (Rich Text)</label>
                <input id="sub_notes" type="hidden" name="submission_notes">
                <trix-editor input="sub_notes" placeholder="Deskripsikan hasil kerja, temuan, atau changelog..."></trix-editor>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">URL Hasil / Link Live Demo (Opsional)</label>
                <input type="url" name="submission_link" placeholder="https://..." class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Unggah Dokumen / Bukti File (Opsional)</label>
                <input type="file" name="submission_file" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700">
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeSubmitModal()" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl">Batal</button>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md">
                    Kirim & Ajukan Review
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <!-- Trix Editor JS -->
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

    <script>
        // Client-side quick filter and pagination logic for management task index
        let currentTasksPage = 1;
        const tasksPerPage = 4; // Display 4 tasks per page
        let activeFilterType = 'all';

        function filterManagementTasks(type, btn) {
            activeFilterType = type;
            currentTasksPage = 1; // Reset to page 1 on filter change
            
            document.querySelectorAll('.task-filter-btn').forEach(b => {
                b.classList.remove('bg-indigo-600', 'text-white', 'shadow-xs');
                b.classList.add('bg-white', 'border', 'border-slate-200', 'text-slate-600');
            });
            btn.classList.add('bg-indigo-600', 'text-white', 'shadow-xs');
            btn.classList.remove('bg-white', 'border', 'border-slate-200', 'text-slate-600');

            updatePaginatedTasks();
        }

        function updatePaginatedTasks() {
            const allCards = Array.from(document.querySelectorAll('.project-task-card'));
            
            const matchedCards = allCards.filter(card => {
                const isCompleted = (card.getAttribute('data-task-status') === 'Completed');
                
                if (activeFilterType === 'all') {
                    return !isCompleted;
                } else if (activeFilterType === 'my') {
                    return (card.getAttribute('data-task-my') === 'true' && !isCompleted);
                } else if (activeFilterType === 'open') {
                    return (card.getAttribute('data-task-open') === 'true' && !isCompleted);
                } else if (activeFilterType === 'completed') {
                    return isCompleted;
                }
                return false;
            });

            const totalTasks = matchedCards.length;
            const totalPages = Math.ceil(totalTasks / tasksPerPage) || 1;
            
            if (currentTasksPage > totalPages) {
                currentTasksPage = totalPages;
            }
            if (currentTasksPage < 1) {
                currentTasksPage = 1;
            }

            const startIndex = (currentTasksPage - 1) * tasksPerPage;
            const endIndex = startIndex + tasksPerPage;

            allCards.forEach(card => card.classList.add('hidden'));
            
            matchedCards.forEach((card, index) => {
                if (index >= startIndex && index < endIndex) {
                    card.classList.remove('hidden');
                }
            });

            const controls = document.getElementById('tasks-pagination-controls');
            if (controls) {
                if (totalTasks === 0) {
                    controls.classList.add('hidden');
                } else {
                    controls.classList.remove('hidden');
                    document.getElementById('page-info').innerText = `Halaman ${currentTasksPage} dari ${totalPages}`;
                    document.getElementById('prev-page-btn').disabled = (currentTasksPage === 1);
                    document.getElementById('next-page-btn').disabled = (currentTasksPage === totalPages);
                }
            }
        }

        function changeTasksPage(direction) {
            currentTasksPage += direction;
            updatePaginatedTasks();
            
            const tasksHeader = document.querySelector('h2.text-base.font-black.text-slate-900.tracking-tight');
            if (tasksHeader) {
                tasksHeader.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const defaultBtn = document.querySelector('.task-filter-btn');
            if (defaultBtn) {
                filterManagementTasks('all', defaultBtn);
            }
        });

        function updateObjectiveOptions() {
            const roadmapSelect = document.getElementById('roadmapSelect');
            const selectedOption = roadmapSelect.options[roadmapSelect.selectedIndex];
            const container = document.getElementById('objectiveContainer');
            const objectiveSelect = document.getElementById('objectiveSelect');

            objectiveSelect.innerHTML = '<option value="">-- Tidak dikaitkan ke target spesifik --</option>';

            if (selectedOption.value && selectedOption.dataset.objectives) {
                const objectives = JSON.parse(selectedOption.dataset.objectives);
                if (objectives.length > 0) {
                    objectives.forEach((obj, idx) => {
                        objectiveSelect.insertAdjacentHTML('beforeend', `
                            <option value="${idx}">Target ${idx + 1}: ${obj.target} ${obj.is_achieved ? '(Sudah Tercapai)' : ''}</option>
                        `);
                    });
                    container.classList.remove('hidden');
                    return;
                }
            }
            container.classList.add('hidden');
        }

        function openSubmitModal(taskId, taskTitle) {
            document.getElementById('submitForm').action = `/management/projects/{{ $project->id }}/tasks/${taskId}/submit-report`;
            document.getElementById('modalTaskTitle').innerText = taskTitle;
            document.getElementById('submitModal').classList.remove('hidden');
        }

        function closeSubmitModal() {
            document.getElementById('submitModal').classList.add('hidden');
        }
        let recognition = null;
        let isRecording = false;

        function toggleVoiceAI() {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (!SpeechRecognition) {
                alert("Browser Anda tidak mendukung Web Speech API. Silakan gunakan Google Chrome atau Microsoft Edge.");
                return;
            }

            const voiceBtn = document.getElementById('voice-ai-btn');
            const feedbackMsg = document.getElementById('voice-ai-feedback-msg');
            const promptInput = document.getElementById('ai-task-text-prompt');

            if (isRecording) {
                if (recognition) recognition.stop();
                return;
            }

            recognition = new SpeechRecognition();
            recognition.lang = 'id-ID';
            recognition.interimResults = false;
            recognition.maxAlternatives = 1;

            recognition.onstart = function() {
                isRecording = true;
                feedbackMsg.innerText = '🔴 LUNOU Mendengarkan suara Anda...';
                feedbackMsg.classList.remove('hidden');
                voiceBtn.classList.remove('bg-slate-800', 'text-slate-350');
                voiceBtn.classList.add('bg-red-500', 'text-white', 'animate-pulse');
            };

            recognition.onresult = function(event) {
                const transcript = event.results[0][0].transcript;
                promptInput.value = transcript;
                feedbackMsg.innerText = '✨ Menganalisis perintah suara...';
                generateTaskWithAiPrompt();
            };

            recognition.onerror = function(event) {
                console.error(event);
                feedbackMsg.innerText = '⚠️ Error Speech Recognition: ' + event.error;
            };

            recognition.onend = function() {
                isRecording = false;
                voiceBtn.classList.remove('bg-red-500', 'text-white', 'animate-pulse');
                voiceBtn.classList.add('bg-slate-800', 'text-slate-350');
            };

            recognition.start();
        }

        async function generateTaskWithAiPrompt() {
            const promptInput = document.getElementById('ai-task-text-prompt');
            const promptText = promptInput.value.trim();
            if (!promptText) {
                alert('Silakan tuliskan deskripsi instruksi tugas terlebih dahulu.');
                return;
            }

            const feedbackMsg = document.getElementById('voice-ai-feedback-msg');
            const btnRun = document.getElementById('btn-run-task-ai');

            btnRun.disabled = true;
            btnRun.innerText = 'AI...';
            feedbackMsg.innerText = '🧠 LUNOU sedang menganalisis proyek & merancang tugas...';
            feedbackMsg.classList.remove('hidden');

            try {
                const res = await fetch("{{ route('management.projects.tasks.generate-ai', $project->id) }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ prompt: promptText })
                });

                const json = await res.json();
                if (json.success && json.data) {
                    const d = json.data;

                    // 1. Fill Title
                    document.querySelector('input[name="title"]').value = d.title || '';

                    // 2. Fill Trix Editor (Rich Text)
                    const trixEditor = document.querySelector('trix-editor');
                    if (trixEditor) {
                        trixEditor.editor.loadHTML(d.description || '');
                    }

                    // 3. Fill Priority
                    if (d.priority) {
                        document.querySelector('select[name="priority"]').value = d.priority;
                    }

                    // 4. Fill Deadline
                    if (d.due_date) {
                        document.querySelector('input[name="due_date"]').value = d.due_date;
                    }

                    // 5. Fill PIC (Assigned to)
                    if (d.assigned_to !== undefined) {
                        document.querySelector('select[name="assigned_to"]').value = d.assigned_to || '';
                    }

                    // 6. Fill Roadmap/Timeline & Objective Index
                    if (d.project_roadmap_id !== undefined) {
                        const roadmapSelect = document.getElementById('roadmapSelect');
                        if (roadmapSelect) {
                            roadmapSelect.value = d.project_roadmap_id || '';
                            // Trigger objective options render
                            updateObjectiveOptions();

                            if (d.linked_objective_index !== undefined && d.linked_objective_index !== null) {
                                setTimeout(() => {
                                    const objSelect = document.getElementById('objectiveSelect');
                                    if (objSelect) {
                                        objSelect.value = d.linked_objective_index;
                                    }
                                }, 150);
                            }
                        }
                    }

                    feedbackMsg.innerText = '✅ Tugas berhasil dirancang secara otomatis!';
                    setTimeout(() => {
                        feedbackMsg.classList.add('hidden');
                    }, 3000);
                } else {
                    alert(json.message || 'Gagal merancang tugas.');
                    feedbackMsg.classList.add('hidden');
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan jaringan.');
                feedbackMsg.classList.add('hidden');
            } finally {
                btnRun.disabled = false;
                btnRun.innerText = 'Rancang';
            }
        }

        function openGenerateTasksModal() {
            document.getElementById('generateTasksModal').classList.remove('hidden');
        }

        function closeGenerateTasksModal() {
            document.getElementById('generateTasksModal').classList.add('hidden');
        }

        function openAddSingleTaskModal() {
            document.getElementById('addSingleTaskModal').classList.remove('hidden');
        }

        function closeAddSingleTaskModal() {
            document.getElementById('addSingleTaskModal').classList.add('hidden');
        }
    </script>
@endpush

<!-- Modal AI Generate Tasks (Custom Prompt) -->
<div id="generateTasksModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-7 shadow-2xl border border-slate-100 space-y-5">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                </svg>
                <span class="font-sans">Generate Tugas via LUNOU AI</span>
            </h3>
            <button type="button" onclick="closeGenerateTasksModal()" class="text-slate-400 hover:text-slate-600 font-bold text-lg cursor-pointer">&times;</button>
        </div>

        <form action="{{ route('management.projects.tasks.generate-roadmap-tasks-ai', $project->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin men-generate daftar tugas secara otomatis? Tugas-tugas baru akan ditambahkan tanpa menghapus atau mengubah tugas yang sudah ada.')" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5 font-sans">Instruksi Khusus (Opsional)</label>
                <textarea name="instruction" rows="3" placeholder="Contoh: Fokuskan pemecahan tugas pada bagian frontend terlebih dahulu, tambahkan detail integrasi Fonnte..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500 font-sans"></textarea>
                <p class="text-[10px] text-slate-400 mt-1 font-sans">Kosongkan jika ingin LUNOU AI membagi tugas secara otomatis berdasarkan seluruh KPI Roadmap yang ada.</p>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeGenerateTasksModal()" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl font-sans cursor-pointer">Batal</button>
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-violet-600 text-white text-xs font-bold rounded-xl shadow-md hover:from-indigo-700 hover:to-violet-700 font-sans cursor-pointer">
                    Mulai Generate
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal AI Tambah Single Task (Custom Prompt) -->
<div id="addSingleTaskModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-7 shadow-2xl border border-slate-100 space-y-5">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                </svg>
                <span class="font-sans">Tambah Tugas dengan LUNOU AI</span>
            </h3>
            <button type="button" onclick="closeAddSingleTaskModal()" class="text-slate-400 hover:text-slate-600 font-bold text-lg cursor-pointer">&times;</button>
        </div>

        <form action="{{ route('management.projects.tasks.add-single-ai', $project->id) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5 font-sans">Deskripsi Tugas yang Ingin Dibuat</label>
                <textarea name="prompt" rows="3" required placeholder="Contoh: Tolong buatkan tugas untuk membuat desain poster promosi produk di Instagram, beri tenggat waktu akhir minggu ini dan prioritas High." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-emerald-500 font-sans"></textarea>
                <p class="text-[10px] text-slate-400 mt-1 font-sans">LUNOU AI akan menganalisis prompt Anda dan langsung membuatkan tugas terperinci di database.</p>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeAddSingleTaskModal()" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl font-sans cursor-pointer">Batal</button>
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-xs font-bold rounded-xl shadow-md hover:from-emerald-600 hover:to-teal-600 font-sans cursor-pointer">
                    Buat Tugas
                </button>
            </div>
        </form>
    </div>
</div>
@endsection