@extends('management.layouts.app')

@section('title', 'Linimasa & Roadmap - ' . $project->name)

@section('content')
<div class="max-w-7xl mx-auto space-y-8 font-sans">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl text-xs space-y-1">
            <span class="font-bold block">Peringatan Validasi Linimasa:</span>
            <ul class="list-disc pl-4">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header Proyek & Rentang Tanggal Batas -->
    <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase px-2.5 py-0.5 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200">
                        ROADMAP & TIMELINE HUB
                    </span>
                    <span class="text-xs font-semibold text-slate-400">• {{ $project->name }}</span>
                </div>
                <h1 class="text-2xl font-black text-slate-900 mt-1 tracking-tight">Linimasa & Rencana Kerja Berurutan</h1>
                <p class="text-xs text-slate-400 mt-0.5">Seluruh fase divalidasi tidak boleh tumpang tindih dan terikat pada jadwal proyek.</p>
            </div>

            <div class="flex items-center gap-2">
                <form action="{{ route('management.projects.roadmaps.generate-ai', $project->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin men-generate otomatis seluruh linimasa menggunakan LUNOU AI? Semua fase linimasa yang sudah ada di proyek ini akan dihapus dan dibuat ulang secara otomatis.')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-violet-600 text-white text-xs font-bold rounded-xl hover:from-indigo-700 hover:to-violet-700 shadow-sm flex items-center gap-2 hover:shadow transition-all cursor-pointer font-sans">
                        <svg class="w-3.5 h-3.5 animate-pulse text-indigo-200" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                        </svg>
                        <span>Generate Linimasa via LUNOU AI</span>
                    </button>
                </form>

                <a href="{{ route('management.projects.show', $project->id) }}" class="px-4 py-2 bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-100 transition-all font-sans">
                    Kembali ke Detail Project
                </a>
            </div>
        </div>

        <!-- Rentang Tanggal Batas Proyek -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-100 text-xs">
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                <span class="text-slate-400 font-bold block mb-1">BATAS AWAL PROYEK</span>
                <span class="font-black text-slate-800">{{ $project->start_date ? $project->start_date->format('d M Y') : 'Tidak dibatasi' }}</span>
            </div>
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                <span class="text-slate-400 font-bold block mb-1">DEADLINE AKHIR PROYEK</span>
                <span class="font-black text-rose-700">{{ $project->deadline ? $project->deadline->format('d M Y') : 'Tidak dibatasi' }}</span>
            </div>
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                <span class="text-slate-400 font-bold block mb-1">STATUS TAHAP PROYEK</span>
                <span class="font-black text-indigo-700 uppercase">{{ $project->current_stage }} ({{ $project->roadmaps->count() }} Fase Aktif)</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">

        <!-- Kolom Kiri: Visual Gantt-like Timeline & Daftar Fase (7 Kolom) -->
        <div class="xl:col-span-7 space-y-6">
            <h2 class="text-base font-black text-slate-900 tracking-tight">Alur Linimasa Berjalan</h2>

            @if($project->roadmaps->count() > 0)
                <div class="relative pl-6 border-l-2 border-indigo-200 space-y-8">
                    @foreach($project->roadmaps as $idx => $rm)
                        <div class="relative bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                            <!-- Bullet Marker -->
                            <div class="absolute -left-[33px] top-6 w-5 h-5 rounded-full border-4 border-white shadow-sm flex items-center justify-center {{ $rm->status === 'Completed' ? 'bg-emerald-500' : ($rm->status === 'In Progress' ? 'bg-indigo-600 animate-pulse' : 'bg-slate-400') }}"></div>

                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] font-black uppercase text-indigo-600 tracking-wider">FASE {{ $idx + 1 }}</span>
                                    <h3 class="text-base font-black text-slate-900">{{ $rm->title }}</h3>
                                    <span class="text-xs font-semibold text-slate-400">
                                        {{ $rm->start_date->format('d M Y') }} &rarr; {{ $rm->end_date->format('d M Y') }}
                                        ({{ $rm->start_date->diffInDays($rm->end_date) + 1 }} Hari)
                                    </span>
                                </div>

                                <span class="px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider {{ $rm->status === 'Completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($rm->status === 'In Progress' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-100 text-slate-600') }}">
                                    {{ $rm->status }}
                                </span>
                            </div>

                            @if($rm->description)
                                <p class="text-xs text-slate-600 leading-relaxed font-medium">{{ $rm->description }}</p>
                            @endif

                            <!-- Progress Bar Ketercapaian -->
                            <div class="space-y-1.5 pt-2 border-t border-slate-100">
                                <div class="flex justify-between text-[11px] font-bold">
                                    <span class="text-slate-400">Target Ketercapaian Fase</span>
                                    <span class="text-slate-800">{{ $rm->progress_percentage }}% Tercapai</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: {{ $rm->progress_percentage }}%"></div>
                                </div>
                            </div>

                            <!-- Checklist Target Ketercapaian -->
                            @if(!empty($rm->objectives))
                                <div class="space-y-2 pt-2">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Target & Output:</span>
                                    <div class="space-y-1.5">
                                        @foreach($rm->objectives as $obj)
                                            <div class="flex items-center gap-2 text-xs {{ $obj['is_achieved'] ? 'text-emerald-700 font-bold' : 'text-slate-600' }}">
                                                <span class="w-4 h-4 rounded-md flex items-center justify-center {{ $obj['is_achieved'] ? 'bg-emerald-500 text-white' : 'border border-slate-300' }}">
                                                    @if($obj['is_achieved']) &#10003; @endif
                                                </span>
                                                <span class="{{ $obj['is_achieved'] ? 'line-through' : '' }}">{{ $obj['target'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Aksi Linimasa -->
                            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                                <button type="button" onclick="openEnhanceModal({{ $rm->id }})" class="px-3 py-1.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white text-xs font-bold rounded-xl shadow-sm hover:shadow transition-all flex items-center gap-1 cursor-pointer font-sans">
                                    <svg class="w-3 h-3 text-emerald-100 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>AI Sempurnakan</span>
                                </button>

                                <button type="button" onclick="openEditModal({{ $rm->id }}, '{{ addslashes($rm->title) }}', '{{ addslashes($rm->description) }}', '{{ $rm->start_date->format('Y-m-d') }}', '{{ $rm->end_date->format('Y-m-d') }}', '{{ $rm->status }}', {{ json_encode($rm->objectives ?? []) }})" class="px-3 py-1.5 bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-bold rounded-xl hover:bg-indigo-100 transition-all font-sans">
                                    Perbarui & Checklist
                                </button>
                                <form method="POST" action="{{ route('management.projects.roadmaps.destroy', [$project->id, $rm->id]) }}" onsubmit="return confirm('Hapus fase linimasa ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-xl transition-all cursor-pointer" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white border border-dashed border-slate-200 rounded-3xl p-12 text-center">
                    <p class="text-xs font-medium text-slate-400">Belum ada linimasa yang dirancang untuk proyek ini.</p>
                </div>
            @endif
        </div>

        <!-- Kolom Kanan: Form Tambah Fase Linimasa Baru (5 Kolom) -->
        <div class="xl:col-span-5 space-y-6">
            <div class="bg-white border border-slate-100 rounded-3xl p-7 shadow-sm space-y-6 sticky top-24">
                <div>
                    <h2 class="text-sm font-black text-slate-900 tracking-tight">+ Tambah Fase Linimasa Baru</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Pastikan tanggal mulai dan selesai tidak bertabrakan dengan fase lain.</p>
                </div>

                <form method="POST" action="{{ route('management.projects.roadmaps.store', $project->id) }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Judul Fase Linimasa</label>
                        <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Fase 1: Perancangan Arsitektur Database" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Deskripsi Singkat</label>
                        <textarea name="description" rows="2" placeholder="Ruang lingkup kerja fase ini..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Tgl Mulai</label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}" required min="{{ optional($project->start_date)->format('Y-m-d') }}" max="{{ optional($project->deadline)->format('Y-m-d') }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Tgl Selesai</label>
                            <input type="date" name="end_date" value="{{ old('end_date') }}" required min="{{ optional($project->start_date)->format('Y-m-d') }}" max="{{ optional($project->deadline)->format('Y-m-d') }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Status Awal</label>
                        <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                            <option value="Pending">Pending</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>

                    <!-- Input Target Ketercapaian / Objectives -->
                    <div class="space-y-2 pt-2 border-t border-slate-100">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-slate-700 uppercase">Target Ketercapaian (Outputs)</label>
                            <button type="button" onclick="addNewObjectiveRow()" class="text-[10px] font-bold text-indigo-600 hover:underline">+ Tambah Target</button>
                        </div>
                        <div id="new-objectives-box" class="space-y-2">
                            <input type="text" name="objectives[]" placeholder="Target 1 (misal: Skema ERD Selesai)" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md transition-all">
                        Simpan Fase Linimasa
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>

<!-- Modal Edit Linimasa & Checklist Target Ketercapaian -->
<div id="editModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-7 shadow-2xl border border-slate-100 space-y-5">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-black text-slate-900">Perbarui Fase & Checklist Target</h3>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
        </div>

        <form id="editForm" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Judul Fase</label>
                <input type="text" id="edit_title" name="title" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Deskripsi</label>
                <textarea id="edit_description" name="description" rows="2" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tgl Mulai</label>
                    <input type="date" id="edit_start" name="start_date" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tgl Selesai</label>
                    <input type="date" id="edit_end" name="end_date" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Status</label>
                <select id="edit_status" name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                    <option value="Pending">Pending</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>

            <!-- Edit & Checklist Objectives -->
            <div class="space-y-2 pt-2 border-t border-slate-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-bold text-slate-700 uppercase">Checklist Ketercapaian</label>
                        <button type="button" id="ai-suggest-btn" onclick="suggestObjectivesViaAi()" class="text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-lg hover:bg-emerald-100 transition-all flex items-center gap-1 cursor-pointer">
                            <svg class="w-2.5 h-2.5 text-emerald-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                            </svg>
                            <span>LUNOU AI: Rekomendasi Target</span>
                        </button>
                    </div>
                    <button type="button" onclick="addEditObjectiveRow()" class="text-[10px] font-bold text-indigo-600 cursor-pointer">+ Tambah</button>
                </div>
                <div id="edit-objectives-box" class="space-y-2"></div>
            </div>

            <div class="flex justify-end gap-2 pt-3">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl">Batal</button>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal AI Sempurnakan (Custom Prompt) -->
<div id="enhanceModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-7 shadow-2xl border border-slate-100 space-y-5">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                </svg>
                <span class="font-sans">AI Sempurnakan Fase</span>
            </h3>
            <button type="button" onclick="closeEnhanceModal()" class="text-slate-400 hover:text-slate-600 font-bold text-lg cursor-pointer">&times;</button>
        </div>

        <form id="enhanceForm" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5 font-sans">Instruksi Khusus (Opsional)</label>
                <textarea name="instruction" id="enhance_instruction" rows="3" placeholder="Contoh: Fokuskan pada backend & database, tambahkan integrasi dengan Fonnte, dll." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-emerald-500 font-sans"></textarea>
                <p class="text-[10px] text-slate-400 mt-1 font-sans">Kosongkan jika ingin LUNOU AI melakukan penyempurnaan standar secara otomatis.</p>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeEnhanceModal()" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl font-sans cursor-pointer">Batal</button>
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-xs font-bold rounded-xl shadow-md hover:from-emerald-600 hover:to-teal-600 font-sans cursor-pointer">
                    Mulai Sempurnakan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function addNewObjectiveRow() {
        document.getElementById('new-objectives-box').insertAdjacentHTML('beforeend', `
            <div class="flex items-center gap-2">
                <input type="text" name="objectives[]" placeholder="Target baru..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 font-bold px-1">&times;</button>
            </div>
        `);
    }

    function openEditModal(id, title, desc, start, end, status, objectives) {
        document.getElementById('editForm').action = `/management/projects/{{ $project->id }}/roadmaps/${id}`;
        document.getElementById('edit_title').value = title;
        document.getElementById('edit_description').value = desc;
        document.getElementById('edit_start').value = start;
        document.getElementById('edit_end').value = end;
        document.getElementById('edit_status').value = status;

        const box = document.getElementById('edit-objectives-box');
        box.innerHTML = '';
        (objectives || []).forEach((obj, idx) => {
            box.insertAdjacentHTML('beforeend', `
                <div class="flex items-center gap-2 p-2 bg-slate-50 rounded-xl border border-slate-200">
                    <input type="checkbox" name="achieved_status[${idx}]" value="1" ${obj.is_achieved ? 'checked' : ''} class="rounded text-indigo-600 focus:ring-indigo-500">
                    <input type="text" name="objectives[${idx}]" value="${obj.target}" class="flex-1 px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-xs font-medium">
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 font-bold px-1">&times;</button>
                </div>
            `);
        });

        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function addEditObjectiveRow() {
        const box = document.getElementById('edit-objectives-box');
        const idx = box.children.length;
        box.insertAdjacentHTML('beforeend', `
            <div class="flex items-center gap-2 p-2 bg-slate-50 rounded-xl border border-slate-200">
                <input type="checkbox" name="achieved_status[${idx}]" value="1" class="rounded text-indigo-600 focus:ring-indigo-500">
                <input type="text" name="objectives[${idx}]" placeholder="Target..." class="flex-1 px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-xs font-medium">
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 font-bold px-1">&times;</button>
            </div>
        `);
    }

    async function suggestObjectivesViaAi() {
        const title = document.getElementById('edit_title').value;
        const desc = document.getElementById('edit_description').value;
        const btn = document.getElementById('ai-suggest-btn');

        if (!title.trim()) {
            alert('Silakan isi judul fase terlebih dahulu agar AI dapat memahami konteks.');
            return;
        }

        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `
            <svg class="animate-spin h-3.5 w-3.5 text-emerald-600 inline" fill="none" viewBox="0 0 24 24" style="width:14px;height:14px;">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Menganalisis...</span>
        `;

        try {
            const response = await fetch(`/management/projects/{{ $project->id }}/roadmaps/suggest-objectives-ai`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ title, description: desc })
            });

            const data = await response.json();
            if (data.status === 'success' && data.objectives) {
                const box = document.getElementById('edit-objectives-box');
                box.innerHTML = '';
                data.objectives.forEach((targetText, idx) => {
                    box.insertAdjacentHTML('beforeend', `
                        <div class="flex items-center gap-2 p-2 bg-slate-50 rounded-xl border border-slate-200">
                            <input type="checkbox" name="achieved_status[${idx}]" value="1" class="rounded text-indigo-600 focus:ring-indigo-500">
                            <input type="text" name="objectives[${idx}]" value="${targetText}" class="flex-1 px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-xs font-medium">
                            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 font-bold px-1">&times;</button>
                        </div>
                    `);
                });
            } else {
                alert('Gagal mendapatkan rekomendasi AI: ' + (data.message || 'Error tidak diketahui'));
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan koneksi saat memanggil LUNOU AI.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }

    function openEnhanceModal(id) {
        document.getElementById('enhanceForm').action = `/management/projects/{{ $project->id }}/roadmaps/${id}/enhance-ai`;
        document.getElementById('enhance_instruction').value = '';
        document.getElementById('enhanceModal').classList.remove('hidden');
    }

    function closeEnhanceModal() {
        document.getElementById('enhanceModal').classList.add('hidden');
    }
</script>
@endpush
@endsection