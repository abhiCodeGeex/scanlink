<?php

namespace Database\Factories;

use App\Enums\ClientUserRole;
use App\Models\Client;
use App\Models\ClientUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClientUser>
 */
class ClientUserFactory extends Factory
{
    protected $model = ClientUser::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'changeme',
            'role' => ClientUserRole::Primary,
            'status' => true,
            'video_upload' => true,
            'checklist_option' => false,
            'customqr_option' => false,
            'is_password_change' => true,
            'expire_at' => now()->addYear(),
            'is_sub_user' => false,
        ];
    }

    public function primary(): static
    {
        return $this->state(fn (): array => [
            'role' => ClientUserRole::Primary,
            'is_sub_user' => false,
        ]);
    }

    public function subUser(): static
    {
        return $this->state(fn (): array => [
            'role' => ClientUserRole::SubUser,
            'is_sub_user' => true,
        ]);
    }
}
