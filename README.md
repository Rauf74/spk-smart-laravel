# SPK SMART - Sistem Pendukung Keputusan Pemilihan Program Studi

Aplikasi Sistem Pendukung Keputusan (SPK) menggunakan metode **SMART (Simple Multi-Attribute Rating Technique)** untuk membantu siswa SMA/SMK memilih program studi yang sesuai dengan minat dan kemampuan mereka.

## 📋 Deskripsi

Aplikasi ini merupakan migrasi modern dari project PHP native ke **Laravel Framework 12.x**. Dilengkapi dengan antarmuka yang bersih, responsif, dan interaktif. Digunakan oleh:
- **Guru BK**: Mengelola data kriteria, alternatif (program studi), pertanyaan, memantau aktivitas siswa (Status Online), dan melihat hasil rekomendasi.
- **Siswa**: Mengisi kuesioner penilaian dan melihat rekomendasi program studi terbaik berdasarkan perhitungan sistem.

## ✨ Fitur Utama (New)

- **Modern Dashboard**: Statistik real-time dengan grafik visual (ApexCharts) dan kartu indikator kinerja.
- **Status Online**: Deteksi otomatis status pengguna (Online/Offline) menggunakan Event Listeners.
- **SweetAlert2**: Notifikasi interaktif untuk feedback aksi (Simpan, Update, Hapus, Error) menggantikan alert standar.
- **Enhanced SMART Logic**: Algoritma perhitungan yang lebih akurat, menangani kasus data kosong (default Utility 0).
- **Interactive Tables**: DataTables untuk pencarian, pengurutan, dan paging data yang cepat.

## 🚀 Tech Stack

| Teknologi | Versi | Keterangan |
|-----------|-------|------------|
| **PHP** | 8.2+ | Backend runtime |
| **Laravel** | 12.x | PHP Framework |
| **PostgreSQL** | 15+ | Database (Supabase) |
| **Eloquent ORM** | - | Database abstraction |
| **Blade** | - | Template engine |
| **Bootstrap** | 5.3 | CSS Framework (Template: Modernize) |
| **SweetAlert2** | 11.x | Interactive Popups |
| **ApexCharts** | - | Dashboard charts |
| **jQuery DataTables** | 2.x | Interactive data tables |

## 📁 Struktur Folder

```
spk-smart-laravel/
├── app/
│   ├── Http/
│   │   └── Controllers/          # Logic aplikasi
│   │       ├── Auth/
│   │       │   └── LoginController.php
│   │       ├── AlternatifController.php
│   │       ├── DashboardController.php
│   │       ├── KriteriaController.php
│   │       ├── PenilaianController.php
│   │       ├── PerangkinganController.php
│   │       ├── PerhitunganController.php   # Logic SMART
│   │       ├── PertanyaanController.php
│   │       ├── ProfileController.php       # Profile & Password
│   │       ├── SubkriteriaController.php
│   │       └── UserController.php
│   └── Models/                   # Representasi tabel database
│       ├── Alternatif.php
│       ├── Kriteria.php
│       ├── Penilaian.php
│       ├── Pertanyaan.php
│       ├── Subkriteria.php
│       └── User.php
├── database/
│   ├── migrations/               # Blueprint tabel database
│   └── seeders/                  # Dummy Data
│       ├── AlternatifSeeder.php
│       ├── DatabaseSeeder.php
│       ├── UserSeeder.php
│       └── ...
├── resources/
│   └── views/                    # Template HTML (Blade)
│       ├── auth/                 # Login views
│       ├── layouts/              # Header, Sidebar, Footer
│       ├── penilaian/            # Form & List Penilaian
│       ├── perangkingan/         # Hasil SMART
│       └── ... (Modules)
├── routes/
│   └── web.php                   # Definisi URL routes
└── .env                          # Konfigurasi environment
```

## 🗄️ Database Schema

```
users ──────────────────────┐
  id_user (PK)              │
  nama_user                 │
  username                  │
  password                  │
  role (Guru BK/Siswa)      │
  nis                       │
  is_logged_in (boolean)    │ <─ New: Status Online
                            │
kriteria ───────────────────┼─── subkriteria
  id_kriteria (PK)          │      id_subkriteria (PK)
  kode_kriteria             │      id_kriteria (FK)
  nama_kriteria             │      nama_subkriteria
  jenis (Benefit/Cost)      │      nilai
  bobot                     │
                            │
alternatif ─────────────────┼─── pertanyaan
  id_alternatif (PK)        │      id_pertanyaan (PK)
  kode_alternatif           │      id_kriteria (FK)
  nama_alternatif           │      id_alternatif (FK)
                            │      teks_pertanyaan
                            │
                            └─── penilaian
                                   id_penilaian (PK)
                                   id_user (FK)
                                   id_alternatif (FK)
                                   id_kriteria (FK)
                                   id_pertanyaan (FK)
                                   id_subkriteria (FK)
                                   jawaban
```

## 🧮 Metode SMART

Metode SMART menghitung rekomendasi dengan langkah:

1. **Normalisasi Bobot**: `normalisasi = bobot / total_bobot`
2. **Hitung Utility**:
   - Benefit: `(nilai - min) / (max - min)`
   - Cost: `(max - nilai) / (max - min)`
   - *Logic Fix*: Jika `min == max == 0` (data kosong), Utility = 0.
3. **Nilai Akhir**: `Σ (utility × normalisasi)`
4. **Ranking**: Urutkan dari nilai akhir tertinggi

## ⚙️ Instalasi

### Prasyarat
- PHP 8.2+
- Composer
- PostgreSQL / MySQL

### Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/Rauf74/spk-smart-laravel.git
cd spk-smart-laravel

# 2. Install dependencies
composer install

# 3. Copy dan konfigurasi environment
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi database di .env
# DB_CONNECTION=pgsql
# ...

# 5. Jalankan migrasi
php artisan migrate

# 6. (Opsional) Jalankan seeder untuk data contoh
php artisan db:seed

# 7. Jalankan server
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

## 📊 Status Development

| Modul | Status | Keterangan |
|-------|--------|------------|
| **Core System** | ✅ Selesai | Auth, Database, Routing |
| **Master Data** | ✅ Selesai | CRUD Kriteria, Sub, Alternatif, User |
| **Logic SMART** | ✅ Selesai | Perhitungan akurat + Edge cases handled |
| **UI/UX** | ✅ Polished | Bootstrap 5, SweetAlert2, Responsive |
| **Features** | ✅ Selesai | Dashboard Stats, Online Status, Reporting |

## 🔐 Roles & Permissions

| Fitur | Guru BK | Siswa |
|-------|:-------:|:-----:|
| Dashboard Metrics | ✅ | ✅ |
| Kelola Master Data | ✅ | ❌ |
| Monitoring User | ✅ | ❌ |
| Isi Penilaian | ❌ | ✅ |
| Lihat Detail Hitung | ✅ | ✅ |
| Lihat Ranking | ✅ (All) | ✅ (Personal) |

## 👤 Author

**Abdur Rauf Al Farras**
- GitHub: [@Rauf74](https://github.com/Rauf74)
- LinkedIn: [Abdur Rauf Al Farras](https://www.linkedin.com/in/abdurrauf74)

## 📄 License

MIT License - This project was developed as a real-world Decision Support System for SMK Muhammadiyah 3 Tangerang Selatan.
