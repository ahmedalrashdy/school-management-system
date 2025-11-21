<div>
    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
        <i class="fas fa-graduation-cap mr-2"></i>
        المعلومات الأكاديمية للطالب
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-form.input
            name="studentAcademicInfo[admission_number]"
            label="رقم القيد"
            wire:model="studentAcademicInfo.admission_number"
            placeholder="ادخل رقم القيد"
            required
        />

        <x-form.input
            name="studentAcademicInfo[date_of_birth]"
            type="date"
            label="تاريخ الميلاد"
            wire:model="studentAcademicInfo.date_of_birth"
            placeholder="اختر تاريخ الميلاد"
            required
        />

        <x-form.input
            name="studentAcademicInfo[city]"
            label="المدينة"
            wire:model="studentAcademicInfo.city"
            placeholder="ادخل المدينة"
            required
        />

        <x-form.input
            name="studentAcademicInfo[district]"
            label="الحي / المنطقة"
            wire:model="studentAcademicInfo.district"
            placeholder="ادخل الحي / المنطقة"
            required
        />
    </div>
</div>
