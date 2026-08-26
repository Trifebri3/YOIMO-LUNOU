@php
    $role = Auth::user()->role;
    $layout = match($role) {
        'superadmin' => 'superadmin.layouts.app',
        'management' => 'management.layouts.app',
        default      => 'user.layouts.app'
    };
    $accentColor = $role === 'superadmin' ? 'emerald' : 'indigo';
@endphp

@extends($layout)

@section('title', 'Manajemen AI Settings')

@section('content')
<div class="space-y-8 font-sans max-w-5xl mx-auto">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Pengaturan Gateway AI</h1>
            <p class="text-xs text-slate-400 mt-0.5">Kelola credentials, provider default, dan parameter model AI multi-gateway secara dinamis.</p>
        </div>
        @if(session()->has('demo_track_id'))
            <button type="button" disabled class="px-5 py-2.5 bg-slate-200 text-slate-400 text-xs font-bold rounded-xl cursor-not-allowed opacity-75" title="Dinonaktifkan di Akun Demo">
                Tambah Konfigurasi Baru
            </button>
        @else
            <button type="button" onclick="openCreateModal()" class="px-5 py-2.5 bg-{{ $accentColor }}-650 hover:bg-{{ $accentColor }}-700 text-white text-xs font-bold rounded-xl shadow-md transition-all">
                Tambah Konfigurasi Baru
            </button>
        @endif
    </div>

    <!-- Session Flash Feedback Messages -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3.5 rounded-2xl text-xs font-bold flex items-center justify-between shadow-sm">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3.5 rounded-2xl text-xs font-bold flex items-center justify-between shadow-sm">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- 1. ACTIVE GATEWAY PROFILE HIGHLIGHT -->
    @php
        $activeGateway = $settings->where('is_active', true)->first();
    @endphp

    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 rounded-3xl p-6 sm:p-8 text-white relative overflow-hidden shadow-xl">
        <div class="absolute -right-12 -top-12 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="text-[10px] font-black uppercase tracking-widest text-indigo-400 bg-indigo-900/50 px-3 py-1 rounded-full">
                    Gateway AI Utama Aktif
                </span>
                @if($activeGateway)
                    <h2 class="text-2xl font-black tracking-tight uppercase mt-1">{{ $activeGateway->name }}</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-2 text-xs font-medium text-slate-350">
                        <div>
                            <span class="text-[9px] uppercase tracking-wider block text-slate-400">Provider</span>
                            <span class="text-white font-bold uppercase">{{ $activeGateway->provider }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] uppercase tracking-wider block text-slate-400">Model Default</span>
                            <span class="text-white font-bold">{{ $activeGateway->model }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] uppercase tracking-wider block text-slate-400">URL Endpoint</span>
                            <span class="text-white font-bold truncate block max-w-[150px]">{{ $activeGateway->base_url ?? 'Default' }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] uppercase tracking-wider block text-slate-400">Fallback Failover</span>
                            <span class="text-white font-bold uppercase">
                                {{ ($activeGateway->settings['enable_fallback'] ?? false) ? 'Aktif (' . ($activeGateway->settings['fallback_provider'] ?? '-') . ')' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>
                @else
                    <h2 class="text-xl font-black text-slate-300">Belum ada Gateway Aktif</h2>
                    <p class="text-xs text-slate-400">Silakan aktifkan salah satu konfigurasi di bawah untuk mulai menggunakan AI.</p>
                @endif
            </div>

            @if($activeGateway)
                <div class="shrink-0 flex gap-2">
                    <button type="button" onclick="testConnection({{ $activeGateway->id }}, '{{ $activeGateway->provider }}', '{{ $activeGateway->model }}', '{{ $activeGateway->base_url }}')" 
                            class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-xs font-bold rounded-xl transition-all">
                        Tes Koneksi
                    </button>
                    <button type="button" onclick="openEditModal({{ json_encode($activeGateway) }})" 
                            class="px-4 py-2.5 bg-white hover:bg-slate-100 text-slate-900 text-xs font-black rounded-xl transition-all">
                        Ubah Setting
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- 2. CONFIGURED PROVIDERS GRID -->
    <div class="space-y-4">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Daftar Konfigurasi Gateway</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($settings as $set)
                <div class="bg-white border {{ $set->is_active ? 'border-indigo-300 ring-4 ring-indigo-50' : 'border-slate-150' }} rounded-3xl p-6 flex flex-col justify-between gap-6 shadow-sm">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[9px] font-black uppercase px-2.5 py-1 rounded {{ $set->is_active ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100 text-slate-500' }}">
                                {{ $set->provider }}
                            </span>
                            @if($set->is_active)
                                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 animate-pulse" title="Default Active"></span>
                            @endif
                        </div>

                        <div class="space-y-1">
                            <h4 class="text-sm font-black text-slate-900 uppercase truncate">{{ $set->name }}</h4>
                            <p class="text-[11px] text-slate-400 truncate">Model: <strong class="text-slate-700">{{ $set->model }}</strong></p>
                            <p class="text-[10px] text-slate-400 block truncate">Base: {{ $set->base_url ?? 'Default Endpoint' }}</p>
                        </div>
                    </div>

                    <div class="space-y-2 border-t border-slate-100 pt-4">
                        <div class="flex items-center gap-1.5 justify-between">
                            @if(!$set->is_active)
                                <form method="POST" action="{{ route('ai-settings.activate', $set->id) }}" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full text-center py-2 bg-slate-900 hover:bg-black text-white text-[10px] font-bold rounded-lg transition-all">
                                        Aktifkan Default
                                    </button>
                                </form>
                            @else
                                <span class="flex-1 text-center py-2 bg-indigo-50 text-indigo-700 text-[10px] font-black rounded-lg border border-indigo-100">
                                    Aktif Saat Ini
                                </span>
                            @endif

                            <button type="button" onclick="testConnection({{ $set->id }}, '{{ $set->provider }}', '{{ $set->model }}', '{{ $set->base_url }}')"
                                    class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold rounded-lg transition-all">
                                Tes
                            </button>

                            <button type="button" onclick="openEditModal({{ json_encode($set) }})"
                                    class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold rounded-lg transition-all">
                                Edit
                            </button>
                        </div>

                        <form method="POST" action="{{ route('ai-settings.destroy', $set->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus konfigurasi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-center text-[9px] text-rose-600 font-bold hover:underline">
                                Hapus Konfigurasi
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full border-2 border-dashed border-slate-200 rounded-3xl p-12 text-center text-xs text-slate-400 bg-slate-50/50">
                    Belum ada konfigurasi gateway AI terdaftar. Silakan buat satu di tombol pojok kanan atas.
                </div>
            @endforelse
        </div>
    </div>

    <!-- 3. CONFIGURATION MODAL (CREATE / EDIT) -->
    <div id="config-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden">
        <div class="bg-white border border-slate-200 rounded-[2.5rem] max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl relative">
            <button type="button" onclick="closeModal()" class="absolute right-6 top-6 text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <div>
                <h3 id="modal-title" class="text-lg font-black text-slate-900 uppercase">Tambah Gateway AI</h3>
                <p class="text-xs text-slate-400 mt-0.5">Isi parameter koneksi endpoint AI secara lengkap.</p>
            </div>

            <form id="modal-form" method="POST" action="" class="space-y-4">
                @csrf
                <div id="method-field"></div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Nama Gateway</label>
                        <input type="text" name="name" id="field-name" required placeholder="misal: Gemini Flash Lite" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Provider</label>
                        <select name="provider" id="field-provider" onchange="loadPresetModels()" 
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="openrouter">OpenRouter</option>
                            <option value="openai">OpenAI</option>
                            <option value="gemini">Google Gemini</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">API KEY</label>
                    <input type="password" name="api_key" id="field-apikey" required placeholder="Masukkan API Key Gateway" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Pilih Model Preset</label>
                        <select id="preset-models-select" onchange="syncCustomModel()" 
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-emerald-500 focus:border-emerald-500">
                            <!-- Populated dynamically via JS -->
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Model ID (Manual/Custom)</label>
                        <input type="text" name="model" id="field-model" required placeholder="google/gemini-2.5-flash-lite" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Custom Endpoint URL (Optional)</label>
                    <input type="text" name="base_url" id="field-url" placeholder="Gunakan default jika kosong" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <!-- Fallback Configuration Panel -->
                <div class="p-4 bg-slate-50 border border-slate-150 rounded-2xl space-y-3">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-black text-slate-800 select-none">
                        <input type="checkbox" name="enable_fallback" id="field-enable-fallback" class="rounded border-slate-350 text-emerald-600 focus:ring-emerald-500">
                        Aktifkan Failover Fallback
                    </label>
                    
                    <div id="fallback-select-container" class="space-y-1.5 hidden">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Jika koneksi utama gagal, hubungkan ke:</span>
                        <select name="fallback_provider" id="field-fallback-provider" 
                                class="w-full px-3.5 py-2.5 bg-white border border-slate-200/80 rounded-xl text-xs font-bold focus:ring-emerald-500">
                            <option value="gemini">Google Gemini</option>
                            <option value="openai">OpenAI</option>
                            <option value="openrouter">OpenRouter</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-4 pt-3">
                    <button type="button" onclick="testConnectionNew()" id="btn-test-modal" 
                            class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all">
                        Uji Koneksi
                    </button>
                    <div class="flex gap-2">
                        <button type="button" onclick="closeModal()" class="px-4 py-3 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md">
                            Simpan Setting
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const presets = @json(config('ai.providers'));

    // Tampilkan fallback select berdasarkan checkbox state
    document.getElementById('field-enable-fallback').addEventListener('change', function() {
        const container = document.getElementById('fallback-select-container');
        if (this.checked) {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    });

    function openCreateModal() {
        document.getElementById('modal-title').innerText = "Tambah Gateway AI";
        document.getElementById('modal-form').action = "{{ route('ai-settings.store') }}";
        document.getElementById('method-field').innerHTML = "";
        
        document.getElementById('field-name').value = "";
        document.getElementById('field-provider').value = "openrouter";
        document.getElementById('field-provider').disabled = false;
        document.getElementById('field-apikey').value = "";
        document.getElementById('field-apikey').required = true;
        document.getElementById('field-model').value = "";
        document.getElementById('field-url').value = "";
        document.getElementById('field-enable-fallback').checked = false;
        document.getElementById('fallback-select-container').classList.add('hidden');

        loadPresetModels();
        document.getElementById('config-modal').classList.remove('hidden');
    }

    function openEditModal(setting) {
        document.getElementById('modal-title').innerText = "Edit Gateway AI: " + setting.name;
        document.getElementById('modal-form').action = "/ai-settings/" + setting.id;
        document.getElementById('method-field').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        
        document.getElementById('field-name').value = setting.name;
        document.getElementById('field-provider').value = setting.provider;
        document.getElementById('field-provider').disabled = true; // Jangan biarkan merubah provider yang sudah diset
        
        // Gunakan placeholder bullet untuk apikey agar aman
        document.getElementById('field-apikey').value = "••••••••";
        document.getElementById('field-apikey').required = false;
        
        document.getElementById('field-model').value = setting.model;
        document.getElementById('field-url').value = setting.base_url || "";

        // Check fallback settings
        const extraSettings = setting.settings || {};
        if (extraSettings.enable_fallback) {
            document.getElementById('field-enable-fallback').checked = true;
            document.getElementById('fallback-select-container').classList.remove('hidden');
            document.getElementById('field-fallback-provider').value = extraSettings.fallback_provider || "gemini";
        } else {
            document.getElementById('field-enable-fallback').checked = false;
            document.getElementById('fallback-select-container').classList.add('hidden');
        }

        loadPresetModels(setting.model);
        document.getElementById('config-modal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('config-modal').classList.add('hidden');
    }

    function loadPresetModels(selectedModel = null) {
        const provider = document.getElementById('field-provider').value;
        const models = presets[provider]?.models || {};
        
        const select = document.getElementById('preset-models-select');
        select.innerHTML = "";

        Object.keys(models).forEach(key => {
            const opt = document.createElement('option');
            opt.value = key;
            opt.innerText = models[key];
            if (key === selectedModel) {
                opt.selected = true;
            }
            select.appendChild(opt);
        });

        // Opsi Custom Model
        const customOpt = document.createElement('option');
        customOpt.value = "custom";
        customOpt.innerText = "Custom Model ID (Isi Manual)";
        if (selectedModel && !models[selectedModel]) {
            customOpt.selected = true;
        }
        select.appendChild(customOpt);

        syncCustomModel();
    }

    function syncCustomModel() {
        const select = document.getElementById('preset-models-select');
        const customVal = select.value;
        
        if (customVal !== 'custom') {
            document.getElementById('field-model').value = customVal;
        }
    }

    /**
     * Uji koneksi dari form isian modal (belum disimpan)
     */
    function testConnectionNew() {
        const btn = document.getElementById('btn-test-modal');
        const oldText = btn.innerText;
        btn.innerText = "Menguji...";
        btn.disabled = true;

        const payload = {
            provider: document.getElementById('field-provider').value,
            model: document.getElementById('field-model').value,
            api_key: document.getElementById('field-apikey').value,
            base_url: document.getElementById('field-url').value,
        };

        // Ambil ID jika sedang mengedit konfigurasi
        const action = document.getElementById('modal-form').action;
        const match = action.match(/\/ai-settings\/(\d+)/);
        if (match) {
            payload.id = match[1];
        }

        fetch("{{ route('ai-settings.test') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            btn.innerText = oldText;
            btn.disabled = false;
            if (data.success) {
                alert("SUCCESS\n\nAI Provider berhasil terhubung.\n\nProvider: " + data.provider + "\nModel: " + data.model);
            } else {
                alert("ERROR\n\nConnection gagal.\n\nDetail: " + data.message);
            }
        })
        .catch(err => {
            btn.innerText = oldText;
            btn.disabled = false;
            alert("ERROR\n\nTerjadi kesalahan koneksi jaringan.");
        });
    }

    /**
     * Uji koneksi untuk baris yang sudah tersimpan
     */
    function testConnection(id, provider, model, baseUrl) {
        const payload = {
            id: id,
            provider: provider,
            model: model,
            api_key: '••••••••',
            base_url: baseUrl || null
        };

        fetch("{{ route('ai-settings.test') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("SUCCESS\n\nAI Provider berhasil terhubung.\n\nProvider: " + data.provider + "\nModel: " + data.model);
            } else {
                alert("ERROR\n\nConnection gagal.\n\nDetail: " + data.message);
            }
        })
        .catch(err => {
            alert("ERROR\n\nTerjadi kesalahan koneksi jaringan.");
        });
    }
</script>
@endpush
