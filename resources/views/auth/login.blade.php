<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - Yoimo Workspace</title>

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
        
        /* Premium Micro-Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-12px) rotate(4deg); }
        }
        @keyframes rotate-slow {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes pulse-gentle {
            0%, 100% { opacity: 0.15; transform: scale(1); }
            50% { opacity: 0.35; transform: scale(1.1); }
        }
        @keyframes ping-slow {
            0% { transform: scale(1); opacity: 1; }
            70%, 100% { transform: scale(2); opacity: 0; }
        }
        
        .float-anim {
            animation: float 5s ease-in-out infinite;
        }
        .rotate-anim {
            animation: rotate-slow 25s linear infinite;
        }
        .pulse-anim {
            animation: pulse-gentle 5s ease-in-out infinite;
        }
        .ping-anim {
            animation: ping-slow 2s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
    </style>
</head>
<body class="h-full bg-slate-50/50 text-slate-800 flex items-center justify-center p-4">

    <!-- Container Utama Split Layout -->
    <div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-12 bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-xl min-h-[580px]">
        
        <!-- Sisi Kiri: Branding & Visual (5 Kolom) -->
        <div class="lg:col-span-5 bg-gradient-to-br from-emerald-900 to-slate-900 p-10 flex flex-col justify-between relative overflow-hidden text-white border-r border-slate-100/10">
            <!-- Decorative Glow Rings (Animated) -->
            <div class="absolute -top-12 -left-12 w-48 h-48 rounded-full bg-emerald-500/10 blur-3xl pulse-anim"></div>
            <div class="absolute -bottom-12 -right-12 w-48 h-48 rounded-full bg-teal-500/10 blur-3xl pulse-anim"></div>

            <!-- Animated Abstract AI Wireframe Orb -->
            <div class="absolute inset-0 flex items-center justify-center opacity-15 pointer-events-none">
                <svg class="w-80 h-80 rotate-anim text-emerald-400" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="100" cy="100" r="85" stroke="currentColor" stroke-width="0.75" stroke-dasharray="4 4" />
                    <circle cx="100" cy="100" r="65" stroke="currentColor" stroke-width="1.25" />
                    <circle cx="100" cy="100" r="45" stroke="currentColor" stroke-width="0.5" stroke-dasharray="2 2" />
                    <line x1="100" y1="15" x2="100" y2="185" stroke="currentColor" stroke-width="0.75" />
                    <line x1="15" y1="100" x2="185" y2="100" stroke="currentColor" stroke-width="0.75" />
                    <!-- Orbiting Nodes -->
                    <circle cx="100" cy="35" r="4" fill="currentColor" />
                    <circle cx="100" cy="165" r="4" fill="currentColor" />
                    <circle cx="35" cy="100" r="4" fill="currentColor" />
                    <circle cx="165" cy="100" r="4" fill="currentColor" />
                </svg>
            </div>

            <!-- Large Floating LUNOU Mascot Background (DIA TERBANG GITU) -->
            <div class="absolute top-1/4 -right-10 z-0 opacity-25 pointer-events-none float-anim">
                <img src="{{ asset('icon/11.png') }}" alt="Floating Mascot" class="w-36 h-36 object-contain">
            </div>

            <!-- Logo (Logo Kotak Tetap Ada & Pakai Warna Putih) -->
            <div class="relative z-10 flex items-center gap-3">
                <img src="{{ asset('logokotak.png') }}" alt="Logo" class="h-10 w-10 object-contain" style="filter: brightness(0) invert(1);">
                <span class="text-lg font-black tracking-tight text-white font-sans">YOIMO WORKSPACE</span>
            </div>

            <!-- Visual Text -->
            <div class="relative z-10 my-auto py-8">
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 border border-white/20 text-emerald-300 rounded-full text-[10px] font-bold tracking-wider uppercase mb-4 font-sans">
                    <!-- Live Glowing Status Dot -->
                    <span class="relative flex h-1.5 w-1.5">
                        <span class="ping-anim absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-400"></span>
                    </span>
                    LUNOU AI ECOSYSTEM
                </span>
                <h1 class="text-3xl font-extrabold tracking-tight text-white leading-tight font-sans">
                    Kelola Proyek & Produktivitas <br>
                    <span class="bg-gradient-to-r from-emerald-400 to-teal-300 bg-clip-text text-transparent font-sans">Berbasis AI</span>
                </h1>
                <p class="mt-4 text-xs font-semibold text-slate-300 leading-relaxed font-sans">
                    Masuk untuk mengakses manajemen linimasa, pelaporan kesehatan, pembagian tugas pintar, serta asisten AI LUNOU.
                </p>
            </div>

            <!-- System Info Footer -->
            <div class="relative z-10 text-[10px] text-slate-400 font-bold flex items-center justify-between font-sans">
                <span>VERSI 2.5 (STABLE)</span>
                <span>© 2026 YOIN GROUP</span>
            </div>
        </div>

        <!-- Sisi Kanan: Form Login (7 Kolom) -->
        <div class="lg:col-span-7 p-8 md:p-12 flex flex-col justify-center bg-white col-span-1">
            <div class="max-w-md w-full mx-auto space-y-8">
                
                <!-- Heading -->
                <div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight font-sans">Selamat Datang Kembali</h2>
                    <p class="text-xs font-semibold text-slate-400 mt-1 font-sans">Masukkan kredensial akun Anda untuk masuk ke sistem.</p>
                </div>

                <!-- Session Status / Alert -->
                @if (session('status'))
                    <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-xs font-semibold text-emerald-700 font-sans">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Validation Error Alert -->
                @if ($errors->any())
                    <div class="p-4 bg-rose-50 border border-rose-100 rounded-2xl text-xs font-semibold text-rose-600 space-y-1 font-sans">
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
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div class="space-y-1.5">
                        <label for="email" class="text-xs font-bold text-slate-400 uppercase tracking-wider font-sans">Email Address</label>
                        <div class="relative">
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                   placeholder="nama@perusahaan.com"
                                   class="w-full pl-4 pr-10 py-3 text-sm bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-semibold text-slate-700 font-sans">
                            <div class="absolute right-3.5 top-3.5 text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="password" class="text-xs font-bold text-slate-400 uppercase tracking-wider font-sans">Password</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-[11px] font-bold text-emerald-600 hover:text-emerald-700 transition-colors font-sans">
                                    Lupa Password?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <input type="password" id="password" name="password" required autocomplete="current-password"
                                   placeholder="••••••••"
                                   class="w-full pl-4 pr-10 py-3 text-sm bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-semibold text-slate-700 font-sans">
                            <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3.5 top-3.5 text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">
                                <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input id="remember_me" type="checkbox" name="remember"
                               class="rounded border-slate-200 text-emerald-600 focus:ring-emerald-500/20 cursor-pointer">
                        <label for="remember_me" class="ml-2 text-xs font-bold text-slate-600 font-sans cursor-pointer">Ingat Saya</label>
                    </div>

                    <!-- Submit -->
                    <button type="submit"
                            class="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-extrabold text-sm rounded-2xl shadow-lg shadow-emerald-500/10 hover:shadow-emerald-500/25 transition-all flex items-center justify-center gap-2 mt-6 cursor-pointer font-sans">
                        <span>Masuk ke Workspace</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </button>
                </form>

            </div>
        </div>

    </div>

    <!-- Password visibility toggle script -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
                `;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                `;
            }
        }
    </script>
</body>
</html>
