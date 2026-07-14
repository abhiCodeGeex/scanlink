<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Profile>
 */
class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'client_id' => Client::factory(),
            'user_id' => null,
            'type_id' => EquipmentType::factory(),
            'name' => $name,
            'code_profile_name' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 999),
            'identification' => fake()->bothify('ID-####'),
            'address' => fake()->streetAddress(),
            'description' => fake()->sentence(),
            'protect' => false,
            'code_type' => '0',
            'deleted' => false,
            'update_or_not' => true,
            'free_code' => false,
            'expired_at' => now()->addYear(),
        ];
    }

    public function deleted(): static
    {
        return $this->state(fn (): array => ['deleted' => true]);
    }

    public function expired(): static
    {
        return $this->state(fn (): array => ['expired_at' => now()->subDay()]);
    }

    public function forOwner(ClientUser $user): static
    {
        return $this->state(fn (): array => [
            'client_id' => $user->client_id,
            'user_id' => $user->id,
        ]);
    }
}
