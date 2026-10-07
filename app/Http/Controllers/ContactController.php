<?php
namespace App\Http\Controllers;

use App\Message;
use Illuminate\Http\Request;

class ContactController extends Controller {

    /**
     * Aloqa formasi — xabar yuborish
     */
    public function send(Request $request) {
        $data = $request->validate([
            'name'    => 'required|string|max:150',
            'email'   => 'required|email|max:150',
            'phone'   => 'nullable|string|max:30',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|max:5000',
        ], [
            'name.required'    => 'Ismingizni kiriting',
            'email.required'   => 'Email manzilingizni kiriting',
            'email.email'      => 'Email manzil noto\'g\'ri',
            'message.required' => 'Xabar matnini kiriting',
        ]);

        Message::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'subject'    => $data['subject'] ?? 'Yangi murojaat',
            'message'    => $data['message'],
            'ip_address' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
        ]);

        return back()->with('contact_success', '✅ Xabaringiz yuborildi! Tez orada siz bilan bog\'lanamiz.');
    }
}