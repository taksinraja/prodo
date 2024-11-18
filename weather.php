<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
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
    <title>Weather Dashboard - Prodo</title>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="images/logo.png" type="image/png">
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        .weather-container {
            padding: 5px;
            color: white;
            margin-top: 0px;
        }

        .search-container {
            margin: 20px 0;
            display: flex;
            gap: 10px;
        }

        .search-container input {
            padding: 15px 10px;
            border-radius: 10px;
            border: none;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            width: 300px;
            font-size: 16px;
        }

        .search-container button {
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            background: #d7be95;
            color: black;
            cursor: pointer;
        }

        .current-weather {
            background: #C8A2D7;
            border-radius: 20px;
            padding: 15px;
            margin: 20px 0;
            font-size: 18px;
            color: black;
        }

        .weather-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 30px;
        }

        .detail-card {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            font-size: 16px;
            background: #313131;
            color: white;
            border-radius: 15px;
            padding: 15px;
        }

        .forecast {
            display:flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            flex-direction: row;
            scrollbar-width: none;
            -ms-overflow-style: none;
            gap: 10px;
            margin-top: 20px;
        }

        .forecast-day {
            background: #d7be95;
            border-radius: 15px;
            padding: 15px;
            text-align: center;
            color: black;
            font-size: 14px;
        }

        .forecast-icon {
            width: 50px;
            height: 50px;
        }

        .back-button {
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
        }

        .map-container {
            margin-top: 30px;
            position: relative;
            width: 100%;
            height: 300px;
            border-radius: 20px;
            overflow: hidden;
            cursor: pointer;
        }

        #map {
            width: 100%;
            height: 100%;
            pointer-events: none; /* Prevents scrolling/zooming on main page */
        }

        .map-overlay {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 10px 20px;
            width: 53%;
            border-radius: 20px;
            font-size: 14px;
            z-index: 1000;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>
    <a href="app.php" class="back-button">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15 19L9 13L15 7" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>  
    </a>

    <div class="weather-container">
        <div class="search-container">
            <input type="text" id="citySearch" placeholder="Search for a city...">
            <button onclick="searchCity()">Search</button>
        </div>

        <div class="current-weather">
            <h2 id="location">Loading...</h2>
            <div style="font-size: 48px;" id="temperature">--°</div>
            <div id="weather-description">--</div>
            <div id="high-low">H:--° L:--°</div>
            
            <div class="weather-details">
                <div class="detail-card">
                    <h3>Feels Like</h3>
                    <div id="feels-like">--°</div>
                </div>
                <div class="detail-card">
                    <h3>Humidity</h3>
                    <div id="humidity">--%</div>
                </div>
                <div class="detail-card">
                    <h3>Wind Speed</h3>
                    <div id="wind-speed">-- km/h</div>
                </div>
                <div class="detail-card">
                    <h3>Pressure</h3>
                    <div id="pressure">-- hPa</div>
                </div>
            </div>
        </div>

        <h2>7-Day Forecast</h2>
        <div class="forecast" id="forecast">
            <!-- Forecast cards will be inserted here -->
        </div>

        <div class="map-container" id="mapContainer">
            <div id="map"></div>
            <div class="map-overlay">Click to view full map</div>
        </div>
    </div>

    <script>
        const API_KEY = 'a2e9c4e128c0d0a65b3c008f12186ec7'; // Replace with your OpenWeather API key

        // Get user's location on page load
        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    getWeatherData(position.coords.latitude, position.coords.longitude);
                    getForecastData(position.coords.latitude, position.coords.longitude);
                }, error => {
                    console.error(error);
                    // Default to a location if geolocation fails
                    searchCity('London');
                });
            }
        }

        // Search city weather
        function searchCity() {
            const city = document.getElementById('citySearch').value;
            if (city) {
                fetch(`https://api.openweathermap.org/data/2.5/weather?q=${city}&units=metric&appid=${API_KEY}`)
                    .then(response => response.json())
                    .then(data => {
                        // Update weather info
                        updateCurrentWeather(data);
                        
                        // Update map with searched city's coordinates
                        const lat = data.coord.lat;
                        const lng = data.coord.lon;
                        updateMap(lat, lng);
                        
                        // Get forecast for the searched city
                        getForecastData(lat, lng);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('City not found. Please try again.');
                    });
            }
        }

        // Get weather data by coordinates
        function getWeatherData(lat, lon) {
            fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&units=metric&appid=${API_KEY}`)
                .then(response => response.json())
                .then(data => {
                    updateCurrentWeather(data);
                    updateMap(lat, lon); // Update map with new location
                })
                .catch(error => console.error('Error:', error));
        }

        // Get 7-day forecast
        function getForecastData(lat, lon) {
            fetch(`https://api.openweathermap.org/data/2.5/forecast?lat=${lat}&lon=${lon}&units=metric&appid=${API_KEY}`)
                .then(response => response.json())
                .then(data => {
                    // Group forecast data by day
                    const dailyForecasts = groupForecastsByDay(data.list);
                    updateForecast(dailyForecasts);
                })
                .catch(error => console.error('Error:', error));
        }

        // New function to group forecasts by day
        function groupForecastsByDay(forecastList) {
            const dailyForecasts = {};
            
            forecastList.forEach(forecast => {
                const date = new Date(forecast.dt * 1000);
                const day = date.toISOString().split('T')[0];
                
                if (!dailyForecasts[day]) {
                    dailyForecasts[day] = {
                        date: date,
                        temp_max: forecast.main.temp_max,
                        temp_min: forecast.main.temp_min,
                        weather: forecast.weather[0],
                        dt: forecast.dt
                    };
                } else {
                    dailyForecasts[day].temp_max = Math.max(dailyForecasts[day].temp_max, forecast.main.temp_max);
                    dailyForecasts[day].temp_min = Math.min(dailyForecasts[day].temp_min, forecast.main.temp_min);
                }
            });

            return Object.values(dailyForecasts);
        }

        // Update current weather UI
        function updateCurrentWeather(data) {
            document.getElementById('location').textContent = data.name;
            document.getElementById('temperature').textContent = `${Math.round(data.main.temp)}°`;
            document.getElementById('weather-description').textContent = data.weather[0].description;
            document.getElementById('high-low').textContent = `H:${Math.round(data.main.temp_max)}° L:${Math.round(data.main.temp_min)}°`;
            document.getElementById('feels-like').textContent = `${Math.round(data.main.feels_like)}°`;
            document.getElementById('humidity').textContent = `${data.main.humidity}%`;
            document.getElementById('wind-speed').textContent = `${Math.round(data.wind.speed * 3.6)} km/h`;
            document.getElementById('pressure').textContent = `${data.main.pressure} hPa`;
        }

        // Updated updateForecast function
        function updateForecast(dailyData) {
            const forecastContainer = document.getElementById('forecast');
            forecastContainer.innerHTML = '';

            // Take only first 7 days
            dailyData.slice(0, 7).forEach(day => {
                const date = new Date(day.date);
                const dayName = date.toLocaleDateString('en-US', { weekday: 'short' });
                const dateStr = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                
                const forecastDay = document.createElement('div');
                forecastDay.className = 'forecast-day';
                forecastDay.innerHTML = `
                    <div>${dayName}</div>
                    <div>${dateStr}</div>
                    <img src="http://openweathermap.org/img/wn/${day.weather.icon}@2x.png" alt="Weather icon" class="forecast-icon">
                    <div>${Math.round(day.temp_max)}°/${Math.round(day.temp_min)}°</div>
                    <div>${day.weather.description}</div>
                `;
                forecastContainer.appendChild(forecastDay);
            });
        }

        // Initialize weather data on page load
        getCurrentLocation();

        // Add event listener for Enter key in search input
        document.getElementById('citySearch').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchCity();
            }
        });

        let map;
        let currentLat;
        let currentLng;

        // Initialize map with default location
        function initMap(lat, lng) {
            currentLat = lat;
            currentLng = lng;
            
            if (map) {
                map.remove(); // Clean up existing map
            }
            
            map = L.map('map', {
                center: [lat, lng],
                zoom: 12,
                zoomControl: false,
                dragging: false,
                touchZoom: false,
                scrollWheelZoom: false,
                doubleClickZoom: false
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Add marker for location
            L.marker([lat, lng]).addTo(map);
        }

        // Update map when weather location changes
        function updateMap(lat, lng) {
            currentLat = lat;  // Update current coordinates
            currentLng = lng;
            
            if (map) {
                map.setView([lat, lng], 12);
                // Clear existing markers
                map.eachLayer((layer) => {
                    if (layer instanceof L.Marker) {
                        map.removeLayer(layer);
                    }
                });
                // Add new marker
                L.marker([lat, lng]).addTo(map);
            } else {
                initMap(lat, lng);
            }
        }

        // Handle map click - open full map in same tab
        document.getElementById('mapContainer').addEventListener('click', function() {
            const mapUrl = `map.php?lat=${currentLat}&lng=${currentLng}`;
            window.location.href = mapUrl; // This will open map in the same tab
        });
    </script>
</body>
</html> 