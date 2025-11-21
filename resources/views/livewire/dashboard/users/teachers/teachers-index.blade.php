<div>
    <!-- Filters -->
    <x-ui.card class="mb-6">
        <form
            wire:submit.prevent="applyFilters"
            class="flex flex-wrap gap-4"
        >
            <div class="flex-1 min-w-[200px]">
                <x-form.select
                    name="specialization"
                    label="التخصص"
                    :options="['' => 'جميع التخصصات'] +
                        $this->specializations->mapWithKeys(fn($spec) => [$spec => $spec])->toArray()"
                    wire:model.live="specialization"
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
                    placeholder="ابحث بالاسم، الهاتف، أو البريد الإلكتروني"
                    wire:model.live.debounce.300ms="search"
                />
            </div>

            @if ($this->hasActiveFilters())
                <div class="flex items-end gap-2">
                    <button
                        type="button"
                        wire:click="resetFilters"
                        class="inline-flex mb-4 items-center gap-2 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                    >
                        <i class="fas fa-redo"></i>
                        إعادة تعيين
                    </button>
                </div>
            @endif
        </form>
    </x-ui.card>

    <!-- Table -->
    <x-ui.card>
        @if ($this->teachers->count() > 0)
            <x-table :headers="[
                ['label' => 'الاسم الكامل'],
                ['label' => 'التخصص'],
                ['label' => 'معلومات الاتصال'],
                ['label' => 'الحالة'],
                ['label' => 'الإجراءات'],
            ]">
                @foreach ($this->teachers as $teacher)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <x-table.td nowrap>
                            <div class="flex gap-2 items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if ($teacher->user->avatar)
                                        <img
                                            class="h-10 w-10 rounded-full object-cover"
                                            src="{{ $teacher->user->avatar }}"
                                            alt="{{ $teacher->user->full_name }}"
                                        >
                                    @else
                                        <div
                                            class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                            <i class="fas fa-user text-gray-500 dark:text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $teacher->user->full_name }}
                                    </div>
                                    @if ($teacher->user->reset_password_required)
                                        <div class="text-xs text-warning-600 dark:text-warning-400 mt-1">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            يتطلب إعادة تعيين كلمة المرور
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="text-sm text-gray-900 dark:text-white">
                                {{ $teacher->specialization }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $teacher->qualification->label() }}
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="text-sm text-gray-900 dark:text-white">
                                @if ($teacher->user->phone_number)
                                    <i class="fas fa-phone mr-1"></i>
                                    {{ $teacher->user->phone_number }}
                                @endif
                            </div>
                            @if ($teacher->user->email)
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-envelope mr-1"></i>
                                    {{ $teacher->user->email }}
                                </div>
                            @endif
                        </x-table.td>
                        <x-table.td nowrap>
                            @if ($teacher->user->is_active)
                                <x-ui.badge variant="success">نشط</x-ui.badge>
                            @else
                                <x-ui.badge variant="danger">غير نشط</x-ui.badge>
                            @endif
                        </x-table.td>
                        <x-table.td>
                            <x-table.action-view
                                wire:navigate
                                href="{{ route('dashboard.teachers.show', $teacher) }}"
                                :permissions="\Perm::TeachersView"
                            />
                        </x-table.td>
                    </tr>
                @endforeach
            </x-table>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $this->teachers->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-chalkboard-teacher text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                <p class="text-gray-500 dark:text-gray-400">لا يوجد مدرسون</p>
            </div>
        @endif
    </x-ui.card>
</div>
