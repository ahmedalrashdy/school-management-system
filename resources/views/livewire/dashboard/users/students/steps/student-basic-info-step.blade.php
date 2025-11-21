<div>
    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
        <i class="fas fa-user mr-2"></i>
        البيانات الأساسية للطالب
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-form.input
            name="studentBasicInfo[first_name]"
            label="الاسم الأول"
            wire:model="studentBasicInfo.first_name"
            placeholder="ادخل الاسم الأول"
            required
        />

        <x-form.input
            name="studentBasicInfo[last_name]"
            label="اسم العائلة"
            wire:model="studentBasicInfo.last_name"
            placeholder="ادخل اسم العائلة"
            required
        />

        <x-form.select
            name="studentBasicInfo[gender]"
            label="الجنس"
            wire:model="studentBasicInfo.gender"
            :options="$genders"
            required
            placeholder="اختر الجنس"
        />

        <x-form.input
            name="studentBasicInfo[phone_number]"
            type="tel"
            label="رقم الهاتف"
            wire:model="studentBasicInfo.phone_number"
            placeholder="ادخل رقم الهاتف"
        />

        <x-form.input
            name="studentBasicInfo[email]"
            type="email"
            label="البريد الإلكتروني"
            wire:model="studentBasicInfo.email"
            placeholder="ادخل البريد الإلكتروني"
        />

        <div class="md:col-span-2">
            <x-form.textarea
                name="studentBasicInfo[address]"
                label="العنوان"
                wire:model="studentBasicInfo.address"
                placeholder="ادخل العنوان بالتفصيل"
                rows="3"
            />
        </div>
    </div>
</div>
