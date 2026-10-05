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
                <a href="#" class="nav-link" data-target="staff-portal">
                    <i class="ti ti-users"></i> Portal Staff
                </a>
                <a href="#" class="nav-link" data-target="reports">
                    <i class="ti ti-report-analytics"></i> Rekap & Laporan
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
                <div class="topbar-actions">
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
</body>
</html>
