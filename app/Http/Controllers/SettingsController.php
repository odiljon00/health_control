<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SettingsController extends Controller {

    /**
     * Sozlamalar sahifasi
     */
    public function index() {
        $user = Auth::user();
        return view('settings.index', compact('user'));
    }

    /**
     * Profil ma'lumotlarini yangilash
     */
    public function updateProfile(Request $request) {
        $user = Auth::user();

        $data = $request->validate([
            'name'     => 'required|string|max:150',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone'    => 'nullable|string|max:30',
            'position' => 'nullable|string|max:100',
            'avatar'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Avatar yuklash
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/avatars'), $filename);
            $data['avatar'] = 'uploads/avatars/' . $filename;

            // Eski avatarni o'chirish
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                @unlink(public_path($user->avatar));
            }
        }

        $user->update($data);

        return back()->with('success', '✅ Profil muvaffaqiyatli yangilandi');
    }

    /**
     * Parolni o'zgartirish
     */
    public function updatePassword(Request $request) {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Hozirgi parolni kiriting',
            'password.min'              => 'Yangi parol kamida 8 belgidan iborat bo\'lishi kerak',
            'password.confirmed'        => 'Parollar mos kelmadi',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Hozirgi parol noto\'g\'ri']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', '🔐 Parol muvaffaqiyatli o\'zgartirildi');
    }

    /**
     * Bildirishnoma sozlamalari
     */
    public function updateNotifications(Request $request) {
        $user = Auth::user();

        $user->update([
            'notify_email'      => $request->boolean('notify_email'),
            'notify_sms'        => $request->boolean('notify_sms'),
            'notify_risk_cases' => $request->boolean('notify_risk_cases'),
            'notify_attendance' => $request->boolean('notify_attendance'),
        ]);

        return back()->with('success', '🔔 Bildirishnoma sozlamalari saqlandi');
    }

    /**
     * Tizim sozlamalari
     */
    public function updateSystem(Request $request) {
    $request->validate([
        'language' => 'required|in:uz,ru,en',
        'timezone' => 'required|string|max:50',
        'theme'    => 'required|in:light,dark',
    ]);

    $user = Auth::user();
    $user->update([
        'language' => $request->language,
        'timezone' => $request->timezone,
        'theme'    => $request->theme,
    ]);

    // Sessiyaga ham yozamiz (tezroq ishlashi uchun)
    session(['theme' => $request->theme]);

    return back()->with('success', '⚙️ Tizim sozlamalari saqlandi');
}
}