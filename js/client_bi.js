let currentClientId = null;
let currentClientData = null;

// --- Configuração de Métricas (Settings Gear) ---
const availableMetrics = [
    { id: 'gasto', label: 'Gasto', format: 'currency', defaultActive: true },
    { id: 'leads', label: 'Leads', format: 'number', defaultActive: true },
    { id: 'cpl', label: 'CPL', format: 'currency', defaultActive: true },
    { id: 'cac', label: 'CAC', format: 'currency', defaultActive: true },
    { id: 'roas', label: 'ROAS', format: 'multiplier', defaultActive: true },
    { id: 'cpm', label: 'CPM', format: 'currency', defaultActive: false },
    { id: 'ctr', label: 'CTR', format: 'percent', defaultActive: false },
    { id: 'cliques', label: 'Cliques', format: 'number', defaultActive: false },
    { id: 'conversoes', label: 'Conversões', format: 'number', defaultActive: false },
];

let activeMetrics = availableMetrics.filter(m => m.defaultActive).map(m => m.id);

function toggleMetricsMenu() {
    const menu = document.getElementById('metricsConfigMenu');
    if (menu.classList.contains('hidden')) {
        renderMetricsConfig();
        menu.classList.remove('hidden');
    } else {
        menu.classList.add('hidden');
    }
}

// Close menu when clicking outside
document.addEventListener('click', (e) => {
    const menu = document.getElementById('metricsConfigMenu');
    if (!menu) return;
    if (!menu.classList.contains('hidden') && !e.target.closest('.relative')) {
        menu.classList.add('hidden');
    }
});

function renderMetricsConfig() {
    const container = document.getElementById('metricsCheckboxList');
    container.innerHTML = availableMetrics.map(m => `
        <label class="flex items-center gap-2 p-1 hover:bg-gray-50 dark:hover:bg-slate-800 rounded cursor-pointer transition">
            <input type="checkbox" value="${m.id}" ${activeMetrics.includes(m.id) ? 'checked' : ''} onchange="toggleMetricConfig('${m.id}')" class="rounded border-gray-300 dark:border-slate-600 text-[var(--theme-color)] focus:ring-[var(--theme-color)]">
            <span class="text-xs text-slate-700 dark:text-slate-300">${m.label}</span>
        </label>
    `).join('');
}

function toggleMetricConfig(id) {
    if (activeMetrics.includes(id)) {
        activeMetrics = activeMetrics.filter(m => m !== id);
    } else {
        activeMetrics.push(id);
    }
    updateBI();
}

// --- Dados Simulados do BI ---
function formatValue(value, format) {
    if (format === 'currency') return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    if (format === 'percent') return parseFloat(value).toFixed(2).replace('.', ',') + '%';
    if (format === 'multiplier') return parseFloat(value).toFixed(2).replace('.', ',') + 'x';
    return parseInt(value).toLocaleString('pt-BR');
}

function mockData(periodo) {
    // Generate realistic relative numbers based on period
    let multiplier = 1;
    if (periodo === 'ontem') multiplier = 0.8;
    if (periodo === '7d') multiplier = 6.5;
    if (periodo === '30d' || periodo === 'mes') multiplier = 28;

    // Base daily values 
    const baseGasto = 150 + (Math.random() * 50);
    const baseLeads = 12 + Math.floor(Math.random() * 5);
    const baseCliques = 350 + Math.floor(Math.random() * 100);
    const baseImpressions = 15000 + Math.floor(Math.random() * 5000);
    const baseConversoes = 2 + Math.floor(Math.random() * 3);

    const gasto = baseGasto * multiplier;
    const leads = Math.floor(baseLeads * multiplier);
    const cliques = Math.floor(baseCliques * multiplier);
    const impressions = Math.floor(baseImpressions * multiplier);
    const conversoes = Math.floor(baseConversoes * multiplier);

    const roasBase = 2.5 + (Math.random() * 3);

    return {
        gasto: gasto,
        leads: leads,
        cpl: leads > 0 ? gasto / leads : 0,
        cac: conversoes > 0 ? gasto / conversoes : 0,
        roas: roasBase,
        cpm: (gasto / impressions) * 1000,
        ctr: (cliques / impressions) * 100,
        cliques: cliques,
        conversoes: conversoes
    };
}

function updateBI() {
    const period = document.getElementById('biPeriodFilter').value;
    const data = mockData(period);

    // Target grid
    const grid = document.getElementById('biMetricsGrid');
    grid.innerHTML = '';

    availableMetrics.forEach(m => {
        if (activeMetrics.includes(m.id)) {
            const val = formatValue(data[m.id], m.format);
            grid.innerHTML += `
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700/50 p-4 transition-all hover:shadow-md hover:border-[var(--theme-color)] dark:hover:border-[var(--theme-color)] group flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-16 h-16 bg-[var(--theme-color)] opacity-5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="flex justify-between items-center mb-2 relative z-10">
                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">${m.label}</div>
                        <svg class="w-4 h-4 text-[var(--theme-color)] opacity-40 group-hover:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"></path></svg>
                    </div>
                    <div class="text-xl md:text-2xl font-black text-slate-800 dark:text-white tracking-tighter truncate group-hover:text-[var(--theme-color)] dark:group-hover:text-[var(--theme-color)] transition-colors relative z-10">${val}</div>
                </div>
            `;
        }
    });
}

// --- WhatsApp e Exportação ---
function openClientWhatsapp() {
    if (!currentClientData || !currentClientData.cliente) return;
    const phone = currentClientData.cliente.whatsapp || currentClientData.cliente.telefone;
    if (!phone) {
        alert("Cliente não possui telefone cadastrado.");
        return;
    }
    const cleanPhone = phone.replace(/\D/g, '');
    let msg = currentClientData.cliente.whatsapp_template || localStorage.getItem('whatsapp_default_msg') || "Olá {nome}, tudo bem?";
    msg = msg.replace(/{nome}/g, currentClientData.cliente.nome_responsavel || currentClientData.cliente.nome_empresa);
    window.open(`https://wa.me/55${cleanPhone}?text=${encodeURIComponent(msg)}`, '_blank');
}

function openAgendaContext() {
    if (!currentClientData || !currentClientData.cliente) return;
    const c = currentClientData.cliente;
    const leadId = c.id;
    const leadName = c.nome_empresa || c.nome_responsavel || 'Cliente';
    // Navigate to Agenda with prefill params
    window.location.href = `agenda.php?lead_id=${encodeURIComponent(leadId)}&lead_name=${encodeURIComponent(leadName)}`;
}

function copiarRelatorioWhatsApp() {
    if (!currentClientData || !currentClientData.cliente) return;
    const c = currentClientData.cliente;
    const periodSelect = document.getElementById('biPeriodFilter');
    const periodText = periodSelect.options[periodSelect.selectedIndex].text;

    const data = mockData(periodSelect.value);
    let metricsText = "";

    availableMetrics.forEach(m => {
        if (activeMetrics.includes(m.id)) {
            metricsText += `▪️ *${m.label}:* ${formatValue(data[m.id], m.format)}\n`;
        }
    });

    const notesList = currentClientData.notes || [];
    const recentNote = notesList.length > 0 ? notesList[0].conteudo : "Nenhuma anotação recente no período.";

    // Get specific template or use default
    let customTemplate = document.getElementById('clientWppTemplate')?.value;
    if (!customTemplate || customTemplate.trim() === '') {
        customTemplate = `📊 *RELATÓRIO DE PERFORMANCE*\n👤 *Cliente:* {nome}\n📅 *Período:* {periodo}\n\n🎯 *Principais Métricas:*\n{metricas}\n💡 *Nota Recente:* {nota_recente}\n✅ _Gerado via PMDCRM_`;
    }

    let text = customTemplate
        .replace(/{nome}/g, c.nome_empresa)
        .replace(/{periodo}/g, periodText)
        .replace(/{metricas}/g, metricsText)
        .replace(/{nota_recente}/g, recentNote);

    // Replace individual metrics if requested
    availableMetrics.forEach(m => {
        const val = formatValue(data[m.id], m.format);
        text = text.replace(new RegExp(`{${m.id}}`, 'g'), val);
    });

    navigator.clipboard.writeText(text).then(() => {
        alert('Relatório copiado para a área de transferência!');
    }).catch(err => {
        alert('Erro ao copiar relatório. Permissão negada.');
    });
}


function gerarRelatorioPDF() {
    const originalContent = document.body.innerHTML;
    const c = currentClientData.cliente;
    const periodSelect = document.getElementById('biPeriodFilter');
    const periodText = periodSelect.options[periodSelect.selectedIndex].text;

    // Get logo from nav if exists
    let logoHtml = '';
    const navLogo = document.querySelector('nav img[alt="Logo"]') || document.querySelector('aside img[alt="Logo"]');
    if (navLogo && navLogo.src) {
        logoHtml = `<img src="${navLogo.src}" alt="Logo" style="max-height: 50px; object-fit: contain;">`;
    }

    // System theme color for styling (fallback to black)
    const themeColorHex = getComputedStyle(document.documentElement).getPropertyValue('--theme-color').trim() || '#0f172a';

    // Rebuild grid specifically for PDF, stripping absolute positions and background SVGs
    let pdfGridHtml = '';
    const data = mockData(periodSelect.value);
    availableMetrics.forEach(m => {
        if (activeMetrics.includes(m.id)) {
            const val = formatValue(data[m.id], m.format);
            pdfGridHtml += `
                <div style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px;">
                    <div style="font-size: 10px; color: #64748b; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">${m.label}</div>
                    <div style="font-size: 24px; font-weight: 900; color: #0f172a;">${val}</div>
                </div>
            `;
        }
    });

    const printView = `
        <div style="font-family: 'Inter', sans-serif; padding: 40px; color: #1e293b; max-width: 800px; margin: 0 auto; background: white;">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 2px solid ${themeColorHex}; padding-bottom: 20px; margin-bottom: 30px;">
                <div>
                    ${logoHtml}
                    <h1 style="margin: 15px 0 0 0; font-size: 28px; color: #0f172a; letter-spacing: -0.5px;">RELATÓRIO DE PERFORMANCE</h1>
                    <p style="margin: 8px 0 0 0; color: #64748b; font-size: 14px;">Gerado em ${new Date().toLocaleDateString('pt-BR')} às ${new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}</p>
                </div>
                <div style="text-align: right; background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #f1f5f9;">
                    <p style="margin: 0; font-size: 11px; text-transform: uppercase; color: #94a3b8; font-weight: bold;">Cliente</p>
                    <p style="margin: 2px 0 10px 0; font-size: 16px; font-weight: bold; color: #0f172a;">${c.nome_empresa}</p>
                    <p style="margin: 0; font-size: 11px; text-transform: uppercase; color: #94a3b8; font-weight: bold;">Período Analisado</p>
                    <p style="margin: 2px 0 0 0; font-size: 14px; font-weight: bold; color: ${themeColorHex};">${periodText}</p>
                </div>
            </div>
            
            <h2 style="font-size: 14px; text-transform: uppercase; color: ${themeColorHex}; letter-spacing: 1px; margin-bottom: 15px; border-left: 4px solid ${themeColorHex}; padding-left: 10px;">Visão Geral de Métricas</h2>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
                ${pdfGridHtml}
            </div>
            
            <div style="margin-top: 50px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                Este é um documento gerado automaticamente. Dados sujeitos a pequenas variações dependendo do sincronismo das plataformas de origens.
            </div>
            
            <style>
                @media print {
                    @page { margin: 1cm; }
                    body { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
                }
            </style>
        </div>
    `;

    document.body.innerHTML = printView;
    window.print();
    document.body.innerHTML = originalContent;
    location.reload(); // Quick restore of event listeners
}

// --- Funções Originais do Drawer (Transferidas e Adaptadas) ---

// Tabs Logic
function switchTab(tabName) {
    ['details', 'notes', 'timeline'].forEach(t => {
        document.getElementById(`content-${t}`).classList.add('hidden');
        document.getElementById(`tab-${t}`).classList.remove('tab-active');
        document.getElementById(`tab-${t}`).classList.add('border-transparent');
        document.getElementById(`tab-${t}`).classList.remove('border-[var(--theme-color)]', 'text-slate-900', 'dark:text-white');
    });

    document.getElementById(`content-${tabName}`).classList.remove('hidden');
    document.getElementById(`tab-${tabName}`).classList.add('tab-active', 'border-[var(--theme-color)]', 'text-slate-900', 'dark:text-white');
    document.getElementById(`tab-${tabName}`).classList.remove('border-transparent');
}

function toggleMetaAccordion() {
    const content = document.getElementById('metaContent');
    const arrow = document.getElementById('metaArrow');
    content.classList.toggle('hidden');
    arrow.classList.toggle('rotate-180');
}

// --- OPEN DRAWER ---
async function openClientDrawer(id) {
    currentClientId = id;
    const drawer = document.getElementById('clientDrawer');
    const overlay = document.getElementById('clientDrawerOverlay');

    drawer.classList.remove('translate-x-full');
    overlay.classList.remove('hidden');
    // slight delay for transition
    setTimeout(() => overlay.classList.remove('opacity-0'), 10);


    // Reset
    switchTab('details');
    updateBI(); // Initialize Mock BI Data

    // Clear dynamic content
    document.getElementById('drawerPinnedLinks').innerHTML = '';
    document.getElementById('drawerLinksList').innerHTML = '';
    document.getElementById('drawerNotesList').innerHTML = '<p class="text-slate-400 text-sm">Carregando...</p>';
    document.getElementById('drawerTimelineList').innerHTML = '<p class="text-slate-400 text-sm">Carregando...</p>';

    try {
        const res = await fetch(`api/cliente_detalhes.php?id=${id}`);
        const data = await res.json();

        if (!data.success) throw new Error(data.error || 'Erro ao carregar');
        currentClientData = data;
        const c = data.cliente;

        // -- TOP HEADER --
        document.getElementById('drawerClientName').innerText = c.nome_empresa;
        document.getElementById('drawerClientStatus').innerText = c.status_contrato;
        document.getElementById('drawerClientStatus').className = 'px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest rounded-none border ' + getStatusColorBadges(c.status_contrato);
        document.getElementById('drawerClientInfo').innerText = `CNPJ: ${c.cnpj || 'Incompleto'}`;

        // Links
        renderLinks(data.links);

        // Meta
        if (c.lead_id) fetchMeta(c.lead_id);
        else document.getElementById('metaContent').innerHTML = '<p class="text-slate-400">Sem vínculo com formulários (Meta Ads).</p>';

        // Render Full Client Info (Read Only portion)
        renderClientInfo(c);

        // -- TAB NOTES & TIMELINE --
        renderNotes(data.notes);
        renderLogs(data.logs);

    } catch (e) {
        console.error(e);
        alert('Erro: ' + e.message);
    }
}

function renderLinks(links) {
    const pinnedContainer = document.getElementById('drawerPinnedLinks');
    const listContainer = document.getElementById('drawerLinksList');
    const pinnedSection = document.getElementById('pinnedLinksSection');

    pinnedContainer.innerHTML = '';
    listContainer.innerHTML = '';

    const pinned = links.filter(l => l.is_pinned == 1);
    const others = links.filter(l => l.is_pinned != 1);

    // Render Pinned (Anti-Safe Harbor Design)
    if (pinned.length > 0) {
        pinnedSection.classList.remove('hidden');
        pinned.forEach(l => {
            pinnedContainer.innerHTML += `
                <div class="flex items-center justify-between bg-white dark:bg-[#1e293b] border border-gray-200 dark:border-slate-700 hover:border-black dark:hover:border-white p-3 transition-colors group">
                    <a href="${l.url}" target="_blank" class="flex-1 font-bold text-slate-800 dark:text-white truncate text-xs uppercase tracking-wider">
                        ${l.titulo}
                    </a>
                    <div class="flex opacity-0 group-hover:opacity-100 transition-opacity">
                        <button onclick="togglePin(${l.id})" class="text-slate-400 hover:text-black dark:hover:text-white px-2" title="Desfixar">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M16 12V4h1c.55 0 1-.45 1-1s-.45-1-1-1H7c-.55 0-1 .45-1 1s.45 1 1 1h1v8c0 1.66-1.34 3-3 3v2h5v6l1 1 1-1v-6h5v-2c-1.66 0-3-1.34-3-3z"></path></svg>
                        </button>
                    </div>
                </div>
            `;
        });
    } else {
        pinnedSection.classList.add('hidden');
    }

    // Render List
    if (others.length === 0) {
        listContainer.innerHTML = '<li class="text-slate-400 italic text-xs">Nenhum link adicional.</li>';
    } else {
        others.forEach(l => {
            listContainer.innerHTML += `
               <li class="flex justify-between items-center group bg-gray-50 dark:bg-slate-900 border border-transparent hover:border-gray-200 dark:hover:border-slate-800 p-2 transition">
                    <a href="${l.url}" target="_blank" class="flex items-center gap-2 text-slate-600 dark:text-slate-300 hover:text-black dark:hover:text-white truncate font-medium max-w-[70%]">
                        <span class="w-1.5 h-1.5 bg-slate-300 dark:bg-slate-700 group-hover:bg-current"></span>
                        ${l.titulo}
                    </a>
                    <div class="flex items-center opacity-0 group-hover:opacity-100 transition">
                        <button onclick="togglePin(${l.id})" class="text-slate-400 hover:text-black dark:hover:text-white p-1" title="Fixar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                        </button>
                        <button onclick="deleteLink(${l.id})" class="text-slate-400 hover:text-rose-500 p-1" title="Excluir">&times;</button>
                    </div>
                </li>
            `;
        });
    }
}

function renderNotes(notes) {
    const list = document.getElementById('drawerNotesList');
    list.innerHTML = '';
    if (!notes || notes.length === 0) {
        list.innerHTML = '<p class="text-slate-400 text-xs italic">Nenhuma anotação.</p>';
        return;
    }
    notes.forEach(n => {
        const date = new Date(n.created_at).toLocaleString('pt-BR');
        list.innerHTML += `
            <div class="bg-gray-50 dark:bg-slate-900 p-4 border border-gray-200 dark:border-slate-800">
                <p class="text-sm text-slate-800 dark:text-slate-200 whitespace-pre-wrap leading-relaxed">${n.conteudo}</p>
                <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-200 dark:border-slate-800 text-[10px] text-slate-500 uppercase font-black tracking-widest">
                    <span>${n.autor || 'Sistema'} • ${date}</span>
                    <button onclick="deleteNote(${n.id})" class="hover:text-rose-500 transition">Excluir</button>
                </div>
            </div>
        `;
    });
}

function renderLogs(logs) {
    const list = document.getElementById('drawerTimelineList');
    list.innerHTML = '';
    logs.forEach(l => {
        const date = new Date(l.created_at).toLocaleDateString('pt-BR', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });
        list.innerHTML += `
            <div class="relative pb-2">
                <div class="absolute -left-6 top-1.5 w-2 h-2 rounded-full bg-black dark:bg-white ring-4 ring-white dark:ring-[#0f172a]"></div>
                <div class="text-sm text-slate-800 dark:text-white font-bold tracking-tight">${l.acao}</div>
                <div class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-widest mt-1">${l.usuario || 'Sistema'} • ${date}</div>
            </div>
        `;
    });
}

// Add Note Logic
document.getElementById('addNoteForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!currentClientId) return;
    const content = document.getElementById('newNoteContent').value;
    if (!content) return;

    try {
        await fetch('api/cliente_notas.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ cliente_id: currentClientId, conteudo: content })
        });
        document.getElementById('newNoteContent').value = '';
        openClientDrawer(currentClientId);
    } catch (e) { alert('Erro ao salvar nota'); }
});

// Add Link Logic
document.getElementById('addLinkForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!currentClientId) return;
    const titulo = document.getElementById('newLinkTitle').value;
    const url = document.getElementById('newLinkUrl').value;
    const isPinned = document.getElementById('newLinkPinned').checked;

    if (!titulo || !url) return;

    try {
        await fetch('api/cliente_links.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'create',
                cliente_id: currentClientId,
                titulo,
                url,
                is_pinned: isPinned ? 1 : 0
            })
        });
        document.getElementById('addLinkForm').reset();
        openClientDrawer(currentClientId);
    } catch (e) { alert('Erro ao salvar link'); }
});

async function togglePin(id) {
    try {
        await fetch('api/cliente_links.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'toggle_pin', id: id, cliente_id: currentClientId })
        });
        openClientDrawer(currentClientId);
    } catch (e) { alert('Erro ao alterar destaque'); }
}

async function deleteLink(id) {
    if (!confirm('Excluir link?')) return;
    fetch('api/cliente_links.php', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
    }).then(() => openClientDrawer(currentClientId));
}

async function deleteNote(id) {
    if (!confirm('Excluir nota?')) return;
    fetch('api/cliente_notas.php', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
    }).then(() => openClientDrawer(currentClientId));
}

// Auto-Save Configuration
async function saveClientConfig() {
    if (!currentClientId) return;
    const adsInput = document.getElementById('clientAdsAccountId');
    const wppInput = document.getElementById('clientWppTemplate');

    if (!adsInput || !wppInput) return; // Inputs not rendered

    const ads_account_id = adsInput.value;
    const whatsapp_template = wppInput.value;

    const btn = document.getElementById('btnSaveConfig');
    if (btn) {
        btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Salvando...`;
    }

    try {
        await fetch('api/cliente_update_fields.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id: currentClientId,
                ads_account_id,
                whatsapp_template
            })
        });

        // Update local object to avoid overwriting later
        if (currentClientData && currentClientData.cliente) {
            currentClientData.cliente.ads_account_id = ads_account_id;
            currentClientData.cliente.whatsapp_template = whatsapp_template;
        }

        if (btn) {
            btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Salvo!`;
            btn.classList.add('bg-green-600', 'text-white', 'border-transparent');
            btn.classList.remove('bg-white', 'dark:bg-[#0f172a]', 'text-slate-700', 'dark:text-slate-300');
            setTimeout(() => {
                btn.innerHTML = `Salvar Configurações`;
                btn.classList.remove('bg-green-600', 'text-white', 'border-transparent');
                btn.classList.add('bg-white', 'dark:bg-[#0f172a]', 'text-slate-700', 'dark:text-slate-300');
            }, 2000);
        }
    } catch (e) {
        console.error('Auto-save falhou', e);
        if (btn) btn.innerHTML = `Erro ao salvar`;
    }
}

function closeClientDrawer() {
    saveClientConfig(); // Save text fields on close
    const drawer = document.getElementById('clientDrawer');
    const overlay = document.getElementById('clientDrawerOverlay');

    drawer.classList.add('translate-x-full');
    overlay.classList.add('opacity-0');
    setTimeout(() => overlay.classList.add('hidden'), 300);
}

// Global ESC Listener for Drawer/Modals
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const drawer = document.getElementById('clientDrawer');
        if (drawer && !drawer.classList.contains('translate-x-full')) {
            closeClientDrawer();
        }
    }
});

function openAdsManager() {
    const actId = document.getElementById('clientAdsAccountId')?.value;
    if (actId && actId.trim() !== '') {
        window.open(`https://adsmanager.facebook.com/adsmanager/manage/campaigns?act=${actId.replace('act_', '')}`, '_blank');
    } else {
        window.open('https://adsmanager.facebook.com/adsmanager/', '_blank');
    }
}

async function fetchMeta(leadId) {
    try {
        const res = await fetch(`api/lead_meta.php?id=${leadId}`);
        const data = await res.json();

        if (data.success && data.meta) {
            let html = `<div class="grid grid-cols-2 gap-x-6 gap-y-4">`;
            html += `<div><strong class="block text-[10px] text-slate-400 uppercase tracking-widest mb-1">Campanha</strong> <span class="text-slate-900 dark:text-white font-bold">${data.meta.campaign_name}</span></div>`;
            html += `<div><strong class="block text-[10px] text-slate-400 uppercase tracking-widest mb-1">Anúncio</strong> <span class="text-slate-900 dark:text-white font-bold">${data.meta.ad_name}</span></div>`;
            html += `<div><strong class="block text-[10px] text-slate-400 uppercase tracking-widest mb-1">Plataforma</strong> <span class="text-slate-900 dark:text-white font-bold uppercase">${data.meta.platform}</span></div>`;
            html += `<div><strong class="block text-[10px] text-slate-400 uppercase tracking-widest mb-1">Data</strong> <span class="text-slate-900 dark:text-white font-bold">${new Date(data.meta.created_time).toLocaleDateString()}</span></div>`;
            html += `</div>`;

            if (data.meta.extra_data && data.meta.extra_data.length > 0) {
                html += `<div class="mt-4 pt-4 border-t border-gray-200 dark:border-slate-800 space-y-3">`;
                data.meta.extra_data.forEach(item => {
                    html += `<div><strong class="block text-[10px] text-slate-400 uppercase tracking-widest">${item.label}</strong> <span class="text-slate-900 dark:text-white font-medium">${item.value}</span></div>`;
                });
                html += `</div>`;
            }
            document.getElementById('metaContent').innerHTML = html;
        } else {
            document.getElementById('metaContent').innerHTML = '<p class="text-slate-400 text-xs">Dados de formulário não encontrados.</p>';
        }
    } catch (e) {
        document.getElementById('metaContent').innerHTML = '<p class="text-rose-500 text-xs">Erro ao carregar dados do Meta.</p>';
    }
}

function getStatusColorBadges(status) {
    const map = {
        'ativo': 'bg-green-100 text-green-800 dark:bg-transparent dark:text-green-400 border-green-200 dark:border-green-800',
        'pendente': 'bg-yellow-100 text-yellow-800 dark:bg-transparent dark:text-yellow-400 border-yellow-200 dark:border-yellow-800',
        'suspenso': 'bg-orange-100 text-orange-800 dark:bg-transparent dark:text-orange-400 border-orange-200 dark:border-orange-800',
        'cancelado': 'bg-red-100 text-red-800 dark:bg-transparent dark:text-red-400 border-red-200 dark:border-red-800'
    };
    return map[status?.toLowerCase()] || 'bg-gray-100 text-gray-700 dark:bg-transparent dark:text-slate-400 border-gray-200 dark:border-slate-700';
}

function renderClientInfo(c) {
    const container = document.getElementById('drawerClientFullInfo');

    const val = (v) => v ? `<span class="text-slate-900 dark:text-white font-bold">${v}</span>` : '<span class="text-slate-400 font-normal italic">Não informado</span>';
    const link = (v, type) => {
        if (!v) return val(v);
        if (type === 'email') return `<a href="mailto:${v}" class="text-[var(--theme-color)] hover:underline font-bold">${v}</a>`;
        if (type === 'tel') return `<a href="tel:${v.replace(/\D/g, '')}" class="text-[var(--theme-color)] hover:underline font-bold">${v}</a>`;
        if (type === 'url') return `<a href="${v.startsWith('http') ? v : 'https://' + v}" target="_blank" class="text-[var(--theme-color)] hover:underline truncate block font-bold">${v}</a>`;
        return `<span class="text-slate-900 dark:text-white font-bold">${v}</span>`;
    };

    container.innerHTML = `
        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 border-b border-gray-100 dark:border-slate-800 pb-2">
            Dados Técnicos e Cadastrais
        </h3>
        
        <div class="grid grid-cols-2 gap-x-6 gap-y-4 text-xs">
            <div class="col-span-2">
                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-black mb-0.5">Responsável</p>
                <p>${val(c.nome_responsavel)}</p>
            </div>
            
            <div class="col-span-2 md:col-span-1">
                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-black mb-0.5">Email</p>
                <p class="break-all">${link(c.email, 'email')}</p>
            </div>
            <div class="col-span-2 md:col-span-1">
                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-black mb-0.5">Telefone</p>
                <p>${link(c.whatsapp || c.telefone, 'tel')}</p>
            </div>

            <div class="col-span-2 md:col-span-1">
                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-black mb-0.5">Nicho</p>
                <p>${val(c.nicho)}</p>
            </div>
            <div class="col-span-2 md:col-span-1">
                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-black mb-0.5">Origem</p>
                <p>${val(c.origem)}</p>
            </div>

            <div class="col-span-2">
                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-black mb-0.5">Endereço</p>
                <p>${[c.endereco, c.cidade, c.estado, c.cep].filter(Boolean).join(', ') || val(null)}</p>
            </div>

            <div class="col-span-2">
                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-black mb-0.5">Produto / Serviço Principal</p>
                <p>${val(c.produto_servico)}</p>
            </div>

            <div class="col-span-2 md:col-span-1">
                 <p class="text-[10px] text-slate-400 uppercase tracking-widest font-black mb-0.5">Instagram</p>
                 <p class="truncate">${link(c.instagram, 'url')}</p>
            </div>
            <div class="col-span-2 md:col-span-1">
                 <p class="text-[10px] text-slate-400 uppercase tracking-widest font-black mb-0.5">Site / Landing Page</p>
                 <p class="truncate">${link(c.landing_page_url, 'url')}</p>
            </div>
            
            <!-- MRR & Vencimento (Destaque Minimalista) -->
            <div class="col-span-2 flex items-center justify-between border-t border-gray-100 dark:border-slate-800 pt-4 mt-2">
                <div>
                    <span class="block text-[10px] font-black uppercase text-slate-500 tracking-widest">Contrato (MRR)</span>
                    <span class="text-xl font-black text-slate-900 dark:text-white">${parseFloat(c.valor_mensal).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })}</span>
                </div>
                <div class="text-right">
                    <span class="block text-[10px] font-black uppercase text-slate-500 tracking-widest">Vencimento</span>
                    <span class="text-xl font-black text-slate-900 dark:text-white">Dia ${c.dia_pagamento || '--'}</span>
                </div>
            </div>

            ${c.obs ? `
            <div class="col-span-2 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-800 p-4 mt-2">
                <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest mb-1">Observações Internas</p>
                <p class="text-slate-700 dark:text-slate-300 whitespace-pre-wrap leading-relaxed">${c.obs}</p>
            </div>` : ''}

            <!-- Integration Config -->
            <div class="col-span-2 mt-4 pt-4 border-t border-gray-100 dark:border-slate-800 space-y-4">
               <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 flex items-center justify-between">
                   Configurações Específicas de Exportação
               </h3>
               
               <div>
                   <label class="block text-[10px] text-slate-400 uppercase tracking-widest font-black mb-1">ID da Conta de Anúncios (Meta)</label>
                   <input type="text" id="clientAdsAccountId" value="${c.ads_account_id || ''}" onblur="saveClientConfig()" class="w-full bg-white dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 p-2 text-sm focus:border-[var(--theme-color)] dark:focus:border-[var(--theme-color)] outline-none rounded-lg text-slate-700 dark:text-slate-300 transition" placeholder="Ex: act_123456789">
               </div>
               
               <div>
                   <label class="block text-[10px] text-slate-400 uppercase tracking-widest font-black mb-1">Modelo da Mensagem (WhatsApp)</label>
                   <textarea id="clientWppTemplate" rows="4" onblur="saveClientConfig()" class="w-full bg-white dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 p-2 text-sm focus:border-[var(--theme-color)] dark:focus:border-[var(--theme-color)] outline-none resize-none rounded-lg text-slate-700 dark:text-slate-300 transition" placeholder="Use {nome}, {periodo}, {metricas}">${c.whatsapp_template || ''}</textarea>
                   <p class="text-[9px] text-slate-400 tracking-wider mt-1 font-bold">Tags gerais: {nome}, {periodo}, {metricas}, {nota_recente}. Específicas: {gasto}, {leads}, {cpl}, etc. Digite "{" para ver todas.</p>
               </div>

               <div class="flex items-center justify-between mt-2">
                   <span class="text-[10px] text-slate-400">Salvo automaticamente ao sair do campo.</span>
                   <button id="btnSaveConfig" onclick="saveClientConfig()" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 hover:border-[var(--theme-color)] dark:hover:border-[var(--theme-color)] text-slate-700 dark:text-slate-300 text-[10px] font-bold uppercase tracking-wider rounded-lg transition-colors">
                       Salvar Configurações
                   </button>
               </div>
            </div>
        </div>
    `;

    setTimeout(initWppAutocomplete, 10);
}

function initWppAutocomplete() {
    const textarea = document.getElementById('clientWppTemplate');
    if (!textarea) return;

    let dropdown = document.getElementById('wppTagDropdown');
    if (!dropdown) {
        dropdown = document.createElement('div');
        dropdown.id = 'wppTagDropdown';
        dropdown.className = 'hidden absolute z-50 bg-white dark:bg-[#1e293b] border border-gray-200 dark:border-slate-700 rounded-lg shadow-xl overflow-hidden mt-1 text-sm text-slate-700 dark:text-slate-300 w-48';
        textarea.parentNode.style.position = 'relative';
        textarea.parentNode.appendChild(dropdown);
    }

    const tags = [
        { tag: 'nome', desc: 'Nome do Cliente' },
        { tag: 'periodo', desc: 'Período do Filtro' },
        { tag: 'metricas', desc: 'Resumo Geral de Métricas' },
        { tag: 'nota_recente', desc: 'Última Anotação' }
    ];

    // Inject all individual metrics dynamically
    if (typeof availableMetrics !== 'undefined') {
        availableMetrics.forEach(m => {
            tags.push({ tag: m.id, desc: `Métrica: ${m.label}` });
        });
    }

    textarea.addEventListener('input', function (e) {
        const cursorPosition = this.selectionStart;
        const textBeforeCursor = this.value.substring(0, cursorPosition);

        // Match a '{' followed by letters
        const match = textBeforeCursor.match(/\{([a-zA-Z_]*)$/);

        if (match) {
            const search = match[1].toLowerCase();
            const filteredTags = tags.filter(t => t.tag.includes(search));

            if (filteredTags.length > 0) {
                dropdown.innerHTML = filteredTags.map(t =>
                    `<div class="px-3 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors" onclick="insertWppTag('${t.tag}')">
                        <span class="font-bold text-[var(--theme-color)]">{${t.tag}}</span>
                        <span class="block text-[10px] text-slate-500">${t.desc}</span>
                    </div>`
                ).join('');
                dropdown.classList.remove('hidden');

                window.insertWppTag = function (selectedTag) {
                    const beforeMatch = textBeforeCursor.substring(0, textBeforeCursor.lastIndexOf('{'));
                    const afterCursor = textarea.value.substring(cursorPosition);
                    textarea.value = beforeMatch + '{' + selectedTag + '} ' + afterCursor;
                    dropdown.classList.add('hidden');
                    textarea.focus();
                    saveClientConfig();
                };
            } else {
                dropdown.classList.add('hidden');
            }
        } else {
            dropdown.classList.add('hidden');
        }
    });

    textarea.addEventListener('blur', () => {
        setTimeout(() => dropdown.classList.add('hidden'), 200);
    });
}
