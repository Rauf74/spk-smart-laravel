<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function quick_login_guru_auto_generates_user_dan_berhasil_login(): void
    {
        // Pastikan tidak ada user di database awal
        $this->assertEquals(0, User::count());

        $response = $this->get('/login/quick/guru');

        $response->assertRedirect('/');
        $this->assertAuthenticated();
        
        $user = Auth::user();
        $this->assertNotNull($user);
        $this->assertEquals('Guru BK', $user->role);
        $this->assertTrue((bool) $user->is_logged_in);
    }

    #[Test]
    public function quick_login_siswa_auto_generates_user_dan_berhasil_login(): void
    {
        // Pastikan tidak ada user di database awal
        $this->assertEquals(0, User::count());

        $response = $this->get('/login/quick/siswa');

        $response->assertRedirect('/');
        $this->assertAuthenticated();

        $user = Auth::user();
        $this->assertNotNull($user);
        $this->assertEquals('Siswa', $user->role);
        $this->assertTrue((bool) $user->is_logged_in);
    }

    #[Test]
    public function quick_login_menggunakan_user_existing_jika_sudah_ada(): void
    {
        $existingGuru = User::create([
            'nama_user' => 'Guru BK Asli',
            'username' => 'gurubkasli',
            'password' => bcrypt('password'),
            'role' => 'Guru BK',
            'jenis_kelamin' => 'Laki-laki',
            'is_logged_in' => false,
        ]);

        $response = $this->get('/login/quick/guru');

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($existingGuru);
    }

    #[Test]
    public function quick_generate_guru_membuat_user_acak_baru_dan_mengembalikan_json(): void
    {
        $response = $this->postJson('/login/quick-generate', [
            'role' => 'guru',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'nama_user',
            'username',
            'password',
            'role',
            'redirect',
        ]);

        $this->assertAuthenticated();
        $user = Auth::user();
        $this->assertEquals('Guru BK', $user->role);
        $this->assertStringStartsWith('guru_', $response->json('username'));
    }

    #[Test]
    public function quick_generate_siswa_membuat_user_acak_baru_dan_mengembalikan_json(): void
    {
        $response = $this->postJson('/login/quick-generate', [
            'role' => 'siswa',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'nama_user',
            'username',
            'password',
            'role',
            'redirect',
        ]);

        $this->assertAuthenticated();
        $user = Auth::user();
        $this->assertEquals('Siswa', $user->role);
        $this->assertStringStartsWith('siswa_', $response->json('username'));
    }
}
