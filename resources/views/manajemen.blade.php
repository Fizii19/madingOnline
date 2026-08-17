<x-layout title="MadingBoard - Manajemen Konten">
    <x-navbar :active="'management'" :show-search="true" />

    <main class="flex-grow w-full max-w-[1440px] mx-auto px-container-padding pt-[100px] pb-container-padding flex flex-col gap-stack-gap">
        <header class="flex justify-between items-end mb-gutter">
            <div>
                <h1 class="text-display font-sans text-primary">Manajemen Konten</h1>
                <p class="text-secondary mt-unit text-body-lg font-sans">Kelola, edit, dan atur semua postingan bulletin.</p>
            </div>
            <a href="{{ route('posts.create') }}"
               class="shadow-neu-raised rounded-lg px-8 py-4 flex items-center gap-2 bg-[#e0f2fe] text-primary neu-btn">
                <x-icon name="add" />
                <span class="text-label-caps font-label-caps font-bold">Tambah Postingan</span>
            </a>
        </header>

        @if (session('success'))
            <div class="px-4 py-3 rounded-lg bg-accent-green text-on-surface text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        {{-- Search & filters --}}
        <form method="GET" action="{{ route('management') }}" class="flex flex-col md:flex-row gap-gutter items-end">
            <div class="relative flex-1 w-full">
                <x-icon name="search" class="absolute left-4 top-1/2 -translate-y-1/2 text-secondary" />
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari judul, isi, atau penulis..."
                       class="w-full shadow-neu-inset bg-background rounded-full pl-11 pr-4 py-2.5 text-body-md font-sans text-primary placeholder:text-outline focus:shadow-neu-focus focus:outline-none transition-all">
            </div>

            <div class="relative w-full md:w-48">
                <select name="category"
                        class="w-full shadow-neu-inset bg-background rounded-full px-4 py-2.5 text-body-md font-sans text-primary appearance-none focus:shadow-neu-focus focus:outline-none transition-all">
                    <option value="">Semua Kategori</option>
                    @foreach (\App\Models\Post::CATEGORIES as $value)
                        <option value="{{ $value }}" @selected($category === $value)>{{ ucfirst($value) }}</option>
                    @endforeach
                </select>
                <x-icon name="expand_more" class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-secondary" />
            </div>

            <div class="relative w-full md:w-44">
                <select name="status"
                        class="w-full shadow-neu-inset bg-background rounded-full px-4 py-2.5 text-body-md font-sans text-primary appearance-none focus:shadow-neu-focus focus:outline-none transition-all">
                    <option value="">Semua Status</option>
                    @foreach (\App\Models\Post::STATUSES as $value)
                        <option value="{{ $value }}" @selected($status === $value)>{{ \App\Models\Post::STATUS_LABELS[$value] }}</option>
                    @endforeach
                </select>
                <x-icon name="expand_more" class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-secondary" />
            </div>

            <button type="submit" class="shadow-neu-raised bg-background px-6 py-2.5 rounded-full text-primary font-bold text-sm neu-btn">
                Filter
            </button>

            @if ($search !== '' || $category || $status)
                <a href="{{ route('management') }}" class="text-primary font-bold text-sm hover:underline">Atur Ulang</a>
            @endif
        </form>

        <div class="flex flex-col gap-stack-gap">
            {{-- List header --}}
            <div class="hidden md:grid grid-cols-12 gap-gutter px-card-padding py-unit text-label-caps font-label-caps text-outline border-b-2 border-surface-container-high">
                <div class="col-span-5">Judul &amp; Kategori</div>
                <div class="col-span-2">Penulis</div>
                <div class="col-span-2">Tanggal</div>
                <div class="col-span-3 text-right">Aksi</div>
            </div>

            @forelse ($posts as $post)
                <x-post-row :title="$post->title"
                            :category="$post->category_label"
                            :pinned="$post->is_pinned"
                            :author="$post->author->name"
                            :date="$post->created_at->translatedFormat('d M Y')"
                            :accent="$post->accent"
                            :view-url="route('posts.show', $post)"
                            :edit-url="route('posts.edit', $post)"
                            :delete-url="route('posts.destroy', $post)" />
            @empty
                <div class="shadow-neu-raised bg-background rounded-xl p-card-padding text-center text-secondary">
                    Belum ada postingan. Klik "Add New Post" untuk membuat yang pertama.
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <x-pagination :paginator="$posts" />
    </main>

    <x-footer />
</x-layout>
