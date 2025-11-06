<?php

namespace App\Http\Controllers\Dashboard\Users;

use App\Enums\GenderEnum;
use App\Enums\RelationToStudentEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Users\Students\AttachGuardianRequest;
use App\Http\Requests\Dashboard\Users\Students\UpdateStudentRequest;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.\Perm::StudentsView->value, only: ['index', 'show']),
            new Middleware('can:'.\Perm::StudentsUpdate->value, only: ['edit', 'update', 'toggleActive']),
            new Middleware('can:'.\Perm::StudentsManageGuardians->value, only: ['attachGuardian', 'detachGuardian']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('dashboard.users.students.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student): View
    {
        $student->load('user');

        return view('dashboard.users.students.show', [
            'student' => $student,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student): View
    {
        $student->load('user');

        return view('dashboard.users.students.edit', [
            'student' => $student,
            'genders' => GenderEnum::options(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $student->load('user');
        $user = $student->user;

        DB::transaction(function () use ($request, $student, $user): void {
            // تحديث بيانات الطالب
            $student->update([
                'date_of_birth' => $request->date_of_birth,
                'city' => $request->city,
                'district' => $request->district,
            ]);

            // تحديث بيانات المستخدم (حسب الصلاحيات)
            $userData = [];
            // البيانات الرسمية (إدارة فقط)
            if ($request->has('first_name')) {
                $userData['first_name'] = $request->first_name;
            }
            if ($request->has('last_name')) {
                $userData['last_name'] = $request->last_name;
            }
            if ($request->has('gender')) {
                $userData['gender'] = $request->gender;
            }

            // بيانات الاتصال والعنوان (إدارة يمكن تعديلها دائماً)
            if ($request->has('email')) {
                $userData['email'] = $request->email;
            }
            if ($request->has('phone_number')) {
                $userData['phone_number'] = $request->phone_number;
            }
            if ($request->has('address')) {
                $userData['address'] = $request->address;
            }
            if (! empty($userData)) {
                $user->update($userData);
            }
        });

        return redirect()
            ->route('dashboard.students.show', $student)
            ->with('success', 'تم تحديث بيانات الطالب بنجاح.');
    }

    /**
     * Toggle the active status of the student.
     */
    public function toggleActive(Student $student): RedirectResponse
    {
        $student->load('user');

        $student->user->update([
            'is_active' => ! $student->user->is_active,
        ]);

        $status = $student->user->is_active ? 'تفعيل' : 'تعطيل';

        return redirect()
            ->route('dashboard.students.show', $student)
            ->with('success', "تم {$status} حساب الطالب بنجاح.");
    }

    /**
     * Attach a guardian to the student.
     */
    public function attachGuardian(AttachGuardianRequest $request, Student $student): RedirectResponse
    {
        $relation = RelationToStudentEnum::from($request->relation_to_student);

        $student->guardians()->attach($request->guardian_id, [
            'relation_to_student' => $relation->value,
        ]);

        return redirect()
            ->route('dashboard.students.show', $student)
            ->with('success', 'تم ربط ولي الأمر بالطالب بنجاح.');
    }

    /**
     * Detach a guardian from the student.
     */
    public function detachGuardian(Student $student): RedirectResponse
    {
        request()->validate([
            'guardian_id' => ['required', 'exists:guardians,id'],
        ]);

        $student->guardians()->detach(request()->guardian_id);

        return redirect()
            ->route('dashboard.students.show', $student)
            ->with('success', 'تم فك ارتباط ولي الأمر من الطالب بنجاح.');
    }
}
