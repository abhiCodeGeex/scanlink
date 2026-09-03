<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Models\User;
use Filament\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ExpiredPasswordLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_migrated_blank_password_user_gets_reset_email_and_expired_notice(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'migrated@example.com',
            'user_type' => UserType::Portal,
            'admin_role' => null,
        ]);

        // The migration marks MD5 accounts with a BLANK password (raw, bypassing the cast).
        DB::table('users')->where('id', $user->id)->update(['password' => '']);

        $response = $this->post('/portal-login', [
            'email' => 'migrated@example.com',
            'password' => 'their-old-legacy-password',
        ]);

        // No login; instead they're bounced back with the "expired" notice for the popup...
        $this->assertGuest();
        $response->assertRedirect();
        $response->assertSessionHas('password_expired_email', 'migrated@example.com');
        $response->assertSessionHasNoErrors();

        // ...and a reset link email was sent (same as forgot-password).
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_wrong_password_for_a_normal_account_still_shows_invalid_credentials(): void
    {
        Notification::fake();

        User::factory()->create([
            'email' => 'active@example.com',
            'password' => 'Correct@12345',
            'user_type' => UserType::Portal,
            'admin_role' => null,
        ]);

        $response = $this->post('/portal-login', [
            'email' => 'active@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
        $response->assertSessionMissing('password_expired_email');
        Notification::assertNothingSent();
    }
}
