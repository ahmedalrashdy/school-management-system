<div
    x-data="{ show: @entangle('showDeleteModal') }"
    x-show="show"
    x-on:keydown.escape.window="$wire.closeDeleteModal()"
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
                <button wire:click="closeDeleteModal" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition">
                    <span class="sr-only">إغلاق</span>
                    <i class="fas fa-times text-xl"></i>
                </button>
                
                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">
                    فك تعيين المدرس
                </h3>
            </div>

            <div class="space-y-4">
                <x-ui.alert type="warning">
                    <div class="font-bold mb-2">تنبيه: هل أنت متأكد من فك تعيين هذا المدرس؟</div>
                    <p class="text-sm">
                        سيؤدي هذا الإجراء إلى حذف جميع الحصص الدراسية المجدولة لهذه المادة في هذه الشعبة من الجدول الدراسي بشكل نهائي.
                    </p>
                </x-ui.alert>

                @if($assignmentData)
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">المادة:</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $assignmentData->subject }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">الشعبة:</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $assignmentData->section }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">المدرس:</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $assignmentData->teacher }}</span>
                        </div>
                    </div>
                @endif

                <div class="flex items-center justify-end gap-3 pt-4">
                    <x-ui.button
                        type="button"
                        wire:click="closeDeleteModal"
                        variant="outline"
                        size="sm"
                    >
                        إلغاء
                    </x-ui.button>
                    <x-ui.button
                        type="button"
                        wire:click="destroy"
                        variant="danger"
                        size="sm"
                    >
                        <i class="fas fa-trash"></i>
                        حذف التعيين
                    </x-ui.button>
                </div>
            </div>
        </div>
    </div>
</div>
