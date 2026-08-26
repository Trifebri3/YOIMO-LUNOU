<header class="bg-white/80 backdrop-blur-md border-b border-slate-100 sticky top-0 z-30 px-4 md:px-8 py-3.5 font-sans">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <!-- Tombol Mobile Menu Trigger -->
            <button type="button" onclick="toggleSidebar()" class="p-2 text-slate-500 hover:bg-slate-100 rounded-xl xl:hidden">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>

            <!-- Info Workspace & Role -->
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                <span>Portal:</span>
                <span class="text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md">Management Hub</span>
                <span class="text-slate-300 hidden sm:inline">•</span>
                <span class="hidden sm:inline">Jabatan:</span>
                <span class="text-slate-800 font-bold hidden sm:inline">{{ Auth::user()->position ?? 'Manager Operasional' }}</span>
            </div>
        </div>

        <!-- Profil & Logout -->
        <div class="flex items-center gap-4">
            <div class="text-right">
                <div class="text-sm font-bold text-slate-800">{{ Auth::user()->name }}</div>
                <div class="text-[11px] font-medium text-indigo-600">{{ Auth::user()->email }}</div>
            </div>

            @if(Auth::user()->avatar)
                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="h-9 w-9 rounded-full object-cover ring-2 ring-indigo-400 ring-offset-2">
            @else
                <div class="h-9 w-9 rounded-full bg-slate-900 text-white font-bold text-xs flex items-center justify-center ring-2 ring-indigo-400 ring-offset-2">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
            @endif

            @include('layouts.partials.notifications')

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</header>
