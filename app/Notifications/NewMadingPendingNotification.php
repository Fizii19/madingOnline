<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMadingPendingNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Post $post,
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
        return (new MailMessage)
            ->subject('📝 Mading Baru Menunggu Persetujuan di MadingBoard')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Ada mading baru yang dikirim oleh pengguna dan perlu persetujuanmu:')
            ->line('**Judul:** ' . $this->post->title)
            ->line('**Kategori:** ' . $this->post->category_label)
            ->line('**Oleh:** ' . $this->post->author->name)
            ->line('**Konten:** "' . \Illuminate\Support\Str::limit($this->post->content, 150) . '"')
            ->action('Tinjau Mading', route('management'))
            ->line('Silakan setujui atau tolak mading ini dari halaman Manajemen Konten.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'post_id' => $this->post->id,
            'title' => $this->post->title,
            'category' => $this->post->category,
            'author_name' => $this->post->author->name,
        ];
    }
}
