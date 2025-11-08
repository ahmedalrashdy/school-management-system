<x-layouts.dashboard page-title="تعديل الدور">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'إدارة الأدوار', 'url' => route('dashboard.roles.index'), 'icon' => 'fas fa-user-shield'],
            ['label' => 'تعديل الدور', 'icon' => 'fas fa-edit'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="تعديل الدور"
        description="تعديل الدور وإدارة أذوناته"
        button-text="رجوع"
        button-link="{{ route('dashboard.roles.index') }}"
    />

    <x-ui.card>
        <form
            method="POST"
            action="{{ route('dashboard.roles.update', $role) }}"
            x-data="{
                selectedPermissions: @js($selectedPermissions),
                groupedPermissions: @js($groupedPermissions),
                activeTab: '{{ array_key_first($groupedPermissions) }}',
                togglePermission(permission) {
                    const index = this.selectedPermissions.indexOf(permission);
                    if (index > -1) {
                        this.selectedPermissions.splice(index, 1);
                    } else {
                        this.selectedPermissions.push(permission);
                    }
                },
                isPermissionSelected(permission) {
                    return this.selectedPermissions.includes(permission);
                },
                selectAllInGroup(groupName) {
                    const groupPermissions = Object.keys(this.groupedPermissions[groupName] || {});
                    groupPermissions.forEach(perm => {
                        if (!this.selectedPermissions.includes(perm)) {
                            this.selectedPermissions.push(perm);
                        }
                    });
                },
                deselectAllInGroup(groupName) {
                    const groupPermissions = Object.keys(this.groupedPermissions[groupName] || {});
                    this.selectedPermissions = this.selectedPermissions.filter(
                        perm => !groupPermissions.includes(perm)
                    );
                },
                areAllSelectedInGroup(groupName) {
                    const groupPermissions = Object.keys(this.groupedPermissions[groupName] || {});
                    return groupPermissions.every(perm => this.selectedPermissions.includes(perm));
                },
                getSelectedCountInGroup(groupName) {
                    const groupPermissions = Object.keys(this.groupedPermissions[groupName] || {});
                    return groupPermissions.filter(perm => this.selectedPermissions.includes(perm)).length;
                },
                getTotalCount() {
                    return Object.values(this.groupedPermissions).reduce((sum, perms) => sum + Object.keys(perms).length, 0);
                }
            }"
        >
            @csrf
            @method('PUT')

            <!-- Hidden input for permissions -->
            <template
                x-for="permission in selectedPermissions"
                :key="permission"
            >
                <input
                    name="permissions[]"
                    type="hidden"
                    :value="permission"
                />
            </template>

            <!-- Role Name -->
            <div class="mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-tag mr-2"></i>
                    معلومات الدور
                </h3>

                <div class="max-w-md">
                    <x-form.input
                        name="name"
                        value="{{ old('name', $role->name) }}"
                        label="اسم الدور"
                        placeholder="مثال: مشرف أكاديمي"
                        required
                    />
                </div>
            </div>

            <!-- Permissions with Tabs -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-key mr-2"></i>
                        الأذونات
                    </h3>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <span
                            class="font-medium text-primary-600 dark:text-primary-400"
                            x-text="selectedPermissions.length"
                        ></span>
                        من
                        <span
                            class="font-medium"
                            x-text="getTotalCount()"
                        ></span>
                        أذونات محددة
                    </div>
                </div>

                <!-- Tabs Navigation -->
                <div class="mb-6">
                    <div class="border-b border-gray-200 dark:border-gray-700 overflow-x-auto hide-scrollbar ">
                        <nav
                            class="-mb-px flex space-x-2"
                            aria-label="Tabs"
                        >
                            <template
                                x-for="(permissions, groupName) in groupedPermissions"
                                :key="groupName"
                            >
                                <button
                                    class="whitespace-nowrap py-3 px-4 border-b-2 font-medium text-sm transition rounded-t-lg"
                                    type="button"
                                    @click="activeTab = groupName"
                                    :class="activeTab === groupName ?
                                        'border-primary-500 text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20' :
                                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                >
                                    <i class="fas fa-folder mr-2"></i>
                                    <span x-text="groupName"></span>
                                    <span
                                        class="ml-2 px-2 py-0.5 text-xs rounded-full"
                                        :class="areAllSelectedInGroup(groupName) ?
                                            'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' :
                                            'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400'"
                                    >
                                        <span x-text="getSelectedCountInGroup(groupName)"></span>/
                                        <span x-text="Object.keys(permissions).length"></span>
                                    </span>
                                </button>
                            </template>
                        </nav>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="space-y-4">
                    <template
                        x-for="(permissions, groupName) in groupedPermissions"
                        :key="groupName"
                    >
                        <div
                            class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 bg-white dark:bg-gray-800 shadow-sm"
                            x-show="activeTab === groupName"
                            x-cloak
                        >
                            <!-- Group Header with Select All Button -->
                            <div
                                class="flex items-center justify-between mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        <i class="fas fa-folder-open mr-2 text-primary-500"></i>
                                        <span x-text="groupName"></span>
                                    </h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        <span x-text="Object.keys(permissions).length"></span> أذونات متاحة
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <template x-if="areAllSelectedInGroup(groupName)">
                                        <button
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition text-sm font-medium"
                                            type="button"
                                            @click="deselectAllInGroup(groupName)"
                                        >
                                            <i class="fas fa-times-circle"></i>
                                            إلغاء تحديد الكل
                                        </button>
                                    </template>
                                    <template x-if="!areAllSelectedInGroup(groupName)">
                                        <button
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 rounded-lg hover:bg-primary-100 dark:hover:bg-primary-900/30 transition text-sm font-medium"
                                            type="button"
                                            @click="selectAllInGroup(groupName)"
                                        >
                                            <i class="fas fa-check-double"></i>
                                            تحديد الكل
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <!-- Permissions Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                <template
                                    x-for="(label, permissionValue) in permissions"
                                    :key="permissionValue"
                                >
                                    <label
                                        class="flex items-start gap-3 p-3 rounded-lg border cursor-pointer transition"
                                        :class="isPermissionSelected(permissionValue) ?
                                            'bg-primary-50 dark:bg-primary-900/20 border-primary-300 dark:border-primary-700' :
                                            'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50'"
                                    >
                                        <input
                                            class="mt-1 rounded border-gray-300 text-primary-600 focus:ring-primary-500 focus:ring-2"
                                            type="checkbox"
                                            :value="permissionValue"
                                            @change="togglePermission(permissionValue)"
                                            :checked="isPermissionSelected(permissionValue)"
                                        >
                                        <div class="flex-1">
                                            <span
                                                class="text-sm font-medium text-gray-900 dark:text-white block"
                                                x-text="label"
                                            ></span>
                                            <span
                                                class="text-xs text-gray-500 dark:text-gray-400 mt-1 block font-mono"
                                                x-text="permissionValue"
                                            ></span>
                                        </div>
                                        <i
                                            class="fas fa-check-circle text-primary-500 mt-1"
                                            x-show="isPermissionSelected(permissionValue)"
                                        ></i>
                                    </label>
                                </template>
                            </div>

                            <!-- Group Summary -->
                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">
                                        الأذونات المحددة في هذا القسم:
                                    </span>
                                    <span class="font-semibold text-primary-600 dark:text-primary-400">
                                        <span x-text="getSelectedCountInGroup(groupName)"></span>
                                        من
                                        <span x-text="Object.keys(permissions).length"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    <i class="fas fa-info-circle mr-2"></i>
                    تم تحديد <span
                        class="font-semibold text-primary-600 dark:text-primary-400"
                        x-text="selectedPermissions.length"
                    ></span> أذونات
                </div>
                <div class="flex items-center gap-4">
                    <x-ui.button
                        class="px-6"
                        type="submit"
                        variant="primary"
                    >
                        <i class="fas fa-save mr-2"></i>
                        حفظ التغييرات
                    </x-ui.button>
                    <x-ui.button
                        as="a"
                        href="{{ route('dashboard.roles.index') }}"
                        variant="outline"
                    >
                        <i class="fas fa-times"></i>
                        إلغاء
                    </x-ui.button>
                </div>
            </div>
        </form>
    </x-ui.card>
</x-layouts.dashboard>
