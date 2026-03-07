<?php
// PMDCRM/admin_config.php — Configurações Globais
require_once 'src/auth.php';
require_login();
require_admin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações Globais - PMDCRM Admin</title>
    <script src="js/theme-loader.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-950 text-slate-200 pb-20 md:pb-0 md:pl-64">
    <?php include 'admin_nav.php'; ?>
    <main class="p-4 md:p-8 max-w-3xl mx-auto">
        <header class="mb-8">
            <h1 class="text-3xl font-extrabold text-white">Configurações <span class="text-red-500">Globais</span></h1>
            <p class="text-slate-500 mt-1">Configurações do sistema que afetam todos os tenants</p>
        </header>

        <div class="space-y-6">
            <!-- System Info -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <h3 class="font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Informações do Sistema
                </h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="bg-slate-800 rounded-lg p-3">
                        <p class="text-slate-500 text-xs">Versão</p>
                        <p class="font-bold text-white">v1.3.0</p>
                    </div>
                    <div class="bg-slate-800 rounded-lg p-3">
                        <p class="text-slate-500 text-xs">Ambiente</p>
                        <p class="font-bold text-green-400">Produção (Render)</p>
                    </div>
                    <div class="bg-slate-800 rounded-lg p-3">
                        <p class="text-slate-500 text-xs">Database</p>
                        <p class="font-bold text-blue-400">TiDB Cloud</p>
                    </div>
                    <div class="bg-slate-800 rounded-lg p-3">
                        <p class="text-slate-500 text-xs">PHP</p>
                        <p class="font-bold text-white"><?= phpversion() ?></p>
                    </div>
                </div>
            </div>

            <!-- Manutenção -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <h3 class="font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                    Manutenção
                </h3>
                <p class="text-sm text-slate-400 mb-3">Ferramentas de manutenção e limpeza do sistema.</p>
                <div class="flex gap-3">
                    <button onclick="if(confirm('Limpar logs com mais de 30 dias?')) cleanLogs()" class="bg-slate-800 hover:bg-slate-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition">🧹 Limpar Logs Antigos</button>
                </div>
            </div>

            <!-- Links Úteis -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <h3 class="font-bold text-white mb-4">🔗 Links Rápidos</h3>
                <div class="space-y-2">
                    <a href="/dashboard" class="flex items-center gap-2 text-sm text-slate-400 hover:text-white transition">→ Dashboard do CRM</a>
                    <a href="/admin_tenants" class="flex items-center gap-2 text-sm text-slate-400 hover:text-white transition">→ Gestão de Tenants</a>
                    <a href="/admin_plans" class="flex items-center gap-2 text-sm text-slate-400 hover:text-white transition">→ Planos</a>
                    <a href="/admin_logs" class="flex items-center gap-2 text-sm text-slate-400 hover:text-white transition">→ Logs de Atividade</a>
                </div>
            </div>
        </div>
    </main>
    <script>
    async function cleanLogs() {
        try {
            const res = await fetch('api/admin_logs.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'clean_old' }) });
            const data = await res.json();
            alert(data.success ? 'Logs limpos!' : data.error);
        } catch (e) { alert('Erro'); }
    }
    </script>
</body>
</html>
