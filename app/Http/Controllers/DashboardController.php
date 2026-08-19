<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalPosts = Post::count();
        $publishedPosts = Post::where('status', 'published')->count();
        $pendingPosts = Post::where('status', 'pending')->count();

        $pendingReports = \App\Models\CommentReport::where('status', 'pending')->count();
        $pendingPostReports = \App\Models\PostReport::where('status', 'pending')->count();

        $stats = [
            ['label' => 'TOTAL POSTINGAN', 'value' => number_format($totalPosts), 'accent' => 'bg-accent-blue', 'note' => $publishedPosts.' diterbitkan', 'icon' => 'trending_up', 'color' => 'text-accent-blue'],
            ['label' => 'TOTAL TAYANGAN', 'value' => number_format(Post::sum('views')), 'accent' => 'bg-accent-green', 'note' => 'di semua postingan', 'icon' => 'trending_up', 'color' => 'text-accent-green'],
            ['label' => 'RASIO PUBLIKASI', 'value' => $totalPosts ? round($publishedPosts / $totalPosts * 100).'%' : '0%', 'accent' => 'bg-accent-purple', 'note' => 'terbit / total', 'icon' => 'trending_flat', 'color' => 'text-secondary'],
            ['label' => 'MADING MENUNGGU', 'value' => number_format($pendingPosts), 'accent' => 'bg-accent-orange', 'note' => 'Tinjau Sekarang', 'icon' => 'hourglass_top', 'color' => 'text-accent-orange', 'link' => route('management')],
            ['label' => 'LAPORAN KOMENTAR', 'value' => number_format($pendingReports), 'accent' => 'bg-[#fca5a5]', 'note' => 'Menunggu Ditinjau', 'icon' => 'flag', 'color' => 'text-error', 'link' => route('admin.reports')],
            ['label' => 'LAPORAN POSTINGAN', 'value' => number_format($pendingPostReports), 'accent' => 'bg-[#fca5a5]', 'note' => 'Menunggu Ditinjau', 'icon' => 'report', 'color' => 'text-error', 'link' => route('admin.post-reports')],
        ];

        // Posts created per day over the last 7 days.
        $days = collect(range(6, 0))->map(fn (int $i) => now()->subDays($i)->startOfDay());
        $counts = Post::query()
            ->where('created_at', '>=', $days->first())
            ->get()
            ->groupBy(fn (Post $post) => $post->created_at->toDateString())
            ->map->count();

        $dayNames = ['Ming', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

        $chart = $days->map(function (Carbon $day) use ($counts, $dayNames) {
            return [
                'day' => $dayNames[$day->dayOfWeek],
                'count' => $counts[$day->toDateString()] ?? 0,
            ];
        });

        $maxCount = max(1, $chart->max('count'));

        // Latest activity: the three most recent posts.
        $activities = Post::with('author')->latest()->take(3)->get()->map(function (Post $post) {
            return [
                'icon' => match ($post->status) {
                    'pending' => 'warning',
                    'draft' => 'edit_document',
                    default => 'check_circle',
                },
                'color' => match ($post->status) {
                    'pending' => 'text-accent-orange',
                    'draft' => 'text-accent-blue',
                    default => 'text-accent-green',
                },
                'title' => match ($post->status) {
                    'pending' => 'Postingan menunggu persetujuan',
                    'draft' => 'Postingan disimpan sebagai draf',
                    default => 'Postingan diterbitkan',
                },
                'desc' => '"'.$post->title.'"',
                'time' => $post->created_at->diffForHumans(),
            ];
        });

        return view('dashboard', [
            'stats' => $stats,
            'chart' => $chart,
            'maxCount' => $maxCount,
            'activities' => $activities,
        ]);
    }
}
