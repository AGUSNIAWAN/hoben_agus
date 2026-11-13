<?php

namespace App\Http\Controllers;

use App\Models\ExcelSubmission;
use App\Models\Outlet;
use App\Services\ExcelAnalysisService;
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
            'file'            => 'required|file|mimes:xlsx,xls,csv|max:15360',
            'notes'           => 'nullable|string|max:500',
        ], [
            'excel_module_id.required' => 'Pilih kategori modul form terlebih dahulu.',
            'excel_module_id.exists'   => 'Modul form yang dipilih tidak valid.',
            'file.required'            => 'Pilih berkas Excel untuk diunggah.',
            'file.mimes'               => 'Format berkas harus berupa .xlsx, .xls, atau .csv.',
            'file.max'                 => 'Ukuran berkas maksimal adalah 15MB.',
        ]);

        $user     = Auth::user();
        $outletId = $user->outlet_id ?? Outlet::first()?->id ?? 1;
        $period   = Carbon::today()->format('Y-m');

        // Simpan file ke storage public
        $uploadedFile = $request->file('file');
        $originalName = $uploadedFile->getClientOriginalName();
        $fileSize     = $uploadedFile->getSize();
        $storedPath   = $uploadedFile->store('submissions', 'public');

        // ── Auto-analisis isi berkas ──────────────────────────────────────────
        $analysisData = null;
        try {
            $fullPath     = Storage::disk('public')->path($storedPath);
            $analysisSvc  = new ExcelAnalysisService();
            $analysisResult = $analysisSvc->analyze($fullPath);
            if ($analysisResult['success'] ?? false) {
                $analysisData = json_encode($analysisResult, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        } catch (\Throwable $e) {
            // Gagal analisis tidak menghentikan upload
            \Log::warning('ExcelAnalysisService error: ' . $e->getMessage());
        }

        // Catat sebagai entri histori upload baru (setiap upload file tercatat dalam riwayat)
        $submission = ExcelSubmission::create([
            'outlet_id'       => $outletId,
            'excel_module_id' => $validated['excel_module_id'],
            'submission_date' => Carbon::today(),
            'user_id'         => $user->id,
            'period'          => $period,
            'file_path'       => $storedPath,
            'original_name'   => $originalName,
            'file_size'       => $fileSize,
            'status'          => 'Menunggu Review',
            'notes'           => $validated['notes'] ?? 'Menunggu pengecekan kelengkapan dari Store Leader.',
            'analysis_data'   => $analysisData,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'    => true,
                'message'    => 'File Excel berhasil diunggah dan dianalisis!',
                'submission' => $submission->load(['module', 'user']),
                'has_analysis' => !is_null($analysisData),
            ]);
        }

        return redirect()->back()->with('success', 'File Excel berhasil diunggah dan dianalisis otomatis oleh sistem!');
    }

    /**
     * Leader memverifikasi / menyetujui / meminta revisi dokumen
     */
    public function verify(Request $request, ExcelSubmission $submission)
    {
        // Validasi Otorisasi RBAC: Hanya Store Leader yang berhak memverifikasi
        $user = Auth::user();
        if ($user && method_exists($user, 'isLeader') && !$user->isLeader()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak: Hanya Store Leader / Manajer yang berhak memverifikasi dokumen.'
                ], 403);
            }
            return redirect()->back()->with('error', 'Akses ditolak: Hanya Store Leader / Manajer yang berhak memverifikasi dokumen.');
        }

        $validated = $request->validate([
            'status' => 'required|in:Lengkap,Perlu Perbaikan',
            'notes'  => 'nullable|string|max:500',
        ]);

        $submission->update([
            'status' => $validated['status'],
            'notes'  => $validated['notes'] ?? ($validated['status'] === 'Lengkap'
                ? 'Telah disetujui dan diverifikasi oleh Leader.'
                : 'Mohon perbaiki data yang kurang.'),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'    => true,
                'message'    => 'Status verifikasi berhasil disimpan: ' . $validated['status'],
                'submission' => $submission,
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

        $downloadName = $submission->original_name ?: basename($submission->file_path);
        return Storage::disk('public')->download($submission->file_path, $downloadName);
    }

    /**
     * Unduh hasil analisis data terstruktur (CSV berformat Excel UTF-8 BOM)
     */
    public function downloadAnalysis(ExcelSubmission $submission)
    {
        $analysis = null;
        if ($submission->analysis_data) {
            $analysis = json_decode($submission->analysis_data, true);
        }

        // Jika belum ada analisis atau data item belum lengkap, jalankan on-the-fly
        if (!$analysis || !isset($analysis['all_items'])) {
            if ($submission->file_path && Storage::disk('public')->exists($submission->file_path)) {
                $fullPath = Storage::disk('public')->path($submission->file_path);
                $svc = new ExcelAnalysisService();
                $res = $svc->analyze($fullPath);
                if ($res['success'] ?? false) {
                    $analysis = $res;
                    $submission->update(['analysis_data' => json_encode($res, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]);
                }
            }
        }

        if (!$analysis) {
            return redirect()->back()->with('error', 'Data analisis belum tersedia untuk dokumen ini.');
        }

        $baseTitle = $submission->original_name ? pathinfo($submission->original_name, PATHINFO_FILENAME) : 'Laporan';
        $cleanTitle = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $baseTitle);
        $fileName = "Data_Analisis_{$submission->id}_{$cleanTitle}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($submission, $analysis) {
            $output = fopen('php://output', 'w');

            // UTF-8 BOM agar Excel membukanya dengan encoding yang benar dan kolom rapi
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            // Metadata Ringkasan
            fputcsv($output, ['=== HASIL ANALISIS DATA OPERASIONAL HOKBEN ===']);
            fputcsv($output, ['Judul Dokumen', $analysis['title'] ?? 'Laporan Operasional']);
            fputcsv($output, ['Nama Gerai / Store', $analysis['store_name'] ?? 'HokBen Outlet']);
            fputcsv($output, ['Kategori Modul', $submission->module->name ?? 'Form Excel']);
            fputcsv($output, ['Petugas Pengunggah', ($submission->user->name ?? 'Staff') . ' (' . ($submission->user->division ?? '-') . ')']);
            fputcsv($output, ['Waktu Upload', $submission->created_at ? $submission->created_at->format('d/m/Y H:i') : '-']);
            fputcsv($output, ['Status Verifikasi', $submission->status]);
            fputcsv($output, ['File Sumber Asli', $submission->original_name ?? basename($submission->file_path ?? '-')]);
            fputcsv($output, []);

            // Ringkasan KPI
            if (isset($analysis['metrics'])) {
                $m = $analysis['metrics'];
                fputcsv($output, ['=== RINGKASAN METRIK KPI PENJUALAN ===']);
                fputcsv($output, ['Total Omzet', $m['formatted_revenue'] ?? ('Rp ' . number_format($m['total_revenue'] ?? 0, 0, ',', '.'))]);
                fputcsv($output, ['Total Porsi Terjual', $m['formatted_qty'] ?? number_format($m['total_qty'] ?? 0, 0, ',', '.')]);
                fputcsv($output, ['Total Varian Menu', ($m['total_menu_variants'] ?? 0) . ' Varian']);

                if (isset($m['channels'])) {
                    fputcsv($output, []);
                    fputcsv($output, ['Kanal Penjualan', 'Porsi (Qty)', 'Omzet (Rp)', 'Kontribusi (%)']);
                    foreach ($m['channels'] as $cKey => $cVal) {
                        $cName = match($cKey) {
                            'dine_in' => 'Dine In',
                            'take_away' => 'Take Away',
                            'online_delivery' => 'Online Delivery (GF/GR/SF)',
                            default => ucfirst($cKey)
                        };
                        fputcsv($output, [
                            $cName,
                            $cVal['qty'] ?? 0,
                            $cVal['formatted_revenue'] ?? ('Rp ' . number_format($cVal['revenue'] ?? 0, 0, ',', '.')),
                            $cVal['percentage'] ?? '0%'
                        ]);
                    }
                }
                fputcsv($output, []);
            }

            // Insights
            if (!empty($analysis['insights'])) {
                fputcsv($output, ['=== REKOMENDASI & INSIGHTS SISTEM ===']);
                foreach ($analysis['insights'] as $idx => $ins) {
                    fputcsv($output, [($idx + 1) . '. ' . $ins]);
                }
                fputcsv($output, []);
            }

            // Data Menu Lengkap
            $items = $analysis['all_items'] ?? $analysis['preview_table'] ?? [];
            if (!empty($items)) {
                fputcsv($output, ['=== RINCIAN DATA PER ITEM MENU (HASIL ANALISIS) ===']);
                fputcsv($output, [
                    'No',
                    'Kode PLU',
                    'Nama Item Menu',
                    'Dine In Qty',
                    'Dine In Omzet (Rp)',
                    'Take Away Qty',
                    'Take Away Omzet (Rp)',
                    'Online Qty',
                    'Online Omzet (Rp)',
                    'Total Qty (Porsi)',
                    'Total Omzet (Rp)',
                ]);

                $no = 1;
                foreach ($items as $it) {
                    fputcsv($output, [
                        $no++,
                        $it['plu'] ?? '-',
                        $it['name'] ?? '-',
                        $it['dine_in_qty'] ?? 0,
                        $it['dine_in_rp'] ?? 0,
                        $it['take_away_qty'] ?? 0,
                        $it['take_away_rp'] ?? 0,
                        $it['online_qty'] ?? 0,
                        $it['online_rp'] ?? 0,
                        $it['total_qty'] ?? 0,
                        $it['total_revenue'] ?? 0,
                    ]);
                }
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Endpoint API JSON untuk mengambil data analisis sebuah submission
     */
    public function getAnalysisJson(ExcelSubmission $submission)
    {
        $analysis = null;
        if ($submission->analysis_data) {
            $analysis = json_decode($submission->analysis_data, true);
        }

        if (!$analysis || !isset($analysis['all_items'])) {
            if ($submission->file_path && Storage::disk('public')->exists($submission->file_path)) {
                $fullPath = Storage::disk('public')->path($submission->file_path);
                $svc = new ExcelAnalysisService();
                $res = $svc->analyze($fullPath);
                if ($res['success'] ?? false) {
                    $analysis = $res;
                    $submission->update(['analysis_data' => json_encode($res, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]);
                }
            }
        }

        return response()->json([
            'success' => !is_null($analysis),
            'submission' => [
                'id' => $submission->id,
                'file_name' => $submission->original_name ?? basename($submission->file_path ?? 'Dokumen.xlsx'),
                'file_size' => $submission->formatted_file_size,
                'status' => $submission->status,
                'notes' => $submission->notes,
                'user_name' => $submission->user->name ?? 'Staff',
                'user_division' => $submission->user->division ?? 'Operasional',
                'module_name' => $submission->module->name ?? 'Form Excel',
                'created_at' => $submission->created_at ? $submission->created_at->format('d M Y H:i') : '-',
                'download_url' => route('submissions.download', $submission),
                'download_analysis_url' => route('submissions.download_analysis', $submission),
            ],
            'analysis' => $analysis,
        ]);
    }
}
