const form = document.getElementById("driver-form");
const busSelect = document.getElementById("bus-id");
const startButton = document.getElementById("start-tracking");
const stopButton = document.getElementById("stop-tracking");
const statusEl = document.getElementById("driver-status");

let watchId = null;
let lastPosition = null;
let sendTimerId = null;
const minIntervalMs = 3000;

function setStatus(message, isError = false) {
  statusEl.textContent = message;
  statusEl.className = isError
    ? "mt-2 text-sm text-red-600"
    : "mt-2 text-sm text-zinc-700";
}

function toggleButtons(isTracking) {
  startButton.disabled = isTracking;
  stopButton.disabled = !isTracking;
  busSelect.disabled = isTracking;
}

async function sendLocation(apiUrl, payload) {
  const response = await fetch(apiUrl, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(payload),
  });

  const data = await response.json().catch(() => ({ ok: false }));
  if (!response.ok || !data.ok) {
    const message = data.error || `HTTP_${response.status}`;
    throw new Error(message);
  }
  return data;
}

function buildPayload(busId, position) {
  const coords = position.coords;
  const speedKmh = Number.isFinite(coords.speed) ? coords.speed * 3.6 : null;
  const heading = Number.isFinite(coords.heading) ? coords.heading : null;
  const accuracy = Number.isFinite(coords.accuracy) ? coords.accuracy : null;

  return {
    bus_id: Number(busId),
    lat: coords.latitude,
    lng: coords.longitude,
    speed_kmh: speedKmh,
    heading_deg: heading,
    accuracy_m: accuracy,
    source: "gps",
  };
}

function onPosition(apiUrl, busId, position) {
  lastPosition = position;
  if (!sendTimerId) {
    sendTimerId = window.setInterval(() => {
      if (!lastPosition) return;
      const payload = buildPayload(busId, lastPosition);
      sendLocation(apiUrl, payload)
        .then(() => {
          setStatus(
            `Lokasi terkirim: ${payload.lat.toFixed(6)}, ${payload.lng.toFixed(6)}`,
          );
        })
        .catch((error) => {
          setStatus(`Gagal kirim lokasi: ${error.message}`, true);
        });
    }, minIntervalMs);
  }
}

function onPositionError(error) {
  setStatus(`GPS error: ${error.message}`, true);
  toggleButtons(false);
  if (watchId !== null) {
    navigator.geolocation.clearWatch(watchId);
    watchId = null;
  }
}

startButton?.addEventListener("click", () => {
  const busId = busSelect.value;
  if (!busId) {
    setStatus("Pilih bus dulu sebelum mulai tracking.", true);
    return;
  }

  if (!navigator.geolocation) {
    setStatus("Browser tidak mendukung GPS.", true);
    return;
  }

  const apiUrl = form?.dataset.apiUrl;
  if (!apiUrl) {
    setStatus("API URL tidak ditemukan.", true);
    return;
  }

  toggleButtons(true);
  setStatus("Mengaktifkan tracking...", false);

  watchId = navigator.geolocation.watchPosition(
    (position) => onPosition(apiUrl, busId, position),
    onPositionError,
    {
      enableHighAccuracy: true,
      maximumAge: 0,
      timeout: 10000,
    },
  );
});

stopButton?.addEventListener("click", () => {
  if (watchId !== null) {
    navigator.geolocation.clearWatch(watchId);
    watchId = null;
  }
  toggleButtons(false);
  setStatus("Tracking dihentikan.");
  if (sendTimerId !== null) {
    clearInterval(sendTimerId);
    sendTimerId = null;
  }
});
