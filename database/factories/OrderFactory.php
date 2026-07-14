<?php

namespace Database\Factories;

use App\Enums\PhysicalOrderStatus;
use App\Models\Client;
use App\Models\Order;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'profile_id' => Profile::factory(),
            'qty_small' => fake()->numberBetween(0, 5),
            'qty_large' => fake()->numberBetween(0, 5),
            'price_small' => fake()->randomFloat(2, 5, 20),
            'price_large' => fake()->randomFloat(2, 10, 40),
            'status' => PhysicalOrderStatus::New,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'address1' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'zip' => fake()->postcode(),
            'country' => 'Australia',
            'email' => fake()->safeEmail(),
            'contact' => fake()->phoneNumber(),
            'ordered_on' => now(),
        ];
    }
}
