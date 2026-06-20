<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanKonseling extends Model
{
    use HasFactory;

    protected $table = 'catatan_konseling';
    protected $primaryKey = 'id_catatan';
    public $timestamps = true;

    protected $fillable = [
        'id_user',
        'id_guru',
        'catatan',
    ];

    public function siswa()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'id_guru', 'id_user');
    }
}
