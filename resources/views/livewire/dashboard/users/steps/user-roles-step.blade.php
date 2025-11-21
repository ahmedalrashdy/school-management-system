<div>
    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
        <i class="fas fa-user-shield mr-2"></i>
        إسناد الأدوار
    </h3>

    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400 mt-1 mr-2"></i>
            <div class="text-sm text-yellow-800 dark:text-yellow-300">
                <p class="font-medium mb-1">مهم:</p>
                <p>يجب تحديد دور واحد على الأقل للمستخدم.</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($this->roles as $index => $role)
            <label
                class="flex items-center gap-3 p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer transition
                {{ in_array($role->id, $userRoles->selectedRoles) ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-300 dark:border-primary-700' : '' }}"
            >
                <input
                    type="checkbox"
                    value="{{ $role->id }}"
                    wire:model="userRoles.selectedRoles"
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

    @error('userRoles.selectedRoles')
        <x-ui.alert
            type="danger"
            class="mt-4"
        >
            {{ $message }}
        </x-ui.alert>
    @enderror
</div>
