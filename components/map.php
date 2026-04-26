<?php
$mapQuery = [];

if (isset($_GET['mode']) && $_GET['mode'] === 'detail') {
    $mapQuery['mode'] = 'detail';
}

if (isset($_GET['bus_id'])) {
    $busId = preg_replace('/[^a-zA-Z0-9\-]/', '', (string) $_GET['bus_id']);
    if ($busId !== '') {
        $mapQuery['bus_id'] = $busId;
    }
}

$mapSrc = 'components/map.html';
if (!empty($mapQuery)) {
    $mapSrc .= '?' . http_build_query($mapQuery);
}
?>

<div class="h-full flex-1 w-full overflow-hidden">
    <iframe
        src="<?= htmlspecialchars($mapSrc, ENT_QUOTES, 'UTF-8') ?>"
        title="Peta Tracking Bus Linus"
        class="w-full h-full"
    ></iframe>
</div>