@extends('layouts.admin')

@section('title', 'Novo funcionário | Act Coffee')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-black text-act-neutral">Novo funcionário</h1>
</div>

<form method="POST" action="{{ route('admin.funcionarios.store') }}" class="max-w-2xl rounded-md border border-act-line bg-white p-6">
    @include('admin.employees.form')
</form>
@endsection
