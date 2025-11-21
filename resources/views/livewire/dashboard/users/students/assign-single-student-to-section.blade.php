<div class="space-y-5">
    <div
        class="bg-primary-50 dark:bg-primary-900/10 border border-primary-100 dark:border-primary-800 rounded-lg p-4 flex items-center justify-between">
        <div>
            <h3 class="text-sm font-bold text-primary-900 dark:text-primary-100">
                {{ $student->user->full_name }}
            </h3>
            <div class="text-xs text-primary-600 dark:text-primary-400 mt-1 flex gap-3">
                <span><i class="fas fa-id-card ml-1"></i>{{ $student->admission_number }}</span>
                <span><i class="fas fa-graduation-cap ml-1"></i>{{ $grade_name ?? 'غير محدد' }}</span>
            </div>
        </div>
        <div class="hidden sm:block">
            <i class="fas fa-user-graduate text-3xl text-primary-200 dark:text-primary-700"></i>
        </div>
    </div>

    @if ($blockingReason)
        <div class="rounded-md bg-red-50 p-4 border border-red-200 dark:bg-red-900/20 dark:border-red-800">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="mr-3">
                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                        لا يمكن تعديل الشعبة
                    </h3>
                    <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                        <p>{{ $blockingReason }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="grid grid-cols-1 gap-4">
        <div>
            <x-form.select
                name="academic_term_id"
                label="الفصل الدراسي"
                :options="$this->terms"
                wire:model.live="academic_term_id"
                placeholder="اختر الفصل الدراسي"
            />
        </div>
        <div>
            <x-form.select
                name="section_id"
                label="الشعبة الدراسية"
                :options="$this->sections
                    ->mapWithKeys(fn($s) => [$s->id => $s->name . ($s->capacity ? ' (' . $s->capacity . ')' : '')])
                    ->toArray()"
                wire:model.live="section_id"
                placeholder="-- اختر الشعبة --"
                :disabled="!$canEdit || $this->sections->isEmpty()"
            />

            @if ($this->sections->isEmpty() && $academic_term_id)
                <p class="mt-1 text-xs text-yellow-600 dark:text-yellow-400">
                    <i class="fas fa-exclamation-triangle"></i>
                    لا توجد شعب دراسية مضافة لهذا الصف في هذا الفصل.
                </p>
            @endif
        </div>
    </div>

    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700 mt-2">
        @if ($canEdit)
            <x-ui.button
                wire:click="save"
                variant="primary"
                wire:loading.attr="disabled"
                :disabled="!$section_id"
            >
                <i class="fas fa-save ml-2"></i>
                حفظ التوزيع
            </x-ui.button>
        @else
            <span
                class="text-xs text-gray-400 select-none cursor-not-allowed px-4 py-2 border border-gray-200 rounded-lg"
            >
                التعديل غير متاح
            </span>
        @endif
    </div>
</div>
