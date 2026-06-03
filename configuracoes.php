<?php
// PMDCRM/configuracoes.php
$page_title = "Configurações - PMDCRM";
$body_class = "md:pl-64";
include 'includes/header.php';
include 'nav.php';
?>

    <main class="p-4 md:p-8 transition-colors duration-300">
        <div class="max-w-4xl mx-auto">
            <header class="mb-8">
                <h1 class="text-3xl font-bold text-slate-800 dark:text-white mb-2">Configurações</h1>
                <p class="text-slate-500 dark:text-slate-400">Gerencie suas preferências e ajustes do sistema.</p>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Profile Settings -->
                <a href="perfil.php" class="bg-white dark:bg-[#1e293b] p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 hover:shadow-md transition group">
                    <div class="w-12 h-12 bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-800 dark:text-white mb-1">Meu Perfil</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-normal">Alterar foto, senha e informações pessoais.</p>
                </a>

                <!-- System Appearance -->
                <div class="bg-white dark:bg-[#1e293b] p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 hover:shadow-md transition group cursor-pointer" onclick="alert('O tema escuro/claro pode ser alternado diretamente no rodapé do menu lateral.')">
                    <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-950/30 text-indigo-600 dark:text-indigo-400 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-800 dark:text-white mb-1">Aparência</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-normal">Personalizar cores e tema do sistema.</p>
                </div>

                <!-- Notifications -->
                <div class="bg-white dark:bg-[#1e293b] p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 hover:shadow-md transition group cursor-pointer" onclick="alert('Configuração de alertas e notificações por e-mail em desenvolvimento.')">
                    <div class="w-12 h-12 bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-800 dark:text-white mb-1">Notificações</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-normal">Gerenciar alertas e envios de e-mails.</p>
                </div>

                <!-- Users (Admin only) -->
                <?php if (is_admin()): ?>
                <a href="usuarios.php" class="bg-white dark:bg-[#1e293b] p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 hover:shadow-md transition group">
                    <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-800 dark:text-white mb-1">Gerenciar Equipe</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-normal">Cadastrar e controlar acessos de usuários do time.</p>
                </a>
                <?php else: ?>
                <div class="bg-slate-100/50 dark:bg-slate-800/30 p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-850 opacity-60">
                    <div class="w-12 h-12 bg-slate-200 dark:bg-slate-800 text-slate-400 dark:text-slate-650 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-400 dark:text-slate-500 mb-1">Equipe</h3>
                    <p class="text-sm text-slate-400 dark:text-slate-500 font-normal">Apenas administradores podem gerenciar a equipe.</p>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>
