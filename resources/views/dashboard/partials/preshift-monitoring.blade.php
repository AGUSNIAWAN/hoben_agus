<!-- MONITORING PRE-SHIFT ONLINE AREA 19 View -->
<section id="view-monitoring-preshift-area19" class="view-section hidden">
    <div class="panel-header mb-4" style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 8px;">
        <h2>MONITORING PRE-SHIFT ONLINE AREA 19</h2>
        <p class="sub-text m-0">Monitoring checklist pre-shift harian online Area 19 - Siswanto.</p>
    </div>

    <div class="glass-panel" style="padding: 0; overflow: hidden; display: flex; flex-direction: column; height: 75vh;">
        <div style="padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.3);">
            <h3 style="margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="ti ti-checklist"></i> Monitoring Pre-Shift Online Area 19
            </h3>
            <div style="display: flex; gap: 10px; align-items: center;">
                <input type="file" id="preshiftExcelFileInput" accept=".xlsx, .xls, .csv" style="display: none;" onchange="handlePreshiftExcelUpload(event)">
                <button class="btn-success" onclick="document.getElementById('preshiftExcelFileInput').click()" style="padding: 6px 12px; font-size: 13px;">
                    <i class="ti ti-file-analytics"></i> Upload & Analisa File Excel
                </button>
                <select id="preshiftSheetSelector" style="display: none; padding: 6px 12px; font-size: 13px; border-radius: 6px; background: rgba(255,255,255,0.1); color: var(--text-color); border: 1px solid var(--border-color);" onchange="renderSelectedPreshiftSheet()">
                </select>
                <a href="https://ekabogainti-my.sharepoint.com/:x:/g/personal/siswanto_hokben_co_id/IQBXHwMgD9J4Q67hhuTB5oIsAYnDSE84y7RCwzEn4YjfGQU?rtime=CaZQNS0g30g" target="_blank" class="btn-outline" style="text-decoration: none; padding: 6px 12px; font-size: 13px;">
                    <i class="ti ti-external-link"></i> Buka File Excel Online
                </a>
            </div>
        </div>
        
        <!-- Tempat Munculnya Analisa Data Kosong Pre-Shift -->
        <div id="preshift-analysis-result" style="display: none; padding: 15px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); max-height: 250px; overflow-y: auto;"></div>

        <div style="flex-grow: 1; position: relative;">
            <!-- Embed SharePoint Excel Online -->
            <iframe src="https://ekabogainti-my.sharepoint.com/:x:/g/personal/siswanto_hokben_co_id/IQBXHwMgD9J4Q67hhuTB5oIsAYnDSE84y7RCwzEn4YjfGQU?action=embedview&wdbipreview=true" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;"></iframe>
        </div>
    </div>
</section>
