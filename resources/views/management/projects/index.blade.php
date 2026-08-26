@extends('management.layouts.app')

@section('title', 'Daftar Project')

@section('content')
<div class="space-y-6 font-sans">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Header & Action Button -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Project Management Hub</h1>
            <p class="text-xs text-slate-400 mt-1">Pantau timeline, scope, milestone, deliverables, dan utilisasi budget tim.</p>
        </div>
        <a href="{{ route('management.projects.create') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Buat Project Baru
        </a>
    </div>

    <!-- LUNOU Local AI Command Center -->
    <div class="bg-gradient-to-br from-indigo-50/60 to-purple-50/60 border border-indigo-100 rounded-3xl p-6 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-600"></span>
                </span>
                <h3 class="text-xs font-black uppercase text-indigo-900 tracking-wider">LUNOU Local AI Command Center</h3>
            </div>
            <span class="text-[10px] text-slate-400 font-bold bg-white/60 px-2 py-0.5 border border-indigo-100/50 rounded-lg">PROMPT ENGINE v1.0</span>
        </div>

        <form method="POST" action="{{ route('management.ai-generate') }}" class="space-y-3">
            @csrf
            <div class="flex flex-col sm:flex-row gap-2">
                <input type="text" name="prompt" required 
                       placeholder="Contoh: Buat project Mobile App Splicing, client PT Berkah, budget 50000000. tugas: Slicing Figma, Setup API, testing. assign: developer@company.com" 
                       class="flex-1 px-4 py-3 bg-white border border-slate-200 rounded-2xl text-xs font-medium placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-xs">
                
                <button type="submit" class="px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-2xl shadow-md transition-all shrink-0">
                    Eksekusi AI Command
                </button>
            </div>
            <p class="text-[10px] text-slate-500 font-semibold leading-relaxed">
                * Tulis instruksi dalam satu baris. Parameter yang dapat dibaca otomatis: <strong class="text-indigo-900">project</strong> (Nama), <strong class="text-indigo-900">client</strong> (Klien), <strong class="text-indigo-900">budget</strong> (Anggaran), <strong class="text-indigo-900">tasks / tugas</strong> (Pemisah Koma), dan <strong class="text-indigo-900">assign / PIC</strong> (Alamat Email).
            </p>
        </form>
    </div>

    <!-- CSV Import/Export Panel -->
    <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
            <span class="text-xs font-bold text-slate-700">Integrasi Spreadsheet (CSV)</span>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('management.projects.template') }}" class="px-3.5 py-2 bg-slate-50 border border-slate-200 text-slate-700 hover:bg-slate-100 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5" title="Download Template CSV">
                <span>Template CSV</span>
            </a>
            <a href="{{ route('management.projects.export') }}" class="px-3.5 py-2 bg-slate-50 border border-slate-200 text-slate-700 hover:bg-slate-100 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5">
                <span>Eksport Project</span>
            </a>
            <form method="POST" action="{{ route('management.projects.import') }}" enctype="multipart/form-data" class="flex items-center gap-2 border-l border-slate-100 pl-3">
                @csrf
                <input type="file" name="csv_file" accept=".csv" required class="text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-semibold file:bg-indigo-50 file:text-indigo-700">
                <button type="submit" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all">
                    Import
                </button>
            </form>
        </div>
    </div>

    <!-- Project List Filter Tabs -->
    <div class="flex items-center gap-2 pb-1">
        <a href="{{ route('management.projects.index') }}" 
           class="px-4 py-2 text-xs font-black rounded-xl transition-all {{ !$isArchived ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold' }}">
            Project Aktif ({{ $activeCount }})
        </a>
        <a href="{{ route('management.projects.index', ['filter' => 'archived']) }}" 
           class="px-4 py-2 text-xs font-black rounded-xl transition-all {{ $isArchived ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold' }}">
            Diarsipkan ({{ $archivedCount }})
        </a>
    </div>

    <!-- Table Card -->
    <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/60 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-6">Nama Project & Client</th>
                        <th class="py-4 px-6">Tahap Timeline</th>
                        <th class="py-4 px-6">Progress %</th>
                        <th class="py-4 px-6">Prioritas</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-600">
                    @forelse($projects as $p)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900">{{ $p->name }}</div>
                                <div class="text-[11px] text-indigo-600 font-medium">{{ $p->client_name ?? 'Internal Project' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $p->category }}</div>
                            </td>

                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 bg-slate-100 border border-slate-200 text-slate-700 rounded-lg text-[10px] font-bold uppercase">
                                    {{ $p->current_stage }}
                                </span>
                            </td>

                            <td class="py-4 px-6">
                                <div class="flex items-center gap-2">
                                    <div class="w-24 bg-slate-100 rounded-full h-2">
                                        <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $p->progress_percentage }}%"></div>
                                    </div>
                                    <span class="font-bold text-[11px] text-slate-800">{{ $p->progress_percentage }}%</span>
                                </div>
                            </td>

                            <td class="py-4 px-6">
                                @php
                                    $prioBadge = match($p->priority) {
                                        'Urgent' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'High'   => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'Medium' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        default  => 'bg-slate-100 text-slate-600 border-slate-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 border rounded-lg text-[10px] font-bold uppercase {{ $prioBadge }}">
                                    {{ $p->priority }}
                                </span>
                            </td>

                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg text-[10px] font-bold uppercase">
                                    {{ $p->status }}
                                </span>
                            </td>

                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('management.projects.show', $p->id) }}" class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all" title="Detail Project">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('management.projects.edit', $p->id) }}" class="p-2 text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition-all" title="Edit Project">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('management.projects.toggle-archive', $p->id) }}">
                                        @csrf
                                        <button type="submit" class="p-2 text-slate-500 {{ $p->is_archived ? 'hover:text-emerald-600 hover:bg-emerald-50' : 'hover:text-amber-600 hover:bg-amber-50' }} rounded-xl transition-all" title="{{ $p->is_archived ? 'Aktifkan Kembali Project' : 'Arsipkan Project' }}">
                                            @if($p->is_archived)
                                                <!-- Icon Restore / Arrow Up -->
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                                </svg>
                                            @else
                                                <!-- Icon Archive / Box -->
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('management.projects.destroy', $p->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus project ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all" title="Hapus Project">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Belum ada project yang dibuat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $projects->links() }}
        </div>
    </div>

</div>
@endsection