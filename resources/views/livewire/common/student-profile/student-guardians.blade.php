<div>
    <x-ui.card>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-users mr-2"></i>
                أولياء الأمور
            </h3>
            @if ($context === 'dashboard' && auth()->user()->can(\Perm::StudentsManageGuardians))
                <x-ui.button
                    type="button"
                    @click="$dispatch('open-modal', { name: 'attach-guardian-modal' })"
                    variant="primary"
                >
                    <i class="fas fa-plus"></i>
                    ربط ولي أمر
                </x-ui.button>
            @endif
        </div>
        @php
            $headers = [
                ['label' => 'الاسم'],
                ['label' => 'صلة القرابة'],
                ['label' => 'رقم الهاتف'],
                ['label' => 'البريد الإلكتروني'],
            ];
            if ($context === 'dashboard') {
                $headers[] = ['label' => 'الإجراءات'];
            }
        @endphp

        @if ($this->guardians->count() > 0)
            <x-table :headers="$headers">
                @foreach ($this->guardians as $guardian)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <x-table.td nowrap>
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if ($guardian->user->avatar)
                                        <img
                                            class="h-10 w-10 rounded-full object-cover"
                                            src="{{ $guardian->user->avatar }}"
                                            alt="{{ $guardian->user->full_name }}"
                                        >
                                    @else
                                        <div
                                            class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                            <i class="fas fa-user text-gray-500 dark:text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $guardian->user->full_name }}
                                </div>
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="text-sm text-gray-900 dark:text-white">
                                {{ \App\Enums\RelationToStudentEnum::from($guardian->pivot->relation_to_student)->label() }}
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="text-sm text-gray-900 dark:text-white">
                                {{ $guardian->user->phone_number }}
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="text-sm text-gray-900 dark:text-white">
                                {{ $guardian->user->email }}
                            </div>
                        </x-table.td>
                        @if ($context === 'dashboard' && auth()->user()->can(\Perm::StudentsManageGuardians))
                            <x-table.td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button
                                    wire:click="detachGuardian({{ $guardian->id }})"
                                    wire:confirm="هل أنت متأكد من فك ارتباط ولي الأمر هذا؟"
                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                >
                                    <i class="fas fa-unlink mr-1"></i>
                                    فك الارتباط
                                </button>
                            </x-table.td>
                        @endif
                    </tr>
                @endforeach
            </x-table>
        @else
            <div class="text-center py-12">
                <i class="fas fa-users text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                <p class="text-gray-500 dark:text-gray-400">لا يوجد أولياء أمور مرتبطين بهذا الطالب</p>
            </div>
        @endif
    </x-ui.card>
    @if ($context == 'dashboard')
        <x-ui.modal
            name='attach-guardian-modal'
            title="ربط الطالب بولي أمر"
            maxWidth="2xl"
        >
            <template x-if="show">
                <form
                    wire:submit.prevent="addGuardian"
                    x-data="{ guardian: @entangle('guardianId') }"
                >
                    <x-form.autocomplete
                        name="guardianId"
                        label="ولي الأمر"
                        xModel="guardian"
                        resource="guardians"
                    />
                    <x-form.select
                        label="العلاقة"
                        :options="\App\Enums\RelationToStudentEnum::options()"
                        name="relationToStudent"
                        placeholder="اختر العلاقة بين الطالب و ولي الأمر"
                        wire:model="relationToStudent"
                    />
                    <x-ui.button type="submit">حفظ</x-ui.button>
                </form>
            </template>

        </x-ui.modal>
    @endif
</div>
