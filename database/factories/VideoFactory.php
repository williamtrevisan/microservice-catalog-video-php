<?php

namespace Database\Factories;

use Core\Domain\Enum\Rating;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Video>
 */
class VideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'id' => Str::uuid()->toString(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->sentence(10),
            'year_launched' => now()->addYears(2)->format('Y'),
            'opened' => true,
            'rating' => Rating::L,
            'duration' => 1,
            'created_at' => now(),
        ];
    }
}
