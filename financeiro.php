<?php
// PMDCRM/financeiro.php
require_once 'src/auth.php';
require_login();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="js/theme-loader.js"></script>
    <title>PMDCRM - Financeiro</title>
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/design-system.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
         /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-track { background: #0f172a; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-[#0f172a] text-slate-800 dark:text-slate-200 transition-colors duration-300">

    <?php include 'nav.php'; ?>

    <main class="md:ml-64 p-4 md:p-8 pb-24 transition-all duration-300">
        <!-- Header -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-slate-900 to-slate-600 dark:from-white dark:to-slate-400">
                    Financeiro & Estratégia
                </h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1">Visão geral de fluxo de caixa e investimentos</p>
            </div>
            
            <div class="flex gap-3 items-center">
                <?php include 'header_icons.php'; ?>
                
                <!-- Global Date Filter -->
                <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg p-1 flex items-center shadow-sm gap-2 px-2">
                    <select id="dateRangeSelect" class="bg-transparent text-sm font-medium text-slate-600 dark:text-slate-300 outline-none cursor-pointer" onchange="handleDateFilterChange()">
                        <option value="3months" class="bg-white dark:bg-slate-900">Últimos 3 Meses</option>
                        <option value="current" class="bg-white dark:bg-slate-900">Mês Atual</option>
                        <option value="last_month" class="bg-white dark:bg-slate-900">Mês Anterior</option>
                        <option value="custom" class="bg-white dark:bg-slate-900">Selecionar Mês...</option>
                    </select>
                    <input type="month" id="customMonthPicker" class="text-sm border-l pl-2 border-gray-300 dark:border-slate-600 text-slate-600 dark:text-slate-300 outline-none hidden bg-transparent" onchange="handleCustomDateChange()">
                </div>
                
                <button onclick="openTransactionModal()" class="bg-[var(--theme-color)] hover:brightness-90 text-white px-5 py-2.5 rounded-lg shadow-lg font-medium flex items-center gap-2 transform hover:-translate-y-0.5 transition-all">
                    <span>+</span> Nova Transação
                </button>
            </div>
        </header>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Receita -->
            <div class="p-6 border-l-4 border-[var(--theme-color)] relative overflow-hidden group bg-white dark:bg-slate-800/40 backdrop-blur-sm border-y border-r border-gray-200 dark:border-slate-700/50 rounded-r-xl shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-semibold">Receita Total</p>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1" id="kpi-receita">R$ 0,00</h3>
                    </div>
                    <div class="p-2 bg-green-500/10 rounded-lg text-[var(--theme-color)]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="w-full bg-gray-200 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-[var(--theme-color)] h-full rounded-full" style="width: 75%"></div>
                </div>
            </div>

            <!-- Despesas -->
            <div class="p-6 border-l-4 border-rose-500 relative overflow-hidden group bg-white dark:bg-slate-800/40 backdrop-blur-sm border-y border-r border-gray-200 dark:border-slate-700/50 rounded-r-xl shadow-sm">
                 <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-semibold">Despesas</p>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1" id="kpi-despesas">R$ 0,00</h3>
                    </div>
                    <div class="p-2 bg-rose-500/10 rounded-lg text-rose-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                    </div>
                </div>
                <div class="w-full bg-gray-200 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-rose-500 h-full rounded-full" style="width: 45%"></div>
                </div>
            </div>

            <!-- Lucro Líquido -->
            <div class="p-6 border-l-4 border-blue-500 relative overflow-hidden group bg-white dark:bg-slate-800/40 backdrop-blur-sm border-y border-r border-gray-200 dark:border-slate-700/50 rounded-r-xl shadow-sm">
                 <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-semibold">Lucro Líquido</p>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1" id="kpi-lucro">R$ 0,00</h3>
                    </div>
                    <div class="p-2 bg-blue-500/10 rounded-lg text-blue-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                </div>
                <div class="w-full bg-gray-200 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-blue-500 h-full rounded-full" style="width: 60%"></div>
                </div>
            </div>

            <!-- Margem -->
            <div class="p-6 border-l-4 border-amber-500 relative overflow-hidden group bg-white dark:bg-slate-800/40 backdrop-blur-sm border-y border-r border-gray-200 dark:border-slate-700/50 rounded-r-xl shadow-sm">
                 <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-semibold">Margem de Lucro</p>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1" id="kpi-margem">0%</h3>
                    </div>
                    <div class="p-2 bg-amber-500/10 rounded-lg text-amber-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                </div>
                <div class="w-full bg-gray-200 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-amber-500 h-full rounded-full" style="width: 50%"></div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-2 bg-white dark:bg-slate-800/40 backdrop-blur-sm border border-gray-200 dark:border-slate-700/50 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6">Fluxo de Caixa (6 Meses)</h3>
                <div class="relative h-72 w-full">
                    <canvas id="cashFlowChart"></canvas>
                </div>
            </div>
            
            <div class="bg-white dark:bg-slate-800/40 backdrop-blur-sm border border-gray-200 dark:border-slate-700/50 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6">Composição de Despesas</h3>
                <div class="relative h-64 w-full flex justify-center">
                    <canvas id="expensesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <!-- Transactions List -->
        <div class="bg-white dark:bg-slate-800/40 backdrop-blur-sm border border-gray-200 dark:border-slate-700/50 rounded-xl shadow-sm overflow-hidden flex flex-col">
             <div class="p-6 border-b border-gray-200 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Todas as Transações</h3>
                
                <!-- Filters -->
                <div class="flex gap-3">
                     <select id="filterType" onchange="loadFinancialData()" class="bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-600 dark:text-slate-300 outline-none focus:border-green-500">
                        <option value="all">Todas</option>
                        <option value="receita">Receitas</option>
                        <option value="despesa">Despesas</option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider font-semibold border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50">
                            <th class="p-4 pl-6">Descrição</th>
                            <th class="p-4">Categoria</th>
                            <th class="p-4">Data/Vencimento</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-right">Valor</th>
                            <th class="p-4 pr-6 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="transactionsTableBody" class="text-sm text-slate-600 dark:text-slate-300 divide-y divide-gray-200 dark:divide-slate-800">
                        <!-- Filled by JS -->
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Modal Transaction -->
    <div id="transactionModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-2xl w-full max-w-2xl transform scale-95 transition-transform duration-300 border border-gray-200 dark:border-slate-700">
             <div class="p-6 border-b border-gray-200 dark:border-slate-700 flex justify-between items-center">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">Nova Transação</h3>
                <button onclick="closeTransactionModal()" class="text-slate-400 hover:text-rose-500 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form id="transactionForm" class="p-6 space-y-4" novalidate>
                <input type="hidden" name="action" value="create_transaction">
                <input type="hidden" name="id" value="">
                
                <div class="grid grid-cols-2 gap-4">
                     <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Tipo</label>
                        <select name="tipo" class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:border-[var(--theme-color)] outline-none">
                            <option value="receita">Receita</option>
                            <option value="despesa">Despesa</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Valor (R$)</label>
                        <input type="number" step="0.01" name="valor" required class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:border-[var(--theme-color)] outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Descrição</label>
                    <input type="text" name="descricao" required class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:border-[var(--theme-color)] outline-none">
                </div>

                 <div class="grid grid-cols-2 gap-4">
                     <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Categoria</label>
                         <select name="categoria" class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:border-[var(--theme-color)] outline-none">
                            <option value="vendas">Vendas</option>
                            <option value="marketing">Marketing</option>
                            <option value="operacional">Operacional</option>
                            <option value="pessoal">Pessoal</option>
                            <option value="impostos">Impostos</option>
                            <option value="outros">Outros</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Data Vencimento</label>
                        <input type="date" name="data_vencimento" required class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:border-[var(--theme-color)] outline-none white-calendar-icon">
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Status</label>
                    <select name="status" class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:border-[var(--theme-color)] outline-none">
                        <option value="pago">Pago / Recebido</option>
                        <option value="pendente">Pendente</option>
                    </select>
                </div>

                <!-- Recurrence Options -->
                <div class="flex items-center gap-4 pt-2">
                    <div class="flex items-center">
                        <input type="checkbox" id="recorrente" name="recorrente" onchange="toggleRecurrence()" class="w-4 h-4 text-[var(--theme-color)] border-gray-300 rounded focus:ring-[var(--theme-color)] dark:focus:ring-offset-gray-800">
                        <label for="recorrente" class="ml-2 text-sm font-medium text-slate-700 dark:text-slate-300">Recorrente?</label>
                    </div>
                    
                    <div id="parcelasContainer" class="hidden flex-1">
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Qtd. Parcelas</label>
                        <input type="number" name="parcelas" min="2" max="120" value="12" class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:border-[var(--theme-color)] outline-none">
                    </div>
                </div>

                <div class="pt-4 flex justify-end gap-3">
                     <button type="button" onclick="closeTransactionModal()" class="px-5 py-2.5 rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-700 font-medium transition">Cancelar</button>
                    <button type="submit" class="bg-[var(--theme-color)] hover:brightness-90 text-white px-6 py-2.5 rounded-lg font-bold shadow-lg transition transform hover:-translate-y-0.5">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Init Themes
        if(localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        // --- Logic ---
        
        let startDateStr = '';
        let endDateStr = '';
        let chartCashInstance = null;
        let chartExpensesInstance = null;

        function updateDateVariables(rangeParam) {
            const today = new Date();
            let start, end;
            
            if (rangeParam.startsWith('custom:')) {
                const parts = rangeParam.split(':')[1].split('-');
                const year = parseInt(parts[0]);
                const month = parseInt(parts[1]) - 1; // JS months are 0-11
                start = new Date(year, month, 1);
                end = new Date(year, month + 1, 0);
            } else if (rangeParam === 'current' || rangeParam === 'this_month') { // Handle both
                start = new Date(today.getFullYear(), today.getMonth(), 1);
                end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            } else if (rangeParam === 'last_month') {
                start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                end = new Date(today.getFullYear(), today.getMonth(), 0);
            } else if (rangeParam === '3months') {
                start = new Date(today.getFullYear(), today.getMonth() - 2, 1);
                end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            } else {
                // Default fallback
                start = new Date(today.getFullYear(), today.getMonth(), 1);
                end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            }
            
            // Format YYYY-MM-DD (local time, beware timezone, better use UTC constructed or simple string fmt)
            // Simple robust formatting:
            startDateStr = start.getFullYear() + '-' + String(start.getMonth()+1).padStart(2, '0') + '-' + String(start.getDate()).padStart(2, '0');
            endDateStr = end.getFullYear() + '-' + String(end.getMonth()+1).padStart(2, '0') + '-' + String(end.getDate()).padStart(2, '0');
        }

        function handleDateFilterChange() {
             const val = document.getElementById('dateRangeSelect').value;
             const customPicker = document.getElementById('customMonthPicker');
             
             if(val === 'custom') {
                 customPicker.classList.remove('hidden');
                 // Don't load yet, wait for month pick
             } else {
                 customPicker.classList.add('hidden');
                 updateDateVariables(val);
                 loadFinancialData();
             }
        }

        function handleCustomDateChange() {
            const val = document.getElementById('customMonthPicker').value;
            if(val) {
                 updateDateVariables('custom:' + val);
                 loadFinancialData();
            }
        }

        async function loadFinancialData() {
            try {
                // Initial load
                if(!startDateStr) updateDateVariables(document.getElementById('dateRangeSelect').value);

                let type = document.getElementById('filterType') ? document.getElementById('filterType').value : 'all';
                
                const res = await fetch(`api/financeiro.php?start=${startDateStr}&end=${endDateStr}&type=${type}`);
                const data = await res.json();
                
                if(!data.success && data.error) console.error(data.error);

                updateKPIs(data);
                renderCharts(data);
                
                // Filter transactions locally for now
                let filteredTransactions = data.transactions || [];
                
                // Ensure type is valid
                if(type && type !== 'all') {
                    // Loose comparison just in case, and normalize case
                    filteredTransactions = filteredTransactions.filter(t => t.tipo && t.tipo.toLowerCase() === type.toLowerCase());
                }
                
                renderTransactions(filteredTransactions);

            } catch(e) {
                console.error('Error loading financial data:', e.message || e);
            }
        }
        
        function updateKPIs(data) {
            
            // Handle new API structure (nested in kpi) or fallback to flat
            const kpi = data.kpi || data; 
            
            const receita = parseFloat(kpi.revenue || kpi.receita) || 0;
            const despesas = parseFloat(kpi.expenses || kpi.despesas) || 0;
            const lucro = parseFloat(kpi.profit || kpi.lucro) || 0;
            const margem = parseFloat(kpi.margin || kpi.margem) || 0;

            document.getElementById('kpi-receita').innerText = 'R$ ' + receita.toLocaleString('pt-BR', {minimumFractionDigits: 2});
            document.getElementById('kpi-despesas').innerText = 'R$ ' + despesas.toLocaleString('pt-BR', {minimumFractionDigits: 2});
            document.getElementById('kpi-lucro').innerText = 'R$ ' + lucro.toLocaleString('pt-BR', {minimumFractionDigits: 2});
            document.getElementById('kpi-margem').innerText = margem.toFixed(1) + '%';
        }

        function renderCharts(data) {
             const ctxCash = document.getElementById('cashFlowChart').getContext('2d');
             const ctxExp = document.getElementById('expensesChart').getContext('2d');
             
             // Handle new API structure
             const charts = data.charts || {};
             const flow = charts.cash_flow || {};
             const categoryData = charts.by_category || data.expensesByCategory || []; // Fallback

             if(chartCashInstance) chartCashInstance.destroy();
             if(chartExpensesInstance) chartExpensesInstance.destroy();

             // Cashflow settings
             const gradient = ctxCash.createLinearGradient(0,0,0,300);
             gradient.addColorStop(0, 'rgba(0, 191, 36, 0.2)');
             gradient.addColorStop(1, 'rgba(0, 191, 36, 0)');

             chartCashInstance = new Chart(ctxCash, {
                 type: 'line',
                 data: {
                     labels: flow.labels || ['Out', 'Nov', 'Dez', 'Jan', 'Fev', 'Mar'],
                     datasets: [{
                         label: 'Fluxo Líquido',
                         // Calculate Net Flow (Revenue - Expenses) if available, otherwise just Revenue
                         data: (flow.revenue || []).map((r, i) => r - (flow.expenses?.[i]||0)),
                         borderColor: 'var(--theme-color)',
                         backgroundColor: gradient,
                         fill: true,
                         tension: 0.4
                     }]
                 },
                 options: {
                     maintainAspectRatio: false,
                     plugins: { legend: { display: false } },
                     scales: {
                         x: { grid: { display: false }, ticks: { color: '#94a3b8' } },
                         y: { grid: { color: document.documentElement.classList.contains('dark') ? '#334155' : '#e2e8f0' }, ticks: { color: '#94a3b8' } }
                     }
                 }
             });

             // Calculate Category Data for Chart
             const catLabels = categoryData.map(c => c.categoria);
             const catValues = categoryData.map(c => c.total);

             chartExpensesInstance = new Chart(ctxExp, {
                 type: 'doughnut',
                 data: {
                     labels: catLabels.length ? catLabels : ['Sem dados'],
                     datasets: [{
                         data: catValues.length ? catValues : [1], 
                         backgroundColor: ['#3b82f6', '#f59e0b', '#6366f1', '#ef4444', '#10b981', '#8b5cf6'],
                         borderWidth: 0
                     }]
                 },
                 options: {
                     maintainAspectRatio: false,
                     plugins: {
                        legend: { position: 'right', labels: { color: document.documentElement.classList.contains('dark') ? '#cbd5e1' : '#475569' } }
                     }
                 }
             });
        }

        function renderTransactions(transactions) {
            const tbody = document.getElementById('transactionsTableBody');

            if(!transactions || !Array.isArray(transactions) || transactions.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="p-4 text-center text-slate-500">Nenhuma transação encontrada.</td></tr>`;
                return;
            }

            try {
                tbody.innerHTML = transactions.map(t => {
                    return `
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition border-b border-gray-100 dark:border-slate-800 last:border-0">
                        <td class="p-4 pl-6 font-medium text-slate-800 dark:text-white">${t.descricao || 'Sem descrição'}</td>
                        <td class="p-4 text-slate-500 dark:text-slate-400">
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                ${t.categoria || 'Geral'}
                            </span>
                        </td>
                        <td class="p-4 text-slate-500 dark:text-slate-400">
                            ${t.data_despesa ? new Date(t.data_despesa + 'T00:00:00').toLocaleDateString('pt-BR') : 'Data Inválida'}
                            ${t.recorrente == 1 ? '<span class="ml-1 text-[10px] bg-blue-100 text-blue-600 px-1 rounded">Recorrente</span>' : ''}
                        </td>
                        <td class="p-4">
                            <span class="text-xs font-bold ${t.status === 'pago' ? 'text-green-500' : 'text-amber-500'}">
                                ${t.status === 'pago' ? '● Pago' : '○ Pendente'}
                            </span>
                        </td>
                        <td class="p-4 font-mono font-bold ${t.tipo==='receita' ? 'text-green-500' : 'text-rose-500'}">
                            ${t.tipo==='receita' ? '+' : '-'} R$ ${(parseFloat(t.valor)||0).toLocaleString('pt-BR', {minimumFractionDigits: 2})}
                        </td>
                        <td class="p-4 pr-6 text-right">
                            <button onclick='editTransaction(${JSON.stringify(t)})' class="text-slate-400 hover:text-blue-500 transition mr-2" title="Editar">
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <button onclick="deleteTransaction(${t.id})" class="text-slate-400 hover:text-red-500 transition" title="Excluir">
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </td>
                    </tr>
                `;
                }).join('');
            } catch(err) {
                console.error("Render Error:", err);
                tbody.innerHTML = `<tr><td colspan="6" class="text-red-500 p-4">Erro ao renderizar: ${err.message}</td></tr>`;
            }
        }            

        // Modal Functions
        const modal = document.getElementById('transactionModal');
        
        function openTransactionModal() {
            const form = document.getElementById('transactionForm');
            form.reset();
            form.querySelector('[name="id"]').value = '';
            form.querySelector('[name="action"]').value = 'create_transaction';
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.children[0].classList.remove('scale-95');
                modal.children[0].classList.add('scale-100');
            }, 10);
        }

        function closeTransactionModal() {
            modal.classList.add('opacity-0');
            modal.children[0].classList.remove('scale-100');
            modal.children[0].classList.add('scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        function toggleRecurrence() {
            const isRecorrente = document.getElementById('recorrente').checked;
            const container = document.getElementById('parcelasContainer');
            if(isRecorrente) {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }

        document.getElementById('transactionForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerText;
            submitBtn.innerText = "Salvando...";
            submitBtn.disabled = true;

            const form = e.target;
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            if(!data.descricao || !data.valor) {
                alert("Por favor, preencha Descrição e Valor.");
                submitBtn.innerText = originalText;
                submitBtn.disabled = false;
                return;
            }
            if(data.data_vencimento) {
                data.data_despesa = data.data_vencimento;
                delete data.data_vencimento;
            }
            
            if(data.id === "") delete data.id;

            try {
                const res = await fetch('api/financeiro.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data)
                });
                const result = await res.json();
                
                if(result.success) {
                    alert("Salvo com sucesso!");
                    closeTransactionModal();
                    loadFinancialData();
                } else {
                    alert('Erro ao salvar: ' + (result.error || result.message || 'Desconhecido'));
                }
            } catch(err) {
                console.error(err);
                alert('Erro de conexão: ' + err.message);
            } finally {
                submitBtn.innerText = originalText;
                submitBtn.disabled = false;
            }
        });

        function editTransaction(data) {
            openTransactionModal();
            const form = document.getElementById('transactionForm');
            // Reset and Populate
            form.reset();
            form.querySelector('[name="id"]').value = data.id || '';
            form.querySelector('[name="descricao"]').value = data.descricao || '';
            form.querySelector('[name="valor"]').value = data.valor || '';
            form.querySelector('[name="categoria"]').value = data.categoria || 'outros';
            form.querySelector('[name="data_vencimento"]').value = data.data_despesa || ''; 
            form.querySelector('[name="status"]').value = data.status || 'pendente';
            form.querySelector('[name="tipo"]').value = data.tipo || 'despesa';
        }

        async function deleteTransaction(id) {
            if(!confirm('Tem certeza que deseja excluir esta transação?')) return;
            
            try {
                const res = await fetch('api/financeiro.php', {
                    method: 'DELETE',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ id: id })
                });
                const data = await res.json();
                
                if(data.success) {
                    loadFinancialData();
                } else {
                    alert('Erro: ' + (data.error || 'Falha ao excluir'));
                }
            } catch(e) {
                console.error(e);
                alert('Erro de conexão ao excluir');
            }
        }

        // Initialize
        loadFinancialData();

    </script>
    
    <div id="debug-panel" style="position: fixed; bottom: 0; left: 0; right: 0; height: 150px; background: #000; color: #0f0; font-family: monospace; font-size: 10px; overflow-y: scroll; padding: 10px; z-index: 9999; border-top: 2px solid #0f0; display: none;">
        <div><strong>DEBUG PANEL</strong> <button onclick="this.parentElement.parentElement.style.display='none'" style="color:white; border: 1px solid white; padding: 0 5px;">X</button></div>
        <div id="debug-content"></div>
    </div>

    <script>
        // Override console.log to show in panel
        const originalLog = console.log;
        const originalError = console.error;
        const debugContent = document.getElementById('debug-content');
        const debugPanel = document.getElementById('debug-panel');

        function appendDebug(type, args) {
            // Show panel on first log
            debugPanel.style.display = 'block';
            const line = document.createElement('div');
            line.style.borderBottom = '1px solid #333';
            line.style.padding = '2px 0';
            line.style.color = type === 'error' ? '#f55' : '#0f0';
            
            // Safe stringify
            const msg = Array.from(args).map(arg => {
                if(arg instanceof Error) return `${arg.name}: ${arg.message}`;
                if(typeof arg === 'object') return JSON.stringify(arg);
                return String(arg);
            }).join(' ');

            line.textContent = `[${new Date().toLocaleTimeString()}] ${type.toUpperCase()}: ${msg}`;
            debugContent.appendChild(line);
            debugContent.scrollTop = debugContent.scrollHeight;
        }

        console.log = function(...args) {
            originalLog.apply(console, args);
            appendDebug('info', args);
        };

        console.error = function(...args) {
            originalError.apply(console, args);
            appendDebug('error', args);
        };
        
        window.onerror = function(msg, url, line) {
            console.error(`Global Error: ${msg} @ ${url}:${line}`);
        };
    </script>

    <!-- Settings Drawer -->
    <?php include 'components/settings_drawer.php'; ?>
    <script src="js/settings.js?v=<?= time() ?>"></script>
</body>
</html>
