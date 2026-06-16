@extends('layouts.admin')

@section('title', 'Editar período de férias | Act Coffee')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-black text-act-neutral">Editar período de férias</h1>
</div>

<form method="POST" action="{{ route('admin.ferias.update', $vacation) }}" class="max-w-2xl rounded-md border border-act-line bg-white p-6">
    @method('PUT')
    @include('admin.vacations.form')
</form>
@endsection
