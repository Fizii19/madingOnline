<?php

namespace App\Notifications;

use App\Models\CommentReport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewCommentReportNotification extends Notification
{
    use Queueable;

    public function __construct(
        public CommentReport $report,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $reason = CommentReport::REASON_LABELS[$this->report->reason] ?? $this->report->reason;
        $comment = $this->report->comment;
        $post = $comment->post;

        $mail = (new MailMessage)
            ->subject('⚠️ Laporan Komentar Baru di MadingBoard')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Ada laporan komentar baru yang perlu ditinjau:')
            ->line('**Pelapor:** ' . $this->report->reporter->name)
            ->line('**Alasan:** ' . $reason)
            ->line('**Komentar oleh:** ' . $comment->user->name)
            ->line('**Komentar:** "' . Str::limit($comment->body, 100) . '"')
            ->line('**Di postingan:** ' . $post->title);

        if ($this->report->description) {
            $mail->line('**Deskripsi:** ' . $this->report->description);
        }

        return $mail->action('Tinjau Laporan', route('admin.reports'))
            ->line('Segera tinjau laporan ini untuk menjaga kenyamanan komunitas.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'report_id' => $this->report->id,
            'reason' => $this->report->reason,
            'reporter_name' => $this->report->reporter->name,
            'comment_body' => $this->report->comment->body,
            'post_title' => $this->report->comment->post->title,
        ];
    }
}
