@extends('layouts.admin')

@section('title', 'Feriados personalizados | Act Coffee')

@section('content')
<div class="mb-6 flex flex-wrap items-end justify-between gap-4">
    <div>
        <h1 class="text-3xl font-black text-act-neutral">Feriados personalizados</h1>
        <p class="mt-1 text-sm text-act-muted">Datas internas, municipais ou recessos.</p>
    </div>
    <a href="{{ route('admin.feriados-personalizados.create') }}" class="rounded-md bg-act-primary px-4 py-2 text-sm font-bold text-white hover:bg-act-primary-dark">Novo feriado</a>
</div>

<div class="overflow-hidden rounded-md border border-act-line bg-white">
    <table class="w-full text-left text-sm">
        <thead class="bg-act-primary-light text-xs uppercase text-act-muted">
            <tr>
                <th class="px-4 py-3">Data</th>
                <th class="px-4 py-3">Nome</th>
                <th class="px-4 py-3">Descrição</th>
                <th class="px-4 py-3 text-right">Ações</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-act-line">
            @forelse ($holidays as $holiday)
                <tr>
                    <td class="px-4 py-3 font-medium text-act-muted">{{ $holiday->date->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-act-neutral">{{ $holiday->name }}</td>
                    <td class="px-4 py-3 text-act-muted">{{ $holiday->description ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.feriados-personalizados.edit', $holiday) }}" class="rounded-md border border-act-line px-3 py-1.5 text-sm font-semibold text-act-muted hover:bg-act-primary-light">Editar</a>
                            <form method="POST" action="{{ route('admin.feriados-personalizados.destroy', $holiday) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-md border border-rose-200 bg-rose-50 px-3 py-1.5 text-sm font-semibold text-rose-700 hover:bg-rose-100">Remover</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td class="px-4 py-6 text-act-muted" colspan="4">Nenhum feriado personalizado cadastrado.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $holidays->links() }}</div>
@endsection
