<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('accounts')
            ->insertOrIgnore([
                'account_id' => 'afaa5883-8c01-4975-ab63-8e14e928ab53',
                'first_name' => 'Passenger',
                'last_name' => 'Account',
                'is_driver' => false,
                'is_passenger' => true,
                'phone' => '12345678901',
                'email' => 'passenger.account@example.com',
            ]);
        DB::table('accounts')
            ->insertOrIgnore([
                'account_id' => '742d5c56-52e0-47ce-bf77-2bf2c3290232',
                'first_name' => 'Driver',
                'last_name' => 'Account',
                'is_driver' => true,
                'is_passenger' => false,
                'phone' => '12345678901',
                'email' => 'driver.account@example.com',
            ]);
    }
}
