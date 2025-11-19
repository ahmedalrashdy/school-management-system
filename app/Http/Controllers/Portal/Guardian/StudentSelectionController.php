<?php

namespace App\Http\Controllers\Portal\Guardian;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class StudentSelectionController extends Controller
{
    /**
     * Get available destinations/services for guardian.
     *
     * @return array<string, array<string, mixed>>
     */
    protected function getAvailableDestinations(): array
    {
        return [
            'timetable' => [
                'label' => 'الجدول الدراسي',
                'description' => 'عرض الجدول الأسبوعي',
                'icon' => 'fas fa-calendar-alt',
                'iconColor' => 'info',
                'route' => 'portal.guardian.student.timetable',
            ],
            'attendance' => [
                'label' => 'الحضور والغياب',
                'description' => 'عرض سجل الحضور',
                'icon' => 'fas fa-clipboard-check',
                'iconColor' => 'success',
                'route' => 'portal.guardian.student.attendance',
            ],
            'marks' => [
                'label' => 'الدرجات',
                'description' => 'عرض الدرجات والنتائج',
                'icon' => 'fas fa-chart-line',
                'iconColor' => 'warning',
                'route' => 'portal.guardian.student.marks',
            ],
            'profile' => [
                'label' => 'الملف الشخصي',
                'description' => 'عرض بيانات الطالب',
                'icon' => 'fas fa-user',
                'iconColor' => 'primary',
                'route' => 'portal.guardian.student.profile',
            ],
        ];
    }

    /**
     * Display student selection page for guardian.
     */
    public function index(): View
    {
        $user = auth()->user();
        $guardian = $user->guardian;

        if (! $guardian) {
            abort(404, 'Guardian profile not found');
        }

        $students = $guardian->students()->with('user', 'sections.grade')->get();

        $destination = request()->query('destination');
        $availableDestinations = $this->getAvailableDestinations();
        $validDestinations = array_keys($availableDestinations);

        if ($destination && ! in_array($destination, $validDestinations, true)) {
            $destination = null;
        }

        return view('portal.guardian.select-student', [
            'guardian' => $guardian,
            'students' => $students,
            'preselectedDestination' => $destination,
            'availableDestinations' => $availableDestinations,
        ]);
    }
}
