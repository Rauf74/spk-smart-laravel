<?php

namespace App\Http\Controllers;

use App\Exports\PenilaianExport;
use App\Models\Alternatif;
use App\Models\Penilaian;
use App\Models\User;
use App\Services\SmartCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenilaianExportController extends Controller
{
    /**
     * Download Excel berisi data penilaian + hasil SMART.
     *
     * Query param:
     * - kelas: filter prefix NIS (opsional, misal "10" untuk kelas 10)
     */
    public function download(Request $request)
    {
        // Hanya Guru BK yang boleh export
        if (Auth::user()->role !== 'Guru BK') {
            abort(403, 'Hanya Guru BK yang dapat mengexport data.');
        }

        $kelas = $request->query('kelas');
        $kelas = is_string($kelas) ? trim($kelas) : null;

        // Sanitasi: max 10 char, alphanumeric
        if ($kelas !== null && $kelas !== '') {
            $kelas = substr(preg_replace('/[^a-zA-Z0-9]/', '', $kelas), 0, 10);
        } else {
            $kelas = null;
        }

        $filename = 'Data-Penilaian'
            . ($kelas ? "-Kelas{$kelas}" : '')
            . '-' . date('Y-m-d-His')
            . '.xlsx';

        $export = new PenilaianExport($filename, $kelas);
        $filepath = $export->store(storage_path('app/exports'));

        return response()->download($filepath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Tampilkan halaman rekap per kelas (visual ringkasan + link export).
     */
    public function rekapPerKelas(Request $request, SmartCalculationService $smartService)
    {
        if (Auth::user()->role !== 'Guru BK') {
            abort(403, 'Hanya Guru BK yang dapat melihat rekap.');
        }

        // Ambil semua siswa + kelompokkan per kelas (2 digit pertama NIS)
        $allSiswa = User::where('role', 'Siswa')
            ->whereNotNull('nis')
            ->where('nis', '!=', '')
            ->orderBy('nama_user')
            ->get();

        $rekapKelas = $allSiswa->groupBy(function ($siswa) {
            return substr(preg_replace('/[^0-9]/', '', (string) $siswa->nis), 0, 2);
        })->filter(fn($group, $kelas) => strlen($kelas) === 2 && $group->isNotEmpty())
          ->map(function ($siswas, $kelas) use ($smartService) {
              $totalSiswa = $siswas->count();
              $totalPenilaian = Penilaian::whereIn('id_user', $siswas->pluck('id_user'))->count();
              $siswaSudahNilai = Penilaian::whereIn('id_user', $siswas->pluck('id_user'))
                  ->select('id_user')
                  ->groupBy('id_user')
                  ->havingRaw('COUNT(DISTINCT id_pertanyaan) > 0')
                  ->get()
                  ->count();

              // Distribusi rekomendasi di kelas ini
              $distribusi = collect();
              foreach ($siswas as $siswa) {
                  if (!Penilaian::where('id_user', $siswa->id_user)->exists()) {
                      continue;
                  }
                  $result = $smartService->calculate($siswa->id_user, sortDescending: true);
                  $top = $result['hasil'][0] ?? null;
                  if ($top) {
                      $distribusi[$top['nama_alternatif']] = ($distribusi[$top['nama_alternatif']] ?? 0) + 1;
                  }
              }

              return [
                  'kelas'             => $kelas,
                  'total_siswa'       => $totalSiswa,
                  'siswa_sudah_nilai' => $siswaSudahNilai,
                  'total_penilaian'   => $totalPenilaian,
                  'distribusi'        => $distribusi->sortDesc(),
              ];
          })
          ->sortKeys()
          ->values();

        return view('penilaian.rekap_kelas', [
            'rekapKelas' => $rekapKelas,
        ]);
    }
}
