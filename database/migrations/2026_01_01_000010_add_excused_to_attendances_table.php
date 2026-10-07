<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExcusedToAttendancesTable extends Migration {
    public function up() {
        Schema::table('attendances', function (Blueprint $table) {
            $table->boolean('is_excused')->default(false)->after('status');
        });
    }
    public function down() {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('is_excused');
        });
    }
}