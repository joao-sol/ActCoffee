@extends('layouts.admin')

@section('title', 'Dashboard | Act Coffee')

@section('content')
<div class="mb-8 flex flex-wrap items-end justify-between gap-4">
    <div>
        <h1 class="text-3xl font-black text-zinc-950">Dashboard</h1>
        <p class="mt-1 text-sm text-zinc-500">Resumo operacional da escala.</p>
    </div>
    <a href="{{ route('admin.escala.index') }}" class="rounded-md bg-emerald-700 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-800">Abrir escala</a>
</div>

<div class="grid gap-4 md:grid-cols-3">
    <div class="rounded-md border border-zinc-200 bg-white p-5">
        <p class="text-sm font-medium text-zinc-500">Funcionarios ativos</p>
        <p class="mt-2 text-3xl font-black text-zinc-950">{{ $activeEmployees }}</p>
    </div>
    <div class="rounded-md border border-zinc-200 bg-white p-5">
        <p class="text-sm font-medium text-zinc-500">Funcionarios inativos</p>
        <p class="mt-2 text-3xl font-black text-zinc-950">{{ $inactiveEmployees }}</p>
    </div>
    <div class="rounded-md border border-zinc-200 bg-white p-5">
        <p class="text-sm font-medium text-zinc-500">Responsavel hoje</p>
        @if ($todayStatus['type'] === 'duty')
            <p class="mt-2 text-3xl font-black text-zinc-950">{{ $todayStatus['employee']->name }}</p>
        @else
            <p class="mt-2 text-xl font-bold text-zinc-950">{{ $todayStatus['label'] }}</p>
        @endif
    </div>
</div>

<div class="mt-8 grid gap-8 lg:grid-cols-3">
    <section class="lg:col-span-2">
        <h2 class="mb-4 text-xl font-bold text-zinc-950">Proximos responsaveis</h2>
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
    </section>

    <aside class="space-y-8">
        <section>
            <h2 class="mb-4 text-xl font-bold text-zinc-950">Proximos feriados</h2>
            <div class="space-y-3">
                @foreach ($nextHolidays as $holiday)
                    <div class="rounded-md border border-zinc-200 bg-white p-4">
                        <p class="text-sm font-semibold text-zinc-950">{{ $holiday['name'] }}</p>
                        <p class="mt-1 text-sm text-zinc-500">{{ $holiday['date']->format('d/m/Y') }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section>
            <h2 class="mb-4 text-xl font-bold text-zinc-950">Ferias em andamento</h2>
            <div class="space-y-3">
                @forelse ($currentVacations as $vacation)
                    <div class="rounded-md border border-zinc-200 bg-white p-4">
                        <p class="text-sm font-semibold text-zinc-950">{{ $vacation->employee->name }}</p>
                        <p class="mt-1 text-sm text-zinc-500">{{ $vacation->start_date->format('d/m/Y') }} a {{ $vacation->end_date->format('d/m/Y') }}</p>
                    </div>
                @empty
                    <div class="rounded-md border border-zinc-200 bg-white p-4 text-sm text-zinc-500">Nenhuma ferias ativa hoje.</div>
                @endforelse
            </div>
        </section>
    </aside>
</div>
@endsection
