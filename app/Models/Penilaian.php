<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    /** @use HasFactory<\Database\Factories\PenilaianFactory> */
    use HasFactory;

    protected $table = 'penilaian';
    protected $primaryKey = 'id_penilaian';

    protected $fillable = [
        'id_user',
        'id_alternatif',
        'id_kriteria',
        'id_pertanyaan',
        'id_subkriteria',
        'jawaban',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function alternatif()
    {
        return $this->belongsTo(Alternatif::class, 'id_alternatif');
    }

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'id_kriteria');
    }

    public function pertanyaan()
    {
        return $this->belongsTo(Pertanyaan::class, 'id_pertanyaan');
    }

    public function subkriteria()
    {
        return $this->belongsTo(Subkriteria::class, 'id_subkriteria');
    }
}
