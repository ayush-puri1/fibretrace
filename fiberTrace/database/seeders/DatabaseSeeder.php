<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SystemSettingSeeder::class,
            MarketPriceSeeder::class,
            LotSeeder::class,
            BidSeeder::class,
            TransactionSeeder::class,
        ]);
    }
}
