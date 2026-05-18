<?php
$navItems = [
    ['name' => 'Dashboard', 'icon' => '/assets/dashboard.png', 'path' => '/dashboard', 'page' => 'dashboard'],
    ['name' => 'Info Bus', 'icon' => '/assets/bus.png', 'path' => '/bus-info', 'page' => 'info_bus'],
    ['name' => 'Feedback', 'icon' => '/assets/feedback.png', 'path' => '/feedback', 'page' => 'feedback'],
];

$currentPage = $currentPage ?? 'dashboard';
?>

<header class="relative">
    <div class="bg-[#427435] flex px-[2%] py-2.5 justify-between items-center">
        <div class="flex items-center gap-1">
            <img src="assets/logo.png" alt="Logo" class="w-12">
            <h1 class="text-white text-2xl font-bold">LINUS</h1>
        </div>

        <button
            type="button"
            id="mobile-nav-toggle"
            class="flex md:hidden items-center justify-center rounded-md border border-white/30 px-3 py-2 text-sm font-semibold text-white"
            aria-expanded="false"
            aria-controls="mobile-nav-menu">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
    </div>

    <div id="mobile-nav-menu" class="hidden border-b border-gray-200 bg-white md:hidden">
        <nav class="py-3">
            <ul class="flex flex-col gap-2">
                <?php foreach ($navItems as $item): ?>
                    <?php $isActive = ($currentPage === $item['page']); ?>
                    <li>
                        <a
                            href="<?= app_url($item['path']) ?>"
                            class="flex items-center gap-3 rounded-md px-3 py-5 text-sm font-semibold <?= $isActive ? 'bg-emerald-50 text-emerald-700' : 'text-zinc-700 hover:bg-zinc-100' ?>">
                            <?= $item['name'] ?>
                        </a>
                    </li>
                <?php endforeach; ?>
                <li class="border-t border-zinc-200">
                    <a
                        href="<?= app_url('/logout') ?>"
                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-semibold text-zinc-700 hover:bg-zinc-100">
                        Logout
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</header>

<script>
    (() => {
        const toggle = document.getElementById('mobile-nav-toggle');
        const menu = document.getElementById('mobile-nav-menu');
        if (!toggle || !menu) return;

        toggle.addEventListener('click', () => {
            const isHidden = menu.classList.contains('hidden');
            menu.classList.toggle('hidden');
            toggle.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
        });
    })();
</script>