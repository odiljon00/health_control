<?php
namespace App\Http\Controllers;

use App\Attendance;       // ✅ TO'G'RI (App\Attendance emas)
use App\RiskCase;         // ✅ TO'G'RI
use App\Student;          // ✅ TO'G'RI
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller {

    /**
     * Bugungi davomat sahifasi (ustoz uchun)
     */
    public function today(Request $request) {
    $user = Auth::user();
    $date = $request->filled('date')
        ? Carbon::parse($request->date)
        : now();

    $class = $request->input('class');
    $showOnlyAbsent = $request->input('only_absent', false);   // ← yangi filtr

    // Sinf ro'yxati
    $classes = Student::when($user->school_id, fn($q) => $q->where('school_id', $user->school_id))
        ->distinct()->orderBy('class_name')->pluck('class_name');

    // O'quvchilar + bugungi davomat
    $query = Student::with(['attendances' => function($q) use ($date) {
            $q->whereDate('attendance_date', $date);
        }])
        ->when($user->school_id, fn($q) => $q->where('school_id', $user->school_id))
        ->when($class, fn($q) => $q->where('class_name', $class));

    // Faqat kelmaganlar
    if ($showOnlyAbsent) {
        $query->whereHas('attendances', function($q) use ($date) {
            $q->whereDate('attendance_date', $date)
              ->whereIn('status', ['absent', 'excused']);
        });
    }

    $students = $query->orderBy('class_name')->orderBy('last_name')->get();

    // Statistika
    $stats = [
        'total'   => $students->count(),
        'present' => 0,
        'absent'  => 0,
        'late'    => 0,
        'excused' => 0,
    ];
    foreach ($students as $s) {
        $att = $s->attendances->first();
        if ($att) $stats[$att->status]++;
    }

    return view('attendances.today', compact('students', 'classes', 'class', 'date', 'stats', 'showOnlyAbsent'));
}
    /**
     * Davomatni saqlash (bulk) — ustoz "Saqlash" bosganda
     */
    public function store(Request $request) {
        $request->validate([
            'attendance_date' => 'required|date',
            'statuses'        => 'required|array',
            'statuses.*'      => 'in:present,absent,late,excused',
            'reasons'         => 'nullable|array',
            'reasons.*'       => 'nullable|string|max:255',
            'is_excused'      => 'nullable|array',
            'is_excused.*'    => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $date = $request->attendance_date;

        DB::transaction(function () use ($request, $user, $date) {
            foreach ($request->statuses as $studentId => $status) {
                $student = Student::find($studentId);
                if (!$student) continue;

                // Sababli yoki sababsiz
                $isExcused = (bool) ($request->is_excused[$studentId] ?? false);

                // Ketma-ket kelmagan kunlar soni (sababsiz bo'lsa)
                $consecutive = 0;
                if ($status === 'absent' && !$isExcused) {
                    $consecutive = $student->consecutiveAbsences() + 1;
                } elseif ($status === 'absent' && $isExcused) {
                    // Sababli bo'lsa ham hisoblaymiz, lekin alert qilmaymiz
                    $consecutive = $student->consecutiveAbsences() + 1;
                }

                // Alert: faqat sababsiz va 3+ kun
                $isAlert = ($status === 'absent' && !$isExcused && $consecutive >= 3);

                Attendance::updateOrCreate(
                    [
                        'student_id'      => $studentId,
                        'attendance_date' => $date,
                    ],
                    [
                        'school_id'  => $student->school_id,
                        'marked_by'  => $user->id,
                        'status'     => $status,
                        'is_excused' => $isExcused,
                        'reason'     => $request->reasons[$studentId] ?? null,
                        'is_alert'   => $isAlert,
                    ]
                );

                // Agar 3+ kun sababsiz kelmagan bo'lsa — RiskCase ochish
                if ($isAlert) {
                    $this->createAbsenceCase($student, $consecutive, $user);
                }
            }
        });

        return back()->with('success', "✅ Davomat saqlandi: {$date}");
    }

    /**
     * O'quvchi profilidagi davomat tarixi
     */
    public function studentHistory(Student $student) {
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

        return view('attendances.student', compact('student','attendances','monthlyStats'));
    }

    /**
     * 3+ kun kelmagan o'quvchilar ro'yxati
     */
  public function alerts() {
    $user = Auth::user();

    $students = Student::when($user->school_id, fn($q) => $q->where('school_id', $user->school_id))
        ->with(['school', 'attendances' => function($q) {
            $q->whereDate('attendance_date', '>=', now()->subDays(14))
              ->orderByDesc('attendance_date');
        }])
        ->get()
        ->map(function($s) {
            $s->consecutive_absences = $s->consecutiveAbsences();
            $s->absences_last_7 = $s->absencesInLastDays(7);
            return $s;
        })
        ->filter(fn($s) => $s->consecutive_absences >= 3 || $s->absences_last_7 >= 3)  // ← 2 shart
        ->sortByDesc('consecutive_absences');

    return view('attendances.alerts', compact('students'));
}

    /**
     * Dashboard statistikasi uchun (statik yordamchi)
     */
    public static function dashboardStats($schoolId = null) {
        $today = now()->toDateString();
        $q = Attendance::whereDate('attendance_date', $today);
        if ($schoolId) $q->where('school_id', $schoolId);

        return [
            'present' => (clone $q)->where('status','present')->count(),
            'absent'  => (clone $q)->where('status','absent')->count(),
            'late'    => (clone $q)->where('status','late')->count(),
            'alerts'  => (clone $q)->where('is_alert', true)->count(),
        ];
    }

    /**
     * 3+ kun sababsiz kelmasa avtomatik RiskCase ochish
     */
   protected function createAbsenceCase($student, $consecutive, $user) {
    // Oxirgi 7 kun ichida shu o'quvchi uchun case bormi?
    $existing = RiskCase::where('student_id', $student->id)
        ->where('risk_type', 'absenteeism')
        ->whereIn('status', ['new','in_progress','ongoing'])
        ->where('created_at', '>=', now()->subDays(7))
        ->exists();

    if ($existing) {
        // Mavjud case bo'lsa — faqat SMS yuborish (kun yangilangan)
        app(NotificationService::class)->notifyAbsenceAlert($student, $consecutive);
        return;
    }

    // Yangi RiskCase yaratish
    $case = RiskCase::create([
        'student_id'    => $student->id,
        'reported_by'   => $user->id,
        'school_id'     => $student->school_id,
        'risk_type'     => 'absenteeism',
        'risk_score'    => min(20 + $consecutive * 8, 90),
        'risk_level'    => $consecutive >= 4 ? 'red' : 'yellow',   // 4+ kun = qizil
        'description'   => "{$consecutive} kun ketma-ket darsga kelmadi.",
        'status'        => 'new',
        'notify_parent' => true,
        'notify_iio'    => $consecutive >= 4,
    ]);

    // 📱 SMS yuborish (ota-ona + IIO)
    app(NotificationService::class)->notifyAbsenceAlert($student, $consecutive);

    return $case;
}
    public function absentToday(Request $request) {
    $user = Auth::user();
    $date = $request->filled('date') ? Carbon::parse($request->date) : now();

    $students = Student::with(['attendances' => function($q) use ($date) {
            $q->whereDate('attendance_date', $date);
        }])
        ->when($user->school_id, fn($q) => $q->where('school_id', $user->school_id))
        ->whereHas('attendances', function($q) use ($date) {
            $q->whereDate('attendance_date', $date)
              ->whereIn('status', ['absent', 'excused']);
        })
        ->orderBy('class_name')
        ->orderBy('last_name')
        ->get();

    return view('attendances.absent', compact('students', 'date'));
}
}