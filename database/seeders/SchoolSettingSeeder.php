<?php

namespace Database\Seeders;

use App\Enums\AttendanceModeEnum;
use App\Enums\DayOfWeekEnum;
use App\Models\SchoolSetting;
use Illuminate\Database\Seeder;

class SchoolSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'school_name',
                'value' => 'مدرسة الأفق الجديد الدولية',
                'type' => 'string',
                'group' => 'عام',
                'label' => 'اسم المدرسة',
            ],
            [
                'key' => 'school_logo',
                'value' => null,
                'type' => 'file',
                'group' => 'عام',
                'label' => 'شعار المدرسة',
            ],
            [
                'key' => 'school_email',
                'value' => 'info@newhorizon.edu',
                'type' => 'string',
                'group' => 'عام',
                'label' => 'البريد الإلكتروني الرسمي',
            ],
            [
                'key' => 'school_phone',
                'value' => null,
                'type' => 'string',
                'group' => 'معومات التواصل',
                'label' => 'رقم الهاتف الاساسي',
            ],
            [
                'key' => 'school_phone_secandory',
                'value' => null,
                'type' => 'string',
                'group' => 'معومات التواصل',
                'label' => 'رقم الهاتف الثانوي',
            ],
            [
                'key' => 'school_address',
                'value' => null,
                'type' => 'string',
                'group' => 'معلومات التواصل',
                'label' => 'العنوان',
            ],
            [
                'key' => 'social_links',
                'value' => null,
                'type' => 'json',
                'group' => 'معلومات التواصل',
                'label' => 'روابط التواصل الاجتماعي',
            ],
            [
                'key' => 'system_grading',
                'value' => 'القياسي',
                'type' => 'string',
                'group' => 'أكاديمي',
                'label' => 'نظام الدرجات',
            ],
            [
                'key' => 'attendence_mode',
                'value' => AttendanceModeEnum::PerPeriod->value,
                'type' => 'int',
                'group' => 'أكاديمي',
                'label' => 'أسلوب التحضير',
            ],
            [
                'key' => 'weekend_days',
                'value' => [
                    DayOfWeekEnum::Friday->value,
                    DayOfWeekEnum::Thursday->value,
                ],
                'type' => 'array',
                'group' => 'أكاديمي',
                'label' => 'أيام العطلة الأسبوعية',
            ],
        ];

        foreach ($settings as $setting) {
            // SchoolSetting::updateOrCreate(
            //     ['key' => $setting['key']],
            //     $setting
            // );
            if (SchoolSetting::where('key', $setting['key'])->exists()) {
                $query = SchoolSetting::query()->where('key', $setting['key']);
                unset($setting['key']);
                $setting['value'] = json_encode($setting['value']);
                $query->update($setting);
            } else {
                SchoolSetting::create($setting);
            }
        }
    }
}
