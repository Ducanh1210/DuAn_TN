# DANH SÁCH CHỨC NĂNG — Ninh Bình Travel Hub

Website khám phá và trải nghiệm Ninh Bình – Travel Hub  
Phân theo 4 nhóm quyền: Khách tham quan | Thành viên | Chủ doanh nghiệp | Quản trị viên / Kiểm duyệt viên

> Bản này đối chiếu theo code hiện tại (Laravel 10, routes/controllers/views). Không ghi chức năng chưa có trong hệ thống.

---

## Bảng tổng quan phân quyền

| Nhóm đối tượng | Vai trò | Mô tả ngắn |
| :--- | :--- | :--- |
| Khách tham quan | Guest / Public Client | Chưa đăng nhập: xem landing, bản đồ, Tour 360°, tin tức, sự kiện, chatbot AI, tạo lịch trình AI (chưa lưu), gửi yêu cầu dịch vụ Tour 360°. |
| Thành viên đã đăng nhập | Authenticated User | Toàn bộ quyền Guest + hồ sơ, yêu thích, đánh giá, đề xuất địa điểm, báo cáo/góp ý, lưu lịch trình, nhiệm vụ–điểm thưởng, đăng ký doanh nghiệp. |
| Chủ doanh nghiệp | Business Owner | Tài khoản đã được Admin duyệt: dashboard địa điểm, cập nhật mô tả/liên hệ, quản lý ảnh, trả lời bình luận. |
| Quản trị viên hệ thống | Admin & Moderator | Quản trị dữ liệu, biên tập 360°, kiểm duyệt, duyệt doanh nghiệp/đóng góp, soft-delete địa điểm. *(Cùng nhóm route `/admin`.)* |

---

## Bảng chức năng chi tiết (4 cột)

| Quyền / Vai trò | Chức năng | Chi tiết chức năng | Mô tả từng chức năng nhỏ |
| :--- | :--- | :--- | :--- |
| Khách tham quan (Guest / Public Client) | Trang chủ & Giới thiệu (Landing Page) | Trải nghiệm trang giới thiệu | Xem hình ảnh / nội dung giới thiệu du lịch Ninh Bình; thống kê số địa điểm, tin tức, sự kiện đã công bố. |
|  |  | Danh sách địa điểm nổi bật | Hiển thị địa điểm có lượt xem cao kèm ảnh đại diện và thông tin Tour 360° (nếu có). |
|  |  | Bản tin & Sự kiện mới nhất | Xem tin tức du lịch và sự kiện sắp diễn ra trên trang chủ. |
|  |  | Trang Giới thiệu | Đọc nội dung giới thiệu vùng đất Ninh Bình và một số địa điểm tiêu biểu. |
|  |  | Đăng ký Dịch vụ Tour 360° | Gửi form đăng ký dịch vụ chụp/dựng VR Tour 360° (tên, SĐT, địa điểm, nhu cầu); hệ thống chặn gửi trùng khi còn yêu cầu đang chờ. |
|  | Bản đồ Du lịch tương tác (Interactive Map) | Xem bản đồ POI | Hiển thị vị trí các điểm du lịch Ninh Bình trên bản đồ số (Leaflet), có gom nhóm marker (MarkerCluster). |
|  |  | Tìm kiếm địa điểm | Nhập từ khóa để tìm và đưa bản đồ tới địa điểm tương ứng. |
|  |  | Bộ lọc danh mục (Category Filter) | Lọc địa điểm theo loại hình (danh lam, di tích, lưu trú, ẩm thực,…). |
|  |  | Marker theo danh mục | Mỗi danh mục có icon riêng trên bản đồ. |
|  |  | Định vị GPS | Xác định vị trí hiện tại của người dùng trên bản đồ (nếu trình duyệt cho phép). |
|  |  | Ranh giới khu vực | Hiển thị lớp overlay ranh giới khu vực trên bản đồ. |
|  |  | Thời tiết / tin tức trên bản đồ | Widget thời tiết (Open-Meteo) và slider tin tức mới nhất ngay trên giao diện bản đồ. |
|  |  | Khay Khám phá (Explore drawer) | Danh sách địa điểm dạng thẻ ngang; chọn địa điểm để bay tới marker trên bản đồ. |
|  | Trải nghiệm Tour thực tế ảo (360° VR Viewer) | Xem toàn cảnh 360° | Khám phá địa điểm bằng trình xem panorama 360° (Marzipano). |
|  |  | Chuyển cảnh Hotspot | Bấm hotspot để chuyển sang góc nhìn / khu vực 360° khác hoặc xem thông tin bổ sung. |
|  |  | Thuyết minh âm thanh (Audio Guide) | Bật/tắt bài thuyết minh âm thanh giới thiệu địa điểm. |
|  |  | Bộ sưu tập ảnh (Gallery) | Xem ảnh 2D chất lượng cao của địa điểm. |
|  |  | Đếm lượt xem | Tự tăng `view_count` khi người dùng mở trang Tour 360°. |
|  |  | Xem bình luận & đánh giá | Đọc đánh giá/bình luận của thành viên và phản hồi của chủ doanh nghiệp (nếu có). |
|  | Tin tức & Sự kiện du lịch | Trang danh sách Tin tức | Xem danh sách bài viết tin tức du lịch đã công bố. |
|  |  | Trang danh sách Sự kiện | Xem sự kiện/lễ hội kèm thời gian và địa điểm tổ chức. |
|  |  | Trang chi tiết bài viết / sự kiện | Đọc nội dung chi tiết kèm hình ảnh minh họa. |
|  | Trợ lý AI & Lập kế hoạch chuyến đi | Chatbot tư vấn du lịch AI | Trò chuyện với trợ lý AI (Google Gemini) về du lịch, ẩm thực, di chuyển tại Ninh Bình; gợi ý dựa trên dữ liệu địa điểm trong hệ thống. |
|  |  | Tạo lịch trình AI (Trip Planner) | Trả lời khảo sát (loại chuyến, số ngày, ngân sách, nhịp độ, sở thích…) → AI tạo lịch trình theo ngày; khách xem được kết quả (lưu cần đăng nhập). |
| Thành viên đã đăng nhập (Authenticated User) | Xác thực & Quản lý tài khoản | Đăng ký / Đăng nhập | Đăng ký và đăng nhập bằng email & mật khẩu. |
|  |  | Đăng nhập nhanh Google | Đăng nhập / liên kết tài khoản qua Google OAuth 2.0. |
|  |  | Hồ sơ cá nhân | Cập nhật tên hiển thị; xem điểm, thông báo, yêu thích, bình luận, lịch trình đã lưu, trạng thái doanh nghiệp. |
|  |  | Đổi ảnh đại diện (Avatar) | Thay đổi ảnh đại diện tài khoản. |
|  |  | Đổi mật khẩu | Cập nhật mật khẩu (áp dụng tài khoản đăng ký bằng email/mật khẩu). |
|  |  | Xóa tài khoản | Yêu cầu xóa tài khoản khỏi hệ thống (có xác nhận). |
|  | Tương tác địa điểm & Cộng đồng | Yêu thích địa điểm | Thêm/xóa địa điểm vào danh sách đã lưu; xem lại trong hồ sơ. |
|  |  | Đánh giá / bình luận Tour 360° | Gửi đánh giá (sao) và bình luận tại trang Tour 360° (mỗi địa điểm một đánh giá của mình). |
|  |  | Sửa / Xóa bình luận của mình | Chỉnh sửa hoặc xóa bình luận do chính mình đăng. |
|  |  | Đề xuất địa điểm mới | Gửi thông tin địa điểm chưa có trên hệ thống (tên, mô tả, vị trí, ảnh…) để Admin kiểm duyệt. |
|  |  | Báo cáo vi phạm | Báo cáo địa điểm hoặc bình luận có nội dung không phù hợp. |
|  |  | Góp ý / báo lỗi hệ thống | Gửi góp ý hoặc báo lỗi (sai thông tin, vị trí, ảnh, gợi ý cải thiện…). |
|  | Quản lý lịch trình chuyến đi | Lưu lịch trình AI | Lưu kế hoạch do Trip Planner tạo vào tài khoản. |
|  |  | Xem / Xóa lịch trình đã lưu | Xem lại chi tiết lịch trình đã lưu hoặc xóa khi không còn nhu cầu. |
|  | Hệ thống Nhiệm vụ & Đổi thưởng (Gamification) | Điểm danh hàng ngày | Điểm danh để nhận điểm thưởng theo chuỗi ngày. |
|  |  | Nhiệm vụ (Missions) | Thực hiện nhiệm vụ (xem Tour 360°, bình luận, yêu thích, online…) để nhận điểm / phần thưởng. |
|  |  | Thưởng mốc (Milestones) | Nhận khung đại diện khi đạt mốc điểm (ví dụ 100 / 200 / 500). |
|  |  | Đổi quà & Khung đại diện | Dùng điểm đổi khung avatar / phần thưởng (voucher) trong cửa hàng. |
|  |  | Trang bị khung đại diện | Chọn khung đang dùng; khung hiển thị quanh avatar (ví dụ ở khu vực bình luận). |
|  |  | Bảng xếp hạng | Xem top người dùng theo điểm. |
|  | Đăng ký tài khoản Doanh nghiệp | Gửi hồ sơ nâng cấp | Điền wizard: tên DN, loại hình, danh mục, địa chỉ Ninh Bình, vị trí bản đồ, ảnh, giấy tờ xác minh. |
|  |  | Theo dõi / Hủy yêu cầu | Xem trạng thái chờ duyệt / đã duyệt / từ chối; hủy yêu cầu khi còn chờ duyệt. |
|  |  | Nhận thông báo hệ thống | Nhận thông báo in-app (ví dụ địa điểm bị Admin gỡ / được khôi phục) kèm hướng dẫn hỗ trợ. |
| Chủ doanh nghiệp (Business Owner) | Dashboard doanh nghiệp | Xem tổng quan | Xem lượt xem Tour 360°, số yêu thích, điểm đánh giá trung bình và danh sách bình luận tại địa điểm thuộc sở hữu. |
|  | Quản lý thông tin địa điểm | Cập nhật mô tả | Cập nhật phần mô tả / giới thiệu địa điểm (đồng bộ lên bản đồ). |
|  |  | Cập nhật liên hệ công khai | Cập nhật SĐT công khai, Zalo, Facebook hiển thị cho khách. |
|  | Quản lý thư viện hình ảnh | Tải lên / Xóa ảnh | Upload hoặc xóa ảnh thực tế của cơ sở / địa điểm. |
|  | Chăm sóc khách hàng | Xem bình luận | Xem toàn bộ đánh giá của khách tại địa điểm của mình. |
|  |  | Trả lời bình luận (Official Reply) | Phản hồi dưới danh nghĩa chủ doanh nghiệp (có nhận diện doanh nghiệp trên trang 360°). |
|  |  | Sửa / Thu hồi câu trả lời | Cập nhật hoặc xóa phản hồi đã gửi. |
|  | Dịch vụ Tour 360° | Gửi / Theo dõi yêu cầu | Gửi và theo dõi yêu cầu thuê chụp/dựng Tour 360° từ dashboard / hồ sơ. |
| Quản trị viên hệ thống (Admin & Moderator) | Dashboard tổng quan | Việc cần xử lý & số liệu | Xem hàng chờ (doanh nghiệp, đề xuất, báo cáo, góp ý) và các chỉ số tổng quan (người dùng, địa điểm, tin/sự kiện, bình luận…). |
|  | Quản lý danh mục địa điểm | Thêm / Sửa / Xóa danh mục | Quản lý danh mục (di tích, danh lam, lưu trú, ẩm thực,…). |
|  |  | Icon danh mục | Upload / quản lý icon hiển thị trên bản đồ. |
|  | Quản lý địa điểm du lịch | CRUD địa điểm | Thêm, sửa thông tin địa điểm; xóa tạm (chuyển thùng rác). |
|  |  | Thùng rác & Khôi phục | Xem địa điểm đã xóa tạm; khôi phục hoặc xóa vĩnh viễn (kèm dọn file ảnh/360). |
|  |  | Xóa địa điểm doanh nghiệp | Bắt buộc nhập lý do; gửi thông báo tới chủ DN; nếu là địa điểm cuối → tạm ngưng vai trò doanh nghiệp (có thể khôi phục cùng địa điểm). |
|  |  | Tọa độ & trạng thái | Cấu hình Lat/Lng; trạng thái Draft / Published (và các trạng thái khác nếu có). |
|  |  | Album ảnh 2D | Upload ảnh đại diện; upload/xóa ảnh gallery. |
|  |  | Album 360° (Panoramas) | Upload ảnh toàn cảnh 360° cho từng khu vực của địa điểm. |
|  |  | Audio Guide | Upload / xóa file âm thanh thuyết minh. |
|  |  | Tạo thuyết minh AI (TTS) | Chuyển văn bản mô tả thành file âm thanh thuyết minh bằng VieNeu-TTS (chọn giọng, tham số). |
|  | Trình biên tập Tour VR 360° | Góc nhìn ban đầu | Thiết lập Pitch, Yaw, FOV mặc định cho cảnh. |
|  |  | Cảnh mặc định | Chọn ảnh 360° mở đầu khi khách vào Tour. |
|  |  | Quản lý Hotspot | Thêm, kéo thả vị trí, sửa, xóa hotspot; lưu hàng loạt (bulk save). |
|  |  | Đổi tên cảnh | Đặt tên các góc nhìn / khu vực 360° trong địa điểm. |
|  | Quản lý Tin tức | CRUD & Ẩn/Hiện | Đăng, sửa, xóa bài; toggle ẩn/hiện; upload ảnh minh họa. |
|  | Quản lý Sự kiện | CRUD & Ẩn/Hiện | Tạo/sửa sự kiện; cấu hình thời gian & địa điểm tổ chức; bật/tắt hiển thị. |
|  | Quản lý Người dùng | Danh sách & phân quyền | Quản lý tài khoản; gán vai trò Admin / Moderator / User. |
|  |  | Khóa / Mở khóa | Khóa hoặc mở khóa tài khoản vi phạm. |
|  |  | Cộng / Trừ điểm | Điều chỉnh điểm thưởng trực tiếp cho người dùng. |
|  | Bình luận & Kiểm duyệt AI | Quản lý bình luận | Xem, ẩn/hiện, xóa bình luận trên hệ thống. |
|  |  | Quét AI (Scan Moderation) | Phân tích nội dung bình luận bằng AI để phát hiện spam / nội dung không phù hợp. |
|  | Báo cáo & Góp ý | Xử lý báo cáo | Xem báo cáo địa điểm/bình luận; cập nhật trạng thái xử lý. |
|  |  | Quản lý góp ý | Đọc góp ý/báo lỗi; cập nhật trạng thái / ghi chú; xóa nếu cần. |
|  | Đóng góp địa điểm từ cộng đồng | Duyệt đề xuất | Xem chi tiết đề xuất (kèm điểm gần trên bản đồ nếu có). |
|  |  | Phê duyệt / Từ chối / Yêu cầu bổ sung | Cập nhật trạng thái đề xuất và ghi chú; đưa địa điểm lên bản đồ bằng thao tác tạo/quản lý Location của Admin. |
|  | Hồ sơ đăng ký Doanh nghiệp | Thẩm định hồ sơ | Xem thông tin pháp lý, ảnh xác minh do người dùng gửi. |
|  |  | Phê duyệt / Từ chối | Duyệt → nâng role Business và tạo/khôi phục địa điểm trên bản đồ; từ chối kèm lý do. |
|  | Yêu cầu Dịch vụ Tour 360° | Quản lý đơn | Danh sách yêu cầu thuê chụp/dựng Tour 360°. |
|  |  | Cập nhật tiến độ | Chuyển trạng thái: Chờ liên hệ → Đã liên hệ → Hoàn thành / Hủy; ghi chú nội bộ. |

---

## Công nghệ chính (khớp dự án)

| Thành phần | Công nghệ |
| :--- | :--- |
| Backend | PHP 8.1+, Laravel 10 |
| Cơ sở dữ liệu | MySQL |
| Bản đồ | Leaflet, MarkerCluster, GeoJSON ranh giới |
| Tour 360° | Marzipano (client viewer) + biên tập hotspot phía Admin |
| AI | Google Gemini (Chatbot, Trip Planner, kiểm duyệt bình luận) |
| Thuyết minh TTS | VieNeu-TTS |
| Định tuyến lịch trình | OSRM / Haversine (tối ưu khoảng cách ngày) |
| Đăng nhập Google | Laravel Socialite (OAuth 2.0) |
| Thời tiết | Open-Meteo API |

---

## Ghi chú chỉnh so với bản cũ (để khỏi overclaim khi bảo vệ)

1. **Xóa địa điểm Admin** = xóa tạm (thùng rác) + khôi phục / xóa vĩnh viễn; địa điểm DN bắt buộc lý do + thông báo.
2. **Bản đồ** bổ sung: tìm kiếm, GPS, thời tiết, khay Khám phá, MarkerCluster (không chỉ lọc + banner tin).
3. **Hồ sơ thành viên**: cập nhật tên hiển thị / avatar / mật khẩu (không mô tả như có đủ SĐT–địa chỉ–bio nếu form chưa có).
4. **Bình luận**: thành viên đánh giá của mình; **chủ DN** mới trả lời chính thức — không ghi “reply mọi user”.
5. **Đề xuất cộng đồng**: Admin duyệt trạng thái; đưa lên bản đồ qua quản lý Location (không auto-publish mơ hồ).
6. **Dashboard DN**: mô tả + liên hệ công khai (SĐT/Zalo/Facebook); không ghi quản lý giá vé/giờ mở cửa nếu portal chưa hỗ trợ.
7. **Dashboard Admin**: hàng chờ + số liệu tổng quan (không khẳng định “biểu đồ realtime lượt xem 360°” nếu UI chưa có).
8. Tên sản phẩm dùng **Ninh Bình Travel Hub** (không dùng tên cũ “Ninh Bình POI” trên tài liệu nộp).
