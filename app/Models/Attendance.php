<?php

namespace App\Models;

use App\Enums\AttendanceStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'attendance_sheet_id',
        'student_id',
        'status',
        'notes',
        'modified_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => AttendanceStatusEnum::class,
        ];
    }

    public function attendanceSheet(): BelongsTo
    {
        return $this->belongsTo(AttendanceSheet::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function modifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modified_by');
    }

    /**
     * Get display information for this attendance record.
     */
    public function getDisplayInfo(): array
    {
        $sheet = $this->attendanceSheet;
        $type = $sheet->getDisplayType();
        $subjectName = $sheet->getSubjectName();

        return [
            'type' => $type,
            'subject_name' => $subjectName,
            'status' => $this->status,
            'status_label' => $this->status->label(),
            'notes' => $this->notes,
            'date' => $sheet->schoolDay->date,
        ];
    }
}
