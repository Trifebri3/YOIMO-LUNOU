<div class="portal-asset-card bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-2xs hover:shadow-sm transition-all flex flex-col justify-between space-y-4"
     id="asset-card-{{ $asset->id }}"
     data-id="{{ $asset->id }}"
     data-status="{{ $asset->status }}"
     data-mandatory="{{ $asset->is_mandatory ? 'true' : 'false' }}"
     data-category="{{ $asset->category ?? 'Umum' }}">
    
    <!-- Top Header: Category, Mandatory Indicator & Status Pill -->
    <div class="space-y-2">
        <div class="flex items-center justify-between gap-2 flex-wrap">
            <div class="flex items-center gap-1.5 flex-wrap">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                    {{ $asset->category ?? 'Umum' }}
                </span>
                @if($asset->is_mandatory)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                        Wajib
                    </span>
                @else
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-50 text-slate-400 border border-slate-100">
                        Opsional
                    </span>
                @endif
            </div>

            <!-- Status Pill -->
            <div id="asset-status-pill-{{ $asset->id }}">
                @if($asset->status === 'approved')
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        Disetujui Tim
                    </span>
                @elseif($asset->status === 'submitted')
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-sky-50 text-sky-800 border border-sky-200">
                        <svg class="w-3 h-3 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Telah Dikirim
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-900 border border-amber-200">
                        <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Menunggu Input
                    </span>
                @endif
            </div>
        </div>

        <!-- Title & Description -->
        <div>
            <h5 class="text-sm sm:text-base font-bold text-slate-800 leading-snug">
                {{ $asset->title }}
            </h5>
            @if($asset->description)
                <p class="text-xs text-slate-500 font-normal leading-relaxed mt-1">
                    {{ $asset->description }}
                </p>
            @endif
        </div>
    </div>

    <!-- Submitted Content Details (Files, External Links, Notes) -->
    <div id="asset-details-box-{{ $asset->id }}" class="{{ ($asset->file_path || $asset->external_url || $asset->client_notes) ? 'block' : 'hidden' }} space-y-2 pt-2 border-t border-slate-100 text-xs">
        
        <!-- Uploaded File Preview/Download -->
        @if($asset->file_path)
            <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0 font-bold text-[10px]">
                        @if($asset->isImage())
                            <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        @else
                            <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        @endif
                    </div>
                    <div class="min-w-0 text-left">
                        <span class="block text-xs font-semibold text-slate-800 truncate" title="{{ $asset->file_name }}">
                            {{ $asset->file_name }}
                        </span>
                        <span class="block text-[10px] text-slate-400 font-medium">
                            {{ $asset->file_size ? round($asset->file_size / 1024, 1) . ' KB' : 'Berkas Terlampir' }}
                        </span>
                    </div>
                </div>
                <div class="shrink-0 flex items-center gap-1.5">
                    @if($asset->isImage())
                        <button type="button" onclick="openImageLightbox('{{ asset('storage/' . $asset->file_path) }}', '{{ addslashes($asset->file_name ?? $asset->title) }}')"
                                class="px-2.5 py-1 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-lg text-xs font-semibold cursor-pointer shadow-2xs">
                            Lihat
                        </button>
                    @endif
                    <a href="{{ asset('storage/' . $asset->file_path) }}" download
                       class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-2xs flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>Unduh</span>
                    </a>
                </div>
            </div>
        @endif

        <!-- External Link (Drive/Figma/Cloud) -->
        @if($asset->external_url)
            <div class="p-2.5 bg-sky-50/70 border border-sky-200/80 rounded-xl flex items-center justify-between gap-2">
                <div class="flex items-center gap-2 min-w-0">
                    <svg class="w-4 h-4 text-sky-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    <span class="truncate text-xs font-medium text-sky-900">{{ $asset->external_url }}</span>
                </div>
                <a href="{{ $asset->external_url }}" target="_blank" rel="noopener noreferrer"
                   class="shrink-0 px-2.5 py-1 bg-white hover:bg-sky-100 text-sky-800 border border-sky-300 rounded-lg text-xs font-bold transition-all shadow-2xs">
                    Buka Link &rarr;
                </a>
            </div>
        @endif

        <!-- Client Notes / Credentials -->
        @if($asset->client_notes)
            <div class="p-2.5 bg-amber-50/50 border border-amber-200/60 rounded-xl space-y-1 text-left">
                <span class="text-[10px] font-bold text-amber-800 uppercase tracking-wider block">Catatan / Detail Akses:</span>
                <p class="text-xs text-slate-700 font-normal whitespace-pre-line leading-relaxed italic">{{ $asset->client_notes }}</p>
            </div>
        @endif

        <!-- Submitter and Review Info -->
        <div class="text-[11px] text-slate-400 flex flex-wrap items-center justify-between gap-1 pt-1">
            @if($asset->submitted_by_name)
                <span>Diunggah oleh: <strong class="text-slate-700">{{ $asset->submitted_by_name }}</strong> ({{ $asset->submitted_at?->diffForHumans() ?? 'baru saja' }})</span>
            @endif
            @if($asset->reviewed_at)
                <span class="text-emerald-700 font-medium">Disetujui: {{ $asset->reviewed_at->diffForHumans() }}</span>
            @endif
        </div>
    </div>

    <!-- Actions Bar -->
    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
        <div class="flex items-center gap-1.5">
            <!-- Primary Action: Submit / Update Asset -->
            <button type="button" 
                    onclick="openSubmitAssetModal({{ $asset->id }}, '{{ addslashes($asset->title) }}', '{{ addslashes($asset->description ?? '') }}', '{{ addslashes($asset->external_url ?? '') }}', '{{ addslashes($asset->client_notes ?? '') }}')"
                    class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold transition-all flex items-center gap-1.5 shadow-2xs cursor-pointer">
                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                <span id="asset-btn-label-{{ $asset->id }}">
                    {{ $asset->status === 'pending' ? 'Unggah Berkas / Tautan' : 'Perbarui Berkas' }}
                </span>
            </button>

            <!-- Toggle Approval Button -->
            <button type="button"
                    onclick="toggleAssetApproval({{ $asset->id }})"
                    id="asset-approve-btn-{{ $asset->id }}"
                    class="px-2.5 py-1.5 border rounded-xl text-xs font-semibold transition-all flex items-center gap-1 cursor-pointer {{ $asset->status === 'approved' ? 'bg-amber-50 text-amber-800 border-amber-200 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-800 border-emerald-200 hover:bg-emerald-100' }}"
                    title="{{ $asset->status === 'approved' ? 'Batalkan persetujuan berkas' : 'Verifikasi dan setujui kelayakan berkas' }}">
                @if($asset->status === 'approved')
                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    <span class="hidden sm:inline">Batal Setuju</span>
                @else
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    <span class="hidden sm:inline">Setujui</span>
                @endif
            </button>
        </div>

        <!-- Delete Requirement Button (Adjustable per project needs) -->
        <button type="button" 
                onclick="deleteAssetRequirement({{ $asset->id }}, '{{ addslashes($asset->title) }}')"
                class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer"
                title="Hapus kebutuhan berkas ini dari proyek">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
        </button>
    </div>

</div>
