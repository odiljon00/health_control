<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class RiskCase extends Model {
    protected $fillable = [
        'student_id','reported_by','school_id','risk_type','risk_score','risk_level',
        'description','status','psychologist_id','psychologist_note',
        'notify_parent','notify_iio','notified_at','closed_at'
    ];
    protected $casts = [
        'notified_at' => 'datetime',
        'closed_at'   => 'datetime',
        'notify_parent' => 'boolean',
        'notify_iio'    => 'boolean',
    ];

    public function student()      { return $this->belongsTo(Student::class); }
    public function reporter()     { return $this->belongsTo(User::class, 'reported_by'); }
    public function psychologist() { return $this->belongsTo(User::class, 'psychologist_id'); }
    public function school()       { return $this->belongsTo(School::class); }
    public function actions()      { return $this->hasMany(CaseAction::class); }

    // Xavf darajasini avtomatik hisoblash
    public static function calculateLevel($score) {
        if ($score >= 70) return 'red';
        if ($score >= 40) return 'yellow';
        return 'green';
    }

    public function getLevelColorAttribute() {
        return [
            'red'    => '#E63946',
            'yellow' => '#F4A261',
            'green'  => '#2A9D8F',
        ][$this->risk_level] ?? '#0A2540';
    }
}