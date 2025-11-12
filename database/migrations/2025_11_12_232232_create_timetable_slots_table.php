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
        Schema::create('timetable_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('timetable_id')->constrained('timetables')->cascadeOnDelete();
            $table->foreignId('teacher_assignment_id')->index()->constrained('teacher_assignments')->restrictOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // الأحد, الاثنين...
            $table->unsignedTinyInteger('period_number'); // الحصة 1, 2, 3...
            $table->unsignedTinyInteger('duration_minutes'); // عدد دقائق الحصة
            $table->timestamps();
            $table->unique(['timetable_id', 'day_of_week', 'period_number'], 'unique_slot_in_timetable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetable_slots');
    }
};
