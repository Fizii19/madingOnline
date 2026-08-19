<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['comment_id', 'user_id', 'reason', 'description', 'status'])]
class CommentReport extends Model
{
    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public const REASONS = ['spam', 'ofensir', 'konten_tidak_pantas', 'lainnya'];

    public const REASON_LABELS = [
        'spam' => 'Spam',
        'ofensir' => 'Ofensir',
        'konten_tidak_pantas' => 'Konten Tidak Pantas',
        'lainnya' => 'Lainnya',
    ];
}
