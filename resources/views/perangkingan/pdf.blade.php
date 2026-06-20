<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Rekomendasi - {{ $siswa->nama_user }}</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11pt; line-height: 1.6; color: #333; }
        .kop { text-align: center; border-bottom: 2px solid #5D87FF; padding-bottom: 10px; margin-bottom: 20px; }
        .kop h2 { margin: 0; font-size: 16pt; color: #5D87FF; }
        .kop p { margin: 2px 0; font-size: 10pt; color: #666; }
        .section { margin-bottom: 20px; }
        .section h3 { font-size: 13pt; color: #5D87FF; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 10px; }
        .hero { background: #f0f4ff; border-radius: 8px; padding: 15px; text-align: center; margin-bottom: 20px; }
        .hero h1 { margin: 0; font-size: 18pt; color: #333; }
        .hero .persen { font-size: 28pt; font-weight: bold; color: #5D87FF; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 10pt; }
        th { background: #f8f9fa; font-weight: 600; }
        .footer { margin-top: 30px; text-align: right; font-size: 10pt; color: #666; }
        .footer p { margin: 2px 0; }
    </style>
</head>
<body>
    <div class="kop">
        <h2>SMK Muhammadiyah 3 Tangerang Selatan</h2>
        <p>Sistem Pendukung Keputusan Rekomendasi Program Studi</p>
        <p>Metode SMART (Simple Multi-Attribute Rating Technique)</p>
    </div>

    <div class="section">
        <p><strong>Nama Siswa:</strong> {{ $siswa->nama_user }}</p>
        <p><strong>Username:</strong> {{ $siswa->username }}</p>
        <p><strong>Tanggal:</strong> {{ $tanggal }}</p>
    </div>

    @if($topAlternatif)
        <div class="hero">
            <h1>Rekomendasi Utama</h1>
            <div class="persen">{{ $topAlternatif['nama_alternatif'] }}</div>
            <p>Skor Kecocokan: <strong>{{ $persenKecocokan }}%</strong></p>
        </div>
    @endif

    <div class="section">
        <h3>Perangkingan Lengkap</h3>
        <table>
            <thead>
                <tr>
                    <th>Peringkat</th>
                    <th>Program Studi</th>
                    <th>Nilai Akhir</th>
                    <th>Kecocokan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hasil as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item['nama_alternatif'] }}</td>
                        <td>{{ number_format($item['nilai_akhir'], 4) }}</td>
                        <td>{{ min(100, round($item['nilai_akhir'] * 100)) }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Dicetak pada {{ $tanggal }}</p>
        <p>SPK SMART Rekomendasi Program Studi</p>
    </div>
</body>
</html>
