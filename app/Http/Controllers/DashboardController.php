<?php

namespace App\Http\Controllers;

use App\RiskCase;
use App\School;
use App\Student;
use App\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $schoolId = $user->school_id;

        $query = RiskCase::query();

        if ($schoolId && !$user->isAdmin()) {
            $query->where('school_id', $schoolId);
        }

        $stats = [
            'total'    => (clone $query)->count(),
            'red'      => (clone $query)->where('risk_level', 'red')->count(),
            'yellow'   => (clone $query)->where('risk_level', 'yellow')->count(),
            'green'    => (clone $query)->where('risk_level', 'green')->count(),
            'new'      => (clone $query)->where('status', 'new')->count(),
            'resolved' => (clone $query)->where('status', 'improved')->count(),

            'students' => Student::when(
                $schoolId,
                fn($q) => $q->where('school_id', $schoolId)
            )->count(),
        ];

        $latest = (clone $query)
            ->with('student', 'reporter', 'psychologist')
            ->latest()
            ->take(10)
            ->get();

        // Sinf bo'yicha statistika
        $byClass = RiskCase::selectRaw(
                'students.class_name, count(*) as cnt'
            )
            ->join(
                'students',
                'students.id',
                '=',
                'risk_cases.student_id'
            )
            ->when(
                $schoolId,
                fn($q) => $q->where(
                    'risk_cases.school_id',
                    $schoolId
                )
            )
            ->groupBy('students.class_name')
            ->get();

        // =========================
        // DAVOMAT STATISTIKASI
        // =========================

        $attStats = AttendanceController::dashboardStats($schoolId);

        // Bugun kelmagan o'quvchilar
        $todayAbsent = Attendance::with('student')
            ->whereDate('attendance_date', now())
            ->where('status', 'absent')
            ->when(
                $schoolId,
                fn($q) => $q->where('school_id', $schoolId)
            )
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'stats',
            'latest',
            'byClass',
            'attStats',
            'todayAbsent'
        ));
    }
}