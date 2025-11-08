<x-layouts.dashboard page-title="إدارة الأدوار">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'إدارة الأدوار', 'icon' => 'fas fa-user-shield'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="الأدوار"
        description="إدارة أدوار المستخدمين وصلاحياتهم"
        button-text="إنشاء دور جديد"
        :btnPermissions="\Perm::RolesCreate"
        button-link="{{ route('dashboard.roles.create') }}"
    />



    <!-- Filters -->
    <x-ui.card class="mb-6">
        <form
            method="GET"
            action="{{ route('dashboard.roles.index') }}"
            class="flex flex-wrap gap-4"
        >
            <div class="flex-1 min-w-[200px]">
                <x-form.input
                    name="search"
                    label="البحث"
                    placeholder="ابحث باسم الدور"
                    value="{{ request('search') }}"
                />
            </div>
            <x-ui.button class="self-end mb-4">بحث</x-ui.button>
            @if (request('search'))
                <div class="flex items-end gap-2">
                    <x-ui.button
                        as="a"
                        href="{{ route('dashboard.roles.index') }}"
                        variant="outline"
                        class="mb-4"
                    >
                        <i class="fas fa-redo"></i>
                        إعادة تعيين
                    </x-ui.button>
                </div>
            @endif
        </form>
    </x-ui.card>

    <!-- Table -->
    <x-ui.card>
        @if ($roles->count() > 0)
            <x-table :headers="[
                ['label' => 'اسم الدور'],
                ['label' => 'عدد المستخدمين'],
                ['label' => 'عدد الأذونات'],
                ['label' => 'الإجراءات'],
            ]">
                @foreach ($roles as $role)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <x-table.td nowrap>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $role->name }}
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="text-sm text-gray-900 dark:text-white">
                                {{ $role->users_count }}
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="text-sm text-gray-900 dark:text-white">
                                {{ $role->permissions_count }}
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="flex items-center">
                                <x-table.action-edit
                                    as="a"
                                    href="{{ route('dashboard.roles.edit', $role) }}"
                                    :permissions="\Perm::RolesUpdate"
                                />
                                @if ($role->users_count == 0 && !in_array($role->name, ['طالب', 'مدرس', 'ولي أمر']))
                                    <x-table.action-delete
                                        :permissions="\Perm::RolesDelete"
                                        @click="$dispatch('open-modal', {
                                    name: 'delete-role',
                                    role: {
                                        id: {{ $role->id }},
                                        name: '{{ $role->name }}',
                                        route: '{{ route('dashboard.roles.destroy', $role) }}'
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
                {{ $roles->links() }}
            </div>
        @else
            <x-ui.empty-state
                icon="fas fa-user-shield"
                title="لا توجد أدوار"
            />
        @endif
    </x-ui.card>

    {{-- Delete Confirmation Modal --}}
    <x-ui.confirm-action
        name="delete-role"
        title="تأكيد حذف الدور"
        dataKey="role"
        spoofMethod="DELETE"
        confirmButtonText="حذف"
        confirmButtonVariant="danger"
        :permissions="\Perm::RolesDelete"
    >
        هل أنت متأكد من حذف الدور <strong x-text="role?.name"></strong>؟
    </x-ui.confirm-action>
</x-layouts.dashboard>
