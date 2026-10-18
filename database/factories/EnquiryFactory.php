<?php

namespace Database\Factories;

use App\Enums\EnquiryStatus;
use App\Enums\EnquiryType;
use App\Models\Bike;
use App\Models\Enquiry;
use App\Models\Showroom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enquiry>
 */
class EnquiryFactory extends Factory
{
    protected $model = Enquiry::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Showroom::factory(),
            'user_id' => null,
            'bike_id' => Bike::factory(),
            'enquiry_number' => 'ENQ-'.date('Ymd').'-'.strtoupper(fake()->unique()->bothify('??##')),
            'type' => fake()->randomElement(EnquiryType::cases())->value,
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => fake()->numerify('+91 98########'),
            'preferred_date' => fake()->dateTimeBetween('now', '+14 days')->format('Y-m-d'),
            'preferred_time_slot' => fake()->randomElement(['10:00 - 12:00', '12:00 - 14:00', '14:00 - 16:00', '16:00 - 18:00']),
            'driving_license_number' => 'DL-'.fake()->bothify('##-####-#######'),
            'status' => EnquiryStatus::New->value,
            'customer_notes' => fake()->sentence(),
            'staff_notes' => null,
            'assigned_to_user_id' => null,
            'scheduled_at' => null,
            'contacted_at' => null,
            'completed_at' => null,
        ];
    }

    /**
     * State for test ride requests.
     */
    public function testRide(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => EnquiryType::TestRide->value,
        ]);
    }

    /**
     * State for purchase enquiries.
     */
    public function purchase(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => EnquiryType::Purchase->value,
        ]);
    }

    /**
     * State for contacted leads.
     */
    public function contacted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EnquiryStatus::Contacted->value,
            'contacted_at' => now(),
        ]);
    }

    /**
     * State for scheduled test ride leads.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EnquiryStatus::TestRideScheduled->value,
            'scheduled_at' => now()->addDays(2),
        ]);
    }
}
