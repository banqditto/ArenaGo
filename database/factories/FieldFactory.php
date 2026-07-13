<?php

namespace Database\Factories;

use App\Models\Field;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Field>
 */
class FieldFactory extends Factory
{
    protected $model = Field::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' Field',
            'type' => $this->faker->randomElement(['Futsal', 'Badminton', 'Basketball']),
            'price_per_hour' => $this->faker->randomElement([50000, 75000, 100000, 150000]),
            'image_path' => null,
        ];
    }
}
