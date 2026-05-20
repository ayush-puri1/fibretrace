<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MarketPriceSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            '100% Cotton (White/Raw)' => [46.00, 45.50, 44.80, 46.20],
            'Cotton (Mixed Color)' => [38.00, 38.20, 37.50, 37.00],
            'Poly-Blend (Mixed)' => [24.00, 23.80, 24.50, 25.00],
            'Denim Offcuts' => [31.80, 32.00, 31.50, 30.80],
            '100% Acrylic' => [28.50, 28.00, 27.50, 28.20],
            'Yarn Ends (Cotton)' => [42.00, 41.50, 42.50, 43.00],
        ];

        // Ensure we have a super admin user to set as 'published_by'
        $superAdmin = DB::table('users')->where('email', 'superadmin@fibretrace.in')->first();
        $publishedBy = $superAdmin ? $superAdmin->id : 1;

        $now = Carbon::now();
        
        foreach ($categories as $category => $prices) {
            foreach ($prices as $index => $price) {
                // $prices[0] is current week, $prices[1] is last week, etc.
                $weekStart = $now->copy()->subWeeks($index)->startOfWeek();
                $previousPrice = isset($prices[$index + 1]) ? $prices[$index + 1] : null;

                DB::table('market_prices')->insert([
                    'fiber_category' => $category,
                    'sub_label' => 'Standard',
                    'price_per_kg' => $price,
                    'previous_price' => $previousPrice,
                    'week_start' => $weekStart->format('Y-m-d'),
                    'published_by' => $publishedBy,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
