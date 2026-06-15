@extends('layouts.admin')

@section('title', 'Ferias | Act Coffee')

@section('content')
<div class="mb-6 flex flex-wrap items-end justify-between gap-4">
    <div>
        <h1 class="text-3xl font-black text-zinc-950">Ferias</h1>
        <p class="mt-1 text-sm text-zinc-500">Periodos que pulam a pessoa apenas no dia da vez.</p>
    </div>
    <a href="{{ route('admin.ferias.create') }}" class="rounded-md bg-emerald-700 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-800">Nova ferias</a>
</div>

<div class="overflow-hidden rounded-md border border-zinc-200 bg-white">
    <table class="w-full text-left text-sm">
        <thead class="bg-zinc-100 text-xs uppercase text-zinc-500">
            <tr>
                <th class="px-4 py-3">Funcionario</th>
                <th class="px-4 py-3">Inicio</th>
                <th class="px-4 py-3">Fim</th>
                <th class="px-4 py-3">Motivo</th>
                <th class="px-4 py-3 text-right">Acoes</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-200">
            @forelse ($vacations as $vacation)
                <tr>
                    <td class="px-4 py-3 font-medium text-zinc-950">{{ $vacation->employee->name }}</td>
                    <td class="px-4 py-3 text-zinc-700">{{ $vacation->start_date->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-zinc-700">{{ $vacation->end_date->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-zinc-500">{{ $vacation->reason ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.ferias.edit', $vacation) }}" class="rounded-md border border-zinc-300 px-3 py-1.5 text-sm font-semibold text-zinc-700 hover:bg-zinc-100">Editar</a>
                            <form method="POST" action="{{ route('admin.ferias.destroy', $vacation) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-md border border-rose-200 bg-rose-50 px-3 py-1.5 text-sm font-semibold text-rose-700 hover:bg-rose-100">Remover</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td class="px-4 py-6 text-zinc-500" colspan="5">Nenhuma ferias cadastrada.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $vacations->links() }}</div>
@endsection
