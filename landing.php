<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PMDCRM - Sistema de Gestão de Clientes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
    <meta name="google-site-verification" content="oIR6JINIY1SHn3GnesqHTh0aueaeo1hshbow3uo0vOE" />
</head>
<body class="bg-slate-50 text-slate-800 flex flex-col min-h-screen">
    
    <!-- Navbar -->
    <header class="bg-white border-b border-gray-200 py-4 px-6 md:px-12 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <div class="bg-blue-600 w-8 h-8 rounded-lg flex items-center justify-center text-white font-bold">P</div>
            <span class="text-xl font-bold tracking-tight text-slate-900">PMDCRM</span>
        </div>
        <div>
            <a href="/login" class="text-slate-600 hover:text-blue-600 font-medium mr-4">Entrar</a>
            <a href="/register" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition">Cadastre-se</a>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="flex-grow flex flex-col items-center justify-center text-center px-4 py-20">
        <h1 class="text-4xl md:text-6xl font-extrabold text-slate-900 mb-6 tracking-tight">
            Gestão inteligente com <span class="text-blue-600">PMDCRM</span>
        </h1>
        <p class="text-lg md:text-xl text-slate-600 max-w-2xl mb-10 leading-relaxed">
            O PMDCRM é uma plataforma completa de CRM voltada para otimizar o acompanhamento de leads, unificar o atendimento e escalar os resultados da sua equipe de vendas. Conecte-se com clientes e acompanhe seu pipeline em tempo real.
        </p>
        
        <div class="flex gap-4">
            <a href="/login" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-bold text-lg shadow-lg shadow-blue-500/30 transition transform hover:-translate-y-1">
                Acessar o Sistema
            </a>
            <a href="/politica_privacidade" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 px-8 py-3 rounded-lg font-bold text-lg shadow-sm transition">
                Ler Políticas
            </a>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-8 text-center text-slate-500 text-sm">
        <p>&copy; <?php echo date('Y'); ?> PMDCRM. Todos os direitos reservados.</p>
        <div class="mt-2 space-x-4">
            <a href="/termos_servico" class="hover:text-blue-600 transition">Termos de Serviço</a>
            <a href="/politica_privacidade" class="hover:text-blue-600 transition">Política de Privacidade</a>
        </div>
    </footer>

</body>
</html>
