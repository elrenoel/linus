<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../logic/feedback.php';

$ratingFilterOptions = [
	'all' => 'Semua Rating',
	'5' => '5 Bintang',
	'4' => '4 Bintang',
	'3' => '3 Bintang',
	'2' => '2 Bintang',
	'1' => '1 Bintang',
];

$sortOptions = [
	'latest' => 'Terbaru',
	'highest' => 'Rating Tertinggi',
	'lowest' => 'Rating Terendah',
];

$selectedFilter = $_GET['rating'] ?? 'all';
$selectedSort = $_GET['sort'] ?? 'latest';

if (!array_key_exists($selectedFilter, $ratingFilterOptions)) {
	$selectedFilter = 'all';
}

if (!array_key_exists($selectedSort, $sortOptions)) {
	$selectedSort = 'latest';
}

$ratingToneClasses = [
	5 => 'bg-emerald-100 text-emerald-700 border-emerald-200',
	4 => 'bg-lime-100 text-lime-700 border-lime-200',
	3 => 'bg-amber-100 text-amber-700 border-amber-200',
	2 => 'bg-orange-100 text-orange-700 border-orange-200',
	1 => 'bg-rose-100 text-rose-700 border-rose-200',
];

$formStatus = [
	'submitted' => false,
	'error' => '',
];


$formInput = [
	'username' => '',
	'rating' => '5',
	'bus_label' => '',
	'route_label' => '',
	'comment' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
	$formInput['username'] = trim((string) ($_POST['username'] ?? ''));
	$formInput['rating'] = trim((string) ($_POST['rating'] ?? '5'));
	$formInput['bus_label'] = trim((string) ($_POST['bus_label'] ?? ''));
	$formInput['route_label'] = trim((string) ($_POST['route_label'] ?? ''));
	$formInput['comment'] = trim((string) ($_POST['comment'] ?? ''));

	$saveResult = feedback_insert_review($conn, $formInput);
	if (!$saveResult['ok']) {
		$formStatus['error'] = $saveResult['error'];
	} else {
		$formStatus['submitted'] = true;
		$formInput = [
			'username' => '',
			'rating' => '5',
			'bus_label' => '',
			'route_label' => '',
			'comment' => '',
		];
	}
}

if (!function_exists('feedbackSafeText')) {
	function feedbackSafeText(string $text): string
	{
		return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
	}
}

if (!function_exists('feedbackInitials')) {
	function feedbackInitials(string $username): string
	{
		$name = trim($username);
		if ($name === '') {
			return 'U';
		}

		$parts = preg_split('/\s+/', $name);
		if (!is_array($parts) || count($parts) === 0) {
			return strtoupper(substr($name, 0, 1));
		}

		$first = strtoupper(substr($parts[0], 0, 1));
		$second = '';
		if (count($parts) > 1) {
			$second = strtoupper(substr($parts[count($parts) - 1], 0, 1));
		}

		return $first . $second;
	}
}

if (!function_exists('feedbackRenderStars')) {
	function feedbackRenderStars(int $rating): string
	{
		$normalized = max(1, min(5, $rating));
		$stars = '';
		for ($i = 1; $i <= 5; $i++) {
			$stars .= $i <= $normalized ? '&#9733;' : '&#9734;';
		}

		return $stars;
	}
}

if (!function_exists('feedbackDisplayDate')) {
	function feedbackDisplayDate(string $date): string
	{
		$timestamp = strtotime($date);
		if ($timestamp === false) {
			return 'Tanggal belum tersedia';
		}

		return date('d M Y, H:i', $timestamp);
	}
}

$reviewFeed = feedback_fetch_reviews($conn, $selectedFilter, $selectedSort);
$emptyCommentFallback = 'Pengguna belum menulis komentar detail untuk perjalanan ini.';
$hasReviews = count($reviewFeed) > 0;
?>

<section class="w-full min-h-full bg-zinc-100 p-4 md:p-6">
	<div class="mx-auto max-w-6xl space-y-5">
		<header class="overflow-hidden rounded-lg border border-zinc-300 bg-white shadow-sm">
			<div class="h-2 w-full bg-linear-to-r from-emerald-500 via-lime-500 to-teal-500"></div>
			<div class="grid grid-cols-1 gap-4 p-5 md:grid-cols-[1fr_auto] md:items-center md:p-6">
				<div class="space-y-2">
					<p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">Feedback Pengguna</p>
					<h1 class="text-2xl font-bold tracking-tight text-zinc-900 md:text-3xl">Feed Review Naik Bus Linus Express</h1>
					<p class="max-w-3xl text-sm text-zinc-600 md:text-base">
						Ringkasan pengalaman pengguna selama perjalanan. Kontrol di bawah ini masih bersifat visual untuk tahap awal desain.
					</p>
				</div>
				<div class="inline-flex items-center gap-2 rounded-lg bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-700">
					<span class="h-2 w-2 rounded-full bg-emerald-500"></span>
					<?= count($reviewFeed) ?> review terbaru
				</div>
			</div>
		</header>

		<section class="rounded-lg border border-zinc-300 bg-white p-4 shadow-sm md:p-5" aria-label="Form review penumpang">
			<div class="mb-4">
				<h2 class="text-base font-semibold text-zinc-900 md:text-lg">Tulis Review Perjalanan</h2>
				<p class="text-xs text-zinc-500 md:text-sm">Review tersimpan ke database dan langsung tampil di feed.</p>
			</div>

			<?php if ($formStatus['submitted']): ?>
				<div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
					Review berhasil ditambahkan ke feed.
				</div>
			<?php endif; ?>

			<?php if ($formStatus['error'] !== ''): ?>
				<div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
					<?= feedbackSafeText($formStatus['error']) ?>
				</div>
			<?php endif; ?>

			<form method="post" class="grid grid-cols-1 gap-3 md:grid-cols-2" aria-label="Form kirim review">
				<label class="block">
					<span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-zinc-500">Nama Penumpang</span>
					<input
						type="text"
						name="username"
						value="<?= feedbackSafeText($formInput['username']) ?>"
						placeholder="Contoh: Dita Rahma"
						class="w-full rounded-lg border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-700 outline-none transition focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100">
				</label>

				<label class="block">
					<span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-zinc-500">Rating</span>
					<select
						name="rating"
						class="w-full rounded-lg border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-700 outline-none transition focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100">
						<?php for ($optionRating = 5; $optionRating >= 1; $optionRating--): ?>
							<?php $optionValue = (string) $optionRating; ?>
							<option value="<?= $optionValue ?>" <?= $formInput['rating'] === $optionValue ? 'selected' : '' ?>>
								<?= $optionRating ?> Bintang
							</option>
						<?php endfor; ?>
					</select>
				</label>

				<label class="block">
					<span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-zinc-500">Label Bus (Opsional)</span>
					<input
						type="text"
						name="bus_label"
						value="<?= feedbackSafeText($formInput['bus_label']) ?>"
						placeholder="Contoh: Bus 02"
						class="w-full rounded-lg border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-700 outline-none transition focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100">
				</label>

				<label class="block">
					<span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-zinc-500">Rute (Opsional)</span>
					<input
						type="text"
						name="route_label"
						value="<?= feedbackSafeText($formInput['route_label']) ?>"
						placeholder="Contoh: FMIPA -> Perpustakaan"
						class="w-full rounded-lg border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-700 outline-none transition focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100">
				</label>

				<label class="block md:col-span-2">
					<span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-zinc-500">Komentar</span>
					<textarea
						name="comment"
						rows="4"
						placeholder="Ceritakan pengalaman perjalanan kamu..."
						class="w-full resize-y rounded-lg border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-700 outline-none transition focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100"><?= feedbackSafeText($formInput['comment']) ?></textarea>
				</label>

				<div class="md:col-span-2">
					<button
						type="submit"
						name="submit_review"
						value="1"
						class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">
						Kirim Review
					</button>
				</div>
			</form>
		</section>

		<section class="rounded-lg border border-zinc-300 bg-white p-4 shadow-sm md:p-5" aria-label="Kontrol review">
			<div class="grid grid-cols-1 gap-4 md:grid-cols-[1fr_auto] md:items-center">
				<div>
					<p class="mb-2 text-xs font-semibold uppercase tracking-wide text-zinc-500">Filter Rating</p>
					<form method="get" class="flex flex-wrap gap-2">
						<input type="hidden" name="sort" value="<?= feedbackSafeText($selectedSort) ?>">
						<?php foreach ($ratingFilterOptions as $optionValue => $optionLabel): ?>
							<?php
							$isActiveFilter = ($selectedFilter === $optionValue);
							$filterClass = $isActiveFilter
								? 'bg-emerald-600 text-white border-emerald-600'
								: 'bg-zinc-50 text-zinc-700 border-zinc-300 hover:bg-zinc-100';
							?>
							<button
								type="submit"
								name="rating"
								value="<?= feedbackSafeText($optionValue) ?>"
								class="rounded-lg border px-3 py-1.5 text-xs font-semibold transition <?= $filterClass ?>"
								aria-pressed="<?= $isActiveFilter ? 'true' : 'false' ?>">
								<?= feedbackSafeText($optionLabel) ?>
							</button>
						<?php endforeach; ?>
					</form>
				</div>

				<div class="md:min-w-52">
					<form method="get">
						<input type="hidden" name="rating" value="<?= feedbackSafeText($selectedFilter) ?>">
						<label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-zinc-500">Urutkan</label>
						<div class="relative">
							<select
								name="sort"
								class="w-full appearance-none rounded-lg border border-zinc-300 bg-zinc-50 px-3 py-2 pr-9 text-sm font-medium text-zinc-700 outline-none transition focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100"
								aria-label="Urutkan review">
								<?php foreach ($sortOptions as $sortValue => $sortLabel): ?>
									<option value="<?= feedbackSafeText($sortValue) ?>" <?= $selectedSort === $sortValue ? 'selected' : '' ?>>
										<?= feedbackSafeText($sortLabel) ?>
									</option>
								<?php endforeach; ?>
							</select>
							<span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400">&#9662;</span>
						</div>
						<button
							type="submit"
							class="mt-2 inline-flex w-full items-center justify-center rounded-lg border border-emerald-600 px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-50">
							Terapkan
						</button>
					</form>
				</div>
			</div>
		</section>

		<?php if ($hasReviews): ?>
			<section class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-2" aria-label="Daftar review pengguna">
				<?php foreach ($reviewFeed as $review): ?>
					<?php
					$username = isset($review['username']) ? (string) $review['username'] : 'Pengguna Linus';
					$rawRating = isset($review['rating']) ? (int) $review['rating'] : 3;
					$rating = max(1, min(5, $rawRating));
					$comment = isset($review['comment']) ? trim((string) $review['comment']) : '';
					$dateText = isset($review['date']) ? feedbackDisplayDate((string) $review['date']) : 'Tanggal belum tersedia';
					$busLabel = isset($review['bus_label']) ? trim((string) $review['bus_label']) : '';
					$routeLabel = isset($review['route_label']) ? trim((string) $review['route_label']) : '';
					$toneClass = $ratingToneClasses[$rating] ?? 'bg-zinc-100 text-zinc-700 border-zinc-200';
					?>

					<article class="group relative flex h-full flex-col overflow-hidden rounded-lg border border-zinc-300 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:border-zinc-400 hover:shadow-lg">
						<div class="pointer-events-none absolute right-0 top-0 h-20 w-20 translate-x-6 -translate-y-6 rounded-full bg-emerald-100/70 blur-2xl"></div>
						<div class="flex items-start gap-3">
							<div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-linear-to-br from-zinc-800 to-zinc-600 text-sm font-bold tracking-wide text-white shadow-sm">
								<?= feedbackSafeText(feedbackInitials($username)) ?>
							</div>

							<div class="min-w-0 flex-1 space-y-2">
								<div class="flex flex-wrap items-center justify-between gap-2">
									<h2 class="text-base font-semibold text-zinc-900 md:text-lg"><?= feedbackSafeText($username) ?></h2>
									<span class="text-xs font-medium text-zinc-500"><?= feedbackSafeText($dateText) ?></span>
								</div>

								<div class="flex flex-wrap items-center gap-2">
									<span class="text-base leading-none text-amber-500" aria-label="Rating <?= $rating ?> dari 5">
										<?= feedbackRenderStars($rating) ?>
									</span>
									<span class="rounded-lg border px-2.5 py-0.5 text-xs font-semibold <?= $toneClass ?>">
										<?= $rating ?>/5
									</span>
								</div>
							</div>
						</div>

						<p class="mt-4 flex-1 rounded-lg bg-zinc-50 p-3 text-sm leading-relaxed text-zinc-700 md:text-base">
							<?= feedbackSafeText($comment !== '' ? $comment : $emptyCommentFallback) ?>
						</p>

						<?php if ($busLabel !== '' || $routeLabel !== ''): ?>
							<div class="mt-4 flex flex-wrap gap-2">
								<?php if ($busLabel !== ''): ?>
									<span class="rounded-lg bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
										<?= feedbackSafeText($busLabel) ?>
									</span>
								<?php endif; ?>

								<?php if ($routeLabel !== ''): ?>
									<span class="rounded-lg bg-zinc-100 px-3 py-1 text-xs font-medium text-zinc-700">
										<?= feedbackSafeText($routeLabel) ?>
									</span>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</section>
		<?php else: ?>
			<section class="rounded-lg border border-dashed border-zinc-300 bg-white p-8 text-center shadow-sm">
				<h2 class="text-lg font-semibold text-zinc-900">Belum Ada Review</h2>
				<p class="mt-2 text-sm text-zinc-600 md:text-base">
					Feed review masih kosong. Data akan muncul setelah ada pengguna yang membagikan pengalaman naik bus.
				</p>
			</section>
		<?php endif; ?>
	</div>
</section>