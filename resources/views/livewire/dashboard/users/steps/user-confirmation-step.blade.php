<div>
    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
        <i class="fas fa-check-circle mr-2"></i>
        تأكيد المعلومات
    </h3>

    <!-- Basic Info Summary -->
    <div class="mb-6">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-user mr-2"></i>
            البيانات الأساسية
        </h4>
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 space-y-2">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">الاسم الكامل:</span>
                    <p class="font-medium text-gray-900 dark:text-white">
                        {{ $userBasicInfo->first_name ?? '' }} {{ $userBasicInfo->last_name ?? '' }}
                    </p>
                </div>
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">الجنس:</span>
                    <p class="font-medium text-gray-900 dark:text-white">
                        {{ $genders[$userBasicInfo->gender ?? ''] ?? '-' }}
                    </p>
                </div>
                @if (!empty($userBasicInfo->email))
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">البريد الإلكتروني:</span>
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ $userBasicInfo->email }}
                        </p>
                    </div>
                @endif
                @if (!empty($userBasicInfo->phone_number))
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">رقم الهاتف:</span>
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ $userBasicInfo->phone_number }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Roles Summary -->
    <div class="mb-6">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-user-shield mr-2"></i>
            الأدوار المختارة
        </h4>
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
            @php
                $selectedRoleIds = $userRoles->selectedRoles ?? [];
                $selectedRoles = collect($this->roles)->whereIn('id', $selectedRoleIds);
            @endphp
            @if ($selectedRoles->count() > 0)
                <div class="flex flex-wrap gap-2">
                    @foreach ($selectedRoles as $role)
                        <x-ui.badge variant="primary">{{ $role->name }}</x-ui.badge>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">لا توجد أدوار محددة</p>
            @endif
        </div>
    </div>
</div>
