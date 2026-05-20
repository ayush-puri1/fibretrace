<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\MarketPrice;
use App\Models\SystemSetting;
use App\Services\PriceSuggestionService;
use App\Models\User;

class PriceSuggestionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_price_suggestion_calculates_correct_coefficients()
    {
        // Setup data
        $admin = User::factory()->create(['role' => 'super_admin']);
        
        SystemSetting::create(['key' => 'purity_premium_coefficient', 'value' => '1.15', 'type' => 'number', 'description' => '']);
        SystemSetting::create(['key' => 'color_sort_premium_coefficient', 'value' => '1.08', 'type' => 'number', 'description' => '']);
        
        MarketPrice::create([
            'fiber_category' => '100% Cotton (White/Raw)',
            'sub_label' => 'Standard',
            'price_per_kg' => 46.00,
            'week_start' => now()->startOfWeek(),
            'published_by' => $admin->id,
        ]);

        $service = new PriceSuggestionService();

        // Test 1: No premium (purity < 95, colorSorted false)
        $price1 = $service->suggest('100% Cotton (White/Raw)', 90, false);
        $this->assertEquals(46.00, $price1);

        // Test 2: Purity premium only (purity >= 95, colorSorted false)
        $price2 = $service->suggest('100% Cotton (White/Raw)', 98, false);
        $this->assertEquals(round(46.00 * 1.15, 2), $price2);

        // Test 3: Color premium only (purity < 95, colorSorted true)
        $price3 = $service->suggest('100% Cotton (White/Raw)', 90, true);
        $this->assertEquals(round(46.00 * 1.08, 2), $price3);

        // Test 4: Both premiums
        $price4 = $service->suggest('100% Cotton (White/Raw)', 99, true);
        $this->assertEquals(round(46.00 * 1.15 * 1.08, 2), $price4);
    }
}
