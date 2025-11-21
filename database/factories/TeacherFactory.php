<?php

namespace Database\Factories;

use App\Enums\AcademicQualificationEnum;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Teacher>
 */
class TeacherFactory extends Factory
{
    protected $model = Teacher::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $specializations = [
            'اللغة العربية',
            'الرياضيات',
            'العلوم',
            'اللغة الإنجليزية',
            'التربية الإسلامية',
            'الاجتماعيات',
            'التربية البدنية',
            'الحاسوب',
            'الفيزياء',
            'الكيمياء',
            'الأحياء',
            'التاريخ',
            'الجغرافيا',
        ];

        $qualifications = [
            'بكالوريوس',
            'ماجستير',
            'دكتوراه',
            'دبلوم',
        ];

        return [
            'user_id' => User::factory(),
            'date_of_birth' => fake()->dateTimeBetween('-50 years', '-25 years'),
            'specialization' => fake()->randomElement($specializations),
            'qualification' => fake()->randomElement(array_column(AcademicQualificationEnum::cases(), 'value')),
        ];
    }
}
