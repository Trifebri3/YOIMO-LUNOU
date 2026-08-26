<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-emerald-700 leading-tight">
            {{ __('Finance & Billing Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900">Halo, {{ Auth::user()->name }} (Finance)</h3>
                <p class="mt-2 text-sm text-gray-600">Akses arus kas, verifikasi pembayaran, faktur, dan rekapitulasi keuangan.</p>
            </div>
        </div>
    </div>
</x-app-layout>
