@extends('user.layouts.app')

@section('title', 'Workspace - ' . $company->company_name)

@section('content')
<div class="space-y-8 font-sans">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- 1. Header Company Workspace Banner -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                @if($company->logo)
                    <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->company_name }}" class="w-16 h-16 rounded-2xl object-contain p-2 bg-slate-50 border border-slate-100 shrink-0">
                @else
                    <div class="w-16 h-16 rounded-2xl bg-indigo-600 text-white font-black text-xl flex items-center justify-center shrink-0">
                        {{ strtoupper(substr($company->company_name, 0, 2)) }}
                    </div>
                @endif
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200">
                            COMPANY WORKSPACE
                        </span>
                        <span class="text-xs text-slate-400 font-semibold">• {{ $company->industry ?? 'Technology & Service' }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1 tracking-tight">{{ $company->company_name }}</h1>
                    <p class="text-xs text-slate-400 mt-0.5">Manager: <strong class="text-slate-700">{{ $company->manager->name ?? 'Management Team' }}</strong></p>
                </div>
            </div>

            <!-- Public Portfolio Link -->
            @if($company->slug && $company->is_published)
                <a href="{{ route('public.company.show', $company->slug) }}" target="_blank" class="px-4 py-2.5 bg-slate-900 hover:bg-black text-white text-xs font-bold rounded-xl transition-all shadow-sm flex items-center gap-2">
                    <span>Lihat Profil Publik</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            @endif
        </div>

        <!-- Metrics Workspace Company -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 border-t border-slate-100">
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">PROYEK TERLIBAT</span>
                <div class="text-2xl font-black text-slate-800 mt-1">{{ $companyProjects->count() }}</div>
            </div>
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider block">TUGAS AKTIF SAYA</span>
                <div class="text-2xl font-black text-indigo-600 mt-1">{{ $myCompanyTasks->whereIn('status', ['Todo', 'In Progress'])->count() }}</div>
            </div>
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">AGENDA MENDATANG</span>
                <div class="text-2xl font-black text-emerald-600 mt-1">{{ $upcomingAgendas->count() }}</div>
            </div>
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                <span class="text-[10px] font-bold text-purple-600 uppercase tracking-wider block">DOKUMEN & SOP</span>
                <div class="text-2xl font-black text-purple-600 mt-1">{{ $companyDocuments->count() }}</div>
            </div>
        </div>
    </div>

    <!-- 2. Konten Utama Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">

        <!-- Kolom Kiri: Proyek Company & Tugas Saya di Company Ini (8 Kolom) -->
        <div class="xl:col-span-8 space-y-6">
            
            <!-- A. Daftar Proyek Aktif Company -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-black text-slate-900 tracking-tight">Proyek & Portfolio Berjalan</h2>
                    <span class="text-xs font-bold text-slate-400">{{ $companyProjects->count() }} Proyek</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse($companyProjects as $pj)
                        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col justify-between space-y-4 hover:shadow-md transition-all">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-lg text-[9px] font-black uppercase">
                                        {{ $pj->category }}
                                    </span>
                                    <span class="text-[10px] font-bold text-slate-400">{{ $pj->current_stage }}</span>
                                </div>
                                <h3 class="text-base font-black text-slate-900 leading-snug">{{ $pj->name }}</h3>
                                <p class="text-xs text-slate-500 line-clamp-2">{{ $pj->problem_statement ?? 'Proyek operasional perusahaan' }}</p>
                            </div>

                            <div class="space-y-3 pt-3 border-t border-slate-100">
                                <!-- Progress Bar -->
                                <div class="space-y-1">
                                    <div class="flex justify-between text-[10px] font-bold text-slate-500">
                                        <span>Progres</span>
                                        <span>{{ $pj->progress_percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-1.5">
                                        <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ $pj->progress_percentage }}%"></div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center gap-2 pt-1">
                                    <a href="{{ route('user.projects.show', [$pj->id, 'tab' => 'tasks']) }}" class="flex-1 text-center py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition-all">
                                        Papan Tugas
                                    </a>
                                    <a href="{{ route('user.projects.show', [$pj->id, 'tab' => 'roadmap']) }}" class="px-3 py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all">
                                        Linimasa
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full bg-white border border-dashed border-slate-200 rounded-3xl p-12 text-center text-xs text-slate-400">
                            Belum ada proyek aktif di company ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- B. Tugas Personal Saya di Company Ini -->
            <div class="space-y-4 pt-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-black text-slate-900 tracking-tight">Tugas Saya di Company Ini</h2>
                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-xl border border-indigo-100">
                        {{ $myCompanyTasks->count() }} Tugas
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse($myCompanyTasks as $cTask)
                        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center justify-between gap-4 hover:shadow-md transition-all">
                            <div class="space-y-1 truncate">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase {{ $cTask->status === 'Completed' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ $cTask->status }}
                                    </span>
                                    <span class="text-xs font-semibold text-slate-400">• {{ $cTask->project->name ?? 'Proyek' }}</span>
                                </div>
                                <h4 class="text-sm font-black text-slate-900 truncate">{{ $cTask->title }}</h4>
                            </div>

                            <a href="{{ route('user.projects.show', [$cTask->project_id, 'tab' => 'tasks']) }}" class="px-4 py-2 bg-slate-900 hover:bg-black text-white text-xs font-bold rounded-xl shrink-0 transition-all">
                                {{ $cTask->submission_notes ? 'Lihat Laporan' : 'Kirim Laporan' }}
                            </a>
                        </div>
                    @empty
                        <div class="bg-white border border-dashed border-slate-200 rounded-2xl p-8 text-center text-xs text-slate-400">
                            Tidak ada tugas personal yang tertunda di company ini.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Kolom Kanan: Agenda Tim, Tugas Terbuka & Repositori SOP (4 Kolom) -->
        <div class="xl:col-span-4 space-y-6">

            <!-- 1. Agenda & Jadwal Rapat Tim Company -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Agenda & Rapat Tim</h3>
                    <span class="text-emerald-600 font-bold text-[10px]">Mendatang</span>
                </div>

                <div class="space-y-2.5">
                    @forelse($upcomingAgendas as $uAg)
                        <div class="p-3.5 bg-slate-50 border border-slate-100 rounded-2xl space-y-1 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-black uppercase text-indigo-700">{{ $uAg->category }}</span>
                                <span class="text-[10px] font-bold text-slate-400">{{ $uAg->start_date->format('d M') }}</span>
                            </div>
                            <h4 class="font-black text-slate-900">{{ $uAg->title }}</h4>
                            @if($uAg->location_type === 'online' && $uAg->meeting_url)
                                <a href="{{ $uAg->meeting_url }}" target="_blank" class="text-[11px] text-indigo-600 font-bold underline block mt-1">
                                    Buka Link Meeting &rarr;
                                </a>
                            @elseif($uAg->location_address)
                                <span class="text-[11px] text-slate-500 block mt-1">📍 {{ $uAg->location_address }}</span>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">Belum ada agenda rapat mendatang.</p>
                    @endforelse
                </div>
            </div>

            <!-- 2. Tugas Terbuka (Open Pool) Bisa Diklaim Langsung -->
            @if($openCompanyTasks->count() > 0)
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tugas Terbuka Company</h3>
                        <span class="text-emerald-600 font-bold text-[10px]">Bisa Diambil</span>
                    </div>

                    <div class="space-y-2.5">
                        @foreach($openCompanyTasks as $opTask)
                            <div class="p-3.5 bg-emerald-50/50 border border-emerald-100 rounded-2xl space-y-2 text-xs">
                                <div>
                                    <span class="text-[9px] font-black uppercase text-emerald-700 block">{{ $opTask->project->name ?? 'Proyek' }}</span>
                                    <h4 class="font-black text-slate-900">{{ $opTask->title }}</h4>
                                </div>
                                <form method="POST" action="{{ route('user.projects.tasks.claim', [$opTask->project_id, $opTask->id]) }}">
                                    @csrf
                                    <button type="submit" class="w-full py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] rounded-xl shadow-sm transition-all">
                                        Ambil & Kerjakan Tugas
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- 3. Dokumen Repositori & SOP Cepat -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Dokumen & Panduan SOP</h3>
                    <span class="text-indigo-600 font-bold text-[10px]">Repositori</span>
                </div>

                <div class="space-y-2">
                    @forelse($companyDocuments as $cDoc)
                        <a href="{{ route('user.projects.show', [$cDoc->project_id, 'tab' => 'documents']) }}" class="p-3 bg-slate-50 hover:bg-indigo-50 border border-slate-100 rounded-2xl flex items-center justify-between text-xs transition-all">
                            <div class="truncate mr-2">
                                <span class="font-bold text-slate-900 block truncate">{{ $cDoc->title }}</span>
                                <span class="text-[10px] text-slate-400">{{ $cDoc->category }}</span>
                            </div>
                            <span class="text-[10px] text-indigo-600 font-bold uppercase shrink-0">Buka &rarr;</span>
                        </a>
                    @empty
                        <p class="text-xs text-slate-400 italic">Belum ada dokumen yang diunggah.</p>
                    @endforelse
                </div>
            </div>

            <!-- 4. Transparansi Belanja Company (Jika ada proyek transparan) -->
            @if($transparentProjects->count() > 0)
                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-100 text-slate-800 rounded-3xl p-6 shadow-sm space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase text-indigo-700 tracking-wider">TRANSPARANSI KEUANGAN</span>
                        <span class="text-[10px] text-slate-500">{{ $transparentProjects->count() }} Proyek Aktif</span>
                    </div>
                    <div class="text-2xl font-black text-slate-900">
                        Rp {{ number_format($totalCompanyExpenses, 0, ',', '.') }}
                    </div>
                    <p class="text-[11px] text-slate-600">Total belanja operasional dan infrastruktur terbuka di company ini.</p>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection