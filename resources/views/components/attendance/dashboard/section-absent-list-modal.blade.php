{{-- Section Students Modal --}}
<x-ui.modal
    name="section-students-modal"
    title=""
    max-width="2xl"
>
    <div class="space-y-3 max-h-[60vh] overflow-y-auto custom-scrollbar p-1">
        {{-- Students List --}}
        <template x-if="data && data.students && data.students.length > 0">
            <div class="space-y-2.5">
                <template
                    x-for="student in data.students"
                    :key="student.id"
                >
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl p-3 flex items-center justify-between border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-all">

                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            {{-- Student Icon --}}
                            <div
                                class="w-10 h-10 shrink-0 bg-linear-to-br from-rose-400 to-rose-600 rounded-full flex items-center justify-center shadow-rose-500/20 shadow-md text-white font-bold text-sm">
                                <span x-text="student.name.charAt(0)"></span>
                            </div>

                            {{-- Student Details --}}
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2.5 mb-1">
                                    <p
                                        class="font-bold text-gray-900 dark:text-white text-base truncate"
                                        x-text="student.name"
                                    ></p>
                                    {{-- Status Badge --}}
                                    <span
                                        class="shrink-0 px-2.5 py-1 rounded-md text-[11px] font-bold"
                                        :class="student.status ?
                                            'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-400' :
                                            'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'"
                                    >
                                        <span x-text="student.status ? 'غائب' : 'غير مسجل'"></span>
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-hashtag text-gray-400 text-[10px]"></i>
                                    <span
                                        class="text-xs text-gray-500 dark:text-gray-400 font-semibold"
                                        x-text="student.admission_number"
                                    ></span>
                                </div>
                            </div>
                        </div>

                        {{-- Guardian Section (Right) --}}
                        <div class="flex items-center gap-3 shrink-0">
                            <template x-if="student.guardian_name || student.guardian_phone">
                                <div
                                    class="flex flex-col gap-2.5 min-w-[220px] bg-gray-50 dark:bg-gray-900/40 rounded-lg p-3 border border-gray-200 dark:border-gray-700/50">
                                    {{-- Guardian Relation Badge --}}
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-6 h-6 bg-primary-100 dark:bg-primary-900/30 rounded-md flex items-center justify-center">
                                            <i
                                                class="fas fa-user-shield text-primary-600 dark:text-primary-400 text-[10px]"></i>
                                        </div>
                                        <span
                                            class="text-xs font-bold text-primary-700 dark:text-primary-300"
                                            x-text="student.relation_label || 'ولي الأمر'"
                                        ></span>
                                    </div>

                                    {{-- Guardian Name --}}
                                    <template x-if="student.guardian_name">
                                        <p
                                            class="font-bold text-gray-900 dark:text-white text-sm leading-tight"
                                            x-text="student.guardian_name"
                                        ></p>
                                    </template>

                                    {{-- Phone Button --}}
                                    <template x-if="student.guardian_phone">
                                        <a
                                            :href="'tel:' + student.guardian_phone"
                                            class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 text-white rounded-lg text-xs font-bold transition-all shadow-sm hover:shadow-md hover:scale-[1.02] active:scale-[0.98]"
                                            title="اتصال بولي الأمر"
                                        >
                                            <i class="fas fa-phone text-[10px]"></i>
                                            <span x-text="student.guardian_phone"></span>
                                        </a>
                                    </template>
                                    <template x-if="!student.guardian_phone && student.guardian_name">
                                        <span class="text-xs text-gray-400 dark:text-gray-500 italic">لا يوجد رقم
                                            تواصل</span>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!student.guardian_name && !student.guardian_phone">
                                <div
                                    class="flex flex-col items-center justify-center min-w-[220px] py-4 bg-gray-50 dark:bg-gray-900/40 rounded-lg border border-gray-200 dark:border-gray-700/50">
                                    <div
                                        class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mb-2">
                                        <i class="fas fa-user-slash text-gray-400 dark:text-gray-500 text-sm"></i>
                                    </div>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 text-center font-medium">لا
                                        يوجد ولي أمر مسجل</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        {{-- Empty State --}}
        <template x-if="!data || !data.students || (data.students && data.students.length === 0)">
            <div class="text-center py-12">
                <div
                    class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-gray-400 text-2xl"></i>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-sm">لا يوجد طلاب غائبين</p>
            </div>
        </template>
    </div>
</x-ui.modal>
