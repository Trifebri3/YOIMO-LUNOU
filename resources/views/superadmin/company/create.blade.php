@extends('superadmin.layouts.app')

@section('title', 'Buat Profil Perusahaan Baru')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header Actions -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Formulir Profil Perusahaan</h1>
            <p class="text-xs text-slate-400 mt-1">Daftarkan identitas inti dan rancang kolom dinamis via JSON builder.</p>
        </div>
        <a href="{{ route('superadmin.company.index') }}" class="px-4 py-2 bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-semibold rounded-xl transition-all">
            Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('superadmin.company.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- 1. Identitas Inti -->
        <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Identitas & Kontak Utama</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Perusahaan</label>
                    <input type="text" name="company_name" value="{{ old('company_name') }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 font-medium">
                    @error('company_name') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tagline / Slogan</label>
                    <input type="text" name="tagline" value="{{ old('tagline') }}" placeholder="Contoh: Modern Integrated Solution" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Email Perusahaan</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nomor Telepon / Hotline</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 font-medium">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alamat Kantor / Headquarters</label>
                    <textarea name="address" rows="2" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 font-medium">{{ old('address') }}</textarea>
                </div>
            </div>

            <!-- Upload Logo & Banner -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Logo Resmi (PNG/SVG/WebP)</label>
                    <input type="file" name="logo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Banner Sampul Utama</label>
                    <input type="file" name="banner" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700">
                </div>
            </div>
        </div>

        <!-- 2. Visi Misi & Tentang -->
        <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Visi, Misi & Tentang Kami</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tentang Perusahaan</label>
                    <textarea name="about" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 font-medium">{{ old('about') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Visi Perusahaan</label>
                    <textarea name="vision" rows="2" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 font-medium">{{ old('vision') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Misi Perusahaan (Gunakan baris baru untuk poin)</label>
                    <textarea name="mission" rows="4" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 font-medium">{{ old('mission') }}</textarea>
                </div>
            </div>
        </div>

        <!-- 3. Dynamic Sosmed (JSON) -->
        <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Saluran Sosial Media (JSON)</h2>
                    <p class="text-[11px] text-slate-400">Tambahkan akun Instagram, LinkedIn, TikTok, atau YouTube.</p>
                </div>
                <button type="button" onclick="addSocialMediaRow()" class="px-3 py-1.5 bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold text-xs rounded-xl hover:bg-emerald-100 transition-all">
                    + Tambah Sosmed
                </button>
            </div>
            <div id="social-media-container" class="space-y-3"></div>
        </div>

        <!-- 4. Dynamic Sections Builder (JSON) -->
        <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Custom Modular Content Builder (JSON)</h2>
                    <p class="text-[11px] text-slate-400">Blok fleksibel untuk galeri gambar, video embed, teks panjang, atau link eksternal.</p>
                </div>
                <button type="button" onclick="addDynamicSection()" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition-all">
                    + Tambah Blok Konten
                </button>
            </div>
            <div id="dynamic-sections-container" class="space-y-4"></div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4">
            <button type="submit" class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md transition-all">
                Simpan & Terbitkan Profil
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
    let socmedIndex = 0;
    let dynamicIndex = 0;

    function addSocialMediaRow() {
        const container = document.getElementById('social-media-container');
        const html = `
            <div class="flex items-center gap-3 social-row">
                <input type="text" name="social_media[${socmedIndex}][platform]" placeholder="Platform (Contoh: Instagram)" class="w-1/3 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                <input type="text" name="social_media[${socmedIndex}][url]" placeholder="https://instagram.com/..." class="flex-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
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
                        <input type="text" name="dynamic_sections[${dynamicIndex}][title]" placeholder="Contoh: Sertifikasi ISO / Galeri Proyek" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold">
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
                    <textarea name="dynamic_sections[${dynamicIndex}][content]" rows="2" placeholder="Tuliskan keterangan detail atau masukkan tautan URL..." class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium"></textarea>
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
</script>
@endpush
@endsection
