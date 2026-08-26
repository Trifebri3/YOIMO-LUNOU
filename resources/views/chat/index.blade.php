@php
    $layout = 'user.layouts.app';
    if (Auth::user()->role === 'superadmin') {
        $layout = 'superadmin.layouts.app';
    } elseif (Auth::user()->role === 'management') {
        $layout = 'management.layouts.app';
    }
@endphp

@extends($layout)

@section('title', 'WhatsApp Chat Center')

@push('styles')
<style>
    #messages-container {
        position: relative;
        background-color: #f1f5f9 !important;
    }
    #messages-container::before {
        content: "";
        position: absolute;
        inset: 0;
        background-image: url('{{ asset("icon/11.png") }}');
        background-size: 90px 90px;
        background-repeat: repeat;
        opacity: 0.025;
        pointer-events: none;
        z-index: 0;
    }
    .flex.items-end {
        position: relative;
        z-index: 10;
    }
</style>
@endpush

@section('content')
<div class="h-[calc(100vh-10rem)] md:h-[calc(100vh-7rem)] bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm flex font-sans">
    
    <!-- LEFT SIDEBAR: List Contacts & Group Chats -->
    <div id="chat-sidebar" class="w-full md:w-80 lg:w-96 border-r border-slate-100 flex flex-col shrink-0 {{ ($activeUser || $activeProject) ? 'hidden md:flex' : 'flex' }}">
        
        <!-- Sidebar Header Tabs -->
        <div class="p-4 border-b border-slate-100 bg-slate-50/60 space-y-3">
            <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider">LUNOU Chat Room</h2>
            
            <div class="flex bg-slate-200/60 p-1 rounded-xl">
                <button type="button" onclick="switchTab('personal')" id="tab-personal-btn" class="flex-1 py-1.5 text-center text-xs font-black rounded-lg transition-all bg-white text-indigo-750 shadow-xs">
                    Personal
                </button>
                <button type="button" onclick="switchTab('group')" id="tab-group-btn" class="flex-1 py-1.5 text-center text-xs font-black rounded-lg transition-all text-slate-500 hover:text-slate-800">
                    Project Group
                </button>
            </div>
        </div>

        <!-- Scrollable Chats List -->
        <div class="flex-1 overflow-y-auto divide-y divide-slate-50">
            
            <!-- Tab 1: Personal Chats List -->
            <div id="list-personal" class="space-y-0.5">
                @forelse($contacts as $c)
                    @php
                        $isActive = ($activeUser && $activeUser->id === $c->id);
                    @endphp
                    <a href="{{ route('chat.index', ['user_id' => $c->id]) }}" class="flex items-center gap-3 p-3.5 transition-all hover:bg-slate-50/80 {{ $isActive ? 'bg-indigo-50 border-r-4 border-indigo-600' : '' }}">
                        <div class="relative shrink-0">
                            @if($c->avatar)
                                <img src="{{ asset('storage/' . $c->avatar) }}" alt="Avatar" class="w-10 h-10 rounded-xl object-cover border border-slate-200">
                            @else
                                <div class="w-10 h-10 rounded-xl bg-slate-900 text-white font-black text-xs flex items-center justify-center uppercase">
                                    {{ substr($c->name, 0, 2) }}
                                </div>
                            @endif
                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 rounded-full border border-white"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-black text-slate-800 truncate">{{ $c->name }}</h4>
                                <span class="text-[9px] bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded uppercase font-bold">{{ $c->role }}</span>
                            </div>
                            <p class="text-[10px] text-slate-400 truncate mt-0.5">{{ $c->position ?? 'Staff Anggota' }}</p>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-xs text-slate-400 italic">Tidak ada kontak lain.</div>
                @endforelse
            </div>

            <!-- Tab 2: Group Project Chats List -->
            <div id="list-group" class="hidden space-y-0.5">
                @forelse($groups as $g)
                    @php
                        $isActive = ($activeProject && $activeProject->id === $g->id);
                    @endphp
                    <a href="{{ route('chat.index', ['project_id' => $g->id]) }}" class="flex items-center gap-3 p-3.5 transition-all hover:bg-slate-50/80 {{ $isActive ? 'bg-indigo-50 border-r-4 border-indigo-600' : '' }}">
                        <div class="w-10 h-10 rounded-xl bg-indigo-150 text-indigo-700 font-black text-xs flex items-center justify-center shrink-0">
                            GRP
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-black text-slate-800 truncate uppercase">{{ $g->name }}</h4>
                                <span class="text-[9px] text-slate-500 font-bold bg-slate-100 px-1.5 py-0.5 rounded">{{ $g->status }}</span>
                            </div>
                            <p class="text-[10px] text-slate-400 truncate mt-0.5">{{ $g->category }}</p>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-xs text-slate-400 italic">Belum ada proyek terdaftar.</div>
                @endforelse
            </div>

        </div>

    </div>

    <!-- RIGHT CHAT WINDOW: Messages History & Input -->
    <div id="chat-window" class="flex-1 flex flex-col bg-slate-50/30 relative {{ ($activeUser || $activeProject) ? 'flex' : 'hidden md:flex' }}">
        
        @if($activeUser || $activeProject)
            <!-- Chat Window Header -->
            <div class="p-4 border-b border-slate-100 bg-white flex items-center justify-between sticky top-0 z-10">
                <div class="flex items-center gap-3">
                    <!-- Mobile Back Button -->
                    <a href="{{ route('chat.index') }}" class="p-2 hover:bg-slate-50 rounded-full md:hidden text-slate-500 mr-1" title="Kembali ke Kontak">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>

                    @if($activeUser)
                        @if($activeUser->avatar)
                            <img src="{{ asset('storage/' . $activeUser->avatar) }}" alt="Avatar" class="w-10 h-10 rounded-xl object-cover border border-slate-200">
                        @else
                            <div class="w-10 h-10 rounded-xl bg-slate-900 text-white font-black text-xs flex items-center justify-center uppercase">
                                {{ substr($activeUser->name, 0, 2) }}
                            </div>
                        @endif
                        <div>
                            <h3 class="text-xs font-black text-slate-800">{{ $activeUser->name }}</h3>
                            <span class="text-[10px] text-emerald-500 font-bold block mt-0.5">Online (Direct Chat)</span>
                        </div>
                    @else
                        <div class="w-10 h-10 rounded-xl bg-indigo-150 text-indigo-700 font-black text-xs flex items-center justify-center uppercase shrink-0">
                            GRP
                        </div>
                        <div>
                            <h3 class="text-xs font-black text-slate-800 uppercase">{{ $activeProject->name }}</h3>
                            <span class="text-[10px] text-slate-400 font-bold block mt-0.5">Project Group Chat Room</span>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-[9px] font-black uppercase text-indigo-600 bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded-md">LUNOU Live</span>
                </div>
            </div>

            <!-- Scrollable Messages Feed -->
            <div id="messages-container" class="flex-1 overflow-y-auto p-4 md:p-6 space-y-4 relative">
                
                @forelse($messages as $msg)
                    @php
                        $isMe = ($msg->sender_id === Auth::id());
                    @endphp
                    
                    <div class="flex items-end gap-2 {{ $isMe ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $msg->id }}">
                        
                        @if(!$isMe)
                            <!-- Sender Avatar -->
                            <div class="shrink-0">
                                @if($msg->sender->avatar)
                                    <img src="{{ asset('storage/' . $msg->sender->avatar) }}" alt="Avatar" class="w-8 h-8 rounded-lg object-cover border border-slate-200">
                                @else
                                    <div class="w-8 h-8 rounded-lg bg-slate-900 text-white font-black text-[10px] flex items-center justify-center uppercase">
                                        {{ substr($msg->sender->name, 0, 2) }}
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Chat Bubble Box -->
                        <div class="max-w-[70%] rounded-2xl p-4 shadow-md space-y-1.5 transition-all
                             {{ $isMe ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-white border border-slate-100 text-slate-800 rounded-bl-none' }}">
                            
                            @if(!$isMe)
                                <!-- Sender Name inside bubble -->
                                <span class="block text-[11px] font-black uppercase tracking-wider text-indigo-600 mb-0.5">{{ $msg->sender->name }}</span>
                            @endif

                            @if($msg->message)
                                <p class="text-sm leading-relaxed break-words whitespace-pre-line font-semibold">{{ $msg->message }}</p>
                            @endif

                            <!-- Attachment View inside bubble -->
                            @if($msg->attachment_file)
                                <div class="p-3 rounded-xl border flex items-center gap-2.5 mt-1.5
                                     {{ $isMe ? 'bg-white/10 border-white/20 text-white' : 'bg-slate-50 border-slate-200 text-slate-700' }}">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    <div class="flex-1 min-w-0">
                                        <span class="block text-xs font-black truncate">{{ $msg->attachment_name ?? 'Attachment File' }}</span>
                                    </div>
                                    <a href="{{ asset('storage/' . $msg->attachment_file) }}" target="_blank" 
                                       class="px-2.5 py-1 bg-white text-slate-800 hover:bg-slate-100 rounded-lg text-[10px] font-black tracking-wider uppercase shrink-0 shadow-sm">
                                        Unduh
                                    </a>
                                </div>
                            @endif

                            <!-- Task Card Reference inside bubble -->
                            @if($msg->task)
                                @php
                                    $taskUrl = '#';
                                    if (Auth::user()->role === 'management') {
                                        $taskUrl = route('management.projects.tasks.index', $msg->task->project_id);
                                    } else {
                                        $taskUrl = route('user.projects.show', [$msg->task->project_id, 'tab' => 'tasks']);
                                    }
                                @endphp
                                <div class="p-3.5 rounded-2xl border-l-4 flex flex-col gap-2 shadow-sm my-1.5 text-left
                                     {{ $isMe ? 'bg-white/10 border-white/40 text-white' : 'bg-slate-50 border-indigo-400 text-slate-800' }}">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-[9px] font-black uppercase tracking-wider opacity-80">Referensi Tugas</span>
                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded uppercase
                                             {{ $isMe ? 'bg-white/20 text-white' : 'bg-indigo-50 text-indigo-700' }}">
                                            {{ $msg->task->status }}
                                        </span>
                                    </div>
                                    <div class="space-y-0.5">
                                        <h4 class="text-xs font-black truncate">{{ $msg->task->title }}</h4>
                                        <p class="text-[9px] opacity-75 truncate">Proyek: {{ $msg->task->project->name ?? 'Proyek' }}</p>
                                    </div>
                                    <a href="{{ $taskUrl }}" 
                                       class="mt-1 block text-center py-1.5 rounded-lg text-[9px] font-black tracking-wider uppercase shadow-xs transition-all
                                            {{ $isMe ? 'bg-white text-indigo-750 hover:bg-slate-50' : 'bg-indigo-600 text-white hover:bg-indigo-700' }}">
                                        Buka Detail Tugas
                                    </a>
                                </div>
                            @endif

                            <!-- Bubble Timestamp -->
                            <span class="block text-[10px] text-right mt-1 {{ $isMe ? 'text-white/70' : 'text-slate-400' }} font-bold">
                                {{ $msg->created_at->format('H:i') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex items-center justify-center">
                        <div class="bg-white/80 backdrop-blur-md px-6 py-4 border border-slate-100 rounded-2xl text-center space-y-1 shadow-sm">
                            <h4 class="text-xs font-black text-slate-800 uppercase">Awal Obrolan</h4>
                            <p class="text-[10px] text-slate-400">Silakan ketik pesan pertama Anda untuk memulai obrolan live.</p>
                        </div>
                    </div>
                @endforelse

            </div>

            <!-- Live Chat Input Footer -->
            <div class="p-4 border-t border-slate-100 bg-white sticky bottom-0 z-10 relative">
                
                <!-- Task Selector Floating Menu -->
                <div id="task-selector-menu" class="hidden absolute bottom-20 left-4 right-4 bg-white border border-slate-200 rounded-2xl shadow-xl z-20 max-h-60 overflow-y-auto p-3.5 space-y-2">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-wider">Kaitkan Tugas Ke Obrolan</span>
                        <button type="button" onclick="toggleTaskSelector()" class="text-slate-400 hover:text-slate-600 font-bold">&times; Tutup</button>
                    </div>
                    <div class="space-y-1.5 pt-1">
                        @forelse($availableTasks as $t)
                            <button type="button" onclick="selectTaskForChat({{ $t->id }}, '{{ addslashes($t->title) }}')"
                                    class="w-full text-left p-2.5 hover:bg-slate-50 rounded-xl transition-all flex flex-col gap-0.5 border border-slate-100/50">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-[9px] font-black uppercase text-indigo-650 truncate">{{ $t->project->name ?? 'Proyek' }}</span>
                                    <span class="text-[8px] font-bold bg-slate-100 px-1 py-0.2 rounded uppercase">{{ $t->status }}</span>
                                </div>
                                <span class="text-xs font-bold text-slate-800 truncate">{{ $t->title }}</span>
                            </button>
                        @empty
                            <div class="p-4 text-center text-xs text-slate-400 italic">Tidak ada tugas aktif tersedia.</div>
                        @endforelse
                    </div>
                </div>

                <form id="chat-form" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    @if($activeUser)
                        <input type="hidden" name="recipient_id" value="{{ $activeUser->id }}">
                    @else
                        <input type="hidden" name="project_id" value="{{ $activeProject->id }}">
                    @endif
                    <input type="hidden" name="task_id" id="chat-task-id-input">

                    <div class="flex items-center gap-2">
                        <!-- Custom Attachment Button -->
                        <div class="relative group">
                            <input type="file" name="file" id="chat-file-input" onchange="handleFileSelected(this)" class="hidden">
                            <button type="button" onclick="document.getElementById('chat-file-input').click()" 
                                    class="p-3 bg-slate-50 border border-slate-200 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-2xl transition-all flex items-center justify-center shrink-0"
                                    title="Lampirkan File / Gambar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                            </button>
                        </div>

                        <!-- Discuss Task Pull Button -->
                        <button type="button" onclick="toggleTaskSelector()" 
                                class="p-3 bg-slate-50 border border-slate-200 text-slate-500 hover:text-indigo-650 hover:bg-indigo-50 rounded-2xl transition-all flex items-center justify-center shrink-0"
                                title="Kaitkan Tugas / Task">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        </button>

                        <!-- Main Message Input Text Field -->
                        <input type="text" name="message" id="chat-message-input" autocomplete="off"
                               placeholder="Ketik pesan Anda disini..." 
                               class="flex-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-xs">

                        <!-- Submit Button -->
                        <button type="submit" class="p-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl shadow-md transition-all shrink-0">
                            <svg class="w-4 h-4 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        </button>
                    </div>

                    <!-- Selected Indicators Grid -->
                    <div class="flex flex-col gap-2">
                        <!-- File Selection Indicator Badge -->
                        <div id="file-indicator" class="hidden p-2 bg-indigo-50 border border-indigo-100 rounded-xl text-[10px] text-indigo-700 font-bold flex items-center justify-between">
                            <span id="file-indicator-name" class="truncate max-w-[200px]">document.pdf</span>
                            <button type="button" onclick="clearFileSelected()" class="text-slate-400 hover:text-slate-650 font-bold">&times; Hapus</button>
                        </div>

                        <!-- Task Selection Indicator Badge -->
                        <div id="task-indicator" class="hidden p-2 bg-indigo-50 border border-indigo-100 rounded-xl text-[10px] text-indigo-700 font-bold flex items-center justify-between">
                            <div class="flex items-center gap-1.5">
                                <span class="px-1.5 py-0.5 bg-indigo-600 text-white rounded text-[8px] font-black uppercase">TUGAS</span>
                                <span id="task-indicator-name" class="truncate max-w-[200px]">Task Title</span>
                            </div>
                            <button type="button" onclick="clearTaskSelected()" class="text-slate-400 hover:text-slate-650 font-bold">&times; Hapus</button>
                        </div>
                    </div>
                </form>
            </div>
        @else
            <!-- Placeholder Empty Chat Center View -->
            <div class="flex-1 flex flex-col items-center justify-center p-8 text-center space-y-4">
                <div class="w-24 h-24 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-full flex items-center justify-center text-indigo-600 border border-indigo-100 ring-8 ring-indigo-50/50">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                </div>
                <div class="space-y-1">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">Selamat Datang di Chat Center</h3>
                    <p class="text-xs text-slate-400 max-w-sm leading-relaxed">Pilih kontak tim di tab <strong>Personal</strong> atau masuk ke saluran diskusi proyek di tab <strong>Project Group</strong> untuk memulai diskusi live.</p>
                </div>
            </div>
        @endif

    </div>

</div>

@push('scripts')
<script>
    // Tab Navigation Logic
    let activeTabType = 'personal';

    function switchTab(type) {
        activeTabType = type;
        const personalBtn = document.getElementById('tab-personal-btn');
        const groupBtn = document.getElementById('tab-group-btn');
        const personalList = document.getElementById('list-personal');
        const groupList = document.getElementById('list-group');

        if (type === 'personal') {
            personalBtn.className = "flex-1 py-1.5 text-center text-xs font-black rounded-lg transition-all bg-white text-indigo-750 shadow-xs";
            groupBtn.className = "flex-1 py-1.5 text-center text-xs font-black rounded-lg transition-all text-slate-500 hover:text-slate-800";
            personalList.classList.remove('hidden');
            groupList.classList.add('hidden');
        } else {
            groupBtn.className = "flex-1 py-1.5 text-center text-xs font-black rounded-lg transition-all bg-white text-indigo-750 shadow-xs";
            personalBtn.className = "flex-1 py-1.5 text-center text-xs font-black rounded-lg transition-all text-slate-500 hover:text-slate-800";
            groupList.classList.remove('hidden');
            personalList.classList.add('hidden');
        }
        localStorage.setItem('lunou_chat_active_tab', type);
    }

    // Persist tab type on page load
    document.addEventListener('DOMContentLoaded', () => {
        const savedTab = localStorage.getItem('lunou_chat_active_tab');
        if (savedTab) {
            switchTab(savedTab);
        } else {
            @if($activeProject)
                switchTab('group');
            @else
                switchTab('personal');
            @endif
        }

        // Auto scroll message feed to bottom on load
        scrollToBottom();
    });

    function scrollToBottom() {
        const container = document.getElementById('messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    // Handle File Attachment Selection
    function handleFileSelected(input) {
        const indicator = document.getElementById('file-indicator');
        const indicatorName = document.getElementById('file-indicator-name');
        if (input.files && input.files.length > 0) {
            indicatorName.innerText = input.files[0].name;
            indicator.classList.remove('hidden');
        }
    }

    function clearFileSelected() {
        const input = document.getElementById('chat-file-input');
        const indicator = document.getElementById('file-indicator');
        if (input) input.value = '';
        if (indicator) indicator.classList.add('hidden');
    }

    // Toggle Task Selector Floating Menu
    function toggleTaskSelector() {
        const menu = document.getElementById('task-selector-menu');
        if (menu) {
            menu.classList.toggle('hidden');
        }
    }

    // Select Task for Chat
    function selectTaskForChat(taskId, taskTitle) {
        const input = document.getElementById('chat-task-id-input');
        const indicator = document.getElementById('task-indicator');
        const indicatorName = document.getElementById('task-indicator-name');
        
        if (input && indicator && indicatorName) {
            input.value = taskId;
            indicatorName.innerText = taskTitle;
            indicator.classList.remove('hidden');
        }
        
        // Hide selector menu
        toggleTaskSelector();
    }

    // Clear Selected Task
    function clearTaskSelected() {
        const input = document.getElementById('chat-task-id-input');
        const indicator = document.getElementById('task-indicator');
        if (input) input.value = '';
        if (indicator) indicator.classList.add('hidden');
    }

    // REALTIME LIVE CHAT POLLING ENGINE (AJAX)
    @if($activeUser || $activeProject)
        let lastMessageId = 0;
        
        // Find last message id in blade loaded feed
        const bubbleItems = document.querySelectorAll('[data-message-id]');
        if (bubbleItems.length > 0) {
            const ids = Array.from(bubbleItems).map(el => parseInt(el.getAttribute('data-message-id')));
            lastMessageId = Math.max(...ids);
        }

        const activeUserId = "{{ $activeUser ? $activeUser->id : '' }}";
        const activeProjectId = "{{ $activeProject ? $activeProject->id : '' }}";

        // Poll messages every 3 seconds
        setInterval(fetchNewMessages, 3000);

        function fetchNewMessages() {
            let url = `/chat/fetch?last_id=${lastMessageId}`;
            if (activeUserId) {
                url += `&user_id=${activeUserId}`;
            } else if (activeProjectId) {
                url += `&project_id=${activeProjectId}`;
            }

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (data.messages && data.messages.length > 0) {
                        data.messages.forEach(msg => {
                            // Check if bubble already rendered to prevent duplicates
                            if (document.querySelector(`[data-message-id="${msg.id}"]`)) return;

                            appendMessageBubble(msg);
                            if (msg.id > lastMessageId) {
                                lastMessageId = msg.id;
                            }
                        });
                        scrollToBottom();
                    }
                })
                .catch(err => console.error("Error polling messages:", err));
        }

        // AJAX Chat Message Form Submission Handler
        const chatForm = document.getElementById('chat-form');
        if (chatForm) {
            chatForm.addEventListener('submit', function (e) {
                e.preventDefault();
                
                const msgInput = document.getElementById('chat-message-input');
                const fileInput = document.getElementById('chat-file-input');
                
                if (!msgInput.value.trim() && (!fileInput.files || fileInput.files.length === 0)) {
                    return;
                }

                const formData = new FormData(this);

                // Send via AJAX POST
                fetch('/chat/send', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.message) {
                        // Clear inputs
                        msgInput.value = '';
                        clearFileSelected();
                        clearTaskSelected();

                        // Append instantly in UI
                        appendMessageBubble(data.message);
                        if (data.message.id > lastMessageId) {
                            lastMessageId = data.message.id;
                        }
                        scrollToBottom();
                    } else {
                        alert("Gagal mengirim pesan: " + (data.error || 'Server error'));
                    }
                })
                .catch(err => {
                    console.error("Error sending message:", err);
                    alert("Koneksi gagal saat mengirim pesan.");
                });
            });
        }

        // Helper: Dynamically append chat bubble to list
        function appendMessageBubble(msg) {
            const container = document.getElementById('messages-container');
            if (!container) return;

            const isMe = (msg.sender_id === {{ Auth::id() }});
            
            // Build bubble HTML
            let bubbleHtml = `
                <div class="flex items-end gap-2 ${isMe ? 'justify-end' : 'justify-start'}" data-message-id="${msg.id}">
            `;

            if (!isMe) {
                bubbleHtml += `
                    <div class="shrink-0">
                        ${msg.sender_avatar 
                            ? `<img src="${msg.sender_avatar}" alt="Avatar" class="w-8 h-8 rounded-lg object-cover border border-slate-200">`
                            : `<div class="w-8 h-8 rounded-lg bg-slate-900 text-white font-black text-[10px] flex items-center justify-center uppercase">${msg.sender_name.substring(0, 2)}</div>`
                        }
                    </div>
                `;
            }

            bubbleHtml += `
                <div class="max-w-[70%] rounded-2xl p-4 shadow-md space-y-1.5 transition-all
                     ${isMe ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-white border border-slate-100 text-slate-800 rounded-bl-none'}">
                    
                    ${!isMe ? `<span class="block text-[11px] font-black uppercase tracking-wider text-indigo-600 mb-0.5">${msg.sender_name}</span>` : ''}
                    
                    ${msg.message ? `<p class="text-sm leading-relaxed break-words whitespace-pre-line font-semibold">${msg.message}</p>` : ''}
                    
                    ${msg.attachment_url ? `
                        <div class="p-3 rounded-xl border flex items-center gap-2.5 mt-1.5 ${isMe ? 'bg-white/10 border-white/20 text-white' : 'bg-slate-50 border-slate-200 text-slate-700'}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                            <div class="flex-1 min-w-0">
                                <span class="block text-xs font-black truncate">${msg.attachment_name || 'Attachment File'}</span>
                            </div>
                            <a href="${msg.attachment_url}" target="_blank" class="px-2.5 py-1 bg-white text-slate-800 hover:bg-slate-100 rounded-lg text-[10px] font-black tracking-wider uppercase shrink-0 shadow-sm">Unduh</a>
                        </div>
                    ` : ''}

                    ${msg.task ? `
                        <div class="p-3.5 rounded-2xl border-l-4 flex flex-col gap-2 shadow-sm my-1.5 text-left
                             ${isMe ? 'bg-white/10 border-white/40 text-white' : 'bg-slate-50 border-indigo-400 text-slate-800'}">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[9px] font-black uppercase tracking-wider opacity-80">Referensi Tugas</span>
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded uppercase ${isMe ? 'bg-white/20 text-white' : 'bg-indigo-50 text-indigo-700'}">${msg.task.status}</span>
                            </div>
                            <div class="space-y-0.5">
                                <h4 class="text-xs font-black truncate">${msg.task.title}</h4>
                                <p class="text-[9px] opacity-75 truncate">Proyek: ${msg.task.project_name || 'Proyek'}</p>
                            </div>
                            <a href="${msg.task.project_id ? (window.location.pathname.startsWith('/management') ? `/management/projects/${msg.task.project_id}/tasks` : `/user/projects/${msg.task.project_id}?tab=tasks`) : '#'}" class="mt-1 block text-center py-1.5 rounded-lg text-[9px] font-black tracking-wider uppercase shadow-xs transition-all ${isMe ? 'bg-white text-indigo-750 hover:bg-slate-50' : 'bg-indigo-600 text-white hover:bg-indigo-700'}">Buka Detail Tugas</a>
                        </div>
                    ` : ''}
                    
                    <span class="block text-[10px] text-right mt-1 ${isMe ? 'text-white/70' : 'text-slate-400'} font-bold">${msg.created_at}</span>
                </div>
            </div>
            `;

            // If empty placeholder is shown, clear it first
            const emptyState = container.querySelector('.h-full');
            if (emptyState) {
                container.innerHTML = '';
            }

            container.insertAdjacentHTML('beforeend', bubbleHtml);
        }
    @endif
</script>
@endpush
@endsection
