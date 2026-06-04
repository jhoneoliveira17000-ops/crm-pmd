<?php
// PMDCRM/financeiro.php
require_once 'src/auth.php';
require_login();
$page_title = "PMDCRM - Financeiro";
include 'includes/header.php';
include 'nav.php';
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<main class="p-4 md:p-8 pb-24 transition-all duration-300 bg-[var(--surface-1)] min-h-screen">
        <!-- Header -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 border-b border-[var(--border-1)] pb-4">
            <div>
                <h1 class="text-2xl font-bold text-[var(--text-1)] tracking-tight flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full animate-pulse shadow-[0_0_8px_rgba(22,163,74,0.6)]" style="background:var(--success)"></span>
                    Financeiro & Estratégia
                </h1>
                <p class="text-[var(--text-3)] text-xs mt-1">Visão geral de fluxo de caixa e investimentos</p>
            </div>
            
            <div class="flex gap-3 items-center">
                <?php include 'header_icons.php'; ?>
                
                <!-- Global Date Filter -->
                <div class="bg-[var(--surface-0)] border border-[var(--border-1)] rounded-[var(--radius-md)] p-1 flex items-center shadow-sm gap-2 px-2">
                    <select id="dateRangeSelect" class="bg-transparent text-sm font-semibold text-[var(--text-1)] outline-none cursor-pointer" onchange="handleDateFilterChange()">
                        <option value="3months">Últimos 3 Meses</option>
                        <option value="current">Mês Atual</option>
                        <option value="last_month">Mês Anterior</option>
                        <option value="custom">Selecionar Mês...</option>
                    </select>
                    <input type="month" id="customMonthPicker" class="text-sm border-l pl-2 border-[var(--border-1)] text-[var(--text-2)] outline-none hidden bg-transparent" onchange="handleCustomDateChange()">
                </div>
                
                <button onclick="openTransactionModal()" class="ds-btn ds-btn-primary btn-spring">+ Nova Transação</button>
            </div>
        </header>

        <!-- KPI Cards with Staggered Entrance Animations -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 ds-stagger">
            <!-- Receita -->
            <div class="ds-card-metric ds-animate-spring card-spring relative overflow-hidden group bg-[var(--surface-0)] border border-[var(--border-1)] rounded-[var(--radius-lg)] shadow-sm" style="border-left: 4px solid var(--success)">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-[var(--text-3)] font-bold font-mono">Receita Total</p>
                        <h3 class="text-2xl font-bold text-[var(--text-1)] mt-1" id="kpi-receita">R$ 0,00</h3>
                    </div>
                    <div class="p-2 bg-green-500/10 rounded-lg text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="w-full bg-[var(--surface-2)] h-1.5 rounded-full overflow-hidden">
                    <div class="bg-[var(--success)] h-full rounded-full" style="width: 75%"></div>
                </div>
            </div>

            <!-- Despesas -->
            <div class="ds-card-metric ds-animate-spring card-spring relative overflow-hidden group bg-[var(--surface-0)] border border-[var(--border-1)] rounded-[var(--radius-lg)] shadow-sm" style="border-left: 4px solid var(--danger)">
                 <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-[var(--text-3)] font-bold font-mono">Despesas</p>
                        <h3 class="text-2xl font-bold text-[var(--text-1)] mt-1" id="kpi-despesas">R$ 0,00</h3>
                    </div>
                    <div class="p-2 bg-rose-500/10 rounded-lg text-rose-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                    </div>
                </div>
                <div class="w-full bg-[var(--surface-2)] h-1.5 rounded-full overflow-hidden">
                    <div class="bg-rose-500 h-full rounded-full" style="width: 45%"></div>
                </div>
            </div>

            <!-- Lucro Líquido -->
            <div class="ds-card-metric ds-animate-spring card-spring relative overflow-hidden group bg-[var(--surface-0)] border border-[var(--border-1)] rounded-[var(--radius-lg)] shadow-sm" style="border-left: 4px solid var(--info)">
                 <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-[var(--text-3)] font-bold font-mono">Lucro Líquido</p>
                        <h3 class="text-2xl font-bold text-[var(--text-1)] mt-1" id="kpi-lucro">R$ 0,00</h3>
                    </div>
                    <div class="p-2 bg-blue-500/10 rounded-lg text-blue-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                </div>
                <div class="w-full bg-[var(--surface-2)] h-1.5 rounded-full overflow-hidden">
                    <div class="bg-blue-500 h-full rounded-full" style="width: 60%"></div>
                </div>
            </div>

            <!-- Margem -->
            <div class="ds-card-metric ds-animate-spring card-spring relative overflow-hidden group bg-[var(--surface-0)] border border-[var(--border-1)] rounded-[var(--radius-lg)] shadow-sm" style="border-left: 4px solid var(--warning)">
                 <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-[var(--text-3)] font-bold font-mono">Margem de Lucro</p>
                        <h3 class="text-2xl font-bold text-[var(--text-1)] mt-1" id="kpi-margem">0%</h3>
                    </div>
                    <div class="p-2 bg-amber-500/10 rounded-lg text-amber-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                </div>
                <div class="w-full bg-[var(--surface-2)] h-1.5 rounded-full overflow-hidden">
                    <div class="bg-amber-500 h-full rounded-full" style="width: 50%"></div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="ds-card p-6 lg:col-span-2">
                <h3 class="text-xs font-bold text-[var(--text-3)] mb-6 uppercase font-mono tracking-wider">Fluxo de Caixa (6 Meses)</h3>
                <div class="relative h-72 w-full">
                    <canvas id="cashFlowChart"></canvas>
                </div>
            </div>
            
            <div class="ds-card p-6">
                <h3 class="text-xs font-bold text-[var(--text-3)] mb-6 uppercase font-mono tracking-wider">Composição de Despesas</h3>
                <div class="relative h-64 w-full flex justify-center">
                    <canvas id="expensesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="ds-card flex flex-col shadow-sm overflow-hidden mb-6">
             <div class="p-6 border-b border-[var(--border-1)] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-[var(--surface-0)] rounded-t-[var(--radius-lg)]">
                <h3 class="text-sm font-bold text-[var(--text-3)] uppercase tracking-wider font-mono">Todas as Transações</h3>
                
                <!-- Filters -->
                <div class="flex gap-3">
                     <select id="filterType" onchange="loadFinancialData()" class="bg-[var(--surface-2)] border border-[var(--border-1)] rounded-lg px-3 py-2 text-sm text-[var(--text-2)] outline-none focus:border-[var(--brand)] font-semibold cursor-pointer">
                        <option value="all">Todas</option>
                        <option value="receita">Receitas</option>
                        <option value="despesa">Despesas</option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="ds-table">
                    <thead>
                        <tr class="bg-[var(--surface-2)]">
                            <th class="p-4 pl-6">Descrição</th>
                            <th class="p-4">Categoria</th>
                            <th class="p-4">Data/Vencimento</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-right">Valor</th>
                            <th class="p-4 pr-6 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="transactionsTableBody" class="divide-y divide-[var(--border-1)]">
                        <!-- Filled by JS -->
                    </tbody>
                </table>
            </div>
        </div>

</main>

    <!-- Modal Transaction (Overlay & Modal Pattern) -->
    <div id="transactionModal" class="ds-overlay flex items-center justify-center p-4">
        <div class="ds-modal bg-[var(--surface-0)] rounded-[var(--radius-xl)] shadow-2xl w-full max-w-2xl border border-[var(--border-1)] flex flex-col" id="transactionModalContent">
             <div class="p-6 border-b border-[var(--border-1)] flex justify-between items-center bg-[var(--surface-2)]">
                <h3 class="text-xl font-bold text-[var(--text-1)]">Nova Transação</h3>
                <button onclick="closeTransactionModal()" class="text-[var(--text-3)] hover:text-red-500 font-bold">✕</button>
            </div>
            
            <form id="transactionForm" class="p-6 space-y-4" novalidate>
                <input type="hidden" name="action" value="create_transaction">
                <input type="hidden" name="id" value="">
                
                <div class="grid grid-cols-2 gap-4">
                     <div>
                        <label class="ds-label">Tipo</label>
                        <select name="tipo" class="ds-input appearance-none">
                            <option value="receita">Receita</option>
                            <option value="despesa">Despesa</option>
                        </select>
                    </div>
                    <div>
                        <label class="ds-label">Valor (R$)</label>
                        <input type="number" step="0.01" name="valor" required class="ds-input">
                    </div>
                </div>

                <div>
                    <label class="ds-label">Descrição</label>
                    <input type="text" name="descricao" required class="ds-input">
                </div>

                 <div class="grid grid-cols-2 gap-4">
                     <div>
                        <label class="ds-label">Categoria</label>
                          <select name="categoria" class="ds-input appearance-none">
                            <option value="vendas">Vendas</option>
                            <option value="marketing">Marketing</option>
                            <option value="operacional">Operacional</option>
                            <option value="pessoal">Pessoal</option>
                            <option value="impostos">Impostos</option>
                            <option value="outros">Outros</option>
                        </select>
                    </div>
                    <div>
                        <label class="ds-label">Data Vencimento</label>
                        <input type="date" name="data_vencimento" required class="ds-input">
                    </div>
                </div>
                
                <div>
                    <label class="ds-label">Status</label>
                    <select name="status" class="ds-input appearance-none">
                        <option value="pago">Pago / Recebido</option>
                        <option value="pendente">Pendente</option>
                    </select>
                </div>

                <!-- Recurrence Options -->
                <div class="flex items-center gap-4 pt-2">
                    <div class="flex items-center">
                        <input type="checkbox" id="recorrente" name="recorrente" onchange="toggleRecurrence()" class="w-4 h-4 text-[var(--brand)] border-gray-300 rounded focus:ring-[var(--brand)]">
                        <label for="recorrente" class="ml-2 text-sm font-medium text-[var(--text-2)]">Recorrente?</label>
                    </div>
                    
                    <div id="parcelasContainer" class="hidden flex-1">
                        <label class="ds-label">Qtd. Parcelas</label>
                        <input type="number" name="parcelas" min="2" max="120" value="12" class="ds-input">
                    </div>
                </div>

                <div class="pt-4 flex justify-end gap-3 sticky bottom-0 bg-[var(--surface-0)] py-2">
                    <button type="button" onclick="closeTransactionModal()" class="ds-btn ds-btn-secondary btn-spring">Cancelar</button>
                    <button type="submit" class="ds-btn ds-btn-primary btn-spring">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function getDesignSystemColor(varName, fallback) {
            return getComputedStyle(document.documentElement).getPropertyValue(varName).trim() || fallback;
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
                const month = parseInt(parts[1]) - 1; 
                start = new Date(year, month, 1);
                end = new Date(year, month + 1, 0);
            } else if (rangeParam === 'current' || rangeParam === 'this_month') {
                start = new Date(today.getFullYear(), today.getMonth(), 1);
                end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            } else if (rangeParam === 'last_month') {
                start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                end = new Date(today.getFullYear(), today.getMonth(), 0);
            } else if (rangeParam === '3months') {
                start = new Date(today.getFullYear(), today.getMonth() - 2, 1);
                end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            } else {
                start = new Date(today.getFullYear(), today.getMonth(), 1);
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
                if(!startDateStr) updateDateVariables(document.getElementById('dateRangeSelect').value);

                let type = document.getElementById('filterType') ? document.getElementById('filterType').value : 'all';
                
                const res = await fetch(`api/financeiro.php?start=${startDateStr}&end=${endDateStr}&type=${type}`);
                const data = await res.json();
                
                if(!data.success && data.error) console.error(data.error);

                updateKPIs(data);
                renderCharts(data);
                
                let filteredTransactions = data.transactions || [];
                
                if(type && type !== 'all') {
                    filteredTransactions = filteredTransactions.filter(t => t.tipo && t.tipo.toLowerCase() === type.toLowerCase());
                }
                
                renderTransactions(filteredTransactions);

            } catch(e) {
                console.error('Error loading financial data:', e.message || e);
            }
        }
        
        function updateKPIs(data) {
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
             
             const charts = data.charts || {};
             const flow = charts.cash_flow || {};
             const categoryData = charts.by_category || data.expensesByCategory || []; 

             if(chartCashInstance) chartCashInstance.destroy();
             if(chartExpensesInstance) chartExpensesInstance.destroy();

             const brandColor = getDesignSystemColor('--brand', '#00BF24');
             const border1Color = getDesignSystemColor('--border-1', '#E7E5E4');
             const text3Color = getDesignSystemColor('--text-3', '#78716C');

             const rgbVal = getComputedStyle(document.documentElement).getPropertyValue('--theme-color-rgb').trim() || '0,191,36';
             const gradient = ctxCash.createLinearGradient(0,0,0,300);
             gradient.addColorStop(0, `rgba(${rgbVal}, 0.15)`);
             gradient.addColorStop(1, `rgba(${rgbVal}, 0)`);

             chartCashInstance = new Chart(ctxCash, {
                 type: 'line',
                 data: {
                     labels: flow.labels || ['Out', 'Nov', 'Dez', 'Jan', 'Fev', 'Mar'],
                     datasets: [{
                         label: 'Fluxo Líquido',
                         data: (flow.revenue || []).map((r, i) => r - (flow.expenses?.[i]||0)),
                         borderColor: brandColor,
                         backgroundColor: gradient,
                         fill: true,
                         tension: 0.4
                     }]
                 },
                 options: {
                     maintainAspectRatio: false,
                     plugins: { legend: { display: false } },
                     scales: {
                         x: { grid: { display: false }, ticks: { color: text3Color } },
                         y: { grid: { color: border1Color }, ticks: { color: text3Color } }
                     }
                 }
             });

             const catLabels = categoryData.map(c => c.categoria);
             const catValues = categoryData.map(c => c.total);

             chartExpensesInstance = new Chart(ctxExp, {
                 type: 'doughnut',
                 data: {
                     labels: catLabels.length ? catLabels : ['Sem dados'],
                     datasets: [{
                         data: catValues.length ? catValues : [1], 
                         backgroundColor: ['#2563eb', '#d97706', '#6366f1', '#ef4444', '#16a34a', '#8b5cf6'],
                         borderWidth: 0
                     }]
                 },
                 options: {
                     maintainAspectRatio: false,
                     plugins: {
                        legend: { position: 'right', labels: { color: text3Color } }
                     }
                 }
             });
        }

        function renderTransactions(transactions) {
            const tbody = document.getElementById('transactionsTableBody');

            if(!transactions || !Array.isArray(transactions) || transactions.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="p-8 text-center text-[var(--text-3)] font-mono text-xs uppercase tracking-wider">Nenhuma transação encontrada.</td></tr>`;
                return;
            }

            try {
                tbody.innerHTML = transactions.map(t => {
                    const badgeType = t.status === 'pago' ? 'ds-badge-success' : 'ds-badge-warning';
                    const badgeText = t.status === 'pago' ? 'Pago' : 'Pendente';
                    return `
                    <tr class="ds-animate-spring transition">
                        <td class="p-4 pl-6 font-semibold text-[var(--text-1)]">${t.descricao || 'Sem descrição'}</td>
                        <td class="p-4">
                            <span class="ds-badge ds-badge-neutral">
                                ${t.categoria || 'Geral'}
                            </span>
                        </td>
                        <td class="p-4 text-[var(--text-3)] font-semibold">
                            ${t.data_despesa ? new Date(t.data_despesa + 'T00:00:00').toLocaleDateString('pt-BR') : 'Data Inválida'}
                            ${t.recorrente == 1 ? '<span class="ml-1 ds-badge ds-badge-brand font-mono text-[9px]">Recorrente</span>' : ''}
                        </td>
                        <td class="p-4">
                            <span class="ds-badge ${badgeType}">
                                ${badgeText}
                            </span>
                        </td>
                        <td class="p-4 font-mono font-bold text-right ${t.tipo==='receita' ? 'text-[var(--success)]' : 'text-[var(--danger)]'}">
                            ${t.tipo==='receita' ? '+' : '-'} R$ ${(parseFloat(t.valor)||0).toLocaleString('pt-BR', {minimumFractionDigits: 2})}
                        </td>
                        <td class="p-4 pr-6 text-right">
                            <button onclick='editTransaction(${JSON.stringify(t)})' class="text-[var(--text-3)] hover:text-blue-500 transition mr-2" title="Editar">
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <button onclick="deleteTransaction(${t.id})" class="text-[var(--text-3)] hover:text-red-500 transition" title="Excluir">
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
        const modalContent = document.getElementById('transactionModalContent');
        
        function openTransactionModal() {
            const form = document.getElementById('transactionForm');
            form.reset();
            form.querySelector('[name="id"]').value = '';
            form.querySelector('[name="action"]').value = 'create_transaction';
            
            modal.classList.add('active');
            modalContent.classList.add('active');
        }

        function closeTransactionModal() {
            modal.classList.remove('active');
            modalContent.classList.remove('active');
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
                showToast("Por favor, preencha Descrição e Valor.", "error");
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
                    showToast("Salvo com sucesso!");
                    closeTransactionModal();
                    loadFinancialData();
                } else {
                    showToast('Erro ao salvar: ' + (result.error || result.message || 'Desconhecido'), 'error');
                }
            } catch(err) {
                console.error(err);
                showToast('Erro de conexão: ' + err.message, 'error');
            } finally {
                submitBtn.innerText = originalText;
                submitBtn.disabled = false;
            }
        });

        function editTransaction(data) {
            openTransactionModal();
            const form = document.getElementById('transactionForm');
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
                    showToast("Transação excluída.");
                    loadFinancialData();
                } else {
                    showToast('Erro: ' + (data.error || 'Falha ao excluir'), 'error');
                }
            } catch(e) {
                console.error(e);
                showToast('Erro de conexão ao excluir', 'error');
            }
        }

        // Initialize
        loadFinancialData();

    </script>
    
    <!-- Settings Drawer -->
    <?php include 'components/settings_drawer.php'; ?>
    <script src="js/settings.js?v=<?= time() ?>"></script>
<?php include 'includes/footer.php'; ?>
