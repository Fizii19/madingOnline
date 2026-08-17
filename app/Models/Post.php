<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'title', 'category', 'content', 'image_url', 'image_path', 'status', 'is_pinned', 'views'])]
class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    public const CATEGORIES = ['announcement', 'event', 'news', 'academic', 'alert', 'club', 'finance', 'hr'];

    public const CATEGORY_LABELS = [
        'announcement' => 'Pengumuman',
        'event' => 'Acara',
        'news' => 'Berita',
        'academic' => 'Akademik',
        'alert' => 'Peringatan',
        'club' => 'Klub',
        'finance' => 'Keuangan',
        'hr' => 'SDM',
    ];

    public const STATUSES = ['published', 'draft', 'pending'];

    public const STATUS_LABELS = [
        'published' => 'Terbit',
        'draft' => 'Draf',
        'pending' => 'Menunggu Persetujuan',
    ];

    /**
     * Accent strip colors per category (neumorphic design system).
     */
    public const ACCENT_COLORS = [
        'event' => 'bg-[#a3defe]',
        'academic' => 'bg-[#b5ead7]',
        'alert' => 'bg-[#ffdac1]',
        'club' => 'bg-[#e2f0cb]',
        'announcement' => 'bg-[#bae6fd]',
        'news' => 'bg-[#bbf7d0]',
        'finance' => 'bg-[#c7ceea]',
        'hr' => 'bg-[#fef08a]',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'views' => 'integer',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(PostLike::class);
    }

    public function isLikedBy(?User $user): bool
    {
        return $user !== null && $this->likes()->where('user_id', $user->id)->exists();
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function getAccentAttribute(): string
    {
        return self::ACCENT_COLORS[$this->category] ?? 'bg-[#bae6fd]';
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORY_LABELS[$this->category] ?? ucfirst($this->category);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }

    /**
     * The post cover image: an uploaded file takes precedence over an external URL.
     */
    public function getImageAttribute(): ?string
    {
        return $this->image_path
            ? asset('storage/'.$this->image_path)
            : $this->image_url;
    }
}
