<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Pencapaian: {{ $award->title }} - {{ $award->user->name }} | Yoimo Workspace</title>
    <meta name="description" content="Selamat kepada {{ $award->user->name }} atas pencapaian penghargaan '{{ $award->title }}' yang diterbitkan secara resmi pada {{ $award->issued_date->format('d F Y') }} dalam ekosistem kerja Yoimo Workspace.">

    <!-- Open Graph / Facebook / LinkedIn -->
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ route('public.award.show', $award->share_token) }}">
    <meta property="og:title" content="Sertifikat Pencapaian: {{ $award->title }} - {{ $award->user->name }}">
    <meta property="og:description" content="Penghargaan resmi dianugerahkan kepada {{ $award->user->name }} atas pencapaian '{{ $award->title }}' dalam ekosistem kerja Yoimo Workspace.">
    <meta property="og:image" content="{{ asset('images/yoimo-achievement-og.png') }}">
    <meta property="og:image:secure_url" content="{{ asset('images/yoimo-achievement-og.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="Yoimo Workspace">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ route('public.award.show', $award->share_token) }}">
    <meta name="twitter:title" content="Sertifikat Pencapaian: {{ $award->title }} - {{ $award->user->name }}">
    <meta name="twitter:description" content="Penghargaan resmi dianugerahkan kepada {{ $award->user->name }} atas pencapaian '{{ $award->title }}' dalam ekosistem kerja Yoimo Workspace.">
    <meta name="twitter:image" content="{{ asset('images/yoimo-achievement-og.png') }}">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-3xl w-full bg-slate-900/90 border border-indigo-500/30 rounded-[2.5rem] p-8 md:p-12 text-center shadow-2xl relative overflow-hidden backdrop-blur-md">
        <!-- Certificate Design Borders -->
        <div class="absolute inset-4 border-2 border-dashed border-indigo-500/20 rounded-[2rem] pointer-events-none"></div>
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Yoimo Verification Branding -->
        <div class="relative z-10 space-y-8">
            <div class="flex flex-col items-center">
                <span class="text-xs font-black uppercase tracking-widest text-indigo-400">Yoimo Ecosystem Validation</span>
                <div class="h-0.5 w-12 bg-indigo-500/50 mt-2"></div>
            </div>

            <!-- Certificate Core Title -->
            <div class="space-y-2">
                <h1 class="text-3xl md:text-4xl font-black uppercase text-white tracking-wider">Sertifikat Penghargaan</h1>
                <p class="text-xs text-slate-400 uppercase tracking-widest">Diberikan atas pencapaian luar biasa dalam ekosistem</p>
            </div>

            <!-- Employee Name -->
            <div class="space-y-1 py-4">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Dianugerahkan Kepada</span>
                <h2 class="text-2xl md:text-3xl font-black text-indigo-300">{{ $award->user->name }}</h2>
                <div class="w-1/3 h-px bg-slate-800 mx-auto mt-2"></div>
            </div>

            <!-- Award Title -->
            <div class="bg-indigo-950/40 border border-indigo-900/50 rounded-2xl p-6 max-w-lg mx-auto">
                <span class="text-[10px] font-black uppercase text-indigo-400 tracking-widest block mb-1">Gelar Pencapaian</span>
                <span class="text-xl font-black text-white uppercase tracking-wide block">{{ $award->title }}</span>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                    Diberikan secara resmi karena telah mendemonstrasikan keunggulan, kedisiplinan kerja, dan komitmen tinggi terhadap ritme keseimbangan kerja.
                </p>
            </div>

            <!-- Meta Details -->
            <div class="grid grid-cols-2 gap-4 max-w-sm mx-auto pt-4 text-xs font-bold text-slate-400">
                <div class="text-left border-r border-slate-800 pr-4">
                    <span class="text-[9px] uppercase tracking-wider block mb-0.5">Tanggal Terbit</span>
                    <span class="text-white font-black">{{ $award->issued_date->format('d F Y') }}</span>
                </div>
                <div class="text-left pl-4">
                    <span class="text-[9px] uppercase tracking-wider block mb-0.5">Validasi Kode</span>
                    <span class="text-white font-black uppercase">{{ $award->share_token }}</span>
                </div>
            </div>

            <!-- Action / Share Button -->
            <div class="pt-6 flex flex-col sm:flex-row items-center justify-center gap-4">
                @php
                    $awardShareUrl = route('public.award.show', $award->share_token);
                    $linkedInUrl = "https://www.linkedin.com/sharing/share-offsite/?url=" . urlencode($awardShareUrl);
                @endphp
                
                <button type="button" onclick="shareAwardToLinkedIn()" 
                   class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black rounded-xl transition-all shadow-md flex items-center gap-2">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>
                    <span>Bagikan ke LinkedIn</span>
                </button>
                
                <button onclick="navigator.clipboard.writeText('{{ $awardShareUrl }}'); showToast('Tautan sertifikat berhasil disalin!');" 
                        class="px-6 py-3 bg-slate-800 hover:bg-slate-750 text-slate-300 text-xs font-black rounded-xl transition-all">
                    Salin Tautan Sertifikat
                </button>
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

        function shareAwardToLinkedIn() {
            const shareUrl = '{{ $awardShareUrl }}';
            const caption = `🏆 Penghargaan Resmi Diraih di Yoimo Workspace!\n\nSaya bangga menerima penghargaan: "{{ $award->title }}"\n👤 Penerima: {{ $award->user->name }}\n📅 Tanggal Terbit: {{ $award->issued_date->format('d F Y') }}\n✨ Penghargaan ini diberikan atas dedikasi kerja dan ritme produktivitas yang seimbang.\n\nVerifikasi sertifikat resmi:\n${shareUrl}\n\n#YoimoWorkspace #Award #Achievement #WorkLifeBalance #Recognition #ProfessionalGrowth`;

            navigator.clipboard.writeText(caption).then(() => {
                showToast('Caption LinkedIn Berhasil Disalin!', 'Tekan <kbd class="px-1.5 py-0.5 bg-slate-800 border border-slate-700 rounded text-[10px] font-mono text-amber-300">Ctrl + V</kbd> di LinkedIn, lalu klik <strong>Post</strong>.');
                window.open('{{ $linkedInUrl }}', '_blank', 'width=650,height=600');
            }).catch(() => {
                window.open('{{ $linkedInUrl }}', '_blank', 'width=650,height=600');
            });
        }
    </script>
</body>
</html>
