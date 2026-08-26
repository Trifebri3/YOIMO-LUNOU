@extends('management.layouts.app')

@section('title', 'Detail Perusahaan - ' . $company->company_name)

@section('content')
<div class="max-w-6xl mx-auto space-y-6 font-sans">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Action Bar -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('management.company.index') }}" class="p-2 border border-slate-200 text-slate-500 hover:bg-slate-50 rounded-xl transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">Detail Profil Perusahaan</h1>
                <p class="text-xs text-slate-400">Pratinjau data identitas dan modul JSON yang sedang aktif.</p>
            </div>
        </div>
        <a href="{{ route('management.company.edit', $company->id) }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            Edit Profil Ini
        </a>
    </div>

    <!-- Header Banner & Logo -->
    <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
        <div class="h-60 w-full bg-slate-50 relative">
            @if($company->banner)
                <img src="{{ asset('storage/' . $company->banner) }}" alt="Banner" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-gradient-to-r from-slate-100 to-indigo-50 flex items-center justify-center text-slate-500 text-xs font-semibold">
                    Banner Belum Diunggah
                </div>
            @endif
        </div>

        <div class="p-8 relative">
            <div class="flex flex-col sm:flex-row items-start sm:items-end gap-6 -mt-24 mb-6">
                <div class="w-28 h-28 rounded-3xl bg-white p-2 shadow-xl border border-slate-100 shrink-0">
                    @if($company->logo)
                        <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" class="w-full h-full object-contain">
                    @else
                        <div class="w-full h-full rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-black text-2xl">
                            LOGO
                        </div>
                    @endif
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">{{ $company->company_name }}</h2>
                    <p class="text-xs font-semibold text-indigo-600 mt-0.5">{{ $company->tagline ?? 'Tagline belum diset' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-100 text-xs">
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <span class="text-slate-400 font-medium block mb-1">Email</span>
                    <span class="font-bold text-slate-800">{{ $company->email ?? '-' }}</span>
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <span class="text-slate-400 font-medium block mb-1">Kontak WhatsApp</span>
                    <span class="font-bold text-slate-800">{{ $company->phone ?? '-' }}</span>
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <span class="text-slate-400 font-medium block mb-1">Alamat Kantor</span>
                    <span class="font-bold text-slate-800">{{ $company->address ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Visi Misi -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-3">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Visi</h3>
            <div class="p-4 bg-slate-50 rounded-2xl text-xs text-slate-700 leading-relaxed font-medium">
                {{ $company->vision ?? 'Belum ada visi.' }}
            </div>
        </div>
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-3">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Misi</h3>
            <div class="p-4 bg-slate-50 rounded-2xl text-xs text-slate-700 leading-relaxed font-medium whitespace-pre-line">
                {{ $company->mission ?? 'Belum ada misi.' }}
            </div>
        </div>
    </div>

    <!-- Dynamic Modular Content (JSON Viewer) -->
    @if(!empty($company->dynamic_sections))
        <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Blok Konten Dinamis (JSON Builder)</h3>
                <span class="text-[11px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 px-3 py-1 rounded-xl">
                    {{ count($company->dynamic_sections) }} Blok Aktif
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($company->dynamic_sections as $sec)
                    <div class="p-6 border border-slate-100 rounded-2xl bg-slate-50/60 space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-black text-slate-800">{{ $sec['title'] ?? 'Tanpa Judul' }}</h4>
                            <span class="text-[9px] font-black uppercase tracking-widest px-2.5 py-0.5 rounded-full bg-white border border-slate-200 text-slate-600">
                                {{ $sec['type'] ?? 'text' }}
                            </span>
                        </div>

                        @if(($sec['type'] ?? '') === 'text')
                            <p class="text-xs text-slate-600 leading-relaxed font-medium whitespace-pre-line">{{ $sec['content'] ?? '' }}</p>
                        @elseif(($sec['type'] ?? '') === 'image' && !empty($sec['file_url']))
                            <div class="rounded-xl overflow-hidden border border-slate-200">
                                <img src="{{ asset('storage/' . $sec['file_url']) }}" alt="{{ $sec['title'] ?? 'Gambar' }}" class="w-full h-48 object-cover">
                            </div>
                            @if(!empty($sec['content']))
                                <p class="text-xs text-slate-500 italic">{{ $sec['content'] }}</p>
                            @endif
                        @elseif(($sec['type'] ?? '') === 'video')
                            <div class="p-4 bg-white border border-slate-200 rounded-xl text-xs space-y-1">
                                <span class="text-[10px] text-slate-400 font-bold block">LINK VIDEO:</span>
                                <a href="{{ $sec['content'] ?? '#' }}" target="_blank" class="text-indigo-600 underline font-semibold break-all">{{ $sec['content'] ?? '-' }}</a>
                            </div>
                        @elseif(($sec['type'] ?? '') === 'link')
                            <a href="{{ $sec['content'] ?? '#' }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700 transition-all shadow-sm">
                                <span>Buka Tautan</span>
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Public Portfolio Live Link Bar -->
    @if($company->slug && $company->is_published)
        <div class="bg-indigo-50/80 border border-indigo-200 rounded-3xl p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
                <div>
                    <span class="text-xs font-black text-indigo-950 block">Portofolio Publik Aktif (Live)</span>
                    <span class="text-[11px] text-indigo-700 font-medium break-all" id="publicUrlText">{{ route('public.company.show', $company->slug) }}</span>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" onclick="copyPublicLink()" class="px-3.5 py-1.5 bg-white border border-indigo-200 hover:bg-indigo-50 text-indigo-700 font-bold text-xs rounded-xl transition-all shadow-sm">
                    Salin Link
                </button>
                <a href="{{ route('public.company.show', $company->slug) }}" target="_blank" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition-all shadow-sm flex items-center gap-1.5">
                    <span>Buka Web Publik</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </div>
        </div>

        @push('scripts')
        <script>
            function copyPublicLink() {
                const url = document.getElementById('publicUrlText').innerText;
                navigator.clipboard.writeText(url);
                alert('Tautan portofolio publik berhasil disalin ke clipboard!');
            }
        </script>
        @endpush
    @endif



</div>
@endsection