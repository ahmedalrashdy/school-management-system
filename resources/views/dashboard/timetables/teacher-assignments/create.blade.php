<div
    x-data="{ show: @entangle('showCreateModal') }"
    x-show="show"
    x-on:keydown.escape.window="$wire.closeCreateModal()"
    x-trap.inert.noscroll="show"
    style="display: none;"
    class="fixed inset-0 z-50 overflow-y-auto"
    role="dialog"
    aria-modal="true"
>
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block w-full max-w-md p-6 my-8 text-right align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl"
        >
            <div class="flex items-center justify-between mb-4">
                <button wire:click="closeCreateModal" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition">
                    <span class="sr-only">إغلاق</span>
                    <i class="fas fa-times text-xl"></i>
                </button>
                
                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">
                    تعيين مدرس
                </h3>
            </div>

            <form wire:submit.prevent="store" class="space-y-4">
                @if($this->selectedSection && $this->selectedCurriculumSubject)
                    <div class="bg-primary-50 dark:bg-primary-900/20 rounded-lg p-4 mb-4">
                        <div class="text-sm">
                            <span class="font-medium text-primary-900 dark:text-primary-100">المادة:</span>
                            <span class="text-primary-700 dark:text-primary-300">{{ $this->selectedCurriculumSubject->subject->name }}</span>
                        </div>
                        <div class="text-sm mt-1">
                            <span class="font-medium text-primary-900 dark:text-primary-100">الشعبة:</span>
                            <span class="text-primary-700 dark:text-primary-300">{{ $this->selectedSection->name }}</span>
                        </div>
                    </div>
                @endif

                <div>
                    <x-form.autocomplete
                        name="selectedTeacherId"
                        label="اختر المدرس"
                        resource="teachers"
                        placeholder="ابحث عن مدرس..."
                        search-placeholder="ابحث بالاسم أو رقم الهاتف أو البريد الإلكتروني"
                        wire:model="selectedTeacherId"
                    />
                    @error('selectedTeacherId')
                        <span class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4">
                    <x-ui.button
                        type="button"
                        wire:click="closeCreateModal"
                        variant="outline"
                        size="sm"
                    >
                        إلغاء
                    </x-ui.button>
                    <x-ui.button
                        type="submit"
                        variant="primary"
                        size="sm"
                    >
                        <i class="fas fa-save"></i>
                        حفظ
                    </x-ui.button>
                </div>
            </form>
        </div>
    </div>
</div>
