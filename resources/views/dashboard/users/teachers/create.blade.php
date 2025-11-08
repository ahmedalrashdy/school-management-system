<x-layouts.dashboard page-title="إضافة مدرس جديد">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'المدرسون', 'url' => route('dashboard.teachers.index'), 'icon' => 'fas fa-chalkboard-teacher'],
            ['label' => 'إضافة مدرس جديد', 'icon' => 'fas fa-plus'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="إضافة مدرس جديد"
        description="إضافة مدرس جديد إلى النظام"
        button-text="رجوع"
        button-link="{{ route('dashboard.teachers.index') }}"
    />

    <x-ui.card>
        <form
            method="POST"
            action="{{ route('dashboard.teachers.store') }}"
        >
            @csrf

            <!-- البيانات الشخصية -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-user-circle mr-2"></i>
                    البيانات الشخصية
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-form.input
                        name="first_name"
                        label="الاسم الأول"
                        placeholder="أدخل الاسم الأول"
                        required
                    />

                    <x-form.input
                        name="last_name"
                        label="اسم العائلة"
                        placeholder="أدخل اسم العائلة"
                        required
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <x-form.select
                        name="gender"
                        label="الجنس"
                        :options="$genders"
                        placeholder="اختر الجنس"
                        required
                    />

                    <x-form.input
                        name="date_of_birth"
                        type="date"
                        label="تاريخ الميلاد"
                        required
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <x-form.input
                        name="email"
                        type="email"
                        label="البريد الإلكتروني"
                        placeholder="example@email.com"
                        icon="fas fa-envelope"
                    />

                    <x-form.input
                        name="phone_number"
                        type="tel"
                        label="رقم الهاتف"
                        placeholder="05xxxxxxxx"
                        icon="fas fa-phone"
                    />
                </div>

                <div class="mt-4">
                    <x-form.input
                        name="address"
                        label="العنوان"
                        placeholder="أدخل العنوان الكامل"
                    />
                </div>
            </div>

            <!-- البيانات الأكاديمية -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-graduation-cap mr-2"></i>
                    البيانات الأكاديمية
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-form.input
                        name="specialization"
                        label="التخصص"
                        placeholder="مثال: لغة عربية"
                        required
                    />

                    <x-form.select
                        name="qualification"
                        label="المؤهل العلمي"
                        :options="\App\Enums\AcademicQualificationEnum::options()"
                        required
                    />
                </div>
            </div>
            <div class="mt-6 flex items-center gap-4">
                <x-ui.button
                    type="submit"
                    variant="primary"
                >
                    <i class="fas fa-save mr-2"></i>
                    حفظ المدرس
                </x-ui.button>
                <x-ui.button
                    as="a"
                    href="{{ route('dashboard.teachers.index') }}"
                    variant="outline"
                >
                    إلغاء
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.dashboard>
