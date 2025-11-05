<x-layouts.dashboard page-title="المراحل الدراسية">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'المراحل الدراسية', 'icon' => 'fas fa-graduation-cap'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="المراحل الدراسية"
        description="إدارة المراحل الدراسية في النظام"
        button-text="إضافة مرحلة دراسية"
        :btnPermissions="\Perm::StagesCreate"
        button-link="{{ route('dashboard.stages.create') }}"
    />
    @if ($stages->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($stages as $stage)
                <x-ui.card>
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                {{ $stage->name }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-graduation-cap mr-1"></i>
                                {{ $stage->grades->count() }} صف دراسي
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-ui.button
                                as="a"
                                :permissions="\Perm::StagesUpdate"
                                href="{{ route('dashboard.stages.edit', $stage) }}"
                                variant="primary"
                                size="sm"
                            >
                                <i class="fas fa-edit"></i>
                            </x-ui.button>
                            @if ($stage->grades->count())
                                <x-ui.button
                                    :permissions="\Perm::StagesDelete"
                                    variant="danger"
                                    size="sm"
                                    type="button"
                                    @click="$dispatch('open-modal', {
                                       name: 'delete-stage',
                                       stage: {
                                           id: {{ $stage->id }},
                                           name: '{{ $stage->name }}',
                                           route: '{{ route('dashboard.stages.destroy', $stage) }}'
                                       }
                                   })"
                                >
                                    <i class="fas fa-trash"></i>
                                </x-ui.button>
                            @endif
                        </div>
                    </div>

                    @if ($stage->grades->count() > 0)
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                الصفوف الدراسية:
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($stage->grades as $grade)
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-300"
                                    >
                                        {{ $grade->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center">
                                لا توجد صفوف دراسية
                            </p>
                        </div>
                    @endif
                </x-ui.card>
            @endforeach
        </div>
    @else
        <x-ui.card>
            <x-ui.empty-state
                icon="fas fa-graduation-cap"
                title="لا توجد مراحل دراسية"
            >
                <x-ui.button
                    as="a"
                    :permissions="\Perm::StagesCreate"
                    href="{{ route('dashboard.stages.create') }}"
                >
                    <i class="fas fa-plus"></i>
                    إضافة مرحلة دراسية جديدة
                </x-ui.button>
            </x-ui.empty-state>
        </x-ui.card>
    @endif

    {{-- Delete Confirmation Modal --}}
    <x-ui.confirm-action
        name="delete-stage"
        title="تأكيد حذف المرحلة الدراسية"
        dataKey="stage"
        spoofMethod="DELETE"
        confirmButtonText="حذف"
        confirmButtonVariant="danger"
        :permissions="\Perm::StagesDelete"
    >
        <p class="mb-4">هل أنت متأكد من حذف المرحلة الدراسية <strong x-text="stage?.name"></strong>؟</p>
        <x-ui.warning-box>
            لن تنجح العملية إاذا كان هناك أي بيانات مرتبطة بهذه المرحلة الدراسية
        </x-ui.warning-box>
    </x-ui.confirm-action>
</x-layouts.dashboard>
