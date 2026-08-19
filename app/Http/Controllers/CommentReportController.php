<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentReport;
use App\Models\Post;
use App\Models\User;
use App\Notifications\NewCommentReportNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommentReportController extends Controller
{
    /**
     * User: submit a report for a comment.
     */
    public function store(Request $request, Post $post, Comment $comment): RedirectResponse
    {
        abort_if($comment->post_id !== $post->id, 404);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'in:'.implode(',', CommentReport::REASONS)],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        // Check if user already reported this comment
        $alreadyReported = CommentReport::where('comment_id', $comment->id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if ($alreadyReported) {
            return back()->with('error', 'Kamu sudah melaporkan komentar ini sebelumnya.');
        }

        $report = CommentReport::create([
            'comment_id' => $comment->id,
            'user_id' => $request->user()->id,
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
        ]);

        // Notify all admins about the new report
        User::where('is_admin', true)->each(function (User $admin) use ($report) {
            $admin->notify(new NewCommentReportNotification($report));
        });

        return back()->with('success', 'Laporan berhasil dikirim. Terima kasih!');
    }

    /**
     * Admin: list all comment reports.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status') ?: null;

        $query = CommentReport::with(['comment.post', 'reporter']);

        if ($status !== null) {
            $query->where('status', $status);
        }

        $reports = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'total' => CommentReport::count(),
            'pending' => CommentReport::where('status', 'pending')->count(),
            'approved' => CommentReport::where('status', 'approved')->count(),
            'rejected' => CommentReport::where('status', 'rejected')->count(),
        ];

        return view('admin.reports', compact('reports', 'stats', 'status'));
    }

    /**
     * Admin: approve a report and delete the comment.
     */
    public function approve(CommentReport $report): RedirectResponse
    {
        $report->update(['status' => 'approved']);
        $report->comment->delete();

        return back()->with('success', 'Laporan disetujui dan komentar berhasil dihapus.');
    }

    /**
     * Admin: reject a report.
     */
    public function reject(CommentReport $report): RedirectResponse
    {
        $report->update(['status' => 'rejected']);

        return back()->with('success', 'Laporan ditolak.');
    }
}
