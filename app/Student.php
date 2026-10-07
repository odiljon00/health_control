<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model {
    protected $fillable = [
        'first_name','last_name','middle_name','birth_date','class_name',
        'school_id','parent_id','gender','photo','is_active'
    ];

    public function getFullNameAttribute() {
        return trim("{$this->last_name} {$this->first_name} {$this->middle_name}");
    }

    public function school()  { return $this->belongsTo(School::class); }
    public function parent()  { return $this->belongsTo(User::class, 'parent_id'); }
    public function cases()   { return $this->hasMany(RiskCase::class); }

    public function latestCase() {
        return $this->hasOne(RiskCase::class)->latestOfMany();
    }
    public function attendances() {
    return $this->hasMany(Attendance::class);
}

// Ketma-ket necha kun kelmaganini hisoblash
/**
 * Oxirgi 7 kun ichida necha kun kelmagan.
 */
public function absencesInLastDays($days = 7) {
    return $this->attendances()
        ->where('status', 'absent')
        ->where('is_excused', false)
        ->whereDate('attendance_date', '>=', now()->subDays($days))
        ->count();
}

/**
 * Oxirgi N kun ichida ketma-ket kelmagan kunlar (bugun ham kiradi).
 */
public function consecutiveAbsences($maxDays = 14) {
    $count = 0;
    $date = now()->startOfDay();

    for ($i = 0; $i < $maxDays; $i++) {
        $att = $this->attendances()
            ->whereDate('attendance_date', $date)
            ->first();

        // Yozuv yo'q — kechagi kunga o'tamiz (dam olish kuni)
        if ($att === null) {
            $date->subDay();
            continue;
        }

        // "Keldi" yoki "Kechikdi" — ketma-ketlik uzildi
        if (in_array($att->status, ['present', 'late'])) {
            break;
        }

        // "Kelmadi" — sanaymiz
        if ($att->status === 'absent' && !$att->is_excused) {
            $count++;
        }

        // "Sababli" — ketma-ketlikni uzadi
        if ($att->status === 'excused' || ($att->status === 'absent' && $att->is_excused)) {
            break;
        }

        $date->subDay();
    }

    return $count;
}
protected $casts = [
    'birth_date' => 'date',        // ← BU MUHIM!
    'is_active'  => 'boolean',
];
}