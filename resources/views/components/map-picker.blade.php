@props([
    'prefix' => 'pickup',
    'label' => 'Select Location',
    'lat' => null,
    'lng' => null,
    'address' => null,
])

<div class="map-picker mb-3" data-prefix="{{ $prefix }}">
    <label class="form-label fw-medium">{{ $label }}</label>
    <input type="text" class="form-control map-search mb-2" placeholder="Search address..."
           id="{{ $prefix }}_search" value="{{ $address }}">
    <div class="map-canvas rounded-3 border" id="{{ $prefix }}_map" style="height:220px;background:#e2e8f0;"></div>
    <input type="hidden" name="{{ $prefix }}_address" id="{{ $prefix }}_address" value="{{ $address }}">
    <input type="hidden" name="{{ $prefix }}_lat" id="{{ $prefix }}_lat" value="{{ $lat }}">
    <input type="hidden" name="{{ $prefix }}_lng" id="{{ $prefix }}_lng" value="{{ $lng }}">
    <small class="text-muted d-block mt-1"><i class="bi bi-cursor me-1"></i>Click map or search to set location</small>
</div>
