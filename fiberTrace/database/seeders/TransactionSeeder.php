<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        
        $awardedLot = DB::table('lots')->where('lot_number', 'FT-1003')->first();
        $settledLot = DB::table('lots')->where('lot_number', 'FT-1004')->first();
        
        $awardedBid = DB::table('bids')->where('lot_id', $awardedLot->id)->where('status', 'won')->first();
        $settledBid = DB::table('bids')->where('lot_id', $settledLot->id)->where('status', 'won')->first();

        // Transaction for Awarded Lot (Pending Payment/Escrow)
        $subtotal1 = $awardedLot->weight_kg * $awardedBid->amount;
        $commission1 = $awardedLot->weight_kg * 1.50; // default 1.50 per kg
        
        DB::table('transactions')->insert([
            'transaction_number' => 'TRX-' . rand(10000, 99999),
            'lot_id' => $awardedLot->id,
            'bid_id' => $awardedBid->id,
            'buyer_id' => $awardedBid->buyer_id,
            'seller_id' => $awardedLot->seller_id,
            'agreed_price' => $awardedBid->amount,
            'actual_weight_kg' => null, // not delivered yet
            'subtotal' => $subtotal1,
            'commission_amount' => $commission1,
            'total_amount' => $subtotal1 + $commission1,
            'payment_status' => 'pending',
            'logistics_status' => 'ready_for_pickup',
            'escrow_released_at' => null,
            'created_at' => $now->copy()->subHours(2),
            'updated_at' => $now->copy()->subHours(2),
        ]);

        // Transaction for Settled Lot (Escrow Released)
        $actualWeight = $settledLot->weight_kg * 0.98; // 2% weight loss
        $subtotal2 = $actualWeight * $settledBid->amount;
        $commission2 = $actualWeight * 1.50; // default 1.50 per kg
        
        DB::table('transactions')->insert([
            'transaction_number' => 'TRX-' . rand(10000, 99999),
            'lot_id' => $settledLot->id,
            'bid_id' => $settledBid->id,
            'buyer_id' => $settledBid->buyer_id,
            'seller_id' => $settledLot->seller_id,
            'agreed_price' => $settledBid->amount,
            'actual_weight_kg' => $actualWeight,
            'subtotal' => $subtotal2,
            'commission_amount' => $commission2,
            'total_amount' => $subtotal2 + $commission2,
            'payment_status' => 'released',
            'logistics_status' => 'confirmed',
            'escrow_released_at' => $now->copy()->subDays(1),
            'created_at' => $now->copy()->subDays(5),
            'updated_at' => $now->copy()->subDays(1),
        ]);
    }
}
