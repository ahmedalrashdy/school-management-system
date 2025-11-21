<?php

use App\Enums\DayPartEnum;
use App\Enums\SchoolDayType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->index();
            $table->string('last_name')->index();
            $table->string('email')->unique()->nullable();
            $table->string('phone_number')->unique()->nullable();
            $table->unsignedTinyInteger('gender');
            $table->text('address')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_admin')->default(false);
            $table->boolean('reset_password_required')->default(false);
            $table->rememberToken();
            $table->string('avatar', 2048)->nullable();
            $table->timestamps();
            $table->softDeletes();
            DB::statement('ALTER TABLE users ADD CONSTRAINT chk_email_or_phone CHECK (email IS NOT NULL OR phone_number IS NOT NULL)');
        });
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedTinyInteger('status');
            $table->timestamps();
        });
        Schema::create('academic_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
        Schema::create('stages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stage_id')->constrained('stages')->restrictOnDelete();
            $table->string('name');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->restrictOnDelete();
            $table->foreignId('grade_id')->constrained('grades')->restrictOnDelete();
            $table->string('name');
            $table->integer('capacity')->nullable();
            $table->foreignId('academic_term_id')->constrained();
            $table->timestamps();
            $table->unique(['academic_year_id', 'grade_id', 'academic_term_id', 'name']);
        });
        Schema::create('section_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')
                ->restrictOnDelete();
            $table->foreignId('section_id')->constrained('sections')
                ->restrictOnDelete();
            $table->unique(['student_id', 'section_id']);
            $table->timestamps();
        });
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
        Schema::create('curriculums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
            $table->foreignId('grade_id')->constrained()->restrictOnDelete();
            $table->foreignId('academic_term_id')->constrained()->restrictOnDelete();
            $table->timestamps();
            $table->unique(['academic_year_id', 'grade_id', 'academic_term_id']);
        });
        Schema::create('curriculum_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_id')->constrained('curriculums')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['curriculum_id', 'subject_id']);
        });
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('admission_number')->unique()->comment('رقم القيد');
            $table->date('date_of_birth');
            $table->string('city');
            $table->string('district');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('occupation')->nullable()->comment('المهنة');
            $table->timestamps();
        });
        Schema::create('guardian_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guardian_id')->constrained('guardians')->restrictOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->unsignedTinyInteger('relation_to_student')->comment('صلة القرابة');
            $table->timestamps();

            $table->unique(['guardian_id', 'student_id']);
        });
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')
                ->restrictOnDelete();
            $table->foreignId('grade_id')->constrained('grades')
                ->restrictOnDelete();
            $table->timestamps();
            $table->unique(['student_id', 'academic_year_id']);
        });

        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->date('date_of_birth');

            // Academic Information
            $table->string('specialization');
            $table->string('qualification');
            $table->timestamps();
        });
        Schema::create('exam_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')
                ->restrictOnDelete();
            $table->foreignId('exam_type_id')->constrained('exam_types')
                ->restrictOnDelete();
            $table->foreignId('curriculum_subject_id')->constrained('curriculum_subject')
                ->restrictOnDelete();
            $table->foreignId('section_id')->nullable()
                ->constrained('sections')->restrictOnDelete();
            $table->foreignId('academic_term_id')
                ->constrained()->restrictOnDelete();
            $table->date('exam_date');
            $table->integer('max_marks');
            $table->boolean('is_final')->default(false);
            $table->timestamps();
        });

        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->restrictOnDelete();
            $table->foreignId('exam_id')->constrained('exams')->restrictOnDelete();
            $table->decimal('marks_obtained', 5, 2); // الدرجة التي حصل عليها الطالب
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'exam_id']);
        });

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

            // -- القيد الحاسم لاتساق البيانات --
            // هذا القيد المركب سيكون "الهدف" للمفتاح الخارجي في جدول timetables.
            // يجمع المفتاح الأساسي مع الأعمدة التي سيتم تكرارها.
            $table->unique(['id', 'teacher_id', 'section_id'], 'assignment_composite_target_unique');
        });

        Schema::create('timetable_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();

            // لتحديد ما إذا كانت هذه الإعدادات هي النشطة حالياً
            $table->boolean('is_active')->default(false)->index();

            // حقل JSON لتحديد عدد الحصص في كل يوم دراسي
            $table->json('periods_per_day')->comment("مثال: {'sunday': 7, 'monday': 7, 'tuesday': 6}");

            // وقت بدء أول حصة في اليوم
            $table->time('first_period_start_time')->comment('متى تبدأ أول حصة');

            // المدة الافتراضية لكل حصة بالدقائق
            $table->unsignedSmallInteger('default_period_duration_minutes')->comment('طول الحصة الافتراضي بالدقائق');

            // عدد الحصص المتتالية قبل فترة الراحة الأساسية
            $table->unsignedTinyInteger('periods_before_break')->comment('عدد الحصص قبل بدء الراحة');

            // مدة فترة الراحة بالدقائق
            $table->unsignedSmallInteger('break_duration_minutes')->comment('مدة الراحة بالدقائق');

            $table->timestamps();
        });

        Schema::create('timetables', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->foreignId('section_id')->constrained('sections')->restrictOnDelete();
            $table->foreignId('timetable_setting_id')->constrained('timetable_settings')->restrictOnDelete();
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->unique(['section_id', 'is_active']); // (section 1,true),(section 1 ,false)
        });

        Schema::create('timetable_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('timetable_id')->constrained('timetables')->cascadeOnDelete();
            $table->foreignId('teacher_assignment_id')->constrained('teacher_assignments')->restrictOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // الأحد, الاثنين...
            $table->unsignedTinyInteger('period_number'); // الحصة 1, 2, 3...
            $table->unsignedTinyInteger('duration_minutes'); // عدد دقائق الحصة
            $table->timestamps();
            $table->unique(['timetable_id', 'day_of_week', 'period_number'], 'unique_slot_in_timetable');
        });
        Schema::create('school_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('academic_term_id')->nullable()->constrained('academic_terms')->cascadeOnDelete();
            $table->date('date');
            // الحالة العامة (يوم دراسي، إجازة، الخ)
            $table->unsignedTinyInteger('status')->default(SchoolDayType::SchoolDay->value);
            // 1 = FULL_DAY (اليوم كامل)
            // 2 = PART_ONE_ONLY (الجزء الأول فقط - قبل الفسحة)
            // 3 = PART_TWO_ONLY (الجزء الثاني فقط - بعد الفسحة - نادر الحدوث)
            $table->unsignedTinyInteger('day_part')->default(DayPartEnum::FULL_DAY);

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['academic_year_id', 'date']);
        });

        Schema::create('attendance_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_day_id')->constrained('school_days')->restrictOnDelete();
            $table->foreignId('section_id')->constrained('sections')->restrictOnDelete();

            $table->unsignedTinyInteger('day_part')->nullable();
            $table->foreignId('timetable_slot_id')->nullable()
                ->constrained('timetable_slots')->restrictOnDelete();

            // --- من قام بالتحضير؟ ---
            $table->foreignId('taken_by')->constrained('users'); // المعلم أو المشرف الذي أنشأ السجل

            // --- من قام بآخر تعديل؟ ---
            // مفيد إذا قام مشرف بتعديل التحضير بعد المعلم
            $table->foreignId('updated_by')->nullable()->constrained('users')
                ->restrictOnDelete();

            $table->timestamp('locked_at')->nullable();

            $table->timestamps();

            $table->unique(['school_day_id', 'section_id', 'timetable_slot_id'], 'unique_attendance_slot_sheet');
            $table->unique(['school_day_id', 'section_id', 'day_part'], 'unique_attendance_sheet');
        });
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_sheet_id')->constrained('attendance_sheets')
                ->restrictOnDelete();

            $table->foreignId('student_id')->constrained('students')->restrictOnDelete();

            $table->unsignedTinyInteger('status');
            $table->text('notes')->nullable();

            // لكن يمكن إضافة updated_by هنا إذا أردنا معرفة من عدل حالة هذا الطالب "تحديداً" لاحقاً
            $table->foreignId('modified_by')->nullable()->constrained('users');

            $table->timestamps();

            $table->unique(['attendance_sheet_id', 'student_id']);
        });

        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            // used in forms

            $table->string('key')->unique();

            $table->jsonb('value')->nullable();

            // value type (string, integer, boolean, json)
            $table->string('type')->default('string');

            // setting groups for display(general, system, academic, uploads)
            $table->string('group')->default('general')->index();

            $table->string('label');

            $table->timestamps();
        });

        // 1. الجدول الرئيسي: قاعدة الاحتساب للشعبة والترم
        Schema::create('grading_rules', function (Blueprint $table) {
            $table->id();

            // تحديد النطاق (لمن هذه القاعدة؟)
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();

            // توزيع الدرجات (السقف الأعلى)
            // مثال: أعمال الفصل 20 درجة
            $table->integer('coursework_max_marks');
            // مثال: النهائي 30 درجة (المجموع 50)
            $table->integer('final_exam_max_marks');
            $table->integer('passed_mark');
            $table->integer('total_marks');
            $table->foreignId('final_exam_id')->nullable()->constrained('exams')->nullOnDelete();
            $table->foreignId('curriculum_subject_id')->constrained('curriculum_subject')
                ->cascadeOnDelete();
            $table->boolean('is_published')->default(false);

            $table->timestamps();

            $table->unique(['section_id', 'curriculum_subject_id']);
        });
        // 2. الجدول التفصيلي: مكونات أعمال الفصل
        Schema::create('grading_rule_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grading_rule_id')->constrained('grading_rules')->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
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
        Schema::dropIfExists('subjects');
    }
};
