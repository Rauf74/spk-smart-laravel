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
    public function index(Request $request)
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
        // STATUS SISWA (Guru BK Dashboard)
        // ==============================================
        $totalPertanyaan = \App\Models\Pertanyaan::count();

        // Query dasar untuk status siswa (di-reuse untuk stats + tabel)
        $search = trim((string) $request->query('q', ''));
        $statusFilter = $request->query('status', ''); // '', 'belum', 'sedang', 'selesai'

        $siswaQuery = User::where('role', 'Siswa')
            ->leftJoin('penilaian as p', 'users.id_user', '=', 'p.id_user')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('users.nama_user', 'LIKE', "%{$search}%")
                        ->orWhere('users.username', 'LIKE', "%{$search}%")
                        ->orWhere('users.nis', 'LIKE', "%{$search}%");
                });
            })
            ->selectRaw('
                users.id_user,
                users.nama_user,
                users.username,
                users.nis,
                COUNT(DISTINCT p.id_pertanyaan) as jawaban_count
            ')
            ->groupBy('users.id_user', 'users.nama_user', 'users.username', 'users.nis');

        // Statistik agregat (semua siswa, tanpa pagination)
        $allSiswaStatus = (clone $siswaQuery)
            ->orderBy('users.nama_user')
            ->get()
            ->map(function ($siswa) use ($totalPertanyaan) {
                $jawaban = $siswa->jawaban_count;
                if ($jawaban == 0) {
                    $siswa->status = 'belum';
                    $siswa->status_label = 'Belum Mengisi';
                    $siswa->status_class = 'bg-danger';
                    $siswa->status_icon = 'ti ti-circle-x';
                } elseif ($jawaban < $totalPertanyaan) {
                    $siswa->status = 'sedang';
                    $siswa->status_label = 'Sedang Mengisi';
                    $siswa->status_class = 'bg-warning';
                    $siswa->status_icon = 'ti ti-loader';
                    $siswa->progress = round(($jawaban / $totalPertanyaan) * 100);
                } else {
                    $siswa->status = 'selesai';
                    $siswa->status_label = 'Selesai';
                    $siswa->status_class = 'bg-success';
                    $siswa->status_icon = 'ti ti-circle-check';
                    $siswa->progress = 100;
                }
                return $siswa;
            });

        $belumIsi = $allSiswaStatus->where('status', 'belum')->count();
        $sedangIsi = $allSiswaStatus->where('status', 'sedang')->count();
        $selesaiIsi = $allSiswaStatus->where('status', 'selesai')->count();

        // Tabel siswa dengan pagination (10 per halaman)
        // Filter status diterapkan pada level query SQL (database) menggunakan havingRaw
        $siswaQueryPaginated = clone $siswaQuery;

        if ($statusFilter === 'belum') {
            $siswaQueryPaginated->havingRaw('COUNT(DISTINCT p.id_pertanyaan) = 0');
        } elseif ($statusFilter === 'sedang') {
            $siswaQueryPaginated->havingRaw('COUNT(DISTINCT p.id_pertanyaan) > 0 AND COUNT(DISTINCT p.id_pertanyaan) < ?', [$totalPertanyaan]);
        } elseif ($statusFilter === 'selesai') {
            $siswaQueryPaginated->havingRaw('COUNT(DISTINCT p.id_pertanyaan) >= ?', [$totalPertanyaan]);
        }

        $siswaStatusPaginated = $siswaQueryPaginated
            ->orderBy('users.nama_user')
            ->paginate(10)
            ->withQueryString() // penting: pagination link bawa query string ?q=...&status=...
            ->through(function ($siswa) use ($totalPertanyaan) {
                $jawaban = $siswa->jawaban_count;
                if ($jawaban == 0) {
                    $siswa->status = 'belum';
                    $siswa->status_label = 'Belum Mengisi';
                    $siswa->status_class = 'bg-danger';
                    $siswa->status_icon = 'ti ti-circle-x';
                    $siswa->progress = 0;
                } elseif ($jawaban < $totalPertanyaan) {
                    $siswa->status = 'sedang';
                    $siswa->status_label = 'Sedang Mengisi';
                    $siswa->status_class = 'bg-warning';
                    $siswa->status_icon = 'ti ti-loader';
                    $siswa->progress = round(($jawaban / $totalPertanyaan) * 100);
                } else {
                    $siswa->status = 'selesai';
                    $siswa->status_label = 'Selesai';
                    $siswa->status_class = 'bg-success';
                    $siswa->status_icon = 'ti ti-circle-check';
                    $siswa->progress = 100;
                }
                return $siswa;
            });

        // ==============================================
        // STATISTIK AGREGAT (B1)
        // ==============================================

        // Persentase progress rata-rata siswa
        $totalJawabanAllSiswa = $allSiswaStatus->sum('jawaban_count');
        $maxJawaban = $totalSiswa * max($totalPertanyaan, 1);
        $avgProgressPercent = $maxJawaban > 0
            ? round(($totalJawabanAllSiswa / $maxJawaban) * 100)
            : 0;

        // Kriteria dengan bobot tertinggi (top 3)
        $topKriterias = Kriteria::orderBy('bobot', 'desc')->limit(3)->get();

        // Distribusi rekomendasi program studi (jika ada hasil perangkingan)
        $rekomendasiDistribusi = collect();
        $topRekomendasi = null;
        $avgNilaiAkhir = 0;

        $siswaSelesaiIds = $allSiswaStatus->where('status', 'selesai')->pluck('id_user');
        if ($siswaSelesaiIds->isNotEmpty() && $totalPertanyaan > 0) {
            // Untuk setiap siswa yang sudah selesai, hitung top 1 alternatif
            $distribusiRaw = DB::table('users as u')
                ->join('penilaian as p', 'u.id_user', '=', 'p.id_user')
                ->join('alternatif as a', 'p.id_alternatif', '=', 'a.id_alternatif')
                ->where('u.role', 'Siswa')
                ->whereIn('u.id_user', $siswaSelesaiIds)
                ->groupBy('u.id_user', 'a.id_alternatif', 'a.nama_alternatif')
                ->selectRaw('u.id_user, a.id_alternatif, a.nama_alternatif, COUNT(*) as vote')
                ->get()
                ->groupBy('id_user')
                ->map(function ($votes) {
                    return $votes->sortByDesc('vote')->first();
                })
                ->groupBy('nama_alternatif')
                ->map->count();

            $rekomendasiDistribusi = $distribusiRaw->sortDesc();

            if ($rekomendasiDistribusi->isNotEmpty()) {
                $topRekomendasi = $rekomendasiDistribusi->keys()->first();
            }
        }

        // Aktivitas 7 hari terakhir
        $activities7d = [
            'user_baru'      => User::where('created_at', '>=', now()->subDays(7))->count(),
            'kriteria_baru'  => Kriteria::where('created_at', '>=', now()->subDays(7))->count(),
            'alternatif_baru' => Alternatif::where('created_at', '>=', now()->subDays(7))->count(),
            'penilaian_baru' => Penilaian::where('created_at', '>=', now()->subDays(7))->distinct('id_user')->count('id_user'),
        ];

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
            'siswa_status' => $siswaStatusPaginated,
            'belum_isi' => $belumIsi,
            'sedang_isi' => $sedangIsi,
            'selesai_isi' => $selesaiIsi,
            'total_pertanyaan' => $totalPertanyaan,
            'search' => $search,
            'statusFilter' => $statusFilter,
            'avgProgressPercent' => $avgProgressPercent,
            'topKriterias' => $topKriterias,
            'rekomendasiDistribusi' => $rekomendasiDistribusi,
            'topRekomendasi' => $topRekomendasi,
            'activities7d' => $activities7d,
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
