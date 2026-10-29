<!-- Dashboard View (Leader Metrics & Status) -->
<section id="view-dashboard" class="view-section active">

    <!-- Store Operations Overview Banner -->
    <div class="store-overview-banner glass-panel" style="margin-bottom: 24px; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; background: linear-gradient(135deg, rgba(245, 158, 11, 0.08) 0%, rgba(217, 34, 42, 0.04) 100%); border-left: 4px solid var(--hokben-yellow-dark);">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; background: linear-gradient(135deg, #F59E0B 0%, #D9222A 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.4rem; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3); flex-shrink: 0;">
                <i class="ti ti-building-store"></i>
            </div>
            <div>
                <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--text-main); margin: 0 0 2px 0;">Pusat Komando Operasional Gerai Area 19</h3>
                <p style="font-size: 0.82rem; color: var(--text-muted); margin: 0;">Monitoring checklist shift harian, kepatuhan SOP store, dan verifikasi dokumen leader secara real-time.</p>
            </div>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.78rem; font-weight: 700; color: #10b981;">
                <span class="pulsing-dot-green"></span> Sistem Online
            </span>
            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.78rem; font-weight: 700; color: var(--text-body);">
                <i class="ti ti-clock" style="color: var(--hokben-yellow-dark);"></i> Shift Middle & Closing
            </span>
            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.78rem; font-weight: 700; color: var(--hokben-red);">
                <i class="ti ti-shield-check"></i> SOP 100% Halal
            </span>
        </div>
    </div>

    <!-- Metrics Grid -->
    <div class="metrics-grid">
        <div class="metric-card glass-panel">
            <div class="metric-header">
                <h3>Kepatuhan Harian</h3>
                <div class="icon-box blue"><i class="ti ti-checkup-list"></i></div>
            </div>
            <div class="metric-value">{{ $kpi['compliancePercent'] }}%</div>
            <div class="metric-progress">
                <div class="progress-bar" style="width: {{ $kpi['compliancePercent'] }}%"></div>
            </div>
            <p class="metric-desc">{{ $kpi['submittedCount'] }} dari {{ $kpi['totalModulesCount'] }} Modul SOP Terkumpul</p>
        </div>
        
        <div class="metric-card glass-panel">
            <div class="metric-header">
                <h3>Form Menunggu Review</h3>
                <div class="icon-box orange"><i class="ti ti-file-import"></i></div>
            </div>
            <div class="metric-value">{{ $kpi['pendingCount'] }}</div>
            <div class="metric-progress">
                <div class="progress-bar" style="width: {{ $kpi['pendingCount'] > 0 ? '60' : '0' }}%; background: linear-gradient(90deg, #F59E0B 0%, #D97706 100%);"></div>
            </div>
            <p class="metric-desc">Membutuhkan verifikasi berkas Leader</p>
        </div>

        <div class="metric-card glass-panel">
            <div class="metric-header">
                <h3>Outlet Belum Closing</h3>
                <div class="icon-box red"><i class="ti ti-alert-circle"></i></div>
            </div>
            <div class="metric-value">{{ $kpi['notClosingCount'] }}</div>
            <div class="metric-progress">
                <div class="progress-bar" style="width: {{ $kpi['notClosingCount'] > 0 ? '50' : '0' }}%; background: linear-gradient(90deg, #EF4444 0%, #B91C1C 100%);"></div>
            </div>
            <p class="metric-desc">Shift Closing sedang berjalan hari ini</p>
        </div>
    </div>

    <!-- Analytics Charts Section (Chart.js) -->
    <div class="charts-grid" style="display: grid; grid-template-columns: 1.6fr 1fr; gap: 20px; margin-bottom: 24px;">
        <!-- Chart 1: Tren Kepatuhan SOP & Dokumen (7 Hari Terakhir) -->
        <div class="glass-panel" style="padding: 22px 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
                <div>
                    <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="ti ti-chart-line" style="color: var(--hokben-red); font-size: 1.25rem;"></i> Tren Kepatuhan SOP Gerai (7 Hari Terakhir)
                    </h3>
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin: 4px 0 0 0;">Evaluasi persentase kepatuhan SOP harian & dokumen form terverifikasi.</p>
                </div>
                <div style="display: flex; align-items: center; gap: 12px; font-size: 0.78rem;">
                    <span style="display: inline-flex; align-items: center; gap: 6px; font-weight: 700; color: var(--hokben-red);">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: var(--hokben-red);"></span> % Kepatuhan
                    </span>
                    <span style="display: inline-flex; align-items: center; gap: 6px; font-weight: 700; color: var(--hokben-yellow-dark);">
                        <span style="width: 10px; height: 10px; border-radius: 2px; background: var(--hokben-yellow-dark);"></span> Form Masuk
                    </span>
                </div>
            </div>
            <div style="position: relative; height: 260px; width: 100%;">
                <canvas id="complianceChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Performa Sales Harian & Target -->
        <div class="glass-panel" style="padding: 22px 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
                <div>
                    <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="ti ti-chart-bar" style="color: var(--hokben-yellow-dark); font-size: 1.25rem;"></i> Target vs Aktual Sales (Juta Rp)
                    </h3>
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin: 4px 0 0 0;">Monitoring pencapaian omzet harian Area 19.</p>
                </div>
                <span class="badge" style="background: rgba(16, 185, 129, 0.12); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.25); padding: 4px 10px; border-radius: 8px; font-size: 0.76rem; font-weight: 700;">
                    Target: Rp 50 Jt/hr
                </span>
            </div>
            <div style="position: relative; height: 260px; width: 100%;">
                <canvas id="salesTrendsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <div class="glass-panel main-panel">
            <div class="panel-header">
                <h2>Status Upload Hari Ini</h2>
                <div class="status-filters">
                    <button class="filter-btn active" onclick="filterStatusTable('all', this)">Semua</button>
                    <button class="filter-btn" onclick="filterStatusTable('review', this)">Menunggu Review</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama Staf</th>
                            <th>Divisi</th>
                            <th>Modul Form</th>
                            <th>Status Form</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="dashboard-table-body">
                        @forelse($todaySubmissions as $sub)
                        <tr class="submission-row" data-status="{{ $sub->status === 'Menunggu Review' ? 'review' : 'done' }}">
                            <td style="font-weight: 700; color: var(--text-main);">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--bg-subtle); display: flex; align-items: center; justify-content: center; font-size: 0.8rem; color: var(--text-muted);">
                                        <i class="ti ti-user"></i>
                                    </div>
                                    <span>{{ $sub->user->name ?? 'Staff' }}</span>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight: 600; font-size: 0.84rem; color: var(--text-body);">
                                    {{ $sub->user->division ?? ($sub->module->category ?? 'Umum') }}
                                </span>
                            </td>
                            <td>
                                <span style="font-weight: 500;">{{ $sub->module->name ?? '-' }}</span>
                            </td>
                            <td>
                                @php
                                    $badgeClass = $sub->status === 'Lengkap' ? 'success' : ($sub->status === 'Perlu Perbaikan' ? 'danger' : 'warning');
                                @endphp
                                <span class="badge-status {{ $badgeClass }}">
                                    @if($sub->status === 'Lengkap')
                                        <i class="ti ti-circle-check"></i>
                                    @elseif($sub->status === 'Menunggu Review')
                                        <i class="ti ti-clock"></i>
                                    @else
                                        <i class="ti ti-alert-triangle"></i>
                                    @endif
                                    {{ $sub->status }}
                                </span>
                            </td>
                            <td>
                                <button class="btn-primary" onclick="openVerifyModalWithData({{ $sub->id }}, '{{ addslashes($sub->module->name ?? 'Form Excel') }}', '{{ addslashes($sub->user->name ?? 'Staff') }}', '{{ route('submissions.download', $sub) }}', '{{ addslashes($sub->notes ?? '') }}')">
                                    <i class="ti ti-eye"></i> Review
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 32px 20px;">
                                <i class="ti ti-inbox" style="font-size: 2.2rem; display: block; margin-bottom: 8px; opacity: 0.5;"></i>
                                <p style="font-weight: 600; margin: 0;">Belum ada pengajuan form Excel hari ini.</p>
                                <p style="font-size: 0.82rem; margin-top: 4px; opacity: 0.8;">Staff dapat mengunggah form melalui menu Portal Staff.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="side-panel">
            <div class="glass-panel">
                <h2>Menunggu Verifikasi</h2>
                <div class="verification-list" id="verification-list">
                    @forelse($pendingReviews as $pending)
                    <div class="verify-item">
                        <div class="v-info">
                            <h4>{{ $pending->module->name ?? 'Form Excel' }}</h4>
                            <p><i class="ti ti-user" style="font-size: 0.85rem;"></i> {{ $pending->user->name ?? 'Staff' }} • {{ $pending->created_at ? $pending->created_at->diffForHumans() : 'Hari ini' }}</p>
                        </div>
                        <button class="btn-primary" onclick="openVerifyModalWithData({{ $pending->id }}, '{{ addslashes($pending->module->name ?? 'Form Excel') }}', '{{ addslashes($pending->user->name ?? 'Staff') }}', '{{ route('submissions.download', $pending) }}', '{{ addslashes($pending->notes ?? '') }}')">
                            <i class="ti ti-check"></i> Cek
                        </button>
                    </div>
                    @empty
                    <div style="text-align: center; padding: 32px 20px; color: var(--text-muted);">
                        <i class="ti ti-circle-check" style="font-size: 2.2rem; color: #10b981; display: block; margin-bottom: 8px;"></i>
                        <p style="font-weight: 700; color: var(--text-main); margin: 0;">Semua Dokumen Terverifikasi!</p>
                        <p style="font-size: 0.82rem; margin-top: 4px;">Tidak ada dokumen yang pending verifikasi Leader.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
