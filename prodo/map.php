<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['uid'])) {
    header('Location: signin.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Map - Prodo</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        body {
            overflow: hidden;
            margin: 0;
            padding: 0;
        }
        #fullMap {
            height: 100vh;
            width: 100vw;
            z-index: 1000;
        }
        
        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: #121212;
            width: 40px;
            height: 40px;
            border-radius: 50px;
            border: 1px solid gray;
            color: #ffffff;
            font-size: 24px;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0;
            margin: 0;
            z-index: 1010;
        }
        
        .leaflet-control-zoom {
            position: fixed !important;
            bottom: 20px !important;
            right: 20px !important;
            top: auto !important; /* Override default top position */
        }
        
        .leaflet-control-zoom-in,
        .leaflet-control-zoom-out {
            background: #d7be95 !important;
            color: black !important;
            border: none !important;
            width: 40px !important;
            height: 44px !important;
            line-height: 40px !important;
            font-size: 18px !important;
            border-radius: 8px !important;
            margin-bottom: 5px !important;
        }
        .leaflet-bar {
            border: none !important;
        }
    </style>
</head>
<body>
    <a href="weather.php" class="back-button">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15 19L9 13L15 7" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>  
    </a>
    <div id="fullMap"></div>

    <script>
        // Get coordinates from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const lat = parseFloat(urlParams.get('lat'));
        const lng = parseFloat(urlParams.get('lng'));

        // Initialize full map
        function initFullMap() {
            const map = L.map('fullMap', {
                zoomControl: false  // Disable default zoom control
            }).setView([lat, lng], 12);
            
            // Add custom positioned zoom control
            L.control.zoom({
                position: 'bottomright'
            }).addTo(map);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Add marker
            L.marker([lat, lng]).addTo(map);
        }

        // Initialize map when page loads
        window.onload = initFullMap;
    </script>
</body>
</html> 