# 🚀 Quick Setup Guide

## ✅ Verification Checklist

After following the README.md setup instructions, verify everything works:

### 1. Both servers running:

- ✅ Frontend: http://localhost:3000
- ✅ Backend API: http://localhost:8000

### 2. Test authentication:

- ✅ Go to http://localhost:3000
- ✅ Should redirect to /login (route protection working)
- ✅ Login with: `user@test.com` / `password`
- ✅ Should redirect to dashboard (authentication working)
- ✅ Dashboard displays with layout (UI working)

### 3. Test AI features:

- ✅ Click "Tính năng AI" in sidebar
- ✅ Type a question in Vietnamese like "Quy chế thi cử như thế nào?"
- ✅ Should get AI response (AI integration working)

### 4. Test logout:

- ✅ Click avatar in top right
- ✅ Click "Đăng xuất"
- ✅ Should redirect to login page (logout working)

## 🔧 Common Issues & Solutions

### Frontend won't start:

```bash
cd FE
rm -rf node_modules package-lock.json
npm install
npm run dev
```

### Backend API errors:

```bash
cd BE
composer install
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

### Database issues:

- SQLite is pre-configured and should work out of the box
- For MySQL: Create database `student_hub_ai` first
- Update `.env` with correct database credentials

### CORS errors:

- Check that both servers are running on correct ports
- Frontend: http://localhost:3000
- Backend: http://localhost:8000
- Environment files should match these URLs

## 🎯 Ready for Development!

Once all checkmarks above are ✅, your boilerplate is ready for hackathon development!

Teams can now focus on:

1. Implementing their AI logic in `BE/app/Http/Controllers/API/AiController.php`
2. Adding new features and pages in the frontend
3. Customizing the UI/UX for their specific use case

Good luck! 🏆
