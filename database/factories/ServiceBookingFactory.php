<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\ServiceType;
use App\Models\ServiceBooking;
use App\Models\Showroom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceBooking>
 */
class ServiceBookingFactory extends Factory
{
    protected $model = ServiceBooking::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $serviceType = fake()->randomElement(ServiceType::cases());
        $timeSlot = fake()->randomElement(ServiceBooking::TIME_SLOTS);
        $date = fake()->dateTimeBetween('now', '+14 days')->format('Y-m-d');

        return [
            'tenant_id' => Showroom::factory(),
            'user_id' => null,
            'bike_id' => null,
            'booking_number' => 'SRV-'.date('Ymd').'-'.strtoupper(fake()->unique()->bothify('??##')),
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => fake()->numerify('+91 98########'),
            'bike_make_model' => fake()->randomElement([
                'Royal Enfield Classic 350',
                'Yamaha YZF-R15 V4',
                'KTM Duke 390',
                'Bajaj Pulsar NS200',
                'TVS Apache RTR 160 4V',
                'Honda CB350 H\'ness',
            ]),
            'registration_number' => strtoupper(fake()->bothify('??-##-??-####')),
            'odometer_reading' => fake()->numberBetween(1500, 45000),
            'service_type' => $serviceType->value,
            'booking_date' => $date,
            'time_slot' => $timeSlot,
            'bay_number' => 'Bay '.fake()->numberBetween(1, 4),
            'status' => BookingStatus::Pending->value,
            'estimated_cost' => $serviceType->estimatedPrice(),
            'final_cost' => null,
            'payment_status' => PaymentStatus::Pending->value,
            'customer_notes' => fake()->sentence(),
            'technician_notes' => null,
            'assigned_to_user_id' => null,
            'confirmed_at' => null,
            'checked_in_at' => null,
            'in_service_at' => null,
            'ready_at' => null,
            'completed_at' => null,
        ];
    }

    /**
     * State for confirmed appointments.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::Confirmed->value,
            'confirmed_at' => now(),
        ]);
    }

    /**
     * State for checked-in bikes.
     */
    public function checkedIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::CheckedIn->value,
            'confirmed_at' => now()->subHours(2),
            'checked_in_at' => now()->subHour(),
        ]);
    }

    /**
     * State for bikes currently in service bay.
     */
    public function inService(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::InService->value,
            'confirmed_at' => now()->subHours(3),
            'checked_in_at' => now()->subHours(2),
            'in_service_at' => now()->subMinutes(45),
        ]);
    }

    /**
     * State for bikes ready for customer collection.
     */
    public function ready(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::ReadyForCollection->value,
            'confirmed_at' => now()->subHours(5),
            'checked_in_at' => now()->subHours(4),
            'in_service_at' => now()->subHours(3),
            'ready_at' => now()->subMinutes(15),
        ]);
    }

    /**
     * State for completed bookings.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::Completed->value,
            'confirmed_at' => now()->subHours(6),
            'checked_in_at' => now()->subHours(5),
            'in_service_at' => now()->subHours(4),
            'ready_at' => now()->subHours(1),
            'completed_at' => now(),
            'final_cost' => $attributes['estimated_cost'] ?? 1499.00,
            'payment_status' => PaymentStatus::Captured->value,
        ]);
    }
}
