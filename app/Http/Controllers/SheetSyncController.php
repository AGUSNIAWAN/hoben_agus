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
        
        // GID untuk sheet "monitoring sales" (berdasarkan URL gid=297723641)
        $gid = '297723641';

        try {
            $data = $this->googleSheetService->readSheetCsv($spreadsheetId, $gid);
            
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

    public function syncSerahTerima(Request $request)
    {
        $spreadsheetId = $request->query('spreadsheetId', '19S0wZPXQ4T_u2DMtrUyLXhSpLfUe0tjr');
        $gid = $request->query('gid', '285912838');

        try {
            $data = $this->googleSheetService->readSheetCsv($spreadsheetId, $gid ?: '0');

            return response()->json([
                'success' => true,
                'message' => 'Data serah terima tersinkronisasi',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function syncKfmHelpdesk()
    {
        $spreadsheetId = '1suQMBQyy_rX0pIFptMrqlrIN5GjdZTAT';
        $gid = '891360253';

        try {
            $data = $this->googleSheetService->readSheetCsv($spreadsheetId, $gid);

            return response()->json([
                'success' => true,
                'message' => 'Data KFM dan Helpdesk tersinkronisasi',
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
