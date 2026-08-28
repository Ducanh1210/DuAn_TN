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

.thumb-preview-card.is-oversized {
    border-color: #ef4444;
    box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.25);
}

.thumb-preview-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.contrib-form-error {
    margin-top: 8px;
    padding: 10px 12px;
    border-radius: 6px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #b91c1c;
    font-size: 0.78rem;
    line-height: 1.45;
}

.contrib-form-error.d-none, .contrib-form-error:empty {
    display: none !important;
}

.contrib-field-error {
    color: #dc2626;
    font-size: 0.73rem;
    font-weight: 500;
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.contrib-field-error.d-none {
    display: none !important;
}

.contrib-upload-hint {
    margin-top: 6px;
    font-size: 0.72rem;
    color: #64748b;
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

.btn-contrib-gps {
    background: #f1f5f9;
    color: #1e3a5f;
    border: 1px solid #cbdbe8;
    border-radius: 5px;
    padding: 3px 9px;
    font-size: 0.72rem;
    font-weight: 500;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.15s ease;
}

.btn-contrib-gps:hover {
    background: #e2e8f0;
    color: #0f172a;
    border-color: #94a3b8;
}

.btn-contrib-gps:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.contrib-map-wrapper {
    position: relative;
    width: 100%;
    margin-top: 2px;
}

.btn-map-locate-floating {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 1000;
    width: 32px;
    height: 32px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.15);
    color: #1e3a5f;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
    padding: 0;
}

.btn-map-locate-floating:hover {
    background: #f8fafc;
    color: #0f172a;
    border-color: #94a3b8;
}

.btn-map-locate-floating:disabled, .btn-map-locate-floating.is-loading {
    opacity: 0.6;
    cursor: not-allowed;
}

.locate-floating-toast {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 999999;
    background: #ffffff;
    color: #1e3a5f;
    border: 1px solid #cbdbe8;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15);
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 0.82rem;
    font-weight: 500;
    display: none;
    align-items: center;
    gap: 8px;
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
            <form id="suggestLocationForm" onsubmit="submitLocationSuggestion(event)" novalidate>
                @csrf
                <div class="contrib-grid-form">
                    <!-- Tên địa điểm -->
                    <div class="contrib-form-group">
                        <label class="contrib-form-label">Tên địa điểm <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="contrib-form-control" placeholder="Ví dụ: Chùa Bầu, Đền Lăng...">
                        <div class="contrib-field-error d-none" id="err-name"></div>
                    </div>

                    <!-- Danh mục -->
                    <div class="contrib-form-group">
                        <label class="contrib-form-label">Danh mục <span class="text-danger">*</span></label>
                        <select name="category_suggest" class="contrib-form-control">
                            <option value="" disabled selected>Chọn danh mục...</option>
                            <option value="Tâm linh">Tâm linh</option>
                            <option value="Sinh thái">Sinh thái</option>
                            <option value="Văn hóa - Lịch sử">Văn hóa - Lịch sử</option>
                            <option value="Check-in">Check-in</option>
                            <option value="Ẩm thực">Ẩm thực</option>
                            <option value="Lưu trú">Lưu trú</option>
                            <option value="Khác">Khác</option>
                        </select>
                        <div class="contrib-field-error d-none" id="err-category_suggest"></div>
                    </div>

                    <!-- Địa chỉ chi tiết -->
                    <div class="contrib-form-group contrib-grid-full">
                        <label class="contrib-form-label">Địa chỉ</label>
                        <input type="text" name="address" class="contrib-form-control" placeholder="Ví dụ: Phường Hai Bà Trưng, Phủ Lý...">
                        <div class="contrib-field-error d-none" id="err-address"></div>
                    </div>

                    <!-- Embedded Leaflet Map for Coordinates Selection -->
                    <div class="contrib-form-group contrib-grid-full">
                        <label class="contrib-form-label" style="margin-bottom: 3px;">Chọn vị trí trên bản đồ <span class="text-danger">*</span></label>
                        <p class="text-secondary small mb-2" style="font-size: 0.75rem; color: #64748b; margin-bottom: 8px;">Kéo marker hoặc nhấp trên bản đồ để ghim vị trí. Dùng nút định vị góc phải bản đồ để lấy vị trí hiện tại.</p>
                        <div class="contrib-map-wrapper">
                            <div id="modalPickerMap"></div>
                            <button type="button" class="btn-map-locate-floating" id="btnModalMapLocate" onclick="getContribCurrentLocation(this)" title="Lấy vị trí hiện tại" aria-label="Lấy vị trí hiện tại">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1e3a5f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="display:block;">
                                    <circle cx="12" cy="12" r="8"/>
                                    <line x1="12" y1="2" x2="12" y2="4"/>
                                    <line x1="12" y1="20" x2="12" y2="22"/>
                                    <line x1="2" y1="12" x2="4" y2="12"/>
                                    <line x1="20" y1="12" x2="22" y2="12"/>
                                    <circle cx="12" cy="12" r="2"/>
                                </svg>
                            </button>
                        </div>
                        <input type="hidden" id="suggestLat" name="lat">
                        <input type="hidden" id="suggestLng" name="lng">
                        <div class="contrib-field-error d-none" id="err-map"></div>
                    </div>

                    <!-- Mô tả ngắn -->
                    <div class="contrib-form-group contrib-grid-full">
                        <label class="contrib-form-label">Mô tả ngắn</label>
                        <textarea name="description" class="contrib-form-control" rows="2" placeholder="Giới thiệu sơ lược về địa điểm..."></textarea>
                        <div class="contrib-field-error d-none" id="err-description"></div>
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
                        <div id="suggestFormError" class="contrib-form-error d-none" style="display: none;"></div>
                        <p class="contrib-upload-hint">Tối đa 10 ảnh, mỗi ảnh không quá 10MB (JPG, PNG, WEBP).</p>
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
const SUGGEST_MAX_FILES = 10;
const SUGGEST_MAX_FILE_BYTES = 10 * 1024 * 1024; // 10MB

function formatFileSize(bytes) {
    if (bytes >= 1024 * 1024) {
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }
    return Math.max(1, Math.round(bytes / 1024)) + ' KB';
}

function showSuggestFormError(message) {
    const box = document.getElementById('suggestFormError');
    if (!box) return;

    if (!message || (Array.isArray(message) && message.length === 0) || (typeof message === 'string' && !message.trim())) {
        box.classList.add('d-none');
        box.style.display = 'none';
        box.innerHTML = '';
        return;
    }

    if (Array.isArray(message)) {
        box.innerHTML = message.map(m => `
            <div class="d-flex align-items-center gap-2 mb-1" style="font-size: 0.78rem;">
                <i class="fa-solid fa-circle-exclamation text-danger flex-shrink-0" style="font-size:0.75rem;"></i>
                <span>${m}</span>
            </div>
        `).join('');
    } else {
        box.innerHTML = `
            <div class="d-flex align-items-center gap-2" style="font-size: 0.78rem;">
                <i class="fa-solid fa-circle-exclamation text-danger flex-shrink-0" style="font-size:0.75rem;"></i>
                <span>${message}</span>
            </div>
        `;
    }

    box.classList.remove('d-none');
    box.style.display = 'block';
}

function validateSuggestFiles(files) {
    if (!files || files.length === 0) {
        return { ok: true };
    }
    if (files.length > SUGGEST_MAX_FILES) {
        return {
            ok: false,
            message: `Chỉ được chọn tối đa ${SUGGEST_MAX_FILES} ảnh. Bạn đang chọn ${files.length} ảnh.`,
        };
    }
    const oversized = Array.from(files).filter(f => f.size > SUGGEST_MAX_FILE_BYTES);
    if (oversized.length > 0) {
        const names = oversized.slice(0, 3).map(f => `"${f.name}" (${formatFileSize(f.size)})`).join(', ');
        const suffix = oversized.length > 3 ? ` và ${oversized.length - 3} ảnh khác` : '';
        return {
            ok: false,
            message: `Ảnh quá lớn (tối đa 10MB/ảnh): ${names}${suffix}. Hãy xóa hoặc chọn ảnh nhỏ hơn.`,
        };
    }
    return { ok: true };
}

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

    const check = validateSuggestFiles(dt.files);
    showSuggestFormError(check.ok ? '' : check.message);
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
                    const isOversized = file.size > SUGGEST_MAX_FILE_BYTES;
                    const card = document.createElement('div');
                    card.className = 'thumb-preview-card' + (isOversized ? ' is-oversized' : '');
                    card.title = `${file.name} (${formatFileSize(file.size)})`;
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
        dropzoneText.innerHTML = 'Kéo thả ảnh hoặc nhấp để chọn';
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
    const check = validateSuggestFiles(fileInput.files);
    showSuggestFormError(check.ok ? '' : check.message);
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

let modalLocateBtn = null;
let modalLocateInProgress = false;

function reverseGeocodeContribAddress(lat, lng) {
    const addressInput = document.querySelector('#suggestLocationForm input[name="address"]');
    if (!addressInput) return;

    fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&accept-language=vi`, {
        headers: {
            'Accept-Language': 'vi-VN,vi;q=0.9,en;q=0.8'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data && data.display_name) {
            let addrStr = data.display_name.replace(/, Việt Nam$/i, '').trim();
            addressInput.value = addrStr;
        }
    })
    .catch(err => console.warn('Reverse geocoding error:', err));
}

function addModalLocateControl() {
    if (!modalPickerMap) return;

    const existingBtn = document.querySelector('#modalPickerMap .contrib-locate-control');
    if (existingBtn) return;

    const locateControl = L.control({ position: 'topright' });
    locateControl.onAdd = function() {
        const container = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
        const btn = L.DomUtil.create('a', 'contrib-locate-control', container);
        btn.href = '#';
        btn.title = 'Lấy vị trí hiện tại';
        btn.setAttribute('aria-label', 'Lấy vị trí hiện tại');
        btn.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color: #1e3a5f;">
            <circle cx="12" cy="12" r="8"/>
            <line x1="12" y1="2" x2="12" y2="4"/>
            <line x1="12" y1="20" x2="12" y2="22"/>
            <line x1="2" y1="12" x2="4" y2="12"/>
            <line x1="20" y1="12" x2="22" y2="12"/>
            <circle cx="12" cy="12" r="2"/>
        </svg>`;
        L.DomEvent.disableClickPropagation(btn);
        L.DomEvent.on(btn, 'click', function(e) {
            L.DomEvent.preventDefault(e);
            getContribCurrentLocation(btn);
        });
        modalLocateBtn = btn;
        return container;
    };
    locateControl.addTo(modalPickerMap);
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

    L.tileLayer(@json(config('services.carto.tile_url')), {
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(modalPickerMap);

    addModalLocateControl();

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

        mapContainer.style.borderColor = '#10b981';
        mapContainer.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.12)';

        document.getElementById('suggestLat').value = lat.toFixed(6);
        document.getElementById('suggestLng').value = lng.toFixed(6);

        if (modalPickerMarker) {
            modalPickerMarker.setLatLng(e.latlng);
        } else {
            modalPickerMarker = L.marker(e.latlng).addTo(modalPickerMap);
        }

        reverseGeocodeContribAddress(lat, lng);
    });
}

let contribLocateToast = null;

function showLocateStatusToast(message) {
    dismissLocateStatusToast();
    let container = document.getElementById('locateFloatingToast');
    if (!container) {
        container = document.createElement('div');
        container.id = 'locateFloatingToast';
        container.className = 'locate-floating-toast';
        document.body.appendChild(container);
    }
    container.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" style="width: 12px; height: 12px; border-width: 1.5px; color: #1e3a5f;"></span><span>${message || 'Đang lấy vị trí...'}</span>`;
    container.style.display = 'flex';
}

function dismissLocateStatusToast() {
    const container = document.getElementById('locateFloatingToast');
    if (container) {
        container.style.display = 'none';
    }
}

function triggerContribToast(msg, type = 'info', duration = 3500) {
    if (typeof showToast === 'function') {
        return showToast(msg, type, duration);
    }
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 99999; display: flex; flex-direction: column; gap: 8px;';
        document.body.appendChild(container);
    }
    const t = document.createElement('div');
    t.style.cssText = 'background: #ffffff; color: #1e3a5f; border: 1px solid #cbdbe8; box-shadow: 0 4px 12px rgba(15,23,42,0.12); padding: 10px 16px; border-radius: 6px; font-size: 0.82rem; font-weight: 500; display: flex; align-items: center; gap: 8px; transition: all 0.2s ease;';
    t.innerHTML = (type === 'loading' ? '<span class="spinner-border spinner-border-sm" role="status" style="width: 12px; height: 12px;"></span> ' : '') + `<span>${msg}</span>`;
    container.appendChild(t);
    t.dismiss = () => { t.remove(); };
    if (duration > 0) {
        setTimeout(() => { t.dismiss(); }, duration);
    }
    return t;
}

function getContribCurrentLocation(triggerBtn) {
    const btn = triggerBtn || document.getElementById('btnModalMapLocate');
    const mapContainer = document.getElementById('modalPickerMap');
    
    if (!navigator.geolocation) {
        triggerContribToast('Trình duyệt của bạn không hỗ trợ định vị Geolocation.', 'error', 4000);
        return;
    }

    if (modalLocateInProgress) return;
    modalLocateInProgress = true;

    if (btn) {
        btn.disabled = true;
        btn.classList.add('is-loading');
        btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" style="width: 12px; height: 12px; border-width: 1.5px; color: #1e3a5f;"></span>`;
    }

    showLocateStatusToast('Đang lấy vị trí...');

    navigator.geolocation.getCurrentPosition(
        (position) => {
            modalLocateInProgress = false;
            dismissLocateStatusToast();
            if (btn) {
                btn.disabled = false;
                btn.classList.remove('is-loading');
                btn.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1e3a5f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="display:block;"><circle cx="12" cy="12" r="8"/><line x1="12" y1="2" x2="12" y2="4"/><line x1="12" y1="20" x2="12" y2="22"/><line x1="2" y1="12" x2="4" y2="12"/><line x1="20" y1="12" x2="22" y2="12"/><circle cx="12" cy="12" r="2"/></svg>`;
            }

            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            if (!modalPickerMap) {
                initModalPickerMap();
            }

            const isInside = isPointInHaNamGeoJSON(lat, lng, modalHaNamBoundaryGeoJSON);

            if (!isInside) {
                if (mapContainer) {
                    mapContainer.style.borderColor = '#ef4444';
                    mapContainer.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.18)';
                }
                const warnMsg = 'Vị trí hiện tại của bạn nằm ngoài tỉnh Ninh Bình! Vui lòng nhấp chọn lại trên bản đồ.';
                triggerContribToast(warnMsg, 'warning', 4500);
                return;
            }

            document.getElementById('suggestLat').value = lat.toFixed(6);
            document.getElementById('suggestLng').value = lng.toFixed(6);

            if (mapContainer) {
                mapContainer.style.borderColor = '#10b981';
                mapContainer.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.12)';
            }

            if (modalPickerMarker) {
                modalPickerMarker.setLatLng([lat, lng]);
            } else {
                modalPickerMarker = L.marker([lat, lng]).addTo(modalPickerMap);
            }

            modalPickerMap.setView([lat, lng], 16);
            reverseGeocodeContribAddress(lat, lng);
            triggerContribToast('Đã lấy vị trí hiện tại thành công!', 'success', 3000);
        },
        (err) => {
            modalLocateInProgress = false;
            dismissLocateStatusToast();
            if (btn) {
                btn.disabled = false;
                btn.classList.remove('is-loading');
                btn.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1e3a5f" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="display:block;"><circle cx="12" cy="12" r="8"/><line x1="12" y1="2" x2="12" y2="4"/><line x1="12" y1="20" x2="12" y2="22"/><line x1="2" y1="12" x2="4" y2="12"/><line x1="20" y1="12" x2="22" y2="12"/><circle cx="12" cy="12" r="2"/></svg>`;
            }

            console.warn('Geolocation error:', err);
            let msg = 'Không thể định vị vị trí hiện tại. Vui lòng bật Vị Trí trên trình duyệt.';
            if (err.code === err.PERMISSION_DENIED) {
                msg = 'Bạn đã từ chối quyền truy cập vị trí trên trình duyệt.';
            }
            triggerContribToast(msg, 'error', 4000);
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
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
                addModalLocateControl();
                
                if (typeof map !== 'undefined' && map && !modalPickerMarker) {
                    const center = map.getCenter();
                    const isInside = isPointInHaNamGeoJSON(center.lat, center.lng, modalHaNamBoundaryGeoJSON);
                    
                    if (isInside) {
                        document.getElementById('suggestLat').value = center.lat.toFixed(6);
                        document.getElementById('suggestLng').value = center.lng.toFixed(6);
                        modalPickerMarker = L.marker(center).addTo(modalPickerMap);
                        reverseGeocodeContribAddress(center.lat, center.lng);
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
    const fileInput = document.getElementById('contribFileInput');

    // Clear previous errors & field highlight borders
    showSuggestFormError('');
    form.querySelectorAll('.contrib-form-control').forEach(el => {
        el.style.borderColor = '';
        el.style.boxShadow = '';
    });
    form.querySelectorAll('.contrib-field-error').forEach(el => {
        el.classList.add('d-none');
        el.innerHTML = '';
    });
    const mapBox = document.getElementById('modalPickerMap');
    if (mapBox) {
        mapBox.style.borderColor = '';
        mapBox.style.boxShadow = '';
    }

    const fileCheck = validateSuggestFiles(fileInput ? fileInput.files : []);
    if (!fileCheck.ok) {
        showSuggestFormError(fileCheck.message);
        if (typeof showToast === 'function') {
            showToast('⚠️ ' + fileCheck.message, 'warning', 4500);
        }
        return;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = 'Đang gửi...';

    fetch('{{ route('client.locations.suggest') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(async res => {
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
            if (data.errors) {
                const allMsgs = [];

                Object.keys(data.errors).forEach(fieldName => {
                    const fieldMsgs = data.errors[fieldName];
                    if (Array.isArray(fieldMsgs) && fieldMsgs.length > 0) {
                        allMsgs.push(...fieldMsgs);

                        // Highlight input border
                        const fieldEl = form.querySelector(`[name="${fieldName}"]`);
                        if (fieldEl) {
                            fieldEl.style.borderColor = '#ef4444';
                            fieldEl.style.boxShadow = '0 0 0 2px rgba(239, 68, 68, 0.12)';
                        }

                        // Show inline field error
                        const errDiv = document.getElementById(`err-${fieldName}`);
                        if (errDiv) {
                            errDiv.innerHTML = `<i class="fa-solid fa-circle-exclamation" style="font-size:0.72rem;"></i> <span>${fieldMsgs[0]}</span>`;
                            errDiv.classList.remove('d-none');
                        }

                        if (fieldName === 'lat' || fieldName === 'lng') {
                            if (mapBox) {
                                mapBox.style.borderColor = '#ef4444';
                                mapBox.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.18)';
                            }
                            const mapErrDiv = document.getElementById('err-map');
                            if (mapErrDiv) {
                                mapErrDiv.innerHTML = `<i class="fa-solid fa-circle-exclamation" style="font-size:0.72rem;"></i> <span>${fieldMsgs[0]}</span>`;
                                mapErrDiv.classList.remove('d-none');
                            }
                        }
                    }
                });

                showSuggestFormError(allMsgs);
                const toastMsg = allMsgs[0] || 'Vui lòng điền đầy đủ các trường bắt buộc.';
                if (typeof showToast === 'function') {
                    showToast('⚠️ ' + toastMsg, 'warning', 4500);
                }
                return;
            }
            const serverMsg = data.message || 'Không gửi được đề xuất. Vui lòng thử lại.';
            showSuggestFormError(serverMsg);
            if (typeof showToast === 'function') {
                showToast('⚠️ ' + serverMsg, 'error', 4500);
            }
            return;
        }
        return data;
    })
    .then(data => {
        if (!data) return;
        if (data.success) {
            if (typeof showToast === 'function') {
                showToast(data.message, 'success', 3500);
            } else {
                alert(data.message);
            }
            closeModal('suggestLocationModal');
            form.reset();
            showSuggestFormError('');
            if (modalPickerMarker) {
                modalPickerMap.removeLayer(modalPickerMarker);
                modalPickerMarker = null;
            }
            const previewGrid = document.getElementById('filePreviewGrid');
            const dropzoneText = document.getElementById('dropzoneText');
            if (previewGrid) previewGrid.innerHTML = '';
            if (dropzoneText) dropzoneText.innerHTML = 'Kéo thả ảnh hoặc nhấp để chọn';
        } else {
            const msg = data.message || 'Có lỗi xảy ra.';
            showSuggestFormError(msg);
            if (typeof showToast === 'function') {
                showToast(msg, 'error', 3500);
            } else {
                alert(msg);
            }
        }
    })
    .catch(err => {
        const msg = err.message || 'Không gửi được đề xuất. Vui lòng thử lại.';
        showSuggestFormError(msg);
        if (typeof showToast === 'function') {
            showToast(msg, 'error', 4500);
        }
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Gửi đề xuất';
    });
}

// Add input event listeners to clear error border styling when user edits input
document.addEventListener('DOMContentLoaded', function() {
    const suggestForm = document.getElementById('suggestLocationForm');
    if (suggestForm) {
        suggestForm.querySelectorAll('.contrib-form-control').forEach(input => {
            function clearError() {
                input.style.borderColor = '';
                input.style.boxShadow = '';
                const fieldName = input.getAttribute('name');
                if (fieldName) {
                    const errDiv = document.getElementById(`err-${fieldName}`);
                    if (errDiv) {
                        errDiv.classList.add('d-none');
                        errDiv.innerHTML = '';
                    }
                }
            }
            input.addEventListener('input', clearError);
            input.addEventListener('change', clearError);
        });
    }
});
</script>
