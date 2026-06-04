<?php
// PMDCRM/nav.php — Editorial Sidebar Navigation
?>
<?php if (isset($_SESSION['is_impersonating']) && $_SESSION['is_impersonating']): ?>
<div class="fixed top-0 left-0 right-0 z-[200] flex items-center justify-between px-4 py-2 text-sm font-bold shadow-lg md:pl-64" style="background:var(--danger);color:#fff">
    <span>⚠️ Impersonando: <strong><?= htmlspecialchars($_SESSION['user_nome']) ?></strong> (ID #<?= (int)$_SESSION['user_id'] ?>)</span>
    <button onclick="exitImpersonation()" class="bg-white px-3 py-1 font-bold hover:opacity-90 transition text-xs" style="color:var(--danger);border-radius:var(--radius-sm)">
        ↩️ Voltar ao Admin
    </button>
</div>
<script>
async function exitImpersonation() {
    const res = await fetch('api/admin_impersonate.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'exit' }) });
    const data = await res.json();
    if (data.success) window.location.href = '/admin_tenants';
    else alert(data.error);
}
</script>
<style>body { padding-top: 40px; }</style>
<?php endif; ?>
<?php
$currentPage = basename($_SERVER['PHP_SELF']);

// Helper for nav item active state
function navItemClasses($page, $currentPage) {
    $base = 'group flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200';
    if ($page === $currentPage) {
        return $base . ' bg-[var(--brand-muted)] text-[var(--text-1)]';
    }
    return $base . ' text-[var(--text-3)] hover:text-[var(--text-1)] hover:bg-[var(--surface-2)]';
}

function navIconClasses($page, $currentPage) {
    if ($page === $currentPage) {
        return 'w-[18px] h-[18px] text-[var(--brand)] flex-shrink-0';
    }
    return 'w-[18px] h-[18px] text-[var(--text-4)] group-hover:text-[var(--brand)] flex-shrink-0 transition-colors duration-200';
}

function navActiveIndicator($page, $currentPage) {
    if ($page === $currentPage) {
        return '<span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full" style="background:var(--brand)"></span>';
    }
    return '';
}
?>
<!-- Bottom Navbar (Mobile) -->
<nav class="fixed bottom-0 left-0 w-full md:hidden z-50 transition-colors duration-300" style="background:var(--surface-0);border-top:1px solid var(--border-1)">
    <div class="flex justify-around items-center h-14 max-w-lg mx-auto px-2">
        <a href="dashboard.php" class="flex flex-col items-center justify-center gap-0.5 flex-1 py-1 transition-colors <?= $currentPage == 'dashboard.php' ? 'text-[var(--brand)]' : 'text-[var(--text-4)]' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            <span class="text-[10px] font-semibold tracking-wide">Dashboard</span>
        </a>
        <a href="clientes.php" class="flex flex-col items-center justify-center gap-0.5 flex-1 py-1 transition-colors <?= $currentPage == 'clientes.php' ? 'text-[var(--brand)]' : 'text-[var(--text-4)]' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <span class="text-[10px] font-semibold tracking-wide">Clientes</span>
        </a>
        <a href="crm_kanban.php" class="flex flex-col items-center justify-center gap-0.5 flex-1 py-1 transition-colors <?= $currentPage == 'crm_kanban.php' ? 'text-[var(--brand)]' : 'text-[var(--text-4)]' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
            <span class="text-[10px] font-semibold tracking-wide">Pipeline</span>
        </a>
        <a href="financeiro.php" class="flex flex-col items-center justify-center gap-0.5 flex-1 py-1 transition-colors <?= $currentPage == 'financeiro.php' ? 'text-[var(--brand)]' : 'text-[var(--text-4)]' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-[10px] font-semibold tracking-wide">Financeiro</span>
        </a>
    </div>
</nav>

<!-- Sidebar (Desktop) -->
<aside class="hidden md:flex flex-col w-64 h-screen fixed top-0 left-0 z-40 transition-all duration-300" style="background:var(--surface-0);border-right:1px solid var(--border-1)">
    <!-- Logo / Brand -->
    <div class="h-16 flex items-center justify-between px-5 flex-shrink-0" style="border-bottom:1px solid var(--border-1)">
        <div class="flex items-center gap-2 overflow-hidden min-w-0">
            <?php if (!empty($companyLogo)): ?>
                <img src="<?= e($companyLogo) ?>" alt="Logo" class="max-h-8 max-w-full object-contain">
            <?php else: ?>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-sm font-bold flex-shrink-0" style="background:var(--brand)">
                    <?= strtoupper(substr($companyName, 0, 1)) ?>
                </div>
                <span class="text-sm font-bold truncate" style="color:var(--text-1)"><?= e($companyName) ?></span>
            <?php endif; ?>
        </div>
        <button id="sidebarCollapseBtn" onclick="toggleSidebarLayout(true)" class="p-1.5 rounded-lg transition-colors duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100 hover:opacity-100 focus:opacity-100" style="color:var(--text-4)" onmouseenter="this.style.background='var(--surface-2)'" onmouseleave="this.style.background='transparent'" title="Recolher barra lateral">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7M19 19l-7-7 7-7"></path></svg>
        </button>
    </div>
    
    <!-- Navigation Links -->
    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto ds-scrollbar">
        <p class="px-4 py-2 text-[10px] font-semibold uppercase tracking-widest" style="color:var(--text-4)">Menu</p>

        <div class="relative">
            <?= navActiveIndicator('dashboard.php', $currentPage) ?>
            <a href="dashboard.php" class="<?= navItemClasses('dashboard.php', $currentPage) ?>">
                <svg class="<?= navIconClasses('dashboard.php', $currentPage) ?>" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                Dashboard
            </a>
        </div>
        
        <div class="relative">
            <?= navActiveIndicator('clientes.php', $currentPage) ?>
            <a href="clientes.php" class="<?= navItemClasses('clientes.php', $currentPage) ?>">
                <svg class="<?= navIconClasses('clientes.php', $currentPage) ?>" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Clientes
            </a>
        </div>

        <div class="relative">
            <?= navActiveIndicator('agenda.php', $currentPage) ?>
            <a href="agenda.php" class="<?= navItemClasses('agenda.php', $currentPage) ?>">
                <svg class="<?= navIconClasses('agenda.php', $currentPage) ?>" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Agenda
            </a>
        </div>

        <div class="relative">
            <?= navActiveIndicator('crm_kanban.php', $currentPage) ?>
            <a href="crm_kanban.php" class="<?= navItemClasses('crm_kanban.php', $currentPage) ?>">
                <svg class="<?= navIconClasses('crm_kanban.php', $currentPage) ?>" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                Pipeline
            </a>
        </div>
        
        <div class="relative">
            <?= navActiveIndicator('financeiro.php', $currentPage) ?>
            <a href="financeiro.php" class="<?= navItemClasses('financeiro.php', $currentPage) ?>">
                <svg class="<?= navIconClasses('financeiro.php', $currentPage) ?>" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Financeiro
            </a>
        </div>

        <?php if (is_admin()): ?>
        <div class="mt-4 pt-4" style="border-top:1px solid var(--border-1)">
            <p class="px-4 py-2 text-[10px] font-semibold uppercase tracking-widest" style="color:var(--text-4)">Administração</p>
            <div class="relative">
                <a href="admin_dashboard.php" class="group flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 <?= basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'bg-[var(--danger-subtle)] text-[var(--danger)]' : 'text-[var(--text-3)] hover:text-[var(--danger)] hover:bg-[var(--danger-subtle)]' ?>">
                    <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Painel Admin
                </a>
            </div>
        </div>
        <?php endif; ?>
    </nav>
    
    <!-- Logout -->
    <div class="flex-shrink-0 p-3" style="border-top:1px solid var(--border-1)">
        <button onclick="window.location.href='/logout'" class="group flex items-center gap-3 w-full px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 hover:bg-[var(--danger-subtle)]" style="color:var(--text-3)" onmouseenter="this.style.color='var(--danger)'" onmouseleave="this.style.color='var(--text-3)'">
            <svg class="w-[18px] h-[18px] flex-shrink-0 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            Sair
        </button>
    </div>
</aside>
