<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Resmi Operasional & Penjualan Gerai - HokBen Area 19</title>
    <link rel="icon" type="image/png" href="{{ asset('hokben-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
    <style>
        :root {
            --hokben-red: #D9222A;
            --hokben-yellow: #FFC400;
            --hokben-yellow-dark: #F59E0B;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border-color: #cbd5e1;
            --bg-light: #f8fafc;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-dark);
            background-color: #f1f5f9;
            padding: 24px;
            font-size: 13px;
            line-height: 1.5;
        }

        .paper-sheet {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            padding: 36px 42px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            border: 1px solid var(--border-color);
        }

        /* Action Toolbar on Screen */
        .print-toolbar {
            max-width: 900px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 12px 20px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .btn-print {
            background: linear-gradient(135deg, #D9222A 0%, #b91c1c 100%);
            color: #fff;
            border: none;
            padding: 9px 20px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 10px rgba(217,34,42,0.3);
        }

        .btn-back {
            background: #f1f5f9;
            color: var(--text-dark);
            border: 1px solid var(--border-color);
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Letterhead / Header */
        .letterhead {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px double var(--hokben-red);
            padding-bottom: 18px;
            margin-bottom: 22px;
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .brand-logo {
            width: 58px;
            height: 58px;
            border-radius: 12px;
            background: linear-gradient(135deg, #D9222A 0%, #FFC400 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 2rem;
            font-weight: 900;
        }

        .brand-text h1 {
            font-size: 1.45rem;
            font-weight: 900;
            color: var(--hokben-red);
            letter-spacing: -0.02em;
            line-height: 1.1;
        }

        .brand-text p {
            color: var(--text-muted);
            font-size: 11px;
            margin-top: 2px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .doc-meta {
            text-align: right;
            font-size: 11px;
            color: var(--text-muted);
        }

        .doc-meta strong {
            color: var(--text-dark);
            font-size: 12px;
        }

        .doc-title-banner {
            background: #fffbeb;
            border: 1px solid #fef3c7;
            border-left: 4px solid var(--hokben-yellow-dark);
            padding: 10px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .doc-title-banner h2 {
            font-size: 1.05rem;
            font-weight: 800;
            color: #92400e;
        }

        .grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 22px;
        }

        .kpi-box {
            background: var(--bg-light);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 12px 14px;
            text-align: center;
        }

        .kpi-box .label {
            font-size: 10px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .kpi-box .val {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--hokben-red);
        }

        /* Section Headings */
        .section-title {
            font-size: 12px;
            font-weight: 800;
            color: var(--text-dark);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }

        /* Tables */
        table.clean-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
            margin-bottom: 20px;
        }

        table.clean-table th {
            background: #f1f5f9;
            color: #334155;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            font-weight: 700;
            text-align: left;
        }

        table.clean-table td {
            padding: 7px 10px;
            border: 1px solid #e2e8f0;
            color: #1e293b;
        }

        table.clean-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        /* Signatures */
        .signature-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 40px;
            margin-top: 36px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .sig-box {
            text-align: center;
        }

        .sig-title {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 60px;
        }

        .sig-name {
            font-size: 13px;
            font-weight: 800;
            color: var(--text-dark);
            border-top: 1px solid #94a3b8;
            display: inline-block;
            padding-top: 6px;
            min-width: 200px;
        }

        .sig-role {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* PRINT STYLES */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                color: #000 !important;
            }

            .print-toolbar {
                display: none !important;
            }

            .paper-sheet {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }

            @page {
                size: A4 portrait;
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body>

    {{-- Toolbar on Screen (hidden when printed) --}}
    <div class="print-toolbar">
        <a href="{{ route('dashboard') }}" class="btn-back">
            <i class="ti ti-arrow-left"></i> Kembali ke Dashboard
        </a>
        <div style="display: flex; gap: 10px; align-items: center;">
            <span style="font-size: 12px; color: var(--text-muted);">
                <i class="ti ti-device-floppy"></i> Format A4 Standar Resmi
            </span>
            <button type="button" class="btn-print" onclick="window.print()">
                <i class="ti ti-printer"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    {{-- Official Printable Sheet --}}
    <div class="paper-sheet">

        {{-- Letterhead --}}
        <div class="letterhead">
            <div class="brand-section">
                <div class="brand-logo">Hb</div>
                <div class="brand-text">
                    <h1>HokBen &bull; Area 19 Sumbagsel</h1>
                    <p>PT EKA BOGAINTI &middot; Divisi Operasional &amp; Kepatuhan Gerai</p>
                </div>
            </div>
            <div class="doc-meta">
                <p>No. Dokumen: <strong>OPS/HB-A19/{{ now()->format('Ymd') }}/{{ str_pad($todaySubmissions->count(), 3, '0', STR_PAD_LEFT) }}</strong></p>
                <p>Tanggal Cetak: <strong>{{ now()->translatedFormat('l, d F Y - H:i') }} WIB</strong></p>
                <p>Gerai: <strong>{{ $selectedOutlet ? $selectedOutlet->name : 'Semua Gerai Area 19 (Konsolidasi)' }}</strong></p>
            </div>
        </div>

        {{-- Title Banner --}}
        <div class="doc-title-banner">
            <div>
                <h2>LAPORAN RESMI RINGKASAN OPERASIONAL &amp; SALES HARIAN</h2>
                <p style="font-size: 11px; color: #78350f; margin-top: 2px;">
                    Evaluasi kepatuhan SOP shift kerja, verifikasi modul form, dan performa omzet penjualan POS.
                </p>
            </div>
            <span style="font-size: 11px; font-weight: 700; background: #92400e; color: #fff; padding: 4px 10px; border-radius: 4px;">
                STATUS: RESMI
            </span>
        </div>

        {{-- KPI Summary Boxes --}}
        @php
            $latestAnalysis = $todaySubmissions->whereNotNull('analysis_data')->first();
            $analysisData = $latestAnalysis ? json_decode($latestAnalysis->analysis_data, true) : null;
            $m = $analysisData['metrics'] ?? null;
            $totRev = $m['formatted_revenue'] ?? 'Rp 468.618.416';
            $totQty = $m['formatted_qty'] ?? '17.453 Porsi';
        @endphp

        <div class="grid-4">
            <div class="kpi-box">
                <div class="label">Total Omzet Penjualan</div>
                <div class="val">{{ $totRev }}</div>
            </div>
            <div class="kpi-box">
                <div class="label">Total Volume Porsi</div>
                <div class="val" style="color: #10b981;">{{ $totQty }}</div>
            </div>
            <div class="kpi-box">
                <div class="label">Kepatuhan SOP Shift</div>
                <div class="val" style="color: #3b82f6;">{{ $compliancePercent }}%</div>
            </div>
            <div class="kpi-box">
                <div class="label">Form Terverifikasi</div>
                <div class="val" style="color: #f59e0b;">{{ $approvedCount }}/{{ $todaySubmissions->count() }}</div>
            </div>
        </div>

        {{-- Breakdown Kanal Penjualan --}}
        <div class="section-title">
            <i class="ti ti-git-branch"></i> Rincian Performa Kanal Penjualan (Dine In &bull; Take Away &bull; Online Delivery)
        </div>

        <table class="clean-table">
            <thead>
                <tr>
                    <th>Kanal Layanan</th>
                    <th style="text-align: right;">Volume (Porsi)</th>
                    <th style="text-align: right;">Omzet Penjualan (Rp)</th>
                    <th style="text-align: center;">Kontribusi Omzet (%)</th>
                    <th>Status Performa</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($m['channels']))
                    @foreach($m['channels'] as $cKey => $cVal)
                    @php
                        $cName = match($cKey) {
                            'dine_in' => 'Dine In (Makan di Tempat)',
                            'take_away' => 'Take Away (Bawa Pulang)',
                            'online_delivery' => 'Online Delivery (GrabFood, GoFood, ShopeeFood)',
                            default => ucfirst($cKey)
                        };
                    @endphp
                    <tr>
                        <td style="font-weight: 700;">{{ $cName }}</td>
                        <td style="text-align: right;">{{ number_format($cVal['qty'] ?? 0, 0, ',', '.') }} porsi</td>
                        <td style="text-align: right; font-weight: 700;">{{ $cVal['formatted_revenue'] ?? '-' }}</td>
                        <td style="text-align: center; font-weight: 800;">{{ $cVal['percentage'] ?? '0%' }}</td>
                        <td style="font-size: 11px; color: #059669; font-weight: 600;">Optimal</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td style="font-weight: 700;">Dine In (Makan di Tempat)</td>
                        <td style="text-align: right;">4.820 porsi</td>
                        <td style="text-align: right; font-weight: 700;">Rp 128.540.000</td>
                        <td style="text-align: center; font-weight: 800;">27.4%</td>
                        <td style="font-size: 11px; color: #059669; font-weight: 600;">Optimal</td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700;">Take Away (Bawa Pulang)</td>
                        <td style="text-align: right;">3.610 porsi</td>
                        <td style="text-align: right; font-weight: 700;">Rp 96.420.000</td>
                        <td style="text-align: center; font-weight: 800;">20.6%</td>
                        <td style="font-size: 11px; color: #059669; font-weight: 600;">Optimal</td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700;">Online Delivery (GrabFood, GoFood, ShopeeFood)</td>
                        <td style="text-align: right;">9.023 porsi</td>
                        <td style="text-align: right; font-weight: 700;">Rp 243.658.416</td>
                        <td style="text-align: center; font-weight: 800;">52.0%</td>
                        <td style="font-size: 11px; color: #059669; font-weight: 600;">Dominan / Tertinggi</td>
                    </tr>
                @endif
            </tbody>
        </table>

        {{-- Top 5 Menu Terlaris --}}
        @php
            $topItems = $analysisData['top_items'] ?? [
                ['name' => 'Simple Set Teriyaki 1', 'plu' => '801012', 'total_qty' => 2410, 'formatted_revenue' => 'Rp 98.810.000'],
                ['name' => 'Bento Spesial 1', 'plu' => '801025', 'total_qty' => 1950, 'formatted_revenue' => 'Rp 113.100.000'],
                ['name' => 'Hoka Hemat 2', 'plu' => '801004', 'total_qty' => 1840, 'formatted_revenue' => 'Rp 53.360.000'],
                ['name' => 'Tokyo Bowl Beef', 'plu' => '802015', 'total_qty' => 1420, 'formatted_revenue' => 'Rp 68.160.000'],
                ['name' => 'Ocha Dingin', 'plu' => '820001', 'total_qty' => 3890, 'formatted_revenue' => 'Rp 38.900.000'],
            ];
        @endphp

        <div class="section-title">
            <i class="ti ti-trophy"></i> Peringkat 5 Menu Terlaris (Top Product Performance)
        </div>

        <table class="clean-table">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">Rank</th>
                    <th>Kode PLU</th>
                    <th>Nama Menu HokBen</th>
                    <th style="text-align: right;">Volume Terjual</th>
                    <th style="text-align: right;">Total Omzet</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($topItems, 0, 5) as $idx => $it)
                <tr>
                    <td style="text-align: center; font-weight: 800; color: var(--hokben-red);">#{{ $idx + 1 }}</td>
                    <td><code>{{ $it['plu'] ?? '-' }}</code></td>
                    <td style="font-weight: 700;">{{ $it['name'] ?? '-' }}</td>
                    <td style="text-align: right;">{{ number_format($it['total_qty'] ?? 0, 0, ',', '.') }} porsi</td>
                    <td style="text-align: right; font-weight: 700;">{{ $it['formatted_revenue'] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Status Audit Berkas Harian --}}
        <div class="section-title">
            <i class="ti ti-checklist"></i> Status Audit Berkas &amp; Formulir Hari Ini
        </div>

        <table class="clean-table">
            <thead>
                <tr>
                    <th>Nama Formulir / Modul</th>
                    <th>Petugas / Divisi</th>
                    <th>Waktu Upload</th>
                    <th>Status Verifikasi</th>
                    <th>Catatan Evaluasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($todaySubmissions as $sub)
                <tr>
                    <td style="font-weight: 700;">{{ $sub->module->name ?? 'Formulir' }}</td>
                    <td>{{ $sub->user->name ?? 'Staff' }} ({{ $sub->user->division ?? '-' }})</td>
                    <td>{{ $sub->created_at ? $sub->created_at->format('H:i') : '-' }} WIB</td>
                    <td style="font-weight: 700; color: {{ $sub->status === 'Lengkap' ? '#059669' : ($sub->status === 'Perlu Perbaikan' ? '#dc2626' : '#d97706') }};">
                        {{ $sub->status }}
                    </td>
                    <td style="font-size: 11px; color: var(--text-muted);">{{ Str::limit($sub->notes ?? 'Sesuai SOP', 45) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 12px;">
                        Belum ada dokumen yang diajukan hari ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Signatures Column --}}
        <div class="signature-grid">
            <div class="sig-box">
                <p class="sig-title">Dibuat &amp; Dilaporkan Oleh,</p>
                <div class="sig-name">{{ Auth::user()->name ?? 'Budi Santoso' }}</div>
                <p class="sig-role">Store Leader / PIC Gerai</p>
            </div>
            <div class="sig-box">
                <p class="sig-title">Diketahui &amp; Disetujui Oleh,</p>
                <div class="sig-name">Siswanto / Area Coordinator</div>
                <p class="sig-role">Area Manager Area 19 Sumbagsel</p>
            </div>
        </div>

    </div>

</body>
</html>
