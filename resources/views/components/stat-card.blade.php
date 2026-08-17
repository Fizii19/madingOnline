@props([
    'label',
    'value',
    'accent' => 'bg-accent-blue', // tailwind color class for the top strip
])

<div class="shadow-neu-raised bg-background rounded-xl p-card-padding flex flex-col justify-between relative overflow-hidden">
    <div class="absolute top-0 left-0 w-full h-1 {{ $accent }}"></div>

    <div>
        <span class="text-label-caps font-label-caps text-secondary mb-2 block">{{ $label }}</span>
        <span class="text-display font-sans text-primary">{{ $value }}</span>
    </div>

    <div class="mt-4">
        {{ $slot }}
    </div>
</div>
