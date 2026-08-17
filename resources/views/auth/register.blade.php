<x-layout title="Daftar - MadingBoard">
    <x-navbar :simple="true" :show-search="false" />

    <main class="flex-grow w-full flex items-center justify-center px-container-padding py-container-padding">
        <div class="w-full max-w-md">
            <div class="shadow-neu-raised bg-background rounded-xl p-container-padding relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-2 bg-accent-green"></div>

                <div class="mb-gutter mt-unit text-center">
                    <h1 class="text-headline-lg font-sans text-primary">Buat Akun</h1>
                    <p class="text-secondary mt-2 text-body-md font-sans">Bergabunglah dengan komunitas MadingBoard.</p>
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

                <form method="POST" action="{{ route('register') }}" class="space-y-gutter">
                    @csrf

                    <div>
                        <label for="name" class="block text-label-caps font-label-caps text-on-surface-variant mb-unit">Nama</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                               class="w-full shadow-neu-inset bg-background rounded-lg px-4 py-3 text-body-md font-sans text-primary placeholder:text-outline focus:shadow-neu-focus focus:outline-none transition-all"
                               placeholder="Nama kamu">
                    </div>

                    <div>
                        <label for="email" class="block text-label-caps font-label-caps text-on-surface-variant mb-unit">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                               class="w-full shadow-neu-inset bg-background rounded-lg px-4 py-3 text-body-md font-sans text-primary placeholder:text-outline focus:shadow-neu-focus focus:outline-none transition-all"
                               placeholder="you@example.com">
                    </div>

                    <div>
                        <label for="password" class="block text-label-caps font-label-caps text-on-surface-variant mb-unit">Kata Sandi</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                               class="w-full shadow-neu-inset bg-background rounded-lg px-4 py-3 text-body-md font-sans text-primary placeholder:text-outline focus:shadow-neu-focus focus:outline-none transition-all"
                               placeholder="Minimal 8 karakter">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-label-caps font-label-caps text-on-surface-variant mb-unit">Konfirmasi Kata Sandi</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                               class="w-full shadow-neu-inset bg-background rounded-lg px-4 py-3 text-body-md font-sans text-primary placeholder:text-outline focus:shadow-neu-focus focus:outline-none transition-all"
                               placeholder="Ulangi kata sandimu">
                    </div>

                    <button type="submit" class="w-full shadow-neu-raised px-6 py-3 rounded-lg text-on-primary bg-primary font-bold hover:scale-105 transition-transform neu-btn">
                        Daftar
                    </button>
                </form>

                <p class="text-center mt-stack-gap text-secondary text-sm">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-primary font-bold hover:underline">Masuk</a>
                </p>
            </div>
        </div>
    </main>

    <x-footer />
</x-layout>
