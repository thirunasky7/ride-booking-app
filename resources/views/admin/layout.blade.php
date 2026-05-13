<!DOCTYPE html>
<html>
<head>
    <title>Apartment Shuttle</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="d-flex">

    <div class="bg-dark text-white p-3" style="width:250px;height:100vh;">

        <h4>Admin Panel</h4>

        <hr>

        <ul class="nav flex-column">

    <li class="nav-item mb-2">
        <a href="{{ route('admin.dashboard') }}"
           class="nav-link text-white">
            Dashboard
        </a>
    </li>

    <li class="nav-item mb-2">
        <a href="{{ route('apartments.index') }}"
           class="nav-link text-white">
            Apartments
        </a>
    </li>

    <li class="nav-item mb-2">
        <a href="{{ route('bus-stands.index') }}"
           class="nav-link text-white">
            Bus Stands
        </a>
    </li>

    <li class="nav-item mb-2">
        <a href="{{ route('drivers.index') }}"
           class="nav-link text-white">
            Drivers
        </a>
    </li>

    <li class="nav-item mb-2">
        <a href="{{ route('vehicles.index') }}"
           class="nav-link text-white">
            Vehicles
        </a>
    </li>

    <li class="nav-item mb-2">
        <a href="{{ route('time-slots.index') }}"
           class="nav-link text-white">
            Time Slots
        </a>
    </li>

</ul>

    </div>

    <div class="p-4 w-100">

        @yield('content')

    </div>

</div>

</body>
</html>