<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaseActionsTable extends Migration {
    public function up() {
        Schema::create('case_actions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('risk_case_id');
            $table->unsignedBigInteger('user_id');
            $table->string('action_type');
            $table->text('note')->nullable();
            $table->string('result')->nullable();
            $table->timestamps();

            $table->foreign('risk_case_id')->references('id')->on('risk_cases')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    public function down() { Schema::dropIfExists('case_actions'); }
}