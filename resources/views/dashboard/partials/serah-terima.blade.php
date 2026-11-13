<!-- Serah Terima Sales & Modal Harian View -->
<section id="view-serah-terima" class="view-section hidden">
    <div class="panel-header mb-4" style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 8px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div>
            <h2>SERAH TERIMA SALES DAN MODAL HARIAN</h2>
            <p class="sub-text m-0">Form pencatatan serah terima sales dan modal kasir harian.</p>
        </div>
        <button type="button"
                onclick="toggleUploadPanel('upload-serah-terima')"
                style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px;
                       background:rgba(16,185,129,0.12); border:1px solid rgba(16,185,129,0.3);
                       border-radius:8px; color:#10b981; font-size:0.84rem; font-weight:700; cursor:pointer;">
            <i class="ti ti-cash-banknote"></i> Upload Bukti Serah Terima
        </button>
    </div>

    {{-- Upload Panel Serah Terima --}}
    @include('dashboard.partials.upload-panel', [
        'panelId'   => 'upload-serah-terima',
        'title'     => 'Upload Bukti Serah Terima Sales & Modal',
        'subtitle'  => 'Upload form serah terima kasir harian (.xlsx/.csv) untuk disimpan & diverifikasi Leader.',
        'icon'      => 'ti-cash-banknote',
        'iconColor' => '#10b981',
        'moduleId'  => $modules->first()?->id ?? 1,
        'noteText'  => 'Form serah terima sales dan modal harian',
    ])
    
    <!-- Tabs -->
    <div class="tabs-container mb-4" style="display: flex; gap: 8px; overflow-x: auto; padding-bottom: 8px; border-bottom: 1px solid var(--border-color);">
        <button class="tab-btn active" onclick="switchSerahTerimaTab('JANUARI')" style="padding: 10px 20px; border: none; border-radius: 8px; background: var(--primary); color: white; cursor: pointer; font-weight: 600;">Januari</button>
        <button class="tab-btn" onclick="switchSerahTerimaTab('FEBRUARI')" style="padding: 10px 20px; border: none; border-radius: 8px; background: rgba(255,255,255,0.1); color: var(--text-color); cursor: pointer;">Februari</button>
        <button class="tab-btn" onclick="switchSerahTerimaTab('MARET')" style="padding: 10px 20px; border: none; border-radius: 8px; background: rgba(255,255,255,0.1); color: var(--text-color); cursor: pointer;">Maret</button>
        <button class="tab-btn" onclick="switchSerahTerimaTab('APRIL')" style="padding: 10px 20px; border: none; border-radius: 8px; background: rgba(255,255,255,0.1); color: var(--text-color); cursor: pointer;">April</button>
        <button class="tab-btn" onclick="switchSerahTerimaTab('MEI')" style="padding: 10px 20px; border: none; border-radius: 8px; background: rgba(255,255,255,0.1); color: var(--text-color); cursor: pointer;">Mei</button>
        <button class="tab-btn" onclick="switchSerahTerimaTab('JUNI')" style="padding: 10px 20px; border: none; border-radius: 8px; background: rgba(255,255,255,0.1); color: var(--text-color); cursor: pointer;">Juni</button>
        <button class="tab-btn" onclick="switchSerahTerimaTab('JULI')" style="padding: 10px 20px; border: none; border-radius: 8px; background: rgba(255,255,255,0.1); color: var(--text-color); cursor: pointer;">Juli</button>
        <button class="tab-btn" onclick="switchSerahTerimaTab('AGUSTUS')" style="padding: 10px 20px; border: none; border-radius: 8px; background: rgba(255,255,255,0.1); color: var(--text-color); cursor: pointer;">Agustus</button>
    </div>

    <div class="glass-panel" id="serah-terima-content" style="padding: 0; overflow: hidden; display: flex; flex-direction: column; height: 75vh;">
        <div style="padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.3);">
            <h3 id="serah-terima-title" style="margin: 0;">Data Serah Terima - JANUARI</h3>
            <div style="display: flex; gap: 10px;">
                <button class="btn-success" onclick="fetchSerahTerimaData(this)" style="padding: 6px 12px; font-size: 13px;"><i class="ti ti-refresh"></i> Sync & Analisa Data</button>
                <a id="serah-terima-external-link" href="https://docs.google.com/spreadsheets/d/19S0wZPXQ4T_u2DMtrUyLXhSpLfUe0tjr/edit?gid=285912838" target="_blank" class="btn-outline" style="text-decoration: none; padding: 6px 12px; font-size: 13px;">
                    <i class="ti ti-external-link"></i> Buka di Tab Baru
                </a>
            </div>
        </div>
        
        <!-- Tempat Munculnya Analisa Data Kosong Serah Terima -->
        <div id="serah-terima-analysis-result" style="display: none; padding: 15px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); max-height: 250px; overflow-y: auto;"></div>

        <div style="flex-grow: 1; position: relative;">
            <iframe id="serah-terima-iframe" src="https://docs.google.com/spreadsheets/d/19S0wZPXQ4T_u2DMtrUyLXhSpLfUe0tjr/htmlembed?widget=true&headers=false&gid=285912838" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;"></iframe>
        </div>
    </div>
</section>
