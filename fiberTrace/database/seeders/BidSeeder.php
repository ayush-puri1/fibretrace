<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BidSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        
        $buyer1 = DB::table('users')->where('email', 'contact@panipatspinners.in')->first();
        $buyer2 = DB::table('users')->where('email', 'info@haryanathreads.com')->first();
        
        $activeLot = DB::table('lots')->where('lot_number', 'FT-1001')->first();
        $awardedLot = DB::table('lots')->where('lot_number', 'FT-1003')->first();
        $settledLot = DB::table('lots')->where('lot_number', 'FT-1004')->first();

        // Bids for Active Lot (FT-1001, Base: 45.00)
        $bid1 = DB::table('bids')->insertGetId([
            'lot_id' => $activeLot->id,
            'buyer_id' => $buyer1->id,
            'amount' => 45.50,
            'status' => 'outbid',
            'created_at' => $now->copy()->subHours(10),
            'updated_at' => $now->copy()->subHours(9),
        ]);
        
        $bid2 = DB::table('bids')->insertGetId([
            'lot_id' => $activeLot->id,
            'buyer_id' => $buyer2->id,
            'amount' => 46.00,
            'status' => 'active',
            'created_at' => $now->copy()->subHours(9),
            'updated_at' => $now->copy()->subHours(9),
        ]);
        
        // Update highest bid on active lot
        DB::table('lots')->where('id', $activeLot->id)->update(['highest_bid_id' => $bid2]);

        // Bids for Awarded Lot (FT-1003, Base: 31.00)
        $bid3 = DB::table('bids')->insertGetId([
            'lot_id' => $awardedLot->id,
            'buyer_id' => $buyer1->id,
            'amount' => 31.50,
            'status' => 'won',
            'created_at' => $now->copy()->subDays(1),
            'updated_at' => $now->copy()->subHours(2),
        ]);
        
        // Update highest bid on awarded lot
        DB::table('lots')->where('id', $awardedLot->id)->update(['highest_bid_id' => $bid3]);

        // Bids for Settled Lot (FT-1004, Base: 28.00)
        $bid4 = DB::table('bids')->insertGetId([
            'lot_id' => $settledLot->id,
            'buyer_id' => $buyer2->id,
            'amount' => 29.00,
            'status' => 'won',
            'created_at' => $now->copy()->subDays(6),
            'updated_at' => $now->copy()->subDays(5),
        ]);
        
        // Update highest bid on settled lot
        DB::table('lots')->where('id', $settledLot->id)->update(['highest_bid_id' => $bid4]);
    }
}
