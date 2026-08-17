<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['question', 'options', 'is_active'])]
class Poll extends Model
{
    /** @use HasFactory<\Database\Factories\PollFactory> */
    use HasFactory;

    public const MAX_OPTIONS = 6;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'options' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function totalVotes(): int
    {
        return $this->votes()->count();
    }

    public function voteCountFor(string $option): int
    {
        return $this->votes()->where('option', $option)->count();
    }

    public function hasVoted(?User $user): bool
    {
        return $user !== null && $this->votes()->where('user_id', $user->id)->exists();
    }

    public function userVote(?User $user): ?PollVote
    {
        if ($user === null) {
            return null;
        }

        return $this->votes()->where('user_id', $user->id)->first();
    }
}
