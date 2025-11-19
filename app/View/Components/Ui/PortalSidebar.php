<?php

namespace App\View\Components\Ui;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PortalSidebar extends Component
{
    public string $portalType;

    public array $menuItems;

    public string $homeRoute;

    /**
     * Create a new component instance.
     */
    public function __construct(?string $portalType = null, ?array $menuItems = null, ?string $homeRoute = null)
    {
        // Auto-detect portal type from authenticated user if not provided
        $this->portalType = $portalType ?? $this->detectPortalType();
        $this->menuItems = $menuItems ?? $this->getMenuItemsForType($this->portalType);
        $this->homeRoute = $homeRoute ?? route("portal.{$this->portalType}.index");
    }

    /**
     * Detect portal type from authenticated user.
     */
    protected function detectPortalType(): string
    {
        $user = auth()->user();

        if ($user?->student) {
            return 'student';
        }

        if ($user?->teacher) {
            return 'teacher';
        }

        if ($user?->guardian) {
            return 'guardian';
        }

        return 'student'; // default fallback
    }

    /**
     * Get menu items for the given portal type.
     */
    protected function getMenuItemsForType(string $type): array
    {
        return match ($type) {
            'student' => $this->getStudentMenuItems(),
            'teacher' => $this->getTeacherMenuItems(),
            'guardian' => $this->getGuardianMenuItems(),
            default => [],
        };
    }

    /**
     * Get student menu items.
     */
    protected function getStudentMenuItems(): array
    {
        return [
            ['header' => 'القائمة الرئيسية'],
            [
                'label' => 'الرئيسية',
                'route' => route('portal.student.index'),
                'icon' => 'fas fa-home',
                'active' => 'portal.student.index',
            ],
            ['separator' => true],
            ['header' => 'الخدمات'],
            [
                'label' => 'الجدول الدراسي',
                'route' => route('portal.student.timetable'),
                'icon' => 'fas fa-calendar-alt',
                'active' => 'portal.student.timetable',
            ],
            [
                'label' => 'الحضور والغياب',
                'route' => route('portal.student.attendance'),
                'icon' => 'fas fa-clipboard-check',
                'active' => 'portal.student.attendance',
            ],
            [
                'label' => 'الدرجات',
                'route' => route('portal.student.marks'),
                'icon' => 'fas fa-star',
                'active' => 'portal.student.marks',
            ],
            ['separator' => true],
            ['header' => 'الملف الشخصي'],
            [
                'label' => 'المعلومات',
                'route' => route('portal.student.profile'),
                'icon' => 'fas fa-user',
                'active' => 'portal.student.profile',
            ],
            [
                'label' => 'أولياء الأمور',
                'route' => route('portal.student.profile.guardians'),
                'icon' => 'fas fa-users',
                'active' => 'portal.student.profile.guardians',
            ],
        ];
    }

    /**
     * Get teacher menu items.
     */
    protected function getTeacherMenuItems(): array
    {
        return [
            ['header' => 'القائمة الرئيسية'],
            [
                'label' => 'الرئيسية',
                'route' => route('portal.teacher.index'),
                'icon' => 'fas fa-home',
                'active' => 'portal.teacher.index',
            ],
            ['separator' => true],
            ['header' => 'الخدمات'],
            [
                'label' => 'الجدول الدراسي',
                'route' => route('portal.teacher.timetable'),
                'icon' => 'fas fa-calendar-alt',
                'active' => 'portal.teacher.timetable',
            ],
            [
                'label' => 'الحضور والغياب',
                'route' => route('portal.teacher.attendance'),
                'icon' => 'fas fa-clipboard-check',
                'active' => 'portal.teacher.attendance',
            ],
            [
                'label' => 'الدرجات',
                'route' => route('portal.teacher.marks'),
                'icon' => 'fas fa-star',
                'active' => 'portal.teacher.marks',
            ],
            ['separator' => true],
            ['header' => 'الملف الشخصي'],
            [
                'label' => 'المعلومات',
                'route' => route('portal.teacher.profile.index'),
                'icon' => 'fas fa-user',
                'active' => 'portal.teacher.profile.*',
            ],
        ];
    }

    /**
     * Get guardian menu items.
     */
    protected function getGuardianMenuItems(): array
    {
        return [
            ['header' => 'القائمة الرئيسية'],
            [
                'label' => 'الرئيسية',
                'route' => route('portal.guardian.index'),
                'icon' => 'fas fa-home',
                'active' => 'portal.guardian.index',
            ],
            ['separator' => true],
            ['header' => 'الخدمات'],
            [
                'label' => 'الأبناء',
                'route' => route('portal.guardian.select-student'),
                'icon' => 'fas fa-users',
                'active' => 'portal.guardian.select-student',
            ],
            ['separator' => true],
            ['header' => 'الملف الشخصي'],
            [
                'label' => 'المعلومات',
                'route' => route('portal.guardian.profile.index'),
                'icon' => 'fas fa-user',
                'active' => 'portal.guardian.profile.*',
            ],
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.portal-sidebar');
    }
}
