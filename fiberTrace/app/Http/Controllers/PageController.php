<?php

namespace App\Http\Controllers;

use App\Models\MarketPrice;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;

class PageController extends Controller
{
    public function welcome()
    {
        // Get the latest market prices for the live ticker
        $marketPrices = MarketPrice::latest()->take(8)->get();
        
        // Calculate some dynamic stats for the impact section
        $verifiedMills = User::where('status', 'verified')->count();
        $totalTransactions = Transaction::whereIn('payment_status', ['paid', 'released'])->count();
        $totalYield = Transaction::whereIn('payment_status', ['paid', 'released'])->sum('actual_weight_kg');
        if ($totalYield == 0) {
            $totalYield = Transaction::whereIn('payment_status', ['paid', 'released'])->sum('agreed_price'); // fallback
        }
        
        // Ensure we show at least 8k if database is fresh
        $tonsDiverted = max(8, round($totalYield / 1000));

        return view('welcome', compact('marketPrices', 'verifiedMills', 'totalTransactions', 'tonsDiverted'));
    }
}
