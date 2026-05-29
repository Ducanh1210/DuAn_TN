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
    <!-- MarkerCluster CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    
    <!-- GSAP for animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>


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
            border-radius: 4px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            padding: 0;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .leaflet-popup-content {
            font-family: 'Outfit', sans-serif;
            margin: 0;
            width: 260px !important;
        }
        .leaflet-popup-close-button {
            color: white !important;
            text-shadow: 0 1px 4px rgba(0,0,0,0.8) !important;
            font-size: 22px !important;
            padding: 4px 8px !important;
            z-index: 10;
        }
        .leaflet-popup-close-button:hover {
            color: #f1f5f9 !important;
            background: transparent !important;
        }
        .poi-popup-inner {
            display: flex;
            flex-direction: column;
            text-align: center;
        }
        .poi-thumbnail {
            width: 100%;
            height: 140px;
            object-fit: cover;
            background: #f1f5f9;
        }
        .poi-content {
            padding: 16px;
        }
        .poi-title {
            font-weight: 700;
            font-size: 17px;
            color: #1a1a1a;
            margin-bottom: 6px;
        }
        .poi-desc {
            font-size: 13px;
            color: #555;
            margin-bottom: 16px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.5;
        }
        .poi-btn-360 {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: transparent;
            color: var(--poi-color, var(--primary)) !important;
            padding: 6px 20px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none !important;
            transition: all 0.2s;
            border: 2px solid var(--poi-color, var(--primary));
            width: 100%;
            box-sizing: border-box;
        }
        .poi-btn-360:hover {
            filter: brightness(0.85);
            transform: translateY(-1px);
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

        /* Custom Map Pin */
        .custom-map-pin {
            position: relative;
            width: 30px;
            height: 40px;
            filter: drop-shadow(0px 3px 4px rgba(0,0,0,0.35));
        }
        .custom-map-pin svg {
            position: absolute;
            top: 0;
            left: 0;
        }
        .leaflet-container .leaflet-marker-pane .pin-icon-img {
            position: absolute !important;
            top: 4px !important;
            left: 4px !important;
            width: 22px !important;
            height: 22px !important;
            max-width: 22px !important;
            max-height: 22px !important;
            object-fit: cover !important;
            z-index: 999 !important;
            border-radius: 50% !important;
        }
        
        .custom-pin-tooltip {
            position: absolute;
            top: 50%;
            left: 100%;
            transform: translate(0px, -50%);
            background: linear-gradient(to right, color-mix(in srgb, var(--tip-color) 40%, black), var(--tip-color));
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            z-index: 10001;
        }
        
        .custom-pin-tooltip::before {
            content: '';
            position: absolute;
            top: 50%;
            left: -5px;
            transform: translateY(-50%);
            border-top: 6px solid transparent;
            border-bottom: 6px solid transparent;
            border-right: 6px solid color-mix(in srgb, var(--tip-color) 40%, black);
        }

        /* Cluster Coverage Polygon on Hover */
        .leaflet-cluster-anim .leaflet-marker-icon,
        .leaflet-cluster-anim .leaflet-marker-shadow {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        .marker-cluster {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .marker-cluster:hover {
            transform: scale(1.1);
        }

        /* Cluster coverage polygon animation */
        .cluster-coverage-polygon {
            transition: opacity 0.3s ease;
        }
        .leaflet-overlay-pane svg path {
            transition: fill-opacity 0.3s ease, stroke-opacity 0.3s ease;
        }
    </style>
</head>
<body>

    <!-- Map Container -->
    <div id="map"></div>

    <!-- Leaflet JS & MarkerCluster JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

    <script src="https://unpkg.com/@turf/turf@7.2.0/dist/turf.min.js"></script>

    <script>

        const HA_NAM_BOUNDARY_URL = <?php echo json_encode(asset('geo/ha-nam-old.geojson'), 15, 512) ?>;

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

        // Render markers for locations
        const locations = <?php echo json_encode($locations, 15, 512) ?>;

        // Tạo pane riêng cho coverage polygon (z-index cao hơn dimPane)
        map.createPane('coveragePane');
        map.getPane('coveragePane').style.zIndex = 460;
        const coverageSvgRenderer = L.svg({ pane: 'coveragePane' });

        let coveragePolygon = null;

        const markers = L.markerClusterGroup({
            maxClusterRadius: 80,
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false, // Tự implement thủ công
            zoomToBoundsOnClick: false,
            iconCreateFunction: function(cluster) {
                const count = cluster.getChildCount();
                let size = 'small';
                if (count >= 10) size = 'medium';
                if (count >= 30) size = 'large';
                return L.divIcon({
                    html: '<div><span>' + count + '</span></div>',
                    className: 'marker-cluster marker-cluster-' + size,
                    iconSize: L.point(40, 40)
                });
            }
        });

        // Custom hover coverage polygon
        function convexHull(points) {
            // Graham scan
            if (points.length < 3) return points.slice();
            points = points.slice().sort((a, b) => a[0] - b[0] || a[1] - b[1]);
            const cross = (O, A, B) => (A[0]-O[0])*(B[1]-O[1]) - (A[1]-O[1])*(B[0]-O[0]);
            const lower = [];
            for (const p of points) { while (lower.length >= 2 && cross(lower[lower.length-2], lower[lower.length-1], p) <= 0) lower.pop(); lower.push(p); }
            const upper = [];
            for (let i = points.length - 1; i >= 0; i--) { const p = points[i]; while (upper.length >= 2 && cross(upper[upper.length-2], upper[upper.length-1], p) <= 0) upper.pop(); upper.push(p); }
            upper.pop(); lower.pop();
            return lower.concat(upper);
        }

        markers.on('clustermouseover', function(e) {
            if (coveragePolygon) { map.removeLayer(coveragePolygon); coveragePolygon = null; }

            const childMarkers = e.layer.getAllChildMarkers();
            const points = childMarkers.map(m => {
                const ll = m.getLatLng();
                return [ll.lat, ll.lng];
            });

            if (points.length < 2) return;

            let latlngs;
            if (points.length === 2) {
                latlngs = points.map(p => L.latLng(p[0], p[1]));
            } else {
                const hull = convexHull(points);
                latlngs = hull.map(p => L.latLng(p[0], p[1]));
            }

            coveragePolygon = L.polygon(latlngs, {
                pane: 'coveragePane',
                renderer: coverageSvgRenderer,
                fillColor: '#3388ff',
                fillOpacity: 0.15,
                weight: 2.5,
                opacity: 0.7,
                color: '#3388ff',
                smoothFactor: 1,
                interactive: false,
                className: 'cluster-coverage-polygon'
            }).addTo(map);
        });

        markers.on('clustermouseout', function(e) {
            if (coveragePolygon) {
                map.removeLayer(coveragePolygon);
                coveragePolygon = null;
            }
        });

        markers.on('clusterclick', function (a) {
            if (coveragePolygon) { map.removeLayer(coveragePolygon); coveragePolygon = null; }

            // Zoom dần dần: mỗi lần click tăng tối đa 3 level
            // Dùng vị trí cluster (chỗ anh bấm) thay vì tâm bounds để zoom thẳng vào, không bị lệch
            var clusterLatLng = a.layer.getLatLng();
            var currentZoom = map.getZoom();
            var maxZoom = map.getMaxZoom() || 20;
            var targetZoom = Math.min(currentZoom + 3, maxZoom);

            map.setView(clusterLatLng, targetZoom, { animate: true, duration: 0.4 });
        });

        locations.forEach(loc => {
            if (loc.lat && loc.lng) {
                let markerOptions = {};
                const iconUrl = loc.category && loc.category.icon_url ? loc.category.icon_url : null;

                if (iconUrl) {
                    const iconColor = loc.category && loc.category.icon_color ? loc.category.icon_color : '#ef4444';
                    const pinHtml = '<div class="custom-map-pin">'
                        + '<svg class="pin-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="30" height="40">'
                        + '<path fill="' + iconColor + '" d="M172.3 501.7C27 291 0 269.4 0 192 0 86 86 0 192 0s192 86 192 192c0 77.4-27 99-172.3 309.7-9.5 13.8-29.9 13.8-39.5 0z"/>'
                        + '</svg>'
                        + '<img class="pin-icon-img" src="' + iconUrl + '">'
                        + '<div class="custom-pin-tooltip" style="--tip-color: ' + iconColor + ';">' + loc.name + '</div>'
                        + '</div>';

                    const customIcon = L.divIcon({
                        className: '',
                        html: pinHtml,
                        iconSize: [30, 40],
                        iconAnchor: [15, 40],
                        popupAnchor: [0, -40]
                    });
                    markerOptions = { icon: customIcon };
                }

                const marker = L.marker([loc.lat, loc.lng], markerOptions);
                const thumbUrl = loc.thumbnail_url ? loc.thumbnail_url : 'https://placehold.co/400x250/e2e8f0/475569?text=No+Image';
                const iconColor = loc.category && loc.category.icon_color ? loc.category.icon_color : '#ef4444';
                
                const popupHtml = '<div class="poi-popup-inner" style="--poi-color: ' + iconColor + ';">'
                    + '<img src="' + thumbUrl + '" class="poi-thumbnail" alt="' + loc.name + '">'
                    + '<div class="poi-content">'
                    + '<div class="poi-title">' + loc.name + '</div>'
                    + (loc.short_description ? '<div class="poi-desc">' + loc.short_description + '</div>' : '')
                    + '<a href="/locations/' + loc.slug + '/360" class="poi-btn-360">'
                    + 'Khám phá ngay'
                    + '</a>'
                    + '</div>'
                    + '</div>';
                
                marker.bindPopup(popupHtml, { minWidth: 260, maxWidth: 260, closeButton: false });
                
                // GSAP Animations on Hover
                marker.on('mouseover', function(e) {
                    const el = e.target.getElement();
                    if (el) {
                        const tooltip = el.querySelector('.custom-pin-tooltip');
                        const pinSvg = el.querySelector('.pin-svg');
                        const pinImg = el.querySelector('.pin-icon-img');
                        
                        // Bring element to front while hovering
                        el.style.zIndex = 10000;
                        
                        // Tooltip fade in and slide right
                        gsap.to(tooltip, { overwrite: true, autoAlpha: 1, x: 10, y: "-50%", duration: 0.3, ease: "back.out(1.5)" });
                        
                        // Pin bounce/scale slightly
                        gsap.to([pinSvg, pinImg], { overwrite: true, scale: 1.15, y: -5, duration: 0.3, ease: "back.out(2)" });
                    }
                });

                marker.on('mouseout', function(e) {
                    const el = e.target.getElement();
                    if (el) {
                        const tooltip = el.querySelector('.custom-pin-tooltip');
                        const pinSvg = el.querySelector('.pin-svg');
                        const pinImg = el.querySelector('.pin-icon-img');
                        
                        // Reset z-index
                        el.style.zIndex = '';
                        
                        // Tooltip fade out and slide left
                        gsap.to(tooltip, { overwrite: true, autoAlpha: 0, x: 0, y: "-50%", duration: 0.2, ease: "power2.in" });
                        
                        // Pin return to normal
                        gsap.to([pinSvg, pinImg], { overwrite: true, scale: 1, y: 0, duration: 0.2, ease: "power2.in" });
                    }
                });

                markers.addLayer(marker);
            }
        });

        map.addLayer(markers);

    </script>
</body>
</html>
<?php /**PATH D:\laragon\www\datnv2\DuAn_TN\resources\views/client/home.blade.php ENDPATH**/ ?>