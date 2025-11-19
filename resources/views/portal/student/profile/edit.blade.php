<x-layouts.portal pageTitle="تعديل البيانات الشخصية">
    <x-slot name="sidebar">
        <x-ui.portal-sidebar portalType="student" />
    </x-slot>

    <x-ui.card>
        <form
            action="{{ route('portal.student.profile.update') }}"
            method="POST"
            class="space-y-6"
            enctype="multipart/form-data"
        >
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.input
                    name="email"
                    label="البريد الإلكتروني"
                    type="email"
                    readonly
                    :value="$student->user->email"
                />

                <x-form.input
                    name="phone_number"
                    label="رقم الهاتف"
                    readonly
                    :value="$student->user->phone_number"
                />
                <x-form.file
                    name="avatar"
                    label="الصورة الشخصية"
                    preview="{{ $student->user->avatar ? \Storage::url($student->user->avatar) : null }}"
                    accept="image/*"
                />
                <div class="md:col-span-2">
                    <x-form.textarea
                        name="address"
                        label="العنوان"
                        :value="$student->user->address"
                    />
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <x-ui.button
                    as="a"
                    href="{{ route('portal.student.profile') }}"
                    variant="outline"
                >
                    إلغاء
                </x-ui.button>
                <x-ui.button
                    type="submit"
                    variant="primary"
                >
                    حفظ التغييرات
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.portal>
