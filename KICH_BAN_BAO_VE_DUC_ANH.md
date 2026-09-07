# 📋 KỊCH BẢN BẢO VỆ ĐỒ ÁN & PHÂN TÍCH CHỨC NĂNG (ĐỨC ANH)

---

## 📌 TỔNG QUAN VAI TRÒ & 15 CHỨC NĂNG CỦA ĐỨC ANH

Nhóm chức năng phụ trách bao gồm **15 chức năng** thuộc 5 phân hệ trọng tâm:

| Mã | Phân hệ | Chức năng | Công nghệ / Kỹ thuật chính |
| :--- | :--- | :--- | :--- |
| **A3** | Bản đồ tương tác | Bản đồ POI | Leaflet.js, MarkerCluster, Custom Icon theo Category |
| **A4** | Bản đồ tương tác | Tìm kiếm & Lọc địa điểm | Client-side filter, Explore Drawer, `map.flyTo()` |
| **A5** | Bản đồ tương tác | GPS, Thời tiết & Tin tức trên map | HTML5 Geolocation API, Open-Meteo REST API, News Slider |
| **C1** | Xác thực người dùng | Đăng ký / Đăng nhập thường | Laravel Auth, Session Regenerate, FormRequest Validation |
| **C2** | Xác thực người dùng | Đăng nhập Google | Socialite OAuth 2.0, Account Linking |
| **D1** | AI & Trợ lý | Chatbot du lịch AI | Google Gemini API, Prompt Engineering (Context DB), Regex Sanitizer |
| **E1** | Doanh nghiệp | Đăng ký nâng cấp Doanh nghiệp | Multi-step Wizard, Claim / Tạo địa điểm mới |
| **E2** | Doanh nghiệp | Theo dõi / Hủy yêu cầu nâng cấp | Status Badge Tracker, Cancel Pending Request |
| **E3** | Doanh nghiệp | Dashboard Doanh nghiệp — Tổng quan | Data Aggregation (View, Rating, Favorite, Comments) |
| **E4** | Doanh nghiệp | Cập nhật Mô tả & Liên hệ | Public Contact Regex Validation (Zalo, Facebook, Phone) |
| **E5** | Doanh nghiệp | Quản lý Thư viện ảnh | ImageCompressionService, Storage Cleanup |
| **E6** | Doanh nghiệp | Trả lời Bình luận (Official Reply) | Parent-Child Comment Schema, Business Role Badge |
| **E7** | Doanh nghiệp | Yêu cầu dịch vụ Tour 360° | Duplicate Request Prevention, Request Status Tracker |
| **F8** | Quản trị (Admin) | Quản lý Bình luận & Quét AI | Gemini Toxicity Scan, Automated Comment Hiding |
| **F11**| Quản trị (Admin) | Duyệt hồ sơ Doanh nghiệp | DB Transaction, Role Upgrade, Location Ownership Mapping |

---

# 📜 CHI TIẾT 15 CHỨC NĂNG (TÁC DỤNG — LUỒNG CHẠY — KỊCH BẢN NÓI)

---

## 🗺️ NHÓM 1: BẢN ĐỒ DU LỊCH TƯƠNG TÁC (A3, A4, A5)

### 1. A3 — Bản đồ POI (Leaflet, MarkerCluster, Marker theo danh mục)
* **Tác dụng:** Hiển thị trực quan toàn bộ các điểm du lịch (Point of Interest) lên bản đồ địa lý Ninh Bình dưới dạng các ghim (Marker) có icon phân loại, giúp khách dễ định vị và tra cứu.
* **Luồng chạy:**
  1. Khi người dùng vào trang Bản đồ, JavaScript gọi API `/api/locations` lấy danh sách địa điểm (gồm ID, tên, lat, lng, category icon).
  2. Map khởi tạo bằng thư viện **Leaflet.js**.
  3. Để tránh giật lag khi có nhiều điểm nằm chồng lên nhau, hệ thống đưa tất cả Marker vào `L.markerClusterGroup()`.
  4. Khi click vào từng Marker, một Popup hiển thị thông tin tóm tắt (ảnh, tên, điểm đánh giá, link mở Tour 360).
* **🎙️ Kịch bản nói với hội đồng:**
  > *"Em thưa thầy/cô, chức năng này giúp trực quan hóa vị trí các điểm du lịch. Em sử dụng thư viện Leaflet kết hợp plugin MarkerCluster để gom nhóm các ghim lại khi thu nhỏ bản đồ, tránh làm rối mắt và tối ưu hiệu năng hiển thị trên trình duyệt. Mỗi loại hình địa điểm (di tích, ẩm thực, lưu trú...) đều có Icon nhận diện riêng."*

---

### 2. A4 — Tìm kiếm & Lọc bản đồ (Search, Category Filter, Explore Drawer)
* **Tác dụng:** Cho phép người dùng gõ từ khóa tìm địa điểm, lọc theo danh mục mong muốn và duyệt danh sách dưới dạng khay khám phá (Explore Drawer) để điều hướng nhanh.
* **Luồng chạy:**
  1. Người dùng nhập từ khóa tìm kiếm hoặc chọn danh mục ➔ JS trigger sự kiện filter client-side (hoặc gọi API lọc).
  2. Hệ thống ẩn/hiện các Marker tương ứng trên bản đồ và cập nhật lại tập hợp MarkerCluster.
  3. Đồng thời, thanh **Explore Drawer** (khay danh sách địa điểm phía dưới/bên hông) tự động cập nhật danh sách thẻ địa điểm tương ứng.
  4. Khi bấm vào 1 thẻ trong Explore Drawer, bản đồ sẽ tự động di chuyển (`map.flyTo()`) mượt mà tới vị trí Marker của địa điểm đó.
* **🎙️ Kịch bản nói với hội đồng:**
  > *"Thưa thầy/cô, chức năng này giúp người dùng chủ động thu hẹp phạm vi tìm kiếm. Em kết hợp giữa bộ lọc tức thì (Filter) và khay Explore Drawer. Khi người dùng chọn một địa điểm trong danh sách, em dùng hàm `flyTo` của Leaflet để pan bản đồ tới đúng tọa độ địa điểm đó với hiệu ứng chuyển cảnh mượt mà."*

---

### 3. A5 — GPS, Thời tiết & Tin tức trên bản đồ
* **Tác dụng:** Định vị vị trí hiện tại của khách trên bản đồ (GPS), xem dự báo thời tiết tại khu vực và xem slider tin tức mới nhất ngay trên giao diện bản đồ.
* **Luồng chạy:**
  1. **GPS:** Khi bấm nút Định vị, trình duyệt gọi `navigator.geolocation.getCurrentPosition()`, lấy tọa độ hiện tại và cắm Marker vị trí của khách, đồng thời phóng to bản đồ tới vị trí đó.
  2. **Thời tiết:** Hệ thống gọi API công khai **Open-Meteo** (truyền tọa độ trung tâm Ninh Bình/địa điểm) ➔ Trả về nhiệt độ, độ ẩm, thời tiết theo thời gian thực và hiển thị Widget thời tiết trên góc map.
  3. **Slider Tin tức:** Lấy danh sách tin mới từ DB hiển thị dạng Slider nhỏ ngay giao diện map để tăng tương tác khách hàng.
* **🎙️ Kịch bản nói với hội đồng:**
  > *"Chức năng này nâng cao trải nghiệm người dùng ngay trên giao diện bản đồ. Định vị GPS dùng HTML5 Geolocation để biết khách đang ở đâu. Widget thời tiết em tích hợp API RESTful của Open-Meteo theo tọa độ thực tế để hiển thị nhiệt độ và trạng thái thời tiết Ninh Bình mà không cần tốn chi phí API."*

---

## 🔐 NHÓM 2: XÁC THỰC & HỒ SƠ (C1, C2)

### 4. C1 — Đăng ký / Đăng nhập (Auth Local, Session, Validate)
* **Tác dụng:** Cho phép người dùng tạo tài khoản thường bằng Email/Mật khẩu, đăng nhập hệ thống bảo mật và lưu phiên hoạt động (Session).
* **Luồng chạy:**
  1. Người dùng điền form ➔ Gửi dữ liệu tới `AuthController@login` / `register`.
  2. Request đi qua `LoginRequest` / `RegisterRequest` để Validate định dạng email, độ dài mật khẩu, chống trùng lặp.
  3. Mật khẩu được mã hóa bằng `Hash::make()` (Bcrypt) lưu vào database (`password_hash`).
  4. Đăng nhập thành công ➔ `Auth::attempt()` tạo Session, tái tạo Session ID (`session()->regenerate()`) để chống tấn công **Session Fixation**. Đồng thời kiểm tra cột `status`, nếu tài khoản bị khóa (`inactive/banned`) thì logout ngay và trả về thông báo lỗi.
* **🎙️ Kịch bản nói với hội đồng:**
  > *"Chức năng xác thực này em xây dựng dựa trên cơ chế Auth chuẩn của Laravel. Dữ liệu đầu vào được Validate nghiêm ngặt qua FormRequest, mật khẩu mã hóa Bcrypt. Em cũng xử lý kỹ luồng phân quyền: kiểm tra xem tài khoản có đang bị khóa hay không và regenerate session sau khi login để đảm bảo an toàn bảo mật."*

---

### 5. C2 — Đăng nhập nhanh bằng Google (Socialite OAuth 2.0)
* **Tác dụng:** Giúp người dùng đăng nhập/đăng ký chỉ bằng 1-click thông qua tài khoản Google mà không cần nhớ mật khẩu.
* **Luồng chạy:**
  1. Khách bấm "Đăng nhập Google" ➔ Controller gọi `Socialite::driver('google')->redirect()`, chuyển hướng sang trang đăng nhập Google Cloud.
  2. Người dùng đồng ý cấp quyền ➔ Google trả về `code` tại đường dẫn Callback (`/auth/google/callback`).
  3. `AuthController@handleGoogleCallback` nhận dữ liệu Google User (Email, Name, Avatar, Google ID).
  4. **Logic kiểm tra:**
     - Nếu email đã tồn tại ➔ Cập nhật `provider = 'google'` & `provider_id` (nếu chưa có) và cho phép đăng nhập.
     - Nếu chưa tồn tại ➔ Hệ thống tự tạo tài khoản mới với username duy nhất, mật khẩu ngẫu nhiên an toàn, lưu avatar từ Google và tự động đăng nhập.
* **🎙️ Kịch bản nói với hội đồng:**
  > *"Em sử dụng thư viện Laravel Socialite chuẩn OAuth 2.0. Điểm tối ưu ở luồng này là em có xử lý hợp nhất tài khoản (Account Linking): Nếu email Google trùng với tài khoản đã đăng ký bằng mật khẩu trước đó, hệ thống sẽ tự động liên kết thay vì báo lỗi hoặc tạo tài khoản rác."*

---

## 🤖 NHÓM 3: AI & TRỢ LÝ THÔNG MINH (D1, F8)

### 6. D1 — Chatbot Du lịch AI (Gemini API + Context DB Địa điểm)
* **Tác dụng:** Trợ lý ảo thông minh tư vấn lịch trình, ẩm thực, phương tiện di chuyển tại Ninh Bình 24/7.
* **Luồng chạy:**
  1. Người dùng gửi câu hỏi từ cửa sổ Chat ➔ AJAX gửi request tới `ChatbotController@sendMessage`.
  2. `ChatbotService` truy vấn DB lấy toàn bộ danh sách địa điểm đang công bố (`status = published`) gồm Tên, Danh mục, Tọa độ, Địa chỉ, Mô tả ngắn...
  3. Ghép danh sách này vào **System Prompt** để huấn luyện Gemini "ngay lập tức" (In-context Learning / RAG đơn giản): Ép AI **chỉ được gợi ý địa điểm có thật trong DB**, tuyệt đối không bịa địa điểm ảo.
  4. Đưa ra quy định trả về link địa điểm dạng `[Tên](loc:ID)`.
  5. Khi Gemini phản hồi, `ChatbotService` chạy hàm `sanitizeLocationLinks()` kiểm tra regex để lọc bỏ/sửa lại các link ID bị sai trước khi hiển thị cho người dùng.
* **🎙️ Kịch bản nói với hội đồng:**
  > *"Thưa thầy/cô, trợ lý AI của em được tích hợp Google Gemini API. Điểm đặc biệt là em đã áp dụng kỹ thuật Prompt Engineering nạp ngữ cảnh dữ liệu DB địa điểm thực tế vào System Prompt. Kỹ thuật này giúp giải quyết triệt để hiện tượng 'ảo giác' (Hallucination) của AI - AI sẽ chỉ tư vấn và đưa ra đường dẫn chính xác tới các địa điểm hiện có trên hệ thống của chúng em."*

---

### 7. F8 — Quản lý Bình luận & Quét tự động bằng AI (Admin moderation)
* **Tác dụng:** Giúp Admin quản lý danh sách đánh giá/bình luận của khách, hỗ trợ nút "Scan AI" để Gemini quét phát hiện bình luận có nội dung thô tục, rác (spam), hoặc xúc phạm để tự động ẩn/xử lý.
* **Luồng chạy:**
  1. Trang Admin hiển thị danh sách bình luận (có phân trang, lọc status: `visible/hidden`).
  2. Khi bấm nút **Quét AI** (cho 1 bình luận hoặc quét hàng loạt):
  3. Server gửi nội dung bình luận qua `GeminiClient` với Prompt yêu cầu phân tích độc hại (Toxicity Check): Trả về kết quả `is_violating` (có vi phạm không) và `reason` (lý do).
  4. Nếu AI phát hiện vi phạm ➔ Hệ thống cập nhật trạng thái bình luận thành `hidden` (ẩn) và ghi log lý do để Admin xem lại.
* **🎙️ Kịch bản nói với hội đồng:**
  > *"Để giảm bớt khối lượng công việc kiểm duyệt thủ công cho Admin, em đã tích hợp mô hình AI Gemini để quét độc hại tự động. AI sẽ phân tích ngữ nghĩa bình luận xem có chứa từ ngữ thô tục, spam hay công kích không. Nếu có vi phạm, hệ thống tự động ẩn bình luận đó và cảnh báo lý do cho quản trị viên kiểm tra lại."*

---

## 🏢 NHÓM 4: PHÂN HỆ DOANH NGHIỆP (E1 ➔ E7)

### 8. E1 — Đăng ký nâng cấp Doanh nghiệp (Wizard & Claim / Tạo mới địa điểm)
* **Tác dụng:** Cho phép người dùng thường gửi hồ sơ đăng ký trở thành Chủ doanh nghiệp, có thể chọn "Claim" (xác nhận chủ sở hữu) một địa điểm đã có sẵn trên hệ thống hoặc tạo một địa điểm kinh doanh mới.
* **Luồng chạy:**
  1. Người dùng điền Wizard gồm: Tên doanh nghiệp, Mã số thuế, Giấy phép kinh doanh (upload ảnh), thông tin liên hệ và vị trí địa lý (Lat/Lng).
  2. **Logic kiểm tra địa điểm:** Người dùng chọn nhận địa điểm sẵn có trong hệ thống hoặc nhập thông tin địa điểm mới.
  3. Hệ thống kiểm tra khoảng cách GPS (nếu cần) hoặc kiểm tra xem địa điểm đó đã có ai claim chưa (`created_by`).
  4. Tạo bản ghi trong bảng `business_profiles` với trạng thái `pending` (chờ duyệt).
* **🎙️ Kịch bản nói với hội đồng:**
  > *"Luồng đăng ký Doanh nghiệp được thiết kế dưới dạng Wizard từng bước rõ ràng. Em xử lý 2 trường hợp nghiệp vụ thực tế: Doanh nghiệp đăng ký một điểm kinh doanh hoàn toàn mới, hoặc Doanh nghiệp nhận là chủ của một danh thắng/nhà hàng đã có sẵn trên bản đồ hệ thống. Hồ sơ gửi lên sẽ ở trạng thái Chờ duyệt (Pending)."*

---

### 9. E2 — Theo dõi / Hủy yêu cầu nâng cấp Doanh nghiệp
* **Tác dụng:** Giúp người dùng theo dõi tiến độ duyệt hồ sơ doanh nghiệp (Chờ duyệt / Đã duyệt / Từ chối kèm lý do) và cho phép bấm "Hủy yêu cầu" nếu muốn thay đổi thông tin khi hồ sơ còn đang chờ.
* **Luồng chạy:**
  1. Trong trang Profile cá nhân, hệ thống kiểm tra bảng `business_profiles` theo `user_id`.
  2. Hiển thị Badge trạng thái tương ứng:
     - `pending`: Hiển thị nút **"Hủy yêu cầu"**. Bấm hủy ➔ Xóa bản ghi pending / giải phóng địa điểm tạm giữ.
     - `rejected`: Hiển thị lý do từ chối do Admin nhập ➔ Cho phép gửi lại hồ sơ mới.
     - `approved`: Hiển thị nút truy cập thẳng vào **Business Dashboard**.
* **🎙️ Kịch bản nói với hội đồng:**
  > *"Chức năng này giúp minh bạch trạng thái xử lý cho người dùng. Người dùng có quyền Chủ động hủy yêu cầu khi còn ở trạng thái Pending nếu phát hiện điền sai thông tin. Trường hợp bị từ chối, hệ thống hiển thị rõ lý do Admin đã phản hồi để người dùng biết và chỉnh sửa lại."*

---

### 10. E3 — Dashboard Doanh nghiệp — Tổng quan
* **Tác dụng:** Trang quản trị riêng cho Chủ doanh nghiệp (đã duyệt) để xem các con số thống kê hiệu quả kinh doanh của địa điểm mình quản lý.
* **Luồng chạy:**
  1. Middleware kiểm tra `Auth::user()` có quyền `business` và có `business_profile` trạng thái `approved`.
  2. Query lấy địa điểm tương ứng (`created_by = user_id`).
  3. Thống kê từ DB:
     - **Lượt xem Tour 360:** `location->view_count`.
     - **Lượt yêu thích:** Đếm trong `favorite_locations`.
     - **Điểm đánh giá trung bình:** `AVG(rating)` từ bảng `comments` (chỉ tính comment gốc `parent_id IS NULL` và trạng thái `visible`).
     - **Danh sách bình luận mới nhất** để theo dõi tương tác.
* **🎙️ Kịch bản nói với hội đồng:**
  > *"Đây là bảng điều khiển dành riêng cho Chủ cơ sở kinh doanh. Em tổng hợp dữ liệu theo thời gian thực từ các bảng bình luận, lượt thích và lượt xem Tour 360 để tính ra điểm trung bình rating và các chỉ số thống kê trực quan, giúp doanh nghiệp nắm được mức độ quan tâm của du khách."*

---

### 11. E4 — Cập nhật Mô tả & Liên hệ công khai Doanh nghiệp
* **Tác dụng:** Cho phép chủ doanh nghiệp chủ động chỉnh sửa bài viết mô tả giới thiệu và cập nhật SĐT công khai, Zalo, Facebook hiển thị cho khách truy cập.
* **Luồng chạy:**
  1. Chủ DN nhập mô tả mới ➔ `BusinessDashboardController@updateInfo` cập nhật đồng thời vào `business_profiles` và bảng `locations`.
  2. Chủ DN nhập Kênh liên hệ ➔ `updateContact` Validate dữ liệu kỹ càng bằng Regex (SĐT từ 8-20 số, Zalo chuẩn link/SĐT, Facebook phải có domain `facebook.com` hoặc `fb.com`).
  3. Dữ liệu lưu vào DB và hiển thị ngay ra ngoài trang detail/map cho khách du lịch liên hệ.
* **🎙️ Kịch bản nói với hội đồng:**
  > *"Chức năng này giúp chủ doanh nghiệp quản lý thông tin truyền thông của mình. Em có viết các bộ quy tắc Regex Validate chuẩn xác cho SĐT công khai, link Zalo và Facebook để đảm bảo thông tin liên hệ hiển thị ra ngoài cho khách luôn đúng định dạng và an toàn."*

---

### 12. E5 — Doanh nghiệp — Quản lý Thư viện ảnh (Upload & Xóa)
* **Tác dụng:** Cho phép chủ doanh nghiệp upload thêm ảnh thực tế của nhà hàng/khách sạn/điểm tham quan hoặc xóa ảnh cũ.
* **Luồng chạy:**
  1. Chủ DN chọn upload ảnh ➔ Gửi form file image.
  2. Controller gọi `ImageCompressionService` để tự động nén dung lượng/tối ưu kích thước ảnh trước khi lưu vào disk `storage/app/public/locations/gallery`.
  3. Lưu thông tin đường dẫn ảnh vào bảng `location_images`.
  4. Nút Xóa ảnh ➔ Xóa bản ghi trong DB đồng thời xóa hẳn file vật lý trong Storage bằng `Storage::disk()->delete()`.
* **🎙️ Kịch bản nói với hội đồng:**
  > *"Để tránh quá tải bộ nhớ và giúp trang web tải nhanh, khi doanh nghiệp upload ảnh em có cho qua dịch vụ `ImageCompressionService` để nén tối ưu dung lượng ảnh trước khi lưu. Khi xóa ảnh, hệ thống tự động dọn sạch file vật lý trong ổ cứng server để tránh rác dung lượng."*

---

### 13. E6 — Doanh nghiệp — Trả lời Bình luận (Official Reply)
* **Tác dụng:** Cho phép chủ doanh nghiệp phản hồi (Reply) trực tiếp các đánh giá của du khách dưới danh nghĩa "Câu trả lời chính thức từ Chủ cơ sở".
* **Luồng chạy:**
  1. Tại Dashboard, chủ DN gõ nội dung phản hồi dưới một bình luận của khách.
  2. Gửi request ➔ Lưu bản ghi mới vào bảng `comments` với `parent_id` = ID bình luận của khách, `user_id` = ID chủ DN.
  3. Ngoài giao diện người dùng (Chi tiết 360°/Địa điểm), câu trả lời này được xếp thụt lùi vào trong và có Badge **"Chủ doanh nghiệp"** để du khách phân biệt với bình luận thường.
  4. Chủ DN có quyền Sửa hoặc Xóa phản hồi do chính mình viết.
* **🎙️ Kịch bản nói với hội đồng:**
  > *"Chức năng này tạo cầu nối tương tác 2 chiều giữa du khách và cơ sở kinh doanh. Em mô hình hóa theo dạng bình luận phân cấp (parent-child). Khi chủ doanh nghiệp trả lời, hệ thống sẽ đánh dấu phân biệt bằng Badge chủ cơ sở ở giao diện bên ngoài."*

---

### 14. E7 — Doanh nghiệp — Yêu cầu dịch vụ Tour 360°
* **Tác dụng:** Cho phép chủ doanh nghiệp gửi yêu cầu tới Admin để đặt lịch dịch vụ chụp ảnh/dựng Tour thực tế ảo 360° cho cơ sở của mình.
* **Luồng chạy:**
  1. Trong Dashboard, chủ DN điền form yêu cầu (Ghi chú nhu cầu, thời gian mong muốn).
  2. Hệ thống kiểm tra bảng `panorama_service_requests`: Nếu đã có 1 yêu cầu đang ở trạng thái `pending` thì chặn không cho gửi trùng lặp.
  3. Nếu chưa có ➔ Lưu yêu cầu mới với `status = pending`.
  4. Dashboard hiển thị danh sách các yêu cầu đã gửi kèm trạng thái tiến độ (Chờ xử lý / Đang liên hệ / Hoàn thành).
* **🎙️ Kịch bản nói với hội đồng:**
  > *"Chức năng này giúp doanh nghiệp dễ dàng đăng ký dịch vụ làm Tour VR 360° ngay từ dashboard. Em có cài đặt logic chặn chống spam: nếu doanh nghiệp đang có một đơn đăng ký ở trạng thái Chờ xử lý thì hệ thống sẽ yêu cầu chờ Admin phản hồi chứ không cho gửi dồn dập."*

---

### 15. F11 — Admin Duyệt hồ sơ Doanh nghiệp (Approve / Reject / Revoke)
* **Tác dụng:** Trang dành cho Admin xem xét các đơn đăng ký doanh nghiệp. Admin có thể Duyệt (Approve), Từ chối (Reject), hoặc Thu hồi quyền doanh nghiệp (Revoke).
* **Luồng chạy:**
  1. Admin vào danh sách hồ sơ đăng ký (`Admin/BusinessProfileController`).
  2. **Khi bấm APPROVE (Duyệt):**
     - Cập nhật `business_profiles.status = approved`.
     - Cập nhật vai trò người dùng `users.role = 'business'`.
     - Gán chủ sở hữu địa điểm `locations.created_by = user_id` và đổi trạng thái địa điểm thành `published`.
  3. **Khi bấm REJECT (Từ chối):**
     - Admin nhập lý do từ chối ➔ Cập nhật status `rejected`, ghi nhận `reject_reason`.
  4. **Khi bấm REVOKE (Thu hồi quyền):**
     - Chuyển `role` người dùng về lại `user` thường, hủy liên kết `created_by` tại địa điểm để giải phóng hoặc ẩn địa điểm đó.
* **🎙️ Kịch bản nói với hội đồng:**
  > *"Thưa thầy/cô, đây là chức năng quản trị luồng duyệt Doanh nghiệp. Điểm quan trọng nhất ở đây là tính toàn vẹn dữ liệu: Khi Admin phê duyệt, hệ thống sẽ thực hiện một Transaction đồng bộ cùng lúc: nâng role người dùng thành Business, kích hoạt hồ sơ và gắn quyền sở hữu địa điểm cho người dùng đó. Ngược lại khi Thu hồi, hệ thống sẽ hạ role và ngắt liên kết an toàn."*

---

# 💡 MẸO "VÀNG" KHI TRẢ LỜI BẢO VỆ DÀNH CHO ĐỨC ANH

1. **Giữ thái độ tự tin, ngắn gọn:**
   * Trả lời thẳng vào câu hỏi trong **1-2 câu đầu tiên** (Nói rõ chức năng làm gì và dùng công nghệ gì).
   * Không nói ngập ngừng dạng *"Em cũng không rõ..."*, hãy thay bằng: *"Phần này em áp dụng theo chuẩn thiết kế của Laravel/Javascript là..."*

2. **Các từ khóa kỹ thuật "đắt giá" nên cài vào khi nói:**
   * **Bản đồ:** LeafletJS, MarkerCluster, `flyTo`, Open-Meteo REST API, Geolocation HTML5.
   * **Auth & Security:** OAuth 2.0 (Socialite), Session Regenerate, Bcrypt Hash, FormRequest Validation.
   * **AI:** Prompt Engineering, In-context Learning, Anti-Hallucination (chống bịa), RegEx Sanitizer.
   * **Doanh nghiệp & Admin:** Database Transaction, Relational Mapping (Parent-Child Comment), Image Compression Service.

3. **Cách "chặn" thầy cô đào sâu:**
   * Khi giới thiệu xong luồng, hãy chủ động nói thêm câu chốt về **biện pháp bảo mật hoặc tối ưu** bạn đã làm (Ví dụ: *"Em đã xử lý cả trường hợp nén ảnh tránh đầy ổ cứng"*, hoặc *"Em có xử lý validate Regex và chống Session Fixation"*). Khi nghe thấy bạn đã nghĩ đến các trường hợp biên (edge cases), thầy cô sẽ đánh giá rất cao và chuyển sang câu hỏi khác!
