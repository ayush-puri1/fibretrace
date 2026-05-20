<?php

namespace App\Http\Controllers;

use App\Models\MarketPrice;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class MarketPriceController extends Controller
{
    /**
     * Public market index page — no auth required.
     * Shows latest weekly prices for all fiber categories.
     */
    public function publicIndex()
    {
        // Get the latest price entry per fiber_category
        $prices = MarketPrice::orderByDesc('week_start')
            ->get()
            ->groupBy('fiber_category')
            ->map(fn($group) => $group->first()); // Latest per category

        // Historical data for chart (last 12 weeks)
        $history = MarketPrice::orderByDesc('week_start')
            ->take(72) // 6 categories × 12 weeks
            ->get()
            ->groupBy('fiber_category');

        return view('market-index', compact('prices', 'history'));
    }

    /**
     * Super-admin: manage/update market prices.
     * This is the "Live Market Index Manager" page.
     */
    public function manage()
    {
        $prices = MarketPrice::orderByDesc('week_start')
            ->get()
            ->groupBy('fiber_category')
            ->map(fn($group) => $group->first());

        // Price history for all categories (last 8 weeks each)
        $history = MarketPrice::orderByDesc('week_start')
            ->take(48)
            ->get()
            ->groupBy('fiber_category');

        return view('super-admin.market-index', compact('prices', 'history'));
    }

    /**
     * Super-admin: save a new weekly market price.
     * Creates a new row (preserves history — never overwrites old entries).
     */
    public function update(Request $request)
    {
        $request->validate([
            'fiber_category' => ['required', 'string', 'max:100'],
            'sub_label'      => ['nullable', 'string', 'max:100'],
            'price_per_kg'   => ['required', 'numeric', 'min:0.01'],
            'week_start'     => ['required', 'date'],
        ]);

        // Get previous price for percentage change calculation
        $previous = MarketPrice::where('fiber_category', $request->fiber_category)
            ->orderByDesc('week_start')
            ->first();

        $marketPrice = MarketPrice::create([
            'fiber_category' => $request->fiber_category,
            'sub_label'      => $request->sub_label,
            'price_per_kg'   => $request->price_per_kg,
            'previous_price' => $previous?->price_per_kg,
            'week_start'     => $request->week_start,
            'published_by'   => auth()->id(),
        ]);

        ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'market_price_updated',
            'subject_type' => MarketPrice::class,
            'subject_id'   => $marketPrice->id,
            'description'  => "Updated {$request->fiber_category} price to ₹{$request->price_per_kg}/kg",
            'ip_address'   => $request->ip(),
        ]);

        return back()->with('success', "Market price for {$request->fiber_category} updated to ₹{$request->price_per_kg}/kg.");
    }
}
