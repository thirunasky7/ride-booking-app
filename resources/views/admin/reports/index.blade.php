@extends('admin.layout')

@section('title', 'Reports')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold mb-1">Reports</h3>
        <p class="text-muted mb-0">Revenue, bookings, payments &amp; platform metrics</p>
    </div>
    <form method="GET" class="d-flex flex-wrap gap-2 align-items-end">
        <div>
            <label class="form-label small mb-1">From</label>
            <input type="date" name="from" class="form-control form-control-sm" value="{{ $from }}">
        </div>
        <div>
            <label class="form-label small mb-1">To</label>
            <input type="date" name="to" class="form-control form-control-sm" value="{{ $to }}">
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Apply</button>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
    </form>
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['Total Bookings', $summary['total_bookings'], 'bi-calendar-check', 'primary'],
        ['Completed', $summary['completed_trips'], 'bi-check-circle', 'success'],
        ['Cancelled', $summary['cancelled_trips'], 'bi-x-circle', 'danger'],
        ['Pending', $summary['pending_trips'], 'bi-hourglass', 'warning'],
        ['Revenue', '₹'.number_format($summary['total_revenue'], 0), 'bi-currency-rupee', 'success'],
        ['Commission', '₹'.number_format($summary['total_commission'], 0), 'bi-percent', 'info'],
        ['Driver Payouts', '₹'.number_format($summary['driver_payouts'], 0), 'bi-wallet2', 'secondary'],
        ['Razorpay', '₹'.number_format($summary['razorpay_collected'], 0), 'bi-credit-card', 'primary'],
        ['Cash', '₹'.number_format($summary['cash_collected'], 0), 'bi-cash', 'dark'],
        ['UPI', '₹'.number_format($summary['upi_collected'], 0), 'bi-phone', 'info'],
        ['Pending Payments', $summary['pending_payments'].' (₹'.number_format($summary['pending_payment_amount'], 0).')', 'bi-exclamation-circle', 'warning'],
        ['New Customers', $summary['new_customers'].' / '.$summary['total_customers'], 'bi-people', 'success'],
        ['Active Subscriptions', $summary['active_subscriptions'], 'bi-credit-card-2-front', 'primary'],
        ['Plan Enquiries', $summary['subscription_enquiries'], 'bi-envelope-paper', 'secondary'],
        ['Online Drivers', $summary['online_drivers'], 'bi-person-check', 'success'],
        ['Vehicles', $summary['total_vehicles'], 'bi-truck', 'dark'],
    ] as [$label, $value, $icon, $color])
    <div class="col-xl-3 col-md-4 col-sm-6">
        <div class="stat-card p-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small">{{ $label }}</div>
                    <div class="fs-5 fw-bold">{{ $value }}</div>
                </div>
                <i class="bi {{ $icon }} text-{{ $color }} fs-5"></i>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">Daily Revenue</div>
            <div class="card-body">
                <canvas id="dailyRevenueChart" height="120"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">Bookings by Status</div>
            <div class="card-body">
                <canvas id="statusChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">12-Month Trend</div>
            <div class="card-body">
                <canvas id="monthlyTrendChart" height="140"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">Payments by Method</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Method</th>
                                <th class="text-end">Count</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paymentsByMethod as $row)
                            <tr>
                                <td class="text-capitalize">{{ $row->method }}</td>
                                <td class="text-end">{{ $row->count }}</td>
                                <td class="text-end">₹{{ number_format($row->amount, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">No paid bookings in this period</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Top Routes</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Route</th>
                                <th class="text-end">Trips</th>
                                <th class="text-end">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topRoutes as $route)
                            <tr>
                                <td>
                                    {{ $route->apartment?->name ?? '—' }}
                                    <i class="bi bi-arrow-right mx-1 text-muted"></i>
                                    {{ $route->busStand?->name ?? '—' }}
                                </td>
                                <td class="text-end">{{ $route->trip_count }}</td>
                                <td class="text-end">₹{{ number_format($route->revenue, 0) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">No route data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Recent Payments</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments as $payment)
                            <tr>
                                <td>{{ $payment->customer?->name ?? $payment->customer?->mobile ?? '—' }}</td>
                                <td>₹{{ number_format($payment->price, 0) }}</td>
                                <td class="text-capitalize">{{ $payment->payment_method ?? '—' }}</td>
                                <td class="small text-muted">{{ $payment->paid_at?->format('d M Y') ?? $payment->created_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No payments in this period</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const dailyLabels = {!! json_encode($dailyRevenue->pluck('date')) !!};
const dailyData = {!! json_encode($dailyRevenue->pluck('revenue')) !!};
const statusLabels = {!! json_encode($bookingsByStatus->keys()) !!};
const statusData = {!! json_encode($bookingsByStatus->values()) !!};
const monthLabels = {!! json_encode($monthlyTrend->pluck('month')) !!};
const monthRevenue = {!! json_encode($monthlyTrend->pluck('revenue')) !!};
const monthBookings = {!! json_encode($monthlyTrend->pluck('bookings')) !!};

new Chart(document.getElementById('dailyRevenueChart'), {
    type: 'bar',
    data: {
        labels: dailyLabels,
        datasets: [{ label: 'Revenue (₹)', data: dailyData, backgroundColor: '#0f766e' }]
    },
    options: { scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: statusLabels,
        datasets: [{ data: statusData, backgroundColor: ['#0f766e', '#14b8a6', '#f59e0b', '#ef4444', '#64748b', '#8b5cf6'] }]
    }
});

new Chart(document.getElementById('monthlyTrendChart'), {
    type: 'line',
    data: {
        labels: monthLabels,
        datasets: [
            { label: 'Revenue (₹)', data: monthRevenue, borderColor: '#0f766e', tension: 0.3, yAxisID: 'y' },
            { label: 'Bookings', data: monthBookings, borderColor: '#f59e0b', tension: 0.3, yAxisID: 'y1' }
        ]
    },
    options: {
        scales: {
            y: { beginAtZero: true, position: 'left' },
            y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false } }
        }
    }
});
</script>
@endpush
