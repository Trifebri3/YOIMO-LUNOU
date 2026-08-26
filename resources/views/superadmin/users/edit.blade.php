@extends('superadmin.layouts.app')

@section('title', 'Edit Profil User')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Edit Profil: {{ $user->name }}</h1>
            <p class="text-xs text-slate-400 mt-1">Perbarui nomor telepon Fonnte, posisi, bio, avatar, atau perizinan role.</p>
        </div>
        <a href="{{ route('superadmin.users.index') }}" class="px-4 py-2 bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-semibold rounded-xl transition-all">
            Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('superadmin.users.update', $user->id) }}" enctype="multipart/form-data" class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Nama -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                @error('name') <span class="text-rose-500 text-[10px] font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alamat Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                @error('email') <span class="text-rose-500 text-[10px] font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- WhatsApp -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nomor WhatsApp (Fonnte API)</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                @error('phone') <span class="text-rose-500 text-[10px] font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- Posisi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Jabatan / Posisi Kerja</label>
                <input type="text" name="position" value="{{ old('position', $user->position) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                @error('position') <span class="text-rose-500 text-[10px] font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- Role -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Role Perizinan</label>
                <select name="role" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                    <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User (Client/Portal)</option>
                    <option value="finance" {{ old('role', $user->role) === 'finance' ? 'selected' : '' }}>Finance (Keuangan)</option>
                    <option value="management" {{ old('role', $user->role) === 'management' ? 'selected' : '' }}>Management (Eksekutif)</option>
                    <option value="superadmin" {{ old('role', $user->role) === 'superadmin' ? 'selected' : '' }}>Superadmin (Root Master)</option>
                </select>
                @error('role') <span class="text-rose-500 text-[10px] font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- Ganti Password -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Password Baru (Opsional)</label>
                <input type="password" name="password" placeholder="Kosongkan jika tidak ingin diubah" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                @error('password') <span class="text-rose-500 text-[10px] font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- Avatar -->
            <div class="md:col-span-2 flex items-center gap-4">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-14 h-14 rounded-2xl object-cover ring-2 ring-emerald-200">
                @endif
                <div class="flex-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Ganti Foto Profil</label>
                    <input type="file" name="avatar" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    @error('avatar') <span class="text-rose-500 text-[10px] font-semibold">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Bio -->
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Bio / Catatan Khusus</label>
                <textarea name="bio" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-emerald-500 focus:border-emerald-500 font-medium">{{ old('bio', $user->bio) }}</textarea>
                @error('bio') <span class="text-rose-500 text-[10px] font-semibold">{{ $message }}</span> @enderror
            </div>

        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
                Simpan Perubahan
            </button>
        </div>
    </form>

</div>
@endsection
