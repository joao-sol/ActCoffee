@csrf

<div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-act-muted" for="name">Nome</label>
        <input id="name" name="name" type="text" value="{{ old('name', $employee->name) }}" required class="mt-2 w-full rounded-md border border-act-line px-3 py-2 text-sm outline-none focus:border-act-primary focus:ring-2 focus:ring-act-primary-light">
        @error('name')<p class="mt-2 text-sm text-rose-700">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-act-muted" for="hired_at">Data de contratacao</label>
        <input id="hired_at" name="hired_at" type="date" value="{{ old('hired_at', $employee->hired_at?->format('Y-m-d')) }}" class="mt-2 w-full rounded-md border border-act-line px-3 py-2 text-sm outline-none focus:border-act-primary focus:ring-2 focus:ring-act-primary-light">
        @error('hired_at')<p class="mt-2 text-sm text-rose-700">{{ $message }}</p>@enderror
    </div>

    @if ($employee->exists)
        <div>
            <label class="block text-sm font-medium text-act-muted" for="dismissed_at">Data de desligamento</label>
            <input id="dismissed_at" name="dismissed_at" type="date" value="{{ old('dismissed_at', $employee->dismissed_at?->format('Y-m-d')) }}" class="mt-2 w-full rounded-md border border-act-line px-3 py-2 text-sm outline-none focus:border-act-primary focus:ring-2 focus:ring-act-primary-light">
            @error('dismissed_at')<p class="mt-2 text-sm text-rose-700">{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <input type="hidden" name="active" value="0">
            <label class="flex items-center gap-2 text-sm font-medium text-act-muted">
                <input type="checkbox" name="active" value="1" @checked(old('active', $employee->active)) class="rounded border-act-line text-act-primary-dark focus:ring-act-primary">
                Participa das próximas escalas
            </label>
        </div>
    @endif
</div>

<div class="mt-6 flex flex-wrap gap-3">
    <button type="submit" class="rounded-md bg-act-primary px-4 py-2 text-sm font-bold text-white hover:bg-act-primary-dark">Salvar</button>
    <a href="{{ route('admin.funcionarios.index') }}" class="rounded-md border border-act-line px-4 py-2 text-sm font-bold text-act-muted hover:bg-act-primary-light">Cancelar</a>
</div>
