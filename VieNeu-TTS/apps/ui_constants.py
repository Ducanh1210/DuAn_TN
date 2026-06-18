import gradio as gr

theme = gr.themes.Default(
    primary_hue="indigo",
    secondary_hue="violet",
    neutral_hue="slate",
    font=[gr.themes.GoogleFont('Outfit'), gr.themes.GoogleFont('Inter'), 'ui-sans-serif', 'system-ui'],
).set(
    # Body background
    body_background_fill="#070a13",
    body_background_fill_dark="#070a13",
    
    # Block background
    block_background_fill="#0f1626",
    block_background_fill_dark="#0f1626",
    block_border_width="1px",
    block_border_color="#1e293b",
    block_border_color_dark="#1e293b",
    block_label_background_fill="#1e293b",
    block_label_text_color="#94a3b8",
    
    # Inputs
    input_background_fill="#0a0f1d",
    input_background_fill_dark="#0a0f1d",
    input_border_color="#1e293b",
    input_border_color_dark="#1e293b",
    input_border_color_focus="#818cf8",
    input_border_color_focus_dark="#818cf8",
    
    # Primary button
    button_primary_background_fill="linear-gradient(135deg, #818cf8 0%, #6366f1 50%, #4f46e5 100%)",
    button_primary_background_fill_hover="linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)",
    button_primary_text_color="#ffffff",
    
    # Sliders and controls
    slider_color="#6366f1",
)

css = """
/* Viewport Fitting compact layout */
.container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 8px;
    font-family: 'Outfit', sans-serif !important;
}

/* Slim Glassmorphic Header */
.header-box {
    text-align: center;
    margin-bottom: 12px;
    padding: 10px;
    background: linear-gradient(135deg, rgba(15, 22, 42, 0.9) 0%, rgba(30, 41, 59, 0.8) 100%);
    border: 1px solid rgba(255, 255, 255, 0.06);
    backdrop-filter: blur(20px);
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.35);
}

.header-title {
    font-size: 1.6rem;
    font-weight: 800;
    margin: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.gradient-text {
    background: linear-gradient(90deg, #a78bfa 0%, #818cf8 40%, #34d399 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    display: inline-block;
}

/* Glassmorphic Workspace Unified Dashboard */
.workspace-card {
    background: rgba(15, 22, 42, 0.55) !important;
    border: 1px solid rgba(255, 255, 255, 0.06) !important;
    backdrop-filter: blur(24px);
    border-radius: 14px !important;
    padding: 16px !important;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.35);
    gap: 16px !important;
}

/* Right Column Card styling */
.output-column-card {
    background: rgba(10, 15, 30, 0.4) !important;
    border-left: 1px solid rgba(255, 255, 255, 0.06) !important;
    padding-left: 16px !important;
    gap: 12px !important;
}

/* Accordion Custom Styling */
.gr-accordion {
    border: 1px solid rgba(99, 102, 241, 0.15) !important;
    background: rgba(10, 15, 30, 0.5) !important;
    border-radius: 10px !important;
    margin-bottom: 8px;
}

/* Sleek Active Tabs */
.tabs {
    background: transparent !important;
    border: none !important;
}
.tab-nav {
    border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
    margin-bottom: 10px !important;
    gap: 8px !important;
}
.tab-nav button {
    border-radius: 6px 6px 0 0 !important;
    padding: 6px 14px !important;
    font-size: 0.9rem !important;
    font-weight: 500 !important;
    color: #94a3b8 !important;
    transition: all 0.2s ease !important;
    border: none !important;
    background: transparent !important;
}
.tab-nav button:hover {
    color: #f1f5f9 !important;
    background: rgba(255, 255, 255, 0.02) !important;
}
.tab-nav button.selected {
    color: #818cf8 !important;
    background: rgba(99, 102, 241, 0.08) !important;
    border-bottom: 2px solid #818cf8 !important;
    font-weight: 600 !important;
}

/* Textareas, Dropdowns, and Inputs */
textarea, input[type="text"], .gr-input, .gr-box {
    border-radius: 10px !important;
    background-color: #080c16 !important;
    border: 1px solid #1e293b !important;
    color: #f1f5f9 !important;
    font-size: 0.95rem !important;
    transition: all 0.2s ease !important;
}
textarea:focus, input[type="text"]:focus, .gr-input:focus {
    border-color: #818cf8 !important;
    box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.15) !important;
}

/* Terminal Logger Term Style for Outputs */
.status-box textarea, .estimate-box textarea {
    font-family: 'Consolas', 'Courier New', Courier, monospace !important;
    color: #34d399 !important;
    background: #05070d !important;
    border-color: rgba(52, 211, 153, 0.15) !important;
    font-size: 0.85rem !important;
    line-height: 1.3 !important;
}

/* Audio Player Modern look */
audio {
    width: 100%;
    border-radius: 8px;
    background: #0b0f19;
}

/* Custom Primary Button Styling */
button.primary, button.gr-button-variant-primary, .primary-btn {
    background: linear-gradient(135deg, #818cf8 0%, #6366f1 50%, #4f46e5 100%) !important;
    border: none !important;
    color: white !important;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    border-radius: 8px !important;
    font-weight: 600 !important;
    padding: 10px 20px !important;
    font-size: 0.95rem !important;
}
button.primary:hover, button.gr-button-variant-primary:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 18px rgba(99, 102, 241, 0.45) !important;
}
button.primary:active, button.gr-button-variant-primary:active {
    transform: translateY(0) !important;
}

/* Scrollbars */
textarea::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
textarea::-webkit-scrollbar-track {
    background: transparent;
}
textarea::-webkit-scrollbar-thumb {
    background: rgba(129, 140, 248, 0.2);
    border-radius: 4px;
}
textarea::-webkit-scrollbar-thumb:hover {
    background: rgba(129, 140, 248, 0.4);
}
"""

head_html = """
<link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🦜</text></svg>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
"""

DEFAULT_TEXT_GPU = "Hà Nội, trái tim của Việt Nam, là một thành phố ngàn năm văn hiến với bề dày lịch sử và văn hóa độc đáo. Bước chân trên những con phố cổ kính quanh Hồ Hoàn Kiếm, du khách như được du hành ngược thời gian, chiêm ngưỡng kiến trúc Pháp cổ điển hòa quyện với nét kiến trúc truyền thống Việt Nam. Mỗi con phố trong khu phố cổ mang một tên gọi đặc trưng, phản ánh nghề thủ công truyền thống từng thịnh hành nơi đây như phố Hàng Bạc, Hàng Đào, Hàng Mã. Ẩm thực Hà Nội cũng là một điểm nhấn đặc biệt, từ tô phở nóng hổi buổi sáng, bún chả thơm lừng trưa hè, đến chè Thái ngọt ngào chiều thu. Những món ăn dân dã này đã trở thành biểu tượng của văn hóa ẩm thực Việt, được cả thế giới yêu mến. Người Hà Nội nổi tiếng với tính cách hiền hòa, lịch thiệp nhưng cũng rất cầu toàn trong từng chi tiết nhỏ, từ cách pha trà sen cho đến cách chọn hoa sen tây để thưởng trà."
DEFAULT_TEXT_TURBO = (
    "Trước đây, hệ thống điện chủ yếu sử dụng direct current, nhưng Tesla đã chứng minh rằng alternating current is more efficient for long-distance transmission. Nhờ đó, điện có thể được truyền đi xa hơn với ít tổn thất năng lượng hơn. Đây là một bước tiến cực kỳ quan trọng trong ngành điện.\n\n"
    "Một trong những phát minh nổi tiếng của ông là Tesla coil, một thiết bị có thể tạo ra điện áp rất cao và những tia sét nhân tạo. This device is still used today in demonstrations và trong một số ứng dụng nghiên cứu. Khi nhìn thấy những tia điện này, many people feel both impressed and slightly scared."
)

DEFAULT_TEXT_V3 = (
    "Xin chào mọi người! [hắng giọng] Như bạn đang nghe thấy đấy, tốc độ xử lý của mình cực kỳ nhanh và mượt mà, giúp phản hồi gần như ngay lập tức theo thời gian thực. Chính vì vậy, mình rất phù hợp để ứng dụng trực tiếp vào các hệ thống Chatbot thông minh, trợ lý ảo, hoặc làm tổng đài viên tự động cho các doanh nghiệp. Tiện lợi quá đúng không ạ? [cười] Hi vọng phiên bản nâng cấp v3 này sẽ mang lại trải nghiệm tuyệt vời cho dự án của bạn."
)
