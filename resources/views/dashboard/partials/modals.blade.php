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

<!-- Modal Verifikasi File Excel (Leader Action) -->
<div id="verify-modal" class="modal">
    <div class="modal-content glass-panel" style="max-width: 500px; width: 90%;">
        <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 8px; color: #fff;">
                <i class="ti ti-file-check" style="color: var(--hokben-yellow);"></i> Verifikasi File Excel
            </h3>
            <button type="button" class="close-modal" onclick="closeVerifyModal()" style="background: none; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer;">
                <i class="ti ti-x"></i>
            </button>
        </div>

        <form id="verify-submission-form" method="POST" action="">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" id="verify-status-input" value="Lengkap">

            <div class="modal-body" style="color: #cbd5e1; font-size: 0.9rem;">
                <p style="margin-bottom: 10px;">Mengevaluasi dokumen: <strong id="verify-modal-file-title" style="color: #fff;">-</strong></p>
                <p style="margin-bottom: 16px;">Diajukan oleh: <strong id="verify-modal-user" style="color: #FFC400;">-</strong></p>

                <div class="preview-placeholder" style="background: rgba(18, 24, 38, 0.7); border: 1px dashed rgba(255,255,255,0.2); border-radius: 8px; padding: 18px; text-align: center; margin-bottom: 18px;">
                    <i class="ti ti-file-spreadsheet" style="font-size: 2.2rem; color: #10b981; display: block; margin-bottom: 8px;"></i>
                    <span style="font-size: 0.85rem; color: #94a3b8; display: block; margin-bottom: 10px;">Berkas tersimpan di storage server.</span>
                    <a href="#" id="verify-modal-download-btn" class="btn-outline" style="display: inline-flex; align-items: center; gap: 6px; text-decoration: none; padding: 6px 14px; font-size: 0.85rem;">
                        <i class="ti ti-download"></i> Unduh & Periksa Berkas
                    </a>
                </div>

                <div class="form-group mb-3">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 6px;">Catatan / Feedback Evaluasi</label>
                    <textarea name="notes" id="verify-modal-notes" class="styled-input" rows="3" placeholder="Tuliskan catatan perbaikan atau feedback verifikasi..." style="width: 100%;"></textarea>
                </div>
            </div>

            <div class="modal-footer" style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn-danger" onclick="submitVerification('Perlu Perbaikan')" style="padding: 9px 16px; display: flex; align-items: center; gap: 6px;">
                    <i class="ti ti-arrow-back-up"></i> Perlu Perbaikan
                </button>
                <button type="button" class="btn-success" onclick="submitVerification('Lengkap')" style="padding: 9px 18px; display: flex; align-items: center; gap: 6px;">
                    <i class="ti ti-check"></i> Approve Lengkap
                </button>
            </div>
        </form>
    </div>
</div>
