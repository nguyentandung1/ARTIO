# Welcome to ARTIO!
Dự án được tạo ra với mục đích đem lại cho người dùng một trải nghiệm trưng bày, mua bán và đấu giá tác phẩm nghệ thuật trực tuyến được tối ưu hoá. 
ARTIO hướng tới đối tượng người dùng là những người yêu nghệ thuật, nhà sưu tầm và người bán tác phẩm nghệ thuật, giúp tiết kiệm thời gian, dễ dàng tìm kiếm, mua bán và tham gia đấu giá các tác phẩm nghệ thuật.

## Người thực hiện
  - Nguyễn Tấn Dũng
# Chức năng chính 
## Role 1: Người mua
 -	Đăng nhập/ Đăng ký
 -	Xem giao diện trang chủ, các danh mục
 -	Tìm kiếm sản phẩm
 -	Mua hàng
 -	Đánh giá sản phẩm
 -	Chọn hình thức thanh toán(COD, ví,...)
 -	Rút, nạp tiền vào ví
 -	Xem lịch sử mua hàng
 -	Chỉnh sửa thông tin cá nhân
 -	Xem thông báo về đơn hàng
 -	Tham gia đấu giá tác phẩm nghệ thuật
 -	Xem lịch sử đấu giá
## Role 2: Người bán
 -	Đăng nhập/ Đăng ký
 -	Xem giao diện trang chủ, các danh mục
 -	Tìm kiếm sản phẩm
 -	Thêm, bớt các sản phẩm trên shop
 -	Chấp nhận đơn hàng
 -	Xem trạng thái của sản phẩm
 -	Thông báo khi có người mua hàng
 -	Rút/ Nạp tiền
 -	Xem lịch sử bán hàng
 -	Gửi yêu cầu đấu giá tác phẩm
 -	Theo dõi trạng thái yêu cầu đấu giá
## Role 3: ADMIN
 -	Quản lý các tài khoản hệ thống
 -	Quản lý website(sản phẩm, giao dịch)
 -	Quản lý danh mục sản phẩm
 -	Duyệt yêu cầu đấu giá
 -	Thiết lập giá khởi điểm và thời gian đấu giá
 -	Theo dõi kết quả đấu giá
# Cài đặt hệ thống
## Yêu cầu
 -	Cài đặt XAMPP
 -	PHP
 -  Bootstrap
 -	MySQL
 -	Trình duyệt web
## Hướng dẫn cài đặt
 -	Tải hoặc clone source code của dự án ARTIO về máy
 -	Copy thư mục dự án vào đường dẫn xampp/htdocs/
 -	Đổi tên thư mục dự án thành ARTIO nếu cần
 -	Khởi động XAMPP Control Panel
 -	Bật Apache và MySQL
 -	Truy cập http://localhost/phpmyadmin trên trình duyệt
 -	Tạo cơ sở dữ liệu mới với tên artio
 -	Import file cơ sở dữ liệu .sql của dự án vào database artio
 -	Mở file cấu hình kết nối cơ sở dữ liệu trong dự án
 -	Kiểm tra thông tin kết nối database gồm host, database name, username và password
 -	Truy cập http://localhost/ARTIO để chạy website