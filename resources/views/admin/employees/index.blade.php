@extends('layouts.admin')

@section('title', 'Funcionários | Act Coffee')

@section('content')
<div class="mb-6 flex flex-wrap items-end justify-between gap-4">
    <div>
        <h1 class="text-3xl font-black text-act-neutral">Funcionários</h1>
        <p class="mt-1 text-sm text-act-muted">Fila atual de participantes.</p>
    </div>
    <a href="{{ route('admin.funcionarios.create') }}" class="rounded-md bg-act-primary px-4 py-2 text-sm font-bold text-white hover:bg-act-primary-dark">Novo funcionário</a>
</div>

<div class="overflow-hidden rounded-md border border-act-line bg-white">
    <table class="w-full text-left text-sm">
        <thead class="bg-act-primary-light text-xs uppercase text-act-muted">
            <tr>
                <th class="px-4 py-3">Fila</th>
                <th class="px-4 py-3">Nome</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Contratação</th>
                <th class="px-4 py-3 text-right">Ações</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-act-line">
            @foreach ($employees as $employee)
                <tr>
                    <td class="px-4 py-3 font-semibold text-act-muted">{{ $employee->queue_position }}</td>
                    <td class="px-4 py-3 font-medium text-act-neutral">{{ $employee->name }}</td>
                    <td class="px-4 py-3">
                        <span class="rounded-full border px-2.5 py-1 text-xs font-semibold {{ $employee->active ? 'border-act-primary-light bg-act-primary-light text-act-primary-dark' : 'border-act-line bg-act-bg text-act-muted' }}">{{ $employee->active ? 'Ativo' : 'Inativo' }}</span>
                    </td>
                    <td class="px-4 py-3 text-act-muted">{{ $employee->hired_at?->format('d/m/Y') ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.funcionarios.edit', $employee) }}" class="rounded-md border border-act-line px-3 py-1.5 text-sm font-semibold text-act-muted hover:bg-act-primary-light">Editar</a>
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
