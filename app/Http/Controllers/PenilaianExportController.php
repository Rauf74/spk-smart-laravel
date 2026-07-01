<?php

namespace App\Http\Controllers;

use App\Exports\PenilaianExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
