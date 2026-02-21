<?php
// PMDCRM/clientes.php
require_once 'src/auth.php';
require_login();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="js/theme-loader.js"></script>
    <title>PMDCRM - Clientes</title>
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
<body class="bg-gray-50 dark:bg-[#0f172a] pb-20 md:pb-0 md:pl-64 transition-colors duration-300">

    <?php include 'nav.php'; ?>

    <main class="p-4 md:p-8">
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 dark:text-white tracking-tight">Clientes</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1">Gerencie sua carteira e contratos</p>
            </div>
            <div class="flex items-center gap-2 md:gap-4">
                <?php include 'header_icons.php'; ?>
                
                 <!-- Notification Bell -->
                 <div class="relative">
                    <button id="notifBtn" class="bg-white dark:bg-slate-800 p-2 rounded-full shadow-sm border border-gray-200 dark:border-slate-700 text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span id="notifBadge" class="absolute -top-1 -right-1 bg-rose-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full hidden shadow-sm">0</span>
                    </button>
                    <!-- Dropdown -->
                    <div id="notifDropdown" class="absolute right-0 mt-2 w-80 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-gray-200 dark:border-slate-700 hidden z-50 overflow-hidden">
                        <div class="p-4 border-b border-gray-200 dark:border-slate-800 font-bold text-slate-700 dark:text-white text-sm bg-gray-50 dark:bg-slate-800/50">Notificações</div>
                        <ul id="notifList" class="max-h-80 overflow-y-auto text-sm text-slate-500 dark:text-slate-400">
                            <li class="p-6 text-center text-slate-400 dark:text-slate-500">Nenhuma notificação</li>
                        </ul>
                    </div>
                </div>

        <button onclick="openModal('new')" class="bg-[var(--theme-color)] hover:brightness-90 text-white px-5 py-2.5 rounded-xl font-medium shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition duration-200 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Novo Cliente
                </button>
            </div>
        </header>

        <!-- Tabs Filter -->
        <div class="flex space-x-1 mb-6 bg-white dark:bg-slate-800 p-1.5 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 w-fit">
            <button onclick="filterClients('ativo')" id="tab-ativo" class="px-5 py-2 rounded-lg text-sm font-semibold transition bg-green-500/10 text-green-500 shadow-sm ring-1 ring-green-500/50">Ativos</button>
            <button onclick="filterClients('inativo')" id="tab-inativo" class="px-5 py-2 rounded-lg text-sm font-medium transition text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-700">Inativos</button>
        </div>

        <!-- Client List -->
        <div class="bg-white dark:bg-slate-800/40 backdrop-blur-sm border border-gray-200 dark:border-slate-700/50 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider font-semibold border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50">
                            <th class="p-5">Empresa / Contrato</th>
                            <th class="p-5 hidden md:table-cell">Responsável</th>
                            <th class="p-5">Financeiro/Status</th>
                            <th class="p-5 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="clientsTableBody" class="text-slate-600 dark:text-slate-300 text-sm divide-y divide-gray-200 dark:divide-slate-800">
                      <!-- Content filled by JS -->
                    </tbody>
                </table>
            </div>
             <div id="loading" class="hidden p-8 text-center text-slate-500 dark:text-slate-400">
                <svg class="animate-spin h-8 w-8 mx-auto text-[var(--theme-color)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <p class="mt-2 text-xs">Carregando clientes...</p>
            </div>
             <div id="emptyState" class="hidden p-12 text-center text-slate-400 dark:text-slate-500">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <p>Nenhum cliente encomtrado.</p>
            </div>
        </div>

    </main>
    
    <!-- Modal Cliente -->
    <div id="clientModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white dark:bg-[#1e293b] rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto transform scale-95 transition-transform duration-300 border border-gray-200 dark:border-slate-700" id="modalContent">
            <div class="p-6 border-b border-gray-200 dark:border-slate-700 flex justify-between items-center sticky top-0 bg-white dark:bg-[#1e293b] z-10">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <span class="w-2 h-8 bg-[var(--theme-color)] rounded-full"></span>
                    <span id="modalTitle">Novo Cliente</span>
                </h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-rose-500 transition p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form id="clientForm" class="p-6 space-y-6">
                <input type="hidden" name="id" id="client_id">
                
                <!-- Grid Layout for Form -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Col 1: Basic Info -->
                    <div class="space-y-4">
                        <h4 class="text-xs uppercase text-slate-500 dark:text-slate-400 font-bold tracking-wider mb-4 border-b border-gray-200 dark:border-slate-700 pb-2">Dados da Empresa</h4>
                        
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Nome da Empresa *</label>
                            <input type="text" name="nome_empresa" id="nome_empresa" required class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:border-[var(--theme-color)] focus:ring-1 focus:ring-[var(--theme-color)] outline-none transition">
                        </div>
                         
                         <div class="grid grid-cols-2 gap-4">
                             <div>
                                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">CNPJ</label>
                                <input type="text" name="cnpj" id="cnpj" class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:border-[var(--theme-color)] outline-none transition">
                             </div>
                             <div>
                                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Responsável</label>
                                <input type="text" name="nome_responsavel" id="nome_responsavel" class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:border-[var(--theme-color)] outline-none transition">
                             </div>
                         </div>

                         <div class="grid grid-cols-2 gap-4">
                             <div>
                                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Email</label>
                                <input type="email" name="email" id="email" class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:border-[var(--theme-color)] outline-none transition">
                             </div>
                             <div>
                                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">WhatsApp</label>
                                <input type="text" name="whatsapp" id="whatsapp" class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:border-[var(--theme-color)] outline-none transition">
                             </div>
                         </div>
                    </div>

                    <!-- Col 2: Contract Info -->
                    <div class="space-y-4">
                         <h4 class="text-xs uppercase text-slate-500 dark:text-slate-400 font-bold tracking-wider mb-4 border-b border-gray-200 dark:border-slate-700 pb-2">Contrato & Financeiro</h4>
                         
                         <div class="grid grid-cols-2 gap-4">
                             <div>
                                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Valor Mensal (R$)</label>
                                <input type="number" step="0.01" name="valor_mensal" id="valor_mensal" class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:border-[var(--theme-color)] outline-none transition">
                             </div>
                             <div>
                                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Dia Vencimento</label>
                                <input type="number" min="1" max="31" name="dia_vencimento" id="dia_vencimento" class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:border-[var(--theme-color)] outline-none transition">
                             </div>
                         </div>

                         <div class="grid grid-cols-2 gap-4">
                             <div>
                                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Início Contrato</label>
                                <input type="date" name="data_inicio" id="data_inicio" class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:border-[var(--theme-color)] outline-none transition white-calendar-icon">
                             </div>
                             <div>
                                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Duração (Meses)</label>
                                <select name="duracao_contrato" id="duracao_contrato" class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:border-[var(--theme-color)] outline-none transition">
                                    <option value="12">1 Ano</option>
                                    <option value="1">1 Mês</option>
                                    <option value="2">2 Meses</option>
                                    <option value="3">3 Meses</option>
                                    <option value="4">4 Meses</option>
                                    <option value="5">5 Meses</option>
                                    <option value="6">6 Meses</option>
                                    <option value="0">Indeterminado</option>
                                </select>
                             </div>
                         </div>

                         <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Status Contrato</label>
                            <select name="status_contrato" id="status_contrato" class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:border-[var(--theme-color)] outline-none transition">
                                <option value="ativo">Ativo</option>
                                <option value="pendente">Pendente Assinatura</option>
                                <option value="suspenso">Suspenso</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                         </div>
                    </div>
                </div>
                
                <!-- Additional Info -->
                <div class="space-y-4 pt-4 border-t border-gray-200 dark:border-slate-700">
                     <h4 class="text-xs uppercase text-slate-500 dark:text-slate-400 font-bold tracking-wider">Informações Adicionais</h4>
                     
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                         <!-- Col 1 -->
                         <div class="space-y-4">
                             <div class="grid grid-cols-2 gap-4">
                                 <div>
                                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Nicho</label>
                                    <input type="text" name="nicho" id="nicho" class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:border-[var(--theme-color)] outline-none transition">
                                 </div>
                                 <div>
                                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Origem</label>
                                    <input type="text" name="origem" id="origem" class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:border-[var(--theme-color)] outline-none transition">
                                 </div>
                             </div>
                             <div>
                                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Produto / Serviço</label>
                                <input type="text" name="produto_servico" id="produto_servico" class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:border-[var(--theme-color)] outline-none transition">
                             </div>
                             <div class="grid grid-cols-2 gap-4">
                                 <div>
                                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Instagram (@)</label>
                                    <input type="text" name="instagram" id="instagram" class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:border-[var(--theme-color)] outline-none transition">
                                 </div>
                                 <div>
                                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Site / LP</label>
                                    <input type="url" name="landing_page_url" id="landing_page_url" placeholder="https://" class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:border-[var(--theme-color)] outline-none transition">
                                 </div>
                             </div>
                         </div>
                         
                         <!-- Col 2: Endereço & Obs -->
                         <div class="space-y-4">
                             <div>
                                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Endereço Completo</label>
                                <input type="text" name="endereco" id="endereco" class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:border-[var(--theme-color)] outline-none transition">
                             </div>
                             <div class="grid grid-cols-3 gap-4">
                                 <div class="col-span-2">
                                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Cidade</label>
                                    <input type="text" name="cidade" id="cidade" class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:border-[var(--theme-color)] outline-none transition">
                                 </div>
                                 <div>
                                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Estado</label>
                                    <input type="text" name="estado" id="estado" maxlength="2" placeholder="SP" class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:border-[var(--theme-color)] outline-none transition uppercase">
                                 </div>
                             </div>
                             <div>
                                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Observações Principais</label>
                                <textarea name="obs" id="obs" rows="2" class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:border-[var(--theme-color)] outline-none transition resize-none"></textarea>
                             </div>
                         </div>
                     </div>
                </div>
                
                 <!-- Link Folder -->
                 <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Link Pasta Cliente (Drive/Dropbox)</label>
                    <input type="url" name="link_pasta" id="link_pasta" class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2.5 text-slate-900 dark:text-white focus:border-[var(--theme-color)] outline-none transition">
                 </div>

                <!-- Actions -->
                <div class="pt-6 border-t border-gray-200 dark:border-slate-700 flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-5 py-2.5 rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-700 font-medium transition">Cancelar</button>
                    <button type="submit" class="bg-[var(--theme-color)] hover:brightness-90 text-white px-6 py-2.5 rounded-lg font-bold shadow-lg transition transform hover:-translate-y-0.5" id="btnSave">Salvar Cliente</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Init Settings (Theme)
        if(localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        // --- Client Logic ---
        let clientsData = [];

        async function fetchClients() {
            const table = document.getElementById('clientsTableBody');
            const loading = document.getElementById('loading');
            const empty = document.getElementById('emptyState');
            
            table.innerHTML = '';
            loading.classList.remove('hidden');
            empty.classList.add('hidden');
            
            try {
                const res = await fetch('api/clientes.php');
                const data = await res.json();
                
                if (!Array.isArray(data)) {
                    if (data.error) throw new Error(data.error);
                    throw new Error('Formato inválido');
                }

                clientsData = data;
                renderClients(data);

            } catch (err) {
                console.error(err);
                alert('Erro ao carregar clientes');
            } finally {
                loading.classList.add('hidden');
            }
        }

        function renderClients(data) {
             const table = document.getElementById('clientsTableBody');
             const empty = document.getElementById('emptyState');

             table.innerHTML = '';
             
             if(data.length === 0) {
                 empty.classList.remove('hidden');
                 return;
             }
             
             empty.classList.add('hidden');

             data.forEach(client => {
                 // Map properties for display
                 const dueDate = client.dia_pagamento || client.dia_vencimento; // DB is dia_pagamento typically
                 const folderUrl = client.pasta_drive_url || client.link_pasta;
                 const phone = client.telefone || client.whatsapp;

                 const diffDays = getDaysUntilDue(dueDate);
                 const statusColor = getStatusColor(client.status_contrato);
                 const statusLabel = formatStatus(client.status_contrato);
                 const avatar = client.foto_perfil || null;

                 table.innerHTML += `
                    <tr onclick="openClientDrawer(${client.id})" class="cursor-pointer hover:bg-gray-50 dark:hover:bg-white/5 transition group border-b border-gray-100 dark:border-slate-800 last:border-0 relative">
                        <td class="p-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-lg bg-gray-200 dark:bg-slate-700 flex-shrink-0 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-slate-600">
                                    ${avatar 
                                        ? `<img src="${avatar}" class="w-full h-full object-cover">` 
                                        : `<span class="text-xl font-bold text-slate-400 dark:text-slate-500">${client.nome_empresa.charAt(0)}</span>`
                                    }
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800 dark:text-white text-base">${client.nome_empresa || 'Sem Nome'}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-0.5">
                                        CNPJ: ${client.cnpj || 'N/A'}
                                        ${folderUrl ? `<a href="${folderUrl}" target="_blank" onclick="event.stopPropagation()" class="bg-blue-500/10 text-blue-500 px-1.5 py-0.5 rounded ml-2 hover:bg-blue-500/20 transition flex items-center gap-1" title="Abrir Pasta"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path></svg> Pasta</a>` : ''}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 hidden md:table-cell">
                            <div class="text-sm font-medium text-slate-600 dark:text-slate-300">${client.nome_responsavel || '-'}</div>
                            <div class="text-xs text-slate-400">${phone || '-'}</div>
                        </td>
                        <td class="p-4">
                            <div class="flex flex-col gap-1">
                                <span class="text-sm font-mono font-bold text-slate-800 dark:text-slate-200">R$ ${parseFloat(client.valor_mensal || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</span>
                                <div class="flex gap-2 text-[10px] uppercase font-bold">
                                     <span class="${statusColor}">${statusLabel}</span>
                                     <span class="${diffDays < 5 ? 'text-rose-500' : 'text-slate-500'}">Vence dia ${dueDate || '?'}</span>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-right">
                             <div class="flex justify-end gap-2 opacity-50 group-hover:opacity-100 transition">
                                <button onclick="event.stopPropagation(); editClient(${client.id})" class="p-2 hover:bg-blue-500/10 text-blue-500 rounded-lg transition" title="Editar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button onclick="event.stopPropagation(); deleteClient(${client.id})" class="p-2 hover:bg-rose-500/10 text-rose-500 rounded-lg transition" title="Excluir">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                             </div>
                        </td>
                    </tr>
                 `;
             });
        }

        function filterClients(status) {
            const btnAtivo = document.getElementById('tab-ativo');
            const btnInativo = document.getElementById('tab-inativo');

            // Reset classes
            btnAtivo.className = "px-5 py-2 rounded-lg text-sm font-medium transition text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-700";
            btnInativo.className = "px-5 py-2 rounded-lg text-sm font-medium transition text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-700";

            if (status === 'ativo') {
                btnAtivo.className = "px-5 py-2 rounded-lg text-sm font-bold transition bg-[var(--theme-color)] text-white shadow-sm ring-1 ring-black/5 dark:ring-white/10";
                renderClients(clientsData.filter(c => c.status_contrato === 'ativo' || c.status_contrato === 'pendente'));
            } else {
                btnInativo.className = "px-5 py-2 rounded-lg text-sm font-bold transition bg-gray-200 dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm ring-1 ring-black/5 dark:ring-white/10";
                renderClients(clientsData.filter(c => c.status_contrato === 'cancelado' || c.status_contrato === 'suspenso'));
            }
        }

        // Helpers
        function getDaysUntilDue(day) {
            if (!day) return 0;
            const today = new Date().getDate();
            if (day < today) return 30 - (today - day); // Next month approx
            return day - today;
        }

        function getStatusColor(status) {
            if (!status) return 'text-slate-500';
            const map = {
                'ativo': 'text-green-500',
                'pendente': 'text-yellow-500',
                'suspenso': 'text-orange-500',
                'cancelado': 'text-red-500'
            };
            return map[status] || 'text-slate-500';
        }

        function formatStatus(status) {
            if (!status) return 'Indefinido';
            return status.charAt(0).toUpperCase() + status.slice(1);
        }

        // Modal Logic
        const modal = document.getElementById('clientModal');
        const modalContent = document.getElementById('modalContent');
        const form = document.getElementById('clientForm');

        function openModal(mode, data = null) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
            
            if(mode === 'edit' && data) {
                document.getElementById('modalTitle').innerText = 'Editar Cliente';
                document.getElementById('client_id').value = data.id;
                document.getElementById('nome_empresa').value = data.nome_empresa;
                document.getElementById('cnpj').value = data.cnpj || ''; // DB might miss this col
                document.getElementById('nome_responsavel').value = data.nome_responsavel;
                document.getElementById('email').value = data.email;
                document.getElementById('whatsapp').value = data.telefone || ''; // Map telefone
                document.getElementById('valor_mensal').value = data.valor_mensal;
                document.getElementById('dia_vencimento').value = data.dia_pagamento || ''; // Map dia_pagamento
                document.getElementById('data_inicio').value = data.data_inicio_contrato || ''; // Map data_inicio_contrato
                document.getElementById('link_pasta').value = data.pasta_drive_url || ''; // Map pasta_drive_url
                document.getElementById('nicho').value = data.nicho || '';
                document.getElementById('origem').value = data.origem || '';
                document.getElementById('produto_servico').value = data.produto_servico || '';
                document.getElementById('instagram').value = data.instagram || '';
                document.getElementById('landing_page_url').value = data.landing_page_url || '';
                document.getElementById('endereco').value = data.endereco || '';
                document.getElementById('cidade').value = data.cidade || '';
                document.getElementById('estado').value = data.estado || '';
                document.getElementById('obs').value = data.obs || '';
                 if (data.status_contrato) document.getElementById('status_contrato').value = data.status_contrato;
            } else {
                document.getElementById('modalTitle').innerText = 'Novo Cliente';
                form.reset();
                document.getElementById('client_id').value = '';
            }
        }

        function closeModal() {
            modal.classList.add('opacity-0');
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        function editClient(id) {
            const client = clientsData.find(c => c.id == id);
            if(client) openModal('edit', client);
        }

        async function deleteClient(id) {
            if(!confirm('Tem certeza? Isso apagará histórico financeiro também.')) return;
            
            try {
                const res = await fetch('api/clientes.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                });
                const data = await res.json();
                
                if (data.success) {
                    fetchClients();
                } else {
                    alert('Erro: ' + (data.error || 'Falha ao excluir'));
                }
            } catch(e) { 
                console.error(e);
                alert('Erro de conexão');
            }
        }

        // Save
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            const btn = document.getElementById('btnSave');
            const originalText = btn.innerText;
            btn.innerText = 'Salvando...';
            btn.disabled = true;

            try {
                const res = await fetch('api/clientes.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                
                if(result.success) {
                    closeModal();
                    fetchClients();
                    // showToast('Sucesso!', 'success');
                } else {
                    alert('Erro: ' + (result.message || 'Desconhecido'));
                }
            } catch(e) {
                console.error(e);
                alert('Erro de conexão');
            } finally {
                btn.innerText = originalText;
                btn.disabled = false;
            }
        });

        // Init
        fetchClients();

    </script>

    <!-- Settings Drawer -->
    <?php include 'components/settings_drawer.php'; ?>
    <!-- Client Project Drawer -->
    <?php include 'components/client_drawer.php'; ?>
    
    <script src="js/settings.js?v=<?= time() ?>"></script>
</body>
</html>
