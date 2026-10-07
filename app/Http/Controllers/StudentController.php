<?php
namespace App\Http\Controllers;

use App\Student;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller {

    /**
     * O'quvchilar ro'yxati
     */
    public function index(Request $request) {
        $query = Student::with(['school', 'parent']);

        if (!Auth::user()->isAdmin()) {
            $query->where('school_id', Auth::user()->school_id);
        }

        if ($request->filled('class')) {
            $query->where('class_name', $request->class);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('class_name')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(25);

        return view('students.index', compact('students'));
    }

    /**
     * Bitta o'quvchi tafsiloti
     */
    public function show(Student $student) {
        if (!Auth::user()->isAdmin() && $student->school_id !== Auth::user()->school_id) {
            abort(403, 'Ruxsat yo\'q');
        }

        $student->load([
            'school',
            'parent',
            'cases' => function($q) {
                $q->orderByDesc('created_at');
            },
            'cases.reporter',
            'cases.psychologist',
        ]);

        $attendances = $student->attendances()
            ->orderByDesc('attendance_date')
            ->paginate(30);

        $monthlyStats = $student->attendances()
            ->selectRaw("DATE_FORMAT(attendance_date, '%Y-%m') as month,
                         SUM(CASE WHEN status='present' THEN 1 ELSE 0 END) as present,
                         SUM(CASE WHEN status='absent'  THEN 1 ELSE 0 END) as absent,
                         SUM(CASE WHEN status='late'    THEN 1 ELSE 0 END) as late,
                         SUM(CASE WHEN status='excused' THEN 1 ELSE 0 END) as excused")
            ->groupBy('month')
            ->orderByDesc('month')
            ->get();

        $consecutiveAbsences = $student->consecutiveAbsences();
        $absencesLast7 = $student->absencesInLastDays(7);

        return view('students.show', compact(
            'student',
            'attendances',
            'monthlyStats',
            'consecutiveAbsences',
            'absencesLast7'
        ));
    }

    /**
     * Yangi o'quvchi qo'shish
     */
    public function store(Request $request) {
        $data = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'middle_name'   => 'nullable|string|max:100',
            'class_name'    => 'required|string|max:20',
            'birth_date'    => 'nullable|date',
            'gender'        => 'nullable|in:male,female',
            'parent_name'   => 'nullable|string|max:150',
            'parent_email'  => 'required|email|max:150',
            'parent_phone'  => 'required|string|max:30',
        ], [
            'parent_email.required' => 'Ota-ona emailini kiriting',
            'parent_email.email'    => 'Email manzil noto\'g\'ri',
            'parent_phone.required' => 'Ota-ona telefon raqamini kiriting (SMS yuborish uchun kerak)',
        ]);

        $user = Auth::user();
        $schoolId = $user->school_id;

        if (empty($schoolId)) {
            $firstSchool = \App\School::first();
            if (!$firstSchool) {
                return back()->withInput()
                    ->with('error', "Tizimda hech qanday maktab yo'q.");
            }
            $schoolId = $firstSchool->id;
        }

        // Ota-onani yaratish yoki topish (email orqali)
        $parent = User::firstOrCreate(
            ['email' => $data['parent_email']],
            [
                'name'      => $data['parent_name'] ?? 'Ota-ona',
                'password'  => Hash::make('password'),
                'role'      => 'parent',
                'school_id' => $schoolId,
                'phone'     => $data['parent_phone'],
                'is_active' => true,
            ]
        );

        if (!$parent->wasRecentlyCreated && !empty($data['parent_phone'])) {
            $parent->update(['phone' => $data['parent_phone']]);
        }

        // O'quvchi yaratish
        $student = Student::create([
            'first_name'  => $data['first_name'],
            'last_name'   => $data['last_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'class_name'  => $data['class_name'],
            'birth_date'  => $data['birth_date'] ?? null,
            'gender'      => $data['gender'] ?? null,
            'school_id'   => $schoolId,
            'parent_id'   => $parent->id,
            'is_active'   => true,
        ]);

        // Xush kelibsiz SMS (ixtiyoriy)
        if ($student->parent && $student->parent->phone) {
            try {
                app(\App\Services\NotificationService::class)->notifyNewStudent($student);
            } catch (\Exception $e) {
                \Log::warning('Welcome SMS yuborilmadi: ' . $e->getMessage());
            }
        }

        return redirect()->route('students.index')
            ->with('success',
                "✅ {$data['last_name']} {$data['first_name']} qo'shildi. " .
                "Ota-ona: {$parent->name} ({$parent->phone})"
            );
    }

    /**
     * Tahrirlash formasi (AJAX uchun)
     */
    public function edit(Student $student) {
        if (!Auth::user()->isAdmin() && $student->school_id !== Auth::user()->school_id) {
            abort(403);
        }

        return response()->json([
            'student' => $student->load('parent'),
        ]);
    }

    /**
     * O'quvchini yangilash
     */
    public function update(Request $request, Student $student) {
        if (!Auth::user()->isAdmin() && $student->school_id !== Auth::user()->school_id) {
            abort(403);
        }

        $data = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'middle_name'   => 'nullable|string|max:100',
            'class_name'    => 'required|string|max:20',
            'birth_date'    => 'nullable|date',
            'gender'        => 'nullable|in:male,female',
            'parent_name'   => 'nullable|string|max:150',
            'parent_email'  => 'required|email|max:150',
            'parent_phone'  => 'required|string|max:30',
        ]);

        $student->update([
            'first_name'  => $data['first_name'],
            'last_name'   => $data['last_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'class_name'  => $data['class_name'],
            'birth_date'  => $data['birth_date'] ?? null,
            'gender'      => $data['gender'] ?? null,
        ]);

        if ($student->parent) {
            $student->parent->update([
                'name'  => $data['parent_name'] ?? $student->parent->name,
                'email' => $data['parent_email'],
                'phone' => $data['parent_phone'],
            ]);
        }

        return redirect()->route('students.index')
            ->with('success', "✅ {$student->full_name} yangilandi");
    }

    /**
     * O'quvchini o'chirish
     */
    public function destroy(Student $student) {
        if (!Auth::user()->isAdmin() && $student->school_id !== Auth::user()->school_id) {
            abort(403);
        }

        $name = $student->full_name;

        $student->attendances()->delete();
        $student->cases()->delete();
        $student->delete();

        return redirect()->route('students.index')
            ->with('success', "🗑️ {$name} o'chirildi");
    }
}