@extends('management.layouts.app')

@section('title', 'Edit Profil Perusahaan')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 font-sans">

    <!-- Header Actions -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Edit Konten: {{ $company->company_name }}</h1>
            <p class="text-xs text-slate-400 mt-1">Perbarui identitas, kontak, visi misi, sosial media, dan custom JSON builder.</p>
        </div>
        <a href="{{ route('management.company.index') }}" class="px-4 py-2 bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-semibold rounded-xl transition-all">
            Kembali
        </a>
    </div>

    <!-- Form Action mengarah ke route update management -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Kolom Kiri: LUNOU AI Company Profile Architect (4 Kolom) -->
        <div class="lg:col-span-4">
            <div class="bg-slate-900 text-slate-100 border border-slate-800 rounded-3xl p-6 shadow-lg space-y-5 sticky top-24 max-h-[calc(100vh-8rem)] overflow-y-auto">
                <div class="flex items-center gap-3 pb-3 border-b border-slate-800">
                    <div class="w-8 h-8 rounded-xl bg-indigo-500/10 border border-indigo-500/25 p-1 shrink-0">
                        <img src="{{ asset('icon/11.png') }}" alt="LUNOU" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h2 class="text-xs font-black text-white uppercase tracking-wider">LUNOU Company Architect</h2>
                        <span class="text-[9px] font-bold text-indigo-300 block mt-0.5">Asisten Penulis Profil Perusahaan</span>
                    </div>
                </div>

                <p class="text-[10px] text-slate-450 leading-relaxed">
                    Ketik deskripsi model bisnis, visi, atau instruksi kustom, atau gunakan mic untuk bicara. LUNOU akan merancang identitas perusahaan yang lengkap (Tagline, Visi, Misi, Deskripsi Tentang, Tautan Sosmed, & Blok Kustom Modular).
                </p>

                <!-- Input Prompt Box -->
                <div class="space-y-3">
                    <div class="relative flex items-center">
                        <input type="text" id="ai-company-prompt" placeholder="Contoh: 'Buat profil agency software modern'" class="w-full pl-3 pr-9 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-[11px] text-white placeholder:text-slate-500 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 outline-none font-semibold" onkeydown="if(event.key === 'Enter') generateCompanyWithAi()">
                        
                        <!-- Speech Mic Button -->
                        <button type="button" id="company-mic-btn" onclick="toggleCompanySpeech()" class="absolute right-2 text-slate-400 hover:text-white transition-all flex items-center" title="Gunakan Voice to Text">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                            </svg>
                        </button>
                    </div>

                    <button type="button" id="btn-run-company-ai" onclick="generateCompanyWithAi()" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition-all shadow-md flex items-center justify-center gap-1.5">
                        <span>Rancang Profil Perusahaan</span>
                    </button>
                </div>

                <!-- Status Feedback msg -->
                <div id="company-ai-status" class="hidden text-[10px] text-indigo-300 font-bold animate-pulse text-center"></div>
            </div>
        </div>

        <!-- Kolom Kanan: Pengaturan Form (8 Kolom) -->
        <div class="lg:col-span-8">
            <form method="POST" action="{{ route('management.company.update', $company->id) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Bagian 1: Identitas Inti Perusahaan -->
                <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Identitas & Kontak Utama</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Perusahaan</label>
                            <input type="text" name="company_name" value="{{ old('company_name', $company->company_name) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-indigo-500 font-medium">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Slogan / Tagline</label>
                            <input type="text" name="tagline" value="{{ old('tagline', $company->tagline) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-indigo-500 font-medium">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Email Perusahaan</label>
                            <input type="email" name="email" value="{{ old('email', $company->email) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-indigo-500 font-medium">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nomor WhatsApp / Kontak</label>
                            <input type="text" name="phone" value="{{ old('phone', $company->phone) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-indigo-500 font-medium">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alamat Kantor</label>
                            <textarea name="address" rows="2" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-indigo-500 font-medium">{{ old('address', $company->address) }}</textarea>
                        </div>
                    </div>

                    <!-- Upload Logo & Banner -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Logo Perusahaan</label>
                            @if($company->logo)
                                <div class="mb-2"><img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" class="h-10 object-contain"></div>
                            @endif
                            <input type="file" name="logo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Banner Sampul</label>
                            @if($company->banner)
                                <div class="mb-2"><img src="{{ asset('storage/' . $company->banner) }}" alt="Banner" class="h-10 w-28 object-cover rounded-md"></div>
                            @endif
                            <input type="file" name="banner" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700">
                        </div>
                    </div>
                </div>

                <!-- Bagian 2: Visi, Misi & Tentang -->
                <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Visi, Misi & Tentang</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tentang Perusahaan</label>
                            <textarea name="about" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-indigo-500 font-medium">{{ old('about', $company->about) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Visi Perusahaan</label>
                            <textarea name="vision" rows="2" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-indigo-500 font-medium">{{ old('vision', $company->vision) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Misi Perusahaan</label>
                            <textarea name="mission" rows="4" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-indigo-500 font-medium">{{ old('mission', $company->mission) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Bagian 3: JSON Sosial Media -->
                <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tautan Sosial Media (JSON)</h2>
                            <p class="text-[11px] text-slate-400">Kelola akun resmi perusahaan (LinkedIn, Instagram, TikTok, YouTube).</p>
                        </div>
                        <button type="button" onclick="addSocialMediaRow()" class="px-3 py-1.5 bg-indigo-50 border border-indigo-200 text-indigo-700 font-bold text-xs rounded-xl hover:bg-indigo-100 transition-all">
                            + Tambah Sosmed
                        </button>
                    </div>

                    <div id="social-media-container" class="space-y-3">
                        @if(!empty($company->social_media))
                            @foreach($company->social_media as $idx => $item)
                                <div class="flex items-center gap-3 social-row">
                                    <input type="text" name="social_media[{{ $idx }}][platform]" value="{{ $item['platform'] ?? '' }}" placeholder="Platform (misal: Instagram)" class="w-1/3 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                                    <input type="text" name="social_media[{{ $idx }}][url]" value="{{ $item['url'] ?? '' }}" placeholder="https://..." class="flex-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                                    <button type="button" onclick="removeRow(this)" class="p-2.5 text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Bagian 4: JSON Dynamic Sections (Custom Blocks) -->
                <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Custom Modular Content Builder (JSON)</h2>
                            <p class="text-[11px] text-slate-400">Blok fleksibel untuk galeri gambar, video embed, teks, atau link eksternal.</p>
                        </div>
                        <button type="button" onclick="addDynamicSection()" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition-all">
                            + Tambah Blok Konten
                        </button>
                    </div>

                    <div id="dynamic-sections-container" class="space-y-4">
                        @if(!empty($company->dynamic_sections))
                            @foreach($company->dynamic_sections as $idx => $sec)
                                <div class="p-5 border border-slate-200 rounded-2xl bg-slate-50/50 space-y-4 dynamic-block relative">
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="flex-1">
                                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Judul Kolom / Blok</label>
                                            <input type="text" name="dynamic_sections[{{ $idx }}][title]" value="{{ $sec['title'] ?? '' }}" placeholder="Judul Kolom" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold">
                                        </div>
                                        <div class="w-48">
                                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Jenis Konten</label>
                                            <select name="dynamic_sections[{{ $idx }}][type]" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium">
                                                <option value="text" {{ ($sec['type'] ?? '') === 'text' ? 'selected' : '' }}>Teks / Narasi</option>
                                                <option value="image" {{ ($sec['type'] ?? '') === 'image' ? 'selected' : '' }}>Galeri / Gambar</option>
                                                <option value="video" {{ ($sec['type'] ?? '') === 'video' ? 'selected' : '' }}>Video Link / Embed</option>
                                                <option value="link" {{ ($sec['type'] ?? '') === 'link' ? 'selected' : '' }}>Link Eksternal</option>
                                            </select>
                                        </div>
                                        <button type="button" onclick="removeRow(this)" class="p-2 text-rose-500 hover:bg-rose-50 rounded-xl transition-all mt-4">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Isi Konten / Deskripsi / URL</label>
                                        <textarea name="dynamic_sections[{{ $idx }}][content]" rows="2" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium">{{ $sec['content'] ?? '' }}</textarea>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Lampiran Gambar/File (Opsional)</label>
                                        @if(!empty($sec['file_url']))
                                            <div class="mb-2 text-[10px] text-indigo-600 font-bold flex items-center gap-1">
                                                <span>File aktif:</span>
                                                <a href="{{ asset('storage/' . $sec['file_url']) }}" target="_blank" class="underline truncate">{{ $sec['file_url'] }}</a>
                                            </div>
                                        @endif
                                        <input type="file" name="dynamic_files[{{ $idx }}]" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-white file:border-slate-200">
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Pengaturan Publikasi & URL Slug -->
                <div class="p-5 bg-indigo-50/50 border border-indigo-100 rounded-2xl space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="block text-xs font-bold text-slate-800">Status Publikasi Web Portofolio</label>
                            <p class="text-[11px] text-slate-400">Jika aktif, portofolio dapat diakses oleh publik tanpa perlu login.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_published" value="1" {{ old('is_published', $company->is_published) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Kustom URL Slug Publik</label>
                        <div class="flex items-center">
                            <span class="px-3 py-2 bg-slate-100 border border-r-0 border-slate-200 rounded-l-xl text-xs text-slate-500 font-medium">
                                {{ url('/company') }}/
                            </span>
                            <input type="text" name="slug" value="{{ old('slug', $company->slug) }}" placeholder="nama-perusahaan" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-r-xl text-xs font-bold text-indigo-700 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4">
                    <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md transition-all">
                        Simpan Perubahan Konten
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    let socmedIndex = {{ count($company->social_media ?? []) }};
    let dynamicIndex = {{ count($company->dynamic_sections ?? []) }};

    function addSocialMediaRow() {
        const container = document.getElementById('social-media-container');
        const html = `
            <div class="flex items-center gap-3 social-row">
                <input type="text" name="social_media[${socmedIndex}][platform]" placeholder="Platform (misal: LinkedIn)" class="w-1/3 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                <input type="text" name="social_media[${socmedIndex}][url]" placeholder="https://..." class="flex-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                <button type="button" onclick="removeRow(this)" class="p-2.5 text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        socmedIndex++;
    }

    function addDynamicSection() {
        const container = document.getElementById('dynamic-sections-container');
        const html = `
            <div class="p-5 border border-slate-200 rounded-2xl bg-slate-50/50 space-y-4 dynamic-block relative">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex-1">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Judul Kolom / Blok</label>
                        <input type="text" name="dynamic_sections[${dynamicIndex}][title]" placeholder="Judul Kolom" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold">
                    </div>
                    <div class="w-48">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Jenis Konten</label>
                        <select name="dynamic_sections[${dynamicIndex}][type]" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium">
                            <option value="text">Teks / Narasi</option>
                            <option value="image">Galeri / Gambar</option>
                            <option value="video">Video Link / Embed</option>
                            <option value="link">Link Eksternal</option>
                        </select>
                    </div>
                    <button type="button" onclick="removeRow(this)" class="p-2 text-rose-500 hover:bg-rose-50 rounded-xl transition-all mt-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Isi Konten / Deskripsi / URL</label>
                    <textarea name="dynamic_sections[${dynamicIndex}][content]" rows="2" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium"></textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Lampiran Gambar/File (Opsional)</label>
                    <input type="file" name="dynamic_files[${dynamicIndex}]" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-white file:border-slate-200">
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        dynamicIndex++;
    }

    function removeRow(button) {
        button.closest('.social-row, .dynamic-block').remove();
    }

    let companyRec = null;
    let isCompanyListening = false;

    function toggleCompanySpeech() {
        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            alert("Maaf, browser Anda tidak mendukung fitur Voice-to-Text. Silakan gunakan Google Chrome, Safari, atau Microsoft Edge.");
            return;
        }

        const micBtn = document.getElementById('company-mic-btn');
        const input = document.getElementById('ai-company-prompt');

        if (isCompanyListening) {
            companyRec.stop();
            return;
        }

        const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
        companyRec = new SpeechRec();
        companyRec.lang = 'id-ID';
        companyRec.continuous = false;
        companyRec.interimResults = false;

        companyRec.onstart = function() {
            isCompanyListening = true;
            micBtn.classList.add('text-rose-500', 'animate-pulse');
            input.placeholder = "Mendengarkan...";
        };

        companyRec.onerror = function(event) {
            console.error(event.error);
            stopCompanySpeech();
        };

        companyRec.onend = function() {
            stopCompanySpeech();
        };

        companyRec.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            input.value = transcript;
            generateCompanyWithAi();
        };

        companyRec.start();
    }

    function stopCompanySpeech() {
        isCompanyListening = false;
        const micBtn = document.getElementById('company-mic-btn');
        const input = document.getElementById('ai-company-prompt');
        if (micBtn) {
            micBtn.classList.remove('text-rose-500', 'animate-pulse');
        }
        if (input) {
            input.placeholder = "Contoh: 'Buat profil agency software modern'";
        }
    }

    async function generateCompanyWithAi() {
        const input = document.getElementById('ai-company-prompt');
        const promptText = input.value.trim();
        if (!promptText) {
            alert('Tuliskan detail instruksi kustom terlebih dahulu.');
            return;
        }

        const statusMsg = document.getElementById('company-ai-status');
        const btnRun = document.getElementById('btn-run-company-ai');

        btnRun.disabled = true;
        btnRun.innerText = 'AI...';
        statusMsg.innerText = '🧠 LUNOU sedang merancang profil...';
        statusMsg.classList.remove('hidden');

        try {
            const res = await fetch("{{ route('management.company.generate-ai', $company->id) }}", {
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

                // 1. Tagline
                if (d.tagline) {
                    document.querySelector('input[name="tagline"]').value = d.tagline;
                }

                // 2. About
                if (d.about) {
                    document.querySelector('textarea[name="about"]').value = d.about;
                }

                // 3. Vision
                if (d.vision) {
                    document.querySelector('textarea[name="vision"]').value = d.vision;
                }

                // 4. Mission
                if (d.mission) {
                    document.querySelector('textarea[name="mission"]').value = d.mission;
                }

                // 5. Social Media
                if (d.social_media && Array.isArray(d.social_media)) {
                    const box = document.getElementById('social-media-container');
                    box.innerHTML = '';
                    socmedIndex = 0;
                    d.social_media.forEach(item => {
                        box.insertAdjacentHTML('beforeend', `
                            <div class="flex items-center gap-3 social-row">
                                <input type="text" name="social_media[${socmedIndex}][platform]" value="${item.platform || ''}" placeholder="Platform (misal: LinkedIn)" class="w-1/3 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                                <input type="text" name="social_media[${socmedIndex}][url]" value="${item.url || ''}" placeholder="https://..." class="flex-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                                <button type="button" onclick="removeRow(this)" class="p-2.5 text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        `);
                        socmedIndex++;
                    });
                }

                // 6. Dynamic Sections
                if (d.dynamic_sections && Array.isArray(d.dynamic_sections)) {
                    const box = document.getElementById('dynamic-sections-container');
                    box.innerHTML = '';
                    dynamicIndex = 0;
                    d.dynamic_sections.forEach(sec => {
                        box.insertAdjacentHTML('beforeend', `
                            <div class="p-5 border border-slate-200 rounded-2xl bg-slate-50/50 space-y-4 dynamic-block relative">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex-1">
                                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Judul Kolom / Blok</label>
                                        <input type="text" name="dynamic_sections[${dynamicIndex}][title]" value="${sec.title || ''}" placeholder="Judul Kolom" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold">
                                    </div>
                                    <div class="w-48">
                                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Jenis Konten</label>
                                        <select name="dynamic_sections[${dynamicIndex}][type]" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium">
                                            <option value="text" ${sec.type === 'text' ? 'selected' : ''}>Teks / Narasi</option>
                                            <option value="image" ${sec.type === 'image' ? 'selected' : ''}>Galeri / Gambar</option>
                                            <option value="video" ${sec.type === 'video' ? 'selected' : ''}>Video Link / Embed</option>
                                            <option value="link" ${sec.type === 'link' ? 'selected' : ''}>Link Eksternal</option>
                                        </select>
                                    </div>
                                    <button type="button" onclick="removeRow(this)" class="p-2 text-rose-500 hover:bg-rose-50 rounded-xl transition-all mt-4">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Isi Konten / Deskripsi / URL</label>
                                    <textarea name="dynamic_sections[${dynamicIndex}][content]" rows="2" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium">${sec.content || ''}</textarea>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Lampiran Gambar/File (Opsional)</label>
                                    <input type="file" name="dynamic_files[${dynamicIndex}]" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-white file:border-slate-200">
                                </div>
                            </div>
                        `);
                        dynamicIndex++;
                    });
                }

                statusMsg.innerText = '✅ Profil perusahaan berhasil dirancang!';
                setTimeout(() => {
                    statusMsg.classList.add('hidden');
                }, 3000);
            } else {
                alert(json.message || 'Gagal merancang profil.');
                statusMsg.classList.add('hidden');
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan jaringan.');
            statusMsg.classList.add('hidden');
        } finally {
            btnRun.disabled = false;
            btnRun.innerText = 'Rancang Profil Perusahaan';
        }
    }
</script>
@endpush
@endsection