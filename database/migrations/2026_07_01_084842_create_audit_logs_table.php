<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action', 50);             // create, update, delete
            $table->string('model_type', 100);        // App\Models\Kriteria, dll
            $table->unsignedBigInteger('model_id')->nullable();
            $table->unsignedBigInteger('id_user')->nullable(); // pelaku (FK ke users)
            $table->string('description', 255)->nullable(); // "Ubah kriteria C1"
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['model_type', 'model_id']);
            $table->index('id_user');
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
