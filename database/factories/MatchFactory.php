<?php

namespace Database\Factories;

use App\Models\Matches;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Matches>
 */
class MatchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'week_label' => $this->faker->regexify('202[5-6]-KW[0-5][1-9]'),
            'challenge_text' => $this->faker->paragraph(3),
            'time_limit_minutes' => $this->faker->randomElement([15, 20, 30, 45]),
            'submission_url' => $this->faker->optional(0.7)->url(),
            'submitted_at' => $this->faker->optional(0.7)->dateTimeBetween('-1 week', 'now'),
            'status' => $this->faker->randomElement(['pending', 'submitted', 'closed'])
        ];
    }
}
