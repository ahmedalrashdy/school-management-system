<?php

namespace App\Http\Controllers\Portal\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TeacherProfileController extends Controller
{
    /**
     * Display the teacher profile.
     */
    public function index(): View
    {
        $user = auth()->user();
        $teacher = $user->teacher;

        if (! $teacher) {
            abort(404, 'Teacher profile not found');
        }

        return view('portal.teacher.profile.index', [
            'teacher' => $teacher,
        ]);
    }

    public function edit()
    {
        $teacher = Auth::user()->teacher;

        return view('portal.teacher.profile.edit', compact('teacher'));
    }

    public function update(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $user = $teacher->user;

        $validated = $request->validate([
            'address' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('teachers', 'public');
            $validated['avatar'] = $path;
        } elseif ($request->boolean(key: 'delete_avatar') && $user->avatar) {
            \Storage::disk('public')->delete($user->avatar);
            $validated['avatar'] = null;
        }

        $user->update($validated);

        return redirect()->route('portal.teacher.profile.index')->with('success', 'تم تحديث البيانات الشخصية بنجاح.');
    }
}
