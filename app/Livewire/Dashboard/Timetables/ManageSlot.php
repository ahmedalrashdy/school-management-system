<?php

namespace App\Livewire\Dashboard\Timetables;

use App\Models\TeacherAssignment;
use App\Models\Timetable;
use App\Models\TimetableSlot;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ManageSlot extends Component
{
    use AuthorizesRequests;

    #[Locked]
    public int $timetableId;

    public $day;
    public $period;
    public $slotId = null;

    #[Validate('required|exists:teacher_assignments,id')]
    public $teacherAssignmentId = null;

    #[Validate('required|integer|min:15|max:180')]
    public $durationMinutes = null;

    public $hasConflict = false;
    public $conflictMessage = '';

    #[On('edit-slot')]
    public function loadData(int $day, int $period, ?int $slotId = null): void
    {
        $this->resetValidation();
        $this->reset(['teacherAssignmentId', 'hasConflict', 'conflictMessage']);

        $this->day = $day;
        $this->period = $period;
        $this->slotId = $slotId;
        $this->durationMinutes = $this->timetable->timetableSetting->default_period_duration_minutes;

        if ($this->slotId) {
            $slot = TimetableSlot::find($this->slotId);
            if ($slot) {
                $this->teacherAssignmentId = $slot->teacher_assignment_id;
                $this->durationMinutes = $slot->duration_minutes;
                $this->checkForConflicts();
            }
        }
    }

    public function updatedTeacherAssignmentId(): void
    {
        $this->checkForConflicts();
    }

    public function checkForConflicts(): void
    {
        if (!$this->teacherAssignmentId) {
            $this->reset(['hasConflict', 'conflictMessage']);
            return;
        }

        $assignment = TeacherAssignment::with('teacher.user')->find($this->teacherAssignmentId);
        if (!$assignment)
            return;

        $timetable = Timetable::with('section')->find($this->timetableId);

        $conflictingSlots = TimetableSlot::where('teacher_assignment_id', '!=', $this->teacherAssignmentId)
            ->where('day_of_week', $this->day)
            ->where('period_number', $this->period)
            ->whereHas('teacherAssignment', function ($query) use ($assignment) {
                $query->where('teacher_id', $assignment->teacher_id);
            })
            ->whereHas('timetable', function ($query) use ($timetable) {
                $query->where('is_active', true)
                    ->where('id', '!=', $this->timetableId) // استثناء الجدول الحالي
                    ->whereHas('section', function ($q) use ($timetable) {
                        $q->where('academic_year_id', $timetable->section->academic_year_id)
                            ->where('academic_term_id', $timetable->section->academic_term_id);
                    });
            })
            ->exists();

        if ($conflictingSlots) {
            $this->hasConflict = true;
            $this->conflictMessage = "⚠️ تحذير: المدرس {$assignment->teacher->user->first_name} لديه حصة أخرى في نفس الوقت.";
        } else {
            $this->hasConflict = false;
        }
    }
    #[Computed]
    public function timetable()
    {
        return Timetable::with('timetableSetting')->findOrFail($this->timetableId);
    }

    #[Computed]
    public function availableAssignments()
    {
        return TeacherAssignment::with(['curriculumSubject.subject', 'teacher.user'])
            ->where('section_id', $this->timetable->section_id)
            ->get();
    }

    public function save(): void
    {
        $this->authorize(\Perm::TimetablesUpdate->value);
        $this->validate();



        if ($this->timetable->section->academic_term_id !== school()->currentAcademicTerm()?->id) {
            $this->dispatch('show-toast', type: 'error', message: 'لا يمكن تعديل جداول  دراسي خارج الفصل الحالي.');
            $this->dispatch('close-modal', name: 'manage-slot-modal');
            return;
        }

        $data = [
            'timetable_id' => $this->timetableId,
            'teacher_assignment_id' => $this->teacherAssignmentId,
            'day_of_week' => $this->day,
            'period_number' => $this->period,
            'duration_minutes' => $this->durationMinutes,
        ];

        if ($this->slotId) {
            TimetableSlot::where('id', $this->slotId)->update($data);
            $message = 'تم تحديث الحصة بنجاح.';
        } else {
            TimetableSlot::create($data);
            $message = 'تم إضافة الحصة بنجاح.';
        }

        $this->dispatch('show-toast', type: 'success', message: $message);
        $this->dispatch('timetable-updated');
        $this->dispatch('close-modal', name: 'manage-slot-modal');
    }

    public function render()
    {
        return view('livewire.dashboard.timetables.manage-slot');
    }
}
