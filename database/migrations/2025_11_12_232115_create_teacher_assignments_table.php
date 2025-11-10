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
        Schema::create('teacher_assignments', function (Blueprint $table) {
            $table->id();

            // يحدد المادة ضمن سياق المنهج الدراسي الصحيح.
            $table->foreignId('curriculum_subject_id')
                ->constrained('curriculum_subject')->restrictOnDelete();

            $table->foreignId('teacher_id')->constrained('teachers')->restrictOnDelete();
            $table->foreignId('section_id')->constrained('sections')->restrictOnDelete();
            $table->timestamps();

            // القيد الأساسي: لا يمكن تعيين نفس المدرس لنفس المادة في نفس الشعبة.
            $table->unique(['curriculum_subject_id', 'section_id', 'teacher_id'], 'teacher_assignment_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_assignments');
    }
};
