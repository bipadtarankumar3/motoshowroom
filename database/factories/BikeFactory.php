<?php

namespace Database\Factories;

use App\Enums\BikeCategory;
use App\Enums\BikeStatus;
use App\Models\Bike;
use App\Models\Showroom;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Bike>
 */
class BikeFactory extends Factory
{
    protected $model = Bike::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->randomElement([
            'Apex RR 1000',
            'Thunderbird Phantom 650',
            'Viper Street R 750',
            'CrossTour Dakar 850',
            'UrbanGlide City 300',
            'VoltPulse EV Neo',
            'Predator Stealth 1200',
            'ShadowCraft Bobber 500',
        ]);
        $uniqueSuffix = fake()->unique()->numberBetween(100, 9999);
        $fullName = "{$name} #{$uniqueSuffix}";

        return [
            'tenant_id' => Showroom::factory(),
            'name' => $fullName,
            'slug' => Str::slug($fullName),
            'sku' => 'MOTO-'.strtoupper(Str::random(3)).'-'.fake()->unique()->numerify('####'),
            'brand' => fake()->randomElement(['Yamaha', 'Kawasaki', 'Honda', 'Ducati', 'BMW Motorrad', 'KTM', 'Triumph', 'Royal Enfield']),
            'model_year' => fake()->numberBetween(2023, 2026),
            'category' => fake()->randomElement(BikeCategory::cases())->value,
            'engine_cc' => fake()->randomElement([250, 390, 650, 750, 900, 1000, 1250]),
            'power_bhp' => fake()->randomFloat(2, 25, 215),
            'torque_nm' => fake()->randomFloat(2, 22, 140),
            'fuel_type' => 'petrol',
            'transmission' => fake()->randomElement(['6-speed manual with slipper clutch', '6-speed quickshifter', 'Automatic CVT']),
            'mileage_kmpl' => fake()->randomFloat(2, 14, 45),
            'top_speed_kmh' => fake()->numberBetween(120, 299),
            'curb_weight_kg' => fake()->numberBetween(140, 260),
            'price' => fake()->numberBetween(120000, 1850000),
            'is_featured' => fake()->boolean(25),
            'is_published' => true,
            'status' => BikeStatus::InStock->value,
            'description' => fake()->paragraphs(2, true),
            'specifications' => [
                'Braking' => 'Dual channel Bosch ABS with Brembo Stylema calipers',
                'Suspension' => 'Inverted 43mm adjustable fork, monoshock rear',
                'Fuel Tank' => '17 Litres',
                'Seat Height' => '820 mm',
                'Tyres' => 'Pirelli Diablo Rosso IV (120/70 ZR17 front, 190/55 ZR17 rear)',
            ],
        ];
    }

    /**
     * Indicate that the motorcycle is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the motorcycle is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }

    /**
     * Indicate that the motorcycle is unpublished draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
        ]);
    }

    /**
     * Indicate that the motorcycle is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BikeStatus::OutOfStock->value,
        ]);
    }
}
