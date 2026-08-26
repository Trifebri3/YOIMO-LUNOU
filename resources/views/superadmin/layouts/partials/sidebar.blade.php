<aside id="mobile-sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-100 flex flex-col justify-between h-screen shrink-0 font-sans hidden xl:flex xl:sticky xl:top-0">
    <div class="p-6">
        <!-- Logo Area -->
        <div class="flex items-center justify-between gap-3 mb-8">
            <div class="flex items-center gap-3">
                <img src="{{ asset('logopanjang.png') }}" alt="Logo" class="h-9 w-auto hidden sm:block">
                <img src="{{ asset('logokotak.png') }}" alt="Logo Icon" class="h-9 w-9 sm:hidden object-contain">
            </div>
            <button type="button" onclick="toggleSidebar()" class="p-1.5 text-slate-500 hover:bg-slate-100 rounded-xl xl:hidden" title="Close Menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Workspace Selector -->
        <div class="mb-6">
            <span class="text-[11px] font-bold text-slate-400 tracking-wider uppercase">Workspace Aktif</span>
            <div class="mt-2 flex items-center justify-between px-3.5 py-2.5 bg-slate-50 border border-slate-200/60 rounded-xl text-sm font-medium text-slate-700">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span>Ruang Super Admin</span>
                </div>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>

        <!-- Level & Activity Pill -->
        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100 rounded-2xl p-3.5 mb-8">
            <div class="flex items-center justify-between text-xs font-semibold text-emerald-800 mb-2">
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    Super Master
                </span>
                <span class="text-teal-700 bg-emerald-100/70 px-2 py-0.5 rounded-full text-[10px] tracking-wide">SYSTEM ROOT</span>
            </div>
            <div class="w-full bg-emerald-200/50 rounded-full h-1.5 mb-1.5">
                <div class="bg-emerald-500 h-1.5 rounded-full w-full"></div>
            </div>
            <div class="flex justify-between text-[10px] text-slate-500 font-medium">
                <span>Security: High</span>
                <span>Active 2026</span>
            </div>
        </div>

        <!-- Navigasi Utama -->
        <div>
            <span class="text-[11px] font-bold text-slate-400 tracking-wider uppercase mb-3 block">Navigasi</span>
            <nav class="space-y-1.5">
                <a href="{{ route('superadmin.dashboard') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('superadmin.dashboard') ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/50 shadow-sm shadow-emerald-50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('superadmin.dashboard') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- Chat Room Link -->
                <a href="{{ route('chat.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('chat.index') ? 'bg-indigo-50 text-indigo-700 border border-indigo-200/50 shadow-sm shadow-indigo-50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('chat.index') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <span>LUNOU Chat Room</span>
                </a>

<a href="{{ route('superadmin.users.index') }}"
   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('superadmin.users.*') ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/50 shadow-sm shadow-emerald-50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
    <svg class="w-4 h-4 {{ request()->routeIs('superadmin.users.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
    </svg>
    <span>Manajemen User</span>
</a>

<a href="{{ route('superadmin.company.index') }}"
   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('superadmin.company.*') ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/50 shadow-sm shadow-emerald-50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
    <svg class="w-4 h-4 {{ request()->routeIs('superadmin.company.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
    </svg>
    <span>Profil Perusahaan</span>
</a>

<a href="{{ route('ai-settings.index') }}"
   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('ai-settings.*') ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/50 shadow-sm shadow-emerald-50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
    <svg class="w-4 h-4 {{ request()->routeIs('ai-settings.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364.364l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
    </svg>
    <span>Pengaturan AI</span>
</a>

                <a href="{{ route('superadmin.notification-settings.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('superadmin.notification-settings.*') ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/50 shadow-sm shadow-emerald-50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('superadmin.notification-settings.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <span>Notifikasi & WA</span>
                </a>
            </nav>
        </div>
    </div>

    <!-- Node Keamanan Footer Sidebar -->
    <div class="p-6 border-t border-slate-100">
        <div class="flex items-center justify-between text-[11px] font-bold">
            <span class="text-slate-400 uppercase tracking-wider">Node Keamanan</span>
            <span class="text-emerald-600 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                KONEKSI AKTIF
            </span>
        </div>
    </div>
</aside>
