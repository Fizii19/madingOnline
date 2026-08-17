@props([
    'name',
    'filled' => false,
])

<span {{ $attributes->class(['material-symbols-outlined', 'filled' => $filled]) }}>{{ $name }}</span>
