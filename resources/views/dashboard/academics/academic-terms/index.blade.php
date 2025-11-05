<x-layouts.dashboard page-title="الفصول الدراسية">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'الفصول الدراسية', 'icon' => 'fas fa-calendar-week'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="الفصول الدراسية"
        description="إدارة الفصول الدراسية في النظام"
        button-text="إضافة فصل دراسي"
        :btnPermissions="\Perm::AcademicTermsCreate"
        button-link="{{ route('dashboard.academic-terms.create') }}"
    />
    <x-ui.filter-section :showReset="request()->has('academic_year_id')">
        <x-form.select
            name="academic_year_id"
            label="السنة الدراسية"
            :options="lookup()->getAcademicYears()"
            :selected="request()->input('academic_year_id', school()->activeYear()->id)"
            required
        />
    </x-ui.filter-section>
    <!-- Table -->
    <x-ui.card>
        @if ($academicTerms->count() > 0)
            <x-table :headers="[
                ['label' => 'الاسم'],
                ['label' => 'السنة الدراسية'],
                ['label' => 'تاريخ البداية'],
                ['label' => 'تاريخ النهاية'],
                ['label' => 'الحالة'],
                ['label' => 'الإجراءات'],
            ]">
                @foreach ($academicTerms as $term)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <x-table.td nowrap>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $term->name }}
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $term->academicYear->name }}
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $term->start_date->format('Y-m-d') }}
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $term->end_date->format('Y-m-d') }}
                            </div>
                        </x-table.td>
                        <x-table.td nowrap>
                            <x-ui.badge :variant="$term->is_active ? 'success' : 'default'">
                                {{ $term->is_active ? 'نشط' : 'غير نشط' }}
                            </x-ui.badge>
                        </x-table.td>
                        <x-table.td nowrap>
                            <div class="flex items-center gap-2">
                                @if ($term->is_active)
                                    <x-table.action
                                        icon="fas fa-toggle-on"
                                        variant="warning"
                                        title="تعطيل"
                                        :permissions="\Perm::AcademicTermsActivate"
                                        @click="$dispatch('open-modal', {
                                            name: 'toggle-active-academic-term',
                                            academicTerm: {
                                                id: {{ $term->id }},
                                                name: '{{ $term->name }}',
                                                isActive: {{ $term->is_active ? 'true' : 'false' }},
                                                route: '{{ route('dashboard.academic-terms.toggle-active', $term) }}'
                                            }
                                        })"
                                    />
                                @else
                                    <x-table.action
                                        icon="fas fa-toggle-off"
                                        variant="success"
                                        title="تفعيل"
                                        :permissions="\Perm::AcademicTermsActivate"
                                        @click="$dispatch('open-modal', {
                                            name: 'toggle-active-academic-term',
                                            academicTerm: {
                                                id: {{ $term->id }},
                                                name: '{{ $term->name }}',
                                                isActive: {{ $term->is_active ? 'true' : 'false' }},
                                                route: '{{ route('dashboard.academic-terms.toggle-active', $term) }}'
                                            }
                                        })"
                                    />
                                @endif
                                <x-table.action-edit
                                    :permissions="\Perm::AcademicTermsUpdate"
                                    href="{{ route('dashboard.academic-terms.edit', $term) }}"
                                />
                                <x-table.action-delete
                                    :permissions="\Perm::AcademicTermsDelete"
                                    @click="$dispatch('open-modal', {
                                        name: 'delete-academic-term',
                                        academicTerm: {
                                            id: {{ $term->id }},
                                            name: '{{ $term->name }}',
                                            route: '{{ route('dashboard.academic-terms.destroy', $term) }}'
                                        }
                                    })"
                                />
                            </div>
                        </x-table.td>
                    </tr>
                @endforeach
            </x-table>
            <!-- Pagination -->
            <div class="mt-4">
                {{ $academicTerms->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-calendar-times text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                <p class="text-gray-500 dark:text-gray-400">لا توجد فصول دراسية</p>
                <x-ui.button
                    as="a"
                    href="{{ route('dashboard.academic-terms.create') }}"
                    variant="primary"
                    class="mt-4"
                    :permissions="\Perm::AcademicTermsCreate"
                >
                    <i class="fas fa-plus"></i>
                    إضافة فصل دراسي جديد
                </x-ui.button>
            </div>
        @endif
    </x-ui.card>

    {{-- Toggle Active Confirmation Modal --}}
    <x-ui.confirm-action
        name="toggle-active-academic-term"
        dataKey="academicTerm"
        actionMethod="POST"
        confirmButtonVariant="success"
        :permissions="\Perm::AcademicTermsActivate"
    >
        <div x-show="academicTerm?.isActive">
            <p class="mb-4">هل أنت متأكد من تعطيل الفصل الدراسي <strong x-text="academicTerm?.name"></strong>؟</p>
        </div>
        <div x-show="!academicTerm?.isActive">
            <p class="mb-4">هل أنت متأكد من تفعيل الفصل الدراسي <strong x-text="academicTerm?.name"></strong>؟</p>
            <x-ui.warning-box>
                سيتم تعطيل أي فصل دراسي أخر نشط
            </x-ui.warning-box>
        </div>
        <x-slot:actions>
            <form
                class="inline"
                method="POST"
                x-bind:action="academicTerm?.route"
                x-show="academicTerm"
            >
                @csrf
                <button
                    type="submit"
                    class="inline-flex gap-1 items-center justify-center font-medium rounded-lg transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 px-4 py-2 text-sm"
                    :class="academicTerm?.isActive ? 'bg-warning-600 text-white hover:bg-warning-700 focus:ring-warning-500' :
                        'bg-success-600 text-white hover:bg-success-700 focus:ring-success-500'"
                >
                    <span x-text="academicTerm?.isActive ? 'تعطيل' : 'تفعيل'"></span>
                </button>
            </form>
        </x-slot:actions>
    </x-ui.confirm-action>

    {{-- Delete Confirmation Modal --}}
    <x-ui.confirm-action
        name="delete-academic-term"
        title="تأكيد حذف الفصل الدراسي"
        dataKey="academicTerm"
        spoofMethod="DELETE"
        confirmButtonText="حذف"
        confirmButtonVariant="danger"
        :permissions="\Perm::AcademicTermsDelete"
    >
        هل أنت متأكد من حذف الفصل الدراسي <strong x-text="academicTerm?.name"></strong>؟
    </x-ui.confirm-action>
</x-layouts.dashboard>
