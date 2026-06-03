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
        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
            color: #0f172a;
        }
        .dark body {
            background-color: #030712;
            color: #f3f4f6;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        
        /* Grid overlay background */
        .cyber-grid {
            background-image: 
                linear-gradient(to right, rgba(120, 119, 198, 0.04) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(120, 119, 198, 0.04) 1px, transparent 1px);
            background-size: 3rem 3rem;
        }
        .dark .cyber-grid {
            background-image: 
                linear-gradient(to right, rgba(99, 102, 241, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(99, 102, 241, 0.03) 1px, transparent 1px);
        }

        /* Subtle radial background lighting */
        .cyber-radial {
            background: radial-gradient(circle 600px at 50% 200px, rgba(var(--theme-color-rgb, 0, 191, 36), 0.05), transparent 80%);
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
<body class="cyber-grid min-h-screen flex flex-col relative overflow-x-hidden transition-colors duration-300">
    <div class="cyber-radial absolute inset-0 pointer-events-none z-0"></div>

    <!-- Header / Nav -->
    <header class="w-full max-w-7xl mx-auto px-6 py-5 flex justify-between items-center relative z-10">
        <div class="flex items-center gap-3">
            <?php if (!empty($companyLogo)): ?>
                <img src="<?= e($companyLogo) ?>" alt="Logo" class="max-h-10 max-w-full object-contain">
            <?php else: ?>
                <div class="w-9 h-9 rounded bg-brand flex items-center justify-center text-white font-black shadow-lg shadow-brand/20">
                    <span class="text-lg">P</span>
                </div>
                <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white"><?= e($companyName) ?></span>
            <?php endif; ?>
        </div>

        <div class="flex items-center gap-4">
            <!-- Theme Toggle -->
            <button onclick="toggleTheme()" class="p-2 rounded border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-900/50 transition cursor-pointer" title="Alternar Tema">
                <svg id="themeIconSun" class="w-4 h-4 hidden dark:block text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <svg id="themeIconMoon" class="w-4 h-4 block dark:hidden text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
            </button>

            <!-- Auth Actions -->
            <a href="/login" class="text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium text-sm transition">Entrar</a>
            <a href="/register" class="bg-brand text-white px-4 py-2 rounded text-sm font-bold shadow-lg shadow-brand/20 hover:brightness-110 active:scale-95 transition-all">Começar Grátis</a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow w-full max-w-7xl mx-auto px-6 py-12 md:py-20 flex flex-col lg:flex-row items-center gap-16 relative z-10">
        
        <!-- Hero Column -->
        <div class="w-full lg:w-3/5 space-y-8 text-left">
            <div class="inline-flex items-center gap-2 bg-brand/10 text-brand px-3 py-1 rounded text-xs font-semibold uppercase tracking-wider border border-brand/20">
                <span class="w-2 h-2 rounded-full bg-brand animate-pulse"></span>
                Automação Comercial White-Label
            </div>
            
            <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight leading-none text-slate-900 dark:text-white">
                Seu pipeline de vendas, <br class="hidden md:inline">
                <span class="text-brand">sincronizado e no piloto automático.</span>
            </h1>
            
            <p class="text-lg md:text-xl text-slate-600 dark:text-slate-400 leading-relaxed max-w-2xl">
                Conecte leads do Facebook Ads instantaneamente, organize o funil comercial com um Kanban interativo e automatize o envio de mensagens do WhatsApp. Tudo personalizado com a identidade da sua marca.
            </p>

            <!-- Color Palette Customizer Previews -->
            <div class="p-4 bg-slate-50 dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800 rounded flex flex-col md:flex-row items-start md:items-center justify-between gap-4 max-w-2xl">
                <div>
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest block">White-Label Live Test</span>
                    <span class="text-sm text-slate-600 dark:text-slate-400">Escolha uma cor para pintar este painel de demonstração em tempo real:</span>
                </div>
                <div class="flex gap-2">
                    <button onclick="changeThemeColor('#00BF24')" class="w-6 h-6 rounded-full bg-[#00BF24] border-2 border-transparent hover:scale-110 transition cursor-pointer" title="Verde"></button>
                    <button onclick="changeThemeColor('#3b82f6')" class="w-6 h-6 rounded-full bg-[#3b82f6] border-2 border-transparent hover:scale-110 transition cursor-pointer" title="Azul"></button>
                    <button onclick="changeThemeColor('#f59e0b')" class="w-6 h-6 rounded-full bg-[#f59e0b] border-2 border-transparent hover:scale-110 transition cursor-pointer" title="Laranja"></button>
                    <button onclick="changeThemeColor('#f43f5e')" class="w-6 h-6 rounded-full bg-[#f43f5e] border-2 border-transparent hover:scale-110 transition cursor-pointer" title="Rosa"></button>
                    <button onclick="changeThemeColor('#06b6d4')" class="w-6 h-6 rounded-full bg-[#06b6d4] border-2 border-transparent hover:scale-110 transition cursor-pointer" title="Ciano"></button>
                </div>
            </div>
            
            <div class="flex flex-wrap gap-4 pt-4">
                <a href="/register" class="bg-brand text-white px-8 py-4 rounded font-bold text-base shadow-xl shadow-brand/20 hover:scale-105 active:scale-95 transition-all">
                    Criar Minha Conta
                </a>
                <a href="#showcase" class="bg-white dark:bg-slate-950 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 px-8 py-4 rounded font-bold text-base hover:bg-slate-100 dark:hover:bg-slate-900 transition-all">
                    Ver Funcionalidades
                </a>
            </div>
        </div>

        <!-- Demonstration Widgets Column -->
        <div class="w-full lg:w-2/5 flex flex-col gap-6 relative">
            <!-- Simulated Glow Object -->
            <div class="absolute -inset-4 bg-brand/10 rounded-xl blur-2xl opacity-40 pointer-events-none"></div>
            
            <!-- WIDGET 1: Webhook & Simulator Control Panel -->
            <div class="bg-white dark:bg-[#090d1a] border border-slate-200 dark:border-slate-800 rounded p-5 relative z-10 shadow-xl transition-colors duration-300">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-xs font-bold text-brand uppercase tracking-wider font-mono">&gt;_ SIMULATION_CONTROL</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5 font-mono">Webhook endpoint</label>
                        <div class="bg-slate-50 dark:bg-slate-950 px-3 py-2 rounded text-[11px] font-mono text-slate-600 dark:text-slate-400 select-all overflow-x-auto whitespace-nowrap border border-slate-100 dark:border-slate-900">
                            https://api.pmdcrm.com/v1/webhook?token=demo_fb_ads
                        </div>
                    </div>

                    <button onclick="simulateLead()" id="simBtn" class="w-full bg-brand text-white font-bold py-3 px-4 rounded text-sm hover:brightness-110 active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer shadow-lg shadow-brand/10">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.791-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        Simular Entrada de Lead (Facebook Ads)
                    </button>
                    
                    <!-- Code Preview Log -->
                    <div id="simLog" class="bg-slate-950 text-slate-400 p-3 rounded text-[10px] font-mono border border-slate-900 hidden">
                        <span class="text-brand">INFO:</span> Lead recebido via webhook. Injetando no pipeline...
                    </div>
                </div>
            </div>

            <!-- WIDGET 2: Interactive Kanban Panel -->
            <div class="bg-white dark:bg-[#090d1a] border border-slate-200 dark:border-slate-800 rounded p-5 relative z-10 shadow-xl transition-colors duration-300">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-xs font-bold text-slate-800 dark:text-white uppercase tracking-wider font-mono">Simulador de Kanban</span>
                    <span class="text-[10px] text-slate-400">Clique no card para avançar etapa</span>
                </div>
                
                <div class="grid grid-cols-3 gap-2 text-left">
                    <!-- Column 1: Entrada -->
                    <div class="bg-slate-50 dark:bg-slate-950 p-2 rounded min-h-[140px] border border-slate-100 dark:border-slate-900 flex flex-col gap-2">
                        <span class="text-[9px] font-bold text-slate-500 uppercase">Entrada</span>
                        <div id="kanban-entrada" class="flex flex-col gap-2 flex-grow">
                            <!-- Pre-existing card -->
                            <div onclick="moveLead(this)" class="bg-white dark:bg-[#111827] border border-slate-200 dark:border-slate-800 p-2 rounded shadow-sm hover:border-brand cursor-pointer hover:-translate-y-0.5 transition-all" data-value="1500" data-stage="entrada">
                                <p class="text-[10px] font-bold text-slate-800 dark:text-slate-200 leading-tight">Marcos Silva</p>
                                <span class="text-[9px] text-brand font-medium">R$ 1.500</span>
                            </div>
                        </div>
                    </div>

                    <!-- Column 2: Contato -->
                    <div class="bg-slate-50 dark:bg-slate-950 p-2 rounded min-h-[140px] border border-slate-100 dark:border-slate-900 flex flex-col gap-2">
                        <span class="text-[9px] font-bold text-slate-500 uppercase">Proposta</span>
                        <div id="kanban-contato" class="flex flex-col gap-2 flex-grow">
                            <div onclick="moveLead(this)" class="bg-white dark:bg-[#111827] border border-slate-200 dark:border-slate-800 p-2 rounded shadow-sm hover:border-brand cursor-pointer hover:-translate-y-0.5 transition-all" data-value="3000" data-stage="contato">
                                <p class="text-[10px] font-bold text-slate-800 dark:text-slate-200 leading-tight">Mariana Lima</p>
                                <span class="text-[9px] text-brand font-medium">R$ 3.000</span>
                            </div>
                        </div>
                    </div>

                    <!-- Column 3: Fechado -->
                    <div class="bg-slate-50 dark:bg-slate-950 p-2 rounded min-h-[140px] border border-slate-100 dark:border-slate-900 flex flex-col gap-2">
                        <span class="text-[9px] font-bold text-slate-500 uppercase">Fechado</span>
                        <div id="kanban-fechado" class="flex flex-col gap-2 flex-grow">
                            <!-- Empty initially -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- WIDGET 3: Analytics dashboard mock -->
            <div class="bg-white dark:bg-[#090d1a] border border-slate-200 dark:border-slate-800 rounded p-5 relative z-10 shadow-xl transition-colors duration-300">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-widest block font-mono">Receita Mensal Recorrente (MRR)</span>
                        <span id="mrrValue" class="text-2xl font-black text-slate-900 dark:text-white transition-all">R$ 42.500</span>
                    </div>
                    <span class="text-xs font-bold text-emerald-500 flex items-center gap-1 font-mono">
                        ▲ 12.4%
                    </span>
                </div>
                
                <!-- Animated SVG Line Chart -->
                <div class="h-24 w-full">
                    <svg class="w-full h-full" viewBox="0 0 300 100" preserveAspectRatio="none">
                        <!-- Chart Gradient Area -->
                        <defs>
                            <linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="var(--theme-color)" stop-opacity="0.25"/>
                                <stop offset="100%" stop-color="var(--theme-color)" stop-opacity="0"/>
                            </linearGradient>
                        </defs>
                        <!-- Grid lines -->
                        <line x1="0" y1="25" x2="300" y2="25" stroke="#f1f5f9" class="dark:stroke-slate-900" stroke-width="1" stroke-dasharray="2 2" />
                        <line x1="0" y1="50" x2="300" y2="50" stroke="#f1f5f9" class="dark:stroke-slate-900" stroke-width="1" stroke-dasharray="2 2" />
                        <line x1="0" y1="75" x2="300" y2="75" stroke="#f1f5f9" class="dark:stroke-slate-900" stroke-width="1" stroke-dasharray="2 2" />
                        
                        <!-- Filled Gradient -->
                        <path id="graphGradPath" d="M0,90 Q50,75 100,82 T200,45 T300,30 L300,100 L0,100 Z" fill="url(#chartGrad)"></path>
                        
                        <!-- Line Path -->
                        <path id="graphLinePath" d="M0,90 Q50,75 100,82 T200,45 T300,30" fill="none" stroke="var(--theme-color)" stroke-width="2.5" stroke-linecap="round" class="path-draw"></path>
                        
                        <!-- Pulsing Points -->
                        <circle cx="300" cy="30" r="4.5" fill="var(--theme-color)" class="animate-ping opacity-75"></circle>
                        <circle cx="300" cy="30" r="3" fill="var(--theme-color)"></circle>
                    </svg>
                </div>
            </div>

            <!-- Simulated WhatsApp Toast Alert -->
            <div id="waToast" class="fixed top-24 right-6 bg-[#075e54] text-white p-4 border border-[#128c7e] shadow-2xl rounded max-w-xs z-50 transition-all duration-300 transform translate-x-96 opacity-0 flex gap-3 pointer-events-none">
                <div class="text-xl">💬</div>
                <div>
                    <h4 class="text-xs font-bold font-mono">WhatsApp Automation API</h4>
                    <p class="text-[10px] text-slate-100 mt-0.5" id="waMsgText">Mensagem enviada com sucesso!</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Showcase features anchor section -->
    <section id="showcase" class="w-full max-w-7xl mx-auto px-6 py-20 border-t border-slate-200 dark:border-slate-900 transition-colors duration-300">
        <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-12 text-left">
            Por que escolher o <span class="text-brand"><?= e($companyName) ?></span>?
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="p-6 bg-white dark:bg-[#090d1a] border border-slate-200 dark:border-slate-800 rounded transition-all hover:border-brand">
                <div class="w-10 h-10 rounded bg-brand/10 border border-brand/20 flex items-center justify-center text-brand font-bold mb-4 font-mono">01</div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">Captura de Leads Direta</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">Integração nativa com APIs de leads do Meta. Receba as informações instantaneamente sem depender de plataformas terceiras ou planilhas.</p>
            </div>

            <div class="p-6 bg-white dark:bg-[#090d1a] border border-slate-200 dark:border-slate-800 rounded transition-all hover:border-brand">
                <div class="w-10 h-10 rounded bg-brand/10 border border-brand/20 flex items-center justify-center text-brand font-bold mb-4 font-mono">02</div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">Painel Kanban Otimizado</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">Controle o status do atendimento, feche negociações e acompanhe as conversões do seu time de vendas de forma visual e intuitiva.</p>
            </div>

            <div class="p-6 bg-white dark:bg-[#090d1a] border border-slate-200 dark:border-slate-800 rounded transition-all hover:border-brand">
                <div class="w-10 h-10 rounded bg-brand/10 border border-brand/20 flex items-center justify-center text-brand font-bold mb-4 font-mono">03</div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">Automatizações WhatsApp</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">Configure mensagens automatizadas de boas-vindas ou cobrança recorrente, aumentando as taxas de contato em mais de 70%.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="w-full max-w-7xl mx-auto px-6 py-10 border-t border-slate-200 dark:border-slate-900 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-slate-500 dark:text-slate-400 relative z-10 transition-colors duration-300">
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
            const mrrEl = document.getElementById('mrrValue');
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
                
                mrrEl.innerText = `R$ ${currentVal.toLocaleString('pt-BR')}`;
                
                if (progress < 1) {
                    requestAnimationFrame(animateTick);
                } else {
                    mrrEl.innerText = `R$ ${end.toLocaleString('pt-BR')}`;
                }
            }
            requestAnimationFrame(animateTick);
            
            // Adjust SVG Graph dynamically
            adjustGraphPath();
        }

        function adjustGraphPath() {
            // Animate SVG path to reflect growth
            const line = document.getElementById('graphLinePath');
            const grad = document.getElementById('graphGradPath');
            
            // Scale points based on closed deal
            const yOffset = Math.max(30 - (currentMrr - 42500) / 300, 10);
            
            line.setAttribute('d', `M0,90 Q50,75 100,82 T200,45 T300,${yOffset}`);
            grad.setAttribute('d', `M0,90 Q50,75 100,82 T200,45 T300,${yOffset} L300,100 L0,100 Z`);
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
            card.className = 'bg-white dark:bg-[#111827] border border-slate-200 dark:border-slate-800 p-2 rounded shadow-sm hover:border-brand cursor-pointer hover:-translate-y-0.5 transition-all animate-spring';
            card.setAttribute('data-value', val);
            card.setAttribute('data-stage', 'entrada');
            card.onclick = function() { moveLead(this); };
            card.innerHTML = `
                <p class="text-[10px] font-bold text-slate-800 dark:text-slate-200 leading-tight">${name}</p>
                <span class="text-[9px] text-brand font-medium">R$ ${val.toLocaleString('pt-BR')}</span>
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
