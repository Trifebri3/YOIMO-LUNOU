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

@section('title', 'Perbarui Profil Saya')

@section('content')
<div class="space-y-8 font-sans max-w-4xl mx-auto">

    <!-- Notifikasi Flash Sukses / Status -->
    @if(session('status') === 'profile-updated')
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3.5 rounded-2xl text-xs font-bold flex items-center justify-between shadow-sm">
            <span>Profil Anda berhasil diperbarui.</span>
        </div>
    @endif

    @if(session('status') === 'password-updated')
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3.5 rounded-2xl text-xs font-bold flex items-center justify-between shadow-sm">
            <span>Kata sandi Anda berhasil diperbarui.</span>
        </div>
    @endif

    <!-- 1. KARTU INFORMASI PROFIL LENGKAP -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
        <div>
            <h2 class="text-base font-black text-slate-900 tracking-tight">Informasi Profil Saya</h2>
            <p class="text-xs text-slate-400 mt-0.5">Perbarui detail personal, informasi kontak, jabatan, serta foto profil Anda.</p>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('patch')

            <!-- Grid Photo Profile & Upload -->
            <div class="flex flex-col sm:flex-row items-center gap-6 p-4 bg-slate-50 border border-slate-100 rounded-2xl">
                <!-- Avatar Preview -->
                <div class="shrink-0">
                    @if(Auth::user()->avatar)
                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-20 h-20 rounded-2xl object-cover ring-4 ring-white shadow-md">
                    @else
                        <div class="w-20 h-20 rounded-2xl bg-{{ $accentColor }}-600 text-white font-black text-xl flex items-center justify-center ring-4 ring-white shadow-md">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                    @endif
                </div>

                <!-- Input Upload -->
                <div class="space-y-2 text-center sm:text-left w-full">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Unggah Foto Profil Baru</label>
                    <input type="file" name="avatar" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-{{ $accentColor }}-50 file:text-{{ $accentColor }}-700 hover:file:bg-{{ $accentColor }}-100 transition-all">
                    <p class="text-[10px] text-slate-400">Rekomendasi ukuran square 1:1, format PNG/JPG, maks 5MB.</p>
                    @error('avatar') <span class="text-rose-500 text-[10px] font-semibold block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Grid Fields Info Utama -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" required value="{{ old('name', Auth::user()->name) }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-{{ $accentColor }}-500 focus:border-{{ $accentColor }}-500">
                    @error('name') <span class="text-rose-500 text-[10px] font-semibold block mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Alamat Email</label>
                    <input type="email" name="email" required value="{{ old('email', Auth::user()->email) }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-{{ $accentColor }}-500 focus:border-{{ $accentColor }}-500">
                    @error('email') <span class="text-rose-500 text-[10px] font-semibold block mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Nomor Telepon (WhatsApp Fonnte)</label>
                    <input type="text" name="phone" placeholder="contoh: 08123456789" value="{{ old('phone', Auth::user()->phone) }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-{{ $accentColor }}-500 focus:border-{{ $accentColor }}-500">
                    <p class="text-[9px] text-slate-400 mt-1">Gunakan format lokal/standar. Akan otomatis disesuaikan ke format Fonnte.</p>
                    @error('phone') <span class="text-rose-500 text-[10px] font-semibold block mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Jabatan / Posisi Kerja</label>
                    <input type="text" name="position" placeholder="misal: Backend Developer" value="{{ old('position', Auth::user()->position) }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-{{ $accentColor }}-500 focus:border-{{ $accentColor }}-500">
                    @error('position') <span class="text-rose-500 text-[10px] font-semibold block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Bio Singkat</label>
                <textarea name="bio" rows="3" placeholder="Tulis deskripsi singkat perkenalan tentang diri Anda..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-{{ $accentColor }}-500 focus:border-{{ $accentColor }}-500">{{ old('bio', Auth::user()->bio) }}</textarea>
                @error('bio') <span class="text-rose-500 text-[10px] font-semibold block mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-6 py-2.5 {{ $accentColor === 'emerald' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-indigo-600 hover:bg-indigo-700' }} text-white text-xs font-bold rounded-xl shadow-md transition-all">
                    Simpan Informasi Profil
                </button>
            </div>
        </form>
    </div>

    <!-- KARTU RIWAYAT & STATISTIK POIN AKTIVITAS (GAMIFIKASI) -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
        <div>
            <h2 class="text-base font-black text-slate-900 tracking-tight">Riwayat & Statistik Poin Aktivitas</h2>
            <p class="text-xs text-slate-400 mt-0.5">Pantau akumulasi poin Anda dari harian, ketepatan waktu tugas, dan tingkat level saat ini.</p>
        </div>

        <!-- Summary Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 bg-slate-50 border border-slate-150 rounded-2xl text-center">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Total Poin Terkumpul</span>
                <div class="text-xl font-black text-slate-800 mt-1">{{ $userPoint->total_points }} XP</div>
            </div>
            <div class="p-4 bg-slate-50 border border-slate-150 rounded-2xl text-center">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Level Pengguna</span>
                <div class="text-xl font-black text-{{ $accentColor }}-600 mt-1">Level {{ $userPoint->level }}</div>
            </div>
            <div class="p-4 bg-slate-50 border border-slate-150 rounded-2xl text-center">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Streak Login Harian</span>
                <div class="text-xl font-black text-emerald-600 mt-1">{{ $userPoint->login_streak }} Hari</div>
            </div>
        </div>

        <!-- Point Logs Table -->
        <div class="space-y-3">
            <span class="text-xs font-black text-slate-500 uppercase tracking-wider block">Detail Riwayat Poin Terbaru</span>
            <div class="bg-white border border-slate-150 rounded-2xl overflow-hidden shadow-xs">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase border-b border-slate-150">
                        <tr>
                            <th class="px-4 py-2.5">Aktivitas</th>
                            <th class="px-4 py-2.5">Sumber</th>
                            <th class="px-4 py-2.5 text-right">Poin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pointLogs as $log)
                            <tr>
                                <td class="px-4 py-3">
                                    <span class="font-bold text-slate-800">{{ $log->description }}</span>
                                    <span class="text-[9px] text-slate-400 block mt-0.5">{{ $log->created_at->diffForHumans() }}</span>
                                </td>
                                <td class="px-4 py-3 font-semibold text-slate-500">
                                    @if($log->project)
                                        {{ $log->project->name }}
                                    @elseif($log->company)
                                        {{ $log->company->company_name }}
                                    @else
                                        Sistem Global
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-black {{ $log->points >= 0 ? 'text-emerald-600' : 'text-rose-650' }}">
                                    {{ $log->points >= 0 ? '+' : '' }}{{ $log->points }} XP
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-slate-400 italic">Belum ada riwayat aktivitas koin terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 2. KARTU PERBARUI KATA SANDI -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
        <div>
            <h2 class="text-base font-black text-slate-900 tracking-tight">Ubah Kata Sandi</h2>
            <p class="text-xs text-slate-400 mt-0.5">Pastikan kata sandi Anda menggunakan kombinasi karakter yang aman.</p>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            @method('put')

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Kata Sandi Saat Ini</label>
                <input type="password" name="current_password" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-{{ $accentColor }}-500 focus:border-{{ $accentColor }}-500">
                @error('current_password', 'updatePassword') <span class="text-rose-500 text-[10px] font-semibold block mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Kata Sandi Baru</label>
                    <input type="password" name="password" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-{{ $accentColor }}-500 focus:border-{{ $accentColor }}-500">
                    @error('password', 'updatePassword') <span class="text-rose-500 text-[10px] font-semibold block mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-{{ $accentColor }}-500 focus:border-{{ $accentColor }}-500">
                    @error('password_confirmation', 'updatePassword') <span class="text-rose-500 text-[10px] font-semibold block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-6 py-2.5 {{ $accentColor === 'emerald' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-indigo-600 hover:bg-indigo-700' }} text-white text-xs font-bold rounded-xl shadow-md transition-all">
                    Perbarui Kata Sandi
                </button>
            </div>
        </form>
    </div>

    <!-- 3. KARTU PENGHAPUSAN AKUN (PILIHAN) -->
    <div class="bg-rose-50/40 border border-rose-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
        <div>
            <h2 class="text-base font-black text-rose-800 tracking-tight">Hapus Akun Permanen</h2>
            <p class="text-xs text-rose-600/80 mt-0.5">Setelah akun dihapus, seluruh resource, data aktivitas, dan berkas tugas Anda akan dihapus secara permanen dari server.</p>
        </div>

        <div class="p-4 bg-white border border-rose-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="text-xs font-semibold text-slate-600">
                Aksi ini bersifat final dan tidak dapat dibatalkan kembali.
            </div>
            
            <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun Anda secara permanen? Seluruh data akan hilang.')">
                @csrf
                @method('delete')

                <div>
                    <label class="block text-[10px] font-bold text-rose-800 uppercase tracking-wider mb-2">Konfirmasi Kata Sandi Anda</label>
                    <div class="flex gap-2">
                        <input type="password" name="password" required placeholder="Kata sandi konfirmasi..." class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-rose-500 focus:border-rose-500 w-full sm:w-48">
                        <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-md transition-all shrink-0">
                            Hapus Akun
                        </button>
                    </div>
                    @error('password', 'userDeletion') <span class="text-rose-500 text-[10px] font-semibold block mt-1">{{ $message }}</span> @enderror
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
