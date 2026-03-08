<?php
// PMDCRM/admin_tenants.php — Gestão de Tenants
require_once 'src/auth.php';
require_login();
require_admin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Tenants - PMDCRM Admin</title>
    <script src="js/theme-loader.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-200 pb-20 md:pb-0 md:pl-64 transition-colors">

    <?php include 'admin_nav.php'; ?>

    <main class="p-4 md:p-8 max-w-7xl mx-auto">
        <!-- Header -->
        <header class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-800 dark:text-white tracking-tight">Gestão de <span class="text-red-500">Tenants</span></h1>
                <p class="text-slate-500 mt-1">Gerenciar usuários, planos e acessos</p>
            </div>
            <button onclick="openCreateModal()" class="bg-red-600 hover:bg-red-700 text-white font-bold px-5 py-2.5 rounded-lg shadow-lg shadow-red-900/30 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Novo Usuário
            </button>
        </header>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm">
                <p class="text-xs font-bold text-slate-500 uppercase">Total</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white mt-1" id="statTotal">--</p>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm">
                <p class="text-xs font-bold text-green-600 dark:text-green-500 uppercase">Ativos</p>
                <p class="text-2xl font-black text-green-600 dark:text-green-400 mt-1" id="statActive">--</p>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm">
                <p class="text-xs font-bold text-yellow-600 dark:text-yellow-500 uppercase">Inativos</p>
                <p class="text-2xl font-black text-yellow-600 dark:text-yellow-400 mt-1" id="statInactive">--</p>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm">
                <p class="text-xs font-bold text-red-600 dark:text-red-500 uppercase">Suspensos</p>
                <p class="text-2xl font-black text-red-600 dark:text-red-400 mt-1" id="statSuspended">--</p>
            </div>
        </div>

        <!-- Search -->
        <div class="mb-4">
            <input type="text" id="searchInput" placeholder="Buscar por nome ou email..." 
                   class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg px-4 py-3 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 outline-none focus:border-red-500 dark:focus:border-red-600 transition shadow-sm" 
                   oninput="filterTable()">
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-bold uppercase text-[10px]">
                        <tr>
                            <th class="px-4 py-3">Usuário</th>
                            <th class="px-4 py-3">Plano</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Clientes</th>
                            <th class="px-4 py-3">Leads</th>
                            <th class="px-4 py-3">Criado em</th>
                            <th class="px-4 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="tenantsBody" class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-500 italic">Carregando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Create/Edit Modal -->
    <div id="userModal" class="fixed inset-0 bg-black/50 dark:bg-black/70 backdrop-blur-sm z-[200] hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl w-full max-w-md">
            <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center">
                <h3 class="font-bold text-lg text-slate-900 dark:text-white" id="modalTitle">Novo Usuário</h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-900 dark:hover:text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <form id="userForm" class="p-6 space-y-4" onsubmit="return saveUser(event)">
                <input type="hidden" id="editUserId">
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Nome</label>
                    <input type="text" id="inputNome" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2.5 text-slate-900 dark:text-white outline-none focus:border-red-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Email</label>
                    <input type="email" id="inputEmail" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2.5 text-slate-900 dark:text-white outline-none focus:border-red-500">
                </div>
                <div id="senhaGroup">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Senha</label>
                    <input type="password" id="inputSenha" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2.5 text-slate-900 dark:text-white outline-none focus:border-red-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Role</label>
                        <select id="inputRole" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2.5 text-slate-900 dark:text-white outline-none focus:border-red-500">
                            <option value="gestor">Gestor</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Plano</label>
                        <select id="inputPlan" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2.5 text-slate-900 dark:text-white outline-none focus:border-red-500">
                            <option value="1">Free</option>
                            <option value="2">Pro</option>
                            <option value="3">Enterprise</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition">Salvar</button>
            </form>
        </div>
    </div>

    <!-- Actions Dropdown (positioned dynamically) -->
    <div id="actionsMenu" class="fixed hidden bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xl z-[300] w-56 py-2">
        <button onclick="impersonateUser()" class="w-full px-4 py-2 text-left text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
            Acessar como Tenant
        </button>
        <button onclick="editUser()" class="w-full px-4 py-2 text-left text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            Editar Dados
        </button>
        <button onclick="resetPassword()" class="w-full px-4 py-2 text-left text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
            Resetar Senha
        </button>
        <hr class="border-slate-700 my-1">
        <button onclick="toggleStatus('ativo')" class="w-full px-4 py-2 text-left text-sm text-green-400 hover:bg-slate-100 dark:hover:bg-slate-700">✅ Ativar</button>
        <button onclick="toggleStatus('suspenso')" class="w-full px-4 py-2 text-left text-sm text-yellow-400 hover:bg-slate-100 dark:hover:bg-slate-700">⏸️ Suspender</button>
        <button onclick="toggleStatus('inativo')" class="w-full px-4 py-2 text-left text-sm text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700">🚫 Desativar</button>
        <hr class="border-slate-700 my-1">
        <button onclick="deleteUser()" class="w-full px-4 py-2 text-left text-sm text-red-400 hover:bg-red-900/30">🗑️ Excluir</button>
    </div>

    <script>
    let allUsers = [];
    let selectedUserId = null;

    document.addEventListener('DOMContentLoaded', loadUsers);
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#actionsMenu') && !e.target.closest('.actions-btn')) {
            document.getElementById('actionsMenu').classList.add('hidden');
        }
    });

    async function loadUsers() {
        try {
            const res = await fetch('api/admin_users.php?action=list');
            const data = await res.json();
            if (data.success) {
                allUsers = data.data;
                renderTable(allUsers);
                updateStats(allUsers);
            }
        } catch (e) { console.error(e); }
    }

    function updateStats(users) {
        document.getElementById('statTotal').textContent = users.length;
        document.getElementById('statActive').textContent = users.filter(u => u.status === 'ativo' || !u.status).length;
        document.getElementById('statInactive').textContent = users.filter(u => u.status === 'inativo').length;
        document.getElementById('statSuspended').textContent = users.filter(u => u.status === 'suspenso').length;
    }

    function filterTable() {
        const q = document.getElementById('searchInput').value.toLowerCase();
        const filtered = allUsers.filter(u => u.nome.toLowerCase().includes(q) || u.email.toLowerCase().includes(q));
        renderTable(filtered);
    }

    function renderTable(users) {
        const tbody = document.getElementById('tenantsBody');
        if (users.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">Nenhum usuário encontrado</td></tr>';
            return;
        }
        tbody.innerHTML = users.map(u => {
            const statusColors = { ativo: 'bg-green-900/30 text-green-400 border-green-800', inativo: 'bg-slate-800 text-slate-500 border-slate-700', suspenso: 'bg-yellow-900/30 text-yellow-400 border-yellow-800' };
            const statusClass = statusColors[u.status] || statusColors.ativo;
            const planColors = { Free: 'text-slate-400', Pro: 'text-blue-400', Enterprise: 'text-amber-400' };
            const planClass = planColors[u.plan_name] || 'text-slate-400';
            const date = new Date(u.created_at).toLocaleDateString('pt-BR', { day: '2-digit', month: 'short', year: 'numeric' });
            const isAdmin = u.role === 'admin';
            
            return `
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-sm font-bold ${isAdmin ? 'text-red-500 dark:text-red-400 border border-red-300 dark:border-red-800' : 'text-slate-500 dark:text-slate-400'}">
                                ${u.nome.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900 dark:text-white text-sm">${u.nome} ${isAdmin ? '<span class="text-[10px] bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400 px-1.5 py-0.5 rounded ml-1">ADMIN</span>' : ''}</p>
                                <p class="text-xs text-slate-500">${u.email}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3"><span class="text-xs font-bold ${planClass}">${u.plan_name || 'Free'}</span></td>
                    <td class="px-4 py-3"><span class="text-[10px] font-bold px-2 py-1 rounded border ${statusClass} uppercase">${u.status || 'ativo'}</span></td>
                    <td class="px-4 py-3 text-sm font-mono text-slate-400">${u.total_clients || 0}</td>
                    <td class="px-4 py-3 text-sm font-mono text-slate-400">${u.total_leads || 0}</td>
                    <td class="px-4 py-3 text-xs text-slate-500">${date}</td>
                    <td class="px-4 py-3 text-right">
                        <button class="actions-btn text-slate-400 hover:text-white p-1 rounded hover:bg-slate-700 transition" onclick="showActions(event, ${u.id})">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function showActions(event, userId) {
        event.stopPropagation();
        selectedUserId = userId;
        const menu = document.getElementById('actionsMenu');
        const rect = event.currentTarget.getBoundingClientRect();
        menu.style.top = (rect.bottom + 4) + 'px';
        menu.style.right = (window.innerWidth - rect.right) + 'px';
        menu.style.left = 'auto';
        menu.classList.remove('hidden');
    }

    function closeActionsMenu() {
        document.getElementById('actionsMenu').classList.add('hidden');
    }

    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Novo Usuário';
        document.getElementById('editUserId').value = '';
        document.getElementById('inputNome').value = '';
        document.getElementById('inputEmail').value = '';
        document.getElementById('inputSenha').value = '';
        document.getElementById('senhaGroup').style.display = '';
        document.getElementById('inputSenha').required = true;
        document.getElementById('userModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('userModal').classList.add('hidden');
    }

    async function saveUser(e) {
        e.preventDefault();
        const id = document.getElementById('editUserId').value;
        const body = {
            action: id ? 'update' : 'create',
            id: id ? parseInt(id) : undefined,
            nome: document.getElementById('inputNome').value,
            email: document.getElementById('inputEmail').value,
            role: document.getElementById('inputRole').value,
            plan_id: document.getElementById('inputPlan').value
        };
        if (!id) body.senha = document.getElementById('inputSenha').value;
        
        const res = await fetch('api/admin_users.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body) });
        const data = await res.json();
        if (data.success) { closeModal(); loadUsers(); } else { alert(data.error); }
    }

    function editUser() {
        closeActionsMenu();
        const u = allUsers.find(u => u.id == selectedUserId);
        if (!u) return;
        document.getElementById('modalTitle').textContent = 'Editar Usuário';
        document.getElementById('editUserId').value = u.id;
        document.getElementById('inputNome').value = u.nome;
        document.getElementById('inputEmail').value = u.email;
        document.getElementById('inputRole').value = u.role;
        document.getElementById('inputPlan').value = u.plan_id || 1;
        document.getElementById('senhaGroup').style.display = 'none';
        document.getElementById('inputSenha').required = false;
        document.getElementById('userModal').classList.remove('hidden');
    }

    async function toggleStatus(status) {
        closeActionsMenu();
        const res = await fetch('api/admin_users.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'toggle_status', id: selectedUserId, status }) });
        const data = await res.json();
        if (data.success) loadUsers(); else alert(data.error);
    }

    async function resetPassword() {
        closeActionsMenu();
        const pwd = prompt('Digite a nova senha para este usuário:');
        if (!pwd) return;
        const res = await fetch('api/admin_users.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'reset_password', id: selectedUserId, new_password: pwd }) });
        const data = await res.json();
        alert(data.success ? 'Senha resetada!' : data.error);
    }

    async function deleteUser() {
        closeActionsMenu();
        if (!confirm('Tem certeza que deseja EXCLUIR este usuário permanentemente?')) return;
        const res = await fetch('api/admin_users.php', { method: 'DELETE', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id: selectedUserId }) });
        const data = await res.json();
        if (data.success) loadUsers(); else alert(data.error);
    }

    async function impersonateUser() {
        closeActionsMenu();
        const res = await fetch('api/admin_impersonate.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ target_user_id: selectedUserId }) });
        const data = await res.json();
        if (data.success) window.location.href = '/dashboard';
        else alert(data.error);
    }
    </script>
</body>
</html>
