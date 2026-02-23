<?php
// PMDCRM/admin_dashboard.php
require_once 'src/auth.php';
require_login();
require_admin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - PMDCRM</title>
    <script src="js/theme-loader.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: 'var(--theme-color, #3b82f6)',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .card-bi { border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .dark .card-bi { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5); border: 1px solid #222; }
    </style>
</head>
<body class="bg-gray-100 text-slate-900 dark:bg-[#0f172a] dark:text-[#e2e8f0] pb-20 md:pb-0 md:pl-64 transition-colors duration-300">

    <?php include 'nav.php'; ?>

    <main class="p-4 md:p-8 max-w-7xl mx-auto">
        <!-- Header -->
        <header class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-800 dark:text-white tracking-tight">
                    Painel <span class="text-[var(--theme-color)]">Administrativo</span>
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Visão global do sistema (Métricas Multi-Tenant)</p>
            </div>
            <div class="bg-red-100 text-red-700 px-3 py-1 rounded text-sm font-bold shadow-sm">
                MODO SUPER ADMIN
            </div>
        </header>

        <!-- KPI Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            
            <!-- Inquilinos -->
            <div class="card-bi p-6 bg-white dark:bg-[#1e293b] border border-gray-100 dark:border-slate-800">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Total de Inquilinos</p>
                        <h3 class="text-3xl font-black font-mono text-slate-700 dark:text-white" id="kpiTenants">--</h3>
                    </div>
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/30 text-blue-500 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Global MRR -->
            <div class="card-bi p-6 bg-white dark:bg-[#1e293b] border border-gray-100 dark:border-slate-800">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">MRR Global</p>
                        <h3 class="text-3xl font-black font-mono text-slate-700 dark:text-white" id="kpiMRR">R$ 0,00</h3>
                    </div>
                    <div class="p-2 bg-green-50 dark:bg-green-900/30 text-green-500 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Leads Globally -->
            <div class="card-bi p-6 bg-white dark:bg-[#1e293b] border border-gray-100 dark:border-slate-800">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Total de Leads (Global)</p>
                        <h3 class="text-3xl font-black font-mono text-slate-700 dark:text-white" id="kpiLeads">--</h3>
                    </div>
                    <div class="p-2 bg-purple-50 dark:bg-purple-900/30 text-purple-500 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Total Customers -->
            <div class="card-bi p-6 bg-white dark:bg-[#1e293b] border border-gray-100 dark:border-slate-800">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Clientes Ativos (Global)</p>
                        <h3 class="text-3xl font-black font-mono text-slate-700 dark:text-white" id="kpiClients">--</h3>
                    </div>
                    <div class="p-2 bg-amber-50 dark:bg-amber-900/30 text-amber-500 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
            </div>

        </div>

        <!-- Recent Tenants Table -->
        <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                <h3 class="font-bold text-lg text-slate-800 dark:text-white">Inquilinos Recentes</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-bold uppercase text-[10px]">
                        <tr>
                            <th class="px-6 py-3">ID</th>
                            <th class="px-6 py-3">Inquilino</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Data Registro</th>
                        </tr>
                    </thead>
                    <tbody id="tenantsTableBody" class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr><td colspan="4" class="px-6 py-8 text-center text-slate-400 italic">Carregando dados...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                const res = await fetch('api/admin_metrics.php');
                const data = await res.json();
                
                if (data.success) {
                    const metrics = data.data;
                    document.getElementById('kpiTenants').innerText = metrics.total_tenants.toLocaleString('pt-BR');
                    document.getElementById('kpiMRR').innerText = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(metrics.global_mrr);
                    document.getElementById('kpiLeads').innerText = metrics.total_leads.toLocaleString('pt-BR');
                    document.getElementById('kpiClients').innerText = metrics.total_clients.toLocaleString('pt-BR');

                    const tbody = document.getElementById('tenantsTableBody');
                    tbody.innerHTML = '';
                    if (metrics.recent_tenants.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center">Nenhum usuário encontrado.</td></tr>';
                    } else {
                        metrics.recent_tenants.forEach(t => {
                            const date = new Date(t.created_at).toLocaleDateString('pt-BR', { day: '2-digit', month: 'short', year: 'numeric' });
                            tbody.innerHTML += `
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                    <td class="px-6 py-4 font-mono text-xs">#${t.id}</td>
                                    <td class="px-6 py-4 font-bold text-slate-800 dark:text-white">${t.nome}</td>
                                    <td class="px-6 py-4">${t.email}</td>
                                    <td class="px-6 py-4">${date}</td>
                                </tr>
                            `;
                        });
                    }
                } else {
                    document.getElementById('tenantsTableBody').innerHTML = '<tr><td colspan="4" class="px-6 py-8 text-center text-red-500 italic">Erro ao carregar dados</td></tr>';
                }
            } catch (err) {
                console.error(err);
            }
        });
    </script>
</body>
</html>
