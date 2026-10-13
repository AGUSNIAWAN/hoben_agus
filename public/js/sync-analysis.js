/**
 * HokBen OpsTrack - Sync & Analysis Module
 * Menangani sinkronisasi live Google Sheets dan analisa file Excel secara lokal via SheetJS.
 */

// Sinkronisasi Data Sales Area 19
function fetchArea19Data(btnElement) {
    const originalText = btnElement.innerHTML;
    btnElement.innerHTML = '<i class="ti ti-loader"></i> Menyinkronkan...';
    btnElement.disabled = true;

    const syncUrl = (window.opsTrackRoutes && window.opsTrackRoutes.syncArea19) || '/api/sync-area19';

    fetch(syncUrl)
        .then(response => response.json())
        .then(result => {
            btnElement.innerHTML = originalText;
            btnElement.disabled = false;
            
            if(result.success) {
                const rawData = result.data;
                let emptyByColumn = {};
                let cardsHTML = '';
                
                if (rawData && rawData.length > 0) {
                    const headers = rawData[0]; // Baris pertama adalah header
                    
                    for (let r = 1; r < rawData.length; r++) {
                        const row = rawData[r];
                        // Lewati baris kosong seutuhnya
                        const isRowEmpty = row.length === 0 || row.every(cell => cell === undefined || cell === null || String(cell).trim() === '');
                        if (isRowEmpty) continue;

                        const maxCols = Math.max(headers.length, row.length);
                        for (let c = 0; c < maxCols; c++) {
                            let cellValue = row[c];
                            if (cellValue === undefined || cellValue === null || String(cellValue).trim() === '') {
                                let colName = headers[c] ? String(headers[c]).trim() : '';
                                if (!colName) {
                                    colName = `Kolom ke-${c+1}`;
                                }
                                if (!emptyByColumn[colName]) {
                                    emptyByColumn[colName] = [];
                                }
                                emptyByColumn[colName].push(r + 1);
                            }
                        }
                    }

                    // Format pesan peringatan per kolom menjadi Grid Card
                    for (const [colName, rows] of Object.entries(emptyByColumn)) {
                        let badgesHTML = rows.slice(0, 10).map(r => `<span style="display:inline-block; padding:2px 8px; margin:2px; background:rgba(239,68,68,0.1); color:#ef4444; border-radius:12px; font-size:12px; font-weight:500;">Baris ${r}</span>`).join('');
                        if (rows.length > 10) {
                            badgesHTML += `<span style="display:inline-block; padding:2px 8px; margin:2px; background:#f1f5f9; color:#64748b; border-radius:12px; font-size:12px; font-weight:500;">+ ${rows.length - 10} lainnya</span>`;
                        }
                        
                        cardsHTML += `
                            <div style="background: white; border: 1px solid #fee2e2; border-radius: 8px; padding: 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                <div style="font-weight: 600; color: #b91c1c; margin-bottom: 8px; font-size: 13px; display: flex; justify-content: space-between; align-items: center;">
                                    <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-right: 8px;" title="${colName}">Kolom: ${colName}</span>
                                    <span style="background: #fee2e2; padding: 2px 6px; border-radius: 4px; font-size: 11px; flex-shrink: 0;">${rows.length} Kosong</span>
                                </div>
                                <div style="display: flex; flex-wrap: wrap;">
                                    ${badgesHTML}
                                </div>
                            </div>
                        `;
                    }
                }

                const analysisDiv = document.getElementById('area19-analysis-result');
                if (analysisDiv) {
                    if (cardsHTML !== '') {
                        analysisDiv.innerHTML = `
                            <div style="padding: 15px; border-radius: 8px; background: #fffcfc; border: 1px solid #fecaca;">
                                <div style="display: flex; align-items: center; gap: 8px; color: #ef4444; font-weight: 600; margin-bottom: 15px; font-size: 15px;">
                                    <i class="ti ti-alert-triangle" style="font-size: 18px;"></i> Peringatan! Terdapat ${Object.keys(emptyByColumn).length} Kolom dengan Data Kosong
                                </div>
                                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px;">
                                    ${cardsHTML}
                                </div>
                            </div>
                        `;
                        analysisDiv.style.display = 'block';
                    } else {
                        analysisDiv.innerHTML = `
                            <div style="background: rgba(16, 185, 129, 0.1); border-left: 4px solid #10b981; padding: 15px; border-radius: 4px;">
                                <div style="display: flex; align-items: center; gap: 8px; color: #10b981; font-weight: 600;">
                                    <i class="ti ti-check"></i> Berhasil! Semua data telah terisi lengkap.
                                </div>
                            </div>
                        `;
                        analysisDiv.style.display = 'block';
                    }
                }
                
                console.log("Isi mentah (Raw Data) Google Sheet 'monitoring sales':", result.data);
            } else {
                alert("Gagal sinkronisasi: " + result.message);
            }
        })
        .catch(error => {
            btnElement.innerHTML = originalText;
            btnElement.disabled = false;
            alert("Terjadi kesalahan jaringan atau konfigurasi Google API Client belum selesai.");
            console.error(error);
        });
}

// Sinkronisasi Data Serah Terima Sales & Modal Harian
function fetchSerahTerimaData(btnElement) {
    const originalText = btnElement.innerHTML;
    btnElement.innerHTML = '<i class="ti ti-loader"></i> Menyinkronkan...';
    btnElement.disabled = true;

    const activeMonth = window.activeSerahTerimaMonth || 'JANUARI';
    const sheetConfig = (window.serahTerimaSheets && window.serahTerimaSheets[activeMonth]) ? window.serahTerimaSheets[activeMonth] : { id: '19S0wZPXQ4T_u2DMtrUyLXhSpLfUe0tjr', gid: '285912838' };
    const baseSyncUrl = (window.opsTrackRoutes && window.opsTrackRoutes.syncSerahTerima) || '/api/sync-serah-terima';
    const syncUrl = `${baseSyncUrl}?spreadsheetId=${sheetConfig.id}&gid=${sheetConfig.gid || '0'}`;

    fetch(syncUrl)
        .then(response => response.json())
        .then(result => {
            btnElement.innerHTML = originalText;
            btnElement.disabled = false;
            
            if(result.success) {
                const rawData = result.data;
                let emptyByColumn = {};
                let cardsHTML = '';
                
                if (rawData && rawData.length > 0) {
                    const headers = rawData[0]; // Baris pertama adalah header
                    
                    for (let r = 1; r < rawData.length; r++) {
                        const row = rawData[r];
                        // Lewati baris kosong seutuhnya
                        const isRowEmpty = row.length === 0 || row.every(cell => cell === undefined || cell === null || String(cell).trim() === '');
                        if (isRowEmpty) continue;

                        const maxCols = Math.max(headers.length, row.length);
                        for (let c = 0; c < maxCols; c++) {
                            let cellValue = row[c];
                            if (cellValue === undefined || cellValue === null || String(cellValue).trim() === '') {
                                let colName = headers[c] ? String(headers[c]).trim() : '';
                                if (!colName) {
                                    colName = `Kolom ke-${c+1}`;
                                }
                                if (!emptyByColumn[colName]) {
                                    emptyByColumn[colName] = [];
                                }
                                emptyByColumn[colName].push(r + 1);
                            }
                        }
                    }

                    // Format pesan peringatan per kolom menjadi Grid Card
                    for (const [colName, rows] of Object.entries(emptyByColumn)) {
                        let badgesHTML = rows.slice(0, 10).map(r => `<span style="display:inline-block; padding:2px 8px; margin:2px; background:rgba(239,68,68,0.1); color:#ef4444; border-radius:12px; font-size:12px; font-weight:500;">Baris ${r}</span>`).join('');
                        if (rows.length > 10) {
                            badgesHTML += `<span style="display:inline-block; padding:2px 8px; margin:2px; background:#f1f5f9; color:#64748b; border-radius:12px; font-size:12px; font-weight:500;">+ ${rows.length - 10} lainnya</span>`;
                        }
                        
                        cardsHTML += `
                            <div style="background: white; border: 1px solid #fee2e2; border-radius: 8px; padding: 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                <div style="font-weight: 600; color: #b91c1c; margin-bottom: 8px; font-size: 13px; display: flex; justify-content: space-between; align-items: center;">
                                    <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-right: 8px;" title="${colName}">Kolom: ${colName}</span>
                                    <span style="background: #fee2e2; padding: 2px 6px; border-radius: 4px; font-size: 11px; flex-shrink: 0;">${rows.length} Kosong</span>
                                </div>
                                <div style="display: flex; flex-wrap: wrap;">
                                    ${badgesHTML}
                                </div>
                            </div>
                        `;
                    }
                }

                const analysisDiv = document.getElementById('serah-terima-analysis-result');
                if (analysisDiv) {
                    if (cardsHTML !== '') {
                        analysisDiv.innerHTML = `
                            <div style="padding: 15px; border-radius: 8px; background: #fffcfc; border: 1px solid #fecaca;">
                                <div style="display: flex; align-items: center; gap: 8px; color: #ef4444; font-weight: 600; margin-bottom: 15px; font-size: 15px;">
                                    <i class="ti ti-alert-triangle" style="font-size: 18px;"></i> Peringatan (${activeMonth})! Terdapat ${Object.keys(emptyByColumn).length} Kolom dengan Data Kosong
                                </div>
                                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px;">
                                    ${cardsHTML}
                                </div>
                            </div>
                        `;
                        analysisDiv.style.display = 'block';
                    } else {
                        analysisDiv.innerHTML = `
                            <div style="background: rgba(16, 185, 129, 0.1); border-left: 4px solid #10b981; padding: 15px; border-radius: 4px;">
                                <div style="display: flex; align-items: center; gap: 8px; color: #10b981; font-weight: 600;">
                                    <i class="ti ti-check"></i> Berhasil (${activeMonth})! Semua data pada sheet ini telah terisi lengkap.
                                </div>
                            </div>
                        `;
                        analysisDiv.style.display = 'block';
                    }
                }
            } else {
                alert("Gagal sinkronisasi data serah terima: " + result.message);
            }
        })
        .catch(error => {
            btnElement.innerHTML = originalText;
            btnElement.disabled = false;
            alert("Terjadi kesalahan jaringan saat menganalisa data serah terima.");
            console.error(error);
        });
}

// Sinkronisasi Data KFM dan Helpdesk
function fetchKfmData(btnElement) {
    const originalText = btnElement.innerHTML;
    btnElement.innerHTML = '<i class="ti ti-loader"></i> Menyinkronkan...';
    btnElement.disabled = true;

    const syncUrl = (window.opsTrackRoutes && window.opsTrackRoutes.syncKfmHelpdesk) || '/api/sync-kfm-helpdesk';

    fetch(syncUrl)
        .then(response => response.json())
        .then(result => {
            btnElement.innerHTML = originalText;
            btnElement.disabled = false;

            if(result.success) {
                const rawData = result.data;
                let emptyByColumn = {};
                let cardsHTML = '';

                if (rawData && rawData.length > 0) {
                    let headerRowIdx = 0;
                    for (let i = 0; i < Math.min(rawData.length, 5); i++) {
                        if (rawData[i] && rawData[i].filter(c => c !== undefined && c !== null && String(c).trim() !== '').length > 3) {
                            headerRowIdx = i;
                            break;
                        }
                    }

                    const headers = rawData[headerRowIdx] || [];

                    for (let r = headerRowIdx + 1; r < rawData.length; r++) {
                        const row = rawData[r];
                        if (!row) continue;

                        const hasStoreInfo = row.some((c, idx) => idx < 4 && c !== undefined && c !== null && String(c).trim() !== '');
                        if (!hasStoreInfo) continue;

                        const maxCols = Math.max(headers.length, row.length);
                        for (let c = 1; c < maxCols; c++) {
                            let cellValue = row[c];
                            if (cellValue === undefined || cellValue === null || String(cellValue).trim() === '') {
                                let colName = headers[c] ? String(headers[c]).trim() : '';
                                if (!colName) {
                                    colName = `Kolom ke-${c+1}`;
                                }
                                if (!emptyByColumn[colName]) {
                                    emptyByColumn[colName] = [];
                                }
                                emptyByColumn[colName].push(r + 1);
                            }
                        }
                    }

                    // Format pesan peringatan per kolom menjadi Grid Card
                    for (const [colName, rows] of Object.entries(emptyByColumn)) {
                        let badgesHTML = rows.slice(0, 10).map(r => `<span style="display:inline-block; padding:2px 8px; margin:2px; background:rgba(239,68,68,0.1); color:#ef4444; border-radius:12px; font-size:12px; font-weight:500;">Baris ${r}</span>`).join('');
                        if (rows.length > 10) {
                            badgesHTML += `<span style="display:inline-block; padding:2px 8px; margin:2px; background:#f1f5f9; color:#64748b; border-radius:12px; font-size:12px; font-weight:500;">+ ${rows.length - 10} lainnya</span>`;
                        }

                        cardsHTML += `
                            <div style="background: white; border: 1px solid #fee2e2; border-radius: 8px; padding: 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                <div style="font-weight: 600; color: #b91c1c; margin-bottom: 8px; font-size: 13px; display: flex; justify-content: space-between; align-items: center;">
                                    <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-right: 8px;" title="${colName}">Kolom: ${colName}</span>
                                    <span style="background: #fee2e2; padding: 2px 6px; border-radius: 4px; font-size: 11px; flex-shrink: 0;">${rows.length} Kosong</span>
                                </div>
                                <div style="display: flex; flex-wrap: wrap;">
                                    ${badgesHTML}
                                </div>
                            </div>
                        `;
                    }
                }

                const analysisDiv = document.getElementById('kfm-analysis-result');
                if (analysisDiv) {
                    if (cardsHTML !== '') {
                        analysisDiv.innerHTML = `
                            <div style="padding: 15px; border-radius: 8px; background: #fffcfc; border: 1px solid #fecaca;">
                                <div style="display: flex; align-items: center; gap: 8px; color: #ef4444; font-weight: 600; margin-bottom: 15px; font-size: 15px;">
                                    <i class="ti ti-alert-triangle" style="font-size: 18px;"></i> Peringatan (Monitoring KFM & Helpdesk)! Terdapat ${Object.keys(emptyByColumn).length} Kolom dengan Data Kosong
                                </div>
                                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px;">
                                    ${cardsHTML}
                                </div>
                            </div>
                        `;
                        analysisDiv.style.display = 'block';
                    } else {
                        analysisDiv.innerHTML = `
                            <div style="background: rgba(16, 185, 129, 0.1); border-left: 4px solid #10b981; padding: 15px; border-radius: 4px;">
                                <div style="display: flex; align-items: center; gap: 8px; color: #10b981; font-weight: 600;">
                                    <i class="ti ti-check"></i> Berhasil! Semua data pada sheet KFM & Helpdesk telah terisi lengkap.
                                </div>
                            </div>
                        `;
                        analysisDiv.style.display = 'block';
                    }
                }
            } else {
                alert("Gagal sinkronisasi data KFM dan Helpdesk: " + result.message);
            }
        })
        .catch(error => {
            btnElement.innerHTML = originalText;
            btnElement.disabled = false;
            alert("Terjadi kesalahan jaringan saat menyinkronkan data KFM dan Helpdesk.");
            console.error(error);
        });
}

// Global Workbooks untuk SheetJS
let globalWorkbook = null;
let preshiftWorkbook = null;

// Handle Upload File Excel Sales Aktual
function handleExcelUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        try {
            const data = new Uint8Array(e.target.result);
            globalWorkbook = XLSX.read(data, {type: 'array'});
            
            const sheetSelector = document.getElementById('sheetSelector');
            sheetSelector.innerHTML = '';
            
            globalWorkbook.SheetNames.forEach((name, index) => {
                let isHidden = false;
                if(globalWorkbook.Workbook && globalWorkbook.Workbook.Sheets && globalWorkbook.Workbook.Sheets[index]) {
                    const hiddenState = globalWorkbook.Workbook.Sheets[index].Hidden;
                    if(hiddenState === 1 || hiddenState === 2) {
                        isHidden = true;
                    }
                }

                if(!isHidden) {
                    let option = document.createElement('option');
                    option.value = name;
                    option.text = name;
                    sheetSelector.appendChild(option);
                }
            });
            
            sheetSelector.style.display = 'block';

            let isSelected = false;
            for(let i = 0; i < sheetSelector.options.length; i++) {
                if(sheetSelector.options[i].value.toUpperCase().includes('AKTUAL')) {
                    sheetSelector.selectedIndex = i;
                    isSelected = true;
                    break;
                }
            }
            
            if(!isSelected && sheetSelector.options.length > 4) {
                sheetSelector.selectedIndex = 4;
            }
            
            renderSelectedSheet();
        } catch(error) {
            alert("Gagal membaca file Excel. Pastikan file valid.");
            console.error(error);
        }
    };
    reader.readAsArrayBuffer(file);
}

function renderSelectedSheet() {
    if(!globalWorkbook) return;
    const sheetSelector = document.getElementById('sheetSelector');
    const sheetName = sheetSelector.value;
    const worksheet = globalWorkbook.Sheets[sheetName];
    
    const jsonData = XLSX.utils.sheet_to_json(worksheet, {header: 1, raw: false});
    processExcelData(jsonData, sheetName);
}

function processExcelData(data, sheetName) {
    const tbody = document.querySelector('#aktual-sales-table tbody');
    tbody.innerHTML = '';
    
    let missingDataAlerts = [];
    let currentDay = 4;

    const currentDate = new Date();
    const monthsIndo = ["JANUARI", "FEBRUARI", "MARET", "APRIL", "MEI", "JUNI", "JULI", "AGUSTUS", "SEPTEMBER", "OKTOBER", "NOVEMBER", "DESEMBER"];
    const currentMonthStr = monthsIndo[currentDate.getMonth()];

    let foundMonthCol = 0;
    for (let r = 0; r < 10 && r < data.length; r++) {
        if (data[r]) {
            for (let c = 0; c < data[r].length; c++) {
                if (String(data[r][c]).toUpperCase().includes(currentMonthStr)) {
                    foundMonthCol = c;
                    r = 10;
                    break;
                }
            }
        }
    }

    let startDateIndex = 5;
    for (let r = 0; r < 10 && r < data.length; r++) {
        if (data[r]) {
            for (let c = 0; c < data[r].length; c++) {
                if (String(data[r][c]).trim() === '1' && String(data[r][c+1]).trim() === '2' && String(data[r][c+2]).trim() === '3') {
                    startDateIndex = c;
                    if (foundMonthCol > 0 && c >= foundMonthCol - 5) {
                        r = 10; 
                        break;
                    }
                }
            }
        }
    }

    let storeCount = 0;
    data.forEach((row, rowIndex) => {
        if(row && row.length > 3 && typeof row[2] === 'string' && (row[2].includes('AREA') === false) && rowIndex > 2) {
            if(row[0] && !isNaN(row[0])) {
                storeCount++;
                const no = row[0];
                const storeCode = row[1] || '-';
                const storeName = row[2] || 'Unknown Store';
                const openingDate = row[3] || '-';
                const type = row[4] || '-';

                let tr = document.createElement('tr');
                tr.className = 'data-row';
                
                let html = `<td class="border-right">${no}</td>
                            <td class="border-right">${storeCode}</td>
                            <td class="border-right text-left" style="padding-left: 5px;">${storeName}</td>
                            <td class="border-right">${openingDate}</td>
                            <td class="border-right">${type}</td>`;
                
                for(let i = 1; i <= 31; i++) {
                    const dataIndex = startDateIndex + i - 1;
                    const cellValue = row[dataIndex] !== undefined ? row[dataIndex] : '';
                    
                    let isMissing = false;
                    if(i <= currentDay && (cellValue === '' || cellValue === null || cellValue === undefined)) {
                        isMissing = true;
                        missingDataAlerts.push(`Store ${storeName} belum mengisi data tanggal ${i}.`);
                    }

                    let bgColor = isMissing ? '#ffebee' : 'yellow';
                    let border = isMissing ? '2px solid red' : '1px solid #9ca3af';

                    html += `<td style="background-color: ${bgColor}; color: black; border-right: ${border};" contenteditable="true">${cellValue}</td>`;
                }
                
                tr.innerHTML = html;
                tbody.appendChild(tr);
            }
        }
    });

    if(storeCount === 0) {
        alert(`Tidak menemukan format data store yang cocok di sheet "${sheetName}". Coba cek file Anda.`);
        return;
    }

    if (missingDataAlerts.length > 0) {
        alert(`Peringatan! Terdapat data yang belum diisi (Pengecekan s/d tgl ${currentDay}):\n\n` + 
              missingDataAlerts.slice(0, 10).join("\n") + 
              (missingDataAlerts.length > 10 ? `\n\n...dan ${missingDataAlerts.length - 10} data lainnya.` : ""));
    } else {
        alert(`Lengkap! Semua store sudah mengisi data s/d tanggal ${currentDay}.`);
    }
}

// Handle Upload File Excel Pre-Shift Online Area 19
function handlePreshiftExcelUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        try {
            const data = new Uint8Array(e.target.result);
            preshiftWorkbook = XLSX.read(data, {type: 'array'});
            
            const selector = document.getElementById('preshiftSheetSelector');
            selector.innerHTML = '';
            
            preshiftWorkbook.SheetNames.forEach((name, index) => {
                let isHidden = false;
                if(preshiftWorkbook.Workbook && preshiftWorkbook.Workbook.Sheets && preshiftWorkbook.Workbook.Sheets[index]) {
                    const hiddenState = preshiftWorkbook.Workbook.Sheets[index].Hidden;
                    if(hiddenState === 1 || hiddenState === 2) {
                        isHidden = true;
                    }
                }
                if(!isHidden) {
                    let option = document.createElement('option');
                    option.value = name;
                    option.text = name;
                    selector.appendChild(option);
                }
            });
            
            if (selector.options.length > 0) {
                selector.style.display = 'inline-block';
            }
            
            renderSelectedPreshiftSheet();
        } catch(error) {
            alert("Gagal membaca file Excel Pre-Shift. Pastikan file valid.");
            console.error(error);
        }
    };
    reader.readAsArrayBuffer(file);
}

function renderSelectedPreshiftSheet() {
    if(!preshiftWorkbook) return;
    const selector = document.getElementById('preshiftSheetSelector');
    const sheetName = selector.value || preshiftWorkbook.SheetNames[0];
    const worksheet = preshiftWorkbook.Sheets[sheetName];
    
    const rawData = XLSX.utils.sheet_to_json(worksheet, {header: 1, raw: false});
    analyzePreshiftData(rawData, sheetName);
}

function analyzePreshiftData(rawData, sheetName) {
    const analysisDiv = document.getElementById('preshift-analysis-result');
    if(!analysisDiv) return;

    let emptyByColumn = {};
    let cardsHTML = '';

    if (rawData && rawData.length > 0) {
        let headerRowIdx = 0;
        for (let i = 0; i < Math.min(rawData.length, 5); i++) {
            if (rawData[i] && rawData[i].filter(c => c !== undefined && c !== null && String(c).trim() !== '').length > 2) {
                headerRowIdx = i;
                break;
            }
        }

        const headers = rawData[headerRowIdx] || [];

        for (let r = headerRowIdx + 1; r < rawData.length; r++) {
            const row = rawData[r];
            if (!row) continue;
            
            const isRowEmpty = row.length === 0 || row.every(cell => cell === undefined || cell === null || String(cell).trim() === '');
            if (isRowEmpty) continue;

            const maxCols = Math.max(headers.length, row.length);
            for (let c = 0; c < maxCols; c++) {
                let cellValue = row[c];
                if (cellValue === undefined || cellValue === null || String(cellValue).trim() === '') {
                    let colName = headers[c] ? String(headers[c]).trim() : '';
                    if (!colName) {
                        colName = `Kolom ke-${c+1}`;
                    }
                    if (!emptyByColumn[colName]) {
                        emptyByColumn[colName] = [];
                    }
                    emptyByColumn[colName].push(r + 1);
                }
            }
        }

        // Format peringatan per kolom menjadi Grid Card
        for (const [colName, rows] of Object.entries(emptyByColumn)) {
            let badgesHTML = rows.slice(0, 10).map(r => `<span style="display:inline-block; padding:2px 8px; margin:2px; background:rgba(239,68,68,0.1); color:#ef4444; border-radius:12px; font-size:12px; font-weight:500;">Baris ${r}</span>`).join('');
            if (rows.length > 10) {
                badgesHTML += `<span style="display:inline-block; padding:2px 8px; margin:2px; background:#f1f5f9; color:#64748b; border-radius:12px; font-size:12px; font-weight:500;">+ ${rows.length - 10} lainnya</span>`;
            }
            
            cardsHTML += `
                <div style="background: white; border: 1px solid #fee2e2; border-radius: 8px; padding: 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                    <div style="font-weight: 600; color: #b91c1c; margin-bottom: 8px; font-size: 13px; display: flex; justify-content: space-between; align-items: center;">
                        <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-right: 8px;" title="${colName}">Kolom: ${colName}</span>
                        <span style="background: #fee2e2; padding: 2px 6px; border-radius: 4px; font-size: 11px; flex-shrink: 0;">${rows.length} Kosong</span>
                    </div>
                    <div style="display: flex; flex-wrap: wrap;">
                        ${badgesHTML}
                    </div>
                </div>
            `;
        }
    }

    if (cardsHTML !== '') {
        analysisDiv.innerHTML = `
            <div style="padding: 15px; border-radius: 8px; background: #fffcfc; border: 1px solid #fecaca;">
                <div style="display: flex; align-items: center; gap: 8px; color: #ef4444; font-weight: 600; margin-bottom: 15px; font-size: 15px;">
                    <i class="ti ti-alert-triangle" style="font-size: 18px;"></i> Peringatan [Sheet: ${sheetName}]! Terdapat ${Object.keys(emptyByColumn).length} Kolom dengan Data Kosong
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px;">
                    ${cardsHTML}
                </div>
            </div>
        `;
        analysisDiv.style.display = 'block';
    } else {
        analysisDiv.innerHTML = `
            <div style="background: rgba(16, 185, 129, 0.1); border-left: 4px solid #10b981; padding: 15px; border-radius: 4px;">
                <div style="display: flex; align-items: center; gap: 8px; color: #10b981; font-weight: 600;">
                    <i class="ti ti-check"></i> Berhasil [Sheet: ${sheetName}]! Semua data pada sheet ini telah terisi lengkap.
                </div>
            </div>
        `;
        analysisDiv.style.display = 'block';
    }
}
