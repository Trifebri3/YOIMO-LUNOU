@extends('management.layouts.app')

@section('title', 'Laporan Belanja - ' . $project->name)

@section('content')
<div class="max-w-7xl mx-auto space-y-8 font-sans">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Header Proyek & Panel Kontrol Transparansi Management -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase px-2.5 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                        LAPORAN BELANJA & KEUANGAN
                    </span>
                    <span class="text-xs font-semibold text-slate-400">• {{ $project->name }}</span>
                </div>
                <h1 class="text-2xl font-black text-slate-900 mt-1 tracking-tight">Transparansi Arus Pengeluaran Tim</h1>
                <p class="text-xs text-slate-400 mt-0.5">Rekapan bukti nota dan pembelanjaan proyek yang dicatatkan oleh tim Finance.</p>
            </div>

            <!-- Toggle Switch Transparansi oleh Management -->
            <div class="flex flex-wrap items-center gap-4 bg-slate-50 border border-slate-200/80 p-3.5 rounded-2xl">
                <div>
                    <span class="text-xs font-bold text-slate-900 block">Status Transparansi Tim</span>
                    <span class="text-[10px] font-medium {{ $project->is_financial_transparent ? 'text-emerald-600' : 'text-slate-400' }}">
                        {{ $project->is_financial_transparent ? 'Aktif (Dapat dilihat oleh seluruh user)' : 'Nonaktif (Terkunci dari user)' }}
                    </span>
                </div>

                <form method="POST" action="{{ route('management.projects.expenses.toggle-transparency', $project->id) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-2 {{ $project->is_financial_transparent ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-slate-200 hover:bg-slate-300 text-slate-700' }}">
                        <span class="w-2 h-2 rounded-full {{ $project->is_financial_transparent ? 'bg-white' : 'bg-slate-400' }}"></span>
                        <span>{{ $project->is_financial_transparent ? 'Transparansi Aktif' : 'Aktifkan Transparansi' }}</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- 4 Metrics Ringkasan Finansial -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 pt-4 border-t border-slate-100">
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">ALOKASI TOTAL BUDGET</span>
                <div class="text-xl font-black text-slate-900 mt-1">Rp {{ number_format($totalBudget, 0, ',', '.') }}</div>
            </div>

            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                <span class="text-[10px] font-bold text-rose-600 uppercase tracking-wider block">REALISASI BELANJA</span>
                <div class="text-xl font-black text-rose-600 mt-1">Rp {{ number_format($totalSpent, 0, ',', '.') }}</div>
            </div>

            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">SISA SALDO ANGGARAN</span>
                <div class="text-xl font-black {{ $remainingBudget < 0 ? 'text-rose-700' : 'text-emerald-600' }} mt-1">
                    Rp {{ number_format($remainingBudget, 0, ',', '.') }}
                </div>
            </div>

            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider block">SERAPAN ANGGARAN</span>
                <div class="text-xl font-black text-indigo-700 mt-1">{{ $spentPercentage }}%</div>
            </div>
        </div>

        <!-- Progress Bar Serapan Anggaran -->
        <div class="space-y-1.5 pt-2">
            <div class="flex justify-between text-[11px] font-bold">
                <span class="text-slate-400">Tingkat Penggunaan Anggaran</span>
                <span class="{{ $spentPercentage > 100 ? 'text-rose-600' : 'text-slate-800' }}">{{ $spentPercentage }}% Terpakai</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2">
                <div class="{{ $spentPercentage > 100 ? 'bg-rose-500' : 'bg-emerald-500' }} h-2 rounded-full transition-all" style="width: {{ min($spentPercentage, 100) }}%"></div>
            </div>
        </div>
    </div>

    <!-- Peringatan Status Transparansi Nonaktif -->
    @if(!$project->is_financial_transparent)
        <div class="bg-amber-50 border border-amber-200 text-amber-900 p-4 rounded-2xl text-xs flex items-center justify-between">
            <span class="font-medium">Perhatian: Transparansi belanja saat ini dinonaktifkan oleh Management. Anggota tim reguler (User) tidak dapat melihat rincian finansial ini.</span>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">

        <!-- Kolom Kiri: Tabel Catatan Belanja & Bukti Nota (8 Kolom) -->
        <div class="xl:col-span-8 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h2 class="text-base font-black text-slate-900 tracking-tight">Rincian Pengeluaran Tim</h2>

                <!-- Filter Kategori -->
                <div class="flex flex-wrap items-center gap-1.5">
                    <a href="{{ route('management.projects.expenses.index', $project->id) }}" 
                       class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all {{ empty($selectedCategory) ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-600' }}">
                        Semua
                    </a>
                    @foreach(['Infrastruktur & Server', 'Lisensi & Software', 'Operasional & Konsumsi', 'Peralatan & Hardware'] as $cat)
                        <a href="{{ route('management.projects.expenses.index', [$project->id, 'category' => $cat]) }}" 
                           class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all {{ $selectedCategory === $cat ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-600' }}">
                            {{ $cat }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/60 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-4 px-6">Item Belanja / Keterangan</th>
                                <th class="py-4 px-6">Kategori</th>
                                <th class="py-4 px-6">Tanggal</th>
                                <th class="py-4 px-6">Nominal</th>
                                <th class="py-4 px-6">Bukti Nota</th>
                                <th class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-600">
                            @forelse($expenses as $item)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-slate-900">{{ $item->title }}</div>
                                        @if($item->notes)
                                            <div class="text-[11px] text-slate-400 mt-0.5">{{ $item->notes }}</div>
                                        @endif
                                        <div class="text-[10px] text-indigo-600 font-semibold mt-0.5">
                                            Oleh: {{ $item->uploader->name ?? 'Finance' }}
                                        </div>
                                    </td>

                                    <td class="py-4 px-6">
                                        <span class="px-2.5 py-1 bg-slate-100 border border-slate-200 rounded-lg text-[10px] font-bold text-slate-700">
                                            {{ $item->category }}
                                        </span>
                                    </td>

                                    <td class="py-4 px-6 font-medium text-slate-700">
                                        {{ $item->expense_date->format('d M Y') }}
                                    </td>

                                    <td class="py-4 px-6 font-black text-rose-600">
                                        Rp {{ number_format($item->amount, 0, ',', '.') }}
                                    </td>

                                    <td class="py-4 px-6">
                                        @if($item->receipt_file)
                                            <a href="{{ asset('storage/' . $item->receipt_file) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg text-[10px] font-bold hover:bg-emerald-100 transition-all">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                <span>Lihat Nota</span>
                                            </a>
                                        @else
                                            <span class="text-[10px] text-slate-400 italic">Tanpa Struk</span>
                                        @endif
                                    </td>

                                    <td class="py-4 px-6 text-right">
                                        <form method="POST" action="{{ route('management.projects.expenses.destroy', [$project->id, $item->id]) }}" onsubmit="return confirm('Hapus catatan belanja ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-xl transition-all" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400">Belum ada pengeluaran belanja yang dicatatkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Form Input Pembelanjaan Baru (4 Kolom) -->
        <div class="xl:col-span-4 space-y-6">
            <div class="bg-white border border-slate-100 rounded-3xl p-7 shadow-sm space-y-5 sticky top-24">
                <div>
                    <h2 class="text-sm font-black text-slate-900 tracking-tight">+ Catat Belanja Baru</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Input nominal dan unggah bukti kwitansi / struk pembayaran.</p>
                </div>

                <form method="POST" action="{{ route('management.projects.expenses.store', $project->id) }}" enctype="multipart/form-data" class="space-y-4">
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

    </div>

</div>
@endsection