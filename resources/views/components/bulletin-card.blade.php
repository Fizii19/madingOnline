@props([
    'accent' => 'bg-[#a3defe]', // tailwind class for the top accent strip
    'pinned' => false,          // shows a push-pin in the top corner
    'image' => null,            // optional image url
    'imageAlt' => '',
    'imageHeight' => 'h-48',
    'tags' => [],
    'title' => '',
    'titleSize' => 'text-headline-md',
    'excerpt' => '',
])

<article {{ $attributes->merge(['class' => 'bg-surface rounded-xl shadow-neu-raised overflow-hidden flex flex-col relative']) }}>
    <div class="h-2 w-full {{ $accent }} absolute top-0 left-0"></div>

    @if ($pinned)
        <div class="absolute top-4 right-4 w-4 h-4 rounded-full bg-[#ffb7b2] shadow-sm z-10"></div>
    @endif

    @if ($image)
        <div class="{{ $imageHeight }} w-full relative">
            <img src="{{ $image }}" alt="{{ $imageAlt }}" class="w-full h-full object-cover">
        </div>
    @endif

    <div class="p-card-padding flex-grow flex flex-col">
        @if (count($tags))
            <div class="flex gap-2 mb-3">
                @foreach ($tags as $tag)
                    <span class="bg-surface-variant text-primary text-label-caps font-label-caps px-3 py-1 rounded-full shadow-neu-inset">{{ $tag }}</span>
                @endforeach
            </div>
        @endif

        <h2 class="{{ $titleSize }} font-sans text-primary mb-2">{{ $title }}</h2>
        <p class="text-body-md font-sans text-on-surface-variant mb-6 flex-grow">{{ $excerpt }}</p>

        <div class="flex justify-between items-center mt-auto">
            {{ $footer }}
        </div>
    </div>
</article>
