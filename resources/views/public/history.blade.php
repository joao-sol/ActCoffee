@extends('layouts.public')

@section('title', 'Historico | Act Coffee')

@section('content')
<section class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-black text-zinc-950">Historico</h1>
        <p class="mt-1 text-sm text-zinc-500">Registros dos ultimos 30 dias.</p>
    </div>

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
                @forelse ($history as $duty)
                    <tr>
                        <td class="px-4 py-3 font-medium text-zinc-700">{{ $duty->duty_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-zinc-900">{{ $duty->employee?->name ?? 'Funcionario removido' }}</td>
                        <td class="px-4 py-3"><x-status-badge :status="$duty->status" /></td>
                        <td class="px-4 py-3 text-zinc-500">{{ $duty->originalEmployee?->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td class="px-4 py-6 text-zinc-500" colspan="4">Sem historico recente.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
