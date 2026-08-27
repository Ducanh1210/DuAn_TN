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
    .tp-container.tp-mode-result {
        width: min(98vw, 1480px);
        height: min(96vh, 920px);
        max-width: 1480px;
        max-height: 920px;
        border-radius: 6px;
    }
    .tp-container.tp-mode-result .tp-right {
        display: none;
    }
    .tp-container.tp-mode-result .tp-progress,
    .tp-container.tp-mode-result .tp-footer {
        display: none !important;
    }
    .tp-container.tp-mode-result .tp-wizard-body,
    .tp-container.tp-mode-result .tp-loading-panel {
        display: none !important;
    }
    .tp-container.tp-mode-result .tp-left {
        min-height: 0;
        overflow: hidden;
    }
    .tp-container.tp-mode-result .tp-header {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        z-index: 30;
        padding: 14px 16px;
        background: transparent;
        border-bottom: 0;
        pointer-events: none;
    }
    .tp-container.tp-mode-result .tp-header-left { display: none; }
    .tp-container.tp-mode-result .tp-close-btn {
        pointer-events: auto;
        margin-left: auto;
        width: 38px;
        height: 38px;
        border: 0;
        border-radius: 50%;
        color: #2a2118;
        background: rgba(255, 251, 245, 0.94);
        box-shadow: 0 10px 28px rgba(42, 33, 24, 0.18);
    }
    .tp-container.tp-mode-result .tp-close-btn:hover {
        background: #fff;
        color: #9a3412;
        border-color: transparent;
        transform: scale(1.04);
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
        min-height: 0;
        overflow: hidden;
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

    /* ─── Location Picker Step ─── */
    .tp-loc-toolbar {
        display: flex; gap: 8px; margin-bottom: 10px;
    }
    .tp-loc-search {
        flex: 1; padding: 7px 10px; border: 1px solid #e2e8f0; border-radius: 7px;
        font-size: 0.75rem; color: #1e3a5f; background: #fff; outline: none; font-family: inherit;
    }
    .tp-loc-search:focus { border-color: #93c5fd; box-shadow: 0 0 0 2px rgba(2,132,199,0.1); }
    .tp-loc-cat-filter {
        padding: 7px 8px; border: 1px solid #e2e8f0; border-radius: 7px;
        font-size: 0.72rem; color: #475569; background: #fff; cursor: pointer; font-family: inherit;
    }
    .tp-loc-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 8px;
        max-height: 260px; overflow-y: auto; padding: 2px;
    }
    .tp-loc-card {
        background: #fff; border: 1.5px solid #e2e8f0; border-radius: 8px; cursor: pointer;
        transition: all 0.18s ease; position: relative; overflow: hidden; user-select: none;
    }
    .tp-loc-card:hover { border-color: #93c5fd; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .tp-loc-card.selected {
        border-color: #1e3a5f;
        background: #f0f5fa;
        box-shadow: 0 0 0 2px rgba(30, 58, 95, 0.18);
    }
    .tp-loc-card-img {
        width: 100%; height: 80px; object-fit: cover; display: block;
    }
    .tp-loc-card-img-placeholder {
        height: 80px; display: flex; align-items: center; justify-content: center;
        background: #f1f5f9; color: #94a3b8;
    }
    .tp-loc-card-body { padding: 6px 8px; }
    .tp-loc-card-name {
        font-size: 0.72rem; font-weight: 600; color: #1e3a5f; line-height: 1.3;
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .tp-loc-card-cat { font-size: 0.62rem; color: #94a3b8; margin-top: 2px; }
    .tp-loc-actions {
        display: flex; gap: 8px; justify-content: flex-end; margin-top: 12px;
    }
    .tp-btn { padding: 8px 18px; border-radius: 8px; font-size: 0.78rem; font-weight: 600; cursor: pointer; border: none; font-family: inherit; transition: all 0.15s; }
    .tp-btn-skip { background: #f1f5f9; color: #64748b; }
    .tp-btn-skip:hover { background: #e2e8f0; }
    .tp-btn-confirm { background: #1e3a5f; color: #fff; }
    .tp-btn-confirm:hover { background: #162d4a; }
    .tp-btn-confirm:disabled { opacity: 0.5; cursor: not-allowed; }

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
       RESULT: split journey + route map
       ═══════════════════════════════════════════════════ */
    .tp-result-panel {
        display: none;
        flex-direction: column;
        flex: 1;
        overflow: hidden;
        background: #f3efe7;
        min-height: 0;
    }
    .tp-result-panel.active { display: flex; }
    .tp-result-stage {
        display: grid;
        grid-template-columns: minmax(340px, 42%) 1fr;
        flex: 1;
        min-height: 0;
        overflow: hidden;
    }
    .tp-result-stage:has(.tp-route-pane.hidden) {
        grid-template-columns: 1fr;
    }
    .tp-journey {
        display: flex;
        flex-direction: column;
        min-width: 0;
        min-height: 0;
        height: 100%;
        overflow: hidden;
        background: #f3efe7;
        border-right: 1px solid #e4ddd2;
    }
    .tp-result-hero {
        position: relative;
        min-height: 210px;
        flex-shrink: 0;
        overflow: hidden;
        background: #1b2433 center/cover no-repeat;
    }
    .tp-result-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(16,24,38,.18) 0%, rgba(16,24,38,.78) 100%);
    }
    .tp-result-hero-copy {
        position: relative;
        z-index: 1;
        padding: 28px 24px 20px;
        color: #fff;
        min-height: 210px;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
    }
    .tp-result-kicker {
        font-size: 0.68rem;
        font-weight: 600;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        color: rgba(255,255,255,.72);
        margin-bottom: 8px;
    }
    .tp-result-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #fff;
        margin: 0 0 8px;
        letter-spacing: -0.03em;
        line-height: 1.25;
        text-wrap: balance;
    }
    .tp-result-summary {
        font-size: 0.82rem;
        color: rgba(255,255,255,.82);
        line-height: 1.5;
        font-weight: 400;
        max-width: 40rem;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .tp-result-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 14px;
    }
    .tp-result-stats:empty { display: none; }
    .tp-stat {
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.16);
        border-radius: 999px;
        padding: 5px 10px;
        color: #fff;
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: -0.01em;
        font-variant-numeric: tabular-nums;
    }

    .tp-result-body {
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
        overscroll-behavior: contain;
        padding: 8px 8px 18px 0;
        -webkit-overflow-scrolling: touch;
    }
    .tp-result-body::-webkit-scrollbar { width: 5px; }
    .tp-result-body::-webkit-scrollbar-thumb { background: #d4cbbd; border-radius: 6px; }

    .tp-day-section { padding: 12px 18px 4px; }
    .tp-day-title {
        font-size: 0.78rem;
        font-weight: 700;
        color: #5c4f3e;
        margin: 4px 0 4px 70px;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .tp-day-badge { display: none; }

    .tp-rail {
        position: relative;
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .tp-rail::before {
        content: '';
        position: absolute;
        left: 62px;
        top: 18px;
        bottom: 18px;
        width: 1px;
        background: #d7cfc3;
    }
    .tp-stop {
        position: relative;
        display: grid;
        grid-template-columns: 62px 108px 1fr;
        gap: 12px;
        padding: 10px 18px 10px 8px;
        cursor: pointer;
        transition: background 0.2s cubic-bezier(0.32, 0.72, 0, 1);
    }
    .tp-stop:hover { background: rgba(255,255,255,.45); }
    .tp-stop.is-active { background: #fff; }
    .tp-stop-index {
        margin: 6px 0 0;
        padding-right: 10px;
        background: transparent;
        color: #b45309;
        font-size: 0.68rem;
        font-weight: 700;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: flex-start;
        gap: 2px;
        position: relative;
        z-index: 1;
        box-shadow: none;
        border-radius: 0;
        width: auto;
        height: auto;
        font-variant-numeric: tabular-nums;
        letter-spacing: 0.01em;
        line-height: 1.2;
        text-align: right;
        white-space: nowrap;
    }
    .tp-stop-index-end {
        color: #8a7d6e;
        font-size: 0.62rem;
        font-weight: 600;
    }
    .tp-stop.is-active .tp-stop-index { background: transparent; color: #9a3412; }
    .tp-stop-photo {
        width: 108px;
        height: 86px;
        border-radius: 4px;
        object-fit: cover;
        background: #d8cfc2;
        display: block;
    }
    .tp-stop-photo.placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #7a6a58;
        font-size: 0.62rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .tp-stop-copy { min-width: 0; padding-top: 4px; }
    .tp-stop-time { display: none; }
    .tp-stop-name {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1d1914;
        letter-spacing: -0.02em;
        line-height: 1.3;
        margin: 2px 0 4px;
    }
    .tp-stop-activity {
        font-size: 0.78rem;
        color: #6b6258;
        line-height: 1.45;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .tp-stop-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 6px;
        font-size: 0.7rem;
        color: #8a7d6e;
        font-weight: 600;
    }
    .tp-stop-link {
        color: #1e3a5f;
        text-decoration: none;
        font-weight: 700;
    }
    .tp-stop-link:hover { text-decoration: underline; }

    .tp-tips-section {
        margin: 8px 18px 0;
        padding: 12px 0 0;
        border-top: 1px solid #e4ddd2;
    }
    .tp-tips-title {
        font-size: 0.72rem;
        font-weight: 700;
        color: #5c4f3e;
        margin-bottom: 8px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .tp-tips-list { list-style: none; padding: 0; margin: 0; }
    .tp-tips-list li {
        font-size: 0.78rem;
        color: #6b6258;
        padding: 4px 0;
        line-height: 1.45;
    }

    .tp-route-pane {
        position: relative;
        min-width: 0;
        min-height: 0;
        background: #d7e0ea;
        overflow: hidden;
    }
    .tp-route-pane.hidden { display: none; }
    #tp-route-map, #tp-mini-map {
        width: 100%;
        height: 100%;
    }
    .tp-route-tools {
        position: absolute;
        right: 14px;
        bottom: 14px;
        z-index: 400;
        pointer-events: none;
    }
    .tp-mini-map-btn {
        pointer-events: auto;
        display: inline-flex;
        align-items: center;
        border: 0;
        border-radius: 8px;
        background: #fff;
        color: #1a1a1a;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 9px 14px;
        cursor: pointer;
        font-family: inherit;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.16);
        transition: background 0.15s ease, transform 0.15s ease;
    }
    .tp-mini-map-btn:hover { background: #f4f4f5; }
    .tp-mini-map-btn:active { transform: scale(0.98); }

    .tp-map-num { background: none !important; border: none !important; }
    .tp-pin-num {
        position: absolute;
        top: -7px;
        right: -9px;
        z-index: 2;
        min-width: 16px;
        height: 16px;
        padding: 0 4px;
        border-radius: 999px;
        background: #101826;
        color: #fff;
        font-size: 9px;
        font-weight: 700;
        line-height: 16px;
        text-align: center;
        border: 1.5px solid #fff;
        box-sizing: border-box;
    }
    #tp-route-map .custom-map-pin {
        position: relative;
        width: 26px;
        height: 35px;
    }
    #tp-route-map .custom-map-pin svg {
        position: absolute;
        top: 0;
        left: 0;
        width: 26px;
        height: 35px;
    }
    #tp-route-map .pin-icon-img {
        position: absolute !important;
        top: 3px !important;
        left: 3px !important;
        width: 20px !important;
        height: 20px !important;
        max-width: 20px !important;
        max-height: 20px !important;
        object-fit: cover !important;
        border-radius: 50% !important;
    }
    #tp-route-map .custom-pin-tooltip {
        position: absolute;
        top: 16px;
        left: calc(100% - 2px);
        transform: translate(0, -50%);
        background: linear-gradient(to right, color-mix(in srgb, var(--tip-color) 40%, black), var(--tip-color));
        color: #fff;
        padding: 4px 11px;
        border-radius: 16px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        z-index: 10001;
    }
    #tp-route-map .custom-map-pin:hover .custom-pin-tooltip {
        opacity: 1;
        visibility: visible;
        transform: translate(6px, -50%);
    }
    #tp-route-map .tp-stop-pin.is-active .tp-pin-num { background: #b45309; }
    #tp-route-map .leaflet-popup-content-wrapper {
        border-radius: 4px;
        box-shadow: none;
        padding: 0;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    #tp-route-map .leaflet-popup-content {
        font-family: inherit;
        margin: 0;
        width: 260px !important;
    }
    #tp-route-map .leaflet-popup-tip-container { display: none; }
    #tp-route-map .poi-popup-inner {
        display: flex;
        flex-direction: column;
        text-align: center;
    }
    #tp-route-map .poi-thumbnail {
        width: 100%;
        height: 140px;
        object-fit: cover;
        background: #f1f5f9;
        display: block;
    }
    #tp-route-map .poi-content { padding: 16px; }
    #tp-route-map .poi-title {
        font-weight: 700;
        font-size: 17px;
        color: #1a1a1a;
        margin-bottom: 6px;
    }
    #tp-route-map .poi-desc {
        font-size: 13px;
        color: #555;
        margin-bottom: 16px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.5;
    }
    #tp-route-map .poi-btn-360 {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        color: var(--poi-color, #1e3a5f) !important;
        padding: 6px 20px;
        border-radius: 4px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none !important;
        border: 2px solid var(--poi-color, #1e3a5f);
        width: 100%;
        box-sizing: border-box;
    }

    .tp-result-footer {
        padding: 12px 16px 16px;
        border-top: 1px solid rgba(90, 72, 55, 0.1);
        display: flex;
        align-items: center;
        gap: 10px 16px;
        flex-shrink: 0;
        flex-wrap: wrap;
        background: #f6f1e8;
    }
    .tp-result-footer-side {
        display: flex;
        align-items: center;
        gap: 4px;
        margin-left: auto;
    }
    .tp-btn-new {
        background: transparent;
        color: #6b5d4f;
        font-size: 0.78rem;
        font-weight: 600;
        border: 0;
        border-radius: 999px;
        padding: 8px 12px;
        cursor: pointer;
        font-family: inherit;
        transition: color 0.2s ease, background 0.2s ease;
    }
    .tp-btn-new:hover { background: rgba(42, 33, 24, 0.06); color: #2a2118; }
    .tp-btn-save {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-width: 168px;
        padding: 12px 18px;
        border-radius: 8px;
        background: #1e3a5f;
        color: #fff;
        font-size: 0.82rem;
        font-weight: 650;
        letter-spacing: -0.01em;
        border: none;
        cursor: pointer;
        font-family: inherit;
        box-shadow: none;
        transition: transform 0.2s cubic-bezier(0.32, 0.72, 0, 1), background 0.2s ease;
    }
    .tp-btn-save:hover { background: #2b4c7e; }
    .tp-btn-save:active { transform: scale(0.98); }
    .tp-btn-save:disabled { opacity: 0.55; cursor: not-allowed; box-shadow: none; }
    .tp-btn-save.saved { background: #166534; box-shadow: none; }

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
        .tp-container.tp-mode-result .tp-result-stage {
            display: flex;
            flex-direction: column;
        }
        .tp-container.tp-mode-result .tp-route-pane {
            order: -1;
            flex: 0 0 38vh;
            min-height: 220px;
        }
        .tp-journey { border-right: 0; flex: 1; min-height: 0; }
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
        .tp-header { padding: 14px 18px; }
        .tp-container.tp-mode-result {
            width: 100vw;
            height: 100dvh;
            max-width: 100%;
            max-height: 100%;
            border-radius: 0;
        }
        .tp-stop { grid-template-columns: 52px 84px 1fr; padding-right: 10px; }
        .tp-stop-index { margin-left: 0; padding-right: 8px; font-size: 0.62rem; }
        .tp-rail::before { left: 52px; }
        .tp-day-title { margin-left: 60px; }
        .tp-result-title { font-size: 1.12rem; }
        .tp-result-hero, .tp-result-hero-copy { min-height: 170px; }
        .tp-result-footer { padding: 12px 14px 14px; }
        .tp-result-footer-side { width: 100%; margin-left: 0; justify-content: space-between; }
        .tp-btn-save { width: 100%; }
    }
    /* Custom SweetAlert2 Theme for Ninh Bình POI Widget */
    .swal2-container {
        z-index: 20000 !important;
    }
    .custom-swal-popup {
        border-radius: 16px !important;
        padding: 1.5rem 1.75rem !important;
        font-family: 'Be Vietnam Pro', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
        border: 1px solid #cbdbe8 !important;
        box-shadow: 0 20px 25px -5px rgba(30, 58, 95, 0.12), 0 8px 10px -6px rgba(30, 58, 95, 0.06) !important;
        background: #ffffff !important;
    }
    .custom-swal-title {
        color: #1e3a5f !important;
        font-size: 1.15rem !important;
        font-weight: 600 !important;
        padding-top: 0.25rem !important;
    }
    .custom-swal-text {
        color: #334155 !important;
        font-size: 0.875rem !important;
        margin-top: 0.4rem !important;
        line-height: 1.5 !important;
    }
    .custom-swal-confirm-btn {
        background-color: #1e3a5f !important;
        color: #ffffff !important;
        font-size: 0.825rem !important;
        font-weight: 500 !important;
        padding: 0.5rem 1.25rem !important;
        border-radius: 8px !important;
        border: none !important;
        margin: 0.25rem !important;
        cursor: pointer !important;
        transition: all 0.15s ease !important;
        box-shadow: none !important;
    }
    .custom-swal-confirm-btn:hover {
        background-color: #0f2442 !important;
        color: #ffffff !important;
    }
    .custom-swal-confirm-danger {
        background-color: #dc2626 !important;
        color: #ffffff !important;
    }
    .custom-swal-confirm-danger:hover {
        background-color: #b91c1c !important;
        color: #ffffff !important;
    }
    .custom-swal-cancel-btn {
        background-color: #f1f5f9 !important;
        color: #475569 !important;
        font-size: 0.825rem !important;
        font-weight: 500 !important;
        padding: 0.5rem 1.25rem !important;
        border-radius: 8px !important;
        border: 1px solid #cbdbe8 !important;
        margin: 0.25rem !important;
        cursor: pointer !important;
        transition: all 0.15s ease !important;
        box-shadow: none !important;
    }
    .custom-swal-cancel-btn:hover {
        background-color: #e2e8f0 !important;
        color: #1e3a5f !important;
    }
    .custom-swal-toast {
        border-radius: 10px !important;
        font-family: 'Be Vietnam Pro', 'Plus Jakarta Sans', system-ui, sans-serif !important;
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
                <div class="tp-result-stage">
                    <div class="tp-journey">
                        <div class="tp-result-hero" id="tp-result-hero">
                            <div class="tp-result-hero-copy">
                                <div class="tp-result-kicker" id="tp-result-kicker">Hành trình</div>
                    <div class="tp-result-title" id="tp-result-title"></div>
                    <div class="tp-result-summary" id="tp-result-summary"></div>
                                <div class="tp-result-stats" id="tp-result-stats"></div>
                            </div>
                </div>
                <div class="tp-result-body" id="tp-result-body"></div>
                <div class="tp-result-footer">
                            <button class="tp-btn-save" id="tp-btn-save" type="button">Lưu hành trình</button>
                            <div class="tp-result-footer-side">
                                <button class="tp-btn-new" id="tp-btn-restart" type="button">Lên lịch mới</button>
                                <button class="tp-btn-new" id="tp-btn-close-result" type="button">Đóng</button>
                            </div>
                        </div>
                    </div>
                    <div class="tp-route-pane" id="tp-route-pane">
                        <div id="tp-route-map"></div>
                        <div class="tp-route-tools">
                            <button type="button" class="tp-mini-map-btn" id="tp-btn-show-route">Google Maps</button>
                        </div>
                    </div>
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
        { value: 'spiritual',      icon: '☸', label: 'Tâm linh',     desc: 'Chùa, đền, phủ' },
        { value: 'food_tour',      icon: '◎', label: 'Ẩm thực',      desc: 'Đặc sản địa phương' },
        { value: 'check_in',       icon: '◉', label: 'Check-in',     desc: 'Điểm đẹp, chụp ảnh' },
        { value: 'family',         icon: '⌂', label: 'Gia đình',     desc: 'Cả nhà cùng đi' },
        { value: 'couple',         icon: '♡', label: 'Cặp đôi',      desc: 'Đi hai người' },
        { value: 'resort',         icon: '△', label: 'Nghỉ dưỡng',   desc: 'Ở lại, thư giãn' },
        { value: 'team_building',  icon: '⬡', label: 'Đoàn nhóm',    desc: 'Công ty, team' },
        { value: 'backpacking',    icon: '↗', label: 'Phượt',        desc: 'Tự túc, khám phá' },
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
    const IS_AUTHENTICATED = @json(auth()->check());
    const CURRENT_USER_ID = @json(auth()->id());
    const LOGIN_URL = @json(route('login'));
    const PROFILE_URL = @json(route('client.profile'));

    // Bản nháp chỉ giữ trong tab hiện tại + theo user — không dùng chung localStorage mãi
    const TP_DRAFT_KEY = 'nb_trip_draft_' + (CURRENT_USER_ID ? ('u' + CURRENT_USER_ID) : 'guest');
    const TP_LEGACY_KEYS = ['nb_saved_itinerary', 'nb_trip_draft_guest'];
    const TP_BOUNDARY_URL = @json(asset('geo/ha-nam-old.geojson'));

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
        const duration = {
            key: 'duration_hotel',
            greeting: 'Thời gian quyết định lịch trình dài hay ngắn.',
            question: 'Bạn đi trong bao lâu?',
            type: 'single',
            options: type === 'resort'
                ? [
                    { value: '2d1n_hotel', label: '2 ngày 1 đêm' },
                    { value: '3d2n_hotel', label: '3 ngày 2 đêm' },
                    { value: '1_day', label: 'Đi trong ngày' },
                ]
                : [
                    { value: '1_day', label: 'Đi trong ngày' },
                    { value: '2d1n_hotel', label: '2 ngày 1 đêm' },
                    { value: '3d2n_hotel', label: '3 ngày 2 đêm' },
                ],
        };

        const whoByType = {
            spiritual: [
                { value: 'gia_dinh_nguoi_lon', label: 'Gia đình có người lớn tuổi' },
                { value: 'gia_dinh_tre_nho', label: 'Gia đình có trẻ nhỏ' },
                { value: 'nhom_ban', label: 'Nhóm bạn' },
                { value: 'doi_lua', label: 'Hai người' },
                { value: 'mot_minh', label: 'Một mình' },
            ],
            family: [
                { value: 'gia_dinh_tre_nho', label: 'Có trẻ nhỏ' },
                { value: 'gia_dinh_nguoi_lon', label: 'Có người lớn tuổi' },
                { value: 'nhom_ban', label: 'Cả nhà / họ hàng' },
            ],
            team_building: [
                { value: 'nhom_ban', label: 'Nhóm nhỏ, dưới 15 người' },
                { value: 'gia_dinh_nguoi_lon', label: 'Đoàn vừa, 15–40 người' },
                { value: 'gia_dinh_tre_nho', label: 'Đoàn lớn, trên 40 người' },
            ],
        };

        const who = type === 'couple' ? null : {
            key: 'who',
            greeting: type === 'team_building' ? 'Đoàn đông hay ít sẽ đổi cách xếp lịch.' : 'Đi cùng ai thì lịch trình sẽ khác nhau.',
            question: type === 'team_building' ? 'Đoàn của bạn khoảng bao nhiêu người?' : 'Bạn đi cùng ai?',
            type: 'single',
            options: whoByType[type] || [
                { value: 'mot_minh', label: 'Một mình' },
                { value: 'doi_lua', label: 'Hai người' },
                { value: 'nhom_ban', label: 'Nhóm bạn' },
                { value: 'gia_dinh_tre_nho', label: 'Gia đình' },
            ],
        };

        const interestsByType = {
            spiritual: [
                { value: 'tam_linh', label: 'Chùa, đền, phủ' },
                { value: 'van_hoa', label: 'Di tích lịch sử' },
                { value: 'am_thuc', label: 'Ăn chay, quán thanh đạm' },
                { value: 'thien_nhien', label: 'Cảnh quanh điểm lễ' },
            ],
            food_tour: [
                { value: 'am_thuc', label: 'Đặc sản địa phương' },
                { value: 'check_in', label: 'Quán đẹp, dễ chụp' },
                { value: 'van_hoa', label: 'Món gắn với vùng' },
                { value: 'thien_nhien', label: 'Quán gần thiên nhiên' },
            ],
            check_in: [
                { value: 'check_in', label: 'Điểm view, sống ảo' },
                { value: 'thien_nhien', label: 'Núi, sông, thiên nhiên' },
                { value: 'van_hoa', label: 'Kiến trúc, di tích' },
                { value: 'am_thuc', label: 'Quán cafe, đồ uống' },
                { value: 'tam_linh', label: 'Chùa đền có cảnh đẹp' },
            ],
            family: [
                { value: 'thien_nhien', label: 'Chỗ thoáng, dễ đi' },
                { value: 'check_in', label: 'Điểm vui, chụp ảnh gia đình' },
                { value: 'am_thuc', label: 'Ăn uống dễ cho cả nhà' },
                { value: 'tam_linh', label: 'Ghé chùa, đền' },
                { value: 'nghi_duong', label: 'Nghỉ ngơi' },
            ],
            couple: [
                { value: 'check_in', label: 'Góc ảnh đẹp' },
                { value: 'am_thuc', label: 'Ăn uống, cafe' },
                { value: 'thien_nhien', label: 'Hoàng hôn, thiên nhiên' },
                { value: 'nghi_duong', label: 'Nghỉ dưỡng' },
            ],
            resort: [
                { value: 'nghi_duong', label: 'Chỗ nghỉ dễ chịu' },
                { value: 'thien_nhien', label: 'Không gian xanh' },
                { value: 'am_thuc', label: 'Ăn uống tại chỗ' },
                { value: 'check_in', label: 'Góc chill, chụp ảnh' },
            ],
            team_building: [
                { value: 'thien_nhien', label: 'Ngoài trời, thiên nhiên' },
                { value: 'am_thuc', label: 'Ăn uống tập thể' },
                { value: 'check_in', label: 'Điểm chụp ảnh nhóm' },
                { value: 'nghi_duong', label: 'Chỗ ở đủ cho đoàn' },
            ],
            backpacking: [
                { value: 'thien_nhien', label: 'Đường đẹp, thiên nhiên' },
                { value: 'check_in', label: 'Điểm view' },
                { value: 'am_thuc', label: 'Quán địa phương' },
                { value: 'van_hoa', label: 'Làng, di tích' },
            ],
        };

        const interests = {
            key: 'interests',
            greeting: 'Chọn thứ bạn muốn làm, có thể chọn nhiều.',
            question: 'Bạn muốn ưu tiên điều gì?',
            type: 'multi',
            options: interestsByType[type] || [
                { value: 'tam_linh', label: 'Tâm linh' },
                { value: 'am_thuc', label: 'Ẩm thực' },
                { value: 'check_in', label: 'Check-in' },
                { value: 'thien_nhien', label: 'Thiên nhiên' },
                { value: 'van_hoa', label: 'Văn hóa, lịch sử' },
                { value: 'nghi_duong', label: 'Nghỉ dưỡng' },
            ],
        };

        const budget = {
            key: 'budget',
            greeting: 'Ngân sách tính trên mỗi người, cả ăn uống và đi lại.',
            question: 'Bạn dự chi khoảng bao nhiêu mỗi người?',
            type: 'single',
            options: [
                { value: 'tiet_kiem', label: 'Dưới 1 triệu' },
                { value: 'tieu_chuan', label: '1 – 2,5 triệu' },
                { value: 'cao_cap', label: 'Trên 2,5 triệu' },
            ],
        };

        const paceByType = {
            spiritual: [
                { value: 'cham_rai', label: 'Chậm, ít điểm, tĩnh tâm' },
                { value: 'can_bang', label: 'Vừa lễ vừa tham quan' },
                { value: 'dap_dong', label: 'Ghé nhiều chùa trong ngày' },
            ],
            food_tour: [
                { value: 'cham_rai', label: 'Ít quán, ăn kỹ' },
                { value: 'can_bang', label: 'Vài quán mỗi ngày' },
                { value: 'dap_dong', label: 'Thử nhiều quán' },
            ],
            check_in: [
                { value: 'cham_rai', label: 'Ít điểm, chụp kỹ' },
                { value: 'can_bang', label: 'Vài điểm mỗi ngày' },
                { value: 'dap_dong', label: 'Ghé nhiều góc ảnh' },
            ],
            family: [
                { value: 'cham_rai', label: 'Chậm, nhiều nghỉ' },
                { value: 'can_bang', label: 'Vừa chơi vừa nghỉ' },
                { value: 'dap_dong', label: 'Xem nhiều điểm' },
            ],
            resort: [
                { value: 'cham_rai', label: 'Ở chỗ nghỉ là chính' },
                { value: 'can_bang', label: 'Nửa nghỉ, nửa đi chơi' },
                { value: 'dap_dong', label: 'Vẫn muốn đi nhiều' },
            ],
            team_building: [
                { value: 'cham_rai', label: 'Nhẹ, thiên về nghỉ' },
                { value: 'can_bang', label: 'Có hoạt động và nghỉ' },
                { value: 'dap_dong', label: 'Nhiều hoạt động nhóm' },
            ],
            backpacking: [
                { value: 'cham_rai', label: 'Đi chậm, tùy hứng' },
                { value: 'can_bang', label: 'Cân bằng' },
                { value: 'dap_dong', label: 'Khám phá nhiều điểm' },
            ],
        };

        const pace = {
            key: 'pace',
            greeting: 'Nhịp độ quyết định ngày dày hay thoải mái.',
            question: 'Bạn muốn lịch trình thế nào?',
            type: 'single',
            options: paceByType[type] || [
                { value: 'cham_rai', label: 'Chậm, ít điểm' },
                { value: 'can_bang', label: 'Vừa phải' },
                { value: 'dap_dong', label: 'Nhiều điểm' },
            ],
        };

        return [
            duration,
            ...(who ? [who] : []),
            interests,
            budget,
            pace,
            {
                key: '__location_picker',
                type: 'location_picker',
                greeting: 'Nếu đã có chỗ muốn ghé, chọn trước để lịch giữ điểm đó.',
                question: 'Bạn muốn ghé địa điểm nào? Có thể bỏ qua.',
            },
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
        html += '<div class="tp-step-greeting">Chọn kiểu chuyến đi để xếp lịch cho đúng.</div>';
        html += '<div class="tp-step-question">Bạn muốn đi theo hướng nào?</div>';
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
                if (tripType === 'couple') {
                    aiAnswers = [{
                        question: 'Bạn đi cùng ai?',
                        answer: 'Hai người',
                        key: 'who',
                        value: 'doi_lua',
                    }];
                } else {
                    aiAnswers = [];
                }
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
        const getCatName = l => l.category?.name || l.category_name || 'Chưa phân loại';
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

        const optionsList = q.options || [];
        const optCount = optionsList.length;
        let colClass = optCount <= 2 ? 'cols-2' : optCount <= 4 ? 'cols-2' : optCount <= 6 ? 'cols-3' : 'cols-4';

        let html = '<div class="tp-step">';
        if (q.greeting) html += `<div class="tp-step-greeting">${q.greeting}</div>`;
        html += `<div class="tp-step-question">${q.question}</div>`;
        html += `<div class="tp-card-grid ${colClass}">`;
        optionsList.forEach(opt => {
            if (isMulti) {
                html += `<div class="tp-card tp-card-multi" data-value="${opt.value}" data-label="${opt.label}" data-type="${q.type}">
                    <div class="tp-checkbox-box">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <span class="tp-card-label">${opt.label}</span>
                </div>`;
            } else {
                html += `<div class="tp-card" data-value="${opt.value}" data-label="${opt.label}" data-type="${q.type}">
                    <span class="tp-card-label">${opt.label}</span>
                </div>`;
            }
        });
        html += '</div></div>';

        wizardBody.innerHTML = html;

        wizardBody.querySelectorAll('.tp-card').forEach(card => {
            card.addEventListener('click', () => handleCardClick(card, q));
        });
    }

    function handleCardClick(card, q) {
        const value = card.dataset.value;
        const label = card.dataset.label;

        if (q.type === 'multi') {
            if (!currentSelection) currentSelection = [];
            const idx = currentSelection.findIndex(s => s.value === value);
            if (idx > -1) {
                currentSelection.splice(idx, 1);
                card.classList.remove('selected');
            } else {
                currentSelection.push({ value, label });
                card.classList.add('selected');
            }
            nextBtn.disabled = currentSelection.length === 0;
            return;
        }

        currentSelection = { value, label };
        wizardBody.querySelectorAll('.tp-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        nextBtn.classList.remove('visible');
        setTimeout(() => advanceFromQuestion(q), 250);
    }

    function advanceFromQuestion(q) {
        let answerText = '';
        if (Array.isArray(currentSelection)) {
            answerText = currentSelection.map(s => s.label).join(', ');
        } else if (currentSelection) {
            answerText = currentSelection.label;
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

        fetch('{{ route("client.trip_planner.generate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
            budget: data.stats?.budget || data.estimated_cost || null,
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
        L.tileLayer(@json(config('services.carto.tile_url')), {
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
            if (stats.budget || data.estimated_cost) chips.push(`<span class="tp-stat">${tpEsc(stats.budget || data.estimated_cost)}</span>`);
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

            // 1. Kiểm tra đăng nhập
            if (!IS_AUTHENTICATED) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Yêu cầu đăng nhập',
                        html: 'Bạn cần đăng nhập để lưu lịch trình vào trang cá nhân. Bạn có muốn đến trang đăng nhập không?',
                        icon: 'info',
                        iconColor: '#1e3a5f',
                        showCancelButton: true,
                        confirmButtonText: 'Đăng nhập',
                        cancelButtonText: 'Để sau',
                        reverseButtons: true,
                        customClass: {
                            popup: 'custom-swal-popup',
                            title: 'custom-swal-title',
                            htmlContainer: 'custom-swal-text',
                            confirmButton: 'custom-swal-confirm-btn',
                            cancelButton: 'custom-swal-cancel-btn'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = LOGIN_URL + '?redirect=' + encodeURIComponent(window.location.pathname + '#trip-planner');
                        }
                    });
                } else {
                    if (confirm('Bạn cần đăng nhập để lưu lịch trình vào trang cá nhân. Đến trang đăng nhập?')) {
                        window.location.href = LOGIN_URL + '?redirect=' + encodeURIComponent(window.location.pathname + '#trip-planner');
                    }
                }
                return;
            }

            const itineraryTitle = currentItinerary.title || 'Lịch trình du lịch';

            // Hàm thực thi lưu lịch trình lên Server
            const doSave = () => {
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
                        saveBtn.textContent = 'Lưu hành trình';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Hết phiên đăng nhập',
                                text: 'Phiên làm việc đã hết hạn. Vui lòng đăng nhập lại.',
                                icon: 'warning',
                                iconColor: '#eab308',
                                confirmButtonText: 'Đăng nhập',
                                customClass: {
                                    popup: 'custom-swal-popup',
                                    title: 'custom-swal-title',
                                    htmlContainer: 'custom-swal-text',
                                    confirmButton: 'custom-swal-confirm-btn'
                                },
                                buttonsStyling: false
                            }).then(() => { window.location.href = LOGIN_URL; });
                        } else {
                            if (confirm('Bạn cần đăng nhập để lưu. Đến trang đăng nhập?')) {
                                window.location.href = LOGIN_URL;
                            }
                        }
                        return;
                    }
                    if (!data.success) {
                        saveBtn.disabled = false;
                        saveBtn.textContent = 'Lưu hành trình';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Không thể lưu',
                                text: data.error || 'Không lưu được lịch trình.',
                                icon: 'error',
                                iconColor: '#dc2626',
                                confirmButtonText: 'Đóng',
                                customClass: {
                                    popup: 'custom-swal-popup',
                                    title: 'custom-swal-title',
                                    htmlContainer: 'custom-swal-text',
                                    confirmButton: 'custom-swal-confirm-btn custom-swal-confirm-danger'
                                },
                                buttonsStyling: false
                            });
                        } else {
                            alert(data.error || 'Không lưu được lịch trình.');
                        }
                        return;
                    }
                    saveBtn.classList.add('saved');
                    saveBtn.textContent = 'Đã lưu ✓';
                    try { clearTripDraftStorage(); } catch (e) {}

                    // Hiển thị Popup Modal thông báo lưu thành công và hỏi chuyển sang trang cá nhân
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Lưu lịch trình thành công',
                            html: 'Lịch trình <strong>"' + tpEsc(itineraryTitle) + '"</strong> đã được lưu vào tài khoản của bạn.<br>Bạn có muốn mở trang cá nhân để xem ngay không?',
                            icon: 'success',
                            iconColor: '#166534',
                            showCancelButton: true,
                            confirmButtonText: 'Xem trang cá nhân',
                            cancelButtonText: 'Ở lại trang này',
                            reverseButtons: true,
                            customClass: {
                                popup: 'custom-swal-popup',
                                title: 'custom-swal-title',
                                htmlContainer: 'custom-swal-text',
                                confirmButton: 'custom-swal-confirm-btn',
                                cancelButton: 'custom-swal-cancel-btn'
                            },
                            buttonsStyling: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = (data.profile_url || PROFILE_URL + '#itineraries');
                            }
                        });
                    } else {
                        if (confirm('Đã lưu vào trang cá nhân. Mở trang cá nhân để xem?')) {
                            window.location.href = (data.profile_url || PROFILE_URL + '#itineraries');
                        }
                    }
                })
                .catch(() => {
                    saveBtn.disabled = false;
                    saveBtn.textContent = 'Lưu hành trình';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Lỗi kết nối',
                            text: 'Không thể kết nối tới máy chủ. Vui lòng thử lại.',
                            icon: 'error',
                            iconColor: '#dc2626',
                            confirmButtonText: 'Đóng',
                            customClass: {
                                popup: 'custom-swal-popup',
                                title: 'custom-swal-title',
                                htmlContainer: 'custom-swal-text',
                                confirmButton: 'custom-swal-confirm-btn custom-swal-confirm-danger'
                            },
                            buttonsStyling: false
                        });
                    } else {
                        alert('Không thể kết nối máy chủ.');
                    }
                });
            };

            // 2. Hỏi xác nhận TRƯỚC khi gửi request lưu lên Server
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Lưu lịch trình',
                    html: 'Bạn có chắc chắn muốn lưu lịch trình <strong>"' + tpEsc(itineraryTitle) + '"</strong> vào trang cá nhân không?',
                    icon: 'question',
                    iconColor: '#1e3a5f',
                    showCancelButton: true,
                    confirmButtonText: 'Đồng ý lưu',
                    cancelButtonText: 'Hủy bỏ',
                    reverseButtons: true,
                    customClass: {
                        popup: 'custom-swal-popup',
                        title: 'custom-swal-title',
                        htmlContainer: 'custom-swal-text',
                        confirmButton: 'custom-swal-confirm-btn',
                        cancelButton: 'custom-swal-cancel-btn'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        doSave();
                    } else {
                        // Người dùng bấm "Hủy bỏ" -> Tuyệt đối không gửi request, không lưu vào DB
                        Swal.fire({
                            icon: 'info',
                            iconColor: '#64748b',
                            title: 'Đã hủy thao tác lưu',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2500,
                            timerProgressBar: true,
                            customClass: {
                                popup: 'custom-swal-toast'
                            }
                        });
                    }
                });
            } else {
                if (confirm('Bạn có chắc chắn muốn lưu lịch trình "' + itineraryTitle + '" vào trang cá nhân không?')) {
                    doSave();
                }
            }
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
        fetch('{{ url("/trip-planner") }}/' + encodeURIComponent(id), {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
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
</script>
