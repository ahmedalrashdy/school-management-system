<x-layouts.auth
    title="تسجيل الدخول"
    subtitle="قم بتسجيل الدخول للوصول إلى لوحة التحكم"
>
    <!-- Session Status -->
    @if (session('status'))
        <x-ui.alert
            type="success"
            dismissible
            class="mb-6"
        >
            {{ session('status') }}
        </x-ui.alert>
    @endif

    <form
        method="POST"
        action="{{ route('login') }}"
        class="space-y-6"
    >
        @csrf

        <!-- Email or Phone -->
        <x-form.input
            name="email"
            label="البريد الإلكتروني أو رقم الهاتف"
            type="text"
            placeholder="أدخل البريد الإلكتروني أو رقم الهاتف"
            icon="fas fa-envelope"
            required
            autofocus
        />

        <!-- Password -->
        <x-form.input
            name="password"
            label="كلمة المرور"
            type="password"
            placeholder="أدخل كلمة المرور"
            icon="fas fa-lock"
            required
        />

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <x-form.checkbox
                name="remember"
                label="تذكرني"
            />

            @if (Route::has('password.request'))
                <a
                    href="{{ route('password.request') }}"
                    class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 transition"
                >
                    نسيت كلمة المرور؟
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <x-ui.button
            type="submit"
            variant="primary"
            size="lg"
            icon="fas fa-sign-in-alt"
            class="w-full"
        >
            تسجيل الدخول
        </x-ui.button>
    </form>

    <!-- Help Text -->
    <div class="mt-6 p-4 bg-info-50 dark:bg-info-900/20 rounded-lg border border-info-200 dark:border-info-800">
        <div class="flex items-start gap-3">
            <i class="fas fa-info-circle text-info-600 dark:text-info-400 mt-0.5"></i>
            <div class="text-sm text-info-800 dark:text-info-300">
                <p class="font-medium mb-1">هل تحتاج إلى مساعدة؟</p>
                <p class="text-xs">يرجى التواصل مع مدير النظام للحصول على بيانات تسجيل الدخول الخاصة بك.</p>
            </div>
        </div>
    </div>
</x-layouts.auth>
