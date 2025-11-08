<x-layouts.dashboard page-title="أولياء الأمور">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'أولياء الأمور', 'icon' => 'fas fa-user-friends'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="أولياء الأمور"
        description="إدارة أولياء الأمور في النظام"
        button-text="إضافة ولي أمر"
        :btnPermissions="\Perm::GuardiansCreate"
        button-link="{{ route('dashboard.guardians.create') }}"
    />

    <!-- Filters -->
    <x-ui.filter-section :showReset="request('search') || request('status')">
        <div class="flex-1 min-w-[200px]">
            <x-form.input
                name="search"
                label="البحث"
                placeholder="ابحث بالاسم أو رقم الهاتف..."
                value="{{ request('search') }}"
            />
        </div>
        <div class="w-48">
            <x-form.select
                name="status"
                label="الحالة"
                :options="[
                    '' => 'جميع الحالات',
                    'active' => 'نشط',
                    'inactive' => 'غير نشط',
                ]"
                selected="{{ request('status') }}"
            />
        </div>
    </x-ui.filter-section>

    <!-- Table -->
    <x-ui.card>
        @if ($guardians->count() > 0)
            <x-table :headers="[
                ['label' => 'الاسم الكامل'],
                ['label' => 'معلومات الاتصال'],
                ['label' => 'الحالة'],
                ['label' => 'عدد الطلاب'],
                ['label' => 'الإجراءات'],
            ]">
                @foreach ($guardians as $guardian)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <x-table.td nowrap>
                            <div class="flex items-center gap-2">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $guardian->user->full_name }}
                                </div>
                                @if ($guardian->user->reset_password_required)
                                    <x-ui.badge
                                        variant="warning"
                                        size="sm"
                                    >
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        غير مفعّل
                                    </x-ui.badge>
                                @endif
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="text-sm text-gray-500 dark:text-gray-400 space-y-1">
                                @if ($guardian->user->email)
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-envelope text-xs"></i>
                                        <span>{{ $guardian->user->email }}</span>
                                    </div>
                                @endif
                                @if ($guardian->user->phone_number)
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-phone text-xs"></i>
                                        <span>{{ $guardian->user->phone_number }}</span>
                                    </div>
                                @endif
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <x-ui.badge :variant="$guardian->user->is_active ? 'success' : 'danger'">
                                {{ $guardian->user->is_active ? 'نشط' : 'غير نشط' }}
                            </x-ui.badge>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $guardian->students_count }} طالب
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="flex items-center gap-4">
                                <x-table.action-view href="{{ route('dashboard.guardians.show', $guardian) }}" />
                                <x-table.action-edit
                                    :permissions="\Perm::GuardiansUpdate"
                                    href="{{ route('dashboard.guardians.edit', $guardian) }}"
                                />
                            </div>
                        </x-table.td>
                    </tr>
                @endforeach
            </x-table>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $guardians->links() }}
            </div>
        @else
            <x-ui.empty-state
                icon="fas fa-users"
                title="لا توجد أولياء أمور"
            >
                <x-ui.button
                    as="a"
                    :permissions="\Perm::GuardiansCreate"
                    href="{{ route('dashboard.guardians.create') }}"
                >
                    <i class="fas fa-plus"></i>
                    إضافة ولي أمر جديد
                </x-ui.button>
            </x-ui.empty-state>
        @endif
    </x-ui.card>
</x-layouts.dashboard>
