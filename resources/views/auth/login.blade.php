<x-layout title="Masuk - MadingBoard">
    <x-navbar :simple="true" :show-search="false" />

    <main class="flex-grow w-full flex items-center justify-center px-container-padding py-container-padding">
        <div class="w-full max-w-md">
            <div class="shadow-neu-raised bg-background rounded-xl p-container-padding relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-2 bg-primary"></div>

                <div class="mb-gutter mt-unit text-center">
                    <h1 class="text-headline-lg font-sans text-primary">Selamat Datang Kembali</h1>
                    <p class="text-secondary mt-2 text-body-md font-sans">Masuk ke akun MadingBoard-mu.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-gutter px-4 py-3 rounded-lg bg-error-container text-on-error-container text-sm font-medium">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-gutter">
                    @csrf

                    <div>
                        <label for="email" class="block text-label-caps font-label-caps text-on-surface-variant mb-unit">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full shadow-neu-inset bg-background rounded-lg px-4 py-3 text-body-md font-sans text-primary placeholder:text-outline focus:shadow-neu-focus focus:outline-none transition-all"
                               placeholder="you@example.com">
                    </div>

                    <div>
                        <label for="password" class="block text-label-caps font-label-caps text-on-surface-variant mb-unit">Kata Sandi</label>
                        <input id="password" type="password" name="password" required
                               class="w-full shadow-neu-inset bg-background rounded-lg px-4 py-3 text-body-md font-sans text-primary placeholder:text-outline focus:shadow-neu-focus focus:outline-none transition-all"
                               placeholder="••••••••">
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="remember" class="sr-only peer">
                            <div class="w-5 h-5 shadow-neu-inset bg-background rounded flex items-center justify-center transition-all peer-checked:bg-primary peer-checked:shadow-none">
                                <svg class="w-3 h-3 text-on-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="ml-2 text-sm text-secondary group-hover:text-primary transition-colors">Ingat saya</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full shadow-neu-raised px-6 py-3 rounded-lg text-on-primary bg-primary font-bold hover:scale-105 transition-transform neu-btn">
                        Masuk
                    </button>
                </form>

                <p class="text-center mt-stack-gap text-secondary text-sm">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="text-primary font-bold hover:underline">Daftar</a>
                </p>
            </div>
        </div>
    </main>

    <x-footer />
</x-layout>
