<?php

namespace App\Http\Controllers\Dashboard\Users;

use App\Enums\GenderEnum;
use App\Enums\RelationToStudentEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Users\Guardians\StoreGuardianRequest;
use App\Http\Requests\Dashboard\Users\Guardians\UpdateGuardianRequest;
use App\Http\Requests\Dashboard\Users\Students\AttachStudentRequest;
use App\Models\Guardian;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class GuardianController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:' . \Perm::GuardiansView->value, only: ['index', 'show']),
            new Middleware('can:' . \Perm::GuardiansCreate->value, only: ['create', 'store']),
            new Middleware('can:' . \Perm::GuardiansUpdate->value, only: ['edit', 'update', 'toggleActive']),
            new Middleware('can:' . \Perm::GuardiansDelete->value, only: ['destroy']),
            new Middleware('can:' . \Perm::StudentsManageGuardians->value, only: ['attachStudent', 'detachStudent']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $guardians = Guardian::with(['user', 'students'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search): void {
                    $q->whereAny(
                        ['first_name', 'last_name', 'email', 'phone_number'],
                        'like',
                        "%{$search}%"
                    );
                });
            })->when($request->status, function ($query, $status) {
                $isActive = $status === 'active';
                $query->whereHas('user', function ($q) use ($isActive): void {
                    $q->where('is_active', $isActive);
                });
            })
            ->withCount('students')
            ->latest('guardians.created_at')
            ->paginate(20)
            ->withQueryString();

        return view('dashboard.users.guardians.index', [
            'guardians' => $guardians,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('dashboard.users.guardians.create', [
            'genders' => GenderEnum::options(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGuardianRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            // إنشاء المستخدم
            $user = User::create([
                ...$request->safe(['first_name', 'last_name', 'email', 'phone_number', 'gender']),
                'password' => Hash::make('default-password'),
                'is_active' => true,
                'reset_password_required' => true,
            ]);
            $user->guardian()->create($request->safe(['occupation']));
        });

        return redirect()
            ->route('dashboard.guardians.index')
            ->with('success', 'تم إنشاء ولي الأمر بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Guardian $guardian): View
    {
        $guardian->load(['user', 'students.user']);

        return view('dashboard.users.guardians.show', [
            'guardian' => $guardian,
            'relationOptions' => RelationToStudentEnum::options(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Guardian $guardian): View
    {
        $guardian->load('user');

        return view('dashboard.users.guardians.edit', [
            'guardian' => $guardian,
            'genders' => GenderEnum::options(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGuardianRequest $request, Guardian $guardian): RedirectResponse
    {
        $guardian->load('user');
        $user = $guardian->user;

        DB::transaction(function () use ($request, $guardian, $user): void {
            $guardian->update($request->safe(['occupation']));

            // إذا كان الحساب لم يُفعّل بعد، يمكن تعديل جميع البيانات
            if ($user->reset_password_required) {
                $user->update([
                    ...$request->safe(['first_name', 'last_name', 'email', 'phone_number', 'gender']),
                    'is_active' => $request->boolean('is_active'),
                ]);
            } else {//يمكن فقط تحديث حالة المستخدم
                $user->update([
                    'is_active' => $request->boolean('is_active'),
                ]);
            }
        });

        return redirect()
            ->route('dashboard.guardians.show', $guardian)
            ->with('success', 'تم تحديث بيانات ولي الأمر بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guardian $guardian): RedirectResponse
    {
        $guardian->loadCount('students');

        // لا يمكن حذف ولي أمر مرتبط بطالب
        if ($guardian->students_count > 0) {
            return redirect()
                ->route('dashboard.guardians.index')
                ->with('error', 'لا يمكن حذف ولي الأمر هذا لوجود طلاب مرتبطين به. يرجى فك ارتباط الطلاب أولاً أو تعطيل الحساب.');
        }
        $guardian->user->delete();
        return redirect()
            ->route('dashboard.guardians.index')
            ->with('success', 'تم حذف ولي الأمر بنجاح.');
    }

    /**
     * Attach a student to the guardian.
     */
    public function attachStudent(AttachStudentRequest $request, Guardian $guardian): RedirectResponse
    {
        $relation = RelationToStudentEnum::from($request->relation_to_student);

        $guardian->students()->attach($request->student_id, [
            'relation_to_student' => $relation->value,
        ]);

        return redirect()
            ->route('dashboard.guardians.show', $guardian)
            ->with('success', 'تم ربط الطالب بولي الأمر بنجاح.');
    }

    /**
     * Detach a student from the guardian.
     */
    public function detachStudent(Request $request, Guardian $guardian): RedirectResponse
    {
        $request->validate([
            'student_id' => ['required', 'exists:students,id'],
        ]);

        $guardian->students()->detach($request->student_id);

        return redirect()
            ->route('dashboard.guardians.show', $guardian)
            ->with('success', 'تم فك ارتباط الطالب من ولي الأمر بنجاح.');
    }

    /**
     * Toggle the active status of the guardian.
     */
    public function toggleActive(Guardian $guardian): RedirectResponse
    {
        $guardian->load('user');

        $guardian->user->update([
            'is_active' => !$guardian->user->is_active,
        ]);

        $status = $guardian->user->is_active ? 'تفعيل' : 'تعطيل';

        return redirect()
            ->route('dashboard.guardians.show', $guardian)
            ->with('success', "تم {$status} حساب ولي الأمر بنجاح.");
    }
}
