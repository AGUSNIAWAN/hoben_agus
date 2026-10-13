<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kanban Task Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .kanban-board {
            display: flex;
            gap: 1.5rem;
            padding: 1rem;
            overflow-x: auto;
            align-items: flex-start;
        }
        .kanban-column {
            background-color: #f4f5f7;
            border-radius: 8px;
            width: 300px;
            min-width: 300px;
            padding: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .kanban-column-header {
            font-weight: 600;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e1e4e8;
            display: flex;
            justify-content: space-between;
        }
        .kanban-items {
            min-height: 200px;
        }
        .kanban-item {
            background: white;
            padding: 1rem;
            border-radius: 6px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
            cursor: grab;
            border-left: 4px solid transparent;
        }
        .kanban-item:active {
            cursor: grabbing;
        }
        .status-todo { border-left-color: #6c757d; }
        .status-in_progress { border-left-color: #0d6efd; }
        .status-review { border-left-color: #ffc107; }
        .status-done { border-left-color: #198754; }
        
        .kanban-item-title {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        .kanban-item-meta {
            font-size: 0.8rem;
            color: #6c757d;
            display: flex;
            justify-content: space-between;
        }
        .btn-add-task {
            background: #0d6efd;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            margin-bottom: 1rem;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            align-items: center; justify-content: center;
        }
        .modal.active { display: flex; }
        .modal-content {
            background: white; padding: 2rem; border-radius: 8px; width: 400px;
        }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;
        }
        .btn-close-modal { background: #6c757d; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="logo-area">
                <i class="ti ti-hexagon-filled"></i>
                <h2>OpsTrack</h2>
            </div>
            
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="ti ti-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('tasks.index') }}" class="nav-link active">
                        <i class="ti ti-layout-kanban"></i>
                        <span>Task Kanban</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <div class="page-title">
                    <h1>Kanban Task Management</h1>
                    <p>Track your team's workflow and tasks</p>
                </div>
            </header>

            <div class="content-wrapper">
                @if(session('success'))
                    <div style="background:#d1e7dd; color:#0f5132; padding:1rem; border-radius:4px; margin-bottom:1rem;">
                        {{ session('success') }}
                    </div>
                @endif

                <button class="btn-add-task" onclick="document.getElementById('taskModal').classList.add('active')">
                    <i class="ti ti-plus"></i> Tambah Task Baru
                </button>

                <div class="kanban-board">
                    @php
                        $columns = [
                            'todo' => 'To Do',
                            'in_progress' => 'In Progress',
                            'review' => 'Review',
                            'done' => 'Done'
                        ];
                    @endphp

                    @foreach($columns as $status => $label)
                    <div class="kanban-column" data-status="{{ $status }}">
                        <div class="kanban-column-header">
                            <span>{{ $label }}</span>
                            <span style="background: #ddd; padding: 2px 8px; border-radius: 12px; font-size: 0.8rem;">
                                {{ isset($tasks[$status]) ? count($tasks[$status]) : 0 }}
                            </span>
                        </div>
                        <div class="kanban-items" ondragover="allowDrop(event)" ondrop="drop(event, '{{ $status }}')">
                            @if(isset($tasks[$status]))
                                @foreach($tasks[$status] as $task)
                                <div class="kanban-item status-{{ $status }}" draggable="true" ondragstart="drag(event, {{ $task->id }})" id="task-{{ $task->id }}">
                                    <div class="kanban-item-title">{{ $task->title }}</div>
                                    @if($task->description)
                                        <div style="font-size: 0.85rem; color: #555; margin-bottom: 0.5rem;">{{ Str::limit($task->description, 50) }}</div>
                                    @endif
                                    <div class="kanban-item-meta">
                                        <span><i class="ti ti-user"></i> {{ $task->user ? $task->user->name : 'Unassigned' }}</span>
                                        @if($task->deadline)
                                        <span style="{{ \Carbon\Carbon::parse($task->deadline)->isPast() && $status != 'done' ? 'color:red;' : '' }}">
                                            <i class="ti ti-clock"></i> {{ \Carbon\Carbon::parse($task->deadline)->format('d M') }}
                                        </span>
                                        @endif
                                    </div>
                                    <div style="margin-top: 10px; text-align: right;">
                                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Hapus task ini?');" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background:none; border:none; color:red; cursor:pointer;"><i class="ti ti-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </main>
    </div>

    <!-- Modal for New Task -->
    <div class="modal" id="taskModal">
        <div class="modal-content">
            <h3 style="margin-bottom:1rem;">Tambah Task Baru</h3>
            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Judul Pekerjaan</label>
                    <input type="text" name="title" required>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Tugaskan Kepada</label>
                    <select name="user_id" required>
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Deadline (Opsional)</label>
                    <input type="datetime-local" name="deadline">
                </div>
                <div style="text-align: right; margin-top: 1rem;">
                    <button type="button" class="btn-close-modal" onclick="document.getElementById('taskModal').classList.remove('active')">Batal</button>
                    <button type="submit" style="background: #0d6efd; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; margin-left: 0.5rem;">Simpan Task</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function allowDrop(ev) {
            ev.preventDefault();
        }

        function drag(ev, id) {
            ev.dataTransfer.setData("text", id);
        }

        function drop(ev, status) {
            ev.preventDefault();
            var id = ev.dataTransfer.getData("text");
            var item = document.getElementById('task-' + id);
            
            // Append visual element to new column
            var dropzone = ev.target.closest('.kanban-items');
            if (dropzone) {
                dropzone.appendChild(item);
                item.className = 'kanban-item status-' + status;

                // Update backend via AJAX
                updateTaskStatus(id, status);
            }
        }

        function updateTaskStatus(taskId, newStatus) {
            fetch(`/tasks/${taskId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if(!data.success) {
                    alert('Gagal mengupdate status!');
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.location.reload();
            });
        }
    </script>
</body>
</html>
