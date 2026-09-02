<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login — {{ $appSettings->site_name ?? 'Apartment Shuttle' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body class="bg-light d-flex align-items-center min-vh-100">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="text-center mb-4">
                @if(!empty($appSettings->logo_url))
                    <img src="{{ $appSettings->logo_url }}" alt="{{ $appSettings->site_name }}" class="mb-3" style="max-height: 64px; max-width: 200px;">
                @else
                    <div class="display-6 text-primary"><i class="bi bi-bus-front"></i></div>
                @endif
                <h4 class="fw-bold mb-1">{{ $appSettings->site_name ?? 'Apartment Shuttle' }}</h4>
                <p class="text-muted small mb-0">Admin Panel</p>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger small py-2">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ route('admin.login.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Sign in
                        </button>
                    </form>
                </div>
            </div>

            <p class="text-center text-muted small mt-3 mb-0">
                <a href="{{ route('home') }}" class="text-decoration-none">← Back to website</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>
