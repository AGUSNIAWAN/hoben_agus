<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Operasional & Kepatuhan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="logo-area">
                <i class="ti ti-hexagon-filled"></i>
                <h2>OpsTrack</h2>
            </div>
            <nav class="nav-menu">
                <a href="#" class="nav-link active" data-target="dashboard">
                    <i class="ti ti-layout-dashboard"></i> Dashboard Leader
                </a>
                <a href="#" class="nav-link" data-target="sales">
                    <i class="ti ti-chart-bar"></i> Monitoring Sales
                </a>
                <a href="#" class="nav-link" data-target="staff-portal">
                    <i class="ti ti-users"></i> Portal Staff
                </a>
                <a href="#" class="nav-link" data-target="reports">
                    <i class="ti ti-report-analytics"></i> Rekap & Laporan
                </a>
                <a href="#" class="nav-link" data-target="serah-terima">
                    <i class="ti ti-cash-banknote"></i> Serah Terima Sales & Modal Harian
                </a>
            </nav>
            <div class="user-profile">
                <div class="avatar">
                    <i class="ti ti-user-circle"></i>
                </div>
                <div class="user-info">
                    <p class="name">Budi Santoso</p>
                    <p class="role">Store Leader</p>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <!-- Topbar -->
            <header class="topbar">
                <div class="page-title">
                    <h1 id="current-page-title">Dashboard Monitoring</h1>
                    <p class="date" id="current-date"></p>
                </div>
                <div class="topbar-actions" style="display: flex; gap: 16px; align-items: center;">
                    <form method="GET" action="{{ route('dashboard') }}" id="periodForm">
                        <input type="month" name="period" value="{{ $currentPeriod }}" class="styled-input" style="padding: 6px 12px;" onchange="document.getElementById('periodForm').submit()">
                    </form>
                    <button class="icon-btn" aria-label="Notifications">
                        <i class="ti ti-bell"></i>
                        <span class="badge">3</span>
                    </button>
                </div>
            </header>

            <!-- Dashboard View -->
            <section id="view-dashboard" class="view-section active">
                <div class="metrics-grid">
                    <div class="metric-card glass-panel">
                        <div class="metric-header">
                            <h3>Kepatuhan Harian</h3>
                            <div class="icon-box blue"><i class="ti ti-checkup-list"></i></div>
                        </div>
                        <div class="metric-value">85%</div>
                        <div class="metric-progress">
                            <div class="progress-bar" style="width: 85%"></div>
                        </div>
                        <p class="metric-desc">17 dari 20 Modul Terkumpul</p>
                    </div>
                    
                    <div class="metric-card glass-panel">
                        <div class="metric-header">
                            <h3>Form Menunggu Review</h3>
                            <div class="icon-box orange"><i class="ti ti-file-import"></i></div>
                        </div>
                        <div class="metric-value">4</div>
                        <p class="metric-desc">Membutuhkan verifikasi Leader</p>
                    </div>

                    <div class="metric-card glass-panel">
                        <div class="metric-header">
                            <h3>Outlet Belum Closing</h3>
                            <div class="icon-box red"><i class="ti ti-alert-circle"></i></div>
                        </div>
                        <div class="metric-value">2</div>
                        <p class="metric-desc">Divisi Kitchen & Cashier</p>
                    </div>
                </div>

                <div class="content-grid">
                    <div class="glass-panel main-panel">
                        <div class="panel-header">
                            <h2>Status Upload Hari Ini</h2>
                            <div class="status-filters">
                                <button class="filter-btn active">Semua</button>
                                <button class="filter-btn">Belum Upload</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Nama Staf</th>
                                        <th>Divisi</th>
                                        <th>Shift</th>
                                        <th>Status Form</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="dashboard-table-body">
                                    <!-- Populated by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="side-panel">
                        <div class="glass-panel">
                            <h2>Menunggu Verifikasi</h2>
                            <div class="verification-list" id="verification-list">
                                <!-- Populated by JS -->
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Staff Portal View -->
            <section id="view-staff-portal" class="view-section hidden">
                <div class="portal-header glass-panel">
                    <div class="shift-selector">
                        <h3>Pilih Shift Aktif:</h3>
                        <div class="radio-group">
                            <label><input type="radio" name="shift" value="Opening" checked> Opening</label>
                            <label><input type="radio" name="shift" value="Middle"> Middle</label>
                            <label><input type="radio" name="shift" value="Closing"> Closing</label>
                        </div>
                    </div>
                </div>

                <div class="staff-grid">
                    <div class="glass-panel">
                        <h2>Checklist Aktivitas (<span id="active-shift-label">Opening</span>)</h2>
                        <ul class="checklist" id="daily-checklist">
                            <!-- Populated by JS -->
                        </ul>
                        <button class="btn-primary full-width mt-4" onclick="alert('Checklist disimpan!')">Simpan Checklist</button>
                    </div>

                    <div class="glass-panel upload-panel">
                        <h2>Upload Form & Modul</h2>
                        <p class="sub-text">Unggah file Excel (xls, xlsx) sesuai kategori tugas.</p>
                        
                        <div class="form-group">
                            <label>Kategori Dokumen</label>
                            <select class="styled-select">
                                <option>Form Inventory Harian</option>
                                <option>Form Quality Control</option>
                                <option>Laporan Kasir</option>
                            </select>
                        </div>

                        <div class="upload-zone" id="upload-zone">
                            <i class="ti ti-cloud-upload"></i>
                            <p>Drag & drop file Excel di sini atau klik untuk browse</p>
                            <input type="file" hidden id="file-input" accept=".xlsx, .xls">
                            <button class="btn-outline mt-2" onclick="document.getElementById('file-input').click()">Pilih File</button>
                        </div>

                        <div class="download-template">
                            <i class="ti ti-file-spreadsheet"></i>
                            <div>
                                <p>Kehilangan template form?</p>
                                <a href="#" class="link-download">Download Template Terbaru</a>
                            </div>
                        </div>

                        <div class="approval-status approved">
                            <i class="ti ti-circle-check"></i>
                            <div>
                                <h4>Status: Disetujui</h4>
                                <p>Laporan tanggal 4 Okt telah diverifikasi oleh Leader.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Reports View -->
            <section id="view-reports" class="view-section hidden">
                <div class="glass-panel mb-4 filter-bar">
                    <div class="filter-group">
                        <label>Tanggal</label>
                        <input type="date" class="styled-input">
                    </div>
                    <div class="filter-group">
                        <label>Divisi</label>
                        <select class="styled-select">
                            <option value="all">Semua Divisi</option>
                            <option value="kitchen">Kitchen</option>
                            <option value="front">Front Counter</option>
                            <option value="cashier">Cashier</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Shift</label>
                        <select class="styled-select">
                            <option value="all">Semua Shift</option>
                            <option value="opening">Opening</option>
                            <option value="middle">Middle</option>
                            <option value="closing">Closing</option>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button class="btn-primary"><i class="ti ti-filter"></i> Terapkan</button>
                        <button class="btn-export"><i class="ti ti-file-export"></i> Export Excel</button>
                    </div>
                </div>

                <div class="glass-panel">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Divisi</th>
                                    <th>Shift</th>
                                    <th>Kepatuhan Checklist</th>
                                    <th>Upload Modul</th>
                                    <th>Status Verifikasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>05 Okt 2026</td>
                                    <td>Kitchen</td>
                                    <td>Opening</td>
                                    <td><span class="badge-status success">100%</span></td>
                                    <td>2/2 File</td>
                                    <td><span class="badge-status success">Lengkap</span></td>
                                </tr>
                                <tr>
                                    <td>05 Okt 2026</td>
                                    <td>Cashier</td>
                                    <td>Closing</td>
                                    <td><span class="badge-status warning">80%</span></td>
                                    <td>0/1 File</td>
                                    <td><span class="badge-status danger">Belum Upload</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Sales Monitoring View -->
            <section id="view-sales" class="view-section hidden">
                <div class="panel-header mb-4" style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h2>AKTUAL SALES HARIAN STORE (Tracking Data Kosong)</h2>
                        <p class="sub-text m-0">Upload file Excel dari SharePoint untuk melihat kolom yang belum diisi.</p>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <select id="sheetSelector" class="styled-select" style="display: none; min-width: 200px; padding: 8px;" onchange="renderSelectedSheet()"></select>
                        <input type="file" id="localExcelFile" accept=".xlsx, .xls, .csv" style="display: none;" onchange="handleExcelUpload(event)">
                        <button class="btn-success" onclick="document.getElementById('localExcelFile').click()"><i class="ti ti-upload"></i> Upload & Cek File Excel</button>
                    </div>
                </div>

                <div class="glass-panel mb-5" style="padding: 0; overflow: hidden;">
                    <div class="excel-container" style="max-height: 500px; overflow-y: auto;">
                        <table class="excel-table" id="aktual-sales-table">
                            <thead>
                                <tr class="header-tier-1">
                                    <th rowspan="2" class="border-right" style="vertical-align: middle;">NO</th>
                                    <th rowspan="2" class="border-right" style="vertical-align: middle;"># STORE</th>
                                    <th rowspan="2" class="border-right" style="vertical-align: middle;">STORE</th>
                                    <th rowspan="2" class="border-right" style="vertical-align: middle;">OPENING DATE</th>
                                    <th rowspan="2" class="border-right" style="vertical-align: middle;">TYPE</th>
                                    <th colspan="31" style="text-align: center; background-color: #d1d5db; color: black; border-bottom: 1px solid #9ca3af;">AKTUAL SALES TANGGAL</th>
                                </tr>
                                <tr class="header-tier-2" style="background-color: #f3f4f6; color: black;">
                                    @for ($i = 1; $i <= 31; $i++)
                                        <th style="min-width: 60px; border-right: 1px solid #9ca3af;">{{ $i }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                <!-- AREA 17 -->
                                <tr style="background-color: #e2e8f0; font-weight: bold; color: black;">
                                    <td colspan="5" class="text-left border-right" style="padding-left: 10px;">AREA 17</td>
                                    @for ($i = 1; $i <= 31; $i++) <td style="border-right: 1px solid #9ca3af;"></td> @endfor
                                </tr>
                                <tr class="data-row">
                                    <td class="border-right">1</td><td class="border-right">A09</td><td class="border-right text-left" style="padding-left: 5px;">SUN PLAZA MEDAN</td><td class="border-right">04-Okt-2023</td><td class="border-right">Mall</td>
                                    <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true">10.054.455</td>
                                    <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true">13.054.554</td>
                                    <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true">15.420.515</td>
                                    <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true"></td>
                                    @for ($i = 5; $i <= 31; $i++) <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true"></td> @endfor
                                </tr>
                                <tr class="data-row">
                                    <td class="border-right">2</td><td class="border-right">A10</td><td class="border-right text-left" style="padding-left: 5px;">BING KOU MEDAN</td><td class="border-right">20-Des-2023</td><td class="border-right">Stand Alone</td>
                                    <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true">20.500.000</td>
                                    <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true">19.200.000</td>
                                    <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true"></td>
                                    <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true"></td>
                                    @for ($i = 5; $i <= 31; $i++) <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true"></td> @endfor
                                </tr>
                                <!-- AREA 18 -->
                                <tr style="background-color: #e2e8f0; font-weight: bold; color: black;">
                                    <td colspan="5" class="text-left border-right" style="padding-left: 10px;">AREA 18</td>
                                    @for ($i = 1; $i <= 31; $i++) <td style="border-right: 1px solid #9ca3af;"></td> @endfor
                                </tr>
                                <tr class="data-row">
                                    <td class="border-right">1</td><td class="border-right">A01</td><td class="border-right text-left" style="padding-left: 5px;">LIVING WORLD PEKAN BARU</td><td class="border-right">04-Jan-2020</td><td class="border-right">Mall</td>
                                    <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true">12.000.000</td>
                                    <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true"></td>
                                    <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true">11.500.000</td>
                                    <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true"></td>
                                    @for ($i = 5; $i <= 31; $i++) <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true"></td> @endfor
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="panel-header mb-4" style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h2>HARIAN SALES PERFORMANCE REGIONAL 5 TH 2026</h2>
                        <p class="sub-text m-0">Rekapitulasi Kinerja Seluruh Area (SharePoint Sync)</p>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button class="btn-primary" onclick="alert('Sinkronisasi SharePoint berjalan...')"><i class="ti ti-refresh"></i> Sync Data</button>
                        <a href="https://ekabogainti-my.sharepoint.com/:x:/g/personal/giri_handoko_hokben_co_id/IQAglXy_uxWuTL3_6jULiDPNAbCmC_K7FrMfxRi6-t283jI?e=FT1W9G&CID=7876485c-ad48-d43b-2814-f198d9fa5fe3" target="_blank" class="btn-outline" style="text-decoration: none;"><i class="ti ti-brand-office"></i> Buka SharePoint Asli</a>
                    </div>
                </div>

                <div class="glass-panel mb-5" style="padding: 0; overflow: hidden;">
                    <div class="excel-container">
                        <table class="excel-table">
                            <thead>
                                <tr class="header-tier-1">
                                    <th rowspan="2" class="border-right" style="vertical-align: middle;">Tanggal</th>
                                    <th colspan="3" class="border-right bg-blue">AREA 15</th>
                                    <th colspan="3" class="border-right bg-green">AREA 16</th>
                                    <th colspan="3" class="border-right bg-orange">AREA 17</th>
                                    <th colspan="3" class="border-right bg-blue">AREA 18</th>
                                    <th colspan="3" class="border-right bg-green">AREA 19</th>
                                    <th colspan="3" class="bg-orange">TOTAL REGIONAL 5</th>
                                </tr>
                                <tr class="header-tier-2">
                                    <th>Target</th><th>Actual</th><th class="border-right">% Ach</th>
                                    <th>Target</th><th>Actual</th><th class="border-right">% Ach</th>
                                    <th>Target</th><th>Actual</th><th class="border-right">% Ach</th>
                                    <th>Target</th><th>Actual</th><th class="border-right">% Ach</th>
                                    <th>Target</th><th>Actual</th><th class="border-right">% Ach</th>
                                    <th>Target</th><th>Actual</th><th>% Ach</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 1; $i <= 31; $i++)
                                <tr>
                                    <td class="text-bold border-right">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}-Okt-26</td>
                                    <td>50.000.000</td><td>45.200.000</td><td class="border-right text-success">90.4%</td>
                                    <td>45.000.000</td><td>47.500.000</td><td class="border-right text-success">105.5%</td>
                                    <td>60.000.000</td><td>55.000.000</td><td class="border-right text-success">91.6%</td>
                                    <td>55.000.000</td><td>58.200.000</td><td class="border-right text-success">105.8%</td>
                                    <td>65.000.000</td><td>68.500.000</td><td class="border-right text-success">105.3%</td>
                                    <td class="text-bold">275.000.000</td><td class="text-bold">274.400.000</td><td class="text-bold text-success">99.7%</td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="panel-header mb-4 mt-4" style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; margin-top: 24px;">
                    <div>
                        <h2>08. AGUSTUS - SALES AREA 19 2026 - SISWANTO</h2>
                        <p class="sub-text m-0">Sinkronisasi Langsung dari Google Sheets</p>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button class="btn-success" onclick="fetchArea19Data(this)"><i class="ti ti-refresh"></i> Sync Data</button>
                        <a href="https://docs.google.com/spreadsheets/d/1zNxrQc1z02yEXmCmvuvGAkkGlJcdwDPH/edit?gid=297723641#gid=297723641" target="_blank" class="btn-outline" style="text-decoration: none;"><i class="ti ti-brand-google-drive"></i> Buka Sheet Asli</a>
                    </div>
                </div>
                
                <div class="glass-panel" style="padding: 0; overflow: hidden;">
                    <div class="excel-container">
                        <table class="excel-table">
                            <thead>
                                <tr class="header-tier-1">
                                    <th colspan="2" class="border-right">Target Info</th>
                                    <th colspan="14" class="border-right bg-blue">Daily Sales Report - Laporan Sales per Layanan</th>
                                    <th colspan="14" class="border-right bg-green">Daily TC Report - Laporan TC per Layanan</th>
                                    <th colspan="8" class="border-right">ACTUAL SDM & MAN HOUR</th>
                                    <th colspan="10" class="bg-orange">Daily AC Report - Laporan AC per Layanan</th>
                                </tr>
                                <tr class="header-tier-2">
                                    <th>Target Sales Per Month</th>
                                    <th class="border-right">Target Sales Berjalan</th>
                                    
                                    <th rowspan="2">Tanggal</th>
                                    <th rowspan="2">Target Sales/Day</th>
                                    <th colspan="9">Channels (Sales)</th>
                                    <th rowspan="2">Total Sales</th>
                                    <th rowspan="2">Persentase/Day</th>
                                    <th rowspan="2" class="border-right">RUPIAH</th>
                                    
                                    <th rowspan="2">Target TC/Day</th>
                                    <th colspan="9">Channels (TC)</th>
                                    <th rowspan="2">Total TC</th>
                                    <th rowspan="2" class="border-right">Persentase/Day</th>

                                    <th rowspan="2">TMS</th>
                                    <th rowspan="2">CL+TMBS</th>
                                    <th rowspan="2">HONORER</th>
                                    <th rowspan="2">PART TIME</th>
                                    <th rowspan="2">CL/TMBS/HON</th>
                                    <th rowspan="2">PARTIME</th>
                                    <th rowspan="2">DI</th>
                                    <th rowspan="2" class="border-right">TA</th>

                                    <th rowspan="2">AC Per Day</th>
                                    <th colspan="9">Channels (AC)</th>
                                </tr>
                                <tr class="header-tier-3">
                                    <th class="text-bold">435.049.397</th>
                                    <th class="text-bold border-right">60.906.916</th>
                                    
                                    <!-- Sales Channels -->
                                    <th>Birthday</th><th>Dine in</th><th>Delivery</th><th>Expoo</th><th>Take Away</th><th>Drive Thru</th><th>Gofood</th><th>Grabfood</th><th>Shopeefood</th>
                                    
                                    <!-- TC Channels -->
                                    <th>Birthday</th><th>Dine in</th><th>Delivery</th><th>Expoo</th><th>Take Away</th><th>Drive Thru</th><th>Gofood</th><th>Grabfood</th><th>Shopeefood</th>
                                    
                                    <!-- AC Channels -->
                                    <th>Birthday</th><th>Dine in</th><th>Delivery</th><th>Expoo</th><th>Take Away</th><th>Drive Thru</th><th>Gofood</th><th>Grabfood</th><th>Shopeefood</th>
                                </tr>
                            </thead>
                            <tbody id="sales-table-body">
                                @for ($i = 1; $i <= 31; $i++)
                                <tr>
                                    <td></td><td class="border-right"></td>
                                    <td class="text-bold">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}-Okt-26</td>
                                    <td>13.051.482</td>
                                    <td>0</td><td>4.951.413</td><td>0</td><td>0</td><td>3.371.397</td><td>0</td><td>2.558.428</td><td>3.419.985</td><td>1.136.801</td>
                                    <td class="text-bold text-success">15.438.024</td>
                                    <td>118,3%</td>
                                    <td class="border-right"></td>
                                    
                                    <td>160</td>
                                    <td>0</td><td>65</td><td>0</td><td>0</td><td>40</td><td>0</td><td>29</td><td>26</td><td>17</td>
                                    <td class="text-bold text-success">177</td>
                                    <td class="border-right">110,6%</td>

                                    <td>2</td><td>4</td><td>2</td><td>1</td><td>39,00</td><td>4,00</td><td>4,12</td><td class="border-right">4</td>

                                    <td>87.220</td>
                                    <td>0</td><td>76.176</td><td>0</td><td>0</td><td>84.285</td><td>0</td><td>88.222</td><td>131.538</td><td>66.871</td>
                                </tr>
                                @endfor
                                
                                <!-- ROW TOTAL -->
                                <tr style="background-color: #f3f4f6; font-weight: bold;">
                                    <td colspan="3" class="text-right border-right">TOTAL BULAN INI</td>
                                    <td>404.595.942</td>
                                    <td>0</td><td>153.493.803</td><td>0</td><td>0</td><td>104.513.307</td><td>0</td><td>79.311.268</td><td>106.019.535</td><td>35.240.831</td>
                                    <td class="text-success">478.578.744</td>
                                    <td></td>
                                    <td class="border-right"></td>
                                    
                                    <td>4.960</td>
                                    <td>0</td><td>2.015</td><td>0</td><td>0</td><td>1.240</td><td>0</td><td>899</td><td>806</td><td>527</td>
                                    <td class="text-success">5.487</td>
                                    <td class="border-right"></td>

                                    <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td class="border-right">-</td>

                                    <td>-</td>
                                    <td>0</td><td>-</td><td>0</td><td>0</td><td>-</td><td>0</td><td>-</td><td>-</td><td>-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Serah Terima Sales & Modal Harian View -->
            <section id="view-serah-terima" class="view-section hidden">
                <div class="panel-header mb-4" style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 8px;">
                    <h2>SERAH TERIMA SALES DAN MODAL HARIAN</h2>
                    <p class="sub-text m-0">Form pencatatan serah terima sales dan modal kasir harian.</p>
                </div>
                
                <!-- Tabs -->
                <div class="tabs-container mb-4" style="display: flex; gap: 8px; overflow-x: auto; padding-bottom: 8px; border-bottom: 1px solid var(--border-color);">
                    <button class="tab-btn active" onclick="switchSerahTerimaTab('JANUARI')" style="padding: 10px 20px; border: none; border-radius: 8px; background: var(--primary); color: white; cursor: pointer; font-weight: 600;">Januari</button>
                    <button class="tab-btn" onclick="switchSerahTerimaTab('FEBRUARI')" style="padding: 10px 20px; border: none; border-radius: 8px; background: rgba(255,255,255,0.1); color: var(--text-color); cursor: pointer;">Februari</button>
                    <button class="tab-btn" onclick="switchSerahTerimaTab('MARET')" style="padding: 10px 20px; border: none; border-radius: 8px; background: rgba(255,255,255,0.1); color: var(--text-color); cursor: pointer;">Maret</button>
                    <button class="tab-btn" onclick="switchSerahTerimaTab('APRIL')" style="padding: 10px 20px; border: none; border-radius: 8px; background: rgba(255,255,255,0.1); color: var(--text-color); cursor: pointer;">April</button>
                    <button class="tab-btn" onclick="switchSerahTerimaTab('MEI')" style="padding: 10px 20px; border: none; border-radius: 8px; background: rgba(255,255,255,0.1); color: var(--text-color); cursor: pointer;">Mei</button>
                    <button class="tab-btn" onclick="switchSerahTerimaTab('JUNI')" style="padding: 10px 20px; border: none; border-radius: 8px; background: rgba(255,255,255,0.1); color: var(--text-color); cursor: pointer;">Juni</button>
                    <button class="tab-btn" onclick="switchSerahTerimaTab('JULI')" style="padding: 10px 20px; border: none; border-radius: 8px; background: rgba(255,255,255,0.1); color: var(--text-color); cursor: pointer;">Juli</button>
                    <button class="tab-btn" onclick="switchSerahTerimaTab('AGUSTUS')" style="padding: 10px 20px; border: none; border-radius: 8px; background: rgba(255,255,255,0.1); color: var(--text-color); cursor: pointer;">Agustus</button>
                </div>

                <div class="glass-panel" id="serah-terima-content" style="padding: 0; overflow: hidden; display: flex; flex-direction: column; height: 75vh;">
                    <div style="padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.3);">
                        <h3 id="serah-terima-title" style="margin: 0;">Data Serah Terima - JANUARI</h3>
                        <a id="serah-terima-external-link" href="https://docs.google.com/spreadsheets/d/19S0wZPXQ4T_u2DMtrUyLXhSpLfUe0tjr/edit?gid=285912838" target="_blank" class="btn-outline" style="text-decoration: none; padding: 6px 12px; font-size: 13px;">
                            <i class="ti ti-external-link"></i> Buka di Tab Baru
                        </a>
                    </div>
                    
                    <div style="flex-grow: 1; position: relative;">
                        <iframe id="serah-terima-iframe" src="https://docs.google.com/spreadsheets/d/19S0wZPXQ4T_u2DMtrUyLXhSpLfUe0tjr/htmlembed?widget=true&headers=false&gid=285912838" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;"></iframe>
                    </div>
                </div>
            </section>
        </main>
    </div>
    
    <!-- Modal Verifikasi -->
    <div id="verify-modal" class="modal">
        <div class="modal-content glass-panel">
            <div class="modal-header">
                <h2>Verifikasi File Excel</h2>
                <button class="close-modal"><i class="ti ti-x"></i></button>
            </div>
            <div class="modal-body">
                <p>Mengevaluasi file: <strong>Form_QC_Kitchen.xlsx</strong> dari <strong>Ahmad (Kitchen)</strong></p>
                <div class="preview-placeholder">
                    <i class="ti ti-file-spreadsheet"></i> Preview File tidak tersedia. Silakan unduh untuk mengecek.
                    <button class="btn-outline mt-2"><i class="ti ti-download"></i> Unduh File</button>
                </div>
                <div class="form-group mt-4">
                    <label>Catatan / Feedback (Jika ada perbaikan)</label>
                    <textarea class="styled-input" rows="3" placeholder="Tuliskan catatan perbaikan..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-danger"><i class="ti ti-arrow-back-up"></i> Perlu Perbaikan</button>
                <button class="btn-success"><i class="ti ti-check"></i> Approve Lengkap</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('app.js') }}"></script>
    <script>
        function fetchArea19Data(btnElement) {
            const originalText = btnElement.innerHTML;
            btnElement.innerHTML = '<i class="ti ti-loader"></i> Menyinkronkan...';
            btnElement.disabled = true;

            fetch('{{ route("api.sync.area19") }}')
                .then(response => response.json())
                .then(result => {
                    btnElement.innerHTML = originalText;
                    btnElement.disabled = false;
                    
                    if(result.success) {
                        const rawData = result.data;
                        let missingDataAlerts = [];
                        
                        if (rawData && rawData.length > 0) {
                            const headers = rawData[0]; // Asumsikan baris pertama adalah header
                            
                            for (let r = 1; r < rawData.length; r++) {
                                const row = rawData[r];
                                // Mengecek setiap kolom berdasarkan panjang header
                                for (let c = 0; c < headers.length; c++) {
                                    if (row[c] === undefined || row[c] === null || String(row[c]).trim() === '') {
                                        let colName = headers[c] || `Kolom ke-${c+1}`;
                                        missingDataAlerts.push(`Baris ${r + 1} - Kolom "${colName}" belum diisi.`);
                                    }
                                }
                            }
                        }

                        if (missingDataAlerts.length > 0) {
                            alert("Peringatan! Terdapat data yang belum lengkap:\n\n" + missingDataAlerts.slice(0, 15).join("\n") + (missingDataAlerts.length > 15 ? `\n\n...dan ${missingDataAlerts.length - 15} data lainnya.` : ""));
                        } else {
                            alert("Berhasil menarik data! Semua kolom telah terisi.");
                        }
                        
                        console.log("Isi mentah (Raw Data) Google Sheet 'monitoring sales':", result.data);
                    } else {
                        alert("Gagal sinkronisasi: " + result.message);
                    }
                })
                .catch(error => {
                    btnElement.innerHTML = originalText;
                    btnElement.disabled = false;
                    alert("Terjadi kesalahan jaringan atau konfigurasi Google API Client belum selesai.");
                    console.error(error);
                });
        }

        // Include SheetJS dynamically if not already present
        if(typeof XLSX === 'undefined') {
            let script = document.createElement('script');
            script.src = "https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js";
            document.head.appendChild(script);
        }

        let globalWorkbook = null;

        function handleExcelUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    globalWorkbook = XLSX.read(data, {type: 'array'});
                    
                    // Populate sheet selector (hanya sheet yang tidak di-hide)
                    const sheetSelector = document.getElementById('sheetSelector');
                    sheetSelector.innerHTML = '';
                    
                    globalWorkbook.SheetNames.forEach((name, index) => {
                        let isHidden = false;
                        // SheetJS menyimpan status hidden di workbook.Workbook.Sheets
                        if(globalWorkbook.Workbook && globalWorkbook.Workbook.Sheets && globalWorkbook.Workbook.Sheets[index]) {
                            const hiddenState = globalWorkbook.Workbook.Sheets[index].Hidden;
                            if(hiddenState === 1 || hiddenState === 2) {
                                isHidden = true;
                            }
                        }

                        if(!isHidden) {
                            let option = document.createElement('option');
                            option.value = name;
                            option.text = name;
                            sheetSelector.appendChild(option);
                        }
                    });
                    
                    sheetSelector.style.display = 'block';

                    // Default select: Cari sheet yang namanya ada kata 'AKTUAL', jika tidak ada pilih index 4 atau 0
                    let isSelected = false;
                    for(let i = 0; i < sheetSelector.options.length; i++) {
                        if(sheetSelector.options[i].value.toUpperCase().includes('AKTUAL')) {
                            sheetSelector.selectedIndex = i;
                            isSelected = true;
                            break;
                        }
                    }
                    
                    if(!isSelected && sheetSelector.options.length > 4) {
                        sheetSelector.selectedIndex = 4; // Sheet ke-5 dari yang visible
                    }
                    
                    renderSelectedSheet();
                } catch(error) {
                    alert("Gagal membaca file Excel. Pastikan file valid.");
                    console.error(error);
                }
            };
            reader.readAsArrayBuffer(file);
        }

        function renderSelectedSheet() {
            if(!globalWorkbook) return;
            const sheetSelector = document.getElementById('sheetSelector');
            const sheetName = sheetSelector.value;
            const worksheet = globalWorkbook.Sheets[sheetName];
            
            // Convert ke JSON array (array of arrays) dengan raw: false agar format tanggal Excel (Opening Date) terbaca benar
            const jsonData = XLSX.utils.sheet_to_json(worksheet, {header: 1, raw: false});
            processExcelData(jsonData, sheetName);
        }

        function processExcelData(data, sheetName) {
            const tbody = document.querySelector('#aktual-sales-table tbody');
            tbody.innerHTML = ''; // Bersihkan tabel mockup lama
            
            let missingDataAlerts = [];
            let currentDay = 4; // Contoh batas pengecekan hari ini (bisa diubah dinamis)

            // Dapatkan nama bulan saat ini
            const currentDate = new Date();
            const monthsIndo = ["JANUARI", "FEBRUARI", "MARET", "APRIL", "MEI", "JUNI", "JULI", "AGUSTUS", "SEPTEMBER", "OKTOBER", "NOVEMBER", "DESEMBER"];
            const currentMonthStr = monthsIndo[currentDate.getMonth()]; // misal: "OKTOBER"

            let foundMonthCol = 0;
            // Cari posisi teks "OKTOBER" di baris header
            for (let r = 0; r < 10 && r < data.length; r++) {
                if (data[r]) {
                    for (let c = 0; c < data[r].length; c++) {
                        if (String(data[r][c]).toUpperCase().includes(currentMonthStr)) {
                            foundMonthCol = c;
                            r = 10;
                            break;
                        }
                    }
                }
            }

            // Auto-detect kolom pertama tanggal (mencari angka 1, 2, 3 berurutan)
            let startDateIndex = 5; // Default fallback
            for (let r = 0; r < 10 && r < data.length; r++) {
                if (data[r]) {
                    for (let c = 0; c < data[r].length; c++) {
                        if (String(data[r][c]).trim() === '1' && String(data[r][c+1]).trim() === '2' && String(data[r][c+2]).trim() === '3') {
                            startDateIndex = c;
                            // Jika kita menemukan angka 1 di dekat judul bulan saat ini, kita berhenti.
                            // Jika tidak, kita terus mencari (akan berhenti di bulan terakhir/paling kanan)
                            if (foundMonthCol > 0 && c >= foundMonthCol - 5) {
                                r = 10; 
                                break;
                            }
                        }
                    }
                }
            }

            // Asumsi struktur row data dimulai setelah header (misal dari baris ke-4 ke atas)
            let storeCount = 0;
            data.forEach((row, rowIndex) => {
                // Lewati baris yang kosong atau header. Identifikasi baris store biasanya ada kode store di index 1 atau 2
                if(row && row.length > 3 && typeof row[2] === 'string' && (row[2].includes('AREA') === false) && rowIndex > 2) {
                    
                    // Pastikan baris ini punya nomor urut (index 0)
                    if(row[0] && !isNaN(row[0])) {
                        storeCount++;
                        const no = row[0];
                        const storeCode = row[1] || '-';
                        const storeName = row[2] || 'Unknown Store';
                        const openingDate = row[3] || '-';
                        const type = row[4] || '-';

                        let tr = document.createElement('tr');
                        tr.className = 'data-row';
                        
                        let html = `<td class="border-right">${no}</td>
                                    <td class="border-right">${storeCode}</td>
                                    <td class="border-right text-left" style="padding-left: 5px;">${storeName}</td>
                                    <td class="border-right">${openingDate}</td>
                                    <td class="border-right">${type}</td>`;
                        
                        // Kolom data tanggal 1 sampai 31
                        for(let i = 1; i <= 31; i++) {
                            const dataIndex = startDateIndex + i - 1;
                            const cellValue = row[dataIndex] !== undefined ? row[dataIndex] : '';
                            
                            // Cek jika data kosong sampai dengan currentDay
                            let isMissing = false;
                            if(i <= currentDay && (cellValue === '' || cellValue === null || cellValue === undefined)) {
                                isMissing = true;
                                missingDataAlerts.push(`Store ${storeName} belum mengisi data tanggal ${i}.`);
                            }

                            let bgColor = isMissing ? '#ffebee' : 'yellow';
                            let border = isMissing ? '2px solid red' : '1px solid #9ca3af';

                            html += `<td style="background-color: ${bgColor}; color: black; border-right: ${border};" contenteditable="true">${cellValue}</td>`;
                        }
                        
                        tr.innerHTML = html;
                        tbody.appendChild(tr);
                    }
                }
            });

            if(storeCount === 0) {
                alert(`Tidak menemukan format data store yang cocok di sheet "${sheetName}". Coba cek file Anda.`);
                return;
            }

            if (missingDataAlerts.length > 0) {
                alert(`Peringatan! Terdapat data yang belum diisi (Pengecekan s/d tgl ${currentDay}):\n\n` + 
                      missingDataAlerts.slice(0, 10).join("\n") + 
                      (missingDataAlerts.length > 10 ? `\n\n...dan ${missingDataAlerts.length - 10} data lainnya.` : ""));
            } else {
                alert(`Lengkap! Semua store sudah mengisi data s/d tanggal ${currentDay}.`);
            }
        }
    </script>
</body>
</html>
