<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Ride Booking') — Apartment Shuttle</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    <style>
        :root {
            --brand-primary: #0f766e;
            --brand-primary-dark: #0d5c56;
            --brand-accent: #f59e0b;
            --brand-bg: #f0fdfa;
            --brand-surface: #ffffff;
            --brand-text: #134e4a;
            --brand-muted: #5f7a78;
            --brand-border: #ccfbf1;
            --brand-shadow: 0 4px 24px rgba(15, 118, 110, 0.08);
            --brand-radius: 16px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: linear-gradient(160deg, var(--brand-bg) 0%, #f8fafc 45%, #fff 100%);
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .site-nav {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--brand-border);
            box-shadow: 0 1px 12px rgba(15, 118, 110, 0.06);
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--brand-primary) !important;
            letter-spacing: -0.02em;
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--brand-primary), #14b8a6);
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.1rem;
        }

        .nav-link-custom {
            color: var(--brand-muted);
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .nav-link-custom:hover,
        .nav-link-custom.active {
            color: var(--brand-primary);
            background: var(--brand-bg);
        }

        .btn-brand {
            background: linear-gradient(135deg, var(--brand-primary), #14b8a6);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 0.6rem 1.25rem;
            border-radius: 10px;
            transition: transform 0.15s, box-shadow 0.15s;
        }

        .btn-brand:hover {
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(15, 118, 110, 0.3);
        }

        .btn-outline-brand {
            border: 2px solid var(--brand-primary);
            color: var(--brand-primary);
            font-weight: 600;
            border-radius: 10px;
            padding: 0.55rem 1.25rem;
        }

        .btn-outline-brand:hover {
            background: var(--brand-primary);
            color: #fff;
        }

        .card-modern {
            background: var(--brand-surface);
            border: 1px solid var(--brand-border);
            border-radius: var(--brand-radius);
            box-shadow: var(--brand-shadow);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(15, 118, 110, 0.12);
        }

        .page-content {
            flex: 1;
            padding: 2rem 0 3rem;
        }

        .site-footer {
            background: var(--brand-text);
            color: rgba(255, 255, 255, 0.75);
            padding: 1.5rem 0;
            font-size: 0.875rem;
            margin-top: auto;
        }

        .user-badge {
            background: var(--brand-bg);
            color: var(--brand-primary);
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
        }
    </style>

    @stack('styles')
</head>
<body>

<nav class="navbar navbar-expand-lg site-nav py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            <span class="brand-icon"><i class="bi bi-bus-front"></i></span>
            Apartment Shuttle
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item">
                    <a class="nav-link nav-link-custom {{ request()->routeIs('home') || request()->routeIs('marketing.*') ? 'active' : '' }}"
                       href="{{ route('home') }}">
                        <i class="bi bi-globe me-1"></i> Website
                    </a>
                </li>
                @auth
                    @if(auth()->user()->role === 'customer')
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}"
                           href="{{ route('customer.dashboard') }}">
                            <i class="bi bi-grid me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('customer.bookRide') ? 'active' : '' }}"
                           href="{{ route('customer.bookRide') }}">
                            <i class="bi bi-plus-circle me-1"></i> Book Ride
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('customer.myBookings') ? 'active' : '' }}"
                           href="{{ route('customer.myBookings') }}">
                            <i class="bi bi-calendar-check me-1"></i> My Bookings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('customer.preBookings') ? 'active' : '' }}"
                           href="{{ route('customer.preBookings') }}">
                            <i class="bi bi-calendar-event me-1"></i> Scheduled
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('customer.subscriptions') ? 'active' : '' }}"
                           href="{{ route('customer.subscriptions') }}">
                            <i class="bi bi-credit-card me-1"></i> Plans
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link nav-link-custom dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-info-circle me-1"></i> More
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><a class="dropdown-item" href="{{ route('marketing.about') }}">About</a></li>
                            <li><a class="dropdown-item" href="{{ route('marketing.services') }}">Services</a></li>
                            <li><a class="dropdown-item" href="{{ route('marketing.pricing') }}">Pricing</a></li>
                            <li><a class="dropdown-item" href="{{ route('marketing.contact') }}">Contact</a></li>
                        </ul>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <span class="user-badge">
                            <i class="bi bi-phone me-1"></i>{{ auth()->user()->mobile }}
                        </span>
                    </li>
                    @endif
                @else
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="{{ route('marketing.about') }}">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="{{ route('marketing.pricing') }}">Pricing</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-brand btn-sm" href="{{ route('customer.login') }}">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<main class="page-content">
    <div class="container">
        @yield('content')
    </div>
</main>

<footer class="site-footer">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
        <span><i class="bi bi-bus-front me-1"></i> Apartment Shuttle &mdash; Reliable rides between your home and the bus stand.</span>
        <span>&copy; {{ date('Y') }} All rights reserved.</span>
    </div>
</footer>

@stack('scripts')
</body>
</html>
