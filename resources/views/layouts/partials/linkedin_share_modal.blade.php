<!-- LinkedIn Share Modal & Clipboard Helper -->
<div id="linkedInShareModal" class="fixed inset-0 z-[9999] hidden flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm animate-fade-in">
    <div class="bg-white border border-slate-100 rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl transform transition-all">
        <!-- Modal Header -->
        <div class="px-6 py-5 bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>
                </div>
                <div>
                    <h4 class="text-sm font-black tracking-wide">Bagikan Pencapaian ke LinkedIn</h4>
                    <p class="text-[11px] text-blue-100 font-medium">Auto Caption & Gambar Siap Posting</p>
                </div>
            </div>
            <button type="button" onclick="closeLinkedInShareModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-5">
            <!-- Simulated LinkedIn Card Preview -->
            <div>
                <label class="block text-[11px] font-black uppercase text-slate-400 tracking-wider mb-2">Pratinjau Kartu LinkedIn (Auto Image & Keterangan)</label>
                <div class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-50 shadow-xs hover:border-blue-300 transition-all">
                    <div class="h-36 bg-slate-900 relative overflow-hidden flex items-center justify-center">
                        <img src="{{ asset('images/yoimo-achievement-og.png') }}" alt="Preview Banner" class="w-full h-full object-cover">
                        <span class="absolute top-2.5 right-2.5 px-2.5 py-0.5 bg-blue-600/90 text-white text-[10px] font-bold rounded-full backdrop-blur-xs">
                            Auto OG Image (1200x630)
                        </span>
                    </div>
                    <div class="p-3.5 space-y-1 bg-white">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block" id="liPreviewDomain">yoimo.lunou.tech</span>
                        <h5 class="text-xs font-black text-slate-800 line-clamp-1" id="liPreviewTitle">Penyelesaian Tugas Terverifikasi</h5>
                        <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed" id="liPreviewDesc">Tugas telah diselesaikan 100% dan terverifikasi secara resmi dalam ekosistem kerja Yoimo Workspace.</p>
                    </div>
                </div>
            </div>

            <!-- Editable Caption Area -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-[11px] font-black uppercase text-slate-500 tracking-wider">Caption Siap Posting</label>
                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">✓ Tinggal Paste & Post</span>
                </div>
                <textarea id="liShareCaptionText" rows="6" 
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-700 font-medium focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-hidden transition-all resize-none"></textarea>
                <p class="text-[10px] text-slate-400 mt-1">Tip: Anda dapat mengedit kalimat di atas sebelum membagikannya.</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center gap-2.5 pt-2">
                <button type="button" onclick="executeLinkedInShare(true)" 
                        class="w-full sm:flex-1 py-3 px-4 bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white text-xs font-black rounded-xl transition-all shadow-md flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>
                    <span>Salin Caption & Buka LinkedIn</span>
                </button>
                <button type="button" onclick="copyOnlyCaption()" 
                        class="w-full sm:w-auto py-3 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all">
                    Salin Caption Saja
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Global Toast Notification -->
<div id="liShareToast" class="fixed bottom-6 right-6 max-w-sm w-full bg-slate-900/95 text-white border border-blue-500/40 rounded-2xl p-4 shadow-2xl backdrop-blur-md transform translate-y-24 opacity-0 transition-all duration-300 z-[10000] pointer-events-none flex items-start gap-3">
    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
    </div>
    <div class="text-xs space-y-1">
        <p id="liToastTitle" class="font-black text-blue-200">Caption LinkedIn Disalin!</p>
        <p id="liToastMsg" class="text-slate-300 leading-relaxed">Tekan <kbd class="px-1.5 py-0.5 bg-slate-800 border border-slate-700 rounded text-[10px] font-mono text-amber-300">Ctrl + V</kbd> di LinkedIn, lalu klik <strong>Post</strong>.</p>
    </div>
</div>

<script>
    let currentLinkedInShareUrl = '';

    function showLinkedInToast(title, msg) {
        const toast = document.getElementById('liShareToast');
        if (!toast) return;
        if (title) document.getElementById('liToastTitle').textContent = title;
        if (msg) document.getElementById('liToastMsg').innerHTML = msg;
        toast.classList.remove('translate-y-24', 'opacity-0');
        setTimeout(() => {
            toast.classList.add('translate-y-24', 'opacity-0');
        }, 5000);
    }

    function openLinkedInShareModal(title, subtitle, url, caption) {
        currentLinkedInShareUrl = url;
        
        const modal = document.getElementById('linkedInShareModal');
        const titleEl = document.getElementById('liPreviewTitle');
        const descEl = document.getElementById('liPreviewDesc');
        const captionEl = document.getElementById('liShareCaptionText');
        
        if (titleEl) titleEl.textContent = title || 'Penyelesaian Tugas Terverifikasi';
        if (descEl) descEl.textContent = (subtitle ? subtitle + ' • ' : '') + 'Terverifikasi secara resmi dalam platform Yoimo Workspace.';
        if (captionEl) captionEl.value = caption;
        
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    function closeLinkedInShareModal() {
        const modal = document.getElementById('linkedInShareModal');
        if (modal) modal.classList.add('hidden');
    }

    function copyOnlyCaption() {
        const text = document.getElementById('liShareCaptionText').value;
        navigator.clipboard.writeText(text).then(() => {
            showLinkedInToast('Caption Berhasil Disalin!', 'Silakan paste di mana saja yang Anda inginkan.');
        });
    }

    function executeLinkedInShare(openWindow = true) {
        const text = document.getElementById('liShareCaptionText').value;
        const targetUrl = currentLinkedInShareUrl || window.location.href;
        const linkedInEndpoint = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(targetUrl)}`;
        
        navigator.clipboard.writeText(text).then(() => {
            showLinkedInToast('Caption LinkedIn Siap Posting!', 'Tekan <kbd class="px-1.5 py-0.5 bg-slate-800 border border-slate-700 rounded text-[10px] font-mono text-amber-300">Ctrl + V</kbd> di LinkedIn, lalu klik <strong>Post</strong>.');
            closeLinkedInShareModal();
            if (openWindow) {
                window.open(linkedInEndpoint, '_blank', 'width=650,height=620');
            }
        }).catch(() => {
            if (openWindow) {
                window.open(linkedInEndpoint, '_blank', 'width=650,height=620');
            }
        });
    }

    // Direct 1-Click Quick Share with auto-copy and direct popup
    function directLinkedInQuickShare(url, caption) {
        currentLinkedInShareUrl = url;
        const linkedInEndpoint = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`;
        navigator.clipboard.writeText(caption).then(() => {
            showLinkedInToast('Caption LinkedIn Siap Posting!', 'Tekan <kbd class="px-1.5 py-0.5 bg-slate-800 border border-slate-700 rounded text-[10px] font-mono text-amber-300">Ctrl + V</kbd> di LinkedIn, lalu klik <strong>Post</strong>.');
            window.open(linkedInEndpoint, '_blank', 'width=650,height=620');
        }).catch(() => {
            window.open(linkedInEndpoint, '_blank', 'width=650,height=620');
        });
    }
</script>
