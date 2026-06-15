@props(['status'])

@php
    $labels = [
        'scheduled' => 'Agendado',
        'completed' => 'Concluido',
    ];

    $classes = [
        'scheduled' => 'border-sky-200 bg-sky-50 text-sky-700',
        'completed' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
    ][$status] ?? 'border-zinc-200 bg-zinc-50 text-zinc-700';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold '.$classes]) }}>
    {{ $labels[$status] ?? ucfirst((string) $status) }}
</span>
