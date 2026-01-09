<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subkriteria extends Model
{
    /** @use HasFactory<\Database\Factories\SubkriteriaFactory> */
    use HasFactory;

    protected $table = 'subkriteria';
    protected $primaryKey = 'id_subkriteria';

    protected $fillable = [
        'id_kriteria',
        'nama_subkriteria',
        'nilai',
    ];

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'id_kriteria');
    }
}
