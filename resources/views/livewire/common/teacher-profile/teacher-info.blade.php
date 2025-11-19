<div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- البيانات الرسمية -->
            <x-ui.card>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-id-card mr-2"></i>
                        البيانات الرسمية
                    </h3>
                    @if ($context === 'dashboard')
                        <x-ui.button
                            as="a"
                            href="{{ route('dashboard.teachers.edit', $teacher) }}"
                            :permissions="\Perm::TeachersUpdate"
                        >
                            <i class="fas fa-edit"></i>
                            تعديل البيانات الرسمية
                        </x-ui.button>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">الاسم
                            الأول</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $teacher->user->first_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">اسم
                            العائلة</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $teacher->user->last_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">الجنس</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $teacher->user->gender->label() }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">تاريخ
                            الميلاد</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $teacher->date_of_birth->format('Y-m-d') }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">التخصص</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $teacher->specialization }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">المؤهل
                            العلمي</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $teacher->qualification->label() }}</p>
                    </div>
                </div>
            </x-ui.card>

            <!-- البيانات الشخصية -->
            <x-ui.card>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-user-circle mr-2"></i>
                        البيانات الشخصية
                    </h3>
                    @if ($context === 'dashboard' && ($teacher->user->reset_password_required || auth()->user()->is_admin))
                        <x-ui.button
                            as="a"
                            href="{{ route('dashboard.teachers.edit', $teacher) }}"
                            :permissions="\Perm::TeachersUpdate"
                        >
                            <i class="fas fa-edit"></i>
                            تعديل البيانات الشخصية
                        </x-ui.button>
                    @elseif ($context === 'portal' && $teacher->user_id == auth()->user()->id)
                        <x-ui.button
                            as="a"
                            href="{{ route('portal.teacher.profile.edit') }}"
                        >
                            <i class="fas fa-edit"></i>
                            تعديل بياناتي
                        </x-ui.button>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">الصورة
                            الرمزية</label>
                        @if ($teacher->user->avatar)
                            <img
                                src="{{ \Storage::url($teacher->user->avatar) }}"
                                alt="{{ $teacher->user->full_name }}"
                                class="h-20 w-20 rounded-full object-cover"
                            >
                        @else
                            <div
                                class="h-20 w-20 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                <i class="fas fa-user text-gray-500 dark:text-gray-400 text-2xl"></i>
                            </div>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">العنوان</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $teacher->user->address ?? 'غير محدد' }}
                        </p>
                    </div>
                    @if ($teacher->user->email)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">البريد
                                الإلكتروني</label>
                            <p class="text-sm text-gray-900 dark:text-white">
                                <i class="fas fa-envelope mr-1"></i>
                                {{ $teacher->user->email }}
                            </p>
                        </div>
                    @endif
                    @if ($teacher->user->phone_number)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">رقم
                                الهاتف</label>
                            <p class="text-sm text-gray-900 dark:text-white">
                                <i class="fas fa-phone mr-1"></i>
                                {{ $teacher->user->phone_number }}
                            </p>
                        </div>
                    @endif
                </div>
            </x-ui.card>

            <!-- حالة الحساب -->
            @if ($context == 'dashboard')
                <x-ui.card>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-info-circle mr-2"></i>
                        حالة الحساب
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">حالة
                                الحساب</label>
                            @if ($teacher->user->is_active)
                                <x-ui.badge variant="success">نشط</x-ui.badge>
                            @else
                                <x-ui.badge variant="danger">غير نشط</x-ui.badge>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">حالة كلمة
                                المرور</label>
                            @if ($teacher->user->reset_password_required)
                                <x-ui.badge variant="warning">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    يتطلب إعادة تعيين
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
            @endif
        </div>

        <!-- الإجراءات العامة -->
        @if ($context === 'dashboard')
            <div class="space-y-6">
                <x-ui.card>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-cog mr-2"></i>
                        الإجراءات
                    </h3>

                    <div class="space-y-3 flex flex-col gap-2">
                        <x-ui.button
                            as="a"
                            href="{{ route('dashboard.teachers.edit', $teacher) }}"
                            variant="primary"
                            class="w-full"
                            :permissions="\Perm::TeachersUpdate"
                        >
                            <i class="fas fa-edit"></i>
                            تعديل البيانات
                        </x-ui.button>

                        <x-ui.button
                            type="button"
                            @click="$dispatch('open-modal', {
                                name: 'toggle-teacher-active',
                                teacher: {
                                    id: {{ $teacher->id }},
                                    name: '{{ $teacher->user->full_name }}',
                                    isActive: {{ $teacher->user->is_active ? 'true' : 'false' }},
                                    route: '{{ route('dashboard.teachers.toggle-active', $teacher) }}'
                                }
                            })"
                            :variant="$teacher->user->is_active ? 'warning' : 'success'"
                            class="w-full"
                            :permissions="\Perm::TeachersUpdate"
                        >
                            <i class="fas {{ $teacher->user->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                            {{ $teacher->user->is_active ? 'تعطيل حساب المدرس' : 'إعادة تفعيل الحساب' }}
                        </x-ui.button>
                    </div>
                </x-ui.card>
            </div>
        @endif
    </div>

    {{-- Toggle Active Confirmation Modal --}}
    @if ($context === 'dashboard')
        <x-ui.confirm-action
            name="toggle-teacher-active"
            title="تأكيد الإجراء"
            dataKey="teacher"
            :permissions="\Perm::TeachersUpdate"
        >
            <div x-show="teacher?.isActive">
                <div>
                    هل أنت متأكد من تعطيل حساب المدرس <strong x-text="teacher?.name"></strong>؟
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        لن يتمكن المدرس من تسجيل الدخول بعد التعطيل.
                    </p>
                </div>
            </div>
            <div x-show="!teacher?.isActive">
                <div>
                    هل أنت متأكد من إعادة تفعيل حساب المدرس <strong x-text="teacher?.name"></strong>؟
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        سيتمكن المدرس من تسجيل الدخول بعد التفعيل.
                    </p>
                </div>
            </div>
            <x-slot:actions>

                <form
                    class="inline"
                    method="POST"
                    x-bind:action="teacher?.route"
                    x-show="teacher"
                >
                    @csrf
                    <button
                        class="px-4 py-2 text-sm font-medium text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors"
                        type="submit"
                        :class="teacher?.isActive ? 'bg-warning-600 hover:bg-warning-700 focus:ring-warning-500' :
                            'bg-success-600 hover:bg-success-700 focus:ring-success-500'"
                    >
                        <span x-text="teacher?.isActive ? 'تعطيل' : 'تفعيل'"></span>
                    </button>
                </form>
            </x-slot:actions>
        </x-ui.confirm-action>
    @endif
</div>
