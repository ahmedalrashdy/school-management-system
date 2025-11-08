@php
    $user = auth()->user();
    $availableRoles = $user->getAvailableRoles();

    $hasMultipleRoles = count($availableRoles) > 0;
    $currentRoute = request()->route()?->getName() ?? '';
    $currentRole = null;

    // Determine current role from route
    if ($currentRoute && str_starts_with($currentRoute, 'portal.teacher.')) {
        $currentRole = 'مدرس';
    } elseif ($currentRoute && str_starts_with($currentRoute, 'portal.student.')) {
        $currentRole = 'طالب';
    } elseif ($currentRoute && str_starts_with($currentRoute, 'portal.guardian.')) {
        $currentRole = 'ولي أمر';
    }
@endphp

<div
    x-data="{ userMenuOpen: false }"
    class="relative"
>
    <button
        @click="userMenuOpen = !userMenuOpen"
        type="button"
        class="flex items-center gap-2 focus:outline-none"
    >
        @if ($user->avatar)
            <img
                src="{{ \Storage::url($user->avatar) }}"
                alt="{{ $user->first_name }}"
                class="w-8 h-8 rounded-full"
            >
        @else
            <div class="w-8 h-8 rounded-full bg-primary-500 flex items-center justify-center">
                <span class="text-white font-bold text-xs">
                    {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                </span>
            </div>
        @endif
    </button>

    <!-- User Dropdown -->
    <div
        x-show="userMenuOpen"
        @click.away="userMenuOpen = false"
        x-transition
        style="display: none;"
        class="absolute left-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden"
    >
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
            <p class="text-sm font-medium text-gray-900 dark:text-white">
                {{ $user->first_name }} {{ $user->last_name }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-1">
                {{ $user->email ?? $user->phone_number }}
            </p>
        </div>
        @if ($hasMultipleRoles)
            <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    التبديل بين الأدوار
                </p>
                <div class="space-y-1">
                    @can(\Perm::AccessAdminPanel->value)
                        <a
                            href="{{ route('dashboard.genarel-page') }}"
                            class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-colors
                                {{ $currentRole == null
                                    ? 'bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300'
                                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        >
                            <i class="fas fa-chalkboard-teacher w-4"></i>
                            <span>إدارة شؤون الطلاب</span>
                        </a>
                    @endcan
                    @if (in_array('مدرس', $availableRoles))
                        <a
                            href="{{ route('portal.teacher.index') }}"
                            class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-colors
                                {{ $currentRole === 'مدرس'
                                    ? 'bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300'
                                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        >
                            <i class="fas fa-chalkboard-teacher w-4"></i>
                            <span>مدرس</span>
                            @if ($currentRole === 'مدرس')
                                <i class="fas fa-check mr-auto text-primary-600"></i>
                            @endif
                        </a>
                    @endif

                    @if (in_array('طالب', $availableRoles))
                        <a
                            href="{{ route('portal.student.index') }}"
                            class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-colors
                                {{ $currentRole === 'طالب'
                                    ? 'bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300'
                                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        >
                            <i class="fas fa-user-graduate w-4"></i>
                            <span>طالب</span>
                            @if ($currentRole === 'طالب')
                                <i class="fas fa-check mr-auto text-primary-600"></i>
                            @endif
                        </a>
                    @endif

                    @if (in_array('ولي أمر', $availableRoles))
                        <a
                            href="{{ route('portal.guardian.index') }}"
                            class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-colors
                                {{ $currentRole === 'ولي أمر'
                                    ? 'bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300'
                                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        >
                            <i class="fas fa-users w-4"></i>
                            <span>ولي أمر</span>
                            @if ($currentRole === 'ولي أمر')
                                <i class="fas fa-check mr-auto text-primary-600"></i>
                            @endif
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <div class="py-1">
            <a
                href="#"
                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
            >
                <i class="fas fa-user w-4"></i>
                <span>الملف الشخصي</span>
            </a>
            <a
                href="#"
                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
            >
                <i class="fas fa-cog w-4"></i>
                <span>الإعدادات</span>
            </a>
        </div>
        <div class="border-t border-gray-200 dark:border-gray-700">
            <form
                method="POST"
                action="{{ route('logout') }}"
            >
                @csrf
                <button
                    type="submit"
                    class="flex items-center gap-2 w-full px-4 py-2 text-sm text-danger-600 dark:text-danger-400 hover:bg-gray-100 dark:hover:bg-gray-700 text-right"
                >
                    <i class="fas fa-sign-out-alt w-4"></i>
                    <span>تسجيل الخروج</span>
                </button>
            </form>
        </div>
    </div>
</div>
