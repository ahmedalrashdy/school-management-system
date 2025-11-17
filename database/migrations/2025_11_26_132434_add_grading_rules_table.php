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
        Schema::create('grading_rules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();

            //درجة اعمال الفصل
            $table->integer('coursework_max_marks');
            //درجة اختبار نهائة الفصل
            $table->integer('final_exam_max_marks');
            $table->integer('passed_mark');
            $table->integer('total_marks');
            $table->foreignId('final_exam_id')->nullable()->constrained('exams')->nullOnDelete();
            $table->foreignId(column: 'curriculum_subject_id')->index()->constrained('curriculum_subject')
                ->cascadeOnDelete();
            $table->boolean('is_published')->default(false);

            $table->timestamps();

            $table->unique(['section_id', 'curriculum_subject_id']);
        });
        //تفصيل توزيع اعمال الفصل على الدرجة النهائية
        Schema::create('grading_rule_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grading_rule_id')->constrained('grading_rules')->cascadeOnDelete();
            $table->foreignId('exam_id')->index()->constrained('exams')->cascadeOnDelete();
            $table->decimal('weight', 5, 2);

            $table->timestamps();

            // لا يمكن تكرار نفس الاختبار في نفس القاعدة
            $table->unique(['grading_rule_id', 'exam_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grading_rule_items');
        Schema::dropIfExists('grading_rules');
    }
};
