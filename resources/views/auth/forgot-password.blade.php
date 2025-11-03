<x-layouts.auth
    title="نسيت كلمة المرور"
    subtitle="أدخل بريدك الإلكتروني وسنرسل لك رابط إعادة تعيين كلمة المرور"
>
    <!-- Info Message -->
    <x-ui.alert
        type="info"
        class="mb-6"
    >
        <p class="text-sm">
            لا تقلق! فقط أدخل بريدك الإلكتروني المسجل وسنرسل لك رابطاً لإعادة تعيين كلمة المرور الخاصة بك.
        </p>
    </x-ui.alert>

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
        action="{{ route('password.email') }}"
        class="space-y-6"
    >
        @csrf

        <!-- Email Address -->
        <x-form.input
            name="email"
            label="البريد الإلكتروني"
            type="email"
            placeholder="أدخل بريدك الإلكتروني"
            icon="fas fa-envelope"
            required
            autofocus
        />

        <!-- Buttons -->
        <div class="flex flex-col gap-3">
            <x-ui.button
                type="submit"
                variant="primary"
                size="lg"
                icon="fas fa-paper-plane"
                class="w-full"
            >
                إرسال رابط إعادة التعيين
            </x-ui.button>

            <a
                href="{{ route('login') }}"
                class="text-center text-sm text-gray-600 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 transition"
            >
                <i class="fas fa-arrow-right mr-1"></i>
                العودة إلى تسجيل الدخول
            </a>
        </div>
    </form>
</x-layouts.auth>
