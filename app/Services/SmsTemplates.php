<?php
namespace App\Services;

use App\Student;

class SmsTemplates {

    /**
     * Ota-onaga — 3+ kun kelmaganlik
     */
    public static function absenceAlert(Student $student, int $days): string {
        if ($days >= 5) {
            return "DIQQAT! Farzandingiz {$student->full_name} ({$student->class_name}) {$days} kun ketma-ket darsga kelmadi. " .
                   "Holat nazoratga olindi. Zudlik bilan maktab bilan bog'laning! SOG'LOM AVLOD";
        }

        if ($days >= 4) {
            return "OGOHLANTIRISH! Farzandingiz {$student->full_name} ({$student->class_name}) {$days} kun ketma-ket darsga kelmadi. " .
                   "Iltimos, zudlik bilan maktab bilan bog'laning! SOG'LOM AVLOD";
        }

        return "Hurmatli ota-ona! Farzandingiz {$student->full_name} ({$student->class_name} sinf) " .
               "{$days} kun ketma-ket darsga kelmadi. Iltimos, maktab bilan bog'laning. SOG'LOM AVLOD";
    }

    /**
     * IIO ga — 4+ kun kelmaganlik
     */
    public static function iioAlert(Student $student, int $days): string {
        return "DIQQAT! IIO profilaktika. {$student->full_name} ({$student->class_name} sinf, {$student->school->name}) " .
               "{$days} kun ketma-ket darsga kelmadi. Ota-ona bilan aloqa o'rnatilmadi. SOG'LOM AVLOD";
    }

    /**
     * Ota-onaga xush kelibsiz
     */
    public static function welcomeParent(Student $student): string {
        return "Hurmatli ota-ona! Farzandingiz {$student->full_name} SOG'LOM AVLOD tizimiga qo'shildi. " .
               "Endi siz farzandingiz davomatidan xabardor bo'lasiz.";
    }

    /**
     * Xavf holati
     */
    public static function riskAlert(Student $student, string $type): string {
        return "DIQQAT! Farzandingiz {$student->full_name} ({$student->class_name}) bo'yicha xavf: {$type}. " .
               "Zudlik bilan maktab bilan bog'laning! SOG'LOM AVLOD";
    }
}