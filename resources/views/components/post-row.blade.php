@props([
    'title',
    'category',
    'pinned' => false,
    'author',
    'date',
    'accent' => 'bg-[#bae6fd]', // tailwind class for the top accent strip
    'viewUrl' => '#',
    'editUrl' => '#',
    'deleteUrl' => null,
])

<div class="shadow-neu-raised bg-background rounded-xl p-card-padding grid grid-cols-1 md:grid-cols-12 gap-gutter items-center relative overflow-hidden">
    <div class="absolute top-0 left-0 w-full h-1 {{ $accent }} opacity-70"></div>

    <div class="md:col-span-5 flex flex-col gap-1">
        <h2 class="text-headline-md font-sans text-primary">{{ $title }}</h2>
        <div class="flex items-center gap-2 flex-wrap">
            <span class="shadow-neu-inset bg-background rounded-full px-3 py-1 text-[10px] font-label-caps uppercase tracking-wider text-secondary">{{ $category }}</span>

            @if ($pinned)
                <span class="shadow-neu-inset bg-background rounded-full px-3 py-1 text-[10px] font-label-caps uppercase tracking-wider text-secondary flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-[#fca5a5]"></span> Disematkan
                </span>
            @endif
        </div>
    </div>

    <div class="md:col-span-2 flex items-center gap-2 text-secondary">
        <x-icon name="person" class="text-[20px]" />
        {{ $author }}
    </div>

    <div class="md:col-span-2 text-secondary text-sm">
        {{ $date }}
    </div>

    <div class="md:col-span-3 flex items-center justify-end gap-3 mt-4 md:mt-0">
        <a href="{{ $viewUrl }}" aria-label="Lihat"
           class="shadow-neu-raised bg-background rounded-full w-10 h-10 flex items-center justify-center text-secondary hover:text-primary neu-btn">
            <x-icon name="visibility" class="text-[20px]" />
        </a>
        <a href="{{ $editUrl }}" aria-label="Edit"
           class="shadow-neu-raised bg-background rounded-full w-10 h-10 flex items-center justify-center text-secondary hover:text-primary neu-btn">
            <x-icon name="edit" class="text-[20px]" />
        </a>
        @if ($deleteUrl)
            <form method="POST" action="{{ $deleteUrl }}"
                  onsubmit="return confirm('Hapus postingan ini? Tindakan tidak bisa dibatalkan.');">
                @csrf
                @method('DELETE')
                <button type="submit" aria-label="Hapus"
                        class="shadow-neu-raised bg-background rounded-full w-10 h-10 flex items-center justify-center text-error hover:text-error-container neu-btn">
                    <x-icon name="delete" class="text-[20px]" />
                </button>
            </form>
        @endif
    </div>
</div>
