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
        Schema::create('attendance_sheets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_day_id')->constrained('school_days')->restrictOnDelete();
            $table->foreignId('section_id')->index()->constrained('sections')->restrictOnDelete();

            $table->unsignedTinyInteger('day_part')->nullable();
            $table->foreignId('timetable_slot_id')->index()->nullable()
                ->constrained('timetable_slots')->restrictOnDelete();

            // --- من قام بالتحضير؟ ---
            $table->foreignId('taken_by')->index()->constrained('users');

            // --- من قام بآخر تعديل؟ ---
            $table->foreignId('updated_by')->index()->nullable()->constrained('users')
                ->restrictOnDelete();

            $table->timestamp('locked_at')->nullable();

            $table->timestamps();

            $table->unique(['school_day_id', 'section_id', 'timetable_slot_id'], 'unique_attendance_slot_sheet');
            $table->unique(['school_day_id', 'section_id', 'day_part'], 'unique_attendance_sheet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_sheets');
    }
};
