<?php

namespace App\Http\Controllers\Portal\Guardian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GuardianProfileController extends Controller
{
    /**
     * Display the guardian profile.
     */
    public function index(): View
    {
        $user = auth()->user();
        $guardian = $user->guardian;

        if (! $guardian) {
            abort(404, 'guardian profile not found');
        }

        return view('portal.guardian.profile.index', [
            'guardian' => $guardian,
        ]);
    }

    public function edit()
    {
        $guardian = Auth::user()->guardian;

        return view('portal.guardian.profile.edit', compact('guardian'));
    }

    public function update(Request $request)
    {
        $guardian = Auth::user()->guardian;
        $user = $guardian->user;

        $validated = $request->validate([
            'address' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('guardians', 'public');
            $validated['avatar'] = $path;
        } elseif ($request->boolean(key: 'delete_avatar') && $user->avatar) {
            \Storage::disk('public')->delete($user->avatar);
            $validated['avatar'] = null;
        }

        $user->update($validated);

        return redirect()->route('portal.guardian.profile.index')->with('success', 'تم تحديث البيانات الشخصية بنجاح.');
    }
}
