<?php

namespace App\Livewire\Dashboard\Users\Teachers;

use App\Models\Teacher;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class TeachersIndex extends Component
{
    use AuthorizesRequests, WithPagination;

    #[Url(history: true, except: '')]
    public ?string $specialization = null;

    #[Url(history: true, except: '')]
    public ?string $status = null;

    #[Url(history: true, except: '')]
    public ?string $search = null;

    public function mount(): void
    {
        $this->authorize(\Perm::TeachersView->value);
    }

    public function updated($property)
    {
        if (in_array($property, ['specialization', 'search', 'status'])) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->specialization = null;
        $this->status = null;
        $this->search = null;
        $this->resetPage();
    }

    #[Computed(persist: true)]
    public function specializations()
    {
        return Teacher::distinct()
            ->whereNotNull('specialization')
            ->pluck('specialization')
            ->sort()
            ->values();
    }

    public function hasActiveFilters(): bool
    {
        return $this->specialization !== null
            || $this->status !== null
            || $this->search !== null;
    }

    #[Computed()]
    public function teachers()
    {
        $query = Teacher::with('user');
        if ($this->specialization) {
            $query->where('specialization', $this->specialization);
        }

        if ($this->status) {
            $isActive = $this->status === 'active';
            $query->whereHas('user', function ($q) use ($isActive) {
                $q->where('is_active', $isActive);
            });
        }
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('user', function ($userQuery) {
                    $userQuery->whereAny(
                        ['first_name', 'last_name', 'phone_number', 'email'],
                        'like',
                        "%{$this->search}%"
                    );
                });
            });
        }

        return $query->latest('teachers.created_at')->paginate(20);
    }
}
