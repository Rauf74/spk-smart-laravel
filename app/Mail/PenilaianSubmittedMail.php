<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PenilaianSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $siswa;
    public int $jumlahJawaban;

    /**
     * Create a new message instance.
     */
    public function __construct(User $siswa, int $jumlahJawaban)
    {
        $this->siswa = $siswa;
        $this->jumlahJawaban = $jumlahJawaban;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[' . config('app.name') . '] ' . $this->siswa->nama_user . ' telah mengisi kuesioner',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.penilaian-submitted',
            with: [
                'siswa' => $this->siswa,
                'jumlahJawaban' => $this->jumlahJawaban,
                'urlHasil' => route('perangkingan.index', ['id_user' => $this->siswa->id_user]),
            ],
        );
    }
}
