@extends('superadmin.layouts.app')

@section('title', 'Detail Profil - ' . $user->name)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header Actions -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('superadmin.users.index') }}" class="p-2 border border-slate-200 text-slate-500 hover:bg-slate-50 rounded-xl transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">Detail Profil Pengguna</h1>
                <p class="text-xs text-slate-400">Informasi kredensial lengkap, status WhatsApp Fonnte, dan hak akses.</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('superadmin.users.edit', $user->id) }}" class="px-4 py-2.5 bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100/60 text-xs font-bold rounded-xl transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit Akun
            </a>
        </div>
    </div>

    <!-- Main Profile Card -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

        <!-- Kolom Kiri: Avatar & Badges -->
        <div class="md:col-span-4 bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col items-center text-center">
            <div class="relative mb-4">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-28 h-28 rounded-3xl object-cover ring-4 ring-emerald-50 shadow-md">
                @else
                    <div class="w-28 h-28 rounded-3xl bg-slate-900 text-white font-black text-2xl flex items-center justify-center shadow-md">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                @endif
                <span class="absolute bottom-1 right-1 w-4 h-4 rounded-full bg-emerald-500 ring-2 ring-white"></span>
            </div>

            <h2 class="text-lg font-bold text-slate-900">{{ $user->name }}</h2>
            <p class="text-xs font-semibold text-emerald-600 mb-4">{{ $user->position ?? 'Posisi Belum Diset' }}</p>

            @php
                $roleBadge = match ($user->role) {
                    'superadmin' => 'bg-rose-50 text-rose-700 border-rose-200',
                    'management' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                    'finance'    => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    default      => 'bg-slate-100 text-slate-700 border-slate-200',
                };
            @endphp
            <span class="px-3.5 py-1 border rounded-full text-[10px] font-black uppercase tracking-wider {{ $roleBadge }} mb-6">
                {{ $user->role }}
            </span>

            <!-- WhatsApp Direct Action Button -->
            @if($user->phone)
                <a href="https://wa.me/{{ $user->phone }}" target="_blank" class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                    Kirim Pesan WhatsApp
                </a>
            @endif
        </div>

        <!-- Kolom Kanan: Rincian Lengkap & Bio -->
        <div class="md:col-span-8 bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Informasi Kredensial & Kontak</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <span class="text-slate-400 font-medium block mb-1">Email Resmi</span>
                    <span class="font-bold text-slate-800">{{ $user->email }}</span>
                </div>

                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <span class="text-slate-400 font-medium block mb-1">Nomor WhatsApp (Fonnte)</span>
                    <span class="font-bold text-slate-800">{{ $user->phone ? '+' . $user->phone : '-' }}</span>
                </div>

                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <span class="text-slate-400 font-medium block mb-1">Status Terdaftar</span>
                    <span class="font-bold text-slate-800">{{ $user->created_at->format('d F Y, H:i') }} WIB</span>
                </div>

                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <span class="text-slate-400 font-medium block mb-1">Terakhir Diperbarui</span>
                    <span class="font-bold text-slate-800">{{ $user->updated_at->format('d F Y, H:i') }} WIB</span>
                </div>
            </div>

            <!-- Bio / Catatan Box -->
            <div class="pt-2">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Bio / Catatan Khusus</h3>
                <div class="p-5 bg-slate-50/70 border border-slate-100 rounded-2xl text-xs text-slate-600 leading-relaxed">
                    {{ $user->bio ?? 'Tidak ada catatan atau bio yang ditambahkan untuk pengguna ini.' }}
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
