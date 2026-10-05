<?php

namespace App\Services;

use App\Models\ExcelModule;
use App\Models\ExcelSubmission;
use Carbon\Carbon;
use Exception;

class ComplianceReportService
{
    protected $googleSheetService;

    public function __construct(GoogleSheetService $googleSheetService)
    {
        $this->googleSheetService = $googleSheetService;
    }

    /**
     * Memeriksa status kepatuhan dari sebuah modul untuk outlet tertentu pada tanggal hari ini
     *
     * @param int $moduleId
     * @param int $outletId
     * @param int $userId (optional) untuk dicatat pada submission jika diupdate
     * @return array
     */
    public function checkModuleStatus($moduleId, $outletId, $userId = null)
    {
        $module = ExcelModule::findOrFail($moduleId);
        
        // Asumsi drive_link menyimpan ID Spreadsheet Google Sheets
        // Pada prakteknya, URL perlu di-parse untuk mendapatkan ID asli
        $spreadsheetId = $this->extractSpreadsheetId($module->drive_link);
        
        if (!$spreadsheetId) {
            return ['status' => 'Belum Diisi', 'notes' => 'Invalid Drive Link'];
        }

        // Contoh: Range untuk mencari baris tanggal di kolom A dan mandatory cells di B,C,D
        // Ini harus dibuat dinamis per modul di database, tapi ini contoh implementasinya.
        $range = 'Sheet1!A:Z'; 
        $mandatoryColumns = [1, 2, 3]; // Index kolom (B, C, D) yang wajib diisi

        try {
            $data = $this->googleSheetService->readSheet($spreadsheetId, $range);
            
            $today = Carbon::today()->format('Y-m-d'); // atau format yang sesuai dengan sheet (misal 'd/m/Y')
            
            $foundRow = null;
            foreach ($data as $rowIndex => $row) {
                // Asumsi kolom pertama (index 0) adalah Tanggal
                if (isset($row[0]) && str_contains($row[0], $today)) {
                    $foundRow = $row;
                    break;
                }
            }

            if (!$foundRow) {
                // Status: Belum Diisi (Tanggal hari ini tidak ditemukan)
                $status = 'Belum Diisi';
                $notes = "Baris untuk tanggal {$today} belum ada.";
            } else {
                $emptyCells = [];
                foreach ($mandatoryColumns as $colIndex) {
                    // Cek jika sel kosong, tidak ada, atau string kosong
                    if (!isset($foundRow[$colIndex]) || trim($foundRow[$colIndex]) === '') {
                        $emptyCells[] = "Kolom ke-" . ($colIndex + 1);
                    }
                }

                if (count($emptyCells) > 0) {
                    // Status: Terisi Sebagian / Belum Lengkap
                    $status = 'Belum Lengkap';
                    $notes = "Data kurang di: " . implode(", ", $emptyCells);
                } else {
                    // Status: Lengkap
                    $status = 'Lengkap';
                    $notes = "Semua mandatory cell terisi.";
                }
            }

            // Simpan atau update ke ExcelSubmission
            // Asumsi period adalah Y-m untuk bulan berjalan
            $period = Carbon::today()->format('Y-m');
            
            $submission = ExcelSubmission::updateOrCreate(
                [
                    'excel_module_id' => $module->id,
                    'outlet_id'       => $outletId,
                    'submission_date' => Carbon::today(),
                ],
                [
                    'user_id' => $userId ?? 1, // Default fallback jika null
                    'period'  => $period,
                    'status'  => $status,
                    'notes'   => $notes,
                ]
            );

            return [
                'success' => true,
                'status'  => $status,
                'notes'   => $notes,
                'submission' => $submission
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'status'  => 'Error',
                'notes'   => $e->getMessage()
            ];
        }
    }

    /**
     * Helper untuk mengekstrak Spreadsheet ID dari link penuh
     */
    private function extractSpreadsheetId($url)
    {
        $pattern = '/\/d\/([a-zA-Z0-9-_]+)/';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
        return $url; // Fallback assume it's already an ID
    }
}
