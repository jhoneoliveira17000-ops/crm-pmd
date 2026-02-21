<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - PMDCRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/design-system.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800">

    <?php include 'nav.php'; ?>

    <main class="md:ml-64 p-4 md:p-8 transition-all duration-300">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-slate-800 mb-2">Configurações</h1>
            <p class="text-slate-500 mb-8">Gerencie suas preferências e ajustes do sistema.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Profile Settings -->
                <a href="perfil.php" class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition group">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-800 mb-1">Meu Perfil</h3>
                    <p class="text-sm text-slate-500">Alterar foto, senha e informações pessoais.</p>
                </a>

                <!-- System Appearance (Placeholder) -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition group cursor-pointer" onclick="alert('Em breve: Tema Escuro/Claro')">
                    <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-800 mb-1">Aparência</h3>
                    <p class="text-sm text-slate-500">Personalizar cores e tema do sistema.</p>
                </div>

                <!-- Notifications (Placeholder) -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition group cursor-pointer" onclick="alert('Em breve: Configurar notificações')">
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-800 mb-1">Notificações</h3>
                    <p class="text-sm text-slate-500">Gerenciar alertas e emails.</p>
                </div>

                 <!-- Users (Admin only - Placeholder) -->
                 <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition group cursor-pointer" onclick="alert('Area restrita para admins (Em breve)')">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-800 mb-1">Usuários</h3>
                    <p class="text-sm text-slate-500">Gerenciar acesso da equipe.</p>
                </div>

            </div>
        </div>
    </main>

</body>
</html>
