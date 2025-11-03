<x-layouts.auth
    title="إعادة تعيين كلمة المرور"
    subtitle="قم بإنشاء كلمة مرور جديدة وآمنة لحسابك"
>
    <form
        method="POST"
        action="{{ route('password.store') }}"
        class="space-y-6"
    >
        @csrf

        <!-- Password Reset Token -->
        <input
            type="hidden"
            name="token"
            value="{{ $request->route('token') }}"
        >

        <!-- Email Address -->
        <x-form.input
            name="email"
            label="البريد الإلكتروني"
            type="email"
            :value="old('email', $request->email)"
            icon="fas fa-envelope"
            required
            autofocus
            readonly
        />

        <!-- Password -->
        <x-form.input
            name="password"
            label="كلمة المرور الجديدة"
            type="password"
            placeholder="أدخل كلمة المرور الجديدة"
            icon="fas fa-lock"
            required
        />

        <!-- Confirm Password -->
        <x-form.input
            name="password_confirmation"
            label="تأكيد كلمة المرور"
            type="password"
            placeholder="أعد إدخال كلمة المرور"
            icon="fas fa-lock"
            required
        />

        <!-- Password Requirements -->
        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <p class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                متطلبات كلمة المرور:
            </p>
            <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-success-500 text-xs"></i>
                    <span>8 أحرف على الأقل</span>
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-success-500 text-xs"></i>
                    <span>تحتوي على حرف كبير وحرف صغير</span>
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-success-500 text-xs"></i>
                    <span>تحتوي على رقم</span>
                </li>
            </ul>
        </div>

        <!-- Submit Button -->
        <x-ui.button
            type="submit"
            variant="primary"
            size="lg"
            icon="fas fa-key"
            class="w-full"
        >
            إعادة تعيين كلمة المرور
        </x-ui.button>
    </form>
</x-layouts.auth>
