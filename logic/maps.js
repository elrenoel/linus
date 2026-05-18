const DEFAULT_GMAPS_API_KEY = "AIzaSyCM7oahEyYfzB-D8tV8uobtdMxzo71kr2I";
const DEFAULT_MAP_ID = "DEMO_MAP_ID";
const API_BASE_PATH = "../logic";
const BUS_POLL_INTERVAL_MS = 2000;
const forwardRouteStops = [
  {
    name: "Halte Pintu 4",
    position: { lat: 3.566629039144497, lng: 98.6531368848205 },
  },
  {
    name: "Halte Pintu Jebol",
    position: { lat: 3.559611278387103, lng: 98.65301164273531 },
  },
  {
    name: "Halte FMIPA",
    position: { lat: 3.5589876083728704, lng: 98.65555557098493 },
  },
  {
    name: "Halte FEB",
    position: { lat: 3.5579196734588012, lng: 98.65611456424476 },
  },
  {
    name: "Halte FISIP",
    position: { lat: 3.5563361572450822, lng: 98.65667192132797 },
  },
  {
    name: "Halte Taman Cinta USU",
    position: { lat: 3.556388817514585, lng: 98.6602460119701 },
  },
  {
    name: "Halte Pintu Sumber/Hukum",
    position: { lat: 3.5592479242449615, lng: 98.66029301921569 },
  },
  {
    name: "Halte Pintu 1",
    position: { lat: 3.566126958176594, lng: 98.66007100578203 },
  },
];

const reverseRouteStops = [
  {
    name: "Halte Pintu 1",
    position: { lat: 3.566126958176594, lng: 98.66007100578203 },
  },
  {
    name: "Halte Pintu Sumber/Hukum",
    position: { lat: 3.5593333880160465, lng: 98.66041372611538 },
  },
  {
    name: "Halte Taman Cinta USU",
    position: { lat: 3.55635222468508, lng: 98.6601869266466 },
  },
  {
    name: "Halte FISIP",
    position: { lat: 3.5563361572450822, lng: 98.65667192132797 },
  },
  {
    name: "Halte FEB",
    position: { lat: 3.5579196734588012, lng: 98.65611456424476 },
  },
  {
    name: "Halte FMIPA",
    position: { lat: 3.5589876083728704, lng: 98.65555557098493 },
  },
  {
    name: "Halte Pintu Jebol",
    position: { lat: 3.5594903317292714, lng: 98.65288413435542 },
  },
  {
    name: "Halte Pintu 4",
    position: { lat: 3.566738676056855, lng: 98.65315597444588 },
  },
];

const BUS_MOCKS = [
  {
    id: 1,
    label: "Linus 1",
    plat: "",
    statusKey: "sedang_berjalan",
    statusLabel: "Sedang Berjalan",
    tujuan: "Halte FMIPA",
    startRoute: "forward",
    startStop: "Halte Pintu 4",
    followMap: true,
  },
  {
    id: 2,
    label: "Linus 2",
    plat: "",
    statusKey: "menuju_halte",
    statusLabel: "Menuju Halte",
    tujuan: "Halte Pintu Sumber/Hukum",
    startRoute: "reverse",
    startStop: "Halte Pintu 1",
    followMap: false,
  },
  {
    id: 3,
    label: "Linus 3",
    plat: "",
    statusKey: "sedang_berhenti",
    statusLabel: "Sedang Berhenti",
    tujuan: "Halte FISIP",
    startRoute: "reverse",
    startStop: "Halte FISIP",
    followMap: false,
  },
];

const errorElement = document.getElementById("map-error");
let map;
let AdvancedMarkerElement;
const busMarkers = new Map();
let busPollerId = null;

function showError(message) {
  errorElement.textContent = message;
  errorElement.classList.remove("hidden");
}

function getApiKey() {
  const queryKey = new URLSearchParams(window.location.search).get("gmapsKey");
  const metaKey = document.querySelector(
    'meta[name="google-maps-api-key"]',
  )?.content;
  const globalKey = window.GOOGLE_MAPS_API_KEY;
  return queryKey || metaKey || globalKey || DEFAULT_GMAPS_API_KEY;
}

function getMapId() {
  const queryMapId = new URLSearchParams(window.location.search).get(
    "gmapsMapId",
  );
  const metaMapId = document.querySelector(
    'meta[name="google-maps-map-id"]',
  )?.content;
  const globalMapId = window.GOOGLE_MAPS_MAP_ID;
  return queryMapId || metaMapId || globalMapId || DEFAULT_MAP_ID;
}

function getMapMode() {
  const mode = new URLSearchParams(window.location.search).get("mode");
  return mode === "detail" ? "detail" : "default";
}

function getBusIdFromQuery() {
  return new URLSearchParams(window.location.search).get("bus_id") || "";
}

function createBusLabel(bus) {
  return bus.label ? `Bus ${bus.label}` : `Bus ${bus.id}`;
}

function normalizeBus(raw) {
  return {
    id: raw.id,
    label: raw.label || "",
    plat: raw.plat || "",
    supir: raw.supir || "",
    statusKey: raw.status_key || raw.statusKey || "sedang_berhenti",
    statusLabel: raw.status_label || raw.statusLabel || "Sedang Berhenti",
    tujuan: raw.tujuan || "-",
    location: raw.location || null,
    followMap: Boolean(raw.followMap),
    startStop: raw.startStop || "",
  };
}

async function fetchBusList() {
  const response = await fetch(`${API_BASE_PATH}/api_bus_list.php`);
  if (!response.ok) {
    throw new Error(`HTTP_${response.status}`);
  }
  const data = await response.json();
  if (!data.ok) {
    throw new Error(data.error || "Gagal memuat data bus.");
  }
  return Array.isArray(data.buses) ? data.buses.map(normalizeBus) : [];
}

async function fetchBusDetail(busId) {
  const response = await fetch(
    `${API_BASE_PATH}/api_bus_detail.php?bus_id=${encodeURIComponent(busId)}`,
  );
  if (!response.ok) {
    throw new Error(`HTTP_${response.status}`);
  }
  const data = await response.json();
  if (!data.ok) {
    throw new Error(data.error || "Gagal memuat detail bus.");
  }
  return normalizeBus(data.bus || {});
}

function toRad(value) {
  return value * (Math.PI / 180);
}

function distanceMeters(a, b) {
  const earthRadius = 6371000;
  const dLat = toRad(b.lat - a.lat);
  const dLng = toRad(b.lng - a.lng);
  const lat1 = toRad(a.lat);
  const lat2 = toRad(b.lat);
  const x =
    Math.sin(dLat / 2) ** 2 +
    Math.cos(lat1) * Math.cos(lat2) * Math.sin(dLng / 2) ** 2;
  return earthRadius * (2 * Math.atan2(Math.sqrt(x), Math.sqrt(1 - x)));
}

function buildPathMetrics(path) {
  const cumulative = [0];
  for (let i = 1; i < path.length; i++) {
    cumulative[i] = cumulative[i - 1] + distanceMeters(path[i - 1], path[i]);
  }
  return {
    path,
    cumulative,
    totalDistance: cumulative[cumulative.length - 1] || 0,
  };
}

function interpolatePosition(metrics, targetDistance) {
  if (metrics.path.length === 1 || metrics.totalDistance === 0) {
    return metrics.path[0];
  }

  for (let i = 1; i < metrics.cumulative.length; i++) {
    const prevDistance = metrics.cumulative[i - 1];
    const nextDistance = metrics.cumulative[i];
    if (targetDistance <= nextDistance) {
      const segmentDistance = nextDistance - prevDistance || 1;
      const ratio = (targetDistance - prevDistance) / segmentDistance;
      const start = metrics.path[i - 1];
      const end = metrics.path[i];
      return {
        lat: start.lat + (end.lat - start.lat) * ratio,
        lng: start.lng + (end.lng - start.lng) * ratio,
      };
    }
  }

  return metrics.path[metrics.path.length - 1];
}

function decodePolyline(encoded) {
  const points = [];
  let index = 0;
  let lat = 0;
  let lng = 0;

  while (index < encoded.length) {
    let shift = 0;
    let result = 0;
    let byte;
    do {
      byte = encoded.charCodeAt(index++) - 63;
      result |= (byte & 0x1f) << shift;
      shift += 5;
    } while (byte >= 0x20);
    const latDelta = result & 1 ? ~(result >> 1) : result >> 1;
    lat += latDelta;

    shift = 0;
    result = 0;
    do {
      byte = encoded.charCodeAt(index++) - 63;
      result |= (byte & 0x1f) << shift;
      shift += 5;
    } while (byte >= 0x20);
    const lngDelta = result & 1 ? ~(result >> 1) : result >> 1;
    lng += lngDelta;

    points.push({ lat: lat / 1e5, lng: lng / 1e5 });
  }

  return points;
}

function parseDurationSeconds(durationValue) {
  if (!durationValue) {
    return 0;
  }
  return Number(String(durationValue).replace("s", "")) || 0;
}

function formatDuration(seconds) {
  if (seconds < 60) {
    return `${seconds} detik`;
  }
  const minutes = Math.floor(seconds / 60);
  const remainSeconds = seconds % 60;
  return remainSeconds === 0
    ? `${minutes} menit`
    : `${minutes} menit ${remainSeconds} detik`;
}

function drawStopMarkers() {
  const allStops = [...forwardRouteStops, ...reverseRouteStops];
  const seen = new Set();

  allStops.forEach((stop) => {
    const key = `${stop.position.lat.toFixed(12)},${stop.position.lng.toFixed(12)}`;
    if (seen.has(key)) {
      return;
    }
    seen.add(key);

    const dot = document.createElement("div");
    dot.style.width = "14px";
    dot.style.height = "14px";
    dot.style.borderRadius = "9999px";
    dot.style.backgroundColor = "#dc2626";
    dot.style.border = "2px solid #ffffff";
    dot.style.boxShadow = "0 2px 6px rgba(0,0,0,0.35)";

    const marker = new AdvancedMarkerElement({
      map,
      position: stop.position,
      title: stop.name,
      content: dot,
      zIndex: 999,
    });

    marker.addEventListener("gmp-click", () => {
      new google.maps.InfoWindow({ content: stop.name }).open({
        map,
        anchor: marker,
      });
    });
  });
}

function stopToWaypoint(stop) {
  return {
    location: {
      latLng: {
        latitude: stop.position.lat,
        longitude: stop.position.lng,
      },
    },
  };
}

async function requestRoute(stops, apiKey) {
  const payload = {
    origin: stopToWaypoint(stops[0]),
    destination: stopToWaypoint(stops[stops.length - 1]),
    intermediates: stops.slice(1, -1).map(stopToWaypoint),
    travelMode: "DRIVE",
    routingPreference: "TRAFFIC_UNAWARE",
    polylineQuality: "HIGH_QUALITY",
    polylineEncoding: "ENCODED_POLYLINE",
    languageCode: "id-ID",
    units: "METRIC",
  };

  const response = await fetch(
    "https://routes.googleapis.com/directions/v2:computeRoutes",
    {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Goog-Api-Key": apiKey,
        "X-Goog-FieldMask":
          "routes.polyline.encodedPolyline,routes.legs.duration,routes.legs.distanceMeters,routes.legs.polyline.encodedPolyline",
      },
      body: JSON.stringify(payload),
    },
  );

  const data = await response.json();
  if (!response.ok || !data.routes || data.routes.length === 0) {
    const apiStatus = data.error?.status || `HTTP_${response.status}`;
    const apiMessage = data.error?.message || "Routes API request denied";
    throw new Error(`${apiStatus}: ${apiMessage}`);
  }

  const route = data.routes[0];
  const legs = route.legs.map((leg, index) => {
    const durationSec = parseDurationSeconds(leg.duration);
    return {
      from: stops[index].name,
      to: stops[index + 1].name,
      durationSec,
      durationText: formatDuration(durationSec),
      path: decodePolyline(leg.polyline.encodedPolyline || ""),
    };
  });

  return {
    path: decodePolyline(route.polyline.encodedPolyline || ""),
    legs,
  };
}

function drawRoutePolyline(path, color) {
  new google.maps.Polyline({
    path,
    geodesic: true,
    strokeColor: color,
    strokeOpacity: 0.9,
    strokeWeight: 5,
    map,
  });
}

function findStopPositionByName(stopName) {
  const combined = [...forwardRouteStops, ...reverseRouteStops];
  const matched = combined.find((stop) => stop.name === stopName);
  return matched ? matched.position : forwardRouteStops[0].position;
}

function resolveBusStartPosition(bus) {
  return findStopPositionByName(bus.startStop);
}

function resolveBusPosition(bus) {
  if (bus.location && Number.isFinite(bus.location.lat)) {
    return bus.location;
  }
  return null;
}

function createBusImage() {
  const busImage = document.createElement("img");
  busImage.src = "../assets/location.png";
  busImage.className = "select-none";
  busImage.style.width = "32px";
  busImage.style.height = "32px";
  busImage.alt = "Bus Linus";
  return busImage;
}

function findLegStartIndex(legs, stopName) {
  const index = legs.findIndex((leg) => leg.from === stopName);
  return index >= 0 ? index : 0;
}

function createBusState({ title, startPosition, followMap = false }) {
  const marker = new AdvancedMarkerElement({
    map,
    position: startPosition,
    title,
    content: createBusImage(),
    zIndex: 1000,
  });

  return {
    marker,
    infoWindow: new google.maps.InfoWindow({ content: title }),
    followMap,
  };
}

function openBusInfoWindow(bus, busState) {
  const title = createBusLabel(bus);
  const content = `
    <div style="font-size:13px;line-height:1.5;min-width:170px;">
      <div style="font-weight:700;color:#0f172a;">${title}</div>
      <div style="color:#334155;">${bus.plat || "-"}</div>
      <div style="margin-top:6px;"><b>Status:</b> ${bus.statusLabel}</div>
      <div><b>Tujuan:</b> ${bus.tujuan}</div>
    </div>
  `;
  busState.infoWindow.setContent(content);
  busState.infoWindow.open({ map, anchor: busState.marker });
}

function ensureBusMarker(bus) {
  const busId = String(bus.id);
  let busState = busMarkers.get(busId);
  if (!busState) {
    busState = createBusState({
      title: createBusLabel(bus),
      startPosition: resolveBusPosition(bus),
      followMap: bus.followMap,
    });
    busState.marker.addListener("gmp-click", () =>
      openBusInfoWindow(bus, busState),
    );
    busMarkers.set(busId, busState);
  }

  const position = resolveBusPosition(bus);
  busState.marker.position = position;
  busState.marker.title = createBusLabel(bus);
  busState.followMap = bus.followMap;
  if (busState.followMap) {
    map.panTo(position);
  }
  return busState;
}

function syncBusMarkers(buses) {
  const activeIds = new Set(buses.map((bus) => String(bus.id)));
  for (const [busId, busState] of busMarkers.entries()) {
    if (!activeIds.has(busId)) {
      busState.infoWindow.close();
      busState.marker.map = null;
      busMarkers.delete(busId);
    }
  }

  buses.forEach((bus) => {
    ensureBusMarker(bus);
  });
}

async function initMap() {
  const apiKey = getApiKey();
  map = new google.maps.Map(document.getElementById("map"), {
    center: forwardRouteStops[0].position,
    zoom: 15.7,
    gestureHandling: "cooperative",
    mapTypeControl: false,
    streetViewControl: false,
    fullscreenControl: true,
    mapId: getMapId(),
  });

  ({ AdvancedMarkerElement } = await google.maps.importLibrary("marker"));
  drawStopMarkers();

  try {
    const forwardTrip = await requestRoute(forwardRouteStops, apiKey);
    const reverseTrip = await requestRoute(reverseRouteStops, apiKey);

    drawRoutePolyline(forwardTrip.path, "#1d4ed8");
    drawRoutePolyline(reverseTrip.path, "#4f46e5");

    const bounds = new google.maps.LatLngBounds();
    [...forwardRouteStops, ...reverseRouteStops].forEach((stop) =>
      bounds.extend(stop.position),
    );
    map.fitBounds(bounds, 40);

    if (getMapMode() === "detail") {
      const busId = getBusIdFromQuery();
      if (!busId) {
        showError(
          "Bus detail tidak ditemukan. Kembali ke daftar Info Bus untuk memilih bus yang valid.",
        );
        return;
      }

      const updateDetail = async () => {
        try {
          const selectedBus = await fetchBusDetail(busId);
          selectedBus.followMap = true;
          const detailBusState = ensureBusMarker(selectedBus);
          openBusInfoWindow(selectedBus, detailBusState);
          map.setCenter(resolveBusPosition(selectedBus));
          map.setZoom(16.3);
        } catch (error) {
          showError(`Gagal memuat detail bus: ${error.message}`);
        }
      };

      await updateDetail();
      busPollerId = window.setInterval(updateDetail, BUS_POLL_INTERVAL_MS);
      return;
    }

    const updateDashboard = async () => {
      try {
        const buses = await fetchBusList();
        if (buses.length === 0) {
          syncBusMarkers(BUS_MOCKS.map(normalizeBus));
          return;
        }
        syncBusMarkers(buses);
      } catch (error) {
        showError(`Gagal memuat bus: ${error.message}`);
        syncBusMarkers(BUS_MOCKS.map(normalizeBus));
      }
    };

    await updateDashboard();
    busPollerId = window.setInterval(updateDashboard, BUS_POLL_INTERVAL_MS);
  } catch (error) {
    showError(`Gagal memuat rute: ${error.message}`);
  }
}

(function loadGoogleMaps() {
  const apiKey = getApiKey();
  if (!apiKey) {
    showError("Google Maps API key belum diatur.");
    return;
  }

  window.gm_authFailure = () => {
    showError(
      "Google Maps gagal otorisasi. Cek API key, billing aktif, dan referrer localhost/127.0.0.1.",
    );
  };
  window.initMap = initMap;
  const script = document.createElement("script");
  script.src = `https://maps.googleapis.com/maps/api/js?key=${encodeURIComponent(apiKey)}&callback=initMap&loading=async&libraries=marker`;
  script.async = true;
  script.defer = true;
  script.onerror = () => showError("Gagal memuat Google Maps API.");
  document.head.appendChild(script);
})();
