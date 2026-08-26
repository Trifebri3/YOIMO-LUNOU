@php
    $user = Auth::user();
    
    // Ambil semua proyek yang diikuti user
    $myProjects = \App\Models\Project::whereJsonContains('team_matrix', ['user_id' => (string) $user->id])
        ->orWhereJsonContains('team_matrix', ['user_id' => (int) $user->id])
        ->orWhere('created_by', $user->id)
        ->with('company')
        ->latest()
        ->get();

    // Ambil list Company yang unik dari proyek user
    $projectCompanies = $myProjects->pluck('company')->filter();
    
    // Ambil list Company yang dimanajeri oleh user
    $managedCompanies = \App\Models\CompanyProfile::where('manager_id', $user->id)->get();
    
    $assignedCompanies = $projectCompanies->concat($managedCompanies)->filter()->unique('id');

    // Tentukan company aktif
    $activeCompany = isset($company) ? $company : $assignedCompanies->first();

    // Hitung total tugas yang sedang ditugaskan ke user ini
    $myPendingTasksCount = \App\Models\ProjectTask::where('assigned_to', $user->id)
        ->whereIn('status', ['Todo', 'In Progress'])
        ->count();

    // Dapatkan data poin/level user
    $userPoint = \App\Models\UserPoint::firstOrCreate(
        ['user_id' => $user->id],
        ['total_points' => 0, 'level' => 1, 'login_streak' => 0]
    );
@endphp

<aside id="mobile-sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-100 flex flex-col justify-between h-screen shrink-0 font-sans select-none hidden md:flex md:sticky md:top-0">
    <div class="p-6 space-y-6">
        <!-- Logo Area -->
        <div class="flex items-center justify-between gap-3">
            <img src="{{ asset('logopanjang.png') }}" alt="Logo" class="h-8 w-auto">
            <button type="button" onclick="toggleSidebar()" class="p-1.5 text-slate-500 hover:bg-slate-100 rounded-xl md:hidden" title="Close Menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- WORKSPACE COMPANY AKTIF (Interactive Multi-Company Switcher) -->
        <div class="relative" id="userWorkspaceDropdownContainer">
            <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase block mb-2">WORKSPACE PERUSAHAAN</span>
            
            @if($assignedCompanies->count() > 0)
                <button type="button" 
                        onclick="toggleUserWorkspaceDropdown()" 
                        class="w-full flex items-center justify-between px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/80 border border-slate-200/70 rounded-2xl text-xs font-bold text-slate-800 transition-all">
                    <div class="flex items-center gap-2.5 truncate">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 shrink-0"></span>
                        <span class="truncate font-black text-slate-900">{{ $activeCompany->company_name ?? 'Pilih Perusahaan' }}</span>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 shrink-0 transition-transform duration-200" id="userDropdownArrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Dropdown List Assigned Companies -->
                <div id="userWorkspaceDropdownMenu" 
                     class="hidden absolute top-full left-0 right-0 mt-2 bg-white border border-slate-100 rounded-2xl shadow-xl z-50 p-2 space-y-1">
                    <span class="text-[9px] font-black text-slate-400 px-3 py-1 uppercase tracking-wider block">Company Saya ({{ $assignedCompanies->count() }})</span>
                    
                    @foreach($assignedCompanies as $item)
                        <a href="{{ route('user.company.workspace', $item->id) }}" 
                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition-all {{ ($activeCompany && $activeCompany->id === $item->id) ? 'bg-indigo-50 text-indigo-800' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <div class="flex items-center gap-2 truncate">
                                <span class="w-2 h-2 rounded-full {{ ($activeCompany && $activeCompany->id === $item->id) ? 'bg-indigo-500' : 'bg-slate-300' }}"></span>
                                <span class="truncate">{{ $item->company_name }}</span>
                            </div>
                            @if($activeCompany && $activeCompany->id === $item->id)
                                <svg class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            @endif
                        </a>
                    @endforeach
                </div>
            @else
                <div class="px-3.5 py-2.5 bg-slate-50 border border-dashed border-slate-200 rounded-2xl text-[11px] font-medium text-slate-400">
                    Belum tergabung company
                </div>
            @endif
        </div>

        <!-- Level & Streak Card -->
        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100/70 rounded-2xl p-3.5 mb-3">
            <div class="flex items-center justify-between text-xs font-bold text-emerald-900 mb-1.5">
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    Lvl {{ $userPoint->level }}
                </span>
                <span class="text-amber-700 bg-amber-100/60 px-2 py-0.5 rounded-full text-[10px] tracking-wide font-black flex items-center gap-1">
                    STREAK {{ $userPoint->login_streak }} HARI
                </span>
            </div>
            <div class="w-full bg-emerald-200/50 rounded-full h-1.5 mb-1.5">
                <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ (($userPoint->total_points % 200) / 200) * 100 }}%"></div>
            </div>
            <div class="flex justify-between text-[10px] text-slate-400 font-semibold">
                <span>XP: {{ $userPoint->total_points }}</span>
                <span>Berikutnya: {{ $userPoint->level * 200 }}</span>
            </div>
        </div>

        <!-- Kartu Profil & Status Kerja -->
        <div class="bg-gradient-to-br from-indigo-50/80 to-slate-50 border border-indigo-100/70 rounded-2xl p-3.5">
            <div class="flex items-center gap-2.5 mb-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-[10px] font-black uppercase text-indigo-900 tracking-wider">MEMBER WORKSPACE</span>
            </div>
            <div class="flex items-center justify-between text-xs font-bold text-slate-700">
                <span>Tugas Aktif Saya</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $myPendingTasksCount > 0 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-500' }}">
                    {{ $myPendingTasksCount }} Tugas
                </span>
            </div>
        </div>

        <!-- NAVIGASI UTAMA -->
        <div>
            <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-2.5 block">NAVIGASI UTAMA</span>
            <nav class="space-y-1">
                <!-- Ruang Pribadi Dashboard -->
                <a href="{{ route('user.dashboard') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('user.dashboard') ? 'bg-indigo-50 text-indigo-700 border border-indigo-200 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('user.dashboard') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Ruang Pribadi</span>
                </a>

                <!-- Portal Switcher: Masuk Mode Pengelola (Jika Manager/Admin) -->
                @if($user->isManagement() || $user->isSuperAdmin())
                    <a href="{{ route('management.dashboard') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 hover:bg-emerald-100 transition-all">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Masuk Mode Pengelola</span>
                    </a>
                @endif

                <!-- Dashboard Company Aktif -->
                @if($activeCompany)
                    <a href="{{ route('user.company.workspace', $activeCompany->id) }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('user.company.workspace') ? 'bg-indigo-50 text-indigo-700 border border-indigo-200 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('user.company.workspace') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span>Dashboard Company</span>
                    </a>

                    <!-- Chat Room Link -->
                    <a href="{{ route('chat.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('chat.index') ? 'bg-indigo-50 text-indigo-700 border border-indigo-200 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('chat.index') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <span>LUNOU Chat Room</span>
                    </a>

                    <!-- AI Settings Link -->
                    <a href="{{ route('ai-settings.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('ai-settings.*') ? 'bg-indigo-50 text-indigo-700 border border-indigo-200 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('ai-settings.*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364.364l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        <span>Pengaturan AI</span>
                    </a>

                    @if($activeCompany->slug && $activeCompany->is_published)
                        <a href="{{ route('public.company.show', $activeCompany->slug) }}" target="_blank" 
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                <span>Portofolio Publik</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                    @endif
                @endif
            </nav>
        </div>

        <!-- DAFTAR PROYEK SAYA -->
<!-- DAFTAR PROYEK SAYA -->
        <div>
            <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-2.5 block">PROYEK SAYA ({{ $myProjects->count() }})</span>
            <div class="space-y-1 max-h-48 overflow-y-auto pr-1">
                @forelse($myProjects as $pj)
                    @php
                        $isCurrentProject = request()->routeIs('user.projects.show') && request()->route('project') && request()->route('project')->id == $pj->id;
                    @endphp
                    <a href="{{ route('user.projects.show', $pj->id) }}" 
                       class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold truncate transition-all {{ $isCurrentProject ? 'bg-indigo-50 text-indigo-700 font-bold border border-indigo-200' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <span class="w-2 h-2 rounded-full {{ $isCurrentProject ? 'bg-indigo-600' : 'bg-slate-300' }} shrink-0"></span>
                        <span class="truncate">{{ $pj->name }}</span>
                    </a>
                @empty
                    <span class="text-[11px] text-slate-400 px-3 py-1 block italic">Belum ada proyek yang diikuti</span>
                @endforelse
            </div>
        </div>
    </div>

    <!-- User Profile Strip Bawah -->
    <div class="p-6 border-t border-slate-100">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5 truncate">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200 shrink-0">
                @else
                    <div class="w-8 h-8 rounded-full bg-slate-900 text-white font-bold text-xs flex items-center justify-center shrink-0">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                @endif
                <div class="truncate">
                    <div class="text-xs font-bold text-slate-900 truncate">{{ $user->name }}</div>
                    <div class="text-[10px] text-slate-400 font-semibold truncate">{{ $user->position ?? 'Team Member' }}</div>
                </div>
            </div>

            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all" title="Keluar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </form>
        </div>
    </div>
</aside>

<script>
    function toggleUserWorkspaceDropdown() {
        const menu = document.getElementById('userWorkspaceDropdownMenu');
        const arrow = document.getElementById('userDropdownArrow');
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    document.addEventListener('click', function(e) {
        const container = document.getElementById('userWorkspaceDropdownContainer');
        const menu = document.getElementById('userWorkspaceDropdownMenu');
        const arrow = document.getElementById('userDropdownArrow');
        if (container && !container.contains(e.target)) {
            if (menu && !menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
                if (arrow) arrow.classList.remove('rotate-180');
            }
        }
    });
</script>