<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    /**
     * PII & Transaction Audit Ledger.
     * Shows all activity_logs with filters. Only admins/super_admins can access.
     * PII (phone, gstin) masked for admin — only super_admin sees full data.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')
            ->orderByDesc('created_at');

        // Filter by action type
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $logs = $query->paginate(30);

        // Distinct action types for filter dropdown
        $actionTypes = ActivityLog::distinct()->pluck('action')->sort()->values();

        // Recent transactions for the financial audit section
        $transactions = Transaction::with(['lot', 'buyer', 'seller'])
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        $isSuperAdmin = auth()->user()->isSuper();

        return view('admin.audit', compact('logs', 'actionTypes', 'transactions', 'isSuperAdmin'));
    }
}
