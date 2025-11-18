<?php

namespace App\Http\Controllers\Dashboard\Examinations;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Examinations\Exams\UpdateExamRequest;
use App\Models\Exam;
use App\Services\Marksheets\ExamMarksheetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class ExamController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:' . \Perm::ExamsView->value, only: ['index', 'list', 'showMarks']),
            new Middleware('can:' . \Perm::ExamsCreate->value, only: ['create']),
            new Middleware('can:' . \Perm::ExamsUpdate->value, only: ['edit', 'update']),
            new Middleware('can:' . \Perm::ExamsDelete->value, only: ['destroy']),
        ];
    }

    /**
     * Display the exams main page.
     */
    public function index(): View
    {
        return view('dashboard.examinations.exams.index');
    }

    /**
     * Display the exams list page.
     */
    public function list(): View
    {
        return view('dashboard.examinations.exams.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {

        return view('dashboard.examinations.exams.create');
    }

    public function showMarks(ExamMarksheetService $examMarksheetService, Request $request, Exam $exam)
    {
        $filters = [
            'search' => $request->input('search'),
            'sort' => $request->input('sort'), // 'desc', 'asc', 'alpha'
        ];

        $data = $examMarksheetService->getExamMarksSheetData($exam, $filters);

        return view('dashboard.examinations.exams.exam-marks', array_merge($data, ['filters' => $filters]));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam $exam): View
    {

        return view('dashboard.examinations.exams.edit', [
            'exam' => $exam,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExamRequest $request, Exam $exam): RedirectResponse
    {
        $exam->update($request->validated());

        return redirect()
            ->route('dashboard.exams.list')
            ->with('success', 'تم تحديث الامتحان بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam): RedirectResponse
    {
        if (!$exam->canBeDeleted()) {
            $reason = $exam->getDeletionBlockReason();

            return redirect()
                ->route('dashboard.exams.list')
                ->with('error', $reason);
        }

        $exam->delete();

        return redirect()
            ->route('dashboard.exams.list')
            ->with('success', 'تم حذف الامتحان بنجاح.');
    }
}
