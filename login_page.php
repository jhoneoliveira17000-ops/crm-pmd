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

        <div class="mt-6">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-200 dark:border-slate-700"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white dark:bg-slate-800 text-slate-500">Ou continue com</span>
                </div>
            </div>

            <div class="mt-6">
                <a href="api/google_login.php" class="w-full flex items-center justify-center gap-2 px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition transform font-medium">
                    <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24">
                        <path d="M12.0003 4.75C13.7703 4.75 15.3553 5.36002 16.6053 6.54998L20.0303 3.125C17.9502 1.19 15.2353 0 12.0003 0C7.31028 0 3.25527 2.69 1.25024 6.65L5.26524 9.765C6.25524 6.79 9.10028 4.75 12.0003 4.75Z" fill="#EA4335" />
                        <path d="M23.49 12.275C23.49 11.49 23.415 10.73 23.3 10H12V14.51H18.47C18.18 15.99 17.34 17.25 16.08 18.1L20.18 21.29C22.57 19.09 23.49 15.9 23.49 12.275Z" fill="#4285F4" />
                        <path d="M5.26498 14.235C5.02498 13.505 4.86997 12.74 4.86997 11.95C4.86997 11.16 5.01497 10.395 5.26498 9.665L1.25498 6.55C0.459976 8.13 0 9.96 0 11.95C0 13.94 0.459976 15.77 1.25498 17.35L5.26498 14.235Z" fill="#FBBC05" />
                        <path d="M12.0004 24.0001C15.2404 24.0001 17.9654 22.935 20.1804 21.29L16.0804 18.1C14.8704 18.895 13.5254 19.32 12.0004 19.32C9.10037 19.32 6.26036 17.26 5.26538 14.235L1.25537 17.35C3.25537 21.31 7.31037 24.0001 12.0004 24.0001Z" fill="#34A853" />
                    </svg>
                    Google
                </a>
            </div>
            
            <div class="mt-4 text-center text-xs text-slate-500 max-w-sm mx-auto">
                Ao entrar ou cadastrar-se, você concorda com nossos <br>
                <a href="/termos_servico" class="text-blue-600 hover:underline">Termos de Serviço</a> e <a href="/politica_privacidade" class="text-blue-600 hover:underline">Política de Privacidade</a>.
            </div>
        </div>

        <p class="text-center mt-6 text-sm text-slate-600 dark:text-slate-400">
            Não tem uma conta? <a href="/register" class="text-blue-600 dark:text-blue-400 font-medium hover:underline">Criar agora</a>
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
                    // Admin vai para painel de admin, gestores para dashboard normal
                    if (result.user && result.user.role === 'admin') {
                        window.location.href = '/admin_dashboard';
                    } else {
                        window.location.href = '/dashboard';
                    }
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
