<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Act Coffee')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/act-coffee-logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-act-bg text-act-neutral antialiased">
    <header class="border-b border-act-line bg-white/95">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-md border border-act-line bg-white p-0.5">
                    <img src="{{ asset('images/act-coffee-logo.png') }}" alt="Act Coffee" class="h-9 w-9 object-contain">
                </span>
                <span>
                    <span class="block text-base font-bold leading-tight">Act Coffee</span>
                    <span class="block text-xs text-act-muted">Escala da cafeteira</span>
                </span>
            </a>
            <nav class="flex items-center gap-1 text-sm font-medium text-act-muted">
                <a class="rounded-md px-3 py-2 hover:bg-act-primary-light" href="{{ route('schedule') }}">Escala</a>
                <a class="rounded-md px-3 py-2 hover:bg-act-primary-light" href="{{ route('history') }}">Histórico</a>
                @auth
                    <a class="rounded-md px-3 py-2 text-act-primary-dark hover:bg-act-primary-light" href="{{ route('admin.dashboard') }}">Admin</a>
                @else
                    <a class="rounded-md px-3 py-2 text-act-primary-dark hover:bg-act-primary-light" href="{{ route('login') }}">Entrar</a>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        <div class="mx-auto max-w-6xl px-4 pt-4 sm:px-6 lg:px-8">
            <x-flash />
        </div>
        @yield('content')
    </main>
</body>
</html>
