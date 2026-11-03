<?php

namespace App\Concerns;

use App\Support\Audit\AuditLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * Pasang trait ini pada model yang perubahannya harus masuk audit log.
 *
 *   class Kategori extends Model
 *   {
 *       use DicatatAuditLog;
 *   }
 *
 * Catatan: update massal lewat query builder
 * (Model::where(...)->update([...])) tidak memicu event model,
 * jadi tidak tercatat. Gunakan $model->update([...]) atau catat manual
 * dengan AuditLogger::catat(...).
 */
trait DicatatAuditLog
{
    protected static function bootDicatatAuditLog(): void
    {
        static::created(function (Model $model): void {
            AuditLogger::catat('CREATE', $model, null, $model->getAttributes());
        });

        static::updated(function (Model $model): void {
            $perubahan = $model->getChanges();

            if ($perubahan === []) {
                return;
            }

            AuditLogger::catat(
                'UPDATE',
                $model,
                Arr::only($model->getOriginal(), array_keys($perubahan)),
                $perubahan,
            );
        });

        static::deleted(function (Model $model): void {
            AuditLogger::catat('DELETE', $model, $model->getAttributes(), null);
        });
    }
}