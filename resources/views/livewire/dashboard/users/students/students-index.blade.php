<div>
    <!-- Filters -->
    <x-ui.card class="mb-6">
        @php
            $yearsTree = lookup()->yearsTree();
            $years = $yearsTree->pluck('name', 'id');
            $grades = lookup()->getGrades(true);
        @endphp
        <form
            x-data="academicController({
                yearsTree: {{ $yearsTree->toJson() }},
                defaultYear: @entangle('academic_year_id').live,
                defaultTerm: @entangle('academic_term_id').live
            })"
            wire:submit.prevent="applyFilters"
            class="flex flex-wrap gap-4"
        >
            <div class="flex-1 min-w-[200px]">
                {{-- Academic Year --}}
                <x-form.select
                    name="academic_year_id"
                    label="العام الدراسي"
                    :options="$years"
                    x-bind="yearInput"
                    required
                />
            </div>

            <div class="flex-1 min-w-[200px]">
                {{-- Grade --}}
                <x-form.select
                    name="grade_id"
                    label="الصف الدراسي"
                    :options="$grades"
                    wire:model.live="grade_id"
                />
            </div>

            {{-- Academic Term --}}
            <div class="flex-1 min-w-[200px]">
                <x-form.select
                    name="academic_term_id"
                    label="الفصل الدراسي"
                    :options="[]"
                    x-bind="termInput"
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
                    placeholder="ابحث بالاسم أو رقم القيد"
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
        @if ($this->students->count() > 0)
            <x-table :headers="[
                ['label' => 'الطالب'],
                ['label' => 'عنوان الميلاد'],
                ['label' => 'الصف الدراسي'],
                ['label' => 'الحالة'],
                ['label' => 'الإجراءات'],
            ]">
                @foreach ($this->students as $student)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-4">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if ($student->user->avatar)
                                        <img
                                            class="h-10 w-10 rounded-full object-cover"
                                            src="{{ $student->user->avatar }}"
                                            alt="{{ $student->user->full_name }}"
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
                                        {{ $student->user->full_name }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $student->admission_number }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">
                                {{ $student->city }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $student->district }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">
                                {{ $student->grades?->first()?->name ?? 'غير مسجل' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($student->user->is_active)
                                <x-ui.badge variant="success">نشط</x-ui.badge>
                            @else
                                <x-ui.badge variant="danger">غير نشط</x-ui.badge>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <x-table.action-view
                                href="{{ route('dashboard.students.show', $student) }}"
                                :permissions="\Perm::StudentsView"
                            />
                        </td>
                    </tr>
                @endforeach
            </x-table>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $this->students->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-users text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                <p class="text-gray-500 dark:text-gray-400">لا توجد طلاب</p>
            </div>
        @endif
    </x-ui.card>
</div>
