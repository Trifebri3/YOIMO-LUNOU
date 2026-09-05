<!-- Universal Quick Navigation & Command Palette Modal (Ctrl + K) -->
<div id="quickNavModal" class="fixed inset-0 z-[10000] hidden items-start justify-center pt-12 sm:pt-20 px-4 bg-slate-950/70 backdrop-blur-sm animate-fade-in font-sans">
    <div class="bg-white border border-slate-200/80 rounded-3xl max-w-2xl w-full overflow-hidden shadow-2xl transform transition-all flex flex-col max-h-[85vh]">
        <!-- Search Bar Header -->
        <div class="p-4 sm:p-5 border-b border-slate-150 flex items-center gap-3 bg-slate-50/70">
            <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <div class="flex-1 min-w-0">
                <input type="text" id="quickNavSearchInput" 
                       placeholder="Cari proyek, menu, atau fitur... (Ketik nama proyek/menu)" 
                       class="w-full bg-transparent border-none text-sm sm:text-base font-bold text-slate-900 placeholder:text-slate-400 focus:outline-hidden focus:ring-0"
                       autocomplete="off"
                       oninput="filterQuickNavItems(this.value)">
            </div>
            <div class="hidden sm:flex items-center gap-1.5 text-[10px] font-bold text-slate-400 uppercase bg-white border border-slate-200 px-2.5 py-1 rounded-xl shrink-0">
                <kbd class="font-mono text-slate-600">ESC</kbd> <span>Tutup</span>
            </div>
            <button type="button" onclick="closeQuickNavModal()" class="sm:hidden p-2 text-slate-400 hover:text-slate-700 rounded-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Scrollable Search Results Area -->
        <div class="p-3 sm:p-4 overflow-y-auto flex-1 space-y-4" id="quickNavResultsContainer">

            <!-- Section: Proyek Saya -->
            @if(isset($navProjects) && $navProjects->count() > 0)
                <div class="quick-nav-group" data-group="projects">
                    <div class="flex items-center justify-between px-3 py-1 text-[11px] font-black uppercase text-indigo-700 tracking-wider">
                        <span class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 fill-current text-indigo-600 shrink-0" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/></svg> Proyek Saya ({{ $navProjects->count() }})</span>
                        <span class="text-[10px] text-slate-400 font-semibold">Klik untuk buka lembar kerja</span>
                    </div>
                    <div class="space-y-1 mt-1">
                        @foreach($navProjects as $p)
                            @php
                                $projTargetUrl = Auth::user()->isManagement() && request()->is('management*') 
                                    ? route('management.projects.show', $p->id) 
                                    : route('user.projects.show', $p->id);
                            @endphp
                            <a href="{{ $projTargetUrl }}" 
                               class="quick-nav-item flex items-center justify-between p-3 hover:bg-indigo-50/70 border border-transparent hover:border-indigo-100 rounded-2xl transition-all group"
                               data-search="{{ strtolower($p->name . ' ' . $p->category . ' ' . ($p->company->company_name ?? '')) }}">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-black text-xs shrink-0 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                        {{ strtoupper(substr($p->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-800 truncate group-hover:text-indigo-950">{{ $p->name }}</p>
                                        <p class="text-[10px] text-slate-400 font-semibold truncate">{{ $p->category }} • {{ $p->company->company_name ?? 'Workspace' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0 ml-3">
                                    <span class="text-[10px] font-black px-2 py-0.5 bg-slate-100 group-hover:bg-indigo-100 text-slate-700 group-hover:text-indigo-800 rounded-lg">
                                        {{ $p->progress_percentage }}%
                                    </span>
                                    <svg class="w-4 h-4 text-slate-300 group-hover:text-indigo-600 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Section: Navigasi Menu Cepat -->
            <div class="quick-nav-group" data-group="menus">
                <div class="px-3 py-1 text-[11px] font-black uppercase text-slate-400 tracking-wider">
                    <span class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 fill-current text-slate-400 shrink-0" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg> Navigasi & Fitur Utama</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 mt-1">
                    <!-- Dashboard Pribadi -->
                    <a href="{{ route('user.dashboard') }}" 
                       class="quick-nav-item flex items-center gap-3 p-3 hover:bg-slate-50 border border-transparent hover:border-slate-200 rounded-2xl transition-all group"
                       data-search="home beranda ruang pribadi dashboard tugas saya">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Ruang Pribadi</span>
                            <span class="text-[10px] text-slate-400 block">Tugas harian, pencapaian & streak</span>
                        </div>
                    </a>

                    <!-- Workspace Perusahaan -->
                    @if(isset($navActiveCompany) && $navActiveCompany)
                        @php
                            $wsUrl = Auth::user()->isManagement() && request()->is('management*')
                                ? route('management.company.workspace', $navActiveCompany->id)
                                : route('user.company.workspace', $navActiveCompany->id);
                        @endphp
                        <a href="{{ $wsUrl }}" 
                           class="quick-nav-item flex items-center gap-3 p-3 hover:bg-slate-50 border border-transparent hover:border-slate-200 rounded-2xl transition-all group"
                           data-search="workspace company perusahaan tim proyek {{ strtolower($navActiveCompany->company_name) }}">
                            <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            <div class="truncate">
                                <span class="text-xs font-bold text-slate-800 block truncate">{{ $navActiveCompany->company_name }}</span>
                                <span class="text-[10px] text-slate-400 block">Workspace kolaborasi tim</span>
                            </div>
                        </a>
                    @endif

                    <!-- LUNOU Chat -->
                    <a href="{{ route('chat.index') }}" 
                       class="quick-nav-item flex items-center justify-between p-3 hover:bg-slate-50 border border-transparent hover:border-slate-200 rounded-2xl transition-all group"
                       data-search="chat pesan diskusi ruang obrolan message lunou">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">LUNOU Chat Room</span>
                                <span class="text-[10px] text-slate-400 block">Obrolan & koordinasi tim</span>
                            </div>
                        </div>
                        @if(isset($navUnreadChatCount) && $navUnreadChatCount > 0)
                            <span class="px-2 py-0.5 bg-rose-500 text-white text-[10px] font-black rounded-full">
                                {{ $navUnreadChatCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Pengaturan AI -->
                    <a href="{{ route('ai-settings.index') }}" 
                       class="quick-nav-item flex items-center gap-3 p-3 hover:bg-slate-50 border border-transparent hover:border-slate-200 rounded-2xl transition-all group"
                       data-search="ai asisten bot kecerdasan buatan setting pengaturan api">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364.364l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Pengaturan AI</span>
                            <span class="text-[10px] text-slate-400 block">Kelola penyedia & prompt AI</span>
                        </div>
                    </a>

                    <!-- Profil Pengguna -->
                    <a href="{{ route('profile.edit') }}" 
                       class="quick-nav-item flex items-center gap-3 p-3 hover:bg-slate-50 border border-transparent hover:border-slate-200 rounded-2xl transition-all group"
                       data-search="profil akun user password ubah sandi setting">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Profil Saya</span>
                            <span class="text-[10px] text-slate-400 block">Informasi akun & keamanan</span>
                        </div>
                    </a>

                    <!-- Log Out / Keluar Cepat (Bisa dicari via Ctrl+K / Cari Cepat di HP & iPad) -->
                    <form method="POST" action="{{ route('logout') }}" 
                          class="quick-nav-item m-0 p-0 block"
                          data-search="logout keluar log out sign out exit selesai tutup akun">
                        @csrf
                        <button type="submit" 
                                class="w-full flex items-center justify-between p-3 hover:bg-rose-50 border border-transparent hover:border-rose-200 rounded-2xl transition-all group text-left cursor-pointer">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-rose-50 group-hover:bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-rose-700 block">Keluar dari Akun (Log Out)</span>
                                    <span class="text-[10px] text-rose-400 block">Akhiri sesi login di perangkat ini</span>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold text-rose-600 bg-rose-100/80 px-2 py-0.5 rounded-lg">Keluar</span>
                        </button>
                    </form>

                    <!-- Portofolio Publik Perusahaan -->
                    @if(isset($navActiveCompany) && $navActiveCompany && $navActiveCompany->slug && $navActiveCompany->is_published)
                        <a href="{{ route('public.company.show', $navActiveCompany->slug) }}" target="_blank"
                           class="quick-nav-item flex items-center gap-3 p-3 hover:bg-slate-50 border border-transparent hover:border-slate-200 rounded-2xl transition-all group"
                           data-search="portofolio publik publikasi website resmi {{ strtolower($navActiveCompany->company_name) }}">
                            <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Portofolio Publik ↗</span>
                                <span class="text-[10px] text-slate-400 block">Halaman showcase terbuka</span>
                            </div>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Section: Alihkan Portal / Mode Peran (Jika Manager/Admin) -->
            @if(Auth::user()->isManagement() || Auth::user()->isSuperAdmin())
                <div class="quick-nav-group pt-2 border-t border-slate-150" data-group="portal-switch">
                    <div class="px-3 py-1 text-[11px] font-black uppercase text-slate-400 tracking-wider">
                        <span class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 fill-current text-slate-400 shrink-0" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/></svg> Alihkan Mode Portal</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-1">
                        <a href="{{ route('management.dashboard') }}" 
                           class="quick-nav-item flex items-center gap-3 p-3 rounded-2xl border transition-all {{ request()->is('management*') ? 'bg-emerald-50 border-emerald-200 text-emerald-900 ring-2 ring-emerald-500/20' : 'bg-slate-50 border-slate-200 hover:bg-emerald-50/50 hover:border-emerald-200' }}"
                           data-search="management hub pengelola admin manager proyek roadmap keuangan">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <div>
                                <span class="text-xs font-black block">Mode Pengelola (Management)</span>
                                <span class="text-[10px] text-slate-400 block">Kelola proyek, tim, & roadmap</span>
                            </div>
                        </a>

                        <a href="{{ route('user.dashboard') }}" 
                           class="quick-nav-item flex items-center gap-3 p-3 rounded-2xl border transition-all {{ (!request()->is('management*') && !request()->is('superadmin*')) ? 'bg-indigo-50 border-indigo-200 text-indigo-900 ring-2 ring-indigo-500/20' : 'bg-slate-50 border-slate-200 hover:bg-indigo-50/50 hover:border-indigo-200' }}"
                           data-search="member portal karyawan anggota staf tugas harian">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                            <div>
                                <span class="text-xs font-black block">Mode Anggota (Member Portal)</span>
                                <span class="text-[10px] text-slate-400 block">Ruang tugas & fokus kerja</span>
                            </div>
                        </a>
                    </div>
                </div>
            @endif

            <!-- Empty Search State -->
            <div id="quickNavEmptyState" class="hidden p-8 text-center text-slate-400 text-xs">
                <svg class="w-8 h-8 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="font-bold text-slate-600">Tidak ada proyek atau menu yang cocok</p>
                <p class="text-[11px] mt-1">Coba kata kunci lain seperti nama proyek, "chat", "workspace", atau "tugas".</p>
            </div>
        </div>

        <!-- Footer / Shortcuts Guide -->
        <div class="px-5 py-3 bg-slate-100/70 border-t border-slate-150 flex items-center justify-between text-[11px] text-slate-500 font-semibold">
            <span class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Yoimo Fast Navigator</span>
            </span>
            <div class="flex items-center gap-3">
                <span class="hidden sm:inline">Pintasan: <kbd class="px-1.5 py-0.5 bg-white border border-slate-200 rounded font-mono text-[10px]">Ctrl</kbd> + <kbd class="px-1.5 py-0.5 bg-white border border-slate-200 rounded font-mono text-[10px]">K</kbd></span>
                <button type="button" onclick="closeQuickNavModal()" class="text-indigo-600 hover:text-indigo-800 font-bold">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function openQuickNavModal() {
        const modal = document.getElementById('quickNavModal');
        const input = document.getElementById('quickNavSearchInput');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                if (input) input.focus();
            }, 50);
        }
    }

    function closeQuickNavModal() {
        const modal = document.getElementById('quickNavModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    function filterQuickNavItems(query) {
        const cleanQuery = query.toLowerCase().trim();
        const items = document.querySelectorAll('.quick-nav-item');
        const groups = document.querySelectorAll('.quick-nav-group');
        const emptyState = document.getElementById('quickNavEmptyState');
        let visibleCount = 0;

        items.forEach(item => {
            const searchData = item.getAttribute('data-search') || '';
            const textContent = item.textContent.toLowerCase();
            if (cleanQuery === '' || searchData.includes(cleanQuery) || textContent.includes(cleanQuery)) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Hide empty groups
        groups.forEach(group => {
            const visibleChildren = group.querySelectorAll('.quick-nav-item:not([style*="display: none"])');
            if (cleanQuery !== '' && visibleChildren.length === 0) {
                group.style.display = 'none';
            } else {
                group.style.display = '';
            }
        });

        if (emptyState) {
            if (visibleCount === 0 && cleanQuery !== '') {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }
        }
    }

    // Keyboard Shortcuts (Ctrl+K or Cmd+K to toggle, ESC to close)
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const modal = document.getElementById('quickNavModal');
            if (modal && !modal.classList.contains('hidden')) {
                closeQuickNavModal();
            } else {
                openQuickNavModal();
            }
        } else if (e.key === 'Escape') {
            closeQuickNavModal();
        }
    });

    // Close when clicking outside content box
    document.getElementById('quickNavModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeQuickNavModal();
        }
    });
</script>
