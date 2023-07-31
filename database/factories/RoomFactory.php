<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::inRandomOrder()->first()->id,
            'location_id' => Location::inRandomOrder()->first()->id,
            'code' => $this->faker->unique()->bothify('??##??##'), // Generates a random alphanumeric code in the pattern "??##??##"
            'name' => $this->faker->unique()->sentence(),
            'description' => $this->faker->paragraph(),
            'start_date' => $this->faker->dateTimeBetween('now', '+1 week'),
            'end_date' => $this->faker->dateTimeBetween('now', '+2 weeks'),
            'max_players' => $this->faker->randomElement([2, 4, 6]),
            'status' => $this->faker->randomElement(['active', 'deactivated', 'closed']),
            'winners_prize' => $this->faker->randomFloat(2, 0, 1000),
        ];
    }
}
