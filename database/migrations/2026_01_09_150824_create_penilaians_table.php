<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id('id_penilaian');
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
            $table->foreignId('id_alternatif')->constrained('alternatif', 'id_alternatif')->onDelete('cascade');
            $table->foreignId('id_kriteria')->constrained('kriteria', 'id_kriteria')->onDelete('cascade');
            $table->foreignId('id_pertanyaan')->constrained('pertanyaan', 'id_pertanyaan')->onDelete('cascade');
            $table->foreignId('id_subkriteria')->constrained('subkriteria', 'id_subkriteria')->onDelete('cascade');
            $table->decimal('jawaban', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian');
    }
};
