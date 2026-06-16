@extends('layouts.public')

@section('title', 'Histórico | Act Coffee')

@section('content')
<section class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-black text-act-neutral">Histórico</h1>
        <p class="mt-1 text-sm text-act-muted">Registros dos ultimos 30 dias.</p>
    </div>

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
                @forelse ($history as $duty)
                    <tr>
                        <td class="px-4 py-3 font-medium text-act-muted">{{ $duty->duty_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-act-neutral">{{ $duty->employee?->name ?? 'Funcionário removido' }}</td>
                        <td class="px-4 py-3"><x-status-badge :status="$duty->status" /></td>
                        <td class="px-4 py-3 text-act-muted">{{ $duty->originalEmployee?->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td class="px-4 py-6 text-act-muted" colspan="4">Sem histórico recente.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
