<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSettingsToUsersTable extends Migration {
    public function up() {
        Schema::table('users', function (Blueprint $table) {
            // Bildirishnomalar
            $table->boolean('notify_email')->default(true)->after('is_active');
            $table->boolean('notify_sms')->default(true)->after('notify_email');
            $table->boolean('notify_risk_cases')->default(true)->after('notify_sms');
            $table->boolean('notify_attendance')->default(true)->after('notify_risk_cases');

            // Tizim
            $table->string('language', 5)->default('uz')->after('notify_attendance');
            $table->string('timezone', 50)->default('Asia/Tashkent')->after('language');
            $table->string('theme', 20)->default('light')->after('timezone');

            // Profil
            $table->string('avatar')->nullable()->after('theme');
            $table->string('position')->nullable()->after('avatar');   // Lavozim
        });
    }

    public function down() {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'notify_email', 'notify_sms', 'notify_risk_cases', 'notify_attendance',
                'language', 'timezone', 'theme', 'avatar', 'position'
            ]);
        });
    }
}