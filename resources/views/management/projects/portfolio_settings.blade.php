@extends('management.layouts.app')

@section('title', 'Seting Portofolio - ' . $project->name)

@section('content')
<div class="max-w-5xl mx-auto space-y-6 font-sans">

    <!-- Header Actions -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Pengaturan Portofolio Publik</h1>
            <p class="text-xs text-slate-400 mt-1">Project: <span class="font-bold text-slate-800">{{ $project->name }}</span></p>
        </div>
        <a href="{{ route('management.projects.show', $project->id) }}" class="px-4 py-2 bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-semibold rounded-xl transition-all">
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Kolom Kiri: LUNOU AI Portfolio Architect (4 Kolom) -->
        <div class="lg:col-span-4">
            <div class="bg-slate-900 text-slate-100 border border-slate-800 rounded-3xl p-6 shadow-lg space-y-5 sticky top-24 max-h-[calc(100vh-8rem)] overflow-y-auto">
                <div class="flex items-center gap-3 pb-3 border-b border-slate-800">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/25 p-1 shrink-0">
                        <img src="{{ asset('icon/11.png') }}" alt="LUNOU" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h2 class="text-xs font-black text-white uppercase tracking-wider">LUNOU Portfolio Architect</h2>
                        <span class="text-[9px] font-bold text-emerald-300 block mt-0.5">Asisten Penulis Studi Kasus Publik</span>
                    </div>
                </div>

                <p class="text-[10px] text-slate-450 leading-relaxed">
                    Tuliskan poin-poin hasil kerja atau klik mic untuk bicara. LUNOU akan menyusun narasi studi kasus profesional (Short Summary, Solusi, Hasil/ROI, Tag Layanan, & Stacks) yang siap dipublikasikan ke portfolio web publik.
                </p>

                <!-- Input Prompt Box -->
                <div class="space-y-3">
                    <div class="relative flex items-center">
                        <input type="text" id="ai-portfolio-prompt" placeholder="Contoh: 'Tolong buat narasi optimasi SEO dan stack Next.js'" class="w-full pl-3 pr-9 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-[11px] text-white placeholder:text-slate-500 focus:ring-1 focus:ring-emerald-500 outline-none font-semibold" onkeydown="if(event.key === 'Enter') generatePortfolioWithAi()">
                        
                        <!-- Speech Mic Button -->
                        <button type="button" id="portfolio-mic-btn" onclick="togglePortfolioSpeech()" class="absolute right-2 text-slate-400 hover:text-white transition-all flex items-center" title="Gunakan Voice to Text">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                            </svg>
                        </button>
                    </div>

                    <button type="button" id="btn-run-portfolio-ai" onclick="generatePortfolioWithAi()" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-md flex items-center justify-center gap-1.5">
                        <span>Rancang Studi Kasus</span>
                    </button>
                </div>

                <!-- Status Feedback msg -->
                <div id="portfolio-ai-status" class="hidden text-[10px] text-emerald-400 font-bold animate-pulse text-center"></div>
            </div>
        </div>

        <!-- Kolom Kanan: Pengaturan Form (8 Kolom) -->
        <div class="lg:col-span-8">
            <form method="POST" action="{{ route('management.projects.portfolio.update', $project->id) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Toggle Publikasi Showcase -->
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-sm font-black text-slate-900 block">Tampilkan di Web Publik Company</span>
                        <p class="text-xs text-slate-400 mt-0.5">Jika aktif, proyek ini otomatis tampil di kartu portofolio halaman publik.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_showcased" value="1" {{ old('is_showcased', $project->is_showcased) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                    </label>
                </div>

                <!-- 1. Visual Assets: Cover Project & Logo Client -->
                <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Aset Visual Portofolio</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Cover Project -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Cover Project (Thumbnail Card)</label>
                            @if($project->project_cover)
                                <img src="{{ asset('storage/' . $project->project_cover) }}" alt="Cover" class="h-28 w-full object-cover rounded-2xl mb-3 border border-slate-200">
                            @endif
                            <input type="file" name="project_cover" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700">
                        </div>

                        <!-- Logo Client -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Logo Klien / Organisasi</label>
                            @if($project->client_logo)
                                <img src="{{ asset('storage/' . $project->client_logo) }}" alt="Client Logo" class="h-16 w-32 object-contain rounded-xl p-2 bg-slate-50 border border-slate-200 mb-3">
                            @endif
                            <input type="file" name="client_logo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700">
                        </div>

                        <!-- Demo URL / Live Website -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Tautan Live Website / Demo URL</label>
                            <input type="url" name="demo_url" value="{{ old('demo_url', $project->demo_url) }}" placeholder="https://demo-app.com" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-emerald-500">
                        </div>
                    </div>
                </div>

                <!-- 2. Narasi: Problem, Solution, Result -->
                <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Narasi & Studi Kasus</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Deskripsi Singkat (Short Summary)</label>
                            <textarea name="short_description" rows="2" placeholder="Ringkasan 1-2 kalimat untuk kartu portofolio..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-emerald-500">{{ old('short_description', $project->short_description ?? $project->problem_statement) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Solusi yang Diberikan (Solution)</label>
                            <textarea name="solution_statement" rows="2" placeholder="Bagaimana sistem ini memecahkan kendala..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-emerald-500">{{ old('solution_statement', $project->solution_statement) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Hasil & Dampak (Result / Impact)</label>
                            <textarea name="result_statement" rows="2" placeholder="Peningkatan efisiensi, metrik ROI, atau output yang dicapai..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-emerald-500">{{ old('result_statement', $project->result_statement) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- 3. Tags: Services & Tech Stacks -->
                <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Services & Teknologi yang Digunakan</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Services -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-slate-700 uppercase">Services Dikerjakan</label>
                                <button type="button" onclick="addServiceTag()" class="text-[10px] font-bold text-emerald-600">+ Tambah Service</button>
                            </div>
                            <div id="services-box" class="space-y-2">
                                @foreach($project->services_rendered ?? ['Web Development', 'UI/UX Design'] as $svc)
                                    <div class="flex items-center gap-2">
                                        <input type="text" name="services_rendered[]" value="{{ $svc }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                                        <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 font-bold px-1">&times;</button>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Tech Stacks -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-slate-700 uppercase">Teknologi / Stack</label>
                                <button type="button" onclick="addTechTag()" class="text-[10px] font-bold text-emerald-600">+ Tambah Stack</button>
                            </div>
                            <div id="tech-box" class="space-y-2">
                                @foreach($project->tech_stacks ?? ['Laravel 13', 'Tailwind CSS', 'MySQL'] as $tech)
                                    <div class="flex items-center gap-2">
                                        <input type="text" name="tech_stacks[]" value="{{ $tech }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                                        <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 font-bold px-1">&times;</button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Galeri Gambar Portofolio -->
                <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-4">
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Galeri Dokumentasi & Mockup (Multiple)</h2>
                    
                    @if(!empty($project->gallery_images))
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3">
                            @foreach($project->gallery_images as $gImg)
                                <img src="{{ asset('storage/' . $gImg) }}" alt="Gallery" class="w-full h-24 object-cover rounded-xl border border-slate-200">
                            @endforeach
                        </div>
                    @endif

                    <input type="file" name="new_gallery[]" multiple accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700">
                    <p class="text-[10px] text-slate-400">Pilih beberapa foto sekaligus untuk menambahkan ke galeri mockup proyek.</p>
                </div>

                <!-- 5. Testimoni Klien -->
                <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-4">
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Testimoni Klien (Opsional)</h2>
                    
                    @php $testi = $project->client_testimonial ?? []; @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-700 uppercase mb-1">Nama Pemberi Testimoni</label>
                            <input type="text" name="testimonial_author" value="{{ $testi['author'] ?? '' }}" placeholder="misal: Bapak Budi Santoso" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-700 uppercase mb-1">Jabatan & Instansi</label>
                            <input type="text" name="testimonial_role" value="{{ $testi['role'] ?? '' }}" placeholder="misal: Direktur Utama PT Maju" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold text-slate-700 uppercase mb-1">Isi Testimoni</label>
                            <textarea name="testimonial_content" rows="2" placeholder="Ulasan kepuasan klien..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">{{ $testi['content'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4">
                    <button type="submit" class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md transition-all">
                        Simpan Konfigurasi Portofolio
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function addServiceTag() {
        document.getElementById('services-box').insertAdjacentHTML('beforeend', `
            <div class="flex items-center gap-2">
                <input type="text" name="services_rendered[]" placeholder="Contoh: Mobile Development" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 font-bold px-1">&times;</button>
            </div>
        `);
    }

    function addTechTag() {
        document.getElementById('tech-box').insertAdjacentHTML('beforeend', `
            <div class="flex items-center gap-2">
                <input type="text" name="tech_stacks[]" placeholder="Contoh: Flutter, Firebase" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 font-bold px-1">&times;</button>
            </div>
        `);
    }

    let portfolioRec = null;
    let isPortfolioListening = false;

    function togglePortfolioSpeech() {
        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            alert("Maaf, browser Anda tidak mendukung fitur Voice-to-Text. Silakan gunakan Google Chrome, Safari, atau Microsoft Edge.");
            return;
        }

        const micBtn = document.getElementById('portfolio-mic-btn');
        const input = document.getElementById('ai-portfolio-prompt');

        if (isPortfolioListening) {
            portfolioRec.stop();
            return;
        }

        const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
        portfolioRec = new SpeechRec();
        portfolioRec.lang = 'id-ID';
        portfolioRec.continuous = false;
        portfolioRec.interimResults = false;

        portfolioRec.onstart = function() {
            isPortfolioListening = true;
            micBtn.classList.add('text-rose-500', 'animate-pulse');
            input.placeholder = "Mendengarkan...";
        };

        portfolioRec.onerror = function(event) {
            console.error(event.error);
            stopPortfolioSpeech();
        };

        portfolioRec.onend = function() {
            stopPortfolioSpeech();
        };

        portfolioRec.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            input.value = transcript;
            generatePortfolioWithAi();
        };

        portfolioRec.start();
    }

    function stopPortfolioSpeech() {
        isPortfolioListening = false;
        const micBtn = document.getElementById('portfolio-mic-btn');
        const input = document.getElementById('ai-portfolio-prompt');
        if (micBtn) {
            micBtn.classList.remove('text-rose-500', 'animate-pulse');
        }
        if (input) {
            input.placeholder = "Contoh: 'Tolong buat narasi optimasi SEO dan stack Next.js'";
        }
    }

    async function generatePortfolioWithAi() {
        const input = document.getElementById('ai-portfolio-prompt');
        const promptText = input.value.trim();
        if (!promptText) {
            alert('Tuliskan detail poin pengerjaan terlebih dahulu.');
            return;
        }

        const statusMsg = document.getElementById('portfolio-ai-status');
        const btnRun = document.getElementById('btn-run-portfolio-ai');

        btnRun.disabled = true;
        btnRun.innerText = 'AI...';
        statusMsg.innerText = '🧠 LUNOU sedang merancang studi kasus...';
        statusMsg.classList.remove('hidden');

        try {
            const res = await fetch("{{ route('management.projects.portfolio.generate-ai', $project->id) }}", {
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

                // 1. Short Description
                document.querySelector('textarea[name="short_description"]').value = d.short_description || '';

                // 2. Solution Statement
                document.querySelector('textarea[name="solution_statement"]').value = d.solution_statement || '';

                // 3. Result Statement
                document.querySelector('textarea[name="result_statement"]').value = d.result_statement || '';

                // 4. Services Rendered
                if (d.services_rendered && Array.isArray(d.services_rendered)) {
                    const box = document.getElementById('services-box');
                    box.innerHTML = '';
                    d.services_rendered.forEach(svc => {
                        box.insertAdjacentHTML('beforeend', `
                            <div class="flex items-center gap-2">
                                <input type="text" name="services_rendered[]" value="${svc}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 font-bold px-1">&times;</button>
                            </div>
                        `);
                    });
                }

                // 5. Tech Stacks
                if (d.tech_stacks && Array.isArray(d.tech_stacks)) {
                    const box = document.getElementById('tech-box');
                    box.innerHTML = '';
                    d.tech_stacks.forEach(tech => {
                        box.insertAdjacentHTML('beforeend', `
                            <div class="flex items-center gap-2">
                                <input type="text" name="tech_stacks[]" value="${tech}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium">
                                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 font-bold px-1">&times;</button>
                            </div>
                        `);
                    });
                }

                statusMsg.innerText = '✅ Studi kasus berhasil dirancang!';
                setTimeout(() => {
                    statusMsg.classList.add('hidden');
                }, 3000);
            } else {
                alert(json.message || 'Gagal merancang portofolio.');
                statusMsg.classList.add('hidden');
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan jaringan.');
            statusMsg.classList.add('hidden');
        } finally {
            btnRun.disabled = false;
            btnRun.innerText = 'Rancang Studi Kasus';
        }
    }
</script>
@endpush
@endsection