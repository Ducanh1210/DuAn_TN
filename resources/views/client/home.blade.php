<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bản Đồ - Hệ Thống POI</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" rel="stylesheet" />

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

    <style>
        :root {
            --primary: #0072FF;
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(255, 255, 255, 0.4);
            --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        }

        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: 'Outfit', sans-serif;
            overflow: hidden;
            background-color: #f0f2f5;
        }

        #map {
            width: 100%;
            height: 100%;
            z-index: 1;
        }



        /* Customizes Leaflet Zoom Control */
        .leaflet-control-zoom {
            border: none !important;
            box-shadow: var(--glass-shadow) !important;
        }
        .leaflet-control-zoom a {
            background: var(--glass-bg) !important;
            backdrop-filter: blur(12px) !important;
            color: #333 !important;
            border-bottom: 1px solid rgba(0,0,0,0.05) !important;
        }
        .leaflet-control-zoom a:hover {
            background: #fff !important;
            color: var(--primary) !important;
        }
        
        /* Custom Popup Styling */
        .leaflet-popup-content-wrapper {
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            padding: 4px;
        }
        .leaflet-popup-content {
            font-family: 'Outfit', sans-serif;
            margin: 12px 16px;
        }
        .poi-title {
            font-weight: 700;
            font-size: 16px;
            color: #1a1a1a;
            margin-bottom: 4px;
        }
        .poi-rate {
            display: inline-block;
            background: #f0fdf4;
            color: #16a34a;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <!-- Map Container -->
    <div id="map"></div>

    <!-- Leaflet JS & MarkerCluster JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

    <script>
        // API Key từ người dùng (OpenTripMap)
        const API_KEY = '5ae2e3f221c38a28845f05b673661bd506e322b0659e42a4930080e7';

        // Khởi tạo bản đồ
        const map = L.map('map', {
            zoomControl: false // Ẩn zoom mặc định để tuỳ chỉnh vị trí
        });

        // Căn chỉnh bản đồ vào khu vực Hà Nam (dựa trên toạ độ bạn đã gửi)
        map.fitBounds([
            [20.2480, 105.7600],
            [20.6040, 106.1010]
        ]);

        // Thêm Base Map
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        L.control.zoom({ position: 'bottomleft' }).addTo(map);

        // Khởi tạo Marker Cluster để gom cụm các địa điểm
        const markers = L.markerClusterGroup({
            showCoverageOnHover: false,
            maxClusterRadius: 60
        });
        map.addLayer(markers);

        // Hàm gọi API OpenTripMap để lấy các địa điểm (POIs) xung quanh điểm click
        function loadPOIsAt(lat, lon) {
            // Lấy POIs trong bán kính 5km
            const radius = 5000;
            
            // Gọi API OpenTripMap với API Key
            const url = `https://api.opentripmap.com/0.1/en/places/radius?radius=${radius}&lon=${lon}&lat=${lat}&kinds=interesting_places&format=geojson&apikey=${API_KEY}`;
            
            fetch(url)
                .then(res => res.json())
                .then(data => {
                    // Xóa marker cũ để bản đồ không bị "lung tung"
                    markers.clearLayers();
                    
                    // Thêm dữ liệu GeoJSON vào Cluster
                    L.geoJSON(data, {
                        pointToLayer: function (feature, latlng) {
                            return L.marker(latlng);
                        },
                        onEachFeature: function (feature, layer) {
                            if (feature.properties && feature.properties.name) {
                                const popupContent = `
                                    <div class="poi-title">${feature.properties.name}</div>
                                    <div class="poi-rate">Rating: ${feature.properties.rate}/7</div>
                                `;
                                layer.bindPopup(popupContent);
                            }
                        }
                    }).addTo(markers);
                })
                .catch(err => console.error('Lỗi khi tải dữ liệu bản đồ:', err));
        }

        // Chỉ hiển thị POI khi người dùng bấm vào một khu vực cụ thể trên bản đồ
        map.on('click', function(e) {
            loadPOIsAt(e.latlng.lat, e.latlng.lng);
        });

    </script>
</body>
</html>
