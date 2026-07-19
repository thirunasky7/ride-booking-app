<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Apartment Shuttle - Premium bike taxi and shuttle booking SaaS platform.">
    <title>@yield('title', 'Admin') — Apartment Shuttle</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-light">
<div class="d-flex">
    <aside class="admin-sidebar text-white p-3 flex-shrink-0">
        <h4 class="fw-bold mb-1"><i class="bi bi-bus-front me-2"></i>Shuttle</h4>
        <small class="text-white-50">Admin Panel</small>
        <hr class="border-light opacity-25 my-3">
        <nav class="nav flex-column">
            @php $r = request()->route()?->getName(); @endphp
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ str_starts_with($r ?? '', 'admin.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
            <a href="{{ route('bookings.index') }}" class="nav-link {{ str_starts_with($r ?? '', 'bookings.') ? 'active' : '' }}"><i class="bi bi-calendar-check me-2"></i>Bookings</a>
            <a href="{{ route('bookings.calendar') }}" class="nav-link {{ $r === 'bookings.calendar' ? 'active' : '' }}"><i class="bi bi-calendar3 me-2"></i>Calendar</a>
            <a href="{{ route('subscriptions.index') }}" class="nav-link {{ str_starts_with($r ?? '', 'subscriptions.') ? 'active' : '' }}"><i class="bi bi-credit-card me-2"></i>Subscriptions</a>
            <a href="{{ route('route-prices.index') }}" class="nav-link {{ str_starts_with($r ?? '', 'route-prices.') ? 'active' : '' }}"><i class="bi bi-currency-rupee me-2"></i>Route Prices</a>
            <a href="{{ route('apartments.index') }}" class="nav-link {{ str_starts_with($r ?? '', 'apartments.') ? 'active' : '' }}"><i class="bi bi-building me-2"></i>Apartments</a>
            <a href="{{ route('bus-stands.index') }}" class="nav-link {{ str_starts_with($r ?? '', 'bus-stands.') ? 'active' : '' }}"><i class="bi bi-signpost me-2"></i>Bus Stands</a>
            <a href="{{ route('drivers.index') }}" class="nav-link {{ str_starts_with($r ?? '', 'drivers.') ? 'active' : '' }}"><i class="bi bi-person-badge me-2"></i>Drivers</a>
            <a href="{{ route('vehicles.index') }}" class="nav-link {{ str_starts_with($r ?? '', 'vehicles.') ? 'active' : '' }}"><i class="bi bi-truck me-2"></i>Vehicles</a>
            <a href="{{ route('time-slots.index') }}" class="nav-link {{ str_starts_with($r ?? '', 'time-slots.') ? 'active' : '' }}"><i class="bi bi-clock me-2"></i>Time Slots</a>
        </nav>
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