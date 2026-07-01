<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditObserver
{
    /**
     * Model yang ingin di-audit.
     * Tambahkan di sini jika ada model baru.
     */
    public static function auditableModels(): array
    {
        return [
            \App\Models\User::class,
            \App\Models\Kriteria::class,
            \App\Models\Subkriteria::class,
            \App\Models\Alternatif::class,
            \App\Models\Pertanyaan::class,
        ];
    }

    public function created(Model $model): void
    {
        AuditLog::record(
            'create',
            get_class($model),
            $model->getKey(),
            $this->describe($model, 'dibuat'),
            null,
            $this->sanitize($model->getAttributes())
        );
    }

    public function updated(Model $model): void
    {
        $changes = $model->getChanges();
        if (empty($changes)) {
            return; // no actual change
        }

        // Pisahkan old vs new
        $oldValues = [];
        $newValues = [];
        foreach ($changes as $key => $newValue) {
            $oldValues[$key] = $model->getOriginal($key);
            $newValues[$key] = $newValue;
        }

        AuditLog::record(
            'update',
            get_class($model),
            $model->getKey(),
            $this->describe($model, 'diubah'),
            $this->sanitize($oldValues),
            $this->sanitize($newValues)
        );
    }

    public function deleted(Model $model): void
    {
        AuditLog::record(
            'delete',
            get_class($model),
            $model->getKey(),
            $this->describe($model, 'dihapus'),
            $this->sanitize($model->getAttributes()),
            null
        );
    }

    /**
     * Sanitasi: hapus field sensitif (password) dari log.
     */
    private function sanitize(array $values): array
    {
        unset($values['password'], $values['remember_token']);
        return $values;
    }

    /**
     * Generate deskripsi: "Ubah kriteria: C1 - Minat"
     */
    private function describe(Model $model, string $verb): string
    {
        $name = $model->nama_kriteria
            ?? $model->nama_user
            ?? $model->nama_subkriteria
            ?? $model->nama_alternatif
            ?? $model->teks_pertanyaan
            ?? "ID {$model->getKey()}";

        // Ambil nama model sederhana: 'Kriteria', 'User', dll
        $className = class_basename($model);
        return "{$className} '{$name}' {$verb}";
    }
}
