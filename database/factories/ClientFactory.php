<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            'client_name' => $name,
            'address' => fake()->streetAddress(),
            'telephone' => fake()->phoneNumber(),
            'contact_person' => fake()->name(),
            'regi_date' => fake()->date(),
            'email' => fake()->unique()->companyEmail(),
            'password' => 'changeme',
            'url' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 999),
            'approve' => true,
            'reseller_code' => null,
            'reseller_email' => null,
            'is_password_change' => true,
        ];
    }

    public function blocked(): static
    {
        return $this->state(fn (): array => ['approve' => false]);
    }
}
