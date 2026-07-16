<?php

namespace Database\Factories;

use App\Models\Client;
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
            'type_id' => 1,
            'name' => $name,
            'code_profile_name' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 999),
            'identification' => fake()->bothify('ID-####'),
            'deleted' => false,
            'update_or_not' => true,
        ];
    }
}
