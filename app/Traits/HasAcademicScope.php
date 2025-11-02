<?php

namespace App\Traits;

use Illuminate\Validation\ValidationException;

trait HasAcademicScope
{
    protected static function bootHasAcademicScope()
    {
        static::creating(function ($model) {
            if (empty($model->academic_year_id)) {
                $activeYear = school()->activeYear();
                if ($activeYear == null) {
                    request()->session()->flash('error', 'لا يمكن إتمام العملية: لا توجد سنة دراسية نشطة حالياً في النظام.');
                    throw ValidationException::withMessages([
                        'academic_year_id' => 'لا يمكن إتمام العملية: لا توجد سنة دراسية نشطة حالياً في النظام.',
                    ]);
                }
                $model->academic_year_id = $activeYear->id;
            }
            if (empty($model->academic_term_id)) {
                $currentTerm = school()->currentAcademicTerm();
                if ($currentTerm == null) {
                    request()->session()->flash('error', 'لا يمكن إتمام العملية: لا يوجد فصل دراسي نشط حالياً.');
                    throw ValidationException::withMessages([
                        'academic_term_id' => 'لا يمكن إتمام العملية: لا يوجد فصل دراسي نشط حالياً.',
                    ]);
                }
                $model->academic_term_id = $currentTerm->id;
            }
        });
    }
}
