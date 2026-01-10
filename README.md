# SPK SMART - Sistem Pendukung Keputusan Pemilihan Program Studi

Aplikasi Sistem Pendukung Keputusan (SPK) menggunakan metode **SMART (Simple Multi-Attribute Rating Technique)** untuk membantu siswa SMA/SMK memilih program studi yang sesuai dengan minat dan kemampuan mereka.

## 📋 Deskripsi

Aplikasi ini merupakan migrasi dari project PHP native ke **Laravel Framework**. Digunakan oleh:
- **Guru BK**: Mengelola data kriteria, alternatif (program studi), pertanyaan, dan melihat hasil penilaian siswa
- **Siswa**: Mengisi penilaian dan melihat rekomendasi program studi

## 🚀 Tech Stack

| Teknologi | Versi | Keterangan |
|-----------|-------|------------|
| **PHP** | 8.2+ | Backend runtime |
| **Laravel** | 12.x | PHP Framework |
| **PostgreSQL** | 15+ | Database (Supabase) |
| **Eloquent ORM** | - | Database abstraction |
| **Blade** | - | Template engine |
| **Bootstrap** | 5.x | CSS Framework (planned) |
| **ApexCharts** | - | Dashboard charts (planned) |

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
│   └── migrations/               # Blueprint tabel database
├── resources/
│   └── views/                    # Template HTML (Blade)
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
# DB_HOST=your-supabase-host
# DB_PORT=5432
# DB_DATABASE=postgres
# DB_USERNAME=postgres
# DB_PASSWORD=your-password

# 5. Jalankan migrasi
php artisan migrate

# 6. (Opsional) Jalankan seeder untuk data contoh
php artisan db:seed

# 7. Jalankan server
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

## 📊 Progress Development

| Fase | Status | Catatan |
|------|--------|---------|
| Database Schema | ✅ Selesai | 6 tabel dengan relasi |
| Eloquent Models | ✅ Selesai | 6 model dengan relationships |
| Controllers | ✅ Selesai | 10 controller (CRUD + SMART logic) |
| Routes | ✅ Selesai | RESTful routes |
| Seeder | ✅ Selesai | Data contoh dari SQL lama |
| Views (Blade) | 🔄 In Progress | Belum dimulai |
| Assets (CSS/JS) | ⏳ Pending | Bootstrap + ApexCharts |
| Deployment | ⏳ Pending | Render + Supabase |

## 🔐 Roles & Permissions

| Fitur | Guru BK | Siswa |
|-------|:-------:|:-----:|
| Dashboard | ✅ | ✅ |
| Kelola Kriteria | ✅ | ❌ |
| Kelola Subkriteria | ✅ | ❌ |
| Kelola Alternatif | ✅ | ❌ |
| Kelola Pertanyaan | ✅ | ❌ |
| Kelola User | ✅ | ❌ |
| Isi Penilaian | ❌ | ✅ |
| Lihat Perhitungan | ✅ | ✅ |
| Lihat Perangkingan | ✅ | ✅ |

## 📝 API Routes

```
GET    /login              → LoginController@showLoginForm
POST   /login              → LoginController@login
POST   /logout             → LoginController@logout

GET    /                   → DashboardController@index

# Master Data (CRUD)
GET    /kriteria           → KriteriaController@index
POST   /kriteria           → KriteriaController@store
GET    /kriteria/{id}/edit → KriteriaController@edit
PUT    /kriteria/{id}      → KriteriaController@update
DELETE /kriteria/{id}      → KriteriaController@destroy

# (sama untuk subkriteria, alternatif, pertanyaan, user)

# SPK
GET    /penilaian          → PenilaianController@index
GET    /penilaian/create/{id} → PenilaianController@create
POST   /penilaian          → PenilaianController@store
GET    /perhitungan        → PerhitunganController@index
GET    /perangkingan       → PerangkinganController@index
```

## 👤 Author

**Abdur Rauf Al Farras**
- GitHub: [@Rauf74](https://github.com/Rauf74)

## 📄 License

Project ini dibuat untuk keperluan akademik (Skripsi).
