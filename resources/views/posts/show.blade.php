<x-layout :title="$post->title.' - MadingBoard'">
    <x-navbar :active="'home'" :show-search="true" />

    <main class="flex-grow pt-[100px] pb-container-padding px-container-padding max-w-[1440px] mx-auto w-full">
        <div class="max-w-4xl mx-auto">
            <div class="mb-stack-gap flex items-center gap-unit">
                <a href="{{ route('home') }}" class="text-secondary hover:text-primary transition-colors flex items-center">
                    <x-icon name="arrow_back" class="mr-1 text-[20px]" />
                    Kembali ke Bulletin
                </a>
            </div>

            <article class="shadow-neu-raised bg-background rounded-xl overflow-hidden relative">
                <div class="h-2 w-full {{ $post->accent }} absolute top-0 left-0"></div>

                @if ($post->image)
                    <div class="h-72 w-full relative">
                        <img src="{{ $post->image }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                    </div>
                @endif

                <div class="p-container-padding">
                    <div class="flex gap-2 mb-4 flex-wrap">
                        <span class="bg-surface-variant text-primary text-label-caps font-label-caps px-3 py-1 rounded-full shadow-neu-inset uppercase">{{ strtoupper($post->category_label) }}</span>
                        @if ($post->is_pinned)
                            <span class="bg-surface-variant text-primary text-label-caps font-label-caps px-3 py-1 rounded-full shadow-neu-inset flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-[#fca5a5]"></span> Disematkan
                            </span>
                        @endif
                    </div>

                    <h1 class="text-display font-sans text-primary mb-4">{{ $post->title }}</h1>

                    <div class="flex items-center gap-4 mb-gutter text-secondary text-sm flex-wrap">
                        <span class="flex items-center gap-1">
                            <x-icon name="person" class="text-[18px]" />
                            {{ $post->author->name }}
                        </span>
                        <span class="flex items-center gap-1">
                            <x-icon name="calendar_today" class="text-[18px]" />
                            {{ $post->created_at->translatedFormat('d M Y') }}
                        </span>
                        <span class="flex items-center gap-1">
                            <x-icon name="visibility" class="text-[18px]" />
                            {{ number_format($post->views) }} tayangan
                        </span>

                        <form method="POST" action="{{ route('posts.like', $post) }}">
                            @csrf
                            <button type="submit"
                                    class="flex items-center gap-1 {{ $liked ? 'text-error' : 'text-secondary' }} hover:text-error transition-colors neu-btn">
                                <x-icon name="favorite" :filled="$liked" class="text-[18px]" />
                                {{ $likesCount }} Suka
                            </button>
                        </form>
                    </div>

                    <div class="text-body-lg font-sans text-on-surface leading-relaxed whitespace-pre-line">
                        {{ $post->content }}
                    </div>
                </div>
            </article>

            {{-- Comments --}}
            <div class="mt-gutter shadow-neu-raised bg-background rounded-xl p-card-padding">
                <h2 class="text-headline-md font-sans text-primary mb-6">Komentar ({{ $post->comments->count() }})</h2>

                @auth
                    <form method="POST" action="{{ route('posts.comments.store', $post) }}" class="mb-gutter">
                        @csrf
                        <label for="comment-body" class="block text-label-caps font-label-caps text-on-surface-variant mb-unit">Tulis komentar</label>
                        <textarea id="comment-body" name="body" rows="3" required placeholder="Bagikan pendapatmu..."
                                  class="w-full shadow-neu-inset bg-background rounded-lg px-4 py-3 text-body-md font-sans text-primary placeholder:text-outline focus:shadow-neu-focus focus:outline-none transition-all resize-y">{{ old('body') }}</textarea>

                        @error('body')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror

                        <div class="flex justify-end mt-unit">
                            <button type="submit" class="shadow-neu-raised px-6 py-2 rounded-lg text-on-primary bg-primary font-bold text-sm neu-btn">
                                Kirim Komentar
                            </button>
                        </div>
                    </form>
                @else
                    <p class="mb-gutter text-secondary text-sm">
                        <a href="{{ route('login') }}" class="text-primary font-bold hover:underline">Masuk</a> untuk memberi komentar &amp; suka.
                    </p>
                @endauth

                <div class="flex flex-col gap-5">
                    @forelse ($post->comments as $comment)
                        <div class="flex gap-3 items-start">
                            <div class="w-10 h-10 rounded-full shadow-neu-inset bg-background flex items-center justify-center shrink-0 text-primary font-bold">
                                {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-primary text-sm">{{ $comment->user->name }}</span>
                                        <span class="text-xs text-secondary">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>

                                    @can('delete', $comment)
                                        <form method="POST" action="{{ route('posts.comments.destroy', [$post, $comment]) }}"
                                              onsubmit="return confirm('Hapus komentar ini? Tindakan tidak bisa dibatalkan.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" aria-label="Hapus komentar"
                                                    class="text-secondary hover:text-error transition-colors">
                                                <x-icon name="delete" class="text-[18px]" />
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                                <p class="text-body-md font-sans text-on-surface mt-1 whitespace-pre-line">{{ $comment->body }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-secondary text-sm">Belum ada komentar. Jadilah yang pertama!</p>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    <x-footer />
</x-layout>
