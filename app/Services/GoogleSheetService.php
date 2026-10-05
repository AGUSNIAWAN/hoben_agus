<?php

namespace App\Services;

use Google_Client;
use Google_Service_Sheets;
use Exception;

class GoogleSheetService
{
    protected $client;
    protected $service;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName('HokBen Monitoring App');
        $this->client->setScopes([Google_Service_Sheets::SPREADSHEETS_READONLY]);
        
        // Cek jika ada kredensial service account
        $credentialsPath = storage_path('app/google-credentials.json');
        
        if (file_exists($credentialsPath)) {
            $this->client->setAuthConfig($credentialsPath);
        } else {
            // Uncomment the line below to show an exception if credentials are required immediately
            // throw new Exception("Google Service Account credentials file not found at: {$credentialsPath}");
        }

        $this->service = new Google_Service_Sheets($this->client);
    }

    /**
     * Membaca data dari Spreadsheet
     *
     * @param string $spreadsheetId ID dari Google Spreadsheet (dari URL)
     * @param string $range Rentang data (contoh: 'Sheet1!A1:E')
     * @return array
     */
    public function readSheet($spreadsheetId, $range)
    {
        try {
            $response = $this->service->spreadsheets_values->get($spreadsheetId, $range);
            return $response->getValues() ?? [];
        } catch (Exception $e) {
            throw new Exception("Error reading from Google Sheet: " . $e->getMessage());
        }
    }
}
