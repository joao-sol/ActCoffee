<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin | Act Coffee')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-zinc-50 text-zinc-900 antialiased">
    <header class="border-b border-zinc-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <span class="flex size-10 items-center justify-center rounded-md bg-emerald-700 text-sm font-black text-white">AC</span>
                <span>
                    <span class="block text-base font-bold leading-tight">Act Coffee</span>
                    <span class="block text-xs text-zinc-500">Painel administrativo</span>
                </span>
            </a>
            <nav class="flex flex-wrap items-center gap-1 text-sm font-medium text-zinc-600">
                <a class="rounded-md px-3 py-2 hover:bg-zinc-100 {{ request()->routeIs('admin.dashboard') ? 'bg-zinc-100 text-zinc-950' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a class="rounded-md px-3 py-2 hover:bg-zinc-100 {{ request()->routeIs('admin.escala.*') ? 'bg-zinc-100 text-zinc-950' : '' }}" href="{{ route('admin.escala.index') }}">Escala</a>
                <a class="rounded-md px-3 py-2 hover:bg-zinc-100 {{ request()->routeIs('admin.funcionarios.*') ? 'bg-zinc-100 text-zinc-950' : '' }}" href="{{ route('admin.funcionarios.index') }}">Funcionarios</a>
                <a class="rounded-md px-3 py-2 hover:bg-zinc-100 {{ request()->routeIs('admin.ferias.*') ? 'bg-zinc-100 text-zinc-950' : '' }}" href="{{ route('admin.ferias.index') }}">Ferias</a>
                <a class="rounded-md px-3 py-2 hover:bg-zinc-100 {{ request()->routeIs('admin.feriados-personalizados.*') ? 'bg-zinc-100 text-zinc-950' : '' }}" href="{{ route('admin.feriados-personalizados.index') }}">Feriados</a>
                <a class="rounded-md px-3 py-2 hover:bg-zinc-100" href="{{ route('home') }}">Publico</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="rounded-md px-3 py-2 text-rose-700 hover:bg-rose-50" type="submit">Sair</button>
                </form>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6 space-y-3">
            <x-flash />
        </div>
        @yield('content')
    </main>
</body>
</html>
