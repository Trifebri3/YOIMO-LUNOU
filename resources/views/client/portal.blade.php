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
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <!-- TailwindCSS CSS (via CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        slate: {
                            850: '#1e293b',
                        },
                        indigo: {
                            650: '#4f46e5',
                            755: '#4338ca',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #f8fafc;
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

        </div>        <!-- 4. Real-time Project Chat & Discussion Section (Synchronized with Chat Center) -->
        <section id="project-chat-section" class="bg-white border border-slate-200/60 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
            
            <!-- Section Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
                <div class="flex items-center gap-3.5">
                    <div class="p-3 bg-indigo-50 border border-indigo-100 rounded-2xl shrink-0">
                        <svg class="w-6 h-6 text-indigo-650" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Ruang Diskusi & Chat Proyek</h3>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Sinkron Live
                            </span>
                        </div>
                        <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Komunikasi dua arah real-time antara tim pengembang & pemangku kepentingan proyek</p>
                    </div>
                </div>

                <!-- Auth / Guest Status Indicator -->
                <div class="flex items-center gap-2.5">
                    @auth
                        <div class="flex items-center gap-2.5 bg-slate-50 border border-slate-200/80 px-3.5 py-1.5 rounded-2xl">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-7 h-7 rounded-xl object-cover border border-slate-200">
                            @else
                                <div class="w-7 h-7 rounded-xl bg-slate-900 text-white font-black text-[10px] flex items-center justify-center uppercase">
                                    {{ substr(Auth::user()->name, 0, 2) }}
                                </div>
                            @endif
                            <div class="text-left">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Akun Terhubung:</span>
                                <span class="text-xs font-black text-slate-800 leading-tight block">{{ Auth::user()->name }} <span class="text-indigo-650 font-bold">({{ ucfirst(Auth::user()->role) }})</span></span>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-xl text-[10px] font-bold">
                                Mode Klien Publik
                            </span>
                            <a href="{{ route('login', ['redirect' => request()->fullUrl()]) }}" class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-xl text-[10px] font-black transition-all flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                <span>Punya Akun Tim? Masuk</span>
                            </a>
                        </div>
                    @endauth
                </div>
            </div>

            <!-- Chat Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- Left Column: Composer Form (Width: 5 cols) -->
                <div class="lg:col-span-5 space-y-4">
                    <div class="bg-slate-50/80 border border-slate-200/60 rounded-2xl p-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider">Kirim Pesan / Kendala</h4>
                            <span class="text-[9px] text-slate-400 font-semibold">Tersinkron ke WhatsApp Chat Center</span>
                        </div>

                        <form id="portal-chat-form" method="POST" action="{{ route('client.portal.messages.send', $project->share_token) }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            
                            <!-- 1. Category Tag Selector -->
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider block flex items-center justify-between">
                                    <span>Tercatat Sebagai Kategori</span>
                                    <span class="text-[9px] font-normal text-slate-400">Pilih tipe catatan</span>
                                </label>
                                <div class="grid grid-cols-3 gap-2">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="message_type" value="chat" checked class="peer sr-only">
                                        <div class="py-2 px-2 text-center text-[10px] font-black rounded-xl border border-slate-200 bg-white text-slate-600 peer-checked:bg-indigo-650 peer-checked:text-white peer-checked:border-indigo-650 transition-all flex items-center justify-center gap-1 shadow-2xs">
                                            <span>Diskusi</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="message_type" value="kendala" class="peer sr-only">
                                        <div class="py-2 px-2 text-center text-[10px] font-black rounded-xl border border-slate-200 bg-white text-slate-600 peer-checked:bg-rose-600 peer-checked:text-white peer-checked:border-rose-600 transition-all flex items-center justify-center gap-1 shadow-2xs">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            <span>Hambatan</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="message_type" value="question" class="peer sr-only">
                                        <div class="py-2 px-2 text-center text-[10px] font-black rounded-xl border border-slate-200 bg-white text-slate-600 peer-checked:bg-sky-600 peer-checked:text-white peer-checked:border-sky-600 transition-all flex items-center justify-center gap-1 shadow-2xs">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>Pertanyaan</span>
                                        </div>
                                    </label>
                                </div>
                                <p class="text-[9px] text-slate-400 italic">Pesan berkategori Hambatan dilengkapi ceklis penyelesaian dan catatan solusi.</p>
                            </div>

                            <!-- 2. Sender Name (For Guests) -->
                            @guest
                                <div class="space-y-1.5">
                                    <label for="client_name" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nama Anda</label>
                                    <input type="text" name="client_name" id="client_name" required placeholder="Contoh: Budi Santoso / Klien"
                                           class="w-full px-4 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-semibold text-slate-700">
                                </div>
                            @endguest

                            <!-- 3. Message Textarea -->
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <label for="portal_message_input" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Isi Pesan / Catatan Kendala</label>
                                    <span class="text-[9px] text-slate-400 font-semibold">Ctrl+V untuk paste screenshot</span>
                                </div>
                                <textarea name="message" id="portal_message_input" rows="4" placeholder="Tuliskan kendala, pertanyaan, atau catatan untuk tim proyek... (Bisa langsung paste screenshot layar di sini)"
                                          class="w-full px-4 py-3 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-semibold text-slate-700 leading-relaxed"></textarea>
                            </div>

                            <!-- 4. Attachment Picker & Preview Box -->
                            <div class="space-y-2">
                                <input type="file" name="file" id="portal_file_input" class="hidden" 
                                       accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.txt">
                                
                                <div class="flex items-center justify-between gap-2">
                                    <button type="button" onclick="document.getElementById('portal_file_input').click()" 
                                            class="px-3.5 py-2 bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                        <span>Lampirkan File / Screenshot</span>
                                    </button>

                                    <span class="text-[9px] text-slate-400 font-semibold">Maks. 20MB</span>
                                </div>

                                <!-- File Preview Box (Hidden until file selected or pasted) -->
                                <div id="portal-file-preview" class="hidden p-3 bg-indigo-50/70 border border-indigo-100 rounded-xl flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div id="portal-file-thumb-container" class="shrink-0">
                                            <!-- Image thumbnail or file icon injected by JS -->
                                        </div>
                                        <div class="min-w-0">
                                            <span id="portal-file-name" class="block text-xs font-black text-slate-800 truncate"></span>
                                            <span id="portal-file-size" class="block text-[9px] text-slate-400 font-semibold"></span>
                                        </div>
                                    </div>
                                    <button type="button" onclick="clearPortalFile()" class="text-slate-400 hover:text-rose-600 font-bold p-1 text-sm cursor-pointer" title="Hapus Lampiran">
                                        &times;
                                    </button>
                                </div>
                            </div>

                            <!-- 5. Submit Button -->
                            <button type="submit" id="portal-send-btn" class="w-full py-3 bg-indigo-650 hover:bg-indigo-755 text-white text-xs font-black rounded-xl shadow-sm transition-all cursor-pointer flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                <span id="portal-send-btn-text">Kirim ke Chat Proyek</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Right Column: Messages Stream (Width: 7 cols) -->
                <div class="lg:col-span-7 flex flex-col space-y-3">
                    
                    <!-- Feed Filter Bar -->
                    <div class="flex flex-wrap items-center justify-between gap-2 bg-slate-50 border border-slate-200/60 p-2 rounded-2xl">
                        <div class="flex flex-wrap items-center gap-1">
                            <button type="button" onclick="filterPortalMessages('all')" id="btn-filter-all" class="px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-white text-indigo-700 shadow-2xs transition-all cursor-pointer">
                                Semua (<span id="count-all">{{ $messages->count() }}</span>)
                            </button>
                            <button type="button" onclick="filterPortalMessages('kendala-pending')" id="btn-filter-kendala-pending" class="px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider text-slate-500 hover:text-slate-800 transition-all cursor-pointer">
                                Hambatan Aktif (<span id="count-kendala-pending">{{ $messages->where('message_type', 'kendala')->where('is_resolved', false)->count() }}</span>)
                            </button>
                            <button type="button" onclick="filterPortalMessages('kendala-resolved')" id="btn-filter-kendala-resolved" class="px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider text-slate-500 hover:text-slate-800 transition-all cursor-pointer">
                                Hambatan Selesai (<span id="count-kendala-resolved">{{ $messages->where('message_type', 'kendala')->where('is_resolved', true)->count() }}</span>)
                            </button>
                            <button type="button" onclick="filterPortalMessages('question')" id="btn-filter-question" class="px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider text-slate-500 hover:text-slate-800 transition-all cursor-pointer">
                                Tanya (<span id="count-question">{{ $messages->where('message_type', 'question')->count() }}</span>)
                            </button>
                        </div>
                        <div class="text-[9px] text-slate-400 font-bold uppercase tracking-wider px-2">
                            Pembaruan Otomatis Tiap 3 Detik
                        </div>
                    </div>

                    <!-- Scrollable Messages Container -->
                    <div id="portal-messages-stream" class="flex-1 bg-slate-50/40 border border-slate-200/60 rounded-2xl p-4 sm:p-5 space-y-4 max-h-[550px] overflow-y-auto">
                        
                        @forelse($messages as $msg)
                            @php
                                $isMe = Auth::check() && ($msg->sender_id === Auth::id());
                                $isTeam = !empty($msg->sender_id);
                            @endphp

                            <div class="portal-message-item flex items-start gap-3 {{ $isMe ? 'flex-row-reverse' : '' }}" 
                                 data-id="{{ $msg->id }}" 
                                 data-type="{{ $msg->message_type ?? 'chat' }}"
                                 data-resolved="{{ $msg->is_resolved ? 'true' : 'false' }}"
                                 data-has-attachment="{{ $msg->attachment_file ? 'true' : 'false' }}">
                                
                                <!-- Avatar -->
                                <div class="shrink-0 mt-0.5">
                                    @if($msg->sender && $msg->sender->avatar)
                                        <img src="{{ asset('storage/' . $msg->sender->avatar) }}" alt="Avatar" class="w-9 h-9 rounded-xl object-cover border border-slate-200">
                                    @elseif($isTeam)
                                        <div class="w-9 h-9 rounded-xl bg-slate-900 text-white font-black text-xs flex items-center justify-center uppercase">
                                            {{ substr($msg->sender->name ?? 'TM', 0, 2) }}
                                        </div>
                                    @else
                                        <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white font-black text-xs flex items-center justify-center uppercase shadow-2xs">
                                            {{ substr($msg->client_name ?? 'KL', 0, 2) }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Card Bubble -->
                                <div class="max-w-[85%] space-y-2 {{ $isMe ? 'items-end text-right' : 'items-start text-left' }}">
                                    
                                    <!-- Header Info -->
                                    <div class="flex items-center gap-2 text-[10px] {{ $isMe ? 'justify-end' : 'justify-start' }}">
                                        <span class="font-black text-slate-850">{{ $msg->sender_display_name }}</span>
                                        <span class="px-1.5 py-0.2 rounded text-[8px] font-black uppercase {{ $isTeam ? 'bg-indigo-100 text-indigo-800' : 'bg-emerald-100 text-emerald-800' }}">
                                            {{ $isTeam ? ($msg->sender->role ?? 'Tim Proyek') : 'Klien' }}
                                        </span>
                                        <span class="text-slate-400 font-semibold">{{ $msg->created_at->diffForHumans() }}</span>
                                    </div>

                                    <!-- Bubble Content Box -->
                                    <div class="p-4 rounded-2xl shadow-xs space-y-2 {{ $isMe ? 'bg-indigo-650 text-white rounded-tr-none' : 'bg-white border border-slate-200/80 text-slate-800 rounded-tl-none' }}">
                                        
                                        <!-- Category Tag Badge -->
                                        @if($msg->message_type === 'kendala')
                                            <div class="flex items-center justify-between gap-2">
                                                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider {{ $isMe ? 'bg-rose-500 text-white' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                    <span>Tercatat: Hambatan / Kendala</span>
                                                </div>
                                                @if($msg->is_resolved)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[8px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800">
                                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                        Terselesaikan
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[8px] font-black uppercase tracking-wider bg-amber-100 text-amber-800">
                                                        Menunggu Solusi
                                                    </span>
                                                @endif
                                            </div>
                                        @elseif($msg->message_type === 'question')
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider {{ $isMe ? 'bg-sky-500 text-white' : 'bg-sky-50 text-sky-700 border border-sky-200' }}">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <span>Tercatat: Pertanyaan / Q&A</span>
                                            </div>
                                        @endif

                                        <!-- Message Text -->
                                        @if($msg->message)
                                            <p class="text-xs font-semibold leading-relaxed whitespace-pre-line break-words text-left">{{ $msg->message }}</p>
                                        @endif

                                        <!-- Attachment Rendering -->
                                        @if($msg->attachment_file)
                                            @if($msg->is_image)
                                                <!-- Screenshot / Image Preview -->
                                                <div class="rounded-xl overflow-hidden border {{ $isMe ? 'border-white/20' : 'border-slate-200' }} mt-2">
                                                    <div class="relative group cursor-pointer" onclick="openImageLightbox('{{ asset('storage/' . $msg->attachment_file) }}', '{{ $msg->attachment_name ?? 'Screenshot' }}')">
                                                        <img src="{{ asset('storage/' . $msg->attachment_file) }}" alt="{{ $msg->attachment_name }}" 
                                                             class="max-h-56 w-full object-cover rounded-xl transition-transform duration-200 group-hover:scale-[1.02]">
                                                        <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-1.5">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                                                            <span>Klik Perbesar</span>
                                                        </div>
                                                    </div>
                                                    <div class="p-2.5 flex items-center justify-between text-[10px] {{ $isMe ? 'bg-white/10 text-white' : 'bg-slate-50 text-slate-700' }}">
                                                        <span class="truncate max-w-[170px] font-medium">{{ $msg->attachment_name ?? 'Screenshot' }}</span>
                                                        <a href="{{ asset('storage/' . $msg->attachment_file) }}" download class="font-bold underline hover:opacity-80">Unduh</a>
                                                    </div>
                                                </div>
                                            @else
                                                <!-- Document File Preview -->
                                                <div class="p-3 rounded-xl border flex items-center gap-2.5 mt-2 {{ $isMe ? 'bg-white/10 border-white/20 text-white' : 'bg-slate-50 border-slate-200 text-slate-700' }}">
                                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                    <div class="flex-1 min-w-0 text-left">
                                                        <span class="block text-xs font-black truncate">{{ $msg->attachment_name ?? 'Dokumen Proyek' }}</span>
                                                    </div>
                                                    <a href="{{ asset('storage/' . $msg->attachment_file) }}" target="_blank" download
                                                       class="px-2.5 py-1 bg-white text-slate-800 hover:bg-slate-100 rounded-lg text-[10px] font-black tracking-wider uppercase shrink-0 shadow-2xs">
                                                        Unduh
                                                    </a>
                                                </div>
                                            @endif
                                        @endif

                                        <!-- Interactive Checklist & Resolution Box for Kendala -->
                                        @if($msg->message_type === 'kendala')
                                            <div id="portal-resolution-card-{{ $msg->id }}" class="mt-3 pt-2.5 border-t {{ $isMe ? 'border-white/20' : 'border-slate-100' }}">
                                                @if(!$msg->is_resolved)
                                                    <div class="rounded-xl p-3 border {{ $isMe ? 'bg-white/10 border-white/25 text-white' : 'bg-amber-50/90 border-amber-200/80 text-slate-800' }} space-y-2 text-left">
                                                        <div class="flex items-center justify-between gap-2">
                                                            <div class="flex items-center gap-1.5 text-xs font-black {{ $isMe ? 'text-amber-200' : 'text-amber-800' }}">
                                                                <svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                <span>Belum Selesai (Pending)</span>
                                                            </div>
                                                            <button type="button" onclick="togglePortalResolutionPrompt({{ $msg->id }})" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[10px] font-black uppercase tracking-wider transition-all flex items-center gap-1 shadow-2xs cursor-pointer">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                                <span>Tandai Terselesaikan</span>
                                                            </button>
                                                        </div>
                                                        
                                                        <!-- Inline Note Prompt Form -->
                                                        <div id="portal-resolve-box-{{ $msg->id }}" class="hidden pt-2 border-t {{ $isMe ? 'border-white/20' : 'border-amber-200/60' }} space-y-2">
                                                            <label class="block text-[10px] font-black uppercase tracking-wider {{ $isMe ? 'text-white/80' : 'text-slate-600' }}">Catatan Solusi / Penanganan Hambatan:</label>
                                                            <textarea id="portal-resolve-note-{{ $msg->id }}" rows="2" placeholder="Tuliskan catatan bagaimana hambatan ini diselesaikan..." class="w-full p-2.5 text-xs bg-white text-slate-800 border border-slate-200 rounded-xl font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"></textarea>
                                                            <div class="flex items-center justify-end gap-2">
                                                                <button type="button" onclick="togglePortalResolutionPrompt({{ $msg->id }})" class="px-2.5 py-1 text-[10px] font-bold {{ $isMe ? 'text-white/80 hover:text-white' : 'text-slate-500 hover:text-slate-700' }} cursor-pointer">Batal</button>
                                                                <button type="button" onclick="submitPortalResolution({{ $msg->id }}, true)" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[10px] font-black uppercase tracking-wider flex items-center gap-1 shadow-2xs cursor-pointer">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                                    <span>Simpan Status Selesai</span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="rounded-xl p-3 border {{ $isMe ? 'bg-white/10 border-white/25 text-white' : 'bg-emerald-50/90 border-emerald-200/80 text-slate-800' }} space-y-2 text-left">
                                                        <div class="flex items-center justify-between gap-2">
                                                            <div class="flex items-center gap-1.5 text-xs font-black {{ $isMe ? 'text-emerald-200' : 'text-emerald-800' }}">
                                                                <svg class="w-4 h-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                <span>Hambatan Terselesaikan</span>
                                                            </div>
                                                            <button type="button" onclick="submitPortalResolution({{ $msg->id }}, false)" class="px-2 py-0.5 text-[9px] font-bold {{ $isMe ? 'text-white/70 hover:text-white hover:bg-white/10' : 'text-slate-500 hover:text-rose-600 hover:bg-rose-50' }} rounded border border-transparent transition-all cursor-pointer" title="Buka kembali kendala jika masih butuh penanganan">
                                                                Buka Kembali
                                                            </button>
                                                        </div>
                                                        <div class="text-[10px] {{ $isMe ? 'bg-white/10 text-white' : 'bg-white/80 text-emerald-950 border border-emerald-100' }} p-2.5 rounded-lg space-y-1">
                                                            <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                                                <span class="font-bold">Diselesaikan oleh:</span>
                                                                <span class="font-black">{{ $msg->resolver_name ?? 'Tim Proyek' }}</span>
                                                                @if($msg->resolved_at)
                                                                    <span class="opacity-70 font-normal">({{ $msg->resolved_at->diffForHumans() }})</span>
                                                                @endif
                                                            </div>
                                                            @if($msg->resolution_note)
                                                                <div class="pt-1.5 border-t {{ $isMe ? 'border-white/10' : 'border-emerald-100' }} mt-1">
                                                                    <span class="font-bold block text-[9px] uppercase tracking-wider {{ $isMe ? 'text-white/80' : 'text-emerald-800' }}">Catatan Solusi:</span>
                                                                    <p class="font-medium {{ $isMe ? 'text-white/90' : 'text-slate-700' }} italic leading-relaxed whitespace-pre-line">{{ $msg->resolution_note }}</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                    </div>

                                </div>

                            </div>
                        @empty
                            <div id="portal-empty-chat-state" class="border border-dashed border-slate-200 rounded-2xl py-14 px-4 text-center space-y-2">
                                <div class="w-10 h-10 mx-auto rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                </div>
                                <h4 class="text-xs font-bold text-slate-700">Belum ada obrolan aktif pada proyek ini.</h4>
                                <p class="text-[10px] text-slate-400 font-semibold max-w-sm mx-auto leading-normal">
                                    Tuliskan kendala, pertanyaan teknis, atau lampirkan dokumen dan screenshot untuk memulai diskusi langsung dengan tim pengembang.
                                </p>
                            </div>
                        @endforelse

                    </div>

                </div>

            </div>

        </section>

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
                    thumbContainer.innerHTML = `<img src="${e.target.result}" class="w-9 h-9 rounded-lg object-cover border border-indigo-200">`;
                };
                reader.readAsDataURL(file);
            } else {
                thumbContainer.innerHTML = `
                    <div class="w-9 h-9 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-black text-xs">
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
            sendBtnText.textContent = "Mengirim...";

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
                sendBtnText.textContent = "Kirim ke Chat Proyek";

                if (data.success && data.message) {
                    messageInput.value = '';
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
                sendBtnText.textContent = "Kirim ke Chat Proyek";
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
                    <div id="portal-resolution-card-${msg.id}" class="mt-3 pt-2.5 border-t ${isMe ? 'border-white/20' : 'border-slate-100'}">
                        <div class="rounded-xl p-3 border ${isMe ? 'bg-white/10 border-white/25 text-white' : 'bg-amber-50/90 border-amber-200/80 text-slate-800'} space-y-2 text-left">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-1.5 text-xs font-black ${isMe ? 'text-amber-200' : 'text-amber-800'}">
                                    <svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Belum Selesai (Pending)</span>
                                </div>
                                <button type="button" onclick="togglePortalResolutionPrompt(${msg.id})" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[10px] font-black uppercase tracking-wider transition-all flex items-center gap-1 shadow-2xs cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    <span>Tandai Terselesaikan</span>
                                </button>
                            </div>
                            
                            <!-- Inline Note Prompt Form -->
                            <div id="portal-resolve-box-${msg.id}" class="hidden pt-2 border-t ${isMe ? 'border-white/20' : 'border-amber-200/60'} space-y-2">
                                <label class="block text-[10px] font-black uppercase tracking-wider ${isMe ? 'text-white/80' : 'text-slate-600'}">Catatan Solusi / Penanganan Hambatan:</label>
                                <textarea id="portal-resolve-note-${msg.id}" rows="2" placeholder="Tuliskan catatan bagaimana hambatan ini diselesaikan..." class="w-full p-2.5 text-xs bg-white text-slate-800 border border-slate-200 rounded-xl font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"></textarea>
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" onclick="togglePortalResolutionPrompt(${msg.id})" class="px-2.5 py-1 text-[10px] font-bold ${isMe ? 'text-white/80 hover:text-white' : 'text-slate-500 hover:text-slate-700'} cursor-pointer">Batal</button>
                                    <button type="button" onclick="submitPortalResolution(${msg.id}, true)" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[10px] font-black uppercase tracking-wider flex items-center gap-1 shadow-2xs cursor-pointer">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        <span>Simpan Status Selesai</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                return `
                    <div id="portal-resolution-card-${msg.id}" class="mt-3 pt-2.5 border-t ${isMe ? 'border-white/20' : 'border-slate-100'}">
                        <div class="rounded-xl p-3 border ${isMe ? 'bg-white/10 border-white/25 text-white' : 'bg-emerald-50/90 border-emerald-200/80 text-slate-800'} space-y-2 text-left">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-1.5 text-xs font-black ${isMe ? 'text-emerald-200' : 'text-emerald-800'}">
                                    <svg class="w-4 h-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Hambatan Terselesaikan</span>
                                </div>
                                <button type="button" onclick="submitPortalResolution(${msg.id}, false)" class="px-2 py-0.5 text-[9px] font-bold ${isMe ? 'text-white/70 hover:text-white hover:bg-white/10' : 'text-slate-500 hover:text-rose-600 hover:bg-rose-50'} rounded border border-transparent transition-all cursor-pointer" title="Buka kembali kendala jika masih butuh penanganan">
                                    Buka Kembali
                                </button>
                            </div>
                            <div class="text-[10px] ${isMe ? 'bg-white/10 text-white' : 'bg-white/80 text-emerald-950 border border-emerald-100'} p-2.5 rounded-lg space-y-1">
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                    <span class="font-bold">Diselesaikan oleh:</span>
                                    <span class="font-black">${msg.resolver_name || 'Tim Proyek'}</span>
                                    ${msg.resolved_at ? `<span class="opacity-70 font-normal">(${msg.resolved_at})</span>` : ''}
                                </div>
                                ${msg.resolution_note ? `
                                    <div class="pt-1.5 border-t ${isMe ? 'border-white/10' : 'border-emerald-100'} mt-1">
                                        <span class="font-bold block text-[9px] uppercase tracking-wider ${isMe ? 'text-white/80' : 'text-emerald-800'}">Catatan Solusi:</span>
                                        <p class="font-medium ${isMe ? 'text-white/90' : 'text-slate-700'} italic leading-relaxed whitespace-pre-line">${msg.resolution_note}</p>
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
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[8px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    Terselesaikan
                                </span>
                            ` : `
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[8px] font-black uppercase tracking-wider bg-amber-100 text-amber-800">
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
                    <div class="flex items-center justify-between gap-2">
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider ${isMe ? 'bg-rose-500 text-white' : 'bg-rose-50 text-rose-700 border border-rose-200'}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <span>Tercatat: Hambatan / Kendala</span>
                        </div>
                        <div class="message-type-header-badge">
                            ${msg.is_resolved ? `
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[8px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    Terselesaikan
                                </span>
                            ` : `
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[8px] font-black uppercase tracking-wider bg-amber-100 text-amber-800">
                                    Menunggu Solusi
                                </span>
                            `}
                        </div>
                    </div>
                `;
            } else if (msg.message_type === 'question') {
                badgeHtml = `
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider ${isMe ? 'bg-sky-500 text-white' : 'bg-sky-50 text-sky-700 border border-sky-200'}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Tercatat: Pertanyaan / Q&A</span>
                    </div>
                `;
            }

            let attachmentHtml = '';
            if (msg.attachment_url) {
                if (msg.is_image) {
                    attachmentHtml = `
                        <div class="rounded-xl overflow-hidden border ${isMe ? 'border-white/20' : 'border-slate-200'} mt-2">
                            <div class="relative group cursor-pointer" onclick="openImageLightbox('${msg.attachment_url}', '${msg.attachment_name || 'Screenshot'}')">
                                <img src="${msg.attachment_url}" alt="${msg.attachment_name}" 
                                     class="max-h-56 w-full object-cover rounded-xl transition-transform duration-200 group-hover:scale-[1.02]">
                                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                                    <span>Klik Perbesar</span>
                                </div>
                            </div>
                            <div class="p-2.5 flex items-center justify-between text-[10px] ${isMe ? 'bg-white/10 text-white' : 'bg-slate-50 text-slate-700'}">
                                <span class="truncate max-w-[170px] font-medium">${msg.attachment_name || 'Screenshot'}</span>
                                <a href="${msg.attachment_url}" download class="font-bold underline hover:opacity-80">Unduh</a>
                            </div>
                        </div>
                    `;
                } else {
                    attachmentHtml = `
                        <div class="p-3 rounded-xl border flex items-center gap-2.5 mt-2 ${isMe ? 'bg-white/10 border-white/20 text-white' : 'bg-slate-50 border-slate-200 text-slate-700'}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <div class="flex-1 min-w-0 text-left">
                                <span class="block text-xs font-black truncate">${msg.attachment_name || 'Dokumen Proyek'}</span>
                            </div>
                            <a href="${msg.attachment_url}" target="_blank" download
                               class="px-2.5 py-1 bg-white text-slate-800 hover:bg-slate-100 rounded-lg text-[10px] font-black tracking-wider uppercase shrink-0 shadow-2xs">
                                Unduh
                            </a>
                        </div>
                    `;
                }
            }

            const avatarHtml = msg.sender_avatar 
                ? `<img src="${msg.sender_avatar}" alt="Avatar" class="w-9 h-9 rounded-xl object-cover border border-slate-200">`
                : `<div class="w-9 h-9 rounded-xl ${isTeam ? 'bg-slate-900' : 'bg-emerald-600'} text-white font-black text-xs flex items-center justify-center uppercase shadow-2xs">${(msg.sender_name || 'KL').substring(0, 2)}</div>`;

            const resolutionBoxHtml = buildPortalResolutionCardHtml(msg, isMe);

            const itemHtml = `
                <div class="portal-message-item flex items-start gap-3 ${isMe ? 'flex-row-reverse' : ''}" 
                     data-id="${msg.id}" 
                     data-type="${msg.message_type || 'chat'}"
                     data-resolved="${msg.is_resolved ? 'true' : 'false'}"
                     data-has-attachment="${msg.attachment_url ? 'true' : 'false'}">
                    
                    <div class="shrink-0 mt-0.5">
                        ${avatarHtml}
                    </div>

                    <div class="max-w-[85%] space-y-2 ${isMe ? 'items-end text-right' : 'items-start text-left'}">
                        <div class="flex items-center gap-2 text-[10px] ${isMe ? 'justify-end' : 'justify-start'}">
                            <span class="font-black text-slate-850">${msg.sender_name}</span>
                            <span class="px-1.5 py-0.2 rounded text-[8px] font-black uppercase ${isTeam ? 'bg-indigo-100 text-indigo-800' : 'bg-emerald-100 text-emerald-800'}">
                                ${msg.sender_role || (isTeam ? 'Tim Proyek' : 'Klien')}
                            </span>
                            <span class="text-slate-400 font-semibold">${msg.created_at}</span>
                        </div>

                        <div class="p-4 rounded-2xl shadow-xs space-y-2 ${isMe ? 'bg-indigo-650 text-white rounded-tr-none' : 'bg-white border border-slate-200/80 text-slate-800 rounded-tl-none'}">
                            ${badgeHtml}
                            ${msg.message ? `<p class="text-xs font-semibold leading-relaxed whitespace-pre-line break-words text-left">${msg.message}</p>` : ''}
                            ${attachmentHtml}
                            ${resolutionBoxHtml}
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
                        btn.className = "px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-white text-indigo-700 shadow-2xs transition-all cursor-pointer";
                    } else {
                        btn.className = "px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider text-slate-500 hover:text-slate-800 transition-all cursor-pointer";
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
            }
        });
    </script>
</body>
</html>

