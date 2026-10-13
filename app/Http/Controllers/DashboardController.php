<?php

namespace App\Http\Controllers;

use App\Models\DailyActivity;
use App\Models\ExcelModule;
use App\Models\ExcelSubmission;
use App\Models\Outlet;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Default periode bulan (YYYY-MM)
        $period = $request->input('period', now()->format('Y-m'));
        $today = Carbon::today();
        
        // Data Kegiatan Kanban
        $tasks = Task::with('user')->orderBy('created_at', 'desc')->get()->groupBy('status');
        $users = User::all();
        $outlets = Outlet::all();

        // Modul Form Excel yang aktif
        $modules = ExcelModule::where('is_active', true)->get();

        // Data Submission Hari Ini
        $todaySubmissions = ExcelSubmission::with(['user', 'module', 'outlet'])
            ->whereDate('submission_date', $today)
            ->get();

        // Pengajuan yang Menunggu Review Leader
        $pendingReviews = ExcelSubmission::with(['user', 'module', 'outlet'])
            ->where('status', 'Menunggu Review')
            ->orderBy('created_at', 'desc')
            ->get();

        // Aktivitas Checklist Hari Ini
        $todayActivities = DailyActivity::with(['user', 'outlet'])
            ->whereDate('date', $today)
            ->get();

        // Perhitungan KPI Dinamis
        $totalModulesCount = max($modules->count(), 4);
        $approvedSubmissionsCount = $todaySubmissions->where('status', 'Lengkap')->count();
        $submittedCount = $todaySubmissions->count();
        
        // Persentase kepatuhan: rasio disetujui / total modul
        $compliancePercent = min(100, round(($approvedSubmissionsCount / $totalModulesCount) * 100));
        
        // Outlet/divisi yang belum closing hari ini
        $closingSubmittedCount = $todayActivities->where('shift', 'Closing')->count();
        $outletsNotClosing = max(0, 2 - $closingSubmittedCount);

        // Rekap Data untuk Halaman Reports
        $reportDate = $request->input('report_date', $today->toDateString());
        $reportDivision = $request->input('report_division', 'all');
        $reportShift = $request->input('report_shift', 'all');

        $reportsQuery = DailyActivity::with(['user', 'outlet'])
            ->whereDate('date', $reportDate);

        if ($reportDivision !== 'all') {
            $reportsQuery->where('division', $reportDivision);
        }
        if ($reportShift !== 'all') {
            $reportsQuery->where('shift', $reportShift);
        }
        $reportActivities = $reportsQuery->get();

        return view('welcome', [
            'currentPeriod' => $period,
            'tasks' => $tasks,
            'users' => $users,
            'outlets' => $outlets,
            'modules' => $modules,
            'todaySubmissions' => $todaySubmissions,
            'pendingReviews' => $pendingReviews,
            'todayActivities' => $todayActivities,
            'reportActivities' => $reportActivities,
            'kpi' => [
                'compliancePercent' => $compliancePercent,
                'totalModulesCount' => $totalModulesCount,
                'submittedCount' => $submittedCount,
                'approvedCount' => $approvedSubmissionsCount,
                'pendingCount' => $pendingReviews->count(),
                'notClosingCount' => $outletsNotClosing,
            ],
        ]);
    }
}
