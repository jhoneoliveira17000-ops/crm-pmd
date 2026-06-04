<?php
// PMDCRM/crm_kanban.php
require_once 'src/auth.php';
require_login();
$page_title = "CRM Kanban - PMDCRM";
$body_class = "overflow-x-hidden bg-[var(--surface-1)] text-[var(--text-1)] transition-colors duration-300 md:pl-64";
include 'includes/header.php';
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<style>
    .kanban-col { min-height: calc(100vh - 200px); }
    .ghost-card { opacity: 0.5; background: var(--surface-3); border: 2px dashed var(--border-3); border-radius: var(--radius-lg); }
    .lead-card { transition: transform 0.2s, box-shadow 0.2s; cursor: grab; }
    .lead-card:active { cursor: grabbing; transform: scale(1.02); }
</style>

    <?php include 'nav.php'; ?>

    <main class="p-4 md:p-6 max-w-[1920px] mx-auto transition-colors duration-300">
        <!-- Page Header -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 border-b border-[var(--border-1)] pb-4">
            <div>
                <h1 class="text-2xl font-bold text-[var(--text-1)] tracking-tight flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full animate-pulse shadow-[0_0_8px_rgba(var(--theme-color-rgb),0.6)]" style="background:var(--brand)"></span>
                    Pipeline de Vendas
                </h1>
                <p class="text-[var(--text-3)] text-[10px] mt-1 uppercase font-bold tracking-widest flex items-center gap-2">
                    Funil de Atendimento Comercial
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                 <?php include 'header_icons.php'; ?>
                 <button onclick="openSettingsModal()" class="ds-btn ds-btn-secondary btn-spring">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    Personalizar Funil
                </button>
                 <button onclick="openLeadModal()" class="ds-btn ds-btn-primary btn-spring">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Novo Lead
                </button>
            </div>
        </header>

        <!-- Kanban Board -->
        <div class="flex gap-6 pb-4 overflow-x-auto custom-scrollbar min-w-full" id="kanbanBoard">
             <div class="w-80 flex-shrink-0 flex flex-col items-center justify-center text-[var(--text-3)] h-96">
                <svg class="animate-spin h-10 w-10 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <p class="font-mono text-xs uppercase tracking-wider">Carregando Funil...</p>
            </div>
        </div>
    </main>

    <!-- Lead Drawer (Slide-Over) -->
    <div id="leadDrawerOverlay" class="ds-overlay" onclick="closeLeadModal()"></div>
    <div id="leadDrawer" class="ds-drawer flex flex-col">
        
        <!-- Header with Tabs -->
        <div class="bg-[var(--surface-2)] border-b border-[var(--border-1)]">
            <div class="p-6 pb-0 flex justify-between items-start">
                <div>
                    <h3 class="font-bold text-xl text-[var(--text-1)]" id="modalTitle">Detalhes do Lead</h3>
                    <p class="text-xs text-[var(--text-3)] mt-1" id="modalSubtitle">Gerencie as informações deste lead.</p>
                </div>
                <button onclick="closeLeadModal()" class="text-[var(--text-3)] hover:text-[var(--text-1)] p-2 rounded-full hover:bg-[var(--surface-3)] transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <!-- Tabs -->
            <div class="flex items-center gap-6 px-6 mt-6">
                <button onclick="switchTab('details')" id="tab-btn-details" class="pb-3 text-sm font-bold text-[var(--brand)] border-b-2 border-[var(--brand)] transition-colors">Detalhes</button>
                <button onclick="switchTab('notes')" id="tab-btn-notes" class="pb-3 text-sm font-medium text-[var(--text-3)] hover:text-[var(--text-1)] border-b-2 border-transparent transition-colors">Anotações</button>
                <button onclick="switchTab('history')" id="tab-btn-history" class="pb-3 text-sm font-medium text-[var(--text-3)] hover:text-[var(--text-1)] border-b-2 border-transparent transition-colors">Histórico</button>
            </div>
        </div>

        <!-- Body (Scrollable) -->
        <div class="flex-1 overflow-y-auto p-6 custom-scrollbar bg-[var(--surface-0)]">
            
            <!-- TAB: DETAILS -->
            <div id="tab-details" class="space-y-6">
                <form id="leadForm" class="space-y-6">
                    <input type="hidden" name="id" id="lead_id">
                    
                    <!-- Status/Convert Actions -->
                    <div class="bg-[var(--surface-1)] p-4 rounded-[var(--radius-lg)] border border-[var(--border-1)] flex flex-col gap-3">
                        <div class="flex items-center gap-3">
                             <div class="bg-[var(--brand-subtle)] text-[var(--brand)] p-2 rounded-lg border border-[var(--brand-muted)]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                             </div>
                             <div>
                                 <h4 class="font-bold text-[var(--text-1)] text-sm">Ações Rápidas</h4>
                                 <p class="text-xs text-[var(--text-3)]">Entre em contato ou mova o lead.</p>
                             </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" onclick="openWhatsApp()" class="ds-btn bg-green-500 hover:bg-green-600 text-white font-bold py-2.5 rounded-[var(--radius-md)] btn-spring flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                WhatsApp
                            </button>
                            <button type="button" onclick="convertLeadCurrent()" id="btnConvertLead" class="ds-btn ds-btn-primary btn-spring">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Tornar Cliente
                            </button>
                        </div>
                    </div>

                <!-- Basic Info -->
                <div class="space-y-4">
                    <h4 class="text-xs font-bold text-[var(--text-3)] uppercase tracking-wider mb-2 font-mono">Informações Básicas</h4>
                    
                    <div>
                        <label class="ds-label">Nome Completo</label>
                        <input type="text" name="nome" id="lead_nome" required class="ds-input">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="ds-label">Valor (R$)</label>
                             <input type="number" step="0.01" name="valor" id="lead_valor" class="ds-input">
                        </div>
                        <div>
                            <label class="ds-label">WhatsApp / Tel</label>
                             <input type="text" name="contato" id="lead_contato" class="ds-input">
                        </div>
                    </div>
                </div>
                
                <!-- Dynamic Answers (Meta Ads) -->
                <div id="leadMetaAnswers" class="hidden space-y-3 pt-4 border-t border-[var(--border-1)]">
                     <h4 class="text-xs font-bold text-blue-500 uppercase tracking-wider mb-2 flex items-center gap-2 font-mono">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.791-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        Respostas do Formulário
                     </h4>
                     <div id="metaAnswersList" class="space-y-3"></div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="ds-label">Origem</label>
                        <select name="origem" id="lead_origem" class="ds-input appearance-none">
                            <option value="Indicação">Indicação</option>
                            <option value="Instagram">Instagram</option>
                            <option value="Google Ads">Google Ads</option>
                            <option value="Linkedin">Linkedin</option>
                            <option value="Outros">Outros</option>
                        </select>
                    </div>
                    
                     <div>
                        <label class="ds-label">Etapa do Funil</label>
                        <select name="etapa_id" id="lead_etapa_id" class="ds-input appearance-none">
                            <!-- Loaded via JS -->
                        </select>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="pt-6 border-t border-[var(--border-1)] flex justify-between items-center sticky bottom-0 bg-[var(--surface-0)] py-4">
                    <button type="button" id="btnDeleteLead" onclick="deleteLeadCurrent()" class="ds-btn ds-btn-ghost text-red-500 hover:text-red-700 font-medium px-4 py-2 hover:bg-red-50 dark:hover:bg-red-950/20 rounded-lg transition flex items-center gap-2 whitespace-nowrap" style="display: none;">
                        <svg class="w-4 h-4" flex-shrink-0 fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Excluir Lead
                    </button>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="closeLeadModal()" class="ds-btn ds-btn-secondary btn-spring">Cancelar</button>
                        <button type="submit" class="ds-btn ds-btn-primary btn-spring">Salvar Alterações</button>
                    </div>
                </div>
            </form>
            </div> <!-- End Tab Details -->

            <!-- TAB: NOTES -->
            <div id="tab-notes" class="hidden space-y-6">
                <!-- Add Note -->
                <div class="bg-[var(--surface-2)] p-4 rounded-[var(--radius-lg)] border border-[var(--border-1)]">
                    <label class="block text-xs font-bold text-[var(--text-3)] mb-2 uppercase font-mono">Nova Anotação</label>
                    <textarea id="newNoteInput" rows="3" class="w-full bg-[var(--surface-0)] border border-[var(--border-2)] rounded-lg p-3 text-sm text-[var(--text-1)] focus:border-[var(--brand)] focus:ring-1 focus:ring-[var(--brand)] outline-none transition resize-none placeholder:text-[var(--text-4)]" placeholder="Digite uma observação importante..."></textarea>
                    <div class="flex justify-end mt-2">
                        <button type="button" onclick="saveNote()" id="btnSaveNote" class="ds-btn ds-btn-primary ds-btn-sm btn-spring">
                            Salvar Nota
                        </button>
                    </div>
                </div>

                <!-- Notes List -->
                <div class="space-y-4">
                     <h4 class="text-xs font-bold text-[var(--text-3)] uppercase tracking-wider font-mono">Histórico de Anotações</h4>
                     <div id="notesList" class="space-y-4 relative before:absolute before:inset-y-0 before:left-2 before:w-0.5 before:bg-[var(--border-1)]">
                         <!-- Loaded via JS -->
                         <p class="text-sm text-[var(--text-3)] pl-6 italic">Carregando anotações...</p>
                     </div>
                </div>
            </div>

            <!-- TAB: HISTORY -->
            <div id="tab-history" class="hidden space-y-4">
                 <div class="p-4 bg-amber-500/10 text-amber-500 rounded-lg text-sm flex items-center gap-2 border border-amber-500/20 font-semibold">
                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                     <span>Histórico de alterações em breve.</span>
                 </div>
            </div>
        </div>
    </div> <!-- END leadDrawer -->

    <!-- Settings Modal (Funnel Customization) -->
    <div id="settingsModal" class="ds-overlay flex items-center justify-center p-4">
        <div class="ds-modal bg-[var(--surface-0)] rounded-[var(--radius-xl)] shadow-2xl w-full max-w-2xl border border-[var(--border-1)] h-[80vh] flex flex-col" id="settingsModalContent">
            <div class="p-6 border-b border-[var(--border-1)] flex justify-between items-center bg-[var(--surface-2)]">
                <div>
                    <h3 class="font-bold text-xl text-[var(--text-1)]">Personalizar Funil</h3>
                    <p class="text-xs text-[var(--text-3)] mt-1">Arraste para reordenar, clique para editar.</p>
                </div>
                <button onclick="closeSettingsModal()" class="text-[var(--text-3)] hover:text-[var(--text-1)] font-bold">✕</button>
            </div>
            
            <div class="flex-1 overflow-y-auto p-6 custom-scrollbar bg-[var(--surface-0)]">
                <div id="stagesList" class="space-y-3">
                    <!-- Populated by JS -->
                </div>
                
                <button onclick="addNewStage()" class="btn-spring mt-4 w-full py-3 border-2 border-dashed border-[var(--border-2)] hover:border-[var(--brand)] hover:text-[var(--brand)] rounded-[var(--radius-lg)] text-[var(--text-3)] transition flex items-center justify-center gap-2 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Adicionar Nova Etapa
                </button>
            </div>
            
            <div class="p-6 border-t border-[var(--border-1)] bg-[var(--surface-2)] flex justify-end">
                <button onclick="closeSettingsModal()" class="ds-btn ds-btn-secondary btn-spring">Concluído</button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
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

                    const columnTotal = stage.leads ? stage.leads.reduce((sum, lead) => sum + parseFloat(lead.valor_estimado || 0), 0) : 0;
                    const formattedTotal = columnTotal.toLocaleString('pt-BR', {minimumFractionDigits: 2});

                    // Create Column
                    const colDiv = document.createElement('div');
                    colDiv.className = 'w-80 flex-shrink-0 flex flex-col bg-[var(--surface-2)] rounded-[var(--radius-lg)] border border-[var(--border-1)] h-full max-h-[calc(100vh-140px)] animate-spring';
                    colDiv.innerHTML = `
                        <!-- Column Header -->
                        <div class="p-4 border-b border-[var(--border-1)] flex flex-col gap-1 bg-[var(--surface-0)] rounded-t-[var(--radius-lg)] z-10">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full" style="background-color: ${stage.color}"></span>
                                    <h3 class="font-bold text-[var(--text-1)] text-xs uppercase tracking-wider">${stage.name}</h3>
                                </div>
                                <span class="ds-badge ds-badge-neutral">${stage.leads ? stage.leads.length : 0}</span>
                            </div>
                            <div class="text-[10px] font-mono text-[var(--text-3)] font-bold">R$ ${formattedTotal}</div>
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
            const formattedVal = parseFloat(lead.valor_estimado || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2});
            const formattedDate = new Date(lead.created_at).toLocaleDateString('pt-BR');
            return `
                <div class="lead-card ds-card p-4 hover:border-[var(--brand)] group relative cursor-pointer" data-id="${lead.id}" onclick="editLead(${lead.id})">
                    <div class="flex justify-between items-start mb-2">
                         <span class="ds-badge ds-badge-brand">${lead.origem || 'N/A'}</span>
                         <button class="opacity-0 group-hover:opacity-100 text-[var(--text-3)] hover:text-[var(--text-1)] transition" onclick="event.stopPropagation(); editLead(${lead.id})">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                         </button>
                    </div>
                    <h4 class="font-bold text-[var(--text-1)] mb-1 leading-tight">${lead.nome || 'Sem Nome'}</h4>
                    <div class="text-sm font-mono text-[var(--text-3)] font-semibold mb-3">R$ ${formattedVal}</div>
                    
                    <div class="flex items-center justify-between text-xs text-[var(--text-3)] pt-3 border-t border-[var(--border-1)]">
                        <div class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            ${formattedDate}
                        </div>
                        <div class="flex items-center gap-1 font-semibold">
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
                loadKanban(); // Refresh to recalculate count and sums accurately
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
            ['details', 'notes', 'history'].forEach(t => {
                document.getElementById('tab-' + t).classList.add('hidden');
                document.getElementById('tab-btn-' + t).classList.remove('text-[var(--brand)]', 'border-[var(--brand)]');
                document.getElementById('tab-btn-' + t).classList.add('text-[var(--text-3)]', 'border-transparent');
            });

            document.getElementById('tab-' + tabId).classList.remove('hidden');
            const btn = document.getElementById('tab-btn-' + tabId);
            btn.classList.remove('text-[var(--text-3)]', 'border-transparent');
            btn.classList.add('text-[var(--brand)]', 'border-[var(--brand)]');
        }

        function openWhatsApp() {
            const phone = document.getElementById('lead_contato').value.replace(/\D/g, '');
            const name = document.getElementById('lead_nome').value;
            
            if(!phone) {
                showToast("Este lead não possui telefone cadastrado.", "error");
                return;
            }

            let msg = whatsappTemplate.replace('{nome}', name || 'Cliente');
            const url = `https://wa.me/${phone}?text=${encodeURIComponent(msg)}`;
            window.open(url, '_blank');
        }

        async function fetchNotes(leadId) {
            const list = document.getElementById('notesList');
            list.innerHTML = '<p class="text-sm text-[var(--text-3)] pl-6 italic">Carregando...</p>';
            
            try {
                const res = await fetch(`api/notes.php?lead_id=${leadId}`);
                const notes = await res.json();
                
                if(Array.isArray(notes) && notes.length > 0) {
                    list.innerHTML = notes.map(note => `
                        <div class="relative pl-6 pb-4 group">
                            <span class="absolute left-0 top-1 w-4 h-4 bg-[var(--surface-2)] rounded-full border-2 border-[var(--surface-0)]"></span>
                            <div class="bg-[var(--surface-0)] p-3 rounded-lg border border-[var(--border-1)] shadow-sm">
                                <p class="text-sm text-[var(--text-1)] whitespace-pre-wrap">${note.note}</p>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-xs font-bold text-[var(--text-3)]">${note.usuario_nome || 'Sistema'}</span>
                                    <span class="text-[10px] text-[var(--text-4)] font-mono">${new Date(note.created_at).toLocaleString('pt-BR')}</span>
                                </div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    list.innerHTML = '<p class="text-sm text-[var(--text-3)] pl-6">Nenhuma anotação encontrada.</p>';
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
                    showToast('Erro ao salvar nota', 'error');
                }
            } catch(e) {
                console.error(e);
                showToast('Erro de conexão', 'error');
            } finally {
                btn.innerText = originalText;
                btn.disabled = false;
            }
        }
        
        function openLeadModal(lead = null) {
            drawerOverlay.classList.add('active');
            drawer.classList.add('active'); 
            
            switchTab('details');

            if(lead) {
                currentLeadId = Math.floor(lead.id); 
                document.getElementById('modalTitle').innerText = 'Detalhes do Lead';
                document.getElementById('modalSubtitle').innerText = 'Gerencie as informações e converta este lead.';
                document.getElementById('lead_id').value = lead.id;
                document.getElementById('lead_nome').value = lead.nome;
                document.getElementById('lead_valor').value = lead.valor || lead.valor_estimado;
                document.getElementById('lead_contato').value = lead.contato || lead.telefone || '';
                document.getElementById('lead_origem').value = lead.origem;
                document.getElementById('lead_etapa_id').value = lead.etapa_id || lead.status_id;
                
                fetchNotes(lead.id);

                const btnDelete = document.getElementById('btnDeleteLead');
                if(btnConvert) {
                    btnConvert.style.display = 'flex';
                    btnConvert.onclick = () => convertLeadCurrent(lead.id);
                }
                if(btnDelete) btnDelete.style.display = 'flex';
                
                const metaContainer = document.getElementById('leadMetaAnswers');
                const metaList = document.getElementById('metaAnswersList');
                
                if (lead.facebook_data) {
                    try {
                        const fbData = typeof lead.facebook_data === 'string' ? JSON.parse(lead.facebook_data) : lead.facebook_data;
                        let answersHTML = '';
                        
                        if (fbData.field_data && Array.isArray(fbData.field_data)) {
                            answersHTML = fbData.field_data.map(field => `
                                <div class="bg-[var(--surface-2)] p-3 rounded-lg border border-[var(--border-1)]">
                                    <p class="text-[10px] font-bold text-[var(--text-3)] uppercase mb-1 font-mono">${field.name}</p>
                                    <p class="text-sm text-[var(--text-1)] font-medium">${field.values[0]}</p>
                                </div>
                            `).join('');
                        } else if (fbData.lead_data) {
                             answersHTML = Object.entries(fbData.lead_data).map(([key, value]) => `
                                <div class="bg-[var(--surface-2)] p-3 rounded-lg border border-[var(--border-1)]">
                                    <p class="text-[10px] font-bold text-[var(--text-3)] uppercase mb-1 font-mono">${key.replace(/_/g, ' ')}</p>
                                    <p class="text-sm text-[var(--text-1)] font-medium">${value}</p>
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
                document.getElementById('notesList').innerHTML = ''; 
            }
        }

        function closeLeadModal() {
            drawerOverlay.classList.remove('active');
            drawer.classList.remove('active');
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
                    showToast(data.message);
                    closeLeadModal();
                    loadKanban();
                } else {
                    showToast("Erro: " + (data.error || "Falha desconhecida"), "error");
                }
            } catch(e) {
                console.error(e);
                showToast("Erro de conexão ao converter lead.", "error");
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
            
            if(!confirm("Tem certeza que deseja excluir permanentemente este lead?")) return;
            
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
                    showToast("Lead removido com sucesso.");
                    closeLeadModal();
                    loadKanban();
                } else {
                    showToast(data.error || "Erro ao excluir.", "error");
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            } catch(e) {
                console.error(e);
                showToast("Erro ao excluir lead.", "error");
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        document.getElementById('leadForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            data.action = 'save_lead'; 

            try {
                await fetch('api/kanban.php', {
                    method: 'POST',
                    body: JSON.stringify(data)
                });
                showToast("Lead salvo com sucesso.");
                closeLeadModal();
                loadKanban();
            } catch(e) { 
                console.error(e); 
                showToast("Erro ao salvar lead.", "error");
            }
        });

        // --- Settings / Stages Customization ---
        const settingsModal = document.getElementById('settingsModal');
        const settingsContent = document.getElementById('settingsModalContent');
        const stagesListEl = document.getElementById('stagesList');
        let stagesDataCache = [];

        function openSettingsModal() {
            settingsModal.classList.add('active');
            settingsContent.classList.add('active');
            renderSettingsList();
        }

        function closeSettingsModal() {
            settingsModal.classList.remove('active');
            settingsContent.classList.remove('active');
            loadKanban(); 
        }

        async function renderSettingsList() {
            try {
                const res = await fetch('api/kanban.php');
                const data = await res.json();
                stagesDataCache = data.stages || [];
                
                stagesListEl.innerHTML = stagesDataCache.map(stage => `
                    <div class="stage-item bg-[var(--surface-0)] p-4 rounded-[var(--radius-lg)] border border-[var(--border-1)] flex items-center justify-between group cursor-move shadow-sm" data-id="${stage.id}">
                        <div class="flex items-center gap-4 flex-1">
                            <div class="text-[var(--text-4)] cursor-grab active:cursor-grabbing">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                            </div>
                            
                            <input type="color" value="${stage.color || '#cbd5e1'}" 
                                onchange="updateStageColor(${stage.id}, this.value)"
                                class="w-8 h-8 rounded cursor-pointer border-0 p-0 bg-transparent" title="Mudar Cor">
                                
                            <input type="text" value="${stage.name}" 
                                onchange="renameStage(${stage.id}, this.value)"
                                class="bg-transparent text-[var(--text-1)] font-bold text-sm border-b border-transparent hover:border-[var(--border-2)] focus:border-[var(--brand)] outline-none transition px-1 py-0.5 w-full">
                        </div>
                        
                        <div class="ml-4">
                            <button onclick="deleteStage(${stage.id})" class="text-[var(--text-3)] hover:text-red-500 transition p-2">
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
                    showToast(data.error, "error");
                } else {
                    renderSettingsList();
                }
            } catch(e) { 
                console.error(e); 
                showToast("Erro ao excluir etapa.", "error"); 
            }
        }

        // Init
        loadKanban();

    </script>
    
    <!-- Settings Drawer -->
    <?php include 'components/settings_drawer.php'; ?>
    <script src="js/settings.js?v=<?= time() ?>"></script>
<?php include 'includes/footer.php'; ?>
