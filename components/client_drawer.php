<!-- Client BI & Details Drawer Overlay -->
<div id="clientDrawerOverlay" class="fixed inset-0 bg-slate-900/50 z-40 hidden transition-opacity opacity-0" onclick="closeClientDrawer()"></div>
<!-- Client BI & Details Drawer -->
<div id="clientDrawer" class="fixed inset-y-0 right-0 w-full md:w-[40vw] bg-white dark:bg-[#0f172a] shadow-2xl transform translate-x-full transition-transform duration-300 z-50 flex flex-col border-l border-gray-200 dark:border-slate-800 font-sans">
    
    <!-- Top Bar: Header -->
    <div class="px-6 py-5 border-b border-gray-200 dark:border-slate-800 flex justify-between items-start bg-gray-50/50 dark:bg-[#0f172a]">
        <div class="flex-1 pr-4">
            <div class="flex items-center gap-3 mb-1">
                <h2 id="drawerClientName" class="text-2xl font-black text-slate-900 dark:text-white tracking-tight truncate">Carregando...</h2>
                <span id="drawerClientStatus" class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest bg-gray-200 text-gray-800 rounded-none border border-current">...</span>
            </div>
            <p id="drawerClientInfo" class="text-xs text-slate-500 dark:text-slate-400 font-medium">CNPJ: ...</p>
        </div>
        <div class="flex items-center gap-3">
            <button id="btnAgendaClient" onclick="openAgendaContext()" class="flex items-center justify-center p-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-none transition-colors shadow-sm" title="Agendar Reunião">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </button>
            <button id="btnWhatsappClient" onclick="openClientWhatsapp()" class="flex items-center justify-center p-2.5 bg-[#25D366] hover:bg-[#128C7E] text-white rounded-none transition-colors shadow-sm" title="Abrir WhatsApp">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
            </button>
            <button onclick="closeClientDrawer()" class="text-slate-400 hover:text-rose-500 p-2 transition">
                <svg class="w-6 h-6 border border-transparent hover:border-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>

    <!-- Tabs Menu (Premium styling) -->
    <div class="px-6 border-b border-gray-200 dark:border-slate-800 flex gap-8 bg-white dark:bg-[#0f172a]">
        <button onclick="switchTab('details')" id="tab-details" class="py-4 text-xs font-bold uppercase tracking-widest border-b-2 border-transparent hover:text-slate-900 dark:hover:text-white text-slate-400 transition-all flex items-center gap-2 group">
            <svg class="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:text-current transition-colors tab-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            Painel
        </button>
        <button onclick="switchTab('notes')" id="tab-notes" class="py-4 text-xs font-bold uppercase tracking-widest border-b-2 border-transparent hover:text-slate-900 dark:hover:text-white text-slate-400 transition-all flex items-center gap-2 group">
            <svg class="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:text-current transition-colors tab-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            Anotações
        </button>
        <button onclick="switchTab('timeline')" id="tab-timeline" class="py-4 text-xs font-bold uppercase tracking-widest border-b-2 border-transparent hover:text-slate-900 dark:hover:text-white text-slate-400 transition-all flex items-center gap-2 group">
            <svg class="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:text-current transition-colors tab-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Timeline
        </button>
    </div>

    <!-- Content Area -->
    <div class="flex-1 overflow-y-auto custom-scrollbar p-6 bg-white dark:bg-[#0f172a] relative">

        <!-- TAB 1: PAINEL DE CONTROLE (BI) -->
        <div id="content-details" class="space-y-8 animate-fade-in">
            
            <!-- BI Actions (Filters & Exports) -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-4 bg-gray-50 dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-slate-800 shadow-sm">
                <div class="flex items-center gap-3">
                    <select id="biPeriodFilter" onchange="updateBI()" class="bg-white dark:bg-[#1e293b] border border-gray-300 dark:border-slate-700 text-slate-800 dark:text-white text-xs font-bold uppercase tracking-wider p-2 rounded-lg outline-none focus:border-[var(--theme-color)] cursor-pointer">
                        <option value="hoje">Hoje</option>
                        <option value="ontem">Ontem</option>
                        <option value="7d">Últimos 7 Dias</option>
                        <option value="30d" selected>Últimos 30 Dias</option>
                        <option value="mes">Mês Atual</option>
                    </select>
                    
                    <!-- Metrics Config Gear -->
                    <div class="relative">
                        <button onclick="toggleMetricsMenu()" class="p-2 bg-white dark:bg-[#1e293b] border border-gray-300 dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-300 hover:text-[var(--theme-color)] transition" title="Configurar Métricas">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </button>
                        <div id="metricsConfigMenu" class="hidden absolute left-0 top-full mt-2 w-48 bg-white dark:bg-[#1e293b] border border-gray-200 dark:border-slate-700 rounded-xl shadow-xl z-50 p-2">
                            <div class="text-[10px] uppercase font-bold text-slate-400 mb-2 px-2">Exibir Métricas</div>
                            <!-- Populated by JS -->
                            <div id="metricsCheckboxList" class="space-y-1"></div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <button onclick="openAdsManager()" class="flex items-center gap-2 px-3 py-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/50 hover:bg-blue-100 dark:hover:bg-blue-900/40 text-blue-700 dark:text-blue-400 font-bold text-[10px] uppercase tracking-wider transition rounded-lg">
                        📊 Ads Manager
                    </button>
                    <button onclick="copiarRelatorioWhatsApp()" class="flex items-center gap-2 px-3 py-2 bg-[var(--theme-color)] border border-transparent hover:opacity-90 text-white font-bold text-[10px] uppercase tracking-wider transition rounded-lg">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 21.053l-2.486-.682-.74-2.42-3.076-2.02.16-3.666 2.059-3.048L11.537 7l3.682.128 3.513 1.968 2.096 3.013.235 3.667-1.921 3.12-3.486 2.113-3.625.044zM8.347 16.59l1.625 1.768q3.535 3.844 5.341 5.612l3.411-1.353-2.193-4.996-2.185-1.077c-.961-.475-1.503-.102-1.933.31l-.664.636q-1.996-1-3.69-2.617c-1.328-1.265-2.071-2.433-2.618-3.69l.635-.664q.608-.636.27-1.928l-1.078-2.185-4.996-2.193-1.353 3.411q1.768 1.806 5.612 5.342l1.768 1.625zM12 2C6.477 2 2 6.477 2 12a9.96 9.96 0 001.373 5.06l-1.309 4.793 4.931-1.293A9.957 9.957 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2zm0 18.118a8.077 8.077 0 01-4.22-1.18l-.302-.178-3.141.822.836-3.06-.195-.31A8.07 8.07 0 013.883 12c0-4.48 3.64-8.118 8.117-8.118C16.48 3.883 20.118 7.52 20.118 12c0 4.479-3.639 8.118-8.118 8.118z"></path></svg>
                        WPP
                    </button>
                    <button onclick="gerarRelatorioPDF()" class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-[#1e293b] border border-gray-300 dark:border-slate-700 hover:border-[var(--theme-color)] dark:hover:border-[var(--theme-color)] hover:text-[var(--theme-color)] dark:hover:text-[var(--theme-color)] text-slate-700 dark:text-slate-300 font-bold text-[10px] uppercase tracking-wider transition rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        PDF
                    </button>
                </div>
            </div>

            <!-- BI Dynamic Grid -->
            <!-- Note: ID 'biMetricsGrid' is targeted heavily by JS to swap cards dynamically -->
            <div id="biMetricsGrid" class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <!-- Cards injected here via client_bi.js -->
            </div>

            <!-- Pinned Links (Fixados) -->
            <div id="pinnedLinksSection" class="hidden">
                 <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2 border-b border-gray-100 dark:border-slate-800 pb-2">
                    <svg class="w-3 h-3 text-[var(--theme-color)]" fill="currentColor" viewBox="0 0 24 24"><path d="M16 12V4h1c.55 0 1-.45 1-1s-.45-1-1-1H7c-.55 0-1 .45-1 1s.45 1 1 1h1v8c0 1.66-1.34 3-3 3v2h5v6l1 1 1-1v-6h5v-2c-1.66 0-3-1.34-3-3z"></path></svg>
                    Hub Fixado
                </h3>
                <div id="drawerPinnedLinks" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Populated by JS -->
                </div>
            </div>

            <!-- Dados do Facebook/Forms -->
            <div class="border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-900/50">
                <button onclick="toggleMetaAccordion()" class="w-full flex justify-between items-center p-4 hover:bg-gray-100 dark:hover:bg-slate-800 transition">
                    <div class="flex items-center gap-2">
                         <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.04c-5.5 0-10 4.49-10 10.02 0 5 3.66 9.15 8.44 9.9v-7H7.9v-2.9h2.54V9.85c0-2.51 1.49-3.89 3.78-3.89 1.09 0 2.23.19 2.23.19v2.47h-1.26c-1.24 0-1.63.77-1.63 1.56v1.88h2.78l-.45 2.9h-2.33v7a10 10 0 008.44-9.9c0-5.53-4.5-10.02-10-10.02z"></path></svg>
                         <span class="font-bold text-slate-900 dark:text-white text-sm tracking-tight">Origem do Lead</span>
                    </div>
                    <svg id="metaArrow" class="w-5 h-5 text-slate-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="metaContent" class="hidden p-4 bg-white dark:bg-[#0f172a] space-y-3 text-sm border-t border-gray-200 dark:border-slate-800 transition-all font-mono">
                    <p class="text-slate-400 italic">Carregando...</p>
                </div>
            </div>
            
             <!-- All Links Management -->
            <div class="space-y-4 pt-6 border-t border-gray-100 dark:border-slate-800">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-gray-100 dark:border-slate-800 pb-2">Gerenciar Links</h3>
                
                <ul id="drawerLinksList" class="space-y-2 text-sm">
                    <!-- Populated by JS -->
                </ul>

                <form id="addLinkForm" class="flex gap-2 items-center mt-4 p-3 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-800">
                    <input type="text" id="newLinkTitle" placeholder="Título (Ex: Drive)" class="flex-1 bg-white dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 px-3 py-2 text-sm outline-none focus:border-[var(--theme-color)] transition">
                    <input type="url" id="newLinkUrl" placeholder="https://" class="w-1/3 bg-white dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 px-3 py-2 text-sm outline-none focus:border-[var(--theme-color)] transition">
                   <div class="flex items-center gap-2">
                       <label class="flex items-center cursor-pointer relative" title="Fixar no Topo">
                            <input type="checkbox" id="newLinkPinned" class="sr-only peer">
                             <div class="w-8 h-8 bg-gray-200 dark:bg-slate-800 text-gray-400 peer-checked:bg-[var(--theme-color)] peer-checked:text-white flex items-center justify-center transition border border-transparent peer-checked:border-current">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M16 12V4h1c.55 0 1-.45 1-1s-.45-1-1-1H7c-.55 0-1 .45-1 1s.45 1 1 1h1v8c0 1.66-1.34 3-3 3v2h5v6l1 1 1-1v-6h5v-2c-1.66 0-3-1.34-3-3z"></path></svg>
                             </div>
                        </label>
                        <button type="submit" class="bg-black dark:bg-white text-white dark:text-black p-2 hover:opacity-90 transition" title="Adicionar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </button>
                   </div>
                </form>
            </div>
            
            <!-- Technical Data (Read Only) -->
            <div id="drawerClientFullInfo" class="pt-6 border-t border-gray-100 dark:border-slate-800 space-y-4">
                <!-- Populated by JS -->
            </div>

        </div>

        <!-- TAB 2: ANOTAÇÕES -->
        <div id="content-notes" class="space-y-6 hidden animate-fade-in">
             <!-- New Note -->
             <div class="bg-gray-50 dark:bg-slate-900 p-4 border border-gray-200 dark:border-slate-800">
                <form id="addNoteForm" class="relative">
                    <textarea id="newNoteContent" rows="4" placeholder="Registre uma nova interação..." class="w-full bg-white dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 p-3 text-sm focus:border-slate-900 dark:focus:border-white outline-none transition resize-none"></textarea>
                    <div class="flex justify-end mt-3">
                        <button type="submit" class="bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-gray-200 text-white dark:text-black px-6 py-2 text-[10px] font-black uppercase tracking-widest transition">Salvar Nota</button>
                    </div>
                </form>
             </div>

             <div class="space-y-4 pt-4 border-t border-gray-100 dark:border-slate-800">
                 <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Histórico Chronológico</h3>
                 <div id="drawerNotesList" class="space-y-4">
                     <!-- Populated JS -->
                 </div>
             </div>
        </div>

        <!-- TAB 3: TIMELINE -->
        <div id="content-timeline" class="hidden animate-fade-in p-2">
             <div class="relative pl-6 space-y-8 before:absolute before:inset-y-0 before:left-[11px] before:w-0.5 before:bg-slate-200 dark:before:bg-slate-800">
                <div id="drawerTimelineList" class="space-y-8">
                    <!-- Populated JS -->
                </div>
             </div>
        </div>

    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.2s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    .tab-active { border-color: var(--theme-color) !important; color: var(--theme-color) !important; }
</style>

<script src="js/client_bi.js"></script>
