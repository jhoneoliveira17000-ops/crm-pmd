<div id="settingsDrawerOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[90] hidden transition-opacity duration-300 opacity-0" onclick="toggleSettings()"></div>
<div id="settingsDrawer" class="fixed inset-y-0 right-0 w-80 bg-white dark:bg-slate-900 shadow-2xl z-[100] transform transition-transform duration-300 translate-x-full border-l border-gray-200 dark:border-slate-800 flex flex-col">
    <div class="p-6 border-b border-gray-200 dark:border-slate-800 flex justify-between items-center bg-gray-50 dark:bg-slate-900">
        <h3 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            Configurações
        </h3>
        <button onclick="toggleSettings()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
    
    <div class="flex-1 overflow-y-auto p-4 space-y-2">
        
        <!-- Aparencia -->
        <details class="group bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 open:border-[var(--theme-color)] dark:open:border-[var(--theme-color)] transition">
            <summary class="flex items-center justify-between p-4 cursor-pointer list-none font-bold text-slate-700 dark:text-slate-300">
                <span>Aparência</span>
                <svg class="w-5 h-5 text-slate-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </summary>
            <div class="px-4 pb-4 pt-0 border-t border-gray-100 dark:border-slate-700/50 mt-2">
                <div class="flex items-center justify-between mt-3">
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Modo Escuro</span>
                    <button onclick="toggleTheme()" id="themeToggleBtn" class="w-11 h-6 bg-gray-200 dark:bg-slate-700 rounded-full relative transition-colors focus:outline-none focus:ring-2 focus:ring-[var(--theme-color)] focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                        <span class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform transform translate-x-0 dark:translate-x-5 shadow-sm"></span>
                    </button>
                </div>
            </div>
        </details>

        <!-- Integração com Meta -->
        <details class="group bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 open:border-[var(--theme-color)] dark:open:border-[var(--theme-color)] transition">
            <summary class="flex items-center justify-between p-4 cursor-pointer list-none font-bold text-slate-700 dark:text-slate-300">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.791-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    Integração Meta Ads
                </span>
                <svg class="w-5 h-5 text-slate-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </summary>
            <div class="px-4 pb-4 pt-0 space-y-4 mt-2">
                
                <!-- Verify Token -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Verify Token</label>
                    <div class="flex gap-2">
                        <input type="text" id="metaVerifyToken" class="flex-1 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-600 rounded p-2.5 text-sm text-slate-800 dark:text-white outline-none focus:border-[var(--theme-color)] font-mono" placeholder="Token de verificação">
                        <button onclick="generateVerifyToken()" class="px-3 bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 rounded border border-gray-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 transition" title="Gerar Token">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Page Access Token -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Page Access Token</label>
                    <input type="password" id="metaAccessToken" class="w-full bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-600 rounded p-2.5 text-sm text-slate-800 dark:text-white outline-none focus:border-[var(--theme-color)] font-mono" placeholder="EAA...">
                </div>

                <!-- Page ID -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Page ID</label>
                    <input type="text" id="metaPageId" class="w-full bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-600 rounded p-2.5 text-sm text-slate-800 dark:text-white outline-none focus:border-[var(--theme-color)] font-mono" placeholder="123456789">
                </div>

                <!-- Save Button (Specific) -->
                <button onclick="saveSettings()" class="w-full bg-[var(--theme-color)] hover:bg-green-600 text-white font-bold py-2.5 rounded-lg shadow-lg shadow-green-900/20 transition">
                    Salvar Meta Config
                </button>

                <hr class="border-gray-100 dark:border-slate-700/50 my-2">

                <!-- Webhook URL -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Webhook URL</label>
                    <div class="relative">
                        <input type="text" id="metaWebhookUrl" readonly class="w-full bg-slate-100 dark:bg-slate-950/50 border border-gray-200 dark:border-slate-700 rounded p-2.5 pr-10 text-xs text-slate-600 dark:text-slate-400 font-mono select-all">
                        <button onclick="copyToClipboard('metaWebhookUrl')" class="absolute right-2 top-2 text-slate-400 hover:text-[var(--theme-color)] transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        </button>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">Configure na Meta Developers</p>
                </div>

            </div>
        </details>

        <!-- Personalização -->
        <details class="group bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 open:border-[var(--theme-color)] dark:open:border-[var(--theme-color)] transition" open>
            <summary class="flex items-center justify-between p-4 cursor-pointer list-none font-bold text-slate-700 dark:text-slate-300">
                <span>Personalização</span>
                <svg class="w-5 h-5 text-slate-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </summary>
            <div class="px-4 pb-4 pt-0 space-y-4 mt-2">
                 <!-- Logo -->
                 <div>
                     <label class="block text-xs font-medium text-slate-500 mb-2">Logo do Sistema (Drag & Drop)</label>
                     <div class="flex items-center gap-3">
                         <div id="logoDropZone" class="relative w-full h-32 border-2 border-dashed border-gray-300 dark:border-slate-600 rounded-lg flex flex-col items-center justify-center bg-gray-50 dark:bg-slate-900 overflow-hidden transition-colors hover:border-[var(--theme-color)] group cursor-pointer" onclick="document.getElementById('logoInput').click()">
                             
                             <img id="logoPreview" src="" alt="Logo" class="absolute inset-0 w-full h-full object-contain p-2 hidden z-10">
                             
                             <div id="logoPlaceholder" class="flex flex-col items-center text-slate-400 group-hover:text-[var(--theme-color)] transition">
                                 <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                 <span class="text-xs font-medium">Arraste ou Clique</span>
                             </div>
                             
                             <input type="file" id="logoInput" class="hidden" accept="image/*" onchange="previewLogo(event)">
                         </div>
                     </div>
                 </div>

                 <!-- Theme Color -->
                 <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Cor do Tema</label>
                    <div class="flex items-center gap-2">
                        <input type="color" id="themeColorInput" class="w-8 h-8 rounded cursor-pointer border-0 p-0" value="var(--theme-color)">
                        <span class="text-xs text-slate-400">Selecione a cor principal</span>
                    </div>
                 </div>

                 <!-- WhatsApp Msg -->
                 <div>
                     <label class="block text-xs font-medium text-slate-500 mb-1">Mensagem Inicial WhatsApp</label>
                     <textarea id="whatsappMsgInput" rows="2" class="w-full bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-600 rounded p-2 text-sm text-slate-800 dark:text-white outline-none focus:border-[var(--theme-color)] resize-none"></textarea>
                     <p class="text-[10px] text-slate-400 mt-1">Variável: <code>{nome}</code></p>
                 </div>
            </div>
        </details>

        <!-- Dados -->
        <details class="group bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 open:border-[var(--theme-color)] dark:open:border-[var(--theme-color)] transition">
            <summary class="flex items-center justify-between p-4 cursor-pointer list-none font-bold text-slate-700 dark:text-slate-300">
                <span>Dados</span>
                <svg class="w-5 h-5 text-slate-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </summary>
            <div class="px-4 pb-4 pt-0 space-y-2 mt-2">
                 <button onclick="window.open('api/export.php', '_blank')" class="w-full flex items-center justify-between px-3 py-2 bg-slate-50 dark:bg-slate-900 rounded border border-gray-200 dark:border-slate-700 hover:border-[var(--theme-color)] transition group/btn">
                     <span class="text-sm text-slate-700 dark:text-slate-300">Baixar Leads (CSV)</span>
                     <svg class="w-4 h-4 text-slate-400 group-hover/btn:text-[var(--theme-color)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                 </button>
            </div>
        </details>

        <div class="px-1 pt-2">
            <button onclick="saveSettings()" class="w-full bg-[var(--theme-color)] hover:bg-green-600 text-white font-bold py-3 rounded-lg shadow-lg shadow-green-900/20 transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Salvar Configurações
            </button>
        </div>
    </div>

        <!-- System Status -->
        <div>
             <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Status do Sistema</h4>
             <div class="space-y-3">
                 <div class="flex items-center justify-between text-sm">
                     <span class="text-slate-600 dark:text-slate-400">Versão</span>
                     <span class="font-mono text-slate-800 dark:text-white">v1.2.1</span>
                 </div>
                 <div class="flex items-center justify-between text-sm">
                     <span class="text-slate-600 dark:text-slate-400">Ambiente</span>
                     <span class="px-2 py-0.5 rounded bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 text-xs font-bold">PRODUÇÃO</span>
                 </div>
             </div>
        </div>
    </div>

</div>
