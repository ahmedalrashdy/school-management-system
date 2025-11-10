<?php

namespace App\Http\Controllers\Dashboard\Academics;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Academics\Subjects\StoreSubjectRequest;
use App\Http\Requests\Dashboard\Academics\Subjects\UpdateSubjectRequest;
use App\Models\Subject;
use App\Traits\HandlesSafeDelete;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class SubjectController extends Controller implements HasMiddleware
{
    use HandlesSafeDelete;
    public static function middleware(): array
    {
        return [
            new Middleware('can:' . \Perm::SubjectsView->value, only: ['index']),
            new Middleware('can:' . \Perm::SubjectsCreate->value, only: ['create', 'store']),
            new Middleware('can:' . \Perm::SubjectsUpdate->value, only: ['edit', 'update']),
            new Middleware('can:' . \Perm::SubjectsDelete->value, only: ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $subjects = Subject::sorted()
            ->withCount("curriculumSubjects")
            ->paginate(20);

        return view('dashboard.academics.subjects.index', [
            'subjects' => $subjects,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('dashboard.academics.subjects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        Subject::create($request->validated());

        return redirect()
            ->route('dashboard.subjects.index')
            ->with('success', 'تم إنشاء المادة الدراسية بنجاح.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject): View
    {
        return view('dashboard.academics.subjects.edit', [
            'subject' => $subject,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubjectRequest $request, Subject $subject): RedirectResponse
    {
        $subject->update($request->validated());

        return redirect()
            ->route('dashboard.subjects.index')
            ->with('success', 'تم تحديث المادة الدراسية بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject): RedirectResponse
    {
        if (!$subject->canBeDeleted()) {
            $reason = $subject->getDeletionBlockReason();

            return redirect()
                ->route('dashboard.subjects.index')
                ->with('error', $reason);
        }

        return $this->safeDelete($subject, route('dashboard.subjects.index'));
    }
}
