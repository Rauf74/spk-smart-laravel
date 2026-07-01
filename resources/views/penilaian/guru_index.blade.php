@extends('layouts.app')

@section('title', 'Penilaian Siswa - Guru BK')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="mb-1">Penilaian Siswa</h1>
            <p class="fs-6 mb-0">Menampilkan rekapitulasi jawaban kuesioner dari setiap siswa, lengkap dengan ringkasan nilai per kriteria.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('penilaian.rekap-kelas') }}" class="btn btn-outline-primary">
                <i class="ti ti-chart-bar me-1"></i>Rekap Kelas
            </a>
            <a href="{{ route('penilaian.export') }}" class="btn btn-success">
                <i class="ti ti-file-spreadsheet me-1"></i>Export ke Excel
            </a>
        </div>
    </div>

    {{-- Dropdown Pilih Siswa & Filter Kelas --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('penilaian.index') }}" method="GET" class="row align-items-end g-2">
                <div class="col-md-4">
                    <label for="id_user" class="form-label fw-bold mb-1">Pilih Siswa</label>
                    <select name="id_user" id="id_user" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id_user }}" {{ $targetUserId == $user->id_user ? 'selected' : '' }}>
                                {{ $user->nama_user }} ({{ $user->nis }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="kelas" class="form-label fw-bold mb-1">Filter Kelas</label>
                    <select name="kelas" id="kelas" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($kelasList ?? [] as $kls)
                            <option value="{{ $kls }}" {{ ($kelasFilter ?? '') === $kls ? 'selected' : '' }}>
                                Kelas {{ $kls }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ti ti-filter me-1"></i>Filter
                    </button>
                </div>
                @if(($kelasFilter ?? '') !== '' || $targetUserId)
                    <div class="col-md-2">
                        <a href="{{ route('penilaian.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="ti ti-x me-1"></i>Reset
                        </a>
                    </div>
                @endif
                <div class="col-md-1 text-end">
                    <a href="{{ route('penilaian.export', ['kelas' => $kelasFilter ?? '']) }}" class="btn btn-success w-100" title="Export ke Excel">
                        <i class="ti ti-file-spreadsheet"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if($targetUserId && count($alternatifs) > 0)
        <!-- Accordion Container -->
        <div class="accordion" id="accordionPenilaian">
            @foreach($alternatifs as $index => $alternatif)
                @php
                    // Cek apakah ada penilaian untuk alternatif ini
                    $penilaianAlt = $existingPenilaians->where('id_alternatif', $alternatif->id_alternatif);
                    $hasData = $penilaianAlt->count() > 0;
                    $headerClass = $hasData ? 'bg-light-success' : 'bg-light';
                @endphp

                <div class="accordion-item mb-3 border shadow-sm rounded overflow-hidden">
                    <h2 class="accordion-header" id="heading{{ $alternatif->id_alternatif }}">
                        <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }} {{ $headerClass }}" type="button" 
                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $alternatif->id_alternatif }}" 
                                aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" 
                                aria-controls="collapse{{ $alternatif->id_alternatif }}">
                            <div class="d-flex w-100 justify-content-between align-items-center me-3">
                                <span class="fw-semibold">Penilaian untuk {{ $alternatif->nama_alternatif }}</span>
                                @if($hasData)
                                    <span class="badge bg-success">Sudah Dinilai</span>
                                @else
                                    <span class="badge bg-secondary">Belum Dinilai</span>
                                @endif
                            </div>
                        </button>
                    </h2>
                    
                    <div id="collapse{{ $alternatif->id_alternatif }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" 
                         aria-labelledby="heading{{ $alternatif->id_alternatif }}" data-bs-parent="#accordionPenilaian">
                        <div class="accordion-body p-4">
                            <form action="{{ route('penilaian.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id_user" value="{{ $targetUserId }}">
                                {{-- Kita tidak perlu id_alternatif di root, tapi di setiap pertanyaan --}}

                                <div class="list-group list-group-flush mb-4">
                                    @foreach($alternatif->pertanyaan as $idx => $pertanyaan)
                                        <div class="list-group-item py-4 border-bottom">
                                            <h6 class="mb-3">
                                                <span class="badge bg-primary rounded-circle p-2 me-2" style="width: 28px; height: 28px; text-align: center;">{{ $idx + 1 }}</span>
                                                <span class="fw-bold">({{ $pertanyaan->kriteria->nama_kriteria }})</span> 
                                                {{ $pertanyaan->teks_pertanyaan }}
                                            </h6>
                                            <div class="d-flex flex-wrap gap-3">
                                                @foreach($pertanyaan->kriteria->subkriteria as $sub)
                                                    @php
                                                        $selected = $penilaianAlt->where('id_pertanyaan', $pertanyaan->id_pertanyaan)->first();
                                                        $isChecked = $selected && $selected->id_subkriteria == $sub->id_subkriteria;
                                                    @endphp
                                                    <div class="form-check me-3">
                                                        <input class="form-check-input" type="radio" 
                                                               name="jawaban[{{ $pertanyaan->id_pertanyaan }}]" 
                                                               value="{{ $sub->id_subkriteria }}"
                                                               id="q{{ $pertanyaan->id_pertanyaan }}_sub{{ $sub->id_subkriteria }}"
                                                               {{ $isChecked ? 'checked' : '' }} required>
                                                        <label class="form-check-label" for="q{{ $pertanyaan->id_pertanyaan }}_sub{{ $sub->id_subkriteria }}">
                                                            {{ $sub->nama_subkriteria }} <span class="text-muted small">({{ $sub->nilai }})</span>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                </div>
                            </form>

                            {{-- Hapus Button (SweetAlert Confirmation) --}}
                            @if($hasData)
                                <div class="mt-3 text-end border-top pt-3">
                                    <button type="button" class="btn btn-danger"
                                        onclick="confirmDelete(
                                            'Hapus Penilaian?',
                                            '<div class=\'text-start\'>Semua penilaian untuk siswa ini pada program studi <strong>{{ $alternatif->nama_alternatif }}</strong> akan dihapus permanen.<br><br><small class=\'text-danger\'>⚠️ Tidak dapat dibatalkan.</small></div>',
                                            '{{ route('penilaian.destroyPerAlternatif') }}',
                                            [
                                                {name: 'id_user', value: '{{ $targetUserId }}'},
                                                {name: 'id_alternatif', value: '{{ $alternatif->id_alternatif }}'}
                                            ]
                                        )">
                                        <i class='ti ti-trash me-1'></i>Hapus Penilaian
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @elseif($targetUserId)
         <div class="alert alert-warning">
             <i class="ti ti-alert-circle me-2"></i> User ini tidak memiliki akses ke alternatif (cek data master).
         </div>
    @else
        <div class="alert alert-info">
            <i class="ti ti-info-circle me-2"></i> Silakan pilih siswa terlebih dahulu untuk melihat data.
        </div>
    @endif
    
</div>
@endsection
