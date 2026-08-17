<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PollVoteController extends Controller
{
    /**
     * Record a vote on an active poll. A user can vote once per poll;
     * voting again with a different choice updates their previous vote.
     */
    public function store(Request $request, Poll $poll): RedirectResponse
    {
        abort_unless($poll->is_active, 404);

        $validated = $request->validate([
            'option' => ['required', 'string', 'in:'.implode(',', $poll->options)],
        ]);

        $poll->votes()->updateOrCreate(
            ['user_id' => $request->user()->id],
            ['option' => $validated['option']],
        );

        return redirect()->back()->with('success', 'Terima kasih! Suaramu sudah dicatat.');
    }
}
