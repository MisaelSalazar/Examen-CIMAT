@props([
    'etiqueta',
    'valor',
    'maximo' => 10,
])

@php
    $porcentaje = $maximo > 0 ? max(0, min(100, ($valor / $maximo) * 100)) : 0;
    $color = match (true) {
        $valor >= 8 => 'bg-emerald-500',
        $valor >= 6 => 'bg-amber-500',
        default => 'bg-rose-500',
    };
@endphp

<div>
    <div class="flex items-baseline justify-between gap-3">
        <span class="text-sm text-slate-600">{{ $etiqueta }}</span>
        <span class="text-sm font-semibold tabular-nums text-slate-900">{{ number_format((float) $valor, 2) }}<span class="text-xs font-normal text-slate-400"> / {{ $maximo }}</span></span>
    </div>
    <div class="mt-1.5 h-2 w-full overflow-hidden rounded-full bg-slate-100"
         role="progressbar"
         aria-valuenow="{{ $valor }}"
         aria-valuemin="0"
         aria-valuemax="{{ $maximo }}"
         aria-label="{{ $etiqueta }}">
        <div class="h-full rounded-full {{ $color }}" style="width: {{ $porcentaje }}%"></div>
    </div>
</div>
