@php
    use App\Enums\RelationToStudentEnum;
@endphp
<x-layouts.dashboard page-title="ملف ولي الأمر">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'أولياء الأمور', 'url' => route('dashboard.guardians.index'), 'icon' => 'fas fa-user-friends'],
            ['label' => 'ملف ولي الأمر', 'icon' => 'fas fa-eye'],
        ]" />
    </x-slot>

    <div
        x-data="{}"
        @if ($errors->has('relation_to_student') || $errors->has('student_id')) x-init="$dispatch('open-modal', { name: 'attach-student-modal' })" @endif
    >


        <x-ui.main-content-header
            title="ملف ولي الأمر"
            description="عرض تفاصيل ولي الأمر: {{ $guardian->user->full_name }}"
            button-text="رجوع"
            button-link="{{ route('dashboard.guardians.index') }}"
        />

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- البيانات الشخصية -->
            <div class="lg:col-span-2 space-y-6">
                <!-- البيانات الأساسية -->
                <x-ui.card>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-user mr-2"></i>
                        البيانات الشخصية
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">الاسم
                                الكامل</label>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $guardian->user->full_name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">الجنس</label>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $guardian->user->gender->label() }}</p>
                        </div>

                        @if ($guardian->user->email)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">البريد
                                    الإلكتروني</label>
                                <p class="text-sm text-gray-900 dark:text-white">
                                    <i class="fas fa-envelope mr-1"></i>
                                    {{ $guardian->user->email }}
                                </p>
                            </div>
                        @endif

                        @if ($guardian->user->phone_number)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">رقم
                                    الهاتف</label>
                                <p class="text-sm text-gray-900 dark:text-white">
                                    <i class="fas fa-phone mr-1"></i>
                                    {{ $guardian->user->phone_number }}
                                </p>
                            </div>
                        @endif

                        @if ($guardian->occupation)
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">المهنة</label>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $guardian->occupation }}</p>
                            </div>
                        @endif
                    </div>
                </x-ui.card>

                <!-- حالة الحساب -->
                <x-ui.card>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-info-circle mr-2"></i>
                        حالة الحساب
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">الحالة</label>
                            <x-ui.badge :variant="$guardian->user->is_active ? 'success' : 'danger'">
                                {{ $guardian->user->is_active ? 'نشط' : 'غير نشط' }}
                            </x-ui.badge>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">حالة كلمة
                                المرور</label>
                            @if ($guardian->user->reset_password_required)
                                <x-ui.badge variant="warning">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    يحتاج إلى تفعيل
                                </x-ui.badge>
                            @else
                                <x-ui.badge variant="success">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    مفعّل
                                </x-ui.badge>
                            @endif
                        </div>
                    </div>
                </x-ui.card>

                <!-- الطلاب المرتبطون -->
                <x-ui.card>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            <i class="fas fa-users mr-2"></i>
                            الطلاب المرتبطون ({{ $guardian->students->count() }})
                        </h3>
                        <x-ui.button
                            type="button"
                            :permissions="\Perm::StudentsManageGuardians"
                            @click="$dispatch('open-modal', { name: 'attach-student-modal' })"
                            variant="primary"
                        >
                            <i class="fas fa-plus"></i>
                            ربط طالب
                        </x-ui.button>
                    </div>

                    @if ($guardian->students->count() > 0)
                        <x-table :headers="[
                            ['label' => 'اسم الطالب'],
                            ['label' => 'رقم القيد'],
                            ['label' => 'صلة القرابة'],
                            ['label' => 'الإجراءات'],
                        ]">
                            @foreach ($guardian->students as $student)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                    <x-table.td nowrap>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $student->user->full_name }}
                                        </div>
                                    </x-table.td>
                                    <x-table.td nowrap>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $student->admission_number }}
                                        </div>
                                    </x-table.td>
                                    <x-table.td nowrap>
                                        <x-ui.badge variant="info">
                                            {{ RelationToStudentEnum::from($student->pivot->relation_to_student)->label() }}
                                        </x-ui.badge>
                                    </x-table.td>
                                    <x-table.td nowrap>
                                        <form
                                            method="POST"
                                            action="{{ route('dashboard.guardians.detach-student', $guardian) }}"
                                            class="inline"
                                            onsubmit="return confirm('هل أنت متأكد من فك ارتباط هذا الطالب؟')"
                                        >
                                            @csrf
                                            <input
                                                type="hidden"
                                                name="student_id"
                                                value="{{ $student->id }}"
                                            >
                                            <x-table.action-delete
                                                :permissions="\Perm::StudentsManageGuardians"
                                                type="submit"
                                                title="فك الارتباط"
                                            />
                                        </form>
                                    </x-table.td>
                                </tr>
                            @endforeach
                        </x-table>
                    @else
                        <x-ui.empty-state
                            icon="fas fa-user-graduate"
                            title="لا يوجد طلاب مرتبطين بهذا ولي الأمر"
                            :padding="false"
                        />
                    @endif
                </x-ui.card>
            </div>

            <!-- الإجراءات الإدارية -->
            <div class="space-y-6">
                <x-ui.card>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-cog mr-2"></i>
                        الإجراءات
                    </h3>

                    <div class="space-y-3 flex flex-col gap-2">
                        <x-ui.button
                            as="a"
                            :permissions="\Perm::GuardiansUpdate"
                            href="{{ route('dashboard.guardians.edit', $guardian) }}"
                            variant="primary"
                            class="w-full"
                        >
                            <i class="fas fa-edit"></i>
                            تعديل البيانات
                        </x-ui.button>

                        <x-ui.button
                            type="button"
                            :permissions="\Perm::GuardiansUpdate"
                            @click="$dispatch('open-modal', {
                                name: 'toggle-guardian-active',
                                guardian: {
                                    id: {{ $guardian->id }},
                                    name: '{{ $guardian->user->full_name }}',
                                    isActive: {{ $guardian->user->is_active ? 'true' : 'false' }},
                                    route: '{{ route('dashboard.guardians.toggle-active', $guardian) }}'
                                }
                            })"
                            :variant="$guardian->user->is_active ? 'warning' : 'success'"
                            class="w-full"
                        >
                            <i class="fas {{ $guardian->user->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                            {{ $guardian->user->is_active ? 'تعطيل الحساب' : 'تفعيل الحساب' }}
                        </x-ui.button>

                        <x-ui.button
                            type="button"
                            :permissions="\Perm::GuardiansDelete"
                            @click="$dispatch('open-modal', {
                                name: 'delete-guardian',
                                guardian: {
                                    id: {{ $guardian->id }},
                                    name: '{{ $guardian->user->full_name }}',
                                    studentsCount: {{ $guardian->students->count() }},
                                    route: '{{ route('dashboard.guardians.destroy', $guardian) }}'
                                }
                            })"
                            variant="danger"
                            class="w-full {{ $guardian->students->count() > 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                            :disabled="$guardian->students->count() > 0"
                        >
                            <i class="fas fa-trash"></i>
                            حذف ولي الأمر
                        </x-ui.button>

                        @if ($guardian->students->count() > 0)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                لا يمكن حذف ولي أمر مرتبط بطالب
                            </p>
                        @endif
                    </div>
                </x-ui.card>
            </div>
        </div>

        <!-- Modal لربط طالب -->
        @if (auth()->user()->can(\Perm::StudentsManageGuardians))
            <x-ui.modal
                name="attach-student-modal"
                title="ربط طالب بولي الأمر"
            >
                <form
                    method="POST"
                    action="{{ route('dashboard.guardians.attach-student', $guardian) }}"
                >
                    @csrf

                    <x-form.autocomplete
                        name="student_id"
                        label="الطالب"
                        resource="students"
                        placeholder="ابحث عن طالب بالاسم أو رقم القيد"
                        search-placeholder="ابحث باسم الطالب أو رقم القيد"
                        required
                    />

                    <x-form.select
                        name="relation_to_student"
                        label="صلة القرابة"
                        :options="$relationOptions"
                        placeholder="اختر صلة القرابة"
                        required
                    />

                    <div class="mt-6 flex items-center gap-4">
                        <x-ui.button
                            type="submit"
                            variant="primary"
                            :permissions="\Perm::StudentsManageGuardians"
                        >
                            <i class="fas fa-save mr-2"></i>
                            حفظ
                        </x-ui.button>
                        <x-ui.button
                            type="button"
                            @click="$dispatch('close-modal', { name: 'attach-student-modal' })"
                            variant="outline"
                        >
                            إلغاء
                        </x-ui.button>
                    </div>
                </form>
            </x-ui.modal>
        @endif

        {{-- Toggle Active Confirmation Modal --}}
        <x-ui.confirm-action
            name="toggle-guardian-active"
            title="تأكيد الإجراء"
            dataKey="guardian"
            :permissions="\Perm::GuardiansUpdate"
        >
            <div x-show="guardian?.isActive">
                <div>
                    هل أنت متأكد من تعطيل حساب ولي الأمر <strong x-text="guardian?.name"></strong>؟
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        لن يتمكن ولي الأمر من تسجيل الدخول بعد التعطيل.
                    </p>
                </div>
            </div>
            <div x-show="!guardian?.isActive">
                <div>
                    هل أنت متأكد من تفعيل حساب ولي الأمر <strong x-text="guardian?.name"></strong>؟
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        سيتمكن ولي الأمر من تسجيل الدخول بعد التفعيل.
                    </p>
                </div>
            </div>
            <x-slot:actions>
                <button
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors"
                    type="button"
                    @click="guardian = null; $dispatch('close-modal', { name: 'toggle-guardian-active' })"
                >
                    إلغاء
                </button>
                <form
                    class="inline"
                    method="POST"
                    x-bind:action="guardian?.route"
                    x-show="guardian"
                >
                    @csrf
                    <button
                        class="px-4 py-2 text-sm font-medium text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors"
                        type="submit"
                        :class="guardian?.isActive ? 'bg-warning-600 hover:bg-warning-700 focus:ring-warning-500' :
                            'bg-success-600 hover:bg-success-700 focus:ring-success-500'"
                    >
                        <span x-text="guardian?.isActive ? 'تعطيل' : 'تفعيل'"></span>
                    </button>
                </form>
            </x-slot:actions>
        </x-ui.confirm-action>

        {{-- Delete Guardian Confirmation Modal --}}
        <x-ui.confirm-action
            name="delete-guardian"
            title="تأكيد حذف ولي الأمر"
            dataKey="guardian"
            spoofMethod="DELETE"
            confirmButtonText="حذف"
            confirmButtonVariant="danger"
            :permissions="\Perm::GuardiansDelete"
        >
            <p class="mb-4">هل أنت متأكد من حذف ولي الأمر <strong x-text="guardian?.name"></strong>؟</p>
            <div x-show="guardian?.studentsCount > 0">
                <x-ui.warning-box>
                    هذا ولي الأمر مرتبط بـ <strong x-text="guardian?.studentsCount"></strong> طالب. لن تنجح العملية إذا
                    كان هناك بيانات مرتبطة به.
                </x-ui.warning-box>
            </div>
            <x-slot:actions>
                <button
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors"
                    type="button"
                    @click="guardian = null; $dispatch('close-modal', { name: 'delete-guardian' })"
                >
                    إلغاء
                </button>
                <form
                    class="inline"
                    method="POST"
                    x-bind:action="guardian?.route"
                    x-show="guardian && guardian?.studentsCount === 0"
                    @submit.prevent="if (guardian?.studentsCount === 0) { $el.submit(); }"
                >
                    @csrf
                    @method('DELETE')
                    <button
                        class="px-4 py-2 text-sm font-medium text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors bg-danger-600 hover:bg-danger-700 focus:ring-danger-500"
                        type="submit"
                    >
                        حذف
                    </button>
                </form>
            </x-slot:actions>
        </x-ui.confirm-action>
    </div>
</x-layouts.dashboard>
