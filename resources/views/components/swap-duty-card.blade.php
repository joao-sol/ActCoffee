@props(['item', 'routeName'])

@php
    $duty = $item['duty'];
    $candidates = $item['candidates'];
    $dutyDate = $duty->duty_date->toDateString();
    $selectedSwapIds = old('swap_date') === $dutyDate
        ? collect(old('replacement_employee_ids', []))->map(fn ($id) => (int) $id)->all()
        : [];
@endphp

<article class="rounded-md border border-act-line bg-white p-5">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <p class="text-sm font-semibold text-act-primary-dark">{{ $duty->duty_date->format('d/m/Y') }}</p>
            <h3 class="mt-1 text-lg font-bold text-act-neutral">{{ $duty->employee?->name ?? 'Funcionário removido' }}</h3>
            @if ($duty->originalEmployee)
                <p class="mt-1 text-sm text-act-muted">Troca de {{ $duty->originalEmployee->name }}</p>
            @endif
        </div>
        <p class="text-xs font-semibold text-act-muted">Aberta até {{ $item['deadline']->format('d/m/Y') }}</p>
    </div>

    @if ($candidates->isEmpty())
        <p class="mt-4 text-sm text-act-muted">Não há outro funcionário ativo e disponível para assumir esta data.</p>
    @else
        <form
            method="POST"
            action="{{ route($routeName, $dutyDate) }}"
            class="mt-4 space-y-4 border-t border-act-line pt-4"
            data-swap-form
            onsubmit="return confirm('Confirmar troca com a pessoa selecionada?')"
        >
            @csrf
            @method('PATCH')
            <input type="hidden" name="swap_date" value="{{ $dutyDate }}">

            <div>
                <h4 class="text-sm font-bold text-act-neutral">Selecionar substituto</h4>
                <p class="mt-1 text-sm text-act-muted">Escolha quem assumiu a lavagem nesta data.</p>
            </div>

            @if (old('swap_date') === $dutyDate)
                @error('replacement_employee_ids')
                    <p class="text-sm text-rose-700">{{ $message }}</p>
                @enderror
            @endif

            <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($candidates as $candidate)
                    <label class="flex cursor-pointer items-center gap-3 rounded-md border border-act-line bg-act-bg px-3 py-2 text-sm font-semibold text-act-neutral hover:border-act-primary hover:bg-act-primary-light">
                        <input
                            type="checkbox"
                            name="replacement_employee_ids[]"
                            value="{{ $candidate->id }}"
                            data-swap-checkbox
                            @checked(in_array($candidate->id, $selectedSwapIds, true))
                            class="rounded border-act-line text-act-primary focus:ring-act-primary"
                        >
                        <span>{{ $candidate->name }}</span>
                    </label>
                @endforeach
            </div>

            <button type="submit" class="rounded-md border border-act-primary-light bg-act-primary-light px-4 py-2 text-sm font-bold text-act-primary-dark hover:bg-blue-100">Trocar com selecionado</button>
        </form>
    @endif
</article>
