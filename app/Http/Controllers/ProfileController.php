<?php
namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller {

    /**
     * Profil sahifasi
     */
    public function index() {
        $user = Auth::user();
        $user->load(['school', 'children', 'reportedCases']);

        // Statistika
        $stats = [
            'cases_reported' => $user->reportedCases()->count(),
            'children'       => $user->children()->count(),
        ];

        return view('profile.index', compact('user', 'stats'));
    }

    /**
     * Profilni tahrirlash
     */
    public function edit() {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Profilni yangilash
     */
    public function update(Request $request) {
        $user = Auth::user();

        $data = $request->validate([
            'name'       => 'required|string|max:150',
            'email'      => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone'      => 'nullable|string|max:30',
            'telegram_id'=> 'nullable|string|max:50',
        ]);

        $user->update($data);

        return redirect()->route('profile')->with('success', '✅ Profil muvaffaqiyatli yangilandi');
    }

    /**
     * Parolni o'zgartirish
     */
    public function updatePassword(Request $request) {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Hozirgi parol noto\'g\'ri']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', '🔐 Parol muvaffaqiyatli o\'zgartirildi');
    }
}