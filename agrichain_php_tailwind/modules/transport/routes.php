<?php
require_once __DIR__.'/../../config/auth.php';
roles_allowed(['transporter','admin']);

include __DIR__.'/../../includes/header.php';
include __DIR__.'/../../includes/navbar.php';
?>

<div class="max-w-6xl mx-auto p-6">
    <h1 class="text-2xl font-bold text-green-700 mb-4">🛣 Route Optimization (Real)</h1>
    <p class="text-gray-600 mb-6">
        Enter your start location and stops below. Google Maps will calculate the optimized driving route.
    </p>

    <!-- Route Input Form -->
    <form id="routeForm" method="POST" class="bg-gray-50 p-4 rounded-xl shadow mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <input type="text" id="start" name="start" placeholder="Start Location" required class="p-2 border rounded">
        <input type="text" id="stops" name="stops" placeholder="Stops (comma separated)" required class="p-2 border rounded">
        <button type="submit" class="bg-green-600 text-white py-2 rounded hover:bg-green-700">Show Route</button>
    </form>

    <!-- Map -->
    <div id="map" class="w-full h-96 rounded-2xl border shadow"></div>
</div>

<!-- Google Maps JS -->
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places"></script>

<script>
let map;
let directionsService;
let directionsRenderer;

function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 23.8103, lng: 90.4125 }, // Dhaka default
        zoom: 12
    });
    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer({ map: map });
}

document.getElementById('routeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const start = document.getElementById('start').value;
    const stops = document.getElementById('stops').value.split(',').map(s => s.trim()).filter(s => s.length > 0);

    const waypoints = stops.map(location => ({ location, stopover: true }));

    directionsService.route(
        {
            origin: start,
            destination: start,
            waypoints: waypoints,
            optimizeWaypoints: true,
            travelMode: google.maps.TravelMode.DRIVING
        },
        (result, status) => {
            if (status === 'OK') {
                directionsRenderer.setDirections(result);

                // Optional: show order in console
                const order = result.routes[0].waypoint_order.map(i => stops[i]);
                console.log("Optimized Stop Order:", order);
            } else {
                alert("Error calculating route: " + status);
            }
        }
    );
});

initMap();
</script>

<?php include __DIR__.'/../../includes/footer.php'; ?>
