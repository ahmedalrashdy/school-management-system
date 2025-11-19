<x-layouts.portal pageTitle="الملف الشخصي">
    <x-slot name="sidebar">
        <x-ui.portal-sidebar portalType="guardian" />
    </x-slot>

    <div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- البيانات الرسمية -->
                <x-ui.card>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            <i class="fas fa-id-card mr-2"></i>
                            البيانات الرسمية
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">الاسم
                                الأول</label>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $guardian->user->first_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">اسم
                                العائلة</label>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $guardian->user->last_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">الجنس</label>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $guardian->user->gender->label() }}</p>
                        </div>
                    </div>
                </x-ui.card>
                <!-- البيانات الشخصية -->
                <x-ui.card>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            <i class="fas fa-user-circle mr-2"></i>
                            البيانات الشخصية
                        </h3>
                        <x-ui.button
                            as="a"
                            href="{{ route('portal.guardian.profile.edit') }}"
                        >
                            <i class="fas fa-edit"></i>
                            تعديل بياناتي
                        </x-ui.button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">الصورة
                                الرمزية</label>
                            @if ($guardian->user->avatar)
                                <img
                                    src="{{ \Storage::url($guardian->user->avatar) }}"
                                    alt="{{ $guardian->user->full_name }}"
                                    class="h-20 w-20 rounded-full object-cover"
                                >
                            @else
                                <div
                                    class="h-20 w-20 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-500 dark:text-gray-400 text-2xl"></i>
                                </div>
                            @endif
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">العنوان</label>
                            <p class="text-sm text-gray-900 dark:text-white">
                                {{ $guardian->user->address ?? 'غير محدد' }}
                            </p>
                        </div>
                        @if ($guardian->user->email)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">البريد
                                    الإلكتروني</label>
                                <p class="text-sm text-gray-900 dark:text-white">
                                    <i class="fas fa-envelope mr-1"></i>
                                    {{ $guardian->user->email }}
                                </p>
                            </div>
                        @endif
                        @if ($guardian->user->phone_number)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">رقم
                                    الهاتف</label>
                                <p class="text-sm text-gray-900 dark:text-white">
                                    <i class="fas fa-phone mr-1"></i>
                                    {{ $guardian->user->phone_number }}
                                </p>
                            </div>
                        @endif
                    </div>
                </x-ui.card>


            </div>
        </div>
    </div>
</x-layouts.portal>
