@props(['paginator'])

@if ($paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
        $start = max(1, $current - 2);
        $end = min($last, $current + 2);
    @endphp

    <nav class="flex justify-center items-center gap-2 mt-8" aria-label="Pagination">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="shadow-neu-inset bg-background rounded-full w-10 h-10 flex items-center justify-center text-outline">
                <x-icon name="chevron_left" />
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" aria-label="Halaman Sebelumnya"
               class="shadow-neu-raised bg-background rounded-full w-10 h-10 flex items-center justify-center text-secondary neu-btn transition-all">
                <x-icon name="chevron_left" />
            </a>
        @endif

        {{-- First page + ellipsis --}}
        @if ($start > 1)
            <a href="{{ $paginator->url(1) }}" aria-label="Halaman 1"
               class="shadow-neu-raised bg-background rounded-full w-10 h-10 flex items-center justify-center text-secondary neu-btn transition-all">1</a>
            @if ($start > 2)
                <span class="text-secondary px-1" aria-hidden="true">...</span>
            @endif
        @endif

        {{-- Page numbers --}}
        @for ($page = $start; $page <= $end; $page++)
            @if ($page === $current)
                <span class="shadow-neu-inset bg-background rounded-full w-10 h-10 flex items-center justify-center text-primary font-bold border-2 border-primary"
                      aria-current="page">{{ $page }}</span>
            @else
                <a href="{{ $paginator->url($page) }}" aria-label="Halaman {{ $page }}"
                   class="shadow-neu-raised bg-background rounded-full w-10 h-10 flex items-center justify-center text-secondary neu-btn transition-all">{{ $page }}</a>
            @endif
        @endfor

        {{-- Last page + ellipsis --}}
        @if ($end < $last)
            @if ($end < $last - 1)
                <span class="text-secondary px-1" aria-hidden="true">...</span>
            @endif
            <a href="{{ $paginator->url($last) }}" aria-label="Halaman {{ $last }}"
               class="shadow-neu-raised bg-background rounded-full w-10 h-10 flex items-center justify-center text-secondary neu-btn transition-all">{{ $last }}</a>
        @endif

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" aria-label="Halaman Berikutnya"
               class="shadow-neu-raised bg-background rounded-full w-10 h-10 flex items-center justify-center text-secondary neu-btn transition-all">
                <x-icon name="chevron_right" />
            </a>
        @else
            <span class="shadow-neu-inset bg-background rounded-full w-10 h-10 flex items-center justify-center text-outline">
                <x-icon name="chevron_right" />
            </span>
        @endif
    </nav>
@endif
