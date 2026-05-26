# ShakarqamishMFY.uz - Mahalla Portali

## Loyiha haqida
O'zbekiston hududidagi mahalla fuqarolar yig'inlari uchun zamonaviy portal.

## Texnologiyalar
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Backend**: PHP 8+
- **Database**: MySQL/MariaDB
- **Admin Panel**: Custom PHP Dashboard

## O'rnatish

### 1. Talablar
- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.3+
- Apache/Nginx web server
- mod_rewrite yoqilgan bo'lishi kerak

### 2. Fayllarni serverga yuklash
```bash
# Barcha fayllarni hosting root papkasiga yuklang
```

### 3. Database sozlash
1. phpMyAdmin orqali yangi database yarating
2. `database.sql` faylini import qiling
3. `.env` faylini yarating va ma'lumotlarni kiriting

### 4. .env fayli
```env
DB_HOST=localhost
DB_NAME=shakarqamish_mfy
DB_USER=root
DB_PASS=your_password
SITE_URL=http://localhost
ADMIN_EMAIL=admin@shakarqamish.uz
ADMIN_PASSWORD=admin123
```

### 5. Ruxsatnomalar
```bash
chmod 755 public/uploads
chmod 644 config/.env
```

## Admin Panel
URL: `/admin`
Default login: admin / admin123

## Xavfsizlik
- CSRF Protection
- XSS Prevention
- SQL Injection Protection
- Password Hashing (bcrypt)
- Session Security

## Strukturasi
```
/app
├── config/          # Konfiguratsiya fayllari
├── core/            # Asosiy funksiyalar
├── public/          # Frontend fayllar
│   ├── assets/      # CSS, JS, Images
│   └── uploads/     # Yuklangan fayllar
├── admin/           # Admin panel
└── includes/        # Qo'shimcha fayllar
```

## Funksiyalar
- ✅ Yangiliklar tizimi
- ✅ E'lonlar
- ✅ Mahalla kengashi a'zolari
- ✅ Online murojaatlar
- ✅ Statistika
- ✅ Galereya
- ✅ Hujjatlar
- ✅ Admin boshqaruvi
- ✅ Responsive dizayn
- ✅ SEO optimizatsiya

## Versiya
1.0.0 - Production Ready

## Muallif
Shakarqamish MFY Development Team
