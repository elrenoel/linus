<?php
$mockBuses = [
	[
		'id' => 'bus-01',
		'plat' => 'BK 1423 USU',
		'supir' => 'Andi Saputra',
		'status_key' => 'sedang_berjalan',
		'status_label' => 'Sedang Berjalan',
		'tujuan' => 'Halte FMIPA',
	],
	[
		'id' => 'bus-02',
		'plat' => 'BK 1524 USU',
		'supir' => 'Budi Hartono',
		'status_key' => 'menuju_halte',
		'status_label' => 'Menuju Halte',
		'tujuan' => 'Halte Pintu Sumber/Hukum',
	],
	[
		'id' => 'bus-03',
		'plat' => 'BK 1625 USU',
		'supir' => 'Citra Lestari',
		'status_key' => 'sedang_berhenti',
		'status_label' => 'Sedang Berhenti',
		'tujuan' => 'Halte FISIP',
	],
];

$statusStyles = [
	'sedang_berjalan' => 'text-emerald-700',
	'menuju_halte' => 'text-amber-700',
	'sedang_berhenti' => 'text-slate-700',
];

$statusDotStyles = [
	'sedang_berjalan' => 'bg-emerald-600',
	'menuju_halte' => 'bg-amber-500',
	'sedang_berhenti' => 'bg-slate-500',
];

$selectedBusId = $_GET['bus_id'] ?? '';
$selectedBusId = preg_replace('/[^a-zA-Z0-9\-]/', '', $selectedBusId);

$selectedBus = null;
foreach ($mockBuses as $bus) {
	if ($bus['id'] === $selectedBusId) {
		$selectedBus = $bus;
		break;
	}
}

$totalBus = count($mockBuses);
$busAktif = $totalBus;
$targetSlots = 4;
$placeholderCount = max(0, $targetSlots - $totalBus);
?>

<section class="w-full min-h-full bg-zinc-100 p-4 md:p-6">
	<div class="mx-auto max-w-6xl space-y-4">
		<header>
			<h1 class="text-3xl font-bold text-zinc-900 md:text-4xl"><?= $busAktif ?> Bus Aktif Sekarang</h1>
			<p class="mt-1 text-sm text-zinc-600">Pilih bus untuk melihat detail posisi, status, dan fokus peta.</p>
		</header>

		<div class="grid grid-cols-[repeat(auto-fit,minmax(320px,1fr))] gap-4">
			<?php foreach ($mockBuses as $index => $bus): ?>
				<?php
				$isSelected = ($selectedBusId !== '' && $selectedBusId === $bus['id']);
				$statusClass = $statusStyles[$bus['status_key']] ?? 'text-zinc-700';
				$statusDotClass = $statusDotStyles[$bus['status_key']] ?? 'bg-zinc-500';
				$detailUrl = '?page=info_bus&bus_id=' . urlencode($bus['id']);
				?>
				<article class="flex min-h-44 flex-col justify-between rounded-md border p-4 transition <?= $isSelected ? 'border-emerald-500 bg-emerald-50/40' : 'border-zinc-300 bg-zinc-100' ?>">
					<div>
						<div class="flex items-start justify-between gap-4">
							<h2 class="text-3xl font-bold leading-none text-zinc-900 md:text-4xl">Bus <?= $index + 1 ?></h2>
							<p class="text-sm font-semibold text-zinc-700"><?= htmlspecialchars($bus['plat'], ENT_QUOTES, 'UTF-8') ?></p>
						</div>
						<div class="mt-3 flex items-center gap-2 text-sm font-semibold <?= $statusClass ?>">
							<span class="h-2.5 w-2.5 rounded-full <?= $statusDotClass ?>"></span>
							<span><?= htmlspecialchars($bus['status_label'], ENT_QUOTES, 'UTF-8') ?></span>
						</div>
						<p class="mt-3 text-sm text-zinc-700">Supir: <?= htmlspecialchars($bus['supir'], ENT_QUOTES, 'UTF-8') ?></p>
						<p class="text-sm text-zinc-700">Tujuan: <?= htmlspecialchars($bus['tujuan'], ENT_QUOTES, 'UTF-8') ?></p>
					</div>

					<div class="mt-4 flex justify-end">
						<a
							href="<?= $detailUrl ?>"
							class="inline-flex items-center rounded-md border border-zinc-500 bg-white px-4 py-1.5 text-sm text-zinc-800 transition hover:bg-zinc-50"
						>
							Lihat detail
						</a>
					</div>
				</article>
			<?php endforeach; ?>

			<?php for ($slot = 0; $slot < $placeholderCount; $slot++): ?>
				<article class="min-h-44 rounded-md border border-zinc-300 bg-zinc-100"></article>
			<?php endfor; ?>
		</div>

		<?php if ($selectedBusId !== '' && $selectedBus === null): ?>
			<section class="rounded-md border border-amber-300 bg-amber-50 p-5 text-amber-900">
				<h3 class="text-lg font-bold">Bus tidak ditemukan</h3>
				<p class="mt-2 text-sm md:text-base">
					ID bus pada URL tidak valid. Silakan kembali ke daftar bus untuk memilih data yang tersedia.
				</p>
				<a href="?page=info_bus" class="mt-4 inline-flex rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">
					Kembali ke Daftar Bus
				</a>
			</section>
		<?php endif; ?>

		<?php if ($selectedBus !== null): ?>
			<?php
			$selectedStatusClass = $statusStyles[$selectedBus['status_key']] ?? 'text-zinc-700';
			$selectedStatusDotClass = $statusDotStyles[$selectedBus['status_key']] ?? 'bg-zinc-500';
			$mapUrl = 'components/map.html?mode=detail&bus_id=' . urlencode($selectedBus['id']);
			?>
			<section class="rounded-md border border-zinc-300 bg-white p-5">
				<div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
					<div>
						<p class="text-sm font-medium uppercase tracking-wide text-zinc-500">Detail Bus Dipilih</p>
						<h3 class="mt-1 text-xl font-bold text-zinc-900 md:text-2xl">
							<?= htmlspecialchars($selectedBus['plat'], ENT_QUOTES, 'UTF-8') ?> - <?= htmlspecialchars($selectedBus['supir'], ENT_QUOTES, 'UTF-8') ?>
						</h3>
						<p class="mt-1 text-sm text-zinc-700">
							Halte tujuan saat ini: <span class="font-semibold text-zinc-900"><?= htmlspecialchars($selectedBus['tujuan'], ENT_QUOTES, 'UTF-8') ?></span>
						</p>
					</div>
					<div class="flex items-center gap-2 text-sm font-semibold <?= $selectedStatusClass ?>">
						<span class="h-2.5 w-2.5 rounded-full <?= $selectedStatusDotClass ?>"></span>
						<span><?= htmlspecialchars($selectedBus['status_label'], ENT_QUOTES, 'UTF-8') ?></span>
					</div>
				</div>

				<div class="mt-4 h-85 overflow-hidden rounded-md border border-zinc-300 md:h-105">
					<iframe src="<?= $mapUrl ?>" title="Map Detail Bus" class="h-full w-full"></iframe>
				</div>
			</section>
		<?php endif; ?>
	</div>
</section>
