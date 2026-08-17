@props([
    'poll',
    'userVote' => null,   // \App\Models\PollVote|null — the current user's vote
    'totalVotes' => 0,
])

<article class="md:col-span-4 bg-surface rounded-xl shadow-neu-raised p-card-padding flex flex-col relative justify-center">
    <div class="h-2 w-full bg-[#c7ceea] absolute top-0 left-0"></div>

    <h2 class="text-headline-md font-sans text-primary mb-4 text-center">Polling Cepat</h2>
    <p class="text-body-md font-sans text-on-surface-variant mb-6 text-center">{{ $poll->question }}</p>

    @guest
        {{-- Visitors: see the options but must log in to vote --}}
        <div class="space-y-3">
            @foreach ($poll->options as $option)
                <div class="flex items-center gap-3 p-3 rounded-lg shadow-neu-raised bg-background">
                    <span class="w-5 h-5 rounded-full shadow-neu-inset bg-background shrink-0"></span>
                    <span class="text-body-md font-sans text-primary">{{ $option }}</span>
                </div>
            @endforeach
        </div>

        <p class="mt-6 text-sm text-secondary text-center">
            <a href="{{ route('login') }}" class="text-primary font-bold hover:underline">Masuk</a>
            untuk ikut vote
        </p>
    @else
        @if ($userVote)
            {{-- Already voted: show results --}}
            <div id="poll-result-{{ $poll->id }}" class="space-y-3">
                @foreach ($poll->options as $option)
                    @php
                        $count = $poll->voteCountFor($option);
                        $pct = $totalVotes > 0 ? round($count / $totalVotes * 100) : 0;
                        $isMine = $userVote->option === $option;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="{{ $isMine ? 'text-primary font-bold' : 'text-secondary' }}">
                                {{ $option }}
                                @if ($isMine)
                                    <span class="ml-1 text-xs font-bold text-accent-blue">(pilihanmu)</span>
                                @endif
                            </span>
                            <span class="text-secondary">{{ $count }} · {{ $pct }}%</span>
                        </div>
                        <div class="h-2.5 rounded-full shadow-neu-inset bg-background overflow-hidden">
                            <div class="h-full rounded-full {{ $isMine ? 'bg-accent-blue' : 'bg-primary' }} transition-all"
                                 style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach

                <p class="text-sm text-secondary text-center pt-2">{{ $totalVotes }} total suara</p>

                <button type="button" onclick="togglePollForm({{ $poll->id }})"
                        class="mt-1 w-full text-sm text-primary font-bold hover:underline text-center">
                    Ubah Pilihan
                </button>
            </div>

            {{-- Change-vote form (hidden until "Ubah Pilihan" is clicked) --}}
            <form id="poll-form-{{ $poll->id }}" method="POST" action="{{ route('polls.vote', $poll) }}" class="hidden space-y-3">
                @csrf
                @foreach ($poll->options as $option)
                    <label class="flex items-center gap-3 p-3 rounded-lg shadow-neu-raised bg-background cursor-pointer hover:bg-surface-container-low transition-colors">
                        <input type="radio" name="option" value="{{ $option }}" @checked($userVote->option === $option)
                               class="w-5 h-5 text-primary border-outline focus:ring-primary bg-transparent shadow-neu-inset">
                        <span class="text-body-md font-sans text-primary">{{ $option }}</span>
                    </label>
                @endforeach
                <div class="flex gap-2">
                    <button type="submit"
                            class="flex-1 shadow-neu-raised bg-background text-primary font-bold px-4 py-2 rounded-lg text-body-md font-sans neu-btn">
                        Simpan Pilihan
                    </button>
                    <button type="button" onclick="togglePollForm({{ $poll->id }})"
                            class="shadow-neu-inset bg-background text-secondary px-4 py-2 rounded-lg text-body-md font-sans">
                        Batal
                    </button>
                </div>
            </form>
        @else
            {{-- Not voted yet: show the vote form --}}
            <form method="POST" action="{{ route('polls.vote', $poll) }}" class="space-y-3">
                @csrf
                @foreach ($poll->options as $option)
                    <label class="flex items-center gap-3 p-3 rounded-lg shadow-neu-raised bg-background cursor-pointer hover:bg-surface-container-low transition-colors">
                        <input type="radio" name="option" value="{{ $option }}" required
                               class="w-5 h-5 text-primary border-outline focus:ring-primary bg-transparent shadow-neu-inset">
                        <span class="text-body-md font-sans text-primary">{{ $option }}</span>
                    </label>
                @endforeach

                <button type="submit"
                        class="mt-3 w-full shadow-neu-raised bg-background text-primary font-bold px-4 py-2 rounded-lg text-body-md font-sans neu-btn">
                    Kirim Suara
                </button>
            </form>
        @endif
    @endguest

    <script>
        function togglePollForm(id) {
            const form = document.getElementById('poll-form-' + id);
            const result = document.getElementById('poll-result-' + id);
            if (!form || !result) return;
            const showForm = form.classList.contains('hidden');
            form.classList.toggle('hidden', !showForm);
            result.classList.toggle('hidden', showForm);
        }
    </script>
</article>
