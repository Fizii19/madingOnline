<?php

namespace Tests\Feature;

use App\Models\CommentReport;
use App\Models\Poll;
use App\Models\Post;
use App\Models\PostReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MadingPagesSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_home_renders_for_guests(): void
    {
        $this->get('/')->assertStatus(200)->assertSee('MadingBoard');
    }

    public function test_home_shows_published_posts_from_database(): void
    {
        Post::factory()->create(['title' => 'Postingan Publik Terlihat']);
        Post::factory()->create(['title' => 'Postingan Rahasia', 'status' => 'draft']);

        $this->get('/')
            ->assertSee('Postingan Publik Terlihat')
            ->assertDontSee('Postingan Rahasia');
    }

    public function test_beranda_can_be_filtered_by_search_and_category(): void
    {
        Post::factory()->create(['title' => 'Festival Musik Kampus', 'category' => 'event']);
        Post::factory()->create(['title' => 'Jadwal Ujian Akhir', 'category' => 'academic']);

        $this->get('/?q=musik')
            ->assertSee('Festival Musik Kampus')
            ->assertDontSee('Jadwal Ujian Akhir');

        $this->get('/?category=academic')
            ->assertSee('Jadwal Ujian Akhir')
            ->assertDontSee('Festival Musik Kampus');
    }

    public function test_home_paginates_filtered_results(): void
    {
        Post::factory()->count(12)->create([
            'title' => 'Acara Pagination',
            'category' => 'event',
            'status' => 'published',
        ]);

        // More than one page of results -> pagination controls appear.
        $this->get('/?category=event')
            ->assertStatus(200)
            ->assertSee('Menampilkan 12 hasil')
            ->assertSee('aria-label="Halaman 2"', false);

        // Page 2 keeps the filter and shows the remaining results.
        $this->get('/?category=event&page=2')
            ->assertStatus(200)
            ->assertSee('Acara Pagination')
            ->assertSee('aria-label="Halaman 1"', false);
    }

    public function test_home_shows_no_pagination_without_filter(): void
    {
        Post::factory()->count(10)->create(['status' => 'published']);

        $this->get('/')
            ->assertStatus(200)
            ->assertDontSee('aria-label="Halaman 2"', false);
    }

    public function test_management_can_be_filtered_by_search_and_category(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Post::factory()->create(['title' => 'Laporan Keuangan Q4', 'category' => 'finance', 'status' => 'published']);
        Post::factory()->create(['title' => 'Agenda Rapat Osis', 'category' => 'hr', 'status' => 'draft']);

        $this->actingAs($admin)->get('/management?q=keuangan')
            ->assertSee('Laporan Keuangan Q4')
            ->assertDontSee('Agenda Rapat Osis');

        $this->actingAs($admin)->get('/management?status=draft')
            ->assertSee('Agenda Rapat Osis')
            ->assertDontSee('Laporan Keuangan Q4');
    }

    public function test_post_detail_page_increments_views(): void
    {
        $post = Post::factory()->create(['views' => 10]);

        $this->get(route('posts.show', $post))->assertStatus(200)->assertSee($post->title);

        $this->assertSame(11, $post->fresh()->views);
    }

    public function test_draft_post_is_not_publicly_visible(): void
    {
        $post = Post::factory()->create(['status' => 'draft']);

        $this->get(route('posts.show', $post))->assertNotFound();
    }

    public function test_guests_cannot_comment_or_like(): void
    {
        $post = Post::factory()->create();

        $this->post(route('posts.comments.store', $post), ['body' => 'Halo'])->assertRedirect(route('login'));
        $this->post(route('posts.like', $post))->assertRedirect(route('login'));
    }

    public function test_user_can_comment_on_a_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->post(route('posts.comments.store', $post), ['body' => 'Komentar uji coba'])
            ->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'body' => 'Komentar uji coba',
        ]);

        $this->get(route('posts.show', $post))->assertSee('Komentar uji coba');
    }

    public function test_user_can_like_and_unlike_a_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)->post(route('posts.like', $post))->assertRedirect();
        $this->assertDatabaseHas('post_likes', ['post_id' => $post->id, 'user_id' => $user->id]);

        $this->actingAs($user)->post(route('posts.like', $post))->assertRedirect();
        $this->assertDatabaseMissing('post_likes', ['post_id' => $post->id, 'user_id' => $user->id]);
    }

    public function test_post_detail_shows_like_count(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $post->likes()->create(['user_id' => $user->id]);

        $this->get(route('posts.show', $post))->assertSee('1 Suka');
    }

    public function test_author_can_delete_own_comment(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $comment = $post->comments()->create(['user_id' => $user->id, 'body' => 'Komentar milik sendiri']);

        $this->actingAs($user)
            ->delete(route('posts.comments.destroy', [$post, $comment]))
            ->assertRedirect();

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_admin_can_delete_any_comment(): void
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);
        $post = Post::factory()->create();
        $comment = $post->comments()->create(['user_id' => $owner->id, 'body' => 'Komentar orang lain']);

        $this->actingAs($admin)
            ->delete(route('posts.comments.destroy', [$post, $comment]))
            ->assertRedirect();

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_other_user_cannot_delete_someone_elses_comment(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $post = Post::factory()->create();
        $comment = $post->comments()->create(['user_id' => $owner->id, 'body' => 'Komentar terlarang']);

        $this->actingAs($other)
            ->delete(route('posts.comments.destroy', [$post, $comment]))
            ->assertForbidden();

        $this->assertDatabaseHas('comments', ['id' => $comment->id]);
    }

    public function test_login_and_register_pages_render(): void
    {
        $this->get('/login')->assertStatus(200);
        $this->get('/register')->assertStatus(200);
    }

    public function test_admin_pages_redirect_guests_to_login(): void
    {
        foreach (['/dashboard', '/management', '/posts/create', '/admin/reports', '/admin/post-reports'] as $route) {
            $this->get($route)->assertRedirect(route('login'));
        }
    }

    public function test_admin_pages_are_forbidden_for_regular_users(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get('/dashboard')->assertForbidden();
        $this->actingAs($user)->get('/management')->assertForbidden();
        $this->actingAs($user)->get('/posts/create')->assertForbidden();
        $this->actingAs($user)->get('/admin/reports')->assertForbidden();
        $this->actingAs($user)->get('/admin/post-reports')->assertForbidden();
    }

    public function test_admin_pages_render_for_admins(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get('/dashboard')->assertStatus(200)->assertSee('MadingBoard');
        $this->actingAs($admin)->get('/management')->assertStatus(200);
        $this->actingAs($admin)->get('/posts/create')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/reports')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/post-reports')->assertStatus(200);
    }

    public function test_admin_can_create_a_post(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->post(route('posts.store'), [
            'title' => 'Postingan Baru dari Test',
            'category' => 'announcement',
            'status' => 'published',
            'content' => 'Isi konten postingan uji coba.',
            'is_pinned' => '1',
        ])->assertRedirect(route('management'));

        $this->assertDatabaseHas('posts', [
            'title' => 'Postingan Baru dari Test',
            'user_id' => $admin->id,
            'is_pinned' => 1,
        ]);
    }

    public function test_admin_can_upload_a_cover_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->post(route('posts.store'), [
            'title' => 'Postingan dengan Gambar',
            'category' => 'event',
            'status' => 'published',
            'content' => 'Konten postingan dengan cover image.',
            'image' => UploadedFile::fake()->image('cover.jpg', 600, 400),
        ])->assertRedirect(route('management'));

        $post = Post::where('title', 'Postingan dengan Gambar')->first();

        $this->assertNotNull($post->image_path);
        Storage::disk('public')->assertExists($post->image_path);
    }

    public function test_updating_a_post_replaces_the_cover_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true]);
        $post = Post::factory()->create([
            'image_path' => UploadedFile::fake()->image('old.jpg')->store('posts', 'public'),
        ]);
        $oldPath = $post->image_path;

        $this->actingAs($admin)->put(route('posts.update', $post), [
            'title' => $post->title,
            'category' => $post->category,
            'status' => 'published',
            'content' => $post->content,
            'image' => UploadedFile::fake()->image('new.jpg'),
        ])->assertRedirect(route('management'));

        $post->refresh();

        $this->assertNotSame($oldPath, $post->image_path);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($post->image_path);
    }

    public function test_admin_can_edit_and_delete_a_post(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $post = Post::factory()->create(['title' => 'Judul Lama']);

        $this->actingAs($admin)
            ->put(route('posts.update', $post), [
                'title' => 'Judul Baru',
                'category' => 'event',
                'status' => 'published',
                'content' => 'Konten baru.',
            ])
            ->assertRedirect(route('management'));

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Judul Baru']);

        $this->actingAs($admin)
            ->delete(route('posts.destroy', $post))
            ->assertRedirect(route('management'));

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_admin_can_log_in(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin Mading',
            'email' => 'admin@mading.test',
            'password' => 'password',
            'is_admin' => true,
        ]);

        $this->post('/login', [
            'email' => 'admin@mading.test',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_regular_user_can_log_in_and_goes_home(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'password',
            'is_admin' => false,
        ]);

        $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'password',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticated();
    }

    public function test_login_rejects_bad_credentials(): void
    {
        $this->post('/login', [
            'email' => 'nobody@example.com',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_logout_ends_the_session(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->post('/logout')
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }

    public function test_admin_can_create_and_delete_a_poll(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->post(route('polls.store'), [
            'question' => 'Kantin apa yang kamu suka?',
            'options' => ['Sushi Bar', 'Taco Stand', 'Vegan Deli'],
            'is_active' => '1',
        ])->assertRedirect(route('polls.index'));

        $poll = Poll::where('question', 'Kantin apa yang kamu suka?')->first();
        $this->assertNotNull($poll);
        $this->assertSame(['Sushi Bar', 'Taco Stand', 'Vegan Deli'], $poll->options);
        $this->assertTrue($poll->is_active);

        $this->actingAs($admin)->delete(route('polls.destroy', $poll))->assertRedirect(route('polls.index'));
        $this->assertDatabaseMissing('polls', ['id' => $poll->id]);
    }

    public function test_admin_polls_page_is_protected(): void
    {
        $this->get(route('polls.index'))->assertRedirect(route('login'));

        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user)->get(route('polls.index'))->assertForbidden();
    }

    public function test_user_can_vote_and_change_vote_on_a_poll(): void
    {
        $user = User::factory()->create();
        $poll = Poll::factory()->create([
            'question' => 'Minuman favoritmu?',
            'options' => ['Kopi', 'Teh', 'Jus'],
        ]);

        $this->actingAs($user)->from(route('home'))->post(route('polls.vote', $poll), ['option' => 'Teh'])
            ->assertRedirect(route('home'));

        $this->assertDatabaseHas('poll_votes', ['poll_id' => $poll->id, 'user_id' => $user->id, 'option' => 'Teh']);

        // Voting again with another choice updates the vote (still one vote per user).
        $this->actingAs($user)->from(route('home'))->post(route('polls.vote', $poll), ['option' => 'Kopi'])
            ->assertRedirect(route('home'));

        $this->assertSame(1, $poll->votes()->count());
        $this->assertDatabaseHas('poll_votes', ['poll_id' => $poll->id, 'user_id' => $user->id, 'option' => 'Kopi']);
    }

    public function test_guest_cannot_vote_and_sees_login_prompt(): void
    {
        $poll = Poll::factory()->create(['question' => 'Poll untuk tamu?']);

        $this->post(route('polls.vote', $poll), ['option' => 'A'])->assertRedirect(route('login'));

        $this->get(route('home'))->assertSee('Poll untuk tamu?')->assertSee('untuk ikut vote');
    }

    public function test_home_shows_only_the_latest_active_poll(): void
    {
        Poll::factory()->create(['question' => 'Poll nonaktif', 'is_active' => false]);
        Poll::factory()->create(['question' => 'Poll aktif terbaru']);

        $this->get(route('home'))
            ->assertSee('Poll aktif terbaru')
            ->assertDontSee('Poll nonaktif');
    }

    // --- Feature: Comment Reports ---

    public function test_user_can_report_a_comment(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $post = Post::factory()->create();
        $comment = $post->comments()->create(['user_id' => $owner->id, 'body' => 'Komentar buruk']);

        $this->actingAs($user)
            ->post(route('posts.comments.report', [$post, $comment]), [
                'reason' => 'spam',
                'description' => 'Ini spam',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('comment_reports', [
            'comment_id' => $comment->id,
            'user_id' => $user->id,
            'reason' => 'spam',
        ]);
    }

    public function test_user_cannot_report_own_comment(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $comment = $post->comments()->create(['user_id' => $user->id, 'body' => 'Komentar sendiri']);

        $this->actingAs($user)
            ->post(route('posts.comments.report', [$post, $comment]), [
                'reason' => 'spam',
            ])
            ->assertRedirect();

        $this->assertDatabaseMissing('comment_reports', [
            'comment_id' => $comment->id,
        ]);
    }

    public function test_admin_can_approve_report_and_delete_comment(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $owner = User::factory()->create();
        $post = Post::factory()->create();
        $comment = $post->comments()->create(['user_id' => $owner->id, 'body' => 'Komentar ofensif']);
        $report = CommentReport::create([
            'comment_id' => $comment->id,
            'user_id' => $admin->id,
            'reason' => 'ofensir',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.reports.approve', $report))
            ->assertRedirect();

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
        $this->assertDatabaseHas('comment_reports', ['id' => $report->id, 'status' => 'approved']);
    }

    public function test_admin_can_reject_report(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $owner = User::factory()->create();
        $post = Post::factory()->create();
        $comment = $post->comments()->create(['user_id' => $owner->id, 'body' => 'Komentar oke']);
        $report = CommentReport::create([
            'comment_id' => $comment->id,
            'user_id' => $admin->id,
            'reason' => 'lainnya',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.reports.reject', $report))
            ->assertRedirect();

        $this->assertDatabaseHas('comments', ['id' => $comment->id]);
        $this->assertDatabaseHas('comment_reports', ['id' => $report->id, 'status' => 'rejected']);
    }

    // --- Feature: User Mading Upload ---

    public function test_user_can_upload_mading(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('mading.store'), [
                'title' => 'Mading dari User',
                'category' => 'event',
                'content' => 'Isi mading yang dikirim user.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('posts', [
            'title' => 'Mading dari User',
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
    }

    public function test_admin_can_approve_pending_post(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        $post = Post::factory()->create(['status' => 'pending', 'user_id' => $user->id]);

        $this->actingAs($admin)
            ->post(route('posts.approve', $post))
            ->assertRedirect();

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'status' => 'published']);
    }

    public function test_admin_can_reject_pending_post(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        $post = Post::factory()->create(['status' => 'pending', 'user_id' => $user->id]);

        $this->actingAs($admin)
            ->post(route('posts.reject', $post))
            ->assertRedirect();

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'status' => 'draft']);
    }

    public function test_mading_upload_page_requires_auth(): void
    {
        $this->get(route('mading.upload'))->assertRedirect(route('login'));
    }

    // --- Feature: Post Reports ---

    public function test_user_can_report_a_post(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        $this->actingAs($user)
            ->post(route('posts.report', $post), [
                'reason' => 'spam',
                'description' => 'Postingan spam',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('post_reports', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'reason' => 'spam',
        ]);
    }

    public function test_user_cannot_report_own_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->post(route('posts.report', $post), [
                'reason' => 'spam',
            ])
            ->assertRedirect();

        $this->assertDatabaseMissing('post_reports', [
            'post_id' => $post->id,
        ]);
    }

    public function test_user_cannot_report_same_post_twice(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        $this->actingAs($user)
            ->post(route('posts.report', $post), ['reason' => 'spam'])
            ->assertRedirect();

        $this->assertDatabaseHas('post_reports', ['post_id' => $post->id, 'user_id' => $user->id]);

        // Second report should fail
        $this->actingAs($user)
            ->post(route('posts.report', $post), ['reason' => 'ofensir'])
            ->assertSessionHas('error');

        $this->assertSame(1, PostReport::where('post_id', $post->id)->count());
    }

    public function test_guest_cannot_report_a_post(): void
    {
        $post = Post::factory()->create();

        $this->post(route('posts.report', $post), ['reason' => 'spam'])
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_approve_post_report_and_delete_post(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $author = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);
        $report = PostReport::create([
            'post_id' => $post->id,
            'user_id' => $admin->id,
            'reason' => 'ofensir',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.post-reports.approve', $report))
            ->assertRedirect();

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
        $this->assertDatabaseHas('post_reports', ['id' => $report->id, 'status' => 'approved']);
    }

    public function test_admin_can_reject_post_report(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $author = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);
        $report = PostReport::create([
            'post_id' => $post->id,
            'user_id' => $admin->id,
            'reason' => 'lainnya',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.post-reports.reject', $report))
            ->assertRedirect();

        $this->assertDatabaseHas('posts', ['id' => $post->id]);
        $this->assertDatabaseHas('post_reports', ['id' => $report->id, 'status' => 'rejected']);
    }

    public function test_regular_user_cannot_access_post_reports_admin(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get(route('admin.post-reports'))->assertForbidden();
    }

    public function test_admin_post_reports_page_renders(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get(route('admin.post-reports'))->assertStatus(200)->assertSee('Laporan Postingan');
    }
}
