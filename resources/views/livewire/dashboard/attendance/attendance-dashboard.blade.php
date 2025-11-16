<div class="space-y-6">
    {{-- Header Bar with Date and Mode --}}
    <x-attendance.dashboard.header-bar
        :today-info="$this->todayInfo"
        :attendance-mode="$this->attendanceModeEnum"
        :selected-day-part="$selectedDayPart"
    />

    {{-- Global Summary Stats --}}
    <x-attendance.dashboard.summary-stats :stats="$this->summaryStats" />

    {{-- Quick Actions Card --}}
    <x-attendance.dashboard.quick-actions-card
        title="الأحداث السريعة"
        :actions="[
            [
                'title' => 'تقارير الحضور والغياب',
                'description' => 'عرض التقارير التفصيلية والإحصائيات',
                'url' => route('dashboard.attendance.reports.index'),
                'icon' => 'fa-chart-line',
            ],
        ]"
    />

    {{-- Day Part Tabs (Only for SplitDaily mode) --}}
    @if ($this->attendanceModeEnum === \App\Enums\AttendanceModeEnum::SplitDaily)
        <x-attendance.dashboard.day-part-tabs :selected-day-part="$selectedDayPart" />
    @endif

    {{-- Main Content based on Mode --}}
    @if ($this->attendanceModeEnum === \App\Enums\AttendanceModeEnum::PerPeriod)
        {{-- Per Period Mode: Tabs for grades then sections --}}
        <x-attendance.dashboard.per-period-view
            :grades="$this->grades"
            :selected-grade-id="$selectedGradeId"
            :selected-section-id="$selectedSectionId"
            :school-day="$this->currentSchoolDay"
        />
    @else
        {{-- Daily/SplitDaily Mode: Expandable Cards --}}
        <div class="space-y-4">
            @foreach ($this->grades as $grade)
                <x-attendance.dashboard.grade-accordion
                    :grade="$grade"
                    :is-expanded="in_array($grade['id'], $expandedGrades)"
                    :school-day="$this->currentSchoolDay"
                    :attendance-mode="$this->attendanceModeEnum"
                    :selected-day-part="$selectedDayPart"
                />
            @endforeach
        </div>
    @endif
    <x-attendance.dashboard.section-absent-list-modal />
</div>
