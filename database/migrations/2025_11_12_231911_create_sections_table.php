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
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->restrictOnDelete();
            $table->foreignId('grade_id')->index()->constrained('grades')->restrictOnDelete();
            $table->string('name');
            $table->integer('capacity')->nullable();
            $table->foreignId('academic_term_id')->index()->constrained();
            $table->timestamps();
            $table->unique(['academic_year_id', 'grade_id', 'academic_term_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
