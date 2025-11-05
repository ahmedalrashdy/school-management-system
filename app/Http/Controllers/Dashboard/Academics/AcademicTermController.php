<?php

namespace App\Http\Controllers\Dashboard\Academics;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Academics\AcademicTerms\StoreAcademicTermRequest;
use App\Http\Requests\Dashboard\Academics\AcademicTerms\UpdateAcademicTermRequest;
use App\Models\AcademicTerm;
use App\Services\SchoolDayService;
use App\Traits\HandlesSafeDelete;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class AcademicTermController extends Controller implements HasMiddleware
{
    use HandlesSafeDelete;
    public static function middleware(): array
    {
        return [
            new Middleware('can:' . \Perm::AcademicTermsView->value, only: ['index']),
            new Middleware('can:' . \Perm::AcademicTermsCreate->value, only: ['create', 'store']),
            new Middleware('can:' . \Perm::AcademicTermsUpdate->value, only: ['edit', 'update']),
            new Middleware('can:' . \Perm::AcademicTermsDelete->value, only: ['destroy']),
            new Middleware('can:' . \Perm::AcademicTermsActivate->value, only: ['toggleActive']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $yearId = $request->integer('academic_year_id', school()->activeYear()?->id);
        $academicTerms = AcademicTerm::with('academicYear')
            ->orderBy('academic_year_id')
            ->orderBy('start_date')
            ->when($yearId !== null, fn($q) => $q->where('academic_year_id', $yearId))
            ->paginate(20)
            ->appends(['academic_year_id' => $yearId]);

        return view('dashboard.academics.academic-terms.index', [
            'academicTerms' => $academicTerms,

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('dashboard.academics.academic-terms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAcademicTermRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        \DB::transaction(function () use ($validated) {
            $term = AcademicTerm::create($validated);
            $schoolDayService = app(SchoolDayService::class);
            $schoolDayService->generateDaysForTerm($term);
        });

        return redirect()
            ->route('dashboard.academic-terms.index')
            ->with('success', 'تم إنشاء الترم الدراسي بنجاح.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicTerm $academicTerm): View
    {
        $academicTerm->load('academicYear');

        return view('dashboard.academics.academic-terms.edit', [
            'academicTerm' => $academicTerm,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAcademicTermRequest $request, AcademicTerm $academicTerm): RedirectResponse
    {
        $validated = $request->validated();

        try {
            \DB::transaction(function () use ($validated, $academicTerm) {
                $academicTerm->update($validated);
                $schoolDayService = app(SchoolDayService::class);
                $schoolDayService->syncDaysForTerm($academicTerm);
            });

            return redirect()
                ->route('dashboard.academic-terms.index')
                ->with('success', 'تم تحديث الترم الدراسي بنجاح.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicTerm $academicTerm): RedirectResponse
    {
        // Check if any school day in this term has attendance records
        $hasAttendanceRecords = $academicTerm->schoolDays()
            ->whereHas('attendanceSheets')
            ->exists();

        if ($hasAttendanceRecords) {
            return redirect()
                ->route('dashboard.academic-terms.index')
                ->with('error', 'لا يمكن حذف الترم الدراسي لأنه يحتوي على سجلات حضور مرتبطة به.');
        }

        return $this->safeDelete($academicTerm, route('dashboard.academic-terms.index'));
    }

    /**
     * Toggle active status of an academic term.
     */
    public function toggleActive(AcademicTerm $academicTerm): RedirectResponse
    {

        if ($academicTerm->academic_year_id != school()->activeYear()?->id && !$academicTerm->is_active) {
            return back()->with('error', 'لا يمكن تفعيل فصل دراسي خارج العام النشط');
        }
        try {
            $wasActive = $academicTerm->is_active;
            $academicYearId = $academicTerm->academic_year_id;

            \DB::transaction(function () use ($academicTerm, $wasActive) {
                if ($wasActive) {
                    // Deactivate
                    $academicTerm->update(['is_active' => false]);
                } else {
                    // Activate - deactivate all other terms
                    AcademicTerm::where('id', '!=', $academicTerm->id)
                        ->update(['is_active' => false]);

                    // Activate this term
                    $academicTerm->update(['is_active' => true]);
                }
            });

            $message = $wasActive
                ? 'تم تعطيل الفصل الدراسي بنجاح.'
                : 'تم تفعيل الفصل الدراسي بنجاح.';

            return redirect()
                ->route('dashboard.academic-terms.index', ['academic_year_id' => $academicYearId])
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()
                ->route('dashboard.academic-terms.index', ['academic_year_id' => $academicTerm->academic_year_id])
                ->with('error', 'حدث خطأ أثناء تغيير حالة الفصل الدراسي: ' . $e->getMessage());
        }
    }
}
