@extends('layouts.admin')

@section('title', 'Funcionarios | Act Coffee')

@section('content')
<div class="mb-6 flex flex-wrap items-end justify-between gap-4">
    <div>
        <h1 class="text-3xl font-black text-zinc-950">Funcionarios</h1>
        <p class="mt-1 text-sm text-zinc-500">Fila atual de participantes.</p>
    </div>
    <a href="{{ route('admin.funcionarios.create') }}" class="rounded-md bg-emerald-700 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-800">Novo funcionario</a>
</div>

<div class="overflow-hidden rounded-md border border-zinc-200 bg-white">
    <table class="w-full text-left text-sm">
        <thead class="bg-zinc-100 text-xs uppercase text-zinc-500">
            <tr>
                <th class="px-4 py-3">Fila</th>
                <th class="px-4 py-3">Nome</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Contratacao</th>
                <th class="px-4 py-3 text-right">Acoes</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-200">
            @foreach ($employees as $employee)
                <tr>
                    <td class="px-4 py-3 font-semibold text-zinc-700">{{ $employee->queue_position }}</td>
                    <td class="px-4 py-3 font-medium text-zinc-950">{{ $employee->name }}</td>
                    <td class="px-4 py-3">
                        <span class="rounded-full border px-2.5 py-1 text-xs font-semibold {{ $employee->active ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-zinc-200 bg-zinc-50 text-zinc-600' }}">{{ $employee->active ? 'Ativo' : 'Inativo' }}</span>
                    </td>
                    <td class="px-4 py-3 text-zinc-500">{{ $employee->hired_at?->format('d/m/Y') ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.funcionarios.edit', $employee) }}" class="rounded-md border border-zinc-300 px-3 py-1.5 text-sm font-semibold text-zinc-700 hover:bg-zinc-100">Editar</a>
                            @if ($employee->active)
                                <form method="POST" action="{{ route('admin.funcionarios.deactivate', $employee) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-md border border-rose-200 bg-rose-50 px-3 py-1.5 text-sm font-semibold text-rose-700 hover:bg-rose-100">Inativar</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $employees->links() }}</div>
@endsection
