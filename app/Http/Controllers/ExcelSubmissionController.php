<?php

namespace App\Http\Controllers;

use App\Models\ExcelSubmission;
use App\Models\Outlet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExcelSubmissionController extends Controller
{
    /**
     * Staf mengunggah file form Excel harian/bulanan
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'excel_module_id' => 'required|exists:excel_modules,id',
            'file' => 'required|file|mimes:xlsx,xls,csv|max:15360',
            'notes' => 'nullable|string|max:500',
        ], [
            'excel_module_id.required' => 'Pilih kategori modul form terlebih dahulu.',
            'excel_module_id.exists' => 'Modul form yang dipilih tidak valid.',
            'file.required' => 'Pilih berkas Excel untuk diunggah.',
            'file.mimes' => 'Format berkas harus berupa .xlsx, .xls, atau .csv.',
            'file.max' => 'Ukuran berkas maksimal adalah 15MB.',
        ]);

        $user = Auth::user();
        $outletId = $user->outlet_id ?? Outlet::first()?->id ?? 1;
        $period = Carbon::today()->format('Y-m');

        // Simpan file ke storage public
        $storedPath = $request->file('file')->store('submissions', 'public');

        // Buat atau perbarui submission untuk hari ini dan modul terkait
        $submission = ExcelSubmission::updateOrCreate(
            [
                'outlet_id' => $outletId,
                'excel_module_id' => $validated['excel_module_id'],
                'submission_date' => Carbon::today(),
            ],
            [
                'user_id' => $user->id,
                'period' => $period,
                'file_path' => $storedPath,
                'status' => 'Menunggu Review',
                'notes' => $validated['notes'] ?? 'Menunggu pengecekan kelengkapan dari Store Leader.',
            ]
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'File Excel berhasil diunggah dan masuk antrean verifikasi Leader!',
                'submission' => $submission->load(['module', 'user'])
            ]);
        }

        return redirect()->back()->with('success', 'File Excel berhasil diunggah dan masuk antrean verifikasi Leader!');
    }

    /**
     * Leader memverifikasi / menyetujui / meminta revisi dokumen
     */
    public function verify(Request $request, ExcelSubmission $submission)
    {
        $validated = $request->validate([
            'status' => 'required|in:Lengkap,Perlu Perbaikan',
            'notes' => 'nullable|string|max:500',
        ]);

        $submission->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? ($validated['status'] === 'Lengkap' ? 'Telah disetujui dan diverifikasi oleh Leader.' : 'Mohon perbaiki data yang kurang.'),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status verifikasi berhasil disimpan: ' . $validated['status'],
                'submission' => $submission
            ]);
        }

        return redirect()->back()->with('success', 'Status verifikasi dokumen berhasil diperbarui!');
    }

    /**
     * Unduh berkas form Excel yang diunggah
     */
    public function download(ExcelSubmission $submission)
    {
        if (!$submission->file_path || !Storage::disk('public')->exists($submission->file_path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($submission->file_path);
    }
}
