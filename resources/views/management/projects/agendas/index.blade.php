@extends('management.layouts.app')

@section('title', 'Agenda & Kegiatan - ' . $project->name)

@section('content')
<div class="max-w-7xl mx-auto space-y-8 font-sans">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Header Agenda & Project Link -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase px-2.5 py-0.5 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200">
                        WORKSPACE AGENDA & EVENT
                    </span>
                    <span class="text-xs font-semibold text-slate-400">• {{ $project->name }}</span>
                </div>
                <h1 class="text-2xl font-black text-slate-900 mt-1 tracking-tight">Kalender Agenda & Aktivitas Tim</h1>
                <p class="text-xs text-slate-400 mt-0.5">Jadwalkan rapat mingguan rutin, aktivitas kebugaran, liburan, roadshow, dan agenda kebersamaan.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button type="button" onclick="openGenerateAgendasModal()" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-violet-600 text-white text-xs font-bold rounded-xl hover:from-indigo-700 hover:to-violet-700 shadow-sm flex items-center gap-2 hover:shadow transition-all cursor-pointer font-sans">
                    <svg class="w-3.5 h-3.5 animate-pulse text-indigo-200" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                    </svg>
                    <span>Generate Agenda via LUNOU AI</span>
                </button>

                <a href="{{ route('management.projects.tasks.index', $project->id) }}" class="px-4 py-2 bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-100 transition-all font-sans">
                    Papan Tugas
                </a>
                <a href="{{ route('management.projects.roadmaps.index', $project->id) }}" class="px-4 py-2 bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-100 transition-all font-sans">
                    Linimasa
                </a>
                <a href="{{ route('management.projects.show', $project->id) }}" class="px-4 py-2 bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-100 transition-all font-sans">
                    Detail Project
                </a>
            </div>
        </div>

        <!-- Filter Kategori Kancing Cepat -->
        <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2">
            <a href="{{ route('management.projects.agendas.index', $project->id) }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ empty($selectedCategory) ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                Semua Agenda
            </a>
            @php
                $categories = [
                    'Meeting Online'       => 'bg-blue-50 text-blue-700 border-blue-200',
                    'Meeting Offline'      => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                    'Olahraga & Kesehatan' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'Liburan & Outing'     => 'bg-amber-50 text-amber-700 border-amber-200',
                    'Nonton & Hiburan'     => 'bg-purple-50 text-purple-700 border-purple-200',
                    'Roadshow & Kunjungan' => 'bg-rose-50 text-rose-700 border-rose-200',
                    'Workshop & Pelatihan' => 'bg-teal-50 text-teal-700 border-teal-200',
                ];
            @endphp
            @foreach($categories as $cat => $style)
                <a href="{{ route('management.projects.agendas.index', [$project->id, 'category' => $cat]) }}" 
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $selectedCategory === $cat ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">

        <!-- Kolom Kiri: Daftar Kartu Agenda (7 Kolom) -->
        <div class="xl:col-span-7 space-y-6">
            <h2 class="text-base font-black text-slate-900 tracking-tight">Daftar Jadwal & Agenda Mendatang</h2>

            @forelse($agendas as $agenda)
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-5 hover:shadow-md transition-all">
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <!-- Badge Kategori -->
                                <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider border {{ $categories[$agenda->category] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ $agenda->category }}
                                </span>

                                <!-- Badge Status -->
                                @php
                                    $stClass = match($agenda->status) {
                                        'Ongoing'   => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'Completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'Cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        default     => 'bg-slate-100 text-slate-600 border-slate-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider border {{ $stClass }}">
                                    {{ $agenda->status }}
                                </span>

                                <!-- Badge Recurrence (Sifat Berulang) -->
                                @if($agenda->recurrence !== 'once')
                                    <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        Rutin: {{ ucfirst($agenda->recurrence) }} {{ $agenda->recurrence_days ? '(' . $agenda->recurrence_days . ')' : '' }}
                                    </span>
                                @endif
                            </div>

                            <h3 class="text-lg font-black text-slate-900 mt-1">{{ $agenda->title }}</h3>
                        </div>

                        <!-- Date Badge Box -->
                        <div class="p-3 bg-slate-50 border border-slate-100 rounded-2xl text-center shrink-0 min-w-[100px]">
                            <span class="text-[10px] font-black text-indigo-600 uppercase tracking-wider block">
                                {{ $agenda->start_date->format('M Y') }}
                            </span>
                            <span class="text-xl font-black text-slate-900 block leading-tight">
                                {{ $agenda->start_date->format('d') }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-semibold block">
                                {{ $agenda->start_time ? date('H:i', strtotime($agenda->start_time)) : 'All Day' }}
                            </span>
                        </div>
                    </div>

                    @if($agenda->description)
                        <p class="text-xs text-slate-600 leading-relaxed font-medium bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                            {{ $agenda->description }}
                        </p>
                    @endif

                    <!-- Lokasi / Link Platform Meeting -->
                    <div class="p-3.5 bg-slate-50/80 rounded-2xl border border-slate-200/60 text-xs space-y-1">
                        <div class="flex items-center gap-2 font-bold text-slate-700">
                            @if($agenda->location_type === 'online')
                                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                                <span>Platform Virtual Online:</span>
                            @else
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>Tempat / Lokasi Offline:</span>
                            @endif
                        </div>
                        
                        @if($agenda->location_type === 'online' && $agenda->meeting_url)
                            <a href="{{ $agenda->meeting_url }}" target="_blank" class="text-indigo-600 font-bold underline block break-all">
                                {{ $agenda->meeting_url }} &rarr; Buka Link Meeting
                            </a>
                        @elseif($agenda->location_address)
                            <span class="text-slate-600 font-medium block">{{ $agenda->location_address }}</span>
                        @else
                            <span class="text-slate-400 italic">Detail lokasi belum dicantumkan.</span>
                        @endif
                    </div>

                    <!-- Peserta / Anggota yang Terlibat -->
                    @if(!empty($agenda->attendee_ids))
                        <div class="space-y-2 pt-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">
                                Peserta Terdaftar ({{ count($agenda->attendee_ids) }} Orang):
                            </span>
                            <div class="flex flex-wrap items-center gap-2">
                                @foreach($agenda->attendee_ids as $uId)
                                    @php $att = \App\Models\User::find($uId); @endphp
                                    @if($att)
                                        <div class="flex items-center gap-1.5 px-2.5 py-1 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-700 shadow-sm">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>{{ $att->name }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Catatan Pasca Agenda / Notulensi -->
                    @if($agenda->agenda_notes)
                        <div class="p-4 bg-emerald-50/50 border border-emerald-100 rounded-2xl text-xs space-y-1">
                            <span class="font-bold text-emerald-900 block">Notulensi & Catatan Kegiatan:</span>
                            <p class="text-slate-700 leading-relaxed font-medium whitespace-pre-line">{{ $agenda->agenda_notes }}</p>
                        </div>
                    @endif

                    <!-- Action Bar Bawah -->
                    <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100">
                        <!-- Update Status Form -->
                        <form method="POST" action="{{ route('management.projects.agendas.update-status', [$project->id, $agenda->id]) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:ring-indigo-500">
                                <option value="Scheduled" {{ $agenda->status === 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="Ongoing" {{ $agenda->status === 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
                                <option value="Completed" {{ $agenda->status === 'Completed' ? 'selected' : '' }}>Completed (Selesai)</option>
                                <option value="Cancelled" {{ $agenda->status === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </form>

                        <div class="flex items-center gap-2">
                            <!-- Tombol Tambah/Edit Notulensi -->
                            <button type="button" onclick="openNotesModal({{ $agenda->id }}, '{{ addslashes($agenda->title) }}', '{{ addslashes($agenda->agenda_notes ?? '') }}')" class="px-3.5 py-1.5 bg-indigo-50 border border-indigo-200 text-indigo-700 hover:bg-indigo-100 text-xs font-bold rounded-xl transition-all">
                                {{ $agenda->agenda_notes ? 'Perbarui Notulensi' : '+ Catatan Notulensi' }}
                            </button>

                            <!-- Hapus Agenda -->
                            <form method="POST" action="{{ route('management.projects.agendas.destroy', [$project->id, $agenda->id]) }}" onsubmit="return confirm('Hapus agenda ini dari kalender?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-xl transition-all" title="Hapus Agenda">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            @empty
                <div class="bg-white border border-dashed border-slate-200 rounded-3xl p-12 text-center">
                    <p class="text-xs font-medium text-slate-400">Belum ada agenda kegiatan yang dijadwalkan pada kategori ini.</p>
                </div>
            @endforelse
        </div>

        <!-- Kolom Kanan: Form Jadwalkan Agenda Baru (5 Kolom) -->
        <div class="xl:col-span-5 space-y-6">
            <div class="bg-white border border-slate-100 rounded-3xl p-7 shadow-sm space-y-5 sticky top-24 max-h-[calc(100vh-8rem)] overflow-y-auto">
                <div>
                    <h2 class="text-sm font-black text-slate-900 tracking-tight">+ Jadwalkan Agenda / Acara</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Pilih secara manual atau gunakan LUNOU AI Copilot.</p>
                </div>

                <!-- LUNOU AI Agenda Copilot Panel -->
                <div class="p-4 bg-slate-900 text-slate-100 border border-slate-800 rounded-2xl space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-indigo-500/10 border border-indigo-500/25 p-0.5 shrink-0">
                                <img src="{{ asset('icon/11.png') }}" alt="LUNOU" class="w-full h-full object-contain">
                            </div>
                            <span class="text-[10px] font-black text-indigo-300 uppercase tracking-wider">LUNOU AI Agenda Copilot</span>
                        </div>
                        <button type="button" id="agenda-ai-mic-btn" onclick="toggleAgendaSpeech()" class="p-1.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-350 hover:text-white rounded-xl transition-all flex items-center justify-center relative" title="Gunakan Voice Command AI">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                            </svg>
                        </button>
                    </div>

                    <p class="text-[9px] text-slate-450 leading-normal">
                        Ketik instruksi agenda atau klik mic untuk bicara. LUNOU akan merancang detail agenda, memilih kategori, menentukan waktu, tempat, dan mengundang tim secara otomatis.
                    </p>

                    <div class="relative flex items-center gap-1.5">
                        <input type="text" id="ai-agenda-text-prompt" placeholder="Contoh: 'Tolong buat rapat Zoom besok jam 10 pagi, undang finance'" class="flex-1 px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-[11px] text-white focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 outline-none placeholder:text-slate-500 font-semibold" onkeydown="if(event.key === 'Enter') generateAgendaWithAiPrompt()">
                        <button type="button" id="btn-run-agenda-ai" onclick="generateAgendaWithAiPrompt()" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black rounded-xl transition-all shrink-0">
                            Rancang
                        </button>
                    </div>

                    <!-- Voice / Status Feedback message -->
                    <div id="agenda-ai-feedback-msg" class="hidden text-[10px] text-indigo-300 font-bold animate-pulse"></div>
                </div>

                <form method="POST" action="{{ route('management.projects.agendas.store', $project->id) }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Nama / Judul Agenda</label>
                        <input type="text" name="title" required placeholder="misal: Rapat Evaluasi Mingguan / Lari Pagi" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-indigo-500">
                    </div>

                    <!-- Kategori Acara Beragam -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Kategori Kegiatan</label>
                        <select name="category" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-indigo-700 focus:ring-indigo-500">
                            <option value="Meeting Online">Meeting Online (Google Meet / Zoom)</option>
                            <option value="Meeting Offline">Meeting Offline (Kantor / Klien)</option>
                            <option value="Olahraga & Kesehatan">Olahraga & Kesehatan (Lari / Badminton / Gym)</option>
                            <option value="Liburan & Outing">Liburan & Outing (Gathering / Liburan Tim)</option>
                            <option value="Nonton & Hiburan">Nonton & Hiburan (Bioskop / Hangout / Makan)</option>
                            <option value="Roadshow & Kunjungan">Roadshow & Kunjungan (Pameran / Kunjungan Lapangan)</option>
                            <option value="Workshop & Pelatihan">Workshop & Pelatihan (Sharing Session)</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Deskripsi / Susunan Acara</label>
                        <textarea name="description" rows="2" placeholder="Tuliskan tujuan acara, rundown, atau pakaian yang dikenakan..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500"></textarea>
                    </div>

                    <!-- Sifat Berulang (Recurrence) -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Sifat Jadwal</label>
                            <select name="recurrence" id="recurrenceSelect" onchange="toggleRecurrenceDays()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                                <option value="once">Sekali Jalan (One-Time)</option>
                                <option value="weekly">Rutin Mingguan</option>
                                <option value="daily">Rutin Harian</option>
                                <option value="monthly">Rutin Bulanan</option>
                            </select>
                        </div>
                        <div id="recurrenceDaysContainer" class="hidden">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Hari Berulang</label>
                            <input type="text" name="recurrence_days" placeholder="Senin, Kamis" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                        </div>
                    </div>

                    <!-- Tanggal & Jam -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Tgl Mulai</label>
                            <input type="date" name="start_date" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Jam Mulai</label>
                            <input type="time" name="start_time" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                        </div>
                    </div>

                    <!-- Tipe Lokasi -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Tipe Lokasi</label>
                        <select name="location_type" id="locationTypeSelect" onchange="toggleLocationFields()" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                            <option value="online">Virtual / Online (Tautan Meeting)</option>
                            <option value="offline">Tatap Muka / Lokasi Offline</option>
                        </select>
                    </div>

                    <!-- Field Input URL Meeting -->
                    <div id="meetingUrlBox">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Link Google Meet / Zoom</label>
                        <input type="url" name="meeting_url" placeholder="https://meet.google.com/..." class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                    </div>

                    <!-- Field Input Alamat Offline -->
                    <div id="locationAddressBox" class="hidden">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Alamat Tempat / Ruangan</label>
                        <input type="text" name="location_address" placeholder="misal: Ruang Rapat Lt. 2 / Lapangan Saparua" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                    </div>

                    <!-- Undang Peserta Tim (Multi Select) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Undang Anggota Tim</label>
                        <div class="max-h-32 overflow-y-auto p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-1.5">
                            @foreach($teamMembers as $tm)
                                <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer">
                                    <input type="checkbox" name="attendee_ids[]" value="{{ $tm->id }}" class="rounded text-indigo-600 focus:ring-indigo-500">
                                    <span class="font-medium">{{ $tm->name }} ({{ strtoupper($tm->role) }})</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md transition-all">
                        Jadwalkan Agenda
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>

<!-- Modal Notulensi & Catatan Pasca Agenda -->
<div id="notesModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-7 shadow-2xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-black text-slate-900">Catatan & Notulensi Agenda</h3>
                <span id="modalAgendaTitle" class="text-xs text-indigo-600 font-bold block mt-0.5"></span>
            </div>
            <button type="button" onclick="closeNotesModal()" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
        </div>

        <form id="notesForm" method="POST" action="" class="space-y-4">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="Completed">

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Hasil Rapat / Dokumentasi Kegiatan</label>
                <textarea id="agendaNotesInput" name="agenda_notes" rows="5" placeholder="Tuliskan keputusan rapat, ringkasan sharing session, atau dokumentasi..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeNotesModal()" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl">Batal</button>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md">
                    Simpan Notulensi & Tandai Selesai
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleRecurrenceDays() {
        const select = document.getElementById('recurrenceSelect');
        const container = document.getElementById('recurrenceDaysContainer');
        if (select.value === 'weekly') {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    }

    function toggleLocationFields() {
        const type = document.getElementById('locationTypeSelect').value;
        const meetingBox = document.getElementById('meetingUrlBox');
        const addressBox = document.getElementById('locationAddressBox');

        if (type === 'online') {
            meetingBox.classList.remove('hidden');
            addressBox.classList.add('hidden');
        } else {
            meetingBox.classList.add('hidden');
            addressBox.classList.remove('hidden');
        }
    }

    function openNotesModal(agendaId, agendaTitle, notes) {
        document.getElementById('notesForm').action = `/management/projects/{{ $project->id }}/agendas/${agendaId}/status`;
        document.getElementById('modalAgendaTitle').innerText = agendaTitle;
        document.getElementById('agendaNotesInput').value = notes;
        document.getElementById('notesModal').classList.remove('hidden');
    }

    function closeNotesModal() {
        document.getElementById('notesModal').classList.add('hidden');
    }

    let agendaRecognition = null;
    let isAgendaListening = false;

    function toggleAgendaSpeech() {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) {
            alert("Browser Anda tidak mendukung Web Speech API. Silakan gunakan Google Chrome atau Microsoft Edge.");
            return;
        }

        const voiceBtn = document.getElementById('agenda-ai-mic-btn');
        const feedbackMsg = document.getElementById('agenda-ai-feedback-msg');
        const promptInput = document.getElementById('ai-agenda-text-prompt');

        if (isAgendaListening) {
            if (agendaRecognition) agendaRecognition.stop();
            return;
        }

        agendaRecognition = new SpeechRecognition();
        agendaRecognition.lang = 'id-ID';
        agendaRecognition.interimResults = false;
        agendaRecognition.maxAlternatives = 1;

        agendaRecognition.onstart = function() {
            isAgendaListening = true;
            feedbackMsg.innerText = '🔴 LUNOU Mendengarkan suara Anda...';
            feedbackMsg.classList.remove('hidden');
            voiceBtn.classList.remove('bg-slate-800', 'text-slate-350');
            voiceBtn.classList.add('bg-red-500', 'text-white', 'animate-pulse');
        };

        agendaRecognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            promptInput.value = transcript;
            feedbackMsg.innerText = '✨ Menganalisis perintah suara...';
            generateAgendaWithAiPrompt();
        };

        agendaRecognition.onerror = function(event) {
            console.error(event);
            feedbackMsg.innerText = '⚠️ Error Speech Recognition: ' + event.error;
        };

        agendaRecognition.onend = function() {
            isAgendaListening = false;
            voiceBtn.classList.remove('bg-red-500', 'text-white', 'animate-pulse');
            voiceBtn.classList.add('bg-slate-800', 'text-slate-350');
        };

        agendaRecognition.start();
    }

    async function generateAgendaWithAiPrompt() {
        const promptInput = document.getElementById('ai-agenda-text-prompt');
        const promptText = promptInput.value.trim();
        if (!promptText) {
            alert('Silakan tuliskan deskripsi instruksi agenda terlebih dahulu.');
            return;
        }

        const feedbackMsg = document.getElementById('agenda-ai-feedback-msg');
        const btnRun = document.getElementById('btn-run-agenda-ai');

        btnRun.disabled = true;
        btnRun.innerText = 'AI...';
        feedbackMsg.innerText = '🧠 LUNOU sedang menganalisis proyek & merancang agenda...';
        feedbackMsg.classList.remove('hidden');

        try {
            const res = await fetch("{{ route('management.projects.agendas.generate-ai', $project->id) }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ prompt: promptText })
            });

            const json = await res.json();
            if (json.success && json.data) {
                const d = json.data;

                // 1. Fill Title
                document.querySelector('input[name="title"]').value = d.title || '';

                // 2. Fill Category
                if (d.category) {
                    document.querySelector('select[name="category"]').value = d.category;
                }

                // 3. Fill Description
                document.querySelector('textarea[name="description"]').value = d.description || '';

                // 4. Fill Recurrence
                if (d.recurrence) {
                    document.getElementById('recurrenceSelect').value = d.recurrence;
                    toggleRecurrenceDays();
                }

                // 5. Fill Date & Time
                if (d.start_date) {
                    document.querySelector('input[name="start_date"]').value = d.start_date;
                }
                if (d.start_time) {
                    document.querySelector('input[name="start_time"]').value = d.start_time;
                }

                // 6. Fill Location Type
                if (d.location_type) {
                    document.getElementById('locationTypeSelect').value = d.location_type;
                    toggleLocationFields();
                }

                // 7. Fill Meeting URL & Address
                if (d.meeting_url) {
                    document.querySelector('input[name="meeting_url"]').value = d.meeting_url;
                }
                if (d.location_address) {
                    document.querySelector('input[name="location_address"]').value = d.location_address;
                }

                // 8. Select Attendees
                document.querySelectorAll('input[name="attendee_ids[]"]').forEach(chk => {
                    chk.checked = false;
                });
                if (d.attendee_ids && Array.isArray(d.attendee_ids)) {
                    d.attendee_ids.forEach(uid => {
                        const chk = document.querySelector(`input[name="attendee_ids[]"][value="${uid}"]`);
                        if (chk) chk.checked = true;
                    });
                }

                feedbackMsg.innerText = '✅ Agenda berhasil dirancang secara otomatis!';
                setTimeout(() => {
                    feedbackMsg.classList.add('hidden');
                }, 3000);
            } else {
                alert(json.message || 'Gagal merancang agenda.');
                feedbackMsg.classList.add('hidden');
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan jaringan.');
            feedbackMsg.classList.add('hidden');
        } finally {
            btnRun.disabled = false;
            btnRun.innerText = 'Rancang';
        }
    }

    function openGenerateAgendasModal() {
        document.getElementById('generateAgendasModal').classList.remove('hidden');
    }

    function closeGenerateAgendasModal() {
        document.getElementById('generateAgendasModal').classList.add('hidden');
    }
</script>
@endpush

<!-- Modal AI Generate Agendas (Custom Prompt) -->
<div id="generateAgendasModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-7 shadow-2xl border border-slate-100 space-y-5">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                </svg>
                <span class="font-sans">Generate Agenda via LUNOU AI</span>
            </h3>
            <button type="button" onclick="closeGenerateAgendasModal()" class="text-slate-400 hover:text-slate-600 font-bold text-lg cursor-pointer">&times;</button>
        </div>

        <form action="{{ route('management.projects.agendas.generate-bulk-ai', $project->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin men-generate jadwal pertemuan proyek secara otomatis? Rapat baru akan ditambahkan tanpa menghapus agenda yang sudah ada.')" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5 font-sans">Instruksi Khusus (Opsional)</label>
                <textarea name="instruction" rows="3" placeholder="Contoh: Jadwalkan rapat evaluasi setiap hari Jumat siang, tambahkan agenda olahraga bareng setiap bulan..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500 font-sans"></textarea>
                <p class="text-[10px] text-slate-400 mt-1 font-sans">Kosongkan jika ingin LUNOU AI menjadwalkan pertemuan rutin standard mengikuti fase-fase linimasa proyek secara otomatis.</p>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeGenerateAgendasModal()" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl font-sans cursor-pointer">Batal</button>
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-violet-600 text-white text-xs font-bold rounded-xl shadow-md hover:from-indigo-700 hover:to-violet-700 font-sans cursor-pointer">
                    Mulai Generate
                </button>
            </div>
        </form>
    </div>
</div>
@endsection