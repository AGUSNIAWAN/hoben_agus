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
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h2 style="margin: 0;">Checklist Aktivitas (<span id="active-shift-label">Opening</span>)</h2>
                <span class="badge" style="background: rgba(255,196,0,0.15); color: #FFC400; padding: 4px 10px; border-radius: 20px; font-size: 12px;">
                    {{ Auth::user()->division ?? 'All Division' }}
                </span>
            </div>
            
            <ul class="checklist" id="daily-checklist">
                <!-- Populated by JS based on shift selection -->
            </ul>

            <button type="button" class="btn-primary full-width mt-4" id="btn-save-checklist" onclick="saveShiftChecklist()">
                <i class="ti ti-device-floppy"></i> Simpan Checklist ke Database
            </button>
            <div id="checklist-feedback" style="display: none; padding: 10px; border-radius: 6px; margin-top: 12px; font-size: 13px; text-align: center;"></div>
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
</section>
