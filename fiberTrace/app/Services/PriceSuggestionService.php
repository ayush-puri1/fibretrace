<?php

namespace App\Services;

use App\Models\MarketPrice;
use App\Models\SystemSetting;

/**
 * PriceSuggestionService
 *
 * Calculates a smart suggested base price for a new lot listing.
 * Formula: BaseMarketPrice × PurityCoefficient × ColorSortCoefficient
 *
 * All coefficients are driven from system_settings (editable by super-admin),
 * so the business rules can be adjusted live without a code deploy.
 */
class PriceSuggestionService
{
    /**
     * Suggest a price per kg for a given fiber type.
     *
     * @param  string  $fiberType     e.g. "100% Cotton", "Poly-Blend 65/35"
     * @param  int     $purityPct     Fiber purity percentage (0–100)
     * @param  bool    $colorSorted   Whether the lot has been color-sorted
     * @return float   Suggested price in ₹ per kg
     */
    public function suggest(string $fiberType, int $purityPct, bool $colorSorted): float
    {
        // Step 1: Get the latest weekly market price for this fiber type.
        // Falls back to ₹35/kg if no market data exists yet.
        $basePrice = MarketPrice::latestForFiber($fiberType)->price_per_kg;

        // Step 2: Purity Premium — if fiber purity >= 95%, apply a premium coefficient.
        // This rewards high-quality, single-fiber lots (easier to recycle).
        $purityThreshold = (int) SystemSetting::get('purity_premium_threshold', 95);
        $purityCoeff = ($purityPct >= $purityThreshold)
            ? (float) SystemSetting::get('purity_premium_coefficient', 1.15)
            : 1.0;

        // Step 3: Color Sort Premium — sorted lots command higher prices because
        // recyclers don't need to do color separation themselves.
        $colorCoeff = $colorSorted
            ? (float) SystemSetting::get('color_sort_premium_coefficient', 1.08)
            : 1.0;

        // Final price = base × purity multiplier × color multiplier
        return round($basePrice * $purityCoeff * $colorCoeff, 2);
    }

    /**
     * Get a breakdown of how the price was calculated.
     * Useful for showing the seller a transparent explanation on the UI.
     */
    public function breakdown(string $fiberType, int $purityPct, bool $colorSorted): array
    {
        $basePrice       = MarketPrice::latestForFiber($fiberType)->price_per_kg;
        $purityThreshold = (int) SystemSetting::get('purity_premium_threshold', 95);
        $purityCoeff     = ($purityPct >= $purityThreshold)
            ? (float) SystemSetting::get('purity_premium_coefficient', 1.15)
            : 1.0;
        $colorCoeff = $colorSorted
            ? (float) SystemSetting::get('color_sort_premium_coefficient', 1.08)
            : 1.0;

        return [
            'market_base_price'   => $basePrice,
            'purity_coefficient'  => $purityCoeff,
            'color_coefficient'   => $colorCoeff,
            'suggested_price'     => round($basePrice * $purityCoeff * $colorCoeff, 2),
            'purity_applied'      => $purityPct >= $purityThreshold,
            'color_applied'       => $colorSorted,
        ];
    }
}
