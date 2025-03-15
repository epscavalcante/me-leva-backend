<?php

namespace Database\Seeders;

use App\Account as AccountModel;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        AccountModel::factory()->passenger()->create([
            'first_name' => 'Passenger',
            'last_name' => 'Account',
            'email' => 'passenger.account@example.com',
        ]);
        AccountModel::factory()->driver()->create([
            'first_name' => 'Driver',
            'last_name' => 'Account',
            'email' => 'driver.account@example.com',
        ]);
    }
}
