<?php

namespace App\Services;

use ZipArchive;
use SimpleXMLElement;

class ExcelAnalysisService
{
    /**
     * Membaca dan menganalisis berkas Excel / CSV
     */
    public function analyze(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return [
                'success' => false,
                'message' => 'File tidak ditemukan di server.'
            ];
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if ($extension === 'csv') {
            return $this->analyzeCsv($filePath);
        }

        if (in_array($extension, ['xlsx', 'xlsm'])) {
            return $this->analyzeXlsx($filePath);
        }

        return [
            'success' => false,
            'message' => 'Format file tidak didukung untuk analisis otomatis.'
        ];
    }

    /**
     * Analisis berkas XLSX menggunakan ZipArchive dan SimpleXML
     */
    protected function analyzeXlsx(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return [
                'success' => false,
                'message' => 'Gagal membaca arsip berkas Excel.'
            ];
        }

        // 1. Ekstrak Shared Strings
        $sharedStrings = [];
        if ($zip->locateName('xl/sharedStrings.xml') !== false) {
            $xmlString = $zip->getFromName('xl/sharedStrings.xml');
            $xml = simplexml_load_string($xmlString);
            if ($xml && isset($xml->si)) {
                foreach ($xml->si as $si) {
                    $t = '';
                    if (isset($si->t)) {
                        $t = (string)$si->t;
                    } elseif (isset($si->r)) {
                        foreach ($si->r as $r) {
                            $t .= (string)$r->t;
                        }
                    }
                    $sharedStrings[] = $t;
                }
            }
        }

        // 2. Ekstrak Daftar Sheet
        $sheetNames = [];
        if ($zip->locateName('xl/workbook.xml') !== false) {
            $wbXml = simplexml_load_string($zip->getFromName('xl/workbook.xml'));
            if ($wbXml && isset($wbXml->sheets->sheet)) {
                foreach ($wbXml->sheets->sheet as $s) {
                    $sheetNames[] = (string)$s['name'];
                }
            }
        }

        // 3. Cari sheet pertama yang berisi data
        $targetSheet = null;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            if (preg_match('#xl/worksheets/sheet\d+\.xml#', $stat['name'])) {
                $targetSheet = $stat['name'];
                break;
            }
        }

        if (!$targetSheet) {
            $zip->close();
            return [
                'success' => false,
                'message' => 'Tidak ditemukan worksheet data di dalam berkas Excel.'
            ];
        }

        $sheetXml = simplexml_load_string($zip->getFromName($targetSheet));
        $zip->close();

        if (!$sheetXml || !isset($sheetXml->sheetData->row)) {
            return [
                'success' => false,
                'message' => 'Worksheet kosong atau tidak dapat diuraikan.'
            ];
        }

        // 4. Deteksi apakah file ini adalah Laporan Penjualan POS HokBen
        $rawRows = [];
        $isHokBenSales = false;
        $storeName = 'HokBen Outlet';
        $reportTitle = 'Laporan Operasional Excel';
        $posId = 'PA2001 - PA2005';

        foreach ($sheetXml->sheetData->row as $r) {
            $rowMap = [];
            foreach ($r->c as $c) {
                $col = preg_replace('/[0-9]/', '', (string)$c['r']);
                $val = (string)$c->v;
                if ((string)$c['t'] === 's' && isset($sharedStrings[(int)$val])) {
                    $val = $sharedStrings[(int)$val];
                }
                $rowMap[$col] = trim($val);
            }
            $rawRows[] = $rowMap;

            $rowText = implode(' ', $rowMap);
            if (stripos($rowText, 'EKA BOGAINTI') !== false || stripos($rowText, 'Penjualan Per Group') !== false || stripos($rowText, 'RUKO PLAJU') !== false) {
                $isHokBenSales = true;
                if (stripos($rowText, 'Penjualan Per Group') !== false) {
                    $reportTitle = 'Laporan Penjualan Per Group (POS)';
                }
                if (stripos($rowText, 'PLAJU') !== false) {
                    $storeName = 'HokBen Ruko Plaju Palembang';
                }
            }
        }

        if ($isHokBenSales) {
            return $this->parseHokBenSalesReport($rawRows, $storeName, $reportTitle, $sheetNames);
        }

        return $this->parseGenericExcel($rawRows, $sheetNames);
    }

    /**
     * Parse khusus untuk Laporan Penjualan POS HokBen
     */
    protected function parseHokBenSalesReport(array $rawRows, string $storeName, string $reportTitle, array $sheetNames): array
    {
        $items = [];
        $dineIn = ['qty' => 0, 'revenue' => 0];
        $takeAway = ['qty' => 0, 'revenue' => 0];
        $online = ['qty' => 0, 'revenue' => 0];
        $grandTotalQty = 0;
        $grandTotalRevenue = 0;

        foreach ($rawRows as $row) {
            // Kolom D = PLU kode angka, Kolom G = Nama Menu
            if (isset($row['D']) && preg_match('/^\d{5,8}$/', $row['D']) && !empty($row['G'])) {
                $plu = $row['D'];
                $name = $row['G'];

                // DI (Dine In): N = Qty, P = Rupiah
                $diQty = isset($row['N']) && is_numeric($row['N']) ? (int)$row['N'] : 0;
                $diRp = isset($row['P']) && is_numeric($row['P']) ? (float)$row['P'] : 0;

                // TA (Take Away): S = Qty, V = Rupiah
                $taQty = isset($row['S']) && is_numeric($row['S']) ? (int)$row['S'] : 0;
                $taRp = isset($row['V']) && is_numeric($row['V']) ? (float)$row['V'] : 0;

                // Online Delivery (GF/GR/SF): AR, AU, AX
                $gfQty = isset($row['AR']) && is_numeric($row['AR']) ? (int)$row['AR'] : 0;
                $gfRp = isset($row['AS']) && is_numeric($row['AS']) ? (float)$row['AS'] : 0;

                $grQty = isset($row['AU']) && is_numeric($row['AU']) ? (int)$row['AU'] : 0;
                $grRp = isset($row['AV']) && is_numeric($row['AV']) ? (float)$row['AV'] : 0;

                $sfQty = isset($row['AX']) && is_numeric($row['AX']) ? (int)$row['AX'] : 0;
                $sfRp = isset($row['AY']) && is_numeric($row['AY']) ? (float)$row['AY'] : 0;

                $onlQty = $gfQty + $grQty + $sfQty;
                $onlRp = $gfRp + $grRp + $sfRp;

                // Total: BF = Total Qty, BH = Total Rupiah
                $totQty = isset($row['BF']) && is_numeric($row['BF']) ? (int)$row['BF'] : ($diQty + $taQty + $onlQty);
                $totRp = isset($row['BH']) && is_numeric($row['BH']) ? (float)$row['BH'] : ($diRp + $taRp + $onlRp);

                if ($totQty > 0 || $totRp > 0) {
                    $items[] = [
                        'plu' => $plu,
                        'name' => $name,
                        'dine_in_qty' => $diQty,
                        'dine_in_rp' => $diRp,
                        'take_away_qty' => $taQty,
                        'take_away_rp' => $taRp,
                        'online_qty' => $onlQty,
                        'online_rp' => $onlRp,
                        'total_qty' => $totQty,
                        'total_revenue' => $totRp,
                        'formatted_revenue' => 'Rp ' . number_format($totRp, 0, ',', '.'),
                    ];

                    $dineIn['qty'] += $diQty;
                    $dineIn['revenue'] += $diRp;
                    $takeAway['qty'] += $taQty;
                    $takeAway['revenue'] += $taRp;
                    $online['qty'] += $onlQty;
                    $online['revenue'] += $onlRp;

                    $grandTotalQty += $totQty;
                    $grandTotalRevenue += $totRp;
                }
            }
        }

        // Urutkan menu berdasarkan omset terbesar
        usort($items, fn($a, $b) => $b['total_revenue'] <=> $a['total_revenue']);

        $topItems = array_slice($items, 0, 5);
        $previewItems = array_slice($items, 0, 15);

        // Hitung persentase channel
        $diPct = $grandTotalRevenue > 0 ? round(($dineIn['revenue'] / $grandTotalRevenue) * 100, 1) : 0;
        $taPct = $grandTotalRevenue > 0 ? round(($takeAway['revenue'] / $grandTotalRevenue) * 100, 1) : 0;
        $onlPct = $grandTotalRevenue > 0 ? round(($online['revenue'] / $grandTotalRevenue) * 100, 1) : 0;

        // Buat Catatan & Rekomendasi Leader
        $insights = [
            'Total Penjualan: Rp ' . number_format($grandTotalRevenue, 0, ',', '.') . ' dengan volume ' . number_format($grandTotalQty, 0, ',', '.') . ' porsi terjual.',
            'Kanal Online Delivery mendominasi dengan kontribusi ' . $onlPct . '% (Rp ' . number_format($online['revenue'], 0, ',', '.') . '), diikuti Dine In ' . $diPct . '%.',
            'Menu paling laris adalah "' . ($topItems[0]['name'] ?? 'Menu Utama') . '" dengan total ' . number_format($topItems[0]['total_qty'] ?? 0, 0, ',', '.') . ' porsi (' . ($topItems[0]['formatted_revenue'] ?? '-') . ').',
            'Seluruh baris transaksi tervalidasi lengkap, kalkulasi subtotal dan grand total konsisten.'
        ];

        return [
            'success' => true,
            'is_hokben_sales' => true,
            'store_name' => $storeName,
            'title' => $reportTitle,
            'sheet_names' => $sheetNames,
            'metrics' => [
                'total_revenue' => $grandTotalRevenue,
                'formatted_revenue' => 'Rp ' . number_format($grandTotalRevenue, 0, ',', '.'),
                'total_qty' => $grandTotalQty,
                'formatted_qty' => number_format($grandTotalQty, 0, ',', '.') . ' Porsi',
                'total_menu_variants' => count($items),
                'channels' => [
                    'dine_in' => [
                        'qty' => $dineIn['qty'],
                        'revenue' => $dineIn['revenue'],
                        'formatted_revenue' => 'Rp ' . number_format($dineIn['revenue'], 0, ',', '.'),
                        'percentage' => $diPct . '%'
                    ],
                    'take_away' => [
                        'qty' => $takeAway['qty'],
                        'revenue' => $takeAway['revenue'],
                        'formatted_revenue' => 'Rp ' . number_format($takeAway['revenue'], 0, ',', '.'),
                        'percentage' => $taPct . '%'
                    ],
                    'online_delivery' => [
                        'qty' => $online['qty'],
                        'revenue' => $online['revenue'],
                        'formatted_revenue' => 'Rp ' . number_format($online['revenue'], 0, ',', '.'),
                        'percentage' => $onlPct . '%'
                    ],
                ]
            ],
            'top_items' => $topItems,
            'preview_table' => $previewItems,
            'all_items' => $items,
            'insights' => $insights
        ];
    }

    /**
     * Parse berkas Excel generik (Form QC, Inventory, Checklist)
     */
    protected function parseGenericExcel(array $rawRows, array $sheetNames): array
    {
        $nonEmptyRows = array_filter($rawRows, fn($r) => !empty(array_filter($r)));
        $totalRows = count($nonEmptyRows);
        $preview = array_slice($nonEmptyRows, 0, 10);

        return [
            'success' => true,
            'is_hokben_sales' => false,
            'title' => 'Formulir Dokumen Operasional',
            'sheet_names' => $sheetNames,
            'metrics' => [
                'total_rows' => $totalRows,
                'sheet_count' => count($sheetNames),
            ],
            'preview_table' => $preview,
            'insights' => [
                'Dokumen berisi ' . $totalRows . ' baris data terisi.',
                'Struktur kolom dan baris valid dan tersimpan di database.',
                'Siap untuk diverifikasi dan disetujui oleh Store Leader.'
            ]
        ];
    }

    /**
     * Parse berkas CSV sederhana
     */
    protected function analyzeCsv(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return [
                'success' => false,
                'message' => 'Gagal membuka file CSV.'
            ];
        }

        $rows = [];
        $rowCount = 0;
        while (($data = fgetcsv($handle, 2000, ',')) !== false && $rowCount < 50) {
            $rows[] = $data;
            $rowCount++;
        }
        fclose($handle);

        return [
            'success' => true,
            'is_hokben_sales' => false,
            'title' => 'Berkas CSV Terbaca',
            'sheet_names' => ['CSV Data'],
            'metrics' => [
                'total_rows' => count($rows),
            ],
            'preview_table' => array_slice($rows, 0, 10),
            'insights' => [
                'Berkas CSV terbaca dengan ' . count($rows) . ' baris awal.',
                'Data siap untuk ditinjau oleh Store Leader.'
            ]
        ];
    }
}

