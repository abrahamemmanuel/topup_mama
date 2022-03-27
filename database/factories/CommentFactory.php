<?php

namespace Database\Factories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
    	return [
            'user_name' => 'Anonymous',
            'body' => $this->faker->sentence,
            'book_id' => $this->faker->numberBetween(1, 100),
            'client_ip' => $this->faker->ipv4,
            'created_at' => $this->faker->dateTime,
            'updated_at' => $this->faker->dateTime,
        ];
    }
}
