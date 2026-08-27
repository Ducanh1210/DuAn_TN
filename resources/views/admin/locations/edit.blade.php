@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa Địa điểm: ' . $location->name)

@section('content')
<ul class="nav nav-tabs mb-4 border-bottom" id="locationTabs" role="tablist" style="border-color: var(--border-light) !important;">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-medium text-dark px-3 py-2" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab" style="font-size: 0.85rem;">Thông tin cơ bản</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-medium text-muted px-3 py-2" id="images-tab" data-bs-toggle="tab" data-bs-target="#images" type="button" role="tab" style="font-size: 0.85rem;">Quản lý Hình ảnh</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-medium text-muted px-3 py-2" id="pano-tab" data-bs-toggle="tab" data-bs-target="#pano" type="button" role="tab" style="font-size: 0.85rem;">Dữ liệu 360°</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-medium text-muted px-3 py-2" id="audio-tab" data-bs-toggle="tab" data-bs-target="#audio" type="button" role="tab" style="font-size: 0.85rem;">Audio thuyết minh</button>
    </li>
</ul>


<div class="tab-content" id="locationTabsContent">
    <!-- INFO TAB -->
    <div class="tab-pane fade show active" id="info" role="tabpanel">
        <div class="card-minimal p-4 mb-4">
            <form action="{{ route('admin.locations.update', [$location->id] + request()->query()) }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')
                
                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="name" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Tên địa điểm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $location->name) }}" style="border-color: #e2e8f0;">
                            @error('name') <div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="short_description" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Mô tả ngắn</label>
                            <textarea class="form-control form-control-sm" id="short_description" name="short_description" rows="3" style="border-color: #e2e8f0;">{{ old('short_description', $location->short_description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Địa chỉ</label>
                            <input type="text" class="form-control form-control-sm" id="address" name="address" value="{{ old('address', $location->address) }}" style="border-color: #e2e8f0;">
                        </div>
                    </div>
                    
                    <div class="col-md-4 border-start" style="border-color: var(--border-light) !important;">
                        <div class="mb-3">
                            <label for="category_id" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Danh mục <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm @error('category_id') is-invalid @enderror" id="category_id" name="category_id" style="border-color: #e2e8f0;">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $location->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div> @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="lat" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Vĩ độ (Lat) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm @error('lat') is-invalid @enderror" id="lat" name="lat" value="{{ old('lat', $location->lat) }}" style="border-color: #e2e8f0;">
                                @error('lat') <div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-6">
                                <label for="lng" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Kinh độ (Lng) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm @error('lng') is-invalid @enderror" id="lng" name="lng" value="{{ old('lng', $location->lng) }}" style="border-color: #e2e8f0;">
                                @error('lng') <div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Trạng thái</label>
                            <select class="form-select form-select-sm" id="status" name="status" style="border-color: #e2e8f0;">
                                <option value="published" {{ old('status', $location->status) == 'published' ? 'selected' : '' }}>Công khai</option>
                                <option value="draft" {{ old('status', $location->status) == 'draft' ? 'selected' : '' }}>Bản nháp</option>
                                <option value="hidden" {{ old('status', $location->status) == 'hidden' ? 'selected' : '' }}>Ẩn</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="thumbnail" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Ảnh đại diện (Thumbnail)</label>
                            <input type="file" class="form-control form-control-sm @error('thumbnail') is-invalid @enderror" id="thumbnail" name="thumbnail" accept="image/*" style="border-color: #e2e8f0;">
                            @error('thumbnail') <div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div> @enderror
                            @if($location->thumbnail_url)
                                <div class="mt-2">
                                    <div class="text-muted mb-1" style="font-size: 0.725rem;">Ảnh đại diện hiện tại:</div>
                                    <img src="{{ asset('storage/' . $location->thumbnail_url) }}" class="rounded border" style="max-height: 90px; object-fit: cover;" alt="Thumbnail">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top" style="border-color: var(--border-light) !important;">
                    <a href="{{ route('admin.locations.index', request()->query()) }}" class="btn-minimal text-decoration-none">Quay lại</a>
                    <button type="submit" class="btn-minimal btn-minimal-primary">Cập nhật Thông tin</button>
                </div>
            </form>
        </div>

        @if(!empty($isBusinessLocation))
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 px-1">
                <span class="text-muted" style="font-size: 0.78rem;">
                    Chủ DN: {{ $businessOwner->email ?? ('#' . $location->created_by) }}
                </span>
                <button type="button" class="btn-minimal py-1 px-2" style="font-size: 0.72rem;"
                    data-bs-toggle="modal" data-bs-target="#revokeBizModal">Thu hồi DN</button>
            </div>

            <div class="modal fade" id="revokeBizModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('admin.locations.revoke_business', $location->id) }}" id="revokeBizForm">
                            @csrf
                            @foreach(request()->only(['search', 'category_id', 'sort_dir']) as $qKey => $qVal)
                                <input type="hidden" name="{{ $qKey }}" value="{{ $qVal }}">
                            @endforeach
                            <div class="modal-header">
                                <h5 class="modal-title" style="font-size: 1rem;">Thu hồi quyền DN</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-secondary mb-2" style="font-size: 0.82rem;">
                                    Gỡ chủ DN khỏi <strong>{{ $location->name }}</strong> (địa điểm vẫn trên map).
                                </p>
                                <label class="form-label" style="font-size: 0.8rem;">Lý do <span class="text-danger">*</span></label>
                                <textarea name="revoke_reason" id="revokeBizReason" class="form-control form-control-sm" rows="3" maxlength="1000" required placeholder="Nhập lý do thu hồi..."></textarea>
                                <div class="invalid-feedback">Vui lòng nhập lý do.</div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn-minimal" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn-minimal">Thu hồi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- IMAGES TAB -->
    <div class="tab-pane fade" id="images" role="tabpanel">
        <div class="card-minimal mb-4">
            <div class="p-3 border-bottom" style="border-color: var(--border-light) !important;">
                <div class="fw-medium text-dark" style="font-size: 0.85rem;">Upload Hình ảnh mới</div>
            </div>
            <div class="p-3">
                <!-- Drag & Drop Dropzone -->
                <div id="imageDropZone" class="border rounded p-4 text-center bg-light mb-3" onclick="document.getElementById('imageUploadInput').click()" style="border-style: dashed !important; border-color: #cbd5e1 !important; transition: all 0.2s ease; cursor: pointer;">
                    <input type="file" id="imageUploadInput" class="d-none" multiple accept="image/*">
                    <div class="text-muted" style="font-size: 0.825rem;">
                        <div class="fw-medium text-dark mb-1">Kéo & thả hình ảnh vào đây hoặc bấm để chọn ảnh</div>
                        <div style="font-size: 0.725rem;">Hỗ trợ tải lên nhiều file ảnh cùng lúc (PNG, JPG, WEBP)</div>
                    </div>
                </div>

                <div id="imagesList">
                    <div class="row g-3">
                        @foreach($images as $img)
                        <div class="col-md-3 col-sm-4 image-card" id="img-{{ $img->id }}">
                            <div class="card-minimal h-100 position-relative p-1">
                                <img src="{{ Storage::url($img->image_url) }}" class="rounded w-100 object-fit-cover" height="130" alt="">
                                <div class="position-absolute top-0 end-0 p-2">
                                    <button class="btn-minimal py-1 px-2 text-danger bg-white shadow-sm border btn-delete-image" data-id="{{ $img->id }}" style="font-size: 0.75rem;">Xóa</button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PANORAMA TAB -->
    <div class="tab-pane fade" id="pano" role="tabpanel">
        <div class="card-minimal text-center p-5 mb-4">
            <h5 class="mb-2 fw-medium text-dark">Trình chỉnh sửa Tour 360° nâng cao</h5>
            <div class="text-muted mb-3 mx-auto" style="max-width: 540px; font-size: 0.825rem;">Nhấn vào nút bên dưới để mở công cụ chỉnh sửa trực quan (thêm hotspot, liên kết ảnh, v.v.).</div>
            <a href="{{ route('admin.locations.360_editor', $location->id) }}" class="btn-minimal btn-minimal-primary mx-auto text-decoration-none px-4 py-2" style="font-size: 0.85rem;">
                Mở 360 Tour Editor
            </a>
        </div>
    </div>

    <!-- AUDIO TAB -->
    <div class="tab-pane fade" id="audio" role="tabpanel">
        <style>
            .transition-transform {
                transition: transform 0.2s ease-in-out;
            }
            .rotate-180 {
                transform: rotate(180deg);
            }
            /* Custom Range Sliders track & thumb visual fixes */
            .form-range {
                height: 1.5rem;
                padding: 0;
                background: transparent;
                margin-top: 5px;
            }
            .form-range::-webkit-slider-runnable-track {
                width: 100%;
                height: 5px;
                cursor: pointer;
                background: #e2e8f0;
                border-radius: 3px;
                border: none;
            }
            .form-range::-webkit-slider-thumb {
                height: 16px;
                width: 16px;
                border-radius: 50%;
                background: #3b82f6;
                cursor: pointer;
                -webkit-appearance: none;
                margin-top: -5.5px;
                box-shadow: 0 1px 2px rgba(0,0,0,0.15);
                transition: transform 0.1s ease, background-color 0.1s ease;
            }
            .form-range::-webkit-slider-thumb:hover {
                transform: scale(1.1);
                background: #2563eb;
            }
            .form-range::-moz-range-track {
                width: 100%;
                height: 5px;
                cursor: pointer;
                background: #e2e8f0;
                border-radius: 3px;
                border: none;
            }
            .form-range::-moz-range-thumb {
                height: 16px;
                width: 16px;
                border-radius: 50%;
                background: #3b82f6;
                cursor: pointer;
                border: none;
                box-shadow: 0 1px 2px rgba(0,0,0,0.15);
                transition: transform 0.1s ease, background-color 0.1s ease;
            }
            .form-range::-moz-range-thumb:hover {
                transform: scale(1.1);
                background: #2563eb;
            }
        </style>
        @if($location->audio_url)
            <div class="card-minimal p-3 mb-4">
                <div class="fw-medium text-dark mb-3 pb-2 border-bottom" style="font-size: 0.85rem; border-color: var(--border-light) !important;">Trình phát audio thuyết minh hiện tại</div>
                <div>
                    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded border {{ isset($location->attributes['tts_text']) ? 'mb-3' : '' }}" style="border-color: #e2e8f0 !important;">
                        <div class="flex-grow-1">
                            <audio controls class="w-100" style="height: 38px;" id="currentAudioPlayer">
                                <source src="{{ asset('storage/' . $location->audio_url) }}" type="audio/mpeg">
                                Trình duyệt không hỗ trợ thẻ audio.
                            </audio>
                        </div>
                        <button class="btn-minimal py-1 px-3 text-danger" id="btnDeleteAudio" style="font-size: 0.775rem;">
                            Xóa audio
                        </button>
                    </div>

                    @if(isset($location->attributes['tts_text']))
                        <div class="p-3 bg-light rounded border" style="border-color: #e2e8f0 !important;">
                            <div class="fw-medium text-muted mb-2" style="font-size: 0.775rem;">Nội dung văn bản thuyết minh AI:</div>
                            <div class="text-dark bg-white p-3 rounded border" style="font-size: 0.8rem; line-height: 1.6; border-left: 3px solid #cbd5e1 !important; white-space: pre-wrap;">"{{ $location->attributes['tts_text'] }}"</div>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="p-3 rounded border mb-4 text-muted" style="border-color: #e2e8f0 !important; background: #f8fafc; font-size: 0.8rem;">
                <div class="fw-medium text-dark mb-1">Chưa có audio thuyết minh</div>
                <div>Chọn một trong hai phương thức bên dưới để thêm âm thanh thuyết minh cho địa điểm này khi xem ở chế độ 360°.</div>
            </div>
        @endif

        <div class="row g-4">
            <!-- Upload thủ công -->
            <div class="col-md-5">
                <div class="card-minimal p-3 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="fw-medium text-dark mb-1" style="font-size: 0.85rem;">Cách 1: Upload file thủ công</div>
                        <div class="text-muted mb-3" style="font-size: 0.75rem;">Tải lên file âm thanh thuyết minh có sẵn trên thiết bị của bạn.</div>
                        <div class="text-center py-4 text-muted" style="font-size: 0.75rem;">
                            <div>Hỗ trợ các định dạng: MP3, WAV, OGG, M4A</div>
                            <div>Dung lượng tối đa: 20MB</div>
                        </div>
                    </div>
                    <div>
                        <input type="file" id="audioUploadInput" class="d-none" accept="audio/*">
                        <button type="button" class="btn-minimal btn-minimal-primary w-100 py-2 text-center" onclick="document.getElementById('audioUploadInput').click()">
                            {{ $location->audio_url ? 'Đổi file audio khác' : 'Chọn file từ thiết bị' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tạo bằng AI -->
            <div class="col-md-7">
                <div class="card-minimal p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 pb-2 border-bottom" style="border-color: var(--border-light) !important;">
                        <div>
                            <div class="fw-medium text-dark" style="font-size: 0.85rem;">Cách 2: Tạo bằng AI Text-to-Speech (VieNeu-TTS)</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Chuyển đổi văn bản tiếng Việt thành giọng nói tự động bằng AI.</div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span id="ttsConnectionBadge" class="badge-minimal">
                                Đang kiểm tra...
                            </span>
                            <button type="button" id="btnRetryTtsConnection" class="btn-minimal py-0 px-2" title="Kiểm tra lại kết nối" style="font-size: 0.75rem;">
                                Tải lại
                            </button>
                        </div>
                    </div>
                    <div>

                        <div class="mb-3">
                            <label for="ttsVoiceSelect" class="form-label text-dark fw-medium" style="font-size: 0.8rem;">Chọn giọng đọc (AI Voice) <span class="text-danger">*</span></label>
                            <select id="ttsVoiceSelect" class="form-select form-select-sm" style="border-color: #e2e8f0;">
                                <option value="">Đang tải danh sách giọng đọc...</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="ttsTextInput" class="form-label text-dark fw-medium" style="font-size: 0.8rem;">Văn bản thuyết minh <span class="text-danger">*</span></label>
                            <textarea id="ttsTextInput" class="form-control form-control-sm" rows="4" placeholder="Nhập đoạn thuyết minh giới thiệu tại đây (Tối đa 5000 ký tự)..." style="border-color: #e2e8f0;">{{ old('tts_text', $location->attributes['tts_text'] ?? $location->short_description) }}</textarea>
                            <div class="form-text d-flex justify-content-end text-muted" style="font-size: 0.725rem;">
                                <span id="ttsCharCount">0/5000</span>
                            </div>
                        </div>

                        <!-- Cài đặt nâng cao -->
                        <div class="mb-3">
                            <button class="btn-minimal w-100 d-flex align-items-center justify-content-between py-1 px-2" type="button" data-bs-toggle="collapse" data-bs-target="#ttsAdvancedSettings" aria-expanded="false" aria-controls="ttsAdvancedSettings" style="font-size: 0.775rem;">
                                <span class="fw-medium text-dark">Cài đặt nâng cao (AI Settings)</span>
                                <span id="advancedChevron" class="transition-transform">▼</span>
                            </button>
                            <div class="collapse mt-2" id="ttsAdvancedSettings">
                                <div class="p-3 bg-light rounded border border-0" style="font-size: 0.75rem;">
                                    <div class="row g-3">
                                        <!-- Cảm xúc -->
                                        <div class="col-md-6">
                                            <label for="ttsEmotionSelect" class="form-label fw-medium mb-1 text-muted" style="font-size: 0.725rem;">CẢM XÚC (EMOTION)</label>
                                            <select id="ttsEmotionSelect" class="form-select form-select-sm" style="border-color: #e2e8f0;">
                                                <option value="natural" selected>Tự nhiên (Natural)</option>
                                                <option value="happy">Vui vẻ (Happy)</option>
                                                <option value="sad">Buồn bã (Sad)</option>
                                                <option value="angry">Tức giận (Angry)</option>
                                                <option value="fearful">Sợ hãi (Fearful)</option>
                                                <option value="excited">Phấn khích (Excited)</option>
                                            </select>
                                        </div>
                                        <!-- Độ ngẫu nhiên -->
                                        <div class="col-md-6">
                                            <label for="ttsTemperatureInput" class="form-label fw-medium mb-1 text-muted" style="font-size: 0.725rem;">ĐỘ NGẪU NHIÊN (TEMP: <span id="ttsTemperatureVal" class="text-dark">0.8</span>)</label>
                                            <input type="range" id="ttsTemperatureInput" class="form-range" min="0.1" max="1.5" step="0.05" value="0.8">
                                            <div class="text-muted mt-1" style="font-size: 0.675rem;">Thấp: giọng đều. Cao: truyền cảm tự nhiên.</div>
                                        </div>
                                        <!-- Top-K -->
                                        <div class="col-md-4">
                                            <label for="ttsTopKInput" class="form-label fw-medium mb-1 text-muted" style="font-size: 0.725rem;">GIỚI HẠN TỪ (TOP-K: <span id="ttsTopKVal" class="text-dark">25</span>)</label>
                                            <input type="range" id="ttsTopKInput" class="form-range" min="1" max="100" step="1" value="25">
                                        </div>
                                        <!-- Top-P -->
                                        <div class="col-md-4">
                                            <label for="ttsTopPInput" class="form-label fw-medium mb-1 text-muted" style="font-size: 0.725rem;">TỶ LỆ LỌC (TOP-P: <span id="ttsTopPVal" class="text-dark">0.95</span>)</label>
                                            <input type="range" id="ttsTopPInput" class="form-range" min="0.1" max="1.0" step="0.05" value="0.95">
                                        </div>
                                        <!-- Phạt lặp từ -->
                                        <div class="col-md-4">
                                            <label for="ttsPenaltyInput" class="form-label fw-medium mb-1 text-muted" style="font-size: 0.725rem;">PHẠT LẶP TỪ (PENALTY: <span id="ttsPenaltyVal" class="text-dark">1.2</span>)</label>
                                            <input type="range" id="ttsPenaltyInput" class="form-range" min="0.5" max="2.0" step="0.05" value="1.2">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tiến trình xử lý AI (Progress Bar) -->
                        <div id="ttsProgressContainer" class="mb-3 d-none p-3 bg-light rounded border border-light">
                            <div class="d-flex justify-content-between mb-1 small" style="font-size: 0.75rem;">
                                <span id="ttsProgressStatus" class="fw-medium text-dark">Đang kết nối tới máy chủ AI...</span>
                                <span id="ttsProgressPercent" class="fw-medium text-muted">0%</span>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 3px;">
                                <div id="ttsProgressBar" class="progress-bar bg-primary" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="mt-1 text-muted" style="font-size: 0.7rem;">
                                <span id="ttsProgressTimer">Đã chạy: 0.0s</span>
                            </div>
                        </div>

                        <button type="button" id="btnGenerateTts" class="btn-minimal btn-minimal-primary w-100 py-2">
                            <span class="spinner-border spinner-border-sm me-1 d-none" role="status" id="ttsSpinner" aria-hidden="true"></span>
                            <span id="ttsRobotIcon"></span>
                            Tạo âm thanh thuyết minh AI
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function() {
        const form = document.getElementById('revokeBizForm');
        const reason = document.getElementById('revokeBizReason');
        if (form && reason) {
            form.addEventListener('submit', function(e) {
                if (!reason.value.trim()) {
                    e.preventDefault();
                    reason.classList.add('is-invalid');
                }
            });
        }
    })();

    // CSRF Token Setup
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });

    // Tự động tách tọa độ khi dán định dạng "lat, lng" vào ô Lat
    document.getElementById('lat').addEventListener('input', function() {
        let val = this.value;
        if (val.includes(',')) {
            let parts = val.split(',');
            if (parts.length >= 2) {
                this.value = parts[0].trim();
                document.getElementById('lng').value = parts[1].trim();
            }
        }
    });

    // Upload Image Drag & Drop
    let dropZone = $('#imageDropZone');
    dropZone.on('dragover dragenter', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropZone.css({'border-color': '#3b82f6', 'background-color': '#eff6ff'});
    });
    dropZone.on('dragleave dragend drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropZone.css({'border-color': '#cbd5e1', 'background-color': '#f8fafc'});
    });
    dropZone.on('drop', function(e) {
        let files = e.originalEvent.dataTransfer.files;
        if(files && files.length > 0) {
            handleImageFiles(files);
        }
    });

    $('#imageUploadInput').change(function() {
        handleImageFiles(this.files);
    });

    function handleImageFiles(files) {
        if(files.length === 0) return;
        for(let i=0; i<files.length; i++) {
            let formData = new FormData();
            formData.append('file', files[i]);
            
            $.ajax({
                url: '{{ route('admin.locations.upload_image', $location->id, false) }}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if(response.success) {
                        let html = `
                            <div class="col-md-3 col-sm-4 image-card" id="img-${response.image.id}">
                                <div class="card-minimal h-100 position-relative p-1">
                                    <img src="${response.url}" class="rounded w-100 object-fit-cover" height="130" alt="">
                                    <div class="position-absolute top-0 end-0 p-2">
                                        <button class="btn-minimal py-1 px-2 text-danger bg-white shadow-sm border btn-delete-image" data-id="${response.image.id}" style="font-size: 0.75rem;">Xóa</button>
                                    </div>
                                </div>
                            </div>
                        `;
                        $('#imagesList .row').append(html);
                    }
                }
            });
        }
    }

    // Delete Image
    $(document).on('click', '.btn-delete-image', function() {
        let btn = $(this);
        let id = btn.data('id');
        
        Swal.fire({
            title: 'Xóa hình ảnh',
            html: 'Bạn có chắc chắn muốn xóa ảnh này khỏi địa điểm không?',
            icon: 'warning',
            iconColor: '#eab308',
            showCancelButton: true,
            confirmButtonText: 'Xóa ảnh',
            cancelButtonText: 'Hủy bỏ',
            reverseButtons: true,
            customClass: {
                popup: 'custom-swal-popup',
                title: 'custom-swal-title',
                htmlContainer: 'custom-swal-text',
                confirmButton: 'custom-swal-confirm-btn custom-swal-confirm-danger',
                cancelButton: 'custom-swal-cancel-btn'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/locations/image/' + id,
                    type: 'DELETE',
                    success: function(res) {
                        if (res.success) {
                            $('#img-' + id).fadeOut(300, function() { $(this).remove(); });
                            Swal.fire({
                                icon: 'success',
                                iconColor: '#166534',
                                title: 'Thành công',
                                text: 'Đã xóa hình ảnh thành công!',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                customClass: { popup: 'custom-swal-toast' }
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            iconColor: '#dc2626',
                            title: 'Có lỗi xảy ra',
                            text: 'Không thể xóa hình ảnh này.',
                            confirmButtonText: 'Đóng',
                            customClass: {
                                popup: 'custom-swal-popup',
                                title: 'custom-swal-title',
                                htmlContainer: 'custom-swal-text',
                                confirmButton: 'custom-swal-confirm-btn custom-swal-confirm-danger'
                            },
                            buttonsStyling: false
                        });
                    }
                });
            }
        });
    });

    // Upload Audio
    $('#audioUploadInput').change(function() {
        let file = this.files[0];
        if (!file) return;

        let formData = new FormData();
        formData.append('audio', file);

        $.ajax({
            url: '{{ route("admin.locations.upload_audio", $location->id, false) }}',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        iconColor: '#166534',
                        title: 'Thành công',
                        text: 'Upload audio thuyết minh thành công!',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    iconColor: '#dc2626',
                    title: 'Lỗi upload audio',
                    text: (xhr.responseJSON?.message || xhr.responseText),
                    confirmButtonText: 'Đóng',
                    customClass: {
                        popup: 'custom-swal-popup',
                        title: 'custom-swal-title',
                        htmlContainer: 'custom-swal-text',
                        confirmButton: 'custom-swal-confirm-btn custom-swal-confirm-danger'
                    },
                    buttonsStyling: false
                });
            }
        });
    });

    // Delete Audio
    $(document).on('click', '#btnDeleteAudio', function() {
        Swal.fire({
            title: 'Xóa audio thuyết minh',
            html: 'Bạn có chắc chắn muốn xóa file audio thuyết minh của địa điểm này không?',
            icon: 'warning',
            iconColor: '#eab308',
            showCancelButton: true,
            confirmButtonText: 'Xóa audio',
            cancelButtonText: 'Hủy bỏ',
            reverseButtons: true,
            customClass: {
                popup: 'custom-swal-popup',
                title: 'custom-swal-title',
                htmlContainer: 'custom-swal-text',
                confirmButton: 'custom-swal-confirm-btn custom-swal-confirm-danger',
                cancelButton: 'custom-swal-cancel-btn'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.locations.delete_audio", $location->id, false) }}',
                    type: 'DELETE',
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                iconColor: '#166534',
                                title: 'Thành công',
                                text: 'Đã xóa audio thuyết minh!',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            iconColor: '#dc2626',
                            title: 'Có lỗi xảy ra',
                            text: 'Lỗi xóa audio: ' + xhr.responseText,
                            confirmButtonText: 'Đóng',
                            customClass: {
                                popup: 'custom-swal-popup',
                                title: 'custom-swal-title',
                                htmlContainer: 'custom-swal-text',
                                confirmButton: 'custom-swal-confirm-btn custom-swal-confirm-danger'
                            },
                            buttonsStyling: false
                        });
                    }
                });
            }
        });
    });

    // Fetch voices list for VieNeu-TTS
    function loadTtsVoices() {
        console.log('[TTS] loadTtsVoices() called');
        let voiceSelect = $('#ttsVoiceSelect');
        let badge = $('#ttsConnectionBadge');

        console.log('[TTS] voiceSelect found:', voiceSelect.length);
        console.log('[TTS] badge found:', badge.length);

        if (voiceSelect.length === 0) {
            console.warn('[TTS] voiceSelect not found, aborting');
            return;
        }
        
        badge.text('Đang kết nối...')
             .attr('title', 'Đang kiểm tra kết nối tới máy chủ VieNeu-TTS...');

        let ajaxUrl = '/admin/locations/tts-voices';
        console.log('[TTS] AJAX URL:', ajaxUrl);
             
        $.ajax({
            url: ajaxUrl,
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(res) {
                console.log('[TTS] AJAX success, response:', res);
                voiceSelect.empty();
                
                // Check if connection failed or error
                if (!Array.isArray(res) || res.length === 0 || (res.length === 1 && res[0].id === 'error')) {
                    voiceSelect.append('<option value="">⚠️ Không có giọng đọc (Server Offline)</option>');
                    $('#btnGenerateTts').prop('disabled', true);
                    
                    let errorMsg = 'Không thể kết nối tới máy chủ VieNeu-TTS.';
                    if (Array.isArray(res) && res.length === 1 && res[0].name) {
                        errorMsg = res[0].name.replace('⚠️ ', '');
                    }
                    
                    badge.text('Lỗi kết nối').attr('title', errorMsg);
                    return;
                }
                
                $('#btnGenerateTts').prop('disabled', false);
                
                badge.text('Kết nối tốt')
                     .attr('title', 'Đã kết nối thành công tới máy chủ VieNeu-TTS.');
                
                let savedVoice = localStorage.getItem('vieneu_tts_voice');
                res.forEach(function(voice) {
                    if (!voice || !voice.id) return;
                    let vId = String(voice.id);
                    let vName = voice.name || vId;
                    let selected = (savedVoice && vId === savedVoice) ? 'selected' : '';
                    voiceSelect.append('<option value="' + vId + '" ' + selected + '>' + vName + '</option>');
                });
                
                updateTtsCharCount();
            },
            error: function(xhr, status, error) {
                console.error('[TTS] AJAX error:', status, error, xhr.status, xhr.responseText);
                voiceSelect.empty().append('<option value="">⚠️ Lỗi kết nối tới máy chủ TTS</option>');
                $('#btnGenerateTts').prop('disabled', true);
                
                badge.text('Mất kết nối')
                     .attr('title', 'Không thể kết nối tới máy chủ VieNeu-TTS (Cổng 8001). Status: ' + xhr.status);
            }
        });
    }

    // Retry TTS Connection
    $(document).on('click', '#btnRetryTtsConnection', function(e) {
        e.preventDefault();
        console.log('[TTS] Retry button clicked');
        let btn = $(this);
        btn.prop('disabled', true);
        loadTtsVoices();
        setTimeout(function() {
            btn.prop('disabled', false);
        }, 1500);
    });

    // Save active tab to localStorage on tab switch & load TTS if audio tab
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        let tabId = $(e.target).attr('id');
        localStorage.setItem('active_location_tab', tabId);
        if (tabId === 'audio-tab') {
            loadTtsVoices();
        }
    });

    // Restore active tab from localStorage on load
    let activeTabId = localStorage.getItem('active_location_tab');
    if (activeTabId) {
        let activeTab = $('#' + activeTabId);
        if (activeTab.length) {
            let tabTrigger = new bootstrap.Tab(activeTab[0]);
            tabTrigger.show();
        }
    }

    // Always trigger loadTtsVoices on page load
    console.log('[TTS] Page loaded, calling loadTtsVoices...');
    loadTtsVoices();

    // Count chars in TTS text input
    function updateTtsCharCount() {
        let text = $('#ttsTextInput').val() || '';
        let len = text.length;
        $('#ttsCharCount').text(len + '/5000');
        if (len > 5000) {
            $('#ttsCharCount').addClass('text-danger');
            $('#btnGenerateTts').prop('disabled', true);
        } else {
            $('#ttsCharCount').removeClass('text-danger');
            // Check if voices are loaded before enabling
            let val = $('#ttsVoiceSelect').val();
            if (val && val !== 'error' && val !== '') {
                $('#btnGenerateTts').prop('disabled', false);
            }
        }
    }
    
    $('#ttsTextInput').on('input', updateTtsCharCount);

    // Update advanced settings slider values and save to localStorage
    $('#ttsTemperatureInput').on('input', function() {
        let val = $(this).val();
        $('#ttsTemperatureVal').text(val);
        localStorage.setItem('vieneu_tts_temp', val);
    });
    $('#ttsTopKInput').on('input', function() {
        let val = $(this).val();
        $('#ttsTopKVal').text(val);
        localStorage.setItem('vieneu_tts_top_k', val);
    });
    $('#ttsTopPInput').on('input', function() {
        let val = $(this).val();
        $('#ttsTopPVal').text(val);
        localStorage.setItem('vieneu_tts_top_p', val);
    });
    $('#ttsPenaltyInput').on('input', function() {
        let val = $(this).val();
        $('#ttsPenaltyVal').text(val);
        localStorage.setItem('vieneu_tts_penalty', val);
    });

    // Save dropdown changes to localStorage
    $(document).on('change', '#ttsVoiceSelect', function() {
        localStorage.setItem('vieneu_tts_voice', $(this).val());
    });
    $('#ttsEmotionSelect').change(function() {
        localStorage.setItem('vieneu_tts_emotion', $(this).val());
    });

    // Restore settings from localStorage on load
    $(document).ready(function() {
        let savedEmotion = localStorage.getItem('vieneu_tts_emotion');
        if (savedEmotion) {
            $('#ttsEmotionSelect').val(savedEmotion);
        }

        let savedTemp = localStorage.getItem('vieneu_tts_temp');
        if (savedTemp) {
            $('#ttsTemperatureInput').val(savedTemp);
            $('#ttsTemperatureVal').text(savedTemp);
        }

        let savedTopK = localStorage.getItem('vieneu_tts_top_k');
        if (savedTopK) {
            $('#ttsTopKInput').val(savedTopK);
            $('#ttsTopKVal').text(savedTopK);
        }

        let savedTopP = localStorage.getItem('vieneu_tts_top_p');
        if (savedTopP) {
            $('#ttsTopPInput').val(savedTopP);
            $('#ttsTopPVal').text(savedTopP);
        }

        let savedPenalty = localStorage.getItem('vieneu_tts_penalty');
        if (savedPenalty) {
            $('#ttsPenaltyInput').val(savedPenalty);
            $('#ttsPenaltyVal').text(savedPenalty);
        }
    });

    // Toggle chevron rotation
    $('#ttsAdvancedSettings').on('show.bs.collapse', function () {
        $('#advancedChevron').addClass('rotate-180');
    }).on('hide.bs.collapse', function () {
        $('#advancedChevron').removeClass('rotate-180');
    });

    // Generate TTS Audio
    $('#btnGenerateTts').click(function() {
        let text = $('#ttsTextInput').val().trim();
        let voiceId = $('#ttsVoiceSelect').val();
        let emotion = $('#ttsEmotionSelect').val();
        let temperature = $('#ttsTemperatureInput').val();
        let topK = $('#ttsTopKInput').val();
        let topP = $('#ttsTopPInput').val();
        let repetitionPenalty = $('#ttsPenaltyInput').val();

        if (!text) {
            Swal.fire({
                icon: 'warning',
                iconColor: '#eab308',
                title: 'Thông báo',
                text: 'Vui lòng nhập văn bản thuyết minh.',
                confirmButtonText: 'Đóng',
                customClass: {
                    popup: 'custom-swal-popup',
                    title: 'custom-swal-title',
                    htmlContainer: 'custom-swal-text',
                    confirmButton: 'custom-swal-confirm-btn'
                },
                buttonsStyling: false
            });
            return;
        }

        let btn = $(this);
        let spinner = $('#ttsSpinner');
        let robotIcon = $('#ttsRobotIcon');

        // Show loading state
        btn.prop('disabled', true);
        spinner.removeClass('d-none');
        robotIcon.addClass('d-none');
        btn.contents().last()[0].textContent = ' Đang xử lý AI TTS...';

        // Calculate expected time based on character length
        // V3 Turbo CPU averages around 40-50 chars per second of generation time.
        let charLen = text.length;
        let expectedSec = Math.max(3, Math.ceil(charLen / 45)) + 3; 
        
        $('#ttsProgressContainer').removeClass('d-none');
        $('#ttsProgressPercent').text('0%');
        $('#ttsProgressBar').css('width', '0%').attr('aria-valuenow', 0);
        $('#ttsProgressTimer').text('Đã chạy: 0.0s');
        
        let start = Date.now();
        let progressInterval = setInterval(function() {
            let elapsed = (Date.now() - start) / 1000;
            $('#ttsProgressTimer').text('Đã chạy: ' + elapsed.toFixed(1) + 's');
            
            // Calculate percentage (cap at 95% until done)
            let percent = Math.min(95, Math.round((elapsed / expectedSec) * 100));
            $('#ttsProgressPercent').text(percent + '%');
            $('#ttsProgressBar').css('width', percent + '%').attr('aria-valuenow', percent);
            
            // Dynamic statuses based on elapsed time
            if (elapsed < 1.5) {
                $('#ttsProgressStatus').html('<i class="fas fa-cog fa-spin me-1 text-warning"></i> Đang khởi tạo mô hình AI...');
            } else if (elapsed < 3) {
                $('#ttsProgressStatus').html('<i class="fas fa-brain fa-spin me-1 text-warning"></i> Đang phân tích cú pháp văn bản...');
            } else if (elapsed < expectedSec * 0.8) {
                $('#ttsProgressStatus').html('<i class="fas fa-volume-up fa-spin me-1 text-warning"></i> Đang tổng hợp giọng nói AI...');
            } else {
                $('#ttsProgressStatus').html('<i class="fas fa-file-audio fa-spin me-1 text-warning"></i> Đang hoàn thiện tệp WAV...');
            }
        }, 100);

        $.ajax({
            url: '{{ route("admin.locations.generate_tts", $location->id, false) }}',
            type: 'POST',
            data: {
                text: text,
                voice_id: voiceId,
                emotion: emotion,
                temperature: temperature,
                top_k: topK,
                top_p: topP,
                repetition_penalty: repetitionPenalty
            },
            success: function(res) {
                clearInterval(progressInterval);
                $('#ttsProgressPercent').text('100%');
                $('#ttsProgressBar').css('width', '100%').attr('aria-valuenow', 100);
                $('#ttsProgressStatus').html('<i class="fas fa-check-circle text-success me-1"></i> Hoàn thành!');

                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        iconColor: '#166534',
                        title: 'Thành công',
                        text: 'Tạo âm thanh thuyết minh AI thành công!',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        iconColor: '#dc2626',
                        title: 'Lỗi tạo âm thanh',
                        text: res.message,
                        confirmButtonText: 'Đóng',
                        customClass: {
                            popup: 'custom-swal-popup',
                            title: 'custom-swal-title',
                            htmlContainer: 'custom-swal-text',
                            confirmButton: 'custom-swal-confirm-btn custom-swal-confirm-danger'
                        },
                        buttonsStyling: false
                    });
                    resetTtsBtn();
                }
            },
            error: function(xhr) {
                clearInterval(progressInterval);
                $('#ttsProgressContainer').addClass('d-none');
                let msg = xhr.responseJSON?.message || xhr.responseText || 'Lỗi không xác định';
                Swal.fire({
                    icon: 'error',
                    iconColor: '#dc2626',
                    title: 'Lỗi chuyển đổi TTS',
                    text: msg,
                    confirmButtonText: 'Đóng',
                    customClass: {
                        popup: 'custom-swal-popup',
                        title: 'custom-swal-title',
                        htmlContainer: 'custom-swal-text',
                        confirmButton: 'custom-swal-confirm-btn custom-swal-confirm-danger'
                    },
                    buttonsStyling: false
                });
                resetTtsBtn();
            }
        });

        function resetTtsBtn() {
            btn.prop('disabled', false);
            spinner.addClass('d-none');
            robotIcon.removeClass('d-none');
            btn.contents().last()[0].textContent = ' Tạo âm thanh thuyết minh AI';
        }
    });

</script>
@endpush
