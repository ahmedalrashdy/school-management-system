<?php

namespace App\Http\Controllers\Dashboard\Academics;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Academics\Sections\StoreSectionRequest;
use App\Http\Requests\Dashboard\Academics\Sections\UpdateSectionRequest;
use App\Models\Section;
use App\Traits\HandlesSafeDelete;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class SectionController extends Controller implements HasMiddleware
{
    use HandlesSafeDelete;
    public static function middleware(): array
    {
        return [
            new Middleware('can:' . \Perm::SectionsView->value, only: ['index']),
            new Middleware('can:' . \Perm::SectionsCreate->value, only: ['create', 'store']),
            new Middleware('can:' . \Perm::SectionsUpdate->value, only: ['edit', 'update']),
            new Middleware('can:' . \Perm::SectionsDelete->value, only: ['destroy']),
            new Middleware('can:' . \Perm::SectionsViewStudents->value, only: ['students']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $activeYear = school()->activeYear();
        $currentTermId = school()->currentAcademicTerm()?->id;
        $query = Section::with(['academicYear', 'academicTerm', 'grade.stage'])
            ->withCount('students')
            ->latest()
            ->where('academic_year_id', $request->integer('academic_year_id', $activeYear?->id))
            ->where('academic_term_id', $request->integer('academic_term_id', $currentTermId));
        if ($request->filled('grade_id')) {
            $query->where('grade_id', $request->grade_id);
        }
        $sections = $query->paginate(20)->withQueryString();

        return view('dashboard.academics.sections.index', [
            'sections' => $sections,
            'activeYear' => $activeYear,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {

        return view('dashboard.academics.sections.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSectionRequest $request): RedirectResponse
    {
        Section::create($request->validated());

        return redirect()
            ->route('dashboard.sections.index')
            ->with('success', 'تم إنشاء الشعبة الدراسية بنجاح.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Section $section): View
    {
        // منع تعديل الشعبة التابعة لسنة مؤرشفة
        if ($section->belongsToArchivedYear()) {
            abort(403, 'لا يمكن تعديل الشعبة التابعة لسنة دراسية مؤرشفة.');
        }

        return view('dashboard.academics.sections.edit', [
            'section' => $section->load(['academicYear', 'academicTerm', 'grade.stage']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSectionRequest $request, Section $section): RedirectResponse
    {
        $section->update($request->validated());

        return redirect()
            ->route('dashboard.sections.index')
            ->with('success', 'تم تحديث الشعبة الدراسية بنجاح.');
    }

    /**
     * Display students of the specified section.
     */
    public function students(Section $section, Request $request): View
    {
        $section->load(['academicYear', 'academicTerm', 'grade.stage']);

        $query = $section->students()
            ->with(['user'])
            ->latest('section_students.created_at');

        // البحث بالاسم أو رقم القيد
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                })
                    ->orWhere('admission_number', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(20)->withQueryString();

        return view('dashboard.academics.sections.students', [
            'section' => $section,
            'students' => $students,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Section $section): RedirectResponse
    {
        if (!$section->canBeDeleted()) {
            $reason = $section->getDeletionBlockReason();

            return redirect()
                ->route('dashboard.sections.index')
                ->with('error', $reason);
        }
        return $this->safeDelete($section, route('dashboard.sections.index'));
    }
}
