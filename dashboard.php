<?php
// PMDCRM/dashboard.php
require_once 'src/auth.php';
require_login();
?>
<!DOCTYPE html>
<html lang="pt-BR"> <!-- Added dark class check in JS -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BI Dashboard - PMDCRM</title>
    <script src="js/theme-loader.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: 'var(--theme-color)',
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .card-bi { border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .dark .card-bi { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5); border: 1px solid #222; }
    
        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .dark ::-webkit-scrollbar-track { background: #0a0a0a; }
        .dark ::-webkit-scrollbar-thumb { background: #333; }
        .dark ::-webkit-scrollbar-thumb:hover { background: #444; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 dark:bg-[#0f172a] dark:text-[#e2e8f0] pb-20 md:pb-0 md:pl-64 transition-colors duration-300">

    <?php include 'nav.php'; ?>

    <main class="p-4 md:p-6 max-w-[1920px] mx-auto">
        <!-- Header -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 border-b border-gray-200 dark:border-gray-800 pb-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight flex items-center gap-2">
                    <span class="w-3 h-3 bg-green-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(34,197,94,0.6)]"></span>
                    Central de Controle
                </h1>
                <p class="text-gray-500 dark:text-slate-400 text-[10px] mt-1 uppercase font-bold tracking-widest flex items-center gap-2">
                    Sistema Ativo e Online
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <!-- Date Filter -->
                <div class="flex items-center gap-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded px-3 py-1.5 shadow-sm">
                    <span class="text-gray-500 text-xs">PERÍODO:</span>
                    <select id="dateRangeSelect" class="bg-transparent text-slate-700 dark:text-white text-sm outline-none cursor-pointer font-mono" onchange="handleDateFilterChange()">
                        <option value="3months">Últimos 3 Meses</option>
                        <option value="current">Mês Atual</option>
                        <option value="last_month">Mês Anterior</option>
                        <option value="custom">Selecionar Mês...</option>
                    </select>
                    <input type="month" id="customMonthPicker" class="text-sm border-l pl-2 border-gray-300 dark:border-gray-600 text-slate-600 dark:text-slate-300 outline-none hidden bg-transparent" onchange="handleCustomDateChange()">
                </div>
                
                <?php include 'header_icons.php'; ?>

                <!-- Notification Bell -->
                <div class="relative">
                    <button id="notifBtn" class="bg-white dark:bg-gray-800 p-2 rounded-full text-gray-400 hover:text-gray-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition relative border border-gray-200 dark:border-gray-700 shadow-sm">
                        <span class="absolute top-0 right-0 w-2.5 h-2.5 bg-rose-500 rounded-full border-2 border-white dark:border-gray-900 hidden animate-pulse" id="notifBadge"></span>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </button>
                    
                    <!-- Dropdown Notificacoes -->
                    <div id="notifDropdown" class="hidden absolute right-0 top-12 w-80 bg-white dark:bg-gray-900 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-800 z-50 overflow-hidden ring-1 ring-black/5 dark:ring-black/50">
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 flex justify-between items-center">
                            <span class="font-bold text-gray-700 dark:text-gray-200 text-sm font-mono">NOTIFICAÇÕES</span>
                            <span class="text-[10px] text-gray-500">TEMPO REAL</span>
                        </div>
                        <ul id="notifList" class="max-h-80 overflow-y-auto">
                            <li class="p-6 text-center text-gray-600 text-xs font-mono">SEM DADOS</li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <!-- KPI Grid (High Density) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- 1. MRR -->
            <div class="card-bi p-4 bg-white dark:bg-[#0a0a0a] border border-gray-200 dark:border-[#222] relative overflow-hidden group">
                <div class="absolute right-2 top-2 opacity-10 text-green-500"><svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                <div class="text-xs text-gray-500 dark:text-gray-400 font-mono mb-1">RECEITA (MRR)</div>
                <div class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight" id="kpi-mrr">R$ 0,00</div>
                <div class="text-[10px] text-[var(--theme-color)] mt-1 flex items-center gap-1">
                    <span>▲</span> <span class="text-gray-500 uppercase">Recorrente</span>
                </div>
                <div class="h-1 w-full bg-gray-200 dark:bg-gray-800 mt-3 rounded-full overflow-hidden">
                    <div class="h-full bg-[var(--theme-color)] w-3/4"></div>
                </div>
            </div>

            <!-- 2. Pipeline Value (CRM) -->
            <div class="card-bi p-4 bg-white dark:bg-[#0a0a0a] border border-gray-200 dark:border-[#222] relative overflow-hidden group">
                <div class="absolute right-2 top-2 opacity-10 text-amber-500"><svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg></div>
                <div class="text-xs text-gray-500 dark:text-gray-400 font-mono mb-1">PIPELINE ABERTO</div>
                <div class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight" id="kpi-pipeline">R$ 0,00</div>
                <div class="text-[10px] text-amber-500 mt-1 flex items-center gap-1">
                    <span id="kpi-leads-count">0</span> LEADS ATIVOS
                </div>
                <div class="h-1 w-full bg-gray-200 dark:bg-gray-800 mt-3 rounded-full overflow-hidden">
                    <div class="h-full bg-amber-500 w-1/2"></div>
                </div>
            </div>

            <!-- 3. Conversion Rate -->
            <div class="card-bi p-4 bg-white dark:bg-[#0a0a0a] border border-gray-200 dark:border-[#222] relative overflow-hidden group">
                <div class="absolute right-2 top-2 opacity-10 text-indigo-500"><svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                 <div class="text-xs text-gray-500 dark:text-gray-400 font-mono mb-1">TAXA CONVERSÃO</div>
                <div class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight" id="kpi-conversion">0%</div>
                <div class="text-[10px] text-indigo-400 mt-1 flex items-center gap-1">
                    LEADS -> CLIENTES
                </div>
                <div class="h-1 w-full bg-gray-200 dark:bg-gray-800 mt-3 rounded-full overflow-hidden">
                    <div class="h-full bg-indigo-500 w-1/3"></div>
                </div>
            </div>

            <!-- 4. Active Clients -->
            <div class="card-bi p-4 bg-white dark:bg-[#0a0a0a] border border-gray-200 dark:border-[#222] relative overflow-hidden group">
               <div class="absolute right-2 top-2 opacity-10 text-[var(--theme-color)]"><svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg></div>
                 <div class="text-xs text-gray-500 dark:text-gray-400 font-mono mb-1">BASE ATIVA</div>
                <div class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight" id="kpi-clients">0</div>
                <div class="text-[10px] text-[var(--theme-color)] mt-1 flex items-center gap-1">
                     <span id="kpi-new-clients">+0</span> NOVOS ESTE MÊS
                </div>
                 <div class="h-1 w-full bg-gray-200 dark:bg-gray-800 mt-3 rounded-full overflow-hidden">
                    <div class="h-full bg-[var(--theme-color)] w-full"></div>
                </div>
            </div>
        </div>

        <!-- Row 2: Analytics (CAC, LTV, ROI, Time) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- CAC -->
            <div class="card-bi p-4 bg-white dark:bg-[#0a0a0a] border border-gray-200 dark:border-[#222]">
                <div class="text-xs text-slate-500 uppercase font-mono mb-1">CAC (AQUISIÇÃO)</div>
                <div class="text-2xl font-bold text-[var(--theme-color)] tracking-tight" id="kpi-cac">R$ 0,00</div>
                <div class="h-1 w-10 bg-[var(--theme-color)] mt-2 rounded-full"></div>
            </div>

            <!-- LTV -->
             <div class="card-bi p-4 bg-white dark:bg-[#0a0a0a] border border-gray-200 dark:border-[#222]">
                <div class="text-xs text-slate-500 uppercase font-mono mb-1">LTV (VALOR VITALÍCIO)</div>
                <div class="text-2xl font-bold text-blue-500 dark:text-blue-400 tracking-tight" id="kpi-ltv">R$ 0,00</div>
                 <div class="text-[10px] text-slate-500 mt-1">RATIO: <span id="kpi-ltv-ratio">0</span>x CAC</div>
            </div>

            <!-- ROI -->
             <div class="card-bi p-4 bg-white dark:bg-[#0a0a0a] border border-gray-200 dark:border-[#222]">
                <div class="text-xs text-slate-500 uppercase font-mono mb-1">ROI MÉDIO</div>
                <div class="text-2xl font-bold text-purple-500 dark:text-purple-400 tracking-tight" id="kpi-roi">0.0x</div>
                 <div class="text-[10px] text-slate-500 mt-1">MÉDIA GLOBAL</div>
            </div>

            <!-- Time -->
             <div class="card-bi p-4 bg-white dark:bg-[#0a0a0a] border border-gray-200 dark:border-[#222]">
                <div class="text-xs text-slate-500 uppercase font-mono mb-1">TEMPO FECHAMENTO</div>
                <div class="text-2xl font-bold text-orange-500 dark:text-orange-400 tracking-tight" id="kpi-time">0 Dias</div>
                 <div class="text-[10px] text-slate-500 mt-1">MÉDIA DO PERÍODO</div>
            </div>
        </div>

        <!-- CRM & Growth Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
             <!-- Sales Funnel Chart -->
            <div class="card-bi p-6 bg-white dark:bg-[#0a0a0a] border border-gray-200 dark:border-[#222]">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center gap-2">
                         <span class="w-2 h-2 rounded-full bg-[var(--theme-color)]"></span>
                        Funil de Vendas
                    </h3>
                </div>
                <div class="h-64 relative">
                    <canvas id="funnelChart"></canvas>
                </div>
            </div>

            <!-- Lead Sources Chart -->
            <div class="card-bi p-6 bg-white dark:bg-[#0a0a0a] border border-gray-200 dark:border-[#222]">
                 <div class="flex justify-between items-center mb-6">
                    <h3 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center gap-2">
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
            <div class="card-bi p-6 lg:col-span-2 bg-white dark:bg-[#0a0a0a] border border-gray-200 dark:border-[#222]">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-[var(--theme-color)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                        Fluxo Financeiro (6 Meses)
                    </h3>
                </div>
                <div class="h-64 relative">
                    <canvas id="financeChart"></canvas> 
                </div>
            </div>
            
             <!-- Top Clients -->
            <div class="card-bi p-6 bg-white dark:bg-[#0a0a0a] border border-gray-200 dark:border-[#222]">
                 <h3 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="text-yellow-500">★</span> Top Clientes
                </h3>
                <div class="space-y-4" id="topClientsList">
                    <!-- Populated via JS -->
                    <div class="animate-pulse flex space-x-4">
                        <div class="rounded-full bg-slate-200 dark:bg-slate-800 h-10 w-10"></div>
                        <div class="flex-1 space-y-2 py-1">
                            <div class="h-2 bg-slate-200 dark:bg-slate-800 rounded"></div>
                            <div class="h-2 bg-slate-200 dark:bg-slate-800 rounded w-3/4"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script>
        // --- CHARTS CONFIG ---
        // Dynamically update chart defaults based on Theme could be nice, but for now fixed neutral
        Chart.defaults.color = '#64748b'; 
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
                    topClientsContainer.innerHTML = data.top_clientes.map((c, i) => `
                        <div class="flex items-center justify-between p-2 hover:bg-slate-100 dark:hover:bg-white/5 rounded-lg transition">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[var(--theme-color)]/20 flex items-center justify-center text-[var(--theme-color)] font-bold text-xs border border-[var(--theme-color)]/30">
                                    ${i+1}
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-slate-700 dark:text-slate-200">${c.nome_empresa}</div>
                                    <div class="text-[10px] text-slate-500">Cliente Premium</div>
                                </div>
                            </div>
                            <div class="text-sm font-mono text-[var(--theme-color)]">R$ ${parseFloat(c.valor_mensal).toLocaleString('pt-BR')}</div>
                        </div>
                    `).join('');
                } else {
                    topClientsContainer.innerHTML = '<div class="text-slate-500 text-xs text-center">Nenhum cliente encontrado.</div>';
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

            const gradientGreen = ctxFinance.createLinearGradient(0, 0, 0, 300);
            gradientGreen.addColorStop(0, 'rgba(0, 191, 36, 0.2)');
            gradientGreen.addColorStop(1, 'rgba(0, 191, 36, 0)');

            chartFinanceInstance = new Chart(ctxFinance, {
                type: 'line',
                data: {
                    labels: data.history.labels,
                    datasets: [
                        {
                            label: 'Receita',
                            data: data.history.mrr,
                            borderColor: 'var(--theme-color)',
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
                            borderColor: '#f59e0b', // Amber
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
                            grid: { color: document.documentElement.classList.contains('dark') ? '#1e293b' : '#e2e8f0' },
                            ticks: { callback: (val) => 'R$ ' + val, color: '#94a3b8' } 
                        },
                        x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                    },
                    plugins: {
                        legend: { display: true, position: 'top', align: 'end', labels: { color: document.documentElement.classList.contains('dark') ? '#cbd5e1' : '#475569' } }
                    }
                }
            });
        }

        function renderCRMCharts(crmData) {
            // Funnel Chart
            if (chartFunnelInstance) chartFunnelInstance.destroy();

            // Transform funnel data for chart
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
                            '#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', 
                            '#ec4899', '#06b6d4', '#f97316', '#14b8a6', 
                            '#6366f1', '#eab308', '#ef4444', '#d946ef'
                        ],
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    scales: {
                        x: { grid: { color: document.documentElement.classList.contains('dark') ? '#334155' : '#e2e8f0' }, ticks: { color: '#94a3b8' } },
                        y: { grid: { display: false }, ticks: { color: document.documentElement.classList.contains('dark') ? '#cbd5e1' : '#475569' } }
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
                            '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', 
                            '#ec4899', '#06b6d4', '#f97316', '#14b8a6'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right', labels: { color: document.documentElement.classList.contains('dark') ? '#cbd5e1' : '#475569', boxWidth: 10, font: { size: 10 } } }
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
                const month = parseInt(parts[1]) - 1; // JS months are 0-11
                start = new Date(year, month, 1);
                end = new Date(year, month + 1, 0);
            } else if (rangeParam === 'current' || rangeParam === 'this_month') {
                start = new Date(today.getFullYear(), today.getMonth(), 1);
                end = new Date(today.getFullYear(), today.getMonth() + 1, 0); 
            } else if (rangeParam === 'last_month') {
                start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                end = new Date(today.getFullYear(), today.getMonth(), 0);
            } else {
                // 3 Messrs (Padrão)
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
                        <li class="p-3 border-b border-gray-200 dark:border-gray-800 last:border-0 hover:bg-gray-100 dark:hover:bg-gray-800/50 cursor-pointer transition group">
                            <div class="flex items-start gap-3">
                                <span class="text-lg bg-gray-100 dark:bg-gray-800 p-1.5 rounded-md border border-gray-200 dark:border-gray-700 group-hover:border-gray-300 dark:group-hover:border-gray-600 transition">
                                    ${n.tipo === 'contrato' ? '📜' : (n.tipo === 'pagamento' ? '💰' : '🔥')}
                                </span>
                                <div>
                                    <div class="font-bold text-gray-700 dark:text-gray-300 text-xs font-mono mb-0.5">${n.nome_empresa}</div>
                                    <div class="text-[10px] text-gray-500 leading-tight">${n.mensagem || (n.tipo==='pagamento' ? 'Pagamento em ' + n.dias_restantes + ' dias' : 'Contrato vence em ' + n.dias_restantes + ' dias')}</div>
                                </div>
                            </div>
                        </li>
                    `).join('');
                } else {
                    notifBadge.classList.add('hidden');
                    notifList.innerHTML = '<li class="p-6 text-center text-gray-500 text-xs font-mono">SEM ALERTAS</li>';
                }
            } catch(e) {
                console.error('Notifications error:', e);
                notifList.innerHTML = '<li class="p-4 text-center text-red-500 text-xs font-mono">ERRO AO CARREGAR</li>';
            }
        }
        
        loadNotifications();
        handleDateFilterChange();

    </script>

    <!-- Settings Drawer -->
    <?php include 'components/settings_drawer.php'; ?>
    <script src="js/settings.js?v=<?= time() ?>"></script>
</body>
</html>
