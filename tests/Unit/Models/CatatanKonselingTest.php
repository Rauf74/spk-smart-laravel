<?php

namespace Tests\Unit\Models;

use App\Models\CatatanKonseling;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test untuk model CatatanKonseling.
 *
 * Memastikan:
 * - Tabel, primary key, fillable, dan relasi terkonfigurasi benar
 * - Relasi siswa() dan guru() berfungsi
 * - Bisa menyimpan catatan baru
 * - Bisa update catatan existing (timestamp berubah)
 */
class CatatanKonselingTest extends TestCase
{
    use RefreshDatabase;

    private User $guru;
    private User $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guru = User::create([
            'nama_user' => 'Pak Budi',
            'username'  => 'guru_test',
            'password'  => bcrypt('123456'),
            'role'      => 'Guru BK',
            'nis'       => null,
        ]);

        $this->siswa = User::create([
            'nama_user' => 'Andi Wijaya',
            'username'  => 'siswa_test',
            'password'  => bcrypt('123456'),
            'role'      => 'Siswa',
            'nis'       => '12345',
        ]);
    }

    // ============================================================
    // 1. Konfigurasi model
    // ============================================================

    #[Test]
    public function menggunakan_tabel_catatan_konseling(): void
    {
        $catatan = new CatatanKonseling();
        $this->assertEquals('catatan_konseling', $catatan->getTable());
    }

    #[Test]
    public function menggunakan_primary_key_id_catatan(): void
    {
        $catatan = new CatatanKonseling();
        $this->assertEquals('id_catatan', $catatan->getKeyName());
    }

    #[Test]
    public function fillable_hanya_id_user_id_guru_dan_catatan(): void
    {
        $catatan = new CatatanKonseling();
        $this->assertEquals(
            ['id_user', 'id_guru', 'catatan'],
            $catatan->getFillable()
        );
    }

    #[Test]
    public function timestamps_aktif(): void
    {
        $catatan = new CatatanKonseling();
        $this->assertTrue($catatan->usesTimestamps());
    }

    // ============================================================
    // 2. Create & update
    // ============================================================

    #[Test]
    public function bisa_menyimpan_catatan_baru(): void
    {
        $catatan = CatatanKonseling::create([
            'id_user' => $this->siswa->id_user,
            'id_guru' => $this->guru->id_user,
            'catatan' => 'Siswa menunjukkan minat tinggi di bidang IT.',
        ]);

        $this->assertNotNull($catatan->id_catatan);
        $this->assertDatabaseHas('catatan_konseling', [
            'id_user' => $this->siswa->id_user,
            'id_guru' => $this->guru->id_user,
            'catatan' => 'Siswa menunjukkan minat tinggi di bidang IT.',
        ]);
    }

    #[Test]
    public function fillable_hanya_mengizinkan_field_yang_ditentukan(): void
    {
        // Mass assignment via create() harusnya mengisi hanya field fillable
        $catatan = CatatanKonseling::create([
            'id_user'   => $this->siswa->id_user,
            'id_guru'   => $this->guru->id_user,
            'catatan'   => 'Test',
            'created_at' => '2020-01-01 00:00:00', // harus diabaikan (bukan fillable)
        ]);

        // Field fillable harus terisi
        $this->assertEquals($this->siswa->id_user, $catatan->id_user);
        $this->assertEquals($this->guru->id_user, $catatan->id_guru);
        $this->assertEquals('Test', $catatan->catatan);

        // created_at dari array harusnya diabaikan, jadi timestamp asli (sekarang)
        $this->assertNotEquals('2020-01-01 00:00:00', $catatan->created_at->format('Y-m-d H:i:s'));
    }

    #[Test]
    public function update_catatan_mengubah_updated_at(): void
    {
        $catatan = CatatanKonseling::create([
            'id_user' => $this->siswa->id_user,
            'id_guru' => $this->guru->id_user,
            'catatan' => 'Catatan awal',
        ]);

        $originalUpdatedAt = $catatan->updated_at;

        // Sleep 1 detik biar timestamp pasti beda
        sleep(1);

        $catatan->update(['catatan' => 'Catatan setelah diubah']);
        $catatan->refresh();

        $this->assertEquals('Catatan setelah diubah', $catatan->catatan);
        $this->assertNotEquals(
            $originalUpdatedAt->timestamp,
            $catatan->updated_at->timestamp,
            'updated_at harus berubah setelah update'
        );
    }

    // ============================================================
    // 3. Relasi
    // ============================================================

    #[Test]
    public function relasi_siswa_mengembalikan_user_siswa(): void
    {
        $catatan = CatatanKonseling::create([
            'id_user' => $this->siswa->id_user,
            'id_guru' => $this->guru->id_user,
            'catatan' => 'Test relasi',
        ]);

        $this->assertInstanceOf(User::class, $catatan->siswa);
        $this->assertEquals($this->siswa->id_user, $catatan->siswa->id_user);
        $this->assertEquals('Andi Wijaya', $catatan->siswa->nama_user);
    }

    #[Test]
    public function relasi_guru_mengembalikan_user_guru(): void
    {
        $catatan = CatatanKonseling::create([
            'id_user' => $this->siswa->id_user,
            'id_guru' => $this->guru->id_user,
            'catatan' => 'Test relasi guru',
        ]);

        $this->assertInstanceOf(User::class, $catatan->guru);
        $this->assertEquals($this->guru->id_user, $catatan->guru->id_user);
        $this->assertEquals('Pak Budi', $catatan->guru->nama_user);
    }

    #[Test]
    public function catatan_bisa_memiliki_lebih_dari_satu_guru_untuk_siswa_yang_sama(): void
    {
        // Buat guru kedua
        $guru2 = User::create([
            'nama_user' => 'Bu Sari',
            'username'  => 'guru_test_2',
            'password'  => bcrypt('123456'),
            'role'      => 'Guru BK',
            'nis'       => null,
        ]);

        // Dua guru catat siswa yang sama
        CatatanKonseling::create([
            'id_user' => $this->siswa->id_user,
            'id_guru' => $this->guru->id_user,
            'catatan' => 'Catatan dari Pak Budi',
        ]);

        CatatanKonseling::create([
            'id_user' => $this->siswa->id_user,
            'id_guru' => $guru2->id_user,
            'catatan' => 'Catatan dari Bu Sari',
        ]);

        $this->assertEquals(2, CatatanKonseling::where('id_user', $this->siswa->id_user)->count());
    }

    #[Test]
    public function catatan_per_guru_unik_untuk_setiap_siswa(): void
    {
        // Guru yang sama catat siswa yang sama 2x
        // (Logic updateOrCreate di controller yang handle, di model ini raw)
        $first = CatatanKonseling::create([
            'id_user' => $this->siswa->id_user,
            'id_guru' => $this->guru->id_user,
            'catatan' => 'Catatan pertama',
        ]);

        $second = CatatanKonseling::create([
            'id_user' => $this->siswa->id_user,
            'id_guru' => $this->guru->id_user,
            'catatan' => 'Catatan kedua (duplikat)',
        ]);

        // Tanpa updateOrCreate, model tetap izinkan duplikat
        // Validasi uniqueness adalah tanggung jawab controller
        $this->assertEquals(2, CatatanKonseling::where('id_user', $this->siswa->id_user)
            ->where('id_guru', $this->guru->id_user)
            ->count());
        $this->assertNotEquals($first->id_catatan, $second->id_catatan);
    }
}
