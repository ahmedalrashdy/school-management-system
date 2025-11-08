<x-layouts.dashboard page-title="إنشاء دور جديد">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'إدارة الأدوار', 'url' => route('dashboard.roles.index'), 'icon' => 'fas fa-user-shield'],
            ['label' => 'إنشاء دور جديد', 'icon' => 'fas fa-plus'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="إنشاء دور جديد"
        description="إنشاء دور جديد في النظام"
        button-text="رجوع"
        button-link="{{ route('dashboard.roles.index') }}"
    />

    <x-ui.card>
        <form
            method="POST"
            action="{{ route('dashboard.roles.store') }}"
        >
            @csrf

            <x-form.input
                name="name"
                label="اسم الدور"
                placeholder="مثال: مشرف أكاديمي"
                value="{{ old('name') }}"
                required
            />



            <div class="mt-6 flex items-center gap-4">
                <x-ui.button
                    type="submit"
                    variant="primary"
                >
                    <i class="fas fa-save mr-2"></i>
                    حفظ الدور
                </x-ui.button>
                <x-ui.button
                    as="a"
                    href="{{ route('dashboard.roles.index') }}"
                    variant="outline"
                >
                    إلغاء
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.dashboard>
