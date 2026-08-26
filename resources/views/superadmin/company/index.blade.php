@extends('superadmin.layouts.app')

@section('title', 'Daftar Perusahaan')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Header Section -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Daftar Portofolio Perusahaan</h1>
            <p class="text-xs text-slate-400 mt-1">Kelola daftar profil perusahaan dan tugaskan role Management sebagai penanggung jawab.</p>
        </div>
        <a href="{{ route('superadmin.company.create') }}" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Perusahaan Baru
        </a>
    </div>

    <!-- Table Card -->
    <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/60 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-6">Perusahaan</th>
                        <th class="py-4 px-6">Pengelola (Management PIC)</th>
                        <th class="py-4 px-6">Kontak & Email</th>
                        <th class="py-4 px-6">Blok Konten</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-600">
                    @forelse($companies as $company)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    @if($company->logo)
                                        <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->company_name }}" class="w-10 h-10 rounded-2xl object-contain p-1 border border-slate-200">
                                    @else
                                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 font-bold text-xs flex items-center justify-center">
                                            CO
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $company->company_name }}</div>
                                        <div class="text-[11px] text-slate-400 font-medium">{{ $company->tagline ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-4 px-6">
                                @if($company->manager)
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                        <span class="font-bold text-indigo-700">{{ $company->manager->name }}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-400">{{ $company->manager->email }}</div>
                                @else
                                    <span class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200 rounded-lg text-[10px] font-bold">
                                        Belum Ditugaskan
                                    </span>
                                @endif
                            </td>

                            <td class="py-4 px-6">
                                <div class="font-medium text-slate-800">{{ $company->email ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $company->phone ?? '-' }}</div>
                            </td>

                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg text-[10px] font-bold">
                                    {{ count($company->dynamic_sections ?? []) }} Blok Terpasang
                                </span>
                            </td>

                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Show -->
                                    <a href="{{ route('superadmin.company.show', $company->id) }}" class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <!-- Edit -->
                                    <a href="{{ route('superadmin.company.edit', $company->id) }}" class="p-2 text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition-all" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <!-- Delete -->
                                    <form method="POST" action="{{ route('superadmin.company.destroy', $company->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus profil perusahaan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">Belum ada perusahaan yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $companies->links() }}
        </div>
    </div>

</div>
@endsection