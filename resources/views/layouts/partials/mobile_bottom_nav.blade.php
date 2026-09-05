<!-- Smart Bottom Navigation Bar for HP and iPad / Tablet (xl:hidden) -->
@php
    $isMgmt = request()->is('management*');
    $homeRoute = $isMgmt ? route('management.dashboard') : route('user.dashboard');
    $isHome = $isMgmt ? request()->routeIs('management.dashboard') : request()->routeIs('user.dashboard');
    $isProjects = request()->routeIs('*.projects.*');
    $isChat = request()->routeIs('chat.*');
    $isWorkspace = request()->routeIs('*.company.workspace');
@endphp

<nav id="mobile_bottom_nav" class="fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-md border-t border-slate-200/80 z-40 flex items-center justify-around py-2 px-3 sm:px-8 xl:hidden shadow-[0_-6px_24px_rgba(0,0,0,0.07)] rounded-t-2xl font-sans select-none">
    
    <!-- 1. Home / Beranda -->
    <a href="{{ $homeRoute }}" 
       class="flex flex-col items-center justify-center min-w-[56px] py-1 transition-all group {{ $isHome ? 'text-indigo-600 font-black' : 'text-slate-500 hover:text-indigo-600' }}">
        <div class="relative">
            <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isHome ? '2.5' : '1.8' }}" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            @if($isHome)
                <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1.5 h-1.5 bg-indigo-600 rounded-full"></span>
            @endif
        </div>
        <span class="text-[10px] sm:text-xs tracking-tight mt-0.5">Home</span>
    </a>

    <!-- 2. Proyek Saya -->
    @php
        $firstProj = isset($navProjects) && $navProjects->count() > 0 ? $navProjects->first() : null;
        $projUrl = $firstProj 
            ? ($isMgmt ? route('management.projects.show', $firstProj->id) : route('user.projects.show', $firstProj->id)) 
            : $homeRoute;
    @endphp
    <button type="button" onclick="openQuickNavModal()" 
       class="flex flex-col items-center justify-center min-w-[56px] py-1 transition-all group {{ $isProjects ? 'text-indigo-600 font-black' : 'text-slate-500 hover:text-indigo-600' }}">
        <div class="relative">
            <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isProjects ? '2.5' : '1.8' }}" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
            </svg>
            @if(isset($navProjects) && $navProjects->count() > 0)
                <span class="absolute -top-1 -right-2 px-1.5 py-0.2 bg-slate-100 text-slate-700 text-[8px] font-black rounded-full border border-slate-200">
                    {{ $navProjects->count() }}
                </span>
            @endif
        </div>
        <span class="text-[10px] sm:text-xs tracking-tight mt-0.5">Proyek</span>
    </button>

    <!-- 3. CENTER BUTTON: QUICK NAV JUMPER (ELEVATED GLOWING BUTTON) -->
    <div class="relative -top-3">
        <button type="button" onclick="openQuickNavModal()" 
                class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-gradient-to-tr from-indigo-600 to-blue-500 hover:from-indigo-700 hover:to-blue-600 text-white flex items-center justify-center shadow-lg shadow-indigo-500/40 ring-4 ring-white active:scale-95 transition-all group"
                title="Cari proyek & menu cepat">
            <svg class="w-6 h-6 transition-transform group-hover:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </button>
        <span class="text-[9px] font-bold text-slate-500 uppercase tracking-tighter block text-center mt-1">Cari / Nav</span>
    </div>

    <!-- 4. LUNOU Chat Room -->
    <a href="{{ route('chat.index') }}" 
       class="flex flex-col items-center justify-center min-w-[56px] py-1 transition-all group {{ $isChat ? 'text-indigo-600 font-black' : 'text-slate-500 hover:text-indigo-600' }}">
        <div class="relative">
            <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isChat ? '2.5' : '1.8' }}" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            @if(isset($navUnreadChatCount) && $navUnreadChatCount > 0)
                <span class="absolute -top-1 -right-2 px-1.5 py-0.2 bg-rose-500 text-white text-[8px] font-black rounded-full animate-pulse ring-1 ring-white">
                    {{ $navUnreadChatCount }}
                </span>
            @endif
        </div>
        <span class="text-[10px] sm:text-xs tracking-tight mt-0.5">Chat</span>
    </a>

    <!-- 5. Mega Menu Drawer Trigger -->
    <button type="button" onclick="toggleSidebar()" 
            class="flex flex-col items-center justify-center min-w-[56px] py-1 text-slate-500 hover:text-indigo-600 transition-all group">
        <div class="relative p-0.5">
            <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
            </svg>
        </div>
        <span class="text-[10px] sm:text-xs tracking-tight mt-0.5">Menu</span>
    </button>

</nav>
