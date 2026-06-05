<?php $__env->startSection('title', 'Thêm địa điểm mới'); ?>

<?php $__env->startSection('styles'); ?>
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map-picker {
        height: 320px;
        width: 100%;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        margin-top: 8px;
    }
    .amenities-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 12px;
        margin-top: 8px;
    }
    .amenity-checkbox {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f1f5f9;
        padding: 10px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        cursor: pointer;
        user-select: none;
    }
    .amenity-checkbox input {
        cursor: pointer;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="header">
    <div class="page-title">
        <h1>Thêm địa điểm mới</h1>
        <p>Tạo mới địa điểm du lịch, quán ăn, khách sạn hay homestay</p>
    </div>
    <div>
        <a href="<?php echo e(route('admin.locations.index')); ?>" class="btn btn-secondary">
            <span class="material-symbols-rounded">arrow_back</span> Quay lại danh sách
        </a>
    </div>
</div>

<form action="<?php echo e(route('admin.locations.store')); ?>" method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start;">
        
        <!-- Left Panel: Main Info -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            
            <!-- Basic Information Card -->
            <div class="card">
                <div class="card-title">Thông tin cơ bản</div>
                
                <div class="form-group">
                    <label for="name">Tên địa điểm <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" required placeholder="Ví dụ: Đền Lảnh Giang, Khách sạn Mường Thanh...">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category_id">Danh mục <span style="color: var(--danger);">*</span></label>
                        <select name="category_id" id="category_id" required>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cat->id); ?>"><?php echo e($cat->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Trạng thái xuất bản</label>
                        <select name="status" id="status">
                            <option value="published" selected>Hiển thị (Published)</option>
                            <option value="draft">Bản nháp (Draft)</option>
                            <option value="hidden">Ẩn (Hidden)</option>
                            <option value="pending">Chờ duyệt (Pending)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="short_description">Mô tả ngắn</label>
                    <textarea name="short_description" id="short_description" rows="3" placeholder="Tóm tắt ngắn gọn hiển thị trên thẻ thông tin..."></textarea>
                </div>

                <div class="form-group">
                    <label for="description">Giới thiệu chi tiết</label>
                    <textarea name="description" id="description" rows="6" placeholder="Giới thiệu đầy đủ về địa điểm, đặc trưng, hoạt động lý thú..."></textarea>
                </div>

                <div class="form-group">
                    <label for="detailed_history">Lịch sử & Chi tiết văn hóa</label>
                    <textarea name="detailed_history" id="detailed_history" rows="5" placeholder="Thông tin chuyên sâu về nguồn gốc lịch sử, giai thoại tâm linh, kiến trúc..."></textarea>
                </div>
            </div>

            <!-- Location & Contact Info Card -->
            <div class="card">
                <div class="card-title">Vị trí & Liên hệ</div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="province">Tỉnh / Thành phố</label>
                        <input type="text" name="province" id="province" class="form-control" value="Hà Nam" required>
                    </div>
                    <div class="form-group">
                        <label for="district">Quận / Huyện</label>
                        <input type="text" name="district" id="district" class="form-control" placeholder="Ví dụ: Kim Bảng, Duy Tiên...">
                    </div>
                    <div class="form-group">
                        <label for="ward">Phường / Xã</label>
                        <input type="text" name="ward" id="ward" class="form-control" placeholder="Ví dụ: Thi Sơn, Đồng Văn...">
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Địa chỉ cụ thể</label>
                    <input type="text" name="address" id="address" class="form-control" placeholder="Số nhà, tên đường, thôn xóm...">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="opening_hours">Giờ hoạt động</label>
                        <input type="text" name="opening_hours" id="opening_hours" class="form-control" placeholder="Ví dụ: 08:00 - 17:30 hoặc Cả ngày">
                    </div>
                    <div class="form-group">
                        <label for="phone">Số điện thoại liên hệ</label>
                        <input type="text" name="phone" id="phone" class="form-control" placeholder="Ví dụ: 0912345678">
                    </div>
                </div>

                <div class="form-group">
                    <label for="website_url">Website / Facebook link</label>
                    <input type="url" name="website_url" id="website_url" class="form-control" placeholder="https://example.com">
                </div>
            </div>

            <!-- Amenities / Attributes Checkbox Card -->
            <div class="card">
                <div class="card-title">Tiện ích & Thuộc tính phụ</div>
                <p style="font-size: 13px; color: var(--text-muted); margin-top: -12px; margin-bottom: 12px;">Chọn các nhãn tiện ích đặc trưng cho địa điểm này</p>
                
                <div class="amenities-grid">
                    <label class="amenity-checkbox">
                        <input type="checkbox" name="attributes[]" value="Wifi miễn phí">
                        <span>Wifi miễn phí</span>
                    </label>
                    <label class="amenity-checkbox">
                        <input type="checkbox" name="attributes[]" value="Bãi đỗ xe rộng">
                        <span>Bãi đỗ xe</span>
                    </label>
                    <label class="amenity-checkbox">
                        <input type="checkbox" name="attributes[]" value="Điều hòa nhiệt độ">
                        <span>Điều hòa</span>
                    </label>
                    <label class="amenity-checkbox">
                        <input type="checkbox" name="attributes[]" value="Phục vụ ăn uống">
                        <span>Ăn uống</span>
                    </label>
                    <label class="amenity-checkbox">
                        <input type="checkbox" name="attributes[]" value="Thân thiện trẻ em">
                        <span>Cho trẻ em</span>
                    </label>
                    <label class="amenity-checkbox">
                        <input type="checkbox" name="attributes[]" value="Hồ bơi">
                        <span>Hồ bơi</span>
                    </label>
                    <label class="amenity-checkbox">
                        <input type="checkbox" name="attributes[]" value="Mở cửa cuối tuần">
                        <span>Mở cuối tuần</span>
                    </label>
                    <label class="amenity-checkbox">
                        <input type="checkbox" name="attributes[]" value="Hướng dẫn viên">
                        <span>Có HDV du lịch</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Right Panel: Map Coordinate Pick & Image Upload -->
        <div style="display: flex; flex-direction: column; gap: 24px; position: sticky; top: 20px;">
            
            <!-- Coordinates Picker Card -->
            <div class="card">
                <div class="card-title">Tọa độ trên bản đồ <span style="color: var(--danger);">*</span></div>
                <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 8px;">Nhấp vào vị trí trên bản đồ để tự động lấy tọa độ Lat/Lng GPS.</p>
                
                <div class="form-row" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label for="lat">Latitude (Vĩ độ)</label>
                        <input type="number" step="any" name="lat" id="lat" class="form-control" placeholder="Vĩ độ" required readonly>
                    </div>
                    <div class="form-group">
                        <label for="lng">Longitude (Kinh độ)</label>
                        <input type="number" step="any" name="lng" id="lng" class="form-control" placeholder="Kinh độ" required readonly>
                    </div>
                </div>

                <div id="map-picker"></div>
            </div>

            <!-- Thumbnail Upload Card -->
            <div class="card">
                <div class="card-title">Ảnh đại diện (Thumbnail)</div>
                <div class="form-group">
                    <label for="thumbnail">Chọn ảnh từ máy tính</label>
                    <input type="file" name="thumbnail" id="thumbnail" class="form-control" accept="image/*" onchange="previewImage(event)">
                </div>
                <div id="thumbnail-preview-container" style="display: none; margin-top: 12px; text-align: center;">
                    <img id="thumbnail-preview" src="#" style="max-width: 100%; max-height: 200px; border-radius: 8px; border: 1px solid var(--border-color); object-fit: cover;" alt="">
                </div>
            </div>

            <!-- SEO Settings Card -->
            <div class="card">
                <div class="card-title">Tùy chọn SEO</div>
                <div class="form-group">
                    <label for="meta_title">Tiêu đề SEO (Meta Title)</label>
                    <input type="text" name="meta_title" id="meta_title" class="form-control" placeholder="Tiêu đề hiển thị trên Google">
                </div>
                <div class="form-group">
                    <label for="meta_description">Mô tả SEO (Meta Description)</label>
                    <textarea name="meta_description" id="meta_description" rows="3" placeholder="Đoạn mô tả ngắn hiển thị trên kết quả tìm kiếm"></textarea>
                </div>
            </div>

            <!-- Submit Button Box -->
            <div class="card" style="background-color: transparent; border: none; padding: 0; box-shadow: none;">
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 16px; justify-content: center; font-size: 16px;">
                    <span class="material-symbols-rounded">save</span> Lưu địa điểm
                </button>
            </div>
            
        </div>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Preview selected thumbnail
    function previewImage(event) {
        const input = event.target;
        const container = document.getElementById('thumbnail-preview-container');
        const preview = document.getElementById('thumbnail-preview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                container.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            container.style.display = 'none';
        }
    }

    // Leaflet Coordinates Picker Setup
    document.addEventListener('DOMContentLoaded', function() {
        // Ha Nam center: 20.5403, 105.9248
        const defaultLat = 20.5403;
        const defaultLng = 105.9248;
        
        const map = L.map('map-picker').setView([defaultLat, defaultLng], 11);
        
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap & CARTO',
            maxZoom: 19
        }).addTo(map);

        let marker = null;

        // Function to place marker and fill inputs
        function updateCoordinates(lat, lng) {
            document.getElementById('lat').value = parseFloat(lat).toFixed(7);
            document.getElementById('lng').value = parseFloat(lng).toFixed(7);

            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                
                // Track drag end
                marker.on('dragend', function(event) {
                    const position = marker.getLatLng();
                    updateCoordinates(position.lat, position.lng);
                });
            }
        }

        // On map click
        map.on('click', function(e) {
            updateCoordinates(e.latlng.lat, e.latlng.lng);
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\Du_An_TN\resources\views/admin/locations/create.blade.php ENDPATH**/ ?>