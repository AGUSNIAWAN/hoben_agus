<?php

namespace Database\Seeders;

use App\Models\ExcelModule;
use App\Models\ExcelSubmission;
use App\Models\Outlet;
use App\Models\User;
use App\Models\DailyActivity;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OpsDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Outlets HokBen
        $outlets = [
            ['name' => 'HokBen Palembang Icon', 'code' => 'HB-PLM01'],
            ['name' => 'HokBen Sun Plaza Medan', 'code' => 'HB-MDN01'],
            ['name' => 'HokBen Living World Pekanbaru', 'code' => 'HB-PKU01'],
            ['name' => 'HokBen Bing Kou Medan', 'code' => 'HB-MDN02'],
        ];

        $outletModels = [];
        foreach ($outlets as $outlet) {
            $outletModels[] = Outlet::firstOrCreate(
                ['code' => $outlet['code']],
                ['name' => $outlet['name']]
            );
        }
        $primaryOutlet = $outletModels[0];

        // 2. Update user roles, division, dan outlet_id
        $userUpdates = [
            'admin@hokben.co.id' => ['role' => 'Store Leader', 'division' => 'Management', 'outlet_id' => $primaryOutlet->id],
            'ahmad@hokben.co.id' => ['role' => 'Staff', 'division' => 'Kitchen', 'outlet_id' => $primaryOutlet->id],
            'siti@hokben.co.id'  => ['role' => 'Staff', 'division' => 'Front Counter', 'outlet_id' => $primaryOutlet->id],
            'rian@hokben.co.id'  => ['role' => 'Staff', 'division' => 'Cashier', 'outlet_id' => $primaryOutlet->id],
            'dewi@hokben.co.id'  => ['role' => 'Staff', 'division' => 'Kitchen', 'outlet_id' => $primaryOutlet->id],
        ];

        foreach ($userUpdates as $email => $data) {
            User::where('email', $email)->update($data);
        }

        // 3. Buat Modul-Modul Form Excel Operasional
        $currentPeriod = Carbon::now()->format('Y-m');
        $modules = [
            [
                'name' => 'Form Quality Control',
                'category' => 'Quality Control',
                'period' => $currentPeriod,
                'drive_link' => 'https://docs.google.com/spreadsheets/d/1suQMBQyy_rX0pIFptMrqlrIN5GjdZTAT',
                'is_active' => true,
            ],
            [
                'name' => 'Form Inventory Harian',
                'category' => 'Inventory',
                'period' => $currentPeriod,
                'drive_link' => 'https://docs.google.com/spreadsheets/d/1zNxrQc1z02yEXmCmvuvGAkkGlJcdwDPH',
                'is_active' => true,
            ],
            [
                'name' => 'Laporan Kasir',
                'category' => 'Cashier',
                'period' => $currentPeriod,
                'drive_link' => 'https://docs.google.com/spreadsheets/d/119S0wZPXQ4T_u2DMtrUyLXhSpLfUe0tjr',
                'is_active' => true,
            ],
            [
                'name' => 'Form Suhu Chiller & Freezer',
                'category' => 'Kitchen',
                'period' => $currentPeriod,
                'drive_link' => 'https://docs.google.com/spreadsheets/d/1suQMBQyy_rX0pIFptMrqlrIN5GjdZTAT',
                'is_active' => true,
            ],
        ];

        $moduleModels = [];
        foreach ($modules as $m) {
            $moduleModels[] = ExcelModule::firstOrCreate(
                ['name' => $m['name'], 'period' => $m['period']],
                $m
            );
        }

        // 4. Buat contoh pengajuan submission Excel hari ini untuk diverifikasi
        $ahmad = User::where('email', 'ahmad@hokben.co.id')->first();
        $siti  = User::where('email', 'siti@hokben.co.id')->first();
        $rian  = User::where('email', 'rian@hokben.co.id')->first();

        if ($ahmad && isset($moduleModels[0])) {
            ExcelSubmission::firstOrCreate(
                [
                    'outlet_id' => $primaryOutlet->id,
                    'excel_module_id' => $moduleModels[0]->id,
                    'submission_date' => Carbon::today(),
                ],
                [
                    'user_id' => $ahmad->id,
                    'period' => $currentPeriod,
                    'file_path' => 'submissions/sample_qc_kitchen.xlsx',
                    'status' => 'Menunggu Review',
                    'notes' => 'Pengecekan suhu chiller pagi dan kualitas kematangan nasi bento.',
                ]
            );
        }

        if ($siti && isset($moduleModels[1])) {
            ExcelSubmission::firstOrCreate(
                [
                    'outlet_id' => $primaryOutlet->id,
                    'excel_module_id' => $moduleModels[1]->id,
                    'submission_date' => Carbon::today(),
                ],
                [
                    'user_id' => $siti->id,
                    'period' => $currentPeriod,
                    'file_path' => 'submissions/sample_inventory_front.xlsx',
                    'status' => 'Lengkap',
                    'notes' => 'Stok cup ocha, sendok bento, dan bumbu pelengkap aman.',
                ]
            );
        }

        if ($rian && isset($moduleModels[2])) {
            ExcelSubmission::firstOrCreate(
                [
                    'outlet_id' => $primaryOutlet->id,
                    'excel_module_id' => $moduleModels[2]->id,
                    'submission_date' => Carbon::today(),
                ],
                [
                    'user_id' => $rian->id,
                    'period' => $currentPeriod,
                    'file_path' => 'submissions/sample_laporan_kasir.xlsx',
                    'status' => 'Menunggu Review',
                    'notes' => 'Laporan serah terima modal kasir shift 1 & 2.',
                ]
            );
        }

        // 5. Buat log aktivitas checklist harian awal
        if ($siti) {
            DailyActivity::firstOrCreate(
                [
                    'user_id' => $siti->id,
                    'outlet_id' => $primaryOutlet->id,
                    'date' => Carbon::today(),
                    'shift' => 'Opening',
                ],
                [
                    'division' => 'Front Counter',
                    'checklist_data' => [
                        ['task' => 'Cek kebersihan area depan', 'completed' => true],
                        ['task' => 'Nyalakan sistem POS', 'completed' => true],
                        ['task' => 'Siapkan uang kembalian', 'completed' => true],
                        ['task' => 'Cek stok bahan baku awal', 'completed' => true],
                    ],
                ]
            );
        }
    }
}
