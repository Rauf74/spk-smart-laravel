<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik Cards
        $total_kriteria = \App\Models\Kriteria::count();
        $total_subkriteria = \App\Models\Subkriteria::count();
        $total_alternatif = \App\Models\Alternatif::count();
        $total_users = \App\Models\User::count();

        // Total Siswa
        $total_siswa = \App\Models\User::where('role', 'Siswa')->count();

        // Total Penilaian (Siswa yang sudah menilai)
        $total_penilaian = \App\Models\Penilaian::distinct('id_user')->count('id_user');

        // Grafik Distribusi Jenis Kriteria
        $kriteria_jenis_data = \App\Models\Kriteria::selectRaw('jenis, COUNT(*) as jumlah')
            ->groupBy('jenis')
            ->get()
            ->map(function ($item) {
                return [
                    'jenis' => $item->jenis,
                    'jumlah' => $item->jumlah
                ];
            });

        // Grafik Bobot Kriteria
        $bobot_kriteria_data = \App\Models\Kriteria::orderBy('bobot', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'nama_kriteria' => $item->nama_kriteria,
                    'bobot' => (float) $item->bobot
                ];
            });

        // Rata-rata Bobot
        $avg_bobot = round(\App\Models\Kriteria::avg('bobot') ?? 0, 2);

        // Program Studi Terbaru
        $alternatif_terbaru = \App\Models\Alternatif::orderBy('id_alternatif', 'desc')
            ->limit(4)
            ->get();

        // Aktivitas Terbaru (Manual Union karena complex query di legacy)
        // Kita bisa gunakan Union query builder Laravel
        $users = \App\Models\User::selectRaw("'user_baru' as type, id_user as id, nama_user as detail, created_at as time, id_user as sort_key")
            ->orderBy('id_user', 'desc')->limit(5);

        $kriterias = \App\Models\Kriteria::selectRaw("'kriteria_baru' as type, id_kriteria as id, nama_kriteria as detail, created_at as time, id_kriteria as sort_key")
            ->orderBy('id_kriteria', 'desc')->limit(5);

        $alternatifs = \App\Models\Alternatif::selectRaw("'alternatif_baru' as type, id_alternatif as id, nama_alternatif as detail, created_at as time, id_alternatif as sort_key")
            ->orderBy('id_alternatif', 'desc')->limit(5);

        // Penilaian selesai agak tricky karena group by. Kita sederhanakan ambil unique penilaian terakhir per user
        // Untuk "penilaian_selesai", legacy logic: max(id_penilaian) per user.
        // Di Laravel kita bisa query builder:
        /*
        SELECT 'penilaian_selesai' as type, p.id_user as id, u.nama_user as detail, MAX(p.id_penilaian) as sort_key
             FROM penilaian p
             JOIN users u ON p.id_user = u.id_user
             GROUP BY p.id_user
             ORDER BY sort_key DESC LIMIT 5
        */
        $penilaians = DB::table('penilaian as p')
            ->join('users as u', 'p.id_user', '=', 'u.id_user')
            ->selectRaw("'penilaian_selesai' as type, p.id_user as id, u.nama_user as detail, MAX(p.created_at) as time, MAX(p.id_penilaian) as sort_key")
            ->groupBy('p.id_user', 'u.nama_user') // PostgreSQL butuh u.nama_user di group by juga
            ->orderBy('sort_key', 'desc')
            ->limit(5);

        // Gabungkan semua query
        $activities = $users->union($kriterias)
            ->union($alternatifs)
            ->union($penilaians)
            ->orderBy('sort_key', 'desc')
            ->limit(4)
            ->get();

        // Format activity data untuk view
        $recent_activities = $activities->map(function ($activity) {
            $action = '';
            $icon = '';
            $color = '';
            $time = 'ID: ' . $activity->sort_key;

            switch ($activity->type) {
                case 'user_baru':
                    $action = "User baru registrasi: <strong>" . htmlspecialchars($activity->detail) . "</strong>";
                    $icon = 'ti ti-user-plus';
                    $color = 'warning';
                    break;
                case 'kriteria_baru':
                    $action = "Kriteria baru ditambahkan: <strong>" . htmlspecialchars($activity->detail) . "</strong>";
                    $icon = 'ti ti-article';
                    $color = 'primary';
                    break;
                case 'alternatif_baru':
                    $action = "Prodi baru ditambahkan: <strong>" . htmlspecialchars($activity->detail) . "</strong>";
                    $icon = 'ti ti-building';
                    $color = 'success';
                    break;
                case 'penilaian_selesai':
                    $action = "Siswa menyelesaikan penilaian: <strong>" . htmlspecialchars($activity->detail) . "</strong>";
                    $icon = 'ti ti-check';
                    $color = 'info';
                    break;
            }

            return [
                'action' => $action, // Kita render raw html di blade dengan {!! !!} hati-hati XSS, tapi ini data internal origin
                'time' => $time,
                'icon' => $icon,
                'color' => $color
            ];
        });

        if ($recent_activities->isEmpty()) {
            $recent_activities->push([
                'action' => 'Belum ada aktivitas terbaru yang tercatat.',
                'time' => '',
                'icon' => 'ti ti-info-circle',
                'color' => 'secondary'
            ]);
        }

        return view('dashboard', compact(
            'total_kriteria',
            'total_subkriteria',
            'total_alternatif',
            'total_users',
            'total_siswa',
            'total_penilaian',
            'kriteria_jenis_data',
            'bobot_kriteria_data',
            'avg_bobot',
            'alternatif_terbaru',
            'recent_activities'
        ));
    }
}
