
// ================================================================
// INLINE UPLOAD PANEL HELPERS
// Digunakan oleh semua section yang punya tombol upload sendiri
// ================================================================

/**
 * Toggle tampil/sembunyikan panel upload inline
 * @param {string} panelId - ID wrap element (panelId + '-wrap')
 */
window.toggleUploadPanel = function(panelId) {
    var wrap = document.getElementById(panelId + '-wrap');
    if (!wrap) return;
    var isHidden = wrap.style.display === 'none' || wrap.style.display === '';
    wrap.style.display = isHidden ? 'block' : 'none';
    if (isHidden) {
        // Smooth scroll ke panel
        setTimeout(function() {
            wrap.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 50);
    }
};

/**
 * Callback saat file dipilih pada upload inline panel
 */
window.onInlineFileChosen = function(input, panelId, accentColor) {
    var file = input.files[0];
    if (!file) return;

    var label = document.getElementById(panelId + '-label');
    var btn   = document.getElementById(panelId + '-btn');
    var zone  = document.getElementById(panelId + '-zone');

    if (label) {
        label.textContent = file.name.length > 32
            ? file.name.substring(0, 29) + '...'
            : file.name;
        label.style.color = accentColor || '#10b981';
    }
    if (zone) {
        zone.style.borderStyle = 'solid';
        zone.style.borderColor = accentColor || '#10b981';
    }
    if (btn) {
        btn.style.display = 'inline-flex';
    }
};

/**
 * Handle form submit untuk upload inline — AJAX POST
 */
window.handleInlineUploadSubmit = function(form, panelId) {
    var feedback = document.getElementById(panelId + '-feedback');
    var btn = document.getElementById(panelId + '-btn');

    if (!form.querySelector('input[type=file]').files.length) {
        if (feedback) {
            feedback.style.display = 'block';
            feedback.style.background = 'rgba(239,68,68,0.15)';
            feedback.style.color = '#fca5a5';
            feedback.textContent = '⚠ Pilih file terlebih dahulu!';
        }
        return false; // prevent submit
    }

    // Tampilkan loading
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="ti ti-loader"></i> Mengupload...'; }
    if (feedback) {
        feedback.style.display = 'block';
        feedback.style.background = 'rgba(245,158,11,0.12)';
        feedback.style.color = '#F59E0B';
        feedback.textContent = '⏳ Mengupload dan menganalisis file...';
    }

    var fd = new FormData(form);
    var csrf = document.querySelector('meta[name="csrf-token"]');
    if (csrf) fd.set('_token', csrf.getAttribute('content'));

    fetch(form.action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf ? csrf.getAttribute('content') : '', 'Accept': 'application/json' },
        body: fd
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            if (feedback) {
                feedback.style.background = 'rgba(16,185,129,0.15)';
                feedback.style.color = '#10b981';
                feedback.innerHTML = '<i class="ti ti-circle-check"></i> ' + (data.message || 'File berhasil diunggah dan dianalisis!');
            }
            // Reset form setelah 2 detik
            setTimeout(function() {
                form.reset();
                var label = document.getElementById(panelId + '-label');
                if (label) { label.textContent = 'Klik atau drag file ke sini'; label.style.color = ''; }
                var zone = document.getElementById(panelId + '-zone');
                if (zone) { zone.style.borderStyle = 'dashed'; }
                if (btn) { btn.style.display = 'none'; btn.disabled = false; btn.innerHTML = '<i class="ti ti-send"></i> Kirim'; }
                // Reload halaman untuk lihat analisis terbaru
                window.location.reload();
            }, 1800);
        } else {
            if (feedback) {
                feedback.style.background = 'rgba(239,68,68,0.15)';
                feedback.style.color = '#fca5a5';
                feedback.innerHTML = '<i class="ti ti-alert-circle"></i> ' + (data.message || 'Gagal mengupload.');
            }
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="ti ti-send"></i> Kirim'; }
        }
    })
    .catch(function(e) {
        if (feedback) {
            feedback.style.background = 'rgba(239,68,68,0.15)';
            feedback.style.color = '#fca5a5';
            feedback.textContent = '⚠ Terjadi kesalahan jaringan: ' + e.message;
        }
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="ti ti-send"></i> Kirim'; }
    });

    return false; // selalu prevent default submit (pakai fetch)
};
