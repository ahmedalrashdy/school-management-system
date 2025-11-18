<x-layouts.dashboard page-title="أنواع الامتحانات">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'إدارة الامتحانات', 'url' => route('dashboard.exams.index'), 'icon' => 'fas fa-clipboard-list'],
            ['label' => 'أنواع الامتحانات', 'icon' => 'fas fa-file-alt'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="أنواع الامتحانات"
        description="إدارة أنواع الامتحانات في النظام"
        button-text="إضافة نوع امتحان"
        button-link="{{ route('dashboard.exam-types.create') }}"
    />
    <x-ui.card>
        @if ($examTypes->count() > 0)
            <x-table :headers="[['label' => 'اسم النوع'], ['label' => 'عدد الامتحانات'], ['label' => 'الإجراءات']]">
                @foreach ($examTypes as $examType)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <x-table.td nowrap>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $examType->name }}
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $examType->exams_count }} امتحان
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="flex items-center gap-2">
                                <x-table.action-edit href="{{ route('dashboard.exam-types.edit', $examType) }}" />
                                <x-table.action-delete
                                    @click="$dispatch('open-modal', {
                                                        name: 'delete-exam-type',
                                                        examType: {
                                                            id: {{ $examType->id }},
                                                            name: '{{ $examType->name }}',
                                                            route: '{{ route('dashboard.exam-types.destroy', $examType) }}'
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
                {{ $examTypes->links() }}
            </div>
        @else
            <x-ui.empty-state
                icon="fas fa-file-alt"
                title="لا توجد أنواع امتحانات"
                :action="[
                    'label' => 'إضافة نوع امتحان جديد',
                    'url' => route('dashboard.exam-types.create'),
                    'icon' => 'fas fa-plus',
                ]"
            />
        @endif
    </x-ui.card>

    {{-- Delete Confirmation Modal --}}
    <x-ui.confirm-action
        name="delete-exam-type"
        title="تأكيد حذف نوع الامتحان"
        dataKey="examType"
        spoofMethod="DELETE"
        confirmButtonText="حذف"
        confirmButtonVariant="danger"
    >
        هل أنت متأكد من حذف نوع الامتحان <strong x-text="examType?.name"></strong>؟
    </x-ui.confirm-action>
</x-layouts.dashboard>
