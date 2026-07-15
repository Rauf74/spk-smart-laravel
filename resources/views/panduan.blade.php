@extends('layouts.app')

@section('title', 'Panduan Penggunaan - SPK SMART')

@section('content')
<div class="container-fluid">
    <div class="card bg-light-primary shadow-none position-relative overflow-hidden mb-4">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9">
                    <h4 class="fw-semibold mb-8 text-primary">Panduan Penggunaan Sistem</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a class="text-muted text-decoration-none" href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item text-primary" aria-current="page">Panduan</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-3 text-end">
                    <i class="ti ti-help text-primary" style="font-size: 60px; opacity: 0.2;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 1: Overview -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <span class="bg-primary-subtle text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="ti ti-info-circle fs-6"></i>
                </span>
                <h4 class="mb-0 fw-bold">Tentang SPK SMART</h4>
            </div>
            <p class="fs-4 text-muted mb-0">
                Aplikasi ini adalah <strong>Sistem Pendukung Keputusan (SPK)</strong> berbasis metode ilmiah <strong>SMART (Simple Multi-Attribute Rating Technique)</strong>. 
                Sistem ini dirancang khusus untuk memandu siswa SMK dalam merekomendasikan Program Studi (Prodi) lanjutan yang paling sesuai berdasarkan faktor minat, bakat, nilai akademik, dan kemampuan ekonomi.
            </p>
        </div>
    </div>

    <!-- Section 2: Terminologi -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="bg-warning-subtle text-warning rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="ti ti-vocabulary fs-6"></i>
                </span>
                <h4 class="mb-0 fw-bold">Istilah & Komponen Sistem</h4>
            </div>
            <p class="text-muted fs-4 mb-4">
                Untuk memudahkan Guru BK dan Siswa non-teknis, kami telah menyederhanakan istilah-istilah teknis pemrograman/database (legacy dari PHP native) menjadi bahasa yang awam:
            </p>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-primary text-white">
                        <tr>
                            <th style="width: 25%;">Nama Teknis (Database)</th>
                            <th style="width: 25%;">Nama Awam (Sistem Saat Ini)</th>
                            <th>Deskripsi Penggunaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong class="text-primary">Alternatif</strong></td>
                            <td><span class="badge bg-primary fs-3">Program Studi</span></td>
                            <td>Pilihan jurusan/program studi yang akan direkomendasikan sistem kepada siswa (misal: Teknik Informatika, Sistem Informasi, dsb).</td>
                        </tr>
                        <tr>
                            <td><strong class="text-success">Kriteria</strong></td>
                            <td><span class="badge bg-success fs-3">Faktor Penilaian</span></td>
                            <td>Dimensi atau parameter yang digunakan sebagai dasar penilaian (misal: Minat, Kesiapan Finansial, dsb).</td>
                        </tr>
                        <tr>
                            <td><strong class="text-warning">Subkriteria</strong></td>
                            <td><span class="badge bg-warning text-dark fs-3">Pilihan Jawaban</span></td>
                            <td>Daftar pilihan tanggapan kuesioner yang memiliki bobot nilai numerik tertentu (misal: Sangat Minat = 5, Cukup = 3, Kurang = 1).</td>
                        </tr>
                        <tr>
                            <td><strong class="text-info">Penilaian</strong></td>
                            <td><span class="badge bg-info text-dark fs-3">Kuesioner Penilaian</span></td>
                            <td>Aktivitas di mana siswa mengisi tanggapan/jawaban terhadap pertanyaan yang disajikan sistem.</td>
                        </tr>
                        <tr>
                            <td><strong class="text-danger">Perangkingan</strong></td>
                            <td><span class="badge bg-danger fs-3">Hasil Rekomendasi</span></td>
                            <td>Urutan program studi terbaik hasil kalkulasi metode SMART yang diurutkan dari persentase kecocokan tertinggi ke terendah.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Section 3: Cara Kerja (Workflow) -->
    <div class="row mb-4">
        <!-- Workflow Siswa -->
        <div class="col-md-6 mb-4 mb-md-0">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <span class="bg-success-subtle text-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="ti ti-user-check fs-6"></i>
                        </span>
                        <h4 class="mb-0 fw-bold">Alur Kerja Siswa</h4>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex align-items-start gap-3 mb-4">
                            <span class="badge bg-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px;">1</span>
                            <div>
                                <h6 class="fw-bold mb-1">Mengisi Kuesioner Penilaian</h6>
                                <p class="text-muted small mb-0">Buka menu <strong>Kuesioner Penilaian</strong>. Jawab pertanyaan-pertanyaan yang diajukan per kriteria dengan jujur sesuai kondisi Anda.</p>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3 mb-4">
                            <span class="badge bg-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px;">2</span>
                            <div>
                                <h6 class="fw-bold mb-1">Simpan Progress Pengisian</h6>
                                <p class="text-muted small mb-0">Anda dapat menyimpan jawaban secara parsial jika ingin beristirahat. Klik <strong>Simpan & Lanjutkan</strong> untuk menyimpan progress.</p>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <span class="badge bg-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px;">3</span>
                            <div>
                                <h6 class="fw-bold mb-1">Melihat Hasil Rekomendasi</h6>
                                <p class="text-muted small mb-0">Setelah semua terisi, buka **Hasil Rekomendasi Saya** untuk mengunduh laporan PDF serta melihat arahan/catatan dari Guru BK Anda.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Workflow Guru BK -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <span class="bg-primary-subtle text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="ti ti-id fs-6"></i>
                        </span>
                        <h4 class="mb-0 fw-bold">Alur Kerja Guru BK</h4>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex align-items-start gap-3 mb-4">
                            <span class="badge bg-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px;">1</span>
                            <div>
                                <h6 class="fw-bold mb-1">Konfigurasi Master Data</h6>
                                <p class="text-muted small mb-0">Kelola Faktor Penilaian, Pilihan Jawaban, Program Studi, dan Pertanyaan. Pastikan total bobot seluruh faktor tepat **100%**.</p>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3 mb-4">
                            <span class="badge bg-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px;">2</span>
                            <div>
                                <h6 class="fw-bold mb-1">Pantau Pengisian Siswa</h6>
                                <p class="text-muted small mb-0">Pantau status siswa di Dashboard (Belum Mengisi, Sedang Mengisi, Selesai) untuk mengingatkan siswa yang belum tuntas mengisi.</p>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <span class="badge bg-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px;">3</span>
                            <div>
                                <h6 class="fw-bold mb-1">Tulis Catatan Konseling & Export</h6>
                                <p class="text-muted small mb-0">Tinjau hasil rekomendasi, berikan Catatan Konseling resmi sebagai panduan, lalu cetak laporan PDF atau ekspor Rekap Excel.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 4: Penjelasan Teori Matematika SMART -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="bg-danger-subtle text-danger rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="ti ti-math-symbols fs-6"></i>
                </span>
                <h4 class="mb-0 fw-bold">Bagaimana Metode SMART Menghitung Hasil?</h4>
            </div>
            <p class="text-muted fs-4 mb-4">
                Metode SMART mengambil keputusan dengan mengonversi nilai kualitatif siswa menjadi nilai kuantitatif terstandarisasi melalui empat tahapan matematika:
            </p>

            <div class="accordion" id="accordionSMART">
                <!-- Tahap 1 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Tahap 1: Normalisasi Bobot Kriteria
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionSMART">
                        <div class="accordion-body">
                            <p class="mb-2">Membagi bobot kriteria masing-masing dengan total seluruh bobot kriteria dalam sistem.</p>
                            <div class="bg-light p-3 rounded text-center mb-3">
                                <span class="fs-5 fw-semibold text-primary">Normalisasi Kriteria (wⱼ) = Bobot Kriteria / Total Bobot Semua Kriteria</span>
                            </div>
                            <p class="small text-muted mb-0"><em>Contoh: Jika bobot kriteria Minat = 40% dan total bobot kriteria lainnya = 100%, maka Normalisasi Minat = 0,4.</em></p>
                        </div>
                    </div>
                </div>

                <!-- Tahap 2 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Tahap 2: Menghitung Nilai Utility (Nilai Konversi)
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionSMART">
                        <div class="accordion-body">
                            <p class="mb-3">Mengonversi nilai mentah pilihan jawaban siswa menjadi skala 0 s.d 1. Konversi ini dipengaruhi oleh <strong>Jenis Kriteria</strong>:</p>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <div class="p-3 border rounded bg-light-primary">
                                        <h6 class="fw-bold text-primary mb-2">Kriteria Benefit (Keuntungan)</h6>
                                        <p class="small text-muted mb-2">Makin besar nilai jawaban makin bagus (misal: Minat, Bakat, Nilai Akademik).</p>
                                        <div class="bg-white p-2 rounded text-center fw-bold small">
                                            Utility = (Nilai - Nilai Min) / (Nilai Max - Nilai Min)
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded bg-light-warning">
                                        <h6 class="fw-bold text-warning-emphasis mb-2">Kriteria Cost (Biaya/Beban)</h6>
                                        <p class="small text-muted mb-2">Makin kecil nilai jawaban makin bagus (misal: Biaya Kuliah, Jarak Kampus).</p>
                                        <div class="bg-white p-2 rounded text-center fw-bold small">
                                            Utility = (Nilai Max - Nilai) / (Nilai Max - Nilai Min)
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tahap 3 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Tahap 3: Menghitung Nilai Akhir & Perangkingan
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionSMART">
                        <div class="accordion-body">
                            <p class="mb-2">Menjumlahkan hasil perkalian **Nilai Utility** dengan **Normalisasi Bobot** untuk setiap kriteria pada alternatif terkait.</p>
                            <div class="bg-light p-3 rounded text-center mb-3">
                                <span class="fs-5 fw-semibold text-primary">Nilai Akhir = Σ (Utility Kriteria × Normalisasi Bobot Kriteria)</span>
                            </div>
                            <p class="small text-muted mb-0">Alternatif (Program Studi) dengan Nilai Akhir tertinggi akan menempati peringkat pertama sebagai rekomendasi utama sistem untuk siswa tersebut.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
