<?php

namespace App\Http\Controllers\Dashboard\Examinations;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Examinations\Exams\StoreExamTypeRequest;
use App\Http\Requests\Dashboard\Examinations\Exams\UpdateExamTypeRequest;
use App\Models\ExamType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class ExamTypeController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.\Perm::ExamTypesManage->value, only: ['index', 'create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $examTypes = ExamType::withCount('exams')
            ->latest()
            ->paginate(20);

        return view('dashboard.examinations.exam-types.index', [
            'examTypes' => $examTypes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('dashboard.examinations.exam-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExamTypeRequest $request): RedirectResponse
    {
        ExamType::create($request->validated());

        return redirect()
            ->route('dashboard.exam-types.index')
            ->with('success', 'تم إنشاء نوع الامتحان بنجاح.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExamType $examType): View
    {
        return view('dashboard.examinations.exam-types.edit', [
            'examType' => $examType,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExamTypeRequest $request, ExamType $examType): RedirectResponse
    {
        $examType->update($request->validated());

        return redirect()
            ->route('dashboard.exam-types.index')
            ->with('success', 'تم تحديث نوع الامتحان بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExamType $examType): RedirectResponse
    {
        if (! $examType->canBeDeleted()) {
            $reason = $examType->getDeletionBlockReason();

            return redirect()
                ->route('dashboard.exam-types.index')
                ->with('error', $reason);
        }

        $examType->delete();

        return redirect()
            ->route('dashboard.exam-types.index')
            ->with('success', 'تم حذف نوع الامتحان بنجاح.');
    }
}
