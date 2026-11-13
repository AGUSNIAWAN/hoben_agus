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

    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #10b981; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="ti ti-circle-check" style="font-size: 1.2rem;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #fca5a5; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="ti ti-alert-circle" style="font-size: 1.2rem;"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <div class="staff-grid">
        <!-- Checklist Aktivitas Harian Staf -->
        <div class="glass-panel">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px; flex-wrap: wrap; gap: 8px;">
                <div>
                    <h2 style="margin: 0; display: flex; align-items: center; gap: 8px;">
                        <span>Checklist Aktivitas (<span id="active-shift-label">Opening</span>)</span>
                    </h2>
                    <p style="font-size: 0.82rem; color: var(--text-muted); margin: 3px 0 0 0;" id="shift-status-subtext">
                        Kepatuhan SOP gerai • <span id="checklist-progress-text">0/4 selesai</span>
                    </p>
                </div>
                <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                    <span id="checklist-saved-badge" class="badge-status warning">
                        <i class="ti ti-clock"></i> Belum Disimpan
                    </span>
                    <span class="badge" style="background: rgba(255,196,0,0.15); color: #FFC400; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700;">
                        {{ Auth::user()->division ?? 'All Division' }}
                    </span>
                </div>
            </div>

            <!-- Progress Bar Kelengkapan Checklist Shift -->
            <div style="margin-bottom: 16px; background: var(--border-color); border-radius: 9999px; height: 6px; overflow: hidden;">
                <div id="checklist-progress-fill" style="width: 0%; height: 100%; background: linear-gradient(90deg, #10b981 0%, #059669 100%); transition: width 0.35s ease;"></div>
            </div>
            
            <ul class="checklist" id="daily-checklist">
                <!-- Populated by JS based on shift selection and saved database state -->
            </ul>

            <button type="button" class="btn-primary full-width mt-4" id="btn-save-checklist" onclick="saveShiftChecklist()">
                <i class="ti ti-device-floppy"></i> Simpan Checklist ke Database
            </button>
            <div id="checklist-feedback" style="display: none; padding: 12px 16px; border-radius: 8px; margin-top: 14px; font-size: 13px; text-align: center; font-weight: 600;"></div>
        </div>

        <!-- Upload Form & Modul ke Server -->
        <div class="glass-panel upload-panel">
            <h2>Upload Form & Modul</h2>
            <p class="sub-text">Unggah file Excel (.xlsx, .xls, .csv) untuk diverifikasi oleh Store Leader.</p>
            
            <form id="upload-submission-form" action="{{ route('submissions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-3">
                    <label>Kategori Dokumen / Modul Form *</label>
                    <select name="excel_module_id" class="styled-select" required style="width: 100%;">
                        @foreach($modules as $module)
                            <option value="{{ $module->id }}">{{ $module->name }} ({{ $module->category ?? 'Umum' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label>Catatan Pengajuan (Opsional)</label>
                    <input type="text" name="notes" placeholder="Contoh: Form QC shift pagi telah terisi lengkap..." class="styled-input" style="width: 100%;">
                </div>

                <div class="upload-zone" id="upload-zone" onclick="document.getElementById('file-input').click()">
                    <i class="ti ti-cloud-upload" style="font-size: 2.2rem; margin-bottom: 8px;"></i>
                    <p style="margin: 0; font-size: 0.9rem;">Drag & drop file Excel di sini atau <strong style="color: var(--hokben-yellow);">klik untuk browse</strong></p>
                    <input type="file" hidden id="file-input" name="file" accept=".xlsx, .xls, .csv" required onchange="handleFileSelected(this)">
                    <p id="selected-file-label" style="display: none; color: #10b981; font-weight: 600; margin-top: 10px; font-size: 0.85rem;"></p>
                </div>

                <button type="submit" class="btn-primary full-width mt-3" id="submit-upload-btn" style="display: none;">
                    <i class="ti ti-send"></i> Kirim Form Excel ke Server
                </button>
            </form>

            <div class="download-template mt-3">
                <i class="ti ti-file-spreadsheet"></i>
                <div>
                    <p style="margin: 0; font-weight: 500;">Butuh template form resmi?</p>
                    <a href="https://docs.google.com/spreadsheets/d/1suQMBQyy_rX0pIFptMrqlrIN5GjdZTAT/edit" target="_blank" class="link-download">Lihat Template Spreadsheet Google Drive</a>
                </div>
            </div>

            @php
                $userTodaySubmission = $todaySubmissions->where('user_id', Auth::id())->first();
            @endphp
            @if($userTodaySubmission)
            <div class="approval-status {{ $userTodaySubmission->status === 'Lengkap' ? 'approved' : ($userTodaySubmission->status === 'Perlu Perbaikan' ? 'rejected' : 'pending') }} mt-3">
                <i class="ti {{ $userTodaySubmission->status === 'Lengkap' ? 'ti-circle-check' : ($userTodaySubmission->status === 'Perlu Perbaikan' ? 'ti-alert-circle' : 'ti-clock') }}"></i>
                <div>
                    <h4>Status: {{ $userTodaySubmission->status }}</h4>
                    <p>{{ $userTodaySubmission->notes ?? 'Dokumen hari ini telah tercatat di server.' }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════
         STATUS HARIAN HARI INI → Analisis Otomatis dari Excel yang di-upload
         ═══════════════════════════════════════════════════════════════════════ -->
    @php
        // Ambil submission hari ini yang punya analysis_data
        $analysisSubmission = $todaySubmissions->whereNotNull('analysis_data')->first();
        $analysis = $analysisSubmission ? json_decode($analysisSubmission->analysis_data, true) : null;
        $isHokBenSales = $analysis && ($analysis['is_hokben_sales'] ?? false);
        $metrics = $analysis['metrics'] ?? null;
        $channels = $metrics['channels'] ?? null;
        $topItems = $analysis['top_items'] ?? [];
        $insights = $analysis['insights'] ?? [];
        $sheetNames = $analysis['sheet_names'] ?? [];
        $monthNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
    @endphp

    <div class="glass-panel" style="margin-top: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
            <div>
                <h2 style="margin: 0; display: flex; align-items: center; gap: 10px; font-size: 1.15rem;">
                    <i class="ti ti-report-analytics" style="color: var(--hokben-yellow-dark); font-size: 1.4rem;"></i>
                    Status Harian — Analisis Laporan Hari Ini
                </h2>
                <p style="font-size: 0.82rem; color: var(--text-muted); margin: 4px 0 0 0;">
                    {{ now()->translatedFormat('l, d F Y') }} •
                    @if($analysisSubmission)
                        Upload oleh <strong>{{ $analysisSubmission->user->name ?? 'Staff' }}</strong> • {{ $analysisSubmission->created_at?->diffForHumans() }}
                    @else
                        Belum ada upload hari ini
                    @endif
                </p>
            </div>
            @if($analysisSubmission)
            <a href="{{ route('submissions.download', $analysisSubmission) }}" class="btn-primary" style="font-size: 0.82rem; padding: 8px 14px;">
                <i class="ti ti-download"></i> Unduh File Asli
            </a>
            @endif
        </div>

        @if(!$analysis)
        <!-- Belum ada upload hari ini -->
        <div style="text-align: center; padding: 60px 20px; color: var(--text-muted);">
            <i class="ti ti-file-upload" style="font-size: 3.5rem; display: block; margin-bottom: 14px; opacity: 0.35;"></i>
            <h3 style="font-weight: 700; color: var(--text-body); margin: 0 0 8px 0;">Belum Ada Data Analisis Hari Ini</h3>
            <p style="font-size: 0.88rem; margin: 0 0 20px 0;">Upload file Excel Laporan Penjualan melalui form di atas, sistem akan membaca dan menganalisis secara otomatis.</p>
            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.3); border-radius: 8px; font-size: 0.82rem; color: var(--hokben-yellow-dark); font-weight: 600;">
                <i class="ti ti-arrow-up"></i> Gunakan form upload di atas
            </span>
        </div>

        @elseif($isHokBenSales)
        <!-- ════════ ANALISIS LAPORAN PENJUALAN POS HOKBEN ════════ -->

        <!-- KPI Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 14px; margin-bottom: 22px;">
            <div style="background: linear-gradient(135deg, rgba(245,158,11,0.12) 0%, rgba(217,34,42,0.06) 100%); border: 1px solid rgba(245,158,11,0.25); border-radius: 12px; padding: 16px 18px;">
                <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0 0 4px 0; font-weight: 600; text-transform: uppercase; letter-spacing: .5px;">Total Omzet</p>
                <p style="font-size: 1.35rem; font-weight: 800; color: var(--hokben-yellow-dark); margin: 0;">{{ $metrics['formatted_revenue'] ?? '-' }}</p>
            </div>
            <div style="background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.2); border-radius: 12px; padding: 16px 18px;">
                <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0 0 4px 0; font-weight: 600; text-transform: uppercase; letter-spacing: .5px;">Total Porsi</p>
                <p style="font-size: 1.35rem; font-weight: 800; color: #10b981; margin: 0;">{{ $metrics['formatted_qty'] ?? '-' }}</p>
            </div>
            <div style="background: rgba(99,102,241,0.08); border: 1px solid rgba(99,102,241,0.2); border-radius: 12px; padding: 16px 18px;">
                <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0 0 4px 0; font-weight: 600; text-transform: uppercase; letter-spacing: .5px;">Varian Menu</p>
                <p style="font-size: 1.35rem; font-weight: 800; color: #818cf8; margin: 0;">{{ $metrics['total_menu_variants'] ?? 0 }} Item</p>
            </div>
            @if($channels)
            <div style="background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); border-radius: 12px; padding: 16px 18px;">
                <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0 0 4px 0; font-weight: 600; text-transform: uppercase; letter-spacing: .5px;">Online Delivery</p>
                <p style="font-size: 1.35rem; font-weight: 800; color: #f87171; margin: 0;">{{ $channels['online_delivery']['percentage'] ?? '0%' }}</p>
            </div>
            @endif
        </div>

        <!-- Channel Breakdown -->
        @if($channels)
        <div style="margin-bottom: 22px;">
            <h4 style="font-size: 0.9rem; font-weight: 700; color: var(--text-body); margin: 0 0 12px 0; display: flex; align-items: center; gap: 7px;">
                <i class="ti ti-git-branch" style="color: var(--hokben-yellow-dark);"></i> Breakdown Kanal Penjualan
            </h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
                @foreach([
                    ['label' => 'Dine In', 'key' => 'dine_in', 'icon' => 'ti-building-store', 'color' => '#10b981'],
                    ['label' => 'Take Away', 'key' => 'take_away', 'icon' => 'ti-shopping-bag', 'color' => '#F59E0B'],
                    ['label' => 'Online Delivery', 'key' => 'online_delivery', 'icon' => 'ti-truck-delivery', 'color' => '#f87171'],
                ] as $ch)
                @php $chData = $channels[$ch['key']] ?? null; @endphp
                @if($chData)
                <div style="background: var(--bg-subtle); border: 1px solid var(--border-color); border-radius: 10px; padding: 14px 16px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                        <i class="ti {{ $ch['icon'] }}" style="color: {{ $ch['color'] }}; font-size: 1.1rem;"></i>
                        <span style="font-weight: 700; font-size: 0.88rem;">{{ $ch['label'] }}</span>
                        <span style="margin-left: auto; font-weight: 800; color: {{ $ch['color'] }}; font-size: 0.95rem;">{{ $chData['percentage'] }}</span>
                    </div>
                    <p style="margin: 0 0 2px 0; font-size: 0.88rem; font-weight: 700; color: var(--text-main);">{{ $chData['formatted_revenue'] }}</p>
                    <p style="margin: 0; font-size: 0.78rem; color: var(--text-muted);">{{ number_format($chData['qty'], 0, ',', '.') }} porsi</p>
                    <div style="margin-top: 8px; height: 4px; background: var(--border-color); border-radius: 9999px; overflow: hidden;">
                        <div style="height: 100%; width: {{ rtrim($chData['percentage'],'%') }}%; background: {{ $ch['color'] }}; border-radius: 9999px;"></div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
        @endif

        <!-- Top 5 Menu Terlaris -->
        @if(count($topItems) > 0)
        <div style="margin-bottom: 22px;">
            <h4 style="font-size: 0.9rem; font-weight: 700; color: var(--text-body); margin: 0 0 12px 0; display: flex; align-items: center; gap: 7px;">
                <i class="ti ti-trophy" style="color: var(--hokben-yellow-dark);"></i> Top 5 Menu Terlaris
            </h4>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                @foreach($topItems as $idx => $item)
                <div style="display: flex; align-items: center; gap: 12px; background: var(--bg-subtle); border: 1px solid var(--border-color); border-radius: 9px; padding: 10px 14px;">
                    <span style="width: 24px; height: 24px; border-radius: 50%; background: {{ $idx === 0 ? '#F59E0B' : ($idx === 1 ? '#94a3b8' : ($idx === 2 ? '#cd7f32' : 'var(--border-color)')) }}; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800; color: {{ $idx < 3 ? '#fff' : 'var(--text-muted)' }}; flex-shrink: 0;">#{{ $idx + 1 }}</span>
                    <div style="flex: 1; min-width: 0;">
                        <p style="margin: 0; font-weight: 700; font-size: 0.88rem; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $item['name'] }}</p>
                        <p style="margin: 0; font-size: 0.75rem; color: var(--text-muted);">PLU {{ $item['plu'] }} • {{ number_format($item['total_qty'], 0, ',', '.') }} porsi</p>
                    </div>
                    <span style="font-weight: 800; color: var(--hokben-yellow-dark); font-size: 0.88rem; white-space: nowrap;">{{ $item['formatted_revenue'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Catatan & Insights -->
        @if(count($insights) > 0)
        <div style="background: rgba(99,102,241,0.06); border: 1px solid rgba(99,102,241,0.18); border-radius: 10px; padding: 16px 18px; margin-bottom: 22px;">
            <h4 style="font-size: 0.88rem; font-weight: 700; color: #818cf8; margin: 0 0 10px 0; display: flex; align-items: center; gap: 7px;">
                <i class="ti ti-bulb"></i> Insights & Rekomendasi Sistem
            </h4>
            <ul style="margin: 0; padding-left: 18px; display: flex; flex-direction: column; gap: 6px;">
                @foreach($insights as $insight)
                <li style="font-size: 0.84rem; color: var(--text-body); line-height: 1.5;">{{ $insight }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @else
        <!-- Generic Excel analysis -->
        <div style="background: var(--bg-subtle); border: 1px solid var(--border-color); border-radius: 10px; padding: 20px;">
            <h4 style="font-weight: 700; color: var(--text-main); margin: 0 0 10px 0;">
                <i class="ti ti-file-spreadsheet" style="color: var(--hokben-yellow-dark);"></i>
                {{ $analysis['title'] ?? 'Dokumen Excel Terunggah' }}
            </h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 10px; margin-bottom: 14px;">
                @if(isset($metrics['total_rows']))
                <div style="text-align: center; padding: 12px; background: rgba(16,185,129,0.08); border-radius: 8px;">
                    <p style="font-size: 1.3rem; font-weight: 800; color: #10b981; margin: 0;">{{ $metrics['total_rows'] }}</p>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0;">Baris Data</p>
                </div>
                @endif
                @if(isset($metrics['sheet_count']))
                <div style="text-align: center; padding: 12px; background: rgba(99,102,241,0.08); border-radius: 8px;">
                    <p style="font-size: 1.3rem; font-weight: 800; color: #818cf8; margin: 0;">{{ $metrics['sheet_count'] }}</p>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0;">Sheet</p>
                </div>
                @endif
            </div>
            @if(count($insights) > 0)
            <ul style="margin: 0; padding-left: 18px;">
                @foreach($insights as $i)
                <li style="font-size: 0.84rem; color: var(--text-body); margin-bottom: 4px;">{{ $i }}</li>
                @endforeach
            </ul>
            @endif
        </div>
        @endif
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════
         SETBAR (Timeline Upload) — Semua upload hari ini dari seluruh staf
         ═══════════════════════════════════════════════════════════════════════ -->
    @if($todaySubmissions->count() > 0)
    <div class="glass-panel" style="margin-top: 20px;">
        <h3 style="margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; font-size: 1rem; font-weight: 800;">
            <i class="ti ti-timeline" style="color: var(--hokben-yellow-dark);"></i>
            Setbar Upload Hari Ini — {{ $todaySubmissions->count() }} Dokumen
        </h3>
        <div style="display: flex; flex-direction: column; gap: 0;">
            @foreach($todaySubmissions as $sub)
            @php
                $subAnalysis = $sub->analysis_data ? json_decode($sub->analysis_data, true) : null;
                $subMetrics = $subAnalysis['metrics'] ?? null;
                $statusColor = $sub->status === 'Lengkap' ? '#10b981' : ($sub->status === 'Perlu Perbaikan' ? '#ef4444' : '#F59E0B');
                $statusIcon = $sub->status === 'Lengkap' ? 'ti-circle-check' : ($sub->status === 'Perlu Perbaikan' ? 'ti-alert-circle' : 'ti-clock');
            @endphp
            <div style="display: flex; gap: 14px; padding: 14px 0; border-bottom: 1px solid var(--border-color); position: relative;">
                <!-- Timeline dot -->
                <div style="display: flex; flex-direction: column; align-items: center; flex-shrink: 0; padding-top: 2px;">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: {{ $statusColor }}22; border: 2px solid {{ $statusColor }}; display: flex; align-items: center; justify-content: center; color: {{ $statusColor }}; font-size: 0.95rem;">
                        <i class="ti {{ $statusIcon }}"></i>
                    </div>
                    @if(!$loop->last)
                    <div style="width: 2px; flex: 1; background: var(--border-color); margin-top: 6px; min-height: 20px;"></div>
                    @endif
                </div>
                <!-- Content -->
                <div style="flex: 1; min-width: 0;">
                    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; flex-wrap: wrap;">
                        <div>
                            <p style="margin: 0 0 2px 0; font-weight: 700; font-size: 0.9rem; color: var(--text-main);">
                                {{ $sub->user->name ?? 'Staff' }}
                                <span style="font-weight: 500; color: var(--text-muted); font-size: 0.8rem;"> — {{ $sub->module->name ?? 'Form Excel' }}</span>
                            </p>
                            <p style="margin: 0; font-size: 0.78rem; color: var(--text-muted);">
                                {{ $sub->created_at ? $sub->created_at->format('H:i') : '-' }} •
                                {{ $sub->user->division ?? ($sub->module->category ?? 'Umum') }}
                            </p>
                        </div>
                        <span style="font-size: 0.75rem; font-weight: 700; color: {{ $statusColor }}; background: {{ $statusColor }}15; border: 1px solid {{ $statusColor }}33; padding: 3px 10px; border-radius: 20px; white-space: nowrap; flex-shrink: 0;">
                            {{ $sub->status }}
                        </span>
                    </div>
                    @if($subMetrics && isset($subMetrics['formatted_revenue']))
                    <div style="display: flex; gap: 10px; margin-top: 8px; flex-wrap: wrap;">
                        <span style="font-size: 0.78rem; background: rgba(245,158,11,0.1); color: var(--hokben-yellow-dark); padding: 3px 10px; border-radius: 6px; font-weight: 700;">
                            💰 {{ $subMetrics['formatted_revenue'] }}
                        </span>
                        @if(isset($subMetrics['formatted_qty']))
                        <span style="font-size: 0.78rem; background: rgba(16,185,129,0.1); color: #10b981; padding: 3px 10px; border-radius: 6px; font-weight: 700;">
                            🍱 {{ $subMetrics['formatted_qty'] }}
                        </span>
                        @endif
                        @if(isset($subMetrics['total_menu_variants']))
                        <span style="font-size: 0.78rem; background: rgba(99,102,241,0.1); color: #818cf8; padding: 3px 10px; border-radius: 6px; font-weight: 700;">
                            📋 {{ $subMetrics['total_menu_variants'] }} varian
                        </span>
                        @endif
                    </div>
                    @endif
                    @if($sub->notes && $sub->notes !== 'Menunggu pengecekan kelengkapan dari Store Leader.')
                    <p style="margin: 6px 0 0 0; font-size: 0.78rem; color: var(--text-muted); font-style: italic;">💬 {{ $sub->notes }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- ═══════════════════════════════════════════════════════════════════════
         BULAN 1-12 — Tab Bulanan dengan Detail dari Sheet Excel
         ═══════════════════════════════════════════════════════════════════════ -->
    @php
        $monthNamesLong = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $currentMonth = (int)now()->format('n'); // 1-12
    @endphp
    <div class="glass-panel" style="margin-top: 20px;">
        <h3 style="margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; font-size: 1rem; font-weight: 800;">
            <i class="ti ti-calendar-month" style="color: var(--hokben-yellow-dark);"></i>
            Laporan Bulanan — Klik Bulan untuk Detail
        </h3>

        <!-- Tab Bulan 1-12 -->
        <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 16px;" id="month-tabs-container">
            @foreach($monthNamesLong as $mIdx => $mName)
            @php $mNum = $mIdx + 1; @endphp
            <button
                onclick="switchMonthTab({{ $mNum }}, '{{ $mName }}')"
                id="month-tab-{{ $mNum }}"
                style="padding: 7px 14px; border-radius: 8px; font-size: 0.82rem; font-weight: 700; cursor: pointer; border: 1px solid {{ $mNum === $currentMonth ? 'var(--hokben-yellow-dark)' : 'var(--border-color)' }}; background: {{ $mNum === $currentMonth ? 'rgba(245,158,11,0.15)' : 'var(--bg-subtle)' }}; color: {{ $mNum === $currentMonth ? 'var(--hokben-yellow-dark)' : 'var(--text-muted)' }}; transition: all 0.2s;">
                {{ substr($mName, 0, 3) }}
            </button>
            @endforeach
        </div>

        <!-- Panel konten per bulan -->
        <div id="month-panel-content">
            @foreach($monthNamesLong as $mIdx => $mName)
            @php
                $mNum = $mIdx + 1;
                // Coba match sheet dari sheetNames (dari analysis)
                $sheetIdx = -1;
                foreach($sheetNames as $si => $sName) {
                    if(stripos($sName, $mName) !== false || stripos($sName, substr($mName, 0, 3)) !== false) {
                        $sheetIdx = $si;
                        break;
                    }
                }
                $hasSheetData = $sheetIdx >= 0;
            @endphp
            <div id="month-content-{{ $mNum }}" style="display: {{ $mNum === $currentMonth ? 'block' : 'none' }};">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <h4 style="margin: 0; font-size: 0.95rem; font-weight: 800; color: var(--text-main);">
                            {{ $mName }} {{ now()->year }}
                        </h4>
                        <p style="margin: 2px 0 0 0; font-size: 0.78rem; color: var(--text-muted);">
                            @if($hasSheetData && $isHokBenSales)
                                Sheet ditemukan: "{{ $sheetNames[$sheetIdx] }}" — data dari file yang diupload
                            @elseif($isHokBenSales)
                                File laporan {{ now()->year }} tersedia — sheet {{ $mName }} belum ada di file yang diupload
                            @else
                                Upload file Laporan Penjualan untuk melihat data bulan ini
                            @endif
                        </p>
                    </div>
                    <!-- Upload per-bulan -->
                    <form action="{{ route('submissions.store') }}" method="POST" enctype="multipart/form-data"
                          style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;"
                          onsubmit="this.querySelector('.btn-upload-month').disabled=true; this.querySelector('.btn-upload-month').innerHTML='<i class=\'ti ti-loader\'></i> Mengupload...'">
                        @csrf
                        <input type="hidden" name="excel_module_id" value="{{ $modules->first()?->id ?? 1 }}">
                        <input type="hidden" name="notes" value="Upload data bulan {{ $mName }} {{ now()->year }}">
                        <label style="padding: 7px 12px; background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.3); border-radius: 8px; cursor: pointer; font-size: 0.8rem; font-weight: 700; color: #818cf8; display: flex; align-items: center; gap: 6px;">
                            <i class="ti ti-upload"></i> Upload {{ substr($mName, 0, 3) }}
                            <input type="file" hidden name="file" accept=".xlsx,.xls,.csv"
                                onchange="this.closest('form').querySelector('.btn-upload-month').style.display='inline-flex'; this.closest('form').querySelector('.file-name-label').textContent=this.files[0]?.name?.substring(0,20)+'...'">
                        </label>
                        <span class="file-name-label" style="font-size: 0.75rem; color: var(--text-muted); max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"></span>
                        <button type="submit" class="btn-primary btn-upload-month" style="display: none; font-size: 0.8rem; padding: 7px 12px;">
                            <i class="ti ti-send"></i> Kirim
                        </button>
                    </form>
                </div>

                @if($isHokBenSales && $analysis)
                <!-- Tampilkan ringkasan dari file yang diupload (data agregat, bukan per-sheet) -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; margin-bottom: 14px;">
                    <div style="background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.2); border-radius: 10px; padding: 14px; text-align: center;">
                        <p style="font-size: 0.72rem; color: var(--text-muted); margin: 0 0 4px 0; font-weight: 600; text-transform: uppercase;">Omzet (Agregat)</p>
                        <p style="font-size: 1.1rem; font-weight: 800; color: var(--hokben-yellow-dark); margin: 0;">{{ $metrics['formatted_revenue'] ?? '-' }}</p>
                    </div>
                    <div style="background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.2); border-radius: 10px; padding: 14px; text-align: center;">
                        <p style="font-size: 0.72rem; color: var(--text-muted); margin: 0 0 4px 0; font-weight: 600; text-transform: uppercase;">Total Porsi</p>
                        <p style="font-size: 1.1rem; font-weight: 800; color: #10b981; margin: 0;">{{ $metrics['formatted_qty'] ?? '-' }}</p>
                    </div>
                    <div style="background: rgba(99,102,241,0.08); border: 1px solid rgba(99,102,241,0.2); border-radius: 10px; padding: 14px; text-align: center;">
                        <p style="font-size: 0.72rem; color: var(--text-muted); margin: 0 0 4px 0; font-weight: 600; text-transform: uppercase;">Varian Menu</p>
                        <p style="font-size: 1.1rem; font-weight: 800; color: #818cf8; margin: 0;">{{ $metrics['total_menu_variants'] ?? 0 }}</p>
                    </div>
                </div>
                <!-- Top 5 menu untuk bulan ini (dari data agregat) -->
                @if(count($topItems) > 0)
                <h5 style="font-size: 0.84rem; font-weight: 700; color: var(--text-body); margin: 0 0 10px 0;">
                    <i class="ti ti-star" style="color: var(--hokben-yellow-dark);"></i> Top 5 Menu Terlaris
                </h5>
                <div style="display: flex; flex-direction: column; gap: 6px;">
                    @foreach(array_slice($topItems, 0, 5) as $ri => $rItem)
                    <div style="display: flex; align-items: center; gap: 10px; background: var(--bg-subtle); border-radius: 8px; padding: 8px 12px;">
                        <span style="font-size: 0.8rem; font-weight: 800; color: {{ $ri===0?'#F59E0B':($ri===1?'#94a3b8':($ri===2?'#cd7f32':'var(--text-muted)')) }}; width: 20px; text-align: center;">#{{ $ri+1 }}</span>
                        <div style="flex: 1; min-width: 0;">
                            <p style="margin: 0; font-size: 0.84rem; font-weight: 700; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $rItem['name'] }}</p>
                            <p style="margin: 0; font-size: 0.72rem; color: var(--text-muted);">{{ number_format($rItem['total_qty'], 0, ',', '.') }} porsi</p>
                        </div>
                        <span style="font-weight: 800; color: var(--hokben-yellow-dark); font-size: 0.84rem; white-space: nowrap;">{{ $rItem['formatted_revenue'] }}</span>
                    </div>
                    @endforeach
                </div>
                @endif

                @else
                <!-- Belum ada data untuk bulan ini -->
                <div style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
                    <i class="ti ti-calendar-off" style="font-size: 2.5rem; display: block; margin-bottom: 10px; opacity: 0.35;"></i>
                    <p style="font-size: 0.88rem; margin: 0;">Belum ada data untuk bulan <strong>{{ $mName }}</strong>.</p>
                    <p style="font-size: 0.8rem; margin: 6px 0 0 0; opacity: 0.7;">Gunakan tombol upload di atas untuk mengunggah laporan {{ $mName }}.</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>

<script>
function switchMonthTab(monthNum, monthName) {
    // Sembunyikan semua panel
    for (var m = 1; m <= 12; m++) {
        var panel = document.getElementById('month-content-' + m);
        var tab   = document.getElementById('month-tab-' + m);
        if (panel) panel.style.display = 'none';
        if (tab) {
            tab.style.border    = '1px solid var(--border-color)';
            tab.style.background = 'var(--bg-subtle)';
            tab.style.color     = 'var(--text-muted)';
        }
    }
    // Tampilkan yang dipilih
    var active = document.getElementById('month-content-' + monthNum);
    var activeTab = document.getElementById('month-tab-' + monthNum);
    if (active) active.style.display = 'block';
    if (activeTab) {
        activeTab.style.border    = '1px solid var(--hokben-yellow-dark)';
        activeTab.style.background = 'rgba(245,158,11,0.15)';
        activeTab.style.color     = 'var(--hokben-yellow-dark)';
    }
}
</script>
