@component('mail::message')
# Kuesioner Baru Telah Dikirim

Halo,

Siswa **{{ $siswa->nama_user }}** ({{ $siswa->nis ?? '-' }}) telah mengirimkan jawaban kuesioner melalui Sistem Pendukung Keputusan.

**Detail:**
- Nama: {{ $siswa->nama_user }}
- Username: {{ $siswa->username }}
- NIS: {{ $siswa->nis ?? '-' }}
- Jumlah Jawaban: {{ $jumlahJawaban }} pertanyaan
- Waktu: {{ now()->format('d F Y, H:i') }} WIB

@component('mail::button', ['url' => $urlHasil, 'color' => 'success'])
Lihat Hasil Rekomendasi
@endcomponent

Salam,<br>
{{ config('app.name') }}
@endcomponent
