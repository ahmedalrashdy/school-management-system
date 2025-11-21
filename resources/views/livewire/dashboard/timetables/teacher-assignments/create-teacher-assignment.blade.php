<x-ui.modal
    name="create-assignment-modal"
    title="تعيين مدرس"
    maxWidth="md"
>
    <div
        x-data="{
            sectionId: null,
            curriculumSubjectId: null,
            subjectName: '',
            sectionName: '',
            newTeacherId: @entangle('newTeacherId')
        }"
        @set-create-data.window="
        sectionId = $event.detail.sectionId;
        curriculumSubjectId = $event.detail.curriculumSubjectId;
        subjectName = $event.detail.subjectName;
        sectionName = $event.detail.sectionName;
     "
    >

        <div
            class="bg-primary-50 dark:bg-primary-900/20 rounded-lg p-4 mb-4 border border-primary-100 dark:border-primary-800">
            <div class="flex justify-between items-center mb-1">
                <span class="text-xs text-primary-600 dark:text-primary-400">المادة</span>
                <span
                    class="text-sm font-bold text-primary-800 dark:text-primary-200"
                    x-text="subjectName"
                ></span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-xs text-primary-600 dark:text-primary-400">الشعبة</span>
                <span
                    class="text-sm font-bold text-primary-800 dark:text-primary-200"
                    x-text="sectionName"
                ></span>
            </div>
        </div>

        <form wire:submit.prevent="store(sectionId, curriculumSubjectId)">
            <div class="mb-6">
                <x-form.autocomplete
                    name="newTeacherId"
                    label="اختر المدرس"
                    resource="teachers"
                    placeholder="ابحث عن مدرس..."
                    search-placeholder="الاسم، الهاتف..."
                    xModel="newTeacherId"
                />
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100 dark:border-gray-700">
                <x-ui.button
                    type="submit"
                    variant="primary"
                    wire:loading.attr="disabled"
                >
                    <i class="fas fa-save ml-1"></i>
                    حفظ
                </x-ui.button>
            </div>
        </form>
    </div>
</x-ui.modal>
