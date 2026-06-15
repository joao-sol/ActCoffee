@extends('layouts.public')

@section('title', 'Act Coffee')

@section('content')
<section class="border-b border-zinc-200 bg-white">
    <div class="mx-auto grid max-w-6xl gap-8 px-4 py-8 sm:px-6 md:grid-cols-[1.1fr_0.9fr] lg:px-8">
        <div class="flex flex-col justify-center">
            <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">{{ $today->format('d/m/Y') }}</p>
            @if ($todayStatus['type'] === 'duty')
                <h1 class="mt-3 text-4xl font-black text-zinc-950 sm:text-5xl">{{ $todayStatus['employee']->name }}</h1>
                <p class="mt-3 text-lg text-zinc-600">Responsavel pela cafeteira hoje.</p>
                <div class="mt-5 flex flex-wrap items-center gap-3">
                    <x-status-badge :status="$todayStatus['status']" />
                    @if ($todayStatus['original_employee'])
                        <span class="rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-800">Troca de {{ $todayStatus['original_employee']->name }}</span>
                    @endif
                </div>
            @else
                <h1 class="mt-3 text-4xl font-black text-zinc-950 sm:text-5xl">{{ $todayStatus['label'] }}</h1>
                <p class="mt-3 text-lg text-zinc-600">Hoje nao consome a vez de ninguem.</p>
            @endif
        </div>
        <div class="overflow-hidden rounded-md border border-zinc-200 bg-zinc-100">
            <img class="h-72 w-full object-cover" src="{{ asset('images/coffee-station.png') }}" alt="Balcao com cafeteira e canecas">
        </div>
    </div>
</section>

<section class="mx-auto grid max-w-6xl gap-8 px-4 py-8 sm:px-6 lg:grid-cols-[1fr_360px] lg:px-8">
    <div>
        <div class="mb-4 flex items-center justify-between gap-4">
            <h2 class="text-xl font-bold text-zinc-950">Proximos responsaveis</h2>
            <a class="text-sm font-semibold text-emerald-700 hover:text-emerald-900" href="{{ route('schedule') }}">Ver escala</a>
        </div>
        <div class="overflow-hidden rounded-md border border-zinc-200 bg-white">
            <table class="w-full text-left text-sm">
                <thead class="bg-zinc-100 text-xs uppercase text-zinc-500">
                    <tr>
                        <th class="px-4 py-3">Data</th>
                        <th class="px-4 py-3">Responsavel</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    @forelse ($upcoming as $day)
                        <tr>
                            <td class="px-4 py-3 font-medium text-zinc-700">{{ $day['date']->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-zinc-900">{{ $day['employee']->name }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$day['status']" /></td>
                        </tr>
                    @empty
                        <tr><td class="px-4 py-6 text-zinc-500" colspan="3">Nenhum funcionario ativo disponivel.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <aside>
        <div class="mb-4 flex items-center justify-between gap-4">
            <h2 class="text-xl font-bold text-zinc-950">Historico recente</h2>
            <a class="text-sm font-semibold text-emerald-700 hover:text-emerald-900" href="{{ route('history') }}">Ver tudo</a>
        </div>
        <div class="space-y-3">
            @forelse ($history as $duty)
                <div class="rounded-md border border-zinc-200 bg-white p-4">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm font-semibold text-zinc-900">{{ $duty->employee?->name ?? 'Funcionario removido' }}</span>
                        <x-status-badge :status="$duty->status" />
                    </div>
                    <p class="mt-1 text-sm text-zinc-500">{{ $duty->duty_date->format('d/m/Y') }}</p>
                </div>
            @empty
                <div class="rounded-md border border-zinc-200 bg-white p-4 text-sm text-zinc-500">Sem historico recente.</div>
            @endforelse
        </div>
    </aside>
</section>
@endsection
