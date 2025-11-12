<?php

namespace App\Livewire\Dashboard\Timetables;

use App\Models\Grade;
use App\Models\Section;
use App\Models\Timetable;
use App\Models\TimetableSetting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CreateTimetable extends Component
{
    use AuthorizesRequests;
    public $gradeId = null;

    #[Validate('required|exists:sections,id')]
    public $sectionId = null;

    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|exists:timetable_settings,id')]
    public $timetableSettingId = null;

    #[Validate('boolean')]
    public $isActive = false;

    public function mount()
    {
        $this->authorize(\Perm::TimetablesCreate);
        $this->gradeId = Grade::sorted()->first()?->id;
    }

    public function updatedGradeId(): void
    {
        $this->reset(['sectionId', 'name']);
    }

    public function updatedSectionId(): void
    {
        // Auto-generate name when section is selected
        if ($this->sectionId) {
            $section = Section::with(['grade.stage', 'academicYear'])->find($this->sectionId);
            if ($section) {
                $this->name = "جدول {$section->grade->name} - شعبة {$section->name} - {$section->academicYear->name}";
            }
        } else {
            $this->name = '';
        }
    }

    #[Computed]
    public function sections()
    {
        if (!$this->gradeId) {
            return collect();
        }

        return Section::where('academic_year_id', school()->activeYear()?->id)
            ->where('grade_id', $this->gradeId)
            ->where('academic_term_id', school()->currentAcademicTerm()?->id)
            ->orderBy('name')
            ->get();
    }

    public function save(): void
    {
        $this->authorize(\Perm::TimetablesCreate);
        $this->validate();


        // Only check if trying to create an active timetable
        if ($this->isActive) {
            $existingActive = Timetable::where('section_id', $this->sectionId)
                ->where('is_active', true)
                ->exists();

            if ($existingActive) {
                $this->addError('isActive', 'يوجد بالفعل جدول نشط لهذه الشعبة. يجب تعطيل الجدول الحالي أولاً.');

                return;
            }
        }

        Timetable::create([
            'name' => $this->name,
            'section_id' => $this->sectionId,
            'timetable_setting_id' => $this->timetableSettingId,
            'is_active' => $this->isActive,
        ]);

        session()->flash('success', 'تم إنشاء الجدول بنجاح.');
        $this->redirect(route('dashboard.timetables.list'), navigate: true);
    }

    #[Computed]
    public function timetableSettings()
    {
        return TimetableSetting::orderBy('is_active', 'desc')
            ->orderBy('name')
            ->pluck('name', 'id')->toArray();
    }

    #[Layout('components.layouts.dashboard')]
    public function render()
    {

        return view('livewire.dashboard.timetables.create-timetable');
    }
}
