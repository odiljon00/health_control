<?php
namespace App\Http\Controllers;

use App\Attendance;
use App\RiskCase;
use App\School;
use App\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller {

    public function index(Request $request) {
        $user = Auth::user();
        $schoolId = $user->isAdmin() ? $request->input('school_id') : $user->school_id;
        $period = $request->input('period', 30); // kun

        // ==================== UMUMIY KO'RSATKICHLAR ====================
        $totalCases = RiskCase::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->count();
        $redCases = RiskCase::when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->where('risk_level', 'red')->count();
        $yellowCases = RiskCase::when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->where('risk_level', 'yellow')->count();
        $greenCases = RiskCase::when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->where('risk_level', 'green')->count();

        $totalStudents = Student::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->count();
        $totalSchools = School::count();

        // ==================== DAVOMAT STATISTIKASI ====================
        $todayStats = [
            'present' => Attendance::whereDate('attendance_date', today())
                ->when($schoolId, fn($q) => $q->where('school_id', $schoolId))
                ->where('status', 'present')->count(),
            'absent' => Attendance::whereDate('attendance_date', today())
                ->when($schoolId, fn($q) => $q->where('school_id', $schoolId))
                ->where('status', 'absent')->count(),
            'late' => Attendance::whereDate('attendance_date', today())
                ->when($schoolId, fn($q) => $q->where('school_id', $schoolId))
                ->where('status', 'late')->count(),
            'alerts' => Attendance::whereDate('attendance_date', today())
                ->when($schoolId, fn($q) => $q->where('school_id', $schoolId))
                ->where('is_alert', true)->count(),
        ];

        // ==================== OXIRGI N KUN DINAMIKASI ====================
        $chartData = $this->getDailyChartData($schoolId, $period);

        // ==================== SINF BO'YICHA ====================
        $byClass = RiskCase::selectRaw('students.class_name, count(*) as cnt')
            ->join('students', 'students.id', '=', 'risk_cases.student_id')
            ->when($schoolId, fn($q) => $q->where('risk_cases.school_id', $schoolId))
            ->groupBy('students.class_name')
            ->orderByDesc('cnt')
            ->take(10)
            ->get();

        // ==================== XAVF TURLARI ====================
        $byType = RiskCase::selectRaw('risk_type, count(*) as cnt')
            ->when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->groupBy('risk_type')
            ->orderByDesc('cnt')
            ->get();

        // ==================== OYLIK STATISTIKA ====================
        $byMonth = RiskCase::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, count(*) as cnt")
            ->when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->groupBy('month')
            ->orderByDesc('month')
            ->take(6)
            ->get();

        // ==================== ENG KO'P KELMAGAN O'QUVCHILAR ====================
        $topAbsent = Student::when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->get()
            ->map(function($s) {
                $s->absences_count = $s->attendances()->where('status', 'absent')->count();
                return $s;
            })
            ->sortByDesc('absences_count')
            ->take(5);

        // ==================== MAKTABLAR (admin uchun) ====================
        $schools = $user->isAdmin() ? School::orderBy('name')->get() : collect();

        return view('statistics.index', compact(
            'totalCases', 'redCases', 'yellowCases', 'greenCases',
            'totalStudents', 'totalSchools',
            'todayStats', 'chartData', 'byClass', 'byType',
            'byMonth', 'topAbsent', 'schools', 'schoolId', 'period'
        ));
    }

    /**
     * Kunlik dinamika ma'lumotlari
     */
    protected function getDailyChartData($schoolId, $days = 30) {
        $labels = [];
        $presentData = [];
        $absentData = [];
        $casesData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d.m');

            $presentData[] = Attendance::whereDate('attendance_date', $date)
                ->when($schoolId, fn($q) => $q->where('school_id', $schoolId))
                ->where('status', 'present')->count();

            $absentData[] = Attendance::whereDate('attendance_date', $date)
                ->when($schoolId, fn($q) => $q->where('school_id', $schoolId))
                ->where('status', 'absent')->count();

            $casesData[] = RiskCase::whereDate('created_at', $date)
                ->when($schoolId, fn($q) => $q->where('school_id', $schoolId))->count();
        }

        return [
            'labels' => $labels,
            'present' => $presentData,
            'absent' => $absentData,
            'cases' => $casesData,
        ];
    }
}