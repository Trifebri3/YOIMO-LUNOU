<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Lupa Password - Yoimo Workspace</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (via Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="h-full bg-slate-50/50 text-slate-800 flex items-center justify-center p-4">

    <!-- Container Utama Box -->
    <div class="w-full max-w-md bg-white border border-slate-100 rounded-3xl p-8 md:p-10 shadow-xl">
        
        <!-- Logo -->
        <div class="flex items-center gap-3 mb-6">
            <img src="{{ asset('logokotak.png') }}" alt="Logo" class="h-10 w-10 object-contain">
            <span class="text-md font-black tracking-tight text-slate-800 font-sans">YOIMO</span>
        </div>

        <!-- Description -->
        <div class="space-y-2 mb-6">
            <h2 class="text-xl font-black text-slate-900 tracking-tight font-sans">Lupa Password?</h2>
            <p class="text-xs font-semibold text-slate-400 leading-relaxed font-sans">
                Jangan khawatir. Masukkan email Anda dan kami akan mengirimkan link untuk mengatur ulang password Anda.
            </p>
        </div>

        <!-- Session Status / Alert -->
        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-xs font-semibold text-emerald-700 mb-4 font-sans">
                {{ session('status') }}
            </div>
        @endif

        <!-- Validation Error Alert -->
        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-100 rounded-2xl text-xs font-semibold text-rose-600 mb-4 space-y-1 font-sans">
                @foreach ($errors->all() as $error)
                    <div class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <span>{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <!-- Email -->
            <div class="space-y-1.5">
                <label for="email" class="text-xs font-bold text-slate-400 uppercase tracking-wider font-sans">Email Address</label>
                <div class="relative">
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                           placeholder="nama@perusahaan.com"
                           class="w-full pl-4 pr-10 py-3 text-sm bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-semibold text-slate-700 font-sans">
                    <div class="absolute right-3.5 top-3.5 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit"
                    class="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-extrabold text-sm rounded-2xl shadow-lg shadow-emerald-500/10 hover:shadow-emerald-500/25 transition-all flex items-center justify-center gap-2 mt-2 cursor-pointer font-sans">
                <span>Kirim Link Reset</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </button>
        </form>

        <!-- Back to Login -->
        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-xs font-bold text-slate-400 hover:text-slate-800 transition-colors font-sans">
                ← Kembali ke Halaman Login
            </a>
        </div>

    </div>
</body>
</html>
