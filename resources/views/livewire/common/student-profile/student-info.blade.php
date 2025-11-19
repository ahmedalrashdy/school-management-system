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
                    @if ($context === 'dashboard' && auth()->user()->can(\Perm::StudentsUpdate))
                        <x-ui.button
                            as="a"
                            href="{{ route('dashboard.students.edit', $student) }}"
                            variant="primary"
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
                        <p class="text-sm text-gray-900 dark:text-white">{{ $student->user->first_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">اسم
                            العائلة</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $student->user->last_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">الجنس</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $student->user->gender->label() }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">تاريخ
                            الميلاد</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $student->date_of_birth->format('Y-m-d') }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">رقم القيد</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $student->admission_number }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">المدينة</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $student->city }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">المنطقة</label>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $student->district }}</p>
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
                    @if ($context === 'dashboard' && auth()->user()->can(\Perm::StudentsUpdate))
                        @if ($student->user->reset_password_required || auth()->user()->is_admin)
                            <x-ui.button
                                as="a"
                                href="{{ route('dashboard.students.edit', $student) }}"
                                variant="primary"
                            >
                                <i class="fas fa-edit"></i>
                                تعديل البيانات الشخصية
                            </x-ui.button>
                        @endif
                    @elseif ($context === 'portal' && $student->user_id === auth()->user()->id)
                        <x-ui.button
                            as="a"
                            href="{{ route('portal.student.profile.edit', $student) }}"
                            variant="primary"
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
                        @if ($student->user->avatar)
                            <img
                                class="h-20 w-20 rounded-full object-cover"
                                src="{{ \Storage::url($student->user->avatar) }}"
                                alt="{{ $student->user->full_name }}"
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
                        <p class="text-sm text-gray-900 dark:text-white">{{ $student->user->address ?? 'غير محدد' }}
                        </p>
                    </div>
                    @if ($student->user->email)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">البريد
                                الإلكتروني</label>
                            <p class="text-sm text-gray-900 dark:text-white">
                                <i class="fas fa-envelope mr-1"></i>
                                {{ $student->user->email }}
                            </p>
                        </div>
                    @endif
                    @if ($student->user->phone_number)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">رقم
                                الهاتف</label>
                            <p class="text-sm text-gray-900 dark:text-white">
                                <i class="fas fa-phone mr-1"></i>
                                {{ $student->user->phone_number }}
                            </p>
                        </div>
                    @endif
                </div>
            </x-ui.card>
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
                            href="{{ route('dashboard.students.edit', $student) }}"
                            variant="primary"
                            class="w-full"
                            :permissions="\Perm::StudentsUpdate"
                        >
                            <i class="fas fa-edit"></i>
                            تعديل البيانات
                        </x-ui.button>
                        <div
                            x-data="{ hasSection: @js($this->hasSection()) }"
                            @student-assigned.window="hasSection = true"
                            x-show="!hasSection"
                        >
                            <button
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition"
                                type="button"
                                @click="$dispatch('open-modal', { name: 'assign-student-modal' })"
                            >
                                <i class="fas fa-users"></i>
                                تسكين في شعبة
                            </button>
                        </div>

                        <x-ui.button
                            type="button"
                            :variant="$student->user->is_active ? 'warning' : 'success'"
                            :permissions="\Perm::StudentsUpdate"
                            class="w-full"
                            @click="$dispatch('open-modal', {
                                    name: 'toggle-student-active',
                                    student: {
                                        id: {{ $student->id }},
                                        name: '{{ $student->user->full_name }}',
                                        isActive: {{ $student->user->is_active ? 'true' : 'false' }},
                                        route: '{{ route('dashboard.students.toggle-active', $student) }}'
                                    }
                                })"
                        >
                            <i class="fas {{ $student->user->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                            {{ $student->user->is_active ? 'تعطيل حساب الطالب' : 'إعادة تفعيل الحساب' }}
                        </x-ui.button>
                    </div>
                </x-ui.card>
            </div>
        @endif
    </div>

    @if ($context === 'dashboard')
        <x-ui.modal
            name="assign-student-modal"
            title="تسكين الطالب في شعبة"
        >
            <livewire:dashboard.users.students.assign-single-student-to-section
                :$student
                lazy
            />
        </x-ui.modal>

        {{-- Toggle Active Confirmation Modal --}}
        <x-ui.confirm-action
            name="toggle-student-active"
            title="تأكيد الإجراء"
            dataKey="student"
            :permissions="\Perm::StudentsUpdate"
        >
            <div x-show="student?.isActive">
                <div>
                    هل أنت متأكد من تعطيل حساب الطالب <strong x-text="student?.name"></strong>؟
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        لن يتمكن الطالب من تسجيل الدخول بعد التعطيل.
                    </p>
                </div>
            </div>
            <div x-show="!student?.isActive">
                <div>
                    هل أنت متأكد من إعادة تفعيل حساب الطالب <strong x-text="student?.name"></strong>؟
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        سيتمكن الطالب من تسجيل الدخول بعد التفعيل.
                    </p>
                </div>
            </div>
            <x-slot:actions>
                <form
                    class="inline"
                    method="POST"
                    x-bind:action="student?.route"
                    x-show="student"
                >
                    @csrf
                    <button
                        class="px-4 py-2 text-sm font-medium text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors"
                        type="submit"
                        :class="student?.isActive ? 'bg-warning-600 hover:bg-warning-700 focus:ring-warning-500' :
                            'bg-success-600 hover:bg-success-700 focus:ring-success-500'"
                    >
                        <span x-text="student?.isActive ? 'تعطيل' : 'تفعيل'"></span>
                    </button>
                </form>
            </x-slot:actions>
        </x-ui.confirm-action>
    @endif
</div>
