<?php

use App\Enums\GenderEnum;
use App\Enums\RelationToStudentEnum;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function (): void {
    $this->user = User::factory()->admin()->create();
    actingAs($this->user);
});

it('displays a listing of guardians', function (): void {
    Guardian::factory()->count(3)->create();

    $response = $this->get(route('dashboard.guardians.index'));

    $response->assertSuccessful();
    $response->assertSee('أولياء الأمور');
});

it('can create a new guardian', function (): void {
    $response = $this->get(route('dashboard.guardians.create'));

    $response->assertSuccessful();
    $response->assertSee('إضافة ولي أمر جديد');
});

it('can store a new guardian', function (): void {
    $data = [
        'first_name' => 'أحمد',
        'last_name' => 'محمد',
        'email' => 'ahmed@example.com',
        'phone_number' => '0501234567',
        'gender' => GenderEnum::Male->value,
        'occupation' => 'مهندس',
    ];

    $response = $this->post(route('dashboard.guardians.store'), $data);

    $response->assertRedirect(route('dashboard.guardians.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'first_name' => 'أحمد',
        'last_name' => 'محمد',
        'email' => 'ahmed@example.com',
        'reset_password_required' => true,
        'is_active' => true,
    ]);

    $user = User::where('email', 'ahmed@example.com')->first();
    $this->assertDatabaseHas('guardians', [
        'user_id' => $user->id,
        'occupation' => 'مهندس',
    ]);
});

it('requires either email or phone number when creating guardian', function (): void {
    $data = [
        'first_name' => 'أحمد',
        'last_name' => 'محمد',
        'gender' => GenderEnum::Male->value,
    ];

    $response = $this->post(route('dashboard.guardians.store'), $data);

    $response->assertSessionHasErrors(['email', 'phone_number']);
});

it('can display a guardian profile', function (): void {
    $guardian = Guardian::factory()->create();

    $response = $this->get(route('dashboard.guardians.show', $guardian));

    $response->assertSuccessful();
    $response->assertSee($guardian->user->full_name);
});

it('can edit a guardian', function (): void {
    $guardian = Guardian::factory()->create();

    $response = $this->get(route('dashboard.guardians.edit', $guardian));

    $response->assertSuccessful();
    $response->assertSee('تعديل ولي أمر');
});

it('can update a guardian when account is not activated', function (): void {
    $guardian = Guardian::factory()->create();
    $guardian->user->update(['reset_password_required' => true]);

    $data = [
        'first_name' => 'محمد',
        'last_name' => 'علي',
        'email' => 'mohammed@example.com',
        'phone_number' => '0509876543',
        'gender' => GenderEnum::Male->value,
        'occupation' => 'طبيب',
    ];

    $response = $this->put(route('dashboard.guardians.update', $guardian), $data);

    $response->assertRedirect(route('dashboard.guardians.show', $guardian));
    $response->assertSessionHas('success');

    $guardian->refresh();
    expect($guardian->user->first_name)->toBe('محمد');
    expect($guardian->occupation)->toBe('طبيب');
});

it('cannot update personal data when account is activated', function (): void {
    $guardian = Guardian::factory()->create();
    $guardian->user->update(['reset_password_required' => false]);

    $originalEmail = $guardian->user->email;
    $originalFirstName = $guardian->user->first_name;

    $data = [
        'occupation' => 'مدرس',
        'is_active' => true,
    ];

    $response = $this->put(route('dashboard.guardians.update', $guardian), $data);

    $response->assertRedirect(route('dashboard.guardians.show', $guardian));

    $guardian->refresh();
    expect($guardian->user->email)->toBe($originalEmail);
    expect($guardian->user->first_name)->toBe($originalFirstName);
    expect($guardian->occupation)->toBe('مدرس');
});

it('cannot delete a guardian with associated students', function (): void {
    $guardian = Guardian::factory()->create();
    $student = Student::factory()->create();
    $guardian->students()->attach($student->id, [
        'relation_to_student' => RelationToStudentEnum::Father->value,
    ]);

    $response = $this->delete(route('dashboard.guardians.destroy', $guardian));

    $response->assertRedirect(route('dashboard.guardians.index'));
    $response->assertSessionHas('error');

    $this->assertDatabaseHas('guardians', ['id' => $guardian->id]);
});

it('can delete a guardian without associated students', function (): void {
    $guardian = Guardian::factory()->create();

    $response = $this->delete(route('dashboard.guardians.destroy', $guardian));

    $response->assertRedirect(route('dashboard.guardians.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('guardians', ['id' => $guardian->id]);
    $this->assertDatabaseMissing('users', ['id' => $guardian->user_id]);
});

it('can attach a student to a guardian', function (): void {
    $guardian = Guardian::factory()->create();
    $student = Student::factory()->create();

    $data = [
        'student_id' => $student->id,
        'relation_to_student' => RelationToStudentEnum::Father->value,
    ];

    $response = $this->post(route('dashboard.guardians.attach-student', $guardian), $data);

    $response->assertRedirect(route('dashboard.guardians.show', $guardian));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('guardian_student', [
        'guardian_id' => $guardian->id,
        'student_id' => $student->id,
        'relation_to_student' => RelationToStudentEnum::Father->value,
    ]);
});

it('can detach a student from a guardian', function (): void {
    $guardian = Guardian::factory()->create();
    $student = Student::factory()->create();
    $guardian->students()->attach($student->id, [
        'relation_to_student' => RelationToStudentEnum::Father->value,
    ]);

    $response = $this->post(route('dashboard.guardians.detach-student', $guardian), [
        'student_id' => $student->id,
    ]);

    $response->assertRedirect(route('dashboard.guardians.show', $guardian));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('guardian_student', [
        'guardian_id' => $guardian->id,
        'student_id' => $student->id,
    ]);
});

it('can toggle guardian active status', function (): void {
    $guardian = Guardian::factory()->create();
    $guardian->user->update(['is_active' => true]);

    $response = $this->post(route('dashboard.guardians.toggle-active', $guardian));

    $response->assertRedirect(route('dashboard.guardians.show', $guardian));
    $response->assertSessionHas('success');

    $guardian->refresh();
    expect($guardian->user->is_active)->toBeFalse();
});
