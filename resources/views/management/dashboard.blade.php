@extends('management.layouts.app')

@section('title', 'Dashboard Management')

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-12 gap-8">

    <!-- Kolom Kiri / Utama (8 Kolom) -->
    <div class="xl:col-span-8 space-y-6">

        <!-- Welcome Banner Card -->
        <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                Selamat datang, {{ Auth::user()->name }}.
            </h1>
            <p class="mt-2 text-xs font-semibold text-slate-400">
                Portal: <span class="text-indigo-700 font-bold">Executive & Operational Hub</span> • Posisi: <span class="text-slate-700 font-bold">{{ Auth::user()->position ?? 'Operational Lead' }}</span>
            </p>
        </div>

        <!-- 4 Metric Cards -->
        @php
            $myCompaniesCount = \App\Models\CompanyProfile::where('manager_id', Auth::id())->count();
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-100 rounded-2xl p-5 text-center shadow-sm">
                <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">UNIT DIKELOLA</span>
                <div class="text-2xl font-black text-indigo-700 mt-2">{{ $myCompaniesCount }}</div>
            </div>
            <div class="bg-white border border-slate-100 rounded-2xl p-5 text-center shadow-sm">
                <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">AGENDA AKTIF</span>
                <div class="text-2xl font-black text-slate-800 mt-2">0</div>
            </div>
            <div class="bg-white border border-slate-100 rounded-2xl p-5 text-center shadow-sm">
                <span class="text-[10px] font-bold text-emerald-600 tracking-wider uppercase">STATUS PROFIL</span>
                <div class="text-2xl font-black text-emerald-600 mt-2">OPTIMAL</div>
            </div>
            <div class="bg-white border border-slate-100 rounded-2xl p-5 text-center shadow-sm">
                <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">LOG AUDIT</span>
                <div class="text-2xl font-black text-slate-800 mt-2">CLEAR</div>
            </div>
        </div>

        <!-- Daftar Unit Usaha Yang Ditugaskan -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-slate-800">Unit Usaha yang Dikelola</h2>
                <a href="{{ route('management.company.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">Lihat Semua &rarr;</a>
            </div>

            @php
                $recentCompanies = \App\Models\CompanyProfile::where('manager_id', Auth::id())->latest()->take(3)->get();
            @endphp

            @if($recentCompanies->count() > 0)
                <div class="space-y-3">
                    @foreach($recentCompanies as $comp)
                        <div class="p-4 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                @if($comp->logo)
                                    <img src="{{ asset('storage/' . $comp->logo) }}" alt="{{ $comp->company_name }}" class="w-10 h-10 rounded-xl object-contain p-1 bg-white border border-slate-200">
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 font-black text-xs flex items-center justify-center">
                                        CO
                                    </div>
                                @endif
                                <div>
                                    <h3 class="text-xs font-bold text-slate-900">{{ $comp->company_name }}</h3>
                                    <span class="text-[11px] text-slate-400">{{ $comp->tagline ?? 'Tanpa Tagline' }}</span>
                                </div>
                            </div>
                            <a href="{{ route('management.company.edit', $comp->id) }}" class="px-3.5 py-1.5 bg-white border border-slate-200 hover:border-indigo-300 text-indigo-700 text-xs font-bold rounded-xl shadow-sm transition-all">
                                Kelola Konten
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="border border-dashed border-slate-200 rounded-2xl py-8 px-4 text-center">
                    <p class="text-xs font-medium text-slate-400">Belum ada unit usaha yang ditugaskan Superadmin kepada Anda.</p>
                </div>
            @endif
        </div>

        <!-- Kalender Operasional Terpadu -->
        <div class="bg-white border border-slate-100 rounded-3xl p-7 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-sm font-bold text-slate-800">Kalender Operasional & Rapat</h2>
                <div class="flex items-center gap-3">
                    <button type="button" class="p-1.5 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <span class="text-xs font-bold text-slate-700">Agustus 2026</span>
                    <button type="button" class="p-1.5 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-7 gap-2 text-center text-[10px] font-bold text-slate-400 uppercase mb-3">
                <div>SEN</div><div>SEL</div><div>RAB</div><div>KAM</div><div>JUM</div><div>SAB</div><div>MIN</div>
            </div>

            <div class="grid grid-cols-7 gap-2 text-xs font-semibold text-slate-700 text-center">
                <div class="py-2.5"></div><div class="py-2.5"></div><div class="py-2.5"></div><div class="py-2.5"></div><div class="py-2.5"></div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">1</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">2</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">3</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">4</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">5</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">6</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">7</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">8</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">9</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">10</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">11</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">12</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">13</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">14</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">15</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">16</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">17</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">18</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">19</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">20</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">21</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">22</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">23</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">24</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">25</div>
                <div class="py-2.5 bg-indigo-50 border border-indigo-400 text-indigo-700 font-bold rounded-xl cursor-pointer">26</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">27</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">28</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">29</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">30</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">31</div>
            </div>
        </div>

    </div>

    <!-- Kolom Kanan / Panel Pendukung (4 Kolom) -->
    <div class="xl:col-span-4 space-y-6">

        <!-- Kartu PIC Profile & WhatsApp Ready -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
            <div class="flex items-center gap-3">
                @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-12 h-12 rounded-2xl object-cover ring-2 ring-indigo-200">
                @else
                    <div class="w-12 h-12 rounded-2xl bg-slate-900 text-white font-black text-sm flex items-center justify-center">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                @endif
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ Auth::user()->name }}</h2>
                    <span class="text-[11px] text-indigo-600 font-semibold">{{ Auth::user()->position ?? 'Management Leader' }}</span>
                </div>
            </div>

            <div class="space-y-2.5 text-xs pt-3 border-t border-slate-100">
                <div class="flex justify-between">
                    <span class="text-slate-400">Email</span>
                    <span class="font-bold text-slate-800">{{ Auth::user()->email }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">WhatsApp PIC</span>
                    <span class="font-bold text-slate-800">{{ Auth::user()->phone ? '+' . Auth::user()->phone : '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Hak Akses</span>
                    <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded-md font-bold text-[10px] uppercase">OPERATIONAL EDIT</span>
                </div>
            </div>
        </div>

        <!-- Panduan & SOP Singkat Management -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-3">
            <h2 class="text-sm font-bold text-slate-900">SOP Pembaruan Konten</h2>
            <p class="text-xs text-slate-500 leading-relaxed">
                Pastikan data visi, misi, legalitas, saluran media sosial, serta blok konten JSON yang Anda kelola selalu terbarui dan valid.
            </p>
            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 text-xs text-slate-600 space-y-2">
                <div class="flex items-start gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 mt-1.5"></span>
                    <span>Format gambar banner disarankan rasio 16:9.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 mt-1.5"></span>
                    <span>Tautan video YouTube otomatis dibuatkan player embed.</span>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
