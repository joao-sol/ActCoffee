<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Act Coffee')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 text-zinc-900 antialiased">
    <header class="border-b border-zinc-200 bg-white/95">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="flex size-10 items-center justify-center rounded-md bg-emerald-700 text-sm font-black text-white">AC</span>
                <span>
                    <span class="block text-base font-bold leading-tight">Act Coffee</span>
                    <span class="block text-xs text-zinc-500">Escala da cafeteira</span>
                </span>
            </a>
            <nav class="flex items-center gap-1 text-sm font-medium text-zinc-600">
                <a class="rounded-md px-3 py-2 hover:bg-zinc-100" href="{{ route('schedule') }}">Escala</a>
                <a class="rounded-md px-3 py-2 hover:bg-zinc-100" href="{{ route('history') }}">Historico</a>
                @auth
                    <a class="rounded-md px-3 py-2 text-emerald-700 hover:bg-emerald-50" href="{{ route('admin.dashboard') }}">Admin</a>
                @else
                    <a class="rounded-md px-3 py-2 text-emerald-700 hover:bg-emerald-50" href="{{ route('login') }}">Entrar</a>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>
