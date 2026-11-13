/**
 * HokBen OpsTrack - Modul Analisa Data Penjualan Per Group
 * Store: Ruko Plaju Palembang (A20) • Area 19 Sumbagsel
 * Periode: Januari s.d. Oktober 2026
 */

(function() {
    let salesData = null;
    let currentSelectedMonth = 'ALL';
    let chartInstances = {};

    // Helper formatter Rupiah
    function formatRupiah(num) {
        if (num === null || num === undefined || isNaN(num)) return 'Rp 0';
        return 'Rp ' + Math.round(num).toLocaleString('id-ID');
    }

    // Helper formatter angka
    function formatNumber(num) {
        if (num === null || num === undefined || isNaN(num)) return '0';
        return Math.round(num).toLocaleString('id-ID');
    }

    // Helper formatter ringkas (Jt / M)
    function formatCompactRupiah(num) {
        if (!num) return 'Rp 0';
        if (num >= 1000000000) {
            return 'Rp ' + (num / 1000000000).toFixed(2).replace('.', ',') + ' M';
        }
        if (num >= 1000000) {
            return 'Rp ' + (num / 1000000).toFixed(1).replace('.', ',') + ' Jt';
        }
        return formatRupiah(num);
    }

    // Sub-tab Navigation
    window.switchAnalysisTab = function(tabId, btnElement) {
        document.querySelectorAll('.analysis-subtab-btn').forEach(btn => btn.classList.remove('active'));
        if (btnElement) btnElement.classList.add('active');

        document.querySelectorAll('.analysis-tab-content').forEach(c => c.style.display = 'none');
        const targetTab = document.getElementById(tabId);
        if (targetTab) targetTab.style.display = 'block';

        const mainCharts = document.getElementById('analysis-charts-container');
        const secondaryCharts = document.getElementById('analysis-secondary-charts');

        // Manage visibility of top charts
        if (tabId === 'tab-overview' || tabId === 'tab-channels') {
            if (mainCharts) mainCharts.style.display = 'grid';
            if (secondaryCharts) secondaryCharts.style.display = 'grid';
            resizeAllCharts();
        } else {
            if (mainCharts) mainCharts.style.display = 'none';
            if (secondaryCharts) secondaryCharts.style.display = 'none';
        }
    };

    window.switchToAnalysisTab = function(tabId) {
        const btn = document.querySelector(`.analysis-subtab-btn[data-tab="${tabId}"]`);
        window.switchAnalysisTab(tabId, btn);
    };

    function resizeAllCharts() {
        setTimeout(() => {
            Object.values(chartInstances).forEach(chart => {
                if (chart && typeof chart.resize === 'function') chart.resize();
            });
        }, 50);
    }

    // Inisialisasi Data dari JSON
    window.initSalesAnalysisPage = function() {
        if (salesData) {
            resizeAllCharts();
            return;
        }

        const dataUrl = '/data/sales_summary_plaju.json?v=' + Date.now();
        fetch(dataUrl)
            .then(res => {
                if (!res.ok) throw new Error('Gagal memuat data penjualan: ' + res.status);
                return res.json();
            })
            .then(data => {
                salesData = data;
                renderDashboardMetrics();
                renderAllCharts();
                renderMonthlyTable();
                renderChannelTable();
                renderGroupTable();
                renderProductTable();
                renderArea19StoreList();
            })
            .catch(err => {
                console.error('Error loading sales analysis:', err);
                const banner = document.getElementById('view-analisa-penjualan');
                if (banner) {
                    const alertBox = document.createElement('div');
                    alertBox.className = 'glass-panel';
                    alertBox.style.padding = '16px';
                    alertBox.style.marginBottom = '20px';
                    alertBox.style.borderColor = 'var(--danger)';
                    alertBox.style.color = 'var(--danger)';
                    alertBox.innerHTML = `<i class="ti ti-alert-triangle"></i> Terjadi kesalahan memuat data: ${err.message}. Pastikan file <code>public/data/sales_summary_plaju.json</code> telah di-generate.`;
                    banner.prepend(alertBox);
                }
            });
    };

    // Render Metric Cards
    function renderDashboardMetrics() {
        if (!salesData) return;
        const ov = salesData.overall_summary;
        const m = currentSelectedMonth === 'ALL' ? null : salesData.monthly_summaries[currentSelectedMonth];

        const totalRevEl = document.getElementById('kpi-total-revenue');
        const avgMonthlyEl = document.getElementById('kpi-avg-monthly');
        const totalQtyEl = document.getElementById('kpi-total-qty');
        const avgPriceEl = document.getElementById('kpi-avg-price');
        const onlineRevEl = document.getElementById('kpi-online-revenue');
        const onlineShareEl = document.getElementById('kpi-online-share');
        const dineInRevEl = document.getElementById('kpi-dinein-revenue');
        const dineInShareEl = document.getElementById('kpi-dinein-share');
        const taRevEl = document.getElementById('kpi-takeaway-revenue');
        const taShareEl = document.getElementById('kpi-takeaway-share');
        const heroNameEl = document.getElementById('kpi-hero-name');
        const heroDetailEl = document.getElementById('kpi-hero-detail');

        if (currentSelectedMonth === 'ALL') {
            if (totalRevEl) totalRevEl.textContent = formatCompactRupiah(ov.total_revenue);
            if (avgMonthlyEl) avgMonthlyEl.innerHTML = `Rata-rata: <strong>${formatCompactRupiah(ov.avg_monthly_revenue)} / bln</strong>`;
            if (totalQtyEl) totalQtyEl.textContent = formatNumber(ov.total_qty);
            if (avgPriceEl) avgPriceEl.innerHTML = `Rata-rata: <strong>${formatNumber(ov.avg_monthly_qty)} item / bln</strong>`;

            if (onlineRevEl) onlineRevEl.textContent = formatCompactRupiah(ov.cumulative_online.rp);
            if (onlineShareEl) onlineShareEl.innerHTML = `Pangsa: <strong>${ov.cumulative_online.pct_rp}%</strong> &bull; ${formatNumber(ov.cumulative_online.qty)} item`;

            const di = ov.cumulative_channels.di;
            if (dineInRevEl) dineInRevEl.textContent = formatCompactRupiah(di.rp);
            if (dineInShareEl) dineInShareEl.innerHTML = `Pangsa: <strong>${di.pct_rp}%</strong> &bull; ${formatNumber(di.qty)} item`;

            const ta = ov.cumulative_channels.ta;
            if (taRevEl) taRevEl.textContent = formatCompactRupiah(ta.rp);
            if (taShareEl) taShareEl.innerHTML = `Pangsa: <strong>${ta.pct_rp}%</strong> &bull; ${formatNumber(ta.qty)} item`;

            const hero = ov.top_15_items_rp[0];
            if (heroNameEl) {
                heroNameEl.textContent = hero.name;
                heroNameEl.title = hero.name;
            }
            if (heroDetailEl) heroDetailEl.innerHTML = `Omzet: <strong>${formatCompactRupiah(hero.total_rp)} (${hero.pct_of_total_rp}%)</strong>`;
        } else if (m) {
            if (totalRevEl) totalRevEl.textContent = formatCompactRupiah(m.total_rp);
            if (avgMonthlyEl) avgMonthlyEl.innerHTML = `Harga Rata-rata: <strong>${formatRupiah(m.avg_price_per_item)} / item</strong>`;
            if (totalQtyEl) totalQtyEl.textContent = formatNumber(m.total_qty);
            if (avgPriceEl) avgPriceEl.innerHTML = `Total SKU Terjual: <strong>${m.items_count} menu</strong>`;

            if (onlineRevEl) onlineRevEl.textContent = formatCompactRupiah(m.online_summary.rp);
            if (onlineShareEl) onlineShareEl.innerHTML = `Pangsa: <strong>${m.online_summary.pct_rp}%</strong> &bull; ${formatNumber(m.online_summary.qty)} item`;

            if (dineInRevEl) dineInRevEl.textContent = formatCompactRupiah(m.dine_in_summary.rp);
            if (dineInShareEl) dineInShareEl.innerHTML = `Pangsa: <strong>${m.dine_in_summary.pct_rp}%</strong> &bull; ${formatNumber(m.dine_in_summary.qty)} item`;

            if (taRevEl) taRevEl.textContent = formatCompactRupiah(m.take_away_summary.rp);
            if (taShareEl) taShareEl.innerHTML = `Pangsa: <strong>${m.take_away_summary.pct_rp}%</strong> &bull; ${formatNumber(m.take_away_summary.qty)} item`;

            const hero = m.top_items_rp[0];
            if (hero && heroNameEl) {
                heroNameEl.textContent = hero.name;
                heroNameEl.title = hero.name;
            }
            if (hero && heroDetailEl) {
                const pct = ((hero.total_rp / m.total_rp) * 100).toFixed(1);
                heroDetailEl.innerHTML = `Omzet: <strong>${formatCompactRupiah(hero.total_rp)} (${pct}%)</strong>`;
            }
        }
    }

    // Handler Ganti Bulan
    window.handleSalesMonthChange = function(val) {
        currentSelectedMonth = val;
        renderDashboardMetrics();
        updateChartsForMonth();
        filterProductTable();
    };

    // Render Charts
    function renderAllCharts() {
        if (!salesData) return;
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const textColor = isDark ? '#cbd5e1' : '#475569';
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)';

        // 1. Sales Trend Chart (Dual Axis)
        const trendCanvas = document.getElementById('salesTrendChart');
        if (trendCanvas) {
            const trendData = salesData.overall_summary.monthly_trend;
            const labels = trendData.map(d => d.month);
            const revenuesJt = trendData.map(d => Math.round(d.rp / 1000000));
            const quantities = trendData.map(d => d.qty);

            if (chartInstances.salesTrend) chartInstances.salesTrend.destroy();
            chartInstances.salesTrend = new Chart(trendCanvas, {
                data: {
                    labels: labels,
                    datasets: [
                        {
                            type: 'bar',
                            label: 'Omzet Penjualan (Juta Rp)',
                            data: revenuesJt,
                            backgroundColor: labels.map(l => l === 'MARET' ? '#B8161D' : 'rgba(217, 34, 42, 0.75)'),
                            hoverBackgroundColor: '#B8161D',
                            borderRadius: 6,
                            yAxisID: 'y',
                            order: 2
                        },
                        {
                            type: 'line',
                            label: 'Volume Qty Produk',
                            data: quantities,
                            borderColor: '#F59E0B',
                            backgroundColor: '#F59E0B',
                            borderWidth: 3,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            tension: 0.35,
                            yAxisID: 'y1',
                            order: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    if (context.dataset.yAxisID === 'y') {
                                        return ` Omzet: Rp ${context.parsed.y} Juta`;
                                    }
                                    return ` Volume: ${context.parsed.y.toLocaleString('id-ID')} unit`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: textColor, font: { family: 'Plus Jakarta Sans', size: 11, weight: 600 } }
                        },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            grid: { color: gridColor },
                            ticks: {
                                color: textColor,
                                font: { family: 'Plus Jakarta Sans', size: 10 },
                                callback: val => 'Rp ' + val + 'Jt'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: { drawOnChartArea: false },
                            ticks: {
                                color: '#F59E0B',
                                font: { family: 'Plus Jakarta Sans', size: 10 },
                                callback: val => (val / 1000).toFixed(0) + 'k'
                            }
                        }
                    }
                }
            });
        }

        // 2. Channel Share Donut Chart
        renderChannelShareDonut();

        // 3. On-Premise vs Off-Premise Stacked Bar
        const premiseCanvas = document.getElementById('premiseChart');
        if (premiseCanvas) {
            const trendData = salesData.overall_summary.monthly_trend;
            const labels = trendData.map(d => d.month);
            const dineInJt = trendData.map(d => Math.round(d.di_rp / 1000000));
            const offPremiseJt = trendData.map(d => Math.round((d.rp - d.di_rp) / 1000000));

            if (chartInstances.premise) chartInstances.premise.destroy();
            chartInstances.premise = new Chart(premiseCanvas, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Dine In (Makan Tempat)',
                            data: dineInJt,
                            backgroundColor: '#3B82F6',
                            borderRadius: 4
                        },
                        {
                            label: 'Off-Premise (Online+TA+DL+DT)',
                            data: offPremiseJt,
                            backgroundColor: '#F59E0B',
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true,
                            grid: { display: false },
                            ticks: { color: textColor, font: { family: 'Plus Jakarta Sans', size: 10, weight: 600 } }
                        },
                        y: {
                            stacked: true,
                            grid: { color: gridColor },
                            ticks: {
                                color: textColor,
                                font: { family: 'Plus Jakarta Sans', size: 10 },
                                callback: val => 'Rp ' + val + 'Jt'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: { color: textColor, font: { family: 'Plus Jakarta Sans', size: 11, weight: 600 } }
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => ` ${ctx.dataset.label}: Rp ${ctx.parsed.y} Juta`
                            }
                        }
                    }
                }
            });
        }

        // 4. Group Revenue Horizontal Bar Chart
        renderGroupRevenueChart();
    }

    // Render Donut Chart
    function renderChannelShareDonut() {
        const donutCanvas = document.getElementById('channelShareChart');
        if (!donutCanvas || !salesData) return;

        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const textColor = isDark ? '#cbd5e1' : '#475569';

        let channelData = [];
        if (currentSelectedMonth === 'ALL') {
            const ch = salesData.overall_summary.cumulative_channels;
            channelData = [
                { label: 'Dine In (DI)', value: ch.di.rp, color: '#3B82F6' },
                { label: 'Take Away (TA)', value: ch.ta.rp, color: '#8B5CF6' },
                { label: 'GoFood (GF)', value: ch.gf.rp, color: '#00AA13' },
                { label: 'ShopeeFood (SF)', value: ch.sf.rp, color: '#EE4D2D' },
                { label: 'GrabFood (GR)', value: ch.gr.rp, color: '#00B14F' },
                { label: 'Delivery (DL)', value: ch.dl.rp, color: '#F59E0B' },
                { label: 'Drive Thru (DT)', value: ch.dt.rp, color: '#06B6D4' },
                { label: 'Birthday/Event (BP)', value: ch.bp.rp, color: '#EC4899' },
            ];
        } else {
            const m = salesData.monthly_summaries[currentSelectedMonth];
            if (m) {
                const ch = m.channels;
                channelData = [
                    { label: 'Dine In (DI)', value: ch.di.rp, color: '#3B82F6' },
                    { label: 'Take Away (TA)', value: ch.ta.rp, color: '#8B5CF6' },
                    { label: 'GoFood (GF)', value: ch.gf.rp, color: '#00AA13' },
                    { label: 'ShopeeFood (SF)', value: ch.sf.rp, color: '#EE4D2D' },
                    { label: 'GrabFood (GR)', value: ch.gr.rp, color: '#00B14F' },
                    { label: 'Delivery (DL)', value: ch.dl.rp, color: '#F59E0B' },
                    { label: 'Drive Thru (DT)', value: ch.dt.rp, color: '#06B6D4' },
                    { label: 'Birthday/Event (BP)', value: ch.bp.rp, color: '#EC4899' },
                ];
            }
        }

        const totalVal = channelData.reduce((acc, c) => acc + c.value, 0);

        if (chartInstances.channelDonut) chartInstances.channelDonut.destroy();
        chartInstances.channelDonut = new Chart(donutCanvas, {
            type: 'doughnut',
            data: {
                labels: channelData.map(c => c.label),
                datasets: [{
                    data: channelData.map(c => c.value),
                    backgroundColor: channelData.map(c => c.color),
                    borderWidth: 2,
                    borderColor: isDark ? '#131a29' : '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                const val = ctx.parsed;
                                const pct = ((val / totalVal) * 100).toFixed(1);
                                return ` ${ctx.label}: ${formatCompactRupiah(val)} (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Legend list
        const legendContainer = document.getElementById('channelLegendList');
        if (legendContainer) {
            legendContainer.innerHTML = channelData.map(c => {
                const pct = ((c.value / totalVal) * 100).toFixed(1);
                return `
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 2px 4px;">
                        <span style="display: flex; align-items: center; gap: 6px; font-weight: 600; color: var(--text-body);">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: ${c.color}; flex-shrink: 0;"></span>
                            <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100px;">${c.label}</span>
                        </span>
                        <strong style="color: var(--text-main);">${pct}%</strong>
                    </div>
                `;
            }).join('');
        }
    }

    // Render Group Revenue Horizontal Bar
    function renderGroupRevenueChart() {
        const groupCanvas = document.getElementById('groupRevenueChart');
        if (!groupCanvas || !salesData) return;

        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const textColor = isDark ? '#cbd5e1' : '#475569';
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)';

        let groupsData = [];
        if (currentSelectedMonth === 'ALL') {
            groupsData = salesData.overall_summary.cumulative_groups.slice(0, 8);
        } else {
            const m = salesData.monthly_summaries[currentSelectedMonth];
            if (m) {
                const gList = Object.values(m.groups).sort((a, b) => b.rp - a.rp);
                groupsData = gList.slice(0, 8).map(g => ({
                    code: g.code,
                    name: g.name,
                    total_rp: g.rp,
                    pct_rp: ((g.rp / m.total_rp) * 100).toFixed(1)
                }));
            }
        }

        const labels = groupsData.map(g => g.name);
        const valuesJt = groupsData.map(g => Math.round(g.total_rp / 1000000));

        if (chartInstances.groupRevenue) chartInstances.groupRevenue.destroy();
        chartInstances.groupRevenue = new Chart(groupCanvas, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Omzet (Juta Rp)',
                    data: valuesJt,
                    backgroundColor: [
                        '#D9222A', '#F59E0B', '#10B981', '#3B82F6', 
                        '#8B5CF6', '#EC4899', '#06B6D4', '#64748B'
                    ],
                    borderRadius: 6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ` Omzet: Rp ${ctx.parsed.x} Juta`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: gridColor },
                        ticks: {
                            color: textColor,
                            font: { family: 'Plus Jakarta Sans', size: 10 },
                            callback: val => 'Rp ' + val + 'Jt'
                        }
                    },
                    y: {
                        grid: { display: false },
                        ticks: {
                            color: textColor,
                            font: { family: 'Plus Jakarta Sans', size: 11, weight: 600 }
                        }
                    }
                }
            }
        });
    }

    // Update charts when month dropdown changes
    function updateChartsForMonth() {
        renderChannelShareDonut();
        renderGroupRevenueChart();
    }

    // Render Tab 1: Monthly Table
    function renderMonthlyTable() {
        if (!salesData) return;
        const tbody = document.getElementById('monthly-summary-tbody');
        const tfoot = document.getElementById('monthly-summary-tfoot');
        if (!tbody) return;

        const trend = salesData.overall_summary.monthly_trend;
        const avgRp = salesData.overall_summary.avg_monthly_revenue;

        tbody.innerHTML = trend.map((row, idx) => {
            const mSummary = salesData.monthly_summaries[row.month];
            const avgPrice = mSummary ? mSummary.avg_price_per_item : Math.round(row.rp / row.qty);
            
            // Growth indicator
            let growthHtml = '<span style="color: var(--text-muted);">&mdash;</span>';
            if (row.growth_rp !== null && row.growth_rp !== undefined) {
                if (row.growth_rp > 0) {
                    growthHtml = `<span style="color: #10b981; font-weight: 700;"><i class="ti ti-arrow-up-right"></i> +${row.growth_rp}%</span>`;
                } else if (row.growth_rp < 0) {
                    growthHtml = `<span style="color: #ef4444; font-weight: 700;"><i class="ti ti-arrow-down-right"></i> ${row.growth_rp}%</span>`;
                } else {
                    growthHtml = `<span style="color: var(--text-muted);">0.0%</span>`;
                }
            }

            // Status Badge
            let statusBadge = '';
            if (row.month === 'MARET') {
                statusBadge = '<span style="padding: 3px 8px; border-radius: 6px; background: rgba(217,34,42,0.12); color: var(--hokben-red); font-size: 0.72rem; font-weight: 700;">Record Peak</span>';
            } else if (row.rp >= avgRp) {
                statusBadge = '<span style="padding: 3px 8px; border-radius: 6px; background: rgba(16,185,129,0.12); color: #10b981; font-size: 0.72rem; font-weight: 700;">Above Target</span>';
            } else {
                statusBadge = '<span style="padding: 3px 8px; border-radius: 6px; background: rgba(100,116,139,0.12); color: var(--text-muted); font-size: 0.72rem; font-weight: 700;">Normal</span>';
            }

            return `
                <tr style="border-bottom: 1px solid var(--border-color); cursor: pointer;" onclick="document.getElementById('salesMonthFilter').value='${row.month}'; handleSalesMonthChange('${row.month}')" title="Klik untuk filter bulan ${row.month}">
                    <td style="padding: 10px 14px; font-weight: 700; color: var(--text-main);">
                        ${row.month} 2026
                    </td>
                    <td style="padding: 10px 14px; text-align: right; font-weight: 600;">${formatNumber(row.qty)}</td>
                    <td style="padding: 10px 14px; text-align: right; font-weight: 800; color: ${row.month === 'MARET' ? 'var(--hokben-red)' : 'var(--text-main)'};">
                        ${formatRupiah(row.rp)}
                    </td>
                    <td style="padding: 10px 14px; text-align: right;">${growthHtml}</td>
                    <td style="padding: 10px 14px; text-align: right; color: #3B82F6; font-weight: 600;">${formatRupiah(row.di_rp)}</td>
                    <td style="padding: 10px 14px; text-align: right; color: #8B5CF6; font-weight: 600;">${formatRupiah(row.ta_rp)}</td>
                    <td style="padding: 10px 14px; text-align: right; color: #10b981; font-weight: 700;">${formatRupiah(row.online_rp)}</td>
                    <td style="padding: 10px 14px; text-align: right; color: #F59E0B;">${formatRupiah(row.dl_rp + row.dt_rp)}</td>
                    <td style="padding: 10px 14px; text-align: right; font-weight: 600;">${formatRupiah(avgPrice)}</td>
                    <td style="padding: 10px 14px; text-align: center;">${statusBadge}</td>
                </tr>
            `;
        }).join('');

        if (tfoot) {
            const ov = salesData.overall_summary;
            const diTot = ov.cumulative_channels.di.rp;
            const taTot = ov.cumulative_channels.ta.rp;
            const onTot = ov.cumulative_online.rp;
            const dlDtTot = ov.cumulative_channels.dl.rp + ov.cumulative_channels.dt.rp;
            const overallAvgPrice = Math.round(ov.total_revenue / ov.total_qty);

            tfoot.innerHTML = `
                <tr>
                    <td style="padding: 12px 14px; text-align: left; font-weight: 800; color: var(--text-main);">TOTAL 10 BULAN</td>
                    <td style="padding: 12px 14px; text-align: right; font-weight: 800; color: var(--text-main);">${formatNumber(ov.total_qty)}</td>
                    <td style="padding: 12px 14px; text-align: right; font-weight: 800; color: var(--hokben-red); font-size: 0.95rem;">${formatRupiah(ov.total_revenue)}</td>
                    <td style="padding: 12px 14px; text-align: right; color: var(--text-muted);">&mdash;</td>
                    <td style="padding: 12px 14px; text-align: right; color: #3B82F6; font-weight: 800;">${formatRupiah(diTot)}</td>
                    <td style="padding: 12px 14px; text-align: right; color: #8B5CF6; font-weight: 800;">${formatRupiah(taTot)}</td>
                    <td style="padding: 12px 14px; text-align: right; color: #10b981; font-weight: 800;">${formatRupiah(onTot)}</td>
                    <td style="padding: 12px 14px; text-align: right; color: #F59E0B; font-weight: 800;">${formatRupiah(dlDtTot)}</td>
                    <td style="padding: 12px 14px; text-align: right; font-weight: 800;">${formatRupiah(overallAvgPrice)}</td>
                    <td style="padding: 12px 14px; text-align: center;"><span style="padding: 3px 8px; border-radius: 6px; background: rgba(16,185,129,0.15); color: #10b981; font-weight: 800; font-size: 0.72rem;">10 Bulan Valid</span></td>
                </tr>
            `;
        }
    }

    // Render Tab 2: Channel Table
    function renderChannelTable() {
        if (!salesData) return;
        const tbody = document.getElementById('channel-comparison-tbody');
        if (!tbody) return;

        const ch = salesData.overall_summary.cumulative_channels;
        const channelsMeta = [
            { key: 'di', type: 'On-Premise', typeColor: '#3B82F6', desc: 'Makan di tempat meja store Plaju. Ramai saat makan siang & weekend.' },
            { key: 'ta', type: 'Off-Premise', typeColor: '#8B5CF6', desc: 'Take Away langsung oleh customer. Membutuhkan kecepatan packing bento.' },
            { key: 'gf', type: 'Online OFO', typeColor: '#00AA13', desc: 'Kanal aggregator terbesar (#1 di Plaju). Menjadi backbone penjualan sore-malam.' },
            { key: 'sf', type: 'Online OFO', typeColor: '#EE4D2D', desc: 'Kanal aggregator #2 dengan pertumbuhan pesat melalui promo voucher diskon.' },
            { key: 'gr', type: 'Online OFO', typeColor: '#00B14F', desc: 'Kanal aggregator #3 dengan basket size tinggi untuk paket keluarga.' },
            { key: 'dl', type: 'Direct Delivery', typeColor: '#F59E0B', desc: 'Pemesanan langsung via HokBen Apps/Call Center 1500505. Tiket size tertinggi (Rp 48k/item).' },
            { key: 'dt', type: 'Drive Thru', typeColor: '#06B6D4', desc: 'Jalur khusus kendaraan roda 2 dan roda 4 di Store Ruko Plaju.' },
            { key: 'bp', type: 'Event / Party', typeColor: '#EC4899', desc: 'Paket Birthday Party anak & arisan (Paket Ureshi 1 & 2).' }
        ];

        tbody.innerHTML = channelsMeta.map(cm => {
            const data = ch[cm.key];
            const avgPrice = data.qty > 0 ? Math.round(data.rp / data.qty) : 0;
            return `
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 12px 14px; font-weight: 800; color: var(--text-main);">
                        ${data.name} (${cm.key.toUpperCase()})
                    </td>
                    <td style="padding: 12px 14px; text-align: center;">
                        <span style="padding: 3px 8px; border-radius: 6px; background: rgba(0,0,0,0.05); color: ${cm.typeColor}; font-weight: 700; font-size: 0.72rem; border: 1px solid ${cm.typeColor}30;">
                            ${cm.type}
                        </span>
                    </td>
                    <td style="padding: 12px 14px; text-align: right; font-weight: 600;">${formatNumber(data.qty)}</td>
                    <td style="padding: 12px 14px; text-align: right; font-weight: 700;">${data.pct_qty}%</td>
                    <td style="padding: 12px 14px; text-align: right; font-weight: 800; color: var(--text-main);">${formatRupiah(data.rp)}</td>
                    <td style="padding: 12px 14px; text-align: right; font-weight: 800; color: ${cm.typeColor};">${data.pct_rp}%</td>
                    <td style="padding: 12px 14px; text-align: right; font-weight: 600;">${formatRupiah(avgPrice)}</td>
                    <td style="padding: 12px 14px; color: var(--text-body); font-size: 0.78rem;">${cm.desc}</td>
                </tr>
            `;
        }).join('');
    }

    // Render Tab 3: Group Table
    function renderGroupTable() {
        if (!salesData) return;
        const tbody = document.getElementById('group-analysis-tbody');
        if (!tbody) return;

        const groups = salesData.overall_summary.cumulative_groups;
        const groupStrategicRoles = {
            '001': 'Ala Carte Traffic Driver & Add-on Upselling',
            '002': 'Core Revenue & Profit Backbone (61.9% Omzet Toko)',
            '003': 'Impulse Buying & Pelengkap Snack Sore',
            '004': 'Cross-selling Menu Hangat & Pelengkap Bento',
            '005': 'Sweet Treats & Dessert Margin Builder',
            '006': 'High Margin Beverage Attachment (Wajib Ditawarkan)',
            '007': 'Healthy Option & Segmen Keluarga',
            '008': 'Fundamental Side Dishes (Nasi & Ekstra Mayo)',
            '009': 'Promotional Support & Merchandise',
            '010': 'Brand Loyalty Gifts & Souvenir Kidzu',
            '011': 'Operational Packaging Materials (Kantong & Box)',
            '012': 'Kids Segment Driver (Kidzu Bento Milo Series)',
            '013': 'Campaign Special & Super Bowl Online Promo',
            '014': 'Complementary Saus & Upgrade Kemasan',
            '016': 'Group Gathering & Birthday Party Revenue',
            '017': 'Price Sensitive Value Seeker (Hoka Hemat Series)'
        };

        const heroPerGroup = {
            '001': 'CHICKEN KATSU (Rp 68,9 Jt)',
            '002': 'NEW BENTO SPECIAL 4 (Rp 505,4 Jt)',
            '003': 'TAKOYAKI MENTAI (Rp 10,7 Jt)',
            '004': 'SHRIMP DUMPLING (Rp 9,1 Jt)',
            '005': 'ES SARANG BURUNG (Rp 1,7 Jt)',
            '006': 'COLD OCHA (Rp 22,7 Jt) & MILO (Rp 18,7 Jt)',
            '007': 'NEW MELON JUICE (Rp 4,1 Jt)',
            '008': 'RICE ONLY (Rp 22,8 Jt) & NASI TO (Rp 19,2 Jt)',
            '010': 'TUMBLER HOKBEN & FREE OCHA',
            '011': 'TAKE AWAY BAG REGULER (Rp 6,4 Jt)',
            '012': 'KIDZU BENTO MILO 1 (Rp 23,2 Jt)',
            '013': 'HOKA HEMAT 1 WIL (Rp 114,0 Jt)',
            '014': 'SAUS SAMBAL EXTRA PEDAS (Rp 1,6 Jt)',
            '016': 'URESHI 1 SS 1 (Rp 13,2 Jt)',
            '017': 'HOKA HEMAT 3 (Rp 176,9 Jt)'
        };

        tbody.innerHTML = groups.map(g => {
            const avgP = g.total_qty > 0 ? Math.round(g.total_rp / g.total_qty) : 0;
            const hero = heroPerGroup[g.code] || '-';
            const role = groupStrategicRoles[g.code] || 'Support Menu Item';
            const isTop3 = ['002', '001', '013'].includes(g.code);

            return `
                <tr style="border-bottom: 1px solid var(--border-color); ${isTop3 ? 'background: rgba(245,158,11,0.03);' : ''}">
                    <td style="padding: 10px 12px; text-align: center; font-weight: 800; font-family: monospace; color: ${isTop3 ? 'var(--hokben-red)' : 'var(--text-muted)'};">
                        ${g.code}
                    </td>
                    <td style="padding: 10px 12px; font-weight: 800; color: var(--text-main);">
                        ${g.name}
                        ${isTop3 ? '<span style="margin-left: 6px; font-size: 0.68rem; font-weight: 700; background: rgba(217,34,42,0.1); color: var(--hokben-red); padding: 2px 6px; border-radius: 4px;">TOP</span>' : ''}
                    </td>
                    <td style="padding: 10px 12px; text-align: right; font-weight: 600;">${formatNumber(g.total_qty)}</td>
                    <td style="padding: 10px 12px; text-align: right; font-weight: 800; color: ${isTop3 ? 'var(--hokben-red)' : 'var(--text-main)'};">
                        ${formatRupiah(g.total_rp)}
                    </td>
                    <td style="padding: 10px 12px; text-align: right; font-weight: 700; color: ${isTop3 ? '#F59E0B' : 'var(--text-body)'};">
                        ${g.pct_rp}%
                    </td>
                    <td style="padding: 10px 12px; text-align: right; font-weight: 600;">${formatRupiah(avgP)}</td>
                    <td style="padding: 10px 12px; font-weight: 600; color: var(--text-main); font-size: 0.78rem;">${hero}</td>
                    <td style="padding: 10px 12px; font-size: 0.76rem; color: var(--text-muted);">${role}</td>
                </tr>
            `;
        }).join('');
    }

    // Render Tab 4: Product Table
    window.filterProductTable = function() {
        if (!salesData) return;
        const tbody = document.getElementById('product-ranking-tbody');
        const countInfo = document.getElementById('productTableCountInfo');
        const searchVal = (document.getElementById('productSearchInput')?.value || '').toLowerCase().trim();
        const groupVal = document.getElementById('productGroupFilter')?.value || 'ALL';
        const limitVal = document.getElementById('productRowLimit')?.value || '50';

        let items = [];
        if (currentSelectedMonth === 'ALL') {
            items = salesData.all_items_cumulative || [];
        } else {
            items = (salesData.monthly_items && salesData.monthly_items[currentSelectedMonth]) || [];
        }

        const filtered = items.filter(itm => {
            const matchesSearch = !searchVal || 
                itm.name.toLowerCase().includes(searchVal) || 
                itm.plu.includes(searchVal);
            const matchesGroup = groupVal === 'ALL' || itm.group_code === groupVal;
            return matchesSearch && matchesGroup;
        });

        const limit = limitVal === 'ALL' ? filtered.length : parseInt(limitVal);
        const displayed = filtered.slice(0, limit);

        if (countInfo) {
            countInfo.textContent = `Menampilkan ${displayed.length} dari ${filtered.length} item ${currentSelectedMonth !== 'ALL' ? '(' + currentSelectedMonth + ')' : '(Kumulatif 10 Bulan)'}`;
        }

        if (displayed.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="13" style="text-align: center; padding: 30px; color: var(--text-muted);">
                        <i class="ti ti-search-off" style="font-size: 2rem; display: block; margin-bottom: 8px;"></i>
                        Tidak ditemukan produk dengan kriteria pencarian "${searchVal}".
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = displayed.map((itm, idx) => {
            const rank = itm.rank || (idx + 1);
            let rankBadge = '';
            if (rank === 1) {
                rankBadge = '<span style="display:inline-block; width:24px; height:24px; border-radius:50%; background:#FFD700; color:#000; font-weight:800; line-height:24px; font-size:0.75rem;">1</span>';
            } else if (rank === 2) {
                rankBadge = '<span style="display:inline-block; width:24px; height:24px; border-radius:50%; background:#C0C0C0; color:#000; font-weight:800; line-height:24px; font-size:0.75rem;">2</span>';
            } else if (rank === 3) {
                rankBadge = '<span style="display:inline-block; width:24px; height:24px; border-radius:50%; background:#CD7F32; color:#fff; font-weight:800; line-height:24px; font-size:0.75rem;">3</span>';
            } else {
                rankBadge = `<span style="font-weight:700; color:var(--text-muted);">${rank}</span>`;
            }

            // Classification badge
            let classBadge = '';
            if (rank <= 10) {
                classBadge = '<span style="padding: 2px 8px; border-radius: 4px; background: rgba(217,34,42,0.12); color: var(--hokben-red); font-size: 0.7rem; font-weight: 700;">Hero #Top10</span>';
            } else if (rank <= 30) {
                classBadge = '<span style="padding: 2px 8px; border-radius: 4px; background: rgba(245,158,11,0.12); color: #F59E0B; font-size: 0.7rem; font-weight: 700;">Fast Moving</span>';
            } else if (itm.total_rp > 10000000) {
                classBadge = '<span style="padding: 2px 8px; border-radius: 4px; background: rgba(16,185,129,0.12); color: #10b981; font-size: 0.7rem; font-weight: 700;">Core SKU</span>';
            } else {
                classBadge = '<span style="padding: 2px 8px; border-radius: 4px; background: rgba(100,116,139,0.1); color: var(--text-muted); font-size: 0.7rem;">Regular</span>';
            }

            return `
                <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s;" onmouseover="this.style.background='var(--table-hover)'" onmouseout="this.style.background='none'">
                    <td style="padding: 8px 10px; text-align: center;">${rankBadge}</td>
                    <td style="padding: 8px 12px; font-family: monospace; font-weight: 700; color: var(--text-muted); font-size: 0.78rem;">${itm.plu}</td>
                    <td style="padding: 8px 12px; font-weight: 800; color: var(--text-main);">${itm.name}</td>
                    <td style="padding: 8px 12px; color: var(--text-muted); font-size: 0.78rem;">
                        <span style="padding: 2px 6px; border-radius: 4px; background: var(--bg-subtle);">${itm.group_code || ''} ${itm.group_name || ''}</span>
                    </td>
                    <td style="padding: 8px 12px; text-align: right; font-weight: 600;">${formatRupiah(itm.unit_price)}</td>
                    <td style="padding: 8px 12px; text-align: right; color: #3B82F6;">${formatNumber(itm.di_qty || 0)}</td>
                    <td style="padding: 8px 12px; text-align: right; color: #8B5CF6;">${formatNumber(itm.ta_qty || 0)}</td>
                    <td style="padding: 8px 12px; text-align: right; color: #10b981; font-weight: 600;">${formatNumber(itm.online_qty || 0)}</td>
                    <td style="padding: 8px 12px; text-align: right; color: #F59E0B;">${formatNumber(itm.other_qty || 0)}</td>
                    <td style="padding: 8px 12px; text-align: right; font-weight: 800; color: var(--text-main);">${formatNumber(itm.total_qty)}</td>
                    <td style="padding: 8px 12px; text-align: right; font-weight: 800; color: var(--hokben-red);">${formatRupiah(itm.total_rp)}</td>
                    <td style="padding: 8px 12px; text-align: right; font-weight: 700; color: #F59E0B;">${itm.pct_rp !== undefined ? itm.pct_rp + '%' : '-'}</td>
                    <td style="padding: 8px 12px; text-align: center;">${classBadge}</td>
                </tr>
            `;
        }).join('');
    };

    function renderProductTable() {
        window.filterProductTable();
    }

    // Render Tab 5: Area 19 Store List Reference
    function renderArea19StoreList() {
        if (!salesData) return;
        const tbody = document.getElementById('area19-store-list-tbody');
        if (!tbody) return;

        tbody.innerHTML = salesData.area19_stores.map(st => {
            const isCurrent = st.code === 'A20';
            return `
                <tr style="border-bottom: 1px solid var(--border-color); ${isCurrent ? 'background: rgba(217,34,42,0.06); font-weight: 700;' : ''}">
                    <td style="padding: 8px 12px; text-align: center;">${st.no}</td>
                    <td style="padding: 8px 12px; text-align: center; font-family: monospace; font-weight: 800; color: ${isCurrent ? 'var(--hokben-red)' : 'var(--text-main)'};">
                        ${st.code}
                    </td>
                    <td style="padding: 8px 12px; font-weight: 800; color: var(--text-main);">
                        ${st.name}
                        ${isCurrent ? ' <span style="font-size:0.7rem; background:var(--hokben-red); color:#fff; padding:2px 6px; border-radius:4px; margin-left:6px;">Store Ini</span>' : ''}
                    </td>
                    <td style="padding: 8px 12px; color: var(--text-body);">
                        <span style="padding: 2px 8px; border-radius: 4px; background: var(--bg-subtle); font-size: 0.74rem;">${st.type}</span>
                    </td>
                    <td style="padding: 8px 12px; color: var(--text-muted);">${st.area}</td>
                    <td style="padding: 8px 12px; text-align: center;">
                        ${isCurrent ? '<span style="color:#10b981; font-weight:700;"><i class="ti ti-circle-check"></i> Dianalisis Lengkap</span>' : '<span style="color:var(--text-muted); font-size:0.75rem;">Master Reference</span>'}
                    </td>
                </tr>
            `;
        }).join('');
    }

    // Export CSV Functionality
    window.exportSalesDataCSV = function() {
        if (!salesData) {
            alert('Data penjualan belum selesai dimuat.');
            return;
        }

        let csv = 'PLU,NAMA MENU,KODE GROUP,NAMA GROUP,EST HARGA,QTY DINE IN,QTY TAKE AWAY,QTY ONLINE,QTY LAIN,TOTAL QTY,TOTAL RUPIAH,SHARE %\n';
        const items = salesData.all_items_cumulative || [];
        items.forEach(itm => {
            const cleanName = `"${itm.name.replace(/"/g, '""')}"`;
            const cleanGroup = `"${(itm.group_name || '').replace(/"/g, '""')}"`;
            csv += `${itm.plu},${cleanName},${itm.group_code || ''},${cleanGroup},${itm.unit_price},${itm.di_qty},${itm.ta_qty},${itm.online_qty},${itm.other_qty},${itm.total_qty},${itm.total_rp},${itm.pct_rp}\n`;
        });

        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `HokBen_A20_Plaju_Analisa_Penjualan_2026.csv`;
        link.click();
    };

    // Custom Drag-and-Drop & Excel Upload Handler
    window.handleCustomSalesDrop = function(e) {
        e.preventDefault();
        const files = e.dataTransfer.files;
        if (files.length > 0) processUploadedExcel(files[0]);
    };

    window.handleCustomSalesUpload = function(e) {
        const files = e.target.files;
        if (files.length > 0) processUploadedExcel(files[0]);
    };

    function processUploadedExcel(file) {
        const statusEl = document.getElementById('uploadStatusMsg');
        if (statusEl) {
            statusEl.style.display = 'block';
            statusEl.style.color = '#3B82F6';
            statusEl.innerHTML = `<i class="ti ti-loader spin"></i> Sedang memproses file ${file.name}...`;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                if (typeof XLSX === 'undefined') {
                    throw new Error('Pustaka SheetJS (XLSX) tidak tersedia di browser.');
                }
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });
                
                if (statusEl) {
                    statusEl.style.color = '#10B981';
                    statusEl.innerHTML = `<i class="ti ti-check"></i> File <strong>${file.name}</strong> berhasil dibaca! Ditemukan ${workbook.SheetNames.length} sheet: ${workbook.SheetNames.join(', ')}.`;
                }
            } catch (err) {
                console.error(err);
                if (statusEl) {
                    statusEl.style.color = '#EF4444';
                    statusEl.innerHTML = `<i class="ti ti-x"></i> Gagal membaca file: ${err.message}`;
                }
            }
        };
        reader.readAsArrayBuffer(file);
    }

    // Handler Pilihan Berkas dari Histori Upload
    window.handleHistoryFileSelect = function(subId) {
        const card = document.getElementById('history-file-analysis-card');
        if (!card) return;

        if (!subId || subId === 'DEFAULT') {
            card.style.display = 'none';
            card.innerHTML = '';
            return;
        }

        card.style.display = 'block';
        card.innerHTML = `
            <div class="glass-panel" style="padding: 24px; text-align: center; color: var(--text-muted); border-left: 4px solid var(--hokben-yellow-dark);">
                <i class="ti ti-loader" style="font-size: 2rem; display: block; margin-bottom: 8px; animation: spin 1s linear infinite;"></i>
                <span>Memuat data hasil analisis berkas #${subId}...</span>
            </div>
        `;

        fetch(`/submissions/${subId}/analysis-json`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (!data || !data.success || !data.analysis) {
                card.innerHTML = `
                    <div class="glass-panel" style="padding: 18px 24px; border-left: 4px solid #EF4444; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                        <div>
                            <p style="margin: 0; font-weight: 700; color: #EF4444; font-size: 0.95rem;">
                                <i class="ti ti-alert-triangle"></i> Data analisis belum selesai diproses untuk berkas ini.
                            </p>
                            <p style="margin: 4px 0 0 0; font-size: 0.8rem; color: var(--text-muted);">
                                Anda dapat mengunduh berkas asli atau memilih berkas lain dari daftar histori.
                            </p>
                        </div>
                        <button type="button" class="btn-outline" onclick="document.getElementById('historyFileSelect').value='DEFAULT'; handleHistoryFileSelect('DEFAULT')" style="padding: 6px 14px; font-size: 0.82rem;">
                            Tutup
                        </button>
                    </div>
                `;
                return;
            }

            const sub = data.submission;
            const a = data.analysis;
            const m = a.metrics || {};
            const channels = m.channels || null;
            const topItems = a.top_items || [];
            const insights = a.insights || [];

            let html = `
                <div class="glass-panel" style="padding: 24px; border-left: 4px solid #10b981; position: relative;">
                    <!-- Header Info Berkas -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 14px; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px; flex-wrap: wrap;">
                                <span style="font-size: 0.72rem; font-weight: 800; background: rgba(16,185,129,0.15); color: #10b981; border: 1px solid rgba(16,185,129,0.3); padding: 3px 10px; border-radius: 20px;">
                                    BERKAS HISTORI #${sub.id}
                                </span>
                                <span style="font-size: 0.76rem; color: var(--text-muted);">
                                    ${sub.module_name} &bull; ${sub.created_at}
                                </span>
                            </div>
                            <h3 style="margin: 0 0 6px 0; font-size: 1.25rem; font-weight: 800; color: var(--text-main);">
                                ${sub.file_name}
                            </h3>
                            <p style="margin: 0; font-size: 0.82rem; color: var(--text-muted);">
                                Diunggah oleh: <strong style="color: #FFC400;">${sub.user_name}</strong> (${sub.user_division}) &bull; Ukuran: ${sub.file_size}
                                &bull; Status: <strong style="color: ${sub.status === 'Lengkap' ? '#10b981' : '#F59E0B'}">${sub.status}</strong>
                            </p>
                        </div>

                        <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                            <a href="${sub.download_analysis_url}" class="btn-primary" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; font-size: 0.82rem; font-weight: 700; text-decoration: none; border-radius: 8px; background: linear-gradient(135deg, #F59E0B 0%, #D9222A 100%); color: #fff; box-shadow: 0 4px 12px rgba(245,158,11,0.25);">
                                <i class="ti ti-table-export"></i> Unduh Data Analisis (.CSV / Excel)
                            </a>
                            <a href="${sub.download_url}" class="btn-outline" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; font-size: 0.82rem; font-weight: 700; text-decoration: none; border-radius: 8px;">
                                <i class="ti ti-download"></i> Unduh File Asli
                            </a>
                            <button type="button" class="btn-outline" onclick="document.getElementById('historyFileSelect').value='DEFAULT'; handleHistoryFileSelect('DEFAULT')" title="Tutup Tampilan Berkas Ini" style="padding: 8px 12px; font-size: 0.82rem;">
                                <i class="ti ti-x"></i> Tutup
                            </button>
                        </div>
                    </div>

                    <!-- Metrik KPI Berkas Terpilih -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; margin-bottom: 20px;">
                        <div style="background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.25); border-radius: 12px; padding: 14px; text-align: center;">
                            <p style="margin: 0 0 4px 0; font-size: 0.74rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Total Omzet Berkas</p>
                            <p style="margin: 0; font-size: 1.3rem; font-weight: 800; color: var(--hokben-yellow-dark);">${m.formatted_revenue || '-'}</p>
                        </div>
                        <div style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.25); border-radius: 12px; padding: 14px; text-align: center;">
                            <p style="margin: 0 0 4px 0; font-size: 0.74rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Total Volume Porsi</p>
                            <p style="margin: 0; font-size: 1.3rem; font-weight: 800; color: #10b981;">${m.formatted_qty || '-'}</p>
                        </div>
                        <div style="background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.25); border-radius: 12px; padding: 14px; text-align: center;">
                            <p style="margin: 0 0 4px 0; font-size: 0.74rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Varian Menu Aktif</p>
                            <p style="margin: 0; font-size: 1.3rem; font-weight: 800; color: #818cf8;">${m.total_menu_variants || (m.total_rows || 0)} Item</p>
                        </div>
                        ${channels && channels.online_delivery ? `
                        <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); border-radius: 12px; padding: 14px; text-align: center;">
                            <p style="margin: 0 0 4px 0; font-size: 0.74rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Pangsa Online Delivery</p>
                            <p style="margin: 0; font-size: 1.3rem; font-weight: 800; color: #f87171;">${channels.online_delivery.percentage || '-'}</p>
                        </div>
                        ` : ''}
                    </div>

                    <!-- Breakdown Kanal Penjualan -->
                    ${channels ? `
                    <div style="margin-bottom: 20px;">
                        <p style="margin: 0 0 10px 0; font-size: 0.88rem; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 7px;">
                            <i class="ti ti-git-branch" style="color: var(--hokben-yellow-dark);"></i> Breakdown Kanal Penjualan Berkas Ini:
                        </p>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
                            ${[
                                { key: 'dine_in', label: 'Dine In', color: '#10b981', icon: 'ti-building-store' },
                                { key: 'take_away', label: 'Take Away', color: '#F59E0B', icon: 'ti-shopping-bag' },
                                { key: 'online_delivery', label: 'Online Delivery (GF/GR/SF)', color: '#f87171', icon: 'ti-truck-delivery' }
                            ].map(ch => {
                                const c = channels[ch.key];
                                if (!c) return '';
                                return `
                                    <div style="background: var(--bg-subtle); border: 1px solid var(--border-color); border-radius: 10px; padding: 12px 16px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                            <span style="font-weight: 700; font-size: 0.84rem; color: ${ch.color}; display: flex; align-items: center; gap: 6px;">
                                                <i class="ti ${ch.icon}"></i> ${ch.label}
                                            </span>
                                            <span style="font-weight: 800; font-size: 0.9rem; color: ${ch.color};">${c.percentage}</span>
                                        </div>
                                        <p style="margin: 0; font-size: 0.92rem; font-weight: 800; color: var(--text-main);">${c.formatted_revenue}</p>
                                        <p style="margin: 2px 0 0 0; font-size: 0.76rem; color: var(--text-muted);">${(c.qty || 0).toLocaleString('id-ID')} porsi terjual</p>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>
                    ` : ''}

                    <!-- Top 5 Menu Ranking -->
                    ${topItems && topItems.length > 0 ? `
                    <div style="margin-bottom: 20px;">
                        <p style="margin: 0 0 10px 0; font-size: 0.88rem; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 7px;">
                            <i class="ti ti-trophy" style="color: var(--hokben-yellow-dark);"></i> Peringkat Menu Terlaris di Berkas Ini:
                        </p>
                        <div style="display: flex; flex-direction: column; gap: 6px;">
                            ${topItems.slice(0, 5).map((it, idx) => `
                                <div style="display: flex; align-items: center; gap: 12px; background: var(--bg-subtle); border: 1px solid var(--border-color); border-radius: 8px; padding: 10px 14px;">
                                    <span style="font-size: 0.8rem; font-weight: 800; color: ${idx === 0 ? '#F59E0B' : '#94a3b8'}; width: 24px;">#${idx + 1}</span>
                                    <div style="flex: 1; min-width: 0;">
                                        <p style="margin: 0; font-weight: 700; font-size: 0.86rem; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${it.name}</p>
                                        <p style="margin: 2px 0 0 0; font-size: 0.74rem; color: var(--text-muted);">PLU ${it.plu} &bull; ${(it.total_qty || 0).toLocaleString('id-ID')} porsi</p>
                                    </div>
                                    <span style="font-weight: 800; color: var(--hokben-yellow-dark); font-size: 0.9rem;">${it.formatted_revenue}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    ` : ''}

                    <!-- Insights -->
                    ${insights && insights.length > 0 ? `
                    <div style="background: rgba(99,102,241,0.06); border: 1px solid rgba(99,102,241,0.22); border-radius: 10px; padding: 14px 16px;">
                        <p style="margin: 0 0 8px 0; font-size: 0.84rem; font-weight: 700; color: #818cf8; display: flex; align-items: center; gap: 6px;">
                            <i class="ti ti-bulb"></i> Insights &amp; Rekomendasi Otomatis Berkas:
                        </p>
                        <ul style="margin: 0; padding-left: 18px; font-size: 0.8rem; color: var(--text-body); line-height: 1.5;">
                            ${insights.map(ins => `<li style="margin-bottom: 4px;">${ins}</li>`).join('')}
                        </ul>
                    </div>
                    ` : ''}
                </div>
            `;

            card.innerHTML = html;
            card.scrollIntoView({ behavior: 'smooth', block: 'start' });
        })
        .catch(err => {
            card.innerHTML = `
                <div class="glass-panel" style="padding: 18px; border-left: 4px solid #EF4444; color: #fca5a5; font-size: 0.85rem;">
                    <i class="ti ti-alert-triangle"></i> Gagal memuat data analisis: ${err.message}
                </div>
            `;
        });
    };

    // Auto-listen to Dark / Light theme toggle
    window.addEventListener('themeChanged', function(e) {
        if (salesData) {
            renderAllCharts();
        }
    });

})();

