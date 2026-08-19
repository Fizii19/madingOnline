<x-layout title="MadingBoard - Laporan Komentar">
    <x-navbar :active="'reports'" :show-search="false" />

    <main class="flex-grow w-full max-w-[1440px] mx-auto px-container-padding pt-[100px] pb-container-padding flex flex-col gap-stack-gap">
        <header class="mb-gutter">
            <h1 class="text-display font-sans text-primary">Laporan Komentar</h1>
            <p class="text-secondary mt-unit text-body-lg font-sans">Kelola laporan komentar dari pengguna.</p>
        </header>

        @if (session('success'))
            <div class="px-4 py-3 rounded-lg bg-accent-green text-on-surface text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-gutter mb-gutter">
            <div class="shadow-neu-raised bg-background rounded-xl p-card-padding">
                <p class="text-label-caps font-label-caps text-secondary mb-1">Total</p>
                <p class="text-headline-md font-sans text-primary">{{ $stats['total'] }}</p>
            </div>
            <div class="shadow-neu-raised bg-background rounded-xl p-card-padding">
                <p class="text-label-caps font-label-caps text-accent-orange mb-1">Menunggu</p>
                <p class="text-headline-md font-sans text-accent-orange">{{ $stats['pending'] }}</p>
            </div>
            <div class="shadow-neu-raised bg-background rounded-xl p-card-padding">
                <p class="text-label-caps font-label-caps text-accent-green mb-1">Disetujui</p>
                <p class="text-headline-md font-sans text-accent-green">{{ $stats['approved'] }}</p>
            </div>
            <div class="shadow-neu-raised bg-background rounded-xl p-card-padding">
                <p class="text-label-caps font-label-caps text-secondary mb-1">Ditolak</p>
                <p class="text-headline-md font-sans text-secondary">{{ $stats['rejected'] }}</p>
            </div>
        </div>

        {{-- Filter --}}
        <div class="flex gap-2 mb-gutter">
            <a href="{{ route('admin.reports') }}"
               class="px-4 py-2 rounded-full text-sm font-bold transition-colors {{ !$status ? 'bg-primary text-on-primary' : 'bg-surface-variant text-secondary hover:text-primary' }}">
                Semua
            </a>
            <a href="{{ route('admin.reports', ['status' => 'pending']) }}"
               class="px-4 py-2 rounded-full text-sm font-bold transition-colors {{ $status === 'pending' ? 'bg-accent-orange text-white' : 'bg-surface-variant text-secondary hover:text-primary' }}">
                Menunggu
            </a>
            <a href="{{ route('admin.reports', ['status' => 'approved']) }}"
               class="px-4 py-2 rounded-full text-sm font-bold transition-colors {{ $status === 'approved' ? 'bg-accent-green text-white' : 'bg-surface-variant text-secondary hover:text-primary' }}">
                Disetujui
            </a>
            <a href="{{ route('admin.reports', ['status' => 'rejected']) }}"
               class="px-4 py-2 rounded-full text-sm font-bold transition-colors {{ $status === 'rejected' ? 'bg-secondary text-white' : 'bg-surface-variant text-secondary hover:text-primary' }}">
                Ditolak
            </a>
        </div>

        {{-- Reports list --}}
        <div class="flex flex-col gap-5">
            @forelse ($reports as $report)
                <div class="shadow-neu-raised bg-background rounded-xl p-card-padding">
                    <div class="flex flex-col md:flex-row md:items-start gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                <span class="px-3 py-1 rounded-full text-xs font-bold
                                    @if ($report->status === 'pending') bg-accent-orange text-white
                                    @elseif ($report->status === 'approved') bg-accent-green text-white
                                    @else bg-surface-variant text-secondary
                                    @endif">
                                    {{ ucfirst($report->status) }}
                                </span>
                                <span class="text-label-caps font-label-caps text-on-surface-variant">
                                    {{ \App\Models\CommentReport::REASON_LABELS[$report->reason] ?? $report->reason }}
                                </span>
                                <span class="text-xs text-secondary">•</span>
                                <span class="text-xs text-secondary">{{ $report->created_at->diffForHumans() }}</span>
                            </div>

                            <p class="text-sm text-secondary mb-2">
                                Dilaporkan oleh <span class="font-bold text-primary">{{ $report->reporter->name }}</span>
                                terhadap komentar oleh <span class="font-bold text-primary">{{ $report->comment->user->name }}</span>
                            </p>

                            @if ($report->description)
                                <p class="text-sm text-on-surface-variant mb-2 italic">"{{ $report->description }}"</p>
                            @endif

                            <div class="shadow-neu-inset bg-background rounded-lg p-3 mt-2">
                                <p class="text-xs text-secondary mb-1">Komentar yang dilaporkan:</p>
                                <p class="text-sm text-on-surface whitespace-pre-line">{{ $report->comment->body }}</p>
                                <p class="text-xs text-secondary mt-1">
                                    di postingan: <a href="{{ route('posts.show', $report->comment->post) }}" class="text-primary hover:underline">{{ $report->comment->post->title }}</a>
                                </p>
                            </div>
                        </div>

                        @if ($report->status === 'pending')
                            <div class="flex md:flex-col gap-2 shrink-0">
                                <form method="POST" action="{{ route('admin.reports.approve', $report) }}"
                                      onsubmit="return confirm('Setujui laporan ini? Komentar akan dihapus permanen.');">
                                    @csrf
                                    <button type="submit"
                                            class="px-4 py-2 rounded-lg text-sm font-bold bg-accent-green text-white neu-btn flex items-center gap-1">
                                        <x-icon name="check" class="text-[16px]" />
                                        Setuju & Hapus
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.reports.reject', $report) }}"
                                      onsubmit="return confirm('Tolak laporan ini?');">
                                    @csrf
                                    <button type="submit"
                                            class="px-4 py-2 rounded-lg text-sm font-bold bg-surface-variant text-secondary neu-btn flex items-center gap-1">
                                        <x-icon name="close" class="text-[16px]" />
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="shadow-neu-raised bg-background rounded-xl p-card-padding text-center text-secondary">
                    Belum ada laporan komentar.
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <x-pagination :paginator="$reports" />
    </main>

    <x-footer />
</x-layout>
