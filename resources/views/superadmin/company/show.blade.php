@extends('superadmin.layouts.app')

@section('title', 'Detail Profil Perusahaan - ' . ($company->company_name ?? 'Preview'))

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- Flash Notification -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Top Action Bar -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('superadmin.company.index') }}" class="p-2 border border-slate-200 text-slate-500 hover:bg-slate-50 rounded-xl transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">Detail Profil Perusahaan</h1>
                <p class="text-xs text-slate-400">Pratinjau lengkap profil, legalitas, saluran resmi, dan blok dinamis.</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('superadmin.company.edit', $company->id) }}" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit Profil & Konten
            </a>
        </div>
    </div>

    <!-- Banner & Identitas Header -->
    <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
        <div class="h-60 w-full bg-slate-900 relative">
            @if($company->banner)
                <img src="{{ asset('storage/' . $company->banner) }}" alt="Banner" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-gradient-to-r from-slate-900 via-teal-950 to-slate-900 flex items-center justify-center text-slate-400 text-xs font-semibold">
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
                        <div class="w-full h-full rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-black text-2xl">
                            LOGO
                        </div>
                    @endif
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">{{ $company->company_name }}</h2>
                    <p class="text-xs font-semibold text-emerald-600 mt-0.5">{{ $company->tagline ?? 'Tagline belum diset' }}</p>
                </div>
            </div>

            <!-- Kontak Bar -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-100 text-xs">
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <span class="text-slate-400 font-medium block mb-1">Email Resmi</span>
                    <span class="font-bold text-slate-800">{{ $company->email ?? '-' }}</span>
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <span class="text-slate-400 font-medium block mb-1">Kontak / Hotline</span>
                    <span class="font-bold text-slate-800">{{ $company->phone ?? '-' }}</span>
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <span class="text-slate-400 font-medium block mb-1">Alamat Kantor</span>
                    <span class="font-bold text-slate-800">{{ $company->address ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tentang Perusahaan -->
    @if($company->about)
        <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-3">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tentang Perusahaan</h3>
            <p class="text-xs text-slate-700 leading-relaxed font-medium whitespace-pre-line">{{ $company->about }}</p>
        </div>
    @endif

    <!-- Visi & Misi Card -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-3">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Visi</h3>
            </div>
            <div class="p-4 bg-slate-50 rounded-2xl text-xs text-slate-700 leading-relaxed font-medium">
                {{ $company->vision ?? 'Visi belum ditambahkan.' }}
            </div>
        </div>

        <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-3">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Misi</h3>
            </div>
            <div class="p-4 bg-slate-50 rounded-2xl text-xs text-slate-700 leading-relaxed font-medium whitespace-pre-line">
                {{ $company->mission ?? 'Misi belum ditambahkan.' }}
            </div>
        </div>
    </div>

    <!-- Saluran Sosial Media -->
    @if(!empty($company->social_media))
        <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Saluran Resmi & Sosial Media</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                @foreach($company->social_media as $socmed)
                    <a href="{{ $socmed['url'] ?? '#' }}" target="_blank" class="p-3.5 bg-slate-50 hover:bg-emerald-50 border border-slate-200/70 hover:border-emerald-200 rounded-2xl text-xs font-bold text-slate-700 hover:text-emerald-700 transition-all flex items-center justify-between">
                        <div class="flex items-center gap-2 truncate">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                            <span class="truncate">{{ $socmed['platform'] ?? 'Platform' }}</span>
                        </div>
                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Dynamic Modular Content (JSON Renderer) -->
    @if(!empty($company->dynamic_sections))
        <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Blok Konten Dinamis (JSON Builder)</h3>
                <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-xl">
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

                        <!-- 1. Text Type -->
                        @if(($sec['type'] ?? '') === 'text')
                            <p class="text-xs text-slate-600 leading-relaxed font-medium whitespace-pre-line">{{ $sec['content'] ?? '' }}</p>

                        <!-- 2. Image Type -->
                        @elseif(($sec['type'] ?? '') === 'image')
                            @if(!empty($sec['file_url']))
                                <div class="rounded-xl overflow-hidden border border-slate-200">
                                    <img src="{{ asset('storage/' . $sec['file_url']) }}" alt="{{ $sec['title'] ?? 'Gambar' }}" class="w-full h-48 object-cover">
                                </div>
                            @endif
                            @if(!empty($sec['content']))
                                <p class="text-xs text-slate-500 italic">{{ $sec['content'] }}</p>
                            @endif

                        <!-- 3. Video Embed Type -->
                        @elseif(($sec['type'] ?? '') === 'video')
                            @php
                                $videoUrl = $sec['content'] ?? '';
                                $embedUrl = null;
                                if (str_contains($videoUrl, 'youtube.com/watch?v=')) {
                                    $embedUrl = str_replace('watch?v=', 'embed/', $videoUrl);
                                } elseif (str_contains($videoUrl, 'youtu.be/')) {
                                    $embedUrl = str_replace('youtu.be/', 'www.youtube.com/embed/', $videoUrl);
                                }
                            @endphp

                            @if($embedUrl)
                                <div class="aspect-video w-full rounded-xl overflow-hidden border border-slate-200">
                                    <iframe class="w-full h-full" src="{{ $embedUrl }}" frameborder="0" allowfullscreen></iframe>
                                </div>
                            @else
                                <div class="p-4 bg-white border border-slate-200 rounded-xl text-xs space-y-1">
                                    <span class="text-[10px] text-slate-400 font-bold block">LINK VIDEO:</span>
                                    <a href="{{ $videoUrl }}" target="_blank" class="text-emerald-600 underline font-semibold break-all">{{ $videoUrl }}</a>
                                </div>
                            @endif

                        <!-- 4. Link Type -->
                        @elseif(($sec['type'] ?? '') === 'link')
                            <div class="pt-2">
                                <a href="{{ $sec['content'] ?? '#' }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-xs font-bold hover:bg-emerald-700 transition-all shadow-sm">
                                    <span>Kunjungi Tautan Terlampir</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
