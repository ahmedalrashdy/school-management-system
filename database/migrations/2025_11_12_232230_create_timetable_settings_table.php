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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetable_settings');
    }
};
