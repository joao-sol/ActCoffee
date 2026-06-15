@extends('layouts.public')

@section('title', 'Escala | Act Coffee')

@section('content')
<section class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-black text-zinc-950">Escala</h1>
        <p class="mt-1 text-sm text-zinc-500">Proximos 30 dias validos.</p>
    </div>

    <div class="overflow-hidden rounded-md border border-zinc-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-zinc-100 text-xs uppercase text-zinc-500">
                <tr>
                    <th class="px-4 py-3">Data</th>
                    <th class="px-4 py-3">Responsavel</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Observacao</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200">
                @forelse ($days as $day)
                    <tr>
                        <td class="px-4 py-3 font-medium text-zinc-700">{{ $day['date']->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-zinc-900">{{ $day['employee']->name }}</td>
                        <td class="px-4 py-3"><x-status-badge :status="$day['status']" /></td>
                        <td class="px-4 py-3 text-zinc-500">
                            @if ($day['original_employee'])
                                Troca de {{ $day['original_employee']->name }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td class="px-4 py-6 text-zinc-500" colspan="4">Nenhum funcionario ativo disponivel.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
