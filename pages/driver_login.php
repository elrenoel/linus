<?php
$error = isset($_GET['error']) ? (string) $_GET['error'] : '';
?>

<main class="flex min-h-full items-center justify-center bg-zinc-100 px-4">
    <div class="w-full max-w-md rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
        <div class="flex items-center gap-2">
            <img src="<?= app_url('/assets/logo.png') ?>" alt="Logo" class="h-2 w-2">
            <h1 class="text-xl font-bold text-zinc-900">Login Supir</h1>
        </div>
        <p class="mt-2 text-sm text-zinc-600">Masuk khusus supir untuk aktifkan tracking.</p>

        <?php if ($error !== ''): ?>
            <div class="mt-4 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                Username atau password supir salah.
            </div>
        <?php endif; ?>

        <form class="mt-5 space-y-4" method="post" action="<?= app_url('/logic/driver_auth_login.php') ?>">
            <div>
                <label for="username" class="text-sm font-semibold text-zinc-700">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    class="mt-2 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm"
                    required
                    autocomplete="username">
            </div>
            <div>
                <label for="password" class="text-sm font-semibold text-zinc-700">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="mt-2 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm"
                    required
                    autocomplete="current-password">
            </div>
            <button
                type="submit"
                class="w-full rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                Masuk
            </button>
        </form>
    </div>
</main>