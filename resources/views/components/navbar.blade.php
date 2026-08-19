@props([
    'active' => null,          // 'home' | 'dashboard' | 'management'
    'showSearch' => true,
    'simple' => false,         // logo only (used on login/register pages)
])

@php
    // Admin-only pages are hidden from the navbar for visitors & regular users.
    $links = ['home' => ['label' => 'Beranda', 'url' => route('home')]];

    if (auth()->check() && auth()->user()->is_admin) {
        $links['dashboard'] = ['label' => 'Dasbor', 'url' => route('dashboard')];
        $links['management'] = ['label' => 'Manajemen', 'url' => route('management')];
        $links['reports'] = ['label' => 'Laporan Komentar', 'url' => route('admin.reports')];
        $links['post-reports'] = ['label' => 'Laporan Postingan', 'url' => route('admin.post-reports')];
        $links['polls'] = ['label' => 'Polling', 'url' => route('polls.index')];
    }

    if (auth()->check() && !auth()->user()->is_admin) {
        $links['mading_my'] = ['label' => 'Mading Saya', 'url' => route('mading.my')];
        $links['mading_upload'] = ['label' => 'Upload Mading', 'url' => route('mading.upload')];
    }
@endphp

<nav class="fixed top-0 left-0 w-full z-50 flex justify-between items-center px-container-padding py-stack-gap max-w-[1440px] mx-auto bg-background shadow-[8px_8px_16px_#AEAEC080,-8px_-8px_16px_#FFFFFF]">
    <div class="flex items-center gap-stack-gap">
        <a href="{{ route('home') }}" class="text-headline-lg font-sans font-extrabold text-primary">MadingBoard</a>

        @unless ($simple)
            <div class="hidden md:flex items-center gap-gutter ml-container-padding">
                @foreach ($links as $key => $link)
                    <a href="{{ $link['url'] }}"
                       class="{{ $active === $key
                            ? 'text-primary font-bold border-b-2 border-primary'
                            : 'text-secondary font-medium hover:text-primary hover:scale-105 transition-transform' }}">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>
        @endunless
    </div>

    <div class="flex items-center gap-stack-gap">
        @if ($showSearch && ! $simple)
            <form method="GET" action="{{ url()->current() }}"
                  class="hidden md:flex relative shadow-neu-inset bg-background rounded-full px-4 py-2 items-center w-64">
                <x-icon name="search" class="text-secondary mr-2" />
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari..."
                       class="bg-transparent border-none focus:outline-none focus:ring-0 text-primary w-full placeholder:text-outline">
            </form>
        @endif

        @auth
            <div class="flex items-center gap-stack-gap">
                <div class="w-10 h-10 rounded-full shadow-neu-raised bg-background flex items-center justify-center text-primary font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <span class="hidden md:block text-secondary text-sm font-medium">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="shadow-neu-raised bg-background rounded-full px-6 py-2 text-primary font-bold neu-btn text-label-caps font-label-caps">
                        Keluar
                    </button>
                </form>
            </div>
        @else
            <a href="{{ route('login') }}"
               class="shadow-neu-raised bg-background rounded-full px-6 py-2 text-primary font-bold neu-btn text-label-caps font-label-caps">
                Masuk
            </a>
        @endauth
    </div>
</nav>
