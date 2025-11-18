@props(['student_id'])
<x-ui.card>
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
        <i class="fas fa-book mr-2"></i>
        السجل الأكاديمي
    </h3>

    @if ($this->enrollments->count() > 0)
        <div class="space-y-6">
            @foreach ($this->enrollments as $enrollment)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-md font-semibold text-gray-900 dark:text-white">
                            {{ $enrollment->academicYear->name }}
                        </h4>
                        <x-ui.badge variant="info">
                            {{ $enrollment->grade->name }} - {{ $enrollment->grade->stage->name }}
                        </x-ui.badge>
                    </div>

                    @php
                        $yearSections = $this->sections->where('academic_year_id', $enrollment->academic_year_id);
                    @endphp

                    @if ($yearSections->count() > 0)
                        <div class="mt-3">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">الشعب الدراسية:</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($yearSections as $section)
                                    <x-ui.badge variant="secondary">
                                        {{ $section->name }} ({{ $section->academicTerm->name }})
                                    </x-ui.badge>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8">
            <i class="fas fa-book text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
            <p class="text-gray-500 dark:text-gray-400">لا يوجد سجل أكاديمي متاح</p>
        </div>
    @endif
</x-ui.card>
