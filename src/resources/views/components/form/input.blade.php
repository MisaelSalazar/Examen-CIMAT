@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'required' => false,
    'hint' => null,
    'step' => null,
    'min' => null,
    'max' => null,
])

@php
    $id = $attributes->get('id', $name);
    $hasError = $errors->has($name);
    $describedBy = $hasError ? $id.'-error' : ($hint ? $id.'-hint' : null);
@endphp

<div class="space-y-1.5">
    <label for="{{ $id }}" class="block text-sm font-medium text-slate-700">
        {{ $label }}@if ($required)<span class="ml-0.5 text-rose-600" aria-hidden="true">*</span>@endif
    </label>

    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @if ($value !== null) value="{{ $value }}" @endif
        @required($required)
        @if ($step !== null) step="{{ $step }}" @endif
        @if ($min !== null) min="{{ $min }}" @endif
        @if ($max !== null) max="{{ $max }}" @endif
        @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
        @if ($hasError) aria-invalid="true" @endif
        {{ $attributes->except(['id', 'class'])->class([
            'block w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition placeholder:text-slate-400 focus:outline-none focus:ring-2',
            'border-rose-400 focus:border-rose-500 focus:ring-rose-200' => $hasError,
            'border-slate-300 focus:border-indigo-500 focus:ring-indigo-200' => ! $hasError,
        ]) }}
    >

    @if ($hasError)
        <p id="{{ $id }}-error" class="text-sm text-rose-600">{{ $errors->first($name) }}</p>
    @elseif ($hint)
        <p id="{{ $id }}-hint" class="text-xs text-slate-500">{{ $hint }}</p>
    @endif
</div>
