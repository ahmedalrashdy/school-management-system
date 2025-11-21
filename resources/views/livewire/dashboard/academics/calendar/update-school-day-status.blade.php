<x-ui.modal
    name="edit-day-modal"
    title="تعديل حالة اليوم الدراسي"
    :skipTeleport="true"
>
    <div
        x-data="{
            date_formatted: '',
            status: @entangle('form.status'),
            init() {
                this.$watch('data', value => {
                    if (value) {
                        this.date_formatted = value.date_formatted;
                    }
                });
            }
        }"
        class="space-y-4"
    >
        <div
            class="rounded-lg bg-gray-50 p-3 text-center dark:bg-gray-700/50 border border-gray-100 dark:border-gray-700">
            <span class="block text-xs text-gray-500 dark:text-gray-400">تاريخ اليوم المحدد</span>
            <span
                class="font-bold text-gray-900 dark:text-white"
                x-text="date_formatted"
            ></span>
        </div>
        <form wire:submit="updateDayStatus">
            <div @class([
                'grid gap-4',
                'pointer-events-none opacity-50 select-none cursor-not-allowed' => !$form->canChangeStatus,
            ])>
                <div>
                    <label class="mb-2  block text-sm font-medium text-gray-900 dark:text-white">
                        حالة اليوم <span @class(['hidden ', 'inline-block text-xs' => !$form->canChangeStatus])>(لا يمكن تعديل حالة اليوم لأن هناك سجل حضور
                            مرتبط
                            )</span>
                    </label>

                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-3 ">
                        @foreach (\App\Enums\SchoolDayType::options() as $value => $label)
                            <label
                                class="relative flex cursor-pointer  rounded-lg border p-3 shadow-sm focus:outline-none"
                                :class="status == {{ $value }} ?
                                    'border-primary-500 ring-1 ring-primary-500 bg-primary-50 dark:bg-primary-900/20 dark:border-primary-400' :
                                    'border-gray-300 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:hover:bg-gray-700'"
                            >

                                <input
                                    type="radio"
                                    wire:model="form.status"
                                    value="{{ $value }}"
                                    class="sr-only"
                                >

                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $label }}
                                        </span>
                                    </span>
                                </span>
                                <i
                                    class="fas fa-check-circle text-primary-600 dark:text-primary-400"
                                    x-show="status == {{ $value }}"
                                ></i>
                            </label>
                        @endforeach
                    </div>
                    @error('form.status')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                {{-- 2. جزء الدوام (يظهر فقط عند اختيار عطلة جزئية) --}}
                <div
                    x-show="status == {{ \App\Enums\SchoolDayType::PartialHoliday->value }}"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="rounded-lg border border-amber-200 bg-amber-50 p-4 dark:bg-amber-900/10 dark:border-amber-800"
                    style="display: none;"
                >

                    <label class="mb-2 block text-sm font-medium text-amber-900 dark:text-amber-100">
                        تحديد وقت الدوام (العطلة الجزئية)
                    </label>

                    <select
                        wire:model="form.part"
                        class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-amber-500 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-amber-500 dark:focus:ring-amber-500"
                    >
                        <option value="{{ \App\Enums\DayPartEnum::PART_ONE_ONLY->value }}">
                            {{ \App\Enums\DayPartEnum::PART_ONE_ONLY->label() }}
                        </option>
                        <option value="{{ \App\Enums\DayPartEnum::PART_TWO_ONLY->value }}">
                            {{ \App\Enums\DayPartEnum::PART_TWO_ONLY->label() }}
                        </option>
                    </select>

                    <p class="mt-1 text-xs text-amber-600/80 dark:text-amber-400">
                        اختر الجزء الذي سيكون فيه دوام دراسي.
                    </p>
                    @error('form.part')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

            </div>
            <div class="mt-4">
                <label class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">ملاحظات
                    (اختياري)</label>
                <textarea
                    wire:model="form.notes"
                    rows="3"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500"
                    placeholder="اكتب سبب العطلة أو التعديل هنا..."
                ></textarea>
                @error('form.notes')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>
            {{-- Action Buttons --}}
            <div class="mt-6 flex items-center justify-end gap-3">
                <button
                    type="submit"
                    class="rounded-lg bg-primary-700 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
                >
                    <span
                        wire:loading.remove
                        wire:target="updateDayStatus"
                    >حفظ التغييرات</span>
                    <span
                        wire:loading
                        wire:target="updateDayStatus"
                    >جاري الحفظ...</span>
                </button>
            </div>
        </form>
    </div>
</x-ui.modal>
