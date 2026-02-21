<!-- Lead Details Drawer -->
<div id="leadDataModal" class="fixed inset-0 z-50 hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity opacity-0" id="leadDataBackdrop" onclick="closeLeadDataModal()"></div>

    <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <!-- Panel -->
                <div class="pointer-events-auto relative w-screen max-w-xl transform transition ease-in-out duration-300 translate-x-full" id="leadDataPanel">
                    
                    <div class="flex h-full flex-col bg-[#0f172a] shadow-2xl border-l border-slate-800">
                        <!-- Header -->
                        <div class="px-6 py-6 border-b border-slate-800 bg-slate-900/50 flex justify-between items-start">
                            <div class="flex-1">
                                <h2 class="text-xl font-bold text-white mb-1 leading-6" id="modalLeadName">Carregando...</h2>
                                <span class="text-xs text-slate-400 font-mono" id="modalLeadMeta">...</span>
                            </div>
                            <div class="flex items-center gap-3 ml-4 h-7">
                                <button onclick="convertLeadToClient()" class="bg-indigo-600 hover:bg-indigo-500 text-white px-3 py-1.5 rounded text-xs font-bold transition flex items-center gap-2 shadow-lg shadow-indigo-500/20">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                    Converter
                                </button>
                                <button onclick="closeLeadDataModal()" class="text-slate-400 hover:text-white transition focus:outline-none">
                                    <span class="sr-only">Close panel</span>
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Body with Tabs -->
                        <div class="flex-1 overflow-hidden flex flex-col relative">
                            <!-- Tabs Nav -->
                            <div class="flex border-b border-slate-800 bg-slate-900/30 px-6">
                                <button onclick="switchTab('details')" class="py-4 text-xs font-bold uppercase tracking-wider text-green-500 border-b-2 border-green-500 transition mr-6" id="tab-btn-details">Detalhes</button>
                                <button onclick="switchTab('notes')" class="py-4 text-xs font-bold uppercase tracking-wider text-slate-400 border-b-2 border-transparent hover:text-white transition mr-6" id="tab-btn-notes">Anotações</button>
                                <button onclick="switchTab('history')" class="py-4 text-xs font-bold uppercase tracking-wider text-slate-400 border-b-2 border-transparent hover:text-white transition" id="tab-btn-history">Histórico</button>
                            </div>

                            <!-- Tab Content -->
                            <div class="flex-1 overflow-y-auto p-6 custom-scrollbar" id="tab-content-details">
                                <div class="space-y-6">
                                    <div class="grid grid-cols-2 gap-5">
                                        <div class="col-span-2">
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1.5">Email</label>
                                            <div class="flex items-center gap-2 bg-slate-800/50 border border-slate-700/50 rounded-lg p-2.5">
                                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                                <input type="text" id="modalEmail" readonly class="bg-transparent w-full text-slate-200 text-sm outline-none">
                                            </div>
                                        </div>
                                        
                                        <div class="col-span-2">
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1.5">Telefone / WhatsApp</label>
                                            <div class="flex gap-2">
                                                <div class="flex-1 flex items-center gap-2 bg-slate-800/50 border border-slate-700/50 rounded-lg p-2.5">
                                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                                    <input type="text" id="modalPhone" readonly class="bg-transparent w-full text-slate-200 text-sm outline-none">
                                                </div>
                                                <a id="modalWhatsAppBtn" href="#" target="_blank" class="bg-[#25D366] hover:bg-[#128C7E] text-white px-4 rounded-lg flex items-center justify-center transition shadow-lg shadow-green-900/20" title="Enviar WhatsApp">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                                </a>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1.5">Valor Estimado</label>
                                            <div class="flex items-center gap-2 bg-slate-800/50 border border-slate-700/50 rounded-lg p-2.5">
                                                <span class="text-slate-500 text-xs">R$</span>
                                                <input type="text" id="modalValue" readonly class="bg-transparent w-full text-slate-200 text-sm font-mono outline-none">
                                            </div>
                                        </div>
                                         <div>
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1.5">Origem</label>
                                             <div class="flex items-center gap-2 bg-slate-800/50 border border-slate-700/50 rounded-lg p-2.5">
                                                <input type="text" id="modalOrigin" readonly class="bg-transparent w-full text-slate-200 text-sm outline-none">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Meta Data Section -->
                                    <div id="metaDataSection" class="hidden border-t border-slate-800 pt-6">
                                        <h3 class="text-xs font-bold text-slate-400 mb-4 flex items-center gap-2 uppercase tracking-wide">
                                            <span class="text-blue-500">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.791-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                            </span>
                                            Meta Ads (Lead Form)
                                        </h3>
                                        <div class="bg-slate-800/40 rounded-lg border border-slate-700/50 p-4 grid grid-cols-1 gap-4">
                                            <div>
                                                <span class="block text-[10px] text-slate-500 uppercase font-bold mb-0.5">Campanha</span>
                                                <span class="text-sm text-slate-200 block" id="metaCampaign">-</span>
                                            </div>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <span class="block text-[10px] text-slate-500 uppercase font-bold mb-0.5">Conjunto</span>
                                                    <span class="text-xs text-slate-300 block" id="metaAdset">-</span>
                                                </div>
                                                <div>
                                                    <span class="block text-[10px] text-slate-500 uppercase font-bold mb-0.5">Anúncio</span>
                                                    <span class="text-xs text-slate-300 block" id="metaAd">-</span>
                                                </div>
                                            </div>
                                            <div id="metaExtraFields"></div>
                                        </div>
                                    </div>

                                    <!-- Template Selector -->
                                    <div class="pt-6 border-t border-slate-800">
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2">Mensagem Rápida (WhatsApp)</label>
                                        <select id="whatsappTemplateSelect" onchange="updateWhatsAppLink()" class="w-full bg-slate-800 border border-slate-700 rounded-lg p-3 text-white text-sm focus:outline-none focus:border-green-500 transition">
                                             <option value="default">👋 Olá, vi seu interesse...</option>
                                             <!-- Add more templates here later -->
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- History Content -->
                            <div class="flex-1 overflow-y-auto p-6 hidden custom-scrollbar" id="tab-content-history">
                                <div id="historyList" class="relative border-l border-slate-700 ml-3 space-y-8">
                                    <!-- Injected via JS -->
                                </div>
                            </div>

                            <!-- Notes Content -->
                            <div class="flex-1 overflow-y-auto p-6 hidden custom-scrollbar" id="tab-content-notes">
                                 <!-- Add Note -->
                                 <div class="mb-6">
                                    <textarea id="newNoteText" rows="3" class="w-full bg-slate-800 border border-slate-700 rounded-xl p-4 text-white placeholder-slate-500 focus:border-green-500 outline-none transition resize-none text-sm" placeholder="Escreva uma observação importante..."></textarea>
                                    <div class="flex justify-end mt-2">
                                        <button onclick="saveNote()" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2">
                                            <span>+</span> Adicionar Nota
                                        </button>
                                    </div>
                                 </div>

                                 <!-- Notes List -->
                                 <div id="notesList" class="space-y-4"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentLead = null;

    function openLeadFullModal(lead) {
        currentLead = lead;
        document.getElementById('modalLeadName').innerText = lead.nome;
        document.getElementById('modalLeadMeta').innerText = `Entrou em ${new Date(lead.created_at).toLocaleDateString()} via ${lead.origem || 'Manual'}`;
        document.getElementById('modalEmail').value = lead.email;
        document.getElementById('modalPhone').value = lead.telefone;
        document.getElementById('modalValue').value = parseFloat(lead.valor_estimado).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
        document.getElementById('modalOrigin').value = lead.origem || 'Manual';
        
        updateWhatsAppLink();
        loadNotes(lead.id);
        loadHistory(lead.id);
        loadMetaDetails(lead.id);

        // Animation: Open
        const modal = document.getElementById('leadDataModal');
        const backdrop = document.getElementById('leadDataBackdrop');
        const panel = document.getElementById('leadDataPanel');

        modal.classList.remove('hidden');
        
        // Small delay for CSS transition to trigger
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('translate-x-full');
        }, 10);

        switchTab('details'); // Reset tab
    }

    function closeLeadDataModal() {
        // Animation: Close
        const modal = document.getElementById('leadDataModal');
        const backdrop = document.getElementById('leadDataBackdrop');
        const panel = document.getElementById('leadDataPanel');

        backdrop.classList.add('opacity-0');
        panel.classList.add('translate-x-full');

        // Wait for transition end
        setTimeout(() => {
            modal.classList.add('hidden');
            currentLead = null;
        }, 300); // match duration-300
    }

    function switchTab(tab) {
        // Reset Styles
        ['details', 'notes', 'history'].forEach(t => {
            const btn = document.getElementById(`tab-btn-${t}`);
            const content = document.getElementById(`tab-content-${t}`);
            
            if(t === tab) {
                btn.classList.add('text-green-500', 'border-green-500');
                btn.classList.remove('text-slate-400', 'border-transparent');
                content.classList.remove('hidden');
            } else {
                btn.classList.remove('text-green-500', 'border-green-500');
                btn.classList.add('text-slate-400', 'border-transparent');
                content.classList.add('hidden');
            }
        });
    }

    function updateWhatsAppLink() {
        if (!currentLead || !currentLead.telefone) {
            document.getElementById('modalWhatsAppBtn').classList.add('opacity-50', 'pointer-events-none');
            return;
        }
        document.getElementById('modalWhatsAppBtn').classList.remove('opacity-50', 'pointer-events-none');
        
        const template = window.whatsappDefaultMsg || "Olá, vi seu interesse...";
        // Simple replace
        const msg = template.replace('{nome}', currentLead.nome);
        const phone = currentLead.telefone.replace(/\D/g, '');
        
        document.getElementById('modalWhatsAppBtn').href = `https://wa.me/${phone}?text=${encodeURIComponent(msg)}`;
    }

    async function loadNotes(leadId) {
        const list = document.getElementById('notesList');
        list.innerHTML = '<p class="text-xs text-slate-500 animate-pulse">Carregando...</p>';
        
        try {
            const res = await fetch(`api/notes.php?lead_id=${leadId}`);
            const notes = await res.json();
            
            if (notes.length === 0) {
                list.innerHTML = '<p class="text-sm text-slate-500 text-center py-4">Nenhuma anotação ainda.</p>';
            } else {
                list.innerHTML = notes.map(n => `
                    <div class="bg-slate-800 p-3 rounded-lg border border-slate-700">
                        <p class="text-sm text-slate-300 whitespace-pre-wrap">${n.note}</p>
                        <div class="flex justify-between items-center mt-2 border-t border-slate-700/50 pt-2">
                             <span class="text-[10px] text-green-500 font-bold">${n.usuario_nome || 'Usuário'}</span>
                             <span class="text-[10px] text-slate-500">${new Date(n.created_at).toLocaleString()}</span>
                        </div>
                    </div>
                `).join('');
            }
        } catch (e) { list.innerHTML = '<p class="text-red-500 text-xs">Erro ao carregar</p>'; }
    }

    async function saveNote() {
        const txt = document.getElementById('newNoteText').value;
        if (!txt.trim()) return;

        try {
            const res = await fetch('api/notes.php', {
                method: 'POST',
                body: JSON.stringify({ lead_id: currentLead.id, note: txt })
            });
            const json = await res.json();
            
            if (json.success) {
                document.getElementById('newNoteText').value = '';
                loadNotes(currentLead.id);
            }
        } catch (e) {
             console.error(e);
             alert('Erro ao salvar nota');
        }
    }

    async function loadHistory(leadId) {
        const list = document.getElementById('historyList');
        list.innerHTML = '<p class="text-xs text-slate-500 animate-pulse ml-4">Carregando...</p>';
        
        try {
            const res = await fetch(`api/history.php?lead_id=${leadId}`);
            const history = await res.json();
            
            if (history.length === 0) {
                list.innerHTML = '<p class="text-sm text-slate-500 ml-4 py-4">Nenhum histórico.</p>';
            } else {
                list.innerHTML = history.map(h => `
                    <div class="mb-4 ml-6 relative group">
                        <span class="absolute -left-[31px] flex items-center justify-center w-4 h-4 rounded-full ring-4 ring-slate-900 bg-slate-700 group-hover:bg-green-500 transition"></span>
                        <h3 class="flex items-center mb-1 text-sm font-semibold text-white">
                            Mudança de Estágio
                            <span class="bg-blue-900 text-blue-300 text-[10px] font-medium mr-2 px-2.5 py-0.5 rounded ml-2">${new Date(h.data_movimentacao).toLocaleTimeString().slice(0,5)}</span>
                        </h3>
                        <p class="mb-1 text-xs font-normal text-slate-400">
                            De <span class="text-red-400 font-bold">${h.de_estagio || '?'}</span> para <span class="text-green-400 font-bold">${h.para_estagio || '?'}</span>
                        </p>
                        <p class="text-[10px] text-slate-500">Por: ${h.usuario_nome || 'Sistema'}</p>
                    </div>
                `).join('');
            }
        } catch(e) { console.error(e); }
    }

    async function loadMetaDetails(leadId) {
        // Reset/Hide Meta Section (Optimistic)
        const metaSection = document.getElementById('metaDataSection');
        metaSection.classList.add('hidden');
        document.getElementById('metaCampaign').innerText = '-';
        document.getElementById('metaAdset').innerText = '-';
        document.getElementById('metaAd').innerText = '-';
        document.getElementById('metaExtraFields').innerHTML = '';

        try {
            const res = await fetch(`api/lead_meta.php?id=${leadId}`);
            const data = await res.json();
            
            if (data.success && data.meta && Object.keys(data.meta).length > 0) {
                // Populate Meta Data
                metaSection.classList.remove('hidden');
                document.getElementById('metaCampaign').innerText = data.meta.campaign_name || '-';
                document.getElementById('metaAdset').innerText = data.meta.adset_name || '-';
                document.getElementById('metaAd').innerText = data.meta.ad_name || '-';
                document.getElementById('metaPlatform').innerText = data.meta.platform || '-';

                // Extra Fields
                if (data.meta.extra_data && data.meta.extra_data.length > 0) {
                     const extras = data.meta.extra_data.map(f => `
                        <div class="mt-2 text-xs">
                            <span class="block text-[10px] text-slate-500 uppercase font-bold">${f.label}</span>
                            <span class="text-slate-300">${f.value}</span>
                        </div>
                     `).join('');
                     document.getElementById('metaExtraFields').innerHTML = '<div class="pt-2 border-t border-slate-700 mt-2">' + extras + '</div>';
                }
            }
        } catch(e) {
            console.error('Error loading meta data:', e);
        }
    }

    async function convertLeadToClient() {
        if (!currentLead) return;
        if (!confirm('Deseja converter este lead em um novo cliente?')) return;
        
        try {
            const clientData = {
                nome_empresa: currentLead.nome,
                email: currentLead.email,
                telefone: currentLead.telefone,
                data_entrada: new Date().toISOString().split('T')[0],
                lead_id: currentLead.id,
                canal_aquisicao: 'Lead Inbound'
            };

            const res = await fetch('api/clientes.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(clientData)
            });
            
            const json = await res.json();
            if (json.success && json.id) {
                // Redirect to new client folder
                window.location.href = `cliente_dashboard.php?id=${json.id}`;
            } else {
                alert('Erro ao criar cliente: ' + (json.error || 'Desconhecido'));
            }
        } catch(e) {
            console.error(e);
            alert('Erro de conexão ao converter cliente.');
        }
    }
</script>
