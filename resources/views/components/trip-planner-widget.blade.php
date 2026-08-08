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
    #trip-planner-overlay.minimizing {
        pointer-events: none;
        opacity: 1;
        transition: none;
    }
    #trip-planner-overlay.minimizing .tp-backdrop {
        opacity: 0;
        transition: opacity 0.35s ease;
    }

    .tp-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        transition: opacity 0.35s ease;
    }

    .tp-container {
        position: relative;
        width: min(96vw, 1040px);
        height: min(90vh, 720px);
        max-width: 1040px;
        max-height: 720px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 20px 56px rgba(0, 0, 0, 0.16);
        display: flex;
        overflow: hidden;
        transform: scale(0.92) translateY(20px);
        opacity: 1;
        transform-origin: center center;
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.35s ease, border-radius 0.4s ease;
        will-change: transform, opacity;
    }
    #trip-planner-overlay.visible .tp-container {
        transform: scale(1) translateY(0);
    }
    #trip-planner-overlay.minimizing .tp-container {
        /* transform set via JS toward dock button */
        transition: transform 0.5s cubic-bezier(0.45, 0.05, 0.55, 0.95), opacity 0.4s ease 0.08s, border-radius 0.45s ease;
    }
    /* Khi xem kết quả: ẩn cột hồ sơ trống, timeline full-width */
    .tp-container.tp-mode-result .tp-right {
        display: none;
    }
    .tp-container.tp-mode-result .tp-progress,
    .tp-container.tp-mode-result .tp-footer {
        display: none !important;
    }
    .tp-container.tp-mode-loading .tp-right {
        opacity: 0.55;
        pointer-events: none;
    }

    @keyframes tpDockPulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(2, 132, 199, 0); }
        35% { transform: scale(1.1); box-shadow: 0 0 0 6px rgba(2, 132, 199, 0.28); background: #0284c7; color: #fff; border-color: #0284c7; }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(2, 132, 199, 0); }
    }
    #randomFlyBtn.tp-dock-pulse {
        animation: tpDockPulse 0.65s ease;
    }
    #randomFlyBtn.tp-dock-pulse .material-symbols-rounded {
        color: #fff !important;
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
        padding: 16px 28px;
        border-bottom: 1px solid #e8ecf2;
        flex-shrink: 0;
    }
    .tp-header-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .tp-header-title {
        font-size: 1rem;
        font-weight: 600;
        color: #1e3a5f;
        letter-spacing: -0.02em;
    }
    .tp-header-subtitle {
        font-size: 0.78rem;
        color: #6482a6;
        font-weight: 400;
        margin-top: 2px;
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
        padding: 24px 28px;
        position: relative;
    }
    .tp-wizard-body::-webkit-scrollbar { width: 5px; }
    .tp-wizard-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

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
        border-radius: 8px;
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
        display: block;
        font-size: 0.72rem;
        font-weight: 500;
        color: #3b5980;
        line-height: 1.25;
    }
    .tp-card-desc {
        display: block;
        font-size: 0.62rem;
        color: #a1a1aa;
        margin-top: 3px;
        line-height: 1.3;
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
        padding: 14px 28px;
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
        width: 280px;
        background: #f7f9fc;
        border-left: 1px solid #eef2f7;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
    }
    .tp-profile-header {
        padding: 16px 20px;
        border-bottom: 1px solid #e8ecf2;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .tp-profile-title {
        font-size: 0.82rem;
        font-weight: 600;
        color: #1e3a5f;
    }
    .tp-profile-body {
        flex: 1;
        overflow-y: auto;
        padding: 16px 20px;
    }
    .tp-profile-body::-webkit-scrollbar { width: 4px; }
    .tp-profile-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }

    .tp-profile-empty {
        text-align: center;
        padding: 36px 12px;
        color: #94a3b8;
        font-size: 0.78rem;
        font-weight: 400;
        line-height: 1.6;
    }

    .tp-profile-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 10px 0;
        border-bottom: 1px solid #eef2f7;
        animation: tpFadeIn 0.3s ease forwards;
    }
    .tp-profile-item:last-child { border-bottom: none; }
    @keyframes tpFadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .tp-profile-item-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #94b4d0;
        margin-top: 6px;
        flex-shrink: 0;
    }
    .tp-profile-item-content { flex: 1; min-width: 0; }
    .tp-profile-item-label {
        font-size: 0.68rem;
        color: #94a3b8;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.35px;
    }
    .tp-profile-item-value {
        font-size: 0.82rem;
        color: #334155;
        font-weight: 500;
        margin-top: 3px;
        line-height: 1.4;
    }

    .tp-generate-cta {
        padding: 16px 20px;
        border-top: 1px solid #e8ecf2;
    }
    .tp-btn-generate {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        background: #1e3a5f;
        color: #fff;
        font-size: 0.82rem;
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
        background: #fbfcfe;
    }
    .tp-result-panel.active { display: flex; }
    .tp-result-header {
        padding: 22px 32px 18px;
        border-bottom: 1px solid #eef2f7;
        flex-shrink: 0;
        background: #fff;
    }
    .tp-result-title {
        font-size: 1.15rem;
        font-weight: 600;
        color: #0f2744;
        margin-bottom: 8px;
        letter-spacing: -0.02em;
        line-height: 1.35;
    }
    .tp-result-summary {
        font-size: 0.88rem;
        color: #64748b;
        line-height: 1.55;
        font-weight: 400;
        max-width: 52rem;
    }
    .tp-result-body {
        flex: 1;
        overflow-y: auto;
        padding: 22px 32px 28px;
    }
    .tp-result-body::-webkit-scrollbar { width: 6px; }
    .tp-result-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 6px; }

    .tp-day-section { margin-bottom: 28px; }
    .tp-day-section:last-child { margin-bottom: 8px; }
    .tp-day-title {
        font-size: 0.92rem;
        font-weight: 600;
        color: #1e3a5f;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e8ecf2;
    }
    .tp-day-badge {
        background: #1e3a5f;
        color: #fff;
        font-size: 0.68rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        letter-spacing: 0.4px;
    }
    .tp-slot {
        display: flex;
        gap: 14px;
        margin-bottom: 12px;
        padding: 14px 16px;
        border-radius: 8px;
        background: #ffffff;
        border: 1px solid #e8eef5;
        box-shadow: 0 1px 2px rgba(15, 39, 68, 0.03);
        transition: all 0.15s;
        animation: tpFadeIn 0.3s ease forwards;
    }
    .tp-slot:hover {
        background: #f8fbff;
        border-color: #c5d7ea;
        box-shadow: 0 4px 12px rgba(15, 39, 68, 0.05);
    }
    .tp-slot-time {
        font-size: 0.78rem;
        font-weight: 600;
        color: #1e3a5f;
        min-width: 7.2rem;
        padding-top: 2px;
        flex-shrink: 0;
        letter-spacing: -0.01em;
        line-height: 1.35;
    }
    .tp-slot-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-top: 5px;
        flex-shrink: 0;
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.06);
    }
    .tp-slot-dot.visit { background: #94a3b8; }
    .tp-slot-dot.food { background: #f59e0b; }
    .tp-slot-dot.transport { background: #22c55e; }
    .tp-slot-dot.rest { background: #8b5cf6; }
    .tp-slot-dot.photo { background: #ec4899; }
    .tp-slot-content { flex: 1; min-width: 0; }
    .tp-slot-activity {
        font-size: 0.9rem;
        font-weight: 500;
        color: #334155;
        line-height: 1.55;
    }
    .tp-slot-location {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.8rem;
        color: #0369a1;
        margin-top: 8px;
        cursor: pointer;
        font-weight: 600;
        text-decoration: underline;
        text-underline-offset: 2px;
    }
    .tp-slot-location:hover { color: #0284c7; }
    .tp-slot-tip {
        font-size: 0.78rem;
        color: #94a3b8;
        margin-top: 8px;
        font-style: italic;
        font-weight: 400;
        line-height: 1.45;
    }
    .tp-slot-distance {
        font-size: 0.72rem;
        color: #64748b;
        margin-top: 6px;
        font-weight: 500;
    }

    .tp-tips-section {
        background: #fff;
        border: 1px solid #e8eef5;
        border-radius: 8px;
        padding: 16px 18px;
        margin-top: 8px;
    }
    .tp-tips-title { font-size: 0.82rem; font-weight: 600; color: #1e3a5f; margin-bottom: 10px; }
    .tp-tips-list { list-style: none; padding: 0; margin: 0; }
    .tp-tips-list li {
        font-size: 0.82rem;
        color: #64748b;
        padding: 6px 0 6px 14px;
        position: relative;
        font-weight: 400;
        line-height: 1.5;
    }
    .tp-tips-list li::before { content: '·'; position: absolute; left: 0; font-weight: 700; color: #94a3b8; }

    .tp-cost-badge {
        margin-top: 14px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 8px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .tp-cost-label { font-size: 0.8rem; color: #3b5980; font-weight: 500; }
    .tp-cost-value { font-size: 0.9rem; color: #166534; font-weight: 600; margin-left: auto; }

    .tp-result-footer {
        padding: 14px 32px 18px;
        border-top: 1px solid #eef2f7;
        display: flex;
        gap: 12px;
        flex-shrink: 0;
        flex-wrap: wrap;
        background: #fff;
    }
    .tp-btn-new {
        flex: 1;
        min-width: 110px;
        padding: 11px 14px;
        border-radius: 8px;
        background: #f8fafc;
        color: #475569;
        font-size: 0.82rem;
        font-weight: 500;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.15s;
        font-family: inherit;
    }
    .tp-btn-new:hover { background: #e2e8f0; color: #1e3a5f; }
    .tp-btn-save {
        flex: 1.5;
        min-width: 140px;
        padding: 11px 14px;
        border-radius: 8px;
        background: #1e3a5f;
        color: #fff;
        font-size: 0.82rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.15s;
        font-family: inherit;
    }
    .tp-btn-save:hover { background: #2b4c7e; }
    .tp-btn-save:disabled { opacity: 0.55; cursor: not-allowed; }
    .tp-btn-save.saved { background: #166534; }

    .tp-raw-result {
        white-space: pre-wrap;
        font-size: 0.85rem;
        color: #334155;
        line-height: 1.6;
        padding: 20px 28px;
        font-weight: 400;
    }

    /* ═══════════════════════════════════════════════════
       RESPONSIVE
       ═══════════════════════════════════════════════════ */
    @media (max-width: 900px) {
        .tp-container {
            width: 96vw;
            height: min(92vh, 700px);
        }
        .tp-right { width: 240px; }
        .tp-result-header,
        .tp-result-body,
        .tp-result-footer { padding-left: 22px; padding-right: 22px; }
    }
    @media (max-width: 768px) {
        .tp-container {
            width: 100vw; height: 100vh;
            max-width: 100%; max-height: 100%;
            border-radius: 0;
            flex-direction: column;
        }
        .tp-right {
            width: 100%; height: auto; max-height: 140px;
            border-left: none; border-top: 1px solid #f1f5f9; order: -1;
        }
        .tp-container.tp-mode-result .tp-right { display: none; }
        .tp-profile-body { padding: 10px 16px; display: flex; flex-wrap: wrap; gap: 4px; }
        .tp-profile-item { padding: 4px 0; border-bottom: none; flex: 0 0 auto; gap: 5px; }
        .tp-generate-cta { padding: 10px 16px; }
        .tp-card-grid.cols-4, .tp-card-grid.cols-3 { grid-template-columns: repeat(2, 1fr); }
        .tp-slot { flex-wrap: wrap; }
        .tp-slot-time { min-width: 100%; margin-bottom: 2px; }
        .tp-header { padding: 14px 18px; }
        .tp-result-header { padding: 16px 18px; }
        .tp-result-body { padding: 16px 18px 22px; }
        .tp-result-footer { padding: 12px 18px 16px; }
    }
    @media (max-width: 480px) {
        .tp-card-grid.cols-2 { grid-template-columns: 1fr; }
        .tp-right { max-height: 120px; }
        .tp-result-title { font-size: 1rem; }
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
                        <div class="tp-header-subtitle">Chọn vài bước — AI sinh lịch trình</div>
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
                    <button class="tp-btn-save" id="tp-btn-save" type="button">Lưu vào trang cá nhân</button>
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
    let currentItinerary = null;
    let lastAnswersPayload = [];
    const IS_AUTHENTICATED = @json(auth()->check());
    const CURRENT_USER_ID = @json(auth()->id());
    const LOGIN_URL = @json(route('login'));
    const PROFILE_URL = @json(route('client.profile'));

    // Bản nháp chỉ giữ trong tab hiện tại + theo user — không dùng chung localStorage mãi
    const TP_DRAFT_KEY = 'nb_trip_draft_' + (CURRENT_USER_ID ? ('u' + CURRENT_USER_ID) : 'guest');
    const TP_LEGACY_KEYS = ['nb_saved_itinerary', 'nb_trip_draft_guest'];

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
    const resultTitle = document.getElementById('tp-result-title');
    const resultSummary = document.getElementById('tp-result-summary');
    const resultBody = document.getElementById('tp-result-body');
    const restartBtn = document.getElementById('tp-btn-restart');
    const closeResultBtn = document.getElementById('tp-btn-close-result');
    const saveBtn = document.getElementById('tp-btn-save');
    const tpContainer = document.querySelector('#trip-planner-overlay .tp-container');

    function setPlannerMode(mode) {
        if (!tpContainer) return;
        tpContainer.classList.remove('tp-mode-result', 'tp-mode-loading', 'tp-mode-wizard');
        tpContainer.classList.add(mode === 'result' ? 'tp-mode-result' : (mode === 'loading' ? 'tp-mode-loading' : 'tp-mode-wizard'));
    }

    window.openTripPlanner = function(forceNew = false) {
        console.log('openTripPlanner triggered');
        const el = document.getElementById('trip-planner-overlay');
        const container = el ? el.querySelector('.tp-container') : null;
        const btn = document.getElementById('randomFlyBtn');

        if (el) {
            el.style.display = 'flex';
            el.classList.add('active');
            el.classList.remove('minimizing');

            // Mở bung ra từ nút dock (ngược hiệu ứng thu)
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
        activeSteps = [];
        wizardBody.style.display = '';
        footer.style.display = '';
        loadingPanel.classList.remove('active');
        resultPanel.classList.remove('active');
        setPlannerMode('wizard');
        generateBtn.disabled = true;
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Lưu vào trang cá nhân';
            saveBtn.classList.remove('saved');
        }
        nextBtn.classList.remove('visible');
        multiHint.style.display = 'none';
        updateProfile();
    }

    function getWizardSteps(type) {
        const label = tripTypeLabel || 'chuyến đi';
        const sharedWho = {
            key: 'who',
            greeting: `Tuyệt — kiểu ${label} rất hợp! Cho mình biết thêm về đoàn của bạn.`,
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
        };

        const sharedTransport = {
            key: 'transport',
            greeting: 'Tiếp theo là cách di chuyển trong chuyến đi.',
            question: 'Bạn sẽ di chuyển bằng phương tiện gì?',
            type: 'single',
            options: [
                { value: 'xe_may', label: 'Xe máy' },
                { value: 'o_to_rieng', label: 'Ô tô riêng / Tự lái' },
                { value: 'limousine', label: 'Xe Limousine / Xe khách' },
                { value: 'tau_hoa', label: 'Tàu hỏa' },
                { value: 'other', label: 'Khác...' }
            ]
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

        const sharedBudget = {
            key: 'budget',
            greeting: 'Ngân sách giúp mình chọn điểm & quán phù hợp hơn.',
            question: 'Mức ngân sách / chi phí dự kiến cho mỗi người là bao nhiêu?',
            type: 'single',
            options: [
                { value: 'tiet_kiem', label: 'Tiết kiệm (Dưới 1 triệu)' },
                { value: 'tieu_chuan', label: 'Tiêu chuẩn (1 - 2.5 triệu)' },
                { value: 'cao_cap', label: 'Thoải mái / Cao cấp (> 2.5 triệu)' },
                { value: 'other', label: 'Khác...' }
            ]
        };

        // Per trip-type personalized blocks
        const byType = {
            spiritual: {
                who: {
                    ...sharedWho,
                    greeting: 'Hành trình tâm linh thường đi theo nhóm hoặc gia đình — bạn đi cùng ai?',
                    options: [
                        { value: 'gia_dinh_nguoi_lon', label: 'Gia đình có người lớn tuổi' },
                        { value: 'gia_dinh_tre_nho', label: 'Gia đình có trẻ nhỏ' },
                        { value: 'nhom_ban', label: 'Nhóm bạn / Đồng hương' },
                        { value: 'mot_minh', label: 'Đi một mình' },
                        { value: 'doi_lua', label: 'Đôi lứa' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
                transport: {
                    ...sharedTransport,
                    greeting: 'Đi lễ thường cần di chuyển ổn định, ít mệt.',
                    options: [
                        { value: 'o_to_rieng', label: 'Ô tô riêng / Tự lái' },
                        { value: 'limousine', label: 'Xe khách / Limousine' },
                        { value: 'xe_may', label: 'Xe máy' },
                        { value: 'tau_hoa', label: 'Tàu hỏa' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
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
                        { value: 'am_thuc', label: 'Ăn chay / Quán thanh đạm' },
                        { value: 'thien_nhien', label: 'Cảnh quan thiên nhiên quanh điểm lễ' },
                        { value: 'check_in', label: 'Chụp ảnh kỷ niệm' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
                food: {
                    key: 'food',
                    greeting: 'Ăn uống trên hành trình tâm linh thường thiên về thanh đạm.',
                    question: 'Bạn muốn ăn uống theo hướng nào?',
                    type: 'single',
                    options: [
                        { value: 'chay', label: 'Đồ chay / Thanh đạm' },
                        { value: 'dac_san', label: 'Đặc sản địa phương' },
                        { value: 'quan_binh_dan', label: 'Quán bình dân gần điểm lễ' },
                        { value: 'linh_hoat', label: 'Linh hoạt' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
                focus: {
                    key: 'focus',
                    greeting: 'Gần xong rồi!',
                    question: 'Điều quan trọng nhất trong chuyến tâm linh này?',
                    type: 'single',
                    options: [
                        { value: 'trai_nghiem_sau', label: 'Không khí trang nghiêm, tĩnh tâm' },
                        { value: 'it_di_chuyen', label: 'Ít di chuyển, đỡ mệt' },
                        { value: 'gia_dinh_vui', label: 'Thuận tiện cho cả gia đình' },
                        { value: 'tiet_kiem_tg', label: 'Sắp xếp giờ lễ hợp lý' },
                        { value: 'anh_dep', label: 'Có ảnh kỷ niệm đẹp' },
                        { value: 'other', label: 'Khác...' }
                    ]
                }
            },
            food_tour: {
                who: {
                    ...sharedWho,
                    greeting: 'Food tour vui hơn khi đi cùng người hợp khẩu vị — bạn đi với ai?',
                },
                transport: {
                    ...sharedTransport,
                    greeting: 'Food tour nên chọn phương tiện dễ dừng quán linh hoạt.',
                    options: [
                        { value: 'xe_may', label: 'Xe máy (dễ ghé quán)' },
                        { value: 'o_to_rieng', label: 'Ô tô riêng' },
                        { value: 'limousine', label: 'Xe khách / Thuê xe' },
                        { value: 'tau_hoa', label: 'Tàu hỏa' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
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
                },
                food: {
                    key: 'food',
                    greeting: 'Đây là câu quan trọng nhất của Food Tour.',
                    question: 'Phong cách ăn uống bạn muốn?',
                    type: 'single',
                    options: [
                        { value: 'dac_san', label: 'Đặc sản phải thử' },
                        { value: 'quan_binh_dan', label: 'Quán bình dân, hàng quán' },
                        { value: 'nha_hang', label: 'Nhà hàng / Set menu' },
                        { value: 'chay', label: 'Thiên về đồ chay' },
                        { value: 'linh_hoat', label: 'Mix linh hoạt' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
                focus: {
                    key: 'focus',
                    greeting: 'Còn một câu nữa thôi!',
                    question: 'Ưu tiên số 1 của food tour này?',
                    type: 'single',
                    options: [
                        { value: 'trai_nghiem_sau', label: 'Món ngon đúng chất' },
                        { value: 'anh_dep', label: 'Quán đẹp để chụp ảnh' },
                        { value: 'it_di_chuyen', label: 'Các quán gần nhau' },
                        { value: 'tiet_kiem_tg', label: 'Ăn nhanh, tối ưu lộ trình' },
                        { value: 'gia_dinh_vui', label: 'Phù hợp cả nhóm / gia đình' },
                        { value: 'other', label: 'Khác...' }
                    ]
                }
            },
            check_in: {
                who: {
                    ...sharedWho,
                    greeting: 'Check-in đẹp thường đi với bạn hoặc couple — bạn đi cùng ai?',
                    options: [
                        { value: 'doi_lua', label: 'Đôi lứa / Couple' },
                        { value: 'nhom_ban', label: 'Nhóm bạn' },
                        { value: 'mot_minh', label: 'Đi một mình' },
                        { value: 'gia_dinh_tre_nho', label: 'Gia đình có trẻ nhỏ' },
                        { value: 'gia_dinh_nguoi_lon', label: 'Gia đình có người lớn tuổi' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
                transport: {
                    ...sharedTransport,
                    greeting: 'Check-in cần di chuyển linh hoạt giữa các điểm view.',
                    options: [
                        { value: 'xe_may', label: 'Xe máy' },
                        { value: 'o_to_rieng', label: 'Ô tô riêng' },
                        { value: 'limousine', label: 'Thuê xe / Limousine' },
                        { value: 'tau_hoa', label: 'Tàu hỏa' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
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
                },
                food: {
                    key: 'food',
                    greeting: 'Giữa các điểm ảnh, bạn muốn ăn kiểu nào?',
                    question: 'Phong cách ăn uống kèm check-in?',
                    type: 'single',
                    options: [
                        { value: 'nha_hang', label: 'Quán / Nhà hàng view đẹp' },
                        { value: 'dac_san', label: 'Đặc sản địa phương' },
                        { value: 'quan_binh_dan', label: 'Ăn nhanh bình dân' },
                        { value: 'linh_hoat', label: 'Linh hoạt' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
                focus: {
                    key: 'focus',
                    greeting: 'Gần xong!',
                    question: 'Điều quan trọng nhất khi đi check-in?',
                    type: 'single',
                    options: [
                        { value: 'anh_dep', label: 'Ánh sáng / Góc ảnh đẹp nhất' },
                        { value: 'tiet_kiem_tg', label: 'Đúng giờ bình minh / hoàng hôn' },
                        { value: 'it_di_chuyen', label: 'Các điểm gần nhau' },
                        { value: 'trai_nghiem_sau', label: 'Ít đông, trải nghiệm chất' },
                        { value: 'gia_dinh_vui', label: 'Dễ đi cùng nhóm' },
                        { value: 'other', label: 'Khác...' }
                    ]
                }
            },
            family: {
                who: {
                    ...sharedWho,
                    greeting: 'Chuyến gia đình cần lịch trình dễ chịu — đoàn của bạn gồm ai?',
                    options: [
                        { value: 'gia_dinh_tre_nho', label: 'Có trẻ nhỏ' },
                        { value: 'gia_dinh_nguoi_lon', label: 'Có người lớn tuổi' },
                        { value: 'nhom_ban', label: 'Gia đình mở rộng / Họ hàng' },
                        { value: 'doi_lua', label: 'Vợ chồng' },
                        { value: 'mot_minh', label: 'Một mình (thăm người thân)' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
                transport: {
                    ...sharedTransport,
                    greeting: 'Gia đình thường ưu tiên xe rộng, an toàn.',
                    options: [
                        { value: 'o_to_rieng', label: 'Ô tô riêng' },
                        { value: 'limousine', label: 'Xe khách / Limousine' },
                        { value: 'xe_may', label: 'Xe máy' },
                        { value: 'tau_hoa', label: 'Tàu hỏa' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
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
                },
                food: {
                    key: 'food',
                    greeting: 'Ăn uống cần dễ cho cả nhà.',
                    question: 'Kiểu ẩm thực phù hợp gia đình?',
                    type: 'single',
                    options: [
                        { value: 'quan_binh_dan', label: 'Quán bình dân, dễ ăn' },
                        { value: 'dac_san', label: 'Đặc sản địa phương' },
                        { value: 'nha_hang', label: 'Nhà hàng rộng rãi' },
                        { value: 'chay', label: 'Có lựa chọn thanh đạm' },
                        { value: 'linh_hoat', label: 'Linh hoạt' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
                focus: {
                    key: 'focus',
                    greeting: 'Câu cuối cùng!',
                    question: 'Điều quan trọng nhất với chuyến gia đình?',
                    type: 'single',
                    options: [
                        { value: 'gia_dinh_vui', label: 'An toàn, vui cả nhà' },
                        { value: 'it_di_chuyen', label: 'Ít di chuyển, đỡ mệt' },
                        { value: 'cham_rai', label: 'Có giờ nghỉ / không gấp' },
                        { value: 'anh_dep', label: 'Ảnh kỷ niệm đẹp' },
                        { value: 'tiet_kiem_tg', label: 'Lịch rõ ràng, dễ theo' },
                        { value: 'other', label: 'Khác...' }
                    ]
                }
            },
            couple: {
                who: {
                    key: 'who',
                    greeting: `Chuyến ${label} lãng mạn — xác nhận nhanh người đi cùng nhé.`,
                    question: 'Bạn đi cùng ai?',
                    type: 'single',
                    options: [
                        { value: 'doi_lua', label: 'Hai người (Couple)' },
                        { value: 'mot_minh', label: 'Một mình (chuẩn bị bất ngờ)' },
                        { value: 'nhom_ban', label: 'Đi cùng nhóm nhỏ' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
                transport: { ...sharedTransport, greeting: 'Couple thường thích tự do dừng đúng chỗ mình muốn.' },
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
                },
                food: {
                    key: 'food',
                    greeting: 'Bữa ăn cũng là một phần của chuyến hẹn hò.',
                    question: 'Kiểu ăn uống bạn muốn?',
                    type: 'single',
                    options: [
                        { value: 'nha_hang', label: 'Nhà hàng / View đẹp' },
                        { value: 'dac_san', label: 'Đặc sản địa phương' },
                        { value: 'quan_binh_dan', label: 'Quán nhỏ dễ thương' },
                        { value: 'linh_hoat', label: 'Linh hoạt' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
                focus: {
                    key: 'focus',
                    greeting: 'Gần xong rồi!',
                    question: 'Điều quan trọng nhất với chuyến couple?',
                    type: 'single',
                    options: [
                        { value: 'anh_dep', label: 'Ảnh đẹp, không khí lãng mạn' },
                        { value: 'it_di_chuyen', label: 'Ít chạy xe, gần nhau' },
                        { value: 'trai_nghiem_sau', label: 'Trải nghiệm riêng tư, ít đông' },
                        { value: 'nghi_duong', label: 'Thư giãn, không áp lực' },
                        { value: 'other', label: 'Khác...' }
                    ]
                }
            },
            resort: {
                who: { ...sharedWho, greeting: 'Nghỉ dưỡng — bạn đi cùng ai?' },
                transport: {
                    ...sharedTransport,
                    greeting: 'Nghỉ dưỡng thường ưu tiên di chuyển êm, ít đổi chỗ.',
                    options: [
                        { value: 'o_to_rieng', label: 'Ô tô riêng' },
                        { value: 'limousine', label: 'Xe đưa đón / Limousine' },
                        { value: 'tau_hoa', label: 'Tàu hỏa' },
                        { value: 'xe_may', label: 'Xe máy' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
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
                budget: {
                    ...sharedBudget,
                    greeting: 'Ngân sách ảnh hưởng khá nhiều đến loại hình lưu trú.',
                    options: [
                        { value: 'cao_cap', label: 'Thoải mái / Cao cấp (> 2.5 triệu)' },
                        { value: 'tieu_chuan', label: 'Tiêu chuẩn (1 - 2.5 triệu)' },
                        { value: 'tiet_kiem', label: 'Tiết kiệm (Dưới 1 triệu)' },
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
                },
                food: {
                    key: 'food',
                    greeting: 'Ẩm thực khi nghỉ dưỡng.',
                    question: 'Bạn thích ăn theo kiểu nào?',
                    type: 'single',
                    options: [
                        { value: 'nha_hang', label: 'Nhà hàng trong/cạnh nơi ở' },
                        { value: 'dac_san', label: 'Đặc sản địa phương' },
                        { value: 'linh_hoat', label: 'Linh hoạt' },
                        { value: 'chay', label: 'Thanh đạm / Healthy' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
                focus: {
                    key: 'focus',
                    greeting: 'Câu cuối!',
                    question: 'Điều quan trọng nhất khi nghỉ dưỡng?',
                    type: 'single',
                    options: [
                        { value: 'it_di_chuyen', label: 'Ít phải di chuyển' },
                        { value: 'trai_nghiem_sau', label: 'Yên tĩnh, thư giãn thật sự' },
                        { value: 'anh_dep', label: 'Không gian đẹp để chill' },
                        { value: 'gia_dinh_vui', label: 'Phù hợp cả gia đình' },
                        { value: 'other', label: 'Khác...' }
                    ]
                }
            },
            team_building: {
                who: {
                    key: 'who',
                    greeting: 'Team building cần biết quy mô đoàn.',
                    question: 'Quy mô / thành phần đoàn của bạn?',
                    type: 'single',
                    options: [
                        { value: 'nhom_ban', label: 'Nhóm nhỏ (< 15 người)' },
                        { value: 'gia_dinh_nguoi_lon', label: 'Công ty / Đoàn vừa (15–40)' },
                        { value: 'gia_dinh_tre_nho', label: 'Đoàn lớn (> 40)' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
                transport: {
                    ...sharedTransport,
                    greeting: 'Đoàn đông thường cần xe tập trung.',
                    options: [
                        { value: 'limousine', label: 'Xe khách / Limousine' },
                        { value: 'o_to_rieng', label: 'Nhiều ô tô riêng' },
                        { value: 'xe_may', label: 'Tự túc xe máy' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
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
                },
                food: {
                    key: 'food',
                    greeting: 'Ăn uống cho đoàn cần dễ order số lượng lớn.',
                    question: 'Kiểu ẩm thực cho đoàn?',
                    type: 'single',
                    options: [
                        { value: 'quan_binh_dan', label: 'Buffet / Quán phục vụ đoàn' },
                        { value: 'dac_san', label: 'Đặc sản địa phương' },
                        { value: 'nha_hang', label: 'Nhà hàng đặt bàn trước' },
                        { value: 'linh_hoat', label: 'Linh hoạt' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
                focus: {
                    key: 'focus',
                    greeting: 'Câu cuối!',
                    question: 'Ưu tiên số 1 của team building?',
                    type: 'single',
                    options: [
                        { value: 'gia_dinh_vui', label: 'Dễ tổ chức, cả đoàn theo kịp' },
                        { value: 'tiet_kiem_tg', label: 'Lịch chặt, đúng giờ' },
                        { value: 'it_di_chuyen', label: 'Điểm gần nhau, ít di chuyển' },
                        { value: 'trai_nghiem_sau', label: 'Hoạt động gắn kết chất lượng' },
                        { value: 'other', label: 'Khác...' }
                    ]
                }
            },
            backpacking: {
                who: {
                    ...sharedWho,
                    greeting: 'Phượt linh hoạt — bạn đi cùng ai?',
                    options: [
                        { value: 'mot_minh', label: 'Solo' },
                        { value: 'nhom_ban', label: 'Nhóm bạn phượt' },
                        { value: 'doi_lua', label: 'Hai người' },
                        { value: 'gia_dinh_tre_nho', label: 'Gia đình' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
                transport: {
                    ...sharedTransport,
                    greeting: 'Phượt thường gắn với phương tiện tự chủ.',
                    options: [
                        { value: 'xe_may', label: 'Xe máy (phượt)' },
                        { value: 'o_to_rieng', label: 'Ô tô tự lái' },
                        { value: 'tau_hoa', label: 'Tàu hỏa + di chuyển địa phương' },
                        { value: 'limousine', label: 'Xe khách' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
                budget: {
                    ...sharedBudget,
                    greeting: 'Phượt hay đi theo ngân sách rõ ràng.',
                    options: [
                        { value: 'tiet_kiem', label: 'Tiết kiệm (Dưới 1 triệu)' },
                        { value: 'tieu_chuan', label: 'Tiêu chuẩn (1 - 2.5 triệu)' },
                        { value: 'cao_cap', label: 'Thoải mái hơn (> 2.5 triệu)' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
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
                },
                food: {
                    key: 'food',
                    greeting: 'Ăn uống kiểu phượt.',
                    question: 'Bạn thích ăn thế nào?',
                    type: 'single',
                    options: [
                        { value: 'quan_binh_dan', label: 'Quán bình dân / Local' },
                        { value: 'dac_san', label: 'Đặc sản phải thử' },
                        { value: 'linh_hoat', label: 'Tùy đường, linh hoạt' },
                        { value: 'chay', label: 'Thanh đạm' },
                        { value: 'other', label: 'Khác...' }
                    ]
                },
                focus: {
                    key: 'focus',
                    greeting: 'Câu cuối!',
                    question: 'Điều quan trọng nhất khi phượt?',
                    type: 'single',
                    options: [
                        { value: 'tiet_kiem_tg', label: 'Lộ trình tối ưu, ít vòng' },
                        { value: 'trai_nghiem_sau', label: 'Khám phá chỗ ít người' },
                        { value: 'anh_dep', label: 'Cảnh đẹp dọc đường' },
                        { value: 'it_di_chuyen', label: 'Không chạy quá xa trong ngày' },
                        { value: 'other', label: 'Khác...' }
                    ]
                }
            }
        };

        const cfg = byType[type] || {};

        return [
            cfg.who || sharedWho,
            cfg.transport || sharedTransport,
            cfg.duration_hotel || sharedDuration,
            cfg.budget || sharedBudget,
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
            cfg.interests || {
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
            },
            cfg.food || {
                key: 'food',
                greeting: 'Ăn uống cũng là một phần quan trọng của chuyến đi.',
                question: 'Bạn thích kiểu ẩm thực nào?',
                type: 'single',
                options: [
                    { value: 'dac_san', label: 'Đặc sản địa phương' },
                    { value: 'chay', label: 'Đồ chay / Thanh đạm' },
                    { value: 'quan_binh_dan', label: 'Quán bình dân, giá mềm' },
                    { value: 'nha_hang', label: 'Nhà hàng / View đẹp' },
                    { value: 'linh_hoat', label: 'Linh hoạt, tùy chỗ' },
                    { value: 'other', label: 'Khác...' }
                ]
            },
            cfg.focus || {
                key: 'focus',
                greeting: 'Gần xong rồi — còn một câu nữa thôi!',
                question: 'Điều quan trọng nhất với bạn trong chuyến đi này là gì?',
                type: 'single',
                options: [
                    { value: 'anh_dep', label: 'Có nhiều góc ảnh đẹp' },
                    { value: 'it_di_chuyen', label: 'Ít di chuyển, gần nhau' },
                    { value: 'trai_nghiem_sau', label: 'Trải nghiệm sâu từng điểm' },
                    { value: 'gia_dinh_vui', label: 'Phù hợp cả gia đình / nhóm' },
                    { value: 'tiet_kiem_tg', label: 'Tối ưu thời gian' },
                    { value: 'other', label: 'Khác...' }
                ]
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

    function renderDefaultStep(idx) {
        if (!activeSteps.length && tripType) {
            activeSteps = getWizardSteps(tripType);
        }
        if (idx < activeSteps.length) {
            currentAiQuestion = activeSteps[idx];
            renderAiQuestion(activeSteps[idx]);
        } else {
            aiDone = true;
            renderDoneStep('Đã ghi nhận đủ thông tin cho chuyến đi của bạn.');
        }
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
        const stepCount = activeSteps.length || 8;
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

        fetch('{{ route("client.trip_planner.generate") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ answers: fullAnswers, trip_type: tripType || tripTypeLabel }),
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
        currentItinerary = data;
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Lưu vào trang cá nhân';
            saveBtn.classList.remove('saved');
        }
        if (saveToStorage) {
            saveTripDraft(data);
        }
        resultPanel.classList.add('active');
        setPlannerMode('result');
        resultTitle.textContent = data.title || 'Lịch trình du lịch';
        resultSummary.textContent = data.summary || '';
        let html = '';
        if (data.days && data.days.length > 0) {
            data.days.forEach(day => {
                let dayTitle = String(day.title || '').trim()
                    .replace(new RegExp('^\\s*Ngày\\s*' + day.day + '\\s*[:\\-–]?\\s*', 'i'), '')
                    .replace(/^Ngày\s*\d+\s*[:\-–]?\s*/i, '')
                    .trim();
                html += `<div class="tp-day-section"><div class="tp-day-title"><span class="tp-day-badge">NGÀY ${day.day}</span>${dayTitle || ''}</div>`;
                (day.slots || []).forEach(slot => {
                    const cls = slot.type || 'visit';
                    const dist = slot.distance_from_prev_km
                        ? `<div class="tp-slot-distance">↔ ${slot.distance_from_prev_km} km từ điểm trước</div>`
                        : '';
                    html += `<div class="tp-slot">
                        <div class="tp-slot-time">${slot.time || ''}</div>
                        <div class="tp-slot-dot ${cls}"></div>
                        <div class="tp-slot-content">
                            <div class="tp-slot-activity">${slot.activity || ''}</div>
                            ${slot.location ? `<div class="tp-slot-location" ${slot.location_id ? `onclick="window.zoomFromTripPlanner('${slot.location_id}')" title="Xem trên bản đồ"` : ''}>${slot.location}</div>` : ''}
                            ${dist}
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

            fetch('{{ route("client.trip_planner.save") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({
                    itinerary: currentItinerary,
                    answers: lastAnswersPayload,
                }),
            })
            .then(async res => {
                const data = await res.json().catch(() => ({}));
                if (res.status === 401 || data.need_login) {
                    saveBtn.disabled = false;
                    saveBtn.textContent = 'Lưu vào trang cá nhân';
                    if (confirm('Bạn cần đăng nhập để lưu. Đến trang đăng nhập?')) {
                        window.location.href = LOGIN_URL;
                    }
                    return;
                }
                if (!data.success) {
                    saveBtn.disabled = false;
                    saveBtn.textContent = 'Lưu vào trang cá nhân';
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
                saveBtn.textContent = 'Lưu vào trang cá nhân';
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
        let f = strContent.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
        resultBody.innerHTML = `<div class="tp-raw-result">${f}</div>`;
    }

    function renderError(msg) {
        currentItinerary = null;
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.textContent = 'Lưu vào trang cá nhân';
        }
        resultPanel.classList.add('active');
        setPlannerMode('result');
        resultTitle.textContent = 'Không thể tạo lịch trình';
        resultSummary.textContent = msg;
        resultBody.innerHTML = `<div style="text-align:center; padding:32px 16px; color:#a1a1aa; font-size:0.75rem;">
            <p>${msg}</p><p style="margin-top:6px; font-size:0.68rem;">Bấm "Lên lịch mới" để thử lại</p></div>`;
    }
});
</script>
