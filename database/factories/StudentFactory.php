<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'admission_number' => strtoupper(fake()->bothify('STD-######')),
            'date_of_birth' => fake()->dateTimeBetween('-18 years', '-10 years'),
            'city' => fake()->city(),
            'district' => fake()->streetName(),
        ];
    }

    /**
     * Indicate that the student should have a specific age range.
     */
    public function ageBetween(int $minAge, int $maxAge): static
    {
        return $this->state(fn(array $attributes) => [
            'date_of_birth' => fake()->dateTimeBetween("-{$maxAge} years", "-{$minAge} years"),
        ]);
    }

    /**
     * Indicate that the student should be in elementary school age (6-12 years).
     */
    public function elementary(): static
    {
        return $this->ageBetween(6, 12);
    }

    /**
     * Indicate that the student should be in middle school age (13-15 years).
     */
    public function middleSchool(): static
    {
        return $this->ageBetween(13, 15);
    }

    /**
     * Indicate that the student should be in high school age (16-18 years).
     */
    public function highSchool(): static
    {
        return $this->ageBetween(16, 18);
    }
}
