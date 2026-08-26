@extends('management.layouts.app')

@section('title', 'Edit Project - ' . $project->name)

@section('content')
<div class="max-w-6xl mx-auto space-y-6 font-sans">

    <!-- Header Actions -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Edit Project: {{ $project->name }}</h1>
            <p class="text-xs text-slate-400 mt-1">Perbarui rincian brief, progress, timeline, scope, dan roster tim.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('management.projects.show', $project->id) }}" class="px-4 py-2 bg-slate-50 border border-slate-200 text-slate-600 text-xs font-semibold rounded-xl hover:bg-slate-100 transition-all">
                Batal
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('management.projects.update', $project->id) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- 1. Basic Information -->
        <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">1. Basic Information</h2>
                @if($project->company)
                    <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-xl border border-emerald-200">
                        Workspace: {{ $project->company->company_name }}
                    </span>
                @endif
            </div>

            <input type="hidden" name="company_profile_id" value="{{ $project->company_profile_id }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Project -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Nama Project</label>
                    <input type="text" name="name" value="{{ old('name', $project->name) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-indigo-500">
                </div>

                <!-- Client Name -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Nama Client / Organisasi</label>
                    <input type="text" name="client_name" value="{{ old('client_name', $project->client_name) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                </div>

                <!-- Kategori Project -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Kategori Project</label>
                    <input type="text" name="category" value="{{ old('category', $project->category) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                </div>

                <!-- Status & Prioritas -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Status & Prioritas</label>
                    <div class="grid grid-cols-2 gap-2">
                        <select name="status" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                            @foreach(['Planning', 'Active', 'On Hold', 'Completed'] as $st)
                                <option value="{{ $st }}" {{ old('status', $project->status) === $st ? 'selected' : '' }}>{{ $st }}</option>
                            @endforeach
                        </select>
                        <select name="priority" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                            @foreach(['Low', 'Medium', 'High', 'Urgent'] as $pr)
                                <option value="{{ $pr }}" {{ old('priority', $project->priority) === $pr ? 'selected' : '' }}>{{ $pr }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Tahap Timeline (Otomatis) -->
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Tahap Timeline Saat Ini</label>
                    <div class="px-4 py-2.5 bg-indigo-50/50 border border-indigo-100 rounded-xl text-xs font-extrabold text-indigo-750">
                        {{ $project->current_stage }} (Kalkulasi Otomatis via Tugas)
                    </div>
                </div>

                <!-- Progress % (Otomatis) -->
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Progress Proyek (%)</label>
                    <div class="px-4 py-2.5 bg-indigo-50/50 border border-indigo-100 rounded-xl text-xs font-extrabold text-indigo-750">
                        {{ $project->progress_percentage }}% (Kalkulasi Otomatis via Tugas)
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Budget Terpakai (Rp)</label>
                    <input type="number" name="budget_spent" value="{{ old('budget_spent', $project->budget_spent) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-indigo-500">
                </div>

                <!-- Tanggal Mulai & Deadline -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Tanggal Mulai & Deadline</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="date" name="start_date" value="{{ old('start_date', optional($project->start_date)->format('Y-m-d')) }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                        <input type="date" name="deadline" value="{{ old('deadline', optional($project->deadline)->format('Y-m-d')) }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                    </div>
                </div>

                <!-- Total Budget -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Total Budget / Nilai Project (Rp)</label>
                    <input type="number" name="budget" value="{{ old('budget', $project->budget) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-emerald-700 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <!-- 2. Project Brief -->
        <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">2. Project Brief</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Masalah yang Ingin Diselesaikan</label>
                    <textarea name="problem_statement" rows="2" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">{{ old('problem_statement', $project->problem_statement) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Tujuan Utama (Project Goals)</label>
                    <textarea name="project_goals" rows="2" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">{{ old('project_goals', $project->project_goals) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Target & Output yang Diharapkan</label>
                    <textarea name="expected_outputs" rows="2" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">{{ old('expected_outputs', $project->expected_outputs) }}</textarea>
                </div>
            </div>
        </div>

        <!-- 3. Scope, Milestones & Deliverables -->
        <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">3. Scope, Milestones & Deliverables</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Scope Included -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-emerald-700 uppercase">Fitur Termasuk (Scope IN)</label>
                        <button type="button" onclick="addScopeInRow()" class="text-[10px] font-bold text-emerald-600 hover:underline">+ Tambah</button>
                    </div>
                    <div id="scope-in-box" class="space-y-2">
                        @foreach($project->scope_included ?? [] as $sc)
                            <div class="flex items-center gap-2">
                                <input type="text" name="scope_included[]" value="{{ $sc }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold px-1">&times;</button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Scope Excluded -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-rose-700 uppercase">Batasan (Scope OUT)</label>
                        <button type="button" onclick="addScopeOutRow()" class="text-[10px] font-bold text-rose-600 hover:underline">+ Tambah</button>
                    </div>
                    <div id="scope-out-box" class="space-y-2">
                        @foreach($project->scope_excluded ?? [] as $se)
                            <div class="flex items-center gap-2">
                                <input type="text" name="scope_excluded[]" value="{{ $se }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold px-1">&times;</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Deliverables & Milestones -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-slate-700 uppercase">Output Deliverables</label>
                        <button type="button" onclick="addDeliverableRow()" class="text-[10px] font-bold text-indigo-600 hover:underline">+ Tambah</button>
                    </div>
                    <div id="deliverables-box" class="space-y-2">
                        @foreach($project->deliverables ?? [] as $d)
                            <div class="flex items-center gap-2">
                                <input type="text" name="deliverables[]" value="{{ $d }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold px-1">&times;</button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-slate-700 uppercase">Milestones</label>
                        <button type="button" onclick="addMilestoneRow()" class="text-[10px] font-bold text-indigo-600 hover:underline">+ Tambah</button>
                    </div>
                    <div id="milestones-box" class="space-y-2">
                        @foreach($project->milestones ?? [] as $idx => $m)
                            <div class="flex items-center gap-2">
                                <input type="text" name="milestones[{{ $idx }}][title]" value="{{ $m['title'] ?? '' }}" placeholder="Judul Milestone" class="flex-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                                <input type="text" name="milestones[{{ $idx }}][target]" value="{{ $m['target'] ?? '' }}" placeholder="Target" class="w-28 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold px-1">&times;</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Team & Responsibility Matrix -->
        <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">4. Team & Responsibility Matrix</h2>
                    <p class="text-[11px] text-slate-400 mt-0.5">Kelola anggota tim aktif dari role User dan Finance.</p>
                </div>
                <button type="button" onclick="addTeamRow()" class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-700 font-bold text-xs rounded-xl transition-all flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Anggota Tim
                </button>
            </div>

            <div id="team-box" class="space-y-3">
                @foreach($project->team_matrix ?? [] as $idx => $tm)
                    <div class="p-4 bg-slate-50/70 border border-slate-200/80 rounded-2xl flex flex-col sm:flex-row items-center gap-3 team-item">
                        <div class="w-full sm:w-1/2">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Pilih Anggota</label>
                            <select name="team_matrix[{{ $idx }}][user_id]" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-indigo-500">
                                @foreach($teamMembers ?? [] as $u)
                                    <option value="{{ $u->id }}" {{ ($tm['user_id'] ?? null) == $u->id ? 'selected' : '' }}>
                                        [{{ strtoupper($u->role) }}] {{ $u->name }} - {{ $u->position ?? 'Staff' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full sm:flex-1">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Peran / Tanggung Jawab</label>
                            <input type="text" name="team_matrix[{{ $idx }}][role_title]" value="{{ $tm['role_title'] ?? '' }}" required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                        </div>
                        <button type="button" onclick="this.closest('.team-item').remove()" class="p-2 text-rose-500 hover:bg-rose-50 rounded-xl transition-all self-end sm:self-center mt-2 sm:mt-5" title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4">
            <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md transition-all">
                Simpan Perubahan Project
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
    let milestoneIdx = {{ count($project->milestones ?? []) }};
    let teamIdx = {{ count($project->team_matrix ?? []) }};
    const teamUsers = @json($teamMembers ?? []);

    function addScopeInRow(val = '') {
        document.getElementById('scope-in-box').insertAdjacentHTML('beforeend', `
            <div class="flex items-center gap-2">
                <input type="text" name="scope_included[]" value="${val}" placeholder="Fitur..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold px-1">&times;</button>
            </div>
        `);
    }

    function addScopeOutRow(val = '') {
        document.getElementById('scope-out-box').insertAdjacentHTML('beforeend', `
            <div class="flex items-center gap-2">
                <input type="text" name="scope_excluded[]" value="${val}" placeholder="Batasan..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold px-1">&times;</button>
            </div>
        `);
    }

    function addDeliverableRow(val = '') {
        document.getElementById('deliverables-box').insertAdjacentHTML('beforeend', `
            <div class="flex items-center gap-2">
                <input type="text" name="deliverables[]" value="${val}" placeholder="Hasil output..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold px-1">&times;</button>
            </div>
        `);
    }

    function addMilestoneRow(title = '', target = '') {
        document.getElementById('milestones-box').insertAdjacentHTML('beforeend', `
            <div class="flex items-center gap-2">
                <input type="text" name="milestones[${milestoneIdx}][title]" value="${title}" placeholder="Judul Milestone" class="flex-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                <input type="text" name="milestones[${milestoneIdx}][target]" value="${target}" placeholder="Target waktu" class="w-28 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold px-1">&times;</button>
            </div>
        `);
        milestoneIdx++;
    }

    function addTeamRow() {
        if (!teamUsers || teamUsers.length === 0) {
            alert('Belum ada user dengan role User atau Finance yang terdaftar.');
            return;
        }

        let options = teamUsers.map(u => {
            let roleBadge = u.role === 'finance' ? '[FINANCE]' : '[USER]';
            return `<option value="${u.id}">${roleBadge} ${u.name} - ${u.position || 'Staff'}</option>`;
        }).join('');

        const html = `
            <div class="p-4 bg-slate-50/70 border border-slate-200/80 rounded-2xl flex flex-col sm:flex-row items-center gap-3 team-item">
                <div class="w-full sm:w-1/2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Pilih Anggota</label>
                    <select name="team_matrix[${teamIdx}][user_id]" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-indigo-500">
                        ${options}
                    </select>
                </div>
                <div class="w-full sm:flex-1">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Peran / Tanggung Jawab di Proyek</label>
                    <input type="text" name="team_matrix[${teamIdx}][role_title]" placeholder="Contoh: Lead Frontend, Staff Budgeting, QA Tester" required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                </div>
                <button type="button" onclick="this.closest('.team-item').remove()" class="p-2 text-rose-500 hover:bg-rose-50 rounded-xl transition-all self-end sm:self-center mt-2 sm:mt-5" title="Hapus">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </div>
        `;

        document.getElementById('team-box').insertAdjacentHTML('beforeend', html);
        teamIdx++;
    }
</script>
@endpush
@endsection