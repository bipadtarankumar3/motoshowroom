<?php

namespace Database\Factories;

use App\Models\Bike;
use App\Models\BikeVariant;
use App\Models\Showroom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BikeVariant>
 */
class BikeVariantFactory extends Factory
{
    protected $model = BikeVariant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Showroom::factory(),
            'bike_id' => Bike::factory(),
            'name' => fake()->randomElement(['Standard ABS', 'Special Edition', 'Racing Trim', 'Dark Stealth Edition']),
            'color_name' => fake()->randomElement(['Matte Black', 'Racing Blue', 'Flame Red', 'Silver Titanium', 'Emerald Green']),
            'color_hex' => fake()->randomElement(['#1A1A1A', '#0D47A1', '#D50000', '#757575', '#004D40']),
            'price_difference' => fake()->randomElement([0.00, 5000.00, 12000.00, 25000.00]),
            'stock_quantity' => fake()->numberBetween(1, 8),
        ];
    }
}
