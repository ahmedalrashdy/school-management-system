<x-layouts.dashboard page-title="تعديل بيانات الطالب">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'الطلاب', 'url' => route('dashboard.students.index'), 'icon' => 'fas fa-user-graduate'],
            ['label' => 'ملف الطالب', 'url' => route('dashboard.students.show', $student), 'icon' => 'fas fa-id-card'],
            ['label' => 'تعديل بيانات الطالب', 'icon' => 'fas fa-edit'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="تعديل بيانات الطالب"
        description="تعديل بيانات الطالب: {{ $student->user->full_name }}"
        button-text="رجوع"
        button-link="{{ route('dashboard.students.show', $student) }}"
    />

    <x-ui.card>
        <form
            method="POST"
            action="{{ route('dashboard.students.update', $student) }}"
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
                    <x-form.input
                        name="first_name"
                        label="الاسم الأول"
                        placeholder="أدخل الاسم الأول"
                        value="{{ $student->user->first_name }}"
                        required
                    />

                    <x-form.input
                        name="last_name"
                        label="اسم العائلة"
                        placeholder="أدخل اسم العائلة"
                        value="{{ $student->user->last_name }}"
                        required
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <x-form.select
                        name="gender"
                        label="الجنس"
                        :options="$genders"
                        :selected="$student->user->gender->value"
                        placeholder="اختر الجنس"
                        required
                    />

                    <x-form.input
                        name="date_of_birth"
                        type="date"
                        label="تاريخ الميلاد"
                        :value="$student->date_of_birth->format('Y-m-d')"
                        required
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            رقم القيد
                        </label>
                        <div
                            class="mt-1 px-3 py-2 block w-full rounded-lg border-gray-300 bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                            {{ $student->admission_number }}
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">رقم القيد لا يمكن تعديله</p>
                    </div>

                    <x-form.input
                        name="city"
                        label="المدينة"
                        placeholder="أدخل المدينة"
                        value="{{ $student->city }}"
                        required
                    />
                </div>

                <div class="mt-4">
                    <x-form.input
                        name="district"
                        label="المنطقة"
                        placeholder="أدخل المنطقة"
                        value="{{ $student->district }}"
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-form.input
                        name="email"
                        type="email"
                        label="البريد الإلكتروني"
                        placeholder="example@email.com"
                        value="{{ $student->user->email }}"
                        icon="fas fa-envelope"
                    />

                    <x-form.input
                        name="phone_number"
                        type="tel"
                        label="رقم الهاتف"
                        placeholder="05xxxxxxxx"
                        value="{{ $student->user->phone_number }}"
                        icon="fas fa-phone"
                    />
                </div>

                <div class="mt-4">
                    <x-form.input
                        name="address"
                        label="العنوان"
                        placeholder="أدخل العنوان الكامل"
                        value="{{ $student->user->address }}"
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
                    href="{{ route('dashboard.students.show', $student) }}"
                    variant="outline"
                >
                    إلغاء
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.dashboard>
