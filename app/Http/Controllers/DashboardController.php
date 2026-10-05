<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Default to current month (YYYY-MM) if not provided
        $period = $request->input('period', now()->format('Y-m'));
        
        // Mock data logic (will be replaced with actual DB queries later)
        // e.g., $submissions = ExcelSubmission::where('period', $period)->get();

        return view('welcome', [
            'currentPeriod' => $period
        ]);
    }
}
