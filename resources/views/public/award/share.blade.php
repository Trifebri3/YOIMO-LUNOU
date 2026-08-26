<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Pencapaian - {{ $award->title }}</title>
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
                    $shareUrl = urlencode(route('public.award.show', $award->share_token));
                    $shareText = urlencode("Saya baru saja meraih penghargaan '" . $award->title . "' di Yoimo! Keseimbangan kerja & wellbeing tercapai. Cek sertifikat digital saya di sini:");
                    $linkedInUrl = "https://www.linkedin.com/sharing/share-offsite/?url=" . $shareUrl;
                @endphp
                
                <a href="{{ $linkedInUrl }}" target="_blank" 
                   class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black rounded-xl transition-all shadow-md flex items-center gap-2">
                    Bagikan ke LinkedIn
                </a>
                
                <button onclick="navigator.clipboard.writeText(window.location.href); alert('Link sertifikat berhasil disalin!');" 
                        class="px-6 py-3 bg-slate-800 hover:bg-slate-750 text-slate-300 text-xs font-black rounded-xl transition-all">
                    Salin Tautan Sertifikat
                </button>
            </div>
        </div>
    </div>
</body>
</html>
