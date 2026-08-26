@extends('management.layouts.app')

@section('title', 'Buat Project Baru')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 font-sans">

    <!-- Header -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Formulir Pembuatan Project</h1>
            <p class="text-xs text-slate-400 mt-1">Lengkapi data secara manual atau gunakan otomasi LUNOU AI generator.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('management.projects.index') }}" class="px-4 py-2 bg-slate-50 border border-slate-200 text-slate-600 text-xs font-semibold rounded-xl hover:bg-slate-100 transition-all">
                Kembali
            </a>
        </div>
    </div>

    <!-- Side-by-Side Companion Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: LUNOU AI Project Architect Companion (lg:col-span-4) -->
        <div class="lg:col-span-4 bg-slate-900 text-slate-100 rounded-3xl p-6 shadow-xl border border-slate-800 space-y-5 lg:sticky lg:top-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 border border-indigo-500/25 p-1 shrink-0">
                    <img src="{{ asset('icon/11.png') }}" alt="LUNOU" class="w-full h-full object-contain">
                </div>
                <div>
                    <h3 class="text-xs font-black text-white uppercase tracking-wider">LUNOU AI Project Architect</h3>
                    <span class="text-[9px] font-bold text-indigo-300 block mt-0.5">Pendikte & Otomasi Formulir</span>
                </div>
            </div>

            <div class="p-4 bg-slate-800/50 border border-slate-700/50 rounded-2xl space-y-3">
                <h4 class="text-[10px] font-black text-indigo-300 uppercase tracking-wider">💡 Yang Perlu Kamu Input / Ucapkan:</h4>
                <ul class="text-[11px] text-slate-350 space-y-2.5 leading-relaxed font-medium">
                    <li class="flex items-start gap-2">
                        <span class="text-indigo-400 mt-0.5">•</span>
                        <span><strong>Basic Info</strong>: Nama proyek, klien, kategori, prioritas, tanggal, dan total budget.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-indigo-400 mt-0.5">•</span>
                        <span><strong>Project Brief</strong>: Masalah yang ingin diselesaikan, gol proyek, dan target output.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-indigo-400 mt-0.5">•</span>
                        <span><strong>Scope & Milestones</strong>: Fitur yang termasuk (Scope IN), batasan (Scope OUT), output deliverables, dan timeline milestones.</span>
                    </li>
                </ul>
            </div>

            <!-- Input Section -->
            <div class="space-y-3">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tulis ide atau gunakan pendikte suara:</label>
                <div class="relative">
                    <textarea id="aiPrompt" rows="5" placeholder="Contoh: Buat proyek web sekolah bernama E-School untuk klien SMA 1, kategori Web App. Masalahnya adm sekolah manual. Golnya digitalisasi adm. Budget 20jt. Fitur input nilai, pembayaran. Batasan: tidak ada mobile app. Milestone: UI Design 5 hari..." class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-2xl text-xs font-semibold text-white focus:ring-2 focus:ring-indigo-500 outline-none placeholder:text-slate-500 leading-relaxed"></textarea>
                    
                    <!-- Mic Dictation Button -->
                    <button type="button" id="ai-project-mic-btn" onclick="toggleProjectCreateSpeech()" class="absolute bottom-3 right-3 p-2 bg-slate-700/80 hover:bg-slate-650 text-slate-300 hover:text-white rounded-xl transition-all" title="Gunakan Suara (Voice to Text)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                        </svg>
                    </button>
                </div>
                
                <button type="button" id="btnRunAi" onclick="generateWithAi()" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-2xl shadow-lg transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span id="aiBtnText">Rancang Proyek & Auto-Fill</span>
                </button>
            </div>
        </div>

        <!-- Right: Main Form (lg:col-span-8) -->
        <form method="POST" action="{{ route('management.projects.store') }}" enctype="multipart/form-data" class="lg:col-span-8 space-y-6">
            @csrf

        <!-- 1. Basic Information -->
        <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">1. Basic Information</h2>
                @if(isset($selectedCompany))
                    <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-xl border border-emerald-200">
                        Workspace: {{ $selectedCompany->company_name }}
                    </span>
                @endif
            </div>

            <!-- Hidden input Company ID otomatis -->
            <input type="hidden" name="company_profile_id" value="{{ $selectedCompany->id ?? '' }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Project (Full Row) -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Nama Project</label>
                    <input type="text" id="p_name" name="name" value="{{ old('name') }}" required placeholder="Masukkan nama project..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-indigo-500">
                </div>

                <!-- Client Name -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Nama Client / Organisasi</label>
                    <input type="text" id="p_client" name="client_name" value="{{ old('client_name') }}" placeholder="Internal / Nama Klien" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                </div>

                <!-- Kategori Project -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Kategori Project</label>
                    <input type="text" id="p_category" name="category" value="{{ old('category', 'Web & Mobile App') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                </div>

                <!-- Status & Prioritas -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Status & Prioritas</label>
                    <div class="grid grid-cols-2 gap-2">
                        <select name="status" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                            <option value="Planning">Planning</option>
                            <option value="Active">Active</option>
                            <option value="On Hold">On Hold</option>
                            <option value="Completed">Completed</option>
                        </select>
                        <select name="priority" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                            <option value="Low">Low</option>
                            <option value="Medium" selected>Medium</option>
                            <option value="High">High</option>
                            <option value="Urgent">Urgent</option>
                        </select>
                    </div>
                </div>

                <!-- Tahap Timeline (Otomatis) -->
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Tahap Timeline Saat Ini</label>
                    <div class="px-4 py-2.5 bg-indigo-50/50 border border-indigo-100 rounded-xl text-xs font-extrabold text-indigo-750">
                        Planning (Diinisiasi Otomatis)
                    </div>
                </div>

                <!-- Tanggal Mulai & Deadline -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Tanggal Mulai & Deadline</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="date" name="start_date" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                        <input type="date" name="deadline" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                    </div>
                </div>

                <!-- Total Budget -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Total Budget / Nilai Project (Rp)</label>
                    <input type="number" name="budget" value="0" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-emerald-700 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <!-- 2. Project Brief -->
        <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">2. Project Brief</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Masalah yang Ingin Diselesaikan (Problem Statement)</label>
                    <textarea id="p_problem" name="problem_statement" rows="2" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Tujuan Utama (Project Goals)</label>
                    <textarea id="p_goals" name="project_goals" rows="2" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Target & Output yang Diharapkan</label>
                    <textarea id="p_outputs" name="expected_outputs" rows="2" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500"></textarea>
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
                        <label class="text-xs font-bold text-emerald-700 uppercase">Fitur yang Termasuk (Scope IN)</label>
                        <button type="button" onclick="addScopeInRow()" class="text-[10px] font-bold text-emerald-600 hover:underline">+ Tambah</button>
                    </div>
                    <div id="scope-in-box" class="space-y-2"></div>
                </div>

                <!-- Scope Excluded -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-rose-700 uppercase">Batasan (Scope OUT / Excluded)</label>
                        <button type="button" onclick="addScopeOutRow()" class="text-[10px] font-bold text-rose-600 hover:underline">+ Tambah</button>
                    </div>
                    <div id="scope-out-box" class="space-y-2"></div>
                </div>
            </div>

            <!-- Deliverables & Milestones -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-slate-700 uppercase">Output Deliverables</label>
                        <button type="button" onclick="addDeliverableRow()" class="text-[10px] font-bold text-indigo-600 hover:underline">+ Tambah</button>
                    </div>
                    <div id="deliverables-box" class="space-y-2"></div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-slate-700 uppercase">Milestones</label>
                        <button type="button" onclick="addMilestoneRow()" class="text-[10px] font-bold text-indigo-600 hover:underline">+ Tambah</button>
                    </div>
                    <div id="milestones-box" class="space-y-2"></div>
                </div>
            </div>
        </div>

        <!-- 4. Team & Responsibility Matrix (User & Finance Multi-Assign) -->
        <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">4. Team & Responsibility Matrix</h2>
                    <p class="text-[11px] text-slate-400 mt-0.5">Tugaskan anggota tim dari role User dan Finance.</p>
                </div>
                <button type="button" onclick="addTeamRow()" class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-700 font-bold text-xs rounded-xl transition-all flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Anggota Tim
                </button>
            </div>

            <div id="team-box" class="space-y-3"></div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4">
            <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md transition-all">
                Simpan & Terbitkan Project
            </button>
        </div>
    </form>
    
    </div> <!-- Close Side-by-Side Grid -->

</div>

@push('scripts')
<script>
    let milestoneIdx = 0;
    let teamIdx = 0;
    const teamUsers = @json($teamMembers ?? []);

    let projectSpeechRecognition = null;
    let isProjectDictating = false;

    function toggleProjectCreateSpeech() {
        window.SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!window.SpeechRecognition) {
            alert('Browser Anda tidak mendukung fitur Speech Recognition.');
            return;
        }

        const micBtn = document.getElementById('ai-project-mic-btn');
        const promptArea = document.getElementById('aiPrompt');

        if (isProjectDictating) {
            if (projectSpeechRecognition) projectSpeechRecognition.stop();
            return;
        }

        projectSpeechRecognition = new window.SpeechRecognition();
        projectSpeechRecognition.lang = 'id-ID';
        projectSpeechRecognition.interimResults = false;
        projectSpeechRecognition.maxAlternatives = 1;

        projectSpeechRecognition.onstart = () => {
            isProjectDictating = true;
            micBtn.classList.remove('bg-slate-700/80', 'text-slate-300');
            micBtn.classList.add('bg-rose-600', 'text-white', 'animate-pulse');
            promptArea.placeholder = "Mendengarkan suara Anda...";
        };

        projectSpeechRecognition.onresult = (event) => {
            const textResult = event.results[0][0].transcript;
            promptArea.value = (promptArea.value ? promptArea.value + " " : "") + textResult;
        };

        projectSpeechRecognition.onerror = (e) => {
            console.error(e);
        };

        projectSpeechRecognition.onend = () => {
            isProjectDictating = false;
            micBtn.classList.remove('bg-rose-600', 'text-white', 'animate-pulse');
            micBtn.classList.add('bg-slate-700/80', 'text-slate-300');
            promptArea.placeholder = "Contoh: Buat proyek web sekolah bernama E-School...";
        };

        projectSpeechRecognition.start();
    }

    async function generateWithAi() {
        const prompt = document.getElementById('aiPrompt').value.trim();
        if (!prompt) return alert('Silakan tuliskan deskripsi ide project terlebih dahulu.');

        const btn = document.getElementById('btnRunAi');
        const text = document.getElementById('aiBtnText');
        btn.disabled = true;
        text.innerText = 'AI Sedang Merancang...';

        try {
            const res = await fetch("{{ route('management.projects.generate-ai') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ prompt: prompt })
            });

            const json = await res.json();
            if (json.success && json.data) {
                const d = json.data;
                document.getElementById('p_name').value = d.name || '';
                document.getElementById('p_client').value = d.client_name || '';
                document.getElementById('p_category').value = d.category || '';
                
                if (d.priority) {
                    const prioritySelect = document.querySelector('select[name="priority"]');
                    if (prioritySelect) prioritySelect.value = d.priority;
                }
                if (d.start_date) {
                    const startDateInput = document.querySelector('input[name="start_date"]');
                    if (startDateInput) startDateInput.value = d.start_date;
                }
                if (d.deadline) {
                    const deadlineInput = document.querySelector('input[name="deadline"]');
                    if (deadlineInput) deadlineInput.value = d.deadline;
                }
                if (d.budget !== undefined) {
                    const budgetInput = document.querySelector('input[name="budget"]');
                    if (budgetInput) budgetInput.value = d.budget;
                }

                document.getElementById('p_problem').value = d.problem_statement || '';
                document.getElementById('p_goals').value = d.project_goals || '';
                document.getElementById('p_outputs').value = d.expected_outputs || '';

                // Scope IN
                document.getElementById('scope-in-box').innerHTML = '';
                (d.scope_included || []).forEach(item => addScopeInRow(item));

                // Scope OUT
                document.getElementById('scope-out-box').innerHTML = '';
                (d.scope_excluded || []).forEach(item => addScopeOutRow(item));

                // Deliverables
                document.getElementById('deliverables-box').innerHTML = '';
                (d.deliverables || []).forEach(item => addDeliverableRow(item));

                // Milestones
                document.getElementById('milestones-box').innerHTML = '';
                (d.milestones || []).forEach(m => addMilestoneRow(m.title, m.target_days));

                // Scroll to top of form
                document.getElementById('p_name').scrollIntoView({ behavior: 'smooth' });
            } else {
                alert(json.message || 'Gagal generate AI.');
            }
        } catch (err) {
            alert('Terjadi kesalahan jaringan.');
        } finally {
            btn.disabled = false;
            text.innerText = 'Rancang Proyek & Auto-Fill';
        }
    }

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

    function addMilestoneRow(title = '', days = '') {
        document.getElementById('milestones-box').insertAdjacentHTML('beforeend', `
            <div class="flex items-center gap-2">
                <input type="text" name="milestones[${milestoneIdx}][title]" value="${title}" placeholder="Judul Milestone" class="flex-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                <input type="text" name="milestones[${milestoneIdx}][target]" value="${days ? days + ' Hari' : ''}" placeholder="Target waktu" class="w-28 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
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
                <button type="button" onclick="this.closest('.team-item').remove()" class="p-2 text-rose-500 hover:bg-rose-50 rounded-xl transition-all self-end sm:self-center mt-2 sm:mt-5" title="Hapus Anggota">
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