@extends('management.layouts.app')

@section('title', $document->title . ' - Repositori Panduan')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 font-sans">

    <!-- Header Actions -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200">
                    {{ $document->category }}
                </span>
                @if($document->is_mandatory)
                    <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider bg-amber-500 text-white">
                        WAJIB DIBACA
                    </span>
                @endif
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1 tracking-tight">{{ $document->title }}</h1>
            <p class="text-xs text-slate-400 mt-0.5">Diterbitkan oleh {{ $document->creator->name ?? 'Management' }} • {{ $document->created_at->format('d M Y H:i') }}</p>
        </div>

        <a href="{{ route('management.projects.documents.index', $project->id) }}" class="px-4 py-2 bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-semibold rounded-xl transition-all">
            Kembali ke Perpustakaan
        </a>
    </div>

    <!-- Isi Dokumen (Render Rich Text) -->
    <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
        @if($document->doc_type === 'article')
            <div class="prose max-w-none text-xs text-slate-700 leading-relaxed font-medium">
                {!! $document->content !!}
            </div>
        @elseif($document->doc_type === 'link')
            <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200 text-center space-y-3">
                <p class="text-xs text-slate-600 font-medium">Dokumen ini berupa tautan eksternal resmi proyek.</p>
                <a href="{{ $document->external_url }}" target="_blank" class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white font-bold text-xs rounded-xl hover:bg-indigo-700 shadow-md transition-all">
                    <span>Buka Tautan: {{ $document->external_url }}</span> &rarr;
                </a>
            </div>
        @elseif($document->doc_type === 'file')
            <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200 text-center space-y-3">
                <p class="text-xs text-slate-600 font-medium">File Dokumen: <strong>{{ $document->file_name_original }}</strong> ({{ $document->file_size }})</p>
                <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 text-white font-bold text-xs rounded-xl hover:bg-emerald-700 shadow-md transition-all">
                    <span>Unduh File</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Tracking Audit Log Pembaca -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">
            Riwayat Pembaca Dokumen ({{ count($document->readers_log ?? []) }} Anggota Tim)
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @forelse($document->readers_log ?? [] as $reader)
                <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl flex items-center justify-between text-xs">
                    <span class="font-bold text-slate-800">{{ $reader['name'] ?? 'Anggota Tim' }}</span>
                    <span class="text-[10px] text-emerald-600 font-bold">{{ date('d M H:i', strtotime($reader['read_at'])) }}</span>
                </div>
            @empty
                <p class="text-xs text-slate-400 italic">Belum ada riwayat pembaca.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection