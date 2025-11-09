@props([
    'academicYearId' => null,
    'academicTerm' => null,
    'sectionId' => null,
    'subjectId' => null,
    'academicYears' => [],
    'academicTerms' => [],
    'sections' => [],
    'subjects' => [],
])

<div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">
        <i class="fas fa-filter text-primary-500 mr-2"></i>
        الفلترة
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @if(count($academicYears) > 0)
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">العام الدراسي</label>
                <select wire:model.live="academicYearId" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    <option value="">جميع الأعوام</option>
                    @foreach($academicYears as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        @if(count($academicTerms) > 0)
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الفصل الدراسي</label>
                <select wire:model.live="academicTerm" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    <option value="">جميع الفصول</option>
                    @foreach($academicTerms as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        @if(count($sections) > 0)
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الشعبة</label>
                <select wire:model.live="sectionId" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    <option value="">جميع الشعب</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}">
                            {{ $section->grade->name }} - {{ $section->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        @if(count($subjects) > 0)
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">المادة</label>
                <select wire:model.live="subjectId" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    <option value="">جميع المواد</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>
</div>

