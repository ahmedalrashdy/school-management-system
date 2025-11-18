<?php

namespace App\Http\Controllers\Dashboard\Examinations;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Student;
use App\Services\Marksheets\SectionAuditService;
use App\Services\Marksheets\SectionMarksheetService;
use App\Services\Marksheets\SectionReadinessService;
use App\Services\Marksheets\StudentMarksheetService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarksController extends Controller
{
    public function __construct(
        protected SectionReadinessService $readinessService,
        protected SectionMarksheetService $marksheetService,
        protected StudentMarksheetService $studentMarksheetService,
        protected SectionAuditService $auditService
    ) {}

    public function index(Request $request): View
    {
        $activeYear = school()->activeYear();

        $query = Section::with(['academicYear', 'academicTerm', 'grade.stage'])
            ->latest()
            ->where('academic_year_id', $request->integer('academic_year_id', $activeYear?->id))
            ->when($request->filled('academic_term_id'), function ($q) use ($request) {
                $q->where('academic_term_id', $request->input('academic_term_id'));
            });

        if ($request->filled('grade_id')) {
            $query->where('grade_id', $request->grade_id);
        }

        $sections = $query->paginate(20)->withQueryString();

        // جلب إحصائيات لجميع الشعب في استعلامات محسّنة
        $bulkStats = $this->readinessService->getBulkStatistics($sections->getCollection());

        // دمج الإحصائيات مع الشعب
        $sectionsWithStats = $sections->map(function ($section) use ($bulkStats) {
            $stats = $bulkStats[$section->id] ?? [
                'total_subjects' => 0,
                'subjects_with_rules' => 0,
                'completed_subjects' => 0,
                'is_ready' => false,
            ];

            return [
                'section' => $section,
                'stats' => $stats,
            ];
        });

        return view('dashboard.examinations.marks.index', [
            'sections' => $sections,
            'sectionsWithStats' => $sectionsWithStats,
            'activeYear' => $activeYear,
        ]);
    }

    public function show(Section $section): View
    {
        $data = $this->marksheetService->getSectionMarksheetData($section);

        return view('dashboard.examinations.marks.show', $data);
    }

    public function studentDetails(Section $section, Student $student): View
    {
        $data = $this->studentMarksheetService->getStudentDetailedMarks($student, $section);

        return view('dashboard.examinations.marks.student-details', $data);
    }

    public function audit(Section $section): View
    {
        $data = $this->auditService->getSectionAuditData($section);

        return view('dashboard.examinations.marks.audit', $data);
    }
}
