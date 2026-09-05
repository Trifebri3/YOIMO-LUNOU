<!-- Universal Slide-Out Mega Navigation Drawer (Mobile & iPad / Tablet) -->
<div id="mobile-mega-drawer-backdrop" 
     onclick="toggleSidebar()" 
     class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-50 hidden xl:hidden transition-opacity duration-300"></div>

<aside id="mobile-sidebar" 
       class="fixed inset-y-0 left-0 z-50 w-80 sm:w-96 bg-white border-r border-slate-200/80 flex flex-col justify-between h-screen shrink-0 font-sans select-none hidden xl:flex xl:sticky xl:top-0 shadow-2xl xl:shadow-none overflow-hidden transition-all duration-300">
    
    <!-- Top Fixed Header inside Drawer -->
    <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between shrink-0 bg-slate-50/50">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
            <img src="{{ asset('logopanjang.png') }}" alt="Logo" class="h-7 sm:h-8 w-auto">
        </a>
        <button type="button" onclick="toggleSidebar()" 
                class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 rounded-xl xl:hidden transition-colors"
                title="Tutup Menu">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <!-- Scrollable Content Area -->
    <div class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-5 divide-y divide-slate-100">
        
        <!-- 1. Profil Pengguna & Gamifikasi -->
        <div class="space-y-3 pb-2">
            <div class="flex items-center gap-3">
                @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-11 h-11 rounded-2xl object-cover ring-2 ring-indigo-500/20 shadow-xs">
                @else
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-indigo-600 to-blue-600 text-white font-black text-sm flex items-center justify-center shadow-xs">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                @endif
                <div class="min-w-0 flex-1">
                    <h4 class="text-xs sm:text-sm font-black text-slate-900 truncate">{{ Auth::user()->name }}</h4>
                    <p class="text-[11px] text-slate-400 font-semibold truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>

            <!-- Quick Actions: Profil & Prominent Log Out (Langsung terlihat tanpa scroll di HP & iPad) -->
            <div class="flex items-center gap-2 pt-1">
                <a href="{{ route('profile.edit') }}" 
                   class="flex-1 py-2 px-3 bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-700 text-xs font-bold rounded-xl transition-all flex items-center justify-center gap-1.5 shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span>Profil</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" 
                            class="w-full py-2 px-3 bg-rose-50 hover:bg-rose-100 active:bg-rose-200 text-rose-700 border border-rose-200/90 text-xs font-black rounded-xl flex items-center justify-center gap-1.5 transition-all shadow-2xs group">
                        <svg class="w-3.5 h-3.5 text-rose-600 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        <span>Log Out</span>
                    </button>
                </form>
            </div>

            <!-- Gamification Level & Streak Bar -->
            @if(isset($navUserPoint))
                <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100/70 rounded-2xl p-3">
                    <div class="flex items-center justify-between text-xs font-bold text-emerald-950 mb-1">
                        <span class="flex items-center gap-1">
                            <span class="text-emerald-600">★</span> Lvl {{ $navUserPoint->level }}
                        </span>
                        <span class="text-amber-700 bg-amber-100/80 px-2 py-0.2 rounded-full text-[9px] font-black tracking-wide">
                            STREAK {{ $navUserPoint->login_streak }} HARI
                        </span>
                    </div>
                    <div class="w-full bg-emerald-200/50 rounded-full h-1.5 mb-1">
                        <div class="bg-emerald-500 h-1.5 rounded-full transition-all" style="width: {{ (($navUserPoint->total_points % 200) / 200) * 100 }}%"></div>
                    </div>
                    <div class="flex justify-between text-[9px] text-slate-400 font-semibold">
                        <span>XP: {{ $navUserPoint->total_points }}</span>
                        <span>Level Berikutnya: {{ $navUserPoint->level * 200 }}</span>
                    </div>
                </div>
            @endif

            <!-- Quick Search Button inside Drawer -->
            <button type="button" onclick="toggleSidebar(); openQuickNavModal();" 
                    class="w-full flex items-center justify-between px-3.5 py-2.5 bg-slate-100/80 hover:bg-indigo-50 border border-slate-200/70 hover:border-indigo-200 rounded-2xl text-xs font-bold text-slate-600 hover:text-indigo-700 transition-all">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span>Cari Cepat (Proyek, Menu)</span>
                </div>
                <span class="text-[9px] px-1.5 py-0.5 bg-white border border-slate-200 rounded-md font-mono text-slate-500">Ctrl+K</span>
            </button>
        </div>

        <!-- 2. Workspace Perusahaan Aktif -->
        <div class="pt-4 space-y-2">
            <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider block">WORKSPACE PERUSAHAAN</span>
            @if(isset($navCompanies) && $navCompanies->count() > 0)
                <div class="relative" id="drawerWorkspaceDropdownContainer">
                    <button type="button" 
                            onclick="toggleDrawerCompanyDropdown()" 
                            class="w-full flex items-center justify-between p-3 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 transition-all">
                        <div class="flex items-center gap-2.5 truncate">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 shrink-0"></span>
                            <span class="truncate font-black">{{ $navActiveCompany->company_name ?? 'Pilih Perusahaan' }}</span>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div id="drawerCompanyDropdownMenu" class="hidden mt-1.5 p-1.5 bg-white border border-slate-200 rounded-2xl shadow-lg space-y-1">
                        @foreach($navCompanies as $c)
                            @php
                                $cUrl = request()->is('management*') 
                                    ? route('management.company.workspace', $c->id) 
                                    : route('user.company.workspace', $c->id);
                            @endphp
                            <a href="{{ $cUrl }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition-all {{ (isset($navActiveCompany) && $navActiveCompany->id === $c->id) ? 'bg-indigo-50 text-indigo-800' : 'text-slate-600 hover:bg-slate-50' }}">
                                <span class="truncate">{{ $c->company_name }}</span>
                                @if(isset($navActiveCompany) && $navActiveCompany->id === $c->id)
                                    <span class="text-indigo-600">✓</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="p-3 bg-slate-50 border border-dashed border-slate-200 rounded-2xl text-[11px] text-slate-400">
                    Belum tergabung perusahaan
                </div>
            @endif
        </div>

        <!-- 3. Navigasi Utama -->
        <div class="pt-4 space-y-1">
            <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider block mb-2">NAVIGASI UTAMA</span>
            
            <!-- Ruang Pribadi -->
            <a href="{{ route('user.dashboard') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('user.dashboard') ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('user.dashboard') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span>Ruang Pribadi / Dashboard</span>
            </a>

            <!-- Workspace Company -->
            @if(isset($navActiveCompany) && $navActiveCompany)
                @php
                    $activeWsUrl = request()->is('management*') 
                        ? route('management.company.workspace', $navActiveCompany->id) 
                        : route('user.company.workspace', $navActiveCompany->id);
                @endphp
                <a href="{{ $activeWsUrl }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('*.company.workspace') ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('*.company.workspace') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span>Dashboard Workspace</span>
                </a>
            @endif

            <!-- LUNOU Chat Room -->
            <a href="{{ route('chat.index') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('chat.*') ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 {{ request()->routeIs('chat.*') ? 'text-purple-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    <span>LUNOU Chat Room</span>
                </div>
                @if(isset($navUnreadChatCount) && $navUnreadChatCount > 0)
                    <span class="px-2 py-0.2 bg-rose-500 text-white text-[10px] font-black rounded-full animate-pulse">
                        {{ $navUnreadChatCount }}
                    </span>
                @endif
            </a>

            <!-- Pengaturan AI -->
            <a href="{{ route('ai-settings.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('ai-settings.*') ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('ai-settings.*') ? 'text-amber-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364.364l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                <span>Pengaturan AI Asisten</span>
            </a>

            <!-- Profil Pengguna -->
            <a href="{{ route('profile.edit') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('profile.*') ? 'bg-slate-100 text-slate-800' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span>Profil & Akun</span>
            </a>
        </div>

        <!-- 4. Daftar Proyek Saya -->
        @if(isset($navProjects) && $navProjects->count() > 0)
            <div class="pt-4 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">PROYEK SAYA ({{ $navProjects->count() }})</span>
                    <button type="button" onclick="toggleSidebar(); openQuickNavModal();" class="text-[10px] text-indigo-600 font-bold hover:underline">
                        Lihat Semua
                    </button>
                </div>
                <div class="space-y-1 max-h-48 overflow-y-auto pr-1">
                    @foreach($navProjects as $pj)
                        @php
                            $pjUrl = request()->is('management*') 
                                ? route('management.projects.show', $pj->id) 
                                : route('user.projects.show', $pj->id);
                            $isCur = request()->routeIs('*.projects.show') && request()->route('project') && (request()->route('project')->id == $pj->id || request()->route('project') == $pj->id);
                        @endphp
                        <a href="{{ $pjUrl }}" 
                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition-all {{ $isCur ? 'bg-indigo-50 text-indigo-900 border border-indigo-200' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <div class="flex items-center gap-2 truncate">
                                <span class="w-2 h-2 rounded-full {{ $isCur ? 'bg-indigo-600' : 'bg-slate-300' }} shrink-0"></span>
                                <span class="truncate">{{ $pj->name }}</span>
                            </div>
                            <span class="text-[9px] font-black px-1.5 py-0.2 bg-slate-100 text-slate-600 rounded">
                                {{ $pj->progress_percentage }}%
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 5. Mode Switcher Banner (Jika Manager/Admin) -->
        @if(Auth::user()->isManagement() || Auth::user()->isSuperAdmin())
            <div class="pt-4">
                <div class="bg-gradient-to-br from-slate-900 to-indigo-950 text-white rounded-2xl p-4 space-y-3 shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="text-[9px] font-black uppercase tracking-widest text-indigo-300">STATUS PORTAL AKTIF</span>
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    </div>
                    <div>
                        <h5 class="text-xs font-black">{{ request()->is('management*') ? 'Sedang di Management Hub' : 'Sedang di Member Portal' }}</h5>
                        <p class="text-[10px] text-slate-300 mt-0.5">Beralih mode untuk mengatur tugas atau mengelola proyek.</p>
                    </div>
                    @if(request()->is('management*'))
                        <a href="{{ route('user.dashboard') }}" 
                           class="block w-full py-2 bg-white hover:bg-slate-100 text-indigo-950 text-center text-xs font-black rounded-xl transition-all shadow-xs">
                            Masuk Ruang Anggota (Member)
                        </a>
                    @else
                        <a href="{{ route('management.dashboard') }}" 
                           class="block w-full py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-center text-xs font-black rounded-xl transition-all shadow-xs">
                            Masuk Mode Pengelola (Management)
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Bottom Fixed Area inside Drawer: Logout -->
    <div class="p-4 border-t border-slate-100 shrink-0 bg-slate-50/50 flex items-center justify-between">
        <div class="text-[11px] font-bold text-slate-500">
            <span>Versi Yoimo 2.6</span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-rose-600 hover:bg-rose-50 rounded-xl transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>

<script>
    function toggleDrawerCompanyDropdown() {
        const menu = document.getElementById('drawerCompanyDropdownMenu');
        if (menu) menu.classList.toggle('hidden');
    }
</script>
