<?php

namespace App\Livewire\Dashboard\Users\Students;

use App\Models\Student;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class StudentsIndex extends Component
{
    use AuthorizesRequests, WithPagination;

    #[Url(history: true)]
    public ?int $academic_year_id = null;

    #[Url(history: true)]
    public ?int $grade_id = null;

    #[Url(history: true)]
    public ?int $academic_term_id = null;

    #[Url(history: true, except: '')]
    public ?string $status = '';

    #[Url(history: true, except: '')]
    public ?string $search = '';

    public function mount(): void
    {
        $this->authorize(\Perm::StudentsView->value);
        $this->academic_year_id = request()->query('academic_year_id', school()->activeYear()?->id);
    }

    public function updated($property)
    {
        if (in_array($property, ['search', 'status', 'academic_term_id', 'grade_id', 'academic_year_id'])) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->academic_year_id = school()->activeYear()?->id;
        $this->reset(['search', 'status', 'grade_id', 'academic_term_id']);
        $this->resetPage();
    }

    public function hasActiveFilters(): bool
    {
        return $this->academic_year_id != school()->activeYear()?->id
            || $this->grade_id !== null
            || $this->academic_term_id !== null
            || ($this->status !== null && $this->status !== '')
            || ($this->search !== null && $this->search !== '');
    }

    #[Computed]
    public function students()
    {
        return Student::query()
            ->distinct()
            ->with([
                'user',
                'grades' => function ($q) {
                    $q->wherePivot('academic_year_id', $this->academic_year_id);
                },
            ])
            ->when($this->academic_year_id !== null, function ($q) {
                $q->whereHas('enrollments', function ($enrollmentQ) {
                    $enrollmentQ->where('academic_year_id', $this->academic_year_id);
                });
            })
            ->when($this->grade_id !== null, function ($q) {
                $q->whereHas('enrollments', function ($enrollmentQ) {
                    $enrollmentQ->where('grade_id', $this->grade_id);
                });
            })
            ->when($this->academic_term_id !== null, function ($q) {
                $q->whereHas('sections', function ($sectionQ) {
                    $sectionQ->where('academic_term_id', $this->academic_term_id);
                });
            })
            ->when($this->status, function ($q) {
                $isActive = $this->status === 'active';
                $q->whereHas('user', function ($userQ) use ($isActive) {
                    $userQ->where('is_active', $isActive);
                });
            })
            ->when($this->search, function ($q) {
                $q->where(function ($subQ) {
                    $subQ->whereHas('user', function ($userQ) {
                        $userQ->whereAny(
                            ['first_name', 'last_name', 'phone_number', 'email'],
                            'like',
                            "%{$this->search}%"
                        );
                    })
                        ->orWhere('admission_number', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->paginate(20);
    }

    public function render()
    {
        return view('livewire.dashboard.users.students.students-index');
    }
}
