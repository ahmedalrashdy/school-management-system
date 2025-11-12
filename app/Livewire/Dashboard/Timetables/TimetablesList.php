<?php

namespace App\Livewire\Dashboard\Timetables;

use App\Models\Section;
use App\Models\Timetable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class TimetablesList extends Component
{
    use AuthorizesRequests, WithPagination;

    #[Url(history: true)]
    public ?int $academicYearId = null;
    #[Url(history: true)]
    public ?int $gradeId = null;

    #[Url(history: true)]
    public ?int $academicTermId = null;

    #[Url(history: true)]
    public ?int $sectionId = null;

    #[Url(history: true)]
    public string $search = '';

    public function mount(): void
    {
        $this->authorize(\Perm::TimetablesView->value);
        $this->academicYearId = $this->academicYearId ?: school()->activeYear()->id;
        $this->academicTermId = $this->academicTermId ?: school()->currentAcademicTerm()?->id;

    }

    public function updatedAcademicYearId(): void
    {
        $this->reset(['academicTermId', 'sectionId']);
        $this->resetPage();
    }

    public function updatedGradeId(): void
    {
        $this->reset(['sectionId']);
        $this->resetPage();
    }

    public function updatedAcademicTermId(): void
    {
        $this->reset(['sectionId']);
        $this->resetPage();
    }

    public function updatedSectionId(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function sections(): Collection
    {
        if (!$this->academicYearId || !$this->gradeId || $this->academicTermId === null) {
            return collect();
        }

        return Section::where('academic_year_id', $this->academicYearId)
            ->where('grade_id', $this->gradeId)
            ->where('academic_term_id', $this->academicTermId)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function timetables()
    {
        $query = Timetable::withCount([
            'slots',
        ])->with([
                    'section.grade.stage',
                    'section.academicYear',
                    'timetableSetting',
                ]);

        if ($this->sectionId) {
            $query->where('section_id', $this->sectionId);
        } elseif ($this->gradeId && $this->academicTermId !== null && $this->academicYearId) {
            $query->whereHas('section', function ($q) {
                $q->where('grade_id', $this->gradeId)
                    ->where('academic_term_id', $this->academicTermId)
                    ->where('academic_year_id', $this->academicYearId);
            });
        } elseif ($this->gradeId && $this->academicYearId) {
            $query->whereHas('section', function ($q) {
                $q->where('grade_id', $this->gradeId)
                    ->where('academic_year_id', $this->academicYearId);
            });
        } elseif ($this->academicYearId) {
            $query->whereHas('section', function ($q) {
                $q->where('academic_year_id', $this->academicYearId);
            });
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereLike('name', "%{$this->search}%")
                    ->orWhereHas('section', function ($sq) {
                        $sq->whereLike('name', "%{$this->search}%");
                    });
            });
        }

        return $query->latest()->paginate(15);
    }

    public function toggleActive($timetableId): void
    {

        $this->authorize(\Perm::TimetablesActivate->value);
        $timetable = Timetable::findOrFail($timetableId);

        if ($timetable->is_active) {
            // Deactivating - no restrictions, multiple inactive timetables are allowed
            $timetable->update(['is_active' => false]);
            $this->dispatch('show-toast', type: 'success', message: 'تم تعطيل الجدول بنجاح.');
        } else {
            try {
                DB::transaction(function () use ($timetable) {
                    //disable others tables belong to  section
                    Timetable::where('section_id', $timetable->section_id)
                        ->where('is_active', true)
                        ->where('id', '!=', $timetable->id)
                        ->update(['is_active' => false]);
                    $timetable->update(['is_active' => true]);
                });
                $this->dispatch('show-toast', type: 'success', message: 'تم تفعيل الجدول وتعطيل الجدول السابق.');
            } catch (\Exception $e) {
                $this->dispatch('show-toast', type: 'error', message: 'حدث خطأ أثناء تبديل الجداول.');
            }
        }
    }

    public function delete($timetableId): void
    {
        $this->authorize(\Perm::TimetablesDelete->value);
        $timetable = Timetable::findOrFail($timetableId);

        if ($timetable->is_active || $timetable->attendanceSheets()->count()) {
            $this->dispatch(
                'show-toast',
                type: 'error',
                message: $timetable->is_active ? 'لا يمكن حذف الجدول النشط. يرجى تعطيله أولاً.' : 'لا يمكن حذف الجدول لأنه مرتبط بسجل حضور '
            );
            return;
        }

        $timetable->delete();
        $this->dispatch('show-toast', type: 'error', message: 'تم حذف الجدول بنجاح.');
    }

    #[Layout('components.layouts.dashboard')]
    public function render()
    {
        return view('livewire.dashboard.timetables.timetables-list');
    }
}
