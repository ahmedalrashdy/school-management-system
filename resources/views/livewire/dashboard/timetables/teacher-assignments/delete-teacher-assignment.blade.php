<x-ui.modal
    name="delete-assignment-modal"
    title="فك تعيين المدرس"
    maxWidth="md"
>
    <div
        x-data="{ id: null, teacher: '', subject: '', section: '' }"
        @set-delete-data.window="
                id = $event.detail.id;
                teacher = $event.detail.teacher;
                subject = $event.detail.subject;
                section = $event.detail.section;
             "
    >

        <div class="text-center mb-6">
            <div
                class="w-12 h-12 rounded-full bg-danger-100 dark:bg-danger-900/30 text-danger-600 mx-auto flex items-center justify-center mb-3">
                <i class="fas fa-user-slash text-xl"></i>
            </div>
            <h4 class="text-gray-900 dark:text-white font-medium mb-1">هل أنت متأكد؟</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400">سيتم حذف هذا التعيين من الجدول الدراسي.</p>
        </div>

        <div
            class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-6 space-y-2 text-sm border border-gray-200 dark:border-gray-700">
            <div class="flex justify-between">
                <span class="text-gray-500 dark:text-gray-400">المدرس:</span>
                <span
                    class="font-semibold text-gray-900 dark:text-white"
                    x-text="teacher"
                ></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500 dark:text-gray-400">المادة:</span>
                <span
                    class="font-semibold text-gray-900 dark:text-white"
                    x-text="subject"
                ></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500 dark:text-gray-400">الشعبة:</span>
                <span
                    class="font-semibold text-gray-900 dark:text-white"
                    x-text="section"
                ></span>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <button
                type="button"
                wire:click="destroy(id)"
                wire:loading.attr="disabled"
                class="px-4 py-2 text-sm font-medium text-white bg-danger-600 rounded-lg hover:bg-danger-700 transition flex items-center"
            >
                <i class="fas fa-trash-alt ml-2"></i>
                <span>تأكيد الحذف</span>
            </button>
        </div>
    </div>
</x-ui.modal>
