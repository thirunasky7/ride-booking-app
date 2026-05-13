@extends('admin.layout')

@section('content')

<div class="container-fluid">

    <h3 class="mb-4">
        Admin Dashboard
    </h3>

    <div class="row">

        <div class="col-md-3 mb-3">

            <div class="card shadow-sm border-0">

                <div class="card-body">

                    <h6>Total Bookings</h6>

                    <h2>
                        {{ $totalBookings }}
                    </h2>

                </div>

            </div>

        </div>

        <div class="col-md-3 mb-3">

            <div class="card shadow-sm border-0">

                <div class="card-body">

                    <h6>Today Bookings</h6>

                    <h2>
                        {{ $todayBookings }}
                    </h2>

                </div>

            </div>

        </div>

        <div class="col-md-3 mb-3">

            <div class="card shadow-sm border-0">

                <div class="card-body">

                    <h6>Completed Trips</h6>

                    <h2>
                        {{ $completedTrips }}
                    </h2>

                </div>

            </div>

        </div>

        <div class="col-md-3 mb-3">

            <div class="card shadow-sm border-0">

                <div class="card-body">

                    <h6>Cancelled Trips</h6>

                    <h2>
                        {{ $cancelledTrips }}
                    </h2>

                </div>

            </div>

        </div>

        <div class="col-md-3 mb-3">

            <div class="card shadow-sm border-0">

                <div class="card-body">

                    <h6>Total Earnings</h6>

                    <h2>
                        ₹{{ number_format($totalEarnings,2) }}
                    </h2>

                </div>

            </div>

        </div>

        <div class="col-md-3 mb-3">

            <div class="card shadow-sm border-0">

                <div class="card-body">

                    <h6>Today Earnings</h6>

                    <h2>
                        ₹{{ number_format($todayEarnings,2) }}
                    </h2>

                </div>

            </div>

        </div>

        <div class="col-md-3 mb-3">

            <div class="card shadow-sm border-0">

                <div class="card-body">

                    <h6>Online Drivers</h6>

                    <h2>
                        {{ $onlineDrivers }}
                    </h2>

                </div>

            </div>

        </div>

        <div class="col-md-3 mb-3">

            <div class="card shadow-sm border-0">

                <div class="card-body">

                    <h6>Total Vehicles</h6>

                    <h2>
                        {{ $totalVehicles }}
                    </h2>

                </div>

            </div>

        </div>

    </div>

    <div class="row mt-4">

        <div class="col-md-6">

            <div class="card">

                <div class="card-header">
                    Monthly Revenue
                </div>

                <div class="card-body">

                    <canvas id="revenueChart"></canvas>

                </div>

            </div>

        </div>

        <div class="col-md-6">

            <div class="card">

                <div class="card-header">
                    Monthly Bookings
                </div>

                <div class="card-body">

                    <canvas id="bookingChart"></canvas>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const revenueCtx =
document.getElementById('revenueChart');

new Chart(revenueCtx, {

    type: 'bar',

    data: {

        labels: {!! json_encode(
            $monthlyRevenue->keys()
        ) !!},

        datasets: [{

            label: 'Revenue',

            data: {!! json_encode(
                $monthlyRevenue->values()
            ) !!},

            borderWidth: 1

        }]
    }
});

const bookingCtx =
document.getElementById('bookingChart');

new Chart(bookingCtx, {

    type: 'line',

    data: {

        labels: {!! json_encode(
            $monthlyBookings->keys()
        ) !!},

        datasets: [{

            label: 'Bookings',

            data: {!! json_encode(
                $monthlyBookings->values()
            ) !!},

            borderWidth: 2

        }]
    }
});

</script>

@endsection