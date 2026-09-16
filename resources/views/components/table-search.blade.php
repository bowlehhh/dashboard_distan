@props([
    'placeholder' => 'Cari data…',
    'label' => 'Cari data',
])

<form method="get" role="search" {{ $attributes->merge(['class' => 'toolbar']) }}>
    <input
        class="search"
        type="search"
        name="search"
        value="{{ request('search') }}"
        placeholder="{{ $placeholder }}"
        aria-label="{{ $label }}"
        autocomplete="off"
    >
    {{ $slot }}
    <button class="btn btn-outline" type="submit">⌕ Cari</button>
    @if (request()->except('page') !== [])
        <a class="btn btn-soft" href="{{ url()->current() }}">Reset</a>
    @endif
</form>
