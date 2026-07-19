@props(['headers' => [], 'empty' => 'No records found.', 'hover' => true])

<div class="table-responsive card-modern p-0 overflow-hidden">
    <table {{ $attributes->merge(['class' => 'table table-modern mb-0'.($hover ? ' table-hover' : '')]) }}>
        @if(count($headers))
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th class="text-muted small text-uppercase">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        @endif
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
    @isset($pagination)
        <div class="p-3 border-top">{{ $pagination }}</div>
    @endisset
</div>