const WAIT_STOP_SECONDS = 10;
const DEFAULT_GMAPS_API_KEY = "AIzaSyCM7oahEyYfzB-D8tV8uobtdMxzo71kr2I";
const DEFAULT_MAP_ID = "DEMO_MAP_ID";
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

const errorElement = document.getElementById("map-error");
let map;
let AdvancedMarkerElement;

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

function pause(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
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

async function animateLeg(leg, busState) {
  const metrics = buildPathMetrics(
    leg.path.length ? leg.path : [forwardRouteStops[0].position],
  );
  const durationMs = Math.max(leg.durationSec, 1) * 1000;
  const startAt = performance.now();

  busState.infoWindow.setContent(`Menuju ${leg.to} (${leg.durationText})`);
  busState.infoWindow.open({ map, anchor: busState.marker });

  return new Promise((resolve) => {
    function step(now) {
      const progress = Math.min((now - startAt) / durationMs, 1);
      const traveled = metrics.totalDistance * progress;
      const position = interpolatePosition(metrics, traveled);
      busState.marker.position = position;

      if (progress < 1) {
        requestAnimationFrame(step);
        return;
      }

      if (busState.followMap) {
        map.panTo(position);
      }
      resolve();
    }

    requestAnimationFrame(step);
  });
}

async function waitAtStop(stopName, busState) {
  busState.infoWindow.setContent(
    `Bus berhenti di: ${stopName} (${WAIT_STOP_SECONDS} detik)`,
  );
  busState.infoWindow.open({ map, anchor: busState.marker });
  await pause(WAIT_STOP_SECONDS * 1000);
}

async function runSimulation(routeSequences, busState, startLegIndex = 0) {
  let routeIndex = 0;
  let legIndex = Math.max(0, startLegIndex);

  while (true) {
    const legs = routeSequences[routeIndex];
    for (let i = legIndex; i < legs.length; i++) {
      await waitAtStop(legs[i].from, busState);
      await animateLeg(legs[i], busState);
    }
    await waitAtStop(legs[legs.length - 1].to, busState);

    legIndex = 0;
    routeIndex = (routeIndex + 1) % routeSequences.length;
  }
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

    const busMain = createBusState({
      title: "Bus Linus 1",
      startPosition: forwardRouteStops[0].position,
      followMap: true,
    });
    const busFromP1 = createBusState({
      title: "Bus Linus 2",
      startPosition: reverseRouteStops[0].position,
      followMap: false,
    });
    const busFromFisip = createBusState({
      title: "Bus Linus 3",
      startPosition: reverseRouteStops[3].position,
      followMap: false,
    });

    runSimulation([forwardTrip.legs, reverseTrip.legs], busMain, 0);
    runSimulation([reverseTrip.legs, forwardTrip.legs], busFromP1, 0);
    runSimulation(
      [reverseTrip.legs, forwardTrip.legs],
      busFromFisip,
      findLegStartIndex(reverseTrip.legs, "Halte FISIP"),
    );
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
