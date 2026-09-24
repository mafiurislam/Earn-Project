<?php

namespace Database\Factories;

use App\Models\Song;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Song>
 */
class SongFactory extends Factory
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
            'title' => fake()->words(3, true),
            'singer' => fake()->name(),
            'composer' => fake()->name(),
            'producer' => 'Rajdoot Nivedan Media',
            'copyright' => '℗ 2026 Rajdoot Nivedan',
            'cover_image' => 'songs/covers/sample.jpg',
            'audio_file' => 'songs/audio/sample.mp3',
            'status' => 'active',
        ];
    }
}
