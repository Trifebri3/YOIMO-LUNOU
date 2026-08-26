<header class="bg-white border-b border-slate-100 h-16 flex items-center justify-between px-4 sm:px-8 shrink-0 sticky top-0 z-30 font-sans">
    <!-- Kolom Kiri: Judul Halaman & Mobile Breadcrumb -->
    <div class="flex items-center gap-3">
        <!-- Tombol Mobile Menu Trigger -->
        <button type="button" onclick="toggleSidebar()" class="p-2 text-slate-500 hover:bg-slate-100 rounded-xl xl:hidden">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>

        <div>
            <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider block leading-tight">MEMBER PORTAL</span>
            <h2 class="text-sm font-black text-slate-900 leading-tight">@yield('title', 'Workspace')</h2>
        </div>
    </div>

    <!-- Kolom Kanan: Status & User Info -->
    <div class="flex items-center gap-4">
        <!-- Penanda Tanggal Aktif -->
        <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-slate-50 border border-slate-100 rounded-xl text-xs font-bold text-slate-600">
            <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <span>{{ now()->format('d M Y') }}</span>
        </div>

        @include('layouts.partials.notifications')

        <!-- Profil Link -->
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 p-1.5 hover:bg-slate-50 rounded-xl transition-all">
            @if(Auth::user()->avatar)
                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-7 h-7 rounded-full object-cover ring-1 ring-slate-200">
            @else
                <div class="w-7 h-7 rounded-full bg-indigo-600 text-white font-bold text-[10px] flex items-center justify-center">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
            @endif
            <span class="text-xs font-bold text-slate-700 hidden sm:block">{{ Auth::user()->name }}</span>
        </a>
    </div>
</header>