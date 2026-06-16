@csrf

<div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-act-muted" for="name">Nome</label>
        <input id="name" name="name" type="text" value="{{ old('name', $customHoliday->name) }}" required class="mt-2 w-full rounded-md border border-act-line px-3 py-2 text-sm outline-none focus:border-act-primary focus:ring-2 focus:ring-act-primary-light">
        @error('name')<p class="mt-2 text-sm text-rose-700">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-act-muted" for="date">Data</label>
        <input id="date" name="date" type="date" value="{{ old('date', $customHoliday->date?->format('Y-m-d')) }}" required class="mt-2 w-full rounded-md border border-act-line px-3 py-2 text-sm outline-none focus:border-act-primary focus:ring-2 focus:ring-act-primary-light">
        @error('date')<p class="mt-2 text-sm text-rose-700">{{ $message }}</p>@enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-act-muted" for="description">Descrição</label>
        <textarea id="description" name="description" rows="4" class="mt-2 w-full rounded-md border border-act-line px-3 py-2 text-sm outline-none focus:border-act-primary focus:ring-2 focus:ring-act-primary-light">{{ old('description', $customHoliday->description) }}</textarea>
        @error('description')<p class="mt-2 text-sm text-rose-700">{{ $message }}</p>@enderror
    </div>
</div>

<div class="mt-6 flex flex-wrap gap-3">
    <button type="submit" class="rounded-md bg-act-primary px-4 py-2 text-sm font-bold text-white hover:bg-act-primary-dark">Salvar</button>
    <a href="{{ route('admin.feriados-personalizados.index') }}" class="rounded-md border border-act-line px-4 py-2 text-sm font-bold text-act-muted hover:bg-act-primary-light">Cancelar</a>
</div>
