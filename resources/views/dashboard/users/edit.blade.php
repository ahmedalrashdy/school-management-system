<x-layouts.dashboard page-title="تعديل بيانات المستخدم">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'المستخدمون', 'url' => route('dashboard.users.index'), 'icon' => 'fas fa-users'],
            ['label' => 'تعديل بيانات مستخدم', 'icon' => 'fas fa-edit'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="تعديل بيانات المستخدم"
        description="تعديل البيانات الشخصية للمستخدم"
        button-text="رجوع"
        button-link="{{ route('dashboard.users.index') }}"
    />

    @if ($isReadonly)
        <x-ui.alert
            type="warning"
            class="mb-6"
        >
            <p class="font-medium">لا يمكن تعديل بيانات هذا المستخدم</p>
            <p class="text-sm mt-1">تم تفعيل حساب المستخدم، ولا يمكن تعديل بياناته بعد التفعيل.</p>
        </x-ui.alert>
    @endif
    <x-ui.card>
        <form
            method="POST"
            action="{{ route('dashboard.users.update', $user) }}"
        >
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form.input
                    name="first_name"
                    label="الاسم الأول"
                    :value="$user->first_name"
                    :readonly="$isReadonly"
                    required
                />

                <x-form.input
                    name="last_name"
                    label="اسم العائلة"
                    :value="$user->last_name"
                    :readonly="$isReadonly"
                    required
                />

                @if ($isReadonly)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            الجنس
                            <span class="text-red-500">*</span>
                        </label>
                        <div
                            class="mt-1 px-3 py-2 block w-full rounded-lg border-gray-300 bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                            {{ $genders[$user->gender->value] }}
                        </div>
                        <input
                            type="hidden"
                            name="gender"
                            value="{{ $user->gender->value }}"
                        >
                    </div>
                @else
                    <x-form.select
                        name="gender"
                        label="الجنس"
                        :options="$genders"
                        :selected="$user->gender->value"
                        required
                    />
                @endif

                <x-form.input
                    name="email"
                    type="email"
                    :value="$user->email"
                    label="البريد الإلكتروني"
                    :readonly="$isReadonly"
                />

                <x-form.input
                    name="phone_number"
                    type="tel"
                    :value="$user->phone_number"
                    label="رقم الهاتف"
                    :readonly="$isReadonly"
                />

                <div class="md:col-span-2">
                    <x-form.textarea
                        name="address"
                        label="العنوان"
                        :value="$user->address"
                        :readonly="$isReadonly"
                        rows="3"
                    />
                </div>
            </div>

            @if (!$isReadonly)
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
                        href="{{ route('dashboard.users.index') }}"
                        variant="outline"
                    >
                        إلغاء
                    </x-ui.button>
                </div>
            @else
                <div class="mt-6">
                    <x-ui.button
                        as="a"
                        href="{{ route('dashboard.users.index') }}"
                        variant="outline"
                    >
                        رجوع
                    </x-ui.button>
                </div>
            @endif
        </form>
    </x-ui.card>
</x-layouts.dashboard>
