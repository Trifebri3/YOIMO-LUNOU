@extends('management.layouts.app')

@section('title', 'Project - ' . $project->name)

@section('content')
<div class="max-w-7xl mx-auto space-y-8 font-sans">

    <!-- Notifikasi Flash -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- 1. Header & Timeline Stage Tracker -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3 py-1 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-xl text-[10px] font-black uppercase tracking-wider">
                        {{ $project->category }}
                    </span>
                    <span class="text-xs text-slate-400 font-semibold">• {{ $project->company->company_name ?? 'Workspace' }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mt-2 tracking-tight">{{ $project->name }}</h1>
                <p class="text-xs text-slate-400 mt-1">
                    Client: <span class="font-bold text-slate-700">{{ $project->client_name ?? 'Internal Team' }}</span> • Dibuat oleh {{ $project->creator->name ?? 'Management' }}
                </p>
            </div>

            <!-- Action Toolbar Grid (Highly Responsive & Premium) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:flex lg:flex-wrap items-center gap-2 w-full lg:w-auto mt-4 lg:mt-0 font-sans text-xs">
                <!-- Papan Tugas -->
                <a href="{{ route('management.projects.tasks.index', $project->id) }}" class="flex items-center justify-center gap-1.5 px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-all shadow-xs">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    <span class="truncate">Papan Tugas</span>
                </a>

                <!-- Workspace Agenda -->
                <a href="{{ route('management.projects.agendas.index', $project->id) }}" class="flex items-center justify-center gap-1.5 px-3 py-2 bg-purple-50 hover:bg-purple-100 border border-purple-100 text-purple-700 font-bold rounded-xl transition-all">
                    <svg class="w-3.5 h-3.5 text-purple-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span class="truncate">Agenda & Event</span>
                </a>

                <!-- Laporan Belanja -->
                <a href="{{ route('management.projects.expenses.index', $project->id) }}" class="flex items-center justify-center gap-1.5 px-3 py-2 bg-emerald-50 hover:bg-emerald-100 border border-emerald-100 text-emerald-800 font-bold rounded-xl transition-all">
                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="truncate">Laporan Belanja</span>
                    @if(!$project->is_financial_transparent)
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-450 shrink-0" title="Transparansi dinonaktifkan"></span>
                    @endif
                </a>

                <!-- Perpustakaan Dokumen -->
                <a href="{{ route('management.projects.documents.index', $project->id) }}" class="flex items-center justify-center gap-1.5 px-3 py-2 bg-blue-50 hover:bg-blue-100 border border-blue-100 text-blue-700 font-bold rounded-xl transition-all">
                    <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <span class="truncate">Dokumen & SOP</span>
                </a>

                <!-- Linimasa / Roadmap -->
                <a href="{{ route('management.projects.roadmaps.index', $project->id) }}" class="flex items-center justify-center gap-1.5 px-3 py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 font-bold rounded-xl transition-all">
                    <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span class="truncate">Linimasa</span>
                </a>

                <!-- Portal Klien -->
                <button type="button" onclick="openClientPortalConfigModal()" class="flex items-center justify-center gap-1.5 px-3 py-2 bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-700 font-bold rounded-xl transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 10.742l-1.922-.641A3.001 3.001 0 1110 8c0 .411-.082.802-.232 1.157l1.922.641A3.001 3.001 0 1114 12c0-.411.082-.802.232-1.157l-1.922-.641A3.001 3.001 0 1110 8c0 .411.082.802.232 1.157z"></path></svg>
                    <span class="truncate">Portal Klien</span>
                </button>

                <!-- Seting Portofolio -->
                <a href="{{ route('management.projects.portfolio.edit', $project->id) }}" class="flex items-center justify-center gap-1.5 px-3 py-2 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-700 font-bold rounded-xl transition-all">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                    <span class="truncate">Seting Portofolio</span>
                </a>

                <!-- Log Aktivitas -->
                <a href="{{ route('management.projects.logs.index', $project->id) }}" class="flex items-center justify-center gap-1.5 px-3 py-2 bg-slate-900 hover:bg-black text-white font-bold rounded-xl transition-all shadow-xs">
                    <svg class="w-3.5 h-3.5 text-slate-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    <span class="truncate">Log Aktivitas</span>
                </a>

                <!-- Action Button Row for Edit & Delete (Full width on mobile grid, auto inline on desktop) -->
                <div class="flex items-center gap-2 col-span-2 sm:col-span-1 justify-center w-full lg:w-auto">
                    <!-- Edit Project -->
                    <a href="{{ route('management.projects.edit', $project->id) }}" class="flex-1 lg:flex-none px-3.5 py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-600 font-bold rounded-xl transition-all flex justify-center items-center" title="Edit Rincian Project">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </a>

                    <!-- Hapus Project (Form DELETE) -->
                    <form method="POST" action="{{ route('management.projects.destroy', $project->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus proyek ini beserta semua data di dalamnya secara permanen?')" class="flex-1 lg:flex-none">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-3.5 py-2 bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-600 hover:text-rose-750 font-bold rounded-xl transition-all cursor-pointer flex justify-center items-center" title="Hapus Project">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Visual Timeline Stages (Responsive Slider / Grid) -->
        @php
            $stages = ['Planning', 'Development', 'Review', 'Revision', 'Launch'];
            $currentIndex = array_search($project->current_stage, $stages);
        @endphp
        <div class="pt-4 border-t border-slate-100">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-3">Timeline Tahapan Proyek:</span>
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                @foreach($stages as $i => $stg)
                    <div class="p-3 text-center rounded-2xl border text-xs font-bold transition-all {{ $i <= $currentIndex ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                        {{ $i + 1 }}. {{ $stg }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- 2. Progress & Metric Dashboard -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">PROGRESS OVERALL</span>
            <div class="text-2xl font-black text-indigo-700 mt-1">{{ $project->progress_percentage }}%</div>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">DEADLINE</span>
            <div class="text-sm font-black text-slate-800 mt-2">{{ $project->deadline ? $project->deadline->format('d M Y') : 'TBD' }}</div>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">TOTAL BUDGET</span>
            <div class="text-sm font-black text-emerald-700 mt-2">Rp {{ number_format($project->budget, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">BUDGET TERPAKAI</span>
            <div class="text-sm font-black text-slate-800 mt-2">Rp {{ number_format($project->budget_spent, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- 3. Konten Utama Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Kolom Kiri: Brief, Scope, Deliverables & Milestones (8 Kolom) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Project Brief Box -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-7 shadow-sm space-y-4">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Project Brief</h2>
                <div class="space-y-3 text-xs">
                    <div>
                        <span class="font-bold text-slate-800 block">Masalah yang Diselesaikan:</span>
                        <p class="text-slate-600 mt-1 leading-relaxed">{{ $project->problem_statement ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="font-bold text-slate-800 block">Tujuan Project:</span>
                        <p class="text-slate-600 mt-1 leading-relaxed">{{ $project->project_goals ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="font-bold text-slate-800 block">Output yang Diharapkan:</span>
                        <p class="text-slate-600 mt-1 leading-relaxed">{{ $project->expected_outputs ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Scope In / Out -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-3">
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider block">Scope Termasuk (IN)</span>
                    <ul class="space-y-1.5 text-xs text-slate-600">
                        @forelse($project->scope_included ?? [] as $sc)
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                <span>{{ $sc }}</span>
                            </li>
                        @empty
                            <li class="text-slate-400 italic">Belum ada item scope.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-3">
                    <span class="text-xs font-bold text-rose-700 uppercase tracking-wider block">Scope Batasan (OUT)</span>
                    <ul class="space-y-1.5 text-xs text-slate-600">
                        @forelse($project->scope_excluded ?? [] as $se)
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                                <span>{{ $se }}</span>
                            </li>
                        @empty
                            <li class="text-slate-400 italic">Belum ada batasan scope.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Milestones & Deliverables -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-7 shadow-sm space-y-4">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Milestones & Deliverables</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <span class="text-[11px] font-bold text-slate-700 block mb-2">Milestones:</span>
                        <div class="space-y-2">
                            @forelse($project->milestones ?? [] as $m)
                                <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl text-xs flex items-center justify-between">
                                    <span class="font-bold text-slate-800">{{ $m['title'] ?? '' }}</span>
                                    <span class="text-[10px] text-indigo-600 font-bold">{{ $m['target'] ?? '' }}</span>
                                </div>
                            @empty
                                <span class="text-xs text-slate-400 italic">Belum ada milestones.</span>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <span class="text-[11px] font-bold text-slate-700 block mb-2">Deliverables:</span>
                        <div class="space-y-2">
                            @forelse($project->deliverables ?? [] as $d)
                                <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl text-xs font-medium text-slate-700">
                                    {{ $d }}
                                </div>
                            @empty
                                <span class="text-xs text-slate-400 italic">Belum ada deliverables.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- 🧠 LUNOU AI Project Assistant Panel (Management View) -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-7 shadow-xl space-y-6 text-slate-100 mt-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 border border-indigo-500/25 p-1.5 shrink-0">
                            <img src="{{ asset('icon/11.png') }}" alt="LUNOU" class="w-full h-full object-contain">
                        </div>
                        <div>
                            <h3 class="text-xs font-black text-white uppercase tracking-wider">LUNOU AI Project Assistant</h3>
                            <span class="text-[9px] font-bold text-indigo-300 block mt-0.5">Analisis Kesehatan & Konsultasi Tim</span>
                        </div>
                    </div>
                </div>

                <!-- AI Insight Report -->
                <div class="space-y-3">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Status Kesehatan Proyek (AI Report)
                    </h4>
                    <div class="p-4 bg-slate-800 border border-slate-700/80 rounded-2xl text-xs font-semibold text-slate-305 leading-relaxed space-y-3">
                        {!! nl2br(e($projectAiInsight)) !!}
                    </div>
                </div>

                <!-- Interactive Counselor Chat Console -->
                <div class="space-y-4 pt-2">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                        Diskusi Proyek Bersama LUNOU
                    </h4>

                    <!-- Persisted Chat History Feed -->
                    <div class="max-h-[250px] overflow-y-auto space-y-4 pr-1 scrollbar-thin scrollbar-thumb-white/10 {{ $projectAiChats->isEmpty() ? 'hidden' : '' }}" id="project-ai-chat-log">
                        @foreach($projectAiChats as $chat)
                            @if($chat->role === 'user')
                                <div class="flex items-start gap-2.5 justify-end">
                                    <div class="bg-indigo-650 text-white rounded-2xl rounded-tr-none px-3.5 py-2 text-xs font-semibold max-w-[85%] shadow-xs text-left">
                                        {{ $chat->message }}
                                    </div>
                                </div>
                            @else
                                <div class="flex items-start gap-2.5">
                                    <img src="{{ asset('icon/11.png') }}" class="w-6 h-6 object-contain bg-white rounded-lg p-0.5 mt-0.5">
                                    <div class="bg-white/10 text-slate-100 rounded-2xl rounded-tl-none px-3.5 py-2 text-xs font-medium max-w-[85%] shadow-xs leading-relaxed">
                                        {!! nl2br(e($chat->message)) !!}
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <!-- Input Footer Console -->
                    <div class="flex items-center gap-2">
                        <!-- Speech Mic Button -->
                        <button type="button" id="project-ai-mic-btn" onclick="toggleProjectSpeech()" class="p-3 bg-white/5 hover:bg-white/10 border border-white/10 text-slate-350 hover:text-white rounded-2xl transition-all shrink-0" title="Gunakan Suara (Voice to Text)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                            </svg>
                        </button>

                        <input type="text" id="project-ai-input" placeholder="Tanyakan LUNOU... (misal: 'Apakah budget aman?', 'Apa langkah selanjutnya?')" class="flex-1 px-4 py-2.5 bg-white/5 border border-white/10 rounded-2xl text-xs font-semibold text-white focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 outline-none placeholder:text-slate-500 font-semibold" onkeydown="if(event.key === 'Enter') sendProjectChatMessage()">

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

        <!-- Kolom Kanan: Roster Tim & Lampiran Dokumen (4 Kolom) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Team Roster Card -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Team Roster ({{ count($project->team_matrix ?? []) }})</h2>
                    <span class="text-[10px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-100">
                        Finance & User
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse($project->team_matrix ?? [] as $tm)
                        @php 
                            $u = \App\Models\User::find($tm['user_id'] ?? null); 
                        @endphp
                        @if($u)
                            <div class="p-3.5 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 truncate">
                                    @if($u->avatar)
                                        <img src="{{ asset('storage/' . $u->avatar) }}" alt="{{ $u->name }}" class="w-9 h-9 rounded-xl object-cover ring-1 ring-slate-200 shrink-0">
                                    @else
                                        <div class="w-9 h-9 rounded-xl bg-slate-900 text-white font-bold text-xs flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($u->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div class="truncate">
                                        <div class="text-xs font-bold text-slate-900 truncate">{{ $u->name }}</div>
                                        <div class="text-[10px] text-indigo-600 font-bold truncate">{{ $tm['role_title'] ?? 'Contributor' }}</div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider {{ $u->role === 'finance' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                        {{ $u->role }}
                                    </span>

                                    @if($u->phone)
                                        <a href="https://wa.me/{{ $u->phone }}" target="_blank" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Hubungi WhatsApp">
                                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @empty
                        <p class="text-xs text-slate-400 italic">Belum ada anggota tim yang ditugaskan.</p>
                    @endforelse
                </div>
            </div>

            <!-- Documents Card -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Lampiran & Dokumen</h2>
                <div class="space-y-2">
                    @forelse($project->documents ?? [] as $doc)
                        <a href="{{ asset('storage/' . ($doc['file_url'] ?? '')) }}" target="_blank" class="p-3 bg-slate-50 hover:bg-indigo-50 border border-slate-100 rounded-xl text-xs font-bold text-slate-700 hover:text-indigo-700 flex items-center justify-between transition-all">
                            <span>{{ $doc['title'] ?? 'Dokumen Project' }}</span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </a>
                    @empty
                        <p class="text-xs text-slate-400 italic">Belum ada dokumen yang diunggah.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@push('scripts')
<script>
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
            micBtn.classList.remove('text-slate-350', 'bg-white/5');
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
            micBtn.classList.add('text-slate-350', 'bg-white/5');
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
            <div class="bg-indigo-650 text-white rounded-2xl rounded-tr-none px-3.5 py-2 text-xs font-semibold max-w-[85%] shadow-xs">
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
            <img src="{{ asset('icon/11.png') }}" class="w-6 h-6 object-contain bg-white rounded-lg p-0.5 mt-0.5">
            <div class="bg-white/10 text-slate-100 rounded-2xl rounded-tl-none px-3.5 py-2 text-xs font-medium max-w-[85%] animate-pulse">
                LUNOU sedang meninjau data proyek...
            </div>
        `;
        chatLog.appendChild(loadingMsgDiv);
        chatLog.scrollTop = chatLog.scrollHeight;

        fetch('/user/projects/{{ $project->id }}/discuss-ai', {
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
                    <img src="{{ asset('icon/11.png') }}" class="w-6 h-6 object-contain bg-white rounded-lg p-0.5 shadow-sm mt-0.5">
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

    function openClientPortalConfigModal() {
        document.getElementById('client-portal-config-modal').classList.remove('hidden');
    }

    function closeClientPortalConfigModal() {
        document.getElementById('client-portal-config-modal').classList.add('hidden');
    }

    function copyClientShareLink() {
        const copyText = document.getElementById("client-share-link-input");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        alert("Link share klien berhasil disalin ke clipboard!");
    }
</script>

<!-- CLIENT PORTAL CONFIG MODAL (EMOJI-FREE) -->
@php
    $clientQuestions = \Illuminate\Support\Facades\DB::table('project_client_questions')
        ->where('project_id', $project->id)
        ->orderBy('created_at', 'desc')
        ->get();
@endphp
<div id="client-portal-config-modal" class="hidden fixed inset-0 flex items-center justify-center bg-slate-900/60 backdrop-blur-[2px] z-50 transition-all duration-300">
    <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-2xl w-full max-w-2xl mx-4 transform transition-all scale-95 duration-300 max-h-[85vh] overflow-y-auto">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-rose-50 border border-rose-100 rounded-2xl">
                        <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 10.742l-1.922-.641A3.001 3.001 0 1110 8c0 .411-.082.802-.232 1.157l1.922.641A3.001 3.001 0 1114 12c0-.411.082-.802.232-1.157l-1.922-.641A3.001 3.001 0 1110 8c0 .411.082.802.232 1.157z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-850 uppercase tracking-tight">Pengaturan Portal Klien</h4>
                        <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Berbagi progres dan komunikasi langsung dengan klien</p>
                    </div>
                </div>
                <button type="button" onclick="closeClientPortalConfigModal()" class="text-slate-400 hover:text-slate-600 font-bold text-lg cursor-pointer">&times;</button>
            </div>

            <!-- Link Configuration Box -->
            <div class="bg-slate-50 border border-slate-200/60 rounded-3xl p-5 space-y-4">
                @if($project->share_token)
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Link Portal Klien Aktif</label>
                        <div class="flex gap-2">
                            <input type="text" readonly id="client-share-link-input" value="{{ url('/shared/project/' . $project->share_token) }}"
                                   class="w-full px-4 py-2.5 text-xs bg-white border border-slate-200 rounded-xl font-mono text-slate-650 focus:outline-none">
                            <button type="button" onclick="copyClientShareLink()" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all cursor-pointer shrink-0">
                                Salin
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 pt-2">
                        <form method="POST" action="{{ route('management.projects.share-token', $project->id) }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all cursor-pointer">
                                Regenerasi Token
                              </button>
                        </form>
                        <form method="POST" action="{{ route('management.projects.disable-share', $project->id) }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-700 text-xs font-bold rounded-xl transition-all cursor-pointer">
                                Nonaktifkan Link
                            </button>
                        </form>
                    </div>
                @else
                    <div class="text-center py-4 space-y-3">
                        <p class="text-xs text-slate-500 font-semibold">Link Portal Klien saat ini belum aktif. Aktifkan untuk mengizinkan klien melihat kemajuan roadmap, progres tugas, laporan AI, dan mengajukan pertanyaan.</p>
                        <form method="POST" action="{{ route('management.projects.share-token', $project->id) }}">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all cursor-pointer">
                                Aktifkan Link Portal Klien
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Questions List -->
            <div class="space-y-4">
                <h5 class="text-xs font-black text-slate-800 uppercase tracking-wider">Pertanyaan dari Klien ({{ count($clientQuestions) }})</h5>

                @if($clientQuestions->isEmpty())
                    <div class="border border-dashed border-slate-200 rounded-2xl py-8 px-4 text-center">
                        <p class="text-xs text-slate-400 font-semibold">Belum ada pertanyaan dari klien yang diajukan.</p>
                    </div>
                @else
                    <div class="space-y-4 max-h-[30vh] overflow-y-auto pr-1">
                        @foreach($clientQuestions as $q)
                            <div class="p-4 bg-slate-50 border border-slate-200/60 rounded-2xl space-y-3">
                                <div class="flex items-center justify-between text-[10px] text-slate-400 font-semibold">
                                    <span>Dari: <strong class="text-slate-700 font-black">{{ $q->client_name }}</strong></span>
                                    <span>{{ \Carbon\Carbon::parse($q->created_at)->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs font-bold text-slate-800 leading-normal">{{ $q->question }}</p>

                                @if($q->answer)
                                    <div class="pl-3 border-l-2 border-emerald-500 space-y-1 pt-1">
                                        <span class="text-[9px] font-black text-emerald-700 uppercase tracking-wider block">Jawaban Tim:</span>
                                        <p class="text-xs font-semibold text-slate-600 leading-normal">{{ $q->answer }}</p>
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('management.projects.questions.answer', $q->id) }}" class="pt-2">
                                        @csrf
                                        <div class="flex gap-2">
                                            <input type="text" name="answer" required placeholder="Tulis jawaban Anda..."
                                                   class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-semibold text-slate-700">
                                            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all cursor-pointer shrink-0">
                                                Kirim
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="flex justify-end pt-3 border-t border-slate-100">
                <button type="button" onclick="closeClientPortalConfigModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endpush

@endsection