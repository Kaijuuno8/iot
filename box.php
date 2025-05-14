<?php
require_once 'config.php';
?>

<!-- Countdown Timer -->
<div class="mb-4 text-sm text-gray-700">
    Refreshing in <span id="countdown" class="font-semibold text-blue-600">15</span> seconds...
</div>

<!-- Coordinates Box -->
<div id="coordinateBox" class="bg-white shadow rounded p-4 border border-gray-200 mb-6">
    <!-- Coordinates will be loaded here -->
</div>

<!-- Map -->
<div id="map" class="w-full h-96 rounded mb-6 border border-gray-300"></div>

<!-- Sensor Cards Container -->
<div id="sensorCards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Cards with charts will be injected here -->
</div>

<script>
// Chart instances (used to update charts instead of recreating)
let chartInstances = {};

// Countdown Timer
let countdown = 15;
function updateCountdown() {
    document.getElementById('countdown').textContent = countdown;
    countdown--;
    if (countdown < 0) countdown = 15;
}
setInterval(updateCountdown, 1000);

// Initialize Map
let map = L.map('map').setView([4.9031, 114.9398], 15); // default coords
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: 'Map data © OpenStreetMap contributors',
    maxZoom: 19,
}).addTo(map);
let marker = L.marker([4.9031, 114.9398]).addTo(map).bindPopup('Sensor Location');

// Load Data Function
function loadSensorData() {
    fetch('<?= SENSEBOX_API_URL ?>')
        .then(res => res.json())
        .then(data => {
            // Coordinates
            const coords = data.currentLocation?.coordinates || [0, 0];
            const longitude = coords[0];
            const latitude = coords[1];

            // Update map
            map.setView([latitude, longitude], 15);
            marker.setLatLng([latitude, longitude]);

            // Update coordinates box
            document.getElementById('coordinateBox').innerHTML = `
                <h2 class="text-xl font-semibold mb-2">Coordinates</h2>
                <p class="text-gray-700"><strong>Latitude:</strong> ${latitude}</p>
                <p class="text-gray-700"><strong>Longitude:</strong> ${longitude}</p>
            `;

            // Sensor cards
            const sensors = data.sensors || [];
            const cardContainer = document.getElementById('sensorCards');
            cardContainer.innerHTML = '';

            sensors.forEach((sensor, index) => {
                const title = sensor.title;
                const value = sensor.lastMeasurement?.value ?? 0;
                const unit = sensor.unit ?? '';
                const createdAt = sensor.lastMeasurement?.createdAt ?? '';
                const canvasId = `sensorChart_${index}`;

                // Create card
                const card = document.createElement('div');
                card.className = 'bg-white p-4 rounded shadow border border-gray-200';
                card.innerHTML = `
                    <h3 class="text-lg font-bold mb-2">${title}</h3>
                    <canvas id="${canvasId}" class="w-full h-40"></canvas>
                    <p class="text-sm text-gray-600 mt-2">
                        Value: <strong>${value} ${unit}</strong><br>
                        Time: ${createdAt}
                    </p>
                `;
                cardContainer.appendChild(card);

                // Update or create chart
                setTimeout(() => {
                    const ctx = document.getElementById(canvasId).getContext('2d');
                    if (chartInstances[canvasId]) {
                        chartInstances[canvasId].data.datasets[0].data = [value];
                        chartInstances[canvasId].data.labels = [createdAt];
                        chartInstances[canvasId].update();
                    } else {
                        chartInstances[canvasId] = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: [createdAt],
                                datasets: [{
                                    label: `${title} (${unit})`,
                                    data: [value],
                                    backgroundColor: 'rgba(96, 165, 250, 0.6)',
                                    borderColor: 'rgba(37, 99, 235, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: { beginAtZero: true }
                                }
                            }
                        });
                    }
                }, 100);
            });
        })
        .catch(err => {
            console.error('Failed to load data:', err);
        });
}

// Initial load
loadSensorData();

// Refresh every 15 seconds
setInterval(() => {
    countdown = 15;
    loadSensorData();
}, 15000);
</script>
