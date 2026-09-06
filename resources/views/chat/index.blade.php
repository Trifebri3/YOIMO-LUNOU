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
            @if($showArchived)
                <div class="mt-2 flex items-center justify-between bg-indigo-50 border border-indigo-100 rounded-xl px-3 py-2 text-xs font-bold text-indigo-800">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <span>Arsip Chat</span>
                    </div>
                    <a href="{{ route('chat.index') }}" class="text-[10px] font-black uppercase text-indigo-600 hover:text-indigo-800 tracking-wider">Semua Chat</a>
                </div>
            @elseif($archivedChatsCount > 0)
                <a href="{{ route('chat.index', ['filter' => 'archived']) }}" class="mt-2 flex items-center justify-between bg-slate-50 hover:bg-slate-100 border border-slate-100 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 transition-all">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <span>Diarsipkan</span>
                    </div>
                    <span class="bg-slate-200 text-slate-700 text-[10px] px-1.5 py-0.5 rounded font-black">{{ $archivedChatsCount }}</span>
                </a>
            @endif
        </div>

        <!-- Scrollable Chats List -->
        <div class="flex-1 overflow-y-auto divide-y divide-slate-50">
            
            <!-- Tab 1: Personal Chats List -->
            <div id="list-personal" class="space-y-0.5">
                @forelse($contacts as $c)
                    @php
                        $isActive = ($activeUser && $activeUser->id === $c->id);
                        $unreadCount = \App\Models\ProjectMessage::where('sender_id', $c->id)
                            ->where('recipient_id', Auth::id())
                            ->where('is_read', false)
                            ->count();
                        
                        $isOnline = $c->last_seen_at && $c->last_seen_at->gt(now()->subMinutes(5));
                        $lastSeenStr = $isOnline ? 'Online' : ($c->last_seen_at ? 'Terakhir dilihat ' . $c->last_seen_at->diffForHumans() : 'Offline');
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
                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 {{ $isOnline ? 'bg-emerald-500' : 'bg-slate-300' }} rounded-full border border-white" title="{{ $lastSeenStr }}"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-black text-slate-800 truncate">{{ $c->name }}</h4>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    @if($unreadCount > 0)
                                        <span class="bg-rose-500 text-[9px] font-black text-white w-4 h-4 rounded-full flex items-center justify-center animate-pulse">
                                            {{ $unreadCount }}
                                        </span>
                                    @endif
                                    <span class="text-[9px] bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded uppercase font-bold">{{ $c->role }}</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between mt-0.5">
                                <p class="text-[10px] text-slate-400 truncate">{{ $c->position ?? 'Staff Anggota' }}</p>
                                <span class="text-[8px] text-slate-400 font-medium shrink-0">{{ $isOnline ? 'online' : ($c->last_seen_at ? $c->last_seen_at->diffForHumans() : '') }}</span>
                            </div>
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
                            @php
                                $activeOnline = $activeUser->last_seen_at && $activeUser->last_seen_at->gt(now()->subMinutes(5));
                                $activeSeenStr = $activeOnline ? 'Online' : ($activeUser->last_seen_at ? 'Terakhir dilihat ' . $activeUser->last_seen_at->diffForHumans() : 'Offline');
                            @endphp
                            <span class="text-[10px] {{ $activeOnline ? 'text-emerald-500 font-bold' : 'text-slate-400 font-medium' }} block mt-0.5">
                                {{ $activeSeenStr }}
                            </span>
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

                <div class="flex items-center gap-3">
                    <form action="{{ route('chat.toggle-archive') }}" method="POST" class="inline">
                        @csrf
                        @if($activeUser)
                            <input type="hidden" name="user_id" value="{{ $activeUser->id }}">
                        @else
                            <input type="hidden" name="project_id" value="{{ $activeProject->id }}">
                        @endif
                        <button type="submit" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 border border-slate-100 hover:bg-slate-100 hover:text-slate-800 text-slate-500 rounded-xl text-[10px] font-black tracking-wider uppercase transition-all cursor-pointer">
                            @if($isActiveChatArchived)
                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                <span>Buka Arsip</span>
                            @else
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 13l-7 7-7-7m7-7v14"></path></svg>
                                <span>Arsipkan</span>
                            @endif
                        </button>
                    </form>
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
                                @if($msg->sender && $msg->sender->avatar)
                                    <img src="{{ asset('storage/' . $msg->sender->avatar) }}" alt="Avatar" class="w-8 h-8 rounded-lg object-cover border border-slate-200">
                                @elseif($msg->sender)
                                    <div class="w-8 h-8 rounded-lg bg-slate-900 text-white font-black text-[10px] flex items-center justify-center uppercase">
                                        {{ substr($msg->sender->name, 0, 2) }}
                                    </div>
                                @else
                                    <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white font-black text-[10px] flex items-center justify-center uppercase" title="Klien Portal">
                                        {{ substr($msg->client_name ?? 'KL', 0, 2) }}
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Chat Bubble Box -->
                        <div class="max-w-[70%] rounded-2xl p-4 shadow-md space-y-1.5 transition-all
                             {{ $isMe ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-white border border-slate-100 text-slate-800 rounded-bl-none' }}">
                            
                            @if(!$isMe)
                                <!-- Sender Name & Badge inside bubble -->
                                <div class="flex items-center gap-1.5 mb-0.5">
                                    <span class="block text-[11px] font-black uppercase tracking-wider text-indigo-600">{{ $msg->sender ? $msg->sender->name : ($msg->client_name ?? 'Klien (Portal)') }}</span>
                                    @if(!$msg->sender)
                                        <span class="px-1.5 py-0.2 bg-emerald-100 text-emerald-800 text-[8px] font-black uppercase rounded">Klien Portal</span>
                                    @endif
                                </div>
                            @endif

                            @if($msg->message_type === 'kendala')
                                <div class="flex items-center justify-between gap-2">
                                    <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider {{ $isMe ? 'bg-rose-500 text-white' : 'bg-rose-100 text-rose-700 border border-rose-200' }}">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        <span>Tercatat: Hambatan / Kendala</span>
                                    </div>
                                    <div id="chat-badge-status-{{ $msg->id }}">
                                        @if($msg->is_resolved)
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded text-[8px] font-black uppercase {{ $isMe ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800' }}">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                Terselesaikan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded text-[8px] font-black uppercase {{ $isMe ? 'bg-white/20 text-amber-200' : 'bg-amber-100 text-amber-800' }}">
                                                Menunggu Solusi
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @elseif($msg->message_type === 'question')
                                <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider {{ $isMe ? 'bg-sky-500 text-white' : 'bg-sky-100 text-sky-700 border border-sky-200' }}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Tercatat: Pertanyaan / Diskusi Klien</span>
                                </div>
                            @endif

                            @if($msg->message)
                                <p class="text-sm leading-relaxed break-words whitespace-pre-line font-semibold">{{ $msg->message }}</p>
                            @endif

                            <!-- Attachment View inside bubble -->
                            @if($msg->attachment_file)
                                @if($msg->is_image)
                                    <div class="mt-2 rounded-xl overflow-hidden border {{ $isMe ? 'border-white/20' : 'border-slate-200' }}">
                                        <a href="{{ asset('storage/' . $msg->attachment_file) }}" target="_blank" class="block group relative">
                                            <img src="{{ asset('storage/' . $msg->attachment_file) }}" alt="{{ $msg->attachment_name }}" class="max-h-60 w-full object-cover rounded-xl transition-transform duration-200 group-hover:scale-[1.02]">
                                            <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                <span>Lihat Screenshot</span>
                                            </div>
                                        </a>
                                        <div class="p-2 flex items-center justify-between text-[10px] {{ $isMe ? 'bg-white/10 text-white' : 'bg-slate-50 text-slate-600' }}">
                                            <span class="truncate max-w-[160px] font-medium">{{ $msg->attachment_name ?? 'Screenshot' }}</span>
                                            <a href="{{ asset('storage/' . $msg->attachment_file) }}" download class="font-bold underline hover:opacity-80">Unduh</a>
                                        </div>
                                    </div>
                                @else
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

                            <!-- Interactive Checklist & Resolution Box for Kendala in Chat Center -->
                            @if($msg->message_type === 'kendala')
                                <div id="chat-resolution-card-{{ $msg->id }}" class="mt-2.5 pt-2 border-t {{ $isMe ? 'border-white/20' : 'border-slate-100' }}">
                                    @if(!$msg->is_resolved)
                                        <div class="rounded-xl p-2.5 border {{ $isMe ? 'bg-white/10 border-white/20 text-white' : 'bg-amber-50/90 border-amber-200/80 text-slate-800' }} space-y-2 text-left">
                                            <div class="flex items-center justify-between gap-2">
                                                <div class="flex items-center gap-1.5 text-xs font-black {{ $isMe ? 'text-amber-200' : 'text-amber-800' }}">
                                                    <svg class="w-3.5 h-3.5 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    <span>Hambatan Belum Selesai</span>
                                                </div>
                                                <button type="button" onclick="toggleChatResolutionPrompt({{ $msg->id }})" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[9px] font-black uppercase tracking-wider transition-all flex items-center gap-1 shadow-2xs cursor-pointer">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                    <span>Tandai Selesai</span>
                                                </button>
                                            </div>
                                            
                                            <!-- Inline Note Box -->
                                            <div id="chat-resolve-box-{{ $msg->id }}" class="hidden pt-2 border-t {{ $isMe ? 'border-white/20' : 'border-amber-200/60' }} space-y-2">
                                                <label class="block text-[9px] font-black uppercase tracking-wider {{ $isMe ? 'text-white/80' : 'text-slate-600' }}">Catatan Solusi / Penyelesaian:</label>
                                                <textarea id="chat-resolve-note-{{ $msg->id }}" rows="2" placeholder="Tuliskan solusi penyelesaian hambatan ini..." class="w-full p-2 text-xs bg-white text-slate-800 border border-slate-200 rounded-lg font-medium focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"></textarea>
                                                <div class="flex items-center justify-end gap-2">
                                                    <button type="button" onclick="toggleChatResolutionPrompt({{ $msg->id }})" class="px-2 py-1 text-[9px] font-bold {{ $isMe ? 'text-white/80 hover:text-white' : 'text-slate-500 hover:text-slate-700' }} cursor-pointer">Batal</button>
                                                    <button type="button" onclick="submitChatResolution({{ $msg->id }}, true)" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[9px] font-black uppercase tracking-wider flex items-center gap-1 shadow-2xs cursor-pointer">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                        <span>Simpan Solusi</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="rounded-xl p-2.5 border {{ $isMe ? 'bg-white/10 border-white/20 text-white' : 'bg-emerald-50/90 border-emerald-200/80 text-slate-800' }} space-y-1.5 text-left">
                                            <div class="flex items-center justify-between gap-2">
                                                <div class="flex items-center gap-1.5 text-xs font-black {{ $isMe ? 'text-emerald-200' : 'text-emerald-800' }}">
                                                    <svg class="w-3.5 h-3.5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    <span>Hambatan Terselesaikan</span>
                                                </div>
                                                <button type="button" onclick="submitChatResolution({{ $msg->id }}, false)" class="px-2 py-0.5 text-[8px] font-bold {{ $isMe ? 'text-white/70 hover:text-white hover:bg-white/10' : 'text-slate-500 hover:text-rose-600 hover:bg-rose-50' }} rounded border border-transparent transition-all cursor-pointer" title="Buka kembali kendala jika masih butuh penanganan">
                                                    Buka Kembali
                                                </button>
                                            </div>
                                            <div class="text-[9px] {{ $isMe ? 'bg-white/10 text-white' : 'bg-white/80 text-emerald-950 border border-emerald-100' }} p-2 rounded-lg space-y-1">
                                                <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                                    <span class="font-bold">Diselesaikan oleh:</span>
                                                    <span class="font-black">{{ $msg->resolver_name ?? 'Tim' }}</span>
                                                    @if($msg->resolved_at)
                                                        <span class="opacity-70 font-normal">({{ $msg->resolved_at->format('H:i') }})</span>
                                                    @endif
                                                </div>
                                                @if($msg->resolution_note)
                                                    <div class="pt-1 border-t {{ $isMe ? 'border-white/10' : 'border-emerald-100' }} mt-0.5">
                                                        <span class="font-bold block text-[8px] uppercase tracking-wider {{ $isMe ? 'text-white/80' : 'text-emerald-800' }}">Catatan Solusi:</span>
                                                        <p class="font-medium {{ $isMe ? 'text-white/90' : 'text-slate-700' }} italic leading-relaxed whitespace-pre-line">{{ $msg->resolution_note }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Bubble Footer: Timestamp & Checkmarks -->
                            <div class="flex items-center justify-end gap-1 mt-1">
                                <span class="block text-[9px] {{ $isMe ? 'text-white/70' : 'text-slate-400' }} font-bold">
                                    {{ $msg->created_at->format('H:i') }}
                                </span>
                                @if($isMe)
                                    @if($msg->is_read)
                                        <!-- Double ticks blue/sky -->
                                        <svg class="w-3.5 h-3.5 text-sky-300 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7M5 12l7 7-7-7"></path>
                                        </svg>
                                    @else
                                        <!-- Single tick -->
                                        <svg class="w-3.5 h-3.5 text-white/50 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @endif
                                @endif
                            </div>
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

                        @if($activeProject)
                            <!-- Category Selector in Project Chat -->
                            <div class="relative">
                                <select name="message_type" id="chat-message-type-select" 
                                        class="px-2.5 py-3 bg-slate-50 border border-slate-200 text-slate-700 rounded-2xl text-xs font-black focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all cursor-pointer shadow-xs"
                                        title="Tercatat sebagai kategori apa">
                                    <option value="chat">Diskusi</option>
                                    <option value="kendala">Hambatan</option>
                                    <option value="question">Pertanyaan</option>
                                </select>
                            </div>
                        @endif

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
                            const existing = document.querySelector(`[data-message-id="${msg.id}"]`);
                            if (existing) {
                                // Sync resolution state if changed
                                if (msg.message_type === 'kendala') {
                                    const isMe = (msg.sender_id === {{ Auth::id() }});
                                    const card = document.getElementById(`chat-resolution-card-${msg.id}`);
                                    if (card) {
                                        const temp = document.createElement('div');
                                        temp.innerHTML = buildChatResolutionCardHtml(msg, isMe);
                                        card.replaceWith(temp.firstElementChild);
                                    }
                                    const badgeContainer = document.getElementById(`chat-badge-status-${msg.id}`);
                                    if (badgeContainer) {
                                        badgeContainer.innerHTML = msg.is_resolved ? `
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded text-[8px] font-black uppercase ${isMe ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800'}">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                Terselesaikan
                                            </span>
                                        ` : `
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded text-[8px] font-black uppercase ${isMe ? 'bg-white/20 text-amber-200' : 'bg-amber-100 text-amber-800'}">
                                                Menunggu Solusi
                                            </span>
                                        `;
                                    }
                                }
                                return;
                            }

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

        // Resolution Checklist Handlers for Chat Center
        function buildChatResolutionCardHtml(msg, isMe) {
            if (msg.message_type !== 'kendala') return '';

            if (!msg.is_resolved) {
                return `
                    <div id="chat-resolution-card-${msg.id}" class="mt-2.5 pt-2 border-t ${isMe ? 'border-white/20' : 'border-slate-100'}">
                        <div class="rounded-xl p-2.5 border ${isMe ? 'bg-white/10 border-white/20 text-white' : 'bg-amber-50/90 border-amber-200/80 text-slate-800'} space-y-2 text-left">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-1.5 text-xs font-black ${isMe ? 'text-amber-200' : 'text-amber-800'}">
                                    <svg class="w-3.5 h-3.5 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Hambatan Belum Selesai</span>
                                </div>
                                <button type="button" onclick="toggleChatResolutionPrompt(${msg.id})" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[9px] font-black uppercase tracking-wider transition-all flex items-center gap-1 shadow-2xs cursor-pointer">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    <span>Tandai Selesai</span>
                                </button>
                            </div>
                            
                            <!-- Inline Note Box -->
                            <div id="chat-resolve-box-${msg.id}" class="hidden pt-2 border-t ${isMe ? 'border-white/20' : 'border-amber-200/60'} space-y-2">
                                <label class="block text-[9px] font-black uppercase tracking-wider ${isMe ? 'text-white/80' : 'text-slate-600'}">Catatan Solusi / Penyelesaian:</label>
                                <textarea id="chat-resolve-note-${msg.id}" rows="2" placeholder="Tuliskan solusi penyelesaian hambatan ini..." class="w-full p-2 text-xs bg-white text-slate-800 border border-slate-200 rounded-lg font-medium focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"></textarea>
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" onclick="toggleChatResolutionPrompt(${msg.id})" class="px-2 py-1 text-[9px] font-bold ${isMe ? 'text-white/80 hover:text-white' : 'text-slate-500 hover:text-slate-700'} cursor-pointer">Batal</button>
                                    <button type="button" onclick="submitChatResolution(${msg.id}, true)" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[9px] font-black uppercase tracking-wider flex items-center gap-1 shadow-2xs cursor-pointer">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        <span>Simpan Solusi</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                return `
                    <div id="chat-resolution-card-${msg.id}" class="mt-2.5 pt-2 border-t ${isMe ? 'border-white/20' : 'border-slate-100'}">
                        <div class="rounded-xl p-2.5 border ${isMe ? 'bg-white/10 border-white/20 text-white' : 'bg-emerald-50/90 border-emerald-200/80 text-slate-800'} space-y-1.5 text-left">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-1.5 text-xs font-black ${isMe ? 'text-emerald-200' : 'text-emerald-800'}">
                                    <svg class="w-3.5 h-3.5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Hambatan Terselesaikan</span>
                                </div>
                                <button type="button" onclick="submitChatResolution(${msg.id}, false)" class="px-2 py-0.5 text-[8px] font-bold ${isMe ? 'text-white/70 hover:text-white hover:bg-white/10' : 'text-slate-500 hover:text-rose-600 hover:bg-rose-50'} rounded border border-transparent transition-all cursor-pointer" title="Buka kembali kendala jika masih butuh penanganan">
                                    Buka Kembali
                                </button>
                            </div>
                            <div class="text-[9px] ${isMe ? 'bg-white/10 text-white' : 'bg-white/80 text-emerald-950 border border-emerald-100'} p-2 rounded-lg space-y-1">
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                    <span class="font-bold">Diselesaikan oleh:</span>
                                    <span class="font-black">${msg.resolver_name || 'Tim'}</span>
                                    ${msg.resolved_at ? `<span class="opacity-70 font-normal">(${msg.resolved_at})</span>` : ''}
                                </div>
                                ${msg.resolution_note ? `
                                    <div class="pt-1 border-t ${isMe ? 'border-white/10' : 'border-emerald-100'} mt-0.5">
                                        <span class="font-bold block text-[8px] uppercase tracking-wider ${isMe ? 'text-white/80' : 'text-emerald-800'}">Catatan Solusi:</span>
                                        <p class="font-medium ${isMe ? 'text-white/90' : 'text-slate-700'} italic leading-relaxed whitespace-pre-line">${msg.resolution_note}</p>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }
        }

        function toggleChatResolutionPrompt(id) {
            const box = document.getElementById(`chat-resolve-box-${id}`);
            if (box) {
                box.classList.toggle('hidden');
                if (!box.classList.contains('hidden')) {
                    const textarea = document.getElementById(`chat-resolve-note-${id}`);
                    if (textarea) textarea.focus();
                }
            }
        }

        function submitChatResolution(id, isResolved) {
            const url = `/chat/messages/${id}/toggle-resolution`;
            const noteEl = document.getElementById(`chat-resolve-note-${id}`);
            const note = noteEl ? noteEl.value.trim() : '';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    is_resolved: isResolved,
                    resolution_note: note
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    const item = document.querySelector(`[data-message-id="${id}"]`);
                    if (item) {
                        const isMe = item.classList.contains('justify-end');
                        const cardContainer = document.getElementById(`chat-resolution-card-${id}`);
                        if (cardContainer) {
                            const temp = document.createElement('div');
                            temp.innerHTML = buildChatResolutionCardHtml({
                                id: id,
                                message_type: 'kendala',
                                is_resolved: isResolved,
                                resolver_name: data.data.resolver_name,
                                resolved_at: data.data.resolved_at,
                                resolution_note: data.data.resolution_note
                            }, isMe);
                            cardContainer.replaceWith(temp.firstElementChild);
                        }

                        const badgeContainer = document.getElementById(`chat-badge-status-${id}`);
                        if (badgeContainer) {
                            badgeContainer.innerHTML = isResolved ? `
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded text-[8px] font-black uppercase ${isMe ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800'}">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    Terselesaikan
                                </span>
                            ` : `
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded text-[8px] font-black uppercase ${isMe ? 'bg-white/20 text-amber-200' : 'bg-amber-100 text-amber-800'}">
                                    Menunggu Solusi
                                </span>
                            `;
                        }
                    }
                } else {
                    alert('Gagal memperbarui status: ' + (data.error || 'Terjadi kesalahan'));
                }
            })
            .catch(err => {
                console.error('Error toggling resolution in chat:', err);
                alert('Gagal menghubungi server.');
            });
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
                            : `<div class="w-8 h-8 rounded-lg ${msg.sender_id ? 'bg-slate-900' : 'bg-emerald-600'} text-white font-black text-[10px] flex items-center justify-center uppercase">${(msg.sender_name || 'KL').substring(0, 2)}</div>`
                        }
                    </div>
                `;
            }

            bubbleHtml += `
                <div class="max-w-[70%] rounded-2xl p-4 shadow-md space-y-1.5 transition-all
                     ${isMe ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-white border border-slate-100 text-slate-800 rounded-bl-none'}">
                    
                    ${!isMe ? `
                        <div class="flex items-center gap-1.5 mb-0.5">
                            <span class="block text-[11px] font-black uppercase tracking-wider text-indigo-600">${msg.sender_name || 'Klien'}</span>
                            ${!msg.sender_id ? `<span class="px-1.5 py-0.2 bg-emerald-100 text-emerald-800 text-[8px] font-black uppercase rounded">Klien Portal</span>` : ''}
                        </div>
                    ` : ''}

                    ${msg.message_type === 'kendala' ? `
                        <div class="flex items-center justify-between gap-2">
                            <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider ${isMe ? 'bg-rose-500 text-white' : 'bg-rose-100 text-rose-700 border border-rose-200'}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                <span>Tercatat: Hambatan / Kendala</span>
                            </div>
                            <div id="chat-badge-status-${msg.id}">
                                ${msg.is_resolved ? `
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded text-[8px] font-black uppercase ${isMe ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800'}">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        Terselesaikan
                                    </span>
                                ` : `
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded text-[8px] font-black uppercase ${isMe ? 'bg-white/20 text-amber-200' : 'bg-amber-100 text-amber-800'}">
                                        Menunggu Solusi
                                    </span>
                                `}
                            </div>
                        </div>
                    ` : (msg.message_type === 'question' ? `
                        <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider ${isMe ? 'bg-sky-500 text-white' : 'bg-sky-100 text-sky-700 border border-sky-200'}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Tercatat: Pertanyaan / Diskusi Klien</span>
                        </div>
                    ` : '')}
                    
                    ${msg.message ? `<p class="text-sm leading-relaxed break-words whitespace-pre-line font-semibold">${msg.message}</p>` : ''}
                    
                    ${msg.attachment_url ? (
                        msg.is_image ? `
                            <div class="mt-2 rounded-xl overflow-hidden border ${isMe ? 'border-white/20' : 'border-slate-200'}">
                                <a href="${msg.attachment_url}" target="_blank" class="block group relative">
                                    <img src="${msg.attachment_url}" alt="${msg.attachment_name || 'Screenshot'}" class="max-h-60 w-full object-cover rounded-xl transition-transform duration-200 group-hover:scale-[1.02]">
                                    <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <span>Lihat Screenshot</span>
                                    </div>
                                </a>
                                <div class="p-2 flex items-center justify-between text-[10px] ${isMe ? 'bg-white/10 text-white' : 'bg-slate-50 text-slate-600'}">
                                    <span class="truncate max-w-[160px] font-medium">${msg.attachment_name || 'Screenshot'}</span>
                                    <a href="${msg.attachment_url}" download class="font-bold underline hover:opacity-80">Unduh</a>
                                </div>
                            </div>
                        ` : `
                            <div class="p-3 rounded-xl border flex items-center gap-2.5 mt-1.5 ${isMe ? 'bg-white/10 border-white/20 text-white' : 'bg-slate-50 border-slate-200 text-slate-700'}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                <div class="flex-1 min-w-0">
                                    <span class="block text-xs font-black truncate">${msg.attachment_name || 'Attachment File'}</span>
                                </div>
                                <a href="${msg.attachment_url}" target="_blank" class="px-2.5 py-1 bg-white text-slate-800 hover:bg-slate-100 rounded-lg text-[10px] font-black tracking-wider uppercase shrink-0 shadow-sm">Unduh</a>
                            </div>
                        `
                    ) : ''}

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

                    ${buildChatResolutionCardHtml(msg, isMe)}
                    
                    <div class="flex items-center justify-end gap-1 mt-1">
                        <span class="block text-[9px] ${isMe ? 'text-white/70' : 'text-slate-400'} font-bold">${msg.created_at}</span>
                        ${isMe ? (msg.is_read 
                            ? `<svg class="w-3.5 h-3.5 text-sky-300 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7M5 12l7 7-7-7"></path></svg>`
                            : `<svg class="w-3.5 h-3.5 text-white/50 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>`
                        ) : ''}
                    </div>
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

