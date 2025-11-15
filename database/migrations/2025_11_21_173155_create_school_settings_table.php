<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_settings');
    }
};
