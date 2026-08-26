@php
    $initialNotifications = Auth::user() 
        ? Auth::user()->unreadNotifications()->take(5)->get()->map(function($n) {
            return [
                'id' => $n->id,
                'type' => $n->data['type'] ?? 'general',
                'action_type' => $n->data['action_type'] ?? '',
                'title' => $n->data['title'] ?? 'Notifikasi',
                'message' => $n->data['message'] ?? '',
                'project_id' => $n->data['project_id'] ?? null,
                'task_id' => $n->data['task_id'] ?? null,
                'agenda_id' => $n->data['agenda_id'] ?? null,
                'created_at' => $n->created_at->diffForHumans(),
            ];
        })
        : collect();

    // Since we also want dynamic tasks/projects deadlines in initial bootstrap:
    $userId = Auth::id();
    $upcomingTasksCount = 0;
    $upcomingProjectsCount = 0;
    if ($userId) {
        $upcomingTasksCount = \App\Models\ProjectTask::where('assigned_to', $userId)
            ->whereIn('status', ['Todo', 'In Progress'])
            ->whereNotNull('due_date')
            ->where('due_date', '<=', \Carbon\Carbon::now()->addDays(3))
            ->where('due_date', '>=', \Carbon\Carbon::now()->subDays(2))
            ->count();

        $myCompanyIds = \App\Models\CompanyProfile::where('manager_id', $userId)->pluck('id');
        $upcomingProjectsCount = \App\Models\Project::where('is_archived', false)
            ->whereNotIn('status', ['Completed'])
            ->whereNotNull('deadline')
            ->where('deadline', '<=', \Carbon\Carbon::now()->addDays(5))
            ->where('deadline', '>=', \Carbon\Carbon::now()->subDays(2))
            ->where(function ($query) use ($myCompanyIds, $userId) {
                $query->whereIn('company_profile_id', $myCompanyIds)
                    ->orWhere('created_by', $userId);
            })
            ->count();
    }
    
    $initialUnreadCount = (Auth::user() ? Auth::user()->unreadNotifications()->count() : 0) + $upcomingTasksCount + $upcomingProjectsCount;
@endphp

<div x-data="{
    open: false,
    notifications: {{ json_encode($initialNotifications) }},
    unreadCount: {{ $initialUnreadCount }},
    
    fetchNotifications() {
        fetch('{{ route('notifications.unread') }}')
            .then(res => res.json())
            .then(data => {
                this.notifications = data.notifications;
                this.unreadCount = data.unread_count;
            })
            .catch(err => console.error('Failed to fetch notifications:', err));
    },
    
    markRead(id) {
        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                this.fetchNotifications();
            }
        })
        .catch(err => console.error(err));
    },
    
    markAllRead() {
        fetch('{{ route('notifications.read-all') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                this.fetchNotifications();
            }
        })
        .catch(err => console.error(err));
    }
}" x-init="fetchNotifications(); setInterval(() => fetchNotifications(), 15000);" 
   @click.outside="open = false" 
   class="relative z-40 font-sans">
   
    <!-- Bell Button -->
    <button @click="open = !open" type="button" 
            class="relative p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all cursor-pointer focus:outline-none"
            :class="{ 'text-indigo-600 bg-indigo-50': open }">
        
        <!-- Bell Icon -->
        <svg class="w-5 h-5 transition-transform duration-300" 
             :class="{ 'animate-swing': unreadCount > 0 && !open }"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
            </path>
        </svg>
        
        <!-- Badge Indicator -->
        <template x-if="unreadCount > 0">
            <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-rose-500 text-[9px] font-black text-white rounded-full flex items-center justify-center ring-2 ring-white" 
                  x-text="unreadCount">
            </span>
        </template>
    </button>
    
    <!-- Dropdown Drawer -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-1"
         class="absolute right-0 mt-2.5 w-80 bg-white border border-slate-100 rounded-2xl shadow-xl z-50 overflow-hidden"
         style="display: none;">
         
        <!-- Header -->
        <div class="px-4 py-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xs font-black text-slate-800 uppercase tracking-wider">Notifikasi</span>
                <span class="px-1.5 py-0.5 bg-indigo-100 text-indigo-800 text-[10px] font-black rounded-md" x-text="unreadCount"></span>
            </div>
            <button x-show="unreadCount > 0" @click="markAllRead()" type="button" 
                    class="text-[10px] font-black text-indigo-600 hover:text-indigo-800 transition-colors uppercase tracking-wider cursor-pointer">
                Tandai semua dibaca
            </button>
        </div>
        
        <!-- List Container -->
        <div class="max-h-[320px] overflow-y-auto divide-y divide-slate-50">
            <!-- Loading/Empty State -->
            <div x-show="notifications.length === 0" class="px-4 py-8 text-center flex flex-col items-center justify-center gap-2">
                <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-xs font-black text-slate-800">Semua tugas beres!</div>
                <div class="text-[10px] text-slate-400 font-medium">Tidak ada notifikasi unread baru saat ini.</div>
            </div>
            
            <!-- Items -->
            <template x-for="item in notifications" :key="item.id">
                <div class="p-3.5 hover:bg-slate-50/50 transition-colors flex items-start gap-3 relative group">
                    <!-- Icon according to type -->
                    <div class="w-8 h-8 rounded-xl shrink-0 flex items-center justify-center"
                         :class="{
                             'bg-amber-50 text-amber-600': item.type === 'agenda',
                             'bg-indigo-50 text-indigo-600': item.type === 'task',
                             'bg-rose-50 text-rose-600': item.type === 'deadline'
                         }">
                        
                        <!-- Agenda / Calendar Icon -->
                        <template x-if="item.type === 'agenda'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </template>
                        
                        <!-- Task / Checklist Icon -->
                        <template x-if="item.type === 'task'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                </path>
                            </svg>
                        </template>
                        
                        <!-- Deadline / Warning Icon -->
                        <template x-if="item.type === 'deadline'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </template>
                    </div>
                    
                    <!-- Content -->
                    <div class="flex-1 min-w-0 pr-6">
                        <div class="flex items-center gap-1.5 justify-between">
                            <span class="text-[9px] font-black uppercase tracking-wider text-slate-400" x-text="item.created_at"></span>
                        </div>
                        <h4 class="text-xs font-black text-slate-800 mt-0.5 truncate" x-text="item.title"></h4>
                        <p class="text-[10px] text-slate-500 font-semibold leading-relaxed mt-0.5" x-text="item.message"></p>
                        
                        <!-- Quick action link -->
                        <div class="mt-1.5">
                            <template x-if="item.type === 'agenda' && item.project_id">
                                <a :href="`/management/projects/${item.project_id}/agendas`" 
                                   class="text-[9px] font-black text-indigo-600 hover:underline uppercase tracking-wider">
                                    Lihat Agenda &rarr;
                                </a>
                            </template>
                            <template x-if="item.type === 'task' && item.project_id">
                                <a :href="`/management/projects/${item.project_id}/tasks`" 
                                   class="text-[9px] font-black text-indigo-600 hover:underline uppercase tracking-wider">
                                    Lihat Tugas &rarr;
                                </a>
                            </template>
                            <template x-if="item.type === 'deadline' && item.project_id">
                                <a :href="`/management/projects/${item.project_id}`" 
                                   class="text-[9px] font-black text-indigo-600 hover:underline uppercase tracking-wider">
                                    Buka Detail &rarr;
                                </a>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Mark as read single button -->
                    <button @click="markRead(item.id)" type="button" 
                            class="absolute top-3.5 right-3 w-6 h-6 bg-slate-100 hover:bg-emerald-50 text-slate-400 hover:text-emerald-600 border border-slate-200/50 rounded-full flex items-center justify-center transition-all cursor-pointer shadow-xs"
                            title="Tandai dibaca">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </button>
                </div>
            </template>
        </div>
        
        <!-- Footer -->
        <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-100 text-center">
            <template x-if="notifications.length > 0">
                <a href="{{ route('management.dashboard') }}" 
                   class="text-[10px] font-black text-indigo-600 hover:text-indigo-800 transition-colors uppercase tracking-wider block">
                    Lihat Dashboard Utama &rarr;
                </a>
            </template>
            <template x-if="notifications.length === 0">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">LUNOU Notifikasi Asisten</span>
            </template>
        </div>
    </div>
</div>

<style>
    @keyframes swing {
        0%, 100% { transform: rotate(0deg); }
        20% { transform: rotate(15deg); }
        40% { transform: rotate(-10deg); }
        60% { transform: rotate(5deg); }
        80% { transform: rotate(-5deg); }
    }
    .animate-swing {
        animation: swing 1s ease-in-out infinite;
        transform-origin: top center;
    }
</style>
