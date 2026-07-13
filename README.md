# SPK SMART - Rekomendasi Program Studi

Sistem Pendukung Keputusan untuk merekomendasikan program studi kepada siswa SMK berdasarkan metode **SMART (Simple Multi-Attribute Rating Technique)**.

Dibangun dengan **Laravel 13** + **Bootstrap 5** (Modernize Template).

## Fitur Utama

- **Kuesioner Wizard** — Step-by-step penilaian per kriteria dengan partial save
- **Hasil Rekomendasi Naratif** — Dual-layer: hero card persen (awam) + detail SMART (teknis)
- **Dashboard Guru BK** — Status siswa real-time: belum/sedang/selesai
- **Catatan Konseling** — Guru BK bisa tulis & simpan catatan per siswa
- **Export PDF** — Hasil rekomendasi dalam format PDF resmi
- **Print-Friendly** — CSS `@media print` untuk cetak langsung dari browser
- **Mobile-First** — Responsive di HP, tablet, dan desktop

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | Laravel 13, PHP 8.3+ |
| Frontend | Bootstrap 5, Tabler Icons, ApexCharts, SweetAlert2 |
| Database | MariaDB (local) / PostgreSQL (production) |
| PDF Export | dompdf |
| Testing | PHPUnit |

## Cara Install (Local)

### 1. Clone & Install Dependency

```bash
git clone <repo-url>
cd spk-smart-laravel
composer install
npm install
npm run build
```

### 2. Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` — sesuaikan database:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=spk_smart_laravel
DB_USERNAME=dev
DB_PASSWORD=dev123
```

### 3. Database & Seeder

```bash
php artisan migrate
php artisan db:seed
```

### 4. Jalankan

```bash
php artisan serve
```

Buka http://localhost:8000

### 5. Login Default

| Role | Username | Password |
|------|----------|----------|
| Guru BK | gurubk | password |
| Siswa | daftarkan via register |

## Struktur Folder Penting

```
app/
  Http/Controllers/    — Controller utama
  Models/              — Eloquent Model
  Services/
    SmartCalculationService.php  — Logic perhitungan SMART

database/migrations/   — Semua migration

resources/views/       — Blade templates
  layouts/             — App layout, sidebar, header
  auth/                — Login & register
  perangkingan/        — Hasil rekomendasi + PDF template

tests/Unit/Services/   — Unit test perhitungan SMART
```

## Commit Format

Format yang digunakan di repo ini:

```
[Fitur]  Deskripsi fitur baru
[Fix]    Deskripsi bug fix
[UI]     Perubahan tampilan/UX
[Chore]  Maintenance, config, cleanup
```

1 fitur = 1 commit. Pisah backend, frontend, dan style.

## Deployment (Production)

Konfigurasi `Dockerfile` sudah membangun asset Vite dan menjalankan migration saat container dimulai, sehingga deploy dari clone baru tidak bergantung pada folder `public/build` lokal.

1. Gunakan `render.yaml` sebagai Web Service Docker dan set `APP_ENV=production`, `APP_DEBUG=false`, serta `APP_URL`.
2. Buat PostgreSQL managed database, lalu isi `DATABASE_URL` dan biarkan `DB_CONNECTION=pgsql`.
3. Render akan membuat `APP_KEY`; jangan masukkan `.env` production ke repository.
4. Setelah deploy, verifikasi login Guru BK, wizard siswa, hasil SMART, dan export PDF.

---

Dibuat untuk **SMK Muhammadiyah 3 Tangerang Selatan**.
