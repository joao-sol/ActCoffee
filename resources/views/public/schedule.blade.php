@extends('layouts.public')

@section('title', 'Escala | Act Coffee')

@section('content')
<section class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-black text-act-neutral">Escala</h1>
        <p class="mt-1 text-sm text-act-muted">Próximos 30 dias válidos.</p>
    </div>

    <div class="overflow-hidden rounded-md border border-act-line bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-act-primary-light text-xs uppercase text-act-muted">
                <tr>
                    <th class="px-4 py-3">Data</th>
                    <th class="px-4 py-3">Responsável</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Observação</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-act-line">
                @forelse ($days as $day)
                    <tr>
                        <td class="px-4 py-3 font-medium text-act-muted">{{ $day['date']->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-act-neutral">{{ $day['employee']->name }}</td>
                        <td class="px-4 py-3"><x-status-badge :status="$day['status']" /></td>
                        <td class="px-4 py-3 text-act-muted">
                            @if ($day['original_employee'])
                                Troca de {{ $day['original_employee']->name }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td class="px-4 py-6 text-act-muted" colspan="4">Nenhum funcionário ativo disponível.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
