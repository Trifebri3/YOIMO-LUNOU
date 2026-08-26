@extends('management.layouts.app')

@section('title', 'Kesehatan & Keseimbangan Tim - ' . $company->company_name)

@section('content')
<div class="space-y-8 font-sans">

    <!-- 1. Header Banner -->
    <div class="bg-gradient-to-br from-indigo-900 to-purple-900 text-white rounded-[2.5rem] p-8 sm:p-10 relative shadow-md overflow-hidden">
        <div class="absolute -right-20 -top-20 w-96 h-96 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 space-y-4">
            <div class="flex items-center gap-2 text-[10px] font-black tracking-wider uppercase">
                <span class="bg-white/10 text-white border border-white/20 px-3 py-1 rounded-full">
                    ANALISIS KESEHATAN EKOSISTEM
                </span>
                <span class="bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 px-3 py-1 rounded-full">
                    PRIVASI TERLINDUNGI
                </span>
            </div>

            <h1 class="text-3xl sm:text-4xl font-black tracking-tight uppercase">
                Keseimbangan Tim: {{ $company->company_name }}
            </h1>
            <p class="text-xs sm:text-sm text-purple-200 max-w-2xl font-medium leading-relaxed">
                Pemantauan kebugaran mental, ritme kerja, dan indeks kelelahan (burnout) tim Anda berdasarkan data agregat terenkripsi.
            </p>
        </div>
    </div>

    <!-- 2. Batasan Privasi Alert (Privacy Boundary Statement) -->
    <div class="bg-indigo-50 border border-indigo-200 text-indigo-900 p-5 rounded-3xl space-y-2 shadow-xs">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            <h4 class="text-xs font-black uppercase tracking-wider">Pemberitahuan Batasan Privasi (Privacy Boundary)</h4>
        </div>
        <p class="text-xs text-indigo-850 font-bold leading-relaxed">
            Data di bawah ini disajikan secara **100% anonim dan agregat**. Detail emosi harian individual, tulisan jurnal refleksi pribadi, dan isi pikiran yang dilepaskan bersifat rahasia sepenuhnya dan tidak akan pernah dibagikan kepada manager atau perusahaan.
        </p>
    </div>

    <!-- 3. Metrik Utama Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Rata-rata Energi -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col justify-between space-y-4">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TINGKAT ENERGI TIM</span>
                <div class="text-3xl font-black text-slate-900 mt-1">{{ number_format($avgEnergy, 1) }} <span class="text-xs text-slate-400">/ 5.0</span></div>
            </div>
            <div class="space-y-1">
                <div class="w-full bg-slate-100 rounded-full h-2">
                    <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ ($avgEnergy/5)*100 }}%"></div>
                </div>
                <span class="text-[10px] font-bold text-slate-400">Tingkat stamina & motivasi tim di tempat kerja</span>
            </div>
        </div>

        <!-- Beban Mental -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col justify-between space-y-4">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">BEBAN MENTAL TIM</span>
                <div class="text-3xl font-black text-slate-900 mt-1">{{ number_format($avgMental, 1) }} <span class="text-xs text-slate-400">/ 5.0</span></div>
            </div>
            <div class="space-y-1">
                <div class="w-full bg-slate-100 rounded-full h-2">
                    <div class="bg-purple-600 h-2 rounded-full" style="width: {{ ($avgMental/5)*100 }}%"></div>
                </div>
                <span class="text-[10px] font-bold text-slate-400">Beban kognitif dan kepenatan pikiran tim</span>
            </div>
        </div>

        <!-- Risiko Burnout -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col justify-between space-y-4">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">INDEKS RISIKO BURNOUT</span>
                <div class="text-2xl font-black mt-1
                    @if($burnoutRisk === 'Tinggi') text-rose-600
                    @elseif($burnoutRisk === 'Sedang') text-amber-600
                    @else text-emerald-600
                    @endif">
                    {{ $burnoutRisk }}
                </div>
            </div>
            <div class="p-3 rounded-2xl text-[11px] font-bold 
                @if($burnoutRisk === 'Tinggi') bg-rose-50 text-rose-700
                @elseif($burnoutRisk === 'Sedang') bg-amber-50 text-amber-700
                @else bg-emerald-50 text-emerald-700
                @endif">
                Dihitung dari rata-rata beban kerja, keterlambatan tugas, dan beban mental kognitif tim.
            </div>
        </div>

    </div>

    <!-- 4. Detail Analisis Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
        
        <!-- Kolom Kiri: Mood Tim & Penggunaan Fitur (8 Kolom) -->
        <div class="xl:col-span-8 space-y-6">
            
            <!-- Distribusi Mood / Emosi Tim -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
                <div>
                    <h3 class="text-base font-black text-slate-900 tracking-tight">Sebaran Emosi Tim (30 Hari Terakhir)</h3>
                    <p class="text-xs text-slate-400">Total Check-in Terkumpul: {{ $totalCheckins }} Log</p>
                </div>

                @if(empty($moodPercentages))
                    <div class="border border-dashed border-slate-200 rounded-2xl p-12 text-center text-xs text-slate-400">
                        Belum ada data check-in emosi terdaftar untuk tim Anda bulan ini.
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @foreach($moodPercentages as $mood => $pct)
                            <div class="p-4 bg-slate-50 border border-slate-100 rounded-2xl space-y-2">
                                <div class="flex justify-between text-xs font-black text-slate-800">
                                    <span>{{ $mood }}</span>
                                    <span>{{ $pct }}%</span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Aktivitas Recovery Tim (Anonymized) -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-4">
                <div>
                    <h3 class="text-base font-black text-slate-900 tracking-tight">Keterlibatan Program Pemulihan (Recovery)</h3>
                    <p class="text-xs text-slate-400">Penggunaan fasilitas wellbeing secara mandiri oleh tim Anda.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-5 bg-teal-50/50 border border-teal-100 rounded-2xl flex items-center justify-between text-xs">
                        <div>
                            <span class="text-[10px] font-bold text-teal-600 uppercase block tracking-wider">JURNAL REFLEKSI AKTIF</span>
                            <span class="text-xl font-black text-teal-800 block mt-1">{{ $journalsCount }} Jurnal</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Ditulis untuk melepaskan beban emosi</span>
                        </div>
                    </div>

                    <div class="p-5 bg-amber-50/50 border border-amber-100 rounded-2xl flex items-center justify-between text-xs">
                        <div>
                            <span class="text-[10px] font-bold text-amber-600 uppercase block tracking-wider">TUJUAN PRIBADI KOLABORATIF</span>
                            <span class="text-xl font-black text-amber-800 block mt-1">{{ $goalsCount }} Target</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Resolusi & komitmen keseimbangan hidup</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Kolom Kanan: Rekomendasi AI (4 Kolom) -->
        <div class="xl:col-span-4 space-y-6">
            
            <!-- Rekomendasi Manajerial AI -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-5">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3 justify-between">
                    <div>
                        <h3 class="text-sm font-black text-slate-900">Rekomendasi AI Manajer</h3>
                        <span class="text-[9px] font-bold text-slate-400 block uppercase">Saran Kepemimpinan Rileks</span>
                    </div>
                    <div>
                        @if($aiUsed)
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl text-[9px] font-black uppercase tracking-wider">
                                ✦ AI Active
                            </span>
                        @else
                            <span class="px-2.5 py-1 bg-slate-50 text-slate-500 border border-slate-200 rounded-xl text-[9px] font-black uppercase tracking-wider" title="Konfigurasikan API Key AI di Pengaturan AI untuk analisis dinamis">
                                Default Rules
                            </span>
                        @endif
                    </div>
                </div>

                <div class="space-y-4">
                    @foreach($recommendations as $rec)
                        <div class="p-4 bg-slate-50 border border-slate-150 rounded-2xl flex gap-3 text-xs">
                            <span class="text-indigo-600 mt-0.5 shrink-0">✦</span>
                            <p class="text-slate-700 font-medium leading-relaxed">{{ $rec }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
