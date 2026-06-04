<?php
$page_title = "PMDCRM - Login";
$body_class = "min-h-screen flex flex-col md:flex-row transition-colors duration-300 bg-[var(--surface-1)]";
include 'includes/header.php';
?>
    <!-- LEFT PANEL (hidden on mobile) -->
    <div class="hidden md:flex md:w-[45%] lg:w-[40%] bg-stone-950 dark:bg-black text-white relative flex-col justify-between p-12 overflow-hidden border-r border-stone-800">
        <!-- background grids & pattern -->
        <div class="absolute inset-0 bg-gradient-to-br from-stone-900 via-stone-950 to-black opacity-95 z-0"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,var(--theme-color,rgba(0,191,36,0.15)),transparent_50%)] z-0"></div>
        <div class="absolute inset-0 z-0 opacity-[0.03]" style="background-image: radial-gradient(var(--theme-color) 1px, transparent 1px); background-size: 24px 24px;"></div>
        
        <!-- logo & brand -->
        <div class="relative z-10 flex items-center gap-3">
            <?php if (!empty($companyLogo)): ?>
                <img src="<?= e($companyLogo) ?>" alt="Logo" class="max-h-10 object-contain">
            <?php else: ?>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-xl font-bold flex-shrink-0" style="background:var(--brand)">
                    <?= strtoupper(substr($companyName, 0, 1)) ?>
                </div>
                <span class="text-lg font-bold tracking-tight text-white"><?= e($companyName) ?></span>
            <?php endif; ?>
        </div>

        <!-- center insight/value phrase -->
        <div class="relative z-10 my-auto max-w-md">
            <span class="text-xs uppercase tracking-widest text-[var(--brand)] font-semibold mb-3 block">CRM White-Label</span>
            <h2 class="text-3xl font-extrabold tracking-tight text-white leading-tight mb-4">
                De lead a receita. Tudo em um painel.
            </h2>
            <p class="text-stone-400 text-sm leading-relaxed">
                Gestão inteligente de leads do Meta Ads, pipeline visual Kanban, dashboard interativo e controle financeiro completo. Tudo integrado sob a sua marca.
            </p>
        </div>

        <!-- footer branding info -->
        <div class="relative z-10 text-xs text-stone-500">
            &copy; <?= date('Y') ?> <?= e($companyName) ?>. Todos os direitos reservados.
        </div>
    </div>

    <!-- RIGHT PANEL (auth form) -->
    <div class="flex-1 flex flex-col justify-center items-center p-8 md:p-16 min-h-screen relative bg-[var(--surface-0)]">
        <div class="w-full max-w-sm">
            <div class="flex items-center justify-between mb-8 md:hidden">
                <!-- Mobile Logo -->
                <div class="flex items-center gap-2">
                    <?php if (!empty($companyLogo)): ?>
                        <img src="<?= e($companyLogo) ?>" alt="Logo" class="max-h-8 object-contain">
                    <?php else: ?>
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-sm font-bold flex-shrink-0" style="background:var(--brand)">
                            <?= strtoupper(substr($companyName, 0, 1)) ?>
                        </div>
                        <span class="text-sm font-bold tracking-tight text-[var(--text-1)]"><?= e($companyName) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mb-8">
                <h1 class="text-3xl font-bold text-[var(--text-1)] tracking-tight mb-2">Bem-vindo de volta</h1>
                <p class="text-[var(--text-3)] text-sm">Acesse sua conta para continuar.</p>
            </div>

            <form id="loginForm" class="space-y-5">
                <div>
                    <label class="ds-label">Email</label>
                    <input type="email" name="email" required placeholder="exemplo@empresa.com" class="ds-input focus:ring-2 focus:ring-brand/20">
                </div>
                
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="ds-label mb-0">Senha</label>
                        <a href="#" onclick="showToast('Recuperação de senha não configurada. Entre em contato com o suporte.', 'info'); return false;" class="text-xs font-semibold text-[var(--brand)] hover:underline">Esqueci minha senha</a>
                    </div>
                    <input type="password" name="senha" required placeholder="••••••••" class="ds-input focus:ring-2 focus:ring-brand/20">
                </div>

                <button type="submit" class="ds-btn ds-btn-primary w-full py-3.5 btn-spring font-bold tracking-wide">
                    Entrar na Conta
                </button>
            </form>

            <div class="mt-6">
                <div class="relative flex items-center justify-center py-2">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-[var(--border-1)]"></div>
                    </div>
                    <span class="relative px-3 bg-[var(--surface-0)] text-xs text-[var(--text-3)] uppercase tracking-wider">Ou continue com</span>
                </div>

                <div class="mt-5">
                    <a href="api/google_login.php" class="w-full flex items-center justify-center gap-3 px-4 py-3 border border-[var(--border-2)] rounded-xl shadow-xs bg-[var(--surface-0)] text-[var(--text-2)] hover:bg-[var(--surface-2)] transition duration-200 font-semibold text-sm">
                        <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24">
                            <path d="M12.0003 4.75C13.7703 4.75 15.3553 5.36002 16.6053 6.54998L20.0303 3.125C17.9502 1.19 15.2353 0 12.0003 0C7.31028 0 3.25527 2.69 1.25024 6.65L5.26524 9.765C6.25524 6.79 9.10028 4.75 12.0003 4.75Z" fill="#EA4335" />
                            <path d="M23.49 12.275C23.49 11.49 23.415 10.73 23.3 10H12V14.51H18.47C18.18 15.99 17.34 17.25 16.08 18.1L20.18 21.29C22.57 19.09 23.49 15.9 23.49 12.275Z" fill="#4285F4" />
                            <path d="M5.26498 14.235C5.02498 13.505 4.86997 12.74 4.86997 11.95C4.86997 11.16 5.01497 10.395 5.26498 9.665L1.25498 6.55C0.459976 8.13 0 9.96 0 11.95C0 13.94 0.459976 15.77 1.25498 17.35L5.26498 14.235Z" fill="#FBBC05" />
                            <path d="M12.0004 24.0001C15.2404 24.0001 17.9654 22.935 20.1804 21.29L16.0804 18.1C14.8704 18.895 13.5254 19.32 12.0004 19.32C9.10037 19.32 6.26036 17.26 5.26538 14.235L1.25537 17.35C3.25537 21.31 7.31037 24.0001 12.0004 24.0001Z" fill="#34A853" />
                        </svg>
                        Conta do Google
                    </a>
                </div>
                
                <div class="mt-6 text-center text-xs text-[var(--text-3)] max-w-sm mx-auto leading-relaxed">
                    Ao entrar ou cadastrar-se, você concorda com nossos <br>
                    <a href="/termos_servico" class="text-[var(--brand)] hover:underline font-semibold">Termos de Serviço</a> e <a href="/politica_privacidade" class="text-[var(--brand)] hover:underline font-semibold">Política de Privacidade</a>.
                </div>
            </div>

            <p class="text-center mt-8 text-sm text-[var(--text-3)]">
                Não tem uma conta? <a href="/register" class="text-[var(--brand)] font-semibold hover:underline">Criar agora</a>
            </p>
        </div>
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
                    showToast(result.error || 'Erro ao entrar', 'error');
                }
            } catch (err) {
                showToast('Erro de conexão', 'error');
            }
        });
    </script>
<?php include 'includes/footer.php'; ?>
