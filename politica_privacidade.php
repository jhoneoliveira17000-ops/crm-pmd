<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidade - PMDCRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800">
    <div class="max-w-4xl mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold mb-8 text-blue-600">Política de Privacidade</h1>
        <p class="mb-4 text-sm text-slate-500">Última atualização: <?php echo date('d/m/Y'); ?></p>

        <section class="mb-8 prose prose-slate">
            <h2 class="text-xl font-semibold mb-4 text-slate-900">1. Coleta e Uso de Informações</h2>
            <p class="mb-4">Quando você utiliza nossa plataforma ou registra-se utilizando provedores externos (como Google), nós coletamos o seu endereço de e-mail, nome de exibição e foto de perfil. Estes dados são usados exclusivamente para garantir acesso seguro à sua conta, gerir o histórico da aplicação e personalizar a experiência multi-tenant do usuário.</p>

            <h2 class="text-xl font-semibold mb-4 text-slate-900">2. Uso de Dados do Google Workspace (Google Calendar API)</h2>
            <p class="mb-4">O uso das informações recebidas ou transferidas das APIs do Google para qualquer outro aplicativo obedecerá à <strong><a href="https://developers.google.com/terms/api-services-user-data-policy" target="_blank" class="text-blue-600 underline">Política de Dados do Usuário dos Serviços de API do Google</a></strong>, incluindo os requisitos de "Uso Limitado".</p>
            <ul class="list-disc pl-5 mb-4 space-y-2">
                <li><strong>Acesso a Calendário:</strong> Solicitamos acesso ao seu Google Agenda unicamente para visualizar compromissos vinculados aos processos do CRM e sincronizar tarefas ou clientes agendados da nossa plataforma com o seu Google Agenda, de modo a agilizar o fluxo de trabalho do seu time.</li>
                <li><strong>Segurança:</strong> Guardamos os Tokens de Acesso OAuth com técnicas modernas de isolamento e bancos de dados relacionais protegidos. Não compartilhamos suas credenciais do Google, nem os eventos da sua agenda com parceiros, redes de publicidade ou ferramentas de datamining de terceiros. A finalidade do recebimento e trânsito dessa informação é promover a funcionalidade fim do software CRM de negócios e organização.</li>
            </ul>

            <h2 class="text-xl font-semibold mb-4 text-slate-900">3. Comunicação e Compartilhamento (Não-Google)</h2>
            <p class="mb-4">Podemos compartilhar dados genéricos de uso com ferramentas de analítica para aprimoramento interno do software ou utilizar os e-mails registrados para o disparo de informes críticos e alertas de sistema. Jamais venderemos ou alugaremos as suas listas de clientes cadastrados dentro do nosso CRM.</p>

            <h2 class="text-xl font-semibold mb-4 text-slate-900">4. Exclusão de Conta</h2>
            <p class="mb-4">Você pode desvincular as suas contas do Google a qualquer momento nas configurações do PMDCRM (revogando as permissões em <code>https://myaccount.google.com/permissions</code>). Da mesma forma, caso deseje apagar irrevogavelmente suas informações e a de seus leads cadastrados da plataforma, basta contatar nosso suporte técnico ou usar o painel do administrador em caso de encerramento total da licença de software.</p>
        </section>
        
        <div class="mt-12 text-center text-sm text-slate-500 hover:text-blue-600 transition">
            <a href="/login">&larr; Voltar para a página de Login</a>
        </div>
    </div>
</body>
</html>
