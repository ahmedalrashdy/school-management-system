<?php

namespace App\Http\Controllers\Portal\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentProfileController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;

        return view('portal.student.profile.index', compact('student'));
    }

    public function editPersonalInfo()
    {
        $student = Auth::user()->student;

        return view('portal.student.profile.edit', compact('student'));
    }

    public function updatePersonalInfo(Request $request)
    {
        $student = Auth::user()->student;
        $user = $student->user;

        $validated = $request->validate([
            'address' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('students', 'public');
            $validated['avatar'] = $path;
        } elseif ($request->boolean(key: 'delete_avatar') && $user->avatar) {
            \Storage::disk('public')->delete($user->avatar);
            $validated['avatar'] = null;
        }

        $user->update($validated);

        return redirect()->route('portal.student.profile')->with('success', 'تم تحديث البيانات الشخصية بنجاح.');
    }

    public function guardians()
    {
        $student = Auth::user()->student;

        return view('portal.student.profile.guardians', compact('student'));
    }
}
