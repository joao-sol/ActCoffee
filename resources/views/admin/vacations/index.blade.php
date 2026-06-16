@extends('layouts.admin')

@section('title', 'Férias | Act Coffee')

@section('content')
<div class="mb-6 flex flex-wrap items-end justify-between gap-4">
    <div>
        <h1 class="text-3xl font-black text-act-neutral">Férias</h1>
        <p class="mt-1 text-sm text-act-muted">Períodos que pulam a pessoa apenas no dia da vez.</p>
    </div>
    <a href="{{ route('admin.ferias.create') }}" class="rounded-md bg-act-primary px-4 py-2 text-sm font-bold text-white hover:bg-act-primary-dark">Novo período de férias</a>
</div>

<div class="overflow-hidden rounded-md border border-act-line bg-white">
    <table class="w-full text-left text-sm">
        <thead class="bg-act-primary-light text-xs uppercase text-act-muted">
            <tr>
                <th class="px-4 py-3">Funcionário</th>
                <th class="px-4 py-3">Início</th>
                <th class="px-4 py-3">Fim</th>
                <th class="px-4 py-3">Motivo</th>
                <th class="px-4 py-3 text-right">Ações</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-act-line">
            @forelse ($vacations as $vacation)
                <tr>
                    <td class="px-4 py-3 font-medium text-act-neutral">{{ $vacation->employee->name }}</td>
                    <td class="px-4 py-3 text-act-muted">{{ $vacation->start_date->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-act-muted">{{ $vacation->end_date->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-act-muted">{{ $vacation->reason ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.ferias.edit', $vacation) }}" class="rounded-md border border-act-line px-3 py-1.5 text-sm font-semibold text-act-muted hover:bg-act-primary-light">Editar</a>
                            <form method="POST" action="{{ route('admin.ferias.destroy', $vacation) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-md border border-rose-200 bg-rose-50 px-3 py-1.5 text-sm font-semibold text-rose-700 hover:bg-rose-100">Remover</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td class="px-4 py-6 text-act-muted" colspan="5">Nenhum período de férias cadastrado.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $vacations->links() }}</div>
@endsection
