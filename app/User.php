<?php
namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    use Notifiable;

   protected $fillable = [
    'name', 'email', 'password', 'role', 'school_id', 'phone', 'telegram_id', 'is_active',
    // Yangi maydonlar:
    'notify_email', 'notify_sms', 'notify_risk_cases', 'notify_attendance',
    'language', 'timezone', 'theme', 'avatar', 'position',
];

protected $casts = [
    'email_verified_at' => 'datetime',
    'notify_email'      => 'boolean',
    'notify_sms'        => 'boolean',
    'notify_risk_cases' => 'boolean',
    'notify_attendance' => 'boolean',
];
    protected $hidden = ['password','remember_token'];
   

    // Rol yordamchilari
    public function isAdmin()        { return $this->role === 'admin'; }
    public function isTeacher()      { return $this->role === 'teacher'; }
    public function isPsychologist() { return $this->role === 'psychologist'; }
    public function isSchoolAdmin()  { return $this->role === 'school_admin'; }
    public function isParent()       { return $this->role === 'parent'; }
    public function isIio()          { return $this->role === 'iio'; }

    public function school()  { return $this->belongsTo(School::class); }
    public function children(){ return $this->hasMany(Student::class, 'parent_id'); }
    public function reportedCases(){ return $this->hasMany(RiskCase::class, 'reported_by'); }
}