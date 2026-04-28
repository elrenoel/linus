<?php
$sidebarItems = [
    ['name' => 'Dashboard', 'icon' => '/assets/dashboard.png', 'path' => '/dashboard', 'page' => 'dashboard'],
    ['name' => 'Info Bus', 'icon' => '/assets/bus.png', 'path' => '/bus-info', 'page' => 'info_bus'],
    ['name' => 'Feedback', 'icon' => '/assets/feedback.png', 'path' => '/feedback', 'page' => 'feedback'],
];

// Use value from index.php (already validated), fallback to dashboard.
$currentPage = $currentPage ?? 'dashboard';
?>


<div class="h-full flex w-fit">
    <div class="border-r border-gray-300 w-64 p-5">
        <nav class="h-full pt-4">
            <ul class="flex flex-col gap-4">
                <?php foreach ($sidebarItems as $item): ?>
                    <?php $isActive = ($currentPage === $item['page']); ?>
                    <li
                        class="px-5 py-2.5 rounded-lg w-full <?= $isActive ? '' : 'hover:bg-gray-200' ?>"
                        style="<?= $isActive ? 'background-color: rgba(35, 114, 39, 0.25);' : '' ?>">
                        <a
                            href="<?= app_url($item['path']) ?>"
                            class="flex items-center gap-3 font-medium text-[16px]">
                            <img
                                src="<?= app_url($item['icon']) ?>"
                                alt="<?= $item['name'] ?>"
                                class="w-5">
                            <?= $item['name'] ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="mt-8">
                <a
                    href="<?= app_url('/logout') ?>"
                    class="flex w-full items-center justify-center rounded-lg bg-[#427435] px-4 py-2.5 text-[16px] font-medium text-white">
                    Logout
                </a>
            </div>
        </nav>
    </div>
</div>