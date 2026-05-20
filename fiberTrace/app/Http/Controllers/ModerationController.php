<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ModerationController extends Controller
{
    /** List all lots for moderation — pending, flagged, active, and suspended */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'pending');

        $lots = Lot::with(['seller', 'reports.reporter', 'images'])
            ->when($filter === 'pending', fn($q) => $q->where('status', 'pending_review')->orderByDesc('created_at'))
            ->when($filter === 'flagged', fn($q) => $q->where('flagged', true)->orderByDesc('flag_count'))
            ->when($filter === 'all', fn($q) => $q->where('status', 'active')->orderByDesc('created_at'))
            ->when($filter === 'suspended', fn($q) => $q->where('status', 'suspended')->orderByDesc('updated_at'))
            ->paginate(20);

        $counts = [
            'pending'   => Lot::where('status', 'pending_review')->count(),
            'flagged'   => Lot::where('flagged', true)->count(),
            'all'       => Lot::where('status', 'active')->count(),
            'suspended' => Lot::where('status', 'suspended')->count(),
        ];

        return view('admin.moderation', compact('lots', 'counts', 'filter'));
    }

    /** Approve a new listing (moves from pending_review to active) */
    public function approve(Request $request, Lot $lot)
    {
        abort_if($lot->status !== 'pending_review', 422, 'Only lots pending review can be approved.');

        $lot->update(['status' => 'active']);

        ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'lot_approved',
            'subject_type' => Lot::class,
            'subject_id'   => $lot->id,
            'description'  => "Approved lot {$lot->lot_number} for active trading.",
            'ip_address'   => $request->ip(),
        ]);

        return back()->with('success', "Lot {$lot->lot_number} has been approved and is now active on the market.");
    }

    /** Suspend a flagged lot (removes from auction floor) */
    public function suspend(Request $request, Lot $lot)
    {
        $request->validate(['reason' => ['nullable', 'string', 'max:500']]);

        $lot->update(['status' => 'suspended']);

        ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'lot_suspended',
            'subject_type' => Lot::class,
            'subject_id'   => $lot->id,
            'description'  => "Suspended lot {$lot->lot_number}. Reason: " . ($request->reason ?? 'Policy violation'),
            'metadata'     => ['reason' => $request->reason],
            'ip_address'   => $request->ip(),
        ]);

        return back()->with('success', "Lot {$lot->lot_number} has been suspended.");
    }

    /** Restore a suspended lot back to active */
    public function restore(Request $request, Lot $lot)
    {
        $lot->update(['status' => 'active', 'flagged' => false, 'flag_count' => 0]);

        ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'lot_restored',
            'subject_type' => Lot::class,
            'subject_id'   => $lot->id,
            'description'  => "Restored lot {$lot->lot_number} to active status.",
            'ip_address'   => $request->ip(),
        ]);

        return back()->with('success', "Lot {$lot->lot_number} restored to active.");
    }
}
