<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiskCasesTable extends Migration {
    public function up() {
        Schema::create('risk_cases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('reported_by');
            $table->unsignedBigInteger('school_id');
            $table->string('risk_type');
            $table->integer('risk_score')->default(0);
            $table->string('risk_level')->default('green');
            $table->text('description')->nullable();
            $table->enum('status', ['new','in_progress','improved','ongoing','severe','closed'])->default('new');
            $table->unsignedBigInteger('psychologist_id')->nullable();
            $table->text('psychologist_note')->nullable();
            $table->boolean('notify_parent')->default(true);
            $table->boolean('notify_iio')->default(false);
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('reported_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('psychologist_id')->references('id')->on('users')->onDelete('set null');
        });
    }
    public function down() { Schema::dropIfExists('risk_cases'); }
}