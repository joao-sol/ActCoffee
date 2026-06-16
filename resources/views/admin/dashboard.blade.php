@extends('layouts.admin')

@section('title', 'Dashboard | Act Coffee')

@section('content')
<div class="mb-8 flex flex-wrap items-end justify-between gap-4">
    <div>
        <h1 class="text-3xl font-black text-act-neutral">Dashboard</h1>
        <p class="mt-1 text-sm text-act-muted">Resumo operacional da escala.</p>
    </div>
    <a href="{{ route('admin.escala.index') }}" class="rounded-md bg-act-primary px-4 py-2 text-sm font-bold text-white hover:bg-act-primary-dark">Abrir escala</a>
</div>

<div class="grid gap-4 md:grid-cols-3">
    <div class="rounded-md border border-act-line bg-white p-5">
        <p class="text-sm font-medium text-act-muted">Funcionários ativos</p>
        <p class="mt-2 text-3xl font-black text-act-neutral">{{ $activeEmployees }}</p>
    </div>
    <div class="rounded-md border border-act-line bg-white p-5">
        <p class="text-sm font-medium text-act-muted">Funcionários inativos</p>
        <p class="mt-2 text-3xl font-black text-act-neutral">{{ $inactiveEmployees }}</p>
    </div>
    <div class="rounded-md border border-act-line bg-white p-5">
        <p class="text-sm font-medium text-act-muted">Responsável hoje</p>
        @if ($todayStatus['type'] === 'duty')
            <p class="mt-2 text-3xl font-black text-act-neutral">{{ $todayStatus['employee']->name }}</p>
        @else
            <p class="mt-2 text-xl font-bold text-act-neutral">{{ $todayStatus['label'] }}</p>
        @endif
    </div>
</div>

<div class="mt-8 grid gap-8 lg:grid-cols-3">
    <section class="lg:col-span-2">
        <h2 class="mb-4 text-xl font-bold text-act-neutral">Próximos responsáveis</h2>
        <div class="overflow-hidden rounded-md border border-act-line bg-white">
            <table class="w-full text-left text-sm">
                <thead class="bg-act-primary-light text-xs uppercase text-act-muted">
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
    </section>

    <aside class="space-y-8">
        <section>
            <h2 class="mb-4 text-xl font-bold text-act-neutral">Próximos feriados</h2>
            <div class="space-y-3">
                @foreach ($nextHolidays as $holiday)
                    <div class="rounded-md border border-act-line bg-white p-4">
                        <p class="text-sm font-semibold text-act-neutral">{{ $holiday['name'] }}</p>
                        <p class="mt-1 text-sm text-act-muted">{{ $holiday['date']->format('d/m/Y') }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section>
            <h2 class="mb-4 text-xl font-bold text-act-neutral">Férias em andamento</h2>
            <div class="space-y-3">
                @forelse ($currentVacations as $vacation)
                    <div class="rounded-md border border-act-line bg-white p-4">
                        <p class="text-sm font-semibold text-act-neutral">{{ $vacation->employee->name }}</p>
                        <p class="mt-1 text-sm text-act-muted">{{ $vacation->start_date->format('d/m/Y') }} a {{ $vacation->end_date->format('d/m/Y') }}</p>
                    </div>
                @empty
                    <div class="rounded-md border border-act-line bg-white p-4 text-sm text-act-muted">Nenhuma pessoa em férias hoje.</div>
                @endforelse
            </div>
        </section>
    </aside>
</div>
@endsection
