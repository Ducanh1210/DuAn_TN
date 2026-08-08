<!-- AI Chatbot Floating Widget Component -->
<style>
    #chatbot-widget {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
        font-family: 'Be Vietnam Pro', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        -webkit-font-smoothing: antialiased;
        transition: bottom 0.35s cubic-bezier(0.16, 1, 0.3, 1), transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Dynamic attachment to Bottom Drawer (thanh nổi bật) */
    #chatbot-widget.drawer-open,
    body:has(.bottom-drawer-wrapper.open) #chatbot-widget,
    body.drawer-open #chatbot-widget {
        bottom: 165px;
    }

    /* Floating Toggle Button */
    #chatbot-toggle {
        width: 54px;
        height: 54px;
        border-radius: 50%;
        background: #ffffff;
        border: 1px solid #cbdbe8;
        box-shadow: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        outline: none;
        padding: 0;
    }

    #chatbot-toggle:hover {
        transform: none;
        box-shadow: none;
        border-color: #1e3a5f;
    }

    #chatbot-toggle:active {
        transform: none;
    }

    #chatbot-toggle .toggle-img {
        width: 38px;
        height: 38px;
        object-fit: contain;
        transition: transform 0.25s ease, filter 0.25s ease;
        filter: none;
    }

    #chatbot-toggle:hover .toggle-img {
        filter: none;
    }

    /* Green status badge on button */
    #chatbot-toggle .toggle-badge {
        position: absolute;
        top: 1px;
        right: 1px;
        width: 12px;
        height: 12px;
        background-color: #22c55e;
        border: 2px solid #ffffff;
        border-radius: 50%;
        box-shadow: none;
        transition: opacity 0.2s ease;
    }

    #chatbot-toggle.is-open {
        opacity: 0;
        visibility: hidden;
        transform: scale(0.3);
        pointer-events: none;
    }

    /* Main Chat Window Container (Expands directly from button position) */
    #chatbot-container {
        display: none;
        width: 350px;
        max-width: calc(100vw - 24px);
        height: 490px;
        max-height: calc(100vh - 90px);
        background: #ffffff;
        border-radius: 12px;
        box-shadow: none;
        position: absolute;
        bottom: 0;
        right: 0;
        flex-direction: column;
        overflow: hidden;
        transform-origin: bottom right;
        transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.22s ease;
        opacity: 0;
        transform: scale(0.2) translateY(20px);
        z-index: 10001;
    }

    #chatbot-container.active {
        display: flex;
        opacity: 1;
        transform: scale(1) translateY(0);
    }

    /* Header */
    #chatbot-header {
        background: #1e3a5f;
        color: #ffffff;
        padding: 12px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        user-select: none;
    }

    .chatbot-brand {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .chatbot-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        padding: 2px;
        flex-shrink: 0;
    }

    .chatbot-avatar img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        filter: none;
    }

    .online-indicator {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 8px;
        height: 8px;
        background-color: #22c55e;
        border: 2px solid #1e3a5f;
        border-radius: 50%;
    }

    .chatbot-title-group {
        display: flex;
        flex-direction: column;
    }

    .chatbot-title {
        font-weight: 500;
        font-size: 0.9rem;
        line-height: 1.2;
        color: #ffffff;
    }

    .chatbot-status {
        font-size: 0.7rem;
        color: rgba(255, 255, 255, 0.75);
        margin-top: 2px;
    }

    .chatbot-actions {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .header-btn {
        background: transparent;
        border: none;
        color: rgba(255, 255, 255, 0.8);
        width: 28px;
        height: 28px;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        outline: none;
    }

    .header-btn:hover {
        background: rgba(255, 255, 255, 0.18);
        color: #ffffff;
    }

    .header-btn svg {
        width: 15px;
        height: 15px;
        fill: currentColor;
    }

    /* Messages Area */
    #chatbot-messages {
        flex: 1;
        padding: 14px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
        background: #f8fafc;
        scroll-behavior: smooth;
    }

    #chatbot-messages::-webkit-scrollbar {
        width: 4px;
    }
    #chatbot-messages::-webkit-scrollbar-track {
        background: transparent;
    }
    #chatbot-messages::-webkit-scrollbar-thumb {
        background: #cbdbe8;
        border-radius: 4px;
    }
    #chatbot-messages::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Chat Message Bubbles */
    .chat-msg {
        max-width: 90%;
        padding: 8px 12px;
        font-size: 0.78rem;
        font-weight: 400;
        line-height: 1.45;
        word-wrap: break-word;
        word-break: break-word;
        box-sizing: border-box;
    }

    .chat-msg.bot {
        background: #ffffff;
        color: #3b5980;
        align-self: flex-start;
        border-radius: 8px 8px 8px 2px;
        border: 1px solid #e5e7eb;
        box-shadow: none;
    }

    .chat-msg.user {
        background: #1e3a5f;
        color: #ffffff;
        align-self: flex-end;
        border-radius: 8px 8px 2px 8px;
    }

    /* Formatting inside Bot Messages */
    .chat-msg.bot strong {
        color: #1e3a5f;
        font-weight: 600;
    }

    .chat-msg.bot ul {
        margin: 3px 0 3px 12px;
        padding: 0;
    }

    .chat-msg.bot li {
        margin-bottom: 2px;
    }

    .chat-msg.bot p {
        margin: 0 0 4px 0;
    }

    .chat-msg.bot p:last-child {
        margin-bottom: 0;
    }

    .chat-msg.bot .chat-hr {
        border: 0;
        height: 1px;
        background: #e5e7eb;
        margin: 6px 0;
    }

    /* Clickable Location Link Buttons in Chat */
    .chat-loc-btn {
        color: #0284c7;
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        padding: 2px 7px;
        border-radius: 5px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        transition: all 0.15s ease;
        cursor: pointer;
        font-size: 0.76rem;
        margin: 1px 2px;
    }

    .chat-loc-btn:hover {
        background: #0284c7;
        color: #ffffff;
        border-color: #0284c7;
    }

    .chat-loc-btn svg {
        width: 12px;
        height: 12px;
        fill: currentColor;
    }

    /* Quick Suggestion Chips */
    .chat-quick-suggestions {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-top: 6px;
        margin-bottom: 2px;
    }

    .chat-chip {
        background: #f1f5f9;
        color: #1e3a5f;
        border: 1px solid #cbdbe8;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        outline: none;
    }

    .chat-chip:hover {
        background: #1e3a5f;
        color: #ffffff;
        border-color: #1e3a5f;
    }

    /* Typing Indicator Dots */
    .typing-indicator {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 8px 12px;
        width: fit-content;
    }

    .typing-dot {
        width: 5px;
        height: 5px;
        background-color: #6482a6;
        border-radius: 50%;
        animation: typingBounce 1.4s infinite ease-in-out both;
    }

    .typing-dot:nth-child(1) { animation-delay: 0s; }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }

    @keyframes typingBounce {
        0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
        40% { transform: scale(1); opacity: 1; }
    }

    /* Input Footer Area */
    #chatbot-input-area {
        display: flex;
        align-items: center;
        padding: 8px 10px;
        background: #ffffff;
        border-top: 1px solid #e5e7eb;
        gap: 6px;
    }

    .chatbot-input-wrapper {
        flex: 1;
        position: relative;
        display: flex;
        align-items: center;
    }

    #chatbot-input {
        width: 100%;
        border: 1px solid #cbdbe8;
        background: #ffffff;
        border-radius: 6px;
        padding: 7px 44px 7px 10px;
        outline: none;
        font-size: 0.78rem;
        color: #3b5980;
        font-family: inherit;
        transition: border-color 0.2s ease;
    }

    #chatbot-input:focus {
        border-color: #1e3a5f;
    }

    #chatbot-input::placeholder {
        color: #a1a1aa;
    }

    #chatbot-char-counter {
        position: absolute;
        right: 8px;
        font-size: 0.65rem;
        font-weight: 500;
        color: #94a3b8;
        pointer-events: none;
        user-select: none;
        transition: color 0.2s ease;
    }

    #chatbot-char-counter.limit-near {
        color: #f59e0b;
    }

    #chatbot-char-counter.limit-reached {
        color: #ef4444;
        font-weight: 600;
    }

    #chatbot-send {
        background: #1e3a5f;
        color: #ffffff;
        border: none;
        border-radius: 6px;
        width: 36px;
        height: 36px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s ease;
        outline: none;
        flex-shrink: 0;
    }

    #chatbot-send:hover {
        background: #2b4c7e;
    }

    #chatbot-send svg {
        width: 15px;
        height: 15px;
        fill: #ffffff;
    }

    /* Floating Greeting Tooltip Bubble (Positioned DIAGONALLY top-left of toggle button) */
    #chatbot-tooltip {
        position: absolute;
        bottom: 58px;
        right: 36px;
        transform: translateY(6px) translateX(-6px);
        background: #ffffff;
        color: #1e3a5f;
        border: 1px solid #cbdbe8;
        padding: 5px 10px;
        border-radius: 10px 10px 2px 10px;
        font-size: 0.75rem;
        font-weight: 500;
        box-shadow: none;
        white-space: nowrap;
        display: flex;
        align-items: center;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: pointer;
        user-select: none;
        z-index: 9998;
    }

    #chatbot-tooltip.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) translateX(0);
    }

    #chatbot-tooltip:hover {
        background: #1e3a5f;
        color: #ffffff;
        border-color: #1e3a5f;
    }



    .tooltip-arrow {
        position: absolute;
        bottom: -6px;
        right: 12px;
        width: 0;
        height: 0;
        border-left: 6px solid transparent;
        border-right: 2px solid transparent;
        border-top: 6px solid #cbdbe8;
        transition: border-top-color 0.2s ease;
    }

    #chatbot-tooltip:hover .tooltip-arrow {
        border-top-color: #1e3a5f;
    }

    @media (max-width: 480px) {
        #chatbot-widget {
            bottom: 16px;
            right: 16px;
        }
        #chatbot-widget.drawer-open,
        body:has(.bottom-drawer-wrapper.open) #chatbot-widget,
        body.drawer-open #chatbot-widget {
            bottom: 155px;
        }
        #chatbot-container {
            width: calc(100vw - 24px);
            height: calc(100vh - 80px);
            bottom: 56px;
        }
        #chatbot-tooltip {
            display: none !important;
        }
    }
</style>

<div id="chatbot-widget">
    <!-- Floating Greeting Speech Bubble Tooltip -->
    <div id="chatbot-tooltip" title="Bấm để hỏi Trợ lý AI">
        <span id="chatbot-tooltip-text">Đi đâu chơi nè?</span>
        <div class="tooltip-arrow"></div>
    </div>

    <!-- Toggle Floating Button -->
    <button id="chatbot-toggle" title="Trợ lý ảo Ninh Bình">
        <img src="{{ asset('images/bot.png') }}" alt="Bot Icon" class="toggle-img">
        <span class="toggle-badge"></span>
    </button>

    <!-- Chat Container -->
    <div id="chatbot-container">
        <!-- Header -->
        <div id="chatbot-header">
            <div class="chatbot-brand">
                <div class="chatbot-avatar">
                    <img src="{{ asset('images/bot.png') }}" alt="Bot Avatar">
                    <span class="online-indicator"></span>
                </div>
                <div class="chatbot-title-group">
                    <span class="chatbot-title">Trợ lý ảo Ninh Bình</span>
                    <span class="chatbot-status">Sẵn sàng hỗ trợ</span>
                </div>
            </div>
            <div class="chatbot-actions">
                <button class="header-btn" id="chatbot-clear" title="Xóa lịch sử chat">
                    <svg viewBox="0 0 24 24">
                        <path d="M15 4V3H9v1H4v2h16V4h-5zm-9 4v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V8H6zm3 3h2v8H9v-8zm4 0h2v8h-2v-8z"/>
                    </svg>
                </button>
                <button class="header-btn" id="chatbot-close" title="Đóng">
                    <svg viewBox="0 0 24 24">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Chat Message Area -->
        <div id="chatbot-messages">
            <div class="chat-msg bot">
                Chào bạn! Mình là Trợ lý ảo Du lịch Ninh Bình. Mình có thể giúp gì cho bạn hôm nay?
                <div class="chat-quick-suggestions">
                    <button class="chat-chip" onclick="window.sendQuickMessage('Địa điểm tâm linh nổi tiếng')">Tâm linh</button>
                    <button class="chat-chip" onclick="window.sendQuickMessage('Đặc sản ngon nên thử')">Đặc sản</button>
                    <button class="chat-chip" onclick="window.sendQuickMessage('Lịch trình 1 ngày cho tôi')">Lịch trình 1 ngày</button>
                    <button class="chat-chip" onclick="window.sendQuickMessage('Khách sạn nghỉ dưỡng tốt')">Khách sạn</button>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div id="chatbot-input-area">
            <div class="chatbot-input-wrapper">
                <input type="text" id="chatbot-input" placeholder="Hỏi về địa điểm, lịch trình, ẩm thực..." maxlength="300" autocomplete="off">
                <span id="chatbot-char-counter">0/300</span>
            </div>
            <button id="chatbot-send" title="Gửi tin nhắn">
                <svg viewBox="0 0 24 24">
                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const widget = document.getElementById('chatbot-widget');
        const toggle = document.getElementById('chatbot-toggle');
        const container = document.getElementById('chatbot-container');
        const closeBtn = document.getElementById('chatbot-close');
        const clearBtn = document.getElementById('chatbot-clear');
        const input = document.getElementById('chatbot-input');
        const sendBtn = document.getElementById('chatbot-send');
        const messages = document.getElementById('chatbot-messages');
        let chatHistory = [];

        // Disable leaflet map propagation if map is present
        if (widget && window.L) {
            L.DomEvent.disableClickPropagation(widget);
            L.DomEvent.disableScrollPropagation(widget);
        }

        // Dynamic position tracking for bottom drawer (thanh điểm nổi bật)
        const drawerWrapper = document.querySelector('.bottom-drawer-wrapper');
        if (drawerWrapper) {
            const checkDrawerState = () => {
                if (drawerWrapper.classList.contains('open')) {
                    widget.classList.add('drawer-open');
                } else {
                    widget.classList.remove('drawer-open');
                }
            };
            checkDrawerState();
            const observer = new MutationObserver(checkDrawerState);
            observer.observe(drawerWrapper, { attributes: true, attributeFilter: ['class'] });
        }

        // Floating Tooltip Speech Bubble Logic
        const tooltip = document.getElementById('chatbot-tooltip');
        const tooltipText = document.getElementById('chatbot-tooltip-text');

        const phrases = [
            'Đi đâu chơi nè?',
            'Ăn gì ở Ninh Bình?',
            'Gợi ý chuyến đi?',
            'Hỏi tôi ngay nhé!'
        ];
        let phraseIdx = 0;

        function showTooltip() {
            if (container.classList.contains('active')) return;
            tooltipText.textContent = phrases[phraseIdx];
            tooltip.classList.add('show');
        }

        function hideTooltip() {
            if (tooltip) tooltip.classList.remove('show');
        }

        setTimeout(() => {
            showTooltip();
            setInterval(() => {
                if (container.classList.contains('active')) return;
                hideTooltip();
                setTimeout(() => {
                    phraseIdx = (phraseIdx + 1) % phrases.length;
                    showTooltip();
                }, 400);
            }, 5000);
        }, 2000);

        if (tooltip) {
            tooltip.addEventListener('click', () => {
                hideTooltip();
                if (!container.classList.contains('active')) {
                    toggle.click();
                }
            });
        }

        toggle.addEventListener('click', () => {
            const isActive = container.classList.contains('active');
            if (isActive) {
                container.classList.remove('active');
                toggle.classList.remove('is-open');
                setTimeout(() => { container.style.display = 'none'; }, 250);
            } else {
                hideTooltip();
                container.style.display = 'flex';
                toggle.classList.add('is-open');
                void container.offsetWidth;
                container.classList.add('active');
                input.focus();
            }
        });

        closeBtn.addEventListener('click', () => {
            container.classList.remove('active');
            toggle.classList.remove('is-open');
            setTimeout(() => { container.style.display = 'none'; }, 250);
        });

        clearBtn.addEventListener('click', () => {
            if (confirm('Bạn có chắc muốn xóa lịch sử cuộc trò chuyện này?')) {
                chatHistory = [];
                messages.innerHTML = `
                    <div class="chat-msg bot">
                        Chào bạn! Mình là Trợ lý ảo Du lịch Ninh Bình. Mình có thể giúp gì cho bạn hôm nay?
                        <div class="chat-quick-suggestions">
                            <button class="chat-chip" onclick="window.sendQuickMessage('Địa điểm tâm linh nổi tiếng')">Tâm linh</button>
                            <button class="chat-chip" onclick="window.sendQuickMessage('Đặc sản ngon nên thử')">Đặc sản</button>
                            <button class="chat-chip" onclick="window.sendQuickMessage('Lịch trình 1 ngày cho tôi')">Lịch trình 1 ngày</button>
                            <button class="chat-chip" onclick="window.sendQuickMessage('Khách sạn nghỉ dưỡng tốt')">Khách sạn</button>
                        </div>
                    </div>
                `;
            }
        });

        function parseMarkdown(text) {
            if (!text) return '';
            let html = text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');

            // Convert [Tên địa điểm](loc:ID) to clickable location button
            html = html.replace(/\[(.*?)\]\(loc:([^\)]+)\)/g, (match, p1, p2) => {
                return `<a href="javascript:void(0)" class="chat-loc-btn" onclick="window.zoomToLocationFromChat('${p2}')" title="Định vị ${p1} trên bản đồ">${p1} <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg></a>`;
            });

            // Headers
            html = html.replace(/^### (.*$)/gim, '<strong class="chat-h3">$1</strong>');
            
            // Bold
            html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            
            // Italic
            html = html.replace(/\*(.*?)\*/g, '<em>$1</em>');
            
            // Horizontal rule
            html = html.replace(/^---$/gim, '<hr class="chat-hr">');
            
            // Bullet points
            html = html.replace(/^[-\*]\s+(.*$)/gim, '• $1');

            // Line breaks
            html = html.replace(/\n/g, '<br>');

            return html;
        }

        function addMessage(text, sender) {
            const msgDiv = document.createElement('div');
            msgDiv.className = `chat-msg ${sender}`;
            msgDiv.innerHTML = parseMarkdown(text);
            messages.appendChild(msgDiv);
            messages.scrollTop = messages.scrollHeight;
        }

        function addTyping() {
            const typingDiv = document.createElement('div');
            typingDiv.className = 'chat-msg bot typing-indicator';
            typingDiv.id = 'typing-indicator';
            typingDiv.innerHTML = `
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            `;
            messages.appendChild(typingDiv);
            messages.scrollTop = messages.scrollHeight;
        }

        function removeTyping() {
            const typing = document.getElementById('typing-indicator');
            if (typing) typing.remove();
        }

        const charCounter = document.getElementById('chatbot-char-counter');

        function updateCharCount() {
            if (!charCounter) return;
            const len = input.value.length;
            charCounter.textContent = `${len}/300`;
            if (len >= 300) {
                charCounter.className = 'limit-reached';
            } else if (len >= 250) {
                charCounter.className = 'limit-near';
            } else {
                charCounter.className = '';
            }
        }

        input.addEventListener('input', updateCharCount);

        window.sendQuickMessage = function(text) {
            input.value = text;
            updateCharCount();
            sendMessage();
        };

        // Zoom to location on Leaflet map when user clicks a location button inside chat
        window.zoomToLocationFromChat = function(identifier) {
            if (typeof locations === 'undefined' || !locations || locations.length === 0) return;
            
            const loc = locations.find(l => 
                l.id == identifier || 
                l.slug == identifier || 
                (l.name && l.name.toLowerCase() === String(identifier).toLowerCase())
            );

            if (!loc || !loc.marker) {
                console.warn('Location not found for chat zoom:', identifier);
                return;
            }

            // Ensure marker layer is visible
            if (typeof markers !== 'undefined' && loc.marker && !markers.hasLayer(loc.marker)) {
                markers.addLayer(loc.marker);
            }

            const openLocPopup = () => {
                setTimeout(() => {
                    if (loc.marker) loc.marker.openPopup();
                }, 100);
            };

            // Zoom & Fly map to marker location smoothly
            if (typeof stepZoomToMarker === 'function') {
                stepZoomToMarker(loc, () => {
                    let targetZoom = Math.max(18, map.getZoom());
                    let dist = map.getCenter().distanceTo([loc.lat, loc.lng]);

                    if (dist > 80) {
                        map.once('moveend', openLocPopup);
                        map.flyTo([loc.lat, loc.lng], targetZoom, { duration: 1.1 });
                    } else {
                        map.setView([loc.lat, loc.lng], targetZoom, { animate: true });
                        openLocPopup();
                    }
                });
            } else if (window.map) {
                map.once('moveend', openLocPopup);
                map.flyTo([loc.lat, loc.lng], 18, { duration: 1.1 });
            }
        };

        function sendMessage() {
            const text = input.value.trim();
            if (!text) return;

            addMessage(text, 'user');
            input.value = '';
            updateCharCount();
            addTyping();

            fetch('{{ route("client.chat.message") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: text, history: chatHistory })
            })
            .then(res => res.json())
            .then(data => {
                removeTyping();
                if (data.success) {
                    addMessage(data.reply, 'bot');
                    chatHistory.push({ role: 'user', content: text });
                    chatHistory.push({ role: 'assistant', content: data.reply });
                    if (chatHistory.length > 20) chatHistory = chatHistory.slice(chatHistory.length - 20);
                } else {
                    addMessage('Hệ thống hiện chưa phản hồi được. Vui lòng thử lại sau!', 'bot');
                }
            })
            .catch(err => {
                console.error(err);
                removeTyping();
                addMessage('Không thể kết nối đến máy chủ.', 'bot');
            });
        }

        sendBtn.addEventListener('click', sendMessage);
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendMessage();
        });
    });
</script>
