<?php

namespace App\Http\Controllers\Dashboard\Academics;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Academics\Stages\StoreStageRequest;
use App\Http\Requests\Dashboard\Academics\Stages\UpdateStageRequest;
use App\Models\Stage;
use App\Traits\HandlesSafeDelete;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class StageController extends Controller implements HasMiddleware
{
    use HandlesSafeDelete;
    public static function middleware(): array
    {
        return [
            new Middleware('can:' . \Perm::StagesView->value, only: ['index']),
            new Middleware('can:' . \Perm::StagesCreate->value, only: ['create', 'store']),
            new Middleware('can:' . \Perm::StagesUpdate->value, only: ['edit', 'update']),
            new Middleware('can:' . \Perm::StagesDelete->value, only: ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $stages = Stage::with(['grades' => fn($q) => $q->sorted()])->sorted()->get();

        return view('dashboard.academics.stages.index', [
            'stages' => $stages,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('dashboard.academics.stages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStageRequest $request): RedirectResponse
    {
        Stage::create($request->validated());

        return redirect()
            ->route('dashboard.stages.index')
            ->with('success', 'تم إنشاء المرحلة الدراسية بنجاح.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stage $stage): View
    {
        return view('dashboard.academics.stages.edit', [
            'stage' => $stage,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStageRequest $request, Stage $stage): RedirectResponse
    {
        $stage->update($request->validated());

        return redirect()
            ->route('dashboard.stages.index')
            ->with('success', 'تم تحديث المرحلة الدراسية بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stage $stage): RedirectResponse
    {
        if ($stage->hasGrades()) {
            return redirect()
                ->route('dashboard.stages.index')
                ->with('error', 'لا يمكن حذف هذه المرحلة لأنها تحتوي على صفوف. يرجى حذف الصفوف المرتبطة بها أولاً.');
        }

        return $this->safeDelete($stage, route('dashboard.stages.index'));

    }
}
