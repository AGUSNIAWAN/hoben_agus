<?php

namespace App\Http\Controllers;

use App\Models\DailyActivity;
use App\Models\Outlet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DailyActivityController extends Controller
{
    /**
     * Menyimpan atau memperbarui checklist aktivitas shift harian staf
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shift' => 'required|in:Opening,Middle,Closing',
            'division' => 'nullable|string|max:100',
            'checklist_data' => 'required|array',
            'date' => 'nullable|date',
        ]);

        $user = Auth::user();
        $date = $validated['date'] ?? Carbon::today()->toDateString();
        $outletId = $user->outlet_id ?? Outlet::first()?->id ?? 1;
        $division = $validated['division'] ?? $user->division ?? 'General';

        $activity = DailyActivity::updateOrCreate(
            [
                'user_id' => $user->id,
                'outlet_id' => $outletId,
                'date' => $date,
                'shift' => $validated['shift'],
            ],
            [
                'division' => $division,
                'checklist_data' => $validated['checklist_data'],
            ]
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Checklist shift ' . $validated['shift'] . ' berhasil disimpan!',
                'activity' => $activity
            ]);
        }

        return redirect()->back()->with('success', 'Checklist shift ' . $validated['shift'] . ' berhasil disimpan!');
    }
}
