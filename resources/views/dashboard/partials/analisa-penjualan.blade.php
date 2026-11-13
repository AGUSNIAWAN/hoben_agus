{{-- ============================================================ --}}
{{-- ANALISA DATA PENJUALAN PER GROUP STORE RUKO PLAJU (A20)       --}}
{{-- Data Source: Laporan Penjualan Per Group (Crystal Reports)    --}}
{{-- Periode: 10 Bulan (Januari s.d. Oktober 2026)                 --}}
{{-- ============================================================ --}}
<section id="view-analisa-penjualan" class="view-section hidden">

    {{-- ===== 1. HEADER HERO BANNER ===== --}}
    <div style="background: linear-gradient(135deg, rgba(217,34,42,0.12) 0%, rgba(245,158,11,0.14) 50%, rgba(16,185,129,0.08) 100%); border: 1px solid rgba(217,34,42,0.22); border-radius: 18px; padding: 24px 28px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 18px; box-shadow: 0 4px 20px -2px rgba(217,34,42,0.06);">
        <div style="display: flex; align-items: center; gap: 18px;">
            <div style="width: 58px; height: 58px; border-radius: 16px; flex-shrink: 0; background: linear-gradient(135deg, #D9222A 0%, #FFC400 100%); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #fff; box-shadow: 0 8px 24px rgba(217,34,42,0.35);">
                <i class="ti ti-chart-histogram"></i>
            </div>
            <div>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px; flex-wrap: wrap;">
                    <h2 style="margin: 0; font-size: 1.45rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.02em;">
                        Analisa Penjualan Store HokBen Plaju (A20)
                    </h2>
                    <span style="font-size: 0.75rem; font-weight: 700; padding: 3px 10px; border-radius: 20px; background: rgba(217,34,42,0.15); color: var(--hokben-red); border: 1px solid rgba(217,34,42,0.3);">
                        KODE STORE: A20
                    </span>
                    <span style="font-size: 0.75rem; font-weight: 700; padding: 3px 10px; border-radius: 20px; background: rgba(245,158,11,0.15); color: #F59E0B; border: 1px solid rgba(245,158,11,0.3);">
                        AREA 19 SUMBAGSEL
                    </span>
                </div>
                <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.4;">
                    Laporan Penjualan Per Group &middot; POS Terminal <strong>PA2001 &ndash; PA2005 (5 POS)</strong> &middot; Shophouse DI &middot; 
                    Periode Analisis: <strong style="color: var(--text-body);">Januari s.d. Oktober 2026 (10 Bulan Lengkap)</strong>
                </p>
            </div>
        </div>

        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            {{-- Pilihan Berkas dari Histori Upload --}}
            <div style="display: flex; align-items: center; gap: 8px; background: var(--panel-bg); padding: 6px 12px; border-radius: 12px; border: 1px solid var(--border-color);">
                <i class="ti ti-file-analytics" style="color: #10b981; font-size: 1.15rem;"></i>
                <label for="historyFileSelect" style="font-size: 0.78rem; font-weight: 600; color: var(--text-muted); margin: 0; white-space: nowrap;">Pilih Berkas Histori:</label>
                <select id="historyFileSelect" class="styled-select" style="font-size: 0.82rem; font-weight: 700; padding: 4px 8px; border: none; background: transparent; color: var(--text-main); cursor: pointer; max-width: 280px;" onchange="handleHistoryFileSelect(this.value)">
                    <option value="DEFAULT">📊 Dataset Master Store A20 (Jan - Okt 2026)</option>
                    @foreach($uploadHistory ?? [] as $hist)
                        @php
                            $fTitle = $hist->original_name ?? basename($hist->file_path ?? 'Dokumen.xlsx');
                            $shortName = Str::limit($fTitle, 30);
                            $uploader = $hist->user->name ?? 'Staff';
                            $tgl = $hist->created_at ? $hist->created_at->format('d/m H:i') : '-';
                        @endphp
                        <option value="{{ $hist->id }}">
                            📄 #{{ $hist->id }} &bull; {{ $shortName }} ({{ $uploader }} - {{ $tgl }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Period Filter Dropdown --}}
            <div style="display: flex; align-items: center; gap: 8px; background: var(--panel-bg); padding: 6px 12px; border-radius: 12px; border: 1px solid var(--border-color);">
                <i class="ti ti-calendar" style="color: var(--hokben-yellow-dark); font-size: 1.1rem;"></i>
                <label for="salesMonthFilter" style="font-size: 0.78rem; font-weight: 600; color: var(--text-muted); margin: 0;">Filter Periode:</label>
                <select id="salesMonthFilter" class="styled-select" style="font-size: 0.82rem; font-weight: 700; padding: 4px 8px; border: none; background: transparent; color: var(--text-main); cursor: pointer;" onchange="handleSalesMonthChange(this.value)">
                    <option value="ALL">Kumulatif 10 Bulan (Jan - Okt 2026)</option>
                    <option value="JANUARI">Januari 2026</option>
                    <option value="FEBRUARI">Februari 2026</option>
                    <option value="MARET">Maret 2026 (Ramadan Peak)</option>
                    <option value="APRIL">April 2026</option>
                    <option value="MEI">Mei 2026</option>
                    <option value="JUNI">Juni 2026</option>
                    <option value="JULI">Juli 2026</option>
                    <option value="AGUSTUS">Agustus 2026 (Promo Merdeka)</option>
                    <option value="SEPTEMBER">September 2026</option>
                    <option value="OKTOBER">Oktober 2026</option>
                </select>
            </div>

            <button type="button" class="btn-outline" onclick="exportSalesDataCSV()" style="padding: 8px 14px; font-size: 0.82rem; font-weight: 700; border-radius: 10px; display: inline-flex; align-items: center; gap: 6px;">
                <i class="ti ti-download"></i>
                <span>Export CSV</span>
            </button>

            <button type="button"
                    onclick="toggleUploadPanel('upload-analisa')"
                    style="padding: 8px 14px; font-size: 0.82rem; font-weight: 700; border-radius: 10px; display: inline-flex; align-items: center; gap: 6px; background: rgba(217,34,42,0.15); border: 1px solid rgba(217,34,42,0.35); color: var(--hokben-red); cursor: pointer;">
                <i class="ti ti-upload"></i>
                <span>Upload Laporan Baru</span>
            </button>
        </div>
    </div>

    {{-- Upload Panel Analisa Penjualan --}}
    @include('dashboard.partials.upload-panel', [
        'panelId'   => 'upload-analisa',
        'title'     => 'Upload Laporan Penjualan Per Group',
        'subtitle'  => 'Upload file XLSX Laporan Penjualan POS untuk dianalisis otomatis (data bulan/multi-sheet).',
        'icon'      => 'ti-chart-histogram',
        'iconColor' => '#D9222A',
        'moduleId'  => $modules->first()?->id ?? 1,
        'noteText'  => 'Laporan Penjualan Per Group Store A20 Ruko Plaju',
    ])

    {{-- Tempat Munculnya Analisis Berkas yang Dipilih dari Histori --}}
    <div id="history-file-analysis-card" style="display: none; margin-bottom: 24px;"></div>

    {{-- ===== 2. EXECUTIVE KPI STAT CARDS ===== --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(185px, 1fr)); gap: 16px; margin-bottom: 24px;">

        {{-- Card 1: Total Revenue --}}
        <div class="glass-panel" style="padding: 18px 20px; border-left: 4px solid var(--hokben-red); position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; width: 70px; height: 70px; border-radius: 50%; background: rgba(217,34,42,0.06);"></div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 0.76rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .05em;">Total Penjualan Bruto</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(217,34,42,0.12); display: flex; align-items: center; justify-content: center; color: var(--hokben-red); font-size: 1.1rem;">
                    <i class="ti ti-cash"></i>
                </div>
            </div>
            <p id="kpi-total-revenue" style="margin: 0; font-size: 1.55rem; font-weight: 800; color: var(--text-main); line-height: 1.1;">Rp 4,80 M</p>
            <p id="kpi-avg-monthly" style="margin: 6px 0 0 0; font-size: 0.75rem; color: var(--text-muted);">
                Rata-rata: <strong>Rp 480,1 Jt / bln</strong>
            </p>
        </div>

        {{-- Card 2: Total Quantity --}}
        <div class="glass-panel" style="padding: 18px 20px; border-left: 4px solid #F59E0B; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; width: 70px; height: 70px; border-radius: 50%; background: rgba(245,158,11,0.06);"></div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 0.76rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .05em;">Total Volume Produk</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(245,158,11,0.12); display: flex; align-items: center; justify-content: center; color: #F59E0B; font-size: 1.1rem;">
                    <i class="ti ti-packages"></i>
                </div>
            </div>
            <p id="kpi-total-qty" style="margin: 0; font-size: 1.55rem; font-weight: 800; color: var(--text-main); line-height: 1.1;">167.252</p>
            <p id="kpi-avg-price" style="margin: 6px 0 0 0; font-size: 0.75rem; color: var(--text-muted);">
                Rata-rata: <strong>16.725 item / bln</strong>
            </p>
        </div>

        {{-- Card 3: Online Delivery Aggregators --}}
        <div class="glass-panel" style="padding: 18px 20px; border-left: 4px solid #10b981; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; width: 70px; height: 70px; border-radius: 50%; background: rgba(16,185,129,0.06);"></div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 0.76rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .05em;">Online Food (GF+SF+GR)</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(16,185,129,0.12); display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 1.1rem;">
                    <i class="ti ti-moped"></i>
                </div>
            </div>
            <p id="kpi-online-revenue" style="margin: 0; font-size: 1.55rem; font-weight: 800; color: #10b981; line-height: 1.1;">Rp 1,64 M</p>
            <p id="kpi-online-share" style="margin: 6px 0 0 0; font-size: 0.75rem; color: var(--text-muted);">
                Pangsa: <strong>34,1%</strong> &bull; 64.113 item
            </p>
        </div>

        {{-- Card 4: Dine In (On-Premise) --}}
        <div class="glass-panel" style="padding: 18px 20px; border-left: 4px solid #3B82F6; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; width: 70px; height: 70px; border-radius: 50%; background: rgba(59,130,246,0.06);"></div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 0.76rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .05em;">Dine In (Makan Tempat)</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(59,130,246,0.12); display: flex; align-items: center; justify-content: center; color: #3B82F6; font-size: 1.1rem;">
                    <i class="ti ti-armchair"></i>
                </div>
            </div>
            <p id="kpi-dinein-revenue" style="margin: 0; font-size: 1.55rem; font-weight: 800; color: var(--text-main); line-height: 1.1;">Rp 1,61 M</p>
            <p id="kpi-dinein-share" style="margin: 6px 0 0 0; font-size: 0.75rem; color: var(--text-muted);">
                Pangsa: <strong>33,5%</strong> &bull; 56.668 item
            </p>
        </div>

        {{-- Card 5: Take Away --}}
        <div class="glass-panel" style="padding: 18px 20px; border-left: 4px solid #8B5CF6; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; width: 70px; height: 70px; border-radius: 50%; background: rgba(139,92,246,0.06);"></div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 0.76rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .05em;">Take Away (Bungkus)</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(139,92,246,0.12); display: flex; align-items: center; justify-content: center; color: #8B5CF6; font-size: 1.1rem;">
                    <i class="ti ti-shopping-bag"></i>
                </div>
            </div>
            <p id="kpi-takeaway-revenue" style="margin: 0; font-size: 1.55rem; font-weight: 800; color: var(--text-main); line-height: 1.1;">Rp 1,04 M</p>
            <p id="kpi-takeaway-share" style="margin: 6px 0 0 0; font-size: 0.75rem; color: var(--text-muted);">
                Pangsa: <strong>21,6%</strong> &bull; 32.430 item
            </p>
        </div>

        {{-- Card 6: Top Product #1 --}}
        <div class="glass-panel" style="padding: 18px 20px; border-left: 4px solid #ec4899; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; width: 70px; height: 70px; border-radius: 50%; background: rgba(236,72,153,0.06);"></div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 0.76rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .05em;">Produk Terlaris #1</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(236,72,153,0.12); display: flex; align-items: center; justify-content: center; color: #ec4899; font-size: 1.1rem;">
                    <i class="ti ti-trophy"></i>
                </div>
            </div>
            <p id="kpi-hero-name" style="margin: 0; font-size: 1.05rem; font-weight: 800; color: #ec4899; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="NEW BENTO SPECIAL 4">
                BENTO SPECIAL 4
            </p>
            <p id="kpi-hero-detail" style="margin: 6px 0 0 0; font-size: 0.75rem; color: var(--text-muted);">
                Omzet: <strong>Rp 505,4 Jt (10,5%)</strong>
            </p>
        </div>

    </div>

    {{-- ===== 3. SUB-NAVIGATION PILLS ===== --}}
    <div style="display: flex; gap: 8px; margin-bottom: 22px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px; overflow-x: auto; scrollbar-width: none;">
        <button type="button" class="analysis-subtab-btn active" data-tab="tab-overview" onclick="switchAnalysisTab('tab-overview', this)">
            <i class="ti ti-layout-grid"></i>
            <span>Ikhtisar & Tren Bulanan</span>
        </button>
        <button type="button" class="analysis-subtab-btn" data-tab="tab-channels" onclick="switchAnalysisTab('tab-channels', this)">
            <i class="ti ti-truck-delivery"></i>
            <span>Analisis Kanal Penjualan</span>
        </button>
        <button type="button" class="analysis-subtab-btn" data-tab="tab-groups" onclick="switchAnalysisTab('tab-groups', this)">
            <i class="ti ti-category-2"></i>
            <span>Analisis Group Menu (15 Group)</span>
        </button>
        <button type="button" class="analysis-subtab-btn" data-tab="tab-ranking" onclick="switchAnalysisTab('tab-ranking', this)">
            <i class="ti ti-list-numbers"></i>
            <span>Ranking Produk Lengkap</span>
        </button>
        <button type="button" class="analysis-subtab-btn" data-tab="tab-structure" onclick="switchAnalysisTab('tab-structure', this)">
            <i class="ti ti-code"></i>
            <span>Bedah Format Crystal Reports</span>
        </button>
        <button type="button" class="analysis-subtab-btn" data-tab="tab-upload" onclick="switchAnalysisTab('tab-upload', this)">
            <i class="ti ti-file-analytics"></i>
            <span>Upload & Analisis Dinamis</span>
        </button>
    </div>

    {{-- ===== 4. CHARTS GRID (TAMPIL DI TAB OVERVIEW) ===== --}}
    <div id="analysis-charts-container" style="display: grid; grid-template-columns: 1.6fr 1fr; gap: 20px; margin-bottom: 24px;">
        {{-- Chart 1: Monthly Sales Trend --}}
        <div class="glass-panel" style="padding: 22px 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 8px;">
                <div>
                    <h3 style="margin: 0 0 4px 0; font-size: 1.05rem; font-weight: 800; color: var(--text-main);">
                        Tren Penjualan Bulanan (Jan &ndash; Okt 2026)
                    </h3>
                    <p style="margin: 0; font-size: 0.78rem; color: var(--text-muted);">
                        Perbandingan Gross Revenue (Rupiah) dan Volume Penjualan (Qty Item) per Bulan
                    </p>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; font-size: 0.75rem;">
                    <span style="display: inline-flex; align-items: center; gap: 5px;">
                        <span style="width: 10px; height: 10px; border-radius: 2px; background: #D9222A;"></span> Omzet (Juta Rp)
                    </span>
                    <span style="display: inline-flex; align-items: center; gap: 5px;">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #F59E0B;"></span> Volume Qty
                    </span>
                </div>
            </div>
            <div style="position: relative; height: 280px; width: 100%;">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>

        {{-- Chart 2: Channel Distribution Donut --}}
        <div class="glass-panel" style="padding: 22px 24px;">
            <div style="margin-bottom: 16px;">
                <h3 style="margin: 0 0 4px 0; font-size: 1.05rem; font-weight: 800; color: var(--text-main);">
                    Komposisi Kanal Penjualan
                </h3>
                <p style="margin: 0; font-size: 0.78rem; color: var(--text-muted);">
                    Distribusi share omzet berdasarkan channel transaksi
                </p>
            </div>
            <div style="position: relative; height: 210px; width: 100%;">
                <canvas id="channelShareChart"></canvas>
            </div>
            <div id="channelLegendList" style="margin-top: 14px; display: grid; grid-template-columns: repeat(2, 1fr); gap: 6px; font-size: 0.75rem;">
                <!-- Filled via JS -->
            </div>
        </div>
    </div>

    {{-- Secondary Charts Row (On-Premise vs Off-Premise & Group Contribution) --}}
    <div id="analysis-secondary-charts" style="display: grid; grid-template-columns: 1fr 1.3fr; gap: 20px; margin-bottom: 24px;">
        {{-- Chart 3: On-Premise vs Off-Premise --}}
        <div class="glass-panel" style="padding: 22px 24px;">
            <div style="margin-bottom: 16px;">
                <h3 style="margin: 0 0 4px 0; font-size: 1.05rem; font-weight: 800; color: var(--text-main);">
                    Dine In vs Off-Premise (Bawa Pulang & Online)
                </h3>
                <p style="margin: 0; font-size: 0.78rem; color: var(--text-muted);">
                    Rasio konsumsi makan di tempat vs pesanan luar toko
                </p>
            </div>
            <div style="position: relative; height: 250px; width: 100%;">
                <canvas id="premiseChart"></canvas>
            </div>
            <div style="margin-top: 10px; padding: 10px; border-radius: 8px; background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.2); font-size: 0.75rem; color: var(--text-body);">
                <i class="ti ti-info-circle" style="color: #F59E0B;"></i> <strong>Insight Utama:</strong> 66,5% transaksi di Store Plaju merupakan <strong>Off-Premise</strong> (Online Food, Take Away, Delivery & DT), menunjukkan kuatnya ketergantungan pada kecepatan packaging dan kitchen dispatch.
            </div>
        </div>

        {{-- Chart 4: Group Revenue Bar Chart --}}
        <div class="glass-panel" style="padding: 22px 24px;">
            <div style="margin-bottom: 16px;">
                <h3 style="margin: 0 0 4px 0; font-size: 1.05rem; font-weight: 800; color: var(--text-main);">
                    Kontribusi Omzet per Group Menu
                </h3>
                <p style="margin: 0; font-size: 0.78rem; color: var(--text-muted);">
                    Ranking kategori penyumbang revenue terbesar (10 Bulan)
                </p>
            </div>
            <div style="position: relative; height: 280px; width: 100%;">
                <canvas id="groupRevenueChart"></canvas>
            </div>
        </div>
    </div>


    {{-- ===== 5. TAB 1: IKHTISAR & TREN BULANAN ===== --}}
    <div id="tab-overview" class="analysis-tab-content">
        <div class="glass-panel" style="padding: 22px 24px; margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
                <div>
                    <h3 style="margin: 0 0 4px 0; font-size: 1.1rem; font-weight: 800; color: var(--text-main);">
                        Rekapitulasi Kinerja Bulanan (Januari &ndash; Oktober 2026)
                    </h3>
                    <p style="margin: 0; font-size: 0.8rem; color: var(--text-muted);">
                        Evaluasi MoM (Month-over-Month Growth), rata-rata harga per item, dan performa kanal per bulan
                    </p>
                </div>
                <div style="display: flex; gap: 8px;">
                    <span style="padding: 5px 10px; border-radius: 8px; background: rgba(16,185,129,0.12); color: #10b981; font-size: 0.75rem; font-weight: 700;">
                        <i class="ti ti-arrow-up-right"></i> Puncak: Maret (Rp 607,1 Jt)
                    </span>
                    <span style="padding: 5px 10px; border-radius: 8px; background: rgba(239,68,68,0.12); color: #ef4444; font-size: 0.75rem; font-weight: 700;">
                        <i class="ti ti-arrow-down-right"></i> Terendah: Februari (Rp 423,9 Jt)
                    </span>
                </div>
            </div>

            <div class="table-responsive" style="overflow-x: auto;">
                <table class="styled-table" id="monthly-summary-table" style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 0.82rem;">
                    <thead>
                        <tr style="background: var(--table-header-bg);">
                            <th style="padding: 12px 14px; text-align: left;">Bulan</th>
                            <th style="padding: 12px 14px; text-align: right;">Total Qty</th>
                            <th style="padding: 12px 14px; text-align: right;">Gross Omzet (Rp)</th>
                            <th style="padding: 12px 14px; text-align: right;">Growth MoM</th>
                            <th style="padding: 12px 14px; text-align: right;">Dine In (Rp)</th>
                            <th style="padding: 12px 14px; text-align: right;">Take Away (Rp)</th>
                            <th style="padding: 12px 14px; text-align: right;">Online Food (Rp)</th>
                            <th style="padding: 12px 14px; text-align: right;">Delivery & DT</th>
                            <th style="padding: 12px 14px; text-align: right;">Rata-rata/Item</th>
                            <th style="padding: 12px 14px; text-align: center;">Status</th>
                        </tr>
                    </thead>
                    <tbody id="monthly-summary-tbody">
                        <!-- Populated by JS -->
                    </tbody>
                    <tfoot id="monthly-summary-tfoot" style="background: var(--table-header-bg); font-weight: 800; border-top: 2px solid var(--border-color);">
                        <!-- Populated by JS -->
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Monthly Strategic Insights Box --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 16px;">
            <div class="glass-panel" style="padding: 20px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(245,158,11,0.15); display: flex; align-items: center; justify-content: center; color: #F59E0B; font-size: 1.2rem;">
                        <i class="ti ti-flame"></i>
                    </div>
                    <h4 style="margin: 0; font-size: 0.95rem; font-weight: 800; color: var(--text-main);">Momentum Musiman & Fenomena Ramadhan</h4>
                </div>
                <p style="font-size: 0.8rem; color: var(--text-body); line-height: 1.5; margin: 0 0 10px 0;">
                    Bulan <strong>Maret 2026</strong> mencatatkan lonjakan omzet fantastis sebesar <strong>+43,2% MoM</strong> ke angka <strong>Rp 607,15 Juta</strong>. Pemicu utamanya adalah bulan puasa Ramadhan, di mana menu spesial seperti <em>Bento Ramadan 1, 2, 3, 4</em> dan paket buka bersama mendongkrak penjualan Dine In (+86%) dan Take Away (+48%).
                </p>
                <div style="padding: 8px 12px; background: rgba(16,185,129,0.08); border-radius: 8px; font-size: 0.74rem; color: #10b981; font-weight: 600;">
                    <i class="ti ti-check"></i> Rekomendasi: Siapkan forecasting stok Bento Ramadan 1 bulan sebelum H-Ramadhan 2027.
                </div>
            </div>

            <div class="glass-panel" style="padding: 20px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(59,130,246,0.15); display: flex; align-items: center; justify-content: center; color: #3B82F6; font-size: 1.2rem;">
                        <i class="ti ti-devices"></i>
                    </div>
                    <h4 style="margin: 0; font-size: 0.95rem; font-weight: 800; color: var(--text-main);">Kekuatan Dominan Online Delivery</h4>
                </div>
                <p style="font-size: 0.8rem; color: var(--text-body); line-height: 1.5; margin: 0 0 10px 0;">
                    Kanal Online Aggregator (GoFood Rp 721 Jt, ShopeeFood Rp 493 Jt, GrabFood Rp 421 Jt) berkontribusi konsisten di atas <strong>Rp 150 &ndash; 180 Juta per bulan</strong>. GoFood mendominasi 44% dari total pesanan online, disusul ShopeeFood 30%, dan GrabFood 26%.
                </p>
                <div style="padding: 8px 12px; background: rgba(59,130,246,0.08); border-radius: 8px; font-size: 0.74rem; color: #3B82F6; font-weight: 600;">
                    <i class="ti ti-bulb"></i> Rekomendasi: Pertahankan promo bundling eksklusif GoFood & ShopeeFood untuk menjaga basket size.
                </div>
            </div>

            <div class="glass-panel" style="padding: 20px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(217,34,42,0.15); display: flex; align-items: center; justify-content: center; color: var(--hokben-red); font-size: 1.2rem;">
                        <i class="ti ti-box"></i>
                    </div>
                    <h4 style="margin: 0; font-size: 0.95rem; font-weight: 800; color: var(--text-main);">Konsistensi Menu Paket sebagai Core Driver</h4>
                </div>
                <p style="font-size: 0.8rem; color: var(--text-body); line-height: 1.5; margin: 0 0 10px 0;">
                    Kategori <strong>002 Menu Paket</strong> konsisten menjadi tulang punggung omzet outlet sebesar <strong>61,85%</strong> (Rp 2,97 Miliar). Menu seperti <em>New Bento Special 4</em>, <em>Paket C</em>, dan <em>Simple Set Teriyaki 1 & 2</em> memiliki brand loyalty yang sangat tinggi.
                </p>
                <div style="padding: 8px 12px; background: rgba(217,34,42,0.08); border-radius: 8px; font-size: 0.74rem; color: var(--hokben-red); font-weight: 600;">
                    <i class="ti ti-star"></i> Rekomendasi: Jaga kualitas salad & porsi beef/chicken pada New Bento Special 4 agar tidak terjadi keluhan rasa.
                </div>
            </div>
        </div>
    </div>


    {{-- ===== 6. TAB 2: ANALISIS KANAL PENJUALAN ===== --}}
    <div id="tab-channels" class="analysis-tab-content" style="display: none;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; margin-bottom: 24px;">

            {{-- Dine In Card --}}
            <div class="glass-panel" style="padding: 20px; border-top: 4px solid #3B82F6;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="font-weight: 800; color: #3B82F6; font-size: 0.95rem;">Dine In (DI)</span>
                    <span style="padding: 3px 8px; border-radius: 6px; background: rgba(59,130,246,0.12); color: #3B82F6; font-size: 0.72rem; font-weight: 700;">On-Premise</span>
                </div>
                <p style="font-size: 1.5rem; font-weight: 800; margin: 0; color: var(--text-main);">Rp 1.608.221.423</p>
                <div style="display: flex; justify-content: space-between; font-size: 0.78rem; color: var(--text-muted); margin-top: 8px;">
                    <span>Volume: <strong>56.668 item</strong></span>
                    <span>Pangsa: <strong>33,50%</strong></span>
                </div>
                <div style="margin-top: 10px; font-size: 0.74rem; color: var(--text-muted);">
                    Rata-rata: Rp 28.380/item &bull; Peak: Maret (Rp 218 Jt)
                </div>
            </div>

            {{-- Take Away Card --}}
            <div class="glass-panel" style="padding: 20px; border-top: 4px solid #8B5CF6;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="font-weight: 800; color: #8B5CF6; font-size: 0.95rem;">Take Away (TA)</span>
                    <span style="padding: 3px 8px; border-radius: 6px; background: rgba(139,92,246,0.12); color: #8B5CF6; font-size: 0.72rem; font-weight: 700;">Bungkus</span>
                </div>
                <p style="font-size: 1.5rem; font-weight: 800; margin: 0; color: var(--text-main);">Rp 1.039.154.472</p>
                <div style="display: flex; justify-content: space-between; font-size: 0.78rem; color: var(--text-muted); margin-top: 8px;">
                    <span>Volume: <strong>32.430 item</strong></span>
                    <span>Pangsa: <strong>21,64%</strong></span>
                </div>
                <div style="margin-top: 10px; font-size: 0.74rem; color: var(--text-muted);">
                    Rata-rata: Rp 32.043/item &bull; Peak: Maret (Rp 129 Jt)
                </div>
            </div>

            {{-- GoFood Card --}}
            <div class="glass-panel" style="padding: 20px; border-top: 4px solid #00AA13;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="font-weight: 800; color: #00AA13; font-size: 0.95rem;">GoFood (GF)</span>
                    <span style="padding: 3px 8px; border-radius: 6px; background: rgba(0,170,19,0.12); color: #00AA13; font-size: 0.72rem; font-weight: 700;">Aggregator #1</span>
                </div>
                <p style="font-size: 1.5rem; font-weight: 800; margin: 0; color: var(--text-main);">Rp 721.381.621</p>
                <div style="display: flex; justify-content: space-between; font-size: 0.78rem; color: var(--text-muted); margin-top: 8px;">
                    <span>Volume: <strong>28.741 item</strong></span>
                    <span>Pangsa: <strong>15,03%</strong></span>
                </div>
                <div style="margin-top: 10px; font-size: 0.74rem; color: var(--text-muted);">
                    Rata-rata: Rp 25.099/item &bull; Dominasi 44,1% Online
                </div>
            </div>

            {{-- ShopeeFood Card --}}
            <div class="glass-panel" style="padding: 20px; border-top: 4px solid #EE4D2D;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="font-weight: 800; color: #EE4D2D; font-size: 0.95rem;">ShopeeFood (SF)</span>
                    <span style="padding: 3px 8px; border-radius: 6px; background: rgba(238,77,45,0.12); color: #EE4D2D; font-size: 0.72rem; font-weight: 700;">Aggregator #2</span>
                </div>
                <p style="font-size: 1.5rem; font-weight: 800; margin: 0; color: var(--text-main);">Rp 493.073.752</p>
                <div style="display: flex; justify-content: space-between; font-size: 0.78rem; color: var(--text-muted); margin-top: 8px;">
                    <span>Volume: <strong>20.986 item</strong></span>
                    <span>Pangsa: <strong>10,27%</strong></span>
                </div>
                <div style="margin-top: 10px; font-size: 0.74rem; color: var(--text-muted);">
                    Rata-rata: Rp 23.495/item &bull; Dominasi 30,1% Online
                </div>
            </div>

            {{-- GrabFood Card --}}
            <div class="glass-panel" style="padding: 20px; border-top: 4px solid #00B14F;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="font-weight: 800; color: #00B14F; font-size: 0.95rem;">GrabFood (GR)</span>
                    <span style="padding: 3px 8px; border-radius: 6px; background: rgba(0,177,79,0.12); color: #00B14F; font-size: 0.72rem; font-weight: 700;">Aggregator #3</span>
                </div>
                <p style="font-size: 1.5rem; font-weight: 800; margin: 0; color: var(--text-main);">Rp 420.984.638</p>
                <div style="display: flex; justify-content: space-between; font-size: 0.78rem; color: var(--text-muted); margin-top: 8px;">
                    <span>Volume: <strong>14.386 item</strong></span>
                    <span>Pangsa: <strong>8,77%</strong></span>
                </div>
                <div style="margin-top: 10px; font-size: 0.74rem; color: var(--text-muted);">
                    Rata-rata: Rp 29.263/item &bull; Dominasi 25,7% Online
                </div>
            </div>

            {{-- Delivery Langsung (DL) --}}
            <div class="glass-panel" style="padding: 20px; border-top: 4px solid #F59E0B;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="font-weight: 800; color: #F59E0B; font-size: 0.95rem;">Delivery Langsung (DL)</span>
                    <span style="padding: 3px 8px; border-radius: 6px; background: rgba(245,158,11,0.12); color: #F59E0B; font-size: 0.72rem; font-weight: 700;">HokBen Kurir</span>
                </div>
                <p style="font-size: 1.5rem; font-weight: 800; margin: 0; color: var(--text-main);">Rp 338.747.169</p>
                <div style="display: flex; justify-content: space-between; font-size: 0.78rem; color: var(--text-muted); margin-top: 8px;">
                    <span>Volume: <strong>6.961 item</strong></span>
                    <span>Pangsa: <strong>7,06%</strong></span>
                </div>
                <div style="margin-top: 10px; font-size: 0.74rem; color: var(--text-muted);">
                    Rata-rata: Rp 48.663/item &bull; Pesanan bernilai besar
                </div>
            </div>

            {{-- Drive Thru (DT) --}}
            <div class="glass-panel" style="padding: 20px; border-top: 4px solid #06B6D4;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="font-weight: 800; color: #06B6D4; font-size: 0.95rem;">Drive Thru (DT)</span>
                    <span style="padding: 3px 8px; border-radius: 6px; background: rgba(6,182,212,0.12); color: #06B6D4; font-size: 0.72rem; font-weight: 700;">Jalur Kendaraan</span>
                </div>
                <p style="font-size: 1.5rem; font-weight: 800; margin: 0; color: var(--text-main);">Rp 148.776.865</p>
                <div style="display: flex; justify-content: space-between; font-size: 0.78rem; color: var(--text-muted); margin-top: 8px;">
                    <span>Volume: <strong>5.894 item</strong></span>
                    <span>Pangsa: <strong>3,10%</strong></span>
                </div>
                <div style="margin-top: 10px; font-size: 0.74rem; color: var(--text-muted);">
                    Rata-rata: Rp 25.242/item &bull; Ruko Plaju Shophouse
                </div>
            </div>

            {{-- Birthday Party (BP) --}}
            <div class="glass-panel" style="padding: 20px; border-top: 4px solid #EC4899;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="font-weight: 800; color: #EC4899; font-size: 0.95rem;">Birthday Party (BP)</span>
                    <span style="padding: 3px 8px; border-radius: 6px; background: rgba(236,72,153,0.12); color: #EC4899; font-size: 0.72rem; font-weight: 700;">Event Ultah</span>
                </div>
                <p style="font-size: 1.5rem; font-weight: 800; margin: 0; color: var(--text-main);">Rp 30.708.292</p>
                <div style="display: flex; justify-content: space-between; font-size: 0.78rem; color: var(--text-muted); margin-top: 8px;">
                    <span>Volume: <strong>1.185 item</strong></span>
                    <span>Pangsa: <strong>0,64%</strong></span>
                </div>
                <div style="margin-top: 10px; font-size: 0.74rem; color: var(--text-muted);">
                    Paket Ureshi 1 & 2 &bull; Margin & brand loyalty
                </div>
            </div>

        </div>

        {{-- Channel Comparison Table --}}
        <div class="glass-panel" style="padding: 22px 24px;">
            <h3 style="margin: 0 0 16px 0; font-size: 1.1rem; font-weight: 800; color: var(--text-main);">
                Matriks Perbandingan Kanal Penjualan (10 Bulan)
            </h3>
            <div class="table-responsive" style="overflow-x: auto;">
                <table class="styled-table" style="width: 100%; font-size: 0.82rem;">
                    <thead>
                        <tr style="background: var(--table-header-bg);">
                            <th style="padding: 10px 14px; text-align: left;">Kanal Penjualan</th>
                            <th style="padding: 10px 14px; text-align: center;">Tipe</th>
                            <th style="padding: 10px 14px; text-align: right;">Total Qty</th>
                            <th style="padding: 10px 14px; text-align: right;">Share Qty %</th>
                            <th style="padding: 10px 14px; text-align: right;">Total Omzet (Rp)</th>
                            <th style="padding: 10px 14px; text-align: right;">Share Omzet %</th>
                            <th style="padding: 10px 14px; text-align: right;">Rata-rata Harga/Item</th>
                            <th style="padding: 10px 14px; text-align: left;">Karakteristik & Menu Dominan</th>
                        </tr>
                    </thead>
                    <tbody id="channel-comparison-tbody">
                        <!-- Filled by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    {{-- ===== 7. TAB 3: ANALISIS GROUP MENU ===== --}}
    <div id="tab-groups" class="analysis-tab-content" style="display: none;">
        <div class="glass-panel" style="padding: 22px 24px; margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
                <div>
                    <h3 style="margin: 0 0 4px 0; font-size: 1.1rem; font-weight: 800; color: var(--text-main);">
                        Struktur & Kontribusi 15 Group Menu HokBen (10 Bulan)
                    </h3>
                    <p style="margin: 0; font-size: 0.8rem; color: var(--text-muted);">
                        Hierarki kode group produk (Group 001 s.d. 045) dan kontribusinya terhadap omzet toko
                    </p>
                </div>
            </div>

            <div class="table-responsive" style="overflow-x: auto;">
                <table class="styled-table" style="width: 100%; font-size: 0.82rem;">
                    <thead>
                        <tr style="background: var(--table-header-bg);">
                            <th style="padding: 10px 12px; text-align: center; width: 60px;">Kode</th>
                            <th style="padding: 10px 12px; text-align: left;">Nama Group Menu</th>
                            <th style="padding: 10px 12px; text-align: right;">Total Qty</th>
                            <th style="padding: 10px 12px; text-align: right;">Total Omzet (Rp)</th>
                            <th style="padding: 10px 12px; text-align: right;">Pangsa %</th>
                            <th style="padding: 10px 12px; text-align: right;">Rata-rata/Item</th>
                            <th style="padding: 10px 12px; text-align: left;">Menu Terlaris (Hero SKU)</th>
                            <th style="padding: 10px 12px; text-align: left;">Peran Strategis Bisnis</th>
                        </tr>
                    </thead>
                    <tbody id="group-analysis-tbody">
                        <!-- Filled by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    {{-- ===== 8. TAB 4: RANKING PRODUK LENGKAP ===== --}}
    <div id="tab-ranking" class="analysis-tab-content" style="display: none;">
        <div class="glass-panel" style="padding: 22px 24px;">
            {{-- Toolbar Filter --}}
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 12px;">
                <div>
                    <h3 style="margin: 0 0 4px 0; font-size: 1.1rem; font-weight: 800; color: var(--text-main);">
                        Master Matriks Ranking Produk & SKU
                    </h3>
                    <p style="margin: 0; font-size: 0.78rem; color: var(--text-muted);">
                        Data lengkap 574 SKU produk terjual beserta sebaran kanal dan status penjualan
                    </p>
                </div>

                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    {{-- Search Input --}}
                    <div style="position: relative; width: 220px;">
                        <i class="ti ti-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                        <input type="text" id="productSearchInput" placeholder="Cari Menu / PLU..." class="styled-input" style="padding-left: 32px; font-size: 0.82rem; height: 38px; width: 100%; border-radius: 8px;" onkeyup="filterProductTable()">
                    </div>

                    {{-- Group Filter --}}
                    <select id="productGroupFilter" class="styled-select" style="font-size: 0.82rem; padding: 8px 12px; height: 38px; border-radius: 8px;" onchange="filterProductTable()">
                        <option value="ALL">Semua Group Menu</option>
                        <option value="001">001 Menu Pokok</option>
                        <option value="002">002 Menu Paket</option>
                        <option value="003">003 Menu Snack</option>
                        <option value="004">004 Menu Soup</option>
                        <option value="005">005 Menu Pudding / Desert</option>
                        <option value="006">006 Menu Soft Drink</option>
                        <option value="007">007 Juice</option>
                        <option value="008">008 Nasi + Salad</option>
                        <option value="010">010 Promosi</option>
                        <option value="011">011 Kemasan</option>
                        <option value="012">012 Paket Promosi</option>
                        <option value="013">013 Paket Khusus</option>
                        <option value="014">014 LAIN-LAIN</option>
                        <option value="016">016 ULTAH</option>
                        <option value="017">017 MENU HEMAT</option>
                    </select>

                    {{-- Rows Limit --}}
                    <select id="productRowLimit" class="styled-select" style="font-size: 0.82rem; padding: 8px 10px; height: 38px; border-radius: 8px;" onchange="filterProductTable()">
                        <option value="20">Tampilkan 20</option>
                        <option value="50" selected>Tampilkan 50</option>
                        <option value="100">Tampilkan 100</option>
                        <option value="ALL">Tampilkan Semua</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive" style="overflow-x: auto; max-height: 600px;">
                <table class="styled-table" id="product-ranking-table" style="width: 100%; font-size: 0.82rem;">
                    <thead style="position: sticky; top: 0; z-index: 2; background: var(--table-header-bg);">
                        <tr>
                            <th style="padding: 10px 10px; text-align: center; width: 50px;">Rank</th>
                            <th style="padding: 10px 12px; text-align: left; width: 85px;">PLU</th>
                            <th style="padding: 10px 12px; text-align: left;">Nama Menu</th>
                            <th style="padding: 10px 12px; text-align: left;">Kategori</th>
                            <th style="padding: 10px 12px; text-align: right;">Est. Harga</th>
                            <th style="padding: 10px 12px; text-align: right;">Dine In</th>
                            <th style="padding: 10px 12px; text-align: right;">Take Away</th>
                            <th style="padding: 10px 12px; text-align: right;">Online Food</th>
                            <th style="padding: 10px 12px; text-align: right;">Kanal Lain</th>
                            <th style="padding: 10px 12px; text-align: right;">Total Qty</th>
                            <th style="padding: 10px 12px; text-align: right;">Total Omzet (Rp)</th>
                            <th style="padding: 10px 12px; text-align: right;">Pangsa %</th>
                            <th style="padding: 10px 12px; text-align: center;">Klasifikasi</th>
                        </tr>
                    </thead>
                    <tbody id="product-ranking-tbody">
                        <!-- Filled by JS -->
                    </tbody>
                </table>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 14px; font-size: 0.78rem; color: var(--text-muted);">
                <span id="productTableCountInfo">Menampilkan 50 dari 574 item</span>
                <span>Diurutkan berdasarkan total omzet penjualan tertinggi</span>
            </div>
        </div>
    </div>


    {{-- ===== 9. TAB 5: BEDAH FORMAT DATA EXCEL (TECHNICAL DOCUMENTATION) ===== --}}
    <div id="tab-structure" class="analysis-tab-content" style="display: none;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
            
            {{-- Format Explanation --}}
            <div class="glass-panel" style="padding: 22px 24px;">
                <h3 style="margin: 0 0 12px 0; font-size: 1.1rem; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                    <i class="ti ti-file-code" style="color: var(--hokben-red);"></i>
                    Format File Asal: Crystal Reports Export
                </h3>
                <p style="font-size: 0.82rem; color: var(--text-body); line-height: 1.6; margin: 0 0 14px 0;">
                    File ini merupakan hasil export otomatis dari sistem reporting enterprise HokBen (PT. Eka Bogainti) berbasis <strong>SAP Crystal Reports</strong> dengan template file:
                    <br><code style="font-size: 0.76rem; background: var(--bg-subtle); padding: 2px 6px; border-radius: 4px; color: var(--hokben-red); font-weight: 600;">C:\WINDOWS\TEMP\FrmRptSales_Group {GUID}.rpt</code>.
                </p>

                <div style="background: var(--bg-subtle); border-radius: 12px; padding: 14px 16px; margin-bottom: 14px;">
                    <h5 style="margin: 0 0 8px 0; font-size: 0.82rem; font-weight: 700; color: var(--text-main);">Karakteristik & Ciri Khusus File:</h5>
                    <ul style="margin: 0; padding-left: 18px; font-size: 0.78rem; color: var(--text-body); line-height: 1.6;">
                        <li><strong>Multi-Row Header:</strong> Informasi PT, tanggal cetak, nama laporan, nama store, dan nomor POS berada di baris 1 s.d. 10.</li>
                        <li><strong>Hierarki Per Group:</strong> Setiap kelompok menu didahului baris kode 3 digit (misal: <code>001 Menu Pokok</code>, <code>002 Menu Paket</code>) tanpa PLU.</li>
                        <li><strong>Baris Subtotal Otomatis:</strong> Di akhir tiap group terdapat baris subtotal tanpa teks keterangan (hanya angka rekapitulasi).</li>
                        <li><strong>Dua Baris Kolom Header:</strong> Baris atas berisi kode kanal (DI, TA, DL, EX, BP, DT, CT, GF, GR, SF, TE), baris bawah mendefinisikan pasangan <code>Qty, Rupiah</code>.</li>
                    </ul>
                </div>

                <div style="padding: 12px; background: rgba(16,185,129,0.08); border-left: 3px solid #10b981; border-radius: 6px; font-size: 0.78rem; color: var(--text-body);">
                    <strong>Formula Rekonsiliasi Total:</strong> Total Qty = DI + TA + DL + EX + BP + DT + CT + GF + GR + SF + TE. Seluruh angka dihitung dalam nilai sebelum pajak/service (Net of Tax di POS).
                </div>
            </div>

            {{-- Column Codes Dictionary --}}
            <div class="glass-panel" style="padding: 22px 24px;">
                <h3 style="margin: 0 0 12px 0; font-size: 1.1rem; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                    <i class="ti ti-vocabulary" style="color: #F59E0B;"></i>
                    Kamus Singkatan Kanal Penjualan (Columns)
                </h3>
                <div class="table-responsive">
                    <table class="styled-table" style="width: 100%; font-size: 0.78rem;">
                        <thead>
                            <tr style="background: var(--table-header-bg);">
                                <th style="padding: 6px 10px; width: 50px;">Kode</th>
                                <th style="padding: 6px 10px;">Kepanjangan</th>
                                <th style="padding: 6px 10px;">Keterangan / Deskripsi Operasional</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td><strong style="color: #3B82F6;">D I</strong></td><td>Dine In</td><td>Makan di tempat (Layanan meja / store tray)</td></tr>
                            <tr><td><strong style="color: #8B5CF6;">T A</strong></td><td>Take Away</td><td>Bungkus bawa pulang oleh customer langsung di kasir</td></tr>
                            <tr><td><strong style="color: #F59E0B;">D L</strong></td><td>Delivery</td><td>HokBen Delivery langsung (Call Center 1-500-505 / App HokBen)</td></tr>
                            <tr><td><strong>EX</strong></td><td>Express</td><td>Layanan pick up cepat / gerai satelit express</td></tr>
                            <tr><td><strong style="color: #EC4899;">B P</strong></td><td>Birthday Party</td><td>Paket acara ulang tahun & pemesanan massal store</td></tr>
                            <tr><td><strong style="color: #06B6D4;">D T</strong></td><td>Drive Thru</td><td>Layanan pemesanan lewat jendela kendaraan (khusus store Ruko/Standalone)</td></tr>
                            <tr><td><strong>C T</strong></td><td>Catering</td><td>Pemesanan katering skala besar instansi/pabrik</td></tr>
                            <tr><td><strong style="color: #00AA13;">GF</strong></td><td>GoFood</td><td>Pesanan online via armada Gojek GoFood</td></tr>
                            <tr><td><strong style="color: #00B14F;">GR</strong></td><td>GrabFood</td><td>Pesanan online via armada Grab GrabFood</td></tr>
                            <tr><td><strong style="color: #EE4D2D;">SF</strong></td><td>ShopeeFood</td><td>Pesanan online via armada ShopeeFood</td></tr>
                            <tr><td><strong>TE</strong></td><td>Traveloka Eats</td><td>Pesanan online pihak ketiga lainnya</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- Area 19 Store List Reference --}}
        <div class="glass-panel" style="padding: 22px 24px;">
            <h3 style="margin: 0 0 12px 0; font-size: 1.1rem; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                <i class="ti ti-building-store" style="color: var(--hokben-red);"></i>
                Daftar Store HokBen Terkait di AREA 19 (SUMBAGSEL)
            </h3>
            <p style="font-size: 0.8rem; color: var(--text-muted); margin: 0 0 14px 0;">
                Data dari sheet <code>REKAP STORE</code>: Area 19 menaungi 13 gerai aktif di wilayah Sumatera Bagian Selatan (Lampung, Palembang, Bengkulu, Lahat, Baturaja, Pangkal Pinang).
            </p>
            <div class="table-responsive">
                <table class="styled-table" style="width: 100%; font-size: 0.8rem;">
                    <thead>
                        <tr style="background: var(--table-header-bg);">
                            <th style="padding: 8px 12px; text-align: center; width: 45px;">No</th>
                            <th style="padding: 8px 12px; text-align: center; width: 80px;">Kode Store</th>
                            <th style="padding: 8px 12px; text-align: left;">Nama Store</th>
                            <th style="padding: 8px 12px; text-align: left;">Tipe Outlet</th>
                            <th style="padding: 8px 12px; text-align: left;">Wilayah Area</th>
                            <th style="padding: 8px 12px; text-align: center;">Status Analisa Ini</th>
                        </tr>
                    </thead>
                    <tbody id="area19-store-list-tbody">
                        <!-- Populated by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    {{-- ===== 10. TAB 6: UPLOAD & ANALISIS DINAMIS ===== --}}
    <div id="tab-upload" class="analysis-tab-content" style="display: none;">
        <div class="glass-panel" style="padding: 26px 30px; margin-bottom: 24px; text-align: center;">
            <div style="width: 64px; height: 64px; border-radius: 18px; background: rgba(217,34,42,0.12); color: var(--hokben-red); display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; margin-bottom: 14px;">
                <i class="ti ti-file-upload"></i>
            </div>
            <h3 style="margin: 0 0 6px 0; font-size: 1.25rem; font-weight: 800; color: var(--text-main);">
                Upload & Analisis Laporan Penjualan Per Group Baru
            </h3>
            <p style="margin: 0 auto 20px auto; max-width: 600px; font-size: 0.84rem; color: var(--text-muted); line-height: 1.5;">
                Punya file Excel (<code>.xlsx</code> / <code>.xls</code> / <code>.csv</code>) baru dari Crystal Reports untuk bulan atau store lain? Upload di sini untuk menganalisa metrik, visualisasi grafik, dan ranking produknya secara instan!
            </p>

            <div style="border: 2px dashed rgba(217,34,42,0.3); border-radius: 16px; padding: 36px 20px; background: var(--bg-subtle); cursor: pointer; transition: all 0.2s; max-width: 680px; margin: 0 auto;" onclick="document.getElementById('customSalesFileInput').click()" ondragover="event.preventDefault(); this.style.borderColor='var(--hokben-red)';" ondragleave="this.style.borderColor='rgba(217,34,42,0.3)';" ondrop="handleCustomSalesDrop(event)">
                <i class="ti ti-cloud-upload" style="font-size: 2.8rem; color: var(--hokben-yellow-dark); margin-bottom: 8px; display: block;"></i>
                <h4 style="margin: 0 0 4px 0; font-size: 1rem; font-weight: 700; color: var(--text-main);">Tarik & Lepaskan File Excel Laporan Penjualan di Sini</h4>
                <p style="margin: 0; font-size: 0.78rem; color: var(--text-muted);">atau klik untuk memilih file dari komputer Anda (Format didukung: .xlsx, .xls, .csv)</p>
                <input type="file" id="customSalesFileInput" accept=".xlsx, .xls, .csv" style="display: none;" onchange="handleCustomSalesUpload(event)">
            </div>

            <div id="uploadStatusMsg" style="margin-top: 16px; font-size: 0.82rem; font-weight: 600; display: none;"></div>
        </div>
    </div>

</section>

