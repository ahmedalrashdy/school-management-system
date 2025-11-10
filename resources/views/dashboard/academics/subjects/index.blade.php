<x-layouts.dashboard page-title="المواد الدراسية">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'المواد الدراسية', 'icon' => 'fas fa-book'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="المواد الدراسية"
        description="إدارة المواد الدراسية في النظام"
        button-text="إضافة مادة دراسية"
        :btnPermissions="\Perm::SubjectsCreate"
        button-link="{{ route('dashboard.subjects.create') }}"
    />
    <x-ui.card>
        @if ($subjects->count() > 0)
            <x-table :headers="[['label' => 'اسم المادة'], ['label' => 'عدد المناهج المستخدمة'], ['label' => 'الإجراءات']]">
                @foreach ($subjects as $subject)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <x-table.td nowrap>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $subject->name }}
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $subject->curriculum_subjects_count }} منهج
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="flex items-center gap-2">
                                <x-table.action-edit
                                    :permissions="\Perm::SubjectsUpdate"
                                    href="{{ route('dashboard.subjects.edit', $subject) }}"
                                />
                                <x-table.action-delete
                                    :permissions="\Perm::SubjectsDelete"
                                    @click="$dispatch('open-modal', {
                                                name: 'delete-subject',
                                                subject: {
                                                    id: {{ $subject->id }},
                                                    name: '{{ $subject->name }}',
                                                    route: '{{ route('dashboard.subjects.destroy', $subject) }}'
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
                {{ $subjects->links() }}
            </div>
        @else
            <x-ui.empty-state
                icon="fas fa-book"
                title="لا توجد مواد دراسية"
            >
                <x-ui.button
                    as="a"
                    :permissions="\Perm::SubjectsCreate"
                    href="{{ route('dashboard.subjects.create') }}"
                >
                    <i class="fas fa-plus"></i>
                    إضافة مادة دراسية جديدة
                </x-ui.button>
            </x-ui.empty-state>
        @endif
    </x-ui.card>

    {{-- Delete Confirmation Modal --}}
    <x-ui.confirm-action
        name="delete-subject"
        title="تأكيد حذف المادة الدراسية"
        dataKey="subject"
        spoofMethod="DELETE"
        confirmButtonText="حذف"
        confirmButtonVariant="danger"
        :permissions="\Perm::SubjectsDelete"
    >
        <p class="mb-4">هل أنت متأكد من حذف المادة الدراسية <strong x-text="subject?.name"></strong>؟</p>
        <x-ui.warning-box>
            لن تنجح العملية إذا كان هناك بيانات مرتبطة بهذه المادة الدراسية (مناهج، درجات، إلخ).
        </x-ui.warning-box>
    </x-ui.confirm-action>
</x-layouts.dashboard>
