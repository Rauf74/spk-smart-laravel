<?php

namespace App\Exports;

use App\Models\Penilaian;
use App\Models\User;
use App\Services\SmartCalculationService;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;

/**
 * Export data penilaian ke Excel (.xlsx).
 *
 * Output berisi 2 sheet:
 * - Sheet 1 "Data Penilaian": semua baris penilaian dengan info siswa
 * - Sheet 2 "Hasil SMART": ranking per siswa (top 3 rekomendasi)
 */
class PenilaianExport
{
    private string $filename;
    private ?string $kelasFilter;

    public function __construct(string $filename = 'Data-Penilaian.xlsx', ?string $kelasFilter = null)
    {
        $this->filename = $filename;
        $this->kelasFilter = $kelasFilter;
    }

    /**
     * Generate file Excel dan simpan ke path.
     * Return path file.
     */
    public function store(string $directory): string
    {
        $filepath = rtrim($directory, '/') . '/' . $this->filename;

        $writer = new Writer();
        $writer->openToFile($filepath);

        // Style untuk header
        $headerStyle = (new Style())
            ->withFontBold()
            ->withBackgroundColor('5D87FF')
            ->withFontColor('FFFFFF');

        // === SHEET 1: Data Penilaian ===
        $sheet1 = $writer->getCurrentSheet();
        $sheet1->setName('Data Penilaian');

        $writer->addRow(Row::fromValues([
            'No', 'NIS', 'Nama Siswa', 'Username', 'Kode Kriteria', 'Nama Kriteria',
            'Jenis Kriteria', 'Bobot', 'Kode Program Studi', 'Nama Program Studi',
            'ID Pertanyaan', 'Teks Pertanyaan', 'Pilihan Jawaban', 'Nilai Jawaban', 'Tanggal',
        ], $headerStyle));

        $rowNumber = 1;
        Penilaian::with(['user', 'kriteria', 'alternatif', 'pertanyaan', 'subkriteria'])
            ->whereHas('user', function ($q) {
                $q->where('role', 'Siswa');
                if ($this->kelasFilter) {
                    // Filter by NIS prefix (misal "10" untuk kelas 10)
                    $q->where('nis', 'LIKE', $this->kelasFilter . '%');
                }
            })
            ->orderBy('id_user')
            ->orderBy('id_pertanyaan')
            ->chunk(500, function ($penilaians) use ($writer, &$rowNumber) {
                foreach ($penilaians as $p) {
                    $writer->addRow(Row::fromValues([
                        $rowNumber++,
                        $p->user->nis ?? '-',
                        $p->user->nama_user ?? '-',
                        $p->user->username ?? '-',
                        $p->kriteria->kode_kriteria ?? '-',
                        $p->kriteria->nama_kriteria ?? '-',
                        $p->kriteria->jenis ?? '-',
                        $p->kriteria->bobot ?? 0,
                        $p->alternatif->kode_alternatif ?? '-',
                        $p->alternatif->nama_alternatif ?? '-',
                        $p->id_pertanyaan,
                        $p->pertanyaan->teks_pertanyaan ?? '-',
                        $p->subkriteria->nama_subkriteria ?? '-',
                        $p->jawaban,
                        $p->created_at?->format('Y-m-d H:i') ?? '-',
                    ]));
                }
            });

        // === SHEET 2: Hasil SMART ===
        $writer->addNewSheetAndMakeItCurrent();
        $sheet2 = $writer->getCurrentSheet();
        $sheet2->setName('Hasil SMART');

        $writer->addRow(Row::fromValues([
            'No', 'NIS', 'Nama Siswa', 'Top 1 Rekomendasi', 'Nilai Top 1 (%)',
            'Top 2 Rekomendasi', 'Nilai Top 2 (%)', 'Top 3 Rekomendasi', 'Nilai Top 3 (%)',
        ], $headerStyle));

        $smartService = app(SmartCalculationService::class);
        $rowNumber = 1;

        User::where('role', 'Siswa')
            ->when($this->kelasFilter, fn($q) => $q->where('nis', 'LIKE', $this->kelasFilter . '%'))
            ->orderBy('nama_user')
            ->chunk(100, function ($siswas) use ($writer, $smartService, &$rowNumber) {
                foreach ($siswas as $siswa) {
                    // Skip siswa tanpa penilaian
                    if (!Penilaian::where('id_user', $siswa->id_user)->exists()) {
                        continue;
                    }

                    $result = $smartService->calculate($siswa->id_user, sortDescending: true);
                    $hasil = $result['hasil'];

                    $top1 = $hasil[0] ?? null;
                    $top2 = $hasil[1] ?? null;
                    $top3 = $hasil[2] ?? null;

                    $writer->addRow(Row::fromValues([
                        $rowNumber++,
                        $siswa->nis ?? '-',
                        $siswa->nama_user,
                        $top1['nama_alternatif'] ?? '-',
                        $top1 ? round($top1['nilai_akhir'] * 100, 2) : 0,
                        $top2['nama_alternatif'] ?? '-',
                        $top2 ? round($top2['nilai_akhir'] * 100, 2) : 0,
                        $top3['nama_alternatif'] ?? '-',
                        $top3 ? round($top3['nilai_akhir'] * 100, 2) : 0,
                    ]));
                }
            });

        $writer->close();

        return $filepath;
    }
}
