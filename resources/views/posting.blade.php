@php
    $isEdit = isset($post) && $post !== null;
    $categories = \App\Models\Post::CATEGORY_LABELS;
    $statuses = \App\Models\Post::STATUS_LABELS;
@endphp

<x-layout :title="($isEdit ? 'Edit Postingan' : 'Tambah Postingan').' - MadingBoard'">
    <x-navbar :active="'management'" :show-search="true" />

    <main class="flex-grow w-full max-w-[1440px] mx-auto px-container-padding pt-[100px] pb-container-padding">
        <div class="max-w-3xl mx-auto">
            <div class="mb-stack-gap flex items-center gap-unit">
                <a href="{{ route('management') }}" class="text-secondary hover:text-primary transition-colors flex items-center">
                    <x-icon name="arrow_back" class="mr-1 text-[20px]" />
                    Kembali ke Manajemen
                </a>
            </div>

            <div class="shadow-neu-raised bg-background rounded-xl p-container-padding relative overflow-hidden">
                {{-- Accent strip --}}
                <div class="absolute top-0 left-0 w-full h-2 bg-accent-blue"></div>

                <div class="mb-gutter mt-unit">
                    <h1 class="text-headline-lg font-sans text-primary">{{ $isEdit ? 'Edit Postingan' : 'Tambah Postingan' }}</h1>
                    <p class="text-secondary mt-2">{{ $isEdit ? 'Perbarui postingan bulletin di MadingBoard.' : 'Buat bulletin baru untuk MadingBoard.' }}</p>
                </div>

                @if ($errors->any())
                    <div class="mb-gutter px-4 py-3 rounded-lg bg-error-container text-on-error-container text-sm font-medium">
                        <ul class="list-disc pl-4 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ $isEdit ? route('posts.update', $post) : route('posts.store') }}" enctype="multipart/form-data" class="space-y-gutter">
                    @csrf
                    @if ($isEdit)
                        @method('PUT')
                    @endif

                    {{-- Title --}}
                    <div>
                        <label for="post-title" class="block text-label-caps font-label-caps text-on-surface-variant mb-unit">Judul Postingan</label>
                        <input id="post-title" type="text" name="title" value="{{ old('title', $isEdit ? $post->title : '') }}" placeholder="Masukkan judul yang deskriptif" required
                               class="w-full shadow-neu-inset bg-background rounded-lg px-4 py-3 text-body-md font-sans text-primary placeholder:text-outline focus:shadow-neu-focus focus:outline-none transition-all">
                    </div>

                    {{-- Category & status --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-gutter">
                        <div>
                            <label for="category" class="block text-label-caps font-label-caps text-on-surface-variant mb-unit">Kategori</label>
                            <div class="relative">
                                <select id="category" name="category" required
                                        class="w-full shadow-neu-inset bg-background rounded-lg px-4 py-3 text-body-md font-sans text-primary appearance-none focus:shadow-neu-focus focus:outline-none transition-all">
                                    @foreach ($categories as $value => $label)
                                        <option value="{{ $value }}" @selected(old('category', $isEdit ? $post->category : '') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-icon name="expand_more" class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-secondary" />
                            </div>
                        </div>

                        <div>
                            <label for="status" class="block text-label-caps font-label-caps text-on-surface-variant mb-unit">Status</label>
                            <div class="relative">
                                <select id="status" name="status" required
                                        class="w-full shadow-neu-inset bg-background rounded-lg px-4 py-3 text-body-md font-sans text-primary appearance-none focus:shadow-neu-focus focus:outline-none transition-all">
                                    @foreach ($statuses as $value => $label)
                                        <option value="{{ $value }}" @selected(old('status', $isEdit ? $post->status : 'published') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-icon name="expand_more" class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-secondary" />
                            </div>
                        </div>
                    </div>

                    {{-- Pin to top & image url fallback --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-gutter items-end">
                        <div>
                            <label class="block text-label-caps font-label-caps text-on-surface-variant mb-unit">Sematkan di Atas</label>
                            <label class="flex items-center cursor-pointer group mt-3">
                                <div class="relative">
                                    <input type="checkbox" name="is_pinned" value="1" @checked(old('is_pinned', $isEdit ? $post->is_pinned : false)) class="sr-only peer">
                                    <div class="w-6 h-6 shadow-neu-inset bg-background rounded flex items-center justify-center transition-all peer-checked:bg-primary peer-checked:shadow-none">
                                        <svg class="w-4 h-4 text-on-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                </div>
                                <span class="ml-3 text-body-md font-sans text-primary group-hover:text-secondary transition-colors">Sematkan postingan ini</span>
                            </label>
                        </div>

                        <div>
                            <label for="image_url" class="block text-label-caps font-label-caps text-on-surface-variant mb-unit">...atau pakai URL gambar</label>
                            <input id="image_url" type="url" name="image_url" value="{{ old('image_url', $isEdit ? $post->image_url : '') }}" placeholder="https://example.com/image.jpg"
                                   class="w-full shadow-neu-inset bg-background rounded-lg px-4 py-3 text-body-md font-sans text-primary placeholder:text-outline focus:shadow-neu-focus focus:outline-none transition-all">
                        </div>
                    </div>

                    {{-- Cover image upload --}}
                    <div>
                        <label class="block text-label-caps font-label-caps text-on-surface-variant mb-unit">Gambar Sampul (Opsional)</label>

                        @if ($isEdit && $post->image)
                            <img src="{{ $post->image }}" alt="Preview cover saat ini"
                                 class="w-full max-h-48 object-cover rounded-lg shadow-neu-raised mb-unit">
                        @endif

                        <label class="w-full shadow-neu-inset bg-background rounded-lg border-2 border-dashed border-outline-variant p-8 flex flex-col items-center justify-center text-center cursor-pointer hover:bg-surface-container-low transition-colors group">
                            <div class="w-16 h-16 rounded-full shadow-neu-raised bg-background flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                                <x-icon name="cloud_upload" class="text-secondary text-[32px]" />
                            </div>
                            <p class="text-body-md font-sans text-primary font-medium mb-1">Seret dan letakkan gambar di sini</p>
                            <p class="text-sm text-secondary">atau klik untuk pilih file (JPG, PNG, GIF, WEBP — Maks 5MB)</p>
                            <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp" class="hidden">
                        </label>

                        @if ($isEdit && $post->image)
                            <p class="text-xs text-secondary mt-unit">Upload gambar baru untuk mengganti cover saat ini.</p>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div>
                        <label for="content" class="block text-label-caps font-label-caps text-on-surface-variant mb-unit">Isi Konten</label>
                        <textarea id="content" name="content" rows="8" placeholder="Tulis isi kontenmu di sini..." required
                                  class="w-full shadow-neu-inset bg-background rounded-lg px-4 py-3 text-body-md font-sans text-primary placeholder:text-outline focus:shadow-neu-focus focus:outline-none resize-y transition-all">{{ old('content', $isEdit ? $post->content : '') }}</textarea>
                    </div>

                    {{-- Actions --}}
                    <div class="pt-stack-gap flex flex-col sm:flex-row justify-end gap-stack-gap border-t border-outline-variant/30 mt-gutter">
                        <a href="{{ route('management') }}"
                           class="shadow-neu-raised bg-background px-8 py-3 rounded-lg text-primary font-bold hover:scale-105 transition-transform text-center neu-btn">
                            Batal
                        </a>
                        <button type="submit" class="shadow-neu-raised px-8 py-3 rounded-lg text-on-primary bg-primary font-bold hover:scale-105 transition-transform text-center neu-btn">
                            {{ $isEdit ? 'Perbarui Postingan' : 'Simpan Postingan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <x-footer />
</x-layout>
