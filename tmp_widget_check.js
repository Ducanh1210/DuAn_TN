
document.addEventListener('DOMContentLoaded', function() {

    const TRIP_TYPES = [
        { value: 'spiritual',      icon: '☸', label: 'Tâm linh',      desc: 'Chùa, đền, phủ' },
        { value: 'food_tour',      icon: '◎', label: 'Food Tour',     desc: 'Ẩm thực địa phương' },
        { value: 'check_in',       icon: '◉', label: 'Check-in',      desc: 'Địa điểm đẹp' },
        { value: 'family',         icon: '⌂', label: 'Gia đình',      desc: 'Cả nhà cùng đi' },
        { value: 'couple',         icon: '♡', label: 'Couple',        desc: 'Hẹn hò lãng mạn' },
        { value: 'resort',         icon: '△', label: 'Nghỉ dưỡng',    desc: 'Thư giãn' },
        { value: 'team_building',  icon: '⬡', label: 'Team Building', desc: 'Nhóm & công ty' },
        { value: 'backpacking',    icon: '↗', label: 'Phượt',         desc: 'Khám phá tự do' },
    ];

    let tripType = '';
    let tripTypeLabel = '';
    let currentStep = 0;
    let aiAnswers = [];
    let currentAiQuestion = null;
    let currentSelection = null;
    let stepHistory = [];
    let isLoading = false;
    let aiDone = false;
    let currentItinerary = null;
    let lastAnswersPayload = [];
    let tpMiniMap = null;
    let tpRouteMarkers = [];
    const IS_AUTHENTICATED = null;
    const CURRENT_USER_ID = null;
    const LOGIN_URL = null;
    const PROFILE_URL = null;

    // Bản nháp chỉ giữ trong tab hiện tại + theo user — không dùng chung localStorage mãi
    const TP_DRAFT_KEY = 'nb_trip_draft_' + (CURRENT_USER_ID ? ('u' + CURRENT_USER_ID) : 'guest');
    const TP_LEGACY_KEYS = ['nb_saved_itinerary', 'nb_trip_draft_guest'];
    const TP_BOUNDARY_URL = null;

    function clearTripDraftStorage() {
        try {
            sessionStorage.removeItem(TP_DRAFT_KEY);
            TP_LEGACY_KEYS.forEach(function (k) { localStorage.removeItem(k); });
            // Xóa draft của user khác còn sót trong sessionStorage
            Object.keys(sessionStorage).forEach(function (k) {
                if (k.indexOf('nb_trip_draft_') === 0 && k !== TP_DRAFT_KEY) {
                    sessionStorage.removeItem(k);
                }
            });
        } catch (e) {}
    }

    function saveTripDraft(data) {
        try {
            sessionStorage.setItem(TP_DRAFT_KEY, JSON.stringify({
                userId: CURRENT_USER_ID || null,
                savedAt: Date.now(),
                itinerary: data,
            }));
            // Gỡ bản cũ trên localStorage để hết “dính” khi đổi acc
            TP_LEGACY_KEYS.forEach(function (k) { localStorage.removeItem(k); });
        } catch (e) {
            console.warn('Could not save trip draft:', e);
        }
    }

    function loadTripDraft() {
        try {
            // Dọn key cũ toàn cục
            TP_LEGACY_KEYS.forEach(function (k) { localStorage.removeItem(k); });

            const raw = sessionStorage.getItem(TP_DRAFT_KEY);
            if (!raw) return null;
            const parsed = JSON.parse(raw);
            const ownerId = parsed && Object.prototype.hasOwnProperty.call(parsed, 'userId')
                ? parsed.userId
                : null;
            const currentId = CURRENT_USER_ID || null;
            if (ownerId !== currentId) {
                clearTripDraftStorage();
                return null;
            }
            // Hết hạn sau 2 giờ
            if (parsed.savedAt && (Date.now() - parsed.savedAt > 2 * 60 * 60 * 1000)) {
                clearTripDraftStorage();
                return null;
            }
            return parsed.itinerary || null;
        } catch (e) {
            clearTripDraftStorage();
            return null;
        }
    }

    // Đăng xuất → xóa draft ngay
    document.querySelectorAll('form#logout-form, form[action*="logout"]').forEach(function (form) {
        form.addEventListener('submit', function () {
            clearTripDraftStorage();
            try {
                Object.keys(localStorage).forEach(function (k) {
                    if (k === 'biz_wizard_state' || k.indexOf('biz_wizard_state_') === 0) {
                        localStorage.removeItem(k);
                    }
                });
            } catch (e) {}
            try { indexedDB.deleteDatabase('biz_wizard_db'); } catch (e) {}
        });
    });

    // Dọn localStorage cũ ngay khi load trang (tránh lịch trình “dính” giữa các tài khoản)
    try {
        TP_LEGACY_KEYS.forEach(function (k) { localStorage.removeItem(k); });
        Object.keys(localStorage).forEach(function (k) {
            if (k.indexOf('nb_saved_itinerary') === 0 || k.indexOf('nb_trip_draft_') === 0) {
                localStorage.removeItem(k);
            }
        });
    } catch (e) {}

    const overlay = document.getElementById('trip-planner-overlay');
    const backdrop = document.getElementById('tp-backdrop');
    const closeBtn = document.getElementById('tp-close-btn');
    const wizardBody = document.getElementById('tp-wizard-body');
    const progressBar = document.getElementById('tp-progress');
    const footer = document.getElementById('tp-footer');
    const backBtn = document.getElementById('tp-btn-back');
    const multiHint = document.getElementById('tp-multi-hint');
    const nextBtn = document.getElementById('tp-btn-next');
    const profileBody = document.getElementById('tp-profile-body');
    const generateBtn = document.getElementById('tp-btn-generate');
    const loadingPanel = document.getElementById('tp-loading-panel');
    const loadingMsg = document.getElementById('tp-loading-msg');
    const loadingBarFill = document.getElementById('tp-loading-bar-fill');
    const resultPanel = document.getElementById('tp-result-panel');
    const resultHero = document.getElementById('tp-result-hero');
    const resultKicker = document.getElementById('tp-result-kicker');
    const resultTitle = document.getElementById('tp-result-title');
    const resultSummary = document.getElementById('tp-result-summary');
    const resultStats = document.getElementById('tp-result-stats');
    const resultBody = document.getElementById('tp-result-body');
    const routePane = document.getElementById('tp-route-pane');
    const showRouteBtn = document.getElementById('tp-btn-show-route');
    const restartBtn = document.getElementById('tp-btn-restart');
    const closeResultBtn = document.getElementById('tp-btn-close-result');
    const saveBtn = document.getElementById('tp-btn-save');
    const tpContainer = document.querySelector('#trip-planner-overlay .tp-container');

    function setPlannerMode(mode) {
        if (!tpContainer) return;
        tpContainer.classList.remove('tp-mode-result', 'tp-mode-loading', 'tp-mode-wizard');
        tpContainer.classList.add(mode === 'result' ? 'tp-mode-result' : (mode === 'loading' ? 'tp-mode-loading' : 'tp-mode-wizard'));
    }

    function showPlannerOverlay() {
        clearTripRouteOnMainMap();
        setMainMapPoisVisible(true);
        const el = document.getElementById('trip-planner-overlay');
        const container = el ? el.querySelector('.tp-container') : null;
        const btn = document.getElementById('randomFlyBtn');

        if (!el) return;

            el.style.display = 'flex';
            el.classList.add('active');
        el.classList.remove('minimizing');

        if (container && btn) {
            const bRect = btn.getBoundingClientRect();
            el.classList.add('visible');
            void container.offsetWidth;
            const cRect = container.getBoundingClientRect();
            const dx = (bRect.left + bRect.width / 2) - (cRect.left + cRect.width / 2);
            const dy = (bRect.top + bRect.height / 2) - (cRect.top + cRect.height / 2);
            const scale = Math.max(0.04, Math.min(bRect.width / cRect.width, bRect.height / cRect.height) * 0.95);

            container.style.transition = 'none';
            container.style.transform = `translate(${dx}px, ${dy}px) scale(${scale})`;
            container.style.opacity = '0.35';
            container.style.borderRadius = '8px';
            void container.offsetWidth;

            requestAnimationFrame(() => {
                container.style.transition = '';
                container.style.transform = 'translate(0px, 0px) scale(1)';
                container.style.opacity = '1';
                container.style.borderRadius = '';
                setTimeout(() => resetContainerStyles(container), 420);
            });
        } else {
            setTimeout(() => el.classList.add('visible'), 20);
        }
        }

    window.openTripPlanner = function(forceNew = false) {
        console.log('openTripPlanner triggered');
        showPlannerOverlay();

        if (!forceNew) {
            const saved = loadTripDraft();
            if (saved && (saved.days || saved.title)) {
                        wizardBody.style.display = 'none';
                        footer.style.display = 'none';
                        loadingPanel.classList.remove('active');
                renderItinerary(saved, false);
                        return;
                    }
        } else {
            clearTripDraftStorage();
        }

        resetState();
        renderTripTypeStep();
    };

    window.openTripPlannerItinerary = function(data) {
        if (!data || !(data.days || data.title)) return;
        showPlannerOverlay();
        wizardBody.style.display = 'none';
        if (footer) footer.style.display = 'none';
        loadingPanel.classList.remove('active');
        renderItinerary(data, false);
    };

    function resetContainerStyles(container) {
        if (!container) return;
        container.style.transform = '';
        container.style.opacity = '';
        container.style.borderRadius = '';
        container.style.transition = '';
    }

    function pulseDockButton() {
        const btn = document.getElementById('randomFlyBtn');
        if (!btn) return;
        btn.classList.remove('tp-dock-pulse');
        void btn.offsetWidth;
        btn.classList.add('tp-dock-pulse');
        setTimeout(() => btn.classList.remove('tp-dock-pulse'), 700);
    }

    /**
     * Thu popup bay vào nút "Lịch trình cho bạn".
     * @param {() => void} [onDone]
     */
    function minimizePlannerToDock(onDone) {
        const el = document.getElementById('trip-planner-overlay');
        const container = el ? el.querySelector('.tp-container') : null;
        const btn = document.getElementById('randomFlyBtn');

        if (!el || !el.classList.contains('active')) {
            if (typeof onDone === 'function') onDone();
            return;
        }

        if (!container || !btn) {
            closePlanner(onDone);
            return;
        }

        const cRect = container.getBoundingClientRect();
        const bRect = btn.getBoundingClientRect();
        const dx = (bRect.left + bRect.width / 2) - (cRect.left + cRect.width / 2);
        const dy = (bRect.top + bRect.height / 2) - (cRect.top + cRect.height / 2);
        const scale = Math.max(0.04, Math.min(bRect.width / cRect.width, bRect.height / cRect.height) * 0.95);

        // Giữ frame hiện tại rồi mới animate — tránh snap về scale(0.92)
        container.style.transition = 'none';
        container.style.transform = 'translate(0px, 0px) scale(1)';
        container.style.opacity = '1';
        void container.offsetWidth;

        el.classList.add('minimizing');
            el.classList.remove('visible');

        requestAnimationFrame(() => {
            container.style.transition = '';
            container.style.transform = `translate(${dx}px, ${dy}px) scale(${scale})`;
            container.style.opacity = '0';
            container.style.borderRadius = '8px';
        });

        const finish = () => {
            el.classList.remove('active', 'minimizing');
            el.style.display = 'none';
            resetContainerStyles(container);
            pulseDockButton();
            if (typeof onDone === 'function') onDone();
        };

        setTimeout(finish, 520);
    }

    function closePlanner(onDone) {
        const el = document.getElementById('trip-planner-overlay');
        if (el) {
            el.classList.remove('visible', 'minimizing');
            setTimeout(() => {
                el.classList.remove('active');
                el.style.display = 'none';
                resetContainerStyles(el.querySelector('.tp-container'));
                if (typeof onDone === 'function') onDone();
            }, 350);
        } else if (typeof onDone === 'function') {
            onDone();
        }
    }

    window.closeTripPlanner = closePlanner;

    /** Zoom map + thu panel bay vào nút "Lịch trình cho bạn". */
    window.zoomFromTripPlanner = function(identifier) {
        minimizePlannerToDock(() => {
            if (typeof window.zoomToLocationFromChat === 'function') {
                window.zoomToLocationFromChat(identifier);
            }
        });
    };

    closeBtn.addEventListener('click', () => minimizePlannerToDock());
    backdrop.addEventListener('click', () => minimizePlannerToDock());
    closeResultBtn.addEventListener('click', () => minimizePlannerToDock());

    restartBtn.addEventListener('click', () => {
        clearTripDraftStorage();
        resultPanel.classList.remove('active');
        loadingPanel.classList.remove('active');
        wizardBody.style.display = '';
        footer.style.display = '';
        setPlannerMode('wizard');
        resetState();
        renderTripTypeStep();
    });

    function resetState() {
        tripType = '';
        tripTypeLabel = '';
        currentStep = 0;
        aiAnswers = [];
        currentAiQuestion = null;
        currentSelection = null;
        stepHistory = [];
        isLoading = false;
        aiDone = false;
        currentItinerary = null;
        lastAnswersPayload = [];
        destroyTripMiniMap();
        clearTripRouteOnMainMap();
        setMainMapPoisVisible(true);
        activeSteps = [];
        wizardBody.style.display = '';
        footer.style.display = '';
        loadingPanel.classList.remove('active');
        resultPanel.classList.remove('active');
        setPlannerMode('wizard');
        generateBtn.disabled = true;
        const heading = document.querySelector('#trip-planner-overlay .tp-header-title');
        const subtitle = document.querySelector('#trip-planner-overlay .tp-header-subtitle');
        if (heading) heading.textContent = 'Lên lịch trình';
        if (subtitle) subtitle.textContent = 'Chọn vài bước — AI sinh lịch trình';
        if (resultHero) resultHero.style.backgroundImage = '';
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Lưu hành trình';
            saveBtn.classList.remove('saved');
        }
        nextBtn.classList.remove('visible');
        multiHint.style.display = 'none';
        updateProfile();
    }

    function getWizardSteps(type) {
        const label = tripTypeLabel || 'chuyến đi';
        // Loại chuyến đã nói lên sở thích chính, nên câu sở thích bỏ bớt lựa chọn trùng.
        const impliedInterest = {
            spiritual: 'tam_linh',
            food_tour: 'am_thuc',
            check_in: 'check_in',
            resort: 'nghi_duong'
        };

        const sharedDuration = {
            key: 'duration_hotel',
            greeting: 'Thời lượng quyết định khá nhiều đến lịch trình.',
            question: 'Bạn dự định đi trong bao lâu và có cần khách sạn không?',
            type: 'single',
            options: [
                { value: '1_day', label: 'Đi 1 ngày (Không ở lại)' },
                { value: '2d1n_hotel', label: '2 ngày 1 đêm (Cần khách sạn)' },
                { value: '3d2n_hotel', label: '3 ngày 2 đêm (Cần khách sạn)' },
                { value: 'other', label: 'Khác...' }
            ]
        };

        // Mỗi loại chuyến chỉ tinh chỉnh ba câu thực sự ảnh hưởng tới lịch trình.
        const byType = {
            spiritual: {
                duration_hotel: {
                    ...sharedDuration,
                    greeting: 'Nhiều người kết hợp hành hương 1–2 ngày.',
                },
                pace: {
                    key: 'pace',
                    greeting: 'Với chuyến tâm linh, nhịp độ thoải mái thường hợp hơn.',
                    question: 'Bạn muốn lịch trình tâm linh theo kiểu nào?',
                    type: 'single',
                    options: [
                        { value: 'cham_rai', label: 'Chậm rãi, tĩnh tâm (ít điểm)' },
                        { value: 'can_bang', label: 'Vừa lễ vừa tham quan' },
                        { value: 'dap_dong', label: 'Ghé nhiều chùa / phủ trong ngày' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
                interests: {
                    key: 'interests',
                    greeting: 'Chọn đúng điểm nhấn tâm linh bạn quan tâm.',
                    question: 'Bạn muốn ưu tiên điều gì? (có thể chọn nhiều)',
                    type: 'multi',
                    options: [
                        { value: 'tam_linh', label: 'Chùa / Đền / Phủ nổi tiếng' },
                        { value: 'van_hoa', label: 'Di tích lịch sử kèm theo' },
                        { value: 'am_thuc', label: 'Quán ăn thanh đạm gần điểm lễ' },
                        { value: 'thien_nhien', label: 'Cảnh quan thiên nhiên quanh điểm lễ' },
                        { value: 'check_in', label: 'Chụp ảnh kỷ niệm' },
                        { value: 'other', label: 'Khác...' }
                    ]
                }
            },
            food_tour: {
                pace: {
                    key: 'pace',
                    greeting: 'Food tour cần cân lượng ăn và khoảng cách các quán.',
                    question: 'Bạn muốn “ăn” với nhịp độ nào?',
                    type: 'single',
                    options: [
                        { value: 'cham_rai', label: 'Ít quán, ăn kỹ từng món' },
                        { value: 'can_bang', label: '3–4 điểm ăn / ngày' },
                        { value: 'dap_dong', label: 'Thử càng nhiều quán càng tốt' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
                interests: {
                    key: 'interests',
                    greeting: 'Chọn đúng “gu” ẩm thực sẽ giúp lộ trình ngon hơn.',
                    question: 'Bạn muốn tập trung vào đâu? (có thể chọn nhiều)',
                    type: 'multi',
                    options: [
                        { value: 'am_thuc', label: 'Đặc sản địa phương' },
                        { value: 'check_in', label: 'Quán có view / sống ảo' },
                        { value: 'thien_nhien', label: 'Quán ngoài trời / gần thiên nhiên' },
                        { value: 'van_hoa', label: 'Ẩm thực gắn văn hóa vùng' },
                        { value: 'nghi_duong', label: 'Quán thư giãn, không ồn' },
                        { value: 'other', label: 'Khác...' }
                    ]
                }
            },
            check_in: {
                pace: {
                    key: 'pace',
                    greeting: 'Ảnh đẹp phụ thuộc thời gian ánh sáng và số điểm.',
                    question: 'Bạn muốn “săn ảnh” dày hay thong thả?',
                    type: 'single',
                    options: [
                        { value: 'cham_rai', label: 'Ít điểm, chụp kỹ / đợi ánh sáng' },
                        { value: 'can_bang', label: '3–4 điểm check-in / ngày' },
                        { value: 'dap_dong', label: 'Ghé thật nhiều góc sống ảo' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
                interests: {
                    key: 'interests',
                    greeting: 'Chọn kiểu cảnh bạn thích để lịch trình “ra ảnh”.',
                    question: 'Bạn muốn ưu tiên góc nào? (có thể chọn nhiều)',
                    type: 'multi',
                    options: [
                        { value: 'check_in', label: 'Điểm viral / view đẹp' },
                        { value: 'thien_nhien', label: 'Thiên nhiên / Núi sông' },
                        { value: 'van_hoa', label: 'Kiến trúc / Di tích' },
                        { value: 'am_thuc', label: 'Quán cafe / Đồ uống sống ảo' },
                        { value: 'tam_linh', label: 'Chùa đền có khung cảnh đẹp' },
                        { value: 'other', label: 'Khác...' }
                    ]
                }
            },
            family: {
                pace: {
                    key: 'pace',
                    greeting: 'Với gia đình, lịch quá dày dễ mệt.',
                    question: 'Nhịp độ phù hợp với cả nhà?',
                    type: 'single',
                    options: [
                        { value: 'cham_rai', label: 'Chậm, nhiều nghỉ' },
                        { value: 'can_bang', label: 'Cân bằng, vừa chơi vừa nghỉ' },
                        { value: 'dap_dong', label: 'Muốn xem nhiều điểm' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
                interests: {
                    key: 'interests',
                    greeting: 'Chọn hoạt động cả nhà cùng thích.',
                    question: 'Ưu tiên trải nghiệm nào? (có thể chọn nhiều)',
                    type: 'multi',
                    options: [
                        { value: 'thien_nhien', label: 'Thiên nhiên / Không khí trong lành' },
                        { value: 'check_in', label: 'Điểm vui / Dễ chụp ảnh gia đình' },
                        { value: 'am_thuc', label: 'Ăn uống phù hợp trẻ em' },
                        { value: 'van_hoa', label: 'Tham quan nhẹ nhàng' },
                        { value: 'tam_linh', label: 'Ghé chùa / Đền' },
                        { value: 'nghi_duong', label: 'Nghỉ dưỡng' },
                        { value: 'other', label: 'Khác...' }
                    ]
                }
            },
            couple: {
                pace: {
                    key: 'pace',
                    greeting: 'Nhịp độ quyết định vibe chuyến couple.',
                    question: 'Bạn muốn chuyến đi thế nào?',
                    type: 'single',
                    options: [
                        { value: 'cham_rai', label: 'Chậm, thư giãn, ít điểm' },
                        { value: 'can_bang', label: 'Vừa khám phá vừa chill' },
                        { value: 'dap_dong', label: 'Nhiều trải nghiệm trong ngày' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
                interests: {
                    key: 'interests',
                    greeting: 'Chọn vibe couple bạn thích.',
                    question: 'Ưu tiên trải nghiệm nào? (có thể chọn nhiều)',
                    type: 'multi',
                    options: [
                        { value: 'check_in', label: 'Góc ảnh lãng mạn' },
                        { value: 'am_thuc', label: 'Ăn uống / Cafe đẹp' },
                        { value: 'thien_nhien', label: 'Hoàng hôn / Thiên nhiên' },
                        { value: 'nghi_duong', label: 'Nghỉ dưỡng riêng tư' },
                        { value: 'van_hoa', label: 'Dạo nhẹ di tích' },
                        { value: 'other', label: 'Khác...' }
                    ]
                }
            },
            resort: {
                duration_hotel: {
                    ...sharedDuration,
                    greeting: 'Nghỉ dưỡng thường nên ở lại ít nhất 1 đêm.',
                    options: [
                        { value: '2d1n_hotel', label: '2 ngày 1 đêm (Cần khách sạn)' },
                        { value: '3d2n_hotel', label: '3 ngày 2 đêm (Cần khách sạn)' },
                        { value: '1_day', label: 'Đi trong ngày (spa / nghỉ ngắn)' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
                pace: {
                    key: 'pace',
                    greeting: 'Nghỉ dưỡng = ít lịch, nhiều thời gian rảnh.',
                    question: 'Bạn muốn lịch trình nghỉ dưỡng thế nào?',
                    type: 'single',
                    options: [
                        { value: 'cham_rai', label: 'Chủ yếu ở resort / khách sạn' },
                        { value: 'can_bang', label: 'Nửa nghỉ, nửa đi chơi nhẹ' },
                        { value: 'dap_dong', label: 'Vẫn muốn tham quan khá nhiều' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
                interests: {
                    key: 'interests',
                    greeting: 'Chọn trải nghiệm nghỉ dưỡng bạn thích.',
                    question: 'Bạn muốn ưu tiên gì? (có thể chọn nhiều)',
                    type: 'multi',
                    options: [
                        { value: 'nghi_duong', label: 'Khách sạn / Resort chất lượng' },
                        { value: 'thien_nhien', label: 'Không gian xanh / View đẹp' },
                        { value: 'am_thuc', label: 'Ăn uống tại chỗ / Nhà hàng' },
                        { value: 'check_in', label: 'Góc check-in thư giãn' },
                        { value: 'tam_linh', label: 'Ghé điểm tâm linh gần đó' },
                        { value: 'other', label: 'Khác...' }
                    ]
                }
            },
            team_building: {
                pace: {
                    key: 'pace',
                    greeting: 'Team building cần lịch rõ, dễ điều phối.',
                    question: 'Bạn muốn lịch trình đoàn thế nào?',
                    type: 'single',
                    options: [
                        { value: 'can_bang', label: 'Cân bằng hoạt động + nghỉ' },
                        { value: 'dap_dong', label: 'Nhiều hoạt động gắn kết' },
                        { value: 'cham_rai', label: 'Nhẹ nhàng, thiên về nghỉ' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
                interests: {
                    key: 'interests',
                    greeting: 'Chọn hoạt động phù hợp team.',
                    question: 'Ưu tiên trải nghiệm nào? (có thể chọn nhiều)',
                    type: 'multi',
                    options: [
                        { value: 'thien_nhien', label: 'Outdoor / Thiên nhiên' },
                        { value: 'am_thuc', label: 'Ăn uống tập thể' },
                        { value: 'check_in', label: 'Điểm tham quan chụp ảnh nhóm' },
                        { value: 'van_hoa', label: 'Tham quan văn hóa' },
                        { value: 'nghi_duong', label: 'Nơi ở đủ chỗ cho đoàn' },
                        { value: 'other', label: 'Khác...' }
                    ]
                }
            },
            backpacking: {
                pace: {
                    key: 'pace',
                    greeting: 'Phượt có thể chill hoặc “cày” nhiều điểm.',
                    question: 'Nhịp độ bạn muốn?',
                    type: 'single',
                    options: [
                        { value: 'dap_dong', label: 'Khám phá nhiều điểm' },
                        { value: 'can_bang', label: 'Cân bằng' },
                        { value: 'cham_rai', label: 'Đi chậm, tùy hứng' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
                interests: {
                    key: 'interests',
                    greeting: 'Chọn hướng khám phá bạn thích.',
                    question: 'Ưu tiên trải nghiệm nào? (có thể chọn nhiều)',
                    type: 'multi',
                    options: [
                        { value: 'thien_nhien', label: 'Thiên nhiên / Đường đẹp' },
                        { value: 'check_in', label: 'Điểm view / Check-in' },
                        { value: 'am_thuc', label: 'Quán địa phương giá mềm' },
                        { value: 'van_hoa', label: 'Làng / Di tích ít người' },
                        { value: 'tam_linh', label: 'Ghé chùa đền dọc đường' },
                        { value: 'other', label: 'Khác...' }
                    ]
                }
            }
        };

        const cfg = byType[type] || {};

        const interests = cfg.interests || {
            key: 'interests',
            greeting: 'Chọn đúng sở thích sẽ giúp lịch trình sát ý bạn hơn.',
            question: 'Bạn muốn ưu tiên trải nghiệm nào? (có thể chọn nhiều)',
            type: 'multi',
            options: [
                { value: 'tam_linh', label: 'Tâm linh / Chùa đền' },
                { value: 'am_thuc', label: 'Ẩm thực đặc sản' },
                { value: 'check_in', label: 'Check-in / Sống ảo' },
                { value: 'thien_nhien', label: 'Thiên nhiên / Sinh thái' },
                { value: 'van_hoa', label: 'Văn hóa - Lịch sử' },
                { value: 'nghi_duong', label: 'Nghỉ dưỡng / Thư giãn' },
                { value: 'other', label: 'Khác...' }
            ]
        };

        const duplicate = impliedInterest[type];
        const trimmedInterests = duplicate
            ? { ...interests, options: interests.options.filter(o => o.value !== duplicate) }
            : interests;

        return [
            cfg.duration_hotel || sharedDuration,
            trimmedInterests,
            cfg.pace || {
                key: 'pace',
                greeting: `Tiếp theo là nhịp độ cho chuyến ${label}.`,
                question: 'Bạn muốn lịch trình dày hay thoải mái?',
                type: 'single',
                options: [
                    { value: 'cham_rai', label: 'Chậm rãi, thư giãn (ít điểm)' },
                    { value: 'can_bang', label: 'Cân bằng (3–4 điểm/ngày)' },
                    { value: 'dap_dong', label: 'Dồn dập, xem nhiều điểm' },
                    { value: 'other', label: 'Khác...' }
                ]
            },
            {
                key: '__location_picker',
                type: 'location_picker',
                greeting: 'Gần xong rồi! Bạn có muốn chọn trước điểm cụ thể không?',
                question: 'Chọn 1 địa điểm bạn muốn ghé (có thể bỏ qua)',
            }
        ];
    }

    let activeSteps = [];
    let defaultStepIndex = 0;

    /* ─── Step 0: Trip Type ─── */
    function renderTripTypeStep() {
        currentStep = 0;
        defaultStepIndex = 0;
        updateProgress();
        backBtn.disabled = true;
        nextBtn.classList.remove('visible');
        multiHint.style.display = 'none';

        let html = '<div class="tp-step">';
        html += '<div class="tp-step-greeting">Xin chào, mình giúp bạn lên lịch trình nhé.</div>';
        html += '<div class="tp-step-question">Bạn muốn chuyến đi kiểu gì?</div>';
        html += '<div class="tp-card-grid cols-4">';
        TRIP_TYPES.forEach(opt => {
            html += `<div class="tp-card tp-card-large" data-value="${opt.value}" data-label="${opt.label}">
                <span class="tp-card-icon">${opt.icon}</span>
                <span class="tp-card-label">${opt.label}</span>
                <span class="tp-card-desc">${opt.desc}</span>
            </div>`;
        });
        html += '</div></div>';
        wizardBody.innerHTML = html;

        wizardBody.querySelectorAll('.tp-card').forEach(card => {
            card.addEventListener('click', () => {
                tripType = card.dataset.value;
                tripTypeLabel = card.dataset.label;
                wizardBody.querySelectorAll('.tp-card').forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
                updateProfile();
                setTimeout(() => {
                    stepHistory.push({ step: 0, renderFn: renderTripTypeStep });
                    currentStep = 1;
                    defaultStepIndex = 0;
                    activeSteps = getWizardSteps(tripType);
                    renderDefaultStep(0);
                }, 250);
            });
        });
    }

    let pickedLocationId = null;

    function renderDefaultStep(idx) {
        if (!activeSteps.length && tripType) {
            activeSteps = getWizardSteps(tripType);
        }
        if (idx < activeSteps.length) {
            const step = activeSteps[idx];
            currentAiQuestion = step;
            if (step.type === 'location_picker') {
                renderLocationPickerStep(step);
                } else {
                renderAiQuestion(step);
                }
            } else {
            aiDone = true;
            renderDoneStep('Đã ghi nhận đủ thông tin cho chuyến đi của bạn.');
            }
    }

    function renderLocationPickerStep(q) {
        backBtn.disabled = stepHistory.length === 0;
        nextBtn.classList.remove('visible');
        multiHint.style.display = 'none';
        updateProgress();
        pickedLocationId = null;

        const allLocs = (typeof locations !== 'undefined' && Array.isArray(locations)) ? locations : [];
        const getCatName = l => l.category?.name || l.category_name || 'Khác';
        const cats = [...new Set(allLocs.map(getCatName))].sort();

        let html = '<div class="tp-step tp-location-picker-step">';
        if (q.greeting) html += `<div class="tp-step-greeting">${q.greeting}</div>`;
        html += `<div class="tp-step-question">${q.question}</div>`;

        html += `<div class="tp-loc-toolbar">
            <input type="text" id="tp-loc-search" class="tp-loc-search" placeholder="Tìm địa điểm..." autocomplete="off" />
            <select id="tp-loc-cat-filter" class="tp-loc-cat-filter">
                <option value="">Tất cả danh mục</option>
                ${cats.map(c => `<option value="${c}">${c}</option>`).join('')}
            </select>
        </div>`;

        html += '<div class="tp-loc-grid" id="tp-loc-grid">';
        html += buildLocCards(allLocs);
        html += '</div>';

        html += `<div class="tp-loc-actions">
            <button class="tp-btn tp-btn-skip" id="tp-loc-skip">Bỏ qua</button>
            <button class="tp-btn tp-btn-confirm" id="tp-loc-confirm" disabled>Xác nhận</button>
        </div>`;
        html += '</div>';
        wizardBody.innerHTML = html;

        const grid = document.getElementById('tp-loc-grid');
        const searchInput = document.getElementById('tp-loc-search');
        const catFilter = document.getElementById('tp-loc-cat-filter');
        const confirmBtn = document.getElementById('tp-loc-confirm');
        const skipBtn = document.getElementById('tp-loc-skip');

        function filterLocs() {
            const keyword = (searchInput?.value || '').trim().toLowerCase();
            const cat = catFilter?.value || '';
            let filtered = allLocs;
            if (keyword) filtered = filtered.filter(l => (l.name || '').toLowerCase().includes(keyword) || (l.address || '').toLowerCase().includes(keyword));
            if (cat) filtered = filtered.filter(l => getCatName(l) === cat);
            grid.innerHTML = buildLocCards(filtered);
            bindLocCards();
        }

        function bindLocCards() {
            grid.querySelectorAll('.tp-loc-card').forEach(card => {
                const id = parseInt(card.dataset.id);
                if (pickedLocationId === id) card.classList.add('selected');
                card.addEventListener('click', () => {
                    if (pickedLocationId === id) {
                        pickedLocationId = null;
                        card.classList.remove('selected');
                    } else {
                        grid.querySelectorAll('.tp-loc-card').forEach(c => c.classList.remove('selected'));
                        pickedLocationId = id;
                        card.classList.add('selected');
                    }
                    confirmBtn.disabled = pickedLocationId === null;
                });
            });
        }

        if (searchInput) searchInput.addEventListener('input', filterLocs);
        if (catFilter) catFilter.addEventListener('change', filterLocs);
        bindLocCards();

        function finishLocStep(picked) {
            const names = picked.map(id => { const l = allLocs.find(x => x.id === id); return l ? l.name : id; });
            aiAnswers.push({
                question: q.question,
                answer: names.length ? names.join(', ') : 'Bỏ qua',
                key: 'preferred_locations',
                value: picked.join(','),
            });
            stepHistory.push({ step: currentStep, question: q, selection: picked, answersSnapshot: JSON.parse(JSON.stringify(aiAnswers)) });
            updateProfile();
            currentStep++;
            defaultStepIndex = activeSteps.length;
            aiDone = true;
            renderDoneStep('Đã ghi nhận đủ thông tin cho chuyến đi của bạn.');
        }

        skipBtn.addEventListener('click', () => finishLocStep([]));
        confirmBtn.addEventListener('click', () => finishLocStep(pickedLocationId ? [pickedLocationId] : []));
    }

    function buildLocCards(locs) {
        if (!locs.length) return '<div style="text-align:center;padding:20px;color:#94a3b8;font-size:0.75rem;">Không tìm thấy địa điểm nào.</div>';
        return locs.map(l => {
            const img = l.thumbnail_url || l.image || '';
            const imgHtml = img ? `<img src="${img}" alt="" class="tp-loc-card-img" loading="lazy" />` : `<div class="tp-loc-card-img tp-loc-card-img-placeholder"><span class="material-symbols-rounded">place</span></div>`;
            return `<div class="tp-loc-card" data-id="${l.id}">
                ${imgHtml}
                <div class="tp-loc-card-body">
                    <div class="tp-loc-card-name">${l.name || 'Địa điểm'}</div>
                    <div class="tp-loc-card-cat">${l.category?.name || l.category_name || ''}</div>
                </div>
            </div>`;
        }).join('');
    }

    function renderAiQuestion(q) {
        currentSelection = q.type === 'multi' ? [] : null;
        backBtn.disabled = stepHistory.length === 0;
        const isMulti = q.type === 'multi';
        multiHint.style.display = isMulti ? '' : 'none';
        if (isMulti) { nextBtn.classList.add('visible'); nextBtn.disabled = true; }
        else { nextBtn.classList.remove('visible'); }
        updateProgress();

        const rawOpts = q.options || [];
        const hasOther = rawOpts.some(o => o.value === 'other' || o.label.toLowerCase().includes('khác'));
        const optionsList = [...rawOpts];
        if (!hasOther) {
            optionsList.push({ value: 'other', label: 'Khác...' });
        }

        const optCount = optionsList.length;
        let colClass = optCount <= 2 ? 'cols-2' : optCount <= 4 ? 'cols-2' : optCount <= 6 ? 'cols-3' : 'cols-4';

        let html = '<div class="tp-step">';
        if (q.greeting) html += `<div class="tp-step-greeting">${q.greeting}</div>`;
        html += `<div class="tp-step-question">${q.question}</div>`;
        html += `<div class="tp-card-grid ${colClass}">`;
        optionsList.forEach(opt => {
            const isOther = (opt.value === 'other' || opt.label.toLowerCase().includes('khác'));
            if (isMulti) {
                html += `<div class="tp-card tp-card-multi" data-value="${opt.value}" data-label="${opt.label}" data-type="${q.type}" data-is-other="${isOther}">
                    <div class="tp-checkbox-box">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <span class="tp-card-label">${opt.label}</span>
                </div>`;
            } else {
                html += `<div class="tp-card" data-value="${opt.value}" data-label="${opt.label}" data-type="${q.type}" data-is-other="${isOther}">
                    <span class="tp-card-label">${opt.label}</span>
                </div>`;
            }
        });
        html += '</div>';

        html += `<div class="tp-other-input-wrap" id="tp-other-input-wrap" style="display: none;">
            <input type="text" id="tp-other-input" class="tp-other-input" placeholder="Nhập ý kiến / lựa chọn khác của bạn..." maxlength="150" autocomplete="off" />
        </div>`;
        html += '</div>';

        wizardBody.innerHTML = html;

        const otherInput = document.getElementById('tp-other-input');
        if (otherInput) {
            otherInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (currentAiQuestion) advanceFromQuestion(currentAiQuestion);
                }
            });
        }

        wizardBody.querySelectorAll('.tp-card').forEach(card => {
            card.addEventListener('click', () => handleCardClick(card, q));
        });
    }

    function handleCardClick(card, q) {
        const value = card.dataset.value;
        const label = card.dataset.label;
        const isOther = card.dataset.isOther === 'true';
        const otherWrap = document.getElementById('tp-other-input-wrap');
        const otherInput = document.getElementById('tp-other-input');

        if (q.type === 'multi') {
            if (!currentSelection) currentSelection = [];
            const idx = currentSelection.findIndex(s => s.value === value);
            if (idx > -1) {
                currentSelection.splice(idx, 1);
                card.classList.remove('selected');
                if (isOther && otherWrap) {
                    otherWrap.style.display = 'none';
                    if (otherInput) otherInput.value = '';
                }
            } else {
                currentSelection.push({ value, label, isOther });
                card.classList.add('selected');
                if (isOther && otherWrap) {
                    otherWrap.style.display = 'block';
                    if (otherInput) setTimeout(() => otherInput.focus(), 100);
                }
            }
            nextBtn.disabled = currentSelection.length === 0;
            return;
        }

        currentSelection = { value, label, isOther };
        wizardBody.querySelectorAll('.tp-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');

        if (isOther) {
            if (otherWrap) otherWrap.style.display = 'block';
            if (otherInput) setTimeout(() => otherInput.focus(), 100);
            nextBtn.classList.add('visible');
            nextBtn.disabled = false;
        } else {
            if (otherWrap) otherWrap.style.display = 'none';
            if (otherInput) otherInput.value = '';
            nextBtn.classList.remove('visible');
            setTimeout(() => advanceFromQuestion(q), 250);
        }
    }

    function advanceFromQuestion(q) {
        const otherInput = document.getElementById('tp-other-input');
        const customText = otherInput ? otherInput.value.trim() : '';

        let answerText = '';
        if (Array.isArray(currentSelection)) {
            let labels = currentSelection.map(s => {
                if (s.isOther) {
                    return customText ? `Khác (${customText})` : s.label;
                }
                return s.label;
            });
            answerText = labels.join(', ');
        } else if (currentSelection) {
            if (currentSelection.isOther) {
                answerText = customText ? `Khác (${customText})` : currentSelection.label;
            } else {
                answerText = currentSelection.label;
            }
        }

        if (!answerText) return;

        const answerValue = Array.isArray(currentSelection)
            ? currentSelection.map(s => s.value).join(',')
            : (currentSelection?.value || null);

        aiAnswers.push({
            question: q.question,
            answer: answerText,
            key: q.key || null,
            value: answerValue,
        });
        stepHistory.push({
            step: currentStep,
            question: q,
            selection: JSON.parse(JSON.stringify(currentSelection)),
            answersSnapshot: JSON.parse(JSON.stringify(aiAnswers)),
        });
        updateProfile();
        currentStep++;

        if (defaultStepIndex < activeSteps.length - 1) {
            defaultStepIndex++;
            renderDefaultStep(defaultStepIndex);
        } else {
            defaultStepIndex = activeSteps.length;
            aiDone = true;
            renderDoneStep('Đã ghi nhận đủ thông tin cho chuyến đi của bạn.');
        }
    }

    nextBtn.addEventListener('click', () => {
        if (!currentAiQuestion || nextBtn.disabled) return;
        advanceFromQuestion(currentAiQuestion);
    });

    backBtn.addEventListener('click', () => {
        if (stepHistory.length === 0) return;
        const prev = stepHistory.pop();
        if (prev.step === 0) {
            currentStep = 0; tripType = ''; tripTypeLabel = '';
            aiAnswers = []; aiDone = false; currentAiQuestion = null;
            defaultStepIndex = 0;
            activeSteps = [];
            generateBtn.disabled = true;
            updateProfile(); renderTripTypeStep();
        } else {
            currentStep = prev.step;
            aiAnswers = prev.answersSnapshot ? prev.answersSnapshot.slice(0, -1) : [];
            aiDone = false; currentAiQuestion = prev.question; generateBtn.disabled = true;
            defaultStepIndex = Math.max(0, currentStep - 1);
            if (!activeSteps.length && tripType) activeSteps = getWizardSteps(tripType);
                updateProfile();
                renderDefaultStep(defaultStepIndex);
        }
    });

    function renderDoneStep(greeting) {
        nextBtn.classList.remove('visible');
        multiHint.style.display = 'none';
        backBtn.disabled = stepHistory.length === 0;
        generateBtn.disabled = false;
        updateProgress();
        wizardBody.innerHTML = `
            <div class="tp-step" style="text-align: center; padding: 28px 0;">
                <div class="tp-step-question" style="margin-bottom: 10px;">${greeting}</div>
                <div style="font-size: 0.74rem; color: #6482a6; margin-bottom: 18px;">Hệ thống đã thu thập đầy đủ mong muốn cho chuyến đi của bạn.</div>
                <button class="tp-btn-generate-main" id="tp-btn-generate-main"
                    style="margin: 0 auto; background: #0284c7; color: #ffffff; padding: 12px 28px; border-radius: 10px; border: none; cursor: pointer; font-size: 0.88rem; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35); transition: all 0.2s ease;">
                    <span class="material-symbols-rounded" style="font-size: 20px;">auto_awesome</span>
                    Tạo lịch trình ngay
                </button>
            </div>`;

        const mainGenBtn = document.getElementById('tp-btn-generate-main');
        if (mainGenBtn) {
            mainGenBtn.addEventListener('click', () => startGeneration());
        }
    }

    function updateProgress() {
        const stepCount = activeSteps.length || 6;
        const totalFixed = 1 + stepCount; // trip type + follow-up steps
        let html = '';
        for (let i = 0; i < totalFixed; i++) {
            let cls = 'tp-progress-dot';
            if (i < currentStep) cls += ' done';
            else if (i === currentStep) cls += ' active';
            else if (aiDone && i < totalFixed) cls += ' done';
            html += `<div class="${cls}"></div>`;
        }
        html += `<span class="tp-progress-label">${currentStep === 0 ? '' : 'Bước ' + Math.min(currentStep, totalFixed) + '/' + totalFixed}</span>`;
        progressBar.innerHTML = html;
    }

    function updateProfile() {
        let items = [];
        if (tripTypeLabel) items.push({ label: 'Kiểu chuyến đi', value: tripTypeLabel });
        aiAnswers.forEach(a => {
            let short = a.question.length > 22 ? a.question.substring(0, 22) + '…' : a.question;
            items.push({ label: short, value: a.answer });
        });
        if (items.length === 0) {
            profileBody.innerHTML = '<div class="tp-profile-empty">Chọn các thẻ bên trái để xây dựng hồ sơ</div>';
            return;
        }
        let html = '';
        items.forEach(item => {
            html += `<div class="tp-profile-item">
                <div class="tp-profile-item-dot"></div>
                <div class="tp-profile-item-content">
                    <div class="tp-profile-item-label">${item.label}</div>
                    <div class="tp-profile-item-value">${item.value}</div>
                </div>
            </div>`;
        });
        profileBody.innerHTML = html;
    }

    /* ─── Generate ─── */
    generateBtn.addEventListener('click', () => {
        if (generateBtn.disabled || isLoading) return;
        startGeneration();
    });

    const LOADING_MESSAGES = [
        'Đang phân tích thông tin...',
        'Đang chọn địa điểm phù hợp...',
        'AI đang sắp xếp lịch trình (thường 20–45 giây)...',
        'Đang tối ưu di chuyển...',
        'Gần xong rồi...',
    ];

    function startGeneration() {
        isLoading = true;
        wizardBody.style.display = 'none';
        footer.style.display = 'none';
        loadingPanel.classList.add('active');
        setPlannerMode('loading');
        loadingBarFill.style.width = '0%';

        let msgIdx = 0;
        loadingMsg.textContent = LOADING_MESSAGES[0];
        const msgInterval = setInterval(() => {
            msgIdx++;
            if (msgIdx < LOADING_MESSAGES.length) {
                loadingMsg.style.opacity = '0';
                setTimeout(() => { loadingMsg.textContent = LOADING_MESSAGES[msgIdx]; loadingMsg.style.opacity = '1'; }, 200);
            }
        }, 2200);

        let progress = 0;
        const barInterval = setInterval(() => {
            progress += Math.random() * 8 + 2;
            if (progress > 90) progress = 90;
            loadingBarFill.style.width = progress + '%';
        }, 500);

        const fullAnswers = [
            { question: 'Kiểu chuyến đi', answer: tripTypeLabel, key: 'trip_type', value: tripType },
            ...aiAnswers
        ];
        lastAnswersPayload = fullAnswers;

        fetch('X', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': 'X',
            },
            body: JSON.stringify({ answers: fullAnswers, trip_type: tripType || tripTypeLabel }),
        })
        .then(async (res) => {
            const text = await res.text();
            let data = null;
            try {
                data = text ? JSON.parse(text) : null;
            } catch (e) {
                data = null;
            }

            clearInterval(msgInterval); clearInterval(barInterval);
            loadingBarFill.style.width = '100%';
            setTimeout(() => {
                loadingPanel.classList.remove('active'); isLoading = false;
                if (data && data.success && data.itinerary) {
                    renderItinerary(data.itinerary);
                    return;
                }
                if (data && data.error) {
                    renderError(data.error);
                    return;
                }
                if (res.status === 419) {
                    renderError('Phiên làm việc hết hạn. Tải lại trang rồi thử lại.');
                    return;
                }
                if (res.status === 504 || res.status >= 500) {
                    renderError('Máy chủ xử lý quá lâu. Bấm "Lên lịch mới" để thử lại.');
                    return;
                }
                renderError('Không tạo được lịch trình. Bấm "Lên lịch mới" để thử lại.');
            }, 500);
        })
        .catch(() => {
            clearInterval(msgInterval); clearInterval(barInterval);
            loadingPanel.classList.remove('active'); isLoading = false;
            renderError('Không thể kết nối máy chủ. Kiểm tra mạng rồi bấm "Lên lịch mới".');
        });
    }

    function tpEsc(str) {
        return String(str ?? '').replace(/[&<>"']/g, s => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[s]));
    }

    function lookupMapPlace(id, name) {
        if (typeof locations === 'undefined' || !Array.isArray(locations)) return null;
        const nid = String(id || '');
        const nname = String(name || '').trim().toLowerCase();
        return locations.find(l => String(l.id) === nid)
            || (nname ? locations.find(l => String(l.name || '').toLowerCase() === nname) : null)
            || null;
    }

    function haversineKm(a, b) {
        const R = 6371;
        const dLat = (b.lat - a.lat) * Math.PI / 180;
        const dLng = (b.lng - a.lng) * Math.PI / 180;
        const x = Math.sin(dLat / 2) ** 2
            + Math.cos(a.lat * Math.PI / 180) * Math.cos(b.lat * Math.PI / 180) * Math.sin(dLng / 2) ** 2;
        return R * 2 * Math.atan2(Math.sqrt(x), Math.sqrt(1 - x));
    }

    function enrichItineraryPlaces(data) {
        let prev = null;
        let stops = 0;
        let meals = 0;
        let distance = 0;
        const seen = {};
        (data.days || []).forEach(day => {
            (day.slots || []).forEach(slot => {
                if ((slot.type || '') === 'food') meals += 1;
                const hit = lookupMapPlace(slot.place?.id || slot.location_id, slot.place?.name || slot.location);
                if (hit) {
                    const lat = hit.lat ? parseFloat(hit.lat) : null;
                    const lng = hit.lng ? parseFloat(hit.lng) : null;
                    slot.location_id = hit.id;
                    slot.location = hit.name;
                    slot.place = Object.assign({}, slot.place || {}, {
                        id: hit.id,
                        name: hit.name,
                        slug: hit.slug,
                        lat,
                        lng,
                        address: hit.address || (slot.place && slot.place.address) || '',
                        image: hit.thumbnail_url || (slot.place && slot.place.image) || null,
                        rating: hit.average_rating || (slot.place && slot.place.rating) || null,
                        url: hit.slug ? ('/locations/' + hit.slug + '/360') : (slot.place && slot.place.url) || null,
                        category: (hit.category && hit.category.name) || (slot.place && slot.place.category) || null,
                    });
                    if (prev && prev.lat && prev.lng && lat && lng) {
                        const km = haversineKm(prev, { lat, lng });
                        if (km >= 0.1) {
                            slot.distance_from_prev_km = Math.round(km * 10) / 10;
                            distance += slot.distance_from_prev_km;
                        }
                    }
                    if (lat && lng) prev = { lat, lng };
                    if (hit.id && !seen[hit.id]) {
                        seen[hit.id] = true;
                        stops += 1;
                    }
                } else if (slot.distance_from_prev_km) {
                    distance += Number(slot.distance_from_prev_km) || 0;
                    if (slot.location_id && !seen[slot.location_id]) {
                        seen[slot.location_id] = true;
                        stops += 1;
                    }
                }
            });
        });
        data.stats = Object.assign({}, data.stats || {}, {
            days: data.stats?.days || (data.days || []).length,
            stops: data.stats?.stops || stops,
            meals: data.stats?.meals || meals,
            distance_km: data.stats?.distance_km || Math.round(distance * 10) / 10,
        });
        if (!data.stats.stops) data.stats.stops = stops;
        if (!data.stats.distance_km) data.stats.distance_km = Math.round(distance * 10) / 10;
        return data;
    }

    function collectTripPoints(data) {
        const pts = [];
        (data.days || []).forEach(day => {
            (day.slots || []).forEach(slot => {
                const hit = lookupMapPlace(slot.place?.id || slot.location_id, slot.place?.name || slot.location);
                const p = slot.place || {};
                const lat = parseFloat(p.lat || hit?.lat);
                const lng = parseFloat(p.lng || hit?.lng);
                if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;
                pts.push({
                    lat,
                    lng,
                    name: (hit && hit.name) || p.name || slot.location || '',
                    id: (hit && hit.id) || p.id || slot.location_id,
                    loc: hit || null,
                    place: p,
                });
            });
        });
        return pts;
    }

    function collectTripPointSegments(data) {
        const segments = [];
        (data.days || []).forEach(day => {
            const dayPts = [];
            (day.slots || []).forEach(slot => {
                const hit = lookupMapPlace(slot.place?.id || slot.location_id, slot.place?.name || slot.location);
                const p = slot.place || {};
                const lat = parseFloat(p.lat || hit?.lat);
                const lng = parseFloat(p.lng || hit?.lng);
                if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

                const last = dayPts[dayPts.length - 1];
                if (last && last.lat === lat && last.lng === lng) return;
                dayPts.push({ lat, lng });
            });
            if (dayPts.length > 1) {
                segments.push(dayPts.map(p => [p.lat, p.lng]));
            }
        });
        return segments;
    }

    function firstHeroImage(data) {
        for (const day of (data.days || [])) {
            for (const slot of (day.slots || [])) {
                if (slot.place && slot.place.image) return slot.place.image;
            }
        }
        return '';
    }

    function destroyTripMiniMap() {
        if (tpMiniMap) {
            try { tpMiniMap.remove(); } catch (e) {}
            tpMiniMap = null;
        }
        tpRouteMarkers = [];
    }

    function setMainMapPoisVisible(visible) {
        try {
            if (typeof map === 'undefined') return;
            const cluster = window.mapPoiCluster;
            if (!cluster) return;
            if (visible) {
                if (!map.hasLayer(cluster)) map.addLayer(cluster);
            } else if (map.hasLayer(cluster)) {
                map.removeLayer(cluster);
            }
        } catch (e) {}
    }

    function clearTripRouteOnMainMap() {
        try {
            if (typeof map !== 'undefined' && window._tpRouteGroup) {
                map.removeLayer(window._tpRouteGroup);
                window._tpRouteGroup = null;
            }
        } catch (e) {}
    }

    function tripPopupHtml(p) {
        const loc = (p && p.loc) || {};
        const place = (p && p.place) || {};
        const name = loc.name || (p && p.name) || place.name || '';
        const color = (loc.category && loc.category.icon_color) || '#ef4444';
        const thumb = loc.thumbnail_url || place.image || '';
        const desc = loc.short_description || '';
        const slug = loc.slug || place.slug || '';
        const url = place.url || (slug ? ('/locations/' + slug + '/360') : '');
        const thumbUrl = thumb || 'https://placehold.co/400x250/e2e8f0/475569?text=No+Image';
        let html = '<div class="poi-popup-inner" style="--poi-color: ' + color + ';">'
            + '<img src="' + tpEsc(thumbUrl) + '" class="poi-thumbnail" alt="' + tpEsc(name) + '">'
            + '<div class="poi-content">'
            + '<div class="poi-title">' + tpEsc(name) + '</div>';
        if (desc) html += '<div class="poi-desc">' + tpEsc(desc) + '</div>';
        if (url) {
            html += '<a href="' + tpEsc(url) + '" class="poi-btn-360" target="_blank" rel="noopener">Khám phá ngay</a>';
        }
        html += '</div></div>';
        return html;
    }

    function tripPinIcon(locLike, opts) {
        opts = opts || {};
        const loc = locLike && locLike.loc ? locLike.loc : locLike;
        const color = (loc && loc.category && loc.category.icon_color) ? loc.category.icon_color : '#ef4444';
        const iconUrl = (loc && loc.category && loc.category.icon_url) ? loc.category.icon_url : '';
        const name = (loc && loc.name) || locLike.name || '';
        const num = opts.number
            ? '<span class="tp-pin-num">' + opts.number + '</span>'
            : '';
        const cls = 'custom-map-pin tp-stop-pin' + (opts.active ? ' is-active' : '');
        const img = iconUrl ? ('<img class="pin-icon-img" src="' + tpEsc(iconUrl) + '" alt="">') : '';
        const html = '<div class="' + cls + '">'
            + '<svg class="pin-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="26" height="35">'
            + '<path fill="' + color + '" d="M172.3 501.7C27 291 0 269.4 0 192 0 86 86 0 192 0s192 86 192 192c0 77.4-27 99-172.3 309.7-9.5 13.8-29.9 13.8-39.5 0z"/>'
            + '</svg>'
            + img
            + num
            + '<div class="custom-pin-tooltip" style="--tip-color: ' + color + ';">' + tpEsc(name) + '</div>'
            + '</div>';
        return L.divIcon({
            className: 'my-custom-marker',
            html: html,
            iconSize: [26, 35],
            iconAnchor: [13, 35],
            popupAnchor: [0, -35],
        });
    }

    function tripRouteLatLngs(pts) {
        const out = [];
        pts.forEach(p => {
            const last = out[out.length - 1];
            if (last && last[0] === p.lat && last[1] === p.lng) return;
            out.push([p.lat, p.lng]);
        });
        return out;
    }

    function tripRouteLineStyle() {
        return {
            color: '#1e3a5f',
            weight: 3,
            opacity: 0.88,
            dashArray: '8 7',
            lineCap: 'round',
            lineJoin: 'round',
        };
    }

    function ringsFromHaNamGeo(geo) {
        const holes = [];
        const g = geo && geo.type === 'Feature' ? geo.geometry : geo;
        if (!g) return holes;
        if (g.type === 'MultiPolygon') {
            g.coordinates.forEach((polygon) => {
                holes.push(polygon[0].map(([lng, lat]) => [lat, lng]));
            });
        } else if (g.type === 'Polygon') {
            holes.push(g.coordinates[0].map(([lng, lat]) => [lat, lng]));
        }
        return holes;
    }

    function loadHaNamGeo() {
        if (typeof haNamGeo !== 'undefined' && haNamGeo) {
            return Promise.resolve(haNamGeo);
        }
        if (window._tpHaNamGeo) {
            return Promise.resolve(window._tpHaNamGeo);
        }
        return fetch(TP_BOUNDARY_URL)
            .then(res => res.json())
            .then(geo => {
                window._tpHaNamGeo = geo;
                return geo;
            })
            .catch(() => null);
    }

    function paintHaNamOnTripMap(geo) {
        if (!tpMiniMap || !geo) return null;
        try {
            if (!tpMiniMap.getPane('tpDimPane')) {
                const pane = tpMiniMap.createPane('tpDimPane');
                pane.style.zIndex = 350;
            }
            const world = [[-90, -180], [-90, 180], [90, 180], [90, -180], [-90, -180]];
            const holes = ringsFromHaNamGeo(geo);
            if (holes.length) {
                L.polygon([world, ...holes], {
                    pane: 'tpDimPane',
                    fillColor: '#94a3b8',
                    fillOpacity: 0.22,
                    stroke: false,
                    interactive: false,
                }).addTo(tpMiniMap);
            }
            const border = L.geoJSON(geo, {
                style: {
                    color: '#7ba7d4',
                    weight: 2,
                    opacity: 0.55,
                    fillColor: '#f8fafc',
                    fillOpacity: 0.04,
                },
                interactive: false,
            }).addTo(tpMiniMap);
            const provinceBounds = border.getBounds();
            tpMiniMap.setMaxBounds(provinceBounds.pad(0.5));
            return provinceBounds;
            } catch (e) {
            return null;
        }
    }

    function lockTripMinZoom(routeBounds) {
        if (!tpMiniMap || !routeBounds || !routeBounds.isValid()) return;
        tpMiniMap.fitBounds(routeBounds.pad(0.32), { maxZoom: 15, animate: false });
        const z = tpMiniMap.getZoom();
        if (Number.isFinite(z)) {
            tpMiniMap.setMinZoom(z);
        }
    }

    function renderTripMiniMap(data) {
        destroyTripMiniMap();
        const el = document.getElementById('tp-route-map');
        if (!routePane || !el || typeof L === 'undefined') {
            if (routePane) routePane.classList.add('hidden');
            return;
        }

        const pts = collectTripPoints(data);
        if (!pts.length) {
            routePane.classList.add('hidden');
            return;
        }

        routePane.classList.remove('hidden');
        tpMiniMap = L.map(el, {
            zoomControl: false,
            attributionControl: false,
            scrollWheelZoom: true,
            maxBoundsViscosity: 0.8,
            maxZoom: 20,
        });
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            maxZoom: 20,
        }).addTo(tpMiniMap);
        L.control.zoom({ position: 'topleft' }).addTo(tpMiniMap);

        const routeSegments = collectTripPointSegments(data);
        routeSegments.forEach((latlngs) => {
            if (latlngs.length > 1) {
                L.polyline(latlngs, tripRouteLineStyle()).addTo(tpMiniMap);
            }
        });
        if (!routeSegments.length) {
            const latlngs = tripRouteLatLngs(pts);
            if (latlngs.length > 1) {
                L.polyline(latlngs, tripRouteLineStyle()).addTo(tpMiniMap);
            }
        }

        pts.forEach((p, i) => {
            const marker = L.marker([p.lat, p.lng], {
                icon: tripPinIcon(p, { number: i + 1, active: i === 0 }),
                keyboard: false,
                zIndexOffset: 600,
            });
            marker.bindPopup(tripPopupHtml(p), {
                minWidth: 260,
                maxWidth: 260,
                closeButton: false,
                autoPanPadding: [28, 28],
            });
            marker.on('click', () => focusTripStop(i, false));
            marker.addTo(tpMiniMap);
            tpRouteMarkers.push(marker);
        });

        const routeBounds = L.latLngBounds(pts.map(p => [p.lat, p.lng]));
        tpMiniMap.fitBounds(routeBounds.pad(0.32), { maxZoom: 15, animate: false });
        loadHaNamGeo().then(geo => {
            if (!tpMiniMap) return;
            if (geo) paintHaNamOnTripMap(geo);
            else tpMiniMap.setMaxBounds(routeBounds.pad(1.5));
            setTimeout(() => {
                if (!tpMiniMap) return;
                tpMiniMap.invalidateSize();
                lockTripMinZoom(routeBounds);
            }, 180);
        });
    }

    function focusTripStop(index, fly) {
        document.querySelectorAll('.tp-stop').forEach((el, i) => {
            el.classList.toggle('is-active', i === index);
        });
        const pts = collectTripPoints(currentItinerary || {});
        const pt = pts[index];
        if (!pt || !tpMiniMap) return;
        tpRouteMarkers.forEach((m, i) => {
            try { m.setIcon(tripPinIcon(pts[i], { number: i + 1, active: i === index })); } catch (e) {}
        });
        if (fly !== false) {
            tpMiniMap.flyTo([pt.lat, pt.lng], Math.max(tpMiniMap.getZoom(), 15), { duration: 0.55 });
        }
        const marker = tpRouteMarkers[index];
        if (marker) {
            setTimeout(() => {
                try { marker.openPopup(); } catch (e) {}
            }, fly !== false ? 320 : 40);
        }
    }

    function googleMapsTripUrl(pts) {
        const unique = [];
        pts.forEach(p => {
            const last = unique[unique.length - 1];
            if (last && last.lat === p.lat && last.lng === p.lng) return;
            unique.push(p);
        });
        if (!unique.length) return '';
        const coord = p => Number(p.lat).toFixed(6) + ',' + Number(p.lng).toFixed(6);
        if (unique.length === 1) {
            const p = unique[0];
            const q = p.name ? (p.name + ' ' + coord(p)) : coord(p);
            return 'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(q);
        }
        const origin = coord(unique[0]);
        const destination = coord(unique[unique.length - 1]);
        const mids = unique.slice(1, -1).slice(0, 8).map(coord).join('|');
        let url = 'https://www.google.com/maps/dir/?api=1'
            + '&origin=' + encodeURIComponent(origin)
            + '&destination=' + encodeURIComponent(destination)
            + '&travelmode=driving';
        if (mids) url += '&waypoints=' + encodeURIComponent(mids);
        return url;
    }

    function openTripInGoogleMaps() {
        if (!currentItinerary) return;
        const pts = collectTripPoints(currentItinerary);
        const url = googleMapsTripUrl(pts);
        if (!url) return;
        window.open(url, '_blank', 'noopener,noreferrer');
    }

    function renderItinerary(data, saveToStorage = true) {
        data = enrichItineraryPlaces(data || {});
        currentItinerary = data;
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Lưu hành trình';
            saveBtn.classList.remove('saved');
        }
        if (saveToStorage) {
            saveTripDraft(data);
        }
        resultPanel.classList.add('active');
        setPlannerMode('result');

        const heroImg = firstHeroImage(data);
        if (resultHero) {
            resultHero.style.backgroundImage = heroImg ? ('url(' + JSON.stringify(heroImg) + ')') : '';
        }
        if (resultKicker) resultKicker.textContent = (data.days && data.days.length > 1) ? (data.days.length + ' ngày') : 'Trong ngày';
        resultTitle.textContent = data.title || 'Lịch trình du lịch';
        resultSummary.textContent = data.summary || '';

        const stats = data.stats || {};
        const dayCount = stats.days || (data.days || []).length || 0;
        const dist = stats.distance_km;
        if (resultStats) {
            const chips = [];
            chips.push(`<span class="tp-stat">${tpEsc(dayCount)} ngày</span>`);
            if (stats.stops) chips.push(`<span class="tp-stat">${tpEsc(stats.stops)} điểm dừng</span>`);
            if (dist) chips.push(`<span class="tp-stat">${tpEsc(dist)} km</span>`);
            resultStats.innerHTML = chips.join('');
        }

        const typeLabel = {
            visit: 'Tham quan',
            food: 'Ăn uống',
            transport: 'Di chuyển',
            rest: 'Nghỉ ngơi',
            photo: 'Check-in',
        };

        let html = '';
        let stopIndex = 0;
        if (data.days && data.days.length > 0) {
            data.days.forEach(day => {
                let dayTitle = String(day.title || '').trim()
                    .replace(new RegExp('^\\s*Ngày\\s*' + day.day + '\\s*[:\\-–]?\\s*', 'i'), '')
                    .replace(/^Ngày\s*\d+\s*[:\-–]?\s*/i, '')
                    .trim();
                html += `<section class="tp-day-section"><div class="tp-day-title">Ngày ${tpEsc(day.day)}${dayTitle ? ' · ' + tpEsc(dayTitle) : ''}</div><div class="tp-rail">`;
                (day.slots || []).forEach(slot => {
                    const cls = slot.type || 'visit';
                    const place = slot.place || {};
                    const locName = place.name || slot.location || typeLabel[cls] || 'Chặng';
                    const idx = stopIndex++;
                    const thumb = place.image
                        ? `<img class="tp-stop-photo" src="${tpEsc(place.image)}" alt="${tpEsc(locName)}" loading="lazy">`
                        : `<div class="tp-stop-photo placeholder">${tpEsc(typeLabel[cls] || '')}</div>`;
                    const meta = [];
                    meta.push(typeLabel[cls] || 'Hoạt động');
                    if (slot.distance_from_prev_km) meta.push(slot.distance_from_prev_km + ' km');
                    if (place.rating) meta.push(place.rating + '/5');
                    const detail = place.url
                        ? `<a class="tp-stop-link" href="${tpEsc(place.url)}" target="_blank" rel="noopener" data-stop-link="1">Xem 360</a>`
                        : '';
                    const timeParts = String(slot.time || '').trim().split(/\s*[-–—]\s*/).filter(Boolean);
                    const timeHtml = timeParts.length
                        ? `<span class="tp-stop-index-start">${tpEsc(timeParts[0])}</span>${timeParts[1] ? `<span class="tp-stop-index-end">${tpEsc(timeParts[1])}</span>` : ''}`
                        : '<span class="tp-stop-index-start">—</span>';
                    html += `<article class="tp-stop" data-stop-index="${idx}">
                        <div class="tp-stop-index">${timeHtml}</div>
                        ${thumb}
                        <div class="tp-stop-copy">
                            <div class="tp-stop-name">${tpEsc(locName)}</div>
                            <div class="tp-stop-activity">${tpEsc(slot.activity || slot.tip || '')}</div>
                            <div class="tp-stop-meta"><span>${tpEsc(meta.join(' · '))}</span>${detail}</div>
                        </div>
                    </article>`;
                });
                html += '</div></section>';
            });
        }
        if (data.tips && data.tips.length > 0) {
            html += `<div class="tp-tips-section"><div class="tp-tips-title">Lưu ý</div><ul class="tp-tips-list">${data.tips.map(t => `<li>${tpEsc(t)}</li>`).join('')}</ul></div>`;
        }
        resultBody.innerHTML = html;

        resultBody.querySelectorAll('.tp-stop').forEach(el => {
            el.addEventListener('click', (e) => {
                if (e.target.closest('[data-stop-link]')) return;
                const idx = parseInt(el.getAttribute('data-stop-index'), 10);
                if (Number.isFinite(idx)) focusTripStop(idx, true);
            });
        });

        renderTripMiniMap(data);
        clearTripRouteOnMainMap();
        setMainMapPoisVisible(true);
        if (collectTripPoints(data).length) focusTripStop(0, false);
        const subtitle = document.querySelector('#trip-planner-overlay .tp-header-subtitle');
        if (subtitle) subtitle.textContent = 'Hành trình gắn với bản đồ và dữ liệu địa điểm';
        const heading = document.querySelector('#trip-planner-overlay .tp-header-title');
        if (heading) heading.textContent = 'Lịch trình';
    }

    if (showRouteBtn) {
        showRouteBtn.addEventListener('click', openTripInGoogleMaps);
    }

    if (saveBtn) {
        saveBtn.addEventListener('click', () => {
            if (!currentItinerary || saveBtn.disabled) return;

            if (!IS_AUTHENTICATED) {
                if (confirm('Bạn cần đăng nhập để lưu lịch trình vào trang cá nhân. Đến trang đăng nhập?')) {
                    window.location.href = LOGIN_URL + '?redirect=' + encodeURIComponent(window.location.pathname + '#trip-planner');
                }
                return;
            }

            saveBtn.disabled = true;
            saveBtn.textContent = 'Đang lưu...';

            fetch('X', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': 'X', 'Accept': 'application/json' },
                body: JSON.stringify({
                    itinerary: currentItinerary,
                    answers: lastAnswersPayload,
                }),
            })
            .then(async res => {
                const data = await res.json().catch(() => ({}));
                if (res.status === 401 || data.need_login) {
                    saveBtn.disabled = false;
                    saveBtn.textContent = 'Lưu hành trình';
                    if (confirm('Bạn cần đăng nhập để lưu. Đến trang đăng nhập?')) {
                        window.location.href = LOGIN_URL;
                    }
                    return;
                }
                if (!data.success) {
                    saveBtn.disabled = false;
                    saveBtn.textContent = 'Lưu hành trình';
                    alert(data.error || 'Không lưu được lịch trình.');
                    return;
                }
                saveBtn.classList.add('saved');
                saveBtn.textContent = 'Đã lưu ✓';
                try { clearTripDraftStorage(); } catch (e) {}
                if (confirm('Đã lưu vào trang cá nhân. Mở trang cá nhân để xem?')) {
                    window.location.href = (data.profile_url || PROFILE_URL + '#itineraries');
                }
            })
            .catch(() => {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Lưu hành trình';
                alert('Không thể kết nối máy chủ.');
            });
        });
    }

    function renderRawResult(raw) {
        let strContent = typeof raw === 'string' ? raw : JSON.stringify(raw);
        try {
            let clean = strContent.replace(/^```(?:json)?\s*/i, '').replace(/\s*```$/i, '').trim();
            let match = clean.match(/\{[\s\S]*/);
            if (match) {
                let jsonStr = match[0];
                let lastBrace = jsonStr.lastIndexOf('}');
                if (lastBrace !== -1) {
                    jsonStr = jsonStr.substring(0, lastBrace + 1);
                }
                jsonStr = jsonStr.replace(/[\x00-\x1F\x7F]/g, ' ').replace(/,\s*([\]\}])/g, '$1');
                let parsed = JSON.parse(jsonStr);
                if (parsed && (parsed.days || parsed.title)) {
                    renderItinerary(parsed, true);
                    return;
                }
            }
        } catch (e) {
            console.warn('Could not parse raw JSON on client:', e);
        }

        resultPanel.classList.add('active');
        setPlannerMode('result');
        resultTitle.textContent = 'Lịch trình du lịch';
        resultSummary.textContent = '';
        if (resultStats) resultStats.innerHTML = '';
        let f = strContent.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
        resultBody.innerHTML = `<div class="tp-raw-result">${f}</div>`;
    }

    function renderError(msg) {
        currentItinerary = null;
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.textContent = 'Lưu hành trình';
        }
        resultPanel.classList.add('active');
        setPlannerMode('result');
        resultTitle.textContent = 'Không thể tạo lịch trình';
        resultSummary.textContent = msg;
        if (resultStats) resultStats.innerHTML = '';
        resultBody.innerHTML = `<div style="text-align:center; padding:32px 16px; color:#a1a1aa; font-size:0.75rem;">
            <p>${msg}</p><p style="margin-top:6px; font-size:0.68rem;">Bấm "Lên lịch mới" để thử lại</p></div>`;
    }

    (function bootSavedItineraryFromUrl() {
        const id = new URLSearchParams(window.location.search).get('itinerary');
        if (!id) return;
        fetch('X/' + encodeURIComponent(id), {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': 'X' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.itinerary) {
                window.openTripPlannerItinerary(data.itinerary);
            }
        })
        .catch(() => {});
    })();
});
