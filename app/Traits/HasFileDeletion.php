<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

trait HasFileDeletion
{
    /**
     * Boot the trait to listen to events.
     */
    protected static function bootHasFileDeletion(): void
    {
        static::updated(function (Model $model) {
            $model->processFileDeletionOnUpdate();
        });

        static::deleted(function (Model $model) {
            $model->processFileDeletionOnDelete();
        });
    }

    /**
     * يجب على الموديل تحديد الحقول والقرص الخاص بها.
     * الصيغة: ['field_name' => 'disk_name'] أو ['field_name'] (للقرص الافتراضي)
     */
    abstract public function deletableFiles(): array;

    protected function processFileDeletionOnUpdate(): void
    {
        foreach ($this->getNormalizedDeletableFiles() as $field => $disk) {
            if ($this->isDirty($field)) {
                $oldFile = $this->getOriginal($field);

                if ($oldFile) {
                    $this->scheduleFileDeletion($disk, $oldFile);
                }
            }
        }
    }

    protected function processFileDeletionOnDelete(): void
    {
        // skip  when softDelete
        if (method_exists($this, 'isForceDeleting') && ! $this->isForceDeleting()) {
            return;
        }

        foreach ($this->getNormalizedDeletableFiles() as $field => $disk) {

            $oldFile = $this->getOriginal($field);
            if ($oldFile) {
                $this->scheduleFileDeletion($disk, $oldFile);
            }
        }
    }

    protected function scheduleFileDeletion(string $disk, string $path): void
    {
        // نستخدم afterCommit لضمان عدم حذف الملف إلا بعد نجاح الـ Transaction
        DB::afterCommit(function () use ($disk, $path) {
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        });
    }

    private function getNormalizedDeletableFiles(): array
    {
        $files = $this->deletableFiles();
        $normalized = [];

        foreach ($files as $key => $value) {
            if (is_numeric($key)) {
                $normalized[$value] = config('filesystems.default');
            } else {
                $normalized[$key] = $value;
            }
        }

        return $normalized;
    }
}
