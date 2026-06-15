@extends('layouts.admin')

@section('title', 'Nova ferias | Act Coffee')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-black text-zinc-950">Nova ferias</h1>
</div>

<form method="POST" action="{{ route('admin.ferias.store') }}" class="max-w-2xl rounded-md border border-zinc-200 bg-white p-6">
    @include('admin.vacations.form')
</form>
@endsection
