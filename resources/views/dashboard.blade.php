@php
    $maxDayName = $chart->firstWhere('count', $maxCount)['day'] ?? null;
@endphp

<x-layout title="MadingBoard - Dasbor Admin">
    <x-navbar :active="'dashboard'" :show-search="false" />

    <main class="pt-[100px] px-container-padding max-w-[1440px] mx-auto pb-container-padding w-full">
        <div class="flex justify-between items-end mb-gutter">
            <div>
                <h1 class="text-display font-sans text-primary mb-2">Ringkasan</h1>
                <p class="text-body-lg font-sans text-secondary">Pantau kinerja papan bulletin digitalmu.</p>
            </div>
            <a href="{{ route('posts.create') }}"
               class="shadow-neu-raised bg-background px-6 py-3 rounded-xl text-primary font-bold flex items-center gap-2 neu-btn">
                <x-icon name="add_circle" :filled="true" />
                Postingan Baru
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-gutter mb-gutter">
            {{-- Kartu statistik --}}
            @foreach ($stats as $stat)
                <div class="md:col-span-3">
                    <x-stat-card :label="$stat['label']" :value="$stat['value']" :accent="$stat['accent']">
                        @if (! empty($stat['link']))
                            <a href="{{ $stat['link'] }}" class="flex items-center gap-1 text-sm font-bold text-primary underline">
                                <x-icon :name="$stat['icon']" class="text-sm" />
                                {{ $stat['note'] }}
                            </a>
                        @else
                            <div class="flex items-center gap-1 {{ $stat['color'] }}">
                                <x-icon :name="$stat['icon']" class="text-sm" />
                                <span class="text-sm font-bold">{{ $stat['note'] }}</span>
                            </div>
                        @endif
                    </x-stat-card>
                </div>
            @endforeach

            {{-- Postingan per hari selama seminggu terakhir --}}
            <div class="md:col-span-8 shadow-neu-raised bg-background rounded-xl p-card-padding min-h-[400px] flex flex-col">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-headline-md font-sans text-primary">Postingan Minggu Ini</h2>
                    <span class="shadow-neu-inset bg-background px-4 py-1 rounded-full text-sm font-bold text-primary">Mingguan</span>
                </div>

                <div class="flex-grow flex items-end justify-between gap-4 pt-8 pb-4 border-b border-surface-variant relative">
                    @foreach ($chart as $bar)
                        @php
                            $pct = $maxCount > 0 ? (int) round($bar['count'] / $maxCount * 100) : 0;
                            $isMax = $bar['day'] === $maxDayName;
                        @endphp
                        <div class="w-1/12 shadow-neu-raised rounded-t-lg relative h-[{{ $pct }}%] {{ $isMax && $pct > 0 ? 'bg-accent-blue shadow-[0_0_15px_rgba(130,177,255,0.5)]' : 'bg-surface-bright' }}"></div>
                    @endforeach
                </div>

                <div class="flex justify-between mt-2 text-label-caps font-label-caps text-secondary">
                    @foreach ($chart as $bar)
                        <span class="{{ $bar['day'] === $maxDayName ? 'text-primary font-bold' : '' }}">{{ $bar['day'] }}</span>
                    @endforeach
                </div>
            </div>

            {{-- Aktivitas terbaru --}}
            <div class="md:col-span-4 shadow-neu-raised bg-background rounded-xl p-card-padding flex flex-col">
                <h2 class="text-headline-md font-sans text-primary mb-6">Aktivitas Terbaru</h2>
                <div class="flex flex-col gap-4 flex-grow overflow-y-auto pr-2">
                    @forelse ($activities as $activity)
                        <x-activity-item :icon="$activity['icon']"
                                         :icon-color="$activity['color']"
                                         :title="$activity['title']"
                                         :description="$activity['desc']"
                                         :time="$activity['time']" />
                    @empty
                        <p class="text-secondary text-sm">Belum ada aktivitas.</p>
                    @endforelse
                </div>
                <a href="{{ route('management') }}" class="w-full mt-4 py-2 text-sm font-bold text-secondary hover:text-primary transition-colors text-center block">
                    Lihat Semua Riwayat
                </a>
            </div>
        </div>
    </main>

    <x-footer />
</x-layout>
