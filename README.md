# Student Hub AI - Hackathon Boilerplate

## 📋 Mô tả dự án

Student Hub AI là một nền tảng hỗ trợ sinh viên toàn diện với tích hợp AI, được xây dựng để làm boilerplate cho cuộc thi hackathon. Dự án bao gồm:

- **Frontend**: Next.js 15 với TypeScript, Ant Design, và Tailwind CSS
- **Backend**: Laravel 11 với Laravel Sanctum cho API authentication
- **Database**: MySQL
- **Tính năng AI**: Chatbot tư vấn học tập thông minh

## 🚀 Tính năng chính

✅ **Xác thực người dùng**: Đăng ký, đăng nhập, đăng xuất hoàn chỉnh  
✅ **Bảo vệ Routes**: Middleware tự động chuyển hướng người dùng chưa đăng nhập  
✅ **Giao diện sẵn sàng**: Layout với Header, Sidebar, và Content area  
✅ **AI Chatbot**: Trợ lý AI hỗ trợ sinh viên 24/7  
✅ **CORS đã cấu hình**: Frontend và Backend kết nối mượt mà  
✅ **Sample Data**: Tài khoản mẫu để test ngay lập tức

## 🛠 Cài đặt và khởi chạy

### Yêu cầu hệ thống

- Node.js 18+
- PHP 8.1+
- Composer
- MySQL 5.7+

### 1. Clone dự án

```bash
git clone <repository-url>
cd AI
```

### 2. Thiết lập Backend (Laravel)

```bash
cd BE

# Cài đặt dependencies
composer install

# Cấu hình environment
cp .env.example .env

# Tạo application key
php artisan key:generate

# Cấu hình database trong .env:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=student_hub_ai
# DB_USERNAME=root
# DB_PASSWORD=

# Tạo database
mysql -u root -p
CREATE DATABASE student_hub_ai;
exit

# Chạy migrations và seeders
php artisan migrate --seed

# Khởi chạy server
php artisan serve
```

### 3. Thiết lập Frontend (Next.js)

```bash
cd FE

# Cài đặt dependencies
npm install

# Khởi chạy development server
npm run dev
```

### 4. Truy cập ứng dụng

- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8000
- **Tài khoản demo**: user@test.com / password

## 📁 Cấu trúc dự án

```
AI/
├── FE/                          # Next.js Frontend
│   ├── src/
│   │   ├── app/                 # App Router pages
│   │   ├── components/          # React components
│   │   ├── contexts/            # React contexts (Auth)
│   │   └── lib/                 # Utilities (API config)
│   └── middleware.ts            # Route protection
└── BE/                          # Laravel Backend
    ├── app/
    │   └── Http/Controllers/API/
    │       ├── AuthController.php    # Authentication API
    │       └── AiController.php      # AI features API
    ├── routes/api.php           # API routes
    └── database/seeders/        # Sample data
```

## 🎯 Điểm bắt đầu cho teams

### Phát triển tính năng AI

Tất cả logic AI nên được triển khai trong:

```php
// File: BE/app/Http/Controllers/API/AiController.php
public function process(Request $request) {
    // TODO: Implement your AI logic here
    $userMessage = $request->input('message');

    // Gọi API AI của bạn
    // Xử lý dữ liệu
    // Trả về kết quả

    return response()->json(['result' => $aiResponse]);
}
```

### Thêm tính năng mới

1. **Backend**: Tạo controller và routes trong `routes/api.php`
2. **Frontend**: Tạo page trong `src/app/` và component trong `src/components/`
3. **API calls**: Sử dụng configured axios instance từ `src/lib/api.ts`

## 🔧 API Endpoints

### Authentication

- `POST /api/register` - Đăng ký người dùng mới
- `POST /api/login` - Đăng nhập
- `POST /api/logout` - Đăng xuất (cần auth)
- `GET /api/user` - Lấy thông tin user (cần auth)

### AI Features

- `POST /api/ai/process` - Xử lý yêu cầu AI (cần auth)

## 🚨 Lưu ý quan trọng

1. **Database**: Nhớ tạo database `student_hub_ai` trước khi chạy migrations
2. **CORS**: Đã được cấu hình cho localhost:3000
3. **Token Authentication**: Tự động được xử lý bởi axios interceptor
4. **Sample Data**: Chạy `php artisan migrate --seed` để có data mẫu

## 🏆 Tiêu chí đánh giá hackathon

- **Coding (30%)**: Code sạch, cấu trúc rõ ràng, tích hợp đầy đủ
- **AI Integration (30%)**: Tính năng AI hoạt động chính xác và sáng tạo
- **UI/UX (25%)**: Giao diện chuyên nghiệp, responsive, thân thiện
- **Hiệu quả (15%)**: Giá trị thực tiễn, giải quyết vấn đề sinh viên

## 📞 Hỗ trợ

Nếu gặp vấn đề trong quá trình setup:

1. Kiểm tra requirements (PHP, Node.js, MySQL versions)
2. Đảm bảo database đã được tạo
3. Kiểm tra .env configuration
4. Kiểm tra CORS settings nếu có lỗi kết nối API

---

**Chúc teams thi đấu thành công! 🎉**
