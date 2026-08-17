@props([
    'icon',
    'iconColor' => 'text-accent-green',
    'title',
    'description',
    'time',
])

<div class="flex gap-3 items-start p-3 rounded-lg hover:shadow-neu-inset hover:bg-background transition-shadow cursor-pointer">
    <div class="w-10 h-10 rounded-full shadow-neu-inset bg-background flex items-center justify-center shrink-0">
        <x-icon :name="$icon" :filled="true" class="{{ $iconColor }}" />
    </div>

    <div>
        <p class="font-bold text-primary">{{ $title }}</p>
        <p class="text-sm text-secondary line-clamp-1">{{ $description }}</p>
        <span class="text-xs text-secondary mt-1 block">{{ $time }}</span>
    </div>
</div>
