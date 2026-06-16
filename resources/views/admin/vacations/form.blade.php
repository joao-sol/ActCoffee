@csrf

<div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-act-muted" for="employee_id">Funcionário</label>
        <select id="employee_id" name="employee_id" required class="mt-2 w-full rounded-md border border-act-line px-3 py-2 text-sm outline-none focus:border-act-primary focus:ring-2 focus:ring-act-primary-light">
            <option value="">Selecione</option>
            @foreach ($employees as $employee)
                <option value="{{ $employee->id }}" @selected((int) old('employee_id', $vacation->employee_id) === $employee->id)>{{ $employee->name }}</option>
            @endforeach
        </select>
        @error('employee_id')<p class="mt-2 text-sm text-rose-700">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-act-muted" for="start_date">Data inicial</label>
        <input id="start_date" name="start_date" type="date" value="{{ old('start_date', $vacation->start_date?->format('Y-m-d')) }}" required class="mt-2 w-full rounded-md border border-act-line px-3 py-2 text-sm outline-none focus:border-act-primary focus:ring-2 focus:ring-act-primary-light">
        @error('start_date')<p class="mt-2 text-sm text-rose-700">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-act-muted" for="end_date">Data final</label>
        <input id="end_date" name="end_date" type="date" value="{{ old('end_date', $vacation->end_date?->format('Y-m-d')) }}" required class="mt-2 w-full rounded-md border border-act-line px-3 py-2 text-sm outline-none focus:border-act-primary focus:ring-2 focus:ring-act-primary-light">
        @error('end_date')<p class="mt-2 text-sm text-rose-700">{{ $message }}</p>@enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-act-muted" for="reason">Motivo</label>
        <input id="reason" name="reason" type="text" value="{{ old('reason', $vacation->reason) }}" class="mt-2 w-full rounded-md border border-act-line px-3 py-2 text-sm outline-none focus:border-act-primary focus:ring-2 focus:ring-act-primary-light">
        @error('reason')<p class="mt-2 text-sm text-rose-700">{{ $message }}</p>@enderror
    </div>
</div>

<div class="mt-6 flex flex-wrap gap-3">
    <button type="submit" class="rounded-md bg-act-primary px-4 py-2 text-sm font-bold text-white hover:bg-act-primary-dark">Salvar</button>
    <a href="{{ route('admin.ferias.index') }}" class="rounded-md border border-act-line px-4 py-2 text-sm font-bold text-act-muted hover:bg-act-primary-light">Cancelar</a>
</div>
