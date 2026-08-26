@extends('management.layouts.app')

@section('title', 'Perpustakaan Dokumen - ' . $project->name)

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <style>
        trix-toolbar .trix-button-row { display: flex; flex-wrap: wrap; gap: 2px; }
        trix-editor { 
            min-height: 120px !important; 
            max-height: 250px; 
            overflow-y: auto; 
            background-color: #f8fafc; 
            border-radius: 0.75rem; 
            border-color: #e2e8f0; 
            font-size: 0.75rem;
        }
    </style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto space-y-8 font-sans">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Header Perpustakaan & Filter Toolbar -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase px-2.5 py-0.5 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200">
                        REPOSITORY & KNOWLEDGE BASE
                    </span>
                    <span class="text-xs font-semibold text-slate-400">• {{ $project->name }}</span>
                </div>
                <h1 class="text-2xl font-black text-slate-900 mt-1 tracking-tight">Perpustakaan Panduan & Dokumen Proyek</h1>
                <p class="text-xs text-slate-400 mt-0.5">Pusat aset digital, SOP kerja, dokumen spesifikasi teknis, serta file panduan resmi tim.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('management.projects.tasks.index', $project->id) }}" class="px-4 py-2 bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-100 transition-all">
                    Papan Tugas
                </a>
                <a href="{{ route('management.projects.roadmaps.index', $project->id) }}" class="px-4 py-2 bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-100 transition-all">
                    Linimasa
                </a>
                <a href="{{ route('management.projects.show', $project->id) }}" class="px-4 py-2 bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-100 transition-all">
                    Detail Project
                </a>
            </div>
        </div>

        <!-- Filter Kategori Kancing Cepat -->
        <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2">
            <a href="{{ route('management.projects.documents.index', $project->id) }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ empty($selectedCategory) ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                Semua Koleksi ({{ $project->repositoryDocuments->count() }})
            </a>
            @php
                $categories = [
                    'SOP & Panduan Kerja'       => 'bg-blue-50 text-blue-700 border-blue-200',
                    'Kontrak & Legalitas'       => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'Spesifikasi Teknis & API'  => 'bg-purple-50 text-purple-700 border-purple-200',
                    'Desain & Brand Asset'      => 'bg-pink-50 text-pink-700 border-pink-200',
                    'Laporan & Riset'           => 'bg-amber-50 text-amber-700 border-amber-200',
                    'Template & Format'         => 'bg-teal-50 text-teal-700 border-teal-200',
                ];
            @endphp
            @foreach($categories as $cat => $style)
                <a href="{{ route('management.projects.documents.index', [$project->id, 'category' => $cat]) }}" 
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $selectedCategory === $cat ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">

        <!-- Kolom Kiri: Grid Koleksi Repositori (7 Kolom) -->
        <div class="xl:col-span-7 space-y-6">
            <h2 class="text-base font-black text-slate-900 tracking-tight">Koleksi Dokumen & Aset Panduan</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @forelse($documents as $doc)
                    <div class="bg-white border {{ $doc->is_mandatory ? 'border-amber-300 ring-2 ring-amber-50 shadow-md' : 'border-slate-100 shadow-sm' }} rounded-3xl p-6 flex flex-col justify-between space-y-4 hover:shadow-md transition-all">
                        
                        <div class="space-y-3">
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2.5 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider border {{ $categories[$doc->category] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ $doc->category }}
                                </span>

                                @if($doc->is_mandatory)
                                    <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider bg-amber-500 text-white shadow-sm">
                                        WAJIB DIBACA
                                    </span>
                                @endif
                            </div>

                            <div>
                                <h3 class="text-base font-black text-slate-900 leading-snug">{{ $doc->title }}</h3>
                                <span class="text-[10px] text-slate-400 font-semibold block mt-1">
                                    Format: <strong class="text-slate-700 uppercase">{{ $doc->doc_type }}</strong>
                                    @if($doc->file_size) • {{ $doc->file_size }} @endif
                                </span>
                            </div>
                        </div>

                        <div class="space-y-3 pt-3 border-t border-slate-100">
                            <!-- Tracking Pembaca -->
                            <div class="flex items-center justify-between text-[11px] font-bold text-slate-500">
                                <span class="flex items-center gap-1">
                                    👥 {{ count($doc->readers_log ?? []) }} Orang Membaca
                                </span>
                                <span class="text-[10px] text-slate-400">{{ $doc->created_at->format('d M Y') }}</span>
                            </div>

                            <!-- Tombol Aksi Membuka / Mengunduh -->
                            <div class="flex items-center justify-between gap-2 pt-1">
                                @if($doc->doc_type === 'article')
                                    <a href="{{ route('management.projects.documents.show', [$project->id, $doc->id]) }}" class="w-full text-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                                        Buka Artikel Panduan
                                    </a>
                                @elseif($doc->doc_type === 'link')
                                    <a href="{{ $doc->external_url }}" target="_blank" onclick="fetch('{{ route('management.projects.documents.show', [$project->id, $doc->id]) }}')" class="w-full text-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm flex items-center justify-center gap-1.5">
                                        <span>Buka Tautan Eksternal</span> &rarr;
                                    </a>
                                @elseif($doc->doc_type === 'file' && $doc->file_path)
                                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" onclick="fetch('{{ route('management.projects.documents.show', [$project->id, $doc->id]) }}')" class="w-full text-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm flex items-center justify-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        <span>Unduh File Dokumen</span>
                                    </a>
                                @endif

                                <form method="POST" action="{{ route('management.projects.documents.destroy', [$project->id, $doc->id]) }}" onsubmit="return confirm('Hapus dokumen ini dari perpustakaan?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-rose-500 hover:bg-rose-50 rounded-xl transition-all" title="Hapus Dokumen">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                @empty
                    <div class="sm:col-span-2 bg-white border border-dashed border-slate-200 rounded-3xl p-12 text-center">
                        <p class="text-xs font-medium text-slate-400">Belum ada dokumen repositori yang ditambahkan pada kategori ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Kolom Kanan: Form Tambah Dokumen / Panduan Baru (5 Kolom) -->
        <div class="xl:col-span-5 space-y-6">
            <div class="bg-white border border-slate-100 rounded-3xl p-7 shadow-sm space-y-5 sticky top-24">
                <div>
                    <h2 class="text-sm font-black text-slate-900 tracking-tight">+ Tambah Dokumen & Panduan</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Unggah file repositori, tautan Google Drive/Figma, atau artikel panduan kerja.</p>
                </div>

                <form method="POST" action="{{ route('management.projects.documents.store', $project->id) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Judul Dokumen / Panduan</label>
                        <input type="text" name="title" required placeholder="misal: SOP Deployment Server & Wireframe Figma" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-indigo-500">
                    </div>

                    <!-- Kategori Repositori -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Kategori Dokumen</label>
                        <select name="category" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-indigo-700 focus:ring-indigo-500">
                            <option value="SOP & Panduan Kerja">SOP & Panduan Kerja</option>
                            <option value="Kontrak & Legalitas">Kontrak & Legalitas</option>
                            <option value="Spesifikasi Teknis & API">Spesifikasi Teknis & API</option>
                            <option value="Desain & Brand Asset">Desain & Brand Asset</option>
                            <option value="Laporan & Riset">Laporan & Riset</option>
                            <option value="Template & Format">Template & Format</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <!-- Sifat Wajib Baca Toggle -->
                    <div class="p-4 bg-amber-50/50 border border-amber-200 rounded-2xl flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Sifat: Wajib Dibaca Tim</span>
                            <span class="text-[10px] text-slate-400">Tandai dokumen sebagai instruksi krusial yang wajib dibuka.</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_mandatory" value="1" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                        </label>
                    </div>

                    <!-- Tipe Dokumen Switcher -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Tipe Format Dokumen</label>
                        <select name="doc_type" id="docTypeSelect" onchange="toggleDocTypeFields()" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                            <option value="file">File Upload (PDF, Word, Excel, ZIP, Gambar)</option>
                            <option value="link">Tautan Eksternal (Figma, Notion, Google Docs/Drive)</option>
                            <option value="article">Artikel / Panduan Teks Langsung (Rich Text)</option>
                        </select>
                    </div>

                    <!-- Field File Upload -->
                    <div id="fileUploadBox">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Pilih File Dokumen</label>
                        <input type="file" name="doc_file" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700">
                    </div>

                    <!-- Field Link Eksternal -->
                    <div id="externalUrlBox" class="hidden">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Tautan URL Dokumen</label>
                        <input type="url" name="external_url" placeholder="https://docs.google.com/... atau https://figma.com/..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500">
                    </div>

                    <!-- Field Artikel Teks Editor -->
                    <div id="articleBox" class="hidden">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Isi Panduan Kerja (Rich Text)</label>
                        <input id="doc_content" type="hidden" name="content">
                        <trix-editor input="doc_content" placeholder="Tuliskan materi panduan, checklist langkah kerja, atau SOP lengkap..."></trix-editor>
                    </div>

                    <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md transition-all">
                        Simpan ke Repositori
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>

@push('scripts')
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
    <script>
        function toggleDocTypeFields() {
            const type = document.getElementById('docTypeSelect').value;
            const fileBox = document.getElementById('fileUploadBox');
            const linkBox = document.getElementById('externalUrlBox');
            const articleBox = document.getElementById('articleBox');

            fileBox.classList.add('hidden');
            linkBox.classList.add('hidden');
            articleBox.classList.add('hidden');

            if (type === 'file') {
                fileBox.classList.remove('hidden');
            } else if (type === 'link') {
                linkBox.classList.remove('hidden');
            } else if (type === 'article') {
                articleBox.classList.remove('hidden');
            }
        }
    </script>
@endpush
@endsection