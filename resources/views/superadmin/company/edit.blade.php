@extends('superadmin.layouts.app')

@section('title', 'Edit Profil Perusahaan')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header Actions -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Edit Profil: {{ $company->company_name }}</h1>
            <p class="text-xs text-slate-400 mt-1">Konfigurasikan aset brand, visi misi, penugasan manager, serta custom dynamic blocks.</p>
        </div>
        <a href="{{ route('superadmin.company.index') }}" class="px-4 py-2 bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-semibold rounded-xl transition-all">
            Kembali
        </a>
    </div>

    <!-- FORM ACTION DISERTAI PARAMETER $company->id -->
    <form method="POST" action="{{ route('superadmin.company.update', $company->id) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Bagian 1: Identitas Inti & Penugasan Manager -->
        <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Identitas & PIC Pengelola</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Dropdown Penugasan Role Management -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tugaskan Pengelola (Role Management)</label>
                    <select name="manager_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 font-medium">
                        <option value="">-- Belum Ditugaskan / Kelola Langsung Superadmin --</option>
                        @foreach($managers as $manager)
                            <option value="{{ $manager->id }}" {{ (old('manager_id', $company->manager_id) == $manager->id) ? 'selected' : '' }}>
                                {{ $manager->name }} ({{ $manager->email }}) - {{ $manager->position ?? 'Management' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Perusahaan</label>
                    <input type="text" name="company_name" value="{{ old('company_name', $company->company_name) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Slogan / Tagline</label>
                    <input type="text" name="tagline" value="{{ old('tagline', $company->tagline) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Email Perusahaan</label>
                    <input type="email" name="email" value="{{ old('email', $company->email) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone', $company->phone) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 font-medium">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alamat Kantor</label>
                    <textarea name="address" rows="2" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 font-medium">{{ old('address', $company->address) }}</textarea>
                </div>
            </div>

            <!-- Upload Logo & Banner -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Logo Perusahaan</label>
                    @if($company->logo)
                        <div class="mb-2"><img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" class="h-10 object-contain"></div>
                    @endif
                    <input type="file" name="logo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Banner Sampul</label>
                    @if($company->banner)
                        <div class="mb-2"><img src="{{ asset('storage/' . $company->banner) }}" alt="Banner" class="h-10 w-28 object-cover rounded-md"></div>
                    @endif
                    <input type="file" name="banner" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700">
                </div>
            </div>
        </div>

        <!-- Bagian 2: Visi, Misi, dan Tentang -->
        <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Visi, Misi & Tentang</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tentang Perusahaan</label>
                    <textarea name="about" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 font-medium">{{ old('about', $company->about) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Visi Perusahaan</label>
                    <textarea name="vision" rows="2" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 font-medium">{{ old('vision', $company->vision) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Misi Perusahaan</label>
                    <textarea name="mission" rows="4" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 font-medium">{{ old('mission', $company->mission) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Bagian 3: JSON Sosial Media -->
        <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tautan Sosial Media (JSON)</h2>
                    <p class="text-[11px] text-slate-400">Daftarkan URL akun resmi seperti LinkedIn, Instagram, TikTok, YouTube.</p>
                </div>
                <button type="button" onclick="addSocialMediaRow()" class="px-3 py-1.5 bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold text-xs rounded-xl hover:bg-emerald-100 transition-all">
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
                    <p class="text-[11px] text-slate-400">Kelola blok fleksibel untuk galeri gambar, video embed, teks panjang, atau link eksternal.</p>
                </div>
                <button type="button" onclick="addDynamicSection()" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition-all">
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
                                    <div class="mb-2 text-[10px] text-emerald-600 font-bold flex items-center gap-1">
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

        <div class="flex items-center justify-end gap-3 pt-4">
            <button type="submit" class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md transition-all">
                Simpan Perubahan
            </button>
        </div>
    </form>

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
                <input type="text" name="social_media[${socmedIndex}][url]" placeholder="https://linkedin.com/company/..." class="flex-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
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
                    <textarea name="dynamic_sections[${dynamicIndex}][content]" rows="2" placeholder="Tulis teks atau masukkan tautan video/link..." class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium"></textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Lampiran Gambar/File Tambahan (Opsional)</label>
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
</script>
@endpush
@endsection