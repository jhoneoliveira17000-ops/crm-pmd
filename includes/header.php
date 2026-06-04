<?php
// PMDCRM/includes/header.php
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/helpers.php';

// Verify CSRF token on modifying request methods
verify_csrf_or_exit();

// Load White-Label Branding settings
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

// Generate RGB components for rgba() usage
$themeColorRgb = '';
if (preg_match('/^#([0-9a-f]{6})$/i', $themeColor, $m)) {
    $r = hexdec(substr($m[1], 0, 2));
    $g = hexdec(substr($m[1], 2, 2));
    $b = hexdec(substr($m[1], 4, 2));
    $themeColorRgb = "$r,$g,$b";
}

$title = isset($page_title) ? str_replace('PMDCRM', $companyName, $page_title) : $companyName;
?>
<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>
    
    <!-- SEO & Metadata -->
    <meta name="description" content="<?= e($companyName) ?> - Sistema de CRM e Gestão de Clientes.">
    <meta name="theme-color" content="<?= $themeColor ?>">
    
    <!-- Google Fonts Preconnect & Load -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Design System CSS (Global) -->
    <link rel="stylesheet" href="assets/css/design-system.css">
    
    <!-- Scripts & Styles -->
    <script>
        localStorage.setItem('theme_color', '<?= $themeColor ?>');
        localStorage.setItem('company_name', '<?= e($companyName) ?>');
    </script>
    <script src="js/theme-loader.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            DEFAULT: 'var(--theme-color)',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        :root {
            --theme-color: <?= $themeColor ?>;
            --theme-color-rgb: <?= $themeColorRgb ?: '0,191,36' ?>;
        }

        /* Spring Effect classes (Suave e Elegante) */
        .btn-spring, .card-spring {
            transition: transform 0.5s cubic-bezier(0.34, 1.3, 0.64, 1), box-shadow 0.5s cubic-bezier(0.34, 1.3, 0.64, 1), border-color 0.5s ease, background-color 0.5s ease !important;
            will-change: transform;
        }
        .btn-spring:hover {
            transform: scale(1.035) translateY(-1px) !important;
        }
        .btn-spring:active {
            transform: scale(0.97) translateY(0) !important;
        }
        .card-spring:hover {
            transform: scale(1.008) translateY(-2px) !important;
            box-shadow: var(--shadow-md) !important;
        }
        .dark .card-spring:hover {
            box-shadow: var(--shadow-lg) !important;
            border-color: rgba(var(--theme-color-rgb), 0.2) !important;
        }
        .card-spring:active {
            transform: scale(0.998) translateY(0) !important;
        }

        body {
            font-family: var(--font-sans, 'Inter', sans-serif);
            background: var(--surface-1);
            color: var(--text-1);
        }
        .font-mono { font-family: var(--font-mono, 'JetBrains Mono', monospace); }
        
        /* Legacy card-bi (backwards compat) */
        .card-bi {
            background: var(--surface-0);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-1);
            box-shadow: var(--shadow-xs);
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border-2); border-radius: 999px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--border-3); }

        /* Collapsible Sidebar Styles */
        @media (min-width: 768px) {
            body {
                transition: padding-left 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }
            html.sidebar-collapsed body {
                padding-left: 0 !important;
            }
            html.sidebar-collapsed aside {
                transform: translateX(-100%) !important;
                box-shadow: none !important;
            }
            html:not(.sidebar-collapsed) #sidebarExpandBtn {
                display: none !important;
            }
            html.sidebar-collapsed #sidebarExpandBtn {
                display: flex !important;
            }
            aside {
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.3s, border-color 0.3s, box-shadow 0.3s !important;
            }
        }
    </style>
    
    <script>
        // Check sidebar collapsed state from localStorage before body renders to avoid flickering
        (function() {
            const sidebarCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
            if (sidebarCollapsed && window.innerWidth >= 768) {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        })();

        function toggleSidebarLayout(collapsed) {
            if (collapsed) {
                document.documentElement.classList.add('sidebar-collapsed');
                localStorage.setItem('sidebar-collapsed', 'true');
            } else {
                document.documentElement.classList.remove('sidebar-collapsed');
                localStorage.setItem('sidebar-collapsed', 'false');
            }
            // Trigger window resize to recalculate canvas layouts (e.g. Chart.js, FullCalendar)
            setTimeout(() => {
                window.dispatchEvent(new Event('resize'));
            }, 300);
        }
    </script>
    
    <!-- CSRF Token Meta -->
    <meta name="csrf-token" content="<?= generate_csrf_token() ?>">
</head>
<body class="<?= isset($body_class) ? $body_class : 'bg-[var(--surface-1)] text-[var(--text-1)] pb-20 md:pb-0 md:pl-64' ?> transition-colors duration-300">

    <!-- Expand Sidebar Button (Desktop only, visible when sidebar is collapsed) -->
    <button id="sidebarExpandBtn" onclick="toggleSidebarLayout(false)" class="fixed top-4 left-4 z-50 ds-card-flat p-2.5 shadow-lg hover:bg-[var(--surface-2)] transition duration-200 hidden md:flex items-center justify-center text-[var(--text-3)] hover:text-[var(--text-1)]" style="border-radius:var(--radius-md)" title="Expandir barra lateral">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
    </button>
