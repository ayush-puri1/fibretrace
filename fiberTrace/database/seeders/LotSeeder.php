<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LotSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        
        $seller1 = DB::table('users')->where('email', 'admin@globaltextiles.com')->first();
        $seller2 = DB::table('users')->where('email', 'sunrisegarments@gmail.com')->first();

        // 1 Active Lot
        DB::table('lots')->insert([
            'seller_id' => $seller1->id,
            'lot_number' => 'FT-1001',
            'category' => 'cutting_scraps',
            'fiber_type' => '100% Cotton',
            'fiber_purity_pct' => 100,
            'color_sorted' => true,
            'color_description' => 'White/Raw',
            'weight_kg' => 5000,
            'base_price' => 45.00,
            'status' => 'active',
            'auction_ends_at' => $now->copy()->addHours(12),
            'created_at' => $now->copy()->subHours(12),
            'updated_at' => $now->copy()->subHours(12),
        ]);

        // 1 Draft Lot
        DB::table('lots')->insert([
            'seller_id' => $seller1->id,
            'lot_number' => 'FT-1002',
            'category' => 'yarn_ends',
            'fiber_type' => 'Poly-Blend',
            'fiber_purity_pct' => 65,
            'color_sorted' => false,
            'color_description' => 'Mixed Colors',
            'weight_kg' => 1200,
            'base_price' => 24.00,
            'status' => 'draft',
            'auction_ends_at' => null,
            'created_at' => $now->copy()->subDays(1),
            'updated_at' => $now->copy()->subDays(1),
        ]);

        // 1 Awarded Lot
        DB::table('lots')->insert([
            'seller_id' => $seller2->id,
            'lot_number' => 'FT-1003',
            'category' => 'cutting_scraps',
            'fiber_type' => 'Denim Offcuts',
            'fiber_purity_pct' => 95,
            'color_sorted' => true,
            'color_description' => 'Blue/Indigo',
            'weight_kg' => 15000,
            'base_price' => 31.00,
            'status' => 'awarded',
            'auction_ends_at' => $now->copy()->subHours(2),
            'created_at' => $now->copy()->subDays(2),
            'updated_at' => $now->copy()->subHours(2),
        ]);

        // 1 Settled Lot
        DB::table('lots')->insert([
            'seller_id' => $seller2->id,
            'lot_number' => 'FT-1004',
            'category' => 'rejected_batches',
            'fiber_type' => '100% Acrylic',
            'fiber_purity_pct' => 100,
            'color_sorted' => false,
            'color_description' => 'Mixed',
            'weight_kg' => 3000,
            'base_price' => 28.00,
            'status' => 'settled',
            'auction_ends_at' => $now->copy()->subDays(5),
            'created_at' => $now->copy()->subDays(7),
            'updated_at' => $now->copy()->subDays(5),
        ]);

        // 1 Cancelled Lot
        DB::table('lots')->insert([
            'seller_id' => $seller1->id,
            'lot_number' => 'FT-1005',
            'category' => 'selvedge',
            'fiber_type' => 'Cotton',
            'fiber_purity_pct' => 90,
            'color_sorted' => false,
            'color_description' => 'Mixed',
            'weight_kg' => 500,
            'base_price' => 35.00,
            'status' => 'cancelled',
            'auction_ends_at' => $now->copy()->subDays(1),
            'created_at' => $now->copy()->subDays(3),
            'updated_at' => $now->copy()->subDays(1),
        ]);

        // Adding mock images
        $lots = DB::table('lots')->get();
        foreach ($lots as $lot) {
            DB::table('lot_images')->insert([
                'lot_id' => $lot->id,
                'file_path' => 'placeholder/image_' . rand(1, 5) . '.jpg',
                'file_name' => 'image_' . rand(1, 5) . '.jpg',
                'file_size' => 102400,
                'sort_order' => 0,
                'created_at' => $now,
            ]);
        }
    }
}
