<?php
// PMDCRM landing page
require_once __DIR__ . '/src/auth.php';
require_once __DIR__ . '/src/db.php';
require_once __DIR__ . '/src/helpers.php';

// Load branding settings from default admin or active tenant to support white label
$themeColor = '#00BF24';
$companyName = 'PMDCRM';
$companyLogo = '';

if (isset($pdo)) {
    $targetUserId = null;
    if (isset($_SESSION['user_id'])) {
        $targetUserId = $_SESSION['user_id'];
    } else {
        try {
            $stmtAdmin = $pdo->query("SELECT id FROM usuarios WHERE role = 'admin' ORDER BY id ASC LIMIT 1");
            $targetUserId = $stmtAdmin->fetchColumn() ?: null;
        } catch (Exception $e) {}
    }

    if ($targetUserId) {
        try {
            $stmtConfig = $pdo->prepare("SELECT key_name, value FROM config WHERE user_id = ?");
            $stmtConfig->execute([$targetUserId]);
            $configs = $stmtConfig->fetchAll(PDO::FETCH_KEY_PAIR);
            
            if (!empty($configs['theme_color'])) {
                $themeColor = $configs['theme_color'];
            }
            if (!empty($configs['company_name'])) {
                $companyName = $configs['company_name'];
            }
            if (!empty($configs['company_logo'])) {
                $companyLogo = $configs['company_logo'];
            }
        } catch (Exception $e) {}
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($companyName) ?> - CRM Inteligente & Automação de Leads</title>
    
    <!-- SEO & Meta -->
    <meta name="description" content="Otimize suas vendas com o <?= e($companyName) ?>. Unifique seus leads do Facebook Ads, acompanhe seu pipeline via Kanban e automatize comunicações instantaneamente.">
    <meta name="theme-color" content="<?= $themeColor ?>">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Design System CSS (Global) -->
    <link rel="stylesheet" href="assets/css/design-system.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            DEFAULT: 'var(--theme-color)',
                            50: 'var(--theme-color)10',
                            100: 'var(--theme-color)20',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Theme Loader to prevent flash -->
    <script>
        (function () {
            const cachedColor = localStorage.getItem('theme_color') || '<?= $themeColor ?>';
            document.documentElement.style.setProperty('--theme-color', cachedColor);
            
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
    
    <style>
        :root {
            --theme-color: <?= $themeColor ?>;
        }
        
        /* Grid overlay background */
        .cyber-grid {
            background-image: 
                linear-gradient(to right, rgba(120, 119, 198, 0.02) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(120, 119, 198, 0.02) 1px, transparent 1px);
            background-size: 3rem 3rem;
        }
        .dark .cyber-grid {
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.015) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.015) 1px, transparent 1px);
        }

        /* Subtle radial background lighting */
        .cyber-radial {
            background: radial-gradient(circle 600px at 50% 200px, rgba(var(--theme-color-rgb, 0, 191, 36), 0.04), transparent 80%);
        }
        
        /* Spring animations */
        @keyframes spring-in {
            0% { transform: scale(0.8) translateY(20px); opacity: 0; }
            70% { transform: scale(1.03) translateY(-2px); opacity: 0.9; }
            100% { transform: scale(1) translateY(0); opacity: 1; }
        }
        .animate-spring {
            animation: spring-in 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        /* Float animation */
        @keyframes float-slow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(1deg); }
        }
        .animate-float {
            animation: float-slow 6s ease-in-out infinite;
        }

        /* Draw SVG Graph line */
        @keyframes draw-path {
            to { stroke-dashoffset: 0; }
        }
        .path-draw {
            stroke-dasharray: 1000;
            stroke-dashoffset: 1000;
            animation: draw-path 2.5s ease-out forwards;
        }
    </style>
</head>
<body class="cyber-grid min-h-screen flex flex-col relative overflow-x-hidden transition-colors duration-300 bg-[var(--surface-1)] text-[var(--text-1)]">
    <div class="cyber-radial absolute inset-0 pointer-events-none z-0"></div>

    <!-- Header / Nav -->
    <header class="w-full max-w-7xl mx-auto px-6 py-5 flex justify-between items-center relative z-10">
        <div class="flex items-center gap-3">
            <?php if (!empty($companyLogo)): ?>
                <img src="<?= e($companyLogo) ?>" alt="Logo" class="max-h-10 max-w-full object-contain">
            <?php else: ?>
                <div class="w-9 h-9 rounded-[var(--radius-sm)] bg-brand flex items-center justify-center text-white font-black shadow-lg shadow-brand/20">
                    <span class="text-lg">P</span>
                </div>
                <span class="text-xl font-bold tracking-tight text-[var(--text-1)]"><?= e($companyName) ?></span>
            <?php endif; ?>
        </div>

        <div class="flex items-center gap-4">
            <!-- Theme Toggle -->
            <button onclick="toggleTheme()" class="p-2 rounded-[var(--radius-sm)] border border-[var(--border-2)] text-[var(--text-3)] hover:bg-[var(--surface-2)] transition cursor-pointer" title="Alternar Tema">
                <svg id="themeIconSun" class="w-4 h-4 hidden dark:block text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <svg id="themeIconMoon" class="w-4 h-4 block dark:hidden text-[var(--text-2)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
            </button>

            <!-- Auth Actions -->
            <a href="/login" class="text-[var(--text-2)] hover:text-[var(--text-1)] font-medium text-sm transition">Entrar</a>
            <a href="/register" class="bg-brand text-white px-4 py-2 rounded-[var(--radius-sm)] text-sm font-bold shadow-lg shadow-brand/20 hover:brightness-110 active:scale-95 transition-all">Começar Grátis</a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow w-full max-w-7xl mx-auto px-6 py-12 md:py-20 flex flex-col items-center gap-12 relative z-10">
        
        <!-- Center-Aligned Hero -->
        <div class="w-full max-w-4xl text-center space-y-6">
            <div class="inline-flex items-center gap-2 bg-brand/10 text-brand px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider border border-brand/20">
                <span class="w-2 h-2 rounded-full bg-brand animate-pulse"></span>
                Automação Comercial White-Label
            </div>
            
            <h1 class="text-4xl md:text-7xl font-extrabold tracking-tight leading-none text-[var(--text-1)]">
                De lead a receita. <br>
                <span class="text-brand">Tudo em um painel.</span>
            </h1>
            
            <p class="text-lg md:text-xl text-[var(--text-3)] leading-relaxed max-w-2xl mx-auto">
                CRM com Meta Ads, pipeline visual, dashboard e financeiro integrados para gestores de tráfego, agências e empresas.
            </p>

            <div class="flex flex-wrap justify-center gap-4 pt-4">
                <a href="/register" class="ds-btn ds-btn-primary btn-spring px-8 py-4 text-base shadow-xl shadow-brand/20">
                    Comece grátis
                </a>
                <a href="#showcase" class="ds-btn ds-btn-secondary btn-spring px-8 py-4 text-base hover:bg-[var(--surface-2)] transition-all">
                    Ver funcionalidades
                </a>
            </div>
        </div>

        <!-- Live Color Palette Customizer Previews -->
        <div class="w-full max-w-3xl p-4 bg-[var(--surface-2)] border border-[var(--border-1)] rounded-[var(--radius-md)] flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <span class="text-xs font-bold text-[var(--text-3)] uppercase tracking-widest block font-mono">White-Label Live Test</span>
                <span class="text-sm text-[var(--text-2)]">Escolha uma cor para pintar os painéis de demonstração em tempo real:</span>
            </div>
            <div class="flex gap-2">
                <button onclick="changeThemeColor('#00BF24')" class="w-6 h-6 rounded-full bg-[#00BF24] border-2 border-transparent hover:scale-110 transition cursor-pointer" title="Verde"></button>
                <button onclick="changeThemeColor('#3b82f6')" class="w-6 h-6 rounded-full bg-[#3b82f6] border-2 border-transparent hover:scale-110 transition cursor-pointer" title="Azul"></button>
                <button onclick="changeThemeColor('#f59e0b')" class="w-6 h-6 rounded-full bg-[#f59e0b] border-2 border-transparent hover:scale-110 transition cursor-pointer" title="Laranja"></button>
                <button onclick="changeThemeColor('#f43f5e')" class="w-6 h-6 rounded-full bg-[#f43f5e] border-2 border-transparent hover:scale-110 transition cursor-pointer" title="Rosa"></button>
                <button onclick="changeThemeColor('#06b6d4')" class="w-6 h-6 rounded-full bg-[#06b6d4] border-2 border-transparent hover:scale-110 transition cursor-pointer" title="Ciano"></button>
            </div>
        </div>

        <!-- IMMERSIVE INTERACTIVE SHOWCASE PANEL -->
        <div class="w-full max-w-5xl bg-[var(--surface-0)] border border-[var(--border-1)] rounded-[var(--radius-lg)] shadow-2xl relative overflow-hidden flex flex-col">
            <!-- Top Bar / Tab Navigation -->
            <div class="border-b border-[var(--border-1)] p-4 flex flex-col sm:flex-row justify-between items-center gap-4 bg-[var(--surface-1)]/50">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <span class="text-xs text-[var(--text-3)] font-mono ml-2">demo_environment.pmdcrm.com</span>
                </div>
                
                <!-- Spring-physics tab selectors -->
                <div class="flex bg-[var(--surface-2)] p-1 rounded border border-[var(--border-1)]">
                    <button onclick="switchTab('tab-kanban')" id="btn-tab-kanban" class="btn-spring px-4 py-2 text-xs font-bold rounded transition-all bg-brand text-white shadow-sm">
                        Pipeline Kanban
                    </button>
                    <button onclick="switchTab('tab-client')" id="btn-tab-client" class="btn-spring px-4 py-2 text-xs font-bold rounded transition-all text-[var(--text-3)] hover:text-[var(--text-1)]">
                        Ficha do Cliente
                    </button>
                    <button onclick="switchTab('tab-finance')" id="btn-tab-finance" class="btn-spring px-4 py-2 text-xs font-bold rounded transition-all text-[var(--text-3)] hover:text-[var(--text-1)]">
                        Painel Financeiro
                    </button>
                </div>
            </div>

            <!-- Showcase Content Panes -->
            <div class="p-6 md:p-8 min-h-[380px]">
                
                <!-- TAB 1: Kanban Funnel -->
                <div id="panel-kanban" class="showcase-pane transition-all duration-300">
                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                        <!-- Left Side: Simulator controls -->
                        <div class="lg:col-span-2 space-y-6 text-left">
                            <div class="space-y-2">
                                <h3 class="text-lg font-bold text-[var(--text-1)]">Simulador de Webhook</h3>
                                <p class="text-sm text-[var(--text-3)]">
                                    Veja o poder da captura instantânea. Quando um lead preenche um formulário no Facebook Ads, a API do Meta envia um webhook para o CRM.
                                </p>
                            </div>
                            
                            <div class="bg-[var(--surface-1)] p-4 rounded-[var(--radius-lg)] border border-[var(--border-1)] space-y-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-[var(--text-3)] uppercase tracking-widest mb-1 font-mono">Endereço do Webhook</label>
                                    <div class="bg-[var(--surface-0)] px-3 py-2 rounded text-[11px] font-mono text-[var(--text-2)] select-all overflow-x-auto whitespace-nowrap border border-[var(--border-1)]">
                                        https://api.pmdcrm.com/v1/webhook?token=demo_fb_ads
                                    </div>
                                </div>

                                <button onclick="simulateLead()" id="simBtn" class="w-full ds-btn ds-btn-primary btn-spring py-3 font-bold flex items-center justify-center gap-2 cursor-pointer shadow-lg shadow-brand/10">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.791-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    Simular Entrada de Lead
                                </button>
                                
                                <div id="simLog" class="bg-stone-950 text-stone-400 p-3 rounded border border-stone-850 text-[10px] font-mono hidden">
                                    <span class="text-brand">INFO:</span> Lead recebido via webhook. Injetando no pipeline...
                                </div>
                            </div>
                        </div>

                        <!-- Right Side: Kanban Board -->
                        <div class="lg:col-span-3 space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-[var(--text-3)] uppercase tracking-widest font-mono">Visualizador do Funil</span>
                                <span class="text-[10px] text-[var(--text-3)]">Dica: clique nos cards para avançar etapa</span>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-3">
                                <!-- Stage 1: Entrada -->
                                <div class="bg-[var(--surface-1)] p-3 rounded-[var(--radius-lg)] border border-[var(--border-1)] flex flex-col gap-2 min-h-[220px]">
                                    <span class="text-[9px] font-bold text-[var(--text-3)] uppercase tracking-wider">Entrada</span>
                                    <div id="kanban-entrada" class="flex flex-col gap-2 flex-grow">
                                        <div onclick="moveLead(this)" class="bg-[var(--surface-0)] border border-[var(--border-1)] p-3 rounded-[var(--radius-md)] hover:border-brand cursor-pointer hover:-translate-y-0.5 transition-all text-left" data-value="1500" data-stage="entrada">
                                            <p class="text-[10px] font-bold text-[var(--text-1)] leading-tight">Marcos Silva</p>
                                            <span class="text-[9px] text-brand font-bold block mt-1">R$ 1.500/mês</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Stage 2: Proposta -->
                                <div class="bg-[var(--surface-1)] p-3 rounded-[var(--radius-lg)] border border-[var(--border-1)] flex flex-col gap-2 min-h-[220px]">
                                    <span class="text-[9px] font-bold text-[var(--text-3)] uppercase tracking-wider">Proposta</span>
                                    <div id="kanban-contato" class="flex flex-col gap-2 flex-grow">
                                        <div onclick="moveLead(this)" class="bg-[var(--surface-0)] border border-[var(--border-1)] p-3 rounded-[var(--radius-md)] hover:border-brand cursor-pointer hover:-translate-y-0.5 transition-all text-left" data-value="3000" data-stage="contato">
                                            <p class="text-[10px] font-bold text-[var(--text-1)] leading-tight">Mariana Lima</p>
                                            <span class="text-[9px] text-brand font-bold block mt-1">R$ 3.000/mês</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Stage 3: Fechado -->
                                <div class="bg-[var(--surface-1)] p-3 rounded-[var(--radius-lg)] border border-[var(--border-1)] flex flex-col gap-2 min-h-[220px]">
                                    <span class="text-[9px] font-bold text-[var(--text-3)] uppercase tracking-wider">Fechado</span>
                                    <div id="kanban-fechado" class="flex flex-col gap-2 flex-grow">
                                        <!-- Empty initially -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: Ficha do Cliente -->
                <div id="panel-client" class="showcase-pane hidden transition-all duration-300">
                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                        <!-- Left Side: Cliente info card & Risk switcher -->
                        <div class="lg:col-span-2 space-y-6 text-left">
                            <div class="space-y-2">
                                <h3 class="text-lg font-bold text-[var(--text-1)]">Ficha do Cliente</h3>
                                <p class="text-sm text-[var(--text-3)]">
                                    Centralize todas as informações de um cliente em uma visão unificada. Monitore contratos, status financeiro e meça a saúde da conta.
                                </p>
                            </div>
                            
                            <!-- Immersive Interactive Client Card -->
                            <div class="bg-[var(--surface-1)] p-5 rounded-[var(--radius-lg)] border border-[var(--border-1)] space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-brand/10 border border-brand/20 flex items-center justify-center text-brand font-bold text-sm">
                                        MS
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-[var(--text-1)]" id="mock-client-name">Marcos Silva</h4>
                                        <p class="text-xs text-[var(--text-3)]">Tech Inovações Ltda</p>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-2 text-left pt-2 border-t border-[var(--border-1)]">
                                    <div>
                                        <span class="text-[9px] font-bold text-[var(--text-3)] uppercase block">Valor Contratual</span>
                                        <span class="text-xs font-bold text-[var(--text-1)]">R$ 1.500 / mês</span>
                                    </div>
                                    <div>
                                        <span class="text-[9px] font-bold text-[var(--text-3)] uppercase block">WhatsApp Link</span>
                                        <a href="#" onclick="event.preventDefault(); alert('Simulação de chat WhatsApp iniciada!');" class="text-xs font-bold text-brand hover:underline block">+55 11 99888...</a>
                                    </div>
                                </div>

                                <div class="pt-2">
                                    <span class="text-[9px] font-bold text-[var(--text-3)] uppercase block mb-1.5">Análise de Risco de Churn</span>
                                    <div class="flex gap-2">
                                        <button onclick="setRisk('baixo')" id="risk-baixo" class="btn-spring flex-1 py-1.5 text-[10px] font-bold rounded text-center border bg-green-500/10 text-green-500 border-green-500/30">
                                            Baixo
                                        </button>
                                        <button onclick="setRisk('medio')" id="risk-medio" class="btn-spring flex-1 py-1.5 text-[10px] font-bold rounded text-center border text-amber-500 border-[var(--border-1)]">
                                            Médio
                                        </button>
                                        <button onclick="setRisk('alto')" id="risk-alto" class="btn-spring flex-1 py-1.5 text-[10px] font-bold rounded text-center border text-rose-500 border-[var(--border-1)]">
                                            Alto
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side: Chronological Timeline Tracker -->
                        <div class="lg:col-span-3 space-y-4 text-left">
                            <h4 class="text-xs font-bold text-[var(--text-3)] uppercase tracking-widest font-mono mb-2">Histórico de Atividades</h4>
                            
                            <!-- Timeline Stack -->
                            <div class="relative border-l border-[var(--border-1)] ml-3 pl-6 space-y-6 py-2">
                                <!-- Point 1 -->
                                <div class="relative">
                                    <span class="absolute -left-9 top-1 w-6 h-6 rounded-full bg-brand/10 border border-brand flex items-center justify-center text-[10px] text-brand font-bold">1</span>
                                    <div class="bg-[var(--surface-0)] border border-[var(--border-1)] p-3 rounded-[var(--radius-lg)]">
                                        <span class="text-[9px] font-mono text-[var(--text-3)] block mb-0.5">Captura Automática (Meta API)</span>
                                        <p class="text-xs font-bold text-[var(--text-1)]">Lead Marcos Silva importado via webhook Facebook Ads.</p>
                                    </div>
                                </div>
                                <!-- Point 2 -->
                                <div class="relative">
                                    <span class="absolute -left-9 top-1 w-6 h-6 rounded-full bg-brand/10 border border-brand flex items-center justify-center text-[10px] text-brand font-bold">2</span>
                                    <div class="bg-[var(--surface-0)] border border-[var(--border-1)] p-3 rounded-[var(--radius-lg)]">
                                        <span class="text-[9px] font-mono text-[var(--text-3)] block mb-0.5">Engajamento WhatsApp</span>
                                        <p class="text-xs font-bold text-[var(--text-1)]">Mensagem disparada automaticamente para iniciar negociação.</p>
                                        <blockquote class="text-[10px] italic text-[var(--text-3)] mt-1 border-l-2 pl-2 border-[var(--border-1)]">"Olá Marcos! Vi o seu interesse em nossa solução..."</blockquote>
                                    </div>
                                </div>
                                <!-- Point 3 -->
                                <div class="relative">
                                    <span class="absolute -left-9 top-1 w-6 h-6 rounded-full bg-brand/10 border border-brand flex items-center justify-center text-[10px] text-brand font-bold">3</span>
                                    <div id="mock-timeline-risk" class="bg-[var(--surface-0)] border border-[var(--border-1)] p-3 rounded-[var(--radius-lg)] border-l-4 border-l-green-500">
                                        <span class="text-[9px] font-mono text-[var(--text-3)] block mb-0.5">Acompanhamento Comercial</span>
                                        <p class="text-xs font-bold text-[var(--text-1)]" id="risk-timeline-text">Status de risco atualizado: Cliente engajado e com baixa probabilidade de cancelamento.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: Painel Financeiro -->
                <div id="panel-finance" class="showcase-pane hidden transition-all duration-300">
                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                        <!-- Left Side: Interactive Metrics & KPIs -->
                        <div class="lg:col-span-2 space-y-6 text-left">
                            <div class="space-y-2">
                                <h3 class="text-lg font-bold text-[var(--text-1)]">Painel Financeiro & LTV</h3>
                                <p class="text-sm text-[var(--text-3)]">
                                    Acompanhe métricas avançadas de negócios de forma simplificada: MRR (Receita Recorrente), CAC (Custo de Aquisição) e LTV (Valor de Tempo de Vida).
                                </p>
                            </div>
                            
                            <!-- Dynamic KPI selector lists -->
                            <div class="space-y-3">
                                <button onclick="selectFinanceMetric('mrr')" id="metric-btn-mrr" class="btn-spring w-full p-4 rounded-[var(--radius-md)] text-left border bg-brand/10 text-brand border-brand/30 flex justify-between items-center">
                                    <div>
                                        <span class="text-[9px] font-bold uppercase tracking-widest block opacity-85">Monthly Recurring Revenue</span>
                                        <span id="mrrValueMock" class="text-xl font-black">R$ 42.500</span>
                                    </div>
                                    <span class="text-xs font-bold bg-brand/20 px-2 py-0.5 rounded font-mono">▲ 12.4%</span>
                                </button>
                                
                                <button onclick="selectFinanceMetric('cac')" id="metric-btn-cac" class="btn-spring w-full p-4 rounded-[var(--radius-md)] text-left border border-[var(--border-2)] hover:border-brand/45 flex justify-between items-center text-[var(--text-2)] bg-[var(--surface-0)]">
                                    <div>
                                        <span class="text-[9px] font-bold uppercase tracking-widest block opacity-75">Customer Acquisition Cost</span>
                                        <span class="text-xl font-black text-[var(--text-1)]">R$ 380,00</span>
                                    </div>
                                    <span class="text-xs font-bold bg-rose-500/10 text-rose-500 px-2 py-0.5 rounded font-mono">▼ 4.2%</span>
                                </button>

                                <button onclick="selectFinanceMetric('ltv')" id="metric-btn-ltv" class="btn-spring w-full p-4 rounded-[var(--radius-md)] text-left border border-[var(--border-2)] hover:border-brand/45 flex justify-between items-center text-[var(--text-2)] bg-[var(--surface-0)]">
                                    <div>
                                        <span class="text-[9px] font-bold uppercase tracking-widest block opacity-75">Lifetime Value (LTV)</span>
                                        <span class="text-xl font-black text-[var(--text-1)]">R$ 18.000</span>
                                    </div>
                                    <span class="text-xs font-bold bg-green-500/10 text-green-500 px-2 py-0.5 rounded font-mono">▲ 8.1%</span>
                                </button>
                            </div>
                        </div>

                        <!-- Right Side: Dynamic Vector Graph -->
                        <div class="lg:col-span-3 space-y-4">
                            <div class="flex justify-between items-center">
                                <span id="mock-chart-title" class="text-xs font-bold text-[var(--text-3)] uppercase tracking-widest font-mono">Gráfico: Receita Mensal Recorrente (6 Meses)</span>
                                <span class="text-[10px] text-[var(--text-3)] font-mono" id="mock-chart-axis">R$ 0 - R$ 50.000</span>
                            </div>
                            
                            <div class="bg-[var(--surface-1)] p-6 rounded-[var(--radius-lg)] border border-[var(--border-1)] flex items-center justify-center">
                                <div class="h-56 w-full relative">
                                    <!-- Animated SVG Line Chart -->
                                    <svg class="w-full h-full" viewBox="0 0 300 100" preserveAspectRatio="none">
                                        <!-- Chart Gradient Area -->
                                        <defs>
                                            <linearGradient id="chartGradShowcase" x1="0" y1="0" x2="0" y2="1">
                                                <stop offset="0%" stop-color="var(--theme-color)" stop-opacity="0.25"/>
                                                <stop offset="100%" stop-color="var(--theme-color)" stop-opacity="0"/>
                                            </linearGradient>
                                        </defs>
                                        <!-- Grid lines -->
                                        <line x1="0" y1="20" x2="300" y2="20" stroke="#f1f5f9" class="dark:stroke-slate-900" stroke-width="0.75" stroke-dasharray="2 2" />
                                        <line x1="0" y1="40" x2="300" y2="40" stroke="#f1f5f9" class="dark:stroke-slate-900" stroke-width="0.75" stroke-dasharray="2 2" />
                                        <line x1="0" y1="60" x2="300" y2="60" stroke="#f1f5f9" class="dark:stroke-slate-900" stroke-width="0.75" stroke-dasharray="2 2" />
                                        <line x1="0" y1="80" x2="300" y2="80" stroke="#f1f5f9" class="dark:stroke-slate-900" stroke-width="0.75" stroke-dasharray="2 2" />
                                        
                                        <!-- Filled Gradient -->
                                        <path id="graphGradShowcasePath" d="M0,90 Q50,75 100,82 T200,45 T300,30 L300,100 L0,100 Z" fill="url(#chartGradShowcase)"></path>
                                        
                                        <!-- Line Path -->
                                        <path id="graphLineShowcasePath" d="M0,90 Q50,75 100,82 T200,45 T300,30" fill="none" stroke="var(--theme-color)" stroke-width="2.5" stroke-linecap="round" class="path-draw"></path>
                                        
                                        <!-- Pulsing Points -->
                                        <circle id="pulsingPointShowcase" cx="300" cy="30" r="4.5" fill="var(--theme-color)" class="animate-ping opacity-75"></circle>
                                        <circle id="pulsingPointShowcaseInner" cx="300" cy="30" r="3" fill="var(--theme-color)"></circle>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Simulated WhatsApp Toast Alert -->
        <div id="waToast" class="fixed top-24 right-6 bg-[#075e54] text-white p-4 border border-[#128c7e] shadow-2xl rounded-[var(--radius-sm)] max-w-xs z-50 transition-all duration-300 transform translate-x-96 opacity-0 flex gap-3 pointer-events-none">
            <div class="text-xl">💬</div>
            <div>
                <h4 class="text-xs font-bold font-mono">WhatsApp Automation API</h4>
                <p class="text-[10px] text-slate-100 mt-0.5" id="waMsgText">Mensagem enviada com sucesso!</p>
            </div>
        </div>
    </main>

    <!-- Casos de Uso Section -->
    <section class="w-full max-w-7xl mx-auto px-6 py-16 border-t border-[var(--border-1)] transition-colors duration-300">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs uppercase tracking-widest text-[var(--brand)] font-semibold mb-3 block">Casos de Uso</span>
            <h2 class="text-3xl md:text-5xl font-black text-[var(--text-1)] tracking-tight">Projetado para quem vive de tráfego e conversão</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Card 1: Gestores de Tráfego -->
            <div class="ds-card p-8 flex flex-col justify-between min-h-[320px] transition-all duration-300">
                <div class="space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-[var(--brand-subtle)] flex items-center justify-center text-[var(--brand)]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-[var(--text-1)] font-sans">Gestores de Tráfego</h3>
                    <p class="text-sm text-[var(--text-3)] leading-relaxed">Parem de perder leads por demora no atendimento. Capture leads do Facebook Ads diretamente e dispare mensagens automáticas.</p>
                </div>
                <div class="pt-6 border-t border-[var(--border-1)] flex items-center justify-between text-xs font-bold text-[var(--brand)]">
                    <span>Funil integrado Meta Ads</span>
                    <span>→</span>
                </div>
            </div>
            <!-- Card 2: Donos de Agência -->
            <div class="ds-card p-8 flex flex-col justify-between min-h-[320px] transition-all duration-300">
                <div class="space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-[var(--brand-subtle)] flex items-center justify-center text-[var(--brand)]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-[var(--text-1)] font-sans">Donos de Agência</h3>
                    <p class="text-sm text-[var(--text-3)] leading-relaxed">Entregue um CRM premium para seus clientes com sua própria identidade visual e domínio próprio (White-Label total).</p>
                </div>
                <div class="pt-6 border-t border-[var(--border-1)] flex items-center justify-between text-xs font-bold text-[var(--brand)]">
                    <span>Plataforma 100% White-Label</span>
                    <span>→</span>
                </div>
            </div>
            <!-- Card 3: Empresas que Anunciam -->
            <div class="ds-card p-8 flex flex-col justify-between min-h-[320px] transition-all duration-300">
                <div class="space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-[var(--brand-subtle)] flex items-center justify-center text-[var(--brand)]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-[var(--text-1)] font-sans">Empresas</h3>
                    <p class="text-sm text-[var(--text-3)] leading-relaxed">Controle total de campanhas, leads, financeiro, despesas e fluxo de caixa em tempo real para tomada de decisões seguras.</p>
                </div>
                <div class="pt-6 border-t border-[var(--border-1)] flex items-center justify-between text-xs font-bold text-[var(--brand)]">
                    <span>Dashboard de ROI em tempo real</span>
                    <span>→</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Showcase features anchor section -->
    <section id="showcase" class="w-full max-w-7xl mx-auto px-6 py-20 border-t border-[var(--border-1)] transition-colors duration-300">
        <h2 class="text-2xl md:text-3xl font-extrabold text-[var(--text-1)] tracking-tight mb-12 text-left">
            Por que escolher o <span class="text-brand"><?= e($companyName) ?></span>?
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="ds-card p-6 transition-all">
                <div class="w-10 h-10 rounded-[var(--radius-sm)] bg-brand/10 border border-brand/20 flex items-center justify-center text-brand font-bold mb-4 font-mono">01</div>
                <h3 class="text-lg font-bold text-[var(--text-1)] mb-2">Pipeline Visual</h3>
                <p class="text-sm text-[var(--text-3)]">Acompanhe cada negociação por estágio, responsável e próximo passo</p>
            </div>

            <div class="ds-card p-6 transition-all">
                <div class="w-10 h-10 rounded-[var(--radius-sm)] bg-brand/10 border border-brand/20 flex items-center justify-center text-brand font-bold mb-4 font-mono">02</div>
                <h3 class="text-lg font-bold text-[var(--text-1)] mb-2">Meta Ads Integrado</h3>
                <p class="text-sm text-[var(--text-3)]">Leads do Facebook e Instagram direto no seu pipeline automaticamente</p>
            </div>

            <div class="ds-card p-6 transition-all">
                <div class="w-10 h-10 rounded-[var(--radius-sm)] bg-brand/10 border border-brand/20 flex items-center justify-center text-brand font-bold mb-4 font-mono">03</div>
                <h3 class="text-lg font-bold text-[var(--text-1)] mb-2">Financeiro Completo</h3>
                <p class="text-sm text-[var(--text-3)]">Receita, despesas e resultado financeiro no mesmo painel da operação</p>
            </div>

            <div class="ds-card p-6 transition-all">
                <div class="w-10 h-10 rounded-[var(--radius-sm)] bg-brand/10 border border-brand/20 flex items-center justify-center text-brand font-bold mb-4 font-mono">04</div>
                <h3 class="text-lg font-bold text-[var(--text-1)] mb-2">Dashboard em Tempo Real</h3>
                <p class="text-sm text-[var(--text-3)]">KPIs, funil de vendas e performance por atendente atualizados ao vivo</p>
            </div>

            <div class="ds-card p-6 transition-all">
                <div class="w-10 h-10 rounded-[var(--radius-sm)] bg-brand/10 border border-brand/20 flex items-center justify-center text-brand font-bold mb-4 font-mono">05</div>
                <h3 class="text-lg font-bold text-[var(--text-1)] mb-2">White-Label</h3>
                <p class="text-sm text-[var(--text-3)]">Sua marca, suas cores, seu domínio. Seus clientes nunca veem o nosso nome</p>
            </div>

            <div class="ds-card p-6 transition-all">
                <div class="w-10 h-10 rounded-[var(--radius-sm)] bg-brand/10 border border-brand/20 flex items-center justify-center text-brand font-bold mb-4 font-mono">06</div>
                <h3 class="text-lg font-bold text-[var(--text-1)] mb-2">Automação & Webhooks</h3>
                <p class="text-sm text-[var(--text-3)]">Leads entram, notificações disparam, tarefas se criam. Sem digitação manual</p>
            </div>
        </div>
    </section>

    <!-- CTA Final Section -->
    <section class="w-full max-w-5xl mx-auto px-6 py-20 my-12 text-center rounded-[var(--radius-xl)] relative overflow-hidden bg-stone-950 dark:bg-black text-white border border-stone-800">
        <div class="absolute inset-0 bg-gradient-to-br from-stone-900 to-black opacity-90 z-0"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,var(--theme-color,rgba(0,191,36,0.12)),transparent_60%)] z-0"></div>
        
        <div class="relative z-10 space-y-6 max-w-3xl mx-auto">
            <h2 class="text-3xl md:text-5xl font-black tracking-tight leading-tight">Pronto para transformar leads em receita?</h2>
            <p class="text-stone-400 text-base md:text-lg leading-relaxed max-w-xl mx-auto">Comece a centralizar sua operação comercial hoje mesmo com a plataforma white-label mais moderna e completa do mercado.</p>
            <div class="flex flex-wrap justify-center gap-4 pt-4">
                <a href="/register" class="ds-btn ds-btn-primary btn-spring px-8 py-4 text-base font-bold">Comece grátis</a>
                <a href="/login" class="ds-btn ds-btn-secondary btn-spring bg-transparent text-white border-stone-750 hover:bg-stone-900 hover:border-stone-600 px-8 py-4 text-base font-bold">Ver funcionalidades</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="w-full max-w-7xl mx-auto px-6 py-10 border-t border-[var(--border-1)] flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-[var(--text-3)] relative z-10 transition-colors duration-300">
        <p>&copy; <?= date('Y') ?> <?= e($companyName) ?>. Todos os direitos reservados.</p>
        <div class="flex gap-6">
            <a href="/termos_servico" class="hover:text-brand transition">Termos de Serviço</a>
            <a href="/politica_privacidade" class="hover:text-brand transition">Política de Privacidade</a>
        </div>
    </footer>

    <!-- Interactive Simulator Scripts -->
    <script>
        // Toggle Theme
        function toggleTheme() {
            const html = document.documentElement;
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }

        // Live Customizer
        function changeThemeColor(color) {
            document.documentElement.style.setProperty('--theme-color', color);
            localStorage.setItem('theme_color', color);
            
            // Re-render styles dynamically
            let style = document.getElementById('landing-custom-style');
            if (!style) {
                style = document.createElement('style');
                style.id = 'landing-custom-style';
                document.head.appendChild(style);
            }
            
            // Extract RGB values for radial lighting
            const rgb = hexToRgb(color);
            if (rgb) {
                document.documentElement.style.setProperty('--theme-color-rgb', `${rgb.r}, ${rgb.g}, ${rgb.b}`);
            }
        }

        function hexToRgb(hex) {
            const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? {
                r: parseInt(result[1], 16),
                g: parseInt(result[2], 16),
                b: parseInt(result[3], 16)
            } : null;
        }

        // Showcase Tab Switching
        function switchTab(tabId) {
            // Hide all panels
            document.getElementById('panel-kanban').classList.add('hidden');
            document.getElementById('panel-client').classList.add('hidden');
            document.getElementById('panel-finance').classList.add('hidden');
            
            // Show target panel
            const activePanel = document.getElementById(tabId.replace('tab-', 'panel-'));
            activePanel.classList.remove('hidden');
            
            // Reset button classes
            const tabs = ['btn-tab-kanban', 'btn-tab-client', 'btn-tab-finance'];
            tabs.forEach(id => {
                const btn = document.getElementById(id);
                btn.className = "btn-spring px-4 py-2 text-xs font-bold rounded transition-all text-[var(--text-3)] hover:text-[var(--text-1)]";
            });
            
            // Set active button style
            const activeBtn = document.getElementById(tabId.replace('tab-', 'btn-tab-'));
            activeBtn.className = "btn-spring px-4 py-2 text-xs font-bold rounded transition-all bg-brand text-white shadow-sm";
        }

        // Client Risk status switcher
        function setRisk(level) {
            const low = document.getElementById('risk-baixo');
            const med = document.getElementById('risk-medio');
            const high = document.getElementById('risk-alto');
            const riskText = document.getElementById('risk-timeline-text');
            const trackerContainer = document.getElementById('mock-timeline-risk');
            
            // Clear styles
            low.className = "btn-spring flex-1 py-1.5 text-[10px] font-bold rounded text-center border text-green-500 border-[var(--border-1)]";
            med.className = "btn-spring flex-1 py-1.5 text-[10px] font-bold rounded text-center border text-amber-500 border-[var(--border-1)]";
            high.className = "btn-spring flex-1 py-1.5 text-[10px] font-bold rounded text-center border text-rose-500 border-[var(--border-1)]";
            
            if (level === 'baixo') {
                low.className = "btn-spring flex-1 py-1.5 text-[10px] font-bold rounded text-center border bg-green-500/10 text-green-500 border-green-500/30";
                riskText.innerText = "Status de risco atualizado: Cliente engajado e com baixa probabilidade de cancelamento.";
                trackerContainer.className = "bg-[var(--surface-0)] border border-[var(--border-1)] p-3 rounded-[var(--radius-lg)] border-l-4 border-l-green-500";
            } else if (level === 'medio') {
                med.className = "btn-spring flex-1 py-1.5 text-[10px] font-bold rounded text-center border bg-amber-500/10 text-amber-500 border-amber-500/30";
                riskText.innerText = "Status de risco atualizado: Cliente inativo há 5 dias. Sugerido novo contato WhatsApp.";
                trackerContainer.className = "bg-[var(--surface-0)] border border-[var(--border-1)] p-3 rounded-[var(--radius-lg)] border-l-4 border-l-amber-500";
            } else if (level === 'alto') {
                high.className = "btn-spring flex-1 py-1.5 text-[10px] font-bold rounded text-center border bg-rose-500/10 text-rose-500 border-rose-500/30";
                riskText.innerText = "Status de risco ALTO: Fatura vencida há 12 dias. Risco crítico de cancelamento!";
                trackerContainer.className = "bg-[var(--surface-0)] border border-[var(--border-1)] p-3 rounded-[var(--radius-lg)] border-l-4 border-l-rose-500";
            }
        }

        // Financial metrics selector logic
        function selectFinanceMetric(metric) {
            const mrrBtn = document.getElementById('metric-btn-mrr');
            const cacBtn = document.getElementById('metric-btn-cac');
            const ltvBtn = document.getElementById('metric-btn-ltv');
            
            const chartTitle = document.getElementById('mock-chart-title');
            const chartAxis = document.getElementById('mock-chart-axis');
            
            const lineShowcase = document.getElementById('graphLineShowcasePath');
            const gradShowcase = document.getElementById('graphGradShowcasePath');
            const pointShowcase = document.getElementById('pulsingPointShowcase');
            
            // Clear styles
            mrrBtn.className = "btn-spring w-full p-4 rounded-[var(--radius-md)] text-left border border-[var(--border-1)] hover:border-brand/40 flex justify-between items-center text-[var(--text-3)] hover:text-[var(--text-1)] bg-[var(--surface-0)]";
            cacBtn.className = "btn-spring w-full p-4 rounded-[var(--radius-md)] text-left border border-[var(--border-1)] hover:border-brand/40 flex justify-between items-center text-[var(--text-3)] hover:text-[var(--text-1)] bg-[var(--surface-0)]";
            ltvBtn.className = "btn-spring w-full p-4 rounded-[var(--radius-md)] text-left border border-[var(--border-1)] hover:border-brand/40 flex justify-between items-center text-[var(--text-3)] hover:text-[var(--text-1)] bg-[var(--surface-0)]";
            
            if (metric === 'mrr') {
                mrrBtn.className = "btn-spring w-full p-4 rounded-[var(--radius-md)] text-left border bg-brand/10 text-brand border-brand/30 flex justify-between items-center";
                chartTitle.innerText = "Gráfico: Receita Mensal Recorrente (6 Meses)";
                chartAxis.innerText = "R$ 0 - R$ 50.000";
                
                const yOffset = Math.max(30 - (currentMrr - 42500) / 300, 10);
                lineShowcase.setAttribute('d', `M0,90 Q50,75 100,82 T200,45 T300,${yOffset}`);
                gradShowcase.setAttribute('d', `M0,90 Q50,75 100,82 T200,45 T300,${yOffset} L300,100 L0,100 Z`);
                pointShowcase.setAttribute('cy', yOffset);
            } else if (metric === 'cac') {
                cacBtn.className = "btn-spring w-full p-4 rounded-[var(--radius-md)] text-left border bg-brand/10 text-brand border-brand/30 flex justify-between items-center";
                chartTitle.innerText = "Gráfico: Custo de Aquisição de Clientes (6 Meses)";
                chartAxis.innerText = "R$ 0 - R$ 500";
                
                lineShowcase.setAttribute('d', `M0,35 Q50,42 100,50 T200,68 T300,75`);
                gradShowcase.setAttribute('d', `M0,35 Q50,42 100,50 T200,68 T300,75 L300,100 L0,100 Z`);
                pointShowcase.setAttribute('cy', 75);
            } else if (metric === 'ltv') {
                ltvBtn.className = "btn-spring w-full p-4 rounded-[var(--radius-md)] text-left border bg-brand/10 text-brand border-brand/30 flex justify-between items-center";
                chartTitle.innerText = "Gráfico: Valor do Ciclo de Vida do Cliente (LTV)";
                chartAxis.innerText = "R$ 0 - R$ 25.000";
                
                lineShowcase.setAttribute('d', `M0,85 Q50,80 100,70 T200,38 T300,20`);
                gradShowcase.setAttribute('d', `M0,85 Q50,80 100,70 T200,38 T300,20 L300,100 L0,100 Z`);
                pointShowcase.setAttribute('cy', 20);
            }
        }

        // Kanban Interactions & Value Calculations
        let currentMrr = 42500;
        
        function moveLead(element) {
            const stage = element.getAttribute('data-stage');
            const val = parseInt(element.getAttribute('data-value'));
            
            if (stage === 'entrada') {
                // Move to Contato
                element.setAttribute('data-stage', 'contato');
                document.getElementById('kanban-contato').appendChild(element);
            } else if (stage === 'contato') {
                // Move to Fechado
                element.setAttribute('data-stage', 'fechado');
                element.onclick = null; // Disable clicking once closed
                element.classList.remove('hover:border-brand', 'cursor-pointer');
                document.getElementById('kanban-fechado').appendChild(element);
                
                // Trigger MRR update
                updateMrr(val);
            }
        }

        function updateMrr(value) {
            const oldValue = currentMrr;
            currentMrr += value;
            
            // Animate number tick
            const mrrEl = document.getElementById('mrrValueMock');
            let start = oldValue;
            const end = currentMrr;
            const duration = 1000;
            const startTime = performance.now();

            function animateTick(now) {
                const elapsed = now - startTime;
                const progress = Math.min(elapsed / duration, 1);
                
                // Ease out quad
                const easeProgress = progress * (2 - progress);
                const currentVal = Math.floor(start + (end - start) * easeProgress);
                
                if (mrrEl) mrrEl.innerText = `R$ ${currentVal.toLocaleString('pt-BR')}`;
                
                if (progress < 1) {
                    requestAnimationFrame(animateTick);
                } else {
                    if (mrrEl) mrrEl.innerText = `R$ ${end.toLocaleString('pt-BR')}`;
                }
            }
            requestAnimationFrame(animateTick);
            
            // Adjust SVG Graph dynamically
            adjustGraphPath();
        }

        function adjustGraphPath() {
            // Animate SVG path to reflect growth
            const lineShowcase = document.getElementById('graphLineShowcasePath');
            const gradShowcase = document.getElementById('graphGradShowcasePath');
            const pointShowcase = document.getElementById('pulsingPointShowcase');
            
            // Scale points based on closed deal
            const yOffset = Math.max(30 - (currentMrr - 42500) / 300, 10);
            
            if (lineShowcase) lineShowcase.setAttribute('d', `M0,90 Q50,75 100,82 T200,45 T300,${yOffset}`);
            if (gradShowcase) gradShowcase.setAttribute('d', `M0,90 Q50,75 100,82 T200,45 T300,${yOffset} L300,100 L0,100 Z`);
            if (pointShowcase) pointShowcase.setAttribute('cy', yOffset);
        }

        // Webhook Simulator
        const leadNames = ['João Pedro', 'Amanda Costa', 'Guilherme Reis', 'Sofia Vieira', 'Rodrigo Ramos'];
        const leadPhones = ['+5511998888888', '+5521997777777', '+5531996666666', '+5519995555555', '+5551994444444'];
        
        async function simulateLead() {
            const btn = document.getElementById('simBtn');
            const log = document.getElementById('simLog');
            
            btn.disabled = true;
            btn.innerHTML = '<span class="animate-spin text-white">⟳</span> Processando Webhook...';
            
            log.classList.remove('hidden');
            log.innerHTML = `<span class="text-brand">INFO:</span> Aguardando requisição POST...`;

            await new Promise(resolve => setTimeout(resolve, 1500));
            
            const randomIdx = Math.floor(Math.random() * leadNames.length);
            const name = leadNames[randomIdx];
            const phone = leadPhones[randomIdx];
            const val = Math.floor(Math.random() * 4 + 1) * 1000 + 500; // R$ 1.500 to R$ 4.500
            
            log.innerHTML = `<span class="text-brand">PAYLOAD:</span> { "nome": "${name}", "telefone": "${phone}", "origem": "Facebook Ads Form" }`;
            
            // Create Kanban Card dynamically with spring-in animation
            const card = document.createElement('div');
            card.className = 'bg-[var(--surface-0)] border border-[var(--border-1)] p-3 rounded-[var(--radius-md)] hover:border-brand cursor-pointer hover:-translate-y-0.5 transition-all text-left animate-spring';
            card.setAttribute('data-value', val);
            card.setAttribute('data-stage', 'entrada');
            card.onclick = function() { moveLead(this); };
            card.innerHTML = `
                <p class="text-[10px] font-bold text-[var(--text-1)] leading-tight">${name}</p>
                <span class="text-[9px] text-brand font-bold block mt-1">R$ ${val.toLocaleString('pt-BR')}/mês</span>
            `;
            
            document.getElementById('kanban-entrada').appendChild(card);
            
            // Show WhatsApp Outreach Notification
            triggerWhatsAppNotification(name, phone);
            
            btn.disabled = false;
            btn.innerHTML = `
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.791-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                Simular Entrada de Lead (Facebook Ads)
            `;
        }

        function triggerWhatsAppNotification(name, phone) {
            const toast = document.getElementById('waToast');
            const txt = document.getElementById('waMsgText');
            
            txt.innerHTML = `Enviando WhatsApp de Boas-vindas para <b>${name}</b> (${phone})...`;
            
            // Slide in
            toast.classList.remove('translate-x-96', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
            
            setTimeout(() => {
                txt.innerHTML = `Mensagem enviada com sucesso para <b>${name}</b>! ✓`;
                toast.classList.add('bg-emerald-600', 'border-emerald-500');
                
                setTimeout(() => {
                    // Slide out
                    toast.classList.remove('translate-x-0', 'opacity-100');
                    toast.classList.add('translate-x-96', 'opacity-0');
                    setTimeout(() => {
                        toast.classList.remove('bg-emerald-600', 'border-emerald-500');
                    }, 300);
                }, 3000);
            }, 1800);
        }
    </script>
</body>
</html>
