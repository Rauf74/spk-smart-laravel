<?php

namespace App\Observers;

use App\Mail\PenilaianSubmittedMail;
use App\Models\Penilaian;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PenilaianObserver
{
    /**
     * Track siswa yang sudah dikirim email notifikasi supaya tidak spam
     * kalau 1 siswa submit berkali-kali (misal dari auto-save).
     */
    private array $notifiedSiswa = [];

    public function created(Penilaian $penilaian): void
    {
        // Hindari spam: hanya kirim notifikasi 1x per siswa per sesi observer
        if (in_array($penilaian->id_user, $this->notifiedSiswa, true)) {
            return;
        }
        $this->notifiedSiswa[] = $penilaian->id_user;

        try {
            $siswa = User::find($penilaian->id_user);
            if (!$siswa || $siswa->role !== 'Siswa') {
                return;
            }

            // Ambil semua Guru BK yang punya email
            $guruList = User::where('role', 'Guru BK')
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->get();

            if ($guruList->isEmpty()) {
                return;
            }

            $jumlahJawaban = Penilaian::where('id_user', $siswa->id_user)
                ->distinct('id_pertanyaan')
                ->count('id_pertanyaan');

            foreach ($guruList as $guru) {
                Mail::to($guru->email)->send(new PenilaianSubmittedMail($siswa, $jumlahJawaban));
            }

            Log::info('Email notifikasi dikirim', [
                'siswa' => $siswa->nama_user,
                'guru_count' => $guruList->count(),
            ]);
        } catch (\Throwable $e) {
            // Log error tapi jangan ganggu flow utama
            Log::error('Gagal kirim email notifikasi: ' . $e->getMessage());
        }
    }

    public function updated(Penilaian $penilaian): void {}
    public function deleted(Penilaian $penilaian): void {}
    public function restored(Penilaian $penilaian): void {}
    public function forceDeleted(Penilaian $penilaian): void {}
}
