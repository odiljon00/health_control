<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model {
    protected $fillable = [
       'student_id','school_id','marked_by','attendance_date',
        'status','is_excused','reason','note','is_alert'
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'is_alert'        => 'boolean',
        'is_excused'      => 'boolean',
    ];

    public function student() { return $this->belongsTo(Student::class); }
    public function school()  { return $this->belongsTo(School::class); }
    public function marker()  { return $this->belongsTo(User::class, 'marked_by'); }

    public function getStatusColorAttribute() {
        return [
            'present' => '#2A9D8F',
            'absent'  => '#E63946',
            'late'    => '#F4A261',
            'excused' => '#0A2540',
        ][$this->status] ?? '#666';
    }

    public function getStatusLabelAttribute() {
        return [
            'present' => '✅ Keldi',
            'absent'  => '❌ Kelmadi',
            'late'    => '⏰ Kechikdi',
            'excused' => '📝 Sababli',
        ][$this->status] ?? $this->status;
    }

    // Sababli yoki sababsiz
    public function getExcusedLabelAttribute() {
        if (!$this->is_excused) return null;
        return '📝 Sababli';
    }
}