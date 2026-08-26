<!-- Global Toast Notifications Container -->
<div id="global-toast-container" class="fixed top-5 right-5 z-[9999] space-y-3 max-w-sm w-full pointer-events-none">
    
    @if(session('success'))
        <div class="toast-item pointer-events-auto bg-white border-l-4 border-emerald-500 rounded-2xl shadow-xl p-4 flex items-start gap-3 transition-all duration-300 transform translate-x-0">
            <span class="p-1.5 bg-emerald-50 text-emerald-600 rounded-lg shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            </span>
            <div class="flex-1">
                <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider">Berhasil</h4>
                <p class="text-[11px] text-slate-500 font-semibold leading-relaxed mt-0.5">{{ session('success') }}</p>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="toast-item pointer-events-auto bg-white border-l-4 border-rose-500 rounded-2xl shadow-xl p-4 flex items-start gap-3 transition-all duration-300 transform translate-x-0">
            <span class="p-1.5 bg-rose-50 text-rose-600 rounded-lg shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </span>
            <div class="flex-1">
                <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider">Kesalahan</h4>
                <p class="text-[11px] text-slate-500 font-semibold leading-relaxed mt-0.5">{{ session('error') }}</p>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-650 font-bold">&times;</button>
        </div>
    @endif

    @if($errors->any())
        @foreach($errors->all() as $error)
            <div class="toast-item pointer-events-auto bg-white border-l-4 border-rose-500 rounded-2xl shadow-xl p-4 flex items-start gap-3 transition-all duration-300 transform translate-x-0">
                <span class="p-1.5 bg-rose-50 text-rose-600 rounded-lg shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </span>
                <div class="flex-1">
                    <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider">Input Bermasalah</h4>
                    <p class="text-[11px] text-slate-500 font-semibold leading-relaxed mt-0.5">{{ $error }}</p>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
            </div>
        @endforeach
    @endif

</div>

<!-- JS Dismissal Animation -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            document.querySelectorAll('.toast-item').forEach(item => {
                item.classList.add('opacity-0', 'translate-x-10');
                setTimeout(() => item.remove(), 400);
            });
        }, 5000);
    });
</script>
