@php
    $dashboardRoute = match (Auth::user()->role) {
        'superadmin' => 'superadmin.dashboard',
        'management' => 'management.dashboard',
        'finance'    => 'finance.dashboard',
        default      => 'user.dashboard',
    };
@endphp

<!-- Navigation Links -->
<div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
    <x-nav-link :href="route($dashboardRoute)" :active="request()->routeIs($dashboardRoute)">
        {{ __('Dashboard') }}
    </x-nav-link>

    {{-- Menu Khusus Superadmin --}}
    @if(Auth::user()->isSuperAdmin())
        <x-nav-link :href="route('superadmin.dashboard')" :active="request()->routeIs('superadmin.*')">
            {{ __('Admin Panel') }}
        </x-nav-link>
    @endif

    {{-- Menu Khusus Finance --}}
    @if(Auth::user()->isFinance() || Auth::user()->isSuperAdmin())
        <x-nav-link :href="route('finance.dashboard')" :active="request()->routeIs('finance.*')">
            {{ __('Keuangan') }}
        </x-nav-link>
    @endif
</div>
