<?php

namespace App\Http\Requests\Dashboard\Academics\AcademicTerms;

use App\Models\AcademicTerm;
use App\Models\AcademicYear;
use App\Rules\WithinRelationDateRange;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAcademicTermRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'name' => ['required', 'string', 'max:255'],
            'start_date' => [
                'required',
                'date',
                new WithinRelationDateRange(
                    'academic_year_id',
                    AcademicYear::class,
                    'start_date',
                    'end_date',
                    'تاريخ البداية يجب أن يكون ضمن نطاق تواريخ السنة الدراسية المختارة.'
                ),

            ],
            'end_date' => [
                'required',
                'date',
                'after:start_date',
                new WithinRelationDateRange(
                    'academic_year_id',
                    AcademicYear::class,
                    'start_date',
                    'end_date',
                    'تاريخ النهاية يجب أن يكون ضمن نطاق تواريخ السنة الدراسية المختارة.',
                ),
            ],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $academicTerm = $this->route('academic_term');
            $academicYearId = $this->input('academic_year_id');
            $startDate = $this->input('start_date');
            $endDate = $this->input('end_date');

            if (! $academicTerm || ! $academicYearId || ! $startDate || ! $endDate) {
                return;
            }

            // Check for date overlap with other terms in the same academic year (excluding current term)
            $overlappingTerm = AcademicTerm::where('academic_year_id', $academicYearId)
                ->where('id', '!=', $academicTerm->id)
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($q) use ($startDate, $endDate) {
                            $q->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                        });
                })
                ->first();

            if ($overlappingTerm) {
                $validator->errors()->add(
                    'start_date',
                    'التواريخ المحددة تتداخل مع ترم آخر في نفس السنة الدراسية.'
                );
                $validator->errors()->add(
                    'end_date',
                    'التواريخ المحددة تتداخل مع ترم آخر في نفس السنة الدراسية.'
                );
            }
        });
    }
}
