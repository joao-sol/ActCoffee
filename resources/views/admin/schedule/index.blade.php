@extends('layouts.admin')

@section('title', 'Escala Admin | Act Coffee')

@section('content')
<div class="mb-8 flex flex-wrap items-end justify-between gap-4">
    <div>
        <h1 class="text-3xl font-black text-act-neutral">Escala</h1>
        <p class="mt-1 text-sm text-act-muted">Controle do responsável do dia e próximas datas válidas.</p>
    </div>
</div>

<section class="mb-8 rounded-md border border-act-line bg-white p-5">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-act-primary-dark">Hoje - {{ $today->format('d/m/Y') }}</p>
            @if ($todayStatus['type'] === 'duty')
                <h2 class="mt-2 text-3xl font-black text-act-neutral">{{ $todayStatus['employee']->name }}</h2>
                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <x-status-badge :status="$todayStatus['status']" />
                    @if ($todayStatus['original_employee'])
                        <span class="rounded-full border border-act-primary-light bg-act-primary-light px-3 py-1 text-xs font-semibold text-act-primary-dark">Troca de {{ $todayStatus['original_employee']->name }}</span>
                    @endif
                </div>
            @else
                <h2 class="mt-2 text-2xl font-black text-act-neutral">{{ $todayStatus['label'] }}</h2>
            @endif
        </div>

        @if ($todayStatus['type'] === 'duty')
            <div class="flex flex-wrap gap-3">
                <form method="POST" action="{{ route('admin.escala.complete', $today->toDateString()) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" @disabled($todayStatus['status'] === 'completed') class="rounded-md bg-act-accent px-4 py-2 text-sm font-bold text-white hover:bg-green-600 disabled:cursor-not-allowed disabled:bg-slate-300">Concluir</button>
                </form>
                <form method="POST" action="{{ route('admin.escala.swap', $today->toDateString()) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" @disabled($todayStatus['status'] === 'completed') class="rounded-md border border-act-primary-light bg-act-primary-light px-4 py-2 text-sm font-bold text-act-primary-dark hover:bg-blue-100 disabled:cursor-not-allowed disabled:border-act-line disabled:bg-act-primary-light disabled:text-slate-400">Trocar</button>
                </form>
            </div>
        @endif
    </div>
</section>

<div class="overflow-hidden rounded-md border border-act-line bg-white">
    <table class="w-full text-left text-sm">
        <thead class="bg-act-primary-light text-xs uppercase text-act-muted">
            <tr>
                <th class="px-4 py-3">Data</th>
                <th class="px-4 py-3">Responsável</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Troca</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-act-line">
            @forelse ($days as $day)
                <tr>
                    <td class="px-4 py-3 font-medium text-act-muted">{{ $day['date']->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-act-neutral">{{ $day['employee']->name }}</td>
                    <td class="px-4 py-3"><x-status-badge :status="$day['status']" /></td>
                    <td class="px-4 py-3 text-act-muted">{{ $day['original_employee']?->name ?? '-' }}</td>
                </tr>
            @empty
                <tr><td class="px-4 py-6 text-act-muted" colspan="4">Nenhum funcionário ativo disponível.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
