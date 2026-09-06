<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Klien: {{ $project->name }} | Yoimo Workspace</title>
    <meta name="description" content="Portal pemantauan progres proyek, linimasa roadmap, dan transparansi kolaborasi resmi untuk {{ $project->client_name ?? 'Klien' }} pada proyek {{ $project->name }}.">

    <!-- Open Graph / LinkedIn -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('client.portal.show', $project->share_token) }}">
    <meta property="og:title" content="Portal Klien: {{ $project->name }} - Yoimo Workspace">
    <meta property="og:description" content="Pantau progres proyek, linimasa roadmap, dan transparansi kerja secara real-time pada proyek {{ $project->name }}.">
    <meta property="og:image" content="{{ $project->project_cover ? asset('storage/' . $project->project_cover) : asset('images/yoimo-og-banner.png') }}">
    <meta property="og:site_name" content="Yoimo Workspace">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ route('client.portal.show', $project->share_token) }}">
    <meta name="twitter:title" content="Portal Klien: {{ $project->name }} - Yoimo Workspace">
    <meta name="twitter:description" content="Pantau progres proyek, linimasa roadmap, dan transparansi kerja secara real-time pada proyek {{ $project->name }}.">
    <meta name="twitter:image" content="{{ $project->project_cover ? asset('storage/' . $project->project_cover) : asset('images/yoimo-og-banner.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <!-- TailwindCSS CSS (via CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        slate: {
                            850: '#1e293b',
                        },
                        indigo: {
                            650: '#4f46e5',
                            755: '#4338ca',
                        },
                        wa: {
                            canvas: '#efeae2',
                            outgoing: '#d9fdd3',
                            header: '#f0f2f5',
                            teal: '#00a884',
                            'teal-hover': '#008f6f',
                            'teal-dark': '#008069'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #f8fafc;
            font-feature-settings: "cv02", "cv03", "cv04", "cv11";
        }
        /* Custom WhatsApp-style scrollbar */
        #portal-messages-stream::-webkit-scrollbar {
            width: 6px;
        }
        #portal-messages-stream::-webkit-scrollbar-track {
            background: transparent;
        }
        #portal-messages-stream::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.15);
            border-radius: 3px;
        }
        #portal-messages-stream::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.28);
        }
        /* Subtle WhatsApp doodle background texture */
        .wa-chat-bg {
            background-color: #efeae2;
            background-image: radial-gradient(#d5dbde 0.75px, transparent 0.75px);
            background-size: 18px 18px;
        }
        /* Filter tabs active state */
        .portal-filter-active {
            background-color: #1e293b !important;
            color: #ffffff !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        }
    </style>
</head>
<body class="font-sans text-slate-800 antialiased min-h-screen pb-12">

    <!-- Header Section -->
    <header class="bg-white border-b border-slate-100 shadow-xs sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 bg-indigo-50 border border-indigo-100 rounded-xl text-[10px] font-black text-indigo-700 uppercase tracking-wider">
                    Portal Klien
                </span>
                <span class="h-4 w-px bg-slate-200"></span>
                <h1 class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $project->name }}</h1>
            </div>
            <div class="text-right">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">PROGRES PROYEK</span>
                <span class="text-lg font-black text-indigo-650">{{ $progress }}%</span>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 mt-8 space-y-8">

        <!-- Flash Alert Notification -->
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-sm">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
            </div>
        @endif

        <!-- 1. Hero Overview -->
        <section class="bg-white border border-slate-200/60 rounded-3xl p-6 sm:p-8 shadow-sm flex flex-col md:flex-row justify-between gap-6 items-start md:items-center">
            <div class="space-y-2 max-w-2xl">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 bg-indigo-50 border border-indigo-100 text-indigo-700 rounded-lg text-[9px] font-black uppercase tracking-wider">{{ $project->category }}</span>
                    <span class="text-xs text-slate-400 font-semibold">• Tahapan Saat Ini: <strong class="text-slate-700">{{ $project->current_stage }}</strong></span>
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ $project->name }}</h2>
                <p class="text-xs text-slate-400 font-semibold leading-relaxed">
                    {{ $project->description ?? 'Deskripsi proyek belum ditambahkan.' }}
                </p>
            </div>

            <!-- Progress Circle -->
            <div class="shrink-0 flex items-center gap-4 bg-slate-50 border border-slate-200/60 p-4 rounded-2xl shadow-xs">
                <div class="w-12 h-12 rounded-full border-4 border-indigo-100 border-t-indigo-650 flex items-center justify-center font-black text-xs text-indigo-755">
                    {{ $progress }}%
                </div>
                <div>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">RATE PENYELESAIAN</span>
                    <span class="text-xs font-bold text-slate-700 block mt-0.5">Tugas Selesai</span>
                </div>
            </div>
        </section>

        <!-- 2. AI Executive Summary (Report) -->
        <section class="bg-white border border-slate-200/60 rounded-3xl p-6 sm:p-8 shadow-sm space-y-4">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-indigo-50 border border-indigo-100 rounded-2xl">
                    <img src="{{ asset('icon/11.png') }}" class="w-6 h-6 object-contain" alt="LUNOU">
                </div>
                <div>
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">Laporan Kemajuan LUNOU AI</h3>
                    <span class="text-[9px] font-semibold text-slate-400 uppercase tracking-wider block mt-0.5">Disusun & dirapikan otomatis oleh AI</span>
                </div>
            </div>
            
            <div class="prose prose-sm text-xs font-semibold text-slate-600 leading-relaxed border-t border-slate-100 pt-4 space-y-3">
                {!! $aiReport !!}
            </div>
        </section>

        <!-- 3. Roadmap & Active Tasks Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Roadmap (Width: 5 columns) -->
            <div class="lg:col-span-5 bg-white border border-slate-200/60 rounded-3xl p-6 shadow-sm space-y-5">
                <div>
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">Roadmap & Milestone</h3>
                    <span class="text-[9px] font-semibold text-slate-400 uppercase tracking-wider block mt-0.5">Linimasa fase kerja utama</span>
                </div>

                @if($roadmaps->isEmpty())
                    <div class="border border-dashed border-slate-200 rounded-2xl py-8 px-4 text-center">
                        <p class="text-xs text-slate-400 font-semibold">Linimasa roadmap belum dikonfigurasi.</p>
                    </div>
                @else
                    <div class="space-y-4 relative border-l border-slate-100 pl-4 ml-2">
                        @foreach($roadmaps as $idx => $r)
                            <div class="relative space-y-1.5">
                                <!-- Dot -->
                                <span class="absolute -left-[21px] top-1.5 w-2.5 h-2.5 rounded-full bg-indigo-650 border-2 border-white shadow-sm"></span>
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-[9px] font-black text-indigo-700 uppercase tracking-tight">Fase {{ $idx + 1 }}</span>
                                    <span class="text-[9px] font-bold text-slate-400">{{ \Carbon\Carbon::parse($r->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($r->end_date)->format('d M Y') }}</span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-800">{{ $r->title }}</h4>
                                <p class="text-[10px] text-slate-500 font-semibold leading-normal">{{ $r->description }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Active Tasks (Width: 7 columns) -->
            <div class="lg:col-span-7 bg-white border border-slate-200/60 rounded-3xl p-6 shadow-sm space-y-5">
                <div>
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">Tracking Pengerjaan Tugas</h3>
                    <span class="text-[9px] font-semibold text-slate-400 uppercase tracking-wider block mt-0.5">Status pengerjaan terperinci</span>
                </div>

                @if($tasks->isEmpty())
                    <div class="border border-dashed border-slate-200 rounded-2xl py-8 px-4 text-center">
                        <p class="text-xs text-slate-400 font-semibold">Belum ada tugas pengerjaan yang aktif.</p>
                    </div>
                @else
                    <div class="overflow-x-auto max-h-[45vh] overflow-y-auto pr-1">
                        <table class="w-full text-left text-xs font-semibold">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider text-[9px]">
                                    <th class="py-3 pr-2">Tugas</th>
                                    <th class="py-3 px-2">Status</th>
                                    <th class="py-3 px-2">PIC</th>
                                    <th class="py-3 pl-2 text-right">Deadline</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-slate-700">
                                @foreach($tasks as $t)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="py-3 pr-2 font-bold text-slate-900 leading-normal">{{ $t->title }}</td>
                                        <td class="py-3 px-2">
                                            <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase border {{ $t->status === 'Completed' ? 'bg-emerald-50 border-emerald-100 text-emerald-700' : ($t->status === 'In Progress' ? 'bg-purple-50 border-purple-100 text-purple-700' : 'bg-slate-50 border-slate-200 text-slate-500') }}">
                                                {{ $t->status }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-2 text-slate-500 text-[10px]">{{ $t->assigned_to ? 'Ada PIC' : 'Internal Team' }}</td>
                                        <td class="py-3 pl-2 text-right text-slate-400 text-[10px]">
                                            {{ $t->due_date ? \Carbon\Carbon::parse($t->due_date)->format('d M Y') : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
        <!-- 4. Real-time Project Chat & Discussion Section (WhatsApp Inspired, Neutral & Mobile Friendly) -->
        <section id="project-chat-section" class="bg-white border border-slate-200/80 rounded-3xl shadow-sm overflow-hidden flex flex-col">
            
            <!-- WhatsApp Top Bar -->
            <div class="px-4 sm:px-6 py-3.5 bg-[#f0f2f5] border-b border-slate-200/80 flex items-center justify-between gap-3">
                <!-- Left: Project Avatar & Info -->
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-[#00a884] text-white flex items-center justify-center font-bold text-sm shrink-0 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm sm:text-base font-bold text-slate-800 tracking-tight truncate">Ruang Diskusi & Chat Proyek</h3>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800 shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Sinkron Live
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 font-normal truncate mt-0.5">Komunikasi dua arah real-time tim pengembang & pemangku kepentingan</p>
                    </div>
                </div>

                <!-- Right: Auth / Guest Status Indicator -->
                <div class="flex items-center gap-2 shrink-0">
                    @auth
                        <div class="flex items-center gap-2 bg-white/90 border border-slate-200/80 px-3 py-1.5 rounded-full shadow-2xs">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-6 h-6 rounded-full object-cover border border-slate-200">
                            @else
                                <div class="w-6 h-6 rounded-full bg-slate-800 text-white font-bold text-[10px] flex items-center justify-center uppercase">
                                    {{ substr(Auth::user()->name, 0, 2) }}
                                </div>
                            @endif
                            <div class="text-left hidden sm:block">
                                <span class="text-xs font-semibold text-slate-800 leading-tight block">{{ Auth::user()->name }}</span>
                            </div>
                            <span class="text-[10px] font-semibold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                                {{ ucfirst(Auth::user()->role) }}
                            </span>
                        </div>
                    @else
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 bg-white/90 text-slate-600 border border-slate-200 rounded-full text-xs font-medium hidden sm:inline shadow-2xs">
                                Mode Klien
                            </span>
                            <a href="{{ route('login', ['redirect' => request()->fullUrl()]) }}" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200/80 rounded-full text-xs font-semibold transition-all flex items-center gap-1.5 shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                <span>Masuk Tim</span>
                            </a>
                        </div>
                    @endauth
                </div>
            </div>

            <!-- Dual Mode Navigation Switcher: Diskusi Proyek VS Pengumpulan Aset & Berkas -->
            <div class="px-4 sm:px-6 py-2 bg-white border-b border-slate-200/80 flex items-center justify-between gap-3 overflow-x-auto no-scrollbar">
                <div class="flex items-center gap-1.5 shrink-0">
                    <button type="button" id="tab-btn-chat" onclick="switchPortalView('chat')"
                            class="px-4 py-2 text-xs sm:text-sm font-bold border-b-2 border-[#00a884] text-[#00a884] transition-all flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        <span>Obrolan Diskusi</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200" id="badge-chat-count">{{ $messages->count() }}</span>
                    </button>
                    <button type="button" id="tab-btn-assets" onclick="switchPortalView('assets')"
                            class="px-4 py-2 text-xs sm:text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-all flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Pengumpulan Aset</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $assetProgress === 100 ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200' }}" id="badge-assets-summary">
                            <span id="tab-assets-submitted-count">{{ $submittedAssets }}</span>/<span id="tab-assets-total-count">{{ $totalAssets }}</span>
                        </span>
                    </button>
                </div>
                <div class="shrink-0 flex items-center gap-2">
                    <button type="button" onclick="openAddAssetRequirementModal()" 
                            class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-full text-xs font-semibold flex items-center gap-1.5 shadow-2xs transition-all cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        <span class="hidden sm:inline">Tambah Kebutuhan Aset</span>
                        <span class="sm:hidden">+ Aset</span>
                    </button>
                </div>
            </div>

            <!-- 1. TAB VIEW: Obrolan Diskusi -->
            <div id="portal-view-chat" class="flex flex-col flex-1">
                
                <!-- Pinned Asset Collection Notification Banner -->
                <div id="pinned-asset-banner" class="px-4 sm:px-6 py-2.5 bg-emerald-50/90 border-b border-emerald-100 flex items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="text-base shrink-0">📁</span>
                        <div class="truncate">
                            <span class="font-bold text-emerald-900">Formulir Pengumpulan Aset:</span>
                            <span class="text-emerald-800 font-normal">
                                <span id="banner-submitted-count">{{ $submittedAssets }}</span> dari <span id="banner-total-count">{{ $totalAssets }}</span> berkas terkumpul (<span id="banner-progress">{{ $assetProgress }}%</span>).
                            </span>
                        </div>
                    </div>
                    <button type="button" onclick="switchPortalView('assets')" class="shrink-0 px-3 py-1 bg-white hover:bg-emerald-100 text-emerald-800 border border-emerald-300/80 rounded-full text-xs font-bold transition-all shadow-2xs flex items-center gap-1 cursor-pointer">
                        <span>Buka Formulir</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>

                <!-- WhatsApp Navigation Filter Bar -->
                <div class="px-4 sm:px-6 py-2.5 bg-white border-b border-slate-200/60 flex items-center justify-between gap-2 overflow-x-auto no-scrollbar">
                    <div class="flex items-center gap-1.5 shrink-0">
                    <button type="button" onclick="filterPortalMessages('all')" id="btn-filter-all" class="portal-filter-active px-3 py-1.5 rounded-full text-xs font-medium transition-all cursor-pointer">
                        Semua (<span id="count-all">{{ $messages->count() }}</span>)
                    </button>
                    <button type="button" onclick="filterPortalMessages('kendala-pending')" id="btn-filter-kendala-pending" class="px-3 py-1.5 rounded-full text-xs font-medium transition-all cursor-pointer bg-slate-100 hover:bg-slate-200 text-slate-700">
                        ⚠️ Hambatan Aktif (<span id="count-kendala-pending">{{ $messages->where('message_type', 'kendala')->where('is_resolved', false)->count() }}</span>)
                    </button>
                    <button type="button" onclick="filterPortalMessages('question')" id="btn-filter-question" class="px-3 py-1.5 rounded-full text-xs font-medium transition-all cursor-pointer bg-slate-100 hover:bg-slate-200 text-slate-700">
                        ❓ Tanya (<span id="count-question">{{ $messages->where('message_type', 'question')->count() }}</span>)
                    </button>
                    <button type="button" onclick="filterPortalMessages('kendala-resolved')" id="btn-filter-kendala-resolved" class="px-3 py-1.5 rounded-full text-xs font-medium transition-all cursor-pointer bg-slate-100 hover:bg-slate-200 text-slate-700">
                        ✅ Selesai (<span id="count-kendala-resolved">{{ $messages->where('message_type', 'kendala')->where('is_resolved', true)->count() }}</span>)
                    </button>
                </div>
                <div class="text-[11px] text-slate-400 font-normal shrink-0 hidden md:block">
                    Pembaruan otomatis tiap 3 detik
                </div>
            </div>

            <!-- Scrollable Messages Canvas (WhatsApp Chat Stream) -->
            <div id="portal-messages-stream" class="wa-chat-bg flex-1 p-3.5 sm:p-6 space-y-3.5 overflow-y-auto min-h-[380px] h-[480px] sm:h-[540px] relative scroll-smooth">
                
                <!-- Date Pill in Center -->
                <div class="flex justify-center sticky top-0 z-10 pointer-events-none pb-2">
                    <span class="bg-white/90 backdrop-blur-xs text-slate-600 text-[11px] font-medium px-3.5 py-1 rounded-full shadow-2xs border border-slate-200/60 pointer-events-auto">
                        Riwayat Diskusi Proyek
                    </span>
                </div>

                @forelse($messages as $msg)
                    @php
                        $isMe = Auth::check() && ($msg->sender_id === Auth::id());
                        $isTeam = !empty($msg->sender_id);
                    @endphp

                    <div class="portal-message-item flex items-end gap-2 sm:gap-2.5 {{ $isMe ? 'flex-row-reverse' : 'flex-row' }}" 
                         data-id="{{ $msg->id }}" 
                         data-type="{{ $msg->message_type ?? 'chat' }}"
                         data-resolved="{{ $msg->is_resolved ? 'true' : 'false' }}"
                         data-has-attachment="{{ $msg->attachment_file ? 'true' : 'false' }}">
                        
                        <!-- Avatar -->
                        <div class="shrink-0 mb-1">
                            @if($msg->sender && $msg->sender->avatar)
                                <img src="{{ asset('storage/' . $msg->sender->avatar) }}" alt="Avatar" class="w-8 h-8 rounded-full object-cover border border-slate-200 shadow-2xs">
                            @elseif($isTeam)
                                <div class="w-8 h-8 rounded-full bg-slate-800 text-white font-bold text-[10px] flex items-center justify-center uppercase shadow-2xs">
                                    {{ substr($msg->sender->name ?? 'TM', 0, 2) }}
                                </div>
                            @else
                                <div class="w-8 h-8 rounded-full bg-emerald-600 text-white font-bold text-[10px] flex items-center justify-center uppercase shadow-2xs">
                                    {{ substr($msg->client_name ?? 'KL', 0, 2) }}
                                </div>
                            @endif
                        </div>

                        <!-- Card Bubble -->
                        <div class="max-w-[85%] sm:max-w-[75%] space-y-1 {{ $isMe ? 'items-end' : 'items-start' }}">
                            
                            <!-- Bubble Content Box -->
                            <div class="p-3 sm:p-3.5 rounded-2xl shadow-2xs space-y-1.5 transition-all {{ $isMe ? 'bg-[#d9fdd3] text-slate-800 rounded-br-xs border border-emerald-200/50' : 'bg-white text-slate-800 rounded-bl-xs border border-slate-200/70' }}">
                                
                                <!-- Header Info: Sender Name & Badges -->
                                <div class="flex items-center justify-between gap-2 border-b {{ $isMe ? 'border-emerald-200/40' : 'border-slate-100' }} pb-1.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xs font-bold {{ $isMe ? 'text-emerald-900' : 'text-slate-800' }}">{{ $msg->sender_display_name }}</span>
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-semibold {{ $isTeam ? 'bg-slate-100 text-slate-700' : 'bg-emerald-100 text-emerald-800' }}">
                                            {{ $isTeam ? ($msg->sender->role ?? 'Tim Proyek') : 'Klien' }}
                                        </span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-normal">{{ $msg->created_at->diffForHumans() }}</span>
                                </div>

                                <!-- Category Tag Badge -->
                                @if($msg->message_type === 'kendala')
                                    <div class="flex items-center justify-between gap-2 pt-0.5">
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-900 border border-amber-200/80">
                                            <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            <span>Hambatan / Kendala</span>
                                        </div>
                                        <div class="message-type-header-badge">
                                            @if($msg->is_resolved)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    Terselesaikan
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-semibold bg-amber-100 text-amber-900 border border-amber-200">
                                                    Menunggu Solusi
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @elseif($msg->message_type === 'question')
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-sky-50 text-sky-800 border border-sky-200/80 pt-0.5">
                                        <svg class="w-3 h-3 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span>Pertanyaan / Q&A</span>
                                    </div>
                                @endif

                                <!-- Message Text -->
                                @if($msg->message)
                                    <p class="text-xs sm:text-[13px] font-normal leading-relaxed whitespace-pre-line break-words text-slate-800 py-0.5">{{ $msg->message }}</p>
                                @endif

                                <!-- Attachment Rendering -->
                                @if($msg->attachment_file)
                                    @if($msg->is_image)
                                        <!-- Screenshot / Image Preview -->
                                        <div class="rounded-xl overflow-hidden border border-slate-200/80 mt-1 bg-white/60">
                                            <div class="relative group cursor-pointer" onclick="openImageLightbox('{{ asset('storage/' . $msg->attachment_file) }}', '{{ $msg->attachment_name ?? 'Screenshot' }}')">
                                                <img src="{{ asset('storage/' . $msg->attachment_file) }}" alt="{{ $msg->attachment_name }}" 
                                                     class="max-h-56 w-full object-cover rounded-xl transition-transform duration-200 group-hover:scale-[1.01]">
                                                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-medium gap-1.5">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                                                    <span>Klik Perbesar</span>
                                                </div>
                                            </div>
                                            <div class="p-2 flex items-center justify-between text-[11px] bg-slate-50 text-slate-700">
                                                <span class="truncate max-w-[170px] font-medium">{{ $msg->attachment_name ?? 'Screenshot' }}</span>
                                                <a href="{{ asset('storage/' . $msg->attachment_file) }}" download class="font-semibold text-emerald-700 hover:underline">Unduh</a>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Document File Preview -->
                                        <div class="p-2.5 rounded-xl border border-slate-200/80 bg-white flex items-center gap-2.5 mt-1 shadow-2xs">
                                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </div>
                                            <div class="flex-1 min-w-0 text-left">
                                                <span class="block text-xs font-semibold text-slate-800 truncate">{{ $msg->attachment_name ?? 'Dokumen Proyek' }}</span>
                                                <span class="block text-[10px] text-slate-400">Berkas Lampiran</span>
                                            </div>
                                            <a href="{{ asset('storage/' . $msg->attachment_file) }}" target="_blank" download
                                               class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold shrink-0 transition-colors">
                                                Unduh
                                            </a>
                                        </div>
                                    @endif
                                @endif

                                <!-- Interactive Checklist & Resolution Box for Kendala -->
                                @if($msg->message_type === 'kendala')
                                    <div id="portal-resolution-card-{{ $msg->id }}" class="mt-2 pt-2 border-t {{ $isMe ? 'border-emerald-200/50' : 'border-slate-100' }}">
                                        @if(!$msg->is_resolved)
                                            <div class="rounded-xl p-3 border bg-amber-50/70 border-amber-200/70 text-slate-800 space-y-2 text-left">
                                                <div class="flex items-center justify-between gap-2">
                                                    <div class="flex items-center gap-1.5 text-xs font-semibold text-amber-900">
                                                        <svg class="w-4 h-4 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        <span>Status: Menunggu Penanganan</span>
                                                    </div>
                                                    <button type="button" onclick="togglePortalResolutionPrompt({{ $msg->id }})" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition-all flex items-center gap-1 shadow-2xs cursor-pointer">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                        <span>Tandai Selesai</span>
                                                    </button>
                                                </div>
                                                
                                                <!-- Inline Note Prompt Form -->
                                                <div id="portal-resolve-box-{{ $msg->id }}" class="hidden pt-2 border-t border-amber-200/60 space-y-2">
                                                    <label class="block text-xs font-medium text-slate-700">Catatan Solusi / Penanganan:</label>
                                                    <textarea id="portal-resolve-note-{{ $msg->id }}" rows="2" placeholder="Tuliskan catatan bagaimana kendala ini diselesaikan..." class="w-full p-2.5 text-xs bg-white text-slate-800 border border-slate-200 rounded-xl font-normal focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                                                    <div class="flex items-center justify-end gap-2">
                                                        <button type="button" onclick="togglePortalResolutionPrompt({{ $msg->id }})" class="px-2.5 py-1 text-xs font-medium text-slate-600 hover:text-slate-800 cursor-pointer">Batal</button>
                                                        <button type="button" onclick="submitPortalResolution({{ $msg->id }}, true)" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold flex items-center gap-1 shadow-2xs cursor-pointer">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                            <span>Simpan Status</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="rounded-xl p-3 border bg-emerald-50/70 border-emerald-200/70 text-slate-800 space-y-2 text-left">
                                                <div class="flex items-center justify-between gap-2">
                                                    <div class="flex items-center gap-1.5 text-xs font-semibold text-emerald-800">
                                                        <svg class="w-4 h-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        <span>Kendala Telah Diselesaikan</span>
                                                    </div>
                                                    <button type="button" onclick="submitPortalResolution({{ $msg->id }}, false)" class="px-2 py-0.5 text-xs font-medium text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded transition-all cursor-pointer" title="Buka kembali kendala jika masih butuh penanganan">
                                                        Buka Kembali
                                                    </button>
                                                </div>
                                                <div class="text-xs bg-white/90 text-slate-700 p-2.5 rounded-lg space-y-1 border border-emerald-100">
                                                    <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 text-[11px]">
                                                        <span class="font-medium text-slate-500">Diselesaikan oleh:</span>
                                                        <span class="font-semibold text-slate-800">{{ $msg->resolver_name ?? 'Tim Proyek' }}</span>
                                                        @if($msg->resolved_at)
                                                            <span class="text-slate-400 font-normal">({{ $msg->resolved_at->diffForHumans() }})</span>
                                                        @endif
                                                    </div>
                                                    @if($msg->resolution_note)
                                                        <div class="pt-1.5 border-t border-slate-100 mt-1">
                                                            <span class="font-medium text-[10px] text-slate-500 block">Catatan Solusi:</span>
                                                            <p class="text-slate-700 font-normal italic leading-relaxed whitespace-pre-line">{{ $msg->resolution_note }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <!-- WhatsApp Message Footer (Time & Checkmarks) -->
                                <div class="flex items-center justify-end gap-1 pt-0.5 text-[10px] {{ $isMe ? 'text-emerald-800/80' : 'text-slate-400' }}">
                                    <span>{{ $msg->created_at->format('H:i') }}</span>
                                    @if($isMe)
                                        <!-- WhatsApp double checkmark -->
                                        <svg class="w-3.5 h-3.5 text-emerald-600" viewBox="0 0 16 16" fill="currentColor">
                                            <path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0l7-7zm-4.5 4.5l.708-.708-3.5-3.5a.5.5 0 0 0-.708.708l3.5 3.5z"/>
                                        </svg>
                                    @endif
                                </div>

                            </div>

                        </div>

                    </div>
                @empty
                    <div id="portal-empty-chat-state" class="border border-dashed border-slate-300/80 bg-white/60 rounded-2xl py-12 px-4 text-center space-y-2 my-auto">
                        <div class="w-10 h-10 mx-auto rounded-full bg-emerald-50 text-[#00a884] flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        </div>
                        <h4 class="text-sm font-semibold text-slate-700">Belum ada obrolan pada proyek ini</h4>
                        <p class="text-xs text-slate-500 font-normal max-w-sm mx-auto leading-normal">
                            Tuliskan pesan, pertanyaan, atau laporkan kendala dengan tim pengembang melalui kolom di bawah.
                        </p>
                    </div>
                @endforelse

            </div>

            <!-- WhatsApp Bottom Composer Form -->
            <div class="bg-[#f0f2f5] border-t border-slate-200/80 p-3 sm:p-4 space-y-2">
                <form id="portal-chat-form" method="POST" action="{{ route('client.portal.messages.send', $project->share_token) }}" enctype="multipart/form-data" class="space-y-2">
                    @csrf

                    <!-- 1. Category Quick Selector Chips & Guest Name -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-0.5">
                        <!-- Category Chips -->
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-xs font-medium text-slate-500 mr-1 hidden sm:inline">Kategori:</span>
                            <label class="cursor-pointer">
                                <input type="radio" name="message_type" value="chat" checked class="peer sr-only">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-white text-slate-600 border border-slate-200/80 peer-checked:bg-slate-800 peer-checked:text-white peer-checked:border-slate-800 transition-all shadow-2xs flex items-center gap-1">
                                    💬 <span>Diskusi</span>
                                </span>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="message_type" value="kendala" class="peer sr-only">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-white text-slate-600 border border-slate-200/80 peer-checked:bg-amber-600 peer-checked:text-white peer-checked:border-amber-600 transition-all shadow-2xs flex items-center gap-1">
                                    ⚠️ <span>Hambatan</span>
                                </span>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="message_type" value="question" class="peer sr-only">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-white text-slate-600 border border-slate-200/80 peer-checked:bg-sky-600 peer-checked:text-white peer-checked:border-sky-600 transition-all shadow-2xs flex items-center gap-1">
                                    ❓ <span>Pertanyaan</span>
                                </span>
                            </label>
                        </div>

                        <!-- Guest Name Field (if not logged in) -->
                        @guest
                            <div class="flex items-center gap-1.5 w-full sm:w-auto">
                                <span class="text-xs font-medium text-slate-500 shrink-0">Nama Anda:</span>
                                <input type="text" name="client_name" id="client_name" required placeholder="Contoh: Budi Santoso / Klien"
                                       class="flex-1 sm:w-48 px-3 py-1 text-xs bg-white border border-slate-200 rounded-lg focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 font-medium text-slate-700 shadow-2xs">
                            </div>
                        @endguest
                    </div>

                    <!-- 2. Attachment Preview Box (Hidden until file selected or pasted) -->
                    <div id="portal-file-preview" class="hidden p-2.5 bg-white border border-emerald-300 rounded-xl flex items-center justify-between gap-3 shadow-2xs">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div id="portal-file-thumb-container" class="shrink-0">
                                <!-- Image thumbnail or file icon injected by JS -->
                            </div>
                            <div class="min-w-0">
                                <span id="portal-file-name" class="block text-xs font-semibold text-slate-800 truncate"></span>
                                <span id="portal-file-size" class="block text-[10px] text-slate-400 font-medium"></span>
                            </div>
                        </div>
                        <button type="button" onclick="clearPortalFile()" class="text-slate-400 hover:text-rose-600 font-bold p-1 text-sm cursor-pointer" title="Hapus Lampiran">
                            &times;
                        </button>
                    </div>

                    <!-- 3. WhatsApp Main Input Row: Paperclip + Expanding Textarea + Round Send Button -->
                    <div class="flex items-end gap-2">
                        <input type="file" name="file" id="portal_file_input" class="hidden" 
                               accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.txt">

                        <!-- Paperclip Attachment Button -->
                        <button type="button" onclick="document.getElementById('portal_file_input').click()" 
                                class="w-10 h-10 rounded-full bg-white hover:bg-slate-100 active:scale-95 text-slate-600 border border-slate-200/80 flex items-center justify-center shrink-0 transition-all shadow-2xs cursor-pointer" 
                                title="Lampirkan Dokumen / Screenshot (Maks. 20MB)">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                        </button>

                        <!-- Expanding Input Box -->
                        <div class="flex-1 min-w-0 bg-white rounded-2xl border border-slate-200 shadow-2xs focus-within:border-[#00a884] focus-within:ring-1 focus-within:ring-[#00a884] transition-all px-4 py-2.5 flex items-center">
                            <textarea name="message" id="portal_message_input" rows="1" 
                                      placeholder="Tuliskan kendala, pertanyaan, atau pesan... (Ctrl+V untuk paste screenshot)"
                                      class="w-full text-xs sm:text-sm bg-transparent border-0 focus:ring-0 p-0 text-slate-800 placeholder:text-slate-400 font-normal resize-none max-h-32 leading-relaxed"
                                      oninput="this.style.height = ''; this.style.height = Math.min(this.scrollHeight, 128) + 'px'"></textarea>
                        </div>

                        <!-- Circular WhatsApp Send Button -->
                        <button type="submit" id="portal-send-btn" 
                                class="w-10 h-10 rounded-full bg-[#00a884] hover:bg-[#008f6f] active:scale-95 text-white flex items-center justify-center shrink-0 transition-all shadow-sm cursor-pointer"
                                title="Kirim ke Chat Proyek">
                            <svg class="w-5 h-5 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            <span id="portal-send-btn-text" class="sr-only">Kirim ke Chat Proyek</span>
                        </button>
                    </div>

                    <!-- Hints Row -->
                    <div class="flex items-center justify-between text-[10px] text-slate-400 px-1 pt-0.5">
                        <span class="hidden sm:inline">Tekan Enter untuk kirim • Shift+Enter baris baru • Ctrl+V tempel screenshot</span>
                        <span class="sm:hidden">Ctrl+V untuk tempel screenshot</span>
                        <span>Maks. berkas 20MB</span>
                    </div>

                </form>
            </div>
            </div>

            <!-- 2. TAB VIEW: Formulir Pengumpulan Aset & Berkas -->
            <div id="portal-view-assets" class="hidden flex flex-col flex-1 bg-[#f8fafc]">
                
                <!-- Asset Hub Header & Progress Summary Card -->
                <div class="p-4 sm:p-6 bg-white border-b border-slate-200/80 space-y-4">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    Intake Berkas Klien
                                </span>
                                <span class="text-xs text-slate-400 font-normal">&bull; Kebutuhan Desain & Teknis Proyek</span>
                            </div>
                            <h4 class="text-base sm:text-lg font-bold text-slate-800">Formulir Pengumpulan Aset & Bahan Proyek</h4>
                            <p class="text-xs sm:text-sm text-slate-500 font-normal leading-relaxed max-w-2xl">
                                Silakan kirimkan berkas, dokumen, atau tautan penyimpanan cloud (Google Drive, Figma, Dropbox) yang dibutuhkan. Tim kami akan langsung memproses bahan yang Anda submit dan terhubung otomatis dengan ruang obrolan.
                            </p>
                        </div>

                        <!-- Progress Bar & Quick Stats -->
                        <div class="bg-slate-50 border border-slate-200/90 rounded-2xl p-4 min-w-[260px] space-y-2.5 shadow-2xs">
                            <div class="flex items-center justify-between text-xs font-bold text-slate-700">
                                <span>Kelengkapan Berkas</span>
                                <span id="asset-progress-pct-label" class="text-emerald-700 font-black">{{ $assetProgress }}%</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
                                <div id="asset-progress-bar-fill" class="bg-emerald-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $assetProgress }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-slate-500">
                                <span><strong id="stat-submitted-count" class="text-slate-800">{{ $submittedAssets }}</strong> dari <strong id="stat-total-count" class="text-slate-800">{{ $totalAssets }}</strong> berkas</span>
                                @php
                                    $mandatoryPending = $assetRequirements->where('is_mandatory', true)->whereNotIn('status', ['submitted', 'approved'])->count();
                                @endphp
                                <span id="stat-mandatory-badge" class="{{ $mandatoryPending > 0 ? 'text-amber-700 font-semibold' : 'text-emerald-700 font-semibold' }}">
                                    {{ $mandatoryPending > 0 ? $mandatoryPending . ' berkas wajib tersisa' : 'Semua berkas wajib terpenuhi' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Chips for Assets -->
                    <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100 flex-wrap">
                        <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5">
                            <button type="button" onclick="filterAssetRequirements('all')" id="asset-filter-btn-all"
                                    class="px-3 py-1.5 rounded-full text-xs font-medium transition-all cursor-pointer bg-slate-800 text-white shadow-2xs">
                                Semua (<span id="asset-count-all">{{ $totalAssets }}</span>)
                            </button>
                            <button type="button" onclick="filterAssetRequirements('pending')" id="asset-filter-btn-pending"
                                    class="px-3 py-1.5 rounded-full text-xs font-medium transition-all cursor-pointer bg-slate-100 hover:bg-slate-200 text-slate-700">
                                🟡 Menunggu Input (<span id="asset-count-pending">{{ $totalAssets - $submittedAssets }}</span>)
                            </button>
                            <button type="button" onclick="filterAssetRequirements('submitted')" id="asset-filter-btn-submitted"
                                    class="px-3 py-1.5 rounded-full text-xs font-medium transition-all cursor-pointer bg-slate-100 hover:bg-slate-200 text-slate-700">
                                🔵 Terkumpul (<span id="asset-count-submitted">{{ $submittedAssets }}</span>)
                            </button>
                            <button type="button" onclick="filterAssetRequirements('mandatory')" id="asset-filter-btn-mandatory"
                                    class="px-3 py-1.5 rounded-full text-xs font-medium transition-all cursor-pointer bg-slate-100 hover:bg-slate-200 text-slate-700">
                                ⭐ Wajib Saja (<span id="asset-count-mandatory">{{ $assetRequirements->where('is_mandatory', true)->count() }}</span>)
                            </button>
                        </div>
                        <button type="button" onclick="openAddAssetRequirementModal()" 
                                class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-xl text-xs font-semibold transition-all flex items-center gap-1.5 cursor-pointer shadow-2xs">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                            <span>Sesuaikan / Tambah Aset</span>
                        </button>
                    </div>
                </div>

                <!-- Asset Cards Container -->
                <div class="p-4 sm:p-6 space-y-4 flex-1 overflow-y-auto">
                    <div id="portal-assets-list" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        @forelse($assetRequirements as $asset)
                            @include('client.partials.asset_card', ['asset' => $asset, 'token' => $project->share_token])
                        @empty
                            <div id="portal-assets-empty" class="col-span-full py-16 px-4 text-center border-2 border-dashed border-slate-200 rounded-3xl bg-white space-y-3">
                                <div class="w-12 h-12 mx-auto rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <h4 class="text-sm font-bold text-slate-800">Belum ada daftar kebutuhan aset</h4>
                                <p class="text-xs text-slate-500 max-w-sm mx-auto">
                                    Klik tombol di bawah untuk menambahkan berkas atau materi yang perlu dikumpulkan dari klien.
                                </p>
                                <button type="button" onclick="openAddAssetRequirementModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold shadow-xs">
                                    + Tambah Kebutuhan Aset
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </section>

        <!-- MODAL: Submit Asset / Upload Berkas Klien -->
        <div id="modal-submit-asset" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-xs p-4 overflow-y-auto">
            <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden my-8" onclick="event.stopPropagation()">
                <!-- Modal Header -->
                <div class="p-5 bg-gradient-to-r from-slate-900 to-slate-800 text-white flex items-center justify-between">
                    <div class="space-y-0.5 min-w-0 pr-4">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            Unggah Aset Proyek
                        </span>
                        <h4 id="submit-modal-title" class="text-base font-bold text-white truncate mt-1">Nama Aset</h4>
                        <p id="submit-modal-desc" class="text-xs text-slate-300 font-normal line-clamp-2 leading-relaxed"></p>
                    </div>
                    <button type="button" onclick="closeSubmitAssetModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-lg font-bold cursor-pointer shrink-0 transition-colors">
                        &times;
                    </button>
                </div>

                <!-- Modal Form -->
                <form id="form-submit-asset" onsubmit="submitAssetForm(event)" enctype="multipart/form-data" class="p-5 sm:p-6 space-y-4">
                    @csrf
                    <input type="hidden" id="submit-asset-id" name="asset_id" value="">

                    @guest
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-700">Nama Anda / Perusahaan <span class="text-rose-500">*</span></label>
                            <input type="text" name="client_name" id="submit-client-name" required placeholder="Contoh: Budi (Klien)"
                                   class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 font-medium text-slate-800 shadow-2xs">
                        </div>
                    @endguest

                    <!-- Input Option 1: File Upload -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 flex items-center justify-between">
                            <span>Opsi 1: Unggah File Langsung</span>
                            <span class="text-[10px] font-normal text-slate-400">JPG, PNG, PDF, ZIP, RAR, AI, EPS, SVG (Maks. 50MB)</span>
                        </label>
                        <div class="relative border-2 border-dashed border-slate-200 hover:border-emerald-400 rounded-2xl p-4 text-center cursor-pointer transition-colors bg-slate-50/50 group"
                             onclick="document.getElementById('submit-file-input').click()">
                            <input type="file" name="file" id="submit-file-input" class="hidden" onchange="handleSubmitModalFileChange(this)">
                            <div class="space-y-1">
                                <div class="w-10 h-10 mx-auto rounded-full bg-emerald-50 group-hover:bg-emerald-100 text-emerald-600 flex items-center justify-center transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                </div>
                                <div class="text-xs font-semibold text-slate-700">
                                    <span class="text-emerald-600 underline">Klik untuk memilih berkas</span> atau seret ke sini
                                </div>
                                <div id="submit-file-selected-name" class="text-[11px] font-medium text-slate-500 truncate pt-1">
                                    Belum ada berkas dipilih
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Input Option 2: External URL (Google Drive, Figma, OneDrive) -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 flex items-center justify-between">
                            <span>Opsi 2: Tautan Cloud Storage / Link Eksternal</span>
                            <span class="text-[10px] font-normal text-slate-400">Google Drive, Figma, Dropbox, OneDrive</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                            </div>
                            <input type="url" name="external_url" id="submit-external-url" placeholder="https://drive.google.com/drive/folders/..."
                                   class="w-full pl-9 pr-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 font-medium text-slate-800 shadow-2xs">
                        </div>
                    </div>

                    <!-- Input Option 3: Notes / Credentials / Access Details -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">
                            Opsi 3: Catatan, Keterangan Tambahan, atau Kredensial Akses
                        </label>
                        <textarea name="client_notes" id="submit-client-notes" rows="3" placeholder="Contoh: Username cPanel, nomor kontak PIC desainer, atau catatan detail aset..."
                                  class="w-full p-3 text-xs bg-white border border-slate-200 rounded-xl focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 font-normal text-slate-800 shadow-2xs"></textarea>
                    </div>

                    <!-- Modal Actions -->
                    <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                        <button type="button" onclick="closeSubmitAssetModal()" class="px-4 py-2 text-xs font-medium text-slate-600 hover:text-slate-800 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" id="btn-submit-asset-save" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span id="btn-submit-asset-text">Kirimkan Berkas</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Tambah Kebutuhan Aset Baru (Bisa Disesuaikan Sesuai Kebutuhan Proyek) -->
        <div id="modal-add-asset-requirement" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-xs p-4 overflow-y-auto">
            <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden my-8" onclick="event.stopPropagation()">
                <!-- Modal Header -->
                <div class="p-5 bg-slate-900 text-white flex items-center justify-between">
                    <div class="space-y-0.5">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/10 text-emerald-300 border border-emerald-400/30">
                            Kustomisasi Kebutuhan
                        </span>
                        <h4 class="text-base font-bold text-white mt-1">Tambah Kebutuhan Aset</h4>
                    </div>
                    <button type="button" onclick="closeAddAssetRequirementModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-lg font-bold cursor-pointer transition-colors">
                        &times;
                    </button>
                </div>

                <!-- Modal Form -->
                <form id="form-add-asset-requirement" onsubmit="saveNewAssetRequirement(event)" class="p-5 sm:p-6 space-y-4">
                    @csrf
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700">Nama / Judul Berkas Aset <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" id="new-asset-title" required placeholder="Contoh: Foto Produk Resolusi Tinggi, Surat Perjanjian, dll."
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 font-medium text-slate-800 shadow-2xs">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700">Kategori</label>
                        <div class="flex items-center gap-1.5 flex-wrap mb-1.5">
                            <button type="button" onclick="selectNewAssetCategory('Branding')" class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 cursor-pointer">Branding</button>
                            <button type="button" onclick="selectNewAssetCategory('Konten')" class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 cursor-pointer">Konten</button>
                            <button type="button" onclick="selectNewAssetCategory('Teknis')" class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 cursor-pointer">Teknis</button>
                            <button type="button" onclick="selectNewAssetCategory('Foto & Video')" class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 cursor-pointer">Foto & Video</button>
                            <button type="button" onclick="selectNewAssetCategory('Legal / Dokumen')" class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 cursor-pointer">Legal</button>
                        </div>
                        <input type="text" name="category" id="new-asset-category" value="Umum" placeholder="Atau ketik kategori sendiri..."
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 font-medium text-slate-800 shadow-2xs">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700">Deskripsi / Format yang Diminta</label>
                        <textarea name="description" id="new-asset-desc" rows="3" placeholder="Contoh: Mohon sediakan file foto orientasi landscape minimal 1920x1080px..."
                                  class="w-full p-3 text-xs bg-white border border-slate-200 rounded-xl focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 font-normal text-slate-800 shadow-2xs"></textarea>
                    </div>

                    <div class="pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_mandatory" value="1" checked class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                            <span class="text-xs font-semibold text-slate-700">Tandai sebagai berkas wajib (Mandatory)</span>
                        </label>
                    </div>

                    <!-- Modal Actions -->
                    <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                        <button type="button" onclick="closeAddAssetRequirementModal()" class="px-4 py-2 text-xs font-medium text-slate-600 hover:text-slate-800 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" id="btn-save-new-asset" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-xs flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                            <span>Tambahkan ke Daftar</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- IMAGE LIGHTBOX MODAL -->
        <div id="imageLightboxModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4 transition-all" onclick="closeImageLightbox()">
            <div class="relative max-w-4xl max-h-[90vh] bg-slate-900 rounded-2xl overflow-hidden shadow-2xl border border-slate-800 flex flex-col" onclick="event.stopPropagation()">
                <div class="p-3 bg-slate-950 flex items-center justify-between border-b border-slate-800 text-white">
                    <span id="lightboxTitle" class="text-xs font-bold truncate max-w-md">Pratinjau Screenshot</span>
                    <div class="flex items-center gap-3">
                        <a id="lightboxDownloadBtn" href="#" download class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold rounded-lg transition-all">
                            Unduh Asli
                        </a>
                        <button type="button" onclick="closeImageLightbox()" class="text-slate-400 hover:text-white font-bold text-lg cursor-pointer px-1">
                            &times;
                        </button>
                    </div>
                </div>
                <div class="flex-1 overflow-auto p-2 flex items-center justify-center bg-slate-900/50">
                    <img id="lightboxImage" src="" alt="Screenshot" class="max-h-[80vh] max-w-full object-contain rounded-lg">
                </div>
            </div>
        </div>

    </main>

    <!-- Real-time Polling & Interactive Chat Script -->
    <script>
        let lastPortalMsgId = {{ $messages->last()->id ?? 0 }};
        const currentUserId = {{ Auth::id() ?? 'null' }};
        const fetchUrl = "{{ route('client.portal.messages.fetch', $project->share_token) }}";
        const sendUrl = "{{ route('client.portal.messages.send', $project->share_token) }}";
        const resolveUrlTemplate = "{{ route('client.portal.messages.toggle-resolution', [$project->share_token, '__MSG_ID__']) }}";
        const csrfToken = "{{ csrf_token() }}";
        let currentFilter = 'all';

        // Restore Client Name from localStorage if guest
        document.addEventListener('DOMContentLoaded', () => {
            const nameInput = document.getElementById('client_name');
            if (nameInput) {
                const savedName = localStorage.getItem('yoimo_client_name');
                if (savedName) {
                    nameInput.value = savedName;
                }
                nameInput.addEventListener('input', () => {
                    localStorage.setItem('yoimo_client_name', nameInput.value.trim());
                });
            }

            // Auto-scroll messages feed to bottom initially
            scrollStreamToBottom();

            // Set interval for AJAX polling (every 3.5 seconds)
            setInterval(pollNewPortalMessages, 3500);

            // Clipboard Paste Listener for Screenshots (Ctrl+V)
            const msgInput = document.getElementById('portal_message_input');
            if (msgInput) {
                msgInput.addEventListener('paste', handleClipboardPaste);
            }

            // File input change handler
            const fileInput = document.getElementById('portal_file_input');
            if (fileInput) {
                fileInput.addEventListener('change', handleFileSelected);
            }

            // AJAX Form Submission
            const form = document.getElementById('portal-chat-form');
            if (form) {
                form.addEventListener('submit', handleFormSubmit);
            }
        });

        // 1. Handle Clipboard Paste for Screenshots
        function handleClipboardPaste(e) {
            const items = (e.clipboardData || e.originalEvent.clipboardData).items;
            for (let i = 0; i < items.length; i++) {
                if (items[i].type.indexOf('image') !== -1) {
                    const blob = items[i].getAsFile();
                    const timestamp = new Date().toISOString().replace(/[:.-]/g, '');
                    const file = new File([blob], `screenshot_${timestamp}.png`, { type: blob.type });

                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    document.getElementById('portal_file_input').files = dataTransfer.files;

                    renderFilePreview(file);
                    break;
                }
            }
        }

        // 2. Handle File Input Change
        function handleFileSelected(e) {
            if (e.target.files && e.target.files[0]) {
                renderFilePreview(e.target.files[0]);
            }
        }

        function renderFilePreview(file) {
            const previewBox = document.getElementById('portal-file-preview');
            const thumbContainer = document.getElementById('portal-file-thumb-container');
            const nameSpan = document.getElementById('portal-file-name');
            const sizeSpan = document.getElementById('portal-file-size');

            if (!previewBox || !file) return;

            nameSpan.textContent = file.name;
            sizeSpan.textContent = (file.size / 1024 > 1024) 
                ? (file.size / (1024 * 1024)).toFixed(2) + ' MB'
                : (file.size / 1024).toFixed(1) + ' KB';

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    thumbContainer.innerHTML = `<img src="${e.target.result}" class="w-9 h-9 rounded-lg object-cover border border-emerald-300">`;
                };
                reader.readAsDataURL(file);
            } else {
                thumbContainer.innerHTML = `
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xs border border-emerald-200">
                        DOC
                    </div>
                `;
            }

            previewBox.classList.remove('hidden');
        }

        function clearPortalFile() {
            const fileInput = document.getElementById('portal_file_input');
            const previewBox = document.getElementById('portal-file-preview');
            if (fileInput) fileInput.value = '';
            if (previewBox) previewBox.classList.add('hidden');
        }

        // Add Enter key support for textarea
        document.addEventListener('DOMContentLoaded', () => {
            const msgInput = document.getElementById('portal_message_input');
            const form = document.getElementById('portal-chat-form');
            if (msgInput && form) {
                msgInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        if (typeof form.requestSubmit === 'function') {
                            form.requestSubmit();
                        } else {
                            form.dispatchEvent(new Event('submit', { cancelable: true }));
                        }
                    }
                });
            }
        });

        // 3. Handle Form Submit via AJAX
        function handleFormSubmit(e) {
            e.preventDefault();

            const form = e.target;
            const messageInput = document.getElementById('portal_message_input');
            const fileInput = document.getElementById('portal_file_input');
            const nameInput = document.getElementById('client_name');
            const sendBtn = document.getElementById('portal-send-btn');
            const sendBtnText = document.getElementById('portal-send-btn-text');

            if (!messageInput.value.trim() && (!fileInput.files || fileInput.files.length === 0)) {
                alert("Silakan masukkan teks pesan atau lampirkan file.");
                return;
            }

            if (nameInput && !nameInput.value.trim()) {
                alert("Harap isi nama Anda terlebih dahulu.");
                nameInput.focus();
                return;
            }

            sendBtn.disabled = true;
            if (sendBtnText) sendBtnText.textContent = "Mengirim...";

            const formData = new FormData(form);

            fetch(sendUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                sendBtn.disabled = false;
                if (sendBtnText) sendBtnText.textContent = "Kirim ke Chat Proyek";

                if (data.success && data.message) {
                    messageInput.value = '';
                    messageInput.style.height = '';
                    clearPortalFile();

                    appendPortalMessage(data.message);
                    if (data.message.id > lastPortalMsgId) {
                        lastPortalMsgId = data.message.id;
                    }
                    scrollStreamToBottom();
                    updateCounts();
                } else {
                    alert("Gagal mengirim: " + (data.error || "Terjadi kesalahan server."));
                }
            })
            .catch(err => {
                console.error("Error sending message:", err);
                sendBtn.disabled = false;
                if (sendBtnText) sendBtnText.textContent = "Kirim ke Chat Proyek";
                alert("Gagal menghubungi server. Periksa koneksi Anda.");
            });
        }

        // 4. AJAX Polling for New and Updated Messages
        function pollNewPortalMessages() {
            fetch(`${fetchUrl}?last_id=${lastPortalMsgId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.messages && data.messages.length > 0) {
                        let hasNew = false;
                        data.messages.forEach(msg => {
                            const existing = document.querySelector(`.portal-message-item[data-id="${msg.id}"]`);
                            if (existing) {
                                // Update resolution status if it changed remotely
                                const wasResolved = existing.getAttribute('data-resolved') === 'true';
                                if (msg.message_type === 'kendala' && wasResolved !== msg.is_resolved) {
                                    existing.setAttribute('data-resolved', msg.is_resolved ? 'true' : 'false');
                                    const isMe = existing.classList.contains('flex-row-reverse');
                                    const card = document.getElementById(`portal-resolution-card-${msg.id}`);
                                    if (card) {
                                        const temp = document.createElement('div');
                                        temp.innerHTML = buildPortalResolutionCardHtml(msg, isMe);
                                        card.replaceWith(temp.firstElementChild);
                                    }
                                    updateCounts();
                                }
                                return;
                            }

                            appendPortalMessage(msg);
                            if (msg.id > lastPortalMsgId) {
                                lastPortalMsgId = msg.id;
                            }
                            hasNew = true;
                        });

                        if (hasNew) {
                            scrollStreamToBottom();
                            updateCounts();
                            if (currentFilter !== 'all') {
                                filterPortalMessages(currentFilter);
                            }
                        }
                    }
                })
                .catch(err => console.error("Error polling portal messages:", err));
        }

        // 5. Build Resolution Card HTML for Kendala
        function buildPortalResolutionCardHtml(msg, isMe) {
            if (msg.message_type !== 'kendala') return '';

            if (!msg.is_resolved) {
                return `
                    <div id="portal-resolution-card-${msg.id}" class="mt-2 pt-2 border-t ${isMe ? 'border-emerald-200/50' : 'border-slate-100'}">
                        <div class="rounded-xl p-3 border bg-amber-50/70 border-amber-200/70 text-slate-800 space-y-2 text-left">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-1.5 text-xs font-semibold text-amber-900">
                                    <svg class="w-4 h-4 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Status: Menunggu Penanganan</span>
                                </div>
                                <button type="button" onclick="togglePortalResolutionPrompt(${msg.id})" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition-all flex items-center gap-1 shadow-2xs cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    <span>Tandai Selesai</span>
                                </button>
                            </div>
                            
                            <!-- Inline Note Prompt Form -->
                            <div id="portal-resolve-box-${msg.id}" class="hidden pt-2 border-t border-amber-200/60 space-y-2">
                                <label class="block text-xs font-medium text-slate-700">Catatan Solusi / Penanganan:</label>
                                <textarea id="portal-resolve-note-${msg.id}" rows="2" placeholder="Tuliskan catatan bagaimana kendala ini diselesaikan..." class="w-full p-2.5 text-xs bg-white text-slate-800 border border-slate-200 rounded-xl font-normal focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" onclick="togglePortalResolutionPrompt(${msg.id})" class="px-2.5 py-1 text-xs font-medium text-slate-600 hover:text-slate-800 cursor-pointer">Batal</button>
                                    <button type="button" onclick="submitPortalResolution(${msg.id}, true)" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold flex items-center gap-1 shadow-2xs cursor-pointer">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        <span>Simpan Status</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                return `
                    <div id="portal-resolution-card-${msg.id}" class="mt-2 pt-2 border-t ${isMe ? 'border-emerald-200/50' : 'border-slate-100'}">
                        <div class="rounded-xl p-3 border bg-emerald-50/70 border-emerald-200/70 text-slate-800 space-y-2 text-left">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-1.5 text-xs font-semibold text-emerald-800">
                                    <svg class="w-4 h-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Kendala Telah Diselesaikan</span>
                                </div>
                                <button type="button" onclick="submitPortalResolution(${msg.id}, false)" class="px-2 py-0.5 text-xs font-medium text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded transition-all cursor-pointer" title="Buka kembali kendala jika masih butuh penanganan">
                                    Buka Kembali
                                </button>
                            </div>
                            <div class="text-xs bg-white/90 text-slate-700 p-2.5 rounded-lg space-y-1 border border-emerald-100">
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 text-[11px]">
                                    <span class="font-medium text-slate-500">Diselesaikan oleh:</span>
                                    <span class="font-semibold text-slate-800">${msg.resolver_name || 'Tim Proyek'}</span>
                                    ${msg.resolved_at ? `<span class="text-slate-400 font-normal">(${msg.resolved_at})</span>` : ''}
                                </div>
                                ${msg.resolution_note ? `
                                    <div class="pt-1.5 border-t border-slate-100 mt-1">
                                        <span class="font-medium text-[10px] text-slate-500 block">Catatan Solusi:</span>
                                        <p class="text-slate-700 font-normal italic leading-relaxed whitespace-pre-line">${msg.resolution_note}</p>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }
        }

        function togglePortalResolutionPrompt(id) {
            const box = document.getElementById(`portal-resolve-box-${id}`);
            if (box) {
                box.classList.toggle('hidden');
                if (!box.classList.contains('hidden')) {
                    const textarea = document.getElementById(`portal-resolve-note-${id}`);
                    if (textarea) textarea.focus();
                }
            }
        }

        function submitPortalResolution(id, isResolved) {
            const url = resolveUrlTemplate.replace('__MSG_ID__', id);
            const noteEl = document.getElementById(`portal-resolve-note-${id}`);
            const note = noteEl ? noteEl.value.trim() : '';

            let resolverName = '';
            const nameInput = document.getElementById('client_name');
            if (nameInput && nameInput.value.trim()) {
                resolverName = nameInput.value.trim();
            } else {
                resolverName = localStorage.getItem('yoimo_client_name') || 'Klien';
            }

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    is_resolved: isResolved,
                    resolution_note: note,
                    resolver_name: resolverName
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    const item = document.querySelector(`.portal-message-item[data-id="${id}"]`);
                    if (item) {
                        item.setAttribute('data-resolved', isResolved ? 'true' : 'false');
                        const isMe = item.classList.contains('flex-row-reverse');
                        const cardContainer = document.getElementById(`portal-resolution-card-${id}`);
                        if (cardContainer) {
                            const temp = document.createElement('div');
                            temp.innerHTML = buildPortalResolutionCardHtml({
                                id: id,
                                message_type: 'kendala',
                                is_resolved: isResolved,
                                resolver_name: data.data.resolver_name,
                                resolved_at: data.data.resolved_at,
                                resolution_note: data.data.resolution_note
                            }, isMe);
                            cardContainer.replaceWith(temp.firstElementChild);
                        }

                        // Also update category badge in header
                        const badgeContainer = item.querySelector('.message-type-header-badge');
                        if (badgeContainer) {
                            badgeContainer.innerHTML = isResolved ? `
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    Terselesaikan
                                </span>
                            ` : `
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-semibold bg-amber-100 text-amber-900 border border-amber-200">
                                    Menunggu Solusi
                                </span>
                            `;
                        }
                    }
                    updateCounts();
                    if (currentFilter !== 'all') {
                        filterPortalMessages(currentFilter);
                    }
                } else {
                    alert('Gagal memperbarui status: ' + (data.error || 'Terjadi kesalahan'));
                }
            })
            .catch(err => {
                console.error('Error toggling resolution:', err);
                alert('Gagal menghubungi server.');
            });
        }

        // 6. Append message bubble to stream DOM
        function appendPortalMessage(msg) {
            const stream = document.getElementById('portal-messages-stream');
            const emptyState = document.getElementById('portal-empty-chat-state');
            if (emptyState) emptyState.remove();

            const isMe = currentUserId && (msg.sender_id === currentUserId);
            const isTeam = !!msg.sender_id;

            let badgeHtml = '';
            if (msg.message_type === 'kendala') {
                badgeHtml = `
                    <div class="flex items-center justify-between gap-2 pt-0.5">
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-900 border border-amber-200/80">
                            <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <span>Hambatan / Kendala</span>
                        </div>
                        <div class="message-type-header-badge">
                            ${msg.is_resolved ? `
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    Terselesaikan
                                </span>
                            ` : `
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-semibold bg-amber-100 text-amber-900 border border-amber-200">
                                    Menunggu Solusi
                                </span>
                            `}
                        </div>
                    </div>
                `;
            } else if (msg.message_type === 'question') {
                badgeHtml = `
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-sky-50 text-sky-800 border border-sky-200/80 pt-0.5">
                        <svg class="w-3 h-3 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Pertanyaan / Q&A</span>
                    </div>
                `;
            }

            let attachmentHtml = '';
            if (msg.attachment_url) {
                if (msg.is_image) {
                    attachmentHtml = `
                        <div class="rounded-xl overflow-hidden border border-slate-200/80 mt-1 bg-white/60">
                            <div class="relative group cursor-pointer" onclick="openImageLightbox('${msg.attachment_url}', '${msg.attachment_name || 'Screenshot'}')">
                                <img src="${msg.attachment_url}" alt="${msg.attachment_name}" 
                                     class="max-h-56 w-full object-cover rounded-xl transition-transform duration-200 group-hover:scale-[1.01]">
                                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-medium gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                                    <span>Klik Perbesar</span>
                                </div>
                            </div>
                            <div class="p-2 flex items-center justify-between text-[11px] bg-slate-50 text-slate-700">
                                <span class="truncate max-w-[170px] font-medium">${msg.attachment_name || 'Screenshot'}</span>
                                <a href="${msg.attachment_url}" download class="font-semibold text-emerald-700 hover:underline">Unduh</a>
                            </div>
                        </div>
                    `;
                } else {
                    attachmentHtml = `
                        <div class="p-2.5 rounded-xl border border-slate-200/80 bg-white flex items-center gap-2.5 mt-1 shadow-2xs">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div class="flex-1 min-w-0 text-left">
                                <span class="block text-xs font-semibold text-slate-800 truncate">${msg.attachment_name || 'Dokumen Proyek'}</span>
                                <span class="block text-[10px] text-slate-400">Berkas Lampiran</span>
                            </div>
                            <a href="${msg.attachment_url}" target="_blank" download
                               class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold shrink-0 transition-colors">
                                Unduh
                            </a>
                        </div>
                    `;
                }
            }

            const avatarHtml = msg.sender_avatar 
                ? `<img src="${msg.sender_avatar}" alt="Avatar" class="w-8 h-8 rounded-full object-cover border border-slate-200 shadow-2xs">`
                : `<div class="w-8 h-8 rounded-full ${isTeam ? 'bg-slate-800' : 'bg-emerald-600'} text-white font-bold text-[10px] flex items-center justify-center uppercase shadow-2xs">${(msg.sender_name || 'KL').substring(0, 2)}</div>`;

            const resolutionBoxHtml = buildPortalResolutionCardHtml(msg, isMe);

            const itemHtml = `
                <div class="portal-message-item flex items-end gap-2 sm:gap-2.5 ${isMe ? 'flex-row-reverse' : 'flex-row'}" 
                     data-id="${msg.id}" 
                     data-type="${msg.message_type || 'chat'}"
                     data-resolved="${msg.is_resolved ? 'true' : 'false'}"
                     data-has-attachment="${msg.attachment_url ? 'true' : 'false'}">
                    
                    <div class="shrink-0 mb-1">
                        ${avatarHtml}
                    </div>

                    <div class="max-w-[85%] sm:max-w-[75%] space-y-1 ${isMe ? 'items-end' : 'items-start'}">
                        <div class="p-3 sm:p-3.5 rounded-2xl shadow-2xs space-y-1.5 transition-all ${isMe ? 'bg-[#d9fdd3] text-slate-800 rounded-br-xs border border-emerald-200/50' : 'bg-white text-slate-800 rounded-bl-xs border border-slate-200/70'}">
                            <div class="flex items-center justify-between gap-2 border-b ${isMe ? 'border-emerald-200/40' : 'border-slate-100'} pb-1.5">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs font-bold ${isMe ? 'text-emerald-900' : 'text-slate-800'}">${msg.sender_name}</span>
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-semibold ${isTeam ? 'bg-slate-100 text-slate-700' : 'bg-emerald-100 text-emerald-800'}">
                                        ${msg.sender_role || (isTeam ? 'Tim Proyek' : 'Klien')}
                                    </span>
                                </div>
                                <span class="text-[10px] text-slate-400 font-normal">${msg.created_at}</span>
                            </div>

                            ${badgeHtml}
                            ${msg.message ? `<p class="text-xs sm:text-[13px] font-normal leading-relaxed whitespace-pre-line break-words text-slate-800 py-0.5">${msg.message}</p>` : ''}
                            ${attachmentHtml}
                            ${resolutionBoxHtml}

                            <div class="flex items-center justify-end gap-1 pt-0.5 text-[10px] ${isMe ? 'text-emerald-800/80' : 'text-slate-400'}">
                                <span>${msg.created_at}</span>
                                ${isMe ? `
                                    <svg class="w-3.5 h-3.5 text-emerald-600" viewBox="0 0 16 16" fill="currentColor">
                                        <path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0l7-7zm-4.5 4.5l.708-.708-3.5-3.5a.5.5 0 0 0-.708.708l3.5 3.5z"/>
                                    </svg>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;

            stream.insertAdjacentHTML('beforeend', itemHtml);
        }

        // 7. Scroll Stream to Bottom
        function scrollStreamToBottom() {
            const stream = document.getElementById('portal-messages-stream');
            if (stream) {
                stream.scrollTop = stream.scrollHeight;
            }
        }

        // 8. Filter Tabs (Semua, Hambatan Aktif, Hambatan Selesai, Pertanyaan)
        function filterPortalMessages(type) {
            currentFilter = type;
            const items = document.querySelectorAll('.portal-message-item');
            items.forEach(item => {
                const itemType = item.getAttribute('data-type');
                const isResolved = item.getAttribute('data-resolved') === 'true';

                if (type === 'all') {
                    item.classList.remove('hidden');
                } else if (type === 'kendala-pending') {
                    item.classList.toggle('hidden', !(itemType === 'kendala' && !isResolved));
                } else if (type === 'kendala-resolved') {
                    item.classList.toggle('hidden', !(itemType === 'kendala' && isResolved));
                } else if (type === 'question') {
                    item.classList.toggle('hidden', itemType !== 'question');
                }
            });

            // Update Tab Active Styling
            ['all', 'kendala-pending', 'kendala-resolved', 'question'].forEach(t => {
                const btn = document.getElementById(`btn-filter-${t}`);
                if (btn) {
                    if (t === type) {
                        btn.className = "portal-filter-active px-3 py-1.5 rounded-full text-xs font-medium transition-all cursor-pointer";
                    } else {
                        btn.className = "px-3 py-1.5 rounded-full text-xs font-medium transition-all cursor-pointer bg-slate-100 hover:bg-slate-200 text-slate-700";
                    }
                }
            });
        }

        function updateCounts() {
            const items = document.querySelectorAll('.portal-message-item');
            let kendalaPending = 0;
            let kendalaResolved = 0;
            let questionCount = 0;

            items.forEach(item => {
                const type = item.getAttribute('data-type');
                const isResolved = item.getAttribute('data-resolved') === 'true';

                if (type === 'kendala') {
                    if (isResolved) {
                        kendalaResolved++;
                    } else {
                        kendalaPending++;
                    }
                }
                if (type === 'question') {
                    questionCount++;
                }
            });

            const countAll = document.getElementById('count-all');
            const countKendalaPending = document.getElementById('count-kendala-pending');
            const countKendalaResolved = document.getElementById('count-kendala-resolved');
            const countQuestion = document.getElementById('count-question');

            if (countAll) countAll.textContent = items.length;
            if (countKendalaPending) countKendalaPending.textContent = kendalaPending;
            if (countKendalaResolved) countKendalaResolved.textContent = kendalaResolved;
            if (countQuestion) countQuestion.textContent = questionCount;
        }

        // 9. Image Lightbox Functions
        function openImageLightbox(src, title) {
            const modal = document.getElementById('imageLightboxModal');
            const img = document.getElementById('lightboxImage');
            const titleSpan = document.getElementById('lightboxTitle');
            const downloadBtn = document.getElementById('lightboxDownloadBtn');

            if (!modal || !img) return;

            img.src = src;
            if (titleSpan) titleSpan.textContent = title || 'Pratinjau Screenshot';
            if (downloadBtn) {
                downloadBtn.href = src;
                downloadBtn.download = title || 'screenshot.png';
            }

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeImageLightbox() {
            const modal = document.getElementById('imageLightboxModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeImageLightbox();
                closeSubmitAssetModal();
                closeAddAssetRequirementModal();
            }
        });

        // Asset Intake Hub Variables & URLs
        const assetSubmitUrlTemplate = "{{ route('client.portal.assets.submit', [$project->share_token, '__ASSET_ID__']) }}";
        const assetStoreUrl = "{{ route('client.portal.assets.store', $project->share_token) }}";
        const assetDeleteUrlTemplate = "{{ route('client.portal.assets.delete', [$project->share_token, '__ASSET_ID__']) }}";
        const assetToggleApprovalUrlTemplate = "{{ route('client.portal.assets.toggle-approval', [$project->share_token, '__ASSET_ID__']) }}";
        let currentAssetFilter = 'all';

        // 10. Switch Portal View (Chat vs Assets)
        function switchPortalView(viewName) {
            const chatView = document.getElementById('portal-view-chat');
            const assetsView = document.getElementById('portal-view-assets');
            const chatTabBtn = document.getElementById('tab-btn-chat');
            const assetsTabBtn = document.getElementById('tab-btn-assets');

            if (viewName === 'assets') {
                if (chatView) chatView.classList.add('hidden');
                if (assetsView) assetsView.classList.remove('hidden');

                if (chatTabBtn) {
                    chatTabBtn.className = "px-4 py-2 text-xs sm:text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-all flex items-center gap-2 cursor-pointer";
                }
                if (assetsTabBtn) {
                    assetsTabBtn.className = "px-4 py-2 text-xs sm:text-sm font-bold border-b-2 border-[#00a884] text-[#00a884] transition-all flex items-center gap-2 cursor-pointer";
                }
                window.location.hash = 'assets';
            } else {
                if (assetsView) assetsView.classList.add('hidden');
                if (chatView) chatView.classList.remove('hidden');

                if (assetsTabBtn) {
                    assetsTabBtn.className = "px-4 py-2 text-xs sm:text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-all flex items-center gap-2 cursor-pointer";
                }
                if (chatTabBtn) {
                    chatTabBtn.className = "px-4 py-2 text-xs sm:text-sm font-bold border-b-2 border-[#00a884] text-[#00a884] transition-all flex items-center gap-2 cursor-pointer";
                }
                window.location.hash = 'chat';
            }
        }

        // Check URL hash on page load
        if (window.location.hash === '#assets') {
            switchPortalView('assets');
        }

        // 11. Filter Asset Requirements (All, Pending, Submitted, Mandatory)
        function filterAssetRequirements(filterType) {
            currentAssetFilter = filterType;
            const cards = document.querySelectorAll('.portal-asset-card');

            cards.forEach(card => {
                const status = card.getAttribute('data-status');
                const isMandatory = card.getAttribute('data-mandatory') === 'true';

                if (filterType === 'all') {
                    card.classList.remove('hidden');
                } else if (filterType === 'pending') {
                    card.classList.toggle('hidden', status !== 'pending');
                } else if (filterType === 'submitted') {
                    card.classList.toggle('hidden', status === 'pending');
                } else if (filterType === 'mandatory') {
                    card.classList.toggle('hidden', !isMandatory);
                }
            });

            // Update filter chip styling
            ['all', 'pending', 'submitted', 'mandatory'].forEach(t => {
                const btn = document.getElementById(`asset-filter-btn-${t}`);
                if (btn) {
                    if (t === filterType) {
                        btn.className = "px-3 py-1.5 rounded-full text-xs font-medium transition-all cursor-pointer bg-slate-800 text-white shadow-2xs";
                    } else {
                        btn.className = "px-3 py-1.5 rounded-full text-xs font-medium transition-all cursor-pointer bg-slate-100 hover:bg-slate-200 text-slate-700";
                    }
                }
            });
        }

        // 12. Modal Submit Asset Functions
        function openSubmitAssetModal(id, title, desc, externalUrl = '', clientNotes = '') {
            document.getElementById('submit-asset-id').value = id;
            document.getElementById('submit-modal-title').textContent = title;
            document.getElementById('submit-modal-desc').textContent = desc || 'Silakan lampirkan file, tautan Google Drive / Cloud, atau catatan akses.';
            document.getElementById('submit-external-url').value = externalUrl || '';
            document.getElementById('submit-client-notes').value = clientNotes || '';
            document.getElementById('submit-file-input').value = '';
            document.getElementById('submit-file-selected-name').textContent = 'Belum ada berkas dipilih';

            const modal = document.getElementById('modal-submit-asset');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeSubmitAssetModal() {
            const modal = document.getElementById('modal-submit-asset');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        function handleSubmitModalFileChange(input) {
            const nameEl = document.getElementById('submit-file-selected-name');
            if (input.files && input.files[0]) {
                const f = input.files[0];
                const sizeStr = (f.size / 1024 > 1024) 
                    ? (f.size / (1024 * 1024)).toFixed(2) + ' MB'
                    : (f.size / 1024).toFixed(1) + ' KB';
                nameEl.innerHTML = `<strong class="text-emerald-700">✓ ${f.name}</strong> (${sizeStr})`;
            } else {
                nameEl.textContent = 'Belum ada berkas dipilih';
            }
        }

        function submitAssetForm(e) {
            e.preventDefault();
            const form = e.target;
            const assetId = document.getElementById('submit-asset-id').value;
            const fileInput = document.getElementById('submit-file-input');
            const urlInput = document.getElementById('submit-external-url');
            const notesInput = document.getElementById('submit-client-notes');
            const nameInput = document.getElementById('submit-client-name');
            const saveBtn = document.getElementById('btn-submit-asset-save');
            const saveBtnText = document.getElementById('btn-submit-asset-text');

            if ((!fileInput.files || fileInput.files.length === 0) && !urlInput.value.trim() && !notesInput.value.trim()) {
                alert("Harap unggah file, masukkan link, atau isi catatan berkas.");
                return;
            }

            if (nameInput && !nameInput.value.trim()) {
                alert("Harap isi nama Anda terlebih dahulu.");
                nameInput.focus();
                return;
            }

            saveBtn.disabled = true;
            if (saveBtnText) saveBtnText.textContent = "Mengunggah...";

            const url = assetSubmitUrlTemplate.replace('__ASSET_ID__', assetId);
            const formData = new FormData(form);

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                saveBtn.disabled = false;
                if (saveBtnText) saveBtnText.textContent = "Kirimkan Berkas";

                if (data.success && data.asset) {
                    closeSubmitAssetModal();
                    updateAssetCardDOM(data.asset);
                    if (data.metrics) {
                        updateAssetMetricsDOM(data.metrics);
                    }
                    alert("Aset berhasil dikirimkan ke tim proyek!");
                    location.reload(); // Reload to refresh chat and attachments display cleanly
                } else {
                    alert("Gagal mengirimkan berkas: " + (data.error || "Terjadi kesalahan."));
                }
            })
            .catch(err => {
                console.error("Error submitting asset:", err);
                saveBtn.disabled = false;
                if (saveBtnText) saveBtnText.textContent = "Kirimkan Berkas";
                alert("Gagal menghubungi server. Periksa koneksi Anda.");
            });
        }

        // 13. Add Asset Requirement Modal Functions
        function openAddAssetRequirementModal() {
            document.getElementById('form-add-asset-requirement').reset();
            const modal = document.getElementById('modal-add-asset-requirement');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeAddAssetRequirementModal() {
            const modal = document.getElementById('modal-add-asset-requirement');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        function selectNewAssetCategory(cat) {
            document.getElementById('new-asset-category').value = cat;
        }

        function saveNewAssetRequirement(e) {
            e.preventDefault();
            const form = e.target;
            const titleInput = document.getElementById('new-asset-title');
            const saveBtn = document.getElementById('btn-save-new-asset');

            if (!titleInput.value.trim()) {
                alert("Harap isi nama kebutuhan aset.");
                titleInput.focus();
                return;
            }

            saveBtn.disabled = true;
            const formData = new FormData(form);

            fetch(assetStoreUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                saveBtn.disabled = false;
                if (data.success) {
                    closeAddAssetRequirementModal();
                    location.reload();
                } else {
                    alert("Gagal menyimpan: " + (data.error || "Terjadi kesalahan."));
                }
            })
            .catch(err => {
                console.error("Error saving requirement:", err);
                saveBtn.disabled = false;
                alert("Gagal menghubungi server.");
            });
        }

        // 14. Toggle Asset Approval
        function toggleAssetApproval(assetId) {
            const url = assetToggleApprovalUrlTemplate.replace('__ASSET_ID__', assetId);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.asset) {
                    updateAssetCardDOM(data.asset);
                } else {
                    alert("Gagal memperbarui status: " + (data.error || "Terjadi kesalahan."));
                }
            })
            .catch(err => {
                console.error("Error toggling approval:", err);
                alert("Gagal menghubungi server.");
            });
        }

        // 15. Delete Asset Requirement
        function deleteAssetRequirement(assetId, assetTitle) {
            if (!confirm(`Apakah Anda yakin ingin menghapus kebutuhan aset "${assetTitle}" dari proyek ini?`)) {
                return;
            }

            const url = assetDeleteUrlTemplate.replace('__ASSET_ID__', assetId);

            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const card = document.getElementById(`asset-card-${assetId}`);
                    if (card) {
                        card.remove();
                    }
                    if (data.metrics) {
                        updateAssetMetricsDOM(data.metrics);
                    }
                } else {
                    alert("Gagal menghapus: " + (data.error || "Terjadi kesalahan."));
                }
            })
            .catch(err => {
                console.error("Error deleting asset:", err);
                alert("Gagal menghubungi server.");
            });
        }

        // 16. Helper DOM Updates
        function updateAssetCardDOM(asset) {
            const card = document.getElementById(`asset-card-${asset.id}`);
            if (!card) return;

            card.setAttribute('data-status', asset.status);

            const statusPill = document.getElementById(`asset-status-pill-${asset.id}`);
            if (statusPill) {
                if (asset.status === 'approved') {
                    statusPill.innerHTML = `
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            Disetujui Tim
                        </span>
                    `;
                } else if (asset.status === 'submitted') {
                    statusPill.innerHTML = `
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-sky-50 text-sky-800 border border-sky-200">
                            <svg class="w-3 h-3 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Telah Dikirim
                        </span>
                    `;
                } else {
                    statusPill.innerHTML = `
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-900 border border-amber-200">
                            <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Menunggu Input
                        </span>
                    `;
                }
            }

            const btnLabel = document.getElementById(`asset-btn-label-${asset.id}`);
            if (btnLabel) {
                btnLabel.textContent = (asset.status === 'pending') ? 'Unggah Berkas / Tautan' : 'Perbarui Berkas';
            }

            const approveBtn = document.getElementById(`asset-approve-btn-${asset.id}`);
            if (approveBtn) {
                if (asset.status === 'approved') {
                    approveBtn.className = "px-2.5 py-1.5 border rounded-xl text-xs font-semibold transition-all flex items-center gap-1 cursor-pointer bg-amber-50 text-amber-800 border-amber-200 hover:bg-amber-100";
                    approveBtn.innerHTML = `
                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span class="hidden sm:inline">Batal Setuju</span>
                    `;
                } else {
                    approveBtn.className = "px-2.5 py-1.5 border rounded-xl text-xs font-semibold transition-all flex items-center gap-1 cursor-pointer bg-emerald-50 text-emerald-800 border-emerald-200 hover:bg-emerald-100";
                    approveBtn.innerHTML = `
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <span class="hidden sm:inline">Setujui</span>
                    `;
                }
            }
        }

        function updateAssetMetricsDOM(metrics) {
            if (!metrics) return;

            const fill = document.getElementById('asset-progress-bar-fill');
            const pctLabel = document.getElementById('asset-progress-pct-label');
            const statSub = document.getElementById('stat-submitted-count');
            const statTot = document.getElementById('stat-total-count');
            const countAll = document.getElementById('asset-count-all');
            const countPending = document.getElementById('asset-count-pending');
            const countSubmitted = document.getElementById('asset-count-submitted');
            const bannerSub = document.getElementById('banner-submitted-count');
            const bannerTot = document.getElementById('banner-total-count');
            const bannerProg = document.getElementById('banner-progress');
            const tabSub = document.getElementById('tab-assets-submitted-count');
            const tabTot = document.getElementById('tab-assets-total-count');

            if (fill) fill.style.width = metrics.progress + '%';
            if (pctLabel) pctLabel.textContent = metrics.progress + '%';
            if (statSub) statSub.textContent = metrics.submitted;
            if (statTot) statTot.textContent = metrics.total;
            if (countAll) countAll.textContent = metrics.total;
            if (countPending) countPending.textContent = metrics.total - metrics.submitted;
            if (countSubmitted) countSubmitted.textContent = metrics.submitted;
            if (bannerSub) bannerSub.textContent = metrics.submitted;
            if (bannerTot) bannerTot.textContent = metrics.total;
            if (bannerProg) bannerProg.textContent = metrics.progress + '%';
            if (tabSub) tabSub.textContent = metrics.submitted;
            if (tabTot) tabTot.textContent = metrics.total;
        }
    </script>
</body>
</html>

