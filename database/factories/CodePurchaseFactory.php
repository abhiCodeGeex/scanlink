<?php

namespace Database\Factories;

use App\Enums\CodeOrderStatus;
use App\Models\Client;
use App\Models\CodePurchase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CodePurchase>
 */
class CodePurchaseFactory extends Factory
{
    protected $model = CodePurchase::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'email' => fake()->companyEmail(),
            'town' => fake()->city(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'company_name' => fake()->company(),
            'billing_address' => fake()->streetAddress(),
            'phone' => fake()->phoneNumber(),
            'postal_code' => fake()->postcode(),
            'no_of_codes' => $codes = fake()->numberBetween(1, 50),
            'per_code_amount' => $amount = fake()->randomFloat(2, 5, 25),
            'total_amount' => $codes * $amount,
            'status' => CodeOrderStatus::Paid,
            'enable' => true,
            'exipry_date' => now()->addYear(),
            'is_reseller_pricing_code' => false,
            'free_code' => false,
            'ordered_on' => now(),
        ];
    }

    public function statusNew(): static
    {
        return $this->state(fn (): array => ['status' => CodeOrderStatus::New]);
    }

    public function invoiceSend(): static
    {
        return $this->state(fn (): array => ['status' => CodeOrderStatus::InvoiceSend]);
    }

    public function freeCode(): static
    {
        return $this->state(fn (): array => [
            'status' => CodeOrderStatus::New,
            'free_code' => true,
            'per_code_amount' => 0,
            'total_amount' => 0,
        ]);
    }
}
