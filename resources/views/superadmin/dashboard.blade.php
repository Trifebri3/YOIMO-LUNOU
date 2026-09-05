@extends('superadmin.layouts.app')

@section('title', 'Dashboard Superadmin')

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-12 gap-8">

    <!-- Kolom Tengah / Kiri (Lebar: 8 Kolom) -->
    <div class="xl:col-span-8 space-y-6">

        <!-- Welcome Banner Card -->
        <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                Selamat sore, {{ Auth::user()->name }}.
            </h1>
            <p class="mt-2 text-xs font-semibold text-slate-400">
                Workspace: <span class="text-emerald-700 font-bold">Jaringan YOIN Grup</span> • Ekosistem: <span class="text-amber-500 font-bold">LUNOU CORE</span>
            </p>
        </div>

        <!-- 4 Metric Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-100 rounded-2xl p-5 text-center shadow-sm">
                <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">TOTAL PROYEK</span>
                <div class="text-2xl font-black text-slate-800 mt-2">4</div>
            </div>
            <div class="bg-white border border-slate-100 rounded-2xl p-5 text-center shadow-sm">
                <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">TOTAL TUGAS</span>
                <div class="text-2xl font-black text-slate-800 mt-2">0</div>
            </div>
            <div class="bg-white border border-slate-100 rounded-2xl p-5 text-center shadow-sm">
                <span class="text-[10px] font-bold text-emerald-600 tracking-wider uppercase">SEDANG BERJALAN</span>
                <div class="text-2xl font-black text-emerald-600 mt-2">0</div>
            </div>
            <div class="bg-white border border-slate-100 rounded-2xl p-5 text-center shadow-sm">
                <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">SELESAI</span>
                <div class="text-2xl font-black text-slate-800 mt-2">0</div>
            </div>
        </div>

        <!-- Fokus Hari Ini Card -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Fokus Hari Ini</h2>
            <div class="border border-dashed border-slate-200 rounded-2xl py-12 px-4 text-center">
                <p class="text-sm font-medium text-slate-400">Tidak ada tugas pending hari ini. Alur kerja Anda luar biasa!</p>
            </div>
        </div>

        <!-- Kalender Kerja Terpadu Card -->
        <div class="bg-white border border-slate-100 rounded-3xl p-7 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-sm font-bold text-slate-800">Kalender Kerja Terpadu (Rekapan Semua)</h2>
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

            <!-- Calendar Days Header -->
            <div class="grid grid-cols-7 gap-2 text-center text-[10px] font-bold text-slate-400 uppercase mb-3">
                <div>SEN</div><div>SEL</div><div>RAB</div><div>KAM</div><div>JUM</div><div>SAB</div><div>MIN</div>
            </div>

            <!-- Calendar Dates Grid -->
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
                <div class="py-2.5 bg-emerald-50 border border-emerald-400 text-emerald-700 font-bold rounded-xl cursor-pointer">25</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">26</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">27</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">28</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">29</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">30</div>
                <div class="py-2.5 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer">31</div>
            </div>
        </div>

    </div>

    <!-- Kolom Kanan / Sidebar Panel (Lebar: 4 Kolom) -->
    <div class="xl:col-span-4 space-y-6">

        <!-- Informasi Keuangan Super Card -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                    $
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Informasi Keuangan Pekerja</h2>
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">LUNOU ROSTER RINCIAN</span>
                </div>
            </div>

            <div class="space-y-3.5 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">Nomor Pekerja</span>
                    <span class="font-bold text-slate-800">YOTA-CEO-001</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Gaji Pokok</span>
                    <span class="font-bold text-slate-800">Rp 25.000.000</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Tunjangan / Hak</span>
                    <span class="font-bold text-slate-800">Rp 5.000.000</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Akumulasi Bagi Hasil (25.00%)</span>
                    <span class="font-bold text-slate-800">Rp 12.543.832</span>
                </div>
            </div>

            <!-- Sub-rincian Box -->
            <div class="mt-5 bg-slate-50 border border-slate-100 rounded-2xl p-4 text-xs space-y-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">RINCIAN PER PROYEK:</span>
                <div class="flex justify-between text-slate-600">
                    <span>Institut Hijau Indonesia 1</span>
                    <span class="font-semibold text-slate-800">Rp 11.074.620</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>DS Language Center</span>
                    <span class="font-semibold text-slate-800">Rp 1.469.212</span>
                </div>
            </div>

            <!-- Total Badge -->
            <div class="mt-6 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-800">Total Gaji + Hak</span>
                <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1 rounded-full text-xs font-black">
                    Rp 42.543.832
                </span>
            </div>

            <div class="mt-4 p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-start gap-2.5 text-[11px] text-slate-500">
                <svg class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Penggajian diakumulasi otomatis dari seluruh profit proyek aktif berdasarkan persenan porsi Anda.</span>
            </div>
        </div>

        <!-- Keamanan Biometrik / Face ID Card -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
            <h2 class="text-sm font-bold text-slate-900 mb-2">Keamanan Face ID YOIMO</h2>
            <p class="text-xs text-slate-500 leading-relaxed mb-6">
                Daftarkan sidik wajah perangkat Anda untuk masuk tanpa password secara cepat. YOIMO menyimpan kunci kredensial kriptografi aman di perangkat lokal Anda.
            </p>

            <div class="space-y-3">
                <button type="button" class="w-full flex items-center justify-center gap-2 py-3 px-4 bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold text-xs rounded-xl hover:bg-emerald-100/60 transition-all">
                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Biometrik Passkey Aktif
                </button>
                <button type="button" class="w-full py-2.5 px-4 bg-slate-50 border border-slate-200 text-slate-600 font-semibold text-xs rounded-xl hover:bg-slate-100 transition-all">
                    Hapus Face ID dari Perangkat
                </button>
            </div>
        </div>

    </div>

</div>
@endsection
