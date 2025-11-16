<div>
    <x-ui.main-content-header
        :title="$displayInfo['title']"
        :description="$displayInfo['subtitle']"
        button-text="رجوع"
        :button-link="$this->getBackRoute()"
    />
    <x-ui.card class="mb-6">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-{{ count($displayInfo['info']) }}">
            @foreach ($displayInfo['info'] as $label => $value)
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $label }}</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $value }}
                    </p>
                </div>
            @endforeach
        </div>
    </x-ui.card>

    {{-- جدول الطلاب --}}
    <x-ui.card>
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-users ml-2"></i>
                قائمة الطلاب
            </h3>
        </div>

        @if ($students->count() > 0)
            <div
                x-data="{
                    hasChanges: false,
                    resetChanges() {
                        this.hasChanges = false;
                    }
                }"
                x-on:change="hasChanges = true"
                @beforeunload.window="if(hasChanges) return 'لديك تغييرات غير محفوظة. هل أنت متأكد من المغادرة؟'"
            >
                <x-table :headers="[
                    ['label' => '#'],
                    ['label' => 'اسم الطالب'],
                    ['label' => 'حالة الحضور'],
                    ['label' => 'ملاحظات'],
                ]">
                    @foreach ($students as $index => $student)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <x-table.td nowrap>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $students->firstItem() + $index }}
                                </div>
                            </x-table.td>
                            <x-table.td nowrap>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $student->user->first_name }} {{ $student->user->last_name }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    رقم القيد: {{ $student->admission_number }}
                                </div>
                            </x-table.td>
                            <x-table.td nowrap>
                                <div
                                    class="flex flex-wrap gap-2"
                                    x-data="{
                                        selectedValue: @js($attendances[$student->id] ?? \App\Enums\AttendanceStatusEnum::Present->value),
                                        init() {
                                            // تحديث الحالة عند تغيير Livewire
                                            this.$watch('$wire.attendances[{{ $student->id }}]', (value) => {
                                                if (value !== undefined) {
                                                    this.selectedValue = value;
                                                }
                                            });
                                        }
                                    }"
                                >
                                    @foreach (\App\Enums\AttendanceStatusEnum::options() as $value => $label)
                                        <label
                                            class="relative inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium cursor-pointer transition"
                                            :class="selectedValue == {{ $value }} ?
                                                'bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200 border-2 border-primary-500' :
                                                'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border-2 border-transparent hover:bg-gray-200 dark:hover:bg-gray-600'"
                                        >
                                            <input
                                                class="sr-only"
                                                name="attendance[{{ $student->id }}]"
                                                type="radio"
                                                value="{{ $value }}"
                                                wire:model="attendances.{{ $student->id }}"
                                                @change="selectedValue = {{ $value }}; hasChanges = true"
                                            />
                                            {{ $label }}
                                        </label>
                                    @endforeach
                                </div>
                            </x-table.td>
                            <x-table.td>
                                <input
                                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                    type="text"
                                    wire:model="attendanceNotes.{{ $student->id }}"
                                    placeholder="ملاحظات..."
                                    @change="hasChanges = true"
                                />
                            </x-table.td>
                        </tr>
                    @endforeach
                </x-table>
                {{-- Pagination --}}
                <div class="mt-4">
                    {{ $students->links() }}
                </div>
                <div
                    class="mt-6 flex items-center justify-between gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <x-ui.button
                            type="button"
                            wire:click="save"
                            variant="outline"
                            x-on:click="resetChanges()"
                        >
                            <i class="fas fa-save ml-2"></i>
                            حفظ فقط
                        </x-ui.button>

                        @if ($students->hasMorePages())
                            <x-ui.button
                                type="button"
                                wire:click="saveAndGoToNextPage"
                                variant="primary"
                                x-on:click="resetChanges()"
                            >
                                <i class="fas fa-arrow-left ml-2"></i>
                                حفظ ومتابعة
                            </x-ui.button>
                        @else
                            <x-ui.button
                                type="button"
                                variant="success"
                                disabled
                            >
                                <i class="fas fa-check ml-2"></i>
                                تم حفظ جميع البيانات
                            </x-ui.button>
                        @endif
                    </div>

                    <x-ui.button
                        type="button"
                        wire:click="saveAndExit"
                        variant="primary"
                        x-on:click="resetChanges()"
                    >
                        <i class="fas fa-sign-out-alt ml-2"></i>
                        حفظ والخروج
                    </x-ui.button>
                </div>
            </div>
        @else
            <x-ui.empty-state
                icon="fas fa-users"
                title="لا يوجد طلاب في هذه الشعبة"
            />
        @endif
    </x-ui.card>
</div>
