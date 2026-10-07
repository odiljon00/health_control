<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration {
    public function up() {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('marked_by'); // kim belgiladi (teacher)
            $table->date('attendance_date');         // kun
            // status: present (keldi), absent (kelmadi), late (kechikdi), excused (sababli)
            $table->enum('status', ['present','absent','late','excused'])->default('present');
            // absent sababi: illness, family, unknown, other
            $table->string('reason')->nullable();
            $table->text('note')->nullable();
            // Agar o'quvchi 3+ kun kelmasa — signal
            $table->boolean('is_alert')->default(false);
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('marked_by')->references('id')->on('users')->onDelete('cascade');

            // Bir kunda bir o'quvchi uchun faqat bitta yozuv
            $table->unique(['student_id','attendance_date']);
            $table->index(['school_id','attendance_date']);
        });
    }
    public function down() { Schema::dropIfExists('attendances'); }
}