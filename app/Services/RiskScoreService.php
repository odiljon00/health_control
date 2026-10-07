<?php
namespace App\Services;

use App\RiskCase;

class RiskScoreService {
    /**
     * Xavf ballini hisoblash (0-100).
     * Har bir omil bo'yicha vazn.
     */
    public function calculate(array $data): int {
        $score = 0;

        // 1. Xavf turi bo'yicha asosiy ball
        $typeWeights = [
            'tobacco'     => 40,
            'alcohol'     => 50,
            'drugs'       => 70,
            'crime'       => 75,
            'aggression'  => 35,
            'depression'  => 30,
            'absenteeism' => 20,
            'other'       => 15,
        ];
        $score += $typeWeights[$data['risk_type']] ?? 20;

        // 2. Takrorlanish soni
        $previousCount = RiskCase::where('student_id', $data['student_id'])
            ->whereIn('status', ['new','in_progress','ongoing','severe'])
            ->count();
        $score += min($previousCount * 5, 20);

        // 3. Sinf darajasi (kichik sinflar — ko'proq e'tibor)
        if (!empty($data['class_name'])) {
            preg_match('/(\d+)/', $data['class_name'], $m);
            $grade = (int)($m[1] ?? 9);
            if ($grade <= 8) $score += 5;
        }

        // 4. Og'irlik darajasi (description uzunligi va kalit so'zlar)
        $desc = mb_strtolower($data['description'] ?? '');
        foreach (['giyohvand','narkotik','o\'g\'irlik','o\'qituvchi','qochish','tahdid'] as $kw) {
            if (mb_strpos($desc, $kw) !== false) $score += 5;
        }

        return max(0, min(100, $score));
    }
}