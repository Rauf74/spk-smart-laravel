<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Kriteria;
use App\Models\Subkriteria;
use App\Models\Alternatif;
use App\Models\Penilaian;

/**
 * Controller untuk halaman Dashboard.
 * 
 * Dashboard menampilkan ringkasan data sistem:
 * - Statistik (jumlah kriteria, alternatif, user, dll)
 * - Grafik distribusi kriteria
 * - Aktivitas terbaru
 */
class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard dengan semua statistik.
     */
    public function index()
    {
        // ==============================================
        // STATISTIK CARDS
        // ==============================================
        $totalKriteria = Kriteria::count();
        $totalSubkriteria = Subkriteria::count();
        $totalAlternatif = Alternatif::count();
        $totalUsers = User::count();
        $totalSiswa = User::where('role', 'Siswa')->count();

        // Jumlah siswa yang sudah melakukan penilaian
        $totalPenilaian = Penilaian::distinct('id_user')->count('id_user');

        // ==============================================
        // DATA UNTUK GRAFIK
        // ==============================================

        // Grafik: Distribusi Jenis Kriteria (Benefit vs Cost)
        $kriteriaJenisData = Kriteria::selectRaw('jenis, COUNT(*) as jumlah')
            ->groupBy('jenis')
            ->get()
            ->map(fn($item) => [
                'jenis' => $item->jenis,
                'jumlah' => $item->jumlah
            ]);

        // Grafik: Bobot per Kriteria
        $bobotKriteriaData = Kriteria::orderBy('bobot', 'desc')
            ->get()
            ->map(fn($item) => [
                'nama_kriteria' => $item->nama_kriteria,
                'bobot' => (float) $item->bobot
            ]);

        // Rata-rata bobot
        $avgBobot = round(Kriteria::avg('bobot') ?? 0, 2);

        // ==============================================
        // ALTERNATIF TERBARU
        // ==============================================
        $alternatifTerbaru = Alternatif::orderBy('id_alternatif', 'desc')
            ->limit(4)
            ->get();

        // ==============================================
        // AKTIVITAS TERBARU
        // ==============================================
        $recentActivities = $this->getRecentActivities();

        // Kirim data ke view
        return view('dashboard', [
            'total_kriteria' => $totalKriteria,
            'total_subkriteria' => $totalSubkriteria,
            'total_alternatif' => $totalAlternatif,
            'total_users' => $totalUsers,
            'total_siswa' => $totalSiswa,
            'total_penilaian' => $totalPenilaian,
            'kriteria_jenis_data' => $kriteriaJenisData,
            'bobot_kriteria_data' => $bobotKriteriaData,
            'avg_bobot' => $avgBobot,
            'alternatif_terbaru' => $alternatifTerbaru,
            'recent_activities' => $recentActivities,
        ]);
    }

    /**
     * Ambil aktivitas terbaru dari berbagai tabel.
     * 
     * Menggunakan UNION query untuk menggabungkan:
     * - User baru
     * - Kriteria baru
     * - Alternatif baru
     * - Penilaian selesai
     * 
     * @return \Illuminate\Support\Collection
     */
    private function getRecentActivities()
    {
        // Query untuk user baru
        $usersQuery = User::selectRaw("
            'user_baru' as type, 
            id_user as id, 
            nama_user as detail, 
            created_at as time, 
            id_user as sort_key
        ")->orderBy('id_user', 'desc')->limit(5);

        // Query untuk kriteria baru
        $kriteriaQuery = Kriteria::selectRaw("
            'kriteria_baru' as type, 
            id_kriteria as id, 
            nama_kriteria as detail, 
            created_at as time, 
            id_kriteria as sort_key
        ")->orderBy('id_kriteria', 'desc')->limit(5);

        // Query untuk alternatif baru
        $alternatifQuery = Alternatif::selectRaw("
            'alternatif_baru' as type, 
            id_alternatif as id, 
            nama_alternatif as detail, 
            created_at as time, 
            id_alternatif as sort_key
        ")->orderBy('id_alternatif', 'desc')->limit(5);

        // Query untuk penilaian selesai
        $penilaianQuery = DB::table('penilaian as p')
            ->join('users as u', 'p.id_user', '=', 'u.id_user')
            ->selectRaw("
                'penilaian_selesai' as type, 
                p.id_user as id, 
                u.nama_user as detail, 
                MAX(p.created_at) as time, 
                MAX(p.id_penilaian) as sort_key
            ")
            ->groupBy('p.id_user', 'u.nama_user')
            ->orderBy('sort_key', 'desc')
            ->limit(5);

        // Gabungkan semua query dengan UNION
        $activities = $usersQuery
            ->union($kriteriaQuery)
            ->union($alternatifQuery)
            ->union($penilaianQuery)
            ->orderBy('sort_key', 'desc')
            ->limit(4)
            ->get();

        // Format data untuk tampilan
        $formatted = $activities->map(function ($activity) {
            return $this->formatActivity($activity);
        });

        // Jika kosong, tampilkan pesan default
        if ($formatted->isEmpty()) {
            $formatted->push([
                'action' => 'Belum ada aktivitas terbaru.',
                'time' => '',
                'icon' => 'ti ti-info-circle',
                'color' => 'secondary'
            ]);
        }

        return $formatted;
    }

    /**
     * Format data aktivitas untuk tampilan.
     * 
     * @param object $activity
     * @return array
     */
    private function formatActivity($activity)
    {
        $config = [
            'user_baru' => [
                'action' => "User baru: <strong>" . e($activity->detail) . "</strong>",
                'icon' => 'ti ti-user-plus',
                'color' => 'warning',
            ],
            'kriteria_baru' => [
                'action' => "Kriteria baru: <strong>" . e($activity->detail) . "</strong>",
                'icon' => 'ti ti-article',
                'color' => 'primary',
            ],
            'alternatif_baru' => [
                'action' => "Prodi baru: <strong>" . e($activity->detail) . "</strong>",
                'icon' => 'ti ti-building',
                'color' => 'success',
            ],
            'penilaian_selesai' => [
                'action' => "Penilaian selesai: <strong>" . e($activity->detail) . "</strong>",
                'icon' => 'ti ti-check',
                'color' => 'info',
            ],
        ];

        $default = [
            'action' => 'Aktivitas tidak dikenal',
            'icon' => 'ti ti-question-mark',
            'color' => 'secondary',
        ];

        $data = $config[$activity->type] ?? $default;
        $data['time'] = 'ID: ' . $activity->sort_key;

        return $data;
    }
}
