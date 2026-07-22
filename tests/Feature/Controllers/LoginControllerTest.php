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
}
