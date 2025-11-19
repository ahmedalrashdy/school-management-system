<div>
    <x-ui.card>
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-tasks ml-2"></i>
                التعيينات الدراسية
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                عرض جميع المواد والشعب التي أنت مسؤول عن تدريسها
            </p>
            @php
                $yearsTree = lookup()->yearsTree();
                $years = $yearsTree->pluck('name', 'id');
            @endphp
            {{-- الفلاتر --}}
            <div
                x-data="academicController({
                    yearsTree: {{ $yearsTree->toJson() }},
                    defaultYear: @entangle('academicYearId').live,
                    defaultTerm: @entangle('academicTermId').live
                })"
                class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6"
            >
                <x-form.select
                    name="academicYearId"
                    label="السنة الدراسية"
                    :options="$years"
                    x-bind="yearInput"
                    placeholder="اختر السنة الدراسية"
                />
                <x-form.select
                    name="academicTermId"
                    label="الفصل الدراسي"
                    :options="[]"
                    x-bind="termInput"
                />
            </div>
        </div>

        {{-- الجدول --}}
        @if ($this->assignments->count() > 0)
            <x-table :headers="[
                ['label' => 'المادة'],
                ['label' => 'الصف'],
                ['label' => 'الشعبة'],
                ['label' => 'الفصل الدراسي'],
            ]">
                @foreach ($this->assignments as $assignment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <x-table.td nowrap>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-book text-primary-600 dark:text-primary-400"></i>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $assignment->curriculumSubject->subject->name }}
                                </span>
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="text-sm text-gray-900 dark:text-white">
                                {{ $assignment->section->grade->name }}
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <x-ui.badge variant="primary">
                                {{ $assignment->section->name }}
                            </x-ui.badge>
                        </x-table.td>
                        <x-table.td nowrap>
                            <x-ui.badge variant="info">
                                {{ $assignment->section->academicTerm->name }}
                            </x-ui.badge>
                        </x-table.td>
                    </tr>
                @endforeach
            </x-table>

            <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                <i class="fas fa-info-circle ml-1"></i>
                إجمالي التعيينات: <span class="font-semibold">{{ $this->assignments->count() }}</span>
            </div>
        @else
            <x-ui.empty-state
                icon="fas fa-tasks"
                title="لا توجد تعيينات دراسية"
                :description="$academicYearId
                    ? 'لا توجد تعيينات دراسية للعرض في الفترة المحددة'
                    : 'يرجى اختيار سنة دراسية لعرض التعيينات'"
            />
        @endif
    </x-ui.card>
</div>
