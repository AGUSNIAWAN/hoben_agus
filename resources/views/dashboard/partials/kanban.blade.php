<!-- Monitoring Kegiatan Kanban Section -->
<section id="view-monitoring-kegiatan" class="view-section hidden">
    <div class="content-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="margin: 0; font-size: 1.5rem; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 10px;">
                <i class="ti ti-layout-kanban" style="color: var(--hokben-yellow);"></i> Monitoring Kegiatan Operasional
            </h2>
            <p style="margin: 4px 0 0 0; color: #94a3b8; font-size: 0.9rem;">
                Pantau progres penugasan harian outlet dan alur kerja staf HokBen Area 19
            </p>
        </div>
        <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <div style="position: relative;">
                <i class="ti ti-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 1rem;"></i>
                <input type="text" id="kanban-search" placeholder="Cari kegiatan atau PIC..." 
                       oninput="filterKanbanCards(this.value)"
                       style="padding: 9px 14px 9px 36px; background: rgba(18, 24, 38, 0.7); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; color: #fff; font-size: 0.85rem; width: 220px; outline: none; transition: border 0.2s;"
                       onfocus="this.style.borderColor='var(--hokben-red)'"
                       onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
            </div>
            <button class="btn-primary" onclick="openTaskModal()" style="display: flex; align-items: center; gap: 8px; padding: 9px 18px; font-weight: 600; border-radius: 8px;">
                <i class="ti ti-plus"></i> Tambah Kegiatan Baru
            </button>
        </div>
    </div>

    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #10b981; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="ti ti-circle-check" style="font-size: 1.2rem;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @php
        $kanbanCols = [
            'todo' => ['label' => 'To Do / Belum Berjalan', 'desc' => 'Daftar rencana kegiatan baru', 'color' => 'todo', 'icon' => 'ti-list-check'],
            'in_progress' => ['label' => 'In Progress / Berjalan', 'desc' => 'Sedang dalam pengerjaan', 'color' => 'in_progress', 'icon' => 'ti-loader-2'],
            'review' => ['label' => 'Review / Verifikasi', 'desc' => 'Menunggu pengecekan Leader', 'color' => 'review', 'icon' => 'ti-eye-check'],
            'done' => ['label' => 'Done / Selesai', 'desc' => 'Pekerjaan tuntas', 'color' => 'done', 'icon' => 'ti-circle-check']
        ];
    @endphp

    <!-- Quick Summary KPI Cards -->
    <div class="kanban-stats-grid">
        @foreach($kanbanCols as $statusKey => $colMeta)
        <div class="kanban-stat-card">
            <div class="kanban-stat-icon {{ $colMeta['color'] }}">
                <i class="ti {{ $colMeta['icon'] }}"></i>
            </div>
            <div class="kanban-stat-info">
                <p class="num" id="stat-{{ $statusKey }}">{{ isset($tasks[$statusKey]) ? count($tasks[$statusKey]) : 0 }}</p>
                <p class="label">{{ $colMeta['label'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Kanban Board Grid -->
    <div class="kanban-board-container">
        @foreach($kanbanCols as $statusKey => $colMeta)
        <div class="kanban-board-column" data-status="{{ $statusKey }}">
            <div class="kanban-column-top">
                <div class="col-title">
                    <span class="col-indicator {{ $colMeta['color'] }}"></span>
                    <span>{{ $colMeta['label'] }}</span>
                </div>
                <span class="col-count" id="count-{{ $statusKey }}">
                    {{ isset($tasks[$statusKey]) ? count($tasks[$statusKey]) : 0 }}
                </span>
            </div>
            
            <div class="kanban-card-dropzone" ondragover="allowDrop(event)" ondragleave="dragLeave(event)" ondrop="drop(event, '{{ $statusKey }}')">
                @if(isset($tasks[$statusKey]) && count($tasks[$statusKey]) > 0)
                    @foreach($tasks[$statusKey] as $task)
                    <div class="kanban-activity-card card-{{ $statusKey }}" 
                         draggable="true" 
                         ondragstart="drag(event, {{ $task->id }})" 
                         id="task-{{ $task->id }}"
                         data-title="{{ strtolower($task->title) }}"
                         data-user="{{ strtolower($task->user ? $task->user->name : '') }}">
                        
                        <div class="card-header-row">
                            <h4 class="card-title">{{ $task->title }}</h4>
                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini?');" style="margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="card-delete-btn" title="Hapus Kegiatan">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </div>

                        @if($task->description)
                            <p class="card-desc">{{ Str::limit($task->description, 95) }}</p>
                        @endif

                        <div class="card-footer-row">
                            <div class="card-user-tag">
                                <i class="ti ti-user-circle"></i>
                                <span>{{ $task->user ? $task->user->name : 'Unassigned' }}</span>
                            </div>

                            @if($task->deadline)
                            @php
                                $isOverdue = \Carbon\Carbon::parse($task->deadline)->isPast() && $statusKey != 'done';
                            @endphp
                            <div class="card-deadline-tag {{ $isOverdue ? 'overdue' : '' }}" title="Target: {{ \Carbon\Carbon::parse($task->deadline)->format('d M Y, H:i') }}">
                                <i class="ti ti-calendar-time"></i>
                                <span>{{ \Carbon\Carbon::parse($task->deadline)->format('d M, H:i') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="kanban-empty-placeholder" style="text-align: center; padding: 35px 15px; border: 2px dashed rgba(255,255,255,0.08); border-radius: 12px; color: #64748b; font-size: 0.85rem;">
                        <i class="ti ti-inbox" style="font-size: 1.6rem; display: block; margin-bottom: 6px; opacity: 0.5;"></i>
                        <span>Belum ada kegiatan</span>
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</section>
