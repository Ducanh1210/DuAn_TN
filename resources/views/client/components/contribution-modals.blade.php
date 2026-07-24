<script src="https://unpkg.com/@turf/turf@7.2.0/dist/turf.min.js"></script>

<style>
/* Modern Ultra-Clean Modal Styling - Sharp Crisp Edges */
.custom-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(15, 23, 42, 0.4);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    z-index: 100000;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.2s ease, visibility 0.2s ease;
}

.custom-modal-overlay.active {
    opacity: 1;
    visibility: visible;
}

.custom-modal-card {
    background: #ffffff;
    border-radius: 6px;
    width: 92%;
    max-width: 480px;
    box-shadow: 0 16px 36px -8px rgba(15, 23, 42, 0.15);
    transform: translateY(6px);
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    overflow: hidden;
    border: 1px solid #e2e8f0;
}

.custom-modal-overlay.active .custom-modal-card {
    transform: translateY(0);
}

.custom-modal-header {
    padding: 14px 18px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #ffffff;
}

.contrib-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: #0f172a;
    margin: 0;
}

.close-modal-btn {
    background: transparent;
    border: none;
    padding: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #94a3b8;
    border-radius: 4px;
    font-size: 1.15rem;
    line-height: 1;
    transition: color 0.15s ease;
    flex-shrink: 0;
}

.close-modal-btn:hover {
    color: #0f172a;
}

.custom-modal-body {
    padding: 16px 18px 18px;
    max-height: 82vh;
    overflow-y: auto;
}

/* Clean Form Layout */
.contrib-grid-form {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px 10px;
}

.contrib-grid-full {
    grid-column: 1 / -1;
}

.contrib-form-group {
    display: flex;
    flex-direction: column;
}

.contrib-form-label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    font-size: 0.76rem;
    color: #475569;
}

.contrib-form-control {
    width: 100%;
    padding: 7px 10px;
    border: 1px solid #cbd5e1;
    border-radius: 5px;
    font-family: inherit;
    font-size: 0.82rem;
    font-weight: 400;
    color: #0f172a;
    background: #ffffff;
    box-sizing: border-box;
    transition: border-color 0.15s ease;
}

.contrib-form-control::-webkit-input-placeholder {
    color: #cbd5e1;
    font-size: 0.78rem;
    font-weight: 300;
}

.contrib-form-control:-ms-input-placeholder {
    color: #cbd5e1;
    font-size: 0.78rem;
    font-weight: 300;
}

.contrib-form-control::placeholder {
    color: #cbd5e1;
    font-size: 0.78rem;
    font-weight: 300;
}

.contrib-form-control:focus {
    outline: none;
    border-color: #475569;
    box-shadow: 0 0 0 2px rgba(71, 85, 105, 0.1);
}

/* Crisp & Clear Dropzone Upload Area */
.contrib-dropzone {
    border: 1.5px dashed #94a3b8;
    border-radius: 5px;
    background: #f8fafc;
    padding: 16px 12px;
    text-align: center;
    cursor: pointer;
    transition: all 0.15s ease;
    user-select: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.contrib-dropzone:hover, .contrib-dropzone.dragover {
    border-color: #0284c7;
    background: #f0f9ff;
}

.dropzone-icon {
    margin-bottom: 4px;
    color: #64748b;
    transition: color 0.15s ease;
}

.contrib-dropzone:hover .dropzone-icon {
    color: #0284c7;
}

.dropzone-text {
    font-size: 0.78rem;
    font-weight: 500;
    color: #475569;
    transition: color 0.15s ease;
}

.contrib-dropzone:hover .dropzone-text {
    color: #0284c7;
}

/* Image Thumbnail Grid */
.file-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(64px, 1fr));
    gap: 8px;
    margin-top: 8px;
}

.thumb-preview-card {
    position: relative;
    width: 100%;
    height: 64px;
    border-radius: 5px;
    overflow: hidden;
    border: 1px solid #cbd5e1;
    background: #f1f5f9;
}

.thumb-preview-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.thumb-remove-btn {
    position: absolute;
    top: 2px;
    right: 2px;
    background: rgba(15, 23, 42, 0.7);
    color: #ffffff;
    border-radius: 50%;
    width: 16px;
    height: 16px;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    line-height: 1;
}

.thumb-remove-btn:hover {
    background: #ef4444;
}

/* Map Container */
#modalPickerMap {
    height: 200px;
    width: 100%;
    border-radius: 5px;
    border: 1px solid #cbd5e1;
    margin-top: 2px;
    z-index: 1;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

/* Footer Actions */
.contrib-modal-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    padding-top: 12px;
    margin-top: 12px;
    border-top: 1px solid #f1f5f9;
}

.btn-contrib-cancel {
    background: transparent;
    border: none;
    padding: 6px 14px;
    font-size: 0.78rem;
    font-weight: 500;
    color: #64748b;
    cursor: pointer;
    border-radius: 5px;
}

.btn-contrib-cancel:hover {
    background: #f8fafc;
    color: #0f172a;
}

.btn-contrib-submit {
    background: #0f172a;
    color: #ffffff;
    border: none;
    padding: 6px 18px;
    border-radius: 5px;
    font-weight: 500;
    font-size: 0.78rem;
    cursor: pointer;
    transition: background 0.15s ease;
}

.btn-contrib-submit:hover {
    background: #1e293b;
}
</style>

<!-- Contribution Modal (Dedicated Location Suggestion) -->
<div class="custom-modal-overlay" id="suggestLocationModal">
    <div class="custom-modal-card">
        <div class="custom-modal-header">
            <h3 class="contrib-title">Đề xuất địa điểm</h3>
            <button type="button" class="close-modal-btn" onclick="closeModal('suggestLocationModal')" title="Đóng">&times;</button>
        </div>
        <div class="custom-modal-body">
            @if(Auth::check())
            <form id="suggestLocationForm" onsubmit="submitLocationSuggestion(event)">
                @csrf
                <div class="contrib-grid-form">
                    <!-- Tên địa điểm -->
                    <div class="contrib-form-group">
                        <label class="contrib-form-label">Tên địa điểm</label>
                        <input type="text" name="name" class="contrib-form-control" required placeholder="Ví dụ: Chùa Bầu, Đền Lăng...">
                    </div>

                    <!-- Danh mục -->
                    <div class="contrib-form-group">
                        <label class="contrib-form-label">Danh mục</label>
                        <select name="category_suggest" class="contrib-form-control" required>
                            <option value="" disabled selected>Chọn danh mục...</option>
                            <option value="Tâm linh">Tâm linh</option>
                            <option value="Sinh thái">Sinh thái</option>
                            <option value="Văn hóa - Lịch sử">Văn hóa - Lịch sử</option>
                            <option value="Check-in">Check-in</option>
                            <option value="Ẩm thực">Ẩm thực</option>
                            <option value="Lưu trú">Lưu trú</option>
                            <option value="Khác">Khác</option>
                        </select>
                    </div>

                    <!-- Địa chỉ chi tiết -->
                    <div class="contrib-form-group contrib-grid-full">
                        <label class="contrib-form-label">Địa chỉ</label>
                        <input type="text" name="address" class="contrib-form-control" placeholder="Ví dụ: Phường Hai Bà Trưng, Phủ Lý...">
                    </div>

                    <!-- Embedded Leaflet Map for Coordinates Selection -->
                    <div class="contrib-form-group contrib-grid-full">
                        <label class="contrib-form-label">Chọn vị trí trên bản đồ</label>
                        <div id="modalPickerMap"></div>
                        <input type="hidden" id="suggestLat" name="lat" required>
                        <input type="hidden" id="suggestLng" name="lng" required>
                    </div>

                    <!-- Mô tả ngắn -->
                    <div class="contrib-form-group contrib-grid-full">
                        <label class="contrib-form-label">Mô tả ngắn</label>
                        <textarea name="description" class="contrib-form-control" rows="2" placeholder="Giới thiệu sơ lược về địa điểm..."></textarea>
                    </div>

                    <!-- Clean Multi-Image Upload Zone -->
                    <div class="contrib-form-group contrib-grid-full">
                        <label class="contrib-form-label">Hình ảnh đính kèm</label>
                        <div class="contrib-dropzone" id="contribDropzone" onclick="document.getElementById('contribFileInput').click()">
                            <input type="file" id="contribFileInput" name="images[]" multiple accept="image/*" style="display: none;" onchange="handleFileSelect(this.files)">
                            <svg class="dropzone-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/>
                                <line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                            <div class="dropzone-text" id="dropzoneText">Kéo thả ảnh hoặc nhấp để chọn</div>
                        </div>
                        <div id="filePreviewGrid" class="file-preview-grid"></div>
                    </div>
                </div>

                <div class="contrib-modal-footer">
                    <button type="button" class="btn-contrib-cancel" onclick="closeModal('suggestLocationModal')">Hủy</button>
                    <button type="submit" class="btn-contrib-submit">Gửi đề xuất</button>
                </div>
            </form>
            @else
            <div style="text-align: center; padding: 20px 8px;">
                <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 14px;">Bạn cần đăng nhập tài khoản để gửi đề xuất địa điểm.</p>
                <a href="{{ route('login') }}" class="btn-contrib-submit" style="text-decoration: none; display: inline-block;">Đăng nhập ngay</a>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
let modalPickerMap = null;
let modalPickerMarker = null;
let modalHaNamBoundaryGeoJSON = null;

// Multi-File Drag & Drop with Thumbnail Grid Preview
function handleFileSelect(files) {
    const fileInput = document.getElementById('contribFileInput');
    if (!fileInput) return;

    const dt = new DataTransfer();
    Array.from(files).forEach(f => dt.items.add(f));
    fileInput.files = dt.files;
    
    renderThumbnailPreviews(dt.files);
}

function renderThumbnailPreviews(files) {
    const previewGrid = document.getElementById('filePreviewGrid');
    const dropzoneText = document.getElementById('dropzoneText');
    if (!previewGrid) return;
    
    previewGrid.innerHTML = '';
    
    if (files.length > 0) {
        dropzoneText.innerHTML = `Đã chọn <strong style="color: #0f172a;">${files.length} hình ảnh</strong>`;
        Array.from(files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const card = document.createElement('div');
                    card.className = 'thumb-preview-card';
                    card.title = file.name;
                    card.innerHTML = `
                        <img src="${e.target.result}" alt="Preview" />
                        <span class="thumb-remove-btn" onclick="removeFileAtIndex(event, ${index})" title="Xóa">&times;</span>
                    `;
                    previewGrid.appendChild(card);
                };
                reader.readAsDataURL(file);
            }
        });
    } else {
        dropzoneText.innerHTML = `Kéo thả ảnh hoặc nhấp để chọn`;
    }
}

function removeFileAtIndex(event, indexToRemove) {
    event.stopPropagation();
    const fileInput = document.getElementById('contribFileInput');
    if (!fileInput) return;

    const dt = new DataTransfer();
    const { files } = fileInput;
    
    for (let i = 0; i < files.length; i++) {
        if (i !== indexToRemove) {
            dt.items.add(files[i]);
        }
    }
    fileInput.files = dt.files;
    renderThumbnailPreviews(fileInput.files);
}

document.addEventListener('DOMContentLoaded', function() {
    const dropzone = document.getElementById('contribDropzone');
    if (!dropzone) return;

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.add('dragover');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.remove('dragover');
        }, false);
    });

    dropzone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        if (dt && dt.files && dt.files.length > 0) {
            const currentInput = document.getElementById('contribFileInput');
            const newDt = new DataTransfer();
            
            if (currentInput && currentInput.files) {
                Array.from(currentInput.files).forEach(f => newDt.items.add(f));
            }
            Array.from(dt.files).forEach(f => newDt.items.add(f));
            
            handleFileSelect(newDt.files);
        }
    }, false);
});

// Pure JS Ray-Casting Point-In-Polygon Check for GeoJSON
function isPointInHaNamGeoJSON(lat, lng, geojson) {
    if (!geojson) return true;
    
    if (typeof turf !== 'undefined' && turf.booleanPointInPolygon) {
        try {
            const pt = turf.point([lng, lat]);
            if (geojson.type === 'FeatureCollection') {
                return geojson.features.some(f => turf.booleanPointInPolygon(pt, f));
            }
            if (geojson.type === 'Feature') {
                return turf.booleanPointInPolygon(pt, geojson);
            }
            return turf.booleanPointInPolygon(pt, turf.feature(geojson));
        } catch(err) {
            console.warn('Turf check error, using ray-casting fallback:', err);
        }
    }

    function pointInRing(pLng, pLat, ring) {
        let inside = false;
        for (let i = 0, j = ring.length - 1; i < ring.length; j = i++) {
            let xi = ring[i][0], yi = ring[i][1];
            let xj = ring[j][0], yj = ring[j][1];
            let intersect = ((yi > pLat) !== (yj > pLat)) && (pLng < (xj - xi) * (pLat - yi) / (yj - yi) + xi);
            if (intersect) inside = !inside;
        }
        return inside;
    }

    function pointInPolygon(pLng, pLat, polygon) {
        if (!polygon || polygon.length === 0) return false;
        if (!pointInRing(pLng, pLat, polygon[0])) return false;
        for (let i = 1; i < polygon.length; i++) {
            if (pointInRing(pLng, pLat, polygon[i])) return false;
        }
        return true;
    }

    const geom = geojson.geometry || geojson;
    if (geom.type === 'Polygon') {
        return pointInPolygon(lng, lat, geom.coordinates);
    }
    if (geom.type === 'MultiPolygon') {
        return geom.coordinates.some(polyCoords => pointInPolygon(lng, lat, polyCoords));
    }
    if (geojson.type === 'FeatureCollection') {
        return geojson.features.some(f => isPointInHaNamGeoJSON(lat, lng, f));
    }
    return true;
}

function initModalPickerMap() {
    if (modalPickerMap) return;

    const mapElement = document.getElementById('modalPickerMap');
    if (!mapElement) return;

    modalPickerMap = L.map('modalPickerMap', {
        maxBoundsViscosity: 0.8,
        zoomControl: true,
        attributionControl: false,
        minZoom: 10
    }).setView([20.545, 105.912], 11);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(modalPickerMap);

    fetch('{{ asset('geo/ha-nam-old.geojson') }}')
        .then(res => res.json())
        .then(data => {
            modalHaNamBoundaryGeoJSON = data;
            const border = L.geoJSON(data, {
                style: {
                    color: '#7ba7d4',
                    weight: 2,
                    opacity: 0.55,
                    fillColor: '#f8fafc',
                    fillOpacity: 0.04
                }
            }).addTo(modalPickerMap);
            
            const bounds = border.getBounds();
            modalPickerMap.fitBounds(bounds);
            modalPickerMap.setMaxBounds(bounds.pad(0.2));
        })
        .catch(err => console.error('Lỗi tải ranh giới Hà Nam:', err));

    modalPickerMap.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        const mapContainer = document.getElementById('modalPickerMap');
        
        const isInside = isPointInHaNamGeoJSON(lat, lng, modalHaNamBoundaryGeoJSON);

        // TỨC THỜI BÁO ĐỎ KHUNG MAP KHI NGOÀI ĐỊA PHẬN HÀ NAM!
        if (!isInside) {
            mapContainer.style.borderColor = '#ef4444';
            mapContainer.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.18)';
            
            if (typeof showToast === 'function') {
                showToast('⚠️ Vị trí ngoài tỉnh Ninh Bình! Vui lòng nhấp chọn lại.', 'warning', 3000);
            }
            
            document.getElementById('suggestLat').value = '';
            document.getElementById('suggestLng').value = '';
            
            if (modalPickerMarker) {
                modalPickerMap.removeLayer(modalPickerMarker);
                modalPickerMarker = null;
            }
            return;
        }

        // KHI HỢP LỆ -> VIỀN XANH LÁ HỢP LỆ
        mapContainer.style.borderColor = '#10b981';
        mapContainer.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.12)';

        document.getElementById('suggestLat').value = lat.toFixed(6);
        document.getElementById('suggestLng').value = lng.toFixed(6);

        if (modalPickerMarker) {
            modalPickerMarker.setLatLng(e.latlng);
        } else {
            modalPickerMarker = L.marker(e.latlng).addTo(modalPickerMap);
        }
    });
}

function openModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.add('active');

    if (id === 'suggestLocationModal') {
        setTimeout(() => {
            initModalPickerMap();
            if (modalPickerMap) {
                modalPickerMap.invalidateSize();
                
                if (typeof map !== 'undefined' && map && !modalPickerMarker) {
                    const center = map.getCenter();
                    const isInside = isPointInHaNamGeoJSON(center.lat, center.lng, modalHaNamBoundaryGeoJSON);
                    
                    if (isInside) {
                        document.getElementById('suggestLat').value = center.lat.toFixed(6);
                        document.getElementById('suggestLng').value = center.lng.toFixed(6);
                        modalPickerMarker = L.marker(center).addTo(modalPickerMap);
                    }
                }
            }
        }, 150);
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.remove('active');
}

function submitLocationSuggestion(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');

    if (!formData.get('lat') || !formData.get('lng')) {
        if (typeof showToast === 'function') {
            showToast('⚠️ Vui lòng nhấp chọn vị trí hợp lệ trong địa phận tỉnh Ninh Bình!', 'warning', 3500);
        } else {
            alert('⚠️ Vui lòng nhấp chọn vị trí hợp lệ trong địa phận tỉnh Ninh Bình!');
        }
        return;
    }
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Đang gửi...';

    fetch('{{ route('client.locations.suggest') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            if (typeof showToast === 'function') {
                showToast(data.message, 'success', 3500);
            } else {
                alert(data.message);
            }
            closeModal('suggestLocationModal');
            form.reset();
            if (modalPickerMarker) {
                modalPickerMap.removeLayer(modalPickerMarker);
                modalPickerMarker = null;
            }
            const previewGrid = document.getElementById('filePreviewGrid');
            const dropzoneText = document.getElementById('dropzoneText');
            if (previewGrid) previewGrid.innerHTML = '';
            if (dropzoneText) dropzoneText.innerHTML = 'Kéo thả ảnh hoặc nhấp để chọn';
        } else {
            if (typeof showToast === 'function') {
                showToast(data.message || 'Có lỗi xảy ra.', 'error', 3500);
            } else {
                alert(data.message || 'Có lỗi xảy ra.');
            }
        }
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Gửi đề xuất';
    });
}
</script>
