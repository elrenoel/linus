<?php
require_once __DIR__ . '/../logic/bus_service.php';

$busList = bus_fetch_all(bus_get_conn());
$apiUrl = app_url('/logic/api_bus_location_update.php');
$driverName = $_SESSION['driver_name'] ?? 'Supir';
?>

<section class="w-full min-h-full bg-zinc-100 p-4 md:p-6">
    <div class="mx-auto flex w-full max-w-4xl flex-col gap-6">
        <header class="rounded-md border border-zinc-200 bg-white p-5">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-zinc-900 md:text-3xl">Mode Supir</h1>
                    <p class="mt-2 text-sm text-zinc-600">
                        Pilih bus lalu aktifkan tracking. Lokasi akan dikirim otomatis setiap 3-5 detik.
                    </p>
                    <p class="mt-2 text-sm font-semibold text-zinc-800">
                        Supir: <?= htmlspecialchars($driverName, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>
                <a
                    href="<?= app_url('/driver-logout') ?>"
                    class="inline-flex items-center justify-center rounded-md border border-zinc-400 px-4 py-2 text-sm font-semibold text-zinc-700 hover:bg-zinc-50">
                    Logout Supir
                </a>
            </div>
        </header>

        <div class="rounded-md border border-zinc-200 bg-white p-5">
            <form id="driver-form" class="space-y-4" data-api-url="<?= htmlspecialchars($apiUrl, ENT_QUOTES, 'UTF-8') ?>">
                <div>
                    <label class="text-sm font-semibold text-zinc-700" for="bus-id">Pilih Bus</label>
                    <select
                        id="bus-id"
                        name="bus_id"
                        class="mt-2 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm text-zinc-800"
                        required>
                        <option value="">-- Pilih Bus --</option>
                        <?php foreach ($busList as $bus): ?>
                            <option value="<?= (int) $bus['id'] ?>">
                                <?= htmlspecialchars($bus['label'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button
                        type="button"
                        id="start-tracking"
                        class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                        Mulai Tracking
                    </button>
                    <button
                        type="button"
                        id="stop-tracking"
                        class="rounded-md border border-zinc-400 px-4 py-2 text-sm font-semibold text-zinc-700"
                        disabled>
                        Stop Tracking
                    </button>
                </div>

                <div class="rounded-md border border-zinc-200 bg-zinc-50 p-4">
                    <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Status</div>
                    <p id="driver-status" class="mt-2 text-sm text-zinc-700">Belum aktif.</p>
                </div>
            </form>
        </div>
    </div>
</section>

<script src="<?= app_url('/logic/driver.js') ?>"></script>