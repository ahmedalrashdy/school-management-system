<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class WithinRelationDateRange implements DataAwareRule, ValidationRule
{
    protected array $data = [];

    public function __construct(
        protected string $foreignKeyField,
        protected string $modelClass,
        protected string $startColumn = 'start_date',
        protected string $endColumn = 'end_date',
        protected ?string $customMessage = null
    ) {}

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $relatedId = $this->data[$this->foreignKeyField] ?? null;

        if (! $relatedId) {
            return;
        }

        $record = $this->modelClass::find($relatedId);

        if (! $record) {
            return;
        }

        $inputDate = Carbon::parse($value);
        $startDate = Carbon::parse($record->{$this->startColumn});
        $endDate = Carbon::parse($record->{$this->endColumn});

        if ($inputDate->lt($startDate) || $inputDate->gt($endDate)) {

            $startStr = $startDate->format('Y-m-d');
            $endStr = $endDate->format('Y-m-d');

            if ($this->customMessage) {
                $message = str_replace(
                    [':start', ':end'],
                    [$startStr, $endStr],
                    $this->customMessage
                );

                $fail($message);
            } else {
                $fail('تاريخ :attribute يجب أن يكون ضمن النطاق المسموح (:start إلى :end).')
                    ->translate(['start' => $startStr, 'end' => $endStr]);
            }
        }
    }
}
