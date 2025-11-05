<x-layouts.dashboard page-title="الصفوف الدراسية">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'الصفوف الدراسية', 'icon' => 'fas fa-layer-group'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="الصفوف الدراسية"
        description="إدارة الصفوف الدراسية في النظام"
        button-text="إضافة صف دراسي"
        :btnPermissions="\Perm::GradesCreate"
        button-link="{{ route('dashboard.grades.create') }}"
    />


    @if ($stages->count() > 0)
        <div class="space-y-6">
            @foreach ($stages as $stage)
                <x-ui.card>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            <i class="fas fa-graduation-cap mr-2 text-primary-600"></i>
                            {{ $stage->name }}
                        </h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $stage->grades->count() }} صف
                        </span>
                    </div>

                    @if ($stage->grades->count() > 0)
                        <x-table :headers="[['label' => 'اسم الصف'], ['label' => 'الإجراءات']]">
                            @foreach ($stage->grades as $grade)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                    <x-table.td nowrap>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $grade->name }}
                                        </div>
                                    </x-table.td>
                                    <x-table.td nowrap>
                                        <div class="flex items-center gap-2">
                                            <x-table.action-edit
                                                :permissions="\Perm::GradesUpdate"
                                                href="{{ route('dashboard.grades.edit', $grade) }}"
                                            />
                                            <x-table.action-delete
                                                :permissions="\Perm::GradesDelete"
                                                @click="$dispatch('open-modal', {
                                                    name: 'delete-grade',
                                                    grade: {
                                                        id: {{ $grade->id }},
                                                        name: '{{ $grade->name }}',
                                                        route: '{{ route('dashboard.grades.destroy', $grade) }}'
                                                    }
                                                })"
                                            />
                                        </div>
                                    </x-table.td>
                                </tr>
                            @endforeach
                        </x-table>
                    @else
                        <div class="text-center py-8">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                لا توجد صفوف دراسية في هذه المرحلة
                            </p>
                        </div>
                    @endif
                </x-ui.card>
            @endforeach
        </div>
    @else
        <x-ui.card>
            <div class="text-center py-12">
                <i class="fas fa-graduation-cap text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                <p class="text-gray-500 dark:text-gray-400 mb-4">لا توجد مراحل دراسية. يرجى إضافة مرحلة دراسية أولاً.
                </p>
                <a
                    href="{{ route('dashboard.stages.create') }}"
                    class="inline-flex items-center gap-2 text-primary-600 hover:text-primary-700 dark:text-primary-400"
                >
                    <i class="fas fa-plus"></i>
                    إضافة مرحلة دراسية
                </a>
            </div>
        </x-ui.card>
    @endif

    {{-- Delete Confirmation Modal --}}
    <x-ui.confirm-action
        name="delete-grade"
        title="تأكيد حذف الصف الدراسي"
        dataKey="grade"
        spoofMethod="DELETE"
        confirmButtonText="حذف"
        confirmButtonVariant="danger"
        :permissions="\Perm::GradesDelete"
    >
        <p class="mb-4">هل أنت متأكد من حذف الصف الدراسي <strong x-text="grade?.name"></strong>؟</p>
        <x-ui.warning-box>
            لن تنجح العملية إذا كان هناك بيانات مرتبطة بهذا الصف الدراسي (شعب، طلاب، مناهج، إلخ).
        </x-ui.warning-box>
    </x-ui.confirm-action>
</x-layouts.dashboard>
