<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada beberapa staf HokBen untuk penugasan PIC
        $usersData = [
            ['name' => 'Budi Santoso (Store Leader)', 'email' => 'admin@hokben.co.id'],
            ['name' => 'Siti Aminah (Front Counter)', 'email' => 'siti@hokben.co.id'],
            ['name' => 'Ahmad Fauzi (Kitchen Leader)', 'email' => 'ahmad@hokben.co.id'],
            ['name' => 'Rian Hidayat (Cashier PIC)', 'email' => 'rian@hokben.co.id'],
            ['name' => 'Dewi Lestari (Inventory / QC)', 'email' => 'dewi@hokben.co.id'],
        ];

        $users = [];
        foreach ($usersData as $u) {
            $users[] = User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make('password123'),
                ]
            );
        }

        // Contoh kegiatan operasional nyata di HokBen
        $sampleTasks = [
            [
                'title' => 'Cek Suhu Chiller & Freezer Pagi',
                'description' => 'Monitoring form suhu cold storage area kitchen shift opening memastikan standar -18°C hingga 4°C.',
                'status' => 'todo',
                'user_id' => $users[2]->id, // Ahmad
                'deadline' => Carbon::now()->addHours(3),
            ],
            [
                'title' => 'Input Serah Terima Kasir & Modal Pagi',
                'description' => 'Rekapitulasi modal awal laci kasir counter 1 & 2 serta cek ketersediaan uang kembalian.',
                'status' => 'todo',
                'user_id' => $users[3]->id, // Rian
                'deadline' => Carbon::now()->addHours(2),
            ],
            [
                'title' => 'Persiapan Bahan Baku (Bento & Teriyaki)',
                'description' => 'Proses marinasi daging beef/chicken teriyaki dan persiapan salad dressing untuk lunch peak hours.',
                'status' => 'in_progress',
                'user_id' => $users[2]->id, // Ahmad
                'deadline' => Carbon::now()->addHours(1),
            ],
            [
                'title' => 'Checklist Pre-Shift Online Area 19',
                'description' => 'Verifikasi ketersediaan stok menu online di aplikasi GrabFood, GoFood, & ShopeeFood.',
                'status' => 'in_progress',
                'user_id' => $users[1]->id, // Siti
                'deadline' => Carbon::now()->addMinutes(45),
            ],
            [
                'title' => 'Review Laporan KFM & Tiket Helpdesk',
                'description' => 'Pemeriksaan tiket kendala mesin POS kasir dan eskalasi ke tim IT support pusat.',
                'status' => 'review',
                'user_id' => $users[0]->id, // Budi
                'deadline' => Carbon::now()->addHours(4),
            ],
            [
                'title' => 'Audit Kebersihan & Sanitasi Dining Area',
                'description' => 'Pengecekan protokol meja makan, dispenser saus, dan kebersihan lantai sebelum shift ramai siang.',
                'status' => 'review',
                'user_id' => $users[4]->id, // Dewi
                'deadline' => Carbon::now()->addHours(2),
            ],
            [
                'title' => 'Briefing Shift Opening Tim Outlet',
                'description' => 'Pembagian station kerja, target penjualan harian, dan pengingat SOP keramahan HokBen.',
                'status' => 'done',
                'user_id' => $users[0]->id, // Budi
                'deadline' => Carbon::now()->subHours(1),
            ],
            [
                'title' => 'Penerimaan Pasokan Bahan Gudang Sentral',
                'description' => 'Penerimaan delivery ayam katsu, ebi furai, beras jepang, serta verifikasi surat jalan vendor.',
                'status' => 'done',
                'user_id' => $users[4]->id, // Dewi
                'deadline' => Carbon::now()->subHours(2),
            ],
        ];

        foreach ($sampleTasks as $task) {
            Task::create($task);
        }
    }
}
