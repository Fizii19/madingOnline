@php
    $categories = \App\Models\Post::CATEGORY_LABELS;
@endphp

<x-layout title="Upload Mading - MadingBoard">
    <x-navbar :active="'home'" :show-search="false" />

    <main class="flex-grow w-full max-w-[1440px] mx-auto px-container-padding pt-[100px] pb-container-padding">
        <div class="max-w-3xl mx-auto">
            <div class="mb-stack-gap flex items-center gap-unit">
                <a href="{{ route('home') }}" class="text-secondary hover:text-primary transition-colors flex items-center">
                    <x-icon name="arrow_back" class="mr-1 text-[20px]" />
                    Kembali ke Beranda
                </a>
            </div>

            <div class="shadow-neu-raised bg-background rounded-xl p-container-padding relative overflow-hidden">
                {{-- Accent strip --}}
                <div class="absolute top-0 left-0 w-full h-2 bg-accent-green"></div>

                <div class="mb-gutter mt-unit">
                    <h1 class="text-headline-lg font-sans text-primary">Upload Mading</h1>
                    <p class="text-secondary mt-2">Bagikan kontenmu ke MadingBoard. Postingan akan ditinjau oleh admin sebelum tayang.</p>
                </div>

                @if (session('success'))
                    <div class="mb-gutter px-4 py-3 rounded-lg bg-accent-green text-on-surface text-sm font-medium">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-gutter px-4 py-3 rounded-lg bg-error-container text-on-error-container text-sm font-medium">
                        <ul class="list-disc pl-4 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('mading.store') }}" enctype="multipart/form-data" class="space-y-gutter">
                    @csrf

                    {{-- Title --}}
                    <div>
                        <label for="post-title" class="block text-label-caps font-label-caps text-on-surface-variant mb-unit">Judul Postingan</label>
                        <input id="post-title" type="text" name="title" value="{{ old('title') }}" placeholder="Masukkan judul yang deskriptif" required
                               class="w-full shadow-neu-inset bg-background rounded-lg px-4 py-3 text-body-md font-sans text-primary placeholder:text-outline focus:shadow-neu-focus focus:outline-none transition-all">
                    </div>

                    {{-- Category --}}
                    <div>
                        <label for="category" class="block text-label-caps font-label-caps text-on-surface-variant mb-unit">Kategori</label>
                        <div class="relative">
                            <select id="category" name="category" required
                                    class="w-full shadow-neu-inset bg-background rounded-lg px-4 py-3 text-body-md font-sans text-primary appearance-none focus:shadow-neu-focus focus:outline-none transition-all">
                                @foreach ($categories as $value => $label)
                                    <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-icon name="expand_more" class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-secondary" />
                        </div>
                    </div>

                    {{-- Cover image upload --}}
                    <div>
                        <label class="block text-label-caps font-label-caps text-on-surface-variant mb-unit">Gambar Sampul (Opsional)</label>
                        <label class="w-full shadow-neu-inset bg-background rounded-lg border-2 border-dashed border-outline-variant p-8 flex flex-col items-center justify-center text-center cursor-pointer hover:bg-surface-container-low transition-colors group">
                            <div class="w-16 h-16 rounded-full shadow-neu-raised bg-background flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                                <x-icon name="cloud_upload" class="text-secondary text-[32px]" />
                            </div>
                            <p class="text-body-md font-sans text-primary font-medium mb-1">Seret dan letakkan gambar di sini</p>
                            <p class="text-sm text-secondary">atau klik untuk pilih file (JPG, PNG, GIF, WEBP — Maks 5MB)</p>
                            <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp" class="hidden">
                        </label>
                    </div>

                    {{-- Content --}}
                    <div>
                        <label for="content" class="block text-label-caps font-label-caps text-on-surface-variant mb-unit">Isi Konten</label>
                        <textarea id="content" name="content" rows="8" placeholder="Tulis isi kontenmu di sini..." required
                                  class="w-full shadow-neu-inset bg-background rounded-lg px-4 py-3 text-body-md font-sans text-primary placeholder:text-outline focus:shadow-neu-focus focus:outline-none resize-y transition-all">{{ old('content') }}</textarea>
                    </div>

                    {{-- Info box --}}
                    <div class="flex items-start gap-3 p-4 rounded-lg bg-[#e0f2fe] text-sm">
                        <x-icon name="info" class="text-primary text-[20px] mt-0.5 shrink-0" />
                        <p class="text-on-surface">Postinganmu akan masuk ke status <strong>Menunggu Persetujuan</strong> dan akan ditinjau oleh admin sebelum tayang di beranda.</p>
                    </div>

                    {{-- Actions --}}
                    <div class="pt-stack-gap flex flex-col sm:flex-row justify-end gap-stack-gap border-t border-outline-variant/30 mt-gutter">
                        <a href="{{ route('home') }}"
                           class="shadow-neu-raised bg-background px-8 py-3 rounded-lg text-primary font-bold hover:scale-105 transition-transform text-center neu-btn">
                            Batal
                        </a>
                        <button type="submit" class="shadow-neu-raised px-8 py-3 rounded-lg text-on-primary bg-primary font-bold hover:scale-105 transition-transform text-center neu-btn">
                            Kirim Mading
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <x-footer />
</x-layout>
