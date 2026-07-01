<?php

namespace Tests\Feature\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\Pertanyaan;
use App\Models\Subkriteria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test untuk PerangkinganController::exportPDF.
 *
 * Memastikan:
 * - Endpoint bisa diakses oleh siswa (data sendiri) & guru (dengan ?id_user)
 * - PDF response valid (Content-Type, content disposition, ada %PDF marker)
 * - File name mengandung nama siswa
 * - Return 404 untuk user_id yang tidak ada
 * - Siswa tidak bisa akses data siswa lain
 */
class PerangkinganControllerExportPdfTest extends TestCase
{
    use RefreshDatabase;

    private User $guru;
    private User $siswa;
    private array $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guru = User::create([
            'nama_user' => 'Pak Guru',
            'username'  => 'guru_test',
            'password'  => bcrypt('123456'),
            'role'      => 'Guru BK',
        ]);

        $this->siswa = User::create([
            'nama_user' => 'Andi Wijaya',
            'username'  => 'siswa_test',
            'password'  => bcrypt('123456'),
            'role'      => 'Siswa',
            'nis'       => '12345',
        ]);

        // Setup data minimum
        $kriteria = Kriteria::create([
            'kode_kriteria' => 'C1',
            'nama_kriteria' => 'Minat',
            'jenis'         => 'Benefit',
            'bobot'         => 100,
        ]);

        $subkriteria = Subkriteria::create([
            'id_kriteria'     => $kriteria->id_kriteria,
            'nama_subkriteria' => 'Tinggi',
            'nilai'           => 5,
        ]);

        $alternatif = Alternatif::create([
            'kode_alternatif'  => 'A1',
            'nama_alternatif'  => 'Teknik Informatika',
        ]);

        $pertanyaan = Pertanyaan::create([
            'id_kriteria'      => $kriteria->id_kriteria,
            'id_alternatif'    => $alternatif->id_alternatif,
            'teks_pertanyaan'  => 'Test',
        ]);

        // Siswa isi penilaian lengkap
        Penilaian::create([
            'id_user'        => $this->siswa->id_user,
            'id_alternatif'  => $alternatif->id_alternatif,
            'id_kriteria'    => $kriteria->id_kriteria,
            'id_pertanyaan'  => $pertanyaan->id_pertanyaan,
            'id_subkriteria' => $subkriteria->id_subkriteria,
            'jawaban'        => 5,
        ]);

        $this->data = compact('kriteria', 'subkriteria', 'alternatif', 'pertanyaan');
    }

    // ============================================================
    // 1. Auth required
    // ============================================================

    #[Test]
    public function export_pdf_memerlukan_authentication(): void
    {
        $response = $this->get('/perangkingan/pdf');
        $response->assertRedirect('/login');
    }

    // ============================================================
    // 2. Siswa akses data sendiri
    // ============================================================

    #[Test]
    public function siswa_bisa_export_pdf_data_sendiri(): void
    {
        $response = $this->actingAs($this->siswa)->get('/perangkingan/pdf');

        $response->assertStatus(200);
        // PDF Content-Type (dompdf: application/pdf atau text/html jika stream)
        $this->assertStringContainsString('pdf', strtolower($response->headers->get('Content-Type') ?? ''));
    }

    #[Test]
    public function pdf_response_mengandung_pdf_magic_bytes(): void
    {
        $response = $this->actingAs($this->siswa)->get('/perangkingan/pdf');

        // PDF file signature: %PDF-
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    #[Test]
    public function pdf_filename_mengandung_nama_siswa(): void
    {
        $response = $this->actingAs($this->siswa)->get('/perangkingan/pdf');

        $contentDisposition = $response->headers->get('Content-Disposition');
        $this->assertNotNull($contentDisposition, 'Content-Disposition harus ada untuk download');
        $this->assertStringContainsString('Hasil-Rekomendasi', $contentDisposition);
        $this->assertStringContainsString('Andi', $contentDisposition, 'Filename harus ada nama siswa');
        $this->assertStringContainsString('.pdf', $contentDisposition);
    }

    // ============================================================
    // 3. Guru bisa akses data siswa
    // ============================================================

    #[Test]
    public function guru_bisa_export_pdf_siswa_tertentu_dengan_id_user(): void
    {
        $response = $this->actingAs($this->guru)
            ->get('/perangkingan/pdf?id_user=' . $this->siswa->id_user);

        $response->assertStatus(200);
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    #[Test]
    public function guru_tanpa_id_user_mengakses_data_sendiri(): void
    {
        // Guru tanpa ?id_user= → PDF data guru (mungkin kosong karena bukan siswa)
        $response = $this->actingAs($this->guru)->get('/perangkingan/pdf');

        $response->assertStatus(200);
        // Tetap return PDF (mungkin dengan data kosong, tapi nggak error)
    }

    #[Test]
    public function export_pdf_dengan_id_user_tidak_ada_return_404(): void
    {
        $response = $this->actingAs($this->guru)->get('/perangkingan/pdf?id_user=99999');

        $response->assertStatus(404);
    }

    // ============================================================
    // 4. Authorization
    // ============================================================

    #[Test]
    public function siswa_tidak_bisa_export_pdf_siswa_lain(): void
    {
        // Buat siswa lain
        $siswaLain = User::create([
            'nama_user' => 'Budi',
            'username'  => 'siswa_lain',
            'password'  => bcrypt('123456'),
            'role'      => 'Siswa',
            'nis'       => '12346',
        ]);

        // Siswa mencoba akses data siswa lain dengan ?id_user=
        // Logic: currentUser->role !== 'Guru BK', jadi $userId = currentUser->id_user
        // Abaikan id_user dari query
        $response = $this->actingAs($this->siswa)
            ->get('/perangkingan/pdf?id_user=' . $siswaLain->id_user);

        $response->assertStatus(200);
        // PDF yang di-generate adalah punya $this->siswa, bukan $siswaLain
        $contentDisposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('Andi', $contentDisposition, 'Siswa hanya boleh akses data sendiri');
        $this->assertStringNotContainsString('Budi', $contentDisposition);
    }

    // ============================================================
    // 5. PDF dengan data kosong
    // ============================================================

    #[Test]
    public function pdf_untuk_siswa_tanpa_penilaian_masih_generate_valid_pdf(): void
    {
        // Buat siswa baru tanpa penilaian
        $emptySiswa = User::create([
            'nama_user' => 'Kosong',
            'username'  => 'empty_siswa',
            'password'  => bcrypt('123456'),
            'role'      => 'Siswa',
            'nis'       => '99999',
        ]);

        $response = $this->actingAs($this->guru)
            ->get('/perangkingan/pdf?id_user=' . $emptySiswa->id_user);

        $response->assertStatus(200);
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }
}
