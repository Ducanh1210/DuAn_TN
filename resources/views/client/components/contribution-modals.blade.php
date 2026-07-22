<style>
/* Modals */
.custom-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(4px);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}
.custom-modal-overlay.active {
    opacity: 1;
    visibility: visible;
}
.custom-modal {
    background: white;
    border-radius: 16px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    transform: scale(0.9);
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    overflow: hidden;
}
.custom-modal-overlay.active .custom-modal {
    transform: scale(1);
}
.custom-modal-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.custom-modal-title {
    font-size: 18px;
    font-weight: 700;
    margin: 0;
}
.close-modal-btn {
    background: none;
    border: none;
    cursor: pointer;
    color: #666;
}
.custom-modal-body {
    padding: 20px;
    max-height: 70vh;
    overflow-y: auto;
}
.form-group {
    margin-bottom: 16px;
}
.form-group label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    font-size: 14px;
    color: #333;
}
.form-control {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-family: inherit;
    font-size: 14px;
    box-sizing: border-box;
}
.form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0,114,255,0.1);
}
.btn-submit {
    background: var(--primary);
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 600;
    width: 100%;
    cursor: pointer;
    transition: background 0.2s;
}
.btn-submit:hover {
    background: #0056b3;
}
</style>

<!-- Modal Đề xuất địa điểm -->
<div class="custom-modal-overlay" id="suggestLocationModal">
    <div class="custom-modal">
        <div class="custom-modal-header">
            <h3 class="custom-modal-title">Thêm địa điểm mới</h3>
            <button class="close-modal-btn" onclick="closeModal('suggestLocationModal')">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        <div class="custom-modal-body">
            @if(Auth::check())
            <form id="suggestLocationForm" onsubmit="submitLocationSuggestion(event)">
                @csrf
                <div class="form-group">
                    <label>Tên địa điểm (*)</label>
                    <input type="text" name="name" class="form-control" required placeholder="Nhập tên địa điểm...">
                </div>
                <div class="form-group">
                    <label>Địa chỉ</label>
                    <input type="text" name="address" class="form-control" placeholder="Nhập địa chỉ cụ thể...">
                </div>
                <div class="form-group">
                    <label>Tọa độ (Kéo ghim trên bản đồ để chọn)</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="suggestLat" name="lat" class="form-control" placeholder="Vĩ độ" readonly>
                        <input type="text" id="suggestLng" name="lng" class="form-control" placeholder="Kinh độ" readonly>
                        <button type="button" class="btn-submit" style="width: auto; padding: 10px;" onclick="enableMapPicking()">Chọn</button>
                    </div>
                </div>
                <div class="form-group">
                    <label>Mô tả ngắn</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Giới thiệu về địa điểm này..."></textarea>
                </div>
                <div class="form-group">
                    <label>Hình ảnh đính kèm (Tối đa 5MB)</label>
                    <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                </div>
                <button type="submit" class="btn-submit">Gửi đề xuất</button>
            </form>
            @else
            <div style="text-align: center; padding: 20px;">
                <p>Bạn cần đăng nhập để gửi đề xuất địa điểm.</p>
                <a href="{{ route('login') }}" class="btn-submit" style="display: inline-block; text-decoration: none;">Đăng nhập ngay</a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Góp ý hệ thống -->
<div class="custom-modal-overlay" id="systemFeedbackModal">
    <div class="custom-modal">
        <div class="custom-modal-header">
            <h3 class="custom-modal-title">Góp ý cải thiện hệ thống</h3>
            <button class="close-modal-btn" onclick="closeModal('systemFeedbackModal')">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        <div class="custom-modal-body">
            @if(Auth::check())
            <form id="systemFeedbackForm" onsubmit="submitSystemFeedback(event)">
                @csrf
                <input type="hidden" name="report_type" value="system_suggestion">
                <input type="hidden" name="target_type" value="system">
                
                <div class="form-group">
                    <label>Nội dung góp ý (*)</label>
                    <textarea name="content" class="form-control" rows="5" required placeholder="Ví dụ: Giao diện web nên thêm màu tối, Tính năng tìm kiếm bị lỗi..."></textarea>
                </div>
                <button type="submit" class="btn-submit">Gửi góp ý</button>
            </form>
            @else
            <div style="text-align: center; padding: 20px;">
                <p>Bạn cần đăng nhập để gửi góp ý.</p>
                <a href="{{ route('login') }}" class="btn-submit" style="display: inline-block; text-decoration: none;">Đăng nhập ngay</a>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).classList.add('active');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}

let isPickingLocation = false;

function enableMapPicking() {
    closeModal('suggestLocationModal');
    isPickingLocation = true;
    
    // Toast
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.style.opacity = '1';
    toast.style.transform = 'translateY(0)';
    toast.style.position = 'fixed';
    toast.style.top = '80px';
    toast.style.zIndex = '9999';
    toast.innerHTML = '<span class="material-symbols-rounded" style="margin-right: 8px; color: #10b981;">touch_app</span> Click vào bản đồ để chọn tọa độ';
    document.body.appendChild(toast);
    
    setTimeout(() => toast.remove(), 4000);
    
    // Handle map click
    map.once('click', function(e) {
        if(!isPickingLocation) return;
        isPickingLocation = false;
        
        document.getElementById('suggestLat').value = e.latlng.lat.toFixed(6);
        document.getElementById('suggestLng').value = e.latlng.lng.toFixed(6);
        
        openModal('suggestLocationModal');
    });
}

function submitLocationSuggestion(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerText = 'Đang gửi...';

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
            alert(data.message);
            closeModal('suggestLocationModal');
            form.reset();
        } else {
            alert(data.message || 'Có lỗi xảy ra.');
        }
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerText = 'Gửi đề xuất';
    });
}

function submitSystemFeedback(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerText = 'Đang gửi...';

    fetch('{{ route('client.feedback.submit') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert(data.message);
            closeModal('systemFeedbackModal');
            form.reset();
        } else {
            alert(data.message || 'Có lỗi xảy ra.');
        }
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerText = 'Gửi góp ý';
    });
}
</script>
