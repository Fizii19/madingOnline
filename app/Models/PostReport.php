<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['post_id', 'user_id', 'reason', 'description', 'status'])]
class PostReport extends Model
{
    public const REASONS = ['spam', 'ofensir', 'konten_tidak_pantas', 'lainnya'];

    public const REASON_LABELS = [
        'spam' => 'Spam',
        'ofensir' => 'Ofensir',
        'konten_tidak_pantas' => 'Konten Tidak Pantas',
        'lainnya' => 'Lainnya',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
