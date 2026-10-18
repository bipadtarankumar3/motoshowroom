<?php

namespace Tests\Feature;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationalReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_http_health_check_endpoint_returns_ok(): void
    {
        $response = $this->get('/up');
        $response->assertOk();
    }

    public function test_system_health_command_executes_successfully(): void
    {
        $this->artisan('system:health')
            ->expectsOutputToContain('MOTOSHOWROOM SAAS - OPERATIONAL HEALTH CHECK')
            ->expectsOutputToContain('Database')
            ->expectsOutputToContain('Cache')
            ->expectsOutputToContain('Storage')
            ->assertSuccessful();
    }

    public function test_system_health_command_json_output_mode(): void
    {
        $this->artisan('system:health', ['--json' => true])
            ->expectsOutputToContain('"status": "healthy"')
            ->assertSuccessful();
    }

    public function test_subscription_expiry_reminders_command_executes_successfully(): void
    {
        $this->artisan('subscriptions:send-expiry-reminders', ['--days' => 7])
            ->expectsOutputToContain('active subscription(s) expiring within 7 days.')
            ->assertSuccessful();
    }

    public function test_scheduled_tasks_are_properly_registered(): void
    {
        $schedule = app(Schedule::class);
        $events = collect($schedule->events());

        $commandSignatures = $events->map(function (Event $event) {
            return $event->command;
        })->filter();

        $hasExpiryReminders = $commandSignatures->contains(function ($cmd) {
            return str_contains($cmd, 'subscriptions:send-expiry-reminders');
        });

        $hasHealthProbe = $commandSignatures->contains(function ($cmd) {
            return str_contains($cmd, 'system:health');
        });

        $this->assertTrue($hasExpiryReminders, 'subscriptions:send-expiry-reminders must be registered in the console scheduler.');
        $this->assertTrue($hasHealthProbe, 'system:health must be registered in the console scheduler.');
    }
}
