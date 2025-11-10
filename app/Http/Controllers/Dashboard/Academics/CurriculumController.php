<?php

namespace App\Http\Controllers\Dashboard\Academics;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Academics\Curriculums\StoreCurriculumRequest;
use App\Models\Curriculum;
use App\Models\CurriculumSubject;
use App\Models\Subject;
use App\Traits\HandlesSafeDelete;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CurriculumController extends Controller implements HasMiddleware
{
    use HandlesSafeDelete;
    public static function middleware(): array
    {
        return [
            new Middleware('can:' . \Perm::CurriculumsView->value, only: ['index', 'show']),
            new Middleware('can:' . \Perm::CurriculumsCreate->value, only: ['create', 'store']),
            new Middleware('can:' . \Perm::CurriculumsDelete->value, only: ['destroy']),
            new Middleware('can:' . \Perm::CurriculumsManageSubjects->value, only: ['addSubject', 'removeSubject']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $activeYear = school()->activeYear();
        $currentTerm = school()->currentAcademicTerm();
        $curriculums = Curriculum::with(['academicYear', 'academicTerm', 'grade.stage', 'subjects'])
            ->withCount('subjects')
            ->where('academic_year_id', $request->integer('academic_year_id', $activeYear?->id))
            ->when($request->filled('grade_id'), fn($q) => $q->where('grade_id', $request->input('grade_id')))
            ->when(
                $request->filled('academic_term_id') || $currentTerm?->id !== null,
                fn($q) => $q->where('academic_term_id', $request->input('academic_term_id', $currentTerm?->id))
            )
            ->paginate(20)->withQueryString();

        return view('dashboard.academics.curriculums.index', [
            'curriculums' => $curriculums,
            'activeYear' => $activeYear,
            'currentTerm' => $currentTerm,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $subjects = Subject::latest()->get();

        return view('dashboard.academics.curriculums.create', [
            'subjects' => $subjects,
        ]);
    }


    public function store(StoreCurriculumRequest $request): RedirectResponse
    {
        $curriculum = DB::transaction(function () use ($request) {

            $curriculum = Curriculum::create($request->validated());

            if ($request->has('subject_ids') && is_array($request->subject_ids)) {
                $curriculum->subjects()->attach($request->subject_ids);

            }

            return $curriculum;
        });
        // $subject = new Subject();
        // $subject->flushCache();
        return redirect()
            ->route('dashboard.curriculums.show', $curriculum)
            ->with('success', 'تم إنشاء المنهج الدراسي بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Curriculum $curriculum): View
    {
        $curriculum->load(['academicYear', 'academicTerm', 'grade.stage', 'curriculumSubjects.subject']);

        $canAddSubject = $curriculum->canAddSubject();
        $curriculumSubjects = $curriculum->curriculumSubjects->pluck('subject_id');
        // الحصول على جميع المواد المتاحة (غير المضافة للمنهج)
        $availableSubjects = $canAddSubject ? Subject::whereNotIn('id', $curriculumSubjects)->get() : collect();

        return view('dashboard.academics.curriculums.show', compact(
            'curriculum',
            'availableSubjects',
            'canAddSubject'
        ));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Curriculum $curriculum): RedirectResponse
    {
        $curriculum->delete();

        return redirect()
            ->route('dashboard.curriculums.index')
            ->with('success', 'تم حذف المنهج الدراسي بنجاح.');
    }

    /**
     * Add a subject to the curriculum.
     */
    public function addSubject(Request $request, Curriculum $curriculum): RedirectResponse
    {
        $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
        ]);
        if (!$curriculum->canAddSubject()) {
            return back()->with('error', 'لا يمكن إضافة مادة دراسية إلا لمنهج  ينتمي للفصل الحالي او السنة القادمة');
        }
        // التحقق من عدم وجود المادة في المنهج
        if ($curriculum->subjects()->where('subjects.id', $request->subject_id)->exists()) {
            return redirect()
                ->route('dashboard.curriculums.show', $curriculum)
                ->with('error', 'هذه المادة موجودة بالفعل في المنهج.');
        }

        $curriculum->subjects()->attach($request->subject_id);

        return redirect()
            ->route('dashboard.curriculums.show', $curriculum)
            ->with('success', 'تم إضافة المادة إلى المنهج بنجاح.');
    }

    /**
     * Remove a subject from the curriculum.
     */
    public function removeSubject(Curriculum $curriculum, CurriculumSubject $curriculumSubject): RedirectResponse
    {
        // التحقق من أن curriculum_subject ينتمي للمنهج
        if ($curriculumSubject->curriculum_id !== $curriculum->id) {
            abort(404);
        }

        if (!$curriculumSubject->canBeDeleted()) {
            $reason = $curriculumSubject->getDeletionBlockReason();

            return redirect()
                ->route('dashboard.curriculums.show', $curriculum)
                ->with('error', $reason);
        }
        return $this->safeDelete($curriculumSubject, route('dashboard.curriculums.show', $curriculum));
    }
}
