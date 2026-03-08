<?php
// PMDCRM/admin_logs.php — Activity Logs
require_once 'src/auth.php';
require_login();
require_admin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs de Atividade - PMDCRM Admin</title>
    <script src="js/theme-loader.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-200 pb-20 md:pb-0 md:pl-64 transition-colors">
    <?php include 'admin_nav.php'; ?>
    <main class="p-4 md:p-8 max-w-7xl mx-auto">
        <header class="mb-8">
            <h1 class="text-3xl font-extrabold text-slate-800 dark:text-white">Logs de <span class="text-red-500">Atividade</span></h1>
            <p class="text-slate-500 mt-1">Audit trail — todas as ações do sistema</p>
        </header>

        <div class="mb-4 flex gap-3">
            <select id="filterAction" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg px-3 py-2 text-sm text-slate-900 dark:text-white outline-none shadow-sm" onchange="loadLogs()">
                <option value="">Todas as Ações</option>
                <option value="user_created">Criação de Usuário</option>
                <option value="user_deleted">Exclusão de Usuário</option>
                <option value="user_status_changed">Mudança de Status</option>
                <option value="user_role_changed">Mudança de Role</option>
                <option value="user_plan_changed">Mudança de Plano</option>
                <option value="user_password_reset">Reset de Senha</option>
                <option value="impersonate_start">Início Impersonação</option>
                <option value="impersonate_end">Fim Impersonação</option>
            </select>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-bold uppercase text-[10px]">
                    <tr>
                        <th class="px-4 py-3">Data</th>
                        <th class="px-4 py-3">Admin</th>
                        <th class="px-4 py-3">Ação</th>
                        <th class="px-4 py-3">Detalhes</th>
                        <th class="px-4 py-3">IP</th>
                    </tr>
                </thead>
                <tbody id="logsBody" class="divide-y divide-slate-100 dark:divide-slate-800">
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500 italic">Carregando...</td></tr>
                </tbody>
            </table>
        </div>
    </main>
    <script>
    document.addEventListener('DOMContentLoaded', loadLogs);
    async function loadLogs() {
        const action = document.getElementById('filterAction').value;
        try {
            const res = await fetch('api/admin_logs.php?action_filter=' + action);
            const data = await res.json();
            if (data.success) {
                const tbody = document.getElementById('logsBody');
                if (data.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">Nenhum log encontrado</td></tr>';
                    return;
                }
                const actionLabels = { user_created: '👤 Criação', user_deleted: '🗑️ Exclusão', user_status_changed: '🔄 Status', user_role_changed: '🎭 Role', user_plan_changed: '📋 Plano', user_password_reset: '🔑 Senha', impersonate_start: '👁️ Impersonar', impersonate_end: '↩️ Sair Imp.', user_updated: '✏️ Edição' };
                tbody.innerHTML = data.data.map(l => {
                    const date = new Date(l.created_at).toLocaleString('pt-BR');
                    const label = actionLabels[l.action] || l.action;
                    return `<tr class="hover:bg-slate-800/50"><td class="px-4 py-3 text-xs text-slate-500 whitespace-nowrap">${date}</td><td class="px-4 py-3 text-sm font-medium text-white">${l.admin_nome || 'Sistema'}</td><td class="px-4 py-3"><span class="text-xs font-bold bg-slate-800 px-2 py-1 rounded">${label}</span></td><td class="px-4 py-3 text-xs text-slate-400 max-w-xs truncate">${l.details || '-'}</td><td class="px-4 py-3 text-xs text-slate-600 font-mono">${l.ip_address || '-'}</td></tr>`;
                }).join('');
            }
        } catch (e) { console.error(e); }
    }
    </script>
</body>
</html>
