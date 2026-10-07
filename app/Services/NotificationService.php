<?php
namespace App\Services;

use App\Mail\AbsenceAlertMail;
use App\Attendance;
use App\RiskCase;
use App\Student;
use App\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService {
    protected $sms;
    protected $iioPhone;

    public function __construct(SmsService $sms) {
        $this->sms = $sms;
        $this->iioPhone = config('services.iio.phone', '+998993554567');
    }

    /**
     * 3+ kun kelmaganда asosiy xabar yuborish
     *
     * @param Student $student O'quvchi
     * @param int $consecutiveDays Ketma-ket kelmagan kunlar
     * @param int $level Darajasi (3, 4, 5)
     */
    public function notifyAbsenceAlert(Student $student, int $consecutiveDays, int $level = 3) {
        $sent = 0;

        // ==================== OTA-ONAGA SMS ====================
        $parent = $student->parent;
        if ($parent) {
            // SMS
            if ($parent->phone) {
                $message = SmsTemplates::absenceAlert($student, $consecutiveDays);
                if ($this->sms->send($parent->phone, $message)) {
                    $sent++;
                    Log::info("📱 Ota-onaga SMS yuborildi: {$parent->phone} ({$consecutiveDays} kun)");
                }
            }
            
            // Email
            if ($parent->email) {
                if ($this->sendEmail($parent, $student, $consecutiveDays)) {
                    $sent++;
                }
            }
        } else {
            Log::warning("⚠️ Ota-ona topilmadi: {$student->full_name}");
        }

        // ==================== 4-5 KUN: IIO GA SMS ====================
        if ($consecutiveDays >= 4) {
            $iioSent = $this->notifyIIO($student, $consecutiveDays);
            if ($iioSent) $sent++;
        }

        // ==================== SINF RAHBARIGA EMAIL ====================
        $teacher = User::where('school_id', $student->school_id)
            ->where('role', 'teacher')
            ->whereNotNull('email')
            ->first();

        if ($teacher) {
            if ($this->sendEmail($teacher, $student, $consecutiveDays)) {
                $sent++;
            }
        }

        // ==================== PSIXOLOGGA EMAIL ====================
        $psychologist = User::where('school_id', $student->school_id)
            ->where('role', 'psychologist')
            ->whereNotNull('email')
            ->first();

        if ($psychologist) {
            if ($this->sendEmail($psychologist, $student, $consecutiveDays)) {
                $sent++;
            }
        }

        Log::info("📧 {$student->full_name} uchun {$sent} ta xabar yuborildi");

        return $sent;
    }

    /**
     * IIO ga SMS yuborish (4+ kun kelmaganда)
     */
    protected function notifyIIO(Student $student, int $consecutiveDays): bool {
        if (!$this->iioPhone) {
            Log::warning("⚠️ IIO telefoni sozlanmagan");
            return false;
        }

        $message = "DIQQAT! IIO profilaktika xizmati. {$student->full_name} ({$student->class_name} sinf, " .
                   "{$student->school->name}) {$consecutiveDays} kun ketma-ket darsga kelmadi. " .
                   "Ota-ona bilan aloqa o'rnatilmadi. SOG'LOM AVLOD";

        if ($this->sms->send($this->iioPhone, $message)) {
            Log::info("🚨 IIO ga SMS yuborildi: {$this->iioPhone} ({$consecutiveDays} kun)");
            return true;
        }

        return false;
    }

    /**
     * RiskCase uchun xabarlar
     */
    public function notifyStakeholders(RiskCase $case) {
        $student = $case->student;
        $consecutive = $student->consecutiveAbsences();

        if ($case->notify_parent && $student->parent) {
            $this->notifyAbsenceAlert($student, $consecutive, $consecutive);
        }

        if ($case->notify_iio && $case->risk_level === 'red') {
            $this->notifyIIO($student, $consecutive);
        }
    }

    /**
     * Email yuborish
     */
    protected function sendEmail(User $recipient, Student $student, int $consecutiveDays): bool {
        if (!$recipient->email) return false;

        $attendance = Attendance::where('student_id', $student->id)
            ->whereDate('attendance_date', now())
            ->first();

        try {
            Mail::to($recipient->email)->send(
                new AbsenceAlertMail($student, $recipient, $consecutiveDays, $attendance)
            );
            Log::info("✅ Email: {$recipient->email}");
            return true;
        } catch (\Exception $e) {
            Log::error("❌ Email xato ({$recipient->email}): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Yangi o'quvchi qo'shilganda
     */
    public function notifyNewStudent(Student $student) {
        $parent = $student->parent;
        if ($parent && $parent->phone) {
            $this->sms->send($parent->phone, SmsTemplates::welcomeParent($student));
        }
    }
}