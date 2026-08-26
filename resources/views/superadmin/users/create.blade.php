@extends('superadmin.layouts.app')

@section('title', 'Tambah User Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Formulir Pendaftaran Akun</h1>
            <p class="text-xs text-slate-400 mt-1">Daftarkan akun profil baru beserta hak akses sistem dan nomor Fonnte.</p>
        </div>
        <a href="{{ route('superadmin.users.index') }}" class="px-4 py-2 bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-semibold rounded-xl transition-all">
            Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('superadmin.users.store') }}" enctype="multipart/form-data" class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Nama Lengkap -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                @error('name') <span class="text-rose-500 text-[10px] font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alamat Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                @error('email') <span class="text-rose-500 text-[10px] font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- Nomor WhatsApp Fonnte -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nomor WhatsApp (Fonnte API)</label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 08123456789 atau 628123456789" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                <p class="text-[10px] text-slate-400 mt-1">Otomatis diformat ke standar 628xxx saat disimpan.</p>
                @error('phone') <span class="text-rose-500 text-[10px] font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- Jabatan / Posisi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Jabatan / Posisi Kerja</label>
                <input type="text" name="position" value="{{ old('position') }}" placeholder="Contoh: Head of Operations, Chief Accountant" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                @error('position') <span class="text-rose-500 text-[10px] font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- Role Akses -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Role Perizinan</label>
                <select name="role" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                    <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User (Client/Portal)</option>
                    <option value="finance" {{ old('role') === 'finance' ? 'selected' : '' }}>Finance (Keuangan)</option>
                    <option value="management" {{ old('role') === 'management' ? 'selected' : '' }}>Management (Eksekutif)</option>
                    <option value="superadmin" {{ old('role') === 'superadmin' ? 'selected' : '' }}>Superadmin (Root Master)</option>
                </select>
                @error('role') <span class="text-rose-500 text-[10px] font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- Password Awal -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Password Akun</label>
                <input type="password" name="password" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                @error('password') <span class="text-rose-500 text-[10px] font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- Foto Profil -->
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Foto Profil (Avatar)</label>
                <input type="file" name="avatar" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                @error('avatar') <span class="text-rose-500 text-[10px] font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- Bio / Deskripsi -->
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Bio / Catatan Khusus</label>
                <textarea name="bio" rows="3" placeholder="Tuliskan deskripsi ringkas atau catatan tugas..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 focus:border-emerald-500 font-medium">{{ old('bio') }}</textarea>
                @error('bio') <span class="text-rose-500 text-[10px] font-semibold">{{ $message }}</span> @enderror
            </div>

        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
                Simpan & Daftarkan User
            </button>
        </div>
    </form>

</div>
@endsection
