# 📁 Project Structure Overview

```
AI/                                 # Root directory
├── README.md                       # Main project documentation
├── SETUP.md                       # Quick setup verification guide
├── package.json                   # Root package.json for convenience scripts
│
├── FE/                            # Next.js Frontend Application
│   ├── src/
│   │   ├── app/                   # App Router (Next.js 13+)
│   │   │   ├── layout.tsx         # Root layout with AuthProvider
│   │   │   ├── page.tsx           # Home dashboard page
│   │   │   ├── login/page.tsx     # Login page
│   │   │   ├── register/page.tsx  # Registration page
│   │   │   ├── ai-features/page.tsx # AI chatbot page
│   │   │   ├── profile/page.tsx   # User profile page
│   │   │   └── globals.css        # Global styles with Ant Design
│   │   │
│   │   ├── components/
│   │   │   └── AppLayout.tsx      # Main layout component (Header, Sidebar, Content)
│   │   │
│   │   ├── contexts/
│   │   │   └── AuthContext.tsx    # Authentication context and state management
│   │   │
│   │   └── lib/
│   │       └── api.ts             # Axios configuration with interceptors
│   │
│   ├── middleware.ts              # Route protection middleware
│   ├── .env.local                 # Frontend environment variables
│   ├── package.json               # Frontend dependencies
│   └── tsconfig.json              # TypeScript configuration
│
└── BE/                            # Laravel Backend API
    ├── app/
    │   ├── Http/Controllers/API/
    │   │   ├── AuthController.php  # Authentication API (login, register, logout)
    │   │   └── AiController.php    # AI processing API (main development area)
    │   │
    │   └── Models/
    │       └── User.php           # User model with HasApiTokens trait
    │
    ├── routes/
    │   ├── api.php                # API routes definition
    │   └── web.php                # Web routes (default Laravel)
    │
    ├── database/
    │   ├── seeders/
    │   │   ├── DatabaseSeeder.php  # Main seeder
    │   │   └── UserSeeder.php     # Sample user data
    │   │
    │   └── migrations/            # Database schema files
    │
    ├── config/
    │   └── sanctum.php           # API authentication configuration
    │
    ├── bootstrap/
    │   └── app.php               # Application bootstrap with API routes
    │
    ├── .env                      # Backend environment variables
    ├── composer.json             # Backend dependencies
    └── database/database.sqlite  # SQLite database file
```

## 🎯 Key Files for Hackathon Teams

### Frontend Development

- **Main Layout**: `FE/src/components/AppLayout.tsx`
- **Home Dashboard**: `FE/src/app/page.tsx`
- **AI Chat Interface**: `FE/src/app/ai-features/page.tsx`
- **API Configuration**: `FE/src/lib/api.ts`
- **Authentication**: `FE/src/contexts/AuthContext.tsx`

### Backend Development

- **🚨 AI Logic**: `BE/app/Http/Controllers/API/AiController.php` ← **Main development area**
- **Authentication**: `BE/app/Http/Controllers/API/AuthController.php`
- **API Routes**: `BE/routes/api.php`
- **Database Models**: `BE/app/Models/`

### Configuration Files

- **Frontend ENV**: `FE/.env.local` (API URL configuration)
- **Backend ENV**: `BE/.env` (Database and app configuration)
- **Route Protection**: `FE/middleware.ts`

## 🔥 Development Focus Areas

1. **AI Implementation** → `BE/app/Http/Controllers/API/AiController.php`
2. **UI/UX Enhancement** → `FE/src/app/` pages and components
3. **New Features** → Add controllers in `BE/app/Http/Controllers/API/`
4. **Database Extensions** → Add migrations and models in `BE/database/`

## 🚀 Quick Commands

```bash
# Start both servers
cd AI && npm run dev

# Setup everything from scratch
cd AI && npm run setup

# Frontend only
cd FE && npm run dev

# Backend only
cd BE && php artisan serve
```

This structure provides a solid foundation for rapid hackathon development while maintaining clean code organization and scalability.
