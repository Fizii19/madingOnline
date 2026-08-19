<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostReport;
use App\Models\User;
use App\Notifications\NewPostReportNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostReportController extends Controller
{
    /**
     * User: submit a report for a post.
     */
    public function store(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'in:'.implode(',', PostReport::REASONS)],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $alreadyReported = PostReport::where('post_id', $post->id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if ($alreadyReported) {
            return back()->with('error', 'Kamu sudah melaporkan postingan ini sebelumnya.');
        }

        $report = PostReport::create([
            'post_id' => $post->id,
            'user_id' => $request->user()->id,
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
        ]);

        // Notify all admins
        User::where('is_admin', true)->each(function (User $admin) use ($report) {
            $admin->notify(new NewPostReportNotification($report));
        });

        return back()->with('success', 'Laporan postingan berhasil dikirim. Terima kasih!');
    }

    /**
     * Admin: list all post reports.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status') ?: null;

        $query = PostReport::with(['post.author', 'reporter']);

        if ($status !== null) {
            $query->where('status', $status);
        }

        $reports = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'total' => PostReport::count(),
            'pending' => PostReport::where('status', 'pending')->count(),
            'approved' => PostReport::where('status', 'approved')->count(),
            'rejected' => PostReport::where('status', 'rejected')->count(),
        ];

        return view('admin.post-reports', compact('reports', 'stats', 'status'));
    }

    /**
     * Admin: approve a report and delete the post.
     */
    public function approve(PostReport $report): RedirectResponse
    {
        $report->update(['status' => 'approved']);
        $report->post->delete();

        return back()->with('success', 'Laporan disetujui dan postingan berhasil dihapus.');
    }

    /**
     * Admin: reject a report.
     */
    public function reject(PostReport $report): RedirectResponse
    {
        $report->update(['status' => 'rejected']);

        return back()->with('success', 'Laporan ditolak.');
    }
}
