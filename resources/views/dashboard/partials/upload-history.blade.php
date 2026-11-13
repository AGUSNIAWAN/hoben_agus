{{-- ============================================================ --}}
{{-- RIWAYAT & HISTORI UPLOAD FILE EXCEL / DOKUMEN                --}}
{{-- Mencatat setiap berkas yang diunggah secara real-time        --}}
{{-- ============================================================ --}}
<section id="view-upload-history-view" class="view-section hidden">

    {{-- Header Banner --}}
    <div style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.12) 0%, rgba(245, 158, 11, 0.10) 50%, rgba(99, 102, 241, 0.08) 100%);
                border: 1px solid rgba(16, 185, 129, 0.25); border-radius: 18px; padding: 22px 28px; margin-bottom: 24px;
                display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                        display: flex; align-items: center; justify-content: center; font-size: 1.6rem; color: #fff; box-shadow: 0 6px 18px rgba(16,185,129,0.35); flex-shrink: 0;">
                <i class="ti ti-history"></i>
            </div>
            <div>
                <h2 style="margin: 0 0 4px 0; font-size: 1.35rem; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                    <span>Riwayat &amp; Histori Upload File</span>
                    <span style="font-size: 0.72rem; font-weight: 700; padding: 3px 10px; border-radius: 20px; background: rgba(16,185,129,0.15); color: #10b981; border: 1px solid rgba(16,185,129,0.3);">
                        LOG AKTIF
                    </span>
                </h2>
                <p style="margin: 0; font-size: 0.84rem; color: var(--text-muted);">
                    Audit trail dan histori seluruh dokumen Excel, form POS, &amp; laporan yang diunggah oleh staf beserta analisis otomatisnya.
                </p>
            </div>
        </div>

        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 10px; font-size: 0.8rem; font-weight: 700; color: var(--text-body);">
                <i class="ti ti-files" style="color: #10b981;"></i> Total: <strong>{{ count($uploadHistory ?? []) }} Berkas</strong>
            </span>
            <button type="button" onclick="window.location.reload()" class="btn-primary" style="font-size: 0.8rem; padding: 7px 14px; display: inline-flex; align-items: center; gap: 6px;">
                <i class="ti ti-refresh"></i> Refresh Data
            </button>
        </div>
    </div>

    {{-- KPI Summary Cards --}}
    @php
        $historyList = $uploadHistory ?? collect();
        $totalFiles = $historyList->count();
        $todayCount = $historyList->filter(fn($s) => $s->created_at && $s->created_at->isToday())->count();
        $approvedCount = $historyList->where('status', 'Lengkap')->count();
        $pendingCount = $historyList->where('status', 'Menunggu Review')->count();
        $revisionCount = $historyList->where('status', 'Perlu Perbaikan')->count();
    @endphp

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; margin-bottom: 24px;">
        <div class="glass-panel" style="padding: 16px 18px; border-left: 4px solid #10b981;">
            <p style="margin: 0 0 4px 0; font-size: 0.74rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Total Histori Dokumen</p>
            <p style="margin: 0; font-size: 1.6rem; font-weight: 800; color: #10b981;">{{ $totalFiles }}</p>
            <p style="margin: 4px 0 0 0; font-size: 0.75rem; color: var(--text-muted);">Semua dokumen tersimpan</p>
        </div>

        <div class="glass-panel" style="padding: 16px 18px; border-left: 4px solid #F59E0B;">
            <p style="margin: 0 0 4px 0; font-size: 0.74rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Upload Hari Ini</p>
            <p style="margin: 0; font-size: 1.6rem; font-weight: 800; color: #F59E0B;">{{ $todayCount }}</p>
            <p style="margin: 4px 0 0 0; font-size: 0.75rem; color: var(--text-muted);">{{ now()->translatedFormat('d F Y') }}</p>
        </div>

        <div class="glass-panel" style="padding: 16px 18px; border-left: 4px solid #3B82F6;">
            <p style="margin: 0 0 4px 0; font-size: 0.74rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Terverifikasi Lengkap</p>
            <p style="margin: 0; font-size: 1.6rem; font-weight: 800; color: #3B82F6;">{{ $approvedCount }}</p>
            <p style="margin: 4px 0 0 0; font-size: 0.75rem; color: var(--text-muted);">Disetujui oleh Leader</p>
        </div>

        <div class="glass-panel" style="padding: 16px 18px; border-left: 4px solid #EF4444;">
            <p style="margin: 0 0 4px 0; font-size: 0.74rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Menunggu Review</p>
            <p style="margin: 0; font-size: 1.6rem; font-weight: 800; color: #EF4444;">{{ $pendingCount }}</p>
            <p style="margin: 4px 0 0 0; font-size: 0.75rem; color: var(--text-muted);">Perlu pengecekan Leader</p>
        </div>
    </div>

    {{-- Filter & Search Bar --}}
    <div class="glass-panel" style="padding: 16px 20px; margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap; flex: 1; min-width: 260px;">
                <div style="position: relative; flex: 1; max-width: 320px;">
                    <i class="ti ti-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.95rem;"></i>
                    <input type="text" id="history-search-input" placeholder="Cari nama berkas, staf, atau modul..."
                           oninput="filterUploadHistoryTable()"
                           style="width: 100%; padding: 8px 12px 8px 36px; background: var(--bg-subtle); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-main); font-size: 0.84rem; outline: none;">
                </div>

                <select id="history-module-filter" onchange="filterUploadHistoryTable()" class="styled-select" style="padding: 8px 12px; font-size: 0.82rem;">
                    <option value="all">Semua Kategori Form</option>
                    @foreach($modules as $m)
                        <option value="{{ strtolower($m->name) }}">{{ $m->name }}</option>
                    @endforeach
                </select>

                <select id="history-status-filter" onchange="filterUploadHistoryTable()" class="styled-select" style="padding: 8px 12px; font-size: 0.82rem;">
                    <option value="all">Semua Status</option>
                    <option value="menunggu review">Menunggu Review</option>
                    <option value="lengkap">Lengkap</option>
                    <option value="perlu perbaikan">Perlu Perbaikan</option>
                </select>
            </div>

            <span id="history-count-badge" style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">
                Menampilkan {{ $totalFiles }} dokumen
            </span>
        </div>
    </div>

    {{-- History Table Panel --}}
    <div class="glass-panel" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="data-table" id="upload-history-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 48px; text-align: center;">#</th>
                        <th>Berkas &amp; Detail File</th>
                        <th>Kategori / Modul</th>
                        <th>Diupload Oleh</th>
                        <th>Waktu &amp; Tanggal</th>
                        <th>Ringkasan Analisis</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center; width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="upload-history-tbody">
                    @forelse($historyList as $index => $item)
                    @php
                        $analysis = $item->analysis_data ? json_decode($item->analysis_data, true) : null;
                        $metrics = $analysis['metrics'] ?? null;
                        $statusClass = $item->status === 'Lengkap' ? 'success' : ($item->status === 'Perlu Perbaikan' ? 'danger' : 'warning');
                        $statusColor = $item->status === 'Lengkap' ? '#10b981' : ($item->status === 'Perlu Perbaikan' ? '#ef4444' : '#F59E0B');
                        $fileName = $item->original_name ?? basename($item->file_path ?? 'Dokumen.xlsx');
                        $fileSizeText = $item->formatted_file_size;
                        $searchableText = strtolower($fileName . ' ' . ($item->module->name ?? '') . ' ' . ($item->user->name ?? '') . ' ' . $item->status . ' ' . ($item->notes ?? ''));
                    @endphp
                    <tr class="history-item-row" data-search="{{ $searchableText }}" data-status="{{ strtolower($item->status) }}" data-module="{{ strtolower($item->module->name ?? '') }}">
                        {{-- Row Index --}}
                        <td style="text-align: center; font-weight: 700; color: var(--text-muted); font-size: 0.82rem;">
                            {{ $index + 1 }}
                        </td>

                        {{-- File Details --}}
                        <td>
                            <div style="display: flex; align-items: flex-start; gap: 10px;">
                                <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(16,185,129,0.12); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0; margin-top: 2px;">
                                    <i class="ti ti-file-spreadsheet"></i>
                                </div>
                                <div style="min-width: 0;">
                                    <p style="margin: 0; font-weight: 700; font-size: 0.88rem; color: var(--text-main); line-height: 1.3;" title="{{ $fileName }}">
                                        {{ Str::limit($fileName, 42) }}
                                    </p>
                                    <div style="display: flex; gap: 8px; align-items: center; margin-top: 3px; font-size: 0.74rem; color: var(--text-muted);">
                                        <span><i class="ti ti-database"></i> {{ $fileSizeText }}</span>
                                        <span>&bull;</span>
                                        <span><i class="ti ti-calendar"></i> Periode {{ $item->period }}</span>
                                    </div>
                                    @if($item->notes && $item->notes !== 'Menunggu pengecekan kelengkapan dari Store Leader.')
                                        <p style="margin: 3px 0 0 0; font-size: 0.74rem; color: var(--text-muted); font-style: italic;">
                                            &ldquo;{{ Str::limit($item->notes, 50) }}&rdquo;
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Module --}}
                        <td>
                            <span style="font-weight: 700; font-size: 0.84rem; color: var(--text-main); display: block;">
                                {{ $item->module->name ?? 'Form Excel' }}
                            </span>
                            <span style="font-size: 0.74rem; color: var(--text-muted);">
                                {{ $item->module->category ?? 'Umum' }}
                            </span>
                        </td>

                        {{-- Uploader --}}
                        <td>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="width: 26px; height: 26px; border-radius: 50%; background: var(--bg-subtle); display: flex; align-items: center; justify-content: center; font-size: 0.78rem; color: var(--text-muted);">
                                    <i class="ti ti-user"></i>
                                </div>
                                <div>
                                    <p style="margin: 0; font-weight: 700; font-size: 0.84rem; color: var(--text-main);">
                                        {{ $item->user->name ?? 'Staff' }}
                                    </p>
                                    <p style="margin: 0; font-size: 0.72rem; color: var(--text-muted);">
                                        {{ $item->user->division ?? 'Operasional' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- Date & Time --}}
                        <td>
                            <div style="font-size: 0.82rem; font-weight: 700; color: var(--text-main);">
                                {{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}
                            </div>
                            <div style="font-size: 0.74rem; color: var(--text-muted); display: flex; align-items: center; gap: 4px; margin-top: 2px;">
                                <i class="ti ti-clock"></i> {{ $item->created_at ? $item->created_at->format('H:i') : '' }}
                                ({{ $item->created_at ? $item->created_at->diffForHumans() : '-' }})
                            </div>
                        </td>

                        {{-- Analysis Summary --}}
                        <td>
                            @if($metrics && isset($metrics['formatted_revenue']))
                                <div style="display: flex; flex-direction: column; gap: 3px;">
                                    <span style="font-weight: 800; color: var(--hokben-yellow-dark); font-size: 0.84rem;">
                                        💰 {{ $metrics['formatted_revenue'] }}
                                    </span>
                                    <span style="font-size: 0.74rem; color: var(--text-muted);">
                                        🍱 {{ $metrics['formatted_qty'] ?? '-' }} &bull; {{ $metrics['total_menu_variants'] ?? 0 }} Varian
                                    </span>
                                </div>
                            @elseif($metrics && isset($metrics['total_rows']))
                                <span style="font-size: 0.8rem; font-weight: 600; color: #818cf8;">
                                    📊 {{ $metrics['total_rows'] }} baris data ({{ $metrics['sheet_count'] ?? 1 }} sheet)
                                </span>
                            @else
                                <span style="font-size: 0.76rem; color: var(--text-muted); font-style: italic;">
                                    Tersimpan di server
                                </span>
                            @endif
                        </td>

                        {{-- Status Badge --}}
                        <td style="text-align: center;">
                            <span class="badge-status {{ $statusClass }}">
                                @if($item->status === 'Lengkap')
                                    <i class="ti ti-circle-check"></i>
                                @elseif($item->status === 'Menunggu Review')
                                    <i class="ti ti-clock"></i>
                                @else
                                    <i class="ti ti-alert-triangle"></i>
                                @endif
                                {{ $item->status }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                <a href="{{ route('submissions.download_analysis', $item) }}" title="Unduh Data Hasil Analisis (Excel/CSV)"
                                   class="btn-primary" style="padding: 6px 10px; font-size: 0.78rem; text-decoration: none; border-radius: 6px; display: inline-flex; align-items: center; background: rgba(245,158,11,0.18); color: #F59E0B; border: 1px solid rgba(245,158,11,0.35);">
                                    <i class="ti ti-table-export"></i>
                                </a>
                                <a href="{{ route('submissions.download', $item) }}" title="Unduh File Asli"
                                   class="btn-outline" style="padding: 6px 10px; font-size: 0.78rem; text-decoration: none; border-radius: 6px; display: inline-flex; align-items: center;">
                                    <i class="ti ti-download"></i>
                                </a>
                                <button type="button" title="Detail & Review"
                                        onclick="openVerifyModalWithData({{ $item->id }}, '{{ addslashes($fileName) }}', '{{ addslashes($item->user->name ?? 'Staff') }}', '{{ route('submissions.download', $item) }}', '{{ addslashes($item->notes ?? '') }}')"
                                        class="btn-primary" style="padding: 6px 12px; font-size: 0.78rem; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="ti ti-eye"></i> Detail
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 48px 20px;">
                            <i class="ti ti-history-off" style="font-size: 3rem; display: block; margin-bottom: 12px; opacity: 0.4;"></i>
                            <h4 style="font-weight: 700; color: var(--text-body); margin: 0 0 4px 0;">Belum Ada Riwayat Upload</h4>
                            <p style="font-size: 0.84rem; margin: 0;">Setiap berkas yang diupload dari seluruh halaman akan otomatis tersimpan dalam histori ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

<script>
window.filterUploadHistoryTable = function() {
    var search = (document.getElementById('history-search-input')?.value || '').toLowerCase().trim();
    var moduleFilter = (document.getElementById('history-module-filter')?.value || 'all').toLowerCase();
    var statusFilter = (document.getElementById('history-status-filter')?.value || 'all').toLowerCase();

    var rows = document.querySelectorAll('#upload-history-tbody .history-item-row');
    var visibleCount = 0;

    rows.forEach(function(row) {
        var text = row.getAttribute('data-search') || '';
        var mod  = row.getAttribute('data-module') || '';
        var stat = row.getAttribute('data-status') || '';

        var matchSearch = !search || text.includes(search);
        var matchModule = moduleFilter === 'all' || mod.includes(moduleFilter);
        var matchStatus = statusFilter === 'all' || stat.includes(statusFilter);

        if (matchSearch && matchModule && matchStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    var badge = document.getElementById('history-count-badge');
    if (badge) {
        badge.textContent = 'Menampilkan ' + visibleCount + ' dokumen';
    }
};
</script>

