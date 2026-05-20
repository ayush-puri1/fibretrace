<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'platform_commission',
                'value' => '1.50',
                'type' => 'number',
                'description' => 'Platform Commission per kg (₹)',
            ],
            [
                'key' => 'escrow_release_delay',
                'value' => '48',
                'type' => 'number',
                'description' => 'Escrow Release Delay (hours) after Delivered status',
            ],
            [
                'key' => 'purity_premium_coefficient',
                'value' => '1.15',
                'type' => 'number',
                'description' => 'Purity Premium Coefficient (for >95% primary fiber)',
            ],
            [
                'key' => 'color_sort_premium_coefficient',
                'value' => '1.08',
                'type' => 'number',
                'description' => 'Color Sort Premium Coefficient (if color-sorted)',
            ],
            [
                'key' => 'min_lot_weight',
                'value' => '100',
                'type' => 'number',
                'description' => 'Minimum Lot Weight (kg)',
            ],
            [
                'key' => 'max_lot_weight',
                'value' => '25000',
                'type' => 'number',
                'description' => 'Maximum Lot Weight (kg)',
            ],
            [
                'key' => 'auction_duration',
                'value' => '24',
                'type' => 'number',
                'description' => 'Auction Duration (hours)',
            ],
            [
                'key' => 'weight_variance_tolerance',
                'value' => '10',
                'type' => 'number',
                'description' => 'Weight Variance Tolerance (± percentage)',
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('system_settings')->updateOrInsert(['key' => $setting['key']], $setting);
        }
    }
}
