@extends('layouts.admin')

@section('title', 'Escala Admin | Act Coffee')

@section('content')
<div class="mb-8 flex flex-wrap items-end justify-between gap-4">
    <div>
        <h1 class="text-3xl font-black text-zinc-950">Escala</h1>
        <p class="mt-1 text-sm text-zinc-500">Controle do responsavel do dia e proximas datas validas.</p>
    </div>
</div>

<section class="mb-8 rounded-md border border-zinc-200 bg-white p-5">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Hoje - {{ $today->format('d/m/Y') }}</p>
            @if ($todayStatus['type'] === 'duty')
                <h2 class="mt-2 text-3xl font-black text-zinc-950">{{ $todayStatus['employee']->name }}</h2>
                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <x-status-badge :status="$todayStatus['status']" />
                    @if ($todayStatus['original_employee'])
                        <span class="rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-800">Troca de {{ $todayStatus['original_employee']->name }}</span>
                    @endif
                </div>
            @else
                <h2 class="mt-2 text-2xl font-black text-zinc-950">{{ $todayStatus['label'] }}</h2>
            @endif
        </div>

        @if ($todayStatus['type'] === 'duty')
            <div class="flex flex-wrap gap-3">
                <form method="POST" action="{{ route('admin.escala.complete', $today->toDateString()) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" @disabled($todayStatus['status'] === 'completed') class="rounded-md bg-emerald-700 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-800 disabled:cursor-not-allowed disabled:bg-zinc-300">Concluir</button>
                </form>
                <form method="POST" action="{{ route('admin.escala.swap', $today->toDateString()) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" @disabled($todayStatus['status'] === 'completed') class="rounded-md border border-amber-300 bg-amber-50 px-4 py-2 text-sm font-bold text-amber-800 hover:bg-amber-100 disabled:cursor-not-allowed disabled:border-zinc-200 disabled:bg-zinc-100 disabled:text-zinc-400">Trocar</button>
                </form>
            </div>
        @endif
    </div>
</section>

<div class="overflow-hidden rounded-md border border-zinc-200 bg-white">
    <table class="w-full text-left text-sm">
        <thead class="bg-zinc-100 text-xs uppercase text-zinc-500">
            <tr>
                <th class="px-4 py-3">Data</th>
                <th class="px-4 py-3">Responsavel</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Troca</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-200">
            @forelse ($days as $day)
                <tr>
                    <td class="px-4 py-3 font-medium text-zinc-700">{{ $day['date']->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-zinc-900">{{ $day['employee']->name }}</td>
                    <td class="px-4 py-3"><x-status-badge :status="$day['status']" /></td>
                    <td class="px-4 py-3 text-zinc-500">{{ $day['original_employee']?->name ?? '-' }}</td>
                </tr>
            @empty
                <tr><td class="px-4 py-6 text-zinc-500" colspan="4">Nenhum funcionario ativo disponivel.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
