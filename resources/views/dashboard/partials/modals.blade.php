<!-- Modal Tambah Kegiatan Baru -->
<div id="task-modal" class="modal">
    <div class="modal-content glass-panel" style="max-width: 480px; width: 90%;">
        <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 8px; color: #fff;">
                <i class="ti ti-calendar-plus" style="color: var(--hokben-yellow);"></i> Tambah Kegiatan Baru
            </h3>
            <button type="button" class="close-modal" onclick="closeTaskModal()" style="background: none; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer;">
                <i class="ti ti-x"></i>
            </button>
        </div>

        <form action="{{ route('tasks.store') }}" method="POST">
            @csrf
            <div class="form-group mb-4">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 6px;">Judul Kegiatan *</label>
                <input type="text" name="title" required class="styled-input" placeholder="Contoh: Checklist Sanitasi Cold Storage..." style="width: 100%;">
                @error('title')
                    <span style="color: #ef4444; font-size: 0.8rem; display: block; margin-top: 4px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group mb-4">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 6px;">Deskripsi / Rincian Instruksi</label>
                <textarea name="description" rows="3" class="styled-input" placeholder="Tuliskan detail pekerjaan atau instruksi teknis..." style="width: 100%;"></textarea>
            </div>

            <div class="form-group mb-4">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 6px;">Petugas / PIC Bertanggung Jawab *</label>
                <select name="user_id" required class="styled-input" style="width: 100%; background: #1e293b; color: #fff;">
                    <option value="">-- Pilih Petugas Karyawan --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                @error('user_id')
                    <span style="color: #ef4444; font-size: 0.8rem; display: block; margin-top: 4px;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;" class="mb-4">
                <div class="form-group" style="margin: 0;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 6px;">Tingkat Prioritas *</label>
                    <select name="priority" required class="styled-input" style="width: 100%; background: #1e293b; color: #fff;">
                        <option value="high">🔥 Urgent (Tinggi)</option>
                        <option value="medium" selected>⚡ Normal (Sedang)</option>
                        <option value="low">✓ Rutin (Rendah)</option>
                    </select>
                </div>

                <div class="form-group" style="margin: 0;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 6px;">Batas Waktu (Opsional)</label>
                    <input type="datetime-local" name="deadline" class="styled-input" style="width: 100%; background: #1e293b; color: #fff;">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px;">
                <button type="button" class="btn-outline" onclick="closeTaskModal()" style="padding: 8px 16px;">Batal</button>
                <button type="submit" class="btn-primary" style="padding: 8px 20px; display: flex; align-items: center; gap: 6px;">
                    <i class="ti ti-check"></i> Simpan Kegiatan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Kegiatan Baru -->
<div id="edit-task-modal" class="modal">
    <div class="modal-content glass-panel" style="max-width: 500px; width: 90%;">
        <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 8px; color: #fff;">
                <i class="ti ti-edit" style="color: var(--hokben-yellow);"></i> Edit Rincian Kegiatan
            </h3>
            <button type="button" class="close-modal" onclick="closeEditTaskModal()" style="background: none; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer;">
                <i class="ti ti-x"></i>
            </button>
        </div>

        <form id="edit-task-form" method="POST" action="">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit-task-id" name="id">

            <div class="form-group mb-3">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 6px;">Judul Kegiatan *</label>
                <input type="text" name="title" id="edit-task-title" required class="styled-input" placeholder="Judul kegiatan operasional..." style="width: 100%;">
            </div>

            <div class="form-group mb-3">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 6px;">Deskripsi / Instruksi Tambahan</label>
                <textarea name="description" id="edit-task-desc" rows="3" class="styled-input" placeholder="Rincian instruksi pekerjaan..." style="width: 100%;"></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;" class="mb-3">
                <div class="form-group" style="margin: 0;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 6px;">Tingkat Prioritas *</label>
                    <select name="priority" id="edit-task-priority" required class="styled-input" style="width: 100%; background: #1e293b; color: #fff;">
                        <option value="high">🔥 Urgent (Tinggi)</option>
                        <option value="medium">⚡ Normal (Sedang)</option>
                        <option value="low">✓ Rutin (Rendah)</option>
                    </select>
                </div>

                <div class="form-group" style="margin: 0;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 6px;">Kolom Status</label>
                    <select name="status" id="edit-task-status" required class="styled-input" style="width: 100%; background: #1e293b; color: #fff;">
                        <option value="todo">To Do</option>
                        <option value="in_progress">In Progress</option>
                        <option value="review">Review</option>
                        <option value="done">Done / Selesai</option>
                    </select>
                </div>
            </div>

            <div class="form-group mb-3">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 6px;">Petugas / PIC Bertanggung Jawab *</label>
                <select name="user_id" id="edit-task-user" required class="styled-input" style="width: 100%; background: #1e293b; color: #fff;">
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mb-4">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 6px;">Batas Waktu / Deadline</label>
                <input type="datetime-local" name="deadline" id="edit-task-deadline" class="styled-input" style="width: 100%; background: #1e293b; color: #fff;">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px;">
                <button type="button" class="btn-outline" onclick="closeEditTaskModal()" style="padding: 8px 16px;">Batal</button>
                <button type="submit" class="btn-primary" style="padding: 8px 20px; display: flex; align-items: center; gap: 6px;">
                    <i class="ti ti-device-floppy"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Verifikasi & Detail File Excel / Data Analisis -->
<div id="verify-modal" class="modal">
    <div class="modal-content glass-panel" style="max-width: 760px; width: 92%; max-height: 88vh; overflow-y: auto;">
        <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid var(--border-color); padding-bottom: 14px;">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 10px; color: #fff; font-size: 1.15rem; font-weight: 800;">
                <i class="ti ti-report-analytics" style="color: var(--hokben-yellow-dark); font-size: 1.4rem;"></i>
                Detail &amp; Data Hasil Analisis Dokumen
            </h3>
            <button type="button" class="close-modal" onclick="closeVerifyModal()" style="background: none; border: none; color: #94a3b8; font-size: 1.3rem; cursor: pointer;">
                <i class="ti ti-x"></i>
            </button>
        </div>

        <form id="verify-submission-form" method="POST" action="">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" id="verify-status-input" value="Lengkap">

            <div class="modal-body" style="color: #cbd5e1; font-size: 0.88rem;">

                {{-- Header Info Dokumen --}}
                <div style="background: var(--bg-subtle); border: 1px solid var(--border-color); border-radius: 12px; padding: 14px 18px; margin-bottom: 18px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 10px;">
                        <div>
                            <p style="margin: 0 0 4px 0; font-size: 0.76rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: .5px;">Nama Dokumen</p>
                            <h4 id="verify-modal-file-title" style="margin: 0 0 6px 0; color: #fff; font-size: 1rem; font-weight: 800;">-</h4>
                            <p style="margin: 0; font-size: 0.78rem; color: var(--text-muted);">
                                Diunggah oleh: <strong id="verify-modal-user" style="color: #FFC400;">-</strong>
                                &bull; <span id="verify-modal-time">-</span>
                            </p>
                        </div>
                        <span id="verify-modal-status-badge" class="badge-status warning" style="font-size: 0.78rem; padding: 4px 12px; border-radius: 20px;">
                            Menunggu Review
                        </span>
                    </div>
                </div>

                {{-- Action Bar Download: Data Analisis & File Asli --}}
                <div style="background: linear-gradient(135deg, rgba(245,158,11,0.12) 0%, rgba(217,34,42,0.08) 100%);
                            border: 1px solid rgba(245,158,11,0.3); border-radius: 12px; padding: 14px 18px; margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                        <div>
                            <p style="margin: 0; font-size: 0.88rem; font-weight: 800; color: var(--text-main);">
                                <i class="ti ti-download" style="color: var(--hokben-yellow-dark);"></i> Unduh Berkas &amp; Data
                            </p>
                            <p style="margin: 2px 0 0 0; font-size: 0.76rem; color: var(--text-muted);">
                                Tersedia unduhan data hasil analisis (Excel/CSV) atau file asli Excel yang diunggah.
                            </p>
                        </div>
                        <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                            {{-- Download Hasil Analisis --}}
                            <a href="#" id="verify-modal-download-analysis-btn"
                               class="btn-primary"
                               style="display: inline-flex; align-items: center; gap: 7px; padding: 9px 16px; font-size: 0.82rem; font-weight: 800; text-decoration: none; border-radius: 8px; background: linear-gradient(135deg, #F59E0B 0%, #D9222A 100%); color: #fff; box-shadow: 0 4px 12px rgba(245,158,11,0.3);">
                                <i class="ti ti-table-export" style="font-size: 1rem;"></i>
                                <span>Unduh Data Hasil Analisis (.CSV / Excel)</span>
                            </a>

                            {{-- Download Berkas Asli --}}
                            <a href="#" id="verify-modal-download-btn"
                               class="btn-outline"
                               style="display: inline-flex; align-items: center; gap: 7px; padding: 9px 14px; font-size: 0.82rem; font-weight: 700; text-decoration: none; border-radius: 8px;">
                                <i class="ti ti-file-spreadsheet"></i>
                                <span>Unduh Berkas Asli</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Kontainer Dinamis Hasil Analisis --}}
                <div id="verify-modal-analysis-container" style="margin-bottom: 20px;">
                    <div style="text-align: center; padding: 24px; color: var(--text-muted);">
                        <i class="ti ti-loader" style="font-size: 1.8rem; display: block; margin-bottom: 8px; animation: spin 1s linear infinite;"></i>
                        <span>Memuat rincian hasil analisis data...</span>
                    </div>
                </div>

                {{-- Catatan / Feedback Evaluasi --}}
                <div class="form-group mb-3">
                    <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #cbd5e1; margin-bottom: 6px;">
                        <i class="ti ti-message-dots" style="color: var(--hokben-yellow-dark);"></i> Catatan / Feedback Evaluasi Leader
                    </label>
                    <textarea name="notes" id="verify-modal-notes" class="styled-input" rows="3"
                              placeholder="Tuliskan catatan evaluasi atau instruksi perbaikan..."
                              style="width: 100%; border-radius: 8px; font-size: 0.84rem;"></textarea>
                </div>
            </div>

            <div class="modal-footer" style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 14px; margin-top: 16px; flex-wrap: wrap; gap: 10px;">
                <button type="button" class="btn-outline" onclick="closeVerifyModal()" style="padding: 8px 16px; font-size: 0.82rem;">
                    Tutup
                </button>
                @if(Auth::check() && Auth::user()->isLeader())
                <div style="display: flex; gap: 8px;">
                    <button type="button" class="btn-danger" onclick="submitVerification('Perlu Perbaikan')"
                            style="padding: 9px 16px; display: inline-flex; align-items: center; gap: 6px; font-size: 0.82rem; font-weight: 700; border-radius: 8px;">
                        <i class="ti ti-arrow-back-up"></i> Perlu Perbaikan
                    </button>
                    <button type="button" class="btn-success" onclick="submitVerification('Lengkap')"
                            style="padding: 9px 18px; display: inline-flex; align-items: center; gap: 6px; font-size: 0.82rem; font-weight: 700; border-radius: 8px;">
                        <i class="ti ti-check"></i> Approve Lengkap
                    </button>
                </div>
                @else
                <div style="display: flex; align-items: center; gap: 8px; color: #94a3b8; font-size: 0.8rem; background: rgba(255,255,255,0.05); padding: 8px 14px; border-radius: 8px; border: 1px dashed var(--border-color);">
                    <i class="ti ti-shield-lock" style="color: var(--hokben-yellow-dark); font-size: 1.1rem;"></i>
                    <span>Verifikasi &amp; persetujuan dokumen khusus akun <strong>Store Leader</strong></span>
                </div>
                @endif
            </div>
        </form>
    </div>
</div>
