<div x-data="{
    openRolesModal(userId, userName, roles) {
        const userData = {
            id: userId,
            name: userName,
            roles: roles,
            rolesCount: roles.length
        };
        $dispatch('open-modal', {
            name: 'user-roles-modal',
            userData: userData
        });
    }
}">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'المستخدمون', 'icon' => 'fas fa-users'],
        ]" />
    </x-slot>

    <!-- Header -->
    <x-ui.main-content-header
        title="المستخدمون"
        description="إدارة المستخدمين وأدوارهم"
        button-text="إنشاء مستخدم جديد"
        :btnPermissions="\Perm::UsersCreate"
        button-link="{{ route('dashboard.users.create') }}"
    />

    <!-- Filters -->
    <x-ui.card class="mb-6">
        <form
            class="flex flex-wrap gap-4"
            wire:submit.prevent
        >
            <div class="flex-1 min-w-[200px]">
                <x-form.select
                    name="userType"
                    label="نوع المستخدم"
                    :options="[
                        '' => 'جميع الأنواع',
                        'admin' => 'مدير',
                        'طالب' => 'طالب',
                        'مدرس' => 'مدرس',
                        'ولي أمر' => 'ولي أمر',
                    ]"
                    wire:model.live="userType"
                />
            </div>

            <div class="flex-1 min-w-[200px]">
                <x-form.select
                    name="status"
                    label="الحالة"
                    :options="['' => 'جميع الحالات', 'active' => 'نشط', 'inactive' => 'غير نشط']"
                    wire:model.live="status"
                />
            </div>

            <div class="flex-1 min-w-[200px]">
                <x-form.input
                    name="search"
                    label="البحث"
                    placeholder="ابحث بالاسم، البريد، أو الهاتف"
                    wire:model.live.debounce.300ms="search"
                />
            </div>

            @if ($hasActiveFilters)
                <x-ui.button
                    variant="outline"
                    type="button"
                    class="self-end mb-4"
                    wire:click="resetFilters"
                >
                    <i class="fas fa-redo"></i>
                    إعادة تعيين
                </x-ui.button>
            @endif
        </form>
    </x-ui.card>

    <!-- Table -->
    <x-ui.card>
        @if ($users->count() > 0)
            <x-table :headers="[
                ['label' => 'الاسم'],
                ['label' => 'معلومات الاتصال'],
                ['label' => 'الأدوار'],
                ['label' => 'النوع'],
                ['label' => 'الحالة'],
                ['label' => 'الإجراءات'],
            ]">
                @foreach ($users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <x-table.td nowrap>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $user->full_name }}
                            </div>
                            @if ($user->is_admin)
                                <div class="text-xs text-primary-600 dark:text-primary-400 mt-1">
                                    <i class="fas fa-crown mr-1"></i>
                                    مدير النظام
                                </div>
                            @endif
                        </x-table.td>
                        <x-table.td nowrap>
                            @if ($user->email)
                                <div class="text-sm text-gray-900 dark:text-white">
                                    <i class="fas fa-envelope mr-1"></i>
                                    {{ $user->email }}
                                </div>
                            @endif
                            @if ($user->phone_number)
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-phone mr-1"></i>
                                    {{ $user->phone_number }}
                                </div>
                            @endif
                        </x-table.td>
                        <x-table.td class="px-6 py-4">
                            @if ($user->roles->count() > 0)
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $user->roles->count() }} {{ $user->roles->count() === 1 ? 'دور' : 'أدوار' }}
                                    </span>
                                    <button
                                        class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded transition"
                                        type="button"
                                        @click="openRolesModal({{ $user->id }}, '{{ $user->full_name }}', @js($user->roles->map(fn($role) => ['id' => $role->id, 'name' => $role->name])))"
                                    >
                                        <i class="fas fa-eye"></i>
                                        عرض
                                    </button>
                                </div>
                            @else
                                <span class="text-xs text-gray-500 dark:text-gray-400">لا توجد أدوار</span>
                            @endif
                        </x-table.td>
                        <x-table.td nowrap>
                            @if ($user->user_type)
                                <x-ui.badge variant="secondary">
                                    @if ($user->user_type === 'admin')
                                        مدير
                                    @elseif($user->user_type === 'طالب')
                                        طالب
                                    @elseif($user->user_type === 'مدرس')
                                        مدرس
                                    @elseif($user->user_type === 'ولي أمر')
                                        ولي أمر
                                    @endif
                                </x-ui.badge>
                            @else
                                <span class="text-xs text-gray-500 dark:text-gray-400">-</span>
                            @endif
                        </x-table.td>
                        <x-table.td nowrap>
                            @if ($user->is_active)
                                <x-ui.badge variant="success">نشط</x-ui.badge>
                            @else
                                <x-ui.badge variant="danger">غير نشط</x-ui.badge>
                            @endif
                        </x-table.td>
                        <x-table.td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-4">
                                <x-table.action-edit
                                    as='a'
                                    href="{{ route('dashboard.users.edit', $user->id) }}"
                                    :permissions="\Perm::UsersUpdate"
                                />
                                @can(\Perm::UsersManageRoles->value)
                                    <a
                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                        href="{{ route('dashboard.users.manage-roles', $user->id) }}"
                                    >
                                        <i class="fas fa-user-shield mr-1"></i>
                                        أدوار
                                    </a>
                                @endcan
                                @if (auth()->user()->can(\Perm::UsersUpdate->value) && !$user->is_admin)
                                    <button
                                        type="button"
                                        class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                                        @click="$dispatch('open-modal', {
                                                        name: 'toggle-user-active',
                                                        user: {
                                                            id: {{ $user->id }},
                                                            name: '{{ $user->full_name }}',
                                                            isActive: {{ $user->is_active ? 'true' : 'false' }}
                                                        }
                                                    })"
                                    >
                                        <i class="fas fa-toggle-{{ $user->is_active ? 'on' : 'off' }} mr-1"></i>
                                        {{ $user->is_active ? 'تعطيل' : 'تفعيل' }}
                                    </button>
                                @endif
                                @if (auth()->user()->can(\Perm::UsersDelete->value) &&
                                        !$user->is_admin &&
                                        !$user->roles->pluck('name')->intersect(['طالب', 'ولي أمر', 'مدرس'])->isNotEmpty())
                                    <x-table.action-delete
                                        :permissions="\Perm::UsersDelete"
                                        @click="$dispatch('open-modal', {
                                    name: 'delete-user',
                                    user: {
                                        id: {{ $user->id }},
                                        name: '{{ $user->full_name }}'
                                    }
                                    })"
                                    />
                                @endif

                            </div>
                        </x-table.td>
                    </tr>
                @endforeach
            </x-table>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-users text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                <p class="text-gray-500 dark:text-gray-400">لا يوجد مستخدمون</p>
            </div>
        @endif
    </x-ui.card>

    {{-- Single Modal for displaying user roles --}}
    <x-ui.modal
        name="user-roles-modal"
        title="أدوار المستخدم"
        maxWidth="md"
    >
        <div
            class="space-y-4"
            x-data="{
                userData: { id: null, name: '', roles: [], rolesCount: 0 }
            }"
            @open-modal.window="if ($event.detail.name === 'user-roles-modal' && $event.detail.userData) { userData = $event.detail.userData }"
            x-show="userData.rolesCount > 0"
        >
            <div class="mb-4 pb-3 border-b dark:border-gray-700">
                <h4 class="text-base font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-user mr-2"></i>
                    <span x-text="'أدوار المستخدم: ' + userData.name"></span>
                </h4>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    <i class="fas fa-info-circle mr-1"></i>
                    المستخدم لديه <strong x-text="userData.rolesCount"></strong>
                    <span x-text="userData.rolesCount === 1 ? 'دور' : 'أدوار'"></span>
                </p>
                <div class="flex flex-wrap gap-2">
                    <template
                        x-for="role in userData.roles"
                        :key="role.id"
                    >
                        <x-ui.badge
                            variant="primary"
                            size="lg"
                        >
                            <i class="fas fa-user-shield mr-1"></i>
                            <span x-text="role.name"></span>
                        </x-ui.badge>
                    </template>
                </div>
            </div>

            @can(\Perm::UsersManageRoles->value)
                <div class="flex justify-end pt-4 border-t dark:border-gray-700">
                    <a
                        class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition"
                        :href="`{{ route('dashboard.users.manage-roles', ':id') }}`.replace(':id', userData.id)"
                    >
                        <i class="fas fa-edit"></i>
                        إدارة الأدوار
                    </a>
                </div>
            @endcan
        </div>
    </x-ui.modal>

    {{-- Delete User Confirmation Modal --}}
    <x-ui.confirm-action
        name="delete-user"
        title="تأكيد حذف المستخدم"
        dataKey="user"
        confirmButtonText="حذف"
        confirmButtonVariant="danger"
        :permissions="\Perm::UsersDelete"
    >
        هل أنت متأكد من حذف المستخدم <strong x-text="user?.name"></strong>؟
        <x-slot:actions>
            <button
                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors"
                type="button"
                @click="user = null; $dispatch('close-modal', { name: 'delete-user' })"
            >
                إلغاء
            </button>
            <button
                class="px-4 py-2 text-sm font-medium text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors bg-danger-600 hover:bg-danger-700 focus:ring-danger-500"
                type="button"
                @click="
                    $wire.deleteUser(user.id).then(() => {
                        user = null;
                        $dispatch('close-modal', { name: 'delete-user' });
                    });
                "
            >
                حذف
            </button>
        </x-slot:actions>
    </x-ui.confirm-action>

    {{-- Toggle Active Confirmation Modal --}}
    <x-ui.confirm-action
        name="toggle-user-active"
        title="تأكيد الإجراء"
        dataKey="user"
        :permissions="\Perm::UsersUpdate"
    >
        <div x-show="user?.isActive">
            <div>
                هل أنت متأكد من تعطيل حساب المستخدم <strong x-text="user?.name"></strong>؟
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    لن يتمكن المستخدم من تسجيل الدخول بعد التعطيل.
                </p>
            </div>
        </div>
        <div x-show="!user?.isActive">
            <div>
                هل أنت متأكد من تفعيل حساب المستخدم <strong x-text="user?.name"></strong>؟
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    سيتمكن المستخدم من تسجيل الدخول بعد التفعيل.
                </p>
            </div>
        </div>
        <x-slot:actions>
            <button
                class="px-4 py-2 text-sm font-medium text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors"
                type="button"
                :class="user?.isActive ? 'bg-warning-600 hover:bg-warning-700 focus:ring-warning-500' :
                    'bg-success-600 hover:bg-success-700 focus:ring-success-500'"
                wire:click="toggleActive(user.id)"
            >
                <span x-text="user?.isActive ? 'تعطيل' : 'تفعيل'"></span>
            </button>
        </x-slot:actions>
    </x-ui.confirm-action>
</div>
