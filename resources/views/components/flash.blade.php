@if (session('success'))
    <div class="rounded-md border border-act-accent/30 bg-act-accent/10 px-4 py-3 text-sm text-act-accent">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="rounded-md border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
        {{ session('error') }}
    </div>
@endif
