<?php

namespace App\Http\Controllers\Dashboard\Examinations;

use App\Http\Controllers\Controller;
use App\Models\GradingRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GradingRuleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:' . \Perm::GradingRulesView->value, only: ['index', 'show']),
            new Middleware('can:' . \Perm::GradingRulesCreate->value, only: ['create']),
            new Middleware('can:' . \Perm::GradingRulesUpdate->value, only: ['edit', 'togglePublish']),
            new Middleware('can:' . \Perm::GradingRulesDelete->value, only: ['destroy']),
        ];
    }

    /**
     * Display the grading rules main page.
     */
    public function index(): View
    {
        return view('dashboard.examinations.grading-rules.index');
    }

    /**
     * عرض تفاصيل قاعدة الاحتساب.
     */
    public function show(GradingRule $gradingRule)
    {
        $gradingRule->load([
            'section.grade.stage',
            'section.academicYear',
            'curriculumSubject.subject',
            'finalExam.examType',
            'items.exam.examType',
        ]);

        return view('dashboard.examinations.grading-rules.show', compact('gradingRule'));
    }

    public function togglePublish(GradingRule $gradingRule)
    {
        $newState = !$gradingRule->is_published;
        $gradingRule->update([
            'is_published' => $newState,
        ]);

        $message = $newState
            ? 'تم نشر قاعدة الدرجات بنجاح. يمكن للطلاب وأولياء الأمور الآن الاطلاع على النتائج.'
            : 'تم إلغاء نشر قاعدة الدرجات. تم إخفاء النتائج عن الطلاب وأولياء الأمور.';

        return back()->with('success', $message);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('dashboard.examinations.grading-rules.create');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GradingRule $gradingRule): View
    {
        return view('dashboard.examinations.grading-rules.edit', [
            'gradingRule' => $gradingRule,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GradingRule $gradingRule): RedirectResponse
    {


        $gradingRule->delete();

        return redirect()
            ->route('dashboard.grading-rules.index')
            ->with('success', 'تم حذف قاعدة الاحتساب بنجاح.');
    }


}
