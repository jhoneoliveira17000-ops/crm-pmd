<?php
// PMDCRM/crm_kanban.php
require_once 'src/auth.php';
require_login();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Kanban - PMDCRM</title>
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .kanban-col { min-height: calc(100vh - 200px); }
        .ghost-card { opacity: 0.5; background: #cbd5e1; border: 2px dashed #94a3b8; }
        .dark .ghost-card { background: #1e293b; border: 2px dashed #475569; }
        .lead-card { transition: transform 0.2s, box-shadow 0.2s; cursor: grab; }
        .lead-card:active { cursor: grabbing; transform: scale(1.02); }
         /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-track { background: #0f172a; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>
<body class="overflow-x-hidden bg-gray-50 dark:bg-[#0f172a] text-slate-800 dark:text-slate-200 transition-colors duration-300">

    <!-- Header -->
    <header class="bg-white dark:bg-slate-900 border-b border-gray-200 dark:border-slate-800 p-4 sticky top-0 z-50">
        <div class="flex justify-between items-center max-w-7xl mx-auto">
            <div class="flex items-center gap-4">
                <a href="dashboard.php" class="text-slate-400 hover:text-slate-900 dark:hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight flex items-center gap-2">
                    <span class="text-[var(--theme-color)]">Pipeline</span> de Vendas
                </h1>
            </div>

            <div class="flex items-center gap-3">
                 <?php include 'header_icons.php'; ?>
                 <button onclick="openSettingsModal()" class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-200 px-4 py-2 rounded-lg font-medium text-sm transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    Personalizar Funil
                </button>
                 <button onclick="openLeadModal()" class="bg-[var(--theme-color)] hover:bg-green-600 text-white px-5 py-2 rounded-lg font-bold text-sm transition shadow-lg shadow-green-900/40 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Novo Lead
                </button>
            </div>
        </div>
    </header>

    <main class="p-4 md:p-8 max-w-[1920px] mx-auto overflow-x-auto pb-20 md:pb-8 custom-scrollbar">
        <!-- Kanban Board -->
        <div class="flex gap-6 min-w-max pb-4" id="kanbanBoard">
            <!-- Columns loaded by JS -->
             <div class="w-80 flex-shrink-0 flex flex-col items-center justify-center text-slate-400 dark:text-slate-600 h-96">
                <svg class="animate-spin h-10 w-10 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <p>Carregando Pipeline...</p>
            </div>
        </div>
    </main>

    <!-- Lead Drawer (Slide-Over) -->
    <div id="leadDrawerOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-[60] transition-opacity duration-300 opacity-0" onclick="closeLeadModal()"></div>
    <div id="leadDrawer" class="fixed inset-y-0 right-0 z-[70] w-full max-w-lg bg-white dark:bg-[#1e293b] shadow-2xl transform transition-transform duration-300 translate-x-full border-l border-gray-200 dark:border-slate-700 flex flex-col">
        
        <!-- Header with Tabs -->
        <div class="bg-gray-50 dark:bg-slate-900/50 border-b border-gray-200 dark:border-slate-700">
            <div class="p-6 pb-0 flex justify-between items-start">
                <div>
                    <h3 class="font-bold text-xl text-slate-800 dark:text-white" id="modalTitle">Detalhes do Lead</h3>
                    <p class="text-xs text-slate-500 mt-1" id="modalSubtitle">Gerencie as informações deste lead.</p>
                </div>
                <button onclick="closeLeadModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-2 rounded-full hover:bg-black/5 dark:hover:bg-white/10 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <!-- Tabs -->
            <div class="flex items-center gap-6 px-6 mt-6">
                <button onclick="switchTab('details')" id="tab-btn-details" class="pb-3 text-sm font-bold text-[var(--theme-color)] border-b-2 border-[var(--theme-color)] transition-colors">Detalhes</button>
                <button onclick="switchTab('notes')" id="tab-btn-notes" class="pb-3 text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 border-b-2 border-transparent transition-colors">Anotações</button>
                <button onclick="switchTab('history')" id="tab-btn-history" class="pb-3 text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 border-b-2 border-transparent transition-colors">Histórico</button>
            </div>
        </div>

        <!-- Body (Scrollable) -->
        <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
            
            <!-- TAB: DETAILS -->
            <div id="tab-details" class="space-y-6">
                <form id="leadForm" class="space-y-6">
                    <input type="hidden" name="id" id="lead_id">
                    
                    <!-- Status/Convert Actions -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl border border-blue-100 dark:border-blue-800/50 flex flex-col gap-3">
                        <div class="flex items-center gap-3">
                             <div class="bg-blue-100 dark:bg-blue-800 text-blue-600 dark:text-blue-300 p-2 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                             </div>
                             <div>
                                 <h4 class="font-bold text-slate-800 dark:text-white text-sm">Ações Rápidas</h4>
                                 <p class="text-xs text-slate-500 dark:text-slate-400">Entre em contato ou mova o lead.</p>
                             </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" onclick="openWhatsApp()" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2.5 rounded-lg shadow-lg shadow-green-900/20 transition flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                WhatsApp
                            </button>
                            <button type="button" onclick="convertLeadCurrent()" id="btnConvertLead" class="w-full bg-slate-900 hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-bold py-2.5 rounded-lg shadow-lg transition flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Tornar Cliente
                            </button>
                        </div>
                    </div>

                <!-- Basic Info -->
                <div class="space-y-4">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Informações Básicas</h4>
                    
                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Nome Completo</label>
                        <input type="text" name="nome" id="lead_nome" required class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg p-3 text-slate-900 dark:text-white focus:border-[var(--theme-color)] focus:ring-1 focus:ring-[var(--theme-color)] outline-none transition">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Valor (R$)</label>
                             <input type="number" step="0.01" name="valor" id="lead_valor" class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg p-3 text-slate-900 dark:text-white focus:border-[var(--theme-color)] focus:ring-1 focus:ring-[var(--theme-color)] outline-none transition">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">WhatsApp / Tel</label>
                             <input type="text" name="contato" id="lead_contato" class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg p-3 text-slate-900 dark:text-white focus:border-[var(--theme-color)] focus:ring-1 focus:ring-[var(--theme-color)] outline-none transition">
                        </div>
                    </div>
                </div>
                
                <!-- Dynamic Answers (Meta Ads) -->
                <div id="leadMetaAnswers" class="hidden space-y-3 pt-4 border-t border-gray-200 dark:border-slate-700">
                     <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.791-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        Respostas do Formulário
                     </h4>
                     <div id="metaAnswersList" class="space-y-3"></div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Origem</label>
                        <select name="origem" id="lead_origem" class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg p-3 text-slate-900 dark:text-white focus:border-[var(--theme-color)] focus:ring-1 focus:ring-[var(--theme-color)] outline-none transition appearance-none">
                            <option value="Indicação">Indicação</option>
                            <option value="Instagram">Instagram</option>
                            <option value="Google Ads">Google Ads</option>
                            <option value="Linkedin">Linkedin</option>
                            <option value="Outros">Outros</option>
                        </select>
                    </div>
                    
                     <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Etapa do Funil</label>
                        <select name="etapa_id" id="lead_etapa_id" class="w-full bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 rounded-lg p-3 text-slate-900 dark:text-white focus:border-[var(--theme-color)] focus:ring-1 focus:ring-[var(--theme-color)] outline-none transition appearance-none">
                            <!-- Loaded via JS -->
                        </select>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="pt-6 border-t border-gray-200 dark:border-slate-700 flex justify-between items-center sticky bottom-0 bg-white dark:bg-[#1e293b] py-4">
                    <button type="button" id="btnDeleteLead" onclick="deleteLeadCurrent()" class="text-red-500 hover:text-red-700 font-medium px-4 py-2 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition flex items-center gap-2 whitespace-nowrap" style="display: none;">
                        <svg class="w-4 h-4" flex-shrink-0 fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Excluir Lead
                    </button>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="closeLeadModal()" class="px-5 py-2.5 rounded-lg border border-gray-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition font-medium whitespace-nowrap">Cancelar</button>
                        <button type="submit" class="bg-slate-900 dark:bg-white hover:bg-slate-800 dark:hover:bg-gray-200 text-white dark:text-slate-900 px-6 py-2.5 rounded-lg font-bold shadow-lg transition whitespace-nowrap">Salvar Alterações</button>
                    </div>
                </div>
            </form>
            </div> <!-- End Tab Details -->

            <!-- TAB: NOTES -->
            <div id="tab-notes" class="hidden space-y-6">
                <!-- Add Note -->
                <div class="bg-gray-50 dark:bg-slate-800/50 p-4 rounded-xl border border-gray-100 dark:border-slate-700">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase">Nova Anotação</label>
                    <textarea id="newNoteInput" rows="3" class="w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-lg p-3 text-sm text-slate-800 dark:text-slate-200 focus:border-[var(--theme-color)] focus:ring-1 focus:ring-[var(--theme-color)] outline-none transition resize-none placeholder:text-slate-400" placeholder="Digite uma observação importante..."></textarea>
                    <div class="flex justify-end mt-2">
                        <button type="button" onclick="saveNote()" id="btnSaveNote" class="bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white text-xs font-bold px-4 py-2 rounded-lg transition flex items-center gap-2">
                            Salvar Nota
                        </button>
                    </div>
                </div>

                <!-- Notes List -->
                <div class="space-y-4">
                     <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Histórico de Anotações</h4>
                     <div id="notesList" class="space-y-4 relative before:absolute before:inset-y-0 before:left-2 before:w-0.5 before:bg-gray-200 dark:before:bg-slate-700">
                         <!-- Loaded via JS -->
                         <p class="text-sm text-slate-400 pl-6 italic">Carregando anotações...</p>
                     </div>
                </div>
            </div>

            <!-- TAB: HISTORY -->
            <div id="tab-history" class="hidden space-y-4">
                 <div class="p-4 bg-yellow-50 dark:bg-yellow-900/10 text-yellow-800 dark:text-yellow-200 rounded-lg text-sm flex items-center gap-2">
                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                     <span>Histórico de alterações em breve.</span>
                 </div>
            </div>
        </div>
    </div> <!-- END leadDrawer -->

    <!-- Settings Modal (Funnel Customization) -->
    <div id="settingsModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-[70] flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-2xl w-full max-w-2xl border border-gray-200 dark:border-slate-700 h-[80vh] flex flex-col transform transition-all scale-95 opacity-0" id="settingsModalContent">
            <div class="p-6 border-b border-gray-200 dark:border-slate-700 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-xl text-slate-800 dark:text-white">Personalizar Funil</h3>
                    <p class="text-xs text-slate-500 mt-1">Arraste para reordenar, clique para editar.</p>
                </div>
                <button onclick="closeSettingsModal()" class="text-slate-400 hover:text-white transition">✕</button>
            </div>
            
            <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
                <div id="stagesList" class="space-y-3">
                    <!-- Populated by JS -->
                </div>
                
                <button onclick="addNewStage()" class="mt-4 w-full py-3 border-2 border-dashed border-gray-300 dark:border-slate-700 rounded-lg text-slate-500 dark:text-slate-400 hover:border-[var(--theme-color)] hover:text-[var(--theme-color)] transition flex items-center justify-center gap-2 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Adicionar Nova Etapa
                </button>
            </div>
            
            <div class="p-6 border-t border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50 rounded-b-xl flex justify-end">
                <button onclick="closeSettingsModal()" class="bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-slate-700 dark:text-white px-6 py-2.5 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-slate-600 transition">Concluído</button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Init Theme
        if(localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        // --- Kanban Logic ---
        let stagesMap = {};
        let leadsMap = {}; // Global map of leads by ID 

        async function loadKanban() {
            try {
                const res = await fetch('api/kanban.php');
                const data = await res.json();
                
                if (data.error) throw new Error(data.error);
                if (!data.stages) throw new Error('Dados inválidos (sem etapas)');

                const stages = data.stages;
                const leads = data.leads || [];

                // Map leads to stages
                stages.forEach(stage => {
                    stage.leads = leads.filter(l => l.status_id == stage.id);
                });
                
                const board = document.getElementById('kanbanBoard');
                const stageSelect = document.getElementById('lead_etapa_id');
                
                board.innerHTML = '';
                stageSelect.innerHTML = '';
                stagesMap = {};

                stages.forEach(stage => {
                    stagesMap[stage.id] = stage.name;
                    
                    // Add to Modal Select
                    stageSelect.innerHTML += `<option value="${stage.id}">${stage.name}</option>`;

                    // Create Column
                    const colDiv = document.createElement('div');
                    colDiv.className = 'w-80 flex-shrink-0 flex flex-col bg-gray-100 dark:bg-slate-800/50 backdrop-blur-sm rounded-xl border border-gray-200 dark:border-slate-700/50 h-full max-h-[calc(100vh-140px)]';
                    colDiv.innerHTML = `
                        <!-- Column Header -->
                        <div class="p-4 border-b border-gray-200 dark:border-slate-700 flex justify-between items-center sticky top-0 bg-gray-100 dark:bg-slate-800 rounded-t-xl z-10">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full" style="background-color: ${stage.color}"></span>
                                <h3 class="font-bold text-slate-700 dark:text-white text-sm uppercase tracking-wide">${stage.name}</h3>
                                <span class="bg-gray-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs px-2 py-0.5 rounded-full font-mono">${stage.leads ? stage.leads.length : 0}</span>
                            </div>
                        </div>
                        
                        <!-- Cards Container -->
                        <div class="p-3 flex-1 overflow-y-auto custom-scrollbar space-y-3 kanban-col" data-stage="${stage.id}">
                            ${stage.leads ? stage.leads.map(lead => {
                                leadsMap[lead.id] = lead; // Populate map
                                return renderCard(lead);
                            }).join('') : ''}
                        </div>
                    `;
                    
                    board.appendChild(colDiv);
                });
                
                initDragAndDrop();

            } catch(e) {
                console.error(e);
            }
        }

        function renderCard(lead) {
            return `
                <div class="lead-card bg-white dark:bg-[#1e293b] p-4 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 hover:border-[var(--theme-color)] dark:hover:border-[var(--theme-color)] group relative" data-id="${lead.id}" onclick="editLead(${lead.id})">
                    <div class="flex justify-between items-start mb-2">
                         <span class="text-xs font-bold text-[var(--theme-color)] bg-green-500/10 px-2 py-0.5 rounded uppercase tracking-wider">${lead.origem || 'N/A'}</span>
                         <button class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-white transition" onclick="event.stopPropagation(); editLead(${lead.id})">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                         </button>
                    </div>
                    <h4 class="font-bold text-slate-800 dark:text-white mb-1 leading-tight">${lead.nome || 'Sem Nome'}</h4>
                    <div class="text-sm font-mono text-slate-500 dark:text-slate-400 mb-3">R$ ${parseFloat(lead.valor_estimado || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</div>
                    
                    <div class="flex items-center justify-between text-xs text-slate-400 dark:text-slate-500 pt-3 border-t border-gray-100 dark:border-slate-700/50">
                        <div class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            ${new Date(lead.created_at).toLocaleDateString('pt-BR')}
                        </div>
                        <div class="flex items-center gap-1">
                             <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                             ${lead.responsavel || 'Eu'}
                        </div>
                    </div>
                </div>
            `;
        }

        function initDragAndDrop() {
            document.querySelectorAll('.kanban-col').forEach(col => {
                new Sortable(col, {
                    group: 'kanban',
                    animation: 150,
                    ghostClass: 'ghost-card',
                    onEnd: function (evt) {
                        const itemEl = evt.item;
                        const newStageId = evt.to.dataset.stage;
                        const leadId = itemEl.dataset.id;
                        
                        updateLeadStage(leadId, newStageId);
                    }
                });
            });
        }

        async function updateLeadStage(leadId, stageId) {
            try {
                await fetch('api/kanban.php', {
                    method: 'POST',
                    body: JSON.stringify({ action: 'move_lead', lead_id: leadId, stage_id: stageId })
                });
                // Optional: Update UI stats for column counts if strict accuracy needed without reload
            } catch(e) { console.error(e); }
        }

        // Drawer Logic
        const drawerOverlay = document.getElementById('leadDrawerOverlay');
        const drawer = document.getElementById('leadDrawer');
        const btnConvert = document.getElementById('btnConvertLead');
        let currentLeadId = null;
        let whatsappTemplate = "Olá {nome}, tudo bem? Vi seu interesse e gostaria de conversar."; // Default

        // Init
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                const res = await fetch('api/settings.php');
                const data = await res.json();
                if(data.success && data.data.whatsapp_default_msg) {
                    whatsappTemplate = data.data.whatsapp_default_msg;
                }
            } catch(e) { console.error("Error loading settings", e); }
        });

        function switchTab(tabId) {
            // Hide all tabs
            ['details', 'notes', 'history'].forEach(t => {
                document.getElementById('tab-' + t).classList.add('hidden');
                document.getElementById('tab-btn-' + t).classList.remove('text-[var(--theme-color)]', 'border-[var(--theme-color)]');
                document.getElementById('tab-btn-' + t).classList.add('text-slate-500', 'border-transparent');
            });

            // Show selected
            document.getElementById('tab-' + tabId).classList.remove('hidden');
            const btn = document.getElementById('tab-btn-' + tabId);
            btn.classList.remove('text-slate-500', 'border-transparent');
            btn.classList.add('text-[var(--theme-color)]', 'border-[var(--theme-color)]');
        }

        function openWhatsApp() {
            const phone = document.getElementById('lead_contato').value.replace(/\D/g, '');
            const name = document.getElementById('lead_nome').value;
            
            if(!phone) {
                alert("Este lead não possui telefone cadastrado.");
                return;
            }

            let msg = whatsappTemplate.replace('{nome}', name || 'Cliente');
            const url = `https://wa.me/${phone}?text=${encodeURIComponent(msg)}`;
            window.open(url, '_blank');
        }

        async function fetchNotes(leadId) {
            const list = document.getElementById('notesList');
            list.innerHTML = '<p class="text-sm text-slate-400 pl-6 italic">Carregando...</p>';
            
            try {
                const res = await fetch(`api/notes.php?lead_id=${leadId}`);
                const notes = await res.json();
                
                if(Array.isArray(notes) && notes.length > 0) {
                    list.innerHTML = notes.map(note => `
                        <div class="relative pl-6 pb-4 group">
                            <span class="absolute left-0 top-1 w-4 h-4 bg-slate-200 dark:bg-slate-600 rounded-full border-2 border-white dark:border-slate-800"></span>
                            <div class="bg-white dark:bg-slate-800 p-3 rounded-lg border border-gray-100 dark:border-slate-700 shadow-sm">
                                <p class="text-sm text-slate-800 dark:text-slate-200 whitespace-pre-wrap">${note.note}</p>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-xs font-bold text-slate-500">${note.usuario_nome || 'Sistema'}</span>
                                    <span class="text-[10px] text-slate-400">${new Date(note.created_at).toLocaleString('pt-BR')}</span>
                                </div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    list.innerHTML = '<p class="text-sm text-slate-400 pl-6">Nenhuma anotação encontrada.</p>';
                }
            } catch(e) {
                console.error(e);
                list.innerHTML = '<p class="text-sm text-red-400 pl-6">Erro ao carregar notas.</p>';
            }
        }

        async function saveNote() {
            const input = document.getElementById('newNoteInput');
            const btn = document.getElementById('btnSaveNote');
            const content = input.value.trim();
            
            if(!content || !currentLeadId) return;

            const originalText = btn.innerText;
            btn.innerText = 'Salvando...';
            btn.disabled = true;

            try {
                const res = await fetch('api/notes.php', {
                    method: 'POST',
                    body: JSON.stringify({ lead_id: currentLeadId, note: content })
                });
                const data = await res.json();
                
                if(data.success) {
                    input.value = '';
                    fetchNotes(currentLeadId);
                } else {
                    alert('Erro ao salvar nota');
                }
            } catch(e) {
                console.error(e);
                alert('Erro de conexão');
            } finally {
                btn.innerText = originalText;
                btn.disabled = false;
            }
        }
        
        function openLeadModal(lead = null) {
            drawerOverlay.classList.remove('hidden');
            drawer.classList.remove('hidden'); 
            
            requestAnimationFrame(() => {
                drawerOverlay.classList.remove('opacity-0');
                drawer.classList.remove('translate-x-full');
            });
            
            // Default to Details tab
            switchTab('details');

            if(lead) {
                currentLeadId = lead.id; // Set global
                document.getElementById('modalTitle').innerText = 'Detalhes do Lead';
                document.getElementById('modalSubtitle').innerText = 'Gerencie as informações e converta este lead.';
                document.getElementById('lead_id').value = lead.id;
                document.getElementById('lead_nome').value = lead.nome;
                document.getElementById('lead_valor').value = lead.valor || lead.valor_estimado;
                document.getElementById('lead_contato').value = lead.contato || lead.telefone || '';
                document.getElementById('lead_origem').value = lead.origem;
                document.getElementById('lead_etapa_id').value = lead.etapa_id || lead.status_id;
                
                // Fetch Notes
                fetchNotes(lead.id);

                // Show/Enable Convert Button
                const btnDelete = document.getElementById('btnDeleteLead');
                if(btnConvert) {
                    btnConvert.style.display = 'flex';
                    btnConvert.onclick = () => convertLeadCurrent(lead.id);
                }
                if(btnDelete) btnDelete.style.display = 'flex';
                
                // Render Meta Answers if available
                const metaContainer = document.getElementById('leadMetaAnswers');
                const metaList = document.getElementById('metaAnswersList');
                
                if (lead.facebook_data) {
                    try {
                        const fbData = typeof lead.facebook_data === 'string' ? JSON.parse(lead.facebook_data) : lead.facebook_data;
                        let answersHTML = '';
                        
                        if (fbData.field_data && Array.isArray(fbData.field_data)) {
                            answersHTML = fbData.field_data.map(field => `
                                <div class="bg-gray-50 dark:bg-slate-800 p-3 rounded-lg border border-gray-100 dark:border-slate-700">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">${field.name}</p>
                                    <p class="text-sm text-slate-800 dark:text-slate-200 font-medium">${field.values[0]}</p>
                                </div>
                            `).join('');
                        } else if (fbData.lead_data) {
                             answersHTML = Object.entries(fbData.lead_data).map(([key, value]) => `
                                <div class="bg-gray-50 dark:bg-slate-800 p-3 rounded-lg border border-gray-100 dark:border-slate-700">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">${key.replace(/_/g, ' ')}</p>
                                    <p class="text-sm text-slate-800 dark:text-slate-200 font-medium">${value}</p>
                                </div>
                            `).join('');
                        }

                        if(answersHTML) {
                            metaList.innerHTML = answersHTML;
                            metaContainer.classList.remove('hidden');
                        } else {
                            metaContainer.classList.add('hidden');
                        }
                    } catch(e) {
                        console.error("Error parsing FB Data", e);
                        metaContainer.classList.add('hidden');
                    }
                } else {
                    metaContainer.classList.add('hidden');
                }

            } else {
                currentLeadId = null;
                document.getElementById('modalTitle').innerText = 'Novo Lead';
                document.getElementById('modalSubtitle').innerText = 'Cadastre um novo lead manualmente.';
                document.getElementById('leadForm').reset();
                const btnDelete = document.getElementById('btnDeleteLead');
                document.getElementById('lead_id').value = '';
                if(btnConvert) btnConvert.style.display = 'none';
                if(btnDelete) btnDelete.style.display = 'none';
                document.getElementById('leadMetaAnswers').classList.add('hidden');
                document.getElementById('notesList').innerHTML = ''; // Clear notes
            }
        }

        function closeLeadModal() {
            drawerOverlay.classList.add('opacity-0');
            drawer.classList.add('translate-x-full');
            
            setTimeout(() => {
                drawerOverlay.classList.add('hidden');
            }, 300);
        }
        
        async function convertLeadCurrent(id) {
            if(!confirm("Deseja realmente converter este Lead em Cliente?")) return;
            
            const btn = document.getElementById('btnConvertLead');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Convertendo...';
            btn.disabled = true;

            try {
                const res = await fetch('api/kanban.php', {
                    method: 'POST',
                    body: JSON.stringify({ action: 'convert_lead', lead_id: id })
                });
                const data = await res.json();
                
                if(data.success) {
                    alert(data.message);
                    closeLeadModal();
                    loadKanban();
                } else {
                    alert("Erro: " + (data.error || "Falha desconhecida"));
                }
            } catch(e) {
                console.error(e);
                alert("Erro de conexão ao converter lead.");
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        function editLead(id) {
             const lead = typeof id === 'object' ? id : leadsMap[id];
             if(lead) openLeadModal(lead);
        }

        async function deleteLeadCurrent() {
            const leadId = document.getElementById('lead_id').value;
            if(!leadId) return;
            
            if(!confirm("Tem certeza que deseja processar a exclusão permanente deste lead e todo seu histórico? Essa ação não pode ser desfeita.")) return;
            
            const btn = document.getElementById('btnDeleteLead');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Excluindo...';
            btn.disabled = true;

            try {
                const res = await fetch('api/kanban.php', {
                    method: 'DELETE',
                    body: JSON.stringify({ id: leadId })
                });
                const data = await res.json();
                
                if(data.success) {
                    closeLeadModal();
                    loadKanban();
                } else {
                    alert(data.error || "Erro ao excluir.");
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            } catch(e) {
                console.error(e);
                alert("Erro ao excluir lead.");
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        document.getElementById('leadForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            data.action = 'save_lead'; // simplified

            try {
                await fetch('api/kanban.php', {
                    method: 'POST',
                    body: JSON.stringify(data)
                });
                closeLeadModal();
                loadKanban();
            } catch(e) { console.error(e); }
        });

        // --- Settings / Stages Customization ---
        const settingsModal = document.getElementById('settingsModal');
        const settingsContent = document.getElementById('settingsModalContent');
        const stagesListEl = document.getElementById('stagesList');
        let stagesDataCache = [];

        function openSettingsModal() {
            settingsModal.classList.remove('hidden');
            setTimeout(() => {
                settingsContent.classList.remove('opacity-0', 'scale-95');
                settingsContent.classList.add('opacity-100', 'scale-100');
            }, 10);
            renderSettingsList();
        }

        function closeSettingsModal() {
            settingsContent.classList.remove('opacity-100', 'scale-100');
            settingsContent.classList.add('opacity-0', 'scale-95');
            setTimeout(() => settingsModal.classList.add('hidden'), 300);
            loadKanban(); // Refresh main board on close
        }

        async function renderSettingsList() {
            // Fetch fresh data just for list
            try {
                const res = await fetch('api/kanban.php');
                const data = await res.json();
                stagesDataCache = data.stages || [];
                
                stagesListEl.innerHTML = stagesDataCache.map(stage => `
                    <div class="stage-item bg-white dark:bg-[#1e293b] p-4 rounded-lg border border-gray-200 dark:border-slate-700 flex items-center justify-between group cursor-move shadow-sm" data-id="${stage.id}">
                        <div class="flex items-center gap-4 flex-1">
                            <div class="text-slate-400 cursor-grab active:cursor-grabbing">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                            </div>
                            
                            <input type="color" value="${stage.color || '#cbd5e1'}" 
                                onchange="updateStageColor(${stage.id}, this.value)"
                                class="w-8 h-8 rounded cursor-pointer border-0 p-0 bg-transparent" title="Mudar Cor">
                                
                            <input type="text" value="${stage.name}" 
                                onchange="renameStage(${stage.id}, this.value)"
                                class="bg-transparent text-slate-700 dark:text-white font-bold text-sm border-b border-transparent hover:border-gray-300 focus:border-[var(--theme-color)] outline-none transition px-1 py-0.5 w-full">
                        </div>
                        
                        <div class="ml-4">
                            <button onclick="deleteStage(${stage.id})" class="text-slate-300 hover:text-red-500 transition p-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                `).join('');

                initSettingsSortable();

            } catch(e) { console.error(e); }
        }

        function initSettingsSortable() {
            new Sortable(stagesListEl, {
                animation: 150,
                handle: '.cursor-move',
                ghostClass: 'ghost-card',
                onEnd: async function () {
                    // Update order
                    const newOrder = Array.from(stagesListEl.children).map((el, index) => ({
                        id: el.dataset.id,
                        ordem: index
                    }));
                    
                    try {
                        await fetch('api/kanban.php', {
                            method: 'POST',
                            body: JSON.stringify({ action: 'update_stage_order', order: newOrder })
                        });
                    } catch(e) { console.error(e); }
                }
            });
        }

        async function updateStageColor(id, color) {
            try {
                await fetch('api/kanban.php', {
                    method: 'POST',
                    body: JSON.stringify({ action: 'update_stage_color', id, cor: color })
                });
            } catch(e) { console.error(e); }
        }

        async function renameStage(id, name) {
            try {
                await fetch('api/kanban.php', {
                    method: 'POST',
                    body: JSON.stringify({ action: 'rename_stage', stage_id: id, nome: name })
                });
            } catch(e) { console.error(e); }
        }

        async function addNewStage() {
            const name = prompt("Nome da nova etapa:");
            if(!name) return;

            try {
                await fetch('api/kanban.php', {
                    method: 'POST',
                    body: JSON.stringify({ action: 'add_stage', nome: name, cor: '#94a3b8' })
                });
                renderSettingsList();
            } catch(e) { console.error(e); }
        }

        async function deleteStage(id) {
            if(!confirm("Tem certeza? Esta ação não pode ser desfeita.")) return;
            
            try {
                const res = await fetch('api/kanban.php', {
                    method: 'POST',
                    body: JSON.stringify({ action: 'delete_stage', id })
                });
                const data = await res.json();
                
                if(data.error) {
                    alert(data.error);
                } else {
                    renderSettingsList();
                }
            } catch(e) { console.error(e); alert("Erro ao excluir etapa."); }
        }

        // Init
        loadKanban();

    </script>
    
    <!-- Settings Drawer -->
    <?php include 'components/settings_drawer.php'; ?>
    <script src="js/settings.js?v=<?= time() ?>"></script>
</body>
</html>
