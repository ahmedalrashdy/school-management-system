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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_sheet_id')->constrained('attendance_sheets')
                ->restrictOnDelete();

            $table->foreignId('student_id')->index()->constrained('students')->restrictOnDelete();

            $table->unsignedTinyInteger('status');
            $table->text('notes')->nullable();

            $table->foreignId('modified_by')->nullable()->constrained('users');

            $table->timestamps();

            $table->unique(['attendance_sheet_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
