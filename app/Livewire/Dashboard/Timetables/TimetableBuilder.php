<?php

namespace App\Livewire\Dashboard\Timetables;

use App\Models\Timetable;
use App\Models\TimetableSlot;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class TimetableBuilder extends Component
{
    use AuthorizesRequests;

    #[Locked]
    public int $timetableId;

    public function mount(int $timetable): void
    {
        $this->timetableId = $timetable;
        $this->authorize(\Perm::TimetablesUpdate->value);
    }

    #[Computed]
    public function timetable()
    {
        return Timetable::with([
            'timetableSetting',
            'section.grade',
            'slots' => function ($query) {
                $query->with([
                    'teacherAssignment.curriculumSubject.subject',
                    'teacherAssignment.teacher.user',
                ]);
            },
        ])->findOrFail($this->timetableId);
    }

    #[On('timetable-updated')]
    public function refreshTimetable(): void
    {
    }

    public function deleteSlot($slotId): void
    {
        $this->authorize(\Perm::TimetablesUpdate->value);

        $slot = TimetableSlot::findOrFail($slotId);

        if ($slot->timetable_id !== $this->timetableId) {
            $this->dispatch('show-toast', type: 'error', message: 'غير مصرح بحذف هذه الحصة.');
            return;
        }

        $slot->delete();
        $this->dispatch('show-toast', type: 'success', message: 'تم حذف الحصة بنجاح.');
    }

    #[Layout('components.layouts.dashboard')]
    public function render()
    {
        return view('livewire.dashboard.timetables.timetable-builder');
    }
}
