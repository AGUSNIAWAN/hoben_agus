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

// Modal Edit Kegiatan
function openEditTaskModal(id, title, desc, userId, priority, status, deadline) {
    const modal = document.getElementById('edit-task-modal');
    const form = document.getElementById('edit-task-form');
    if (!modal) return;

    if (form) form.action = `/tasks/${id}`;

    const idInput = document.getElementById('edit-task-id');
    const titleInput = document.getElementById('edit-task-title');
    const descInput = document.getElementById('edit-task-desc');
    const userInput = document.getElementById('edit-task-user');
    const priorityInput = document.getElementById('edit-task-priority');
    const statusInput = document.getElementById('edit-task-status');
    const deadlineInput = document.getElementById('edit-task-deadline');

    if (idInput) idInput.value = id;
    if (titleInput) titleInput.value = title || '';
    if (descInput) descInput.value = desc || '';
    if (userInput && userId) userInput.value = userId;
    if (priorityInput && priority) priorityInput.value = priority;
    if (statusInput && status) statusInput.value = status;
    if (deadlineInput) deadlineInput.value = deadline || '';

    modal.classList.add('active');
}

function closeEditTaskModal() {
    const modal = document.getElementById('edit-task-modal');
    if (modal) {
        modal.classList.remove('active');
    }
}

// Tutup modal kegiatan saat klik di luar kotak modal
window.addEventListener('click', function(e) {
    const taskModal = document.getElementById('task-modal');
    const editModal = document.getElementById('edit-task-modal');
    if (e.target === taskModal) {
        closeTaskModal();
    }
    if (e.target === editModal) {
        closeEditTaskModal();
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

// Live Search & Priority Filter untuk Kegiatan Kanban
function filterKanbanCards(query) {
    const q = (query || '').trim().toLowerCase();
    const prioritySelect = document.getElementById('kanban-priority-filter');
    const pFilter = prioritySelect ? prioritySelect.value : 'all';

    const cards = document.querySelectorAll('.kanban-activity-card');
    
    cards.forEach(card => {
        const title = card.getAttribute('data-title') || '';
        const user = card.getAttribute('data-user') || '';
        const priority = card.getAttribute('data-priority') || 'medium';

        const matchQuery = !q || title.includes(q) || user.includes(q);
        const matchPriority = pFilter === 'all' || priority === pFilter;

        if (matchQuery && matchPriority) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Hapus Kegiatan Kanban via AJAX tanpa reload halaman
function deleteTaskViaAjax(taskId) {
    if (!confirm('Apakah Anda yakin ingin menghapus kegiatan operasional ini?')) {
        return;
    }

    const card = document.getElementById('task-' + taskId);
    if (card) {
        card.style.opacity = '0';
        card.style.transform = 'scale(0.85)';
        card.style.transition = 'all 0.25s ease';
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? 
                      document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

    fetch(`/tasks/${taskId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (card) {
                const column = card.closest('.kanban-board-column');
                card.remove();
                updateKanbanCounters();

                if (column) {
                    const dropzone = column.querySelector('.kanban-card-dropzone');
                    if (dropzone && dropzone.querySelectorAll('.kanban-activity-card').length === 0) {
                        dropzone.innerHTML = `
                            <div class="kanban-empty-placeholder" style="text-align: center; padding: 35px 15px; border: 2px dashed rgba(255,255,255,0.08); border-radius: 12px; color: #64748b; font-size: 0.85rem;">
                                <i class="ti ti-inbox" style="font-size: 1.6rem; display: block; margin-bottom: 6px; opacity: 0.5;"></i>
                                <span>Belum ada kegiatan</span>
                            </div>
                        `;
                    }
                }
            }
        } else {
            alert('Gagal menghapus kegiatan: ' + (data.message || 'Terjadi kesalahan'));
            if (card) {
                card.style.opacity = '1';
                card.style.transform = 'none';
            }
        }
    })
    .catch(err => {
        console.error('Error deleting task:', err);
        alert('Terjadi kesalahan jaringan saat menghapus kegiatan.');
        if (card) {
            card.style.opacity = '1';
            card.style.transform = 'none';
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
