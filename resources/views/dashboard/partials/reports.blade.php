<!-- Reports View -->
<section id="view-reports" class="view-section hidden">
    <form method="GET" action="{{ route('dashboard') }}" class="glass-panel mb-4 filter-bar" id="reports-filter-form">
        <div class="filter-group">
            <label>Tanggal</label>
            <input type="date" name="report_date" value="{{ request('report_date', now()->toDateString()) }}" class="styled-input">
        </div>
        <div class="filter-group">
            <label>Divisi</label>
            <select name="report_division" class="styled-select">
                <option value="all" {{ request('report_division') === 'all' ? 'selected' : '' }}>Semua Divisi</option>
                <option value="Kitchen" {{ request('report_division') === 'Kitchen' ? 'selected' : '' }}>Kitchen</option>
                <option value="Front Counter" {{ request('report_division') === 'Front Counter' ? 'selected' : '' }}>Front Counter</option>
                <option value="Cashier" {{ request('report_division') === 'Cashier' ? 'selected' : '' }}>Cashier</option>
            </select>
        </div>
        <div class="filter-group">
            <label>Shift</label>
            <select name="report_shift" class="styled-select">
                <option value="all" {{ request('report_shift') === 'all' ? 'selected' : '' }}>Semua Shift</option>
                <option value="Opening" {{ request('report_shift') === 'Opening' ? 'selected' : '' }}>Opening</option>
                <option value="Middle" {{ request('report_shift') === 'Middle' ? 'selected' : '' }}>Middle</option>
                <option value="Closing" {{ request('report_shift') === 'Closing' ? 'selected' : '' }}>Closing</option>
            </select>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn-primary"><i class="ti ti-filter"></i> Terapkan</button>
            <button type="button" class="btn-export" onclick="exportReportsTableToExcel()"><i class="ti ti-file-export"></i> Export Excel</button>
        </div>
    </form>

    <div class="glass-panel">
        <div class="table-responsive">
            <table class="data-table" id="reports-data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Petugas</th>
                        <th>Divisi</th>
                        <th>Shift</th>
                        <th>Kepatuhan Checklist</th>
                        <th>Status Verifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportActivities as $act)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($act->date)->format('d M Y') }}</td>
                        <td style="font-weight: 500;">{{ $act->user->name ?? 'Staff' }}</td>
                        <td>{{ $act->division ?? '-' }}</td>
                        <td><span class="badge" style="background: rgba(255,255,255,0.08); padding: 3px 8px; border-radius: 4px;">{{ $act->shift }}</span></td>
                        <td>
                            @php
                                $totalChecklist = is_array($act->checklist_data) ? count($act->checklist_data) : 0;
                                $completedChecklist = is_array($act->checklist_data) ? collect($act->checklist_data)->where('completed', true)->count() : 0;
                                $percent = $totalChecklist > 0 ? round(($completedChecklist / $totalChecklist) * 100) : 100;
                            @endphp
                            <span class="badge-status {{ $percent >= 100 ? 'success' : 'warning' }}">{{ $percent }}% ({{ $completedChecklist }}/{{ $totalChecklist }})</span>
                        </td>
                        <td><span class="badge-status success">Lengkap Terisi</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #94a3b8; padding: 30px;">
                            <i class="ti ti-file-x" style="font-size: 1.8rem; display: block; margin-bottom: 6px; opacity: 0.6;"></i>
                            Tidak ada riwayat aktivitas checklist pada tanggal dan filter yang dipilih.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
