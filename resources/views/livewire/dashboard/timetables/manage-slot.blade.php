<div>
    {{-- مؤشر تحميل يغطي الفورم عند تغيير الحصة --}}
    <div
        wire:loading
        wire:target="loadData"
        class="absolute inset-0 bg-white/80 dark:bg-gray-800/80 z-10 flex items-center justify-center rounded-lg"
    >
        <div class="flex flex-col items-center">
            <i class="fas fa-spinner fa-spin text-primary-600 text-3xl mb-2"></i>
            <span class="text-sm text-gray-600 dark:text-gray-300">جاري تحميل البيانات...</span>
        </div>
    </div>

    <form wire:submit="save">
        <div class="space-y-4">
            <x-form.select
                name="teacherAssignmentId"
                label="المادة والمدرس"
                :options="$this->availableAssignments
                    ->mapWithKeys(function ($assignment) {
                        $label =
                            $assignment->curriculumSubject->subject->name .
                            ' - ' .
                            $assignment->teacher->user->full_name;
                        return [$assignment->id => $label];
                    })
                    ->toArray()"
                wire:model.live="teacherAssignmentId"
                placeholder="اختر المادة والمدرس"
                required
            />
            <x-form.input
                wire:model="durationMinutes"
                name="durationMinutes"
                type="number"
                label="مدة الحصة (بالدقائق)"
                min="15"
                max="180"
                step="5"
                required
            />
        </div>

        @if ($hasConflict)
            <x-ui.alert
                type="warning"
                class="mt-4"
            >
                {!! $conflictMessage !!}
            </x-ui.alert>
        @endif

        <div class="flex items-center justify-end gap-3 pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
            <x-ui.button
                type="submit"
                variant="primary"
                wire:loading.attr="disabled"
            >
                <i class="fas fa-save ml-2"></i>
                <span
                    wire:loading.remove
                    wire:target="save"
                >حفظ</span>
                <span
                    wire:loading
                    wire:target="save"
                >جاري الحفظ...</span>
            </x-ui.button>
        </div>
    </form>
</div>
