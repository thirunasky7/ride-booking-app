@props(['title', 'action' => null, 'actionLabel' => 'Add New', 'actionIcon' => 'plus-lg'])

<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <h2 class="mb-0 fw-bold">{{ $title }}</h2>
        @if(isset($subtitle))<p class="mb-0 opacity-75 small">{{ $subtitle }}</p>@endif
    </div>
    @if($action)
        <x-button :href="$action" variant="light" :icon="$actionIcon">{{ $actionLabel }}</x-button>
    @endif
</div>
