<?php
require_once __DIR__ . '/../../config/auth.php';
roles_allowed(['transporter','admin']);

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
?>

<div class="max-w-6xl mx-auto p-6">
    <h1 class="text-2xl font-bold text-green-700 mb-4">🚚 Live Map & GPS</h1>
    <p class="text-gray-600 mb-4">Track your fleet in real-time on the map below.</p>

    <!-- Map container -->
    <div id="map" class="w-full h-[500px] rounded-2xl border shadow"></div>
</div>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
// Initialize map (center: Dhaka)
var map = L.map('map').setView([23.8103, 90.4125], 12);

// Add OpenStreetMap layer
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

// Example fleet data (future: fetch from DB/API via AJAX)
var vehicles = [
    {id: 1, name: "Truck #1", lat: 23.8103, lng: 90.4125, status: "En Route"},
    {id: 2, name: "Van #2", lat: 23.8150, lng: 90.4200, status: "Idle"},
    {id: 3, name: "Truck #3", lat: 23.8000, lng: 90.4000, status: "Delivering"}
];

// Store markers
var markers = [];

// Create markers
vehicles.forEach(v => {
    let marker = L.marker([v.lat, v.lng]).addTo(map)
        .bindPopup(`<b>${v.name}</b><br>Status: ${v.status}<br>Last Update: ${new Date().toLocaleTimeString()}`);
    markers.push(marker);
});

// Auto-fit map to show all vehicles
var group = L.featureGroup(markers);
map.fitBounds(group.getBounds().pad(0.2));

// Simulate live GPS updates
setInterval(() => {
    markers.forEach((m, idx) => {
        let lat = vehicles[idx].lat + (Math.random() - 0.5) * 0.01;
        let lng = vehicles[idx].lng + (Math.random() - 0.5) * 0.01;
        m.setLatLng([lat, lng]);
        m.setPopupContent(`<b>${vehicles[idx].name}</b><br>Status: ${vehicles[idx].status}<br>Last Update: ${new Date().toLocaleTimeString()}`);
    });
}, 5000);
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
