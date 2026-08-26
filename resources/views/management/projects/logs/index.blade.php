@extends('management.layouts.app')

@section('title', 'Audit Log & Aktivitas - ' . $project->name)

@section('content')
<div class="max-w-7xl mx-auto space-y-8 font-sans">

    <!-- Header Audit Hub -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase px-2.5 py-0.5 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200">
                        AUDIT TRAIL & SYSTEM LOG
                    </span>
                    <span class="text-xs font-semibold text-slate-400">• {{ $project->name }}</span>
                </div>
                <h1 class="text-2xl font-black text-slate-900 mt-1 tracking-tight">Riwayat Aktivitas & Log Audit Proyek</h1>
                <p class="text-xs text-slate-400 mt-0.5">Rekaman transparan seluruh perubahan tugas, keuangan, dokumen, linimasa, dan agenda tim.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('management.projects.tasks.index', $project->id) }}" class="px-4 py-2 bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-100 transition-all">
                    Papan Tugas
                </a>
                <a href="{{ route('management.projects.expenses.index', $project->id) }}" class="px-4 py-2 bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-100 transition-all">
                    Belanja
                </a>
                <a href="{{ route('management.projects.show', $project->id) }}" class="px-4 py-2 bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-100 transition-all">
                    Detail Project
                </a>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <form method="GET" action="{{ route('management.projects.logs.index', $project->id) }}" class="pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Cari Aktivitas / User / IP</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Filter Modul</label>
                <select name="module" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                    <option value="">Semua Modul</option>
                    @foreach(['Project', 'Task', 'Roadmap', 'Expense', 'Agenda', 'Document'] as $mod)
                        <option value="{{ $mod }}" {{ request('module') === $mod ? 'selected' : '' }}>{{ $mod }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Filter Aksi</label>
                <select name="action" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                    <option value="">Semua Aksi</option>
                    @foreach(['CREATE', 'UPDATE', 'DELETE', 'SUBMIT', 'CLAIM', 'READ', 'TOGGLE'] as $act)
                        <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ $act }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
                    Terapkan Filter
                </button>
                @if(request()->hasAny(['search', 'module', 'action']))
                    <a href="{{ route('management.projects.logs.index', $project->id) }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition-all">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card Activity Log -->
    <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/60 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-6">Waktu Transaksi</th>
                        <th class="py-4 px-6">Pelaku (Actor)</th>
                        <th class="py-4 px-6">Modul & Aksi</th>
                        <th class="py-4 px-6">Deskripsi Aktivitas</th>
                        <th class="py-4 px-6 text-right">Audit IP & Device</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <!-- Waktu -->
                            <td class="py-4 px-6 whitespace-nowrap">
                                <div class="font-bold text-slate-900">{{ $log->created_at->format('d M Y') }}</div>
                                <div class="text-[10px] text-slate-400 font-medium">{{ $log->created_at->format('H:i:s') }} ({{ $log->created_at->diffForHumans() }})</div>
                            </td>

                            <!-- Actor -->
                            <td class="py-4 px-6 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-slate-900 text-white font-black text-[10px] flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($log->user_name ?? 'SY', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $log->user_name ?? 'System Bot' }}</div>
                                        <span class="text-[9px] font-black uppercase tracking-wider px-1.5 py-0.2 rounded {{ $log->user_role === 'management' ? 'bg-indigo-50 text-indigo-700' : ($log->user_role === 'finance' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600') }}">
                                            {{ $log->user_role ?? 'System' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Modul & Action Badge -->
                            <td class="py-4 px-6 whitespace-nowrap">
                                @php
                                    $actBadge = match($log->action) {
                                        'CREATE' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'UPDATE' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'DELETE' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'SUBMIT' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'CLAIM'  => 'bg-teal-50 text-teal-700 border-teal-200',
                                        'READ'   => 'bg-purple-50 text-purple-700 border-purple-200',
                                        default  => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <div class="font-bold text-slate-800 text-[11px]">{{ $log->module }}</div>
                                <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider border {{ $actBadge }} inline-block mt-0.5">
                                    {{ $log->action }}
                                </span>
                            </td>

                            <!-- Deskripsi -->
                            <td class="py-4 px-6">
                                <p class="text-slate-700 font-medium leading-relaxed">{{ $log->description }}</p>
                            </td>

                            <!-- IP & Device -->
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <span class="font-mono text-[10px] font-bold text-slate-700 block">{{ $log->ip_address ?? '127.0.0.1' }}</span>
                                <span class="text-[9px] text-slate-400 max-w-[150px] truncate inline-block" title="{{ $log->user_agent }}">
                                    {{ Str::limit($log->user_agent, 20) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">Belum ada riwayat aktivitas yang tercatat pada filter ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
    </div>

</div>
@endsection