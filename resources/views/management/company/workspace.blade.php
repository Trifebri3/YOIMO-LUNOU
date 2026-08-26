@extends('management.layouts.app')

@section('title', 'Workspace - ' . $company->company_name)

@section('content')
<div class="space-y-8 font-sans">

    <!-- 1. Top Hero Workspace Banner Card (Light Rounded) -->
    <div class="bg-gradient-to-r from-indigo-50 via-purple-50/50 to-indigo-50 border border-indigo-100/70 rounded-[2.5rem] p-8 sm:p-10 text-slate-800 relative shadow-sm overflow-hidden">
        <!-- Subtle Glow Effect -->
        <div class="absolute -right-10 -top-10 w-80 h-80 bg-indigo-500/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2 text-[10px] font-black tracking-wider uppercase">
                    <span class="bg-indigo-100 text-indigo-700 border border-indigo-200 px-3 py-1 rounded-full">
                        OFFICIAL MANAGEMENT PORTAL
                    </span>
                    <span class="bg-rose-100 text-rose-700 border border-rose-200 px-3 py-1 rounded-full">
                        CONTROL PANEL
                    </span>
                </div>

                <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-slate-900 uppercase">
                    {{ $company->company_name }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 max-w-2xl font-medium leading-relaxed">
                    {{ $company->tagline ?? 'Workspace utama kendali ekosistem dan manajemen portofolio digital.' }}
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('management.projects.create', ['company_id' => $company->id]) }}" 
                   class="px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-2xl shadow-md transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Buat Proyek
                </a>
                <a href="{{ route('management.company.edit', $company->id) }}" 
                   class="px-5 py-3 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold rounded-2xl shadow-xs transition-all">
                    Kelola Profil
                </a>
            </div>
        </div>
    </div>

    <!-- 2. Ringkasan Metrik Mini (3 Cards Horizontal) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <!-- Proyek Aktif -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">PROYEK AKTIF</span>
                <div class="text-2xl font-black text-slate-900 mt-0.5">{{ $projects->count() }}</div>
            </div>
        </div>

        <!-- Anggota Workspace -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">ANGGOTA WORKSPACE</span>
                <div class="text-2xl font-black text-slate-900 mt-0.5">{{ $teamMembers->count() }}</div>
            </div>
        </div>

        <!-- Undangan Tertunda -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">UNDANGAN TERTUNDA</span>
                <div class="text-2xl font-black text-slate-900 mt-0.5">0</div>
            </div>
        </div>
    </div>

    <!-- 3. Portofolio Proyek & Sidebar Kanan -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
        
        <!-- Bagian Kiri: Grid Portofolio Proyek (8 Kolom) -->
        <div class="xl:col-span-8 space-y-6">
            <div>
                <h2 class="text-base font-black text-slate-900 tracking-tight">Portofolio Proyek</h2>
                <p class="text-xs text-slate-400">{{ $company->company_name }}</p>
            </div>

            <!-- Proyek Card Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($projects as $proj)
                    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all flex flex-col justify-between space-y-6">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-[10px] font-black uppercase tracking-wider">
                                    {{ $proj->status }}
                                </span>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">
                                        PORTOFOLIO 🌐
                                    </span>
                                    <span class="text-[10px] font-semibold text-slate-400">
                                        {{ $proj->deadline ? $proj->deadline->format('d M Y') : '-' }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <a href="{{ route('management.projects.show', $proj->id) }}" class="text-base font-black text-slate-900 hover:text-emerald-600 transition-colors">
                                    {{ $proj->name }}
                                </a>
                                <p class="text-xs text-slate-400 mt-1 line-clamp-2">
                                    {{ $proj->problem_statement ?? $proj->category }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-4 pt-4 border-t border-slate-100">
                            <!-- Progress Bar -->
                            <div>
                                <div class="flex justify-between text-[11px] font-bold mb-1.5">
                                    <span class="text-slate-400">Kemajuan</span>
                                    <span class="text-slate-800">{{ $proj->progress_percentage }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2">
                                    <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $proj->progress_percentage }}%"></div>
                                </div>
                            </div>

                            <!-- Footer Card: Anggota & Nilai Budget -->
                            <div class="flex items-center justify-between text-xs font-bold pt-1">
                                <span class="text-slate-400 flex items-center gap-1">
                                    👥 {{ count($proj->team_matrix ?? []) }} ANGGOTA
                                </span>
                                <span class="text-slate-900 font-black">
                                    Rp {{ number_format($proj->budget, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="md:col-span-2 border border-dashed border-slate-200 rounded-3xl p-12 text-center bg-white">
                        <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800">Belum Ada Proyek di Workspace Ini</h3>
                        <p class="text-xs text-slate-400 mt-1 mb-4">Mulai rancang proyek baru secara mandiri atau dengan bantuan AI generator.</p>
                        <a href="{{ route('management.projects.create', ['company_id' => $company->id]) }}" class="px-5 py-2.5 bg-emerald-600 text-white text-xs font-bold rounded-xl shadow-md">
                            Buat Proyek Pertama
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Bagian Kanan: Roster Anggota, Monitoring Wellbeing, & Aliran Aktivitas (4 Kolom) -->
        <div class="xl:col-span-4 space-y-6">
            
            <!-- Kesehatan & Keseimbangan Tim (AI Wellbeing Monitoring) -->
            <div class="bg-gradient-to-br from-indigo-50/40 to-slate-50 border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-200/50 pb-2">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 tracking-tight">Keseimbangan Tim (AI)</h3>
                        <span class="text-[9px] font-bold text-slate-400 uppercase block tracking-wider mt-0.5">Analisis Agregat & Anonim</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider
                        @if($burnoutRisk === 'Tinggi') bg-rose-50 text-rose-700 border border-rose-200
                        @elseif($burnoutRisk === 'Sedang') bg-amber-50 text-amber-700 border border-amber-200
                        @else bg-emerald-50 text-emerald-700 border border-emerald-200
                        @endif">
                        Risiko Burnout: {{ $burnoutRisk }}
                    </span>
                </div>

                <!-- Metrik Rata-rata -->
                <div class="grid grid-cols-2 gap-3 text-center">
                    <div class="bg-white p-3 border border-slate-150/80 rounded-2xl shadow-xs">
                        <span class="text-[9px] font-bold text-slate-400 uppercase block">Rata-rata Energi</span>
                        <span class="text-sm font-black text-indigo-700 mt-1 block">{{ number_format($avgEnergy, 1) }} / 5</span>
                    </div>
                    <div class="bg-white p-3 border border-slate-150/80 rounded-2xl shadow-xs">
                        <span class="text-[9px] font-bold text-slate-400 uppercase block">Beban Mental</span>
                        <span class="text-sm font-black text-purple-700 mt-1 block">{{ number_format($avgMental, 1) }} / 5</span>
                    </div>
                </div>

                <!-- Sebaran Mood Tim (Agregat) -->
                <div class="space-y-2 pt-1">
                    <span class="text-[10px] font-bold text-slate-400 uppercase block tracking-wider">Sebaran Emosi Tim</span>
                    @if(empty($moodPercentages))
                        <p class="text-[11px] text-slate-400 italic">Belum ada data check-in emosi terkumpul minggu ini.</p>
                    @else
                        <div class="space-y-2">
                            @foreach($moodPercentages as $mood => $pct)
                                <div class="space-y-1">
                                    <div class="flex justify-between text-[10px] font-black text-slate-600">
                                        <span>{{ $mood }}</span>
                                        <span>{{ $pct }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-1.5">
                                        <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Button to detail report page -->
                <div class="pt-3 border-t border-slate-200/50">
                    <a href="{{ route('management.company.wellbeing', $company->id) }}" 
                       class="w-full text-center block py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all">
                        Lihat Laporan Detail Kesehatan Tim
                    </a>
                </div>
            </div>
            
            <!-- Roster Anggota Card -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                <h3 class="text-sm font-black text-slate-900">Roster Anggota</h3>
                
                <div class="space-y-3">
                    <div class="p-3.5 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-teal-100 text-teal-800 font-black text-xs flex items-center justify-center">
                                {{ strtoupper(substr($company->manager->name ?? 'OF', 0, 2)) }}
                            </div>
                            <div>
                                <div class="text-xs font-black text-slate-900">{{ $company->manager->name ?? 'Official Team' }}</div>
                                <span class="text-[10px] text-slate-400">owner / pic</span>
                            </div>
                        </div>
                        <span class="text-[10px] text-slate-400 truncate max-w-[120px]">{{ $company->manager->email ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Aliran Aktivitas Terbaru -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                <h3 class="text-sm font-black text-slate-900">Aliran Aktivitas Terbaru</h3>

                <div class="space-y-3 text-xs">
                    <div class="flex items-start gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 mt-1.5 shrink-0"></span>
                        <div>
                            <p class="text-slate-700 font-semibold"><span class="font-bold text-slate-900">{{ Auth::user()->name }}</span> mengakses dashboard workspace <span class="text-emerald-700 font-bold">{{ $company->company_name }}</span>.</p>
                            <span class="text-[10px] text-slate-400">Hari ini</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-teal-500 mt-1.5 shrink-0"></span>
                        <div>
                            <p class="text-slate-700 font-semibold">Struktur proyek terpadu dan sistem kecerdasan buatan aktif.</p>
                            <span class="text-[10px] text-slate-400">2026</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection