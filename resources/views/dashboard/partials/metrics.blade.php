<!-- Dashboard View (Leader Metrics & Status) -->
<section id="view-dashboard" class="view-section active">
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
            <p class="metric-desc">{{ $kpi['submittedCount'] }} dari {{ $kpi['totalModulesCount'] }} Modul Terkumpul</p>
        </div>
        
        <div class="metric-card glass-panel">
            <div class="metric-header">
                <h3>Form Menunggu Review</h3>
                <div class="icon-box orange"><i class="ti ti-file-import"></i></div>
            </div>
            <div class="metric-value">{{ $kpi['pendingCount'] }}</div>
            <p class="metric-desc">Membutuhkan verifikasi Leader</p>
        </div>

        <div class="metric-card glass-panel">
            <div class="metric-header">
                <h3>Outlet Belum Closing</h3>
                <div class="icon-box red"><i class="ti ti-alert-circle"></i></div>
            </div>
            <div class="metric-value">{{ $kpi['notClosingCount'] }}</div>
            <p class="metric-desc">Shift Closing hari ini</p>
        </div>
    </div>

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
                            <td style="font-weight: 500;">{{ $sub->user->name ?? 'Staff' }}</td>
                            <td>{{ $sub->user->division ?? ($sub->module->category ?? 'Umum') }}</td>
                            <td>{{ $sub->module->name ?? '-' }}</td>
                            <td>
                                @php
                                    $badgeClass = $sub->status === 'Lengkap' ? 'success' : ($sub->status === 'Perlu Perbaikan' ? 'danger' : 'warning');
                                @endphp
                                <span class="badge-status {{ $badgeClass }}">{{ $sub->status }}</span>
                            </td>
                            <td>
                                <button class="btn-primary" onclick="openVerifyModalWithData({{ $sub->id }}, '{{ addslashes($sub->module->name ?? 'Form Excel') }}', '{{ addslashes($sub->user->name ?? 'Staff') }}', '{{ route('submissions.download', $sub) }}', '{{ addslashes($sub->notes ?? '') }}')">
                                    <i class="ti ti-eye"></i> Review
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #94a3b8; padding: 25px;">
                                <i class="ti ti-inbox" style="font-size: 1.8rem; display: block; margin-bottom: 6px; opacity: 0.6;"></i>
                                Belum ada pengajuan form Excel hari ini.
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
                            <p>Oleh: {{ $pending->user->name ?? 'Staff' }}</p>
                        </div>
                        <button class="btn-primary" onclick="openVerifyModalWithData({{ $pending->id }}, '{{ addslashes($pending->module->name ?? 'Form Excel') }}', '{{ addslashes($pending->user->name ?? 'Staff') }}', '{{ route('submissions.download', $pending) }}', '{{ addslashes($pending->notes ?? '') }}')">
                            <i class="ti ti-check"></i> Cek
                        </button>
                    </div>
                    @empty
                    <div style="text-align: center; padding: 25px; color: #64748b;">
                        <i class="ti ti-circle-check" style="font-size: 1.8rem; color: #10b981; display: block; margin-bottom: 6px;"></i>
                        <span>Semua dokumen telah diverifikasi!</span>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
