<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->city();
        $latitude = $this->faker->unique()->latitude;
        $longitude = $this->faker->unique()->longitude;

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'name' => $name,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];
    }
}
