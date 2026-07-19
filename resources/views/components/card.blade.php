@props(['title' => null, 'subtitle' => null, 'padding' => true, 'class' => ''])

<div {{ $attributes->merge(['class' => 'card-modern '.($padding ? 'p-4 ' : '').$class]) }}>
    @if($title)
        <div class="mb-3">
            <h5 class="fw-semibold mb-0" style="color: var(--brand-text);">{{ $title }}</h5>
            @if($subtitle)<p class="text-muted small mb-0 mt-1">{{ $subtitle }}</p>@endif
        </div>
    @endif
    {{ $slot }}
</div>
