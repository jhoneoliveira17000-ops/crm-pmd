<?php
// PMDCRM/nav.php
?>
<!-- Bottom Navbar (Mobile) -->
<nav class="fixed bottom-0 left-0 w-full bg-white/90 dark:bg-slate-900/90 backdrop-blur border-t border-gray-200 dark:border-slate-800 md:hidden z-50 transition-colors duration-300">
    <div class="flex justify-around items-center h-16">
        <a href="dashboard.php" class="flex flex-col items-center justify-center w-full h-full text-slate-400 hover:text-green-500 active:text-green-600 transition">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            <span class="text-[10px] uppercase font-bold tracking-wide">Dash</span>
        </a>
        <a href="clientes.php" class="flex flex-col items-center justify-center w-full h-full text-slate-400 hover:text-green-500 active:text-green-600 transition">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            <span class="text-[10px] uppercase font-bold tracking-wide">Clientes</span>
        </a>
        <div class="relative -top-5">
            <button onclick="openModal('new')" class="bg-[var(--theme-color)] hover:bg-green-600 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-lg shadow-green-500/30 hover:scale-105 transition duration-300">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </button>
        </div>

        <a href="crm_kanban.php" class="flex flex-col items-center justify-center w-full h-full text-slate-400 hover:text-green-500 active:text-green-600 transition">
             <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            <span class="text-[10px] uppercase font-bold tracking-wide">CRM</span>
        </a>

        <a href="financeiro.php" class="flex flex-col items-center justify-center w-full h-full text-slate-400 hover:text-green-500 active:text-green-600 transition">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-[10px] uppercase font-bold tracking-wide">Finan</span>
        </a>
    </div>
</nav>

<!-- Sidebar (Desktop) -->
<aside class="hidden md:flex flex-col w-64 h-screen bg-white dark:bg-[#0f172a] text-slate-800 dark:text-white fixed top-0 left-0 border-r border-gray-200 dark:border-slate-800 shadow-2xl z-40 transition-colors duration-300">
    <div class="h-24 flex items-center justify-center border-b border-gray-200 dark:border-slate-800 bg-white dark:bg-[#0f172a] overflow-hidden transition-colors duration-300">
        <?php
        // Fetch Logo
        // Ensure DB connection if not already present
        if (!isset($pdo) && file_exists(__DIR__ . '/src/db.php')) {
            require_once __DIR__ . '/src/db.php';
        }

        $logo = '';
        if (isset($pdo)) {
            $stmt = $pdo->prepare("SELECT value FROM config WHERE key_name = 'company_logo'");
            $stmt->execute();
            $logo = $stmt->fetchColumn();
        }
        ?>

        <?php if (!empty($logo)): ?>
            <img src="<?= htmlspecialchars($logo) ?>" alt="Logo" class="max-h-24 max-w-full object-contain">
        <?php else: ?>
            <span class="text-2xl font-black italic tracking-tighter text-slate-900 dark:text-white">PMD<span class="text-[var(--theme-color)]">CRM</span></span>
        <?php endif; ?>
    </div>
    
    <nav class="flex-1 px-4 py-8 space-y-2 overflow-y-auto custom-scrollbar">
        <a href="dashboard.php" class="group flex items-center px-4 py-3 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-800/50 rounded-xl transition duration-200 <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-green-500/10 text-[var(--theme-color)] dark:text-[var(--theme-color)] border border-green-500/20 shadow-[0_0_15px_rgba(0,191,36,0.1)]' : '' ?>">
            <div class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'text-[var(--theme-color)]' : 'text-slate-400 dark:text-slate-500 group-hover:text-[var(--theme-color)]' ?> transition mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            </div>
            <span class="font-medium tracking-wide">Dashboard</span>
        </a>
        
        <a href="clientes.php" class="group flex items-center px-4 py-3 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-800/50 rounded-xl transition duration-200 <?= basename($_SERVER['PHP_SELF']) == 'clientes.php' ? 'bg-green-500/10 text-[var(--theme-color)] dark:text-[var(--theme-color)] border border-green-500/20 shadow-[0_0_15px_rgba(0,191,36,0.1)]' : '' ?>">
            <div class="<?= basename($_SERVER['PHP_SELF']) == 'clientes.php' ? 'text-[var(--theme-color)]' : 'text-slate-400 dark:text-slate-500 group-hover:text-[var(--theme-color)]' ?> transition mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <span class="font-medium tracking-wide">Clientes</span>
        </a>

        <a href="agenda.php" class="group flex items-center px-4 py-3 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-800/50 rounded-xl transition duration-200 <?= basename($_SERVER['PHP_SELF']) == 'agenda.php' ? 'bg-green-500/10 text-[var(--theme-color)] dark:text-[var(--theme-color)] border border-green-500/20 shadow-[0_0_15px_rgba(0,191,36,0.1)]' : '' ?>">
            <div class="<?= basename($_SERVER['PHP_SELF']) == 'agenda.php' ? 'text-[var(--theme-color)]' : 'text-slate-400 dark:text-slate-500 group-hover:text-[var(--theme-color)]' ?> transition mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <span class="font-medium tracking-wide">Agenda</span>
        </a>

        <a href="crm_kanban.php" class="group flex items-center px-4 py-3 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-800/50 rounded-xl transition duration-200 <?= basename($_SERVER['PHP_SELF']) == 'crm_kanban.php' ? 'bg-green-500/10 text-[var(--theme-color)] dark:text-[var(--theme-color)] border border-green-500/20 shadow-[0_0_15px_rgba(0,191,36,0.1)]' : '' ?>">
            <div class="<?= basename($_SERVER['PHP_SELF']) == 'crm_kanban.php' ? 'text-[var(--theme-color)]' : 'text-slate-400 dark:text-slate-500 group-hover:text-[var(--theme-color)]' ?> transition mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
            <span class="font-medium tracking-wide">CRM / Pipeline</span>
        </a>
        
        <a href="financeiro.php" class="group flex items-center px-4 py-3 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-800/50 rounded-xl transition duration-200 <?= basename($_SERVER['PHP_SELF']) == 'financeiro.php' ? 'bg-green-500/10 text-[var(--theme-color)] dark:text-[var(--theme-color)] border border-green-500/20 shadow-[0_0_15px_rgba(0,191,36,0.1)]' : '' ?>">
            <div class="<?= basename($_SERVER['PHP_SELF']) == 'financeiro.php' ? 'text-[var(--theme-color)]' : 'text-slate-400 dark:text-slate-500 group-hover:text-[var(--theme-color)]' ?> transition mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <span class="font-medium tracking-wide">Financeiro</span>
        </a>

        <?php if (is_admin()): ?>
        <a href="admin_dashboard.php" class="group flex items-center px-4 py-3 mt-4 text-red-500 hover:text-white hover:bg-red-600/80 rounded-xl transition duration-200 <?= basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'bg-red-500/10 text-red-600 border border-red-500/20' : '' ?>">
            <div class="mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <span class="font-bold tracking-wide text-sm">Painel Admin</span>
        </a>
        <?php endif; ?>
    </nav>
    
    <div class="p-4 border-t border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-900/50">
        <button onclick="window.location.href='src/logout.php'" class="group flex items-center w-full px-4 py-2 text-rose-500 hover:text-white hover:bg-rose-600/20 rounded-lg transition duration-200">
            <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            <span class="font-medium">Sair</span>
        </button>
    </div>
</aside>
