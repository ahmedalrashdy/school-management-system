<?php

namespace App\Http\Controllers\Dashboard\Academics;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Academics\Grades\StoreGradeRequest;
use App\Http\Requests\Dashboard\Academics\Grades\UpdateGradeRequest;
use App\Models\Grade;
use App\Models\Stage;
use App\Traits\HandlesSafeDelete;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class GradeController extends Controller implements HasMiddleware
{
    use HandlesSafeDelete;
    public static function middleware(): array
    {
        return [
            new Middleware('can:' . \Perm::GradesView->value, only: ['index']),
            new Middleware('can:' . \Perm::GradesCreate->value, only: ['create', 'store']),
            new Middleware('can:' . \Perm::GradesUpdate->value, only: ['edit', 'update']),
            new Middleware('can:' . \Perm::GradesDelete->value, only: ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $stages = Stage::with([
            'grades' => fn($q) => $q->sorted()
        ])->sorted()->get();

        return view('dashboard.academics.grades.index', [
            'stages' => $stages,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {

        return view('dashboard.academics.grades.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGradeRequest $request): RedirectResponse
    {
        Grade::create($request->validated());

        return redirect()
            ->route('dashboard.grades.index')
            ->with('success', 'تم إنشاء الصف الدراسي بنجاح.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Grade $grade): View
    {

        return view('dashboard.academics.grades.edit', [
            'grade' => $grade,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGradeRequest $request, Grade $grade): RedirectResponse
    {
        $grade->update($request->validated());

        return redirect()
            ->route('dashboard.grades.index')
            ->with('success', 'تم تحديث الصف الدراسي بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Grade $grade): RedirectResponse
    {
        if (!$grade->canBeDeleted()) {
            $reason = $grade->getDeletionBlockReason();

            return redirect()
                ->route('dashboard.grades.index')
                ->with('error', $reason);
        }

        return $this->safeDelete($grade, route('dashboard.grades.index'));


    }
}
