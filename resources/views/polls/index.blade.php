<x-layout title="MadingBoard - Polling Cepat">
    <x-navbar :active="'polls'" :show-search="false" />

    <main class="flex-grow w-full max-w-[1440px] mx-auto px-container-padding pt-[100px] pb-container-padding flex flex-col gap-stack-gap">
        <header>
            <h1 class="text-display font-sans text-primary">Polling Cepat</h1>
            <p class="text-secondary mt-unit text-body-lg font-sans">Buat, aktifkan, dan hapus poll yang tampil di beranda.</p>
        </header>

        @if (session('success'))
            <div class="px-4 py-3 rounded-lg bg-accent-green text-on-surface text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="px-4 py-3 rounded-lg bg-error-container text-on-error-container text-sm font-medium">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-start">
            {{-- Create form --}}
            <section class="lg:col-span-5 shadow-neu-raised bg-background rounded-xl p-container-padding relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-2 bg-[#c7ceea]"></div>

                <h2 class="text-headline-md font-sans text-primary mb-gutter mt-unit">Buat Poll Baru</h2>

                <form method="POST" action="{{ route('polls.store') }}" class="space-y-gutter" id="poll-form">
                    @csrf

                    <div>
                        <label for="question" class="block text-label-caps font-label-caps text-on-surface-variant mb-unit">Pertanyaan</label>
                        <input id="question" type="text" name="question" value="{{ old('question') }}" required maxlength="255"
                               placeholder="Contoh: Makanan apa yang kamu inginkan di kantin?"
                               class="w-full shadow-neu-inset bg-background rounded-lg px-4 py-3 text-body-md font-sans text-primary placeholder:text-outline focus:shadow-neu-focus focus:outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-label-caps font-label-caps text-on-surface-variant mb-unit">Pilihan (min 2, maks {{ \App\Models\Poll::MAX_OPTIONS }})</label>
                        <div id="options-container" class="space-y-unit">
                            @php
                                $oldOptions = old('options', ['', '']);
                            @endphp
                            @foreach ($oldOptions as $i => $value)
                                <input type="text" name="options[]" value="{{ $value }}" required maxlength="100"
                                       placeholder="Pilihan {{ $i + 1 }}"
                                       class="option-input w-full shadow-neu-inset bg-background rounded-lg px-4 py-3 text-body-md font-sans text-primary placeholder:text-outline focus:shadow-neu-focus focus:outline-none transition-all">
                            @endforeach
                        </div>
                        <button type="button" id="add-option"
                                class="mt-unit text-primary font-bold text-sm hover:underline flex items-center gap-1">
                            <x-icon name="add" class="text-[18px]" /> Tambah Pilihan
                        </button>
                    </div>

                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                        <div class="w-5 h-5 shadow-neu-inset bg-background rounded flex items-center justify-center transition-all peer-checked:bg-primary peer-checked:shadow-none">
                            <svg class="w-3 h-3 text-on-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="ml-2 text-body-md font-sans text-primary group-hover:text-secondary transition-colors">Aktifkan sekarang</span>
                    </label>

                    <button type="submit" class="w-full shadow-neu-raised px-6 py-3 rounded-lg text-on-primary bg-primary font-bold hover:scale-105 transition-transform neu-btn">
                        Simpan Poll
                    </button>
                </form>
            </section>

            {{-- Poll list --}}
            <section class="lg:col-span-7 flex flex-col gap-stack-gap">
                @forelse ($polls as $poll)
                    <div class="shadow-neu-raised bg-background rounded-xl p-card-padding relative overflow-hidden">
                        <div class="flex flex-wrap items-start justify-between gap-gutter">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="px-2 py-0.5 rounded-full text-label-caps font-label-caps font-bold {{ $poll->is_active ? 'bg-accent-green text-on-surface' : 'bg-surface-container-high text-outline' }}">
                                        {{ $poll->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                    <span class="text-xs text-secondary">{{ $poll->created_at->diffForHumans() }}</span>
                                </div>
                                <h3 class="text-headline-md font-sans text-primary">{{ $poll->question }}</h3>
                                <p class="text-sm text-secondary mt-unit">{{ $poll->votes_count }} total suara</p>

                                <ul class="mt-gutter space-y-2">
                                    @php $total = $poll->votes_count; @endphp
                                    @foreach ($poll->options as $option)
                                        @php
                                            $count = $poll->voteCountFor($option);
                                            $pct = $total > 0 ? round($count / $total * 100) : 0;
                                        @endphp
                                        <li class="flex items-center gap-3">
                                            <span class="text-body-md font-sans text-primary w-40 truncate">{{ $option }}</span>
                                            <div class="flex-1 h-2.5 rounded-full shadow-neu-inset bg-background overflow-hidden">
                                                <div class="h-full rounded-full bg-primary transition-all" style="width: {{ $pct }}%"></div>
                                            </div>
                                            <span class="text-xs text-secondary w-16 text-right">{{ $count }} · {{ $pct }}%</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="flex items-center gap-unit">
                                <form method="POST" action="{{ route('polls.toggle', $poll) }}">
                                    @csrf
                                    <button type="submit"
                                            class="shadow-neu-raised bg-background px-4 py-2 rounded-lg text-primary font-bold text-sm neu-btn">
                                        {{ $poll->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('polls.destroy', $poll) }}"
                                      onsubmit="return confirm('Hapus poll ini beserta semua suaranya?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" aria-label="Hapus poll"
                                            class="shadow-neu-raised bg-background w-10 h-10 rounded-lg flex items-center justify-center text-error neu-btn">
                                        <x-icon name="delete" />
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="shadow-neu-raised bg-background rounded-xl p-card-padding text-center text-secondary">
                        Belum ada poll. Buat poll pertama lewat form di samping.
                    </div>
                @endforelse
            </section>
        </div>
    </main>

    <script>
        // Add more option inputs (up to the max allowed).
        (function () {
            const container = document.getElementById('options-container');
            const max = {{ \App\Models\Poll::MAX_OPTIONS }};
            const addBtn = document.getElementById('add-option');
            const template = container.querySelector('.option-input');

            function refresh() {
                const count = container.querySelectorAll('.option-input').length;
                addBtn.style.display = count >= max ? 'none' : '';
                container.querySelectorAll('.option-input').forEach((input, i) => {
                    input.placeholder = 'Pilihan ' + (i + 1);
                });
            }

            addBtn.addEventListener('click', () => {
                if (container.querySelectorAll('.option-input').length >= max) return;
                const clone = template.cloneNode();
                clone.value = '';
                container.appendChild(clone);
                refresh();
            });

            refresh();
        })();
    </script>

    <x-footer />
</x-layout>
