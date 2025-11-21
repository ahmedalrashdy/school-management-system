<?php

use App\Models\Student;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('returns paginated autocomplete results', function (): void {
    actingAs(User::factory()->create());

    Student::factory()->count(3)->create();

    $response = $this->getJson(route('common.autocomplete', [
        'resource' => 'students',
    ]));

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                ['id', 'text'],
            ],
            'meta' => ['next_cursor', 'has_more_pages'],
        ]);
});

it('filters results by search query', function (): void {
    actingAs(User::factory()->create());

    $matching = Student::factory()->create([
        'admission_number' => 'ADM-001',
    ]);

    $nonMatching = Student::factory()->create([
        'admission_number' => 'XYZ-999',
    ]);

    $response = $this->getJson(route('common.autocomplete', [
        'resource' => 'students',
        'search' => 'ADM-001',
    ]));

    $response->assertOk()->assertJsonPath('data.0.id', $matching->id);
    $response->assertJsonMissingExact(['id' => $nonMatching->id, 'text' => $nonMatching->user->full_name]);
});

it('rejects unauthorized resources', function (): void {
    actingAs(User::factory()->create());

    $this->getJson(route('common.autocomplete', [
        'resource' => 'unknown',
    ]))->assertStatus(422);
});

