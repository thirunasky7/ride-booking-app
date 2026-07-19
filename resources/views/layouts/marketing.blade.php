<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('meta_description', 'Apartment Shuttle - Reliable shuttle rides between apartments and bus stands.')">
    <title>@yield('title', 'Apartment Shuttle')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg site-nav sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-success" href="{{ route('home') }}"><i class="bi bi-bus-front me-2"></i>Apartment Shuttle</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'fw-semibold text-success' : '' }}" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('marketing.about') ? 'fw-semibold text-success' : '' }}" href="{{ route('marketing.about') }}">About</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('marketing.services') ? 'fw-semibold text-success' : '' }}" href="{{ route('marketing.services') }}">Services</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('marketing.pricing') ? 'fw-semibold text-success' : '' }}" href="{{ route('marketing.pricing') }}">Pricing</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('marketing.contact') ? 'fw-semibold text-success' : '' }}" href="{{ route('marketing.contact') }}">Contact</a></li>
                @auth
                    @if(auth()->user()->role === 'customer')
                    <li class="nav-item"><a class="nav-link" href="{{ route('customer.dashboard') }}"><i class="bi bi-grid me-1"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('customer.bookRide') }}"><i class="bi bi-plus-circle me-1"></i>Book Ride</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('customer.myBookings') }}"><i class="bi bi-calendar-check me-1"></i>My Bookings</a></li>
                    @endif
                @else
                    <li class="nav-item ms-lg-2"><a href="{{ route('customer.login') }}" class="btn btn-brand btn-sm">Login</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
@yield('content')
<footer class="bg-dark text-white-50 py-4 mt-5">
    <div class="container d-flex flex-wrap justify-content-between gap-3">
        <span>&copy; {{ date('Y') }} Apartment Shuttle. All rights reserved.</span>
        <span><a href="{{ route('marketing.driver-register') }}" class="text-white-50">Become a Driver</a></span>
    </div>
</footer>
@stack('scripts')
</body>
</html>
