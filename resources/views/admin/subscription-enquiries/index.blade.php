@extends('admin.layout')

@section('title', 'Subscription Enquiries')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Monthly Plan Enquiries</h3>
        <p class="text-muted mb-0">Customers interested in monthly subscription plans</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Plan</th>
                    <th>Start Date</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($enquiries as $enquiry)
                <tr>
                    <td class="small text-muted">{{ $enquiry->created_at->format('d M Y') }}</td>
                    <td class="fw-semibold">{{ $enquiry->name }}</td>
                    <td>{{ $enquiry->mobile }}</td>
                    <td>{{ $enquiry->subscription?->name ?? 'Any plan' }}</td>
                    <td>{{ $enquiry->preferred_start_date?->format('d M Y') ?? '—' }}</td>
                    <td>
                        @php
                            $colors = ['pending' => 'warning', 'contacted' => 'info', 'closed' => 'success'];
                        @endphp
                        <span class="badge bg-{{ $colors[$enquiry->status] ?? 'secondary' }}">{{ ucfirst($enquiry->status) }}</span>
                    </td>
                    <td>
                        <a href="{{ route('subscription-enquiries.show', $enquiry) }}" class="btn btn-sm btn-outline-primary">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">No enquiries yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $enquiries->links() }}</div>

@endsection
