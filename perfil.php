<?php
// PMDCRM/perfil.php
require_once 'src/auth.php';
require_login();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="js/theme-loader.js"></script>
    <title>PMDCRM - Meu Perfil</title>
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 dark:bg-[#0f172a] text-slate-900 dark:text-slate-100 pb-20 md:pb-0 md:pl-64 transition-colors duration-300">

    <?php include 'nav.php'; ?>

    <main class="p-4 md:p-8">
        <header class="mb-8">
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Meu Perfil</h1>
            <p class="text-slate-500 dark:text-slate-400">Gerencie suas informações pessoais</p>
        </header>

        <div class="max-w-2xl bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-8">
            <form id="profileForm" class="space-y-6">
                
                <!-- Foto de Perfil -->
                <div class="flex items-center space-x-6">
                    <div class="shrink-0 relative">
                        <img id="previewPhoto" class="h-24 w-24 object-cover rounded-full border-2 border-slate-200" src="<?= !empty($_SESSION['user_foto']) ? htmlspecialchars($_SESSION['user_foto']) : 'https://ui-avatars.com/api/?name='.urlencode($_SESSION['user_nome']).'&background=random' ?>" alt="Foto atual">
                        <label for="fotoUpload" class="absolute bottom-0 right-0 bg-blue-600 text-white p-1 rounded-full cursor-pointer hover:bg-blue-700 shadow-sm border-2 border-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </label>
                        <input type="file" id="fotoUpload" accept="image/*" class="hidden">
                        <input type="hidden" name="foto_perfil" id="fotoPerfilUrl">
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-slate-900 dark:text-white">Sua Foto</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Clique no ícone da câmera para alterar.</p>
                    </div>
                </div>

                <!-- Campos de Texto -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nome Completo</label>
                    <input type="text" name="nome" id="userName" required class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email (Login)</label>
                    <input type="email" value="<?= $_SESSION['user_email'] ?? '' ?>" disabled class="w-full px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-500 dark:text-slate-400 cursor-not-allowed">
                    <p class="text-xs text-slate-400 mt-1">O email não pode ser alterado.</p>
                </div>

                <hr class="border-slate-100 dark:border-slate-700">

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nova Senha</label>
                    <input type="password" name="nova_senha" placeholder="Deixe em branco para manter a atual" class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium shadow-lg shadow-blue-500/30 transition">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </main>

    <!-- Settings Drawer -->
    <?php include 'components/settings_drawer.php'; ?>
    <script src="js/settings.js?v=<?= time() ?>"></script>

    <script>
        // Carregar dados (simulado por enquanto, idealmente viria de um endpoint 'me')
        // Como o PHP session tem o nome, poderíamos injetar, mas vamos fazer limpo via JS se tivermos endpoint, ou usar o que está na sessão PHP.
        // O endpoint list_usuarios não retorna o logado especificamente.
        // Vamos assumir que o nome está na sessão PHP e injetar no value, ou criar um 'api/me.php'. 
        // Para simplificar, vou injetar via PHP.
        
        document.getElementById('userName').value = "<?= $_SESSION['user_nome'] ?? '' ?>";
        // Foto de perfil na sessão? Se não tiver, usar placeholder.
        
        const photoInput = document.getElementById('fotoUpload');
        const photoPreview = document.getElementById('previewPhoto');
        const photoUrlField = document.getElementById('fotoPerfilUrl');

        // Upload de Imagem
        photoInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('file', file);

            // Preview imediato
            const reader = new FileReader();
            reader.onload = (e) => photoPreview.src = e.target.result;
            reader.readAsDataURL(file);

            try {
                // Upload
                const res = await fetch('api/upload_image.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                
                if (res.ok) {
                    photoUrlField.value = data.url;
                } else {
                    alert('Erro no upload: ' + data.error);
                }
            } catch (err) {
                console.error(err);
                alert('Erro de conexão ao enviar imagem.');
            }
        });

        // Submit do Formulário
        document.getElementById('profileForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            try {
                const res = await fetch('api/update_profile.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                
                if (res.ok) {
                    alert('Perfil atualizado com sucesso!');
                    location.reload(); // Recarrega para atualizar nome na navbar se tiver mudado
                } else {
                    const err = await res.json();
                    alert(err.error || 'Erro ao atualizar');
                }
            } catch (error) {
                alert('Erro de conexão');
            }
        });
    </script>
</body>
</html>
