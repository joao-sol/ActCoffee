@extends('layouts.public')

@section('title', 'Entrar | Act Coffee')

@section('content')
<section class="mx-auto grid min-h-[calc(100vh-73px)] max-w-6xl items-center gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[1fr_420px] lg:px-8">
    <div class="overflow-hidden rounded-md border border-act-line bg-white shadow-sm">
        <img class="h-80 w-full object-cover" src="{{ asset('images/coffee-station.png') }}" alt="Balcão com cafeteira e canecas">
    </div>

    <form method="POST" action="{{ route('login.store') }}" class="rounded-md border border-act-line bg-white p-6 shadow-sm">
        @csrf
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-act-neutral">Acesso administrativo</h1>
            <p class="mt-1 text-sm text-act-muted">Act Coffee</p>
        </div>

        <label class="block text-sm font-medium text-act-muted" for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="mt-2 w-full rounded-md border border-act-line px-3 py-2 text-sm outline-none focus:border-act-primary focus:ring-2 focus:ring-act-primary-light">
        @error('email')
            <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
        @enderror

        <label class="mt-4 block text-sm font-medium text-act-muted" for="password">Senha</label>
        <input id="password" name="password" type="password" required class="mt-2 w-full rounded-md border border-act-line px-3 py-2 text-sm outline-none focus:border-act-primary focus:ring-2 focus:ring-act-primary-light">
        @error('password')
            <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
        @enderror

        <label class="mt-4 flex items-center gap-2 text-sm text-act-muted">
            <input type="checkbox" name="remember" value="1" class="rounded border-act-line text-act-primary-dark focus:ring-act-primary">
            Lembrar acesso
        </label>

        <button type="submit" class="mt-6 w-full rounded-md bg-act-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-act-primary-dark">Entrar</button>
    </form>
</section>
@endsection
