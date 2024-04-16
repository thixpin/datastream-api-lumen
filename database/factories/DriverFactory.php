<?php

namespace Database\Factories;

use App\Models\Driver;
use Illuminate\Database\Eloquent\Factories\Factory;

class DriverFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Driver::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'home_location_point' => generateRandomCoordinates(),
            'driving_radius_miles' => $this->faker->randomFloat($nbMaxDecimals = 2, $min = 0, $max = 10),
        ];
    }
}
