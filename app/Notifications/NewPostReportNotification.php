<?php

namespace App\Notifications;

use App\Models\PostReport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewPostReportNotification extends Notification
{
    use Queueable;

    public function __construct(
        public PostReport $report,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $reason = PostReport::REASON_LABELS[$this->report->reason] ?? $this->report->reason;
        $post = $this->report->post;

        $mail = (new MailMessage)
            ->subject('⚠️ Laporan Postingan Baru di MadingBoard')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Ada laporan postingan baru yang perlu ditinjau:')
            ->line('**Pelapor:** ' . $this->report->reporter->name)
            ->line('**Alasan:** ' . $reason)
            ->line('**Judul postingan:** ' . $post->title)
            ->line('**Oleh:** ' . $post->author->name)
            ->line('**Konten:** "' . Str::limit($post->content, 100) . '"');

        if ($this->report->description) {
            $mail->line('**Deskripsi:** ' . $this->report->description);
        }

        return $mail->action('Tinjau Laporan', route('admin.post-reports'))
            ->line('Segera tinjau laporan ini untuk menjaga kenyamanan komunitas.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'report_id' => $this->report->id,
            'reason' => $this->report->reason,
            'reporter_name' => $this->report->reporter->name,
            'post_title' => $this->report->post->title,
        ];
    }
}
