<x-layouts.dashboard page-title="تعديل بيانات المدرس">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'المدرسون', 'url' => route('dashboard.teachers.index'), 'icon' => 'fas fa-chalkboard-teacher'],
            ['label' => 'ملف المدرس', 'url' => route('dashboard.teachers.show', $teacher), 'icon' => 'fas fa-id-card'],
            ['label' => 'تعديل بيانات المدرس', 'icon' => 'fas fa-edit'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="تعديل بيانات المدرس"
        description="تعديل بيانات المدرس: {{ $teacher->user->full_name }}"
        button-text="رجوع"
        button-link="{{ route('dashboard.teachers.show', $teacher) }}"
    />
    <x-ui.card>
        <form
            method="POST"
            action="{{ route('dashboard.teachers.update', $teacher) }}"
        >
            @csrf
            @method('PUT')
            <!-- البيانات الرسمية -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-id-card mr-2"></i>
                    البيانات الرسمية
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if ($teacher->user->reset_password_required)
                        <x-form.input
                            name="first_name"
                            label="الاسم الأول"
                            placeholder="أدخل الاسم الأول"
                            :value="$teacher->user->first_name"
                            required
                        />

                        <x-form.input
                            name="last_name"
                            label="اسم العائلة"
                            placeholder="أدخل اسم العائلة"
                            :value="$teacher->user->last_name"
                            required
                        />
                    @else
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                الاسم الأول
                            </label>
                            <div
                                class="mt-1 px-3 py-2 block w-full rounded-lg border-gray-300 bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                                {{ $teacher->user->first_name }}
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">للقراءة فقط</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                اسم العائلة
                            </label>
                            <div
                                class="mt-1 px-3 py-2 block w-full rounded-lg border-gray-300 bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                                {{ $teacher->user->last_name }}
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">للقراءة فقط</p>
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    @if ($teacher->user->reset_password_required)
                        <x-form.select
                            name="gender"
                            label="الجنس"
                            :options="$genders"
                            :selected="$teacher->user->gender->value"
                            placeholder="اختر الجنس"
                            required
                        />
                    @else
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                الجنس
                            </label>
                            <div
                                class="mt-1 px-3 py-2 block w-full rounded-lg border-gray-300 bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                                {{ $teacher->user->gender->label() }}
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">للقراءة فقط</p>
                        </div>
                    @endif

                    <x-form.input
                        name="date_of_birth"
                        type="date"
                        label="تاريخ الميلاد"
                        :value="$teacher->date_of_birth->format('Y-m-d')"
                        required
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <x-form.input
                        name="specialization"
                        label="التخصص"
                        placeholder="مثال: لغة عربية"
                        :value="$teacher->specialization"
                        required
                    />

                    <x-form.select
                        name="qualification"
                        label="المؤهل العلمي"
                        :selected="$teacher->qualification->value"
                        :options="\App\Enums\AcademicQualificationEnum::options()"
                        required
                    />
                </div>
            </div>

            <!-- البيانات الشخصية -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-user-circle mr-2"></i>
                    البيانات الشخصية
                </h3>

                @if ($teacher->user->reset_password_required)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-form.input
                            name="email"
                            type="email"
                            label="البريد الإلكتروني"
                            placeholder="example@email.com"
                            :value="$teacher->user->email"
                            icon="fas fa-envelope"
                        />

                        <x-form.input
                            name="phone_number"
                            type="tel"
                            label="رقم الهاتف"
                            placeholder="05xxxxxxxx"
                            :value="$teacher->user->phone_number"
                            icon="fas fa-phone"
                        />
                    </div>

                    <div class="mt-4">
                        <x-form.input
                            name="address"
                            label="العنوان"
                            placeholder="أدخل العنوان الكامل"
                            :value="$teacher->user->address"
                        />
                    </div>
                @else
                    <div
                        class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-yellow-600 dark:text-yellow-400 mt-1 mr-2"></i>
                            <div class="text-sm text-yellow-800 dark:text-yellow-300">
                                <p class="font-medium mb-1">ملاحظة:</p>
                                <p>البيانات الشخصية (الاسم، البريد، الهاتف) للقراءة فقط. يجب على المدرس تعديلها من حسابه
                                    الشخصي.</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                البريد الإلكتروني
                            </label>
                            <div
                                class="mt-1 px-3 py-2 block w-full rounded-lg border-gray-300 bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                                {{ $teacher->user->email ?? 'غير محدد' }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                رقم الهاتف
                            </label>
                            <div
                                class="mt-1 px-3 py-2 block w-full rounded-lg border-gray-300 bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                                {{ $teacher->user->phone_number ?? 'غير محدد' }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            العنوان
                        </label>
                        <div
                            class="mt-1 px-3 py-2 block w-full rounded-lg border-gray-300 bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                            {{ $teacher->user->address ?? 'غير محدد' }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- حالة الحساب -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-cog mr-2"></i>
                    حالة الحساب
                </h3>

                <div class="flex items-center gap-4">
                    <x-form.checkbox
                        name="is_active"
                        label="حساب نشط"
                        :checked="$teacher->user->is_active"
                    />
                </div>
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
                    href="{{ route('dashboard.teachers.show', $teacher) }}"
                    variant="outline"
                >
                    إلغاء
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.dashboard>
