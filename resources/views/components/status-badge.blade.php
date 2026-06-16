@props(['status'])

@php
    $labels = [
        'scheduled' => 'Agendado',
        'completed' => 'Concluído',
    ];

    $classes = [
        'scheduled' => 'border-act-primary-light bg-act-primary-light text-act-primary-dark',
        'completed' => 'border-act-accent/30 bg-act-accent/10 text-act-accent',
    ][$status] ?? 'border-act-line bg-act-bg text-act-muted';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold '.$classes]) }}>
    {{ $labels[$status] ?? ucfirst((string) $status) }}
</span>
