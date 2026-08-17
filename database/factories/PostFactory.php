<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(5),
            'category' => fake()->randomElement(Post::CATEGORIES),
            'content' => fake()->paragraphs(3, true),
            'image_url' => null,
            'status' => 'published',
            'is_pinned' => false,
            'views' => fake()->numberBetween(0, 500),
        ];
    }
}
