<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    protected $table = 'audit_logs';
    public $timestamps = false;

    protected $fillable = [
        'action',
        'model_type',
        'model_id',
        'id_user',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Helper statis: catat perubahan.
     */
    public static function record(
        string $action,
        string $modelType,
        ?int $modelId,
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): self {
        return self::create([
            'action'      => $action,
            'model_type'  => $modelType,
            'model_id'    => $modelId,
            'id_user'     => Auth::id(),
            'description' => $description,
            'old_values'  => $oldValues,
            'new_values'  => $newValues,
            'ip_address'  => request()?->ip(),
            'created_at'  => now(),
        ]);
    }
}
