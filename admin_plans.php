<?php
// PMDCRM/admin_plans.php — Gestão de Planos
require_once 'src/auth.php';
require_login();
require_admin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planos - PMDCRM Admin</title>
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
            <h1 class="text-3xl font-extrabold text-slate-800 dark:text-white">Gestão de <span class="text-red-500">Planos</span></h1>
            <p class="text-slate-500 mt-1">Configurar limites e preços dos planos</p>
        </header>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="plansGrid">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-8 text-center shadow-sm"><p class="text-slate-500">Carregando planos...</p></div>
        </div>
    </main>
    <script>
    document.addEventListener('DOMContentLoaded', loadPlans);
    async function loadPlans() {
        try {
            const res = await fetch('api/admin_plans.php');
            const data = await res.json();
            if (data.success) renderPlans(data.data);
        } catch (e) { console.error(e); }
    }
    function renderPlans(plans) {
        const grid = document.getElementById('plansGrid');
        const colors = ['border-slate-700', 'border-blue-600', 'border-amber-500'];
        const badges = ['', 'POPULAR', 'PREMIUM'];
        grid.innerHTML = plans.map((p, i) => `
            <div class="bg-white dark:bg-slate-900 border-2 ${colors[i] || colors[0]} rounded-2xl p-6 relative shadow-sm ${i === 1 ? 'ring-2 ring-blue-600/20' : ''}">
                ${badges[i] ? `<span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-${i===1?'blue':'amber'}-600 text-white text-[10px] font-bold px-3 py-1 rounded-full">${badges[i]}</span>` : ''}
                <h3 class="text-2xl font-black text-slate-800 dark:text-white text-center mt-2">${p.name}</h3>
                <div class="text-center my-4">
                    <span class="text-4xl font-black text-slate-800 dark:text-white">R$ ${parseFloat(p.price).toFixed(0)}</span>
                    <span class="text-slate-500 text-sm">/mês</span>
                </div>
                <ul class="space-y-3 text-sm mb-6">
                    <li class="flex items-center gap-2 text-slate-300"><svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>Até <strong>${p.max_clients}</strong> clientes</li>
                    <li class="flex items-center gap-2 text-slate-300"><svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>Até <strong>${p.max_leads}</strong> leads</li>
                    <li class="flex items-center gap-2 text-slate-300"><svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg><strong>${p.max_integrations}</strong> integrações</li>
                </ul>
                <div class="flex gap-2">
                    <button onclick="editPlan(${p.id})" class="flex-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-900 dark:text-white py-2 rounded-lg text-sm font-bold transition">Editar</button>
                </div>
                <p class="text-center mt-3 text-[10px] text-slate-600">${p.is_active ? '✅ Ativo' : '🚫 Inativo'}</p>
            </div>
        `).join('');
    }
    function editPlan(id) { alert('Editor de plano será implementado na Fase 3. Plan ID: ' + id); }
    </script>
</body>
</html>
