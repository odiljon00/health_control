<?php
namespace App\Http\Controllers;

use App\RiskCase;
use App\Student;
use App\Services\NotificationService;
use App\Services\RiskScoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiskCaseController extends Controller {
    public function index(Request $request) {
        $user = Auth::user();
        $query = RiskCase::with('student','reporter','psychologist');

        if ($user->school_id && !$user->isAdmin()) {
            $query->where('school_id', $user->school_id);
        }
        if ($request->filled('level'))  $query->where('risk_level', $request->level);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('type'))   $query->where('risk_type', $request->type);

        $cases = $query->latest()->paginate(20);
        return view('cases.index', compact('cases'));
    }

    public function create() {
        $students = Student::where('school_id', Auth::user()->school_id)
            ->orWhereNull('school_id')->get();
        return view('cases.create', compact('students'));
    }

    public function store(Request $request, RiskScoreService $scorer, NotificationService $notifier) {
        $data = $request->validate([
            'student_id'  => 'required|exists:students,id',
            'risk_type'   => 'required|in:tobacco,alcohol,drugs,crime,aggression,depression,absenteeism,other',
            'description' => 'nullable|string|max:2000',
            'notify_parent' => 'boolean',
            'notify_iio'    => 'boolean',
        ]);

        $student = Student::findOrFail($data['student_id']);
        $data['class_name'] = $student->class_name;

        $score = $scorer->calculate($data);
        $level = RiskCase::calculateLevel($score);

        $case = RiskCase::create([
            'student_id'   => $student->id,
            'reported_by'  => Auth::id(),
            'school_id'    => $student->school_id,
            'risk_type'    => $data['risk_type'],
            'description'  => $data['description'] ?? null,
            'risk_score'   => $score,
            'risk_level'   => $level,
            'status'       => 'new',
            'notify_parent'=> $request->boolean('notify_parent', true),
            'notify_iio'   => $request->boolean('notify_iio', false),
        ]);

        // Avtomatik xabar
        $notifier->notifyStakeholders($case);

        return redirect()->route('cases.show', $case)
            ->with('success', "Holat qayd etildi. Xavf balli: {$score} ({$level})");
    }

    public function show(RiskCase $case) {
        $case->load('student','reporter','psychologist','actions.user');
        return view('cases.show', compact('case'));
    }

    public function updateStatus(Request $request, RiskCase $case) {
        $data = $request->validate([
            'status' => 'required|in:new,in_progress,improved,ongoing,severe,closed',
            'psychologist_note' => 'nullable|string',
        ]);

        if (Auth::user()->isPsychologist() || Auth::user()->isAdmin()) {
            $data['psychologist_id'] = Auth::id();
        }

        $case->update($data);
        return back()->with('success', 'Holat yangilandi.');
    }
}