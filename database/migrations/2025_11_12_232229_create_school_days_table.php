<?php

use App\Enums\DayPartEnum;
use App\Enums\SchoolDayType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('school_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('academic_term_id')->index()->nullable()->constrained('academic_terms')->cascadeOnDelete();
            $table->date('date')->index();
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_days');
    }
};
