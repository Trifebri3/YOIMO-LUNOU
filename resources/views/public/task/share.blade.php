<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penyelesaian Tugas: {{ $task->title }} - {{ $task->assignee?->name ?? 'Tim Yoimo' }} | Yoimo Workspace</title>
    <meta name="description" content="Tugas '{{ $task->title }}' pada proyek {{ $task->project?->name ?? 'Yoimo' }} telah berhasil diselesaikan dengan baik oleh {{ $task->assignee?->name ?? 'User' }} dan diverifikasi resmi pada platform Yoimo Workspace.">

    <!-- Open Graph / Facebook / LinkedIn -->
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ route('public.task.show', $task->id) }}">
    <meta property="og:title" content="Penyelesaian Tugas: {{ $task->title }} - {{ $task->assignee?->name ?? 'Tim Yoimo' }}">
    <meta property="og:description" content="Tugas '{{ $task->title }}' pada proyek {{ $task->project?->name ?? 'Yoimo' }} telah selesai 100% dan terverifikasi secara resmi di Yoimo Workspace.">
    <meta property="og:image" content="{{ asset('images/yoimo-achievement-og.png') }}">
    <meta property="og:image:secure_url" content="{{ asset('images/yoimo-achievement-og.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:type" content="image/png">
    <meta property="og:site_name" content="Yoimo Workspace">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ route('public.task.show', $task->id) }}">
    <meta name="twitter:title" content="Penyelesaian Tugas: {{ $task->title }} - {{ $task->assignee?->name ?? 'Tim Yoimo' }}">
    <meta name="twitter:description" content="Tugas '{{ $task->title }}' pada proyek {{ $task->project?->name ?? 'Yoimo' }} telah selesai 100% dan terverifikasi secara resmi di Yoimo Workspace.">
    <meta name="twitter:image" content="{{ asset('images/yoimo-achievement-og.png') }}">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #090d16 0%, #1e1b4b 60%, #0f172a 100%);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 sm:p-6 text-slate-100">

    <div class="max-w-3xl w-full bg-slate-900/90 border border-indigo-500/30 rounded-[2.5rem] p-6 sm:p-12 text-center shadow-2xl relative overflow-hidden backdrop-blur-md">
        <!-- Certificate Design Borders -->
        <div class="absolute inset-4 border-2 border-dashed border-indigo-500/20 rounded-[2rem] pointer-events-none"></div>
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Yoimo Verification Branding -->
        <div class="relative z-10 space-y-7">
            <div class="flex flex-col items-center">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-indigo-500/10 border border-indigo-500/30 rounded-full">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-[11px] font-black uppercase tracking-widest text-indigo-300">Pencapaian Terverifikasi • Yoimo Workspace</span>
                </div>
                <div class="h-0.5 w-12 bg-indigo-500/50 mt-3"></div>
            </div>

            <!-- Header Title -->
            <div class="space-y-2">
                <h1 class="text-2xl sm:text-4xl font-black uppercase text-white tracking-wide">Bukti Penyelesaian Tugas</h1>
                <p class="text-xs text-slate-400 uppercase tracking-widest">Dokumentasi hasil kerja resmi & transparansi kolaborasi</p>
            </div>

            <!-- Assignee Info -->
            <div class="space-y-1.5 py-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Diselesaikan Oleh PIC</span>
                <h2 class="text-2xl sm:text-3xl font-black text-indigo-300">{{ $task->assignee?->name ?? 'Anggota Tim' }}</h2>
                @if($task->project)
                    <p class="text-xs font-semibold text-slate-400">
                        Proyek: <span class="text-white font-bold">{{ $task->project->name }}</span>
                        @if($task->project->company)
                            • <span class="text-indigo-400">{{ $task->project->company->company_name }}</span>
                        @endif
                    </p>
                @endif
                <div class="w-28 h-px bg-slate-800 mx-auto mt-2"></div>
            </div>

            <!-- Task Main Card -->
            <div class="bg-indigo-950/40 border border-indigo-900/50 rounded-2xl p-5 sm:p-6 max-w-xl mx-auto text-left space-y-3">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[10px] font-black uppercase text-indigo-400 tracking-wider">Nama Tugas</span>
                    <span class="px-2.5 py-0.5 bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-[10px] font-black uppercase rounded-full">
                        {{ $task->status === 'Completed' ? 'Selesai 100%' : $task->status }}
                    </span>
                </div>
                
                <h3 class="text-lg sm:text-xl font-black text-white leading-snug">{{ $task->title }}</h3>

                @if($task->description)
                    <p class="text-xs text-slate-300 leading-relaxed pt-1 border-t border-indigo-900/40">
                        {{ $task->description }}
                    </p>
                @endif

                @if($task->submission_notes)
                    <div class="mt-3 p-3 bg-slate-900/70 border border-slate-800 rounded-xl text-xs">
                        <span class="text-[10px] font-bold text-amber-400 block uppercase mb-1">Catatan Laporan / Hasil Kerja:</span>
                        <p class="text-slate-300 italic">{{ $task->submission_notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Verification Metadata -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 max-w-xl mx-auto text-xs font-bold text-slate-400">
                <div class="bg-slate-950/50 border border-slate-800/80 rounded-xl p-3 text-center">
                    <span class="text-[9px] uppercase tracking-wider block text-slate-500 mb-0.5">Waktu Selesai</span>
                    <span class="text-white font-black">
                        {{ $task->submitted_at ? $task->submitted_at->format('d M Y') : ($task->updated_at ? $task->updated_at->format('d M Y') : 'Terverifikasi') }}
                    </span>
                </div>
                <div class="bg-slate-950/50 border border-slate-800/80 rounded-xl p-3 text-center">
                    <span class="text-[9px] uppercase tracking-wider block text-slate-500 mb-0.5">Ketepatan Waktu</span>
                    <span class="text-emerald-400 font-black uppercase">
                        {{ $task->submission_timing_status ?: 'Tepat Waktu' }}
                    </span>
                </div>
                <div class="col-span-2 sm:col-span-1 bg-slate-950/50 border border-slate-800/80 rounded-xl p-3 text-center">
                    <span class="text-[9px] uppercase tracking-wider block text-slate-500 mb-0.5">ID Verifikasi</span>
                    <span class="text-indigo-300 font-black uppercase font-mono">#TASK-{{ str_pad($task->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>

            <!-- Share & Action Buttons -->
            @php
                $taskShareUrl = route('public.task.show', $task->id);
                $encodedUrl = urlencode($taskShareUrl);
                $linkedInUrl = "https://www.linkedin.com/sharing/share-offsite/?url=" . $encodedUrl;
                $assigneeName = $task->assignee?->name ?? 'Anggota Tim';
                $projectName = $task->project?->name ?? 'Proyek';
            @endphp

            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
                <button type="button" onclick="shareTaskToLinkedIn()" 
                   class="w-full sm:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black rounded-xl transition-all shadow-md flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>
                    <span>Bagikan ke LinkedIn</span>
                </button>
                
                <button type="button" onclick="navigator.clipboard.writeText('{{ $taskShareUrl }}'); showToast('Tautan verifikasi berhasil disalin!');" 
                        class="w-full sm:w-auto px-6 py-3 bg-slate-800 hover:bg-slate-750 text-slate-300 text-xs font-black rounded-xl transition-all">
                    Salin Tautan Verifikasi
                </button>

                <a href="{{ url('/') }}" class="w-full sm:w-auto px-5 py-3 bg-indigo-950/60 hover:bg-indigo-900/60 border border-indigo-800/40 text-indigo-300 text-xs font-bold rounded-xl transition-all">
                    Buka Yoimo Workspace
                </a>
            </div>
        </div>
    </div>

    <!-- Notification Toast -->
    <div id="shareToast" class="fixed bottom-6 right-6 max-w-md bg-slate-900/95 text-white border border-indigo-500/40 rounded-2xl p-4 shadow-2xl backdrop-blur-md transform translate-y-24 opacity-0 transition-all duration-300 z-50 pointer-events-none flex items-start gap-3">
        <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <div class="text-xs space-y-1">
            <p id="toastTitle" class="font-black text-indigo-200">Caption LinkedIn Disalin!</p>
            <p id="toastMsg" class="text-slate-300 leading-relaxed">Tekan <kbd class="px-1.5 py-0.5 bg-slate-800 border border-slate-700 rounded text-[10px] font-mono text-amber-300">Ctrl + V</kbd> di jendela LinkedIn, lalu klik <strong>Post</strong>.</p>
        </div>
    </div>

    <script>
        function showToast(title, msg) {
            const toast = document.getElementById('shareToast');
            if (title) document.getElementById('toastTitle').textContent = title;
            if (msg) document.getElementById('toastMsg').innerHTML = msg;
            toast.classList.remove('translate-y-24', 'opacity-0');
            setTimeout(() => {
                toast.classList.add('translate-y-24', 'opacity-0');
            }, 5000);
        }

        function shareTaskToLinkedIn() {
            const shareUrl = '{{ $taskShareUrl }}';
            const caption = `🎯 Senang sekali dapat menyelesaikan tugas "{{ addslashes($task->title) }}" pada proyek "{{ addslashes($projectName) }}" di Yoimo Workspace!\n\n📌 Rincian Pencapaian:\n• Tugas: {{ addslashes($task->title) }}\n• Proyek: {{ addslashes($projectName) }}\n• PIC: {{ addslashes($assigneeName) }}\n• Status: Selesai 100% & Terverifikasi\n\nLihat bukti verifikasi penyelesaian tugas saya secara publik di sini:\n${shareUrl}\n\n#YoimoWorkspace #Productivity #ProjectManagement #WorkLifeHarmony #Achievement #KerjaCerdas`;

            navigator.clipboard.writeText(caption).then(() => {
                showToast('Caption LinkedIn Berhasil Disalin!', 'Tekan <kbd class="px-1.5 py-0.5 bg-slate-800 border border-slate-700 rounded text-[10px] font-mono text-amber-300">Ctrl + V</kbd> di jendela LinkedIn, lalu klik <strong>Post</strong>.');
                window.open('{{ $linkedInUrl }}', '_blank', 'width=650,height=600');
            }).catch(() => {
                window.open('{{ $linkedInUrl }}', '_blank', 'width=650,height=600');
            });
        }
    </script>
</body>
</html>
