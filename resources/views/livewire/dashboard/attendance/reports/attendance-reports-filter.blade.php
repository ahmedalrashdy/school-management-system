<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
            <i class="fas fa-filter text-primary-500 ml-2"></i>
            تصفية التقارير
        </h3>
    </div>

    @php
        $yearsTree = lookup()->yearsTree();
        $years = $yearsTree->pluck('name', 'id');
        $grades = lookup()->getGrades();
    @endphp

    <div
        x-data="academicController({
            yearsTree: {{ $yearsTree->toJson() }},
            defaultYear: @entangle('academicYearId').live,
            defaultTerm: @entangle('academicTermId').live
        })"
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end"
    >
        {{-- Academic Year --}}
        <div class="w-full">
            <x-form.select
                name="academicYearId"
                label="العام الدراسي"
                :options="$years"
                x-bind="yearInput"
                required
            />
        </div>

        {{-- Grade --}}
        <div class="w-full">
            <x-form.select
                name="gradeId"
                label="الصف الدراسي"
                :options="$grades"
                wire:model.live="gradeId"
            />
        </div>

        {{-- Academic Term --}}
        <div class="w-full">
            <x-form.select
                name="academicTermId"
                label="الفصل الدراسي"
                :options="[]"
                x-bind="termInput"
            />
        </div>
        {{-- Section --}}
        <div class="w-full">
            <x-form.select
                name="sectionId"
                label="الشعبة"
                :options="$this->sections->pluck('name', 'id')->toArray()"
                wire:model.live="sectionId"
                placeholder="اختر الشعبة"
                :disabled="!$this->academicTermId || !$this->gradeId"
            />
        </div>

        @if ($academicTermId)
            <div class="w-full">
                <x-form.input
                    type="date"
                    name="startDate"
                    label="من تاريخ"
                    wire:model.live="startDate"
                    :min="$minDate"
                    :max="$maxDate"
                />
            </div>
            <div class="w-full">
                <x-form.input
                    type="date"
                    name="endDate"
                    label="إلى تاريخ"
                    wire:model.live="endDate"
                    :min="$minDate"
                    :max="$maxDate"
                />
            </div>
        @endif

        {{-- Apply Button --}}
        <div class="col-span-2 md:col-span-1 lg:col-span-1 flex gap-2 w-full">
            {{-- Wrapper to stack or side-by-side depending on space --}}
            <x-ui.button
                wire:click="apply"
                class="flex-1 justify-center"
                icon="fas fa-search"
                wire:loading.attr="disabled"
            >
                عرض
            </x-ui.button>
            <x-ui.button
                wire:click="exportPdf"
                class="flex-1 justify-center"
                color="danger"
                icon="fas fa-file-pdf"
                wire:loading.attr="disabled"
                wire:target="exportPdf"
            >
                PDF
            </x-ui.button>
        </div>
    </div>
</div>
