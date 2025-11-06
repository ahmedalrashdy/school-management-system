<?php

namespace App\Http\Controllers\Dashboard\Users;

use App\Enums\GenderEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Users\Teachers\StoreTeacherRequest;
use App\Http\Requests\Dashboard\Users\Teachers\UpdateTeacherRequest;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TeacherController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:' . \Perm::TeachersView->value, only: ['index', 'show']),
            new Middleware('can:' . \Perm::TeachersCreate->value, only: ['create', 'store']),
            new Middleware('can:' . \Perm::TeachersUpdate->value, only: ['edit', 'update', 'toggleActive']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('dashboard.users.teachers.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('dashboard.users.teachers.create', [
            'genders' => GenderEnum::options(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeacherRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            // إنشاء المستخدم
            $user = User::create(
                [
                    ...$request->safe()->only(['first_name', 'last_name', 'gender', 'phone_number', 'email', 'address']),
                    'password' => Hash::make('default-password'),
                    'is_active' => true,
                    'is_admin' => false,
                    'reset_password_required' => true,
                ]
            );
            $user->teacher()->create($request->safe()->only(['date_of_birth', 'specialization', 'qualification']));
        });

        return redirect()
            ->route('dashboard.teachers.index')
            ->with('success', 'تم إنشاء المدرس بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Teacher $teacher): View
    {

        $teacher->load('user');

        return view('dashboard.users.teachers.show', [
            'teacher' => $teacher,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher): View
    {
        $teacher->load('user');

        return view('dashboard.users.teachers.edit', [
            'teacher' => $teacher,
            'genders' => GenderEnum::options(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTeacherRequest $request, Teacher $teacher): RedirectResponse
    {
        $teacher->load('user');
        DB::transaction(function () use ($request, $teacher): void {
            $teacherData = $request->safe()->only(['specialization', 'qualification', 'date_of_birth']);
            $teacher->update($teacherData);
            $userData = $teacher->user->reset_password_required ? $request->safe()->only([
                'first_name',
                'last_name',
                'gender',
                'email',
                'phone_number',
                'address',
            ]) : [];
            $userData['is_active'] = $request->safe()->boolean('is_active');
            $teacher->user->update($userData);
        });

        return redirect()
            ->route('dashboard.teachers.show', $teacher)
            ->with('success', 'تم تحديث بيانات المدرس بنجاح.');
    }

    /**
     * Toggle the active status of the teacher.
     */
    public function toggleActive(Teacher $teacher): RedirectResponse
    {
        $teacher->load('user');

        $teacher->user->update([
            'is_active' => !$teacher->user->is_active,
        ]);
        $status = $teacher->user->is_active ? 'تفعيل' : 'تعطيل';

        return redirect()
            ->route('dashboard.teachers.show', $teacher)
            ->with('success', "تم {$status} حساب المدرس بنجاح.");
    }
}
