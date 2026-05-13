@extends('admin.layout')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">

    <h3>Time Slots</h3>

    <a href="{{ route('time-slots.create') }}"
       class="btn btn-primary">
        Add Time Slot
    </a>

</div>

@if(session('success'))

<div class="alert alert-success">
    {{ session('success') }}
</div>

@endif

<table class="table table-bordered table-striped">

    <thead>

        <tr>

            <th>ID</th>

            <th>Slot Time</th>

            <th>Status</th>

            <th width="180">Action</th>

        </tr>

    </thead>

    <tbody>

        @forelse($timeSlots as $slot)

        <tr>

            <td>{{ $slot->id }}</td>

            <td>
                {{ \Carbon\Carbon::parse($slot->slot_time)->format('h:i A') }}
            </td>

            <td>

                @if($slot->status)

                    <span class="badge bg-success">
                        Active
                    </span>

                @else

                    <span class="badge bg-danger">
                        Inactive
                    </span>

                @endif

            </td>

            <td>

                <a href="{{ route('time-slots.edit',$slot->id) }}"
                   class="btn btn-warning btn-sm">
                    Edit
                </a>

                <form action="{{ route('time-slots.destroy',$slot->id) }}"
                      method="POST"
                      class="d-inline">

                    @csrf
                    @method('DELETE')

                    <button class="btn btn-danger btn-sm"
                            onclick="return confirm('Delete Time Slot?')">

                        Delete

                    </button>

                </form>

            </td>

        </tr>

        @empty

        <tr>

            <td colspan="4" class="text-center">
                No Time Slots Found
            </td>

        </tr>

        @endforelse

    </tbody>

</table>

{{ $timeSlots->links() }}

@endsection