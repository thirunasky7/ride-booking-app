@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <h2 class="mb-0 fw-bold">Dashboard</h2>
    <p class="mb-0 opacity-75">Platform overview &amp; earnings</p>
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['Total Bookings', $totalBookings, 'bi-calendar-check', 'primary'],
        ['Today', $todayBookings, 'bi-sun', 'info'],
        ['Completed', $completedTrips, 'bi-check-circle', 'success'],
        ['Cancelled', $cancelledTrips, 'bi-x-circle', 'danger'],
        ['Revenue', '₹'.number_format($totalEarnings, 0), 'bi-currency-rupee', 'success'],
        ['Commission', '₹'.number_format($totalCommission, 0), 'bi-percent', 'warning'],
        ['Driver Payout', '₹'.number_format($driverEarnings, 0), 'bi-wallet2', 'secondary'],
        ['Today Revenue', '₹'.number_format($todayEarnings, 0), 'bi-graph-up', 'primary'],
        ['Online Drivers', $onlineDrivers, 'bi-person-check', 'success'],
        ['Vehicles', $totalVehicles, 'bi-truck', 'dark'],
    ] as [$label, $value, $icon, $color])
    <div class="col-md-3 col-sm-6">
        <div class="stat-card p-3 bg-white">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small">{{ $label }}</div>
                    <div class="fs-4 fw-bold">{{ $value }}</div>
                </div>
                <i class="bi {{ $icon }} text-{{ $color }} fs-4"></i>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card-modern p-3">
            <h6 class="fw-semibold mb-3">Monthly Revenue</h6>
            <canvas id="revenueChart" height="200"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card-modern p-3">
            <h6 class="fw-semibold mb-3">Monthly Bookings</h6>
            <canvas id="bookingChart" height="200"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($monthlyRevenue->keys()) !!},
        datasets: [{ label: 'Revenue (₹)', data: {!! json_encode($monthlyRevenue->values()) !!}, backgroundColor: '#0f766e' }]
    }
});
new Chart(document.getElementById('bookingChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($monthlyBookings->keys()) !!},
        datasets: [{ label: 'Bookings', data: {!! json_encode($monthlyBookings->values()) !!}, borderColor: '#14b8a6', tension: 0.3 }]
    }
});
</script>
@endpush
