<div>
    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
        <i class="fas fa-book mr-2"></i>
        التسجيل الأكاديمي
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-form.select name="studentEnrollment[academic_year_id]" label="السنة الدراسية"
            wire:model="studentEnrollment.academic_year_id" :options="lookup()->getActiveAndUpcomingYearsOnly()"
            required placeholder="اختر السنة الدراسية" />

        <x-form.select name="studentEnrollment[grade_id]" label="الصف الدراسي" wire:model="studentEnrollment.grade_id"
            :options="lookup()->getGrades()" required placeholder="اختر الصف الدراسي" />
    </div>
</div>