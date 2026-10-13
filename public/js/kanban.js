/**
 * HokBen OpsTrack - Kanban Activities & Modal Module
 * Menangani interaksi drag-and-drop papan kanban, pencarian live, modal kegiatan, dan update status AJAX.
 */

// Modal Tambah Kegiatan
function openTaskModal() {
    const modal = document.getElementById('task-modal');
    if (modal) {
        modal.classList.add('active');
    }
}

function closeTaskModal() {
    const modal = document.getElementById('task-modal');
    if (modal) {
        modal.classList.remove('active');
    }
}

// Tutup modal kegiatan saat klik di luar kotak modal
window.addEventListener('click', function(e) {
    const modal = document.getElementById('task-modal');
    if (e.target === modal) {
        closeTaskModal();
    }
});

// Drag and Drop Handlers
function allowDrop(ev) {
    ev.preventDefault();
    const dropzone = ev.target.closest('.kanban-card-dropzone');
    if (dropzone) {
        dropzone.classList.add('drag-over');
    }
}

function dragLeave(ev) {
    const dropzone = ev.target.closest('.kanban-card-dropzone');
    if (dropzone) {
        dropzone.classList.remove('drag-over');
    }
}

function drag(ev, id) {
    ev.dataTransfer.setData("text/plain", id);
    const card = document.getElementById('task-' + id);
    if (card) {
        card.style.opacity = '0.4';
    }
}

document.addEventListener('dragend', function(ev) {
    if (ev.target.classList && ev.target.classList.contains('kanban-activity-card')) {
        ev.target.style.opacity = '1';
    }
    document.querySelectorAll('.kanban-card-dropzone').forEach(dz => dz.classList.remove('drag-over'));
});

function drop(ev, newStatus) {
    ev.preventDefault();
    document.querySelectorAll('.kanban-card-dropzone').forEach(dz => dz.classList.remove('drag-over'));
    
    const id = ev.dataTransfer.getData("text/plain");
    const card = document.getElementById('task-' + id);
    if (!card) return;

    const targetDropzone = ev.target.closest('.kanban-card-dropzone');
    if (!targetDropzone) return;

    const oldColumn = card.closest('.kanban-board-column');
    const oldStatus = oldColumn ? oldColumn.getAttribute('data-status') : null;

    if (oldStatus === newStatus) {
        card.style.opacity = '1';
        return;
    }

    // Hapus placeholder kosong jika ada di kolom tujuan
    const targetPlaceholder = targetDropzone.querySelector('.kanban-empty-placeholder');
    if (targetPlaceholder) {
        targetPlaceholder.remove();
    }

    // Pindahkan kartu ke kolom baru
    targetDropzone.appendChild(card);
    card.style.opacity = '1';

    // Cek jika kolom lama sekarang kosong
    if (oldColumn) {
        const oldDropzone = oldColumn.querySelector('.kanban-card-dropzone');
        if (oldDropzone && oldDropzone.querySelectorAll('.kanban-activity-card').length === 0) {
            oldDropzone.innerHTML = `
                <div class="kanban-empty-placeholder" style="text-align: center; padding: 35px 15px; border: 2px dashed rgba(255,255,255,0.08); border-radius: 12px; color: #64748b; font-size: 0.85rem;">
                    <i class="ti ti-inbox" style="font-size: 1.6rem; display: block; margin-bottom: 6px; opacity: 0.5;"></i>
                    <span>Belum ada kegiatan</span>
                </div>
            `;
        }
    }

    // Update kelas warna kartu
    card.className = `kanban-activity-card card-${newStatus}`;

    // Update counter badge kolom
    updateKanbanCounters();

    // Patch status ke database via backend API
    updateTaskStatus(id, newStatus);
}

function updateKanbanCounters() {
    ['todo', 'in_progress', 'review', 'done'].forEach(st => {
        const col = document.querySelector(`.kanban-board-column[data-status="${st}"]`);
        const badge = document.getElementById(`count-${st}`);
        const stat = document.getElementById(`stat-${st}`);
        if (col) {
            const count = col.querySelectorAll('.kanban-activity-card').length;
            if (badge) badge.textContent = count;
            if (stat) stat.textContent = count;
        }
    });
}

// Live Search Filter untuk Kegiatan Kanban
function filterKanbanCards(query) {
    const q = query.trim().toLowerCase();
    const cards = document.querySelectorAll('.kanban-activity-card');
    
    cards.forEach(card => {
        const title = card.getAttribute('data-title') || '';
        const user = card.getAttribute('data-user') || '';
        if (!q || title.includes(q) || user.includes(q)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Kirim perubahan status ke backend via AJAX PATCH
function updateTaskStatus(taskId, newStatus) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? 
                      document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

    fetch(`/tasks/${taskId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            console.error('Failed to update task status in backend');
        }
    })
    .catch(err => {
        console.error('Error updating task status:', err);
    });
}
