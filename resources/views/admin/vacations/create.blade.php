@extends('layouts.admin')

@section('title', 'Novo período de férias | Act Coffee')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-black text-act-neutral">Novo período de férias</h1>
</div>

<form method="POST" action="{{ route('admin.ferias.store') }}" class="max-w-2xl rounded-md border border-act-line bg-white p-6">
    @include('admin.vacations.form')
</form>
@endsection
