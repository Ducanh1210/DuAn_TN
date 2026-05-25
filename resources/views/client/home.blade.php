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
            --region-line: #7ba7d4;
            --region-dim: #94a3b8;
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

        .leaflet-container {
            background: #e4e9ef;
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
    <script src="https://unpkg.com/@turf/turf@7.2.0/dist/turf.min.js"></script>

    <script>
        const API_KEY = @json(config('services.opentripmap.key', '5ae2e3f221c38a28845f05b673661bd506e322b0659e42a4930080e7'));
        const HA_NAM_BOUNDARY_URL = @json(asset('geo/ha-nam-old.geojson'));

        let haNamGeo = null;
        let outsideMask = null;

        const map = L.map('map', {
            zoomControl: false,
            attributionControl: false, // Ẩn dòng chữ bản quyền Leaflet ở góc dưới cùng bên phải
            maxBoundsViscosity: 0.8, // Giảm độ cứng của ranh giới (để kéo được dãn ra và tự nảy về)
            preferCanvas: true,
        });

        map.createPane('dimPane');
        map.getPane('dimPane').style.zIndex = 450;

        // Tăng padding để render vùng tối (canvas) rộng hơn hẳn màn hình
        // Giúp khi người dùng kéo bản đồ sẽ không bị lộ phần chưa render
        const vectorRenderer = L.canvas({ padding: 2.0 });
        // Thêm Base Map — tải tile ngay khi kéo, không đợi thả chuột
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
            subdomains: 'abcd',
            maxZoom: 20,
            updateWhenIdle: false,
            updateWhenZooming: true,
            keepBuffer: 4,
            fadeAnimation: false,
        }).addTo(map);

        L.control.zoom({ position: 'bottomleft' }).addTo(map);

        // Khởi tạo Marker Cluster để gom cụm các địa điểm
        const markers = L.markerClusterGroup({
            showCoverageOnHover: false,
            maxClusterRadius: 60
        });
        map.addLayer(markers);

        function ringsFromGeo(geo) {
            const holes = [];
            if (geo.type === 'MultiPolygon') {
                geo.coordinates.forEach((polygon) => {
                    holes.push(polygon[0].map(([lng, lat]) => [lat, lng]));
                });
            } else if (geo.type === 'Polygon') {
                holes.push(geo.coordinates[0].map(([lng, lat]) => [lat, lng]));
            }
            return holes;
        }

        function setOutsideDimMask(geo) {
            if (outsideMask) {
                map.removeLayer(outsideMask);
            }
            const world = [[-90, -180], [-90, 180], [90, 180], [90, -180], [-90, -180]];
            const holes = ringsFromGeo(geo);
            outsideMask = L.polygon([world, ...holes], {
                pane: 'dimPane',
                renderer: vectorRenderer,
                fillColor: '#94a3b8',
                fillOpacity: 0.22,
                stroke: false,
                interactive: false,
            }).addTo(map);
        }

        function refreshMask() {
            if (outsideMask) {
                outsideMask.redraw();
            }
        }

        // Tắt tính năng ép vẽ lại liên tục (redraw) gây giật lag khi kéo bản đồ
        // map.on('move', refreshMask);
        // map.on('zoomanim', refreshMask);
        // map.on('resize', refreshMask);

        // Ranh giới tỉnh Hà Nam cũ (OSM relation 1901010, boundary=historic, hết hiệu lực 30/06/2025)
        fetch(HA_NAM_BOUNDARY_URL)
            .then((res) => res.json())
            .then((geo) => {
                haNamGeo = geo;
                setOutsideDimMask(geo);

                const border = L.geoJSON(geo, {
                    style: {
                        color: '#7ba7d4',
                        weight: 2,
                        opacity: 0.55,
                        fillColor: '#f8fafc',
                        fillOpacity: 0.04,
                    },
                    renderer: vectorRenderer,
                    interactive: false,
                }).addTo(map);
                border.bringToFront();

                const bounds = border.getBounds();
                // Căn giữa bản đồ vào Hà Nam
                map.fitBounds(bounds);
                
                // Mặc định zoom cận cảnh hơn 1 mức (như trong ảnh bạn yêu cầu)
                map.setZoom(map.getZoom() + 1);
                
                // Khóa không cho zoom out xa hơn mức mặc định này
                map.setMinZoom(map.getZoom());
                
                // Nới rộng giới hạn kéo thả để người dùng xem được các vùng lân cận rộng hơn
                map.setMaxBounds(bounds.pad(0.5));

            })
            .catch((err) => console.error('Không tải được ranh giới Hà Nam:', err));

        function isInsideHaNam(lat, lon) {
            if (!haNamGeo || typeof turf === 'undefined') {
                return true;
            }
            return turf.booleanPointInPolygon(turf.point([lon, lat]), haNamGeo);
        }

        function loadPOIsAt(lat, lon) {
            if (!isInsideHaNam(lat, lon)) {
                return;
            }
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
