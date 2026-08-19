<x-layout title="Digital Mading - Home">
    <x-navbar :active="'home'" :show-search="false" />

    <main class="flex-grow pt-[120px] pb-container-padding px-container-padding max-w-[1440px] mx-auto w-full">
        <header class="mb-container-padding text-center md:text-left">
            <h1 class="text-display font-sans text-primary mb-unit">Bulletin Terbaru</h1>
            <p class="text-body-lg font-sans text-secondary max-w-2xl">
                Temukan pengumuman, acara, dan pembaruan terbaru dari papan komunitas.
            </p>

            {{-- Search & category filter --}}
            <form method="GET" action="{{ route('home') }}" class="mt-gutter flex flex-col md:flex-row gap-gutter items-end max-w-5xl mx-auto">
                <div class="relative flex-1 w-full">
                    <x-icon name="search" class="absolute left-4 top-1/2 -translate-y-1/2 text-secondary" />
                    <input type="text" name="q" value="{{ $search }}" placeholder="Cari bulletin..."
                           class="w-full shadow-neu-inset bg-background rounded-full pl-11 pr-4 py-2.5 text-body-md font-sans text-primary placeholder:text-outline focus:shadow-neu-focus focus:outline-none transition-all">
                </div>

                <div class="relative w-full md:w-48">
                    <select name="category"
                            class="w-full shadow-neu-inset bg-background rounded-full px-4 py-2.5 text-body-md font-sans text-primary appearance-none focus:shadow-neu-focus focus:outline-none transition-all">
                        <option value="">Semua Kategori</option>
                        @foreach (\App\Models\Post::CATEGORIES as $value)
                            <option value="{{ $value }}" @selected($category === $value)>{{ \App\Models\Post::CATEGORY_LABELS[$value] }}</option>
                        @endforeach
                    </select>
                    <x-icon name="expand_more" class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-secondary" />
                </div>

                <button type="submit" class="shadow-neu-raised bg-background px-6 py-2.5 rounded-full text-primary font-bold text-sm neu-btn">
                    Cari
                </button>
            </form>

            @if ($hasFilter)
                <p class="mt-unit text-sm text-secondary">
                    Menampilkan {{ $totalResults }} hasil
                    @if ($search !== '')
                        untuk "{{ $search }}"
                    @endif
                    <a href="{{ route('home') }}" class="text-primary font-bold hover:underline">Atur Ulang</a>
                </p>
            @endif
        </header>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-gutter">
            @if (! $featured && $posts->isEmpty())
                <div class="md:col-span-12 shadow-neu-raised bg-background rounded-xl p-card-padding text-center text-secondary">
                    Tidak ada postingan yang cocok dengan pencarianmu.
                </div>
            @else
            @if ($featured)
                {{-- Featured card: pinned / newest post --}}
                <x-bulletin-card class="md:col-span-8"
                                 :accent="$featured->accent"
                                 :pinned="$featured->is_pinned"
                                 :image="$featured->image"
                                 :image-alt="$featured->title"
                                 image-height="h-64"
                                 :tags="[strtoupper($featured->category_label)]"
                                 :title="$featured->title"
                                 title-size="text-headline-lg"
                                 :excerpt="Str::limit($featured->content, 220)">
                    <x-slot:footer>
                        <span class="text-secondary text-label-caps font-label-caps flex items-center gap-1">
                            <x-icon name="calendar_today" class="text-[16px]" /> {{ $featured->created_at->translatedFormat('d M Y') }}
                        </span>
                        <a href="{{ route('posts.show', $featured) }}"                            class="neu-btn bg-surface text-primary font-bold px-6 py-2 rounded-lg text-body-md font-sans border border-surface-container-high transition-all">
                            Baca Selengkapnya
                        </a>
                    </x-slot:footer>
                </x-bulletin-card>
            @endif

            {{-- Standard cards: other recent published posts --}}
            @foreach ($posts as $post)
                <x-bulletin-card class="md:col-span-4"
                                 :accent="$post->accent"
                                 :pinned="$post->is_pinned"
                                 :image="$post->image"
                                 :image-alt="$post->title"
                                 :tags="[strtoupper($post->category_label)]"
                                 :title="$post->title"
                                 :excerpt="Str::limit($post->content, 120)">
                    <x-slot:footer>
                        <span class="text-secondary text-label-caps font-label-caps flex items-center gap-1">
                            <x-icon name="schedule" class="text-[16px]" /> {{ $post->created_at->diffForHumans() }}
                        </span>
                        <a href="{{ route('posts.show', $post) }}" aria-label="Read more"
                           class="neu-btn bg-surface text-primary font-bold p-2 rounded-full flex items-center justify-center transition-all">
                            <x-icon name="arrow_forward" />
                        </a>
                    </x-slot:footer>
                </x-bulletin-card>
            @endforeach
            @endif
        </div>

        @unless ($hasFilter)
            {{-- Quick poll from the database (managed by admin) --}}
            @if ($poll)
                <div class="grid grid-cols-1 md:grid-cols-12 gap-gutter mt-gutter">
                    <x-poll-card :poll="$poll"
                                 :user-vote="$poll->userVote(auth()->user())"
                                 :total-votes="$poll->totalVotes()" />
                </div>
            @endif
        @endunless

        <x-pagination :paginator="$paginator" />
    </main>

    <x-footer />
</x-layout>
