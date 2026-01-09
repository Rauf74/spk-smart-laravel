<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    /** @use HasFactory<\Database\Factories\KriteriaFactory> */
    use HasFactory;

    protected $table = 'kriteria';
    protected $primaryKey = 'id_kriteria';

    protected $fillable = [
        'kode_kriteria',
        'nama_kriteria',
        'jenis', // Benefit, Cost
        'bobot',
    ];

    public function subkriteria()
    {
        return $this->hasMany(Subkriteria::class, 'id_kriteria');
    }
}
