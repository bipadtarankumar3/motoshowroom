<?php

namespace Database\Factories;

use App\Models\Bike;
use App\Models\BikeImage;
use App\Models\Showroom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BikeImage>
 */
class BikeImageFactory extends Factory
{
    protected $model = BikeImage::class;

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
            'image_url' => 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=1200&q=80',
            'alt_text' => fake()->words(3, true),
            'is_primary' => false,
            'sort_order' => 0,
        ];
    }

    /**
     * Mark as primary display image.
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }
}
