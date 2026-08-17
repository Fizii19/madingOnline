<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PollController extends Controller
{
    /**
     * Admin: list all polls with their vote totals.
     */
    public function index(): View
    {
        $polls = Poll::withCount('votes')->latest()->get();

        return view('polls.index', ['polls' => $polls]);
    }

    /**
     * Admin: create a new poll.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'options' => ['required', 'array', 'min:2', 'max:'.Poll::MAX_OPTIONS],
            'options.*' => ['required', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $options = array_values(array_unique(array_map('trim', $validated['options'])));

        if (count($options) < 2) {
            return back()->withErrors(['options' => 'Minimal 2 pilihan yang berbeda.'])->withInput();
        }

        Poll::create([
            'question' => $validated['question'],
            'options' => array_slice($options, 0, Poll::MAX_OPTIONS),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('polls.index')->with('success', 'Poll berhasil dibuat.');
    }

    /**
     * Admin: toggle a poll between active and inactive.
     */
    public function toggleActive(Poll $poll): RedirectResponse
    {
        $poll->update(['is_active' => ! $poll->is_active]);

        return redirect()->route('polls.index')->with('success', 'Status poll diperbarui.');
    }

    /**
     * Admin: delete a poll (votes are removed too via cascade).
     */
    public function destroy(Poll $poll): RedirectResponse
    {
        $poll->delete();

        return redirect()->route('polls.index')->with('success', 'Poll berhasil dihapus.');
    }
}
