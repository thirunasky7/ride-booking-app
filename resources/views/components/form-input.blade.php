@props([
    'label',
    'name',
    'type' => 'text',
    'value' => null,
    'required' => false,
    'placeholder' => ' ',
    'options' => [],
])

@php
    $id = $attributes->get('id', $name);
    $hasError = $errors->has($name);
@endphp

<div class="form-floating mb-3">
    @if($type === 'select')
        <select name="{{ $name }}" id="{{ $id }}" @if($required) required @endif
                {{ $attributes->merge(['class' => 'form-select'.($hasError ? ' is-invalid' : '')]) }}>
            {{ $slot }}
            @foreach($options as $optValue => $optLabel)
                <option value="{{ $optValue }}" @selected(old($name, $value) == $optValue)>{{ $optLabel }}</option>
            @endforeach
        </select>
    @elseif($type === 'textarea')
        <textarea name="{{ $name }}" id="{{ $id }}" placeholder="{{ $placeholder }}"
                  @if($required) required @endif
                  {{ $attributes->merge(['class' => 'form-control'.($hasError ? ' is-invalid' : ''), 'style' => 'min-height:100px']) }}>{{ old($name, $value) }}</textarea>
    @else
        <input type="{{ $type }}" name="{{ $name }}" id="{{ $id }}"
               value="{{ old($name, $value) }}" placeholder="{{ $placeholder }}"
               @if($required) required @endif
               {{ $attributes->merge(['class' => 'form-control'.($hasError ? ' is-invalid' : '')]) }}>
    @endif
    <label for="{{ $id }}">{{ $label }}@if($required)<span class="text-danger">*</span>@endif</label>
    @error($name)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
