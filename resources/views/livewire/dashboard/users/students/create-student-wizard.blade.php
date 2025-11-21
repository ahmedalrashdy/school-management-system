<div>
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'الطلاب', 'url' => route('dashboard.students.index'), 'icon' => 'fas fa-user-graduate'],
            ['label' => 'إضافة طالب جديد', 'icon' => 'fas fa-user-plus'],
        ]" />
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                @for ($step = 1; $step <= $totalSteps; $step++)
                    <div class="flex items-center flex-1">
                        <div
                            class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-colors duration-200
                                    {{ $currentStep >= $step ? 'bg-primary-600 border-primary-600 text-white' : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400' }}">
                            @if ($currentStep > $step)
                                <i class="fas fa-check text-sm"></i>
                            @else
                                <span class="text-sm font-medium">{{ $step }}</span>
                            @endif
                        </div>
                        @if ($step < $totalSteps)
                            <div
                                class="flex-1 h-1 mx-2 transition-colors duration-200
                                            {{ $currentStep > $step ? 'bg-primary-600' : 'bg-gray-300 dark:bg-gray-600' }}">
                            </div>
                        @endif
                    </div>
                @endfor
            </div>
            <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400">
                <span
                    class="{{ $currentStep >= 1 ? 'text-primary-600 dark:text-primary-400 font-medium' : '' }}">البيانات
                    الأساسية</span>
                <span
                    class="{{ $currentStep >= 2 ? 'text-primary-600 dark:text-primary-400 font-medium' : '' }}">المعلومات
                    الأكاديمية</span>
                <span class="{{ $currentStep >= 3 ? 'text-primary-600 dark:text-primary-400 font-medium' : '' }}">أولياء
                    الأمور</span>
                <span
                    class="{{ $currentStep >= 4 ? 'text-primary-600 dark:text-primary-400 font-medium' : '' }}">التسجيل
                    الأكاديمي</span>
            </div>
        </div>

        <!-- Wizard Content -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <!-- Step 1: Basic Information -->
            @if ($currentStep === 1)
                @include('livewire.dashboard.users.students.steps.student-basic-info-step')
            @elseif ($currentStep === 2)
                @include('livewire.dashboard.users.students.steps.student-academic-info-step')
            @elseif ($currentStep === 3)
                @include('livewire.dashboard.users.students.steps.student-guardians-step ')
            @elseif ($currentStep === 4)
                @include('livewire.dashboard.users.students.steps.student-enrollment-step')
            @endif
            <!-- Navigation Buttons -->
            <div class="mt-8 flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                <button
                    class="px-6 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    type="button"
                    wire:click="previousStep"
                    @if ($currentStep === 1) disabled @endif
                >
                    <i class="fas fa-arrow-right mr-2"></i>
                    السابق
                </button>

                @if ($currentStep < $totalSteps)
                    <button
                        class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors duration-200"
                        type="button"
                        wire:click="nextStep"
                    >
                        التالي
                        <i class="fas fa-arrow-left mr-2"></i>
                    </button>
                @else
                    <button
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        type="button"
                        wire:click="save"
                        wire:target="save"
                        wire:loading.attr="disabled"
                    >
                        <span
                            wire:loading.remove
                            wire:target="save"
                        >
                            <i class="fas fa-save mr-2"></i>
                            إنشاء وحفظ الطالب
                        </span>
                        <span
                            wire:loading
                            wire:target="save"
                        >
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            جاري الحفظ...
                        </span>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
