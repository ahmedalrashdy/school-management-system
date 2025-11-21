<div>
    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
        <i class="fas fa-users mr-2"></i>
        إدارة أولياء الأمور
    </h3>



    @foreach ($studentGuardians->guardians as $index => $guardian)
        <div
            {{-- هام جداً لعمل الـ Hooks --}}
            wire:key="guardian-field-{{ $index }}"
            class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-lg {{ $guardian['is_existing'] ?? false ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-300 dark:border-blue-700' : '' }}"
        >

            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                    ولي أمر {{ $index + 1 }}
                    @if ($guardian['is_existing'] ?? false)
                        <span class="ml-2 text-sm text-blue-600 dark:text-blue-400">
                            <i class="fas fa-check-circle mr-1"></i>
                            (موجود في النظام)
                        </span>
                    @endif
                </h4>
                @if (count($studentGuardians->guardians) > 1 && $index != 0)
                    <button
                        class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300"
                        type="button"
                        wire:click="removeGuardian({{ $index }})"
                    >
                        <i class="fas fa-trash"></i>
                    </button>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- البريد الإلكتروني --}}
                <x-form.input
                    name="studentGuardians.guardians.{{ $index }}.email"
                    label="البريد الإلكتروني"
                    wire:model.live.blur="studentGuardians.guardians.{{ $index }}.email"
                    required
                    placeholder="ادخل البريد الإلكتروني"
                    :readonly="$guardian['is_existing'] ?? false"
                />

                <x-form.input
                    name="studentGuardians.guardians.{{ $index }}.phone_number"
                    label="رقم الهاتف"
                    wire:model.blur="studentGuardians.guardians.{{ $index }}.phone_number"
                    required
                    placeholder="ادخل رقم الهاتف"
                    :readonly="$guardian['is_existing'] ?? false"
                />

                <x-form.input
                    name="studentGuardians.guardians.{{ $index }}.first_name"
                    label="الاسم الأول"
                    wire:model="studentGuardians.guardians.{{ $index }}.first_name"
                    required
                    placeholder="ادخل الاسم الأول"
                    :readonly="$guardian['is_existing'] ?? false"
                />

                <x-form.input
                    name="studentGuardians.guardians.{{ $index }}.last_name"
                    label="اسم العائلة"
                    wire:model="studentGuardians.guardians.{{ $index }}.last_name"
                    required
                    placeholder="ادخل اسم العائلة"
                    :readonly="$guardian['is_existing'] ?? false"
                />

                <x-form.select
                    name="studentGuardians.guardians.{{ $index }}.gender"
                    label="الجنس"
                    wire:model="studentGuardians.guardians.{{ $index }}.gender"
                    :options="$genders"
                    required
                    placeholder="اختر الجنس"
                    :disabled="$guardian['is_existing'] ?? false"
                />

                <x-form.input
                    name="studentGuardians.guardians.{{ $index }}.occupation"
                    label="المهنة"
                    wire:model="studentGuardians.guardians.{{ $index }}.occupation"
                    placeholder="ادخل المهنة"
                    :readonly="$guardian['is_existing'] ?? false"
                />

                <div class="md:col-span-2">
                    <x-form.textarea
                        name="studentGuardians.guardians.{{ $index }}.address"
                        label="العنوان"
                        wire:model="studentGuardians.guardians.{{ $index }}.address"
                        placeholder="ادخل العنوان بالتفصيل"
                        rows="2"
                        :readonly="$guardian['is_existing'] ?? false"
                    />
                </div>

                <div class="md:col-span-2">
                    <x-form.select
                        name="studentGuardians.guardians.{{ $index }}.relation_to_student"
                        label="صلة القرابة بالطالب"
                        wire:model="studentGuardians.guardians.{{ $index }}.relation_to_student"
                        :options="$this->relations"
                        required
                        placeholder="اختر صلة القرابة"
                    />
                </div>
            </div>
        </div>
    @endforeach

    <button
        class="mt-4 w-full md:w-auto px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200"
        type="button"
        {{-- تصحيح: استدعاء الدالة من الفورم --}}
        wire:click="addGuardian"
    >
        <i class="fas fa-plus mr-2"></i>
        إضافة ولي أمر آخر
    </button>
</div>
