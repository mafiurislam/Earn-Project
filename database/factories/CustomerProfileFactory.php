<?php

namespace Database\Factories;

use App\Models\CustomerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CustomerProfile>
 */
class CustomerProfileFactory extends Factory
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
            'owner_name' => fake()->name(),
            'channel_name' => fake()->company().' Music',
            'youtube_link' => 'https://youtube.com/@'.fake()->userName(),
            'label_name' => fake()->company().' Records',
        ];
    }
}
