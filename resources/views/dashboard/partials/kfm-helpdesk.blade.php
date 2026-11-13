<!-- MONITORING KFM DAN HELPDESK View -->
<section id="view-monitoring-kfm-helpdesk" class="view-section hidden">
    <div class="panel-header mb-4" style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 8px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div>
            <h2>MONITORING KFM DAN HELPDESK</h2>
            <p class="sub-text m-0">Monitoring data perbaikan KFM & Tiket Helpdesk Maintenance.</p>
        </div>
        <button type="button"
                onclick="toggleUploadPanel('upload-kfm')"
                style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px;
                       background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.3);
                       border-radius:8px; color:#f87171; font-size:0.84rem; font-weight:700; cursor:pointer;">
            <i class="ti ti-tool"></i> Upload Laporan KFM / Helpdesk
        </button>
    </div>

    {{-- Upload Panel KFM Helpdesk --}}
    @include('dashboard.partials.upload-panel', [
        'panelId'   => 'upload-kfm',
        'title'     => 'Upload Laporan KFM & Tiket Helpdesk',
        'subtitle'  => 'Upload file data perbaikan mesin, checklist KFM, atau tiket helpdesk (.xlsx/.csv).',
        'icon'      => 'ti-headset',
        'iconColor' => '#f87171',
        'moduleId'  => $modules->first()?->id ?? 1,
        'noteText'  => 'Laporan monitoring KFM dan helpdesk',
    ])

    <div class="glass-panel" style="padding: 0; overflow: hidden; display: flex; flex-direction: column; height: 75vh; margin-top: 16px;">
        <div style="padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.3);">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="ti ti-headset"></i> Monitoring KFM dan Helpdesk
            </h3>
            <div style="display: flex; gap: 10px;">
                <button class="btn-success" onclick="fetchKfmData(this)" style="padding: 6px 12px; font-size: 13px;">
                    <i class="ti ti-refresh"></i> Sync & Analisa Data
                </button>
                <a href="https://docs.google.com/spreadsheets/d/1suQMBQyy_rX0pIFptMrqlrIN5GjdZTAT/edit?gid=891360253#gid=891360253" target="_blank" class="btn-outline" style="text-decoration: none; padding: 6px 12px; font-size: 13px;">
                    <i class="ti ti-brand-google-drive"></i> Buka Sheet Asli
                </a>
            </div>
        </div>
        
        <!-- Tempat Munculnya Analisa Data Kosong KFM -->
        <div id="kfm-analysis-result" style="display: none; padding: 15px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); max-height: 250px; overflow-y: auto;"></div>

        <div style="flex-grow: 1; position: relative;">
            <!-- Embed Google Sheets Iframe -->
            <iframe src="https://docs.google.com/spreadsheets/d/1suQMBQyy_rX0pIFptMrqlrIN5GjdZTAT/htmlembed?widget=true&headers=false&gid=891360253" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;"></iframe>
        </div>
    </div>
</section>
