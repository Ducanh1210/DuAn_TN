# 🗺️ TỔNG HỢP DANH SÁCH CHỨC NĂNG HỆ THỐNG NINH BÌNH POI 360°

Hệ thống **Ninh Bình POI** là nền tảng bản đồ du lịch số kết hợp trải nghiệm thực tế ảo VR Tour 360°, tích hợp Trợ lý AI và hệ thống Gamification tương tác. 

Dưới đây là danh sách chi tiết toàn bộ chức năng của ứng dụng được phân chia rõ ràng theo từng nhóm đối tượng sử dụng và vai trò trong hệ thống.

---

## 📌 BẢNG TỔNG QUAN PHÂN QUYỀN HỆ THỐNG

| Nhóm đối tượng | Vai trò / Phân quyền | Mô tả ngắn |
| :--- | :--- | :--- |
| **Guest / Public Client** | Khách truy cập | Chưa đăng nhập, trải nghiệm bản đồ, xem VR 360°, tin tức, sự kiện, gửi yêu cầu dịch vụ. |
| **Authenticated User** | Thành viên đã đăng nhập | Sử dụng các tính năng tương tác, lưu địa điểm, lập lịch trình AI, làm nhiệm vụ nhận quà, gửi báo cáo/đóng góp. |
| **Business Owner** | Chủ Doanh nghiệp | Quản lý địa điểm thuộc sở hữu, cập nhật thông tin liên hệ, phản hồi bình luận khách hàng, quản lý ảnh. |
| **Admin / Moderator** | Quản trị viên hệ thống | Toàn quyền quản lý dữ liệu, kiểm duyệt AI, duyệt doanh nghiệp, biên tập Tour 360°, quản lý người dùng. |

---

## 1. 🌐 KHÁCH THAM QUAN (GUEST / PUBLIC CLIENT)

Chức năng dành cho tất cả người dùng khi truy cập vào hệ thống (không bắt buộc đăng nhập):

### 1.1. Trang Chủ & Giới Thiệu (Landing Page)
- **Trải nghiệm trang giới thiệu:** Xem video/hình ảnh ấn tượng về du lịch Ninh Bình, các thông số thống kê nổi bật (số địa điểm, lượt xem, ảnh 360°).
- **Danh sách địa điểm nổi bật:** Hiển thị các địa điểm thu hút lượt xem cao nhất kèm hình ảnh đại diện và số lượng cảnh 360°.
- **Bản tin & Sự kiện mới nhất:** Xem danh sách bài viết tin tức du lịch và các sự kiện nổi bật sắp diễn ra.
- **Đăng ký Dịch vụ Tour 360°:** Gửi thông tin đăng ký dịch vụ chụp và dựng VR Tour 360° dành cho cá nhân/doanh nghiệp có nhu cầu.

### 1.2. Bản Đồ Du Lịch Tương Tác (Interactive Tourism Map)
- **Xem bản đồ POI:** Hiển thị trực quan vị trí các điểm du lịch tại Ninh Bình trên bản đồ số.
- **Bộ lọc danh mục (Category Filter):** Lọc địa điểm theo loại hình (Danh lam thắng cảnh, Di tích lịch sử, Khách sạn, Nhà hàng, Văn hóa - Lễ hội,...).
- **Marker custom theo danh mục:** Mỗi danh mục địa điểm có icon riêng biệt trên bản đồ.
- **Banner tin tức trên bản đồ:** Tích hợp slider trình chiếu tin tức du lịch mới nhất ngay trên giao diện bản đồ.

### 1.3. Trải Nghiệm Tour Thực Tế Ảo (360° VR Viewer)
- **Xem toàn cảnh 360°:** Khám phá địa điểm với góc nhìn 360 độ sắc nét.
- **Chuyển cảnh thông minh (Hotspot Navigation):** Bấm vào các điểm Hotspot trên ảnh 360° để di chuyển sang các khu vực/góc nhìn khác trong cùng địa điểm.
- **Thuyết minh âm thanh (Audio Guide):** Bật/Tắt bài thuyết minh âm thanh tự động giới thiệu chi tiết về địa điểm.
- **Bộ sưu tập ảnh (Gallery):** Xem các hình ảnh chụp 2D chất lượng cao của địa điểm.
- **Đếm lượt xem (View Counter):** Tự động ghi nhận và tăng lượt xem của địa điểm khi du khách truy cập.
- **Xem bình luận cộng đồng:** Đọc các đánh giá, cảm nhận và phản hồi từ những du khách khác.

### 1.4. Tin Tức & Sự Kiện Du Lịch
- **Trang danh sách Tin tức:** Xem danh sách tin tức, bài viết quảng bá văn hóa du lịch Ninh Bình.
- **Trang danh sách Sự kiện:** Xem các sự kiện, lễ hội kèm mốc thời gian bắt đầu/kết thúc và địa điểm diễn ra.
- **Trang chi tiết bài viết:** Đọc nội dung chi tiết bài viết kèm hình ảnh minh họa.

### 1.5. Trợ Lý AI & Lập Kế Hoạch Chuyến Đi (AI Assistant & Trip Planner)
- **Chatbot tư vấn du lịch AI:** Trò chuyện trực tiếp với Trợ lý AI để hỏi đáp thông tin du lịch, thời tiết, ẩm thực, di chuyển tại Ninh Bình (sử dụng Google Gemini AI).
- **Tự động tạo lịch trình du lịch (AI Trip Planner):** Nhập số ngày, ngân sách, sở thích $\rightarrow$ AI tự động phân tích và tạo lịch trình tham quan tối ưu từng ngày.

---

## 2. 👤 THÀNH VIÊN ĐÃ ĐĂNG NHẬP (AUTHENTICATED USER)

Bao gồm toàn bộ chức năng của **Guest**, tích hợp thêm các tính năng cá nhân hóa và tương tác cộng đồng:

### 2.1. Xác Thực & Quản Lý Tài Khoản (Authentication & Profile)
- **Đăng ký / Đăng nhập:** Bằng Email & Mật khẩu cá nhân.
- **Đăng nhập nhanh Google:** Đăng nhập 1-click thông qua tài khoản Google (Google OAuth 2.0).
- **Quản lý hồ sơ (Profile Management):**
  - Cập nhật thông tin cá nhân: Họ tên, Số điện thoại, Địa chỉ, Bio giới thiệu.
  - Đổi ảnh đại diện (Avatar).
  - Đổi mật khẩu tài khoản.
  - Yêu cầu xóa tài khoản cá nhân.

### 2.2. Tương Tác Địa Điểm & Cộng Đồng
- **Yêu thích địa điểm (Favorites):** Thêm/Xóa địa điểm vào danh sách yêu thích cá nhân để dễ dàng xem lại.
- **Bình luận & Thảo luận:**
  - Gửi bình luận đánh giá tại các trang Tour 360°.
  - Phản hồi (Reply) bình luận của người dùng khác.
  - Chỉnh sửa hoặc Xóa bình luận của chính mình.
- **Đề xuất địa điểm mới (Community Contribution):** Gửi thông tin địa điểm du lịch mới chưa có trên hệ thống để Admin kiểm duyệt và đưa lên bản đồ.
- **Báo cáo vi phạm & Góp ý hệ thống:**
  - Báo cáo các bình luận có nội dung không phù hợp/spam.
  - Gửi góp ý, báo lỗi hệ thống cho ban quản trị.

### 2.3. Quản Lý Lịch Trình Chuyến Đi (Trip Planner Management)
- **Lưu lịch trình AI:** Lưu lại các kế hoạch du lịch do AI tạo ra vào tài khoản cá nhân.
- **Quản lý danh sách lịch trình:** Xem lại chi tiết từng lịch trình đã lưu hoặc xóa lịch trình không còn nhu cầu.

### 2.4. Hệ Thống Nhiệm Vụ & Đổi Thưởng (Gamification System)
- **Điểm danh hàng ngày (Daily Check-in):** Đăng nhập mỗi ngày để nhận điểm thưởng (Points).
- **Hệ thống Nhiệm vụ (Missions):**
  - Thực hiện các nhiệm vụ như: Xem Tour 360°, viết bình luận, đề xuất địa điểm,... để tích lũy điểm kinh nghiệm và điểm thưởng.
  - Thưởng mốc (Milestones): Nhận quà tặng đặc biệt khi hoàn thành các cột mốc nhiệm vụ.
- **Cửa hàng đổi quà & Khung đại diện (Rewards & Avatar Frames Shop):**
  - Dùng điểm thưởng đổi lấy các **Khung đại diện (Avatar Frames)** độc đáo.
  - Dùng điểm đổi các quà tặng / voucher ưu đãi.
- **Trang bị khung đại diện:** Đổi khung đại diện đang sử dụng (khung đại diện sẽ hiển thị nổi bật quanh avatar ở phần bình luận).

### 2.5. Đăng Ký Tài Khoản Doanh Nghiệp (Business Upgrade)
- **Gửi hồ sơ nâng cấp:** Người dùng sở hữu địa điểm kinh doanh tại Ninh Bình có thể gửi đơn đăng ký nâng cấp tài khoản cá nhân thành tài khoản Doanh nghiệp (điền tên DN, địa chỉ, giấy phép kinh doanh, ảnh minh chứng).
- **Theo dõi & Quản lý yêu cầu:** Xem trạng thái xét duyệt của Admin hoặc hủy yêu cầu khi cần.

---

## 3. 🏢 CHỦ DOANH NGHIỆP (BUSINESS OWNER PORTAL)

Portal dành riêng cho tài khoản đã được Admin duyệt thành công vai trò **Doanh nghiệp**:

### 3.1. Trang Quan Sát & Thống Kê (Business Dashboard)
- Xem tổng quan lượt truy cập, lượt xem Tour 360°, số lượng bình luận và tương tác tại địa điểm thuộc sở hữu của doanh nghiệp.

### 3.2. Quản Lý Thông Tin Địa Điểm Kinh Doanh
- **Cập nhật thông tin chi tiết:** Cập nhật mô tả giới thiệu, giờ mở cửa, giá vé tham quan/dịch vụ, địa chỉ cụ thể.
- **Cập nhật thông tin liên hệ:** Số điện thoại hotline, Email, địa chỉ Website, đường link Fanpage Facebook.

### 3.3. Quản Lý Thư Viện Hình Ảnh
- Tải lên (Upload) hình ảnh thực tế chất lượng cao của cơ sở kinh doanh/địa điểm.
- Xóa ảnh cũ hoặc không còn phù hợp khỏi bộ sưu tập của địa điểm.

### 3.4. Quản Lý Tương Tác & Chăm Sóc Khách Hàng
- Xem danh sách toàn bộ bình luận của du khách tại địa điểm của mình.
- **Trả lời bình luận (Official Business Reply):** Phản hồi trực tiếp các thắc mắc/đánh giá của du khách dưới danh nghĩa **Chủ doanh nghiệp** (có huy hiệu Doanh nghiệp xác minh).
- Chỉnh sửa hoặc Xóa câu trả lời của doanh nghiệp.

---

## 4. ⚙️ QUẢN TRỊ VIÊN HỆ THỐNG (ADMIN & MODERATOR PANEL)

Hệ thống quản trị tập trung dành cho Quản trị viên (Admin) và Kiểm duyệt viên (Moderator) tại đường dẫn `/admin`:

### 4.1. Dashboard Tổng Quan Hệ Thống
- Xem biểu đồ và các số liệu thống kê thời gian thực: Tổng số người dùng, tổng số địa điểm, số lượt xem 360°, số bình luận, số đơn đăng ký mới,...

### 4.2. Quản Lý Danh Mục Địa Điểm (Category Management)
- Thêm, sửa, xóa các danh mục địa điểm (Di tích, Danh lam thắng cảnh, Lưu trú, Ẩm thực,...).
- Upload và quản lý Icon hiển thị tương ứng trên bản đồ.

### 4.3. Quản Lý Địa Điểm Du Lịch (Location Management)
- **Quản lý CRUD địa điểm:** Thêm mới, chỉnh sửa thông tin, xóa địa điểm du lịch.
- **Quản lý tọa độ & trạng thái:** Cấu hình kinh độ/vĩ độ (Lat/Lng), trạng thái ẩn/hiện (Draft / Published).
- **Quản lý Album ảnh 2D:** Upload ảnh đại diện, upload/xóa nhiều ảnh trong gallery.
- **Quản lý Album 360° (Panoramas):** Upload các tệp ảnh toàn cảnh 360 độ cho từng khu vực của địa điểm.
- **Quản lý Thuyết minh âm thanh (Audio Guide):** Upload/xóa file âm thanh thuyết minh địa điểm.
- **Tạo giọng nói AI tự động (AI Text-to-Speech - TTS):** Chuyển đổi tự động văn bản mô tả địa điểm thành file âm thanh thuyết minh MP3 sử dụng công nghệ AI TTS với nhiều tùy chọn giọng đọc.

### 4.4. Trình Biên Tập Tour VR 360° Tương Tác (360° VR Editor Tool)
- **Cấu hình góc nhìn ban đầu (Initial View):** Thiết lập thông số Pitch, Yaw, FOV mặc định cho cảnh 360°.
- **Đặt cảnh mặc định (Default Scene):** Chọn ảnh 360° làm điểm bắt đầu khi du khách mở Tour.
- **Quản lý Hotspot tương tác:**
  - Thêm điểm liên kết (Hotspot) trên không gian 360° để chuyển sang ảnh 360° khác hoặc hiển thị thông tin bổ sung.
  - Kéo thả vị trí Hotspot trực quan.
  - Sửa / Xóa Hotspot.
  - Lưu hàng loạt cấu hình Hotspots (Bulk save).
- **Quản lý tên cảnh (Scene Names):** Đổi tên các góc nhìn/khu vực 360° trong địa điểm.

### 4.5. Quản Lý Tin Tức & Bài Viết (News Management)
- Đăng bài viết tin tức, thông tin khuyến mãi, bài viết quảng bá du lịch.
- Chỉnh sửa, xóa bài viết.
- Chuyển đổi nhanh trạng thái Ẩn / Hiện bài viết.
- Upload hình ảnh minh họa cho bài viết.

### 4.6. Quản Lý Sự Kiện Văn Hóa - Du Lịch (Event Management)
- Tạo mới và quản lý lịch trình các sự kiện, lễ hội diễn ra tại Ninh Bình.
- Cấu hình địa điểm tổ chức, thời gian bắt đầu, thời gian kết thúc.
- Bật / Tắt trạng thái hiển thị sự kiện trên giao diện Client.

### 4.7. Quản Lý Người Dùng & Phân Quyền (User Management)
- Quản lý danh sách tài khoản toàn hệ thống.
- Phân quyền tài khoản: `Admin`, `Moderator`, `Business`, `Client`.
- Khóa / Mở khóa tài khoản người dùng vi phạm.
- Cộng / Trừ điểm thưởng (Points) trực tiếp cho người dùng.

### 4.8. Quản Lý Bình Luận & Kiểm Duyệt AI (Comment & AI Moderation)
- Quản lý tất cả bình luận trên hệ thống.
- Ẩn / Hiện các bình luận không phù hợp.
- **Quét nội dung vi phạm bằng AI (Scan AI Moderation):** Tự động phân tích nội dung bình luận bằng AI để phát hiện từ ngữ độc hại, xúc phạm hoặc nội dung rác (Spam).

### 4.9. Quản Lý Báo Cáo Vi Phạm & Góp Ý (Reports & Feedback)
- **Xử lý báo cáo vi phạm:** Xem danh sách báo cáo từ người dùng về các bình luận xấu và cập nhật trạng thái xử lý.
- **Quản lý Góp ý (Feedback):** Đọc góp ý/báo lỗi từ du khách và ghi chú trạng thái phản hồi.

### 4.10. Quản Lý Đóng Góp Địa Điểm Tự Cộng Đồng (Community Contributions)
- Duyệt bài đề xuất địa điểm mới do người dùng gửi lên.
- Phê duyệt để đưa vào cơ sở dữ liệu chính thức hoặc từ chối đề xuất.

### 4.11. Quản Lý Hồ Sơ Đăng Ký Doanh Nghiệp (Business Profile Approvals)
- Thẩm định hồ sơ đăng ký tài khoản Doanh nghiệp.
- Xem chi tiết thông tin pháp lý/ảnh xác minh do người dùng cung cấp.
- Bấm **Phê duyệt (Approve)** để nâng cấp tài khoản người dùng lên Doanh nghiệp hoặc **Từ chối (Reject)** kèm lý do cụ thể.

### 4.12. Quản Lý Yêu Cầu Dịch Vụ Tour 360° (Panorama Service Requests)
- Quản lý danh sách các đơn yêu cầu thuê chụp/dựng Tour 360° từ khách hàng.
- Cập nhật tiến độ xử lý đơn hàng (Mới tiếp nhận $\rightarrow$ Đang liên hệ $\rightarrow$ Hoàn thành $\rightarrow$ Hủy).

---

## 🛠️ CÔNG NGHỆ SỬ DỤNG (TECHNICAL STACK)
- **Backend Framework:** Laravel 10 (PHP 8.3)
- **Database:** MySQL
- **VR 360 Library:** Pannellum / Marzipano 360 Viewer
- **AI Integration:** Google Gemini / OpenRouter API (Tư vấn Chatbot, Lập kế hoạch Trip Planner, AI Comment Moderation, AI Text-to-Speech)
- **OAuth:** Laravel Socialite (Google Login)
