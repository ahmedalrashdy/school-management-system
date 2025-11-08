<?php

namespace App\Livewire\Dashboard\Users;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class UsersIndex extends Component
{
    use AuthorizesRequests, WithPagination;

    #[Url(history: true, except: '')]
    public ?string $userType = null;

    #[Url(history: true, except: '')]
    public ?string $status = null;

    #[Url(history: true, except: '')]
    public ?string $search = '';

    public function mount(): void
    {
        $this->authorize(\Perm::UsersView->value);
    }

    public function resetFilters(): void
    {
        $this->reset(['userType', 'status', 'search']);
        $this->resetPage();
    }

    public function getHasActiveFiltersProperty(): bool
    {
        return $this->userType !== null
            || $this->status !== null
            || $this->search !== '';
    }

    public function toggleActive(int $userId): void
    {
        $this->authorize(\Perm::UsersUpdate->value);

        $user = User::findOrFail(id: $userId);
        $user->update(['is_active' => !$user->is_active]);
        $this->dispatch('show-toast', type: 'success', message: 'تم تحديث حالة المستخدم بنجاح.');
        $this->dispatch('close-modal', name: 'toggle-user-active');
    }

    public function deleteUser(int $userId): void
    {
        $this->authorize(\Perm::UsersDelete->value);

        $user = User::findOrFail($userId);

        // Check if user is linked to critical entity
        if ($user->isLinkedToCriticalEntity() || $user->is_admin) {
            $this->dispatch('show-toast', type: 'error', message: 'لا يمكن حذف هذا المستخدم لأنه مرتبط بكيان حيوي (مدير النظام,طالب، مدرس، أو ولي أمر). يرجى تعطيل الحساب بدلاً من ذلك.');

            return;
        }

        $user->delete();

        $this->dispatch('show-toast', type: 'success', message: 'تم حذف المستخدم بنجاح.');
    }

    #[Layout('components.layouts.dashboard', ['page-title' => 'إدارة المستخدمين'])]
    public function render()
    {
        $query = User::with(['student', 'teacher', 'guardian', 'roles']);

        // Filter by user type
        if ($this->userType) {
            match ($this->userType) {
                'admin' => $query->where('is_admin', true),
                'طالب' => $query->whereHas('student'),
                'مدرس' => $query->whereHas('teacher'),
                'ولي أمر' => $query->whereHas('guardian'),
                default => null,
            };
        }

        // Filter by status
        if ($this->status) {
            $isActive = $this->status === 'active';
            $query->where('is_active', $isActive);
        }

        // Search
        if ($this->search) {
            $search = $this->search;
            $query->whereAny(
                ['first_name', 'last_name', 'email', 'phone_number'],
                'like',
                "%{$search}%"
            );
        }

        $users = $query->latest()->paginate(20);

        return view('livewire.dashboard.users.users-index', [
            'users' => $users,
            'hasActiveFilters' => $this->hasActiveFilters,
        ]);
    }
}
