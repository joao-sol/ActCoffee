@csrf

<div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-zinc-700" for="name">Nome</label>
        <input id="name" name="name" type="text" value="{{ old('name', $employee->name) }}" required class="mt-2 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
        @error('name')<p class="mt-2 text-sm text-rose-700">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-zinc-700" for="hired_at">Data de contratacao</label>
        <input id="hired_at" name="hired_at" type="date" value="{{ old('hired_at', $employee->hired_at?->format('Y-m-d')) }}" class="mt-2 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
        @error('hired_at')<p class="mt-2 text-sm text-rose-700">{{ $message }}</p>@enderror
    </div>

    @if ($employee->exists)
        <div>
            <label class="block text-sm font-medium text-zinc-700" for="dismissed_at">Data de desligamento</label>
            <input id="dismissed_at" name="dismissed_at" type="date" value="{{ old('dismissed_at', $employee->dismissed_at?->format('Y-m-d')) }}" class="mt-2 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
            @error('dismissed_at')<p class="mt-2 text-sm text-rose-700">{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <input type="hidden" name="active" value="0">
            <label class="flex items-center gap-2 text-sm font-medium text-zinc-700">
                <input type="checkbox" name="active" value="1" @checked(old('active', $employee->active)) class="rounded border-zinc-300 text-emerald-700 focus:ring-emerald-600">
                Participa das proximas escalas
            </label>
        </div>
    @endif
</div>

<div class="mt-6 flex flex-wrap gap-3">
    <button type="submit" class="rounded-md bg-emerald-700 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-800">Salvar</button>
    <a href="{{ route('admin.funcionarios.index') }}" class="rounded-md border border-zinc-300 px-4 py-2 text-sm font-bold text-zinc-700 hover:bg-zinc-100">Cancelar</a>
</div>
