document.addEventListener('DOMContentLoaded', () => {
    // Current Date Setup
    const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const dateEl = document.getElementById('current-date');
    if (dateEl) {
        dateEl.textContent = new Date().toLocaleDateString('id-ID', dateOptions);
    }

    // Navigation Logic
    const navLinks = document.querySelectorAll('.nav-link');
    const views = document.querySelectorAll('.view-section');
    const pageTitle = document.getElementById('current-page-title');

    const titles = {
        'dashboard': 'Dashboard Monitoring',
        'sales': 'Monitoring Sales',
        'staff-portal': 'Portal Karyawan',
        'reports': 'Rekap & Laporan',
        'serah-terima': 'Serah Terima Sales & Modal Harian',
        'monitoring-preshift-area19': 'MONITORING PRE-SHIFT ONLINE AREA 19',
        'monitoring-kfm-helpdesk': 'MONITORING KFM DAN HELPDESK',
        'monitoring-kegiatan': 'MONITORING KEGIATAN KANBAN'
    };

    function switchView(targetId) {
        if (!titles[targetId]) return;

        // Update Active Link
        navLinks.forEach(l => {
            if (l.getAttribute('data-target') === targetId) {
                l.classList.add('active');
            } else {
                l.classList.remove('active');
            }
        });

        // Update Title
        if (pageTitle) {
            pageTitle.textContent = titles[targetId];
        }

        // Switch View
        views.forEach(view => {
            view.classList.remove('active');
            view.classList.add('hidden');
            if(view.id === `view-${targetId}`) {
                view.classList.remove('hidden');
                setTimeout(() => view.classList.add('active'), 10);
            }
        });

        // Store active tab
        localStorage.setItem('activeOpsTrackTab', targetId);
    }

    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = link.getAttribute('data-target');
            switchView(targetId);
        });
    });

    // Cek tab tersimpan atau URL hash
    const hash = window.location.hash ? window.location.hash.replace('#', '') : '';
    const savedTab = localStorage.getItem('activeOpsTrackTab');
    if (hash && titles[hash]) {
        switchView(hash);
    } else if (savedTab && titles[savedTab] && savedTab !== 'dashboard') {
        switchView(savedTab);
    }

    // Staff Portal Logic: Shift & Checklist
    const shiftRadios = document.querySelectorAll('input[name="shift"]');
    const checklistUl = document.getElementById('daily-checklist');
    const shiftLabel = document.getElementById('active-shift-label');

    const checklists = {
        'Opening': [
            'Cek kebersihan area meja makan & kitchen',
            'Nyalakan sistem POS & sambungkan printer struk',
            'Cek modal awal laci kasir dan uang kembalian',
            'Pemeriksaan log suhu awal chiller & cold storage'
        ],
        'Middle': [
            'Cek kelengkapan stok bahan baku utama (Bento, Teriyaki)',
            'Update log suhu operasional chiller shift siang',
            'Pembersihan station kerja dan buang sampah dapur',
            'Laporan pergantian kasir / serah terima modal'
        ],
        'Closing': [
            'Hitung omzet setoran harian kasir counter 1 & 2',
            'Matikan alat pemanas listrik & mesin fryer',
            'Sanitasi menyeluruh lantai, meja, dan kitchen',
            'Upload form Excel closing harian ke portal'
        ]
    };

    function renderChecklist(shift) {
        if (shiftLabel) shiftLabel.textContent = shift;
        if (!checklistUl) return;

        checklistUl.innerHTML = '';
        (checklists[shift] || []).forEach((task, idx) => {
            const li = document.createElement('li');
            li.innerHTML = `
                <input type="checkbox" id="task-${idx}" checked>
                <label for="task-${idx}">${task}</label>
            `;
            checklistUl.appendChild(li);
        });
    }

    shiftRadios.forEach(radio => {
        radio.addEventListener('change', (e) => {
            renderChecklist(e.target.value);
        });
    });

    // Inisialisasi checklist opening
    renderChecklist('Opening');

    // Drag and Drop Upload logic di Staff Portal
    const uploadZone = document.getElementById('upload-zone');
    const fileInput = document.getElementById('file-input');

    if (uploadZone && fileInput) {
        uploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadZone.style.borderColor = 'var(--primary)';
        });

        uploadZone.addEventListener('dragleave', () => {
            uploadZone.style.borderColor = 'var(--border-color)';
        });

        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadZone.style.borderColor = 'var(--border-color)';
            if(e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                window.handleFileSelected(fileInput);
            }
        });
    }

    // Modal Close Listeners
    const modal = document.getElementById('verify-modal');
    if (modal) {
        window.addEventListener('click', (e) => {
            if(e.target === modal) {
                window.closeVerifyModal();
            }
        });
    }
});

// File Selection Handler di Staff Portal
window.handleFileSelected = function(input) {
    const label = document.getElementById('selected-file-label');
    const submitBtn = document.getElementById('submit-upload-btn');
    if (input.files && input.files.length > 0) {
        const file = input.files[0];
        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
        if (label) {
            label.textContent = `Berkas terpilih: ${file.name} (${fileSizeMB} MB)`;
            label.style.display = 'block';
        }
        if (submitBtn) {
            submitBtn.style.display = 'flex';
        }
    }
};

// Simpan Checklist Shift ke Database via AJAX (Opsi A)
window.saveShiftChecklist = function() {
    const activeShiftRadio = document.querySelector('input[name="shift"]:checked');
    const shift = activeShiftRadio ? activeShiftRadio.value : 'Opening';
    const feedbackEl = document.getElementById('checklist-feedback');
    const btn = document.getElementById('btn-save-checklist');

    const checklistItems = [];
    document.querySelectorAll('#daily-checklist li').forEach(li => {
        const checkbox = li.querySelector('input[type="checkbox"]');
        const label = li.querySelector('label');
        checklistItems.push({
            task: label ? label.textContent.trim() : '',
            completed: checkbox ? checkbox.checked : false
        });
    });

    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? 
                      document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="ti ti-loader"></i> Menyimpan...';
    }

    fetch('/daily-activities', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            shift: shift,
            checklist_data: checklistItems
        })
    })
    .then(res => res.json())
    .then(data => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="ti ti-device-floppy"></i> Simpan Checklist ke Database';
        }
        if (feedbackEl) {
            feedbackEl.style.display = 'block';
            if (data.success) {
                feedbackEl.style.background = 'rgba(16, 185, 129, 0.15)';
                feedbackEl.style.color = '#10b981';
                feedbackEl.style.border = '1px solid #10b981';
                feedbackEl.textContent = data.message;
            } else {
                feedbackEl.style.background = 'rgba(239, 68, 68, 0.15)';
                feedbackEl.style.color = '#ef4444';
                feedbackEl.style.border = '1px solid #ef4444';
                feedbackEl.textContent = data.message || 'Gagal menyimpan checklist.';
            }
        }
    })
    .catch(err => {
        console.error(err);
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="ti ti-device-floppy"></i> Simpan Checklist ke Database';
        }
        if (feedbackEl) {
            feedbackEl.style.display = 'block';
            feedbackEl.style.background = 'rgba(239, 68, 68, 0.15)';
            feedbackEl.style.color = '#ef4444';
            feedbackEl.textContent = 'Terjadi kesalahan jaringan saat menyimpan checklist.';
        }
    });
};

// Modal Verifikasi Dokumen oleh Leader (Opsi B)
window.openVerifyModalWithData = function(id, fileTitle, userName, downloadUrl, notes) {
    const modal = document.getElementById('verify-modal');
    const form = document.getElementById('verify-submission-form');
    const titleEl = document.getElementById('verify-modal-file-title');
    const userEl = document.getElementById('verify-modal-user');
    const downloadBtn = document.getElementById('verify-modal-download-btn');
    const notesEl = document.getElementById('verify-modal-notes');

    if (form) form.action = `/submissions/${id}/verify`;
    if (titleEl) titleEl.textContent = fileTitle;
    if (userEl) userEl.textContent = userName;
    if (downloadBtn) downloadBtn.href = downloadUrl;
    if (notesEl) notesEl.value = notes || '';

    if (modal) modal.classList.add('active');
};

window.closeVerifyModal = function() {
    const modal = document.getElementById('verify-modal');
    if (modal) modal.classList.remove('active');
};

window.submitVerification = function(status) {
    const statusInput = document.getElementById('verify-status-input');
    const form = document.getElementById('verify-submission-form');
    if (statusInput) statusInput.value = status;
    if (form) form.submit();
};

// Filter Status di Tabel Dashboard (Opsi C)
window.filterStatusTable = function(type, btnElement) {
    const rows = document.querySelectorAll('.submission-row');
    const filterBtns = document.querySelectorAll('.status-filters .filter-btn');
    filterBtns.forEach(b => b.classList.remove('active'));
    if (btnElement) btnElement.classList.add('active');

    rows.forEach(r => {
        if (type === 'all' || r.getAttribute('data-status') === type) {
            r.style.display = '';
        } else {
            r.style.display = 'none';
        }
    });
};

// Ekspor Tabel Rekap ke File Excel (SheetJS)
window.exportReportsTableToExcel = function() {
    const table = document.getElementById('reports-data-table');
    if (!table || typeof XLSX === 'undefined') {
        alert('Tabel laporan tidak ditemukan atau SheetJS belum siap.');
        return;
    }
    const wb = XLSX.utils.table_to_book(table, { sheet: "Rekap_Kepatuhan" });
    XLSX.writeFile(wb, `Rekap_Kepatuhan_HokBen_${new Date().toISOString().slice(0,10)}.xlsx`);
};

// Multi-month Google Sheets Serah Terima
window.activeSerahTerimaMonth = 'JANUARI';
window.serahTerimaSheets = {
    'JANUARI': { id: '19S0wZPXQ4T_u2DMtrUyLXhSpLfUe0tjr', gid: '285912838' },
    'FEBRUARI': { id: '1kpxuK-JXa-_lsZvBuNgeI6oFBYgjwGju', gid: '598924100' },
    'MARET': { id: '1zNxrQc1z02yEXmCmvuvGAkkGlJcdwDPH', gid: '1005625293' },
    'APRIL': { id: '1Cvt2EkqE2Y6s1Bh6n1VdAeJhj5nN9JKi', gid: '598924100' },
    'MEI': { id: '1QnIiLqjlukWFNdvNC9EpV9yokE2AAZK2', gid: '' },
    'JUNI': { id: '1SCO4kCpVMTCaMSOT8O2ITeLvU7H4IP8q', gid: '1552507215' },
    'JULI': { id: '1CgqNafwEk3A32l3eiwcf8h4wO93sUg6h', gid: '1185985398' },
    'AGUSTUS': { id: '1kpxuK-JXa-_lsZvBuNgeI6oFBYgjwGju', gid: '1504484096' }
};

window.switchSerahTerimaTab = function(bulan) {
    window.activeSerahTerimaMonth = bulan;
    const titleEl = document.getElementById('serah-terima-title');
    const iframeEl = document.getElementById('serah-terima-iframe');
    const externalLinkEl = document.getElementById('serah-terima-external-link');
    const analysisEl = document.getElementById('serah-terima-analysis-result');
    
    if (analysisEl) {
        analysisEl.style.display = 'none';
        analysisEl.innerHTML = '';
    }

    if(titleEl) {
        titleEl.textContent = 'Data Serah Terima - ' + bulan;
    }
    
    const sheetLinks = window.serahTerimaSheets;

    if(sheetLinks[bulan]) {
        let embedUrl = `https://docs.google.com/spreadsheets/d/${sheetLinks[bulan].id}/htmlembed?widget=true&headers=false`;
        let editUrl = `https://docs.google.com/spreadsheets/d/${sheetLinks[bulan].id}/edit`;
        
        if (sheetLinks[bulan].gid) {
            embedUrl += `&gid=${sheetLinks[bulan].gid}`;
            editUrl += `?gid=${sheetLinks[bulan].gid}`;
        }
        
        if(iframeEl) iframeEl.src = embedUrl;
        if(externalLinkEl) externalLinkEl.href = editUrl;
    }
    
    const tabs = document.querySelectorAll('#view-serah-terima .tab-btn');
    tabs.forEach(tab => {
        if(tab.textContent.toUpperCase() === bulan) {
            tab.classList.add('active');
            tab.style.background = 'var(--primary)';
            tab.style.color = 'white';
            tab.style.fontWeight = '600';
        } else {
            tab.classList.remove('active');
            tab.style.background = 'rgba(255,255,255,0.1)';
            tab.style.color = 'var(--text-color)';
            tab.style.fontWeight = 'normal';
        }
    });
};

// Notification Toggle & Actions
window.toggleNotifications = function(e) {
    if(e) e.stopPropagation();
    const dropdown = document.getElementById('notifDropdown');
    if(dropdown) {
        dropdown.classList.toggle('show');
    }
};

window.markAllNotificationsAsRead = function() {
    const unreadItems = document.querySelectorAll('.notif-item.unread');
    unreadItems.forEach(item => item.classList.remove('unread'));
    
    const badge = document.getElementById('notifBadge');
    if(badge) {
        badge.style.display = 'none';
    }
};

window.handleNotifClick = function(targetSectionId) {
    const dropdown = document.getElementById('notifDropdown');
    if(dropdown) {
        dropdown.classList.remove('show');
    }

    const targetLink = document.querySelector(`.nav-link[data-target="${targetSectionId}"]`);
    if(targetLink) {
        targetLink.click();
    }
};

document.addEventListener('click', (e) => {
    const dropdown = document.getElementById('notifDropdown');
    const notifBtn = document.getElementById('notifBtn');
    if(dropdown && dropdown.classList.contains('show')) {
        if(!dropdown.contains(e.target) && !notifBtn.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    }
});
