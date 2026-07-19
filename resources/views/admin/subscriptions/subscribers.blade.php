@extends('admin.layout')

@section('title', 'Subscribers')

@section('content')
<div class="page-header"><h2 class="mb-0 fw-bold">Active Subscribers</h2></div>
<div class="card-modern p-0 overflow-hidden">
    <table class="table table-hover mb-0">
        <thead class="table-light"><tr><th>Customer</th><th>Plan</th><th>Start</th><th>End</th><th>Remaining</th><th>Status</th></tr></thead>
        <tbody>
            @foreach($subscribers as $sub)
            <tr>
                <td>{{ $sub->user->mobile }}</td>
                <td>{{ $sub->subscription->name }}</td>
                <td>{{ $sub->start_date->format('d M Y') }}</td>
                <td>{{ $sub->end_date->format('d M Y') }}</td>
                <td>{{ $sub->remaining_rides ?? '∞' }}</td>
                <td><span class="badge bg-{{ $sub->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($sub->status) }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{ $subscribers->links() }}
@endsection
