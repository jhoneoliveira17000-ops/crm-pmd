<?php
// PMDCRM/admin_nav.php — Navegação exclusiva do painel de administração
require_once __DIR__ . '/src/auth.php';
$currentPage = basename($_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? '');
?>

<!-- Admin Theme Script (must be before body renders to avoid flash) -->
<script>
(function() {
    const saved = localStorage.getItem('admin_theme');
    if (saved === 'light') {
        document.documentElement.classList.remove('dark');
    } else {
        document.documentElement.classList.add('dark');
    }
})();
function toggleAdminTheme() {
    const html = document.documentElement;
    const isNowDark = html.classList.toggle('dark');
    localStorage.setItem('admin_theme', isNowDark ? 'dark' : 'light');
    // Update icon
    document.getElementById('themeIconSun').classList.toggle('hidden', isNowDark);
    document.getElementById('themeIconMoon').classList.toggle('hidden', !isNowDark);
}
</script>

<!-- Mobile Bottom Nav -->
<nav class="fixed bottom-0 left-0 right-0 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 flex justify-around items-center h-16 z-50 md:hidden transition-colors">
    <a href="admin_dashboard.php" class="flex flex-col items-center py-1 <?= $currentPage == 'admin_dashboard.php' ? 'text-red-500' : 'text-slate-400' ?>">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
        <span class="text-[10px] mt-0.5 font-bold">Dashboard</span>
    </a>
    <a href="admin_tenants.php" class="flex flex-col items-center py-1 <?= $currentPage == 'admin_tenants.php' ? 'text-red-500' : 'text-slate-400' ?>">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        <span class="text-[10px] mt-0.5 font-bold">Tenants</span>
    </a>
    <a href="admin_plans.php" class="flex flex-col items-center py-1 <?= $currentPage == 'admin_plans.php' ? 'text-red-500' : 'text-slate-400' ?>">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
        <span class="text-[10px] mt-0.5 font-bold">Planos</span>
    </a>
    <a href="admin_logs.php" class="flex flex-col items-center py-1 <?= $currentPage == 'admin_logs.php' ? 'text-red-500' : 'text-slate-400' ?>">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        <span class="text-[10px] mt-0.5 font-bold">Logs</span>
    </a>
    <button onclick="toggleAdminTheme()" class="flex flex-col items-center py-1 text-slate-400">
        <svg id="mThemeIconMoon" class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
        <svg id="mThemeIconSun" class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        <span class="text-[10px] mt-0.5 font-bold">Tema</span>
    </button>
</nav>

<!-- Desktop Sidebar -->
<aside class="hidden md:flex fixed inset-y-0 left-0 w-64 bg-white dark:bg-slate-950 border-r border-slate-200 dark:border-slate-800 flex-col z-40 transition-colors">
    
    <!-- Logo Area -->
    <div class="p-6 flex items-center justify-center border-b border-slate-200 dark:border-slate-800">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
            <div>
                <span class="text-lg font-black text-slate-900 dark:text-white">PMD<span class="text-red-500">CRM</span></span>
                <span class="block text-[10px] text-red-400 font-bold uppercase tracking-widest -mt-1">Owner Panel</span>
            </div>
        </div>
    </div>

    <!-- Admin Badge + Theme Toggle -->
    <div class="px-4 py-3 flex items-center gap-2">
        <div class="flex-1 bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800/50 rounded-lg px-3 py-2 flex items-center gap-2">
            <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
            <span class="text-xs font-bold text-red-600 dark:text-red-400 uppercase tracking-wider">Super Admin</span>
        </div>
        <button onclick="toggleAdminTheme()" class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition" title="Alternar tema">
            <svg id="themeIconMoon" class="w-4 h-4 text-slate-600 dark:text-slate-300 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
            <svg id="themeIconSun" class="w-4 h-4 text-amber-500 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-2 space-y-1 overflow-y-auto">
        
        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-600 uppercase tracking-widest px-3 mb-2">Principal</p>
        
        <a href="admin_dashboard.php" class="group flex items-center px-3 py-2.5 rounded-lg transition duration-200 <?= $currentPage == 'admin_dashboard.php' ? 'bg-red-50 dark:bg-red-600/10 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-600/20' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/50' ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            <span class="font-medium text-sm">Dashboard</span>
        </a>

        <a href="admin_tenants.php" class="group flex items-center px-3 py-2.5 rounded-lg transition duration-200 <?= $currentPage == 'admin_tenants.php' ? 'bg-red-50 dark:bg-red-600/10 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-600/20' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/50' ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <span class="font-medium text-sm">Gestão de Tenants</span>
        </a>

        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-600 uppercase tracking-widest px-3 mt-4 mb-2">Sistema</p>

        <a href="admin_plans.php" class="group flex items-center px-3 py-2.5 rounded-lg transition duration-200 <?= $currentPage == 'admin_plans.php' ? 'bg-red-50 dark:bg-red-600/10 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-600/20' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/50' ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            <span class="font-medium text-sm">Planos</span>
        </a>

        <a href="admin_logs.php" class="group flex items-center px-3 py-2.5 rounded-lg transition duration-200 <?= $currentPage == 'admin_logs.php' ? 'bg-red-50 dark:bg-red-600/10 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-600/20' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/50' ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span class="font-medium text-sm">Logs de Atividade</span>
        </a>

        <a href="admin_config.php" class="group flex items-center px-3 py-2.5 rounded-lg transition duration-200 <?= $currentPage == 'admin_config.php' ? 'bg-red-50 dark:bg-red-600/10 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-600/20' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/50' ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <span class="font-medium text-sm">Configurações</span>
        </a>
    </nav>

    <!-- Footer -->
    <div class="p-4 border-t border-slate-200 dark:border-slate-800 space-y-2">
        <a href="dashboard.php" class="group flex items-center w-full px-3 py-2 text-slate-500 hover:text-blue-500 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800/50 rounded-lg transition duration-200">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"></path></svg>
            <span class="font-medium text-sm">Voltar ao CRM</span>
        </a>
        <button onclick="window.location.href='/logout'" class="group flex items-center w-full px-3 py-2 text-rose-500 hover:text-white hover:bg-rose-600/20 rounded-lg transition duration-200">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            <span class="font-medium text-sm">Sair</span>
        </button>
    </div>
</aside>
