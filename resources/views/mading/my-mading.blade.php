<x-layout title="Mading Saya - MadingBoard">
    <x-navbar :active="'mading_my'" :show-search="false" />

    <main class="flex-grow w-full max-w-[1440px] mx-auto px-container-padding pt-[100px] pb-container-padding flex flex-col gap-stack-gap">
        <header class="flex justify-between items-end mb-gutter">
            <div>
                <h1 class="text-display font-sans text-primary">Mading Saya</h1>
                <p class="text-secondary mt-unit text-body-lg font-sans">Lihat status semua mading yang sudah kamu upload.</p>
            </div>
            <a href="{{ route('mading.upload') }}"
               class="shadow-neu-raised rounded-lg px-8 py-4 flex items-center gap-2 bg-[#e0f2fe] text-primary neu-btn">
                <x-icon name="add" />
                <span class="text-label-caps font-label-caps font-bold">Upload Mading</span>
            </a>
        </header>

        @if (session('success'))
            <div class="px-4 py-3 rounded-lg bg-accent-green text-on-surface text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-gutter">
            <div class="shadow-neu-raised bg-background rounded-xl p-card-padding">
                <p class="text-label-caps font-label-caps text-secondary mb-1">Total</p>
                <p class="text-headline-md font-sans text-primary">{{ $stats['total'] }}</p>
            </div>
            <div class="shadow-neu-raised bg-background rounded-xl p-card-padding">
                <p class="text-label-caps font-label-caps text-accent-orange mb-1">Menunggu</p>
                <p class="text-headline-md font-sans text-accent-orange">{{ $stats['pending'] }}</p>
            </div>
            <div class="shadow-neu-raised bg-background rounded-xl p-card-padding">
                <p class="text-label-caps font-label-caps text-accent-green mb-1">Diterbitkan</p>
                <p class="text-headline-md font-sans text-accent-green">{{ $stats['published'] }}</p>
            </div>
            <div class="shadow-neu-raised bg-background rounded-xl p-card-padding">
                <p class="text-label-caps font-label-caps text-secondary mb-1">Draf</p>
                <p class="text-headline-md font-sans text-secondary">{{ $stats['draft'] }}</p>
            </div>
        </div>

        {{-- Filter --}}
        <div class="flex gap-2 mb-gutter">
            <a href="{{ route('mading.my') }}"
               class="px-4 py-2 rounded-full text-sm font-bold transition-colors {{ !$status ? 'bg-primary text-on-primary' : 'bg-surface-variant text-secondary hover:text-primary' }}">
                Semua
            </a>
            <a href="{{ route('mading.my', ['status' => 'pending']) }}"
               class="px-4 py-2 rounded-full text-sm font-bold transition-colors {{ $status === 'pending' ? 'bg-accent-orange text-white' : 'bg-surface-variant text-secondary hover:text-primary' }}">
                Menunggu
            </a>
            <a href="{{ route('mading.my', ['status' => 'published']) }}"
               class="px-4 py-2 rounded-full text-sm font-bold transition-colors {{ $status === 'published' ? 'bg-accent-green text-white' : 'bg-surface-variant text-secondary hover:text-primary' }}">
                Diterbitkan
            </a>
            <a href="{{ route('mading.my', ['status' => 'draft']) }}"
               class="px-4 py-2 rounded-full text-sm font-bold transition-colors {{ $status === 'draft' ? 'bg-secondary text-white' : 'bg-surface-variant text-secondary hover:text-primary' }}">
                Draf
            </a>
        </div>

        {{-- Posts list --}}
        <div class="flex flex-col gap-5">
            @forelse ($posts as $post)
                <div class="shadow-neu-raised bg-background rounded-xl p-card-padding relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 {{ $post->accent }} opacity-70"></div>

                    <div class="flex flex-col md:flex-row md:items-center gap-4">
                        {{-- Image thumbnail --}}
                        @if ($post->image)
                            <div class="w-full md:w-32 h-24 rounded-lg overflow-hidden shrink-0">
                                <img src="{{ $post->image }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                            </div>
                        @endif

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <h3 class="text-headline-md font-sans text-primary truncate">{{ $post->title }}</h3>
                            </div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="shadow-neu-inset bg-background rounded-full px-3 py-1 text-[10px] font-label-caps uppercase tracking-wider text-secondary">
                                    {{ $post->category_label }}
                                </span>
                                <span class="px-3 py-1 rounded-full text-xs font-bold
                                    @if ($post->status === 'pending') bg-accent-orange text-white
                                    @elseif ($post->status === 'published') bg-accent-green text-white
                                    @else bg-surface-variant text-secondary
                                    @endif">
                                    {{ $post->status_label }}
                                </span>
                                <span class="text-xs text-secondary">{{ $post->created_at->translatedFormat('d M Y') }}</span>
                            </div>
                            <p class="text-sm text-on-surface-variant mt-2 line-clamp-2">{{ Str::limit($post->content, 150) }}</p>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 shrink-0">
                            @if ($post->status === 'published')
                                <a href="{{ route('posts.show', $post) }}"
                                   class="shadow-neu-raised bg-background rounded-full w-10 h-10 flex items-center justify-center text-secondary hover:text-primary neu-btn">
                                    <x-icon name="visibility" class="text-[20px]" />
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Status info --}}
                    @if ($post->status === 'pending')
                        <div class="mt-3 flex items-center gap-2 text-xs text-accent-orange">
                            <x-icon name="hourglass_top" class="text-[14px]" />
                            <span>Menunggu persetujuan admin</span>
                        </div>
                    @elseif ($post->status === 'draft')
                        <div class="mt-3 flex items-center gap-2 text-xs text-secondary">
                            <x-icon name="edit_document" class="text-[14px]" />
                            <span>Ditolak oleh admin — disimpan sebagai draf</span>
                        </div>
                    @endif
                </div>
            @empty
                <div class="shadow-neu-raised bg-background rounded-xl p-card-padding text-center">
                    <div class="flex flex-col items-center gap-4">
                        <div class="w-16 h-16 rounded-full shadow-neu-inset bg-background flex items-center justify-center">
                            <x-icon name="article" class="text-secondary text-[32px]" />
                        </div>
                        <div>
                            <p class="text-primary font-bold mb-1">Belum ada mading</p>
                            <p class="text-sm text-secondary">Mulai upload mading pertamamu sekarang!</p>
                        </div>
                        <a href="{{ route('mading.upload') }}"
                           class="shadow-neu-raised px-6 py-2 rounded-lg text-on-primary bg-primary font-bold text-sm neu-btn">
                            Upload Mading
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <x-pagination :paginator="$posts" />
    </main>

    <x-footer />
</x-layout>
