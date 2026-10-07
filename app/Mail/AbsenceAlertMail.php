<?php
namespace App\Mail;

use App\Attendance;
use App\Student;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AbsenceAlertMail extends Mailable {
    use Queueable, SerializesModels;

    public $student;
    public $parent;
    public $consecutiveDays;
    public $attendance;
    public $schoolName;

    public function __construct(
        Student $student,
        User $parent,
        int $consecutiveDays,
        ?Attendance $attendance = null
    ) {
        $this->student         = $student;
        $this->parent          = $parent;
        $this->consecutiveDays = $consecutiveDays;
        $this->attendance      = $attendance;
        $this->schoolName      = $student->school->name ?? 'Maktab';
    }

    public function build() {
        return $this
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->subject("🚨 Ogohlantirish: {$this->student->full_name} — {$this->consecutiveDays} kun kelmadi")
            ->view('emails.absence-alert');
    }
}