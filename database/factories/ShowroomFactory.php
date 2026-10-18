<?php

namespace Database\Factories;

use App\Models\Showroom;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Showroom>
 */
class ShowroomFactory extends Factory
{
    protected $model = Showroom::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company().' Moto World';
        $slug = Str::slug($name).'-'.fake()->unique()->numberBetween(100, 9999);

        return [
            'name' => $name,
            'slug' => $slug,
            'custom_domain' => null,
            'domain_verified_at' => null,
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('+91 98########'),
            'address_line1' => fake()->streetAddress(),
            'address_line2' => fake()->secondaryAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'postal_code' => fake()->numerify('######'),
            'country' => 'India',
            'logo_url' => null,
            'banner_url' => null,
            'brand_color_primary' => '#f97316',
            'brand_color_accent' => '#06b6d4',
            'status' => 'active',
            'public_website_enabled' => true,
            'read_only_mode' => false,
            'settings' => [],
        ];
    }

    /**
     * Indicate that the showroom's public website is disabled.
     */
    public function websiteDisabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'public_website_enabled' => false,
        ]);
    }

    /**
     * Indicate that the showroom is in read-only mode.
     */
    public function readOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_only_mode' => true,
        ]);
    }
}
