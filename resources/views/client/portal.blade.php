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

        </div>

        <!-- 4. Client Q&A Section -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Submit Question Form (Width: 5 columns) -->
            <div class="lg:col-span-5 bg-white border border-slate-200/60 rounded-3xl p-6 shadow-sm space-y-4">
                <div>
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">Ajukan Pertanyaan</h3>
                    <span class="text-[9px] font-semibold text-slate-400 uppercase tracking-wider block mt-0.5">Kirimkan pertanyaan langsung ke tim pengembang</span>
                </div>

                <form method="POST" action="{{ route('client.portal.ask', $project->share_token) }}" class="space-y-4 pt-2">
                    @csrf
                    <div class="space-y-1.5">
                        <label for="client_name" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nama Anda</label>
                        <input type="text" name="client_name" id="client_name" required placeholder="Contoh: Budi Santoso"
                               class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-semibold text-slate-700">
                    </div>

                    <div class="space-y-1.5">
                        <label for="question" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pertanyaan Anda</label>
                        <textarea name="question" id="question" rows="4" required placeholder="Tuliskan pertanyaan detail Anda di sini..."
                                  class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-semibold text-slate-700 leading-relaxed"></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-indigo-650 hover:bg-indigo-755 text-white text-xs font-black rounded-xl shadow-sm transition-all cursor-pointer">
                        Kirim Pertanyaan
                    </button>
                </form>
            </div>

            <!-- Q&A Feed (Width: 7 columns) -->
            <div class="lg:col-span-7 bg-white border border-slate-200/60 rounded-3xl p-6 shadow-sm space-y-4">
                <div>
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">Daftar Tanya Jawab Klien</h3>
                    <span class="text-[9px] font-semibold text-slate-400 uppercase tracking-wider block mt-0.5">Pertanyaan yang telah dijawab & diproses</span>
                </div>

                @if($questions->isEmpty())
                    <div class="border border-dashed border-slate-200 rounded-2xl py-12 px-4 text-center">
                        <p class="text-xs text-slate-400 font-semibold">Belum ada daftar tanya jawab yang aktif.</p>
                    </div>
                @else
                    <div class="space-y-4 max-h-[50vh] overflow-y-auto pr-1">
                        @foreach($questions as $q)
                            <div class="p-4 bg-slate-50 border border-slate-200/60 rounded-2xl space-y-3">
                                <div class="flex items-center justify-between text-[10px] text-slate-400 font-semibold">
                                    <span>Penanya: <strong class="text-slate-700 font-black">{{ $q->client_name }}</strong></span>
                                    <span>{{ \Carbon\Carbon::parse($q->created_at)->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs font-bold text-slate-800 leading-normal">{{ $q->question }}</p>

                                @if($q->answer)
                                    <div class="pl-3 border-l-2 border-emerald-500 space-y-1 pt-1">
                                        <span class="text-[9px] font-black text-emerald-700 uppercase tracking-wider block">Jawaban Tim:</span>
                                        <p class="text-xs font-semibold text-slate-600 leading-normal">{{ $q->answer }}</p>
                                    </div>
                                @else
                                    <div class="pl-3 border-l-2 border-slate-300 space-y-1 pt-1">
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Status:</span>
                                        <p class="text-xs font-semibold text-slate-400">Menunggu tanggapan dari tim proyek...</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </section>

    </main>

</body>
</html>
