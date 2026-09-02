<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $appSettings->site_name ?? 'Apartment Shuttle' }} — Admin panel">
    <title>@yield('title', 'Admin') — {{ $appSettings->site_name ?? 'Apartment Shuttle' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-light">
<div class="d-flex">
    <aside class="admin-sidebar text-white p-3 flex-shrink-0 d-flex flex-column">
        <div class="mb-3">
            @if(!empty($appSettings->logo_url))
                <img src="{{ $appSettings->logo_url }}" alt="{{ $appSettings->site_name }}" class="mb-2" style="max-height: 40px; max-width: 160px;">
            @else
                <h4 class="fw-bold mb-1"><i class="bi bi-bus-front me-2"></i>Shuttle</h4>
            @endif
            <small class="text-white-50 d-block">{{ $appSettings->site_name ?? 'Admin Panel' }}</small>
        </div>
        <hr class="border-light opacity-25 my-2">
        <nav class="nav flex-column flex-grow-1">
            @php $r = request()->route()?->getName(); @endphp
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ str_starts_with($r ?? '', 'admin.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
            <a href="{{ route('admin.reports.index') }}" class="nav-link {{ str_starts_with($r ?? '', 'admin.reports') ? 'active' : '' }}"><i class="bi bi-graph-up me-2"></i>Reports</a>
            <a href="{{ route('admin.bookings.index') }}" class="nav-link {{ str_starts_with($r ?? '', 'admin.bookings') ? 'active' : '' }}"><i class="bi bi-calendar-check me-2"></i>Bookings</a>
            <a href="{{ route('admin.bookings.calendar') }}" class="nav-link {{ $r === 'admin.bookings.calendar' ? 'active' : '' }}"><i class="bi bi-calendar3 me-2"></i>Calendar</a>
            <a href="{{ route('admin.subscriptions.index') }}" class="nav-link {{ str_starts_with($r ?? '', 'admin.subscriptions') ? 'active' : '' }}"><i class="bi bi-credit-card me-2"></i>Subscriptions</a>
            <a href="{{ route('admin.route-prices.index') }}" class="nav-link {{ str_starts_with($r ?? '', 'admin.route-prices') ? 'active' : '' }}"><i class="bi bi-currency-rupee me-2"></i>Route Prices</a>
            <a href="{{ route('admin.apartments.index') }}" class="nav-link {{ str_starts_with($r ?? '', 'admin.apartments') ? 'active' : '' }}"><i class="bi bi-building me-2"></i>Apartments</a>
            <a href="{{ route('admin.bus-stands.index') }}" class="nav-link {{ str_starts_with($r ?? '', 'admin.bus-stands') ? 'active' : '' }}"><i class="bi bi-signpost me-2"></i>Bus Stands</a>
            <a href="{{ route('admin.drivers.index') }}" class="nav-link {{ str_starts_with($r ?? '', 'admin.drivers') ? 'active' : '' }}"><i class="bi bi-person-badge me-2"></i>Drivers</a>
            <a href="{{ route('admin.vehicles.index') }}" class="nav-link {{ str_starts_with($r ?? '', 'admin.vehicles') ? 'active' : '' }}"><i class="bi bi-truck me-2"></i>Vehicles</a>
            <a href="{{ route('admin.time-slots.index') }}" class="nav-link {{ str_starts_with($r ?? '', 'admin.time-slots') ? 'active' : '' }}"><i class="bi bi-clock me-2"></i>Time Slots</a>
            <a href="{{ route('admin.subscription-enquiries.index') }}" class="nav-link {{ str_starts_with($r ?? '', 'admin.subscription-enquiries') ? 'active' : '' }}"><i class="bi bi-envelope-paper me-2"></i>Plan Enquiries</a>
            <a href="{{ route('admin.settings.edit') }}" class="nav-link {{ str_starts_with($r ?? '', 'admin.settings') ? 'active' : '' }}"><i class="bi bi-gear me-2"></i>Settings</a>
        </nav>
        <hr class="border-light opacity-25 my-2">
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="nav-link border-0 bg-transparent text-start w-100 text-white-50">
                <i class="bi bi-box-arrow-left me-2"></i>Logout
            </button>
        </form>
    </aside>
    <main class="flex-grow-1 p-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @yield('content')
    </main>
</div>
@stack('scripts')
</body>
</html>
