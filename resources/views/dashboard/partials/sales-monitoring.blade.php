<!-- Sales Monitoring View -->
<section id="view-sales" class="view-section hidden">
    <div class="panel-header mb-4" style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2>AKTUAL SALES HARIAN STORE (Tracking Data Kosong)</h2>
            <p class="sub-text m-0">Upload file Excel dari SharePoint untuk melihat kolom yang belum diisi.</p>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <select id="sheetSelector" class="styled-select" style="display: none; min-width: 200px; padding: 8px;" onchange="renderSelectedSheet()"></select>
            <input type="file" id="localExcelFile" accept=".xlsx, .xls, .csv" style="display: none;" onchange="handleExcelUpload(event)">
            <button class="btn-success" onclick="document.getElementById('localExcelFile').click()"><i class="ti ti-upload"></i> Upload & Cek File Excel</button>
        </div>
    </div>

    <div class="glass-panel mb-5" style="padding: 0; overflow: hidden;">
        <div class="excel-container" style="max-height: 500px; overflow-y: auto;">
            <table class="excel-table" id="aktual-sales-table">
                <thead>
                    <tr class="header-tier-1">
                        <th rowspan="2" class="border-right" style="vertical-align: middle;">NO</th>
                        <th rowspan="2" class="border-right" style="vertical-align: middle;"># STORE</th>
                        <th rowspan="2" class="border-right" style="vertical-align: middle;">STORE</th>
                        <th rowspan="2" class="border-right" style="vertical-align: middle;">OPENING DATE</th>
                        <th rowspan="2" class="border-right" style="vertical-align: middle;">TYPE</th>
                        <th colspan="31" style="text-align: center; background-color: #d1d5db; color: black; border-bottom: 1px solid #9ca3af;">AKTUAL SALES TANGGAL</th>
                    </tr>
                    <tr class="header-tier-2" style="background-color: #f3f4f6; color: black;">
                        @for ($i = 1; $i <= 31; $i++)
                            <th style="min-width: 60px; border-right: 1px solid #9ca3af;">{{ $i }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    <!-- AREA 17 -->
                    <tr style="background-color: #e2e8f0; font-weight: bold; color: black;">
                        <td colspan="5" class="text-left border-right" style="padding-left: 10px;">AREA 17</td>
                        @for ($i = 1; $i <= 31; $i++) <td style="border-right: 1px solid #9ca3af;"></td> @endfor
                    </tr>
                    <tr class="data-row">
                        <td class="border-right">1</td><td class="border-right">A09</td><td class="border-right text-left" style="padding-left: 5px;">SUN PLAZA MEDAN</td><td class="border-right">04-Okt-2023</td><td class="border-right">Mall</td>
                        <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true">10.054.455</td>
                        <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true">13.054.554</td>
                        <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true">15.420.515</td>
                        <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true"></td>
                        @for ($i = 5; $i <= 31; $i++) <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true"></td> @endfor
                    </tr>
                    <tr class="data-row">
                        <td class="border-right">2</td><td class="border-right">A10</td><td class="border-right text-left" style="padding-left: 5px;">BING KOU MEDAN</td><td class="border-right">20-Des-2023</td><td class="border-right">Stand Alone</td>
                        <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true">20.500.000</td>
                        <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true">19.200.000</td>
                        <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true"></td>
                        <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true"></td>
                        @for ($i = 5; $i <= 31; $i++) <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true"></td> @endfor
                    </tr>
                    <!-- AREA 18 -->
                    <tr style="background-color: #e2e8f0; font-weight: bold; color: black;">
                        <td colspan="5" class="text-left border-right" style="padding-left: 10px;">AREA 18</td>
                        @for ($i = 1; $i <= 31; $i++) <td style="border-right: 1px solid #9ca3af;"></td> @endfor
                    </tr>
                    <tr class="data-row">
                        <td class="border-right">1</td><td class="border-right">A01</td><td class="border-right text-left" style="padding-left: 5px;">LIVING WORLD PEKAN BARU</td><td class="border-right">04-Jan-2020</td><td class="border-right">Mall</td>
                        <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true">12.000.000</td>
                        <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true"></td>
                        <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true">11.500.000</td>
                        <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true"></td>
                        @for ($i = 5; $i <= 31; $i++) <td style="background-color: yellow; color: black; border-right: 1px solid #9ca3af;" contenteditable="true"></td> @endfor
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel-header mb-4" style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2>HARIAN SALES PERFORMANCE REGIONAL 5 TH 2026</h2>
            <p class="sub-text m-0">Rekapitulasi Kinerja Seluruh Area (SharePoint Sync)</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button class="btn-primary" onclick="alert('Sinkronisasi SharePoint berjalan...')"><i class="ti ti-refresh"></i> Sync Data</button>
            <a href="https://ekabogainti-my.sharepoint.com/:x:/g/personal/giri_handoko_hokben_co_id/IQAglXy_uxWuTL3_6jULiDPNAbCmC_K7FrMfxRi6-t283jI?e=FT1W9G&CID=7876485c-ad48-d43b-2814-f198d9fa5fe3" target="_blank" class="btn-outline" style="text-decoration: none;"><i class="ti ti-brand-office"></i> Buka SharePoint Asli</a>
        </div>
    </div>

    <div class="glass-panel mb-5" style="padding: 0; overflow: hidden;">
        <div class="excel-container">
            <table class="excel-table">
                <thead>
                    <tr class="header-tier-1">
                        <th rowspan="2" class="border-right" style="vertical-align: middle;">Tanggal</th>
                        <th colspan="3" class="border-right bg-blue">AREA 15</th>
                        <th colspan="3" class="border-right bg-green">AREA 16</th>
                        <th colspan="3" class="border-right bg-orange">AREA 17</th>
                        <th colspan="3" class="border-right bg-blue">AREA 18</th>
                        <th colspan="3" class="border-right bg-green">AREA 19</th>
                        <th colspan="3" class="bg-orange">TOTAL REGIONAL 5</th>
                    </tr>
                    <tr class="header-tier-2">
                        <th>Target</th><th>Actual</th><th class="border-right">% Ach</th>
                        <th>Target</th><th>Actual</th><th class="border-right">% Ach</th>
                        <th>Target</th><th>Actual</th><th class="border-right">% Ach</th>
                        <th>Target</th><th>Actual</th><th class="border-right">% Ach</th>
                        <th>Target</th><th>Actual</th><th class="border-right">% Ach</th>
                        <th>Target</th><th>Actual</th><th>% Ach</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 1; $i <= 31; $i++)
                    <tr>
                        <td class="text-bold border-right">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}-Okt-26</td>
                        <td>50.000.000</td><td>45.200.000</td><td class="border-right text-success">90.4%</td>
                        <td>45.000.000</td><td>47.500.000</td><td class="border-right text-success">105.5%</td>
                        <td>60.000.000</td><td>55.000.000</td><td class="border-right text-success">91.6%</td>
                        <td>55.000.000</td><td>58.200.000</td><td class="border-right text-success">105.8%</td>
                        <td>65.000.000</td><td>68.500.000</td><td class="border-right text-success">105.3%</td>
                        <td class="text-bold">275.000.000</td><td class="text-bold">274.400.000</td><td class="text-bold text-success">99.7%</td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel-header mb-4 mt-4" style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 8px;">
        <h2>MONITORING SALES AREA 19 2026 - SISWANTO</h2>
        <p class="sub-text m-0">Menampilkan seluruh isi dokumen Google Sheets secara langsung</p>
    </div>

    <!-- Tabs Area 19 -->
    <div class="tabs-container mb-4" style="display: flex; gap: 8px; overflow-x: auto; padding-bottom: 8px; border-bottom: 1px solid var(--border-color);">
        <button class="tab-btn active" style="padding: 10px 20px; border: none; border-radius: 8px; background: var(--primary); color: white; cursor: pointer; font-weight: 600;">10. OKTOBER</button>
    </div>
    
    <div class="glass-panel" style="padding: 0; overflow: hidden; display: flex; flex-direction: column; height: 75vh;">
        <div style="padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.3);">
            <h3 style="margin: 0;">10. OKTOBER - SALES AREA 19 2026 - SISWANTO</h3>
            <div style="display: flex; gap: 10px;">
                <button class="btn-success" onclick="fetchArea19Data(this)" style="padding: 6px 12px; font-size: 13px;"><i class="ti ti-refresh"></i> Sync & Analisa Data</button>
                <a href="https://docs.google.com/spreadsheets/d/1zNxrQc1z02yEXmCmvuvGAkkGlJcdwDPH/edit?gid=297723641#gid=297723641" target="_blank" class="btn-outline" style="text-decoration: none; padding: 6px 12px; font-size: 13px;">
                    <i class="ti ti-brand-google-drive"></i> Buka Sheet Asli
                </a>
            </div>
        </div>
        
        <!-- Tempat Munculnya Analisa Tanda -->
        <div id="area19-analysis-result" style="display: none; padding: 15px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); max-height: 250px; overflow-y: auto;"></div>

        <div style="flex-grow: 1; position: relative;">
            <!-- Embed Iframe Google Sheets -->
            <iframe src="https://docs.google.com/spreadsheets/d/1zNxrQc1z02yEXmCmvuvGAkkGlJcdwDPH/htmlembed?widget=true&headers=false&gid=297723641" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;"></iframe>
        </div>
    </div>
</section>
