<x-layouts.dashboard page-title="إدارة أدوار المستخدم">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'المستخدمون', 'url' => route('dashboard.users.index'), 'icon' => 'fas fa-users'],
            ['label' => 'إدارة أدوار المستخدم', 'icon' => 'fas fa-user-shield'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="إدارة أدوار المستخدم"
        description="إضافة أو إزالة الأدوار للمستخدم: {{ $user->full_name }}"
        button-text="رجوع"
        button-link="{{ route('dashboard.users.index') }}"
    />

    <x-ui.card>
        <form
            method="POST"
            action="{{ route('dashboard.users.update-roles', $user) }}"
            x-data="{
                validateForm(event) {
                        const checked = document.querySelectorAll('input[name=\"roles[]\"]:checked');
            if
            (checked.length===0)
            {
            event.preventDefault();
            alert('يجب
            تحديد
            دور
            واحد
            على
            الأقل
            للمستخدم.');
            return
            false;
            }
            }
            }"
            @submit="validateForm($event)"
        >
            @csrf
            @method('PUT')

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-user-shield mr-2"></i>
                    الأدوار الحالية
                </h3>
                @if ($currentRoles->count() > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach ($currentRoles as $role)
                            <x-ui.badge variant="primary">{{ $role->name }}</x-ui.badge>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400">لا توجد أدوار حالية</p>
                @endif
            </div>

            @if ($coreRoles->count() > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-lock mr-2"></i>
                        الأدوار الأساسية للمستخدم
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        هذه الأدوار مرتبطة بملف المستخدم (طالب/مدرس/ولي أمر) ويتم الاحتفاظ بها تلقائياً.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($coreRoles as $role)
                            <label
                                class="flex items-center gap-3 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/60 opacity-80 cursor-not-allowed"
                            >
                                <input
                                    type="checkbox"
                                    checked
                                    disabled
                                    class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                >
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $role->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        دور أساسي
                                    </div>
                                </div>
                            </label>
                            <input type="hidden" name="roles[]" value="{{ $role->id }}">
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-list mr-2"></i>
                    جميع الأدوار المتاحة
                </h3>
                <div
                    class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-4">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400 mt-1 mr-2"></i>
                        <div class="text-sm text-yellow-800 dark:text-yellow-300">
                            <p class="font-medium mb-1">مهم:</p>
                            <p>يجب أن يكون للمستخدم دور واحد على الأقل.</p>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($availableRoles as $role)
                        <label
                            class="flex items-center gap-3 p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer transition
                            {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-300 dark:border-primary-700' : '' }}"
                        >
                            <input
                                type="checkbox"
                                name="roles[]"
                                value="{{ $role->id }}"
                                {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                            >
                            <div class="flex-1">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $role->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $role->permissions_count }} أذونات
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('roles')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex items-center gap-4">
                <x-ui.button
                    type="submit"
                    variant="primary"
                >
                    <i class="fas fa-save mr-2"></i>
                    حفظ التغييرات
                </x-ui.button>
                <x-ui.button
                    as="a"
                    href="{{ route('dashboard.users.index') }}"
                    variant="outline"
                >
                    إلغاء
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.dashboard>
