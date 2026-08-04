<!-- AI Trip Planner Widget — Refined Hybrid UI -->
<style>
    /* ═══════════════════════════════════════════════════
       OVERLAY & CONTAINER
       ═══════════════════════════════════════════════════ */
    #trip-planner-overlay {
        position: fixed;
        inset: 0;
        z-index: 10500;
        display: none;
        opacity: 0;
        transition: opacity 0.35s ease;
    }
    #trip-planner-overlay.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    #trip-planner-overlay.visible { opacity: 1; }

    .tp-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }

    .tp-container {
        position: relative;
        width: 92vw;
        max-width: 820px;
        height: 82vh;
        max-height: 560px;
        background: #ffffff;
        border-radius: 14px;
        box-shadow: 0 16px 48px rgba(0, 0, 0, 0.18);
        display: flex;
        overflow: hidden;
        transform: scale(0.92) translateY(20px);
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    #trip-planner-overlay.visible .tp-container {
        transform: scale(1) translateY(0);
    }

    .tp-left {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    /* ─── Header ─── */
    .tp-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 20px;
        border-bottom: 1px solid #e8ecf2;
        flex-shrink: 0;
    }
    .tp-header-left {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .tp-header-title {
        font-size: 0.85rem;
        font-weight: 600;
        color: #1e3a5f;
        letter-spacing: -0.01em;
    }
    .tp-header-subtitle {
        font-size: 0.68rem;
        color: #6482a6;
        font-weight: 400;
    }
    .tp-close-btn {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s;
        color: #94a3b8;
        flex-shrink: 0;
    }
    .tp-close-btn:hover {
        background: #fef2f2;
        border-color: #fca5a5;
        color: #dc2626;
    }

    /* ─── Progress Bar ─── */
    .tp-progress {
        padding: 0 20px;
        display: flex;
        align-items: center;
        gap: 5px;
        height: 28px;
        flex-shrink: 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .tp-progress-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #e2e8f0;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }
    .tp-progress-dot.active {
        background: #1e3a5f;
        width: 18px;
        border-radius: 3px;
    }
    .tp-progress-dot.done { background: #22c55e; }
    .tp-progress-label {
        font-size: 0.62rem;
        color: #a1a1aa;
        margin-left: auto;
        font-weight: 400;
    }

    /* ─── Wizard Body ─── */
    .tp-wizard-body {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 20px;
        position: relative;
    }
    .tp-wizard-body::-webkit-scrollbar { width: 3px; }
    .tp-wizard-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }

    .tp-step {
        animation: tpSlideIn 0.35s ease forwards;
    }
    @keyframes tpSlideIn {
        from { opacity: 0; transform: translateX(20px); }
        to { opacity: 1; transform: translateX(0); }
    }
    .tp-step-greeting {
        font-size: 0.75rem;
        color: #6482a6;
        margin-bottom: 4px;
        font-weight: 400;
    }
    .tp-step-question {
        font-size: 0.9rem;
        font-weight: 600;
        color: #1e3a5f;
        margin-bottom: 16px;
        line-height: 1.35;
    }

    /* ─── Card Grid ─── */
    .tp-card-grid {
        display: grid;
        gap: 8px;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    }
    .tp-card-grid.cols-2 { grid-template-columns: repeat(2, 1fr); }
    .tp-card-grid.cols-3 { grid-template-columns: repeat(3, 1fr); }
    .tp-card-grid.cols-4 { grid-template-columns: repeat(4, 1fr); }

    .tp-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
        position: relative;
        user-select: none;
    }
    .tp-card:hover {
        border-color: #93c5fd;
        background: #f8fafc;
    }
    .tp-card.selected {
        border-color: #1e3a5f;
        background: #f0f5fa;
    }
    .tp-card.selected::after {
        content: '\2713';
        position: absolute;
        top: 6px;
        right: 8px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #1e3a5f;
        color: #fff;
        font-size: 0.55rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .tp-card:active { transform: scale(0.97); }

    /* ─── Multi-select Checkbox Card Styling ─── */
    .tp-card.tp-card-multi {
        display: flex;
        align-items: center;
        gap: 10px;
        text-align: left;
        padding: 10px 12px;
    }
    .tp-card.tp-card-multi .tp-checkbox-box {
        width: 18px;
        height: 18px;
        border: 2px solid #cbd5e1;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: #ffffff;
        transition: all 0.15s ease;
        color: transparent;
    }
    .tp-card.tp-card-multi:hover .tp-checkbox-box {
        border-color: #94a3b8;
    }
    .tp-card.tp-card-multi.selected {
        border-color: #1e3a5f;
        background: #f0f5fa;
    }
    .tp-card.tp-card-multi.selected .tp-checkbox-box {
        background: #1e3a5f;
        border-color: #1e3a5f;
        color: #ffffff;
    }
    .tp-card.tp-card-multi.selected::after {
        display: none !important;
    }

    /* ─── Other / Custom Input Option Styling ─── */
    .tp-other-input-wrap {
        width: 100%;
        margin-top: 10px;
        animation: tpSlideIn 0.25s ease forwards;
    }
    .tp-other-input {
        width: 100%;
        padding: 9px 12px;
        border: 1.5px solid #0284c7;
        border-radius: 8px;
        font-size: 0.78rem;
        color: #1e3a5f;
        background: #ffffff;
        outline: none;
        box-sizing: border-box;
        font-family: inherit;
        transition: all 0.2s ease;
    }
    .tp-other-input:focus {
        border-color: #0369a1;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
    }

    .tp-card-icon {
        font-size: 1.15rem;
        margin-bottom: 4px;
        display: block;
        line-height: 1;
        filter: grayscale(30%);
        opacity: 0.85;
    }
    .tp-card-label {
        font-size: 0.72rem;
        font-weight: 500;
        color: #3b5980;
        line-height: 1.25;
    }
    .tp-card-desc {
        font-size: 0.62rem;
        color: #a1a1aa;
        margin-top: 2px;
        line-height: 1.25;
        font-weight: 400;
    }

    .tp-card.tp-card-large { padding: 14px 10px; }
    .tp-card.tp-card-large .tp-card-icon { font-size: 1.3rem; margin-bottom: 5px; }
    .tp-card.tp-card-large .tp-card-label { font-size: 0.75rem; }

    /* ─── AI Thinking ─── */
    .tp-thinking {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        animation: tpSlideIn 0.25s ease forwards;
    }
    .tp-thinking-dots {
        display: flex;
        gap: 5px;
        margin-bottom: 10px;
    }
    .tp-thinking-dot {
        width: 6px;
        height: 6px;
        background: #6482a6;
        border-radius: 50%;
        animation: tpThinkBounce 1.4s infinite ease-in-out both;
    }
    .tp-thinking-dot:nth-child(2) { animation-delay: 0.16s; }
    .tp-thinking-dot:nth-child(3) { animation-delay: 0.32s; }
    @keyframes tpThinkBounce {
        0%, 80%, 100% { transform: scale(0.5); opacity: 0.3; }
        40% { transform: scale(1); opacity: 1; }
    }
    .tp-thinking-text {
        font-size: 0.72rem;
        color: #a1a1aa;
        font-weight: 400;
    }

    /* ─── Footer ─── */
    .tp-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 20px;
        border-top: 1px solid #f1f5f9;
        flex-shrink: 0;
    }
    .tp-btn {
        padding: 6px 16px;
        border-radius: 8px;
        font-size: 0.73rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
        display: flex;
        align-items: center;
        gap: 5px;
        border: none;
        outline: none;
        font-family: inherit;
    }
    .tp-btn-back {
        background: #f1f5f9;
        color: #6482a6;
        border: 1px solid #e2e8f0;
    }
    .tp-btn-back:hover { background: #e2e8f0; color: #3b5980; }
    .tp-btn-back:disabled { opacity: 0.35; cursor: not-allowed; }

    .tp-multi-hint {
        font-size: 0.65rem;
        color: #a1a1aa;
        font-weight: 400;
    }
    .tp-btn-next {
        padding: 6px 18px;
        border-radius: 8px;
        font-size: 0.73rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: none;
        align-items: center;
        gap: 5px;
        border: none;
        outline: none;
        font-family: inherit;
        background: #1e3a5f;
        color: #ffffff;
    }
    .tp-btn-next:hover { background: #2b4c7e; }
    .tp-btn-next:disabled { opacity: 0.35; cursor: not-allowed; }
    .tp-btn-next.visible { display: flex; }

    /* ═══════════════════════════════════════════════════
       RIGHT PANEL (PROFILE)
       ═══════════════════════════════════════════════════ */
    .tp-right {
        width: 240px;
        background: #fafbfd;
        border-left: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
    }
    .tp-profile-header {
        padding: 12px 16px;
        border-bottom: 1px solid #e8ecf2;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .tp-profile-title {
        font-size: 0.75rem;
        font-weight: 600;
        color: #1e3a5f;
    }
    .tp-profile-body {
        flex: 1;
        overflow-y: auto;
        padding: 12px 16px;
    }
    .tp-profile-body::-webkit-scrollbar { width: 3px; }
    .tp-profile-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }

    .tp-profile-empty {
        text-align: center;
        padding: 24px 8px;
        color: #a1a1aa;
        font-size: 0.68rem;
        font-weight: 400;
        line-height: 1.5;
    }

    .tp-profile-item {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        padding: 8px 0;
        border-bottom: 1px solid #f1f5f9;
        animation: tpFadeIn 0.3s ease forwards;
    }
    .tp-profile-item:last-child { border-bottom: none; }
    @keyframes tpFadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .tp-profile-item-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #cbdbe8;
        margin-top: 5px;
        flex-shrink: 0;
    }
    .tp-profile-item-content { flex: 1; min-width: 0; }
    .tp-profile-item-label {
        font-size: 0.62rem;
        color: #a1a1aa;
        font-weight: 400;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .tp-profile-item-value {
        font-size: 0.72rem;
        color: #3b5980;
        font-weight: 500;
        margin-top: 1px;
    }

    .tp-generate-cta {
        padding: 12px 16px;
        border-top: 1px solid #e8ecf2;
    }
    .tp-btn-generate {
        width: 100%;
        padding: 10px;
        border-radius: 8px;
        background: #1e3a5f;
        color: #fff;
        font-size: 0.75rem;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        font-family: inherit;
    }
    .tp-btn-generate:hover { background: #2b4c7e; }
    .tp-btn-generate:disabled { opacity: 0.4; cursor: not-allowed; }

    /* ═══════════════════════════════════════════════════
       LOADING PANEL
       ═══════════════════════════════════════════════════ */
    .tp-loading-panel {
        display: none;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 36px 20px;
        text-align: center;
        flex: 1;
    }
    .tp-loading-panel.active { display: flex; }
    .tp-loading-spinner {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2px solid #e2e8f0;
        border-top-color: #1e3a5f;
        animation: tpSpin 0.8s linear infinite;
        margin-bottom: 16px;
    }
    @keyframes tpSpin { to { transform: rotate(360deg); } }
    .tp-loading-title { font-size: 0.85rem; font-weight: 600; color: #1e3a5f; margin-bottom: 6px; }
    .tp-loading-msg { font-size: 0.72rem; color: #6482a6; transition: opacity 0.3s; min-height: 1em; font-weight: 400; }
    .tp-loading-bar { width: 160px; height: 3px; background: #e2e8f0; border-radius: 3px; margin-top: 16px; overflow: hidden; }
    .tp-loading-bar-fill {
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, #1e3a5f, #6482a6, #1e3a5f);
        background-size: 200% 100%;
        border-radius: 3px;
        animation: tpBarShimmer 1.5s ease infinite;
        transition: width 0.5s ease;
    }
    @keyframes tpBarShimmer {
        0% { background-position: 100% 0; }
        100% { background-position: -100% 0; }
    }

    /* ═══════════════════════════════════════════════════
       RESULT TIMELINE
       ═══════════════════════════════════════════════════ */
    .tp-result-panel {
        display: none;
        flex-direction: column;
        flex: 1;
        overflow: hidden;
    }
    .tp-result-panel.active { display: flex; }
    .tp-result-header {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        flex-shrink: 0;
    }
    .tp-result-title { font-size: 0.88rem; font-weight: 600; color: #1e3a5f; margin-bottom: 3px; }
    .tp-result-summary { font-size: 0.72rem; color: #6482a6; line-height: 1.4; font-weight: 400; }
    .tp-result-body {
        flex: 1;
        overflow-y: auto;
        padding: 16px 20px;
    }
    .tp-result-body::-webkit-scrollbar { width: 3px; }
    .tp-result-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }

    .tp-day-section { margin-bottom: 20px; }
    .tp-day-title {
        font-size: 0.78rem;
        font-weight: 600;
        color: #1e3a5f;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
        padding-bottom: 6px;
        border-bottom: 1px solid #e8ecf2;
    }
    .tp-day-badge {
        background: #1e3a5f;
        color: #fff;
        font-size: 0.58rem;
        font-weight: 600;
        padding: 2px 6px;
        border-radius: 4px;
        letter-spacing: 0.3px;
    }
    .tp-slot {
        display: flex;
        gap: 10px;
        margin-bottom: 6px;
        padding: 8px 10px;
        border-radius: 8px;
        background: #fafbfd;
        border: 1px solid #f1f5f9;
        transition: all 0.15s;
        animation: tpFadeIn 0.3s ease forwards;
    }
    .tp-slot:hover { background: #f0f5fa; border-color: #cbdbe8; }
    .tp-slot-time { font-size: 0.65rem; font-weight: 600; color: #1e3a5f; min-width: 36px; padding-top: 1px; flex-shrink: 0; }
    .tp-slot-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-top: 3px;
        flex-shrink: 0;
    }
    .tp-slot-dot.visit { background: #cbdbe8; }
    .tp-slot-dot.food { background: #fcd34d; }
    .tp-slot-dot.transport { background: #86efac; }
    .tp-slot-dot.rest { background: #c4b5fd; }
    .tp-slot-dot.photo { background: #f9a8d4; }
    .tp-slot-content { flex: 1; min-width: 0; }
    .tp-slot-activity { font-size: 0.72rem; font-weight: 500; color: #3b5980; line-height: 1.3; }
    .tp-slot-location { font-size: 0.65rem; color: #1e3a5f; margin-top: 2px; cursor: pointer; font-weight: 500; }
    .tp-slot-location:hover { text-decoration: underline; }
    .tp-slot-tip { font-size: 0.62rem; color: #a1a1aa; margin-top: 2px; font-style: italic; font-weight: 400; }

    .tp-tips-section {
        background: #fafbfd;
        border: 1px solid #e8ecf2;
        border-radius: 8px;
        padding: 12px 14px;
        margin-top: 8px;
    }
    .tp-tips-title { font-size: 0.72rem; font-weight: 600; color: #3b5980; margin-bottom: 6px; }
    .tp-tips-list { list-style: none; padding: 0; margin: 0; }
    .tp-tips-list li { font-size: 0.68rem; color: #6482a6; padding: 2px 0 2px 10px; position: relative; font-weight: 400; }
    .tp-tips-list li::before { content: '·'; position: absolute; left: 0; font-weight: 700; color: #cbdbe8; }

    .tp-cost-badge {
        margin-top: 10px;
        background: #f0fdf4;
        border: 1px solid #d1fae5;
        border-radius: 8px;
        padding: 8px 12px;
        display: flex;
        align-items: center;
    }
    .tp-cost-label { font-size: 0.68rem; color: #3b5980; font-weight: 500; }
    .tp-cost-value { font-size: 0.72rem; color: #166534; font-weight: 600; margin-left: auto; }

    .tp-result-footer {
        padding: 10px 20px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        gap: 8px;
        flex-shrink: 0;
    }
    .tp-btn-new {
        flex: 1;
        padding: 8px;
        border-radius: 8px;
        background: #f1f5f9;
        color: #6482a6;
        font-size: 0.72rem;
        font-weight: 500;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.15s;
        font-family: inherit;
    }
    .tp-btn-new:hover { background: #e2e8f0; color: #3b5980; }

    .tp-raw-result {
        white-space: pre-wrap;
        font-size: 0.72rem;
        color: #3b5980;
        line-height: 1.55;
        padding: 16px 20px;
        font-weight: 400;
    }

    /* ═══════════════════════════════════════════════════
       RESPONSIVE
       ═══════════════════════════════════════════════════ */
    @media (max-width: 768px) {
        .tp-container {
            width: 100vw; height: 100vh;
            max-width: 100%; max-height: 100%;
            border-radius: 0;
            flex-direction: column;
        }
        .tp-right {
            width: 100%; height: auto; max-height: 130px;
            border-left: none; border-top: 1px solid #f1f5f9; order: -1;
        }
        .tp-profile-body { padding: 8px 16px; display: flex; flex-wrap: wrap; gap: 4px; }
        .tp-profile-item { padding: 4px 0; border-bottom: none; flex: 0 0 auto; gap: 5px; }
        .tp-generate-cta { padding: 8px 16px; }
        .tp-card-grid.cols-4, .tp-card-grid.cols-3 { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 480px) {
        .tp-card-grid.cols-2 { grid-template-columns: 1fr; }
        .tp-right { max-height: 110px; }
    }
</style>

<!-- ═══════════════════════════════════════════════════
     HTML
     ═══════════════════════════════════════════════════ -->
<div id="trip-planner-overlay">
    <div class="tp-backdrop" id="tp-backdrop"></div>
    <div class="tp-container">
        <div class="tp-left">
            <div class="tp-header">
                <div class="tp-header-left">
                    <div>
                        <div class="tp-header-title">Lên lịch trình</div>
                        <div class="tp-header-subtitle">AI hỏi, bạn chọn — lịch trình tự sinh</div>
                    </div>
                </div>
                <button class="tp-close-btn" id="tp-close-btn" title="Đóng">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                </button>
            </div>
            <div class="tp-progress" id="tp-progress"></div>
            <div class="tp-wizard-body" id="tp-wizard-body"></div>

            <div class="tp-loading-panel" id="tp-loading-panel">
                <div class="tp-loading-spinner"></div>
                <div class="tp-loading-title">Đang tạo lịch trình</div>
                <div class="tp-loading-msg" id="tp-loading-msg">Đang phân tích thông tin...</div>
                <div class="tp-loading-bar"><div class="tp-loading-bar-fill" id="tp-loading-bar-fill"></div></div>
            </div>

            <div class="tp-result-panel" id="tp-result-panel">
                <div class="tp-result-header">
                    <div class="tp-result-title" id="tp-result-title"></div>
                    <div class="tp-result-summary" id="tp-result-summary"></div>
                </div>
                <div class="tp-result-body" id="tp-result-body"></div>
                <div class="tp-result-footer">
                    <button class="tp-btn-new" id="tp-btn-restart">Lên lịch mới</button>
                    <button class="tp-btn-new" id="tp-btn-close-result">Đóng</button>
                </div>
            </div>

            <div class="tp-footer" id="tp-footer">
                <button class="tp-btn tp-btn-back" id="tp-btn-back" disabled>
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
                    Quay lại
                </button>
                <span class="tp-multi-hint" id="tp-multi-hint" style="display:none">Có thể chọn nhiều</span>
                <button class="tp-btn tp-btn-next" id="tp-btn-next" disabled>
                    Tiếp theo
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg>
                </button>
            </div>
        </div>

        <div class="tp-right">
            <div class="tp-profile-header">
                <div class="tp-profile-title">Hồ sơ chuyến đi</div>
            </div>
            <div class="tp-profile-body" id="tp-profile-body">
                <div class="tp-profile-empty">Chọn các thẻ bên trái để xây dựng hồ sơ</div>
            </div>
            <div class="tp-generate-cta">
                <button class="tp-btn-generate" id="tp-btn-generate" disabled>Tạo lịch trình</button>
            </div>
        </div>
    </div>
</div>

<script>
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
    const resultTitle = document.getElementById('tp-result-title');
    const resultSummary = document.getElementById('tp-result-summary');
    const resultBody = document.getElementById('tp-result-body');
    const restartBtn = document.getElementById('tp-btn-restart');
    const closeResultBtn = document.getElementById('tp-btn-close-result');

    window.openTripPlanner = function(forceNew = false) {
        console.log('openTripPlanner triggered');
        const el = document.getElementById('trip-planner-overlay');
        if (el) {
            el.style.display = 'flex';
            el.classList.add('active');
            setTimeout(() => el.classList.add('visible'), 20);
        }

        if (!forceNew) {
            const saved = localStorage.getItem('nb_saved_itinerary');
            if (saved) {
                try {
                    const parsed = JSON.parse(saved);
                    if (parsed && (parsed.days || parsed.title)) {
                        wizardBody.style.display = 'none';
                        footer.style.display = 'none';
                        loadingPanel.classList.remove('active');
                        renderItinerary(parsed, false);
                        return;
                    }
                } catch (e) {
                    console.warn('Could not parse saved itinerary:', e);
                }
            }
        }

        resetState();
        renderTripTypeStep();
    };

    function closePlanner() {
        const el = document.getElementById('trip-planner-overlay');
        if (el) {
            el.classList.remove('visible');
            setTimeout(() => {
                el.classList.remove('active');
                el.style.display = 'none';
            }, 350);
        }
    }

    closeBtn.addEventListener('click', closePlanner);
    backdrop.addEventListener('click', closePlanner);
    closeResultBtn.addEventListener('click', closePlanner);

    restartBtn.addEventListener('click', () => {
        localStorage.removeItem('nb_saved_itinerary');
        resultPanel.classList.remove('active');
        loadingPanel.classList.remove('active');
        wizardBody.style.display = '';
        footer.style.display = '';
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
        wizardBody.style.display = '';
        footer.style.display = '';
        loadingPanel.classList.remove('active');
        resultPanel.classList.remove('active');
        generateBtn.disabled = true;
        nextBtn.classList.remove('visible');
        multiHint.style.display = 'none';
        updateProfile();
    }

    const DEFAULT_STEPS = [
        {
            key: 'who',
            greeting: 'Thật tuyệt! Giúp mình hiểu thêm về đoàn của bạn nhé.',
            question: 'Bạn dự định đi cùng ai?',
            type: 'single',
            options: [
                { value: 'mot_minh', label: 'Đi một mình' },
                { value: 'doi_lua', label: 'Đôi lứa / Couple' },
                { value: 'nhom_ban', label: 'Nhóm bạn' },
                { value: 'gia_dinh_tre_nho', label: 'Gia đình có trẻ nhỏ' },
                { value: 'gia_dinh_nguoi_lon', label: 'Gia đình có người lớn tuổi' },
                { value: 'other', label: 'Khác...' }
            ]
        },
        {
            key: 'transport',
            greeting: 'Đã ghi nhận! Tiếp theo là phương tiện di chuyển.',
            question: 'Bạn sẽ di chuyển bằng phương tiện gì?',
            type: 'single',
            options: [
                { value: 'xe_may', label: 'Xe máy' },
                { value: 'o_to_rieng', label: 'Ô tô riêng / Tự lái' },
                { value: 'limousine', label: 'Xe Limousine / Xe khách' },
                { value: 'tau_hoa', label: 'Tàu hỏa' },
                { value: 'other', label: 'Khác...' }
            ]
        },
        {
            key: 'duration_hotel',
            greeting: 'Chuẩn bị xong phương tiện rồi!',
            question: 'Bạn dự định đi trong bao lâu và có cần khách sạn không?',
            type: 'single',
            options: [
                { value: '1_day', label: 'Đi 1 ngày (Không ở lại)' },
                { value: '2d1n_hotel', label: '2 ngày 1 đêm (Cần khách sạn)' },
                { value: '3d2n_hotel', label: '3 ngày 2 đêm (Cần khách sạn)' },
                { value: 'other', label: 'Khác...' }
            ]
        },
        {
            key: 'budget',
            greeting: 'Rất rõ ràng!',
            question: 'Mức ngân sách / chi phí dự kiến cho mỗi người là bao nhiêu?',
            type: 'single',
            options: [
                { value: 'tiet_kiem', label: 'Tiết kiệm (Dưới 1 triệu)' },
                { value: 'tieu_chuan', label: 'Tiêu chuẩn (1 - 2.5 triệu)' },
                { value: 'cao_cap', label: 'Thoải mái / Cao cấp (> 2.5 triệu)' },
                { value: 'other', label: 'Khác...' }
            ]
        }
    ];

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
                    renderDefaultStep(0);
                }, 250);
            });
        });
    }

    function renderDefaultStep(idx) {
        if (idx < DEFAULT_STEPS.length) {
            currentAiQuestion = DEFAULT_STEPS[idx];
            renderAiQuestion(DEFAULT_STEPS[idx]);
        } else {
            askAiNextQuestion();
        }
    }

    /* ─── AI Question Flow ─── */
    function askAiNextQuestion() {
        isLoading = true;
        showThinking();
        const fullAnswers = [
            { question: 'Kiểu chuyến đi', answer: tripTypeLabel },
            ...aiAnswers
        ];
        fetch('{{ route("client.trip_planner.next_question") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ answers: fullAnswers, step: currentStep }),
        })
        .then(res => res.json())
        .then(data => {
            isLoading = false;
            if (data.success && data.data) {
                if (data.data.done) {
                    aiDone = true;
                    renderDoneStep(data.data.greeting || 'Đã đủ thông tin.');
                } else {
                    currentAiQuestion = data.data;
                    renderAiQuestion(data.data);
                }
            } else {
                renderInlineError(data.error || 'AI không phản hồi.');
            }
        })
        .catch(() => {
            isLoading = false;
            renderInlineError('Không thể kết nối. Thử lại nhé.');
        });
    }

    function showThinking() {
        backBtn.disabled = true;
        nextBtn.classList.remove('visible');
        multiHint.style.display = 'none';
        wizardBody.innerHTML = `
            <div class="tp-thinking">
                <div class="tp-thinking-dots">
                    <div class="tp-thinking-dot"></div>
                    <div class="tp-thinking-dot"></div>
                    <div class="tp-thinking-dot"></div>
                </div>
                <div class="tp-thinking-text">AI đang suy nghĩ...</div>
            </div>`;
        updateProgress();
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
                    if (currentAiQuestion) advanceFromAiQuestion(currentAiQuestion);
                }
            });
        }

        wizardBody.querySelectorAll('.tp-card').forEach(card => {
            card.addEventListener('click', () => handleAiCardClick(card, q));
        });
    }

    function handleAiCardClick(card, q) {
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
            setTimeout(() => advanceFromAiQuestion(q), 250);
        }
    }

    function advanceFromAiQuestion(q) {
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

        aiAnswers.push({ question: q.question, answer: answerText });
        stepHistory.push({
            step: currentStep,
            question: q,
            selection: JSON.parse(JSON.stringify(currentSelection)),
            answersSnapshot: JSON.parse(JSON.stringify(aiAnswers)),
        });
        updateProfile();
        currentStep++;

        if (defaultStepIndex < DEFAULT_STEPS.length - 1) {
            defaultStepIndex++;
            renderDefaultStep(defaultStepIndex);
        } else {
            defaultStepIndex = DEFAULT_STEPS.length;
            askAiNextQuestion();
        }
    }

    nextBtn.addEventListener('click', () => {
        if (!currentAiQuestion || nextBtn.disabled) return;
        advanceFromAiQuestion(currentAiQuestion);
    });

    backBtn.addEventListener('click', () => {
        if (stepHistory.length === 0) return;
        const prev = stepHistory.pop();
        if (prev.step === 0) {
            currentStep = 0; tripType = ''; tripTypeLabel = '';
            aiAnswers = []; aiDone = false; currentAiQuestion = null;
            defaultStepIndex = 0;
            updateProfile(); renderTripTypeStep();
        } else {
            currentStep = prev.step;
            aiAnswers = prev.answersSnapshot ? prev.answersSnapshot.slice(0, -1) : [];
            aiDone = false; currentAiQuestion = prev.question; generateBtn.disabled = true;
            if (currentStep <= DEFAULT_STEPS.length) {
                defaultStepIndex = currentStep - 1;
                updateProfile();
                renderDefaultStep(defaultStepIndex);
            } else {
                updateProfile();
                renderAiQuestion(prev.question);
            }
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

    function renderInlineError(msg) {
        backBtn.disabled = stepHistory.length === 0;
        wizardBody.innerHTML = `
            <div class="tp-step" style="text-align: center; padding: 32px 0;">
                <div style="font-size: 0.78rem; color: #6482a6; margin-bottom: 14px;">${msg}</div>
                <button class="tp-btn" id="tp-btn-retry"
                    style="margin: 0 auto; background: #1e3a5f; color: #fff; padding: 6px 16px; border-radius: 8px; cursor: pointer; font-size: 0.72rem;">
                    Thử lại
                </button>
            </div>`;
        document.getElementById('tp-btn-retry').addEventListener('click', () => askAiNextQuestion());
    }

    function updateProgress() {
        const total = Math.max(currentStep + 1, stepHistory.length + 1);
        let html = '';
        for (let i = 0; i <= total && i < 10; i++) {
            let cls = 'tp-progress-dot';
            if (i < currentStep) cls += ' done';
            else if (i === currentStep) cls += ' active';
            html += `<div class="${cls}"></div>`;
        }
        html += `<span class="tp-progress-label">${currentStep === 0 ? '' : 'Bước ' + currentStep}</span>`;
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
        'Đang tìm địa điểm phù hợp...',
        'Đang sắp xếp lịch trình...',
        'Đang tối ưu di chuyển...',
        'Gần xong rồi...',
    ];

    function startGeneration() {
        isLoading = true;
        wizardBody.style.display = 'none';
        footer.style.display = 'none';
        loadingPanel.classList.add('active');
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

        const fullAnswers = [{ question: 'Kiểu chuyến đi', answer: tripTypeLabel }, ...aiAnswers];

        fetch('{{ route("client.trip_planner.generate") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ answers: fullAnswers, trip_type: tripTypeLabel }),
        })
        .then(res => res.json())
        .then(data => {
            clearInterval(msgInterval); clearInterval(barInterval);
            loadingBarFill.style.width = '100%';
            setTimeout(() => {
                loadingPanel.classList.remove('active'); isLoading = false;
                if (data.success) {
                    if (data.itinerary) renderItinerary(data.itinerary);
                    else if (data.raw) renderRawResult(data.raw);
                } else renderError(data.error || 'Có lỗi xảy ra.');
            }, 500);
        })
        .catch(() => {
            clearInterval(msgInterval); clearInterval(barInterval);
            loadingPanel.classList.remove('active'); isLoading = false;
            renderError('Không thể kết nối máy chủ.');
        });
    }

    function renderItinerary(data, saveToStorage = true) {
        if (saveToStorage) {
            try {
                localStorage.setItem('nb_saved_itinerary', JSON.stringify(data));
            } catch (e) {
                console.warn('Could not save itinerary to localStorage:', e);
            }
        }
        resultPanel.classList.add('active');
        resultTitle.textContent = data.title || 'Lịch trình du lịch Ninh Bình';
        resultSummary.textContent = data.summary || '';
        let html = '';
        if (data.days && data.days.length > 0) {
            data.days.forEach(day => {
                html += `<div class="tp-day-section"><div class="tp-day-title"><span class="tp-day-badge">NGÀY ${day.day}</span>${day.title || ''}</div>`;
                (day.slots || []).forEach(slot => {
                    const cls = slot.type || 'visit';
                    html += `<div class="tp-slot">
                        <div class="tp-slot-time">${slot.time || ''}</div>
                        <div class="tp-slot-dot ${cls}"></div>
                        <div class="tp-slot-content">
                            <div class="tp-slot-activity">${slot.activity || ''}</div>
                            ${slot.location ? `<div class="tp-slot-location" ${slot.location_id ? `onclick="window.zoomToLocationFromChat('${slot.location_id}')"` : ''}>${slot.location}</div>` : ''}
                            ${slot.tip ? `<div class="tp-slot-tip">${slot.tip}</div>` : ''}
                        </div>
                    </div>`;
                });
                html += '</div>';
            });
        }
        if (data.tips && data.tips.length > 0) {
            html += `<div class="tp-tips-section"><div class="tp-tips-title">Lưu ý</div><ul class="tp-tips-list">${data.tips.map(t => `<li>${t}</li>`).join('')}</ul></div>`;
        }
        if (data.estimated_cost) {
            html += `<div class="tp-cost-badge"><span class="tp-cost-label">Chi phí ước tính</span><span class="tp-cost-value">${data.estimated_cost}</span></div>`;
        }
        resultBody.innerHTML = html;
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
        resultTitle.textContent = 'Lịch trình du lịch Ninh Bình';
        resultSummary.textContent = '';
        let f = strContent.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
        resultBody.innerHTML = `<div class="tp-raw-result">${f}</div>`;
    }

    function renderError(msg) {
        resultPanel.classList.add('active');
        resultTitle.textContent = 'Không thể tạo lịch trình';
        resultSummary.textContent = msg;
        resultBody.innerHTML = `<div style="text-align:center; padding:32px 16px; color:#a1a1aa; font-size:0.75rem;">
            <p>${msg}</p><p style="margin-top:6px; font-size:0.68rem;">Bấm "Lên lịch mới" để thử lại</p></div>`;
    }
});
</script>
