<x-layouts.auth
    title="تأكيد كلمة المرور"
    subtitle="هذه منطقة آمنة، يرجى تأكيد كلمة المرور للمتابعة"
>
    <!-- Info Message -->
    <x-ui.alert
        type="warning"
        class="mb-6"
    >
        <p class="text-sm">
            أنت على وشك الدخول إلى منطقة آمنة. يرجى إدخال كلمة المرور الخاصة بك للمتابعة.
        </p>
    </x-ui.alert>

    <form
        method="POST"
        action="{{ route('password.confirm') }}"
        class="space-y-6"
    >
        @csrf

        <!-- Password -->
        <x-form.input
            name="password"
            label="كلمة المرور"
            type="password"
            placeholder="أدخل كلمة المرور"
            icon="fas fa-lock"
            required
            autofocus
        />

        <!-- Submit Button -->
        <x-ui.button
            type="submit"
            variant="primary"
            size="lg"
            icon="fas fa-check"
            class="w-full"
        >
            تأكيد
        </x-ui.button>
    </form>
</x-layouts.auth>
