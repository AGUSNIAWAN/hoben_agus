document.addEventListener('DOMContentLoaded', () => {
    // Current Date Setup
    const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('current-date').textContent = new Date().toLocaleDateString('id-ID', dateOptions);

    // Navigation Logic
    const navLinks = document.querySelectorAll('.nav-link');
    const views = document.querySelectorAll('.view-section');
    const pageTitle = document.getElementById('current-page-title');

    const titles = {
        'dashboard': 'Dashboard Monitoring',
        'staff-portal': 'Portal Karyawan',
        'reports': 'Rekap & Laporan'
    };

    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = link.getAttribute('data-target');
            
            // Update Active Link
            navLinks.forEach(l => l.classList.remove('active'));
            link.classList.add('active');

            // Update Title
            pageTitle.textContent = titles[targetId];

            // Switch View
            views.forEach(view => {
                view.classList.remove('active');
                view.classList.add('hidden');
                if(view.id === `view-${targetId}`) {
                    view.classList.remove('hidden');
                    // Small delay to trigger animation
                    setTimeout(() => view.classList.add('active'), 10);
                }
            });
        });
    });

    // Dashboard Mock Data
    const mockStaffData = [
        { name: 'Siti Aminah', div: 'Front Counter', shift: 'Opening', status: 'Lengkap', class: 'success' },
        { name: 'Ahmad Fauzi', div: 'Kitchen', shift: 'Middle', status: 'Perlu Review', class: 'warning' },
        { name: 'Budi (Kasir)', div: 'Cashier', shift: 'Closing', status: 'Belum Upload', class: 'danger' }
    ];

    const tbody = document.getElementById('dashboard-table-body');
    mockStaffData.forEach(staff => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${staff.name}</td>
            <td>${staff.div}</td>
            <td>${staff.shift}</td>
            <td><span class="badge-status ${staff.class}">${staff.status}</span></td>
            <td>
                ${staff.status === 'Perlu Review' 
                    ? `<button class="btn-primary" onclick="openVerifyModal()"><i class="ti ti-eye"></i> Review</button>`
                    : `<button class="btn-outline" disabled><i class="ti ti-minus"></i></button>`
                }
            </td>
        `;
        tbody.appendChild(tr);
    });

    const verifyList = document.getElementById('verification-list');
    verifyList.innerHTML = `
        <div class="verify-item">
            <div class="v-info">
                <h4>Form QC Kitchen (Middle)</h4>
                <p>Oleh: Ahmad Fauzi</p>
            </div>
            <button class="btn-primary" onclick="openVerifyModal()"><i class="ti ti-check"></i> Cek</button>
        </div>
    `;

    // Staff Portal Logic
    const shiftRadios = document.querySelectorAll('input[name="shift"]');
    const checklistUl = document.getElementById('daily-checklist');
    const shiftLabel = document.getElementById('active-shift-label');

    const checklists = {
        'Opening': ['Cek kebersihan area depan', 'Nyalakan sistem POS', 'Siapkan uang kembalian', 'Cek stok bahan baku awal'],
        'Middle': ['Cek kelengkapan stok bahan', 'Buang sampah dapur', 'Update log suhu chiller', 'Laporan pergantian kasir'],
        'Closing': ['Hitung omzet harian (Setoran)', 'Matikan semua alat listrik', 'Bersihkan area kitchen & lantai', 'Upload laporan Excel closing']
    };

    function renderChecklist(shift) {
        shiftLabel.textContent = shift;
        checklistUl.innerHTML = '';
        checklists[shift].forEach((task, idx) => {
            const li = document.createElement('li');
            li.innerHTML = `
                <input type="checkbox" id="task-${idx}">
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

    // Init opening shift checklist
    renderChecklist('Opening');

    // Drag and Drop Upload logic
    const uploadZone = document.getElementById('upload-zone');
    const fileInput = document.getElementById('file-input');

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
            handleFile(e.dataTransfer.files[0]);
        }
    });

    fileInput.addEventListener('change', () => {
        if(fileInput.files.length) {
            handleFile(fileInput.files[0]);
        }
    });

    function handleFile(file) {
        alert(`File "${file.name}" berhasil dipilih!`);
    }

    // Modal Logic
    const modal = document.getElementById('verify-modal');
    const closeModalBtns = document.querySelectorAll('.close-modal');

    window.openVerifyModal = () => {
        modal.classList.add('active');
    }

    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            modal.classList.remove('active');
        });
    });

    window.addEventListener('click', (e) => {
        if(e.target === modal) {
            modal.classList.remove('active');
        }
    });
});
