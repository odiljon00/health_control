<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller {

    /**
     * Xabarlar ro'yxati
     */
    public function index(Request $request) {
        $query = Message::query();

        // Filtrlar
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }
        if ($request->filled('starred')) {
            $query->where('is_starred', true);
        }

        $messages = $query->orderByDesc('created_at')->paginate(20);

        // Statistika
        $stats = [
            'total'    => Message::count(),
            'new'      => Message::where('status', 'new')->count(),
            'read'     => Message::where('status', 'read')->count(),
            'replied'  => Message::where('status', 'replied')->count(),
            'starred'  => Message::where('is_starred', true)->count(),
            'today'    => Message::whereDate('created_at', today())->count(),
        ];

        return view('admin.messages.index', compact('messages', 'stats'));
    }

    /**
     * Xabarni ko'rish
     */
    public function show(Message $message) {
        // O'qilgan deb belgilash
        if ($message->status === 'new') {
            $message->update([
                'status'  => 'read',
                'read_at' => now(),
            ]);
        }

        return view('admin.messages.show', compact('message'));
    }

    /**
     * Holatni yangilash
     */
    public function updateStatus(Request $request, Message $message) {
        $request->validate([
            'status' => 'required|in:new,read,replied,archived',
        ]);

        $data = ['status' => $request->status];

        if ($request->status === 'replied') {
            $data['replied_at'] = now();
        }

        $message->update($data);

        return back()->with('success', '✅ Holat yangilandi');
    }

    /**
     * Yulduzcha (starred) qo'yish/olib tashlash
     */
    public function toggleStar(Message $message) {
        $message->update(['is_starred' => !$message->is_starred]);
        return back()->with('success', $message->is_starred ? '⭐ Belgilandi' : 'Belgidan olindi');
    }

    /**
     * Admin izohi
     */
    public function updateNote(Request $request, Message $message) {
        $request->validate(['admin_note' => 'nullable|string|max:2000']);
        $message->update(['admin_note' => $request->admin_note]);
        return back()->with('success', '✅ Izoh saqlandi');
    }

    /**
     * Xabarni o'chirish
     */
    public function destroy(Message $message) {
        $message->delete();
        return redirect()->route('admin.messages.index')
            ->with('success', '🗑️ Xabar o\'chirildi');
    }
}