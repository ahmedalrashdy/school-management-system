<?php

use App\Livewire\Dashboard\TimetableSettings\TimetableSettingsIndex;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(TimetableSettingsIndex::class)
        ->assertStatus(200);
});
