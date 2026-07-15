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
            <div class="table-responsive shadow-sm rounded-3 overflow-hidden">
                <table class="table table-bordered table-striped align-middle mb-0">
                    <thead style="background: linear-gradient(135deg, #5D87FF 0%, #764ba2 100%); color: white;">
                        <tr>
                            <th class="text-white border-0 py-3" style="width: 25%;">Nama Teknis (Database)</th>
                            <th class="text-white border-0 py-3" style="width: 25%;">Nama Awam (Sistem Saat Ini)</th>
                            <th class="text-white border-0 py-3">Deskripsi Penggunaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-3"><strong class="text-primary">Alternatif</strong></td>
                            <td><span class="badge bg-primary fs-3">Program Studi</span></td>
                            <td>Pilihan jurusan/program studi yang akan direkomendasikan sistem kepada siswa (misal: Teknik Informatika, Sistem Informasi, dsb).</td>
                        </tr>
                        <tr>
                            <td class="ps-3"><strong class="text-success">Kriteria</strong></td>
                            <td><span class="badge bg-success fs-3">Faktor Penilaian</span></td>
                            <td>Dimensi atau parameter yang digunakan sebagai dasar penilaian (misal: Minat, Kesiapan Finansial, dsb).</td>
                        </tr>
                        <tr>
                            <td class="ps-3"><strong class="text-warning">Subkriteria</strong></td>
                            <td><span class="badge bg-warning text-dark fs-3">Pilihan Jawaban</span></td>
                            <td>Daftar pilihan tanggapan kuesioner yang memiliki bobot nilai numerik tertentu (misal: Sangat Minat = 5, Cukup = 3, Kurang = 1).</td>
                        </tr>
                        <tr>
                            <td class="ps-3"><strong class="text-info">Penilaian</strong></td>
                            <td><span class="badge bg-info text-dark fs-3">Kuesioner Penilaian</span></td>
                            <td>Aktivitas di mana siswa mengisi tanggapan/jawaban terhadap pertanyaan yang disajikan sistem.</td>
                        </tr>
                        <tr>
                            <td class="ps-3"><strong class="text-danger">Perangkingan</strong></td>
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
                                <p class="text-muted small mb-0">Setelah semua terisi, buka <strong>Hasil Rekomendasi Saya</strong> untuk mengunduh laporan PDF serta melihat arahan/catatan dari Guru BK Anda.</p>
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
                                <p class="text-muted small mb-0">Kelola Faktor Penilaian, Pilihan Jawaban, Program Studi, dan Pertanyaan. Pastikan total bobot seluruh faktor tepat <strong>100%</strong>.</p>
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

    <!-- Section 4: Penjelasan Teori Matematika SMART (7 TAHAPAN SKRIPSI) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="bg-danger-subtle text-danger rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="ti ti-math-symbols fs-6"></i>
                </span>
                <h4 class="mb-0 fw-bold">Bagaimana Metode SMART Menghitung Hasil? (7 Tahap Algoritma)</h4>
            </div>
            <p class="text-muted fs-4 mb-4">
                Berdasarkan struktur pemrosesan data ilmiah metode SMART, berikut adalah **7 Tahap Perhitungan** yang dirujuk dari rancangan skripsi dan diimplementasikan secara terstruktur pada menu **Detail Perhitungan**:
            </p>

            <div class="accordion" id="accordionSMART">
                <!-- Tahap 1 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Tahap 1: Menentukan Alternatif (Program Studi)
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionSMART">
                        <div class="accordion-body">
                            <p class="mb-0 text-muted">Menetapkan himpunan pilihan program studi ($A_i$) yang akan dievaluasi kelayakan kecocokannya oleh sistem (misalnya: *Teknik Informatika*, *Sistem Informasi*, dsb) sesuai data tabel <code>alternatif</code>.</p>
                        </div>
                    </div>
                </div>

                <!-- Tahap 2 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Tahap 2: Menentukan Kriteria (Faktor Penilaian)
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionSMART">
                        <div class="accordion-body">
                            <p class="mb-0 text-muted">Mengidentifikasi dimensi atau parameter penentu ($C_j$) yang digunakan sebagai acuan penarikan keputusan (seperti: *Minat*, *Prestasi*, *Kesiapan Mental*, dsb) beserta jenis kriteria (*Benefit* atau *Cost*).</p>
                        </div>
                    </div>
                </div>

                <!-- Tahap 3 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Tahap 3: Menentukan Bobot Kriteria (Wⱼ)
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionSMART">
                        <div class="accordion-body">
                            <p class="mb-0 text-muted">Memberikan nilai bobot kepentingan awal ($W_j$) pada tiap faktor penilaian berdasarkan prioritas tingkat kepentingannya oleh Guru BK (misal: Minat = 30%, Prestasi = 25%, dsb) dengan batas total keseluruhan $100\%$.</p>
                        </div>
                    </div>
                </div>

                <!-- Tahap 4 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            Tahap 4: Normalisasi Bobot Kriteria (wⱼ)
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionSMART">
                        <div class="accordion-body">
                            <p class="mb-2">Membagi nilai bobot masing-masing kriteria dengan total seluruh bobot kriteria untuk mendapatkan nilai bobot relatif ($w_j$).</p>
                            <div class="bg-light p-3 rounded text-center mb-3">
                                <span class="fs-5 fw-semibold text-primary">Normalisasi Kriteria (wⱼ) = Wⱼ / ΣWⱼ</span>
                            </div>
                            <p class="small text-muted mb-0"><em>Di sistem ini, normalisasi dihitung otomatis menggunakan formula <code>round($kriteria->bobot / $totalBobot, 4)</code>.</em></p>
                        </div>
                    </div>
                </div>

                <!-- Tahap 5 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFive">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                            Tahap 5: Menilai Alternatif per Kriteria (Nilai Mentah)
                        </button>
                    </h2>
                    <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#accordionSMART">
                        <div class="accordion-body">
                            <p class="mb-0 text-muted">Mengambil data nilai mentah ($x$) dari jawaban siswa terhadap pertanyaan kuesioner prodi. Nilai ini didasarkan pada pilihan jawaban subkriteria yang memiliki bobot terdefinisi (skala 1 s.d 5).</p>
                        </div>
                    </div>
                </div>

                <!-- Tahap 6 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSix">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                            Tahap 6: Menghitung Nilai Utility (uᵢ(a))
                        </button>
                    </h2>
                    <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#accordionSMART">
                        <div class="accordion-body">
                            <p class="mb-3">Mengonversi nilai mentah siswa ($x$) menjadi skala utility terstandarisasi antara 0 s.d 1. Cara konversi dipisahkan berdasarkan jenis kriteria:</p>
                            <div class="row">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <div class="p-3 border rounded bg-light-primary">
                                        <h6 class="fw-bold text-primary mb-2">Kriteria Benefit (Keuntungan)</h6>
                                        <p class="small text-muted mb-2">Makin besar nilai jawaban makin bagus (misal: Minat, Bakat).</p>
                                        <div class="bg-white p-2 rounded text-center fw-bold small">
                                            Utility u(x) = (x - x_min) / (x_max - x_min)
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded bg-light-warning">
                                        <h6 class="fw-bold text-warning-emphasis mb-2">Kriteria Cost (Beban/Biaya)</h6>
                                        <p class="small text-muted mb-2">Makin kecil nilai jawaban makin bagus (misal: Biaya Kuliah).</p>
                                        <div class="bg-white p-2 rounded text-center fw-bold small">
                                            Utility u(x) = (x_max - x) / (x_max - x_min)
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tahap 7 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSeven">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                            Tahap 7: Menghitung Nilai Akhir (V(a)) & Perangkingan
                        </button>
                    </h2>
                    <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven" data-bs-parent="#accordionSMART">
                        <div class="accordion-body">
                            <p class="mb-2">Menghitung nilai kelayakan akhir alternatif ($V(a)$) dengan menjumlahkan hasil perkalian **Nilai Utility** dengan **Normalisasi Bobot** untuk setiap kriteria pada alternatif tersebut.</p>
                            <div class="bg-light p-3 rounded text-center mb-3">
                                <span class="fs-5 fw-semibold text-primary">Nilai Akhir V(a) = Σ (uᵢ(a) × wⱼ)</span>
                            </div>
                            <p class="small text-muted mb-0">Alternatif (Program Studi) dengan Nilai Akhir tertinggi diurutkan ke peringkat pertama sebagai rekomendasi prodi terbaik untuk siswa.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
