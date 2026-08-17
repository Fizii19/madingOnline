<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Public home: show the latest published posts as a bulletin board.
     * Supports search (?q=) and category (?category=) filters.
     */
    public function beranda(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $category = $request->query('category') ?: null;
        $hasFilter = $search !== '' || $category !== null;

        $query = Post::with('author')->published();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($category !== null) {
            $query->where('category', $category);
        }

        $total = (clone $query)->count();

        $query->orderBy('is_pinned', 'desc')->orderByDesc('created_at');

        if ($hasFilter) {
            $paginator = $query->paginate(9)->withQueryString();
            $posts = $paginator->getCollection();
        } else {
            $paginator = null;
            $posts = $query->take(4)->get();
        }

        return view('beranda', [
            'featured' => $posts->shift(),
            'posts' => $posts,
            'paginator' => $paginator,
            'poll' => Poll::active()->latest()->first(),
            'search' => $search,
            'category' => $category,
            'totalResults' => $total,
            'hasFilter' => $hasFilter,
        ]);
    }

    /**
     * Public post detail page.
     */
    public function show(Post $post): View
    {
        abort_unless($post->status === 'published', 404);

        $post->increment('views');

        $post->load(['author', 'comments.user']);

        return view('posts.show', [
            'post' => $post,
            'likesCount' => $post->likes()->count(),
            'liked' => $post->isLikedBy(auth()->user()),
        ]);
    }

    /**
     * Admin: list all posts (management page).
     * Supports search (?q=), category (?category=) and status (?status=) filters.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $category = $request->query('category') ?: null;
        $status = $request->query('status') ?: null;

        $query = Post::with('author');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhereHas('author', fn ($author) => $author->where('name', 'like', "%{$search}%"));
            });
        }

        if ($category !== null) {
            $query->where('category', $category);
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        return view('manajemen', [
            'posts' => $query->latest()->paginate(5)->withQueryString(),
            'search' => $search,
            'category' => $category,
            'status' => $status,
        ]);
    }

    /**
     * Admin: show the create-post form.
     */
    public function create(): View
    {
        return view('posting');
    }

    /**
     * Admin: store a new post.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePost($request);

        Post::create([
            'user_id' => $request->user()->id,
            ...$validated,
            'image_path' => $this->storeCoverImage($request),
            'is_pinned' => $request->boolean('is_pinned'),
        ]);

        return redirect()->route('management')->with('success', 'Postingan berhasil dibuat.');
    }

    /**
     * Admin: show the edit-post form.
     */
    public function edit(Post $post): View
    {
        return view('posting', ['post' => $post]);
    }

    /**
     * Admin: update an existing post.
     */
    public function update(Request $request, Post $post): RedirectResponse
    {
        $validated = $this->validatePost($request);

        if ($request->hasFile('image')) {
            $this->deleteCoverImage($post);
        }

        $post->update([
            ...$validated,
            'image_path' => $this->storeCoverImage($request) ?? $post->image_path,
            'is_pinned' => $request->boolean('is_pinned'),
        ]);

        return redirect()->route('management')->with('success', 'Postingan berhasil diperbarui.');
    }

    /**
     * Admin: delete a post.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $this->deleteCoverImage($post);

        $post->delete();

        return redirect()->route('management')->with('success', 'Postingan berhasil dihapus.');
    }

    /**
     * Shared validation rules for the post form.
     *
     * @return array<string, mixed>
     */
    protected function validatePost(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:'.implode(',', Post::CATEGORIES)],
            'status' => ['required', 'string', 'in:'.implode(',', Post::STATUSES)],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'image_url' => ['nullable', 'url', 'max:500'],
        ]);
    }

    /**
     * Store the uploaded cover image on the public disk.
     */
    protected function storeCoverImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        return $request->file('image')->store('posts', 'public');
    }

    /**
     * Remove a previously uploaded cover image from the public disk.
     */
    protected function deleteCoverImage(Post $post): void
    {
        if ($post->image_path) {
            Storage::disk('public')->delete($post->image_path);
        }
    }
}
