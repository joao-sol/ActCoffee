@extends('layouts.public')

@section('title', 'Act Coffee')

@section('content')
<section class="relative isolate overflow-hidden border-b border-act-line bg-act-neutral">
    <img class="absolute inset-0 -z-20 h-full w-full object-cover" src="{{ asset('images/coffee-station.png') }}" alt="Cafeteira em uma cozinha corporativa com identidade visual tecnológica">
    <div class="absolute inset-0 -z-10 bg-linear-to-r from-white via-white/90 to-white/15"></div>
    <div class="absolute inset-x-0 bottom-0 -z-10 h-28 bg-linear-to-t from-act-bg to-transparent"></div>

    <div class="mx-auto flex min-h-[430px] max-w-6xl items-center px-4 py-10 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <p class="text-sm font-semibold uppercase tracking-wide text-act-primary-dark">{{ $today->format('d/m/Y') }}</p>
            @if ($todayStatus['type'] === 'duty')
                <h1 class="mt-3 text-4xl font-black text-act-neutral sm:text-5xl">{{ $todayStatus['employee']->name }}</h1>
                <p class="mt-3 max-w-xl text-lg text-act-muted">Responsável pela cafeteira hoje.</p>
                <div class="mt-5 flex flex-wrap items-center gap-3">
                    <x-status-badge :status="$todayStatus['status']" />
                    @if ($todayStatus['original_employee'])
                        <span class="rounded-full border border-act-primary-light bg-act-primary-light px-3 py-1 text-xs font-semibold text-act-primary-dark">Troca de {{ $todayStatus['original_employee']->name }}</span>
                    @endif
                </div>
            @else
                <h1 class="mt-3 text-4xl font-black text-act-neutral sm:text-5xl">{{ $todayStatus['label'] }}</h1>
                <p class="mt-3 max-w-xl text-lg text-act-muted">Hoje não consome a vez de ninguém.</p>
            @endif
        </div>
    </div>
</section>

<section class="mx-auto grid max-w-6xl gap-8 px-4 py-8 sm:px-6 lg:grid-cols-[1fr_360px] lg:px-8">
    <div>
        <div class="mb-4 flex items-center justify-between gap-4">
            <h2 class="text-xl font-bold text-act-neutral">Próximos responsáveis</h2>
            <a class="text-sm font-semibold text-act-primary-dark hover:text-act-primary-dark" href="{{ route('schedule') }}">Ver escala</a>
        </div>
        <div class="overflow-hidden rounded-md border border-act-line bg-white shadow-sm shadow-act-primary-light/60">
            <table class="w-full text-left text-sm">
                <thead class="bg-act-primary-light text-xs uppercase text-act-primary-dark">
                    <tr>
                        <th class="px-4 py-3">Data</th>
                        <th class="px-4 py-3">Responsável</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-act-line">
                    @forelse ($upcoming as $day)
                        <tr>
                            <td class="px-4 py-3 font-medium text-act-muted">{{ $day['date']->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-act-neutral">{{ $day['employee']->name }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$day['status']" /></td>
                        </tr>
                    @empty
                        <tr><td class="px-4 py-6 text-act-muted" colspan="3">Nenhum funcionário ativo disponível.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <aside>
        <div class="mb-4 flex items-center justify-between gap-4">
            <h2 class="text-xl font-bold text-act-neutral">Histórico recente</h2>
            <a class="text-sm font-semibold text-act-primary-dark hover:text-act-primary-dark" href="{{ route('history') }}">Ver tudo</a>
        </div>
        <div class="space-y-3">
            @forelse ($history as $duty)
                <div class="rounded-md border border-act-line bg-white p-4 shadow-sm shadow-act-primary-light/50">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm font-semibold text-act-neutral">{{ $duty->employee?->name ?? 'Funcionário removido' }}</span>
                        <x-status-badge :status="$duty->status" />
                    </div>
                    <p class="mt-1 text-sm text-act-muted">{{ $duty->duty_date->format('d/m/Y') }}</p>
                </div>
            @empty
                <div class="rounded-md border border-act-line bg-white p-4 text-sm text-act-muted">Sem histórico recente.</div>
            @endforelse
        </div>
    </aside>
</section>
@endsection
