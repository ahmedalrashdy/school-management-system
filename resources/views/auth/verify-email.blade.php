<x-layouts.auth
    title="التحقق من البريد الإلكتروني"
    subtitle="يرجى التحقق من بريدك الإلكتروني"
>
    <!-- Info Message -->
    <x-ui.alert
        type="info"
        class="mb-6"
    >
        <p class="text-sm">
            شكراً لتسجيلك! قبل البدء، يرجى التحقق من عنوان بريدك الإلكتروني بالنقر على الرابط الذي أرسلناه إليك. إذا لم
            تستلم البريد الإلكتروني، يسعدنا إرسال آخر.
        </p>
    </x-ui.alert>

    @if (session('status') == 'verification-link-sent')
        <x-ui.alert
            type="success"
            dismissible
            class="mb-6"
        >
            <p class="text-sm">
                تم إرسال رابط تحقق جديد إلى عنوان البريد الإلكتروني الذي قدمته أثناء التسجيل.
            </p>
        </x-ui.alert>
    @endif

    <div class="space-y-4">
        <form
            method="POST"
            action="{{ route('verification.send') }}"
        >
            @csrf
            <x-ui.button
                type="submit"
                variant="primary"
                size="lg"
                icon="fas fa-paper-plane"
                class="w-full"
            >
                إعادة إرسال بريد التحقق
            </x-ui.button>
        </form>

        <form
            method="POST"
            action="{{ route('logout') }}"
        >
            @csrf
            <button
                type="submit"
                class="w-full text-center text-sm text-gray-600 hover:text-danger-600 dark:text-gray-400 dark:hover:text-danger-400 transition py-2"
            >
                <i class="fas fa-sign-out-alt mr-1"></i>
                تسجيل الخروج
            </button>
        </form>
    </div>
</x-layouts.auth>
