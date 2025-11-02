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
        Schema::create('curriculum_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_id')->constrained('curriculums')->cascadeOnDelete();
            $table->foreignId('subject_id')->index()->constrained()->cascadeOnDelete();
            $table->timestamps();

            // قيد فريد لضمان عدم تكرار المادة في نفس المنهج
            $table->unique(['curriculum_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curriculum_subject');
    }
};
