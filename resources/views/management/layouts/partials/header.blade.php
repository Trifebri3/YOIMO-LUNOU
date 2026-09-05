<header class="bg-white/95 backdrop-blur-md border-b border-slate-200/80 h-16 flex items-center justify-between px-3 sm:px-6 lg:px-8 shrink-0 sticky top-0 z-30 font-sans select-none">
    
    <!-- Kolom Kiri: Menu Drawer Trigger & Breadcrumbs -->
    <div class="flex items-center gap-2 sm:gap-3 min-w-0">
        <!-- Tombol Menu Drawer (Mobile & iPad) -->
        <button type="button" onclick="toggleSidebar()" 
                class="p-2 text-slate-600 hover:text-emerald-600 hover:bg-slate-100 active:bg-slate-200 rounded-xl xl:hidden shrink-0 transition-colors"
                title="Buka Menu Navigasi">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>

        <!-- Context Breadcrumbs -->
        <div class="min-w-0">
            <div class="flex items-center gap-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                <span class="text-emerald-700 bg-emerald-50 px-2 py-0.2 rounded-md font-black">MANAGEMENT HUB</span>
                @if(request()->routeIs('management.projects.show') && isset($project))
                    <span class="text-slate-300">/</span>
                    <span class="text-emerald-600 truncate">PROYEK</span>
                @elseif(request()->routeIs('management.company.workspace') && isset($company))
                    <span class="text-slate-300">/</span>
                    <span class="text-emerald-600 truncate">WORKSPACE</span>
                @endif
            </div>
            <h2 class="text-xs sm:text-sm font-black text-slate-900 leading-tight truncate">
                @if(request()->routeIs('management.projects.show') && isset($project))
                    {{ $project->name }}
                @elseif(request()->routeIs('management.company.workspace') && isset($company))
                    {{ $company->company_name }}
                @else
                    @yield('title', 'Management Dashboard')
                @endif
            </h2>
        </div>
    </div>

    <!-- Kolom Tengah: Universal Quick Search Bar (Laptop & iPad) -->
    <div class="hidden md:flex items-center flex-1 max-w-md mx-4 lg:mx-8">
        <button type="button" onclick="openQuickNavModal()" 
                class="w-full flex items-center justify-between px-3.5 py-2 bg-slate-100/80 hover:bg-slate-150/80 border border-slate-200/80 rounded-2xl text-xs text-slate-500 font-medium transition-all group shadow-2xs">
            <div class="flex items-center gap-2.5 truncate">
                <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <span class="truncate">Cari proyek, tim, agenda, atau menu...</span>
            </div>
            <kbd class="hidden lg:inline-flex items-center gap-0.5 px-2 py-0.5 bg-white border border-slate-200 rounded-lg text-[10px] font-mono text-slate-500 shadow-2xs shrink-0">
                Ctrl K
            </kbd>
        </button>
    </div>

    <!-- Kolom Kanan: Quick Navigation Pills & Profil -->
    <div class="flex items-center gap-2 sm:gap-3 shrink-0">
        <!-- Quick Search Icon on Mobile -->
        <button type="button" onclick="openQuickNavModal()" 
                class="p-2 text-slate-600 hover:text-emerald-600 hover:bg-slate-100 rounded-xl md:hidden transition-colors"
                title="Cari Cepat (Quick Jump)">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </button>

        <!-- Quick Nav Pills on Desktop -->
        <div class="hidden lg:flex items-center gap-1.5 border-r border-slate-200 pr-3">
            <a href="{{ route('management.dashboard') }}" 
               class="px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ request()->routeIs('management.dashboard') ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600 hover:bg-slate-100' }}">
                <svg class="w-3.5 h-3.5 fill-current text-emerald-600 shrink-0" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/></svg>
                <span>Overview</span>
            </a>

            <!-- Mode Switcher to Member Portal -->
            <a href="{{ route('user.dashboard') }}" 
               class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-800 rounded-xl text-xs font-black transition-all flex items-center gap-1.5 shadow-2xs"
               title="Beralih ke Ruang Kerja Anggota">
                <svg class="w-3.5 h-3.5 fill-current text-indigo-600 shrink-0" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                <span>Ruang Anggota</span>
            </a>
        </div>

        @include('layouts.partials.notifications')

        <!-- User Profile Dropdown with Prominent Log Out (Mobile, iPad, Laptop) -->
        <div class="relative" id="mgmtHeaderProfileContainer">
            <button type="button" onclick="toggleMgmtHeaderMenu()" 
                    class="flex items-center gap-2 p-1 hover:bg-slate-100 active:bg-slate-200 rounded-2xl transition-all cursor-pointer focus:outline-hidden" 
                    id="mgmtHeaderMenuBtn"
                    title="Menu Akun & Keluar">
                @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-8 h-8 rounded-xl object-cover ring-1 ring-slate-200">
                @else
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-600 text-white font-black text-xs flex items-center justify-center shadow-2xs">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                @endif
                <div class="hidden sm:block text-left pr-1">
                    <span class="text-xs font-bold text-slate-800 block leading-tight truncate max-w-[110px]">{{ Auth::user()->name }}</span>
                    <span class="text-[10px] text-emerald-600 font-bold block leading-none">Management</span>
                </div>
                <svg class="w-3.5 h-3.5 text-slate-400 hidden sm:block transition-transform duration-200" id="mgmtHeaderArrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <!-- Dropdown Popover (Super accessible on iPad & HP) -->
            <div id="mgmtHeaderMenuDropdown" 
                 class="hidden absolute right-0 mt-2 w-72 bg-white border border-slate-200/90 rounded-3xl shadow-2xl z-50 p-3 space-y-2.5 animate-in fade-in zoom-in-95 duration-150">
                <!-- User Card Info -->
                <div class="p-3 bg-emerald-50/60 rounded-2xl border border-emerald-100 flex items-center gap-3">
                    @if(Auth::user()->avatar)
                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-10 h-10 rounded-xl object-cover ring-1 ring-emerald-200 shrink-0">
                    @else
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-600 text-white font-black text-sm flex items-center justify-center shadow-xs shrink-0">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <div class="text-xs font-black text-slate-900 truncate">{{ Auth::user()->name }}</div>
                        <div class="text-[11px] text-slate-400 font-semibold truncate">{{ Auth::user()->email }}</div>
                        <div class="mt-1">
                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[9px] font-black rounded-full uppercase">
                                Management
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Navigation Options -->
                <div class="space-y-1 text-xs font-bold">
                    <a href="{{ route('public.portfolio.show', Auth::user()->slug ?? Auth::id()) }}" target="_blank"
                       class="flex items-center justify-between px-3 py-2.5 rounded-xl bg-indigo-50/80 text-indigo-700 hover:bg-indigo-100 transition-colors">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-indigo-600 fill-current shrink-0" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/><path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"/></svg>
                            <span>Portofolio Publik Saya</span>
                        </div>
                        <span class="text-[10px] font-black px-1.5 py-0.5 rounded bg-indigo-200/80 text-indigo-800">Share</span>
                    </a>

                    <a href="{{ route('profile.edit') }}" 
                       class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-slate-700 hover:bg-slate-100 hover:text-emerald-700 transition-colors">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span>Pengaturan Akun & Profil</span>
                    </a>

                    <a href="{{ route('user.dashboard') }}" 
                       class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-indigo-700 hover:bg-indigo-50 transition-colors">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        <span>Beralih ke Ruang Anggota</span>
                    </a>

                    @if(Auth::user()->isSuperAdmin())
                        <a href="{{ route('superadmin.dashboard') }}" 
                           class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-purple-700 hover:bg-purple-50 transition-colors">
                            <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            <span>Portal Superadmin</span>
                        </a>
                    @endif
                </div>

                <!-- Tombol Logout Jelas & Menonjol -->
                <div class="pt-2 border-t border-slate-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="w-full flex items-center justify-between px-3.5 py-2.5 bg-rose-50 hover:bg-rose-100 active:bg-rose-200 text-rose-700 border border-rose-200/90 rounded-2xl text-xs font-black transition-all group shadow-2xs">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center group-hover:bg-rose-200 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                </div>
                                <span class="text-xs font-black">Keluar (Log Out)</span>
                            </div>
                            <svg class="w-4 h-4 text-rose-400 group-hover:text-rose-600 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
function toggleMgmtHeaderMenu() {
    const dropdown = document.getElementById('mgmtHeaderMenuDropdown');
    const arrow = document.getElementById('mgmtHeaderArrow');
    if (dropdown) {
        const isHidden = dropdown.classList.contains('hidden');
        dropdown.classList.toggle('hidden');
        if (arrow) {
            if (isHidden) {
                arrow.classList.add('rotate-180');
            } else {
                arrow.classList.remove('rotate-180');
            }
        }
    }
}

document.addEventListener('click', function(e) {
    const container = document.getElementById('mgmtHeaderProfileContainer');
    const dropdown = document.getElementById('mgmtHeaderMenuDropdown');
    const arrow = document.getElementById('mgmtHeaderArrow');
    if (container && dropdown && !container.contains(e.target)) {
        dropdown.classList.add('hidden');
        if (arrow) arrow.classList.remove('rotate-180');
    }
});
</script>
