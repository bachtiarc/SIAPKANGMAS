# SIAPKANGMAS

**Dinas Perindustrian dan Perdagangan Provinsi Jawa Tengah**

## Deskripsi Proyek

SIAPKANGMAS adalah aplikasi web komprehensif yang dirancang untuk mendukung pengelolaan kegiatan di Dinas Perindustrian dan Perdagangan Provinsi Jawa Tengah. Sistem ini memfasilitasi pengelolaan submission, consultation, complaint, dan ticketing untuk meningkatkan efisiensi administrasi.

---

## Stack Teknologi

### Backend
- **Framework**: Laravel 12.0
- **Runtime**: PHP 8.2+
- **Database**: Supabase
- **ORM**: Eloquent (Laravel ORM)

### Frontend
- **Build Tool**: Vite 7.0
- **Styling**: Tailwind CSS 4.0
- **Templating**: Blade Template Engine
- **HTTP Client**: Axios
- **Task Runner**: Concurrently (untuk development)

### Additional Packages
- **PDF Generation**: Laravel DomPDF
- **Excel Export/Import**: Maatwebsite/Excel
- **CAPTCHA**: Google reCAPTCHA (anhskohbo/no-captcha)
- **Cloud Storage**: AWS S3 (League Flysystem)
- **API Rate Limiting**: Built-in Laravel

### Development Tools
- **Testing**: PHPUnit 11.5+
- **Code Quality**: Laravel Pint
- **Containerization**: Docker & Docker Compose
- **Package Manager**: Composer (PHP), npm (Node.js)
- **Version Control**: Git & GitHub

---

## Struktur Proyek

```
.
├── app/
│   ├── Exports/              # Excel exports
│   ├── Http/
│   │   ├── Controllers/      # Application controllers
│   │   ├── Middleware/       # HTTP middleware
│   │   └── Requests/         # Form requests & validation
│   ├── Mail/                 # Mailable classes
│   ├── Models/               # Eloquent models
│   ├── Notifications/        # Notification classes
│   ├── Providers/            # Service providers
│   └── Services/             # Business logic services
├── bootstrap/                # Framework bootstrap files
├── config/                   # Configuration files
├── database/
│   ├── factories/            # Model factories (testing)
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
├── public/                   # Public assets
├── resources/
│   ├── css/                  # CSS files
│   ├── js/                   # JavaScript files
│   └── views/                # Blade templates
├── routes/
│   ├── api.php              # API routes
│   ├── web.php              # Web routes
│   └── console.php          # Console routes
├── storage/                  # Application storage
├── tests/                    # Test files
├── vendor/                   # Composer dependencies
├── Dockerfile               # Docker configuration
├── docker-compose.yml       # Docker Compose setup
├── vite.config.js          # Vite configuration
├── tailwind.config.js      # Tailwind CSS configuration
├── composer.json           # PHP dependencies
├── package.json            # Node.js dependencies
└── README.md              # This file
```

---

## API Endpoints

Sistem ini menyediakan API RESTful yang dapat diakses di `/api/`:

### Main Endpoints
- `POST /api/submissions` - Buat submission baru
- `GET /api/submissions/{id}` - Dapatkan detail submission
- `POST /api/consultations` - Buat consultation baru
- `POST /api/complaints` - Buat complaint baru
- `GET /api/tickets` - Daftar tickets

Dokumentasi API lengkap tersedia di `routes/api.php`

---

## Email & Notifications

Sistem menggunakan **Brevo** (Sendinblue) untuk pengiriman email:

- **Complaint Created**: Notifikasi saat complaint dibuat
- **Complaint Status Updated**: Update status complaint
- **Consultation Created**: Notifikasi consultation baru
- **Submission Created**: Notifikasi submission baru
- **Email Verification**: Custom email verification

---

## Export Features

### Excel Export
- Tickets Export
- Complaints Export
- Consultations Export
- Submissions Export

### PDF Generation
Powered by Laravel DomPDF untuk generate PDF documents

---

## Security

- CSRF Protection enabled
- SQL Injection Prevention dengan Eloquent ORM
- XSS Protection dengan Blade escaping
- Password hashing dengan bcrypt
- Rate limiting pada API endpoints
- Google reCAPTCHA untuk form protection

---

## Fitur Utama

✅ Manajemen Submission PKL  
✅ Sistem Consultation  
✅ Complaint Management  
✅ Ticketing System  
✅ Export ke Excel & PDF  
✅ Email Notifications  
✅ User Authentication  
✅ Role-based Authorization  
✅ Responsive Design (Tailwind CSS)  
✅ RESTful API  

---

## Kontribusi

Kami menerima kontribusi dari komunitas. Untuk berkontribusi:

1. Fork repository ini
2. Buat branch fitur (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan Anda (`git commit -m 'Add AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

---

## Lisensi

Proyek ini dilisensikan di bawah MIT License. Lihat file `LICENSE` untuk detail lebih lanjut.

---

## Tim Pengembang

- **Cikal Wahyuning Bachtuar**
- **Evia Auamara Unsa Nasyta**

---

## Support & Contact

Untuk pertanyaan atau dukungan teknis, silakan buka issue di repository ini atau hubungi tim pengembang.

---

**Terakhir diupdate**: 2026  
**Status**: Active Development