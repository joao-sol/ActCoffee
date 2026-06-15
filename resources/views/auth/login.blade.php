@extends('layouts.public')

@section('title', 'Entrar | Act Coffee')

@section('content')
<section class="mx-auto grid min-h-[calc(100vh-73px)] max-w-6xl items-center gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[1fr_420px] lg:px-8">
    <div class="overflow-hidden rounded-md border border-zinc-200 bg-white shadow-sm">
        <img class="h-80 w-full object-cover" src="{{ asset('images/coffee-station.png') }}" alt="Balcao com cafeteira e canecas">
    </div>

    <form method="POST" action="{{ route('login.store') }}" class="rounded-md border border-zinc-200 bg-white p-6 shadow-sm">
        @csrf
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-zinc-950">Acesso administrativo</h1>
            <p class="mt-1 text-sm text-zinc-500">Act Coffee</p>
        </div>

        <label class="block text-sm font-medium text-zinc-700" for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="mt-2 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
        @error('email')
            <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
        @enderror

        <label class="mt-4 block text-sm font-medium text-zinc-700" for="password">Senha</label>
        <input id="password" name="password" type="password" required class="mt-2 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
        @error('password')
            <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
        @enderror

        <label class="mt-4 flex items-center gap-2 text-sm text-zinc-600">
            <input type="checkbox" name="remember" value="1" class="rounded border-zinc-300 text-emerald-700 focus:ring-emerald-600">
            Lembrar acesso
        </label>

        <button type="submit" class="mt-6 w-full rounded-md bg-emerald-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-800">Entrar</button>
    </form>
</section>
@endsection
