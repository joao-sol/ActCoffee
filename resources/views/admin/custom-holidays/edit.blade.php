@extends('layouts.admin')

@section('title', 'Editar feriado | Act Coffee')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-black text-act-neutral">Editar feriado</h1>
</div>

<form method="POST" action="{{ route('admin.feriados-personalizados.update', $customHoliday) }}" class="max-w-2xl rounded-md border border-act-line bg-white p-6">
    @method('PUT')
    @include('admin.custom-holidays.form')
</form>
@endsection
