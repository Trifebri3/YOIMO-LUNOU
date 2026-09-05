<header class="bg-white/95 backdrop-blur-md border-b border-slate-200/80 h-16 flex items-center justify-between px-3 sm:px-6 lg:px-8 shrink-0 sticky top-0 z-30 font-sans select-none">
    
    <!-- Kolom Kiri: Menu Drawer Trigger, Back Button & Breadcrumbs -->
    <div class="flex items-center gap-2 sm:gap-3 min-w-0">
        <!-- Tombol Menu Drawer (Mobile & iPad) -->
        <button type="button" onclick="toggleSidebar()" 
                class="p-2 text-slate-600 hover:text-indigo-600 hover:bg-slate-100 active:bg-slate-200 rounded-xl xl:hidden shrink-0 transition-colors"
                title="Buka Menu Navigasi">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>

        <!-- Context Breadcrumbs & Page Info -->
        <div class="min-w-0">
            <div class="flex items-center gap-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                <a href="{{ route('user.dashboard') }}" class="hover:text-indigo-600 transition-colors">HOME</a>
                @if(request()->routeIs('user.projects.show') && isset($project))
                    <span class="text-slate-300">/</span>
                    <span class="text-indigo-600 truncate">PROYEK</span>
                @elseif(request()->routeIs('user.company.workspace') && isset($company))
                    <span class="text-slate-300">/</span>
                    <span class="text-indigo-600 truncate">WORKSPACE</span>
                @elseif(request()->routeIs('chat.index'))
                    <span class="text-slate-300">/</span>
                    <span class="text-purple-600 truncate">CHAT</span>
                @elseif(request()->routeIs('ai-settings.*'))
                    <span class="text-slate-300">/</span>
                    <span class="text-amber-600 truncate">AI SETTING</span>
                @else
                    <span class="text-slate-300">/</span>
                    <span class="text-indigo-600">RUANG PRIBADI</span>
                @endif
            </div>
            <h2 class="text-xs sm:text-sm font-black text-slate-900 leading-tight truncate">
                @if(request()->routeIs('user.projects.show') && isset($project))
                    {{ $project->name }}
                @elseif(request()->routeIs('user.company.workspace') && isset($company))
                    {{ $company->company_name }}
                @else
                    @yield('title', 'Workspace')
                @endif
            </h2>
        </div>
    </div>

    <!-- Kolom Tengah: Universal Quick Search Bar (Laptop & iPad) -->
    <div class="hidden md:flex items-center flex-1 max-w-md mx-4 lg:mx-8">
        <button type="button" onclick="openQuickNavModal()" 
                class="w-full flex items-center justify-between px-3.5 py-2 bg-slate-100/80 hover:bg-slate-150/80 border border-slate-200/80 rounded-2xl text-xs text-slate-500 font-medium transition-all group shadow-2xs">
            <div class="flex items-center gap-2.5 truncate">
                <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <span class="truncate">Cari proyek, menu, atau tugas...</span>
            </div>
            <kbd class="hidden lg:inline-flex items-center gap-0.5 px-2 py-0.5 bg-white border border-slate-200 rounded-lg text-[10px] font-mono text-slate-500 shadow-2xs shrink-0">
                Ctrl K
            </kbd>
        </button>
    </div>

    <!-- Kolom Kanan: Quick Action Pills, Notifikasi & User Menu -->
    <div class="flex items-center gap-2 sm:gap-3 shrink-0">
        <!-- Quick Search Icon Trigger on Mobile -->
        <button type="button" onclick="openQuickNavModal()" 
                class="p-2 text-slate-600 hover:text-indigo-600 hover:bg-slate-100 rounded-xl md:hidden transition-colors"
                title="Cari Cepat (Quick Jump)">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </button>

        <!-- Quick Nav Pills (Visible on Desktop / Laptop) -->
        <div class="hidden lg:flex items-center gap-1.5 border-r border-slate-200 pr-3">
            <!-- Home Link -->
            <a href="{{ route('user.dashboard') }}" 
               class="px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ request()->routeIs('user.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">
                <svg class="w-3.5 h-3.5 fill-current text-slate-500 shrink-0" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                <span>Home</span>
            </a>

            <!-- Quick Projects Button -->
            <button type="button" onclick="openQuickNavModal()" 
               class="px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ request()->routeIs('*.projects.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">
                <svg class="w-3.5 h-3.5 fill-current text-slate-500 shrink-0" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/></svg>
                <span>Proyek</span>
                @if(isset($navProjects) && $navProjects->count() > 0)
                    <span class="px-1.5 py-0.2 bg-slate-200 text-slate-700 text-[10px] font-black rounded-md">
                        {{ $navProjects->count() }}
                    </span>
                @endif
            </button>

            <!-- LUNOU Chat Link -->
            <a href="{{ route('chat.index') }}" 
               class="px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ request()->routeIs('chat.*') ? 'bg-purple-50 text-purple-700' : 'text-slate-600 hover:bg-slate-100' }}">
                <svg class="w-3.5 h-3.5 fill-current text-purple-600 shrink-0" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zm-4 0h-2v2h2V9z" clip-rule="evenodd"/></svg>
                <span>Chat</span>
                @if(isset($navUnreadChatCount) && $navUnreadChatCount > 0)
                    <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                @endif
            </a>

            <!-- Mode Switcher (Jika punya hak Management) -->
            @if(Auth::user()->isManagement() || Auth::user()->isSuperAdmin())
                <a href="{{ route('management.dashboard') }}" 
                   class="px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-black transition-all flex items-center gap-1.5 shadow-2xs"
                   title="Masuk ke Mode Pengelola">
                    <svg class="w-3.5 h-3.5 fill-current text-emerald-700 shrink-0" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
                    <span>Mode Pengelola</span>
                </a>
            @endif
        </div>

        @include('layouts.partials.notifications')

        <!-- User Profile Dropdown with Prominent Log Out (Mobile, iPad, Laptop) -->
        <div class="relative" id="userHeaderProfileContainer">
            <button type="button" onclick="toggleUserHeaderMenu()" 
                    class="flex items-center gap-2 p-1 hover:bg-slate-100 active:bg-slate-200 rounded-2xl transition-all cursor-pointer focus:outline-hidden" 
                    id="userHeaderMenuBtn"
                    title="Menu Akun & Keluar">
                @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-8 h-8 rounded-xl object-cover ring-1 ring-slate-200">
                @else
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-600 text-white font-black text-xs flex items-center justify-center shadow-2xs">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                @endif
                <div class="hidden sm:block text-left pr-1">
                    <span class="text-xs font-bold text-slate-800 block leading-tight truncate max-w-[110px]">{{ Auth::user()->name }}</span>
                    <span class="text-[10px] text-slate-400 font-semibold block leading-none">Lvl {{ $navUserPoint->level ?? 1 }}</span>
                </div>
                <svg class="w-3.5 h-3.5 text-slate-400 hidden sm:block transition-transform duration-200" id="userHeaderArrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <!-- Dropdown Popover (Super accessible on iPad & HP) -->
            <div id="userHeaderMenuDropdown" 
                 class="hidden absolute right-0 mt-2 w-72 bg-white border border-slate-200/90 rounded-3xl shadow-2xl z-50 p-3 space-y-2.5 animate-in fade-in zoom-in-95 duration-150">
                <!-- User Card Info -->
                <div class="p-3 bg-slate-50/80 rounded-2xl border border-slate-100 flex items-center gap-3">
                    @if(Auth::user()->avatar)
                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-10 h-10 rounded-xl object-cover ring-1 ring-slate-200 shrink-0">
                    @else
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-600 text-white font-black text-sm flex items-center justify-center shadow-xs shrink-0">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <div class="text-xs font-black text-slate-900 truncate">{{ Auth::user()->name }}</div>
                        <div class="text-[11px] text-slate-400 font-semibold truncate">{{ Auth::user()->email }}</div>
                        <div class="mt-1 flex items-center gap-1.5">
                            <span class="px-2 py-0.5 bg-indigo-100/80 text-indigo-700 text-[9px] font-black rounded-full uppercase">
                                {{ Auth::user()->role }}
                            </span>
                            @if(isset($navUserPoint))
                                <span class="text-[9px] font-bold text-emerald-600 flex items-center gap-1">
                                    <svg class="w-2.5 h-2.5 fill-current text-emerald-500" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <span>Lvl {{ $navUserPoint->level }}</span>
                                </span>
                            @endif
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
                       class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-slate-700 hover:bg-slate-100 hover:text-indigo-600 transition-colors">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span>Pengaturan Akun & Profil</span>
                    </a>

                    @if(Auth::user()->isManagement() || Auth::user()->isSuperAdmin())
                        <a href="{{ route('management.dashboard') }}" 
                           class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-emerald-700 hover:bg-emerald-50 transition-colors">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                            <span>Beralih ke Management Hub</span>
                        </a>
                    @endif

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
function toggleUserHeaderMenu() {
    const dropdown = document.getElementById('userHeaderMenuDropdown');
    const arrow = document.getElementById('userHeaderArrow');
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
    const container = document.getElementById('userHeaderProfileContainer');
    const dropdown = document.getElementById('userHeaderMenuDropdown');
    const arrow = document.getElementById('userHeaderArrow');
    if (container && dropdown && !container.contains(e.target)) {
        dropdown.classList.add('hidden');
        if (arrow) arrow.classList.remove('rotate-180');
    }
});
</script>