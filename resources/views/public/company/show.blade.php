<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $company->company_name }} - Official Portfolio</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-slate-800 selection:bg-indigo-500 selection:text-white">

    <!-- Top Navigation -->
    <nav class="bg-white/80 backdrop-blur-md border-b border-slate-100 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if($company->logo)
                    <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->company_name }}" class="h-9 w-9 rounded-xl object-contain border border-slate-200/80 p-1">
                @endif
                <span class="font-black text-slate-900 tracking-tight text-lg">{{ $company->company_name }}</span>
            </div>

            @if($company->phone)
                <a href="https://wa.me/{{ $company->phone }}" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                    Hubungi WhatsApp
                </a>
            @endif
        </div>
    </nav>

    <!-- Main Container -->
    <main class="max-w-6xl mx-auto px-6 py-10 space-y-10">

        <!-- Hero Card & Banner -->
        <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
            <div class="h-64 sm:h-80 w-full bg-slate-50 relative">
                @if($company->banner)
                    <img src="{{ asset('storage/' . $company->banner) }}" alt="{{ $company->company_name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gradient-to-r from-slate-100 to-indigo-50 flex items-center justify-center text-slate-500 font-semibold text-sm">
                        Official Business Enterprise
                    </div>
                @endif
            </div>

            <div class="p-8 sm:p-10 relative">
                <div class="flex flex-col sm:flex-row items-start sm:items-end gap-6 -mt-24 sm:-mt-28 mb-8">
                    <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-3xl bg-white p-2.5 shadow-xl border border-slate-100 shrink-0">
                        @if($company->logo)
                            <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" class="w-full h-full object-contain">
                        @else
                            <div class="w-full h-full rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-black text-3xl">
                                {{ strtoupper(substr($company->company_name, 0, 2)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ $company->company_name }}</h1>
                        <p class="text-sm font-semibold text-indigo-600 mt-1">{{ $company->tagline ?? 'Official Profile & Portfolio' }}</p>
                    </div>
                </div>

                <!-- Contact & Info Strip -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-6 border-t border-slate-100 text-xs">
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <span class="text-slate-400 font-medium block mb-1">Email Resmi</span>
                        <span class="font-bold text-slate-800">{{ $company->email ?? '-' }}</span>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <span class="text-slate-400 font-medium block mb-1">Kontak Hotline</span>
                        <span class="font-bold text-slate-800">{{ $company->phone ? '+' . $company->phone : '-' }}</span>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <span class="text-slate-400 font-medium block mb-1">Headquarters</span>
                        <span class="font-bold text-slate-800">{{ $company->address ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- About Us -->
        @if($company->about)
            <div class="bg-white border border-slate-100 rounded-3xl p-8 sm:p-10 shadow-sm space-y-4">
                <h2 class="text-sm font-black text-slate-400 uppercase tracking-wider">Tentang Kami</h2>
                <p class="text-sm text-slate-700 leading-relaxed font-medium whitespace-pre-line">{{ $company->about }}</p>
            </div>
        @endif

        <!-- Visi & Misi -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-4">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
                    <h2 class="text-sm font-black text-slate-400 uppercase tracking-wider">Visi Perusahaan</h2>
                </div>
                <div class="p-5 bg-slate-50 rounded-2xl text-xs sm:text-sm text-slate-700 leading-relaxed font-medium">
                    {{ $company->vision ?? 'Mewujudkan layanan prima dan solusi terpercaya berkelanjutan.' }}
                </div>
            </div>

            <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-4">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-teal-500"></span>
                    <h2 class="text-sm font-black text-slate-400 uppercase tracking-wider">Misi Perusahaan</h2>
                </div>
                <div class="p-5 bg-slate-50 rounded-2xl text-xs sm:text-sm text-slate-700 leading-relaxed font-medium whitespace-pre-line">
                    {{ $company->mission ?? 'Memberikan inovasi dan standar mutu terbaik bagi pelanggan.' }}
                </div>
            </div>
        </div>

        <!-- Saluran & Sosial Media -->
        @if(!empty($company->social_media))
            <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-4">
                <h2 class="text-sm font-black text-slate-400 uppercase tracking-wider">Saluran Komunikasi Resmi</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @foreach($company->social_media as $socmed)
                        <a href="{{ $socmed['url'] ?? '#' }}" target="_blank" class="p-4 bg-slate-50 hover:bg-indigo-50 border border-slate-200/60 rounded-2xl text-xs font-bold text-slate-800 hover:text-indigo-700 transition-all flex items-center justify-between">
                            <span class="flex items-center gap-2 truncate">
                                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                {{ $socmed['platform'] ?? 'Media' }}
                            </span>
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Blok Konten Portofolio Dinamis (JSON) -->
@php
    $showcaseProjects = \App\Models\Project::where('company_profile_id', $company->id)
        ->where('is_showcased', true)
        ->latest()
        ->get();
@endphp

<!-- SEKSI PORTOFOLIO PROYEK PUBLIK (Otomatis dari Project Management) -->
@if($showcaseProjects->count() > 0)
    <div class="space-y-8">
        <div class="flex items-center justify-between border-b border-slate-200/60 pb-4">
            <div>
                <h2 class="text-xl font-black text-slate-900 tracking-tight">Katalog Portofolio & Karya</h2>
                <p class="text-xs text-slate-400 mt-0.5">Daftar proyek dan sistem yang telah berhasil kami rancang dan kembangkan.</p>
            </div>
            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl text-xs font-black">
                {{ $showcaseProjects->count() }} Proyek Live
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach($showcaseProjects as $p)
                <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                    
                    <div>
                        <!-- Cover Project -->
                        <div class="h-56 w-full bg-slate-50 relative overflow-hidden">
                            @if($p->project_cover)
                                <img src="{{ asset('storage/' . $p->project_cover) }}" alt="{{ $p->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-r from-slate-100 to-indigo-50 flex items-center justify-center text-slate-500 font-bold text-xs">
                                    Project Showcase
                                </div>
                            @endif

                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 bg-slate-900/80 backdrop-blur-md text-white text-[10px] font-black uppercase rounded-xl">
                                    {{ $p->category }}
                                </span>
                            </div>

                            @if($p->deadline)
                                <div class="absolute top-4 right-4">
                                    <span class="px-3 py-1 bg-white/90 backdrop-blur-md text-slate-800 text-[10px] font-bold rounded-xl shadow-sm">
                                        {{ $p->deadline->format('Y') }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="p-7 space-y-5">
                            <!-- Client Info & Logo Client -->
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-lg font-black text-slate-900">{{ $p->name }}</h3>
                                    <span class="text-xs font-bold text-emerald-600 block mt-0.5">{{ $p->client_name ?? 'Client Partner' }}</span>
                                </div>
                                @if($p->client_logo)
                                    <img src="{{ asset('storage/' . $p->client_logo) }}" alt="Logo Client" class="h-10 w-20 object-contain p-1 border border-slate-100 rounded-xl bg-slate-50 shrink-0">
                                @endif
                            </div>

                            <!-- Short Summary / Problem & Solution -->
                            <p class="text-xs text-slate-600 leading-relaxed font-medium">
                                {{ $p->short_description ?? $p->problem_statement }}
                            </p>

                            @if($p->solution_statement)
                                <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 text-xs">
                                    <span class="font-bold text-slate-700 block mb-1">Solusi:</span>
                                    <p class="text-slate-600 leading-relaxed">{{ $p->solution_statement }}</p>
                                </div>
                            @endif

                            <!-- Services yang dikerjakan -->
                            @if(!empty($p->services_rendered))
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Layanan yang Dikerjakan:</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($p->services_rendered as $svc)
                                            <span class="px-2.5 py-1 bg-indigo-50 border border-indigo-100 text-indigo-700 rounded-lg text-[10px] font-bold">
                                                {{ $svc }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Tech Stacks -->
                            @if(!empty($p->tech_stacks))
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Teknologi yang Digunakan:</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($p->tech_stacks as $tech)
                                            <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-[10px] font-semibold">
                                                {{ $tech }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Galeri Mockup -->
                            @if(!empty($p->gallery_images))
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Cuplikan Galeri:</span>
                                    <div class="grid grid-cols-3 gap-2">
                                        @foreach(array_slice($p->gallery_images, 0, 3) as $img)
                                            <img src="{{ asset('storage/' . $img) }}" alt="Preview" class="w-full h-16 object-cover rounded-xl border border-slate-100">
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Testimoni Klien -->
                            @if(!empty($p->client_testimonial) && !empty($p->client_testimonial['content']))
                                <div class="p-4 bg-emerald-50/50 border border-emerald-100 rounded-2xl text-xs space-y-1">
                                    <p class="italic text-slate-700 font-medium">"{{ $p->client_testimonial['content'] }}"</p>
                                    <div class="font-bold text-emerald-900 text-[11px]">
                                        — {{ $p->client_testimonial['author'] ?? 'Klien' }} ({{ $p->client_testimonial['role'] ?? '' }})
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer Action Card (Demo Link) -->
                    <div class="p-6 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-400">
                            Durasi: {{ $p->start_date && $p->deadline ? $p->start_date->diffInDays($p->deadline) . ' Hari' : 'Selesai' }}
                        </span>

                        @if($p->demo_url)
                            <a href="{{ $p->demo_url }}" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-1.5">
                                <span>Lihat Website / Demo</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            </a>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>
    </div>
@endif

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200/60 py-8 text-center text-xs text-slate-400">
        <p>&copy; 2026 PT YOTA INOVASI NUSANTARA. All rights reserved. LUNOU Ecosystem Project Showcase.</p>
    </footer>

</body>
</html>