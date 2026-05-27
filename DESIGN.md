# Design System

## Visual Theme
Hà Nam Travel Hub Admin sử dụng hệ thống thiết kế Light Mode với tông màu sáng tinh tế, kết hợp với các hiệu ứng đổ bóng mịn (`shadow`) và viền mảnh (`border`) tạo phong cách giao diện SaaS cao cấp, hiện đại.

## Colors
- **Màu nền ứng dụng (`--bg-dark`)**: `#f8fafc` (Xám xanh nhạt dịu mắt, tránh mỏi mắt).
- **Màu nền thẻ (`--bg-card`)**: `#ffffff` (Trắng tinh khiết).
- **Màu nhấn chủ đạo (`--primary`)**: `#4f46e5` (Tím Indigo công nghệ và hiện đại).
- **Màu nhấn phụ (`--primary-light`)**: `rgba(79, 70, 229, 0.08)` (Màu nền cho các biểu tượng hoạt động, badge trạng thái).
- **Màu chữ chính (`--text-main`)**: `#0f172a` (Slate đậm, có độ tương phản cao đạt chuẩn WCAG AA).
- **Màu chữ phụ (`--text-muted`)**: `#64748b` (Slate nhạt cho mô tả, nhãn phụ).
- **Màu đường viền (`--border-color`)**: `#e2e8f0` (Xám nhạt mịn màng).
- **Màu thành công (`--success`)**: `#10b981` (Xanh ngọc lục bảo).
- **Màu cảnh báo (`--danger`)**: `#ef4444` (Đỏ san hô).

## Typography
- **Font chữ chính**: `Outfit`, sans-serif (Font chữ hiện đại, bo tròn tinh tế tạo cảm giác sáng tạo, thân thiện).
- **Tỉ lệ phân cấp (Font Scale)**: Sử dụng tỉ lệ 1.25x.
  - H1 (Tiêu đề trang): `28px`, Weight `700`.
  - H2/Card Title: `18px`, Weight `600`.
  - Body Text: `14px` hoặc `15px`, Line-height `1.6`.

## Layout & Spacing
- **Sidebar**: Rộng `260px`, nền trắng hoặc xám xanh nhạt.
- **Card**: Góc bo `16px`, đổ bóng `box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)`.
- **Form controls**: Bo góc `10px`, viền `1px solid var(--border-color)`, hiệu ứng hover/focus đổi sang màu `--primary` mềm mại.

## Motion
- Sử dụng các hiệu ứng chuyển đổi mượt mà với `transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1)`.
- Không sử dụng các chuyển động nảy hay quá đà (bounce, elastic).
