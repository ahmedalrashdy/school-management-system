<?php

namespace App\Livewire\Dashboard\Users\Students\Steps;

use App\Enums\GenderEnum;
use App\Enums\RelationToStudentEnum;
use App\Models\Guardian;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Form;

class StudentGuardiansStep extends Form
{
    public function __construct(
        protected Component $component,
        protected $propertyName
    ) {
        parent::__construct($component, $propertyName);
        $this->addGuardian();
    }

    public array $guardians = [];

    // حالة البحث عن ولي الأمر
    public array $searchingGuardians = [];

    public function validStep(): bool
    {

        // 1. التحقق الأساسي من وجود بيانات
        if (empty($this->guardians)) {
            $this->addError('guardians', 'يجب إضافة ولي أمر واحد على الأقل.');
            // نوقف التنفيذ هنا
            $this->validate(['guardians' => 'required']);

            return false;
        }

        $rules = [];
        $messages = [];

        // 2. بناء مصفوفة القواعد ديناميكياً
        foreach ($this->guardians as $index => $guardian) {
            $guardianKey = "guardians.$index";

            if (! empty($guardian['guardian_id'])) {
                // --- حالة ولي أمر موجود مسبقاً ---
                $rules["$guardianKey.relation_to_student"] = [
                    'required',
                    Rule::enum(RelationToStudentEnum::class),
                ];
            } else {
                // --- حالة ولي أمر جديد ---

                // تصحيح المسار لـ required_without
                // يجب الإشارة للحقل الآخر بمساره الكامل: guardians.0.phone_number
                $phoneFieldPath = "guardians.$index.phone_number";
                $emailFieldPath = "guardians.$index.email";

                $rules["$guardianKey.email"] = [
                    'nullable',
                    'email',
                    'max:255',
                    "required_without:{$phoneFieldPath}",
                    Rule::unique('users', 'email')
                        ->whereNull('deleted_at')
                        ->ignore($guardian['user_id'] ?? null),
                ];

                $rules["$guardianKey.phone_number"] = [
                    'nullable',
                    'string',
                    'max:255',
                    "required_without:{$emailFieldPath}",
                    Rule::unique('users', 'phone_number')
                        ->whereNull('deleted_at')
                        ->ignore($guardian['user_id'] ?? null),
                ];

                $rules["$guardianKey.first_name"] = ['required', 'string', 'max:255'];
                $rules["$guardianKey.last_name"] = ['required', 'string', 'max:255'];
                $rules["$guardianKey.gender"] = ['required', Rule::enum(GenderEnum::class)];
                $rules["$guardianKey.occupation"] = ['nullable', 'string', 'max:255'];
                $rules["$guardianKey.address"] = ['nullable', 'string', 'max:500'];
                $rules["$guardianKey.relation_to_student"] = [
                    'required',
                    Rule::enum(RelationToStudentEnum::class),
                ];
            }
        }

        $this->validate($rules);

        // 4. التحقق من التكرار (Manual Validation)
        $guardianIds = array_filter(array_column($this->guardians, 'guardian_id'));
        if (count($guardianIds) !== count(array_unique($guardianIds))) {
            $this->addError('guardians', 'لا يمكن إضافة نفس ولي الأمر مرتين.');

            // نرمي Exception يدوياً لعرض الخطأ العام
            throw \Illuminate\Validation\ValidationException::withMessages([
                'guardians' => 'لا يمكن إضافة نفس ولي الأمر مرتين.',
            ]);
        }

        return true;
    }

    public function addGuardian(): void
    {

        $this->guardians[] = [
            'guardian_id' => null,
            'user_id' => null,
            'email' => '',
            'phone_number' => '',
            'first_name' => '',
            'last_name' => '',
            'gender' => null,
            'occupation' => '',
            'address' => '',
            'relation_to_student' => null,
            'is_existing' => false,
        ];
    }

    public function removeGuardian(int $index): void
    {
        if (count($this->guardians) > 1) {
            unset($this->guardians[$index]);
            $this->guardians = array_values($this->guardians);
            unset($this->searchingGuardians[$index]);
        }
    }

    public function searchGuardian(int $index): void
    {
        $guardian = $this->guardians[$index] ?? null;
        if (! $guardian) {
            return;
        }

        $this->searchingGuardians[$index] = true;

        $email = trim($guardian['email'] ?? '');
        $phoneNumber = trim($guardian['phone_number'] ?? '');

        if (empty($email) && empty($phoneNumber)) {
            // إعادة تعيين الحقول إذا كانت فارغة
            $this->resetGuardianFields($index);
            $this->searchingGuardians[$index] = false;

            return;
        }

        // البحث عن مستخدم يطابق البريد أو الهاتف
        $user = User::where(function ($query) use ($email, $phoneNumber) {
            if (! empty($email)) {
                $query->where('email', $email);
            }
            if (! empty($phoneNumber)) {
                $query->orWhere('phone_number', $phoneNumber);
            }
        })->first();

        if ($user) {
            // التحقق من أن المستخدم هو ولي أمر
            $guardianModel = Guardian::where('user_id', $user->id)->first();

            if ($guardianModel) {
                // ولي أمر موجود
                $this->guardians[$index] = [
                    'guardian_id' => $guardianModel->id,
                    'user_id' => $user->id,
                    'email' => $user->email ?? '',
                    'phone_number' => $user->phone_number ?? '',
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'gender' => $user->gender->value,
                    'occupation' => $guardianModel->occupation ?? '',
                    'address' => $user->address ?? '',
                    'relation_to_student' => $this->guardians[$index]['relation_to_student'] ?? null,
                    'is_existing' => true,
                ];
            } else {
                // مستخدم موجود لكن ليس ولي أمر - إعادة تعيين
                $this->resetGuardianFields($index);
            }
        } else {
            // لم يتم العثور على ولي أمر - إعادة تعيين الحقول
            $this->resetGuardianFields($index);
        }

        $this->searchingGuardians[$index] = false;
    }

    protected function resetGuardianFields(int $index): void
    {
        $currentRelation = $this->guardians[$index]['relation_to_student'] ?? null;
        $this->guardians[$index] = [
            'guardian_id' => null,
            'user_id' => null,
            'email' => $this->guardians[$index]['email'] ?? '',
            'phone_number' => $this->guardians[$index]['phone_number'] ?? '',
            'first_name' => '',
            'last_name' => '',
            'gender' => null,
            'occupation' => '',
            'address' => '',
            'relation_to_student' => $currentRelation,
            'is_existing' => false,
        ];
    }

    public function search($value, $key): void
    {
        // $property ستأتيك بهذا الشكل: "guardians.0.email"

        $parts = explode('.', $key);

        // نتأكد أن التعديل يخص مصفوفة guardians
        if ($parts[0] === 'guardians' && count($parts) >= 3) {
            $index = (int) $parts[1];
            $field = $parts[2];

            // نطبق اللوجيك الخاص بنا
            if (in_array($field, ['email', 'phone_number'])) {
                // نمرر الـ index لدالة البحث
                $this->searchGuardian($index);
            }
        }
    }
}
