<x-layouts.dashboard page-title="إعدادات الجدول الدراسي">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            [
                'label' => 'إدارة الجداول الدراسية',
                'url' => route('dashboard.timetables.index'),
                'icon' => 'fas fa-table',
            ],
            ['label' => 'إعدادات الجدول الدراسي', 'icon' => 'fas fa-cog'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="قوالب إعدادات الوقت"
        description="إدارة قوالب إعدادات الوقت لليوم الدراسي"
        button-text="إضافة قالب جديد"
        :btnPermissions="\Perm::TimetableSettingsManage"
        button-link="{{ route('dashboard.timetable-settings.create') }}"
    />
    <x-ui.card>
        @if ($settings->count() > 0)
            <x-table :headers="[
                ['label' => 'اسم القالب'],
                ['label' => 'بداية الحصة الأولى'],
                ['label' => 'مدة الحصة'],
                ['label' => 'الحالة'],
                ['label' => 'عدد الجداول المستخدمة'],
                ['label' => 'الإجراءات'],
            ]">
                @foreach ($settings as $setting)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <x-table.td nowrap>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $setting->name }}
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($setting->first_period_start_time)->format('H:i') }}
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $setting->default_period_duration_minutes }} دقيقة
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            @if ($setting->is_active)
                                <x-ui.badge
                                    variant="success"
                                    size="sm"
                                >
                                    <i class="fas fa-check-circle mr-1"></i>
                                    مفعل
                                </x-ui.badge>
                            @else
                                <x-ui.badge
                                    variant="default"
                                    size="sm"
                                >
                                    غير مفعل
                                </x-ui.badge>
                            @endif
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $setting->timetables_count }} جدول
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="flex items-center gap-4">
                                <x-table.action-edit
                                    href="{{ route('dashboard.timetable-settings.edit', $setting) }}"
                                    :permissions="\Perm::TimetableSettingsManage"
                                />
                                <x-table.action-delete
                                    type="button"
                                    :permissions="\Perm::TimetableSettingsManage"
                                    @click="$dispatch('open-modal', {
                                        name: 'delete-timetable-setting',
                                        setting: {
                                            id: {{ $setting->id }},
                                            name: '{{ $setting->name }}',
                                            route: '{{ route('dashboard.timetable-settings.destroy', $setting) }}'
                                        }
                                    })"
                                />
                            </div>
                        </x-table.td>
                    </tr>
                @endforeach
            </x-table>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $settings->links() }}
            </div>
        @else
            <x-ui.empty-state
                icon="fas fa-clock"
                title="لا توجد قوالب إعدادات"
            >
                @can(\Perm::TimetableSettingsManage->value)
                    <x-slot:action>
                        <x-ui.button
                            as="a"
                            href="{{ route('dashboard.timetable-settings.create') }}"
                            variant="primary"
                        >
                            <i class="fas fa-plus"></i>
                            إضافة قالب جديد
                        </x-ui.button>
                    </x-slot:action>
                @endcan
            </x-ui.empty-state>
        @endif
    </x-ui.card>

    {{-- Delete Confirmation Modal --}}
    <x-ui.confirm-action
        name="delete-timetable-setting"
        title="تأكيد حذف قالب الإعدادات"
        dataKey="setting"
        spoofMethod="DELETE"
        confirmButtonText="حذف"
        confirmButtonVariant="danger"
        :permissions="\Perm::TimetableSettingsManage"
    >
        هل أنت متأكد من حذف قالب الإعدادات <strong x-text="setting?.name"></strong>؟
    </x-ui.confirm-action>
</x-layouts.dashboard>
