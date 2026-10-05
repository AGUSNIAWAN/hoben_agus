<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleSheetService;

class SheetSyncController extends Controller
{
    protected $googleSheetService;

    public function __construct(GoogleSheetService $googleSheetService)
    {
        $this->googleSheetService = $googleSheetService;
    }

    public function syncArea19Sales()
    {
        // Spreadsheet ID dari URL yang Anda berikan
        $spreadsheetId = '1zNxrQc1z02yEXmCmvuvGAkkGlJcdwDPH';
        
        // Targetkan Sheet dengan nama "monitoring sales" dan jangkauan luas (A1 sampai AZ100)
        $range = "'monitoring sales'!A1:AZ100";

        try {
            $data = $this->googleSheetService->readSheet($spreadsheetId, $range);
            
            // Proses data $data di sini (Simpan ke DB, atau format ulang)
            return response()->json([
                'success' => true,
                'message' => 'Data tersinkronisasi',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
