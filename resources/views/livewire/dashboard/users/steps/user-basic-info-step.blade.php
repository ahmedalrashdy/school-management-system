<div>
    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
        <i class="fas fa-user mr-2"></i>
        البيانات الأساسية للمستخدم
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-form.input
            name="userBasicInfo[first_name]"
            label="الاسم الأول"
            wire:model="userBasicInfo.first_name"
            required
        />

        <x-form.input
            name="userBasicInfo[last_name]"
            label="اسم العائلة"
            wire:model="userBasicInfo.last_name"
            required
        />

        <x-form.select
            name="userBasicInfo[gender]"
            label="الجنس"
            wire:model="userBasicInfo.gender"
            :options="$genders"
            required
            placeholder="اختر الجنس"
        />

        <x-form.input
            name="userBasicInfo[phone_number]"
            type="tel"
            label="رقم الهاتف"
            wire:model="userBasicInfo.phone_number"
        />

        <x-form.input
            name="userBasicInfo[email]"
            type="email"
            label="البريد الإلكتروني"
            wire:model="userBasicInfo.email"
        />

        <div class="md:col-span-2">
            <x-form.textarea
                name="userBasicInfo[address]"
                label="العنوان"
                wire:model="userBasicInfo.address"
                rows="3"
            />
        </div>
    </div>

    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mt-6">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mt-1 mr-2"></i>
            <div class="text-sm text-blue-800 dark:text-blue-300">
                <p class="font-medium mb-1">ملاحظة:</p>
                <p>يجب إدخال إما رقم الهاتف أو البريد الإلكتروني على الأقل.</p>
            </div>
        </div>
    </div>
</div>
