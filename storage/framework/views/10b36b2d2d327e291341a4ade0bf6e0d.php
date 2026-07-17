<!DOCTYPE html>
<html>
<head>
    <title>360 Tour Editor - <?php echo e($location->name); ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, minimal-ui" />
    <style> @-ms-viewport { width: device-width; } </style>
    
    <!-- Marzipano Original CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('marzipano/vendor/reset.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('marzipano/style.css')); ?>?v=<?php echo e(time()); ?>">
    
    <!-- Bootstrap for Editor Modals -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --hotspot-color: <?php echo e($location->category->icon_color ?? '#FF512F'); ?>;
        }
        /* Layout Structure */
        body, html { margin: 0; padding: 0; height: 100vh; overflow: hidden; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        #app { display: flex; height: 100vh; flex-direction: column; }
        
        /* Topbar */
        .topbar { background-color: #2c3e50; color: white; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; z-index: 1000; position: relative; height: 50px; }
        
        /* Main Layout */
        .main-layout { display: flex; flex: 1; height: calc(100vh - 50px); position: relative; }
        
        /* Sidebar */
        .sidebar { width: 300px; background-color: #34495e; color: white; display: flex; flex-direction: column; overflow-y: auto; z-index: 10; }
        .section-header { background-color: #2c3e50; padding: 10px 15px; font-weight: bold; font-size: 14px; text-transform: uppercase; }
        .scene-item { padding: 10px 15px; border-bottom: 1px solid #2c3e50; display: flex; align-items: center; cursor: pointer; transition: background 0.2s; }
        .scene-item:hover, .scene-item.active { background-color: #1abc9c; }
        .scene-item img { width: 50px; height: 35px; object-fit: cover; margin-right: 10px; border-radius: 3px; }
        .scene-item .name { flex: 1; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: white; }
        .scene-item .actions { opacity: 0; transition: opacity 0.2s; }
        .scene-item:hover .actions, .scene-item.active .actions { opacity: 1; }
        .add-pano-btn { display: block; width: 100%; padding: 15px; background: #3498db; color: white; text-align: center; font-weight: bold; border: none; cursor: pointer; }
        .add-pano-btn:hover { background: #2980b9; }

        /* Viewer Area */
        .viewer-area { flex: 1; position: relative; background: #000; overflow: hidden; }
        #pano { position: absolute; top: 0; left: 0; right: 0; bottom: 0; width: 100%; height: 100%; }

        /* Override Marzipano CSS for new layout */
        #titleBar { display: none !important; }
        #autorotateToggle, #fullscreenToggle { top: 0; }
        #sceneList { display: none !important; }
        #sceneListToggle { display: none !important; }

        /* Topbar Buttons */
        .editor-btn {
            background: #3498db; color: white; border: none; padding: 6px 12px;
            border-radius: 4px; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 5px; text-decoration: none;
        }
        .editor-btn:hover { background: #2980b9; color: white; }
        .editor-btn.btn-warning { background: #e67e22; color: white; }
        .editor-btn.btn-purple { background: #9b59b6; color: white; }
        .editor-btn.btn-danger { background: #e74c3c; color: white; }
        .editor-btn.btn-success { background: #2ecc71; color: white; }
        .editor-btn.btn-secondary { background: #7f8c8d; color: white; }
        
        @keyframes pulseGreen {
            0% { box-shadow: 0 0 0 0 rgba(46, 204, 113, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(46, 204, 113, 0); }
            100% { box-shadow: 0 0 0 0 rgba(46, 204, 113, 0); }
        }
        .pulse-green {
            animation: pulseGreen 1.5s infinite;
        }

        /* Draggable Hotspot Icon Override */
        .info-hotspot-header { cursor: pointer; }
        .link-hotspot-icon { cursor: pointer; }
        .hotspot.editing .link-hotspot-icon, .hotspot.editing .info-hotspot-icon-wrapper {
            box-shadow: 0 0 0 4px rgba(255,255,255,0.7); border-radius: 50%;
        }

        /* Prevent native browser drag on hotspot images in editor mode */
        body.editor-mode .hotspot img {
            -webkit-user-drag: none;
            user-drag: none;
            pointer-events: none;
        }
        body.editor-mode .hotspot {
            cursor: grab;
        }
        body.editor-mode .hotspot:active {
            cursor: grabbing;
        }

        /* Permanent Context Menu (Editor Mode) */
        body.editor-mode .hotspot-context-menu { opacity: 0; pointer-events: none; transition: opacity 0.2s, transform 0.2s; transform: translate(-50%, -50%) scale(0.1); display: block; }
        body.editor-mode .hotspot.active-menu .hotspot-context-menu { opacity: 1; pointer-events: auto; transform: translate(-50%, -50%) scale(clamp(0.7, var(--base-scale, 1), 1.2)); }
        body.editor-mode .hotspot.active-menu { z-index: 100000 !important; }
        .hotspot-context-menu {
            --R: 45px;
            display: none;
            position: absolute;
            top: 50%; left: 50%;
            pointer-events: none;
            z-index: 9999 !important;
        }
        .context-menu-btn {
            position: absolute; width: 30px; height: 30px;
            background: rgba(40,40,40,0.85); color: white; border-radius: 50%;
            display: flex; justify-content: center; align-items: center;
            cursor: pointer; font-size: 13px;
            border: 2px solid rgba(255,255,255,0.3);
            transition: background 0.15s, transform 0.15s, box-shadow 0.15s;
            margin-top: -15px; margin-left: -15px; /* Center relative to parent */
            backdrop-filter: blur(4px);
            z-index: 9999 !important;
        }
        .context-menu-btn:hover {
            background: rgba(80,80,80,0.95);
            transform: scale(1.15);
            box-shadow: 0 0 8px rgba(255,255,255,0.3);
        }
        
        .btn-go { transform: translate(calc(-0.258 * var(--R)), calc(-0.965 * var(--R))); }
        .btn-go:hover { transform: translate(calc(-0.258 * var(--R)), calc(-0.965 * var(--R))) scale(1.15); background: #333; }
        .btn-rotate { transform: translate(calc(-0.906 * var(--R)), calc(-0.422 * var(--R))); }
        .btn-rotate:hover { transform: translate(calc(-0.906 * var(--R)), calc(-0.422 * var(--R))) scale(1.15); background: #333; }
        .btn-delete { transform: translate(calc(-0.906 * var(--R)), calc(0.422 * var(--R))); }
        .btn-delete:hover { transform: translate(calc(-0.906 * var(--R)), calc(0.422 * var(--R))) scale(1.15); background: rgba(200,50,50,0.9); }
        .btn-edit { transform: translate(calc(-0.258 * var(--R)), calc(0.965 * var(--R))); }
        .btn-edit:hover { transform: translate(calc(-0.258 * var(--R)), calc(0.965 * var(--R))) scale(1.15); background: rgba(50,130,200,0.9); }

        /* Target Box */
        .target-box {
            position: absolute; top: 75px; left: 15px; width: 200px;
            background: rgba(45,45,45,0.95); border-radius: 3px; color: white; font-family: 'Segoe UI', sans-serif;
            pointer-events: auto; display: none; box-shadow: 0 5px 15px rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
        }
        .target-box.active { display: block; }
        .target-box-header {
            padding: 8px 12px; font-weight: 600; font-size: 14px;
            display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #444;
        }
        .target-box-header .close-btn { cursor: pointer; font-size: 18px; line-height: 1; }
        .target-box-body { padding: 12px; }
        .target-box-body select, .target-box-body input, .target-box-body textarea {
            width: 100%; background: #222; color: white; border: 1px solid #555; padding: 6px; border-radius: 2px; font-size: 13px; outline: none; margin-bottom: 10px;
        }
        .target-box-footer { padding: 0 12px 12px; text-align: right; }
        .target-box-footer button {
            background: #3498db; color: white; border: none; padding: 5px 10px; border-radius: 2px; cursor: pointer; font-size: 13px;
        }
        
        .scene-list-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #333;
            font-size: 13px;
            transition: background 0.1s;
        }
        .scene-list-item:hover { background: #444; }
        .scene-list-item.active { background: #3498db; font-weight: bold; }
        .target-box-footer button:hover { background: #2980b9; }

        /* Hide client tooltips and modals in Editor Mode */
        body.editor-mode .info-hotspot-modal { display: none !important; }
    </style>
</head>

<?php
    $scenes = $location->panoramas()->orderByDesc('is_default')->orderBy('sort_order')->get();
    
    $appData = [
        'name' => $location->name,
        'settings' => [
            'mouseViewMode' => 'drag',
            'autorotateEnabled' => false,
            'fullscreenButton' => true,
            'viewControlButtons' => true
        ],
        'scenes' => $scenes->map(function($p) {
            return [
                'id' => (string)$p->id,
                'name' => $p->scene_name,
                'url' => asset('storage/' . $p->image_url),
                'initialViewParameters' => [
                    'yaw' => $p->initial_yaw * pi() / 180,
                    'pitch' => $p->initial_pitch * pi() / 180,
                    'fov' => $p->initial_fov ? ($p->initial_fov * pi() / 180) : 1.5707963267948966
                ],
                'linkHotspots' => $p->hotspots->where('hotspot_type', 'link')->map(function($h) {
                    return [
                        'id' => $h->id,
                        'yaw' => $h->yaw * pi() / 180,
                        'pitch' => $h->pitch * pi() / 180,
                        'rotation' => 0,
                        'target' => (string)$h->target_panorama_id,
                        'scale' => $h->scale ?? 1.0
                    ];
                })->values(),
                'infoHotspots' => $p->hotspots->where('hotspot_type', 'info')->map(function($h) {
                    return [
                        'id' => $h->id,
                        'yaw' => $h->yaw * pi() / 180,
                        'pitch' => $h->pitch * pi() / 180,
                        'title' => $h->title,
                        'text' => $h->content,
                        'scale' => $h->scale ?? 1.0
                    ];
                })->values()
            ];
        })->values()
    ];
?>

<body class="multiple-scenes editor-mode">

<div id="app">
    <!-- Topbar -->
    <div class="topbar">
        <div class="fw-bold d-flex align-items-center gap-3">
            <span><i class="fas fa-edit text-warning"></i> Marzipano Editor</span>
        </div>
        <div class="d-flex gap-2">
            <button class="editor-btn btn-warning" onclick="console.log('Add Info clicked'); createHotspot('info')"><i class="fas fa-info-circle"></i> Add Info</button>
            <button class="editor-btn btn-primary" onclick="console.log('Add Link clicked'); createHotspot('link')"><i class="fas fa-link"></i> Add Link</button>
            <button id="save-changes-btn" class="editor-btn btn-success disabled" onclick="saveAllChanges()" style="opacity: 0.5; cursor: not-allowed;"><i class="fas fa-save"></i> Lưu thay đổi</button>
            <button class="editor-btn btn-purple" onclick="console.log('Save Default View clicked'); saveInitialView()"><i class="fas fa-eye"></i> Save Default View</button>
            <div class="ms-3 border-start ps-3">
                <a href="<?php echo e(route('admin.locations.edit', $location->id)); ?>" class="editor-btn btn-secondary" onclick="return confirmCloseEditor(event)"><i class="fas fa-times"></i> Đóng Editor</a>
            </div>
        </div>
    </div>

    <div class="main-layout">
        <!-- Sidebar -->
        <div class="sidebar">
            <button class="add-pano-btn" onclick="document.getElementById('panoUploadInput').click()">
                <i class="fas fa-plus me-2"></i> Thêm ảnh 360
            </button>
            <input type="file" id="panoUploadInput" class="d-none" multiple accept="image/*">
            
            <div class="section-header">Danh sách ảnh</div>
            <div id="custom-scene-list">
                <?php $__currentLoopData = $scenes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scene): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="scene-item" id="sidebar-scene-<?php echo e($scene->id); ?>" onclick="switchToScene('<?php echo e($scene->id); ?>')">
                    <img src="<?php echo e(Storage::url($scene->image_url)); ?>" alt="">
                    <div class="name" title="<?php echo e($scene->scene_name); ?>"><?php echo e($scene->scene_name); ?></div>
                    <div class="actions">
                        <i class="fa-star <?php echo e($scene->is_default ? 'fas text-warning' : 'far text-light'); ?> me-2" onclick="event.stopPropagation(); setDefaultScene('<?php echo e($scene->id); ?>')" title="<?php echo e($scene->is_default ? 'Cảnh mặc định' : 'Đặt làm cảnh mặc định'); ?>"></i>
                        <i class="fas fa-pencil-alt text-light me-2" onclick="event.stopPropagation(); editSceneName('<?php echo e($scene->id); ?>', '<?php echo e($scene->scene_name); ?>')" title="Đổi tên"></i>
                        <i class="fas fa-trash text-danger" onclick="event.stopPropagation(); deleteScene('<?php echo e($scene->id); ?>')" title="Xóa ảnh"></i>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <!-- Viewer Area -->
        <div class="viewer-area">
            <div id="pano"></div>

            <div id="titleBar">
              <h1 class="sceneName"></h1>
            </div>

            <a href="javascript:void(0)" id="autorotateToggle">
              <img class="icon off" src="<?php echo e(asset('marzipano/img/play.png')); ?>">
              <img class="icon on" src="<?php echo e(asset('marzipano/img/pause.png')); ?>">
            </a>

            <a href="javascript:void(0)" id="fullscreenToggle">
              <img class="icon off" src="<?php echo e(asset('marzipano/img/fullscreen.png')); ?>">
              <img class="icon on" src="<?php echo e(asset('marzipano/img/windowed.png')); ?>">
            </a>

            <!-- Hide Scene List Toggle from Marzipano -->
            <a href="javascript:void(0)" id="sceneListToggle" style="display:none;"></a>
            <div id="sceneList" style="display:none;"><ul class="scenes"></ul></div>

            <a href="javascript:void(0)" id="viewUp" class="viewControlButton viewControlButton-1">
              <img class="icon" src="<?php echo e(asset('marzipano/img/up.png')); ?>">
            </a>
            <a href="javascript:void(0)" id="viewDown" class="viewControlButton viewControlButton-2">
              <img class="icon" src="<?php echo e(asset('marzipano/img/down.png')); ?>">
            </a>
            <a href="javascript:void(0)" id="viewLeft" class="viewControlButton viewControlButton-3">
              <img class="icon" src="<?php echo e(asset('marzipano/img/left.png')); ?>">
            </a>
            <a href="javascript:void(0)" id="viewRight" class="viewControlButton viewControlButton-4">
              <img class="icon" src="<?php echo e(asset('marzipano/img/right.png')); ?>">
            </a>
            <a href="javascript:void(0)" id="viewIn" class="viewControlButton viewControlButton-5">
              <img class="icon" src="<?php echo e(asset('marzipano/img/plus.png')); ?>">
            </a>
            <a href="javascript:void(0)" id="viewOut" class="viewControlButton viewControlButton-6">
              <img class="icon" src="<?php echo e(asset('marzipano/img/minus.png')); ?>">
            </a>
        </div>
    </div>
</div>

<div id="target-view-overlay" style="display:none; position:absolute; top:80px; left:50%; transform:translateX(-50%); background:rgba(0,0,0,0.85); color:white; padding:15px 25px; border-radius:30px; z-index:9999; box-shadow:0 4px 15px rgba(0,0,0,0.3); text-align:center; backdrop-filter:blur(5px); border:1px solid rgba(255,255,255,0.2);">
    <div style="font-size:15px; margin-bottom:10px; font-weight:500;">Hãy xoay camera đến góc nhìn mong muốn cho điểm neo này</div>
    <button class="btn btn-sm btn-success rounded-pill px-4" onclick="saveTargetView()">Lưu Hướng</button>
    <button class="btn btn-sm btn-secondary rounded-pill px-4 ms-2" onclick="cancelTargetView()">Hủy</button>
</div>

    <div class="target-box" id="link-target-box" style="width: 180px;">
        <div class="target-box-header">
            <span>Chọn cảnh liên kết</span>
            <span class="close-btn" onclick="closeTargetBox()">&times;</span>
        </div>
        <div class="target-box-body" style="padding: 0 0 10px 0; max-height: 250px; overflow-y: auto;">
            <div style="padding: 10px 12px 0;">
                <label style="font-size: 11px; color: #aaa; margin-bottom: 5px; display: block;">Kích thước: <span id="linkScaleVal">1.0</span>x</label>
                <input type="range" id="linkScaleInput" min="0.3" max="3" step="0.1" value="1.0" oninput="updateHotspotScale(this.value, 'linkScaleVal')" style="width: 100%;">
            </div>
            <div class="scene-list-item" onclick="autoSaveLinkHotspot('')">-- Chưa liên kết --</div>
            <?php $__currentLoopData = $scenes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scene): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="scene-list-item" data-id="<?php echo e($scene->id); ?>" onclick="autoSaveLinkHotspot('<?php echo e($scene->id); ?>')"><?php echo e($scene->scene_name); ?></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <div class="target-box" id="info-target-box">
        <div class="target-box-header">
            <span>Edit Information</span>
            <span class="close-btn" onclick="closeTargetBox()">&times;</span>
        </div>
        <div class="target-box-body">
            <label style="font-size: 11px; color: #aaa; margin-bottom: 5px; display: block;">Kích thước: <span id="infoScaleVal">1.0</span>x</label>
            <input type="range" id="infoScaleInput" min="0.3" max="3" step="0.1" value="1.0" oninput="updateHotspotScale(this.value, 'infoScaleVal')" style="width: 100%; margin-bottom: 10px;">
            <input type="text" id="infoTitle" placeholder="Tiêu đề">
            <textarea id="infoContent" rows="3" placeholder="Nội dung..."></textarea>
        </div>
        <div class="target-box-footer">
            <button onclick="saveInfoHotspot()">Lưu lại</button>
        </div>
    </div>

<!-- Setup APP_DATA -->
<script>
    window.isEditorMode = true;
    window.APP_DATA = <?php echo json_encode($appData); ?>;
</script>

<script src="<?php echo e(asset('marzipano/vendor/screenfull.min.js')); ?>" ></script>
<script src="<?php echo e(asset('marzipano/vendor/bowser.min.js')); ?>" ></script>
<script src="<?php echo e(asset('marzipano/vendor/marzipano.js')); ?>" ></script>

<!-- The main Marzipano template script -->
<script src="<?php echo e(asset('marzipano/index.js')); ?>?v=<?php echo e(time()); ?>"></script>

<!-- jQuery and Bootstrap -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Editor Logic (AJAX and UI) -->
<script>
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' } });
    
    window.hasUnsavedChanges = false;
    let pendingCreates = [];
    let pendingUpdates = {};
    let pendingDeletes = [];
    
    function setDirty(isDirty) {
        window.hasUnsavedChanges = isDirty;
        let btn = document.getElementById('save-changes-btn');
        if (btn) {
            if (isDirty) {
                btn.classList.remove('disabled');
                btn.style.opacity = '1';
                btn.style.cursor = 'pointer';
                btn.classList.add('pulse-green');
            } else {
                btn.classList.add('disabled');
                btn.style.opacity = '0.5';
                btn.style.cursor = 'not-allowed';
                btn.classList.remove('pulse-green');
            }
        }
    }

    window.addEventListener('beforeunload', function (e) {
        if (window.hasUnsavedChanges) {
            e.preventDefault();
            e.returnValue = 'Bạn có thay đổi chưa lưu. Bạn có chắc chắn muốn rời khỏi trang không?';
            return e.returnValue;
        }
    });

    function confirmCloseEditor(e) {
        if (window.hasUnsavedChanges) {
            if (!confirm("Bạn có các thay đổi chưa được lưu. Có chắc chắn muốn thoát không?")) {
                e.preventDefault();
                return false;
            }
        }
        return true;
    }
    // Highlight active scene in sidebar
    function highlightSidebar(sceneId) {
        $('.scene-item').removeClass('active');
        $('#sidebar-scene-' + sceneId).addClass('active');
    }

    // Initialize sidebar highlight
    if(window.APP_DATA.scenes.length > 0) {
        highlightSidebar(window.APP_DATA.scenes[0].id);
    }

    // Switch scene from Sidebar
    function switchToScene(id) {
        if (!window.mzScenes) return;
        let targetScene = window.mzScenes.find(s => s.data.id === id);
        if(targetScene) {
            window.switchScene(targetScene);
            highlightSidebar(id);
        }
    }
    
    function getCurrentSceneId() {
        return window.currentSceneId || (window.APP_DATA && window.APP_DATA.scenes && window.APP_DATA.scenes.length > 0 ? window.APP_DATA.scenes[0].id : null);
    }

    function saveInitialView() {
        let view = mzViewer.view();
        let yaw = view.yaw() * 180 / Math.PI;
        let pitch = view.pitch() * 180 / Math.PI;
        let fov = view.fov() * 180 / Math.PI;
        
        let sceneId = getCurrentSceneId();
        
        $.ajax({
            url: `/admin/panoramas/${sceneId}/initial-view`,
            type: 'POST',
            data: { yaw: yaw, pitch: pitch, fov: fov },
            success: function() { 
                alert('Đã lưu góc nhìn mặc định (Initial View) thành công!'); 
            }
        });
    }

    function createHotspot(mode) {
        console.log("createHotspot called with mode:", mode);
        if (!window.mzViewer) {
            console.error("window.mzViewer is not defined!");
            alert("Viewer chưa sẵn sàng!");
            return;
        }
        
        let view = window.mzViewer.view();
        let yaw = view.yaw() * 180 / Math.PI;
        let pitch = view.pitch() * 180 / Math.PI;
        let sceneId = getCurrentSceneId();
        console.log("Calculated sceneId:", sceneId);
        
        let tempId = 'temp_' + Date.now() + '_' + Math.floor(Math.random()*1000);
        let newHotspot = {
            id: tempId,
            tempId: tempId,
            hotspot_type: mode,
            type: mode,
            yaw: yaw,
            pitch: pitch,
            title: mode === 'info' ? 'Điểm thông tin mới' : '',
            content: '',
            target_panorama_id: '',
            target: '',
            sceneId: sceneId
        };
        
        pendingCreates.push(newHotspot);
        
        if (window.addHotspotToActiveScene) {
            window.addHotspotToActiveScene(newHotspot);
        }
        
        setDirty(true);
    }
    
    // Upload Progress Overlay
    function showUploadProgress() {
        let overlay = document.getElementById('upload-progress-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'upload-progress-overlay';
            overlay.innerHTML = `
                <div style="background:rgba(0,0,0,0.7); position:fixed; top:0; left:0; width:100%; height:100%; z-index:99999; display:flex; align-items:center; justify-content:center;">
                    <div style="background:#2c3e50; border-radius:4px; padding:25px 30px; min-width:340px; border:1px solid #3d566e;">
                        <div style="color:#ecf0f1; font-size:14px; font-weight:bold; margin-bottom:15px; text-transform:uppercase;">
                            <i class="fas fa-upload" style="margin-right:8px; color:#1abc9c;"></i>Đang tải ảnh 360°
                        </div>
                        <div id="upload-file-name" style="color:#95a5a6; font-size:12px; margin-bottom:10px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:300px;">Đang tải lên...</div>
                        <div style="background:#34495e; border-radius:3px; height:14px; overflow:hidden; margin-bottom:8px;">
                            <div id="upload-progress-bar" style="background:#1abc9c; height:100%; width:0%; border-radius:3px; transition:width 0.2s ease;"></div>
                        </div>
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <div id="upload-progress-text" style="color:#ecf0f1; font-size:14px; font-weight:bold;">0%</div>
                            <div id="upload-file-counter" style="color:#7f8c8d; font-size:11px;"></div>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(overlay);
        }
        overlay.style.display = 'block';
    }

    function updateUploadProgress(percent, fileName, current, total) {
        document.getElementById('upload-progress-bar').style.width = percent + '%';
        document.getElementById('upload-progress-text').textContent = Math.round(percent) + '%';
        if (fileName) {
            document.getElementById('upload-file-name').textContent = fileName;
        }
        if (total > 1) {
            document.getElementById('upload-file-counter').textContent = `Ảnh ${current} / ${total}`;
        }
    }

    function hideUploadProgress() {
        let overlay = document.getElementById('upload-progress-overlay');
        if (overlay) overlay.style.display = 'none';
    }

    function uploadPanoFile(file, locationId, current, total) {
        return new Promise((resolve, reject) => {
            let formData = new FormData();
            formData.append('file', file);

            let xhr = new XMLHttpRequest();
            xhr.open('POST', `/admin/locations/${locationId}/upload-pano`, true);
            xhr.setRequestHeader('X-CSRF-TOKEN', '<?php echo e(csrf_token()); ?>');

            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    let percent = (e.loaded / e.total) * 100;
                    updateUploadProgress(percent, file.name, current, total);
                }
            };

            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    resolve(xhr.responseText);
                } else {
                    reject(new Error('Upload thất bại: ' + xhr.statusText));
                }
            };

            xhr.onerror = function() {
                reject(new Error('Lỗi kết nối khi upload'));
            };

            xhr.send(formData);
        });
    }

    // Upload
    $('#panoUploadInput').change(async function() {
        let files = this.files;
        if(files.length === 0) return;
        let locationId = <?php echo e($location->id); ?>;

        showUploadProgress();

        try {
            for(let i = 0; i < files.length; i++) {
                updateUploadProgress(0, files[i].name, i + 1, files.length);
                await uploadPanoFile(files[i], locationId, i + 1, files.length);
            }
            updateUploadProgress(100, 'Hoàn tất! Đang tải lại trang...', files.length, files.length);
            setTimeout(() => location.reload(), 500);
        } catch(err) {
            hideUploadProgress();
            alert('Lỗi upload: ' + err.message);
        }
    });

    // Delete Scene
    function deleteScene(sceneId) {
        if(!confirm('Xóa ảnh 360 này? Tất cả hotspot liên kết sẽ bị xóa theo.')) return;
        $.ajax({
            url: `/admin/locations/pano/${sceneId}`,
            type: 'DELETE',
            success: function() { 
                location.reload(); 
            }
        });
    }

    // Edit Scene Name
    function editSceneName(sceneId, currentName) {
        let newName = prompt("Nhập tên mới cho ảnh này:", currentName);
        if(newName && newName.trim() !== "") {
            $.ajax({
                url: `/admin/panoramas/${sceneId}/name`, type: 'PUT', data: {name: newName},
                success: function() { location.reload(); }
            });
        }
    }
    
    // Hotspot Drag & Context Menu Logic
    let activeHotspotId = null;
    let activeHotspotType = null;

    window.onHotspotAction = function(hotspotId, type, action) {
        if(window.isDraggingHotspot) return;
        
        activeHotspotId = hotspotId;
        activeHotspotType = type;
        
        if (action === 'delete') {
            deleteActiveHotspot();
        } else if (action === 'edit') {
            openTargetBox();
        } else if (action === 'go') {
            let targetSceneId = null;
            let targetYaw = null;
            let targetPitch = null;
            let isTargetViewDegrees = false;
            let idStr = String(hotspotId);
            
            // Try pendingCreates first
            if (idStr.startsWith('temp_')) {
                let item = pendingCreates.find(h => h.tempId === hotspotId);
                if (item) {
                    targetSceneId = item.target || item.target_panorama_id;
                    if (item.target_yaw !== undefined) {
                        targetYaw = item.target_yaw;
                        isTargetViewDegrees = true;
                    }
                    if (item.target_pitch !== undefined) targetPitch = item.target_pitch;
                }
            } else {
                // Try pendingUpdates next
                if (pendingUpdates[hotspotId] && pendingUpdates[hotspotId].target !== undefined) {
                    targetSceneId = pendingUpdates[hotspotId].target;
                }
                if (pendingUpdates[hotspotId] && pendingUpdates[hotspotId].target_yaw !== undefined) {
                    targetYaw = pendingUpdates[hotspotId].target_yaw;
                    isTargetViewDegrees = true;
                }
                if (pendingUpdates[hotspotId] && pendingUpdates[hotspotId].target_pitch !== undefined) {
                    targetPitch = pendingUpdates[hotspotId].target_pitch;
                }
                
                // Finally APP_DATA (if not in pending)
                let sceneData = window.APP_DATA.scenes.find(s => s.id == getCurrentSceneId());
                if (sceneData && sceneData.linkHotspots) {
                    let hData = sceneData.linkHotspots.find(h => h.id == hotspotId);
                    if (hData) {
                        if (!targetSceneId) targetSceneId = hData.target;
                        if (targetYaw === null && hData.target_yaw !== undefined && hData.target_yaw !== null) {
                            targetYaw = hData.target_yaw;
                            isTargetViewDegrees = false; // APP_DATA is in radians
                        }
                        if (targetPitch === null && hData.target_pitch !== undefined && hData.target_pitch !== null) {
                            targetPitch = hData.target_pitch;
                        }
                    }
                }
            }
            
            if(targetSceneId && window.mzScenes) {
                let targetSceneObj = window.mzScenes.find(s => s.data.id == targetSceneId);
                if (targetSceneObj) {
                    if (targetYaw !== null && targetYaw !== undefined) {
                        let finalYaw = isTargetViewDegrees ? targetYaw * Math.PI / 180 : targetYaw;
                        let finalPitch = isTargetViewDegrees ? targetPitch * Math.PI / 180 : targetPitch;
                        window.switchScene(targetSceneObj, { yaw: finalYaw, pitch: finalPitch });
                    } else {
                        window.switchScene(targetSceneObj);
                    }
                }
            } else {
                alert('Vui lòng chọn cảnh liên kết trước khi đi đến.');
            }
        } else if (action === 'rotate') {
            let targetSceneId = null;
            let idStr = String(hotspotId);
            
            if (idStr.startsWith('temp_')) {
                let item = pendingCreates.find(h => h.tempId === hotspotId);
                if (item) targetSceneId = item.target || item.target_panorama_id;
            } else {
                if (pendingUpdates[hotspotId] && pendingUpdates[hotspotId].target) {
                    targetSceneId = pendingUpdates[hotspotId].target;
                } else {
                    let sceneData = window.APP_DATA.scenes.find(s => s.id == getCurrentSceneId());
                    if (sceneData && sceneData.linkHotspots) {
                        let hData = sceneData.linkHotspots.find(h => h.id == hotspotId);
                        if (hData) targetSceneId = hData.target;
                    }
                }
            }
            
            if(targetSceneId && window.mzScenes) {
                let targetSceneObj = window.mzScenes.find(s => s.data.id == targetSceneId);
                if (targetSceneObj && targetSceneObj.scene) {
                    window.rotatingHotspotId = hotspotId;
                    window.originalSceneId = getCurrentSceneId();
                    
                    targetSceneObj.scene.switchTo();
                    window.currentSceneId = targetSceneId;
                    
                    // Show Overlay
                    document.getElementById('target-view-overlay').style.display = 'block';
                }
            } else {
                alert('Vui lòng chọn cảnh liên kết trước khi đặt hướng nhìn!');
            }
        }
    };
    
    window.saveTargetView = function() {
        if (!window.mzViewer || !window.rotatingHotspotId) return;
        
        let view = window.mzViewer.view();
        let yawDeg = view.yaw() * 180 / Math.PI;
        let pitchDeg = view.pitch() * 180 / Math.PI;
        
        let idStr = String(window.rotatingHotspotId);
        if (idStr.startsWith('temp_')) {
            let item = pendingCreates.find(h => h.tempId === window.rotatingHotspotId);
            if (item) {
                item.target_yaw = yawDeg;
                item.target_pitch = pitchDeg;
            }
        } else {
            if (!pendingUpdates[window.rotatingHotspotId]) {
                pendingUpdates[window.rotatingHotspotId] = { id: window.rotatingHotspotId };
            }
            pendingUpdates[window.rotatingHotspotId].target_yaw = yawDeg;
            pendingUpdates[window.rotatingHotspotId].target_pitch = pitchDeg;
        }
        
        setDirty(true);
        cancelTargetView(); // hide overlay and switch back
    };
    
    window.cancelTargetView = function() {
        document.getElementById('target-view-overlay').style.display = 'none';
        
        if (window.originalSceneId && window.mzScenes) {
            let originalObj = window.mzScenes.find(s => s.data.id == window.originalSceneId);
            if (originalObj && originalObj.scene) {
                originalObj.scene.switchTo();
                window.currentSceneId = window.originalSceneId;
            }
        }
        
        window.rotatingHotspotId = null;
        window.originalSceneId = null;
    };

    // Click outside only closes target boxes
    document.addEventListener('click', function(e) {
        if(!e.target.closest('.hotspot')) {
            closeTargetBox();
            document.querySelectorAll('.hotspot').forEach(el => {
                el.classList.remove('active-menu');
                el.style.zIndex = '';
            });
        }
    });

    function openTargetBox() {
        if(!activeHotspotId) return;
        let sceneId = getCurrentSceneId();
        
        if (activeHotspotType === 'info') {
            let data = null;
            let sceneData = window.APP_DATA.scenes.find(s => s.id == sceneId);
            if (sceneData && sceneData.infoHotspots) {
                data = sceneData.infoHotspots.find(h => h.id == activeHotspotId);
            }
            if (!data) {
                data = pendingCreates.find(h => h.tempId === activeHotspotId);
            }
            
            $('#infoTitle').val(data ? (data.title || '') : '');
            $('#infoContent').val(data ? (data.text || data.content || '') : '');
            
            let currentScale = data ? (data.scale || 1.0) : 1.0;
            $('#infoScaleInput').val(currentScale);
            $('#infoScaleVal').text(parseFloat(currentScale).toFixed(1));

            let box = document.getElementById('info-target-box');
            let hotspotElement = document.querySelector(`.hotspot[data-id="${activeHotspotId}"]`);
            if (hotspotElement) hotspotElement.appendChild(box);
            box.classList.add('active');
            document.getElementById('link-target-box').classList.remove('active');
        } else if (activeHotspotType === 'link') {
            let data = null;
            let sceneData = window.APP_DATA.scenes.find(s => s.id == sceneId);
            if (sceneData && sceneData.linkHotspots) {
                data = sceneData.linkHotspots.find(h => h.id == activeHotspotId);
            }
            if (!data) {
                data = pendingCreates.find(h => h.tempId === activeHotspotId);
            }
            
            let currentTarget = data ? (data.target || data.target_panorama_id || '') : '';
            
            let currentScale = data ? (data.scale || 1.0) : 1.0;
            $('#linkScaleInput').val(currentScale);
            $('#linkScaleVal').text(parseFloat(currentScale).toFixed(1));

            document.querySelectorAll('#link-target-box .scene-list-item').forEach(el => {
                if(el.getAttribute('data-id') == currentTarget || (!currentTarget && !el.hasAttribute('data-id'))) {
                    el.classList.add('active');
                } else {
                    el.classList.remove('active');
                }
            });
            
            let box = document.getElementById('link-target-box');
            let hotspotElement = document.querySelector(`.hotspot[data-id="${activeHotspotId}"]`);
            if (hotspotElement) hotspotElement.appendChild(box);
            box.classList.add('active');
            document.getElementById('info-target-box').classList.remove('active');
        }
    }
    
    function closeTargetBox() {
        document.getElementById('link-target-box').classList.remove('active');
        document.getElementById('info-target-box').classList.remove('active');
    }

    window.updateHotspotScale = function(val, textId) {
        document.getElementById(textId).innerText = parseFloat(val).toFixed(1);
        if (!activeHotspotId) return;
        
        let hsElement = document.querySelector(`.hotspot[data-id="${activeHotspotId}"]`);
        if (hsElement) {
            hsElement.style.setProperty('--base-scale', val);
        }
        
        // Save to data
        let sceneId = getCurrentSceneId();
        let idStr = String(activeHotspotId);
        if (idStr.startsWith('temp_')) {
            let item = pendingCreates.find(h => h.tempId === activeHotspotId);
            if (item) item.scale = val;
        } else {
            if (!pendingUpdates[activeHotspotId]) {
                pendingUpdates[activeHotspotId] = { id: activeHotspotId };
            }
            pendingUpdates[activeHotspotId].scale = val;
            
            // Also update local APP_DATA
            let sceneData = window.APP_DATA.scenes.find(s => s.id == sceneId);
            if (sceneData) {
                let hData = null;
                if (activeHotspotType === 'info' && sceneData.infoHotspots) {
                    hData = sceneData.infoHotspots.find(h => h.id == activeHotspotId);
                } else if (activeHotspotType === 'link' && sceneData.linkHotspots) {
                    hData = sceneData.linkHotspots.find(h => h.id == activeHotspotId);
                }
                if (hData) hData.scale = val;
            }
        }
        setDirty(true);
    };

    function saveInfoHotspot() {
        let sceneId = getCurrentSceneId();
        let titleVal = $('#infoTitle').val();
        let contentVal = $('#infoContent').val();
        let idStr = String(activeHotspotId);
        
        if (idStr.startsWith('temp_')) {
            let item = pendingCreates.find(h => h.tempId === activeHotspotId);
            if (item) {
                item.title = titleVal;
                item.content = contentVal;
                item.scale = $('#infoScaleInput').val();
            }
        } else {
            if (!pendingUpdates[activeHotspotId]) {
                pendingUpdates[activeHotspotId] = { id: activeHotspotId };
            }
            pendingUpdates[activeHotspotId].title = titleVal;
            pendingUpdates[activeHotspotId].content = contentVal;
            pendingUpdates[activeHotspotId].scale = $('#infoScaleInput').val();
        }
        
        // Update local APP_DATA
        let sceneData = window.APP_DATA.scenes.find(s => s.id == sceneId);
        if (sceneData && sceneData.infoHotspots) {
            let hData = sceneData.infoHotspots.find(h => h.id == activeHotspotId);
            if (hData) {
                hData.title = titleVal;
                hData.text = contentVal;
                hData.scale = $('#infoScaleInput').val();
            }
        }
        
        // Update DOM directly
        let dragTarget = document.querySelector(`.hotspot[data-id="${activeHotspotId}"]`);
        if (dragTarget) {
            let titleEl = dragTarget.querySelector('.info-hotspot-title');
            if (titleEl) titleEl.innerHTML = titleVal;
            let textEl = dragTarget.querySelector('.info-hotspot-text');
            if (textEl) textEl.innerHTML = contentVal;
        }
        
        closeTargetBox();
        document.querySelectorAll('.hotspot').forEach(el => el.classList.remove('editing'));
        setDirty(true);
    }

    window.autoSaveLinkHotspot = function(target) {
        let sceneId = getCurrentSceneId();
        let idStr = String(activeHotspotId);
        
        if (idStr.startsWith('temp_')) {
            let item = pendingCreates.find(h => h.tempId === activeHotspotId);
            if (item) {
                item.target = target;
                item.target_panorama_id = target;
                item.scale = $('#linkScaleInput').val();
            }
        } else {
            if (!pendingUpdates[activeHotspotId]) {
                pendingUpdates[activeHotspotId] = { id: activeHotspotId };
            }
            pendingUpdates[activeHotspotId].target = target;
            pendingUpdates[activeHotspotId].scale = $('#linkScaleInput').val();
        }
        
        // Update local APP_DATA
        let sceneData = window.APP_DATA.scenes.find(s => s.id == sceneId);
        if (sceneData && sceneData.linkHotspots) {
            let hData = sceneData.linkHotspots.find(h => h.id == activeHotspotId);
            if (hData) {
                hData.target = target;
                hData.scale = $('#linkScaleInput').val();
            }
        }
        
        // Update DOM tooltip with target name
        let dragTarget = document.querySelector(`.hotspot[data-id="${activeHotspotId}"]`);
        if (dragTarget) {
            let tooltipEl = dragTarget.querySelector('.hotspot-tooltip');
            if (tooltipEl) {
                let targetSceneData = window.APP_DATA.scenes.find(s => s.id == target);
                tooltipEl.innerHTML = targetSceneData ? targetSceneData.name : 'Chưa liên kết';
            }
        }
        
        closeTargetBox();
        document.querySelectorAll('.hotspot').forEach(el => el.classList.remove('editing'));
        setDirty(true);
    };

    function deleteActiveHotspot() {
        if(!confirm('Xóa điểm neo này?')) return;
        let sceneId = getCurrentSceneId();
        let idStr = String(activeHotspotId);
        
        if (idStr.startsWith('temp_')) {
            pendingCreates = pendingCreates.filter(h => h.tempId !== activeHotspotId);
        } else {
            if (!pendingDeletes.includes(activeHotspotId)) {
                pendingDeletes.push(activeHotspotId);
            }
            delete pendingUpdates[activeHotspotId];
        }
        
        // Dynamically remove from Marzipano container
        if (window.mzScenes) {
            let currentSceneObj = window.mzScenes.find(s => s.data.id == getCurrentSceneId());
            if (currentSceneObj && currentSceneObj.scene) {
                let container = currentSceneObj.scene.hotspotContainer();
                if (container && typeof container.listHotspots === 'function') {
                    let dragTarget = document.querySelector(`.hotspot[data-id="${activeHotspotId}"]`);
                    if (dragTarget) {
                        let mzHotspot = container.listHotspots().find(h => h.domElement() === dragTarget);
                        if (mzHotspot) {
                            container.destroyHotspot(mzHotspot);
                        } else {
                            dragTarget.remove();
                        }
                    }
                }
            }
        }
        
        // Dynamically remove from APP_DATA
        let sceneData = window.APP_DATA.scenes.find(s => s.id == sceneId);
        if (sceneData) {
            if (sceneData.infoHotspots) {
                sceneData.infoHotspots = sceneData.infoHotspots.filter(h => h.id != activeHotspotId);
            }
            if (sceneData.linkHotspots) {
                sceneData.linkHotspots = sceneData.linkHotspots.filter(h => h.id != activeHotspotId);
            }
        }
        
        closeTargetBox();
        activeHotspotId = null;
        setDirty(true);
    }

    // Hotspot Drag Logic
    window.isDraggingHotspot = false;
    setTimeout(() => {
        let dragTarget = null;
        let isMouseDown = false;
        let startX, startY;
        
        // Disable mzViewer controls when dragging a hotspot
        const enableControls = (enable) => {
            if (!window.mzViewer) return;
            let controls = window.mzViewer.controls();
            if(enable) {
                controls.enable();
            } else {
                controls.disable();
            }
        };

        // Use the #pano element with CAPTURING phase so we intercept events
        // before Marzipano's own drag handler processes them.
        let panoEl = document.querySelector('#pano');

        ['mousedown', 'mousemove', 'touchstart', 'touchmove', 'pointerdown', 'pointermove'].forEach(evt => {
            panoEl.addEventListener(evt, function(e) {
                if (e.target.closest('.target-box') || e.target.closest('.hotspot-context-menu')) {
                    e.stopPropagation();
                }
            }, true);
        });

        panoEl.addEventListener('mousedown', function(e) {
            let hs = e.target.closest('.hotspot');
            if (hs) {
                if (e.target.closest('.target-box') || e.target.closest('.hotspot-context-menu')) {
                    return; 
                }

                isMouseDown = true;
                window.isDraggingHotspot = false; // Reset
                dragTarget = hs;
                startX = e.clientX;
                startY = e.clientY;
                e.preventDefault();  // Prevent browser image/text drag
                e.stopPropagation(); // Prevent Marzipano from starting its own drag
                enableControls(false); // Stop rotating view
            }
        }, true); // <-- capturing phase

        document.addEventListener('mousemove', function(e) {
            if (isMouseDown && dragTarget) {
                let dx = Math.abs(e.clientX - startX);
                let dy = Math.abs(e.clientY - startY);
                if (dx > 5 || dy > 5) {
                    window.isDraggingHotspot = true; // It's a drag!
                }
                
                if (window.isDraggingHotspot) {
                    e.preventDefault(); // Prevent text selection during drag
                    if (!window.mzViewer) return;
                    // Update visually
                    let view = window.mzViewer.view();
                    // Marzipano screenToCoordinates needs relative coordinates to the container
                    let panoRect = document.querySelector('#pano').getBoundingClientRect();
                    let x = e.clientX - panoRect.left;
                    let y = e.clientY - panoRect.top;
                    
                    let coords = view.screenToCoordinates({ x: x, y: y });
                    
                    if (coords) {
                        let hotspotId = dragTarget.getAttribute('data-id');
                        if (window.mzScenes) {
                            let currentSceneObj = window.mzScenes.find(s => s.data.id == getCurrentSceneId());
                            if (currentSceneObj && currentSceneObj.scene) {
                                let container = currentSceneObj.scene.hotspotContainer();
                                if (container && typeof container.listHotspots === 'function') {
                                    let mzHotspot = container.listHotspots().find(h => h.domElement() === dragTarget);
                                    if (mzHotspot) {
                                        mzHotspot.setPosition({ yaw: coords.yaw, pitch: coords.pitch });
                                    }
                                }
                            }
                        }
                        
                        dragTarget.dataset.newYaw = coords.yaw;
                        dragTarget.dataset.newPitch = coords.pitch;
                    }
                }
            }
        });

        document.addEventListener('mouseup', function(e) {
            if (isMouseDown && dragTarget) {
                isMouseDown = false;
                enableControls(true);
                
                if (window.isDraggingHotspot) {
                    if (!window.mzViewer) return;
                    // Save new position
                    let view = window.mzViewer.view();
                    let panoRect = document.querySelector('#pano').getBoundingClientRect();
                    let x = e.clientX - panoRect.left;
                    let y = e.clientY - panoRect.top;
                    let coords = view.screenToCoordinates({ x: x, y: y });
                    
                    if(coords) {
                        let hotspotId = dragTarget.getAttribute('data-id');
                        let sceneId = getCurrentSceneId();
                        // Convert radians back to degrees for DB
                        let yawDeg = coords.yaw * 180 / Math.PI;
                        let pitchDeg = coords.pitch * 180 / Math.PI;
                        let idStr = String(hotspotId);
                        
                        if (idStr.startsWith('temp_')) {
                            let item = pendingCreates.find(h => h.tempId === hotspotId);
                            if (item) {
                                item.yaw = yawDeg;
                                item.pitch = pitchDeg;
                            }
                        } else {
                            if (!pendingUpdates[hotspotId]) {
                                pendingUpdates[hotspotId] = { id: hotspotId };
                            }
                            pendingUpdates[hotspotId].yaw = yawDeg;
                            pendingUpdates[hotspotId].pitch = pitchDeg;
                        }
                        
                        // Update local APP_DATA
                        if (window.APP_DATA && window.APP_DATA.scenes) {
                            let sceneData = window.APP_DATA.scenes.find(s => s.id == sceneId);
                            if (sceneData) {
                                let yawRad = coords.yaw;
                                let pitchRad = coords.pitch;
                                let infoH = sceneData.infoHotspots ? sceneData.infoHotspots.find(h => h.id == hotspotId) : null;
                                if (infoH) {
                                    infoH.yaw = yawRad;
                                    infoH.pitch = pitchRad;
                                }
                                let linkH = sceneData.linkHotspots ? sceneData.linkHotspots.find(h => h.id == hotspotId) : null;
                                if (linkH) {
                                    linkH.yaw = yawRad;
                                    linkH.pitch = pitchRad;
                                }
                            }
                        }
                        
                        setDirty(true);
                    }
                }
                
                // Allow a small delay before resetting isDragging to block the click event
                setTimeout(() => {
                    window.isDraggingHotspot = false;
                    dragTarget = null;
                }, 50);
            }
        });
    }, 1000);

    function saveAllChanges() {
        if (!window.hasUnsavedChanges) return;
        
        let createsPayload = pendingCreates.map(h => ({
            sceneId: h.sceneId,
            type: h.hotspot_type || h.type,
            yaw: h.yaw,
            pitch: h.pitch,
            title: h.title,
            content: h.content,
            target: h.target || h.target_panorama_id || null,
            target_yaw: h.target_yaw !== undefined ? h.target_yaw : null,
            target_pitch: h.target_pitch !== undefined ? h.target_pitch : null,
            scale: h.scale || 1.0
        }));
        
        let updatesPayload = Object.values(pendingUpdates).map(h => {
            let payload = { id: h.id };
            if (h.yaw !== undefined) payload.yaw = h.yaw;
            if (h.pitch !== undefined) payload.pitch = h.pitch;
            if (h.title !== undefined) payload.title = h.title;
            if (h.content !== undefined) payload.content = h.content;
            if (h.target !== undefined) payload.target = h.target;
            if (h.target_panorama_id !== undefined) payload.target = h.target_panorama_id;
            if (h.target_yaw !== undefined) payload.target_yaw = h.target_yaw;
            if (h.target_pitch !== undefined) payload.target_pitch = h.target_pitch;
            if (h.scale !== undefined) payload.scale = h.scale;
            return payload;
        });
        
        // Show loading state
        let btn = document.getElementById('save-changes-btn');
        let oldHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';
        btn.style.pointerEvents = 'none';
        
        $.ajax({
            url: '/admin/hotspots/bulk',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                creates: createsPayload,
                updates: updatesPayload,
                deletes: pendingDeletes
            }),
            success: function(response) {
                if (response.success) {
                    alert('Lưu tất cả thay đổi thành công!');
                    setDirty(false);
                    // Clear the pending logs
                    pendingCreates = [];
                    pendingUpdates = {};
                    pendingDeletes = [];
                    // Reload to sync completely clean DB states
                    location.reload();
                } else {
                    alert('Lỗi khi lưu thay đổi: ' + (response.message || 'Không rõ lỗi.'));
                }
            },
            error: function(xhr) {
                alert('Lỗi kết nối khi lưu thay đổi: ' + xhr.responseText);
            },
            complete: function() {
                btn.innerHTML = oldHtml;
                btn.style.pointerEvents = 'auto';
            }
        });
    }

    function setDefaultScene(sceneId) {
        if (!confirm('Đặt cảnh này làm màn hình khởi đầu khi xem 360?')) return;
        
        $.ajax({
            url: '/admin/panoramas/' + sceneId + '/set-default',
            type: 'POST',
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Có lỗi xảy ra.');
                }
            },
            error: function(xhr) {
                alert('Lỗi kết nối: ' + xhr.responseText);
            }
        });
    }
</script>

</body>
</html>
<?php /**PATH D:\laragon\www\Du_An_TN\resources\views/admin/locations/editor-360.blade.php ENDPATH**/ ?>