<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class GoogleSheetService
{
    public function __construct()
    {
        // Tidak perlu inisialisasi Google Client karena menggunakan metode bypass CSV
    }

    /**
     * Membaca data dari Spreadsheet menggunakan format CSV (Bypass Publik)
     *
     * @param string $spreadsheetId ID dari Google Spreadsheet (dari URL)
     * @param string $gid ID dari sheet khusus (bisa dilihat di URL bagian gid=...)
     * @return array
     */
    public function readSheetCsv($spreadsheetId, $gid = '0')
    {
        $url = "https://docs.google.com/spreadsheets/d/{$spreadsheetId}/export?format=csv&gid={$gid}";

        try {
            $response = Http::withOptions(['follow_redirects' => true])->get($url);

            if (!$response->successful()) {
                throw new Exception("Gagal mengambil data. Pastikan Google Sheet sudah diatur ke 'Anyone with the link can view'.");
            }

            $csvData = $response->body();
            $rows = [];
            
            // Parse CSV dengan aman
            $stream = fopen('php://temp', 'r+');
            fwrite($stream, $csvData);
            rewind($stream);
            
            while (($data = fgetcsv($stream)) !== false) {
                $rows[] = $data;
            }
            fclose($stream);
            
            return $rows;
        } catch (Exception $e) {
            throw new Exception("Error reading from Google Sheet: " . $e->getMessage());
        }
    }
}
