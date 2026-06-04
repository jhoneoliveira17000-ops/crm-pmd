<?php
// PMDCRM/dashboard.php
require_once 'src/auth.php';
require_login();
$page_title = "BI Dashboard - PMDCRM";
$body_class = "md:pl-64";
include 'includes/header.php';
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <?php include 'nav.php'; ?>

    <main class="p-4 md:p-6 max-w-[1920px] mx-auto min-h-screen bg-[var(--surface-1)] transition-colors duration-300">
        <!-- Header -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 border-b border-[var(--border-1)] pb-4">
            <div>
                <h1 class="text-2xl font-bold text-[var(--text-1)] tracking-tight flex items-center gap-2">
                    <span class="w-3 h-3 bg-green-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(34,197,94,0.6)]"></span>
                    <?php 
                        $hour = (int)date('H'); 
                        $greeting = ($hour < 12) ? 'Bom dia' : (($hour < 18) ? 'Boa tarde' : 'Boa noite'); 
                        echo $greeting . (isset($_SESSION['user_nome']) ? ', ' . e(explode(' ', $_SESSION['user_nome'])[0]) : ''); 
                    ?>
                </h1>
                <p class="text-[var(--text-3)] text-[10px] mt-1 uppercase font-bold tracking-widest flex items-center gap-2">
                    Sistema Ativo e Online
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <!-- Date Filter -->
                <div class="flex items-center gap-2 bg-[var(--surface-0)] border border-[var(--border-1)] rounded-[var(--radius-md)] px-3 py-1.5 shadow-sm">
                    <span class="text-[var(--text-3)] text-xs font-bold font-mono">PERÍODO:</span>
                    <select id="dateRangeSelect" class="bg-transparent text-[var(--text-1)] text-sm outline-none cursor-pointer font-sans font-semibold" onchange="handleDateFilterChange()">
                        <option value="3months">Últimos 3 Meses</option>
                        <option value="current">Mês Atual</option>
                        <option value="last_month">Mês Anterior</option>
                        <option value="custom">Selecionar Mês...</option>
                    </select>
                    <input type="month" id="customMonthPicker" class="text-sm border-l pl-2 border-[var(--border-1)] text-[var(--text-2)] outline-none hidden bg-transparent" onchange="handleCustomDateChange()">
                </div>
                
                <?php include 'header_icons.php'; ?>

                <!-- Notification Bell -->
                <div class="relative">
                    <button id="notifBtn" class="ds-btn ds-btn-secondary ds-btn-icon btn-spring relative">
                        <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-rose-500 rounded-full border-2 border-[var(--surface-0)] hidden animate-pulse" id="notifBadge"></span>
                        <svg class="w-5 h-5 text-[var(--text-3)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </button>
                    
                    <!-- Dropdown Notificacoes -->
                    <div id="notifDropdown" class="hidden absolute right-0 top-12 w-80 bg-[var(--surface-0)] rounded-[var(--radius-lg)] shadow-xl border border-[var(--border-1)] z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-[var(--border-1)] bg-[var(--surface-2)] flex justify-between items-center">
                            <span class="font-bold text-[var(--text-1)] text-xs tracking-widest font-mono">NOTIFICAÇÕES</span>
                            <span class="text-[10px] text-[var(--text-3)] font-bold font-mono">TEMPO REAL</span>
                        </div>
                        <ul id="notifList" class="max-h-80 overflow-y-auto">
                            <li class="p-6 text-center text-[var(--text-3)] text-xs font-mono">SEM DADOS</li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <!-- KPI Grid (High Density) with Staggered Entrance Animations -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6 ds-stagger">
            <!-- 1. MRR -->
            <div class="ds-card-metric ds-animate-spring card-spring relative overflow-hidden group">
                <div class="absolute right-2 top-2 opacity-10 text-green-500"><svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                <div class="text-[10px] text-[var(--text-3)] font-mono font-bold tracking-widest mb-1">RECEITA (MRR)</div>
                <div class="text-2xl font-bold text-[var(--text-1)] tracking-tight" id="kpi-mrr">R$ 0,00</div>
                <div class="text-[10px] text-[var(--brand)] mt-1 flex items-center gap-1 font-semibold">
                    <span>▲</span> <span class="text-[var(--text-3)] uppercase font-medium">Recorrente</span>
                </div>
                <div class="h-1 w-full bg-[var(--surface-2)] mt-3 rounded-full overflow-hidden">
                    <div class="h-full bg-[var(--brand)] w-3/4"></div>
                </div>
            </div>

            <!-- 2. Pipeline Value (CRM) -->
            <div class="ds-card-metric ds-animate-spring card-spring relative overflow-hidden group">
                <div class="absolute right-2 top-2 opacity-10 text-amber-500"><svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg></div>
                <div class="text-[10px] text-[var(--text-3)] font-mono font-bold tracking-widest mb-1">PIPELINE ABERTO</div>
                <div class="text-2xl font-bold text-[var(--text-1)] tracking-tight" id="kpi-pipeline">R$ 0,00</div>
                <div class="text-[10px] text-amber-500 mt-1 flex items-center gap-1 font-semibold">
                    <span id="kpi-leads-count" class="font-bold">0</span> LEADS ATIVOS
                </div>
                <div class="h-1 w-full bg-[var(--surface-2)] mt-3 rounded-full overflow-hidden">
                    <div class="h-full bg-amber-500 w-1/2"></div>
                </div>
            </div>

            <!-- 3. Conversion Rate -->
            <div class="ds-card-metric ds-animate-spring card-spring relative overflow-hidden group">
                <div class="absolute right-2 top-2 opacity-10 text-indigo-500"><svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                 <div class="text-[10px] text-[var(--text-3)] font-mono font-bold tracking-widest mb-1">TAXA CONVERSÃO</div>
                <div class="text-2xl font-bold text-[var(--text-1)] tracking-tight" id="kpi-conversion">0%</div>
                <div class="text-[10px] text-indigo-400 mt-1 flex items-center gap-1 font-semibold">
                    LEADS -> CLIENTES
                </div>
                <div class="h-1 w-full bg-[var(--surface-2)] mt-3 rounded-full overflow-hidden">
                    <div class="h-full bg-indigo-500 w-1/3"></div>
                </div>
            </div>

            <!-- 4. Active Clients -->
            <div class="ds-card-metric ds-animate-spring card-spring relative overflow-hidden group">
               <div class="absolute right-2 top-2 opacity-10 text-[var(--brand)]"><svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg></div>
                 <div class="text-[10px] text-[var(--text-3)] font-mono font-bold tracking-widest mb-1">BASE ATIVA</div>
                <div class="text-2xl font-bold text-[var(--text-1)] tracking-tight" id="kpi-clients">0</div>
                <div class="text-[10px] text-[var(--brand)] mt-1 flex items-center gap-1 font-semibold">
                     <span id="kpi-new-clients">+0</span> NOVOS ESTE MÊS
                </div>
                 <div class="h-1 w-full bg-[var(--surface-2)] mt-3 rounded-full overflow-hidden">
                    <div class="h-full bg-[var(--brand)] w-full"></div>
                </div>
            </div>
        </div>

        <!-- Row 2: Analytics (CAC, LTV, ROI, Time) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6 ds-stagger">
            <!-- CAC -->
            <div class="ds-card-metric ds-animate-spring card-spring">
                <div class="text-[10px] text-[var(--text-3)] font-mono font-bold tracking-widest mb-1">CAC (AQUISIÇÃO)</div>
                <div class="text-2xl font-bold text-[var(--brand)] tracking-tight" id="kpi-cac">R$ 0,00</div>
                <div class="h-1 w-10 bg-[var(--brand)] mt-2 rounded-full"></div>
            </div>

            <!-- LTV -->
             <div class="ds-card-metric ds-animate-spring card-spring">
                <div class="text-[10px] text-[var(--text-3)] font-mono font-bold tracking-widest mb-1">LTV (VALOR VITALÍCIO)</div>
                <div class="text-2xl font-bold text-blue-500 dark:text-blue-400 tracking-tight" id="kpi-ltv">R$ 0,00</div>
                 <div class="text-[10px] text-[var(--text-3)] mt-1 font-semibold">RATIO: <span id="kpi-ltv-ratio">0</span>x CAC</div>
            </div>

            <!-- ROI -->
             <div class="ds-card-metric ds-animate-spring card-spring">
                <div class="text-[10px] text-[var(--text-3)] font-mono font-bold tracking-widest mb-1">ROI MÉDIO</div>
                <div class="text-2xl font-bold text-purple-500 dark:text-purple-400 tracking-tight" id="kpi-roi">0.0x</div>
                 <div class="text-[10px] text-[var(--text-3)] mt-1 font-semibold">MÉDIA GLOBAL</div>
            </div>

            <!-- Time -->
             <div class="ds-card-metric ds-animate-spring card-spring">
                <div class="text-[10px] text-[var(--text-3)] font-mono font-bold tracking-widest mb-1">TEMPO FECHAMENTO</div>
                <div class="text-2xl font-bold text-orange-500 dark:text-orange-400 tracking-tight" id="kpi-time">0 Dias</div>
                 <div class="text-[10px] text-[var(--text-3)] mt-1 font-semibold">MÉDIA DO PERÍODO</div>
            </div>
        </div>

        <!-- CRM & Growth Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
             <!-- Sales Funnel Chart -->
            <div class="ds-card p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xs font-bold text-[var(--text-3)] uppercase tracking-wider flex items-center gap-2 font-mono">
                         <span class="w-2 h-2 rounded-full bg-[var(--brand)]"></span>
                        Funil de Vendas
                    </h3>
                </div>
                <div class="h-64 relative">
                    <canvas id="funnelChart"></canvas>
                </div>
            </div>

            <!-- Lead Sources Chart -->
            <div class="ds-card p-6">
                 <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xs font-bold text-[var(--text-3)] uppercase tracking-wider flex items-center gap-2 font-mono">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        Origem dos Leads
                    </h3>
                </div>
                <div class="h-64 relative">
                     <canvas id="sourcesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Finance Chart Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="ds-card p-6 lg:col-span-2">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xs font-bold text-[var(--text-3)] uppercase tracking-wider flex items-center gap-2 font-mono">
                        <svg class="w-4 h-4 text-[var(--brand)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                        Fluxo Financeiro (6 Meses)
                    </h3>
                </div>
                <div class="h-64 relative">
                    <canvas id="financeChart"></canvas> 
                </div>
            </div>
            
             <!-- Top Clients -->
            <div class="ds-card p-6">
                 <h3 class="text-xs font-bold text-[var(--text-3)] uppercase tracking-wider mb-4 flex items-center gap-2 font-mono">
                    <span class="text-yellow-500">★</span> Top Clientes
                </h3>
                <div class="space-y-4" id="topClientsList">
                    <!-- Populated via JS -->
                    <div class="animate-pulse flex space-x-4">
                        <div class="rounded-full bg-[var(--surface-2)] h-10 w-10"></div>
                        <div class="flex-1 space-y-2 py-1">
                            <div class="h-2 bg-[var(--surface-2)] rounded"></div>
                            <div class="h-2 bg-[var(--surface-2)] rounded w-3/4"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script>
        // Helper to retrieve style variables dynamically
        function getDesignSystemColor(varName, fallback) {
            return getComputedStyle(document.documentElement).getPropertyValue(varName).trim() || fallback;
        }

        // --- CHARTS CONFIG ---
        Chart.defaults.color = '#78716c'; 
        Chart.defaults.font.family = 'JetBrains Mono';
        
        const ctxFinance = document.getElementById('financeChart').getContext('2d');
        const ctxFunnel = document.getElementById('funnelChart').getContext('2d');
        const ctxSources = document.getElementById('sourcesChart').getContext('2d');
        
        let chartFinanceInstance = null;
        let chartFunnelInstance = null;
        let chartSourcesInstance = null;

        function renderDashboard(data) {
            try {
                // 1. KPIs
                document.getElementById('kpi-mrr').innerText = 'R$ ' + parseFloat(data.mrr).toLocaleString('pt-BR', {minimumFractionDigits: 2});
                document.getElementById('kpi-clients').innerText = data.clientes_ativos;
                document.getElementById('kpi-new-clients').innerText = '+' + data.novos_mes_atual;
                
                // New Metrics
                document.getElementById('kpi-cac').innerText = 'R$ ' + parseFloat(data.cac_real).toLocaleString('pt-BR', {minimumFractionDigits: 2});
                document.getElementById('kpi-ltv').innerText = 'R$ ' + parseFloat(data.ltv).toLocaleString('pt-BR', {minimumFractionDigits: 2});
                
                const roi = parseFloat(data.roi_medio);
                document.getElementById('kpi-roi').innerText = roi.toFixed(1) + 'x';
                
                document.getElementById('kpi-time').innerText = data.tempo_fechamento + ' Dias';
                
                // LTV/CAC Ratio
                const cac = parseFloat(data.cac_real);
                const ltv = parseFloat(data.ltv);
                const ratio = cac > 0 ? (ltv / cac).toFixed(1) : '∞';
                document.getElementById('kpi-ltv-ratio').innerText = ratio;
                
                // CRM KPIs
                if(data.crm) {
                    document.getElementById('kpi-pipeline').innerText = 'R$ ' + parseFloat(data.crm.pipeline_value || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2});
                    document.getElementById('kpi-leads-count').innerText = data.crm.total_leads || 0;
                    document.getElementById('kpi-conversion').innerText = parseFloat(data.crm.taxa_conversao || 0).toFixed(1) + '%';
                }

                // Render Top Clients
                const topClientsContainer = document.getElementById('topClientsList');
                if(data.top_clientes && data.top_clientes.length > 0) {
                    const brandColor = getDesignSystemColor('--brand', '#00BF24');
                    topClientsContainer.innerHTML = data.top_clientes.map((c, i) => `
                        <div class="flex items-center justify-between p-2 hover:bg-[var(--surface-2)] rounded-[var(--radius-sm)] transition">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs border" style="background: color-mix(in srgb, ${brandColor} 10%, transparent); color: ${brandColor}; border-color: color-mix(in srgb, ${brandColor} 20%, transparent)">
                                    ${i+1}
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-[var(--text-1)]">${c.nome_empresa}</div>
                                    <div class="text-[10px] text-[var(--text-3)] font-semibold font-mono">Premium Client</div>
                                </div>
                            </div>
                            <div class="text-sm font-mono font-bold" style="color: ${brandColor}">R$ ${parseFloat(c.valor_mensal).toLocaleString('pt-BR')}</div>
                        </div>
                    `).join('');
                } else {
                    topClientsContainer.innerHTML = '<div class="text-[var(--text-3)] text-xs text-center font-mono">Nenhum cliente encontrado.</div>';
                }

                // Render Charts
                renderCharts(data);
                if(data.crm) renderCRMCharts(data.crm);
            } catch (error) {
                console.error('Error loading dashboard:', error);
            }
        }

        function renderCharts(data) {
            if (chartFinanceInstance) chartFinanceInstance.destroy();

            const brandColor = getDesignSystemColor('--brand', '#00BF24');
            const warningColor = getDesignSystemColor('--warning', '#D97706');
            const border1Color = getDesignSystemColor('--border-1', '#E7E5E4');
            const text3Color = getDesignSystemColor('--text-3', '#78716C');

            const rgbVal = getComputedStyle(document.documentElement).getPropertyValue('--theme-color-rgb').trim() || '0,191,36';
            const gradientGreen = ctxFinance.createLinearGradient(0, 0, 0, 300);
            gradientGreen.addColorStop(0, `rgba(${rgbVal}, 0.15)`);
            gradientGreen.addColorStop(1, `rgba(${rgbVal}, 0)`);

            chartFinanceInstance = new Chart(ctxFinance, {
                type: 'line',
                data: {
                    labels: data.history.labels,
                    datasets: [
                        {
                            label: 'Receita',
                            data: data.history.mrr,
                            borderColor: brandColor,
                            backgroundColor: gradientGreen,
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true,
                            pointRadius: 0,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Custos',
                            data: data.history.custos,
                            borderColor: warningColor,
                            borderWidth: 2,
                            borderDash: [5, 5],
                            tension: 0.3,
                            fill: false,
                            pointRadius: 0
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { 
                            grid: { color: border1Color },
                            ticks: { callback: (val) => 'R$ ' + val, color: text3Color } 
                        },
                        x: { grid: { display: false }, ticks: { color: text3Color } }
                    },
                    plugins: {
                        legend: { display: true, position: 'top', align: 'end', labels: { color: text3Color } }
                    }
                }
            });
        }

        function renderCRMCharts(crmData) {
            // Funnel Chart
            if (chartFunnelInstance) chartFunnelInstance.destroy();

            const border1Color = getDesignSystemColor('--border-1', '#E7E5E4');
            const text3Color = getDesignSystemColor('--text-3', '#78716C');

            const funnelLabels = crmData.funnel ? crmData.funnel.map(s => s.nome) : [];
            const funnelValues = crmData.funnel ? crmData.funnel.map(s => s.count) : [];

            chartFunnelInstance = new Chart(ctxFunnel, {
                type: 'bar',
                data: {
                    labels: funnelLabels,
                    datasets: [{
                        label: 'Leads',
                        data: funnelValues,
                        backgroundColor: [
                            '#16a34a', '#2563eb', '#d97706', '#8b5cf6', 
                            '#ec4899', '#06b6d4', '#f97316', '#14b8a6', 
                            '#6366f1', '#eab308', '#ef4444', '#d946ef'
                        ],
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    scales: {
                        x: { grid: { color: border1Color }, ticks: { color: text3Color } },
                        y: { grid: { display: false }, ticks: { color: text3Color } }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });

            // Sources Chart
            if (chartSourcesInstance) chartSourcesInstance.destroy();

            chartSourcesInstance = new Chart(ctxSources, {
                type: 'doughnut',
                data: {
                    labels: crmData.leads_by_source ? crmData.leads_by_source.map(s => s.origem || 'Desconhecido') : [],
                    datasets: [{
                        data: crmData.leads_by_source ? crmData.leads_by_source.map(s => s.count) : [],
                        backgroundColor: [
                            '#2563eb', '#16a34a', '#d97706', '#8b5cf6', 
                            '#ec4899', '#06b6d4', '#f97316', '#14b8a6'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right', labels: { color: text3Color, boxWidth: 10, font: { size: 10 } } }
                    },
                    cutout: '70%'
                }
            });
        }

        let startDateStr = '';
        let endDateStr = '';

        function updateDateVariables(rangeParam) {
            const today = new Date();
            let start, end;
            
            if (rangeParam.startsWith('custom:')) {
                const parts = rangeParam.split(':')[1].split('-');
                const year = parseInt(parts[0]);
                const month = parseInt(parts[1]) - 1; 
                start = new Date(year, month, 1);
                end = new Date(year, month + 1, 0); 
            } else if (rangeParam === 'current' || rangeParam === 'this_month') {
                start = new Date(today.getFullYear(), today.getMonth(), 1);
                end = new Date(today.getFullYear(), today.getMonth() + 1, 0); 
            } else if (rangeParam === 'last_month') {
                start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                end = new Date(today.getFullYear(), today.getMonth(), 0);
            } else {
                start = new Date(today.getFullYear(), today.getMonth() - 2, 1);
                end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            }
            
            startDateStr = start.getFullYear() + '-' + String(start.getMonth()+1).padStart(2, '0') + '-' + String(start.getDate()).padStart(2, '0');
            endDateStr = end.getFullYear() + '-' + String(end.getMonth()+1).padStart(2, '0') + '-' + String(end.getDate()).padStart(2, '0');
        }

        function handleDateFilterChange() {
             const val = document.getElementById('dateRangeSelect').value;
             const customPicker = document.getElementById('customMonthPicker');
             
             if(val === 'custom') {
                 customPicker.classList.remove('hidden');
             } else {
                 customPicker.classList.add('hidden');
                 updateDateVariables(val);
                 loadData();
             }
         }

        function handleCustomDateChange() {
            const val = document.getElementById('customMonthPicker').value;
            if(val) {
                 updateDateVariables('custom:' + val);
                 loadData();
            }
        }

        async function loadData() {
            try {
                if(!startDateStr) updateDateVariables(document.getElementById('dateRangeSelect').value);

                const res = await fetch(`api/metricas_dashboard.php?inicio=${startDateStr}&fim=${endDateStr}`);
                const data = await res.json();
                
                renderDashboard(data);

            } catch(err) {
                console.error(err);
            }
        }

        // --- NOTIFICATIONS ---
        const notifBtn = document.getElementById('notifBtn');
        const notifDropdown = document.getElementById('notifDropdown');
        const notifBadge = document.getElementById('notifBadge');
        const notifList = document.getElementById('notifList');

        notifBtn.addEventListener('click', (e) => {
             e.stopPropagation();
             notifDropdown.classList.toggle('hidden');
             if(!notifDropdown.classList.contains('hidden')){
                 loadNotifications();
             }
        });

        document.addEventListener('click', (e) => {
            if (!notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) {
                notifDropdown.classList.add('hidden');
            }
        });

        async function loadNotifications() {
            try {
                const res = await fetch('api/notifications.php'); 
                const data = await res.json();
                
                if (data.length > 0) {
                    notifBadge.classList.remove('hidden');
                    notifList.innerHTML = data.map(n => `
                        <li class="p-3 border-b border-[var(--border-1)] last:border-0 hover:bg-[var(--surface-2)] cursor-pointer transition group">
                            <div class="flex items-start gap-3">
                                <span class="text-sm bg-[var(--surface-2)] p-1.5 rounded-lg border border-[var(--border-1)] group-hover:border-[var(--border-2)] transition">
                                    ${n.tipo === 'contrato' ? '📜' : (n.tipo === 'pagamento' ? '💰' : '🔥')}
                                </span>
                                <div>
                                    <div class="font-bold text-[var(--text-1)] text-xs font-mono mb-0.5">${n.nome_empresa}</div>
                                    <div class="text-[10px] text-[var(--text-3)] leading-tight">${n.mensagem || (n.tipo==='pagamento' ? 'Pagamento em ' + n.dias_restantes + ' dias' : 'Contrato vence em ' + n.dias_restantes + ' dias')}</div>
                                </div>
                            </div>
                        </li>
                    `).join('');
                } else {
                    notifBadge.classList.add('hidden');
                    notifList.innerHTML = '<li class="p-6 text-center text-[var(--text-3)] text-xs font-mono uppercase tracking-wider">SEM ALERTAS</li>';
                }
            } catch(e) {
                console.error('Notifications error:', e);
                notifList.innerHTML = '<li class="p-4 text-center text-red-500 text-xs font-mono uppercase">ERRO AO CARREGAR</li>';
            }
        }
        
        loadNotifications();
        handleDateFilterChange();

    </script>

    <!-- Settings Drawer -->
    <?php include 'components/settings_drawer.php'; ?>
    <script src="js/settings.js?v=<?= time() ?>"></script>
<?php include 'includes/footer.php'; ?>
