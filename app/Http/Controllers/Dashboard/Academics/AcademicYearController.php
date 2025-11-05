<?php

namespace App\Http\Controllers\Dashboard\Academics;

use App\Enums\AcademicYearStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Academics\AcademicYears\StoreAcademicYearRequest;
use App\Http\Requests\Dashboard\Academics\AcademicYears\UpdateAcademicYearRequest;
use App\Models\AcademicYear;
use App\Traits\HandlesSafeDelete;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AcademicYearController extends Controller implements HasMiddleware
{

    use HandlesSafeDelete;
    public static function middleware(): array
    {
        return [
            new Middleware('can:' . \Perm::AcademicYearsView->value, only: ['index']),
            new Middleware('can:' . \Perm::AcademicYearsCreate->value, only: ['create', 'store']),
            new Middleware('can:' . \Perm::AcademicYearsUpdate->value, only: ['edit', 'update']),
            new Middleware('can:' . \Perm::AcademicYearsDelete->value, only: ['destroy']),
            new Middleware('can:' . \Perm::AcademicYearsActivate->value, only: ['activate']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = AcademicYear::query()->latest('start_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $academicYears = $query->paginate(15)->withQueryString();

        return view('dashboard.academics.academic-years.index', [
            'academicYears' => $academicYears,
            'statuses' => AcademicYearStatus::options(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $hasActiveYear = school()->activeYear() != null;
        return view('dashboard.academics.academic-years.create', [
            'hasActiveYear' => $hasActiveYear,
            'statuses' => $hasActiveYear
                ? [AcademicYearStatus::Upcoming->value => AcademicYearStatus::Upcoming->label()]
                : [
                    AcademicYearStatus::Active->value => AcademicYearStatus::Active->label(),
                    AcademicYearStatus::Upcoming->value => AcademicYearStatus::Upcoming->label(),
                ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAcademicYearRequest $request): RedirectResponse
    {
        AcademicYear::create($request->validated());

        return redirect()
            ->route('dashboard.academic-years.index')
            ->with('success', 'تم إنشاء السنة الدراسية بنجاح.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicYear $academicYear): View
    {
        if ($academicYear->status === AcademicYearStatus::Archived) {
            abort(403, 'لا يمكن تعديل السنة الدراسية المؤرشفة.');
        }

        return view('dashboard.academics.academic-years.edit', [
            'academicYear' => $academicYear,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAcademicYearRequest $request, AcademicYear $academicYear): RedirectResponse
    {
        $academicYear->update($request->validated());

        return redirect()
            ->route('dashboard.academic-years.index')
            ->with('success', 'تم تحديث السنة الدراسية بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicYear $academicYear): RedirectResponse
    {


        // Check if year has academic terms
        if ($academicYear->academicTerms()->count() > 0) {
            return redirect()
                ->route('dashboard.academic-years.index')
                ->with('error', 'لا يمكن حذف السنة الدراسية لأنها تحتوي على فصول دراسية مرتبطة بها.');
        }

        // Check if year has sections
        if ($academicYear->sections()->count() > 0) {
            return redirect()
                ->route('dashboard.academic-years.index')
                ->with('error', 'لا يمكن حذف السنة الدراسية لأنها تحتوي على شعب دراسية مرتبطة بها.');
        }

        // Check if year has teacher assignments
        if ($academicYear->teacherAssignments()->count() > 0) {
            return redirect()
                ->route('dashboard.academic-years.index')
                ->with('error', 'لا يمكن حذف السنة الدراسية لأنها تحتوي على تعيينات مدرسين مرتبطة بها.');
        }
        return $this->safeDelete($academicYear, route('dashboard.academic-years.index'));
    }

    /**
     * Activate an upcoming academic year.
     */
    public function activate(AcademicYear $academicYear): RedirectResponse
    {
        // التحقق من أن السنة قادمة
        if ($academicYear->status !== AcademicYearStatus::Upcoming) {
            return redirect()
                ->route('dashboard.academic-years.index')
                ->with('error', 'يمكن تفعيل السنوات القادمة فقط.');
        }

        try {
            DB::transaction(function () use ($academicYear) {
                // البحث عن السنة النشطة الحالية
                $currentActiveYear = AcademicYear::where('status', AcademicYearStatus::Active->value)->first();

                // أرشفة السنة النشطة الحالية
                if ($currentActiveYear) {
                    $currentActiveYear->update([
                        'status' => AcademicYearStatus::Archived->value,
                    ]);
                }

                // تفعيل السنة القادمة
                $academicYear->update([
                    'status' => AcademicYearStatus::Active->value,
                ]);
            });

            return redirect()
                ->route('dashboard.academic-years.index')
                ->with('success', 'تم تفعيل السنة الدراسية بنجاح.');
        } catch (\Exception $e) {
            return redirect()
                ->route('dashboard.academic-years.index')
                ->with('error', 'حدث خطأ أثناء تفعيل السنة الدراسية: ' . $e->getMessage());
        }
    }
}
