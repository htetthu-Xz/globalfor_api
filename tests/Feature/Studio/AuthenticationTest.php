<?php

namespace Tests\Feature\Studio;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_administrator_can_sign_in_and_sign_out(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'Correct-Horse-2026!',
            'is_active' => true,
        ]);

        $response = $this->post('/studio/login', [
            'email' => 'ADMIN@example.com',
            'password' => 'Correct-Horse-2026!',
        ]);

        $response->assertRedirect('/studio/activities');
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->last_login_at);

        $this->post('/studio/logout')->assertRedirect('/studio/login');
        $this->assertGuest();
    }

    public function test_inactive_administrator_cannot_sign_in(): void
    {
        User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => 'Correct-Horse-2026!',
            'is_active' => false,
        ]);

        $this->from('/studio/login')->post('/studio/login', [
            'email' => 'inactive@example.com',
            'password' => 'Correct-Horse-2026!',
        ])->assertRedirect('/studio/login')->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_studio_is_private_and_registration_is_disabled(): void
    {
        $this->get('/studio/activities')->assertRedirect('/studio/login');
        $this->get('/studio/register')->assertNotFound();
        $this->post('/studio/register')->assertNotFound();
    }

    public function test_password_reset_uses_laravel_notification_flow(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'admin@example.com']);

        $this->post('/studio/forgot-password', ['email' => $user->email])
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
    }
}
