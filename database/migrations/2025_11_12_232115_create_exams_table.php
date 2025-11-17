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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->index()->constrained('academic_years')
                ->restrictOnDelete();
            $table->foreignId('exam_type_id')->index()->constrained('exam_types')
                ->restrictOnDelete();
            $table->foreignId('curriculum_subject_id')->index()->constrained('curriculum_subject')
                ->restrictOnDelete();
            $table->foreignId('section_id')->index()->nullable()
                ->constrained('sections')->restrictOnDelete();
            $table->foreignId('academic_term_id')->index()
                ->constrained()->restrictOnDelete();
            $table->date('exam_date');
            $table->integer('max_marks');
            $table->boolean('is_final')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
