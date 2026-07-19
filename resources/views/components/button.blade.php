@props([
    'type' => 'button',
    'variant' => 'brand',
    'size' => '',
    'icon' => null,
    'href' => null,
])

@php
    $classes = match($variant) {
        'brand' => 'btn btn-brand',
        'outline' => 'btn btn-outline-secondary',
        'danger' => 'btn btn-outline-danger',
        'success' => 'btn btn-success',
        'warning' => 'btn btn-warning',
        'light' => 'btn btn-light',
        default => 'btn btn-'.$variant,
    };
    if ($size) $classes .= ' btn-'.$size;
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)<i class="bi bi-{{ $icon }} me-1"></i>@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)<i class="bi bi-{{ $icon }} me-1"></i>@endif
        {{ $slot }}
    </button>
@endif
