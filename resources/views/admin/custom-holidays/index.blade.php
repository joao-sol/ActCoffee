@extends('layouts.admin')

@section('title', 'Feriados personalizados | Act Coffee')

@section('content')
<div class="mb-6 flex flex-wrap items-end justify-between gap-4">
    <div>
        <h1 class="text-3xl font-black text-zinc-950">Feriados personalizados</h1>
        <p class="mt-1 text-sm text-zinc-500">Datas internas, municipais ou recessos.</p>
    </div>
    <a href="{{ route('admin.feriados-personalizados.create') }}" class="rounded-md bg-emerald-700 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-800">Novo feriado</a>
</div>

<div class="overflow-hidden rounded-md border border-zinc-200 bg-white">
    <table class="w-full text-left text-sm">
        <thead class="bg-zinc-100 text-xs uppercase text-zinc-500">
            <tr>
                <th class="px-4 py-3">Data</th>
                <th class="px-4 py-3">Nome</th>
                <th class="px-4 py-3">Descricao</th>
                <th class="px-4 py-3 text-right">Acoes</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-200">
            @forelse ($holidays as $holiday)
                <tr>
                    <td class="px-4 py-3 font-medium text-zinc-700">{{ $holiday->date->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-zinc-950">{{ $holiday->name }}</td>
                    <td class="px-4 py-3 text-zinc-500">{{ $holiday->description ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.feriados-personalizados.edit', $holiday) }}" class="rounded-md border border-zinc-300 px-3 py-1.5 text-sm font-semibold text-zinc-700 hover:bg-zinc-100">Editar</a>
                            <form method="POST" action="{{ route('admin.feriados-personalizados.destroy', $holiday) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-md border border-rose-200 bg-rose-50 px-3 py-1.5 text-sm font-semibold text-rose-700 hover:bg-rose-100">Remover</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td class="px-4 py-6 text-zinc-500" colspan="4">Nenhum feriado personalizado cadastrado.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $holidays->links() }}</div>
@endsection
