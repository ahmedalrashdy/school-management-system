<?php

namespace App\Livewire\Common\TeacherProfile;

use App\Models\Teacher;
use Livewire\Attributes\Locked;
use Livewire\Component;

class TeacherInfo extends Component
{
    public Teacher $teacher;

    #[Locked()]
    public string $context = 'dashboard';
}
