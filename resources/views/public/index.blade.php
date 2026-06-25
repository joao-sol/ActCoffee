@extends('layouts.public')

@section('title', 'Act Coffee')

@section('content')
@php
    $selectedSwapIds = collect(old('replacement_employee_ids', []))->map(fn ($id) => (int) $id)->all();
@endphp

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

                <div class="mt-6 max-w-xl">
                    @if ($todayStatus['status'] === 'completed')
                        <p class="text-sm font-semibold text-act-muted">Lavagem de hoje concluída.</p>
                    @else
                        <form method="POST" action="{{ route('schedule.complete', $today->toDateString()) }}" onsubmit="return confirm('Confirmar conclusão da lavagem de hoje?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="rounded-md bg-act-accent px-4 py-2 text-sm font-bold text-white hover:bg-green-600">Concluir</button>
                        </form>
                    @endif
                </div>
            @else
                <h1 class="mt-3 text-4xl font-black text-act-neutral sm:text-5xl">{{ $todayStatus['label'] }}</h1>
                <p class="mt-3 max-w-xl text-lg text-act-muted">Hoje não consome a vez de ninguém.</p>
            @endif
        </div>
    </div>
</section>

@if ($todayStatus['type'] === 'duty')
    <section class="mx-auto max-w-6xl px-4 pt-8 sm:px-6 lg:px-8">
        <div class="rounded-md border border-act-line bg-white p-5">
            @if ($todayStatus['status'] === 'completed')
                <p class="text-sm text-act-muted">A lavagem de hoje já foi concluída. A troca não pode mais alterar este dia.</p>
            @elseif ($swapCandidates->isEmpty())
                <p class="text-sm text-act-muted">Não há outro funcionário ativo e disponível para assumir hoje.</p>
            @else
                <form method="POST" action="{{ route('schedule.swap', $today->toDateString()) }}" class="space-y-4" onsubmit="return confirm('Confirmar troca com a pessoa selecionada?')">
                    @csrf
                    @method('PATCH')

                    <div>
                        <h2 class="text-sm font-bold text-act-neutral">Selecionar substituto</h2>
                        <p class="mt-1 text-sm text-act-muted">Escolha uma pessoa disponível para trocar com o responsável de hoje.</p>
                    </div>

                    @error('replacement_employee_ids')
                        <p class="text-sm text-rose-700">{{ $message }}</p>
                    @enderror

                    <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($swapCandidates as $candidate)
                            <label class="flex cursor-pointer items-center gap-3 rounded-md border border-act-line bg-act-bg px-3 py-2 text-sm font-semibold text-act-neutral hover:border-act-primary hover:bg-act-primary-light">
                                <input
                                    type="checkbox"
                                    name="replacement_employee_ids[]"
                                    value="{{ $candidate->id }}"
                                    data-swap-checkbox
                                    @checked(in_array($candidate->id, $selectedSwapIds, true))
                                    class="rounded border-act-line text-act-primary focus:ring-act-primary"
                                >
                                <span>{{ $candidate->name }}</span>
                            </label>
                        @endforeach
                    </div>

                    <button type="submit" class="rounded-md border border-act-primary-light bg-act-primary-light px-4 py-2 text-sm font-bold text-act-primary-dark hover:bg-blue-100">Trocar com selecionado</button>
                </form>
            @endif
        </div>
    </section>
@endif

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
