<x-layouts.dashboard page-title="تعديل ولي أمر">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'أولياء الأمور', 'url' => route('dashboard.guardians.index'), 'icon' => 'fas fa-user-friends'],
            [
                'label' => 'ملف ولي الأمر',
                'url' => route('dashboard.guardians.show', $guardian),
                'icon' => 'fas fa-id-card',
            ],
            ['label' => 'تعديل ولي أمر', 'icon' => 'fas fa-edit'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="تعديل ولي أمر"
        description="تعديل بيانات ولي الأمر: {{ $guardian->user->full_name }}"
        button-text="رجوع"
        button-link="{{ route('dashboard.guardians.show', $guardian) }}"
    />

    <x-ui.card>
        <form
            method="POST"
            action="{{ route('dashboard.guardians.update', $guardian) }}"
        >
            @csrf
            @method('PUT')

            @php
                $isAccountActivated = !$guardian->user->reset_password_required;
            @endphp

            @if ($isAccountActivated)
                <div
                    class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>ملاحظة:</strong> تم تفعيل هذا الحساب من قبل ولي الأمر. يمكنك فقط تعديل الحالة والمهنة.
                        البيانات الشخصية الأخرى (الاسم، البريد الإلكتروني، رقم الهاتف) أصبحت للقراءة فقط.
                    </p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form.input
                    name="first_name"
                    label="الاسم الأول"
                    placeholder="أدخل الاسم الأول"
                    value="{{ old('first_name', $guardian->user->first_name) }}"
                    :readonly="$isAccountActivated"
                    required
                />

                <x-form.input
                    name="last_name"
                    label="اسم العائلة"
                    placeholder="أدخل اسم العائلة"
                    value="{{ old('last_name', $guardian->user->last_name) }}"
                    :readonly="$isAccountActivated"
                    required
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form.select
                    name="gender"
                    label="الجنس"
                    :options="$genders"
                    :selected="old('gender', $guardian->user->gender->value)"
                    placeholder="اختر الجنس"
                    :disabled="$isAccountActivated"
                    required
                />

                <x-form.input
                    name="occupation"
                    label="المهنة"
                    placeholder="أدخل المهنة (اختياري)"
                    value="{{ old('occupation', $guardian->occupation) }}"
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form.input
                    name="email"
                    type="email"
                    label="البريد الإلكتروني"
                    placeholder="example@email.com"
                    value="{{ old('email', $guardian->user->email) }}"
                    icon="fas fa-envelope"
                    :readonly="$isAccountActivated"
                />

                <x-form.input
                    name="phone_number"
                    type="tel"
                    label="رقم الهاتف"
                    placeholder="05xxxxxxxx"
                    value="{{ old('phone_number', $guardian->user->phone_number) }}"
                    icon="fas fa-phone"
                    :readonly="$isAccountActivated"
                />
            </div>

            <div class="mt-4">
                <x-form.checkbox
                    name="is_active"
                    label="حساب نشط"
                    :checked="old('is_active', $guardian->user->is_active)"
                />
            </div>

            <div class="mt-6 flex items-center gap-4">
                <x-ui.button
                    type="submit"
                    variant="primary"
                >
                    <i class="fas fa-save mr-2"></i>
                    حفظ التغييرات
                </x-ui.button>
                <x-ui.button
                    as="a"
                    href="{{ route('dashboard.guardians.show', $guardian) }}"
                    variant="outline"
                >
                    إلغاء
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.dashboard>
