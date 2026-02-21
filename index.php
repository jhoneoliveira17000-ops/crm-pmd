<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="js/theme-loader.js"></script>
    <title>PMDCRM - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 flex items-center justify-center h-screen px-4 transition-colors duration-300">

    <div class="w-full max-w-sm bg-white dark:bg-slate-800 rounded-2xl shadow-xl p-8 border border-slate-200 dark:border-slate-700">
        <div class="flex justify-center mb-8">
            <div class="bg-blue-600 w-16 h-16 rounded-xl flex items-center justify-center text-white text-2xl font-bold">
                P
            </div>
        </div>
        
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white text-center mb-2">Bem-vindo de volta</h1>
        <p class="text-slate-500 dark:text-slate-400 text-center mb-8">Acesse sua conta para continuar</p>

        <form id="loginForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email</label>
                <input type="email" name="email" required class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Senha</label>
                <input type="password" name="senha" required class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg shadow-lg shadow-blue-500/30 transition transform active:scale-95">
                Entrar
            </button>
        </form>

        <p class="text-center mt-6 text-sm text-slate-600 dark:text-slate-400">
            Não tem uma conta? <a href="register.php" class="text-blue-600 dark:text-blue-400 font-medium hover:underline">Criar agora</a>
        </p>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            try {
                const res = await fetch('api/login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await res.json();
                
                if (res.ok) {
                    window.location.href = 'dashboard.php';
                } else {
                    alert(result.error || 'Erro ao entrar');
                }
            } catch (err) {
                alert('Erro de conexão');
            }
        });
    </script>
    <script src="js/settings.js"></script>
</body>
</html>
