<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SenseBox Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Leaflet for OpenStreetMap -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Chart.js for graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <?php include 'menu.php'; ?>

    <main class="max-w-7xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Live Sensor Data & Map</h2>
        <?php include 'box.php'; ?>
    </main>
</body>
</html>
