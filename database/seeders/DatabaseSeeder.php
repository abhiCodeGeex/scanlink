<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            Phase2Seeder::class,
            Phase3Seeder::class,
            Phase5Seeder::class,
        ]);
    }
}
