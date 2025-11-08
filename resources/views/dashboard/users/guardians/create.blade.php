<x-layouts.dashboard page-title="إضافة ولي أمر جديد">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'أولياء الأمور', 'url' => route('dashboard.guardians.index'), 'icon' => 'fas fa-user-friends'],
            ['label' => 'إضافة ولي أمر جديد', 'icon' => 'fas fa-plus'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="إضافة ولي أمر جديد"
        description="إنشاء ولي أمر جديد في النظام"
        button-text="رجوع"
        button-link="{{ route('dashboard.guardians.index') }}"
    />

    <x-ui.card>
        <form
            method="POST"
            action="{{ route('dashboard.guardians.store') }}"
        >
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form.input
                    name="first_name"
                    label="الاسم الأول"
                    placeholder="أدخل الاسم الأول"
                    value="{{ old('first_name') }}"
                    required
                />

                <x-form.input
                    name="last_name"
                    label="اسم العائلة"
                    placeholder="أدخل اسم العائلة"
                    value="{{ old('last_name') }}"
                    required
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form.select
                    name="gender"
                    label="الجنس"
                    :options="$genders"
                    placeholder="اختر الجنس"
                    required
                />

                <x-form.input
                    name="occupation"
                    label="المهنة"
                    placeholder="أدخل المهنة (اختياري)"
                    value="{{ old('occupation') }}"
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form.input
                    name="email"
                    type="email"
                    label="البريد الإلكتروني"
                    placeholder="example@email.com"
                    value="{{ old('email') }}"
                    icon="fas fa-envelope"
                />

                <x-form.input
                    name="phone_number"
                    type="tel"
                    label="رقم الهاتف"
                    placeholder="05xxxxxxxx"
                    value="{{ old('phone_number') }}"
                    icon="fas fa-phone"
                />
            </div>
            <div class="mt-6 flex items-center gap-4">
                <x-ui.button
                    type="submit"
                    variant="primary"
                >
                    <i class="fas fa-save mr-2"></i>
                    حفظ
                </x-ui.button>
                <x-ui.button
                    as="a"
                    href="{{ route('dashboard.guardians.index') }}"
                    variant="outline"
                >
                    إلغاء
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.dashboard>
