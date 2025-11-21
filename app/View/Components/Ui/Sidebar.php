<?php

namespace App\View\Components\Ui;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;
use Illuminate\View\View;

class Sidebar extends Component
{
    public array $menuItems;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {

        $this->menuItems = $this->filterEmptySections($this->buildMenuItems());
    }

    /**
     * Build the menu items array with permissions.
     */
    protected function buildMenuItems(): array
    {
        $user = Auth::user();

        return [
            ['header' => 'القائمة الرئيسية'],
            [
                'label' => 'لوحة التحكم',
                'route' => route('dashboard.index'),
                'icon' => 'fas fa-home',
                'active' => 'dashboard.index',
                'has_permission' => $user?->can(\Perm::DashboardAccess->value),
            ],

            ['separator' => true],
            ['header' => 'إدارة المستخدمين'],

            [
                'label' => 'الطلاب',
                'route' => route('dashboard.students.index'),
                'icon' => 'fas fa-user-graduate',
                'active' => 'dashboard.students.*',
                'has_permission' => $user?->can(\Perm::StudentsView->value),
            ],
            [
                'label' => 'المدرسين',
                'route' => route('dashboard.teachers.index'),
                'icon' => 'fas fa-chalkboard-teacher',
                'active' => 'dashboard.teachers.*',
                'has_permission' => $user?->can(\Perm::TeachersView->value),
            ],
            [
                'label' => 'أولياء الأمور',
                'route' => route('dashboard.guardians.index'),
                'icon' => 'fas fa-users',
                'active' => 'dashboard.guardians.*',
                'has_permission' => $user?->can(\Perm::GuardiansView->value),
            ],

            ['separator' => true],
            ['header' => 'الجدولة والحضور'],

            [
                'label' => 'الجداول الدراسية',
                'route' => route('dashboard.timetables.index'),
                'icon' => 'fas fa-table',
                'active' => 'dashboard.timetables.*',
                'has_permission' => $user?->can(\Perm::TimetablesView->value),
            ],
            [
                'label' => 'التقويم الدراسي',
                'route' => route('dashboard.academic-calendar.index'),
                'icon' => 'fas fa-table',
                'active' => 'dashboard.academic-calendar.*',
                'has_permission' => $user?->can(\Perm::SchoolDaysView->value),
            ],
            [
                'label' => 'الحضور والغياب',
                'route' => route('dashboard.attendance-dashboard.index'),
                'icon' => 'fas fa-clipboard-check',
                'active' => 'dashboard.attendance-dashboard.*',
                'has_permission' => $user?->can(\Perm::AttendanceSheetsView->value) || $user?->can(\Perm::AttendancesTake->value),
            ],

            ['separator' => true],
            ['header' => 'التقييم والامتحانات'],

            [
                'label' => 'الامتحانات',
                'route' => route('dashboard.exams.index'),
                'icon' => 'fas fa-file-alt',
                'active' => 'dashboard.exams.*',
                'has_permission' => $user?->can(\Perm::ExamsView->value),
            ],
            [
                'label' => 'الدرجات',
                'route' => route('dashboard.marks.index'),
                'icon' => 'fas fa-star',
                'active' => 'dashboard.marks.*',
                'has_permission' => $user?->can(\Perm::MarksView->value),
            ],
            ['separator' => true],
            ['header' => 'الإدارة الأكاديمية'],

            [
                'label' => 'السنوات الدراسية',
                'route' => route('dashboard.academic-years.index'),
                'icon' => 'fas fa-calendar-alt',
                'active' => 'dashboard.academic-years.*',
                'has_permission' => $user?->can(\Perm::AcademicYearsView->value),
            ],
            [
                'label' => 'المراحل الدراسية',
                'route' => route('dashboard.stages.index'),
                'icon' => 'fas fa-layer-group',
                'active' => 'dashboard.stages.*',
                'has_permission' => $user?->can(\Perm::StagesView->value),
            ],
            [
                'label' => 'الفصول الدراسية',
                'route' => route('dashboard.academic-terms.index'),
                'icon' => 'fas fa-layer-group',
                'active' => 'dashboard.academic-terms.*',
                'has_permission' => $user?->can(\Perm::AcademicTermsView->value),
            ],
            [
                'label' => 'الصفوف',
                'route' => route('dashboard.grades.index'),
                'icon' => 'fas fa-graduation-cap',
                'active' => 'dashboard.grades.*',
                'has_permission' => $user?->can(\Perm::GradesView->value),
            ],
            [
                'label' => 'الشعب',
                'route' => route('dashboard.sections.index'),
                'icon' => 'fas fa-door-open',
                'active' => 'dashboard.sections.*',
                'has_permission' => $user?->can(\Perm::SectionsView->value),
            ],
            [
                'label' => 'المواد الدراسية',
                'route' => route('dashboard.subjects.index'),
                'icon' => 'fas fa-book',
                'active' => 'dashboard.subjects.*',
                'has_permission' => $user?->can(\Perm::SubjectsView->value),
            ],
            [
                'label' => 'المناهج الدراسية',
                'route' => route('dashboard.curriculums.index'),
                'icon' => 'fas fa-book',
                'active' => 'dashboard.curriculums.*',
                'has_permission' => $user?->can(\Perm::CurriculumsView->value),
            ],
            ['separator' => true],
            ['header' => 'الإعدادات'],

            [
                'label' => 'المستخدمين',
                'route' => route('dashboard.users.index'),
                'icon' => 'fas fa-user-cog',
                'active' => 'dashboard.users.*',
                'has_permission' => $user?->can(\Perm::UsersView->value),
            ],
            [
                'label' => 'الصلاحيات',
                'route' => route('dashboard.roles.index'),
                'icon' => 'fas fa-key',
                'active' => 'dashboard.roles.*',
                'has_permission' => $user?->can(\Perm::RolesView->value),
            ],
            [
                'label' => 'الإعدادات',
                'route' => route('dashboard.school-settings.index'),
                'icon' => 'fas fa-setting',
                'active' => 'dashboard.school-settings.*',
                'has_permission' => $user?->can(\Perm::SettingsManage->value),
            ],
        ];
    }

    /**
     * Filter the menu: remove hidden items, empty headers, and duplicate separators.
     */
    protected function filterEmptySections(array $items): array
    {
        $filtered = [];
        $pendingHeader = null;

        foreach ($items as $item) {

            if (isset($item['header'])) {
                $pendingHeader = $item;

                continue;
            }

            if (isset($item['separator'])) {
                if (!empty($filtered) && !isset(end($filtered)['separator'])) {
                    $filtered[] = $item;
                }

                $pendingHeader = null;

                continue;
            }

            if ($item['has_permission'] ?? true) {

                if ($pendingHeader) {
                    $filtered[] = $pendingHeader;
                    $pendingHeader = null;
                }

                $filtered[] = $item;
            }
        }

        if (!empty($filtered) && isset(end($filtered)['separator'])) {
            array_pop($filtered);
        }

        return $filtered;
    }

    public function render(): View
    {
        return view('components.ui.sidebar');
    }
}
