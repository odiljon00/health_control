<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration {
    public function up() {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');            // Kimdan
            $table->string('email');           // Email
            $table->string('phone')->nullable(); // Telefon
            $table->string('subject')->nullable(); // Mavzu
            $table->text('message');           // Xabar matni
            $table->enum('status', ['new', 'read', 'replied', 'archived'])->default('new');
            $table->boolean('is_starred')->default(false);
            $table->text('admin_note')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('messages');
    }
}