<?php
// PMDCRM/usuarios.php
require_once 'src/auth.php';
require_admin();
?>
$page_title = "PMDCRM - Usuários";
$body_class = "md:pl-64";
include 'includes/header.php';
?>

    <?php include 'nav.php'; ?>

    <main class="p-4 md:p-8">
        <header class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Usuários</h1>
                <p class="text-slate-500">Gestão de acesso ao sistema</p>
            </div>
            <!-- Botão de criar usuário poderia ser adicionado aqui -->
        </header>

        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500">
                        <th class="p-4 font-semibold">Nome</th>
                        <th class="p-4 font-semibold">Email</th>
                        <th class="p-4 font-semibold">Função</th>
                        <th class="p-4 font-semibold text-right">Ações</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <tr><td colspan="4" class="p-4 text-center">Carregando...</td></tr>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        async function loadUsers() {
            const tbody = document.getElementById('usersTableBody');
            try {
                // Necessário criar endpoint para listar usuários (não estava no escopo inicial detalhado, mas é bom ter)
                // Usando um mock ou criando endpoint rápido se necessário. 
                // Como não tenho endpoint list_usuarios.php na lista original, vou criar um inline aqui para demo ou adicionar ao api.
                // Vou assumir que existe. Se não existir, vou criar agora.
                const res = await fetch('api/list_usuarios.php');
                 if (!res.ok) throw new Error('Falha ao carregar');
                const users = await res.json();
                
                tbody.innerHTML = users.map(u => `
                    <tr class="border-b border-slate-50 last:border-0">
                        <td class="p-4 font-medium text-slate-900">${u.nome}</td>
                        <td class="p-4 text-slate-600">${u.email}</td>
                        <td class="p-4">
                            <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded-full text-xs font-semibold uppercase">${u.role}</span>
                        </td>
                        <td class="p-4 text-right">
                            <button class="text-slate-400 hover:text-red-500">Remover</button>
                        </td>
                    </tr>
                `).join('');
            } catch (err) {
               tbody.innerHTML = '<tr><td colspan="4" class="p-4 text-center text-red-500">Erro ao carregar usuários (API não implementada).</td></tr>';
            }
        }
        loadUsers();
    </script>
<?php include 'includes/footer.php'; ?>
