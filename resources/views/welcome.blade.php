<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HokBen OpsTrack - Monitoring Operasional & Kepatuhan</title>
    <link rel="icon" type="image/png" href="{{ asset('hokben-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}?v={{ time() }}">
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        // Early Theme Initialization to prevent flicker
        (function() {
            const savedTheme = localStorage.getItem('hokbenOpsTrackTheme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="logo-area">
                <img src="{{ asset('hokben-logo-clean.svg') }}" alt="HokBen Official Logo" class="brand-img" onerror="this.src='{{ asset('hokben-icon.png') }}'">
                <div class="logo-text-group">
                    <span class="brand-title">Hok<span>Ben</span></span>
                    <span class="brand-subtitle">Ops Track Area 19</span>
                </div>
            </div>

            <!-- Store Location Badge -->
            <div class="sidebar-location-badge">
                <span class="pulsing-dot-green"></span>
                <span>Area 19 • Palembang Icon</span>
            </div>

            <nav class="nav-menu">
                <a href="#" class="nav-link active" data-target="dashboard">
                    <i class="ti ti-layout-dashboard"></i> Dashboard Leader
                </a>
                <a href="#" class="nav-link" data-target="sales">
                    <i class="ti ti-chart-bar"></i> Monitoring Sales
                </a>
                <a href="#" class="nav-link" data-target="staff-portal">
                    <i class="ti ti-users"></i> Portal Staff
                </a>
                <a href="#" class="nav-link" data-target="reports">
                    <i class="ti ti-report-analytics"></i> Rekap & Laporan
                </a>
                <a href="#" class="nav-link" data-target="serah-terima">
                    <i class="ti ti-cash-banknote"></i> Serah Terima Sales & Modal Harian
                </a>
                <a href="#" class="nav-link" data-target="monitoring-preshift-area19">
                    <i class="ti ti-checklist"></i> MONITORING PRE-SHIFT ONLINE AREA 19
                </a>
                <a href="#" class="nav-link" data-target="monitoring-kfm-helpdesk">
                    <i class="ti ti-headset"></i> MONITORING KFM DAN HELPDESK
                </a>
                <a href="#" class="nav-link" data-target="monitoring-kegiatan">
                    <i class="ti ti-layout-kanban"></i> MONITORING KEGIATAN KANBAN
                </a>
            </nav>

            <div class="user-profile" style="position: relative;">
                <div class="avatar">
                    <i class="ti ti-user-circle"></i>
                </div>
                <div class="user-info" style="flex-grow: 1;">
                    <p class="name">{{ Auth::check() ? Auth::user()->name : 'Guest User' }}</p>
                    <p class="role">{{ Auth::check() ? (Auth::user()->role ?? 'Store Leader') : 'Visitor' }}</p>
                </div>
                @if(Auth::check())
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" title="Logout" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 6px; font-size: 18px; border-radius: 6px; display: flex; align-items: center; justify-content: center; transition: background 0.2s;" onmouseover="this.style.background='rgba(239,68,68,0.1)'" onmouseout="this.style.background='none'">
                        <i class="ti ti-logout"></i>
                    </button>
                </form>
                @endif
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <!-- Topbar -->
            <header class="topbar">
                <div class="page-title">
                    <div class="topbar-store-chip">
                        <i class="ti ti-building-store"></i>
                        <span>Hb Palembang Icon • Regional 5 Sumatera</span>
                    </div>
                    <h1 id="current-page-title">Dashboard Monitoring</h1>
                    <p class="date" id="current-date"></p>
                </div>
                <div class="topbar-actions">
                    <form method="GET" action="{{ route('dashboard') }}" id="periodForm" style="margin: 0;">
                        <input type="month" name="period" value="{{ $currentPeriod }}" class="styled-input period-selector" style="padding: 8px 14px; height: 42px; border-radius: 10px;" onchange="document.getElementById('periodForm').submit()">
                    </form>
                    
                    <button type="button" class="btn-quick-action" onclick="document.querySelector('[data-target=\'staff-portal\']').click()" title="Buka Portal Staff">
                        <i class="ti ti-file-upload"></i>
                        <span>Upload Form</span>
                    </button>

                    <!-- Theme Toggle (Light / Dark) -->
                    <button class="icon-btn theme-toggle-btn" id="themeToggleBtn" aria-label="Toggle Theme" onclick="toggleDashboardTheme()" title="Ganti Tema (Terang/Gelap)">
                        <i class="ti ti-moon" id="themeIcon"></i>
                    </button>

                    <!-- Notification Bell with Interactive Dropdown -->
                    <div class="notif-dropdown-wrapper">
                        <button class="icon-btn" id="notifBtn" aria-label="Notifications" onclick="toggleNotifications(event)">
                            <i class="ti ti-bell"></i>
                            <span class="badge" id="notifBadge">3</span>
                        </button>

                        <div class="notif-dropdown" id="notifDropdown">
                            <div class="notif-header">
                                <h4><i class="ti ti-bell"></i> Pemberitahuan</h4>
                                <button class="mark-read-btn" onclick="markAllNotificationsAsRead()">Tandai dibaca</button>
                            </div>
                            <ul class="notif-list">
                                <li class="notif-item unread" onclick="handleNotifClick('serah-terima')">
                                    <div class="notif-icon alert">
                                        <i class="ti ti-cash-banknote"></i>
                                    </div>
                                    <div class="notif-content">
                                        <p class="notif-title">Serah Terima Kasir Belum Lengkap</p>
                                        <p class="notif-desc">Terdapat 3 sel kosong pada laporan Serah Terima Sales & Modal Harian.</p>
                                        <span class="notif-time">15 menit yang lalu</span>
                                    </div>
                                </li>
                                <li class="notif-item unread" onclick="handleNotifClick('monitoring-kfm-helpdesk')">
                                    <div class="notif-icon warning">
                                        <i class="ti ti-headset"></i>
                                    </div>
                                    <div class="notif-content">
                                        <p class="notif-title">Tiket Helpdesk KFM Baru</p>
                                        <p class="notif-desc">Status mesin fryer Hb Palembang Icon perlu penanganan teknisi.</p>
                                        <span class="notif-time">1 jam yang lalu</span>
                                    </div>
                                </li>
                                <li class="notif-item unread" onclick="handleNotifClick('monitoring-preshift-area19')">
                                    <div class="notif-icon info">
                                        <i class="ti ti-checklist"></i>
                                    </div>
                                    <div class="notif-content">
                                        <p class="notif-title">Pre-Shift Checklist Update</p>
                                        <p class="notif-desc">File Kesiapan Preshift Store Sumatera Oktober telah diperbarui.</p>
                                        <span class="notif-time">3 jam yang lalu</span>
                                    </div>
                                </li>
                            </ul>
                            <div class="notif-footer">
                                <a href="#" onclick="markAllNotificationsAsRead(); return false;">Semua notifikasi telah ditampilkan</a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Dashboard Partials -->
            @include('dashboard.partials.metrics')
            @include('dashboard.partials.staff-portal')
            @include('dashboard.partials.reports')
            @include('dashboard.partials.sales-monitoring')
            @include('dashboard.partials.serah-terima')
            @include('dashboard.partials.preshift-monitoring')
            @include('dashboard.partials.kfm-helpdesk')
            @include('dashboard.partials.kanban')

        </main>
    </div>
    
    <!-- Modals Partial -->
    @include('dashboard.partials.modals')

    <!-- SheetJS Library for Client-side Excel Parsing -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <!-- Route & Config Injection for External JS Files -->
    <script>
        window.opsTrackRoutes = {
            syncArea19: "{{ route('api.sync.area19') }}",
            syncSerahTerima: "{{ route('api.sync.serah_terima') }}",
            syncKfmHelpdesk: "{{ route('api.sync.kfm_helpdesk') }}"
        };

        // Theme Toggle Functionality
        function toggleDashboardTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('hokbenOpsTrackTheme', newTheme);
            updateThemeIcon(newTheme);
        }

        function updateThemeIcon(theme) {
            const icon = document.getElementById('themeIcon');
            if (!icon) return;
            if (theme === 'dark') {
                icon.className = 'ti ti-sun';
            } else {
                icon.className = 'ti ti-moon';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            updateThemeIcon(currentTheme);
        });
    </script>

    <!-- Application Core & Feature Modules -->
    <script src="{{ asset('app.js') }}"></script>
    <script src="{{ asset('js/sync-analysis.js') }}"></script>
    <script src="{{ asset('js/kanban.js') }}"></script>
</body>
</html>
