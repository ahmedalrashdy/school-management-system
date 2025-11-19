<div>
    @if (empty($this->years))
        @php
            $isStudent = auth()->user()->hasRole('طالب');
            $isGuardian = auth()->user()->hasRole('ولي أمر');
        @endphp
        <x-ui.student-has-no-section
            :student="$this->student"
            :isStudentDashboardLayout="$isStudent"
            :isGuardianDashboard="$isGuardian"
        />
    @else
        <x-ui.card class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <x-form.select
                    name="academicYearId"
                    label="العام الدراسي"
                    :options="$this->years"
                    wire:model.live="academicYearId"
                />

                <x-form.select
                    name="gradeId"
                    label="الصف الدراسي"
                    placeholder="الصف الدراسي"
                    :options="$this->grades"
                    wire:model.live="gradeId"
                    :disabled="!$academicYearId"
                />

                <x-form.select
                    name="termId"
                    label="الترم الدراسي"
                    placeholder="الترم الدراسي"
                    :options="$this->terms"
                    wire:model.live="termId"
                    :disabled="!$gradeId"
                />
            </div>
        </x-ui.card>

        <div
            wire:loading
            wire:target="loadGrades, termId"
            class="w-full text-center py-8"
        >
            <div
                class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-primary-500 border-t-transparent">
            </div>
            <p class="mt-2 text-gray-500">جارِ تحميل الدرجات...</p>
        </div>

        @if ($dataLoaded)
            @if ($section)
                <x-student.marks-details
                    :subjects="$subjects"
                    :section="$section"
                />
            @else
                <div class="text-center py-8 text-gray-500">
                    لا توجد بيانات لهذه الفترة
                </div>
            @endif
        @endif
    @endif
</div>
