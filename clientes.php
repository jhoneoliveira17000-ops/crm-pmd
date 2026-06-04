<?php
require_once 'src/auth.php';
require_login();

$page_title = "PMDCRM - Clientes";
$body_class = "pb-20 md:pb-0 md:pl-64 bg-[var(--surface-1)]";
include 'includes/header.php';
include 'nav.php';
?>

    <main class="p-4 md:p-8">
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[var(--text-1)] tracking-tight">Clientes</h1>
                <p class="text-[var(--text-3)] mt-1">Gerencie sua carteira e contratos</p>
            </div>
            <div class="flex items-center gap-2 md:gap-4">
                <?php include 'header_icons.php'; ?>
                
                <button onclick="openModal('new')" class="ds-btn ds-btn-primary btn-spring">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Novo Cliente
                </button>
            </div>
        </header>

        <!-- Tabs Filter -->
        <div class="ds-tabs mb-6 w-fit">
            <button onclick="filterClients('ativo')" id="tab-ativo" class="ds-tab active">Ativos</button>
            <button onclick="filterClients('inativo')" id="tab-inativo" class="ds-tab">Inativos</button>
        </div>

        <!-- Client List -->
        <div class="ds-card overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="ds-table">
                    <thead>
                        <tr>
                            <th>Empresa / Contrato</th>
                            <th class="hidden md:table-cell">Responsável</th>
                            <th>Financeiro/Status</th>
                            <th class="text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="clientsTableBody">
                      <!-- Content filled by JS -->
                    </tbody>
                </table>
            </div>
             <div id="loading" class="hidden p-12 text-center">
                <svg class="animate-spin h-8 w-8 mx-auto text-[var(--theme-color)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <p class="mt-3 text-xs text-[var(--text-3)]">Carregando clientes...</p>
             </div>
             <div id="emptyState" class="ds-empty hidden">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <h3>Nenhum cliente encontrado</h3>
                <p>Crie um novo cliente para começar a gerenciar sua carteira e contratos.</p>
             </div>
        </div>

    </main>
    
    <!-- Modal Cliente -->
    <div id="clientModal" class="ds-overlay flex items-center justify-center p-4">
        <div class="ds-modal bg-[var(--surface-0)] rounded-[var(--radius-xl)] shadow-2xl w-full max-w-4xl border border-[var(--border-1)] flex flex-col" id="modalContent">
            <div class="p-6 border-b border-[var(--border-1)] flex justify-between items-center sticky top-0 bg-[var(--surface-0)] z-10">
                <h3 class="text-xl font-bold text-[var(--text-1)] flex items-center gap-2">
                    <span class="w-2 h-8 bg-[var(--theme-color)] rounded-full"></span>
                    <span id="modalTitle">Novo Cliente</span>
                </h3>
                <button onclick="closeModal()" class="text-[var(--text-3)] hover:text-rose-500 transition p-2 hover:bg-[var(--surface-2)] rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form id="clientForm" class="p-6 space-y-6">
                <input type="hidden" name="id" id="client_id">
                
                <!-- Grid Layout for Form -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Col 1: Basic Info -->
                    <div class="space-y-4">
                        <h4 class="text-xs uppercase text-[var(--text-3)] font-bold tracking-wider mb-4 border-b border-[var(--border-1)] pb-2">Dados da Empresa</h4>
                        
                        <div>
                            <label class="ds-label">Nome da Empresa *</label>
                            <input type="text" name="nome_empresa" id="nome_empresa" required class="ds-input">
                        </div>
                         
                         <div class="grid grid-cols-2 gap-4">
                             <div>
                                <label class="ds-label">CNPJ</label>
                                <input type="text" name="cnpj" id="cnpj" class="ds-input">
                             </div>
                             <div>
                                <label class="ds-label">Responsável</label>
                                <input type="text" name="nome_responsavel" id="nome_responsavel" class="ds-input">
                             </div>
                         </div>

                         <div class="grid grid-cols-2 gap-4">
                             <div>
                                <label class="ds-label">Email</label>
                                <input type="email" name="email" id="email" class="ds-input">
                             </div>
                             <div>
                                <label class="ds-label">WhatsApp</label>
                                <input type="text" name="whatsapp" id="whatsapp" class="ds-input">
                             </div>
                         </div>
                    </div>

                    <!-- Col 2: Contract Info -->
                    <div class="space-y-4">
                         <h4 class="text-xs uppercase text-[var(--text-3)] font-bold tracking-wider mb-4 border-b border-[var(--border-1)] pb-2">Contrato & Financeiro</h4>
                         
                         <div class="grid grid-cols-2 gap-4">
                             <div>
                                <label class="ds-label">Valor Mensal (R$)</label>
                                <input type="number" step="0.01" name="valor_mensal" id="valor_mensal" class="ds-input">
                             </div>
                             <div>
                                <label class="ds-label">Dia Vencimento</label>
                                <input type="number" min="1" max="31" name="dia_vencimento" id="dia_vencimento" class="ds-input">
                             </div>
                         </div>

                         <div class="grid grid-cols-2 gap-4">
                             <div>
                                <label class="ds-label">Início Contrato</label>
                                <input type="date" name="data_inicio" id="data_inicio" class="ds-input white-calendar-icon">
                             </div>
                             <div>
                                <label class="ds-label">Duração (Meses)</label>
                                <select name="duracao_contrato" id="duracao_contrato" class="ds-input">
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
                            <label class="ds-label">Status Contrato</label>
                            <select name="status_contrato" id="status_contrato" class="ds-input">
                                <option value="ativo">Ativo</option>
                                <option value="pendente">Pendente Assinatura</option>
                                <option value="suspenso">Suspenso</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                         </div>
                    </div>
                </div>
                
                <!-- Additional Info -->
                <div class="space-y-4 pt-4 border-t border-[var(--border-1)]">
                     <h4 class="text-xs uppercase text-[var(--text-3)] font-bold tracking-wider">Informações Adicionais</h4>
                     
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                          <!-- Col 1 -->
                          <div class="space-y-4">
                              <div class="grid grid-cols-2 gap-4">
                                  <div>
                                     <label class="ds-label">Nicho</label>
                                     <input type="text" name="nicho" id="nicho" class="ds-input">
                                  </div>
                                  <div>
                                     <label class="ds-label">Origem</label>
                                     <input type="text" name="origem" id="origem" class="ds-input">
                                  </div>
                              </div>
                              <div>
                                 <label class="ds-label">Produto / Serviço</label>
                                 <input type="text" name="produto_servico" id="produto_servico" class="ds-input">
                              </div>
                              <div class="grid grid-cols-2 gap-4">
                                  <div>
                                     <label class="ds-label">Instagram (@)</label>
                                     <input type="text" name="instagram" id="instagram" class="ds-input">
                                  </div>
                                  <div>
                                     <label class="ds-label">Site / LP</label>
                                     <input type="url" name="landing_page_url" id="landing_page_url" placeholder="https://" class="ds-input">
                                  </div>
                              </div>
                          </div>
                          
                          <!-- Col 2: Endereço & Obs -->
                          <div class="space-y-4">
                              <div>
                                 <label class="ds-label">Endereço Completo</label>
                                 <input type="text" name="endereco" id="endereco" class="ds-input">
                              </div>
                              <div class="grid grid-cols-3 gap-4">
                                  <div class="col-span-2">
                                     <label class="ds-label">Cidade</label>
                                     <input type="text" name="cidade" id="cidade" class="ds-input">
                                  </div>
                                  <div>
                                     <label class="ds-label">Estado</label>
                                     <input type="text" name="estado" id="estado" maxlength="2" placeholder="SP" class="ds-input uppercase">
                                  </div>
                              </div>
                              <div>
                                 <label class="ds-label">Observações Principais</label>
                                 <textarea name="obs" id="obs" rows="2" class="ds-input resize-none"></textarea>
                              </div>
                          </div>
                      </div>
                 </div>
                 
                  <!-- Link Folder -->
                  <div>
                     <label class="ds-label">Link Pasta Cliente (Drive/Dropbox)</label>
                     <input type="url" name="link_pasta" id="link_pasta" class="ds-input">
                  </div>

                 <!-- Actions -->
                 <div class="pt-6 border-t border-[var(--border-1)] flex justify-end gap-3">
                     <button type="button" onclick="closeModal()" class="ds-btn ds-btn-secondary btn-spring">Cancelar</button>
                     <button type="submit" class="ds-btn ds-btn-primary btn-spring" id="btnSave">Salvar Cliente</button>
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
                showToast('Erro ao carregar clientes', 'error');
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
                 const dueDate = client.dia_pagamento || client.dia_vencimento;
                 const folderUrl = client.pasta_drive_url || client.link_pasta;
                 const phone = client.telefone || client.whatsapp;

                 const diffDays = getDaysUntilDue(dueDate);
                 const statusLabel = formatStatus(client.status_contrato);
                 const avatar = client.foto_perfil || null;

                 table.innerHTML += `
                    <tr onclick="openClientDrawer(${client.id})" class="cursor-pointer border-b border-[var(--border-1)] relative ds-animate-spring group">
                        <td class="p-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-lg bg-[var(--surface-2)] flex-shrink-0 flex items-center justify-center overflow-hidden border border-[var(--border-1)]">
                                    ${avatar 
                                        ? `<img src="${avatar}" class="w-full h-full object-cover">` 
                                        : `<span class="text-xl font-bold text-[var(--text-3)]">${client.nome_empresa.charAt(0)}</span>`
                                    }
                                </div>
                                <div>
                                    <div class="font-bold text-[var(--text-1)] text-base">${client.nome_empresa || 'Sem Nome'}</div>
                                    <div class="text-xs text-[var(--text-3)] flex items-center gap-1 mt-0.5">
                                        CNPJ: ${client.cnpj || 'N/A'}
                                        ${folderUrl ? `<a href="${folderUrl}" target="_blank" onclick="event.stopPropagation()" class="bg-[var(--brand-subtle)] text-[var(--brand-text)] hover:bg-[var(--brand-light)] px-1.5 py-0.5 rounded ml-2 transition flex items-center gap-1" title="Abrir Pasta"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path></svg> Pasta</a>` : ''}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 hidden md:table-cell">
                            <div class="text-sm font-medium text-[var(--text-2)]">${client.nome_responsavel || '-'}</div>
                            <div class="text-xs text-[var(--text-3)]">${phone || '-'}</div>
                        </td>
                        <td class="p-4">
                            <div class="flex flex-col gap-1">
                                <span class="text-sm font-mono font-bold text-[var(--text-1)]">R$ ${parseFloat(client.valor_mensal || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</span>
                                <div class="flex items-center gap-2 text-[10px] uppercase font-bold mt-1">
                                     <span class="${getStatusBadgeClass(client.status_contrato)}">${statusLabel}</span>
                                     <span class="ds-badge ds-badge-neutral ${diffDays < 5 ? 'text-rose-500 bg-rose-500/10' : 'text-[var(--text-3)]'}">Vence dia ${dueDate || '?'}</span>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-right">
                             <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition">
                                <button onclick="event.stopPropagation(); editClient(${client.id})" class="p-2 hover:bg-[var(--brand-subtle)] text-[var(--brand-text)] rounded-lg transition" title="Editar">
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

            btnAtivo.classList.remove('active');
            btnInativo.classList.remove('active');

            if (status === 'ativo') {
                btnAtivo.classList.add('active');
                renderClients(clientsData.filter(c => c.status_contrato === 'ativo' || c.status_contrato === 'pendente'));
            } else {
                btnInativo.classList.add('active');
                renderClients(clientsData.filter(c => c.status_contrato === 'cancelado' || c.status_contrato === 'suspenso'));
            }
        }

        // Helpers
        function getDaysUntilDue(day) {
            if (!day) return 0;
            const today = new Date().getDate();
            if (day < today) return 30 - (today - day);
            return day - today;
        }

        function getStatusBadgeClass(status) {
            if (!status) return 'ds-badge ds-badge-neutral';
            const map = {
                'ativo': 'ds-badge ds-badge-success',
                'pendente': 'ds-badge ds-badge-warning',
                'suspenso': 'ds-badge ds-badge-brand',
                'cancelado': 'ds-badge ds-badge-danger'
            };
            return map[status] || 'ds-badge ds-badge-neutral';
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
            modal.classList.add('active');
            modalContent.classList.add('active');
            
            if(mode === 'edit' && data) {
                document.getElementById('modalTitle').innerText = 'Editar Cliente';
                document.getElementById('client_id').value = data.id;
                document.getElementById('nome_empresa').value = data.nome_empresa;
                document.getElementById('cnpj').value = data.cnpj || ''; 
                document.getElementById('nome_responsavel').value = data.nome_responsavel;
                document.getElementById('email').value = data.email;
                document.getElementById('whatsapp').value = data.telefone || ''; 
                document.getElementById('valor_mensal').value = data.valor_mensal;
                document.getElementById('dia_vencimento').value = data.dia_pagamento || ''; 
                document.getElementById('data_inicio').value = data.data_inicio_contrato || ''; 
                document.getElementById('link_pasta').value = data.pasta_drive_url || ''; 
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
            modal.classList.remove('active');
            modalContent.classList.remove('active');
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
                    showToast('Cliente excluído com sucesso!', 'success');
                } else {
                    showToast('Erro: ' + (data.error || 'Falha ao excluir'), 'error');
                }
            } catch(e) { 
                console.error(e);
                showToast('Erro de conexão', 'error');
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
                    showToast('Cliente salvo com sucesso!', 'success');
                } else {
                    showToast('Erro: ' + (result.message || 'Desconhecido'), 'error');
                }
            } catch(e) {
                console.error(e);
                showToast('Erro de conexão', 'error');
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
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('action') === 'new') {
            openModal('new');
        }
    </script>
<?php include 'includes/footer.php'; ?>
